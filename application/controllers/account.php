<?php

class Account extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('user_model');
	}
	
	function login()
	{
		if ($this->user_model->Auth()) {
			redirect('account/inbox');
		} else {
		$this->form_validation->set_rules('userEmail', 'email', 'trim|required|valid_email|callback__check_login');
		$this->form_validation->set_rules('userPassword', 'password', 'trim|required');
		
		if($this->form_validation->run())
		{
			// the form has successfully validated
			if($this->user_model->Login(array('userEmail' => $this->input->post('userEmail'), 'userPassword' => $this->input->post('userPassword'))))
			{
				if ($this->session->userdata('return')) {
					redirect($this->session->userdata('return'));
				} else {
					redirect('account/inbox');
				}
			} else {
				redirect('account/login');
			}
		} else {		
			$this->load->view('account/login_form');
		}
		}
	}

	function logout()
	{
		$this->session->sess_destroy();		
		$data['message'] = "You have been logged out.";
		$this->load->view('formsuccess', $data);
	}

	function create()
	{
		$this->form_validation->set_rules('userEmail', 'email', 'trim|required|valid_email|callback__check_email|callback__check_org_email');
		$this->form_validation->set_rules('userPassword', 'password', 'trim|required|min_length[8]');
		$this->form_validation->set_rules('userName', 'username', 'trim|required|alpha_numeric|max_length[20]|callback__check_username');
		$this->form_validation->set_error_delimiters('<div class="formerror">', '</div>');				
		if($this->form_validation->run())
		{
			// the form has successfully validated
			if($returnedId = $this->user_model->AddUser(array('userEmail' => $this->input->post('userEmail'), 'userPassword' => md5($this->input->post('userPassword')), 'userName' => $this->input->post('userName'), 'userToken' => md5($this->input->post('userName')))))
			{
				$this->session->set_userdata('userEmail', $this->input->post('userEmail'));
				$this->session->set_userdata('userId', $returnedId);
				$this->load->library('email');
				$config['newline'] = '\r\n';
				$this->email->initialize($config);
				$this->email->from('support@localeaks.com', 'Localeaks Support');
				$this->email->to($this->input->post('userEmail'));
				$this->email->subject('Verify your email address');
				$this->email->message("Click the link below or paste into your browser to verify your email:\r\n ".base_url().'account/verify/'.md5($this->input->post('userEmail'))."/".md5($this->input->post('userName')));
				$this->email->send();
				$data['message'] = "Success! <br /> Instructions will follow in an email.";
				$this->load->view('formsuccess', $data);
			} else {
				redirect('account/create');
			}
		} else {		
			$this->load->view('account/create_form');
		}
	}

	function inbox()
	{
		if ($this->user_model->Auth()) {
			$this->load->model('leak_model');
			$this->load->model('org_model');
			$orgs = $this->org_model->GetOrgs(array('email' => $this->session->userdata('userEmail')));
			foreach ($orgs as $org) {
				$stats = $this->leak_model->GetStats($org->org_id);
				//echo print_r($stats, true);
				$data['numleaks'] = $stats['numleaks'];
				$data['viewedleaks'] = $stats['viewedleaks'];				
				$leaks = $this->leak_model->GetLeaks(array('status' => 'released', 'org' => $org->org_id));
				if (count($leaks) == 0) {
					$data['message'] = "There are no new leaks available at this time.";
					$this->load->view('inbox', $data);				     
				} else {
					$data['leaks'] = $leaks;
					$this->load->view('inbox', $data);
				}
			}
		} else {
			redirect('account/login');			
		}
	}

	function download($filename)
	{
		if ($this->user_model->Auth()) {
			$file = '/var/www/releases/'.$filename;
			header('Content-type: application/force-download');
			header('Content-Transfer-Encoding: Binary');
			header('Content-disposition: attachment; filename="'. basename($file) .'"');
			header('Content-length: '. filesize($file) );

			readfile( $file );
		} else {
			redirect('account/login');			
		}
	}

	function verify($userEmail, $userToken)
	{
		$user = $this->user_model->GetUsers(array('userToken' => $userToken));
		//echo print_r($user, true);
		if($user) {
			$now = time();
		    if (md5($user->userEmail) == $userEmail && $now < $user->userTokenExpire) {
		    	$updated = $this->user_model->UpdateUser(array('userId' => $user->userId, 'userEmailVerified' => 't'));
				$this->load->model('org_model');
				$orgs = $this->org_model->GetOrgs(array('email' => $user->userEmail));
				foreach ($orgs as $org) {
		    		$claimed = $this->org_model->UpdateOrg(array('org_id' => $org->org_id, 'org_status' => 'claimed'));
		    	}
		    	if ($claimed) {
					$this->load->library('email');
					$config['newline'] = '\r\n';
					$this->email->initialize($config);
					$this->email->from('support@localeaks.com', 'Localeaks Support');
					$this->email->to('mterenzio@gmail.com');
					$this->email->subject('Localeaks Account Verified');
					$this->email->message($org->org_id);
					$this->email->send();		    	
					$data['message'] = "Your account has been verified. <br />You will now be able to <a href=\"https://localeaks.com/account/login\">login</a> and retrieve your data.";
					$this->load->view('formsuccess', $data);
		    	} else {
					$data['message'] = "There was a problem verifying your account. The link may have expired. Please contact <a href=\"mailto:support@localeaks.com\">support@localeaks.com</a>";
					$this->load->view('formsuccess', $data);		    	
		    	}
		    }
		}
		return false;
	}

	function disable($org_email, $org_token)
	{
		$this->load->model('org_model');
		$org = $this->org_model->GetOrgs(array('token' => $org_token));
		//echo print_r($user, true);
		if($org) {
		    if (md5($org->org_email) == $org_email && $org->org_token == $org_token) {
		    		$disabled = $this->org_model->UpdateOrg(array('org_id' => $org->org_id, 'org_status' => 'disabled'));
		    	if ($disabled) {
					$this->load->library('email');
					$config['newline'] = '\r\n';
					$this->email->initialize($config);
					$this->email->from('support@localeaks.com', 'Localeaks Support');
					$this->email->to('mterenzio@gmail.com');
					$this->email->subject('Localeaks Account Disabled');
					$this->email->message($org->org_name.' '.$org->org_state);
					$this->email->send();		    	
					$data['message'] = "Your account has been disabled. <br />If this was in error, please contact <a href=\"mailto:support@localeaks.com\">support@localeaks.com</a>";
					$this->load->view('formsuccess', $data);
		    	}
		    }
		}
		return false;
	}
	
	function password()
	{
		$this->form_validation->set_rules('userEmail', 'email', 'trim|required|valid_email|callback__check_email_exists');
		if($this->form_validation->run())
		{		
			// the form has successfully validated
				$user = $this->user_model->GetUsers(array('userEmail' => $this->input->post('userEmail')));	
				//echo print_r($user, true);
				$userPasswordToken = md5(time().$this->input->post('userEmail'));
		    	$updated = $this->user_model->UpdateUser(array('userId' => $user->userId, 'userPasswordToken' => $userPasswordToken));
		    	//echo print_r($updated, true);
		    	if ($updated) {
					$this->load->library('email');
					$config['newline'] = '\r\n';
					$this->email->initialize($config);
					$this->email->from('support@localeaks.com', 'Localeaks Support');
					$this->email->to($this->input->post('userEmail'));
					$this->email->subject('Update your password request');
					$this->email->message("A password update was requested for this account. \r\n \r\n If you made this request click the link below to update your password, otherwise ignore this email:\r\n ".base_url().'account/updatepassword/'.md5($this->input->post('userEmail'))."/".$userPasswordToken);
					$this->email->send();
					$data['message'] = "You should receive an email with instructions on updating your password. <br /> Email <a href=\"mailto:support@localeaks.com\">support@localeaks.com</a> if you have an issue.";
					$this->load->view('formsuccess', $data);
		    	}
		} else {
		$this->load->view('account/password_form');
		}
	}

	function updatepassword($userEmail='', $userPasswordToken='')
	{
	    if ($userEmail == '' || $userPasswordToken=='') {
	    	// assume trying to post form -- must still verify credentials
			$user = $this->user_model->GetUsers(array('userPasswordToken' => $this->input->post('userPasswordToken')));	
			if ($user) {
		    	$now = time();
				if (md5($user->userEmail) == $this->input->post('userEmailToken') && $now < $user->userPasswordTokenExpire) {		
			   	 	$updated = $this->user_model->UpdateUser(array('userId' => $user->userId, 'userPassword' => md5($this->input->post('userPassword'))));
		    		//echo print_r($updated, true);
		    		if ($updated) {
						$data['message'] = "Success! <br /> <a href=\"https://localeaks.com/account/login\">Login</a> with your new password.";
						$this->load->view('formsuccess', $data);
		    		}
				} else {
					//redirect('account/password_denied'); 
						$data['message'] = "Denied! <br /> Email <a href=\"mailto:support@localeaks.com\">support@localeaks.com</a> if you continue to have an issue.";
						$this->load->view('formsuccess', $data);
				}			
			}
	    } else {
		$user = $this->user_model->GetUsers(array('userPasswordToken' => $userPasswordToken));	
		if ($user) {
		    $now = time();
			if (md5($user->userEmail) == $userEmail && $now < $user->userPasswordTokenExpire) {		
			    //show a password update form
				$data = array();
				$data['emailtoken'] = $userEmail;
				$data['passwordtoken'] = $userPasswordToken;
				$this->load->view('account/update_password_form', $data);			
			} else {
				//redirect('account/password_denied'); 
				$data['message'] = "Denied! <br /> Email <a href=\"mailto:support@localeaks.com\">support@localeaks.com</a> if you continue to have an issue.";
				$this->load->view('formsuccess', $data);			}
		} else {
			$data['message'] = "Denied! <br /> Email <a href=\"mailto:support@localeaks.com\">support@localeaks.com</a> if you continue to have an issue.";
			$this->load->view('formsuccess', $data);
		}
		}
	}
	
	function index()
	{
		//$this->load->view('account/main_index');
	}
	
	function _check_login($userEmail)
	{
		if($this->input->post('userPassword'))
		{
			$user = $this->user_model->GetUsers(array('userEmail' => $userEmail, 'userPassword' => md5($this->input->post('userPassword'))));
			if($user) return true;
		}
		
		$this->form_validation->set_message('_check_login', 'Your username / password combination is invalid.');
		return false;
	}

	function _check_email_exists($userEmail)
	{
		if($this->input->post('userEmail'))
		{
			$user = $this->user_model->GetUsers(array('userEmail' => $userEmail));
			if(!$user) {
		    	$this->form_validation->set_message('_check_email_exists', 'This email does not exist in our records.');			
				return false;
			}
		}
		return true;
	}
	
	
	function _check_email($userEmail)
	{
		if($this->input->post('userEmail'))
		{
			$user = $this->user_model->GetUsers(array('userEmail' => $userEmail));
			if($user) {
		    	$this->form_validation->set_message('_check_email', 'Your email is already in use. Perhaps someone already claimed this organization.');			
				return false;
			}
		}
		return true;
	}

	function _check_org_email($userEmail)
	{
		if($this->input->post('userEmail'))
		{
			$this->load->model('org_model');		
			$org = $this->org_model->GetOrgs(array('email' => $userEmail));
			if(!$org) {
		    	$this->form_validation->set_message('_check_org_email', 'Your email is not in our database. Please email <a href="mailto:support@localeaks.com">support@localeaks.com</a> to change your organizations email.');			
				return false;
			}
		}
		return true;
	}

	
	function _check_username($userName)
	{
		if($this->input->post('userName'))
		{
			$user = $this->user_model->GetUsers(array('userName' => $userName));
			if($user) {
		    	$this->form_validation->set_message('_check_username', 'Username is already in use. Please try another.');			
				return false;
			}
		}
		return true;
	}
	
}

/* End of file account.php */
/* Location: ./system/application/controllers/account.php */