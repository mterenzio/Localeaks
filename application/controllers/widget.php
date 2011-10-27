<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Widget extends CI_Controller {

	function __construct()
	{
		parent::__construct();
	}

	function index()
	{
		$this->load->model('user_model');
		if ($this->user_model->Auth()) {

			$filepath = '/var/www/secure/img/affiliate/';
			$this->load->helper(array('form', 'url'));		
			$this->load->library('form_validation');
			$this->form_validation->set_error_delimiters('<div class="formerror">', '</div>');
			$this->form_validation->set_rules('subdomain', 'Subdomain', 'trim|required|callback__valid_subdomain');
			$this->form_validation->set_rules('org', 'News Org', 'trim|required');
			$this->form_validation->set_rules('website', 'Website', 'trim|required|callback__valid_website');			
			$this->form_validation->set_rules('accept', 'accept checkbox', 'required');			
			if ($this->form_validation->run() == FALSE) {		
				$this->load->model('org_model');		
				$data['orgs'] = $this->org_model->GetOrgs(array('email' => $this->session->userdata('userEmail')));
				$this->load->vars($data);
				$this->load->view('createwidget', $data);
			} else {
				//no captch captcha
				$proceed = false;
				$seconds = 60*10;
				if(isset($_POST['ts']) && isset($_COOKIE['token']) && $_COOKIE['token'] == md5('secret salt'.$_POST['ts'])) $proceed = true;
				if(!$proceed) { 
					echo 'Form processing halted for suspicious activity';
					exit;
				}		
				if(((int)$_POST['ts'] + $seconds) < mktime()) {
					echo 'Too much time elapsed';
					exit;
				}		
		
				//process form
				$config['upload_path'] = $filepath;
				$config['allowed_types'] = 'png';
				$config['overwrite'] = 'TRUE';
				$config['max_size']	= '300';
				$config['max_width']  = '400';
				$config['max_height']  = '90';
				$config['file_name'] = $this->input->post('subdomain').".png";
				$this->load->library('upload', $config);			
				if (!$this->upload->do_upload('file')) {
					//$upload = $this->upload->data();
					////$data = array('upload_data' => $this->upload->data());
					//$mimetype= $data['upload_data']['file_type'];
					//	$imagetype= $data['upload_data']['image_type'];
					//echo $mimetype;
					//echo $imagetype;

					echo $this->upload->display_errors();
					// uploading failed. $error will holds the errors.
				} else {			    
					// uploading successfull, now do your further actions
					$upload = $this->upload->data();
					$this->load->model('org_model');
					$perms = $this->org_model->GetOrgs(array('email' => $this->session->userdata('userEmail')));
					$haspermission = false;
					foreach ($perms as $perm) {
						if ($perm->org_id == $this->input->post('org')) {
							$haspermission = true;
						}
					}
					if ($haspermission) {
						if ($this->org_model->UpdateOrg(array('org_id' => $this->input->post('org'), 'org_subdomain' => $this->input->post('subdomain'), 'org_website' => $this->input->post('website')))) {
							$data['org'] = $this->input->post('subdomain');
							$data['orgid'] = $this->input->post('org');
							$this->load->view('copywidget', $data);
						} else {
							echo 'fail';
							exit;
						}
					} else {
						echo "Sorry, you don't have permission to create an affiliate for that organization. Please contact support.";
						exit;
					}
				}
			}
		
		//not logged in
		} else {
			redirect('account/login');			
		}
		
	}

	function _valid_website($website)
	{
		if($this->input->post('website'))
		{
			if(filter_var($website, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED) == FALSE) {
		    	$this->form_validation->set_message('_valid_website', 'This is not a valid website. Please include http:// before the domain name.');			
				return false;
			}
		}
		return true;
	}	

	function _valid_subdomain($subdomain)
	{
		if($this->input->post('subdomain'))
		{
			if(!ctype_alnum($subdomain)) {
		    	$this->form_validation->set_message('_valid_subdomain', 'This is not a valid sudomain. No spaces and alpha-numeric characters only.');			
				return false;
			}
			//test if available
			$this->load->model('org_model');
			if ($org = $this->org_model->GetOrgs(array('subdomain' => $subdomain))) {
				if ($org->org_id == $this->input->post('org')) {
					return true;
				} else {				
					$this->form_validation->set_message('_valid_subdomain', 'This subdomain is already taken. Please try another.');				
					return false;
				}
			}
		}
		return true;
	}


}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */