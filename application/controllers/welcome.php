<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {

	function __construct()
	{
		parent::__construct();
	}

	function index()
	{
		$filepath = '/var/www/cipherTmp/';
		$this->load->helper(array('form', 'url'));		
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<div class="formerror">', '</div>');
		$this->form_validation->set_rules('tip', 'Tip', 'required');
		$this->form_validation->set_rules('states', 'States', 'required');	
		$this->form_validation->set_rules('orgs', 'News Orgs', 'required');	
		$this->form_validation->set_rules('accept', 'accept checkbox', 'required');	
		//$this->form_validation->set_rules('captcha', 'Word Test', 'required|callback__check_captcha');			
		if ($this->form_validation->run() == FALSE)
		{		
		$this->load->model('state_model');
		$options = array('sortBy' => 'state_name', 'sortDirection' => 'ASC');		
		$data['states'] = $this->state_model->GetStates($options);
		if (isset($_POST['states'])) {
			$this->load->model('org_model');		
			$data['orgs'] = $this->org_model->GetOrgsForSelect($_POST['states']);
		}
		$this->load->vars($data);
		$this->load->view('welcome_message', $data);
		}
		else
		{
			//no captcha captcha
			$proceed = false;
			$seconds = 60*20;
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
			$config['allowed_types'] = 'pdf';
			$config['max_size']	= '20480';
			//$config['max_width']  = '1024';
			//$config['max_height']  = '768';
			$config['encrypt_name'] = TRUE;
			$this->load->library('upload', $config);			
			if (!$this->upload->do_upload('file') && trim($this->upload->display_errors()) != '<p>You did not select a file to upload.</p>')
			{
							$upload = $this->upload->data();
			echo $upload['file_type'];  
				$error = array('error' => $this->upload->display_errors());
 				echo print_r($error, true);
				// uploading failed. $error will holds the errors.
			}
			else
			{			    
				// uploading successfull, now do your further actions
				$upload = $this->upload->data();
				$this->load->model('encrypt_model');
				
				if ($upload['full_path'] == $filepath) {
				//do nothing - no file
				$secureFile = '';
				} else {
				$secureFile = $this->encrypt_model->Encrypt_File("matt@buddybuilder.com", $upload['full_path']);			
				}
				//$text = "New Localeaks Tip\n" 
				//. "\nTip: "       . $this->input->post('tip') 
				//. "\n"
				//. "\nOrgs: "       . $orgs
				//. "\nFile: "       . $secureFile 				
				//. "\n";
				// validate input more??
				$secureText = $this->encrypt_model->Encrypt_Text("matt@buddybuilder.com", $this->input->post('tip'));
				$this->load->model('leak_model');
				if (!is_array($this->input->post('orgs'))) {
					$orgs = array($this->input->post('orgs'));
				} else {
					$orgs = $this->input->post('orgs');
				}
				foreach ($orgs as $org) {
					//test if org first??
					unset($leak);
					//echo $filepath.' '.$secureText;
					$textfilename = explode($filepath, $secureText);
					$leak = array('leak_org' => $org, 'leak_file' => $textfilename[1]);
					if ($this->leak_model->AddLeak($leak)) {
						if ($secureFile != '') {
							unset($leak);
							$uploadfile = $upload['file_name'];
							$leak = array('leak_org' => $org, 'leak_file' => 'clean-'.$upload['file_name']);
							if ($this->leak_model->AddLeak($leak)) {
   
							} else {
								echo "We had a problem processing the form.";
								exit();	
							} 
						}
								$this->load->library('email');
								$this->email->from('notice@localeaks.com', 'Localeaks');
								$this->email->to('webmaster@localeaks.com');
								$this->email->subject('Localeaks Submission');
								$this->email->message('A new submission was made at Localeaks.');
								$this->email->send();
								$data['message'] = "Your form has been successfully submitted.";
								$this->load->view('formsuccess', $data);
					} else {
						echo "We had a problem processing the form.1";
						exit();					
					}
				}				
		
			}
		} 
	}
	
	function _check_captcha($captcha)
	{
		if($this->input->post('captcha'))
		{
			if($captcha != '10') {
		    	$this->form_validation->set_message('_check_captcha', 'You failed the word test.');			
				return false;
			}
		}
		return true;
	}	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */