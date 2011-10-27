<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Feeds extends CI_Controller {

	function __construct()
	{
		parent::__construct();
	}

	function index()
	{
		if (!isset($_SERVER['PHP_AUTH_USER'])) {
		    header('WWW-Authenticate: Basic realm="LocalRealm"');
		    header('HTTP/1.0 401 Unauthorized');
		    echo 'Request Canceled . . . Unauthorized';
		    exit;
		} else {
			$this->load->model('user_model');
			if ($this->user_model->Login(array('userEmail' => $_SERVER['PHP_AUTH_USER'], 'userPassword' => $_SERVER['PHP_AUTH_PW']))) {
					$this->load->model('leak_model');
					$this->load->model('org_model');
					$orgs = $this->org_model->GetOrgs(array('email' => $_SERVER['PHP_AUTH_USER']));
					foreach ($orgs as $org) {
						$stats = $this->leak_model->GetStats($org->org_id);
						//echo print_r($stats, true);
						$data['numleaks'] = $stats['numleaks'];
						$data['viewedleaks'] = $stats['viewedleaks'];				
						$leaks = $this->leak_model->GetLeaks(array('status' => 'released', 'org' => $org->org_id));
						if (count($leaks) == 0) {
							$data['leaks'] = array();	
							$this->load->view('/feeds/feed', $data);			     
						} else {
							$data['leaks'] = $leaks;
							$this->load->view('inbox', $data);
						}
					}
				//$data['leaks'] = array(array('title' => 'tit', 'description' => 'desc'), array('title' => 'tit2', 'description' => 'desc2'));
				//$this->load->view('/feeds/feed', $data);				
			}  else {
		    	header('WWW-Authenticate: Basic realm="LocalRealm"');
		   	 	 header('HTTP/1.0 401 Unauthorized');			
				echo "sorry, you are not authorized.";
						    exit;
			}

		}
	}
}
/* End of file feeds.php */
/* Location: ./application/controllers/feeds.php */