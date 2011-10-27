<?php

class Cron extends CI_Controller {

	function __construct()
	{
		parent::__construct();
	}
	
	function release()
	{
			$this->load->model('leak_model');
			$newleaks = $this->leak_model->GetLeaks(array('status' => 'new', 'limit' => 600));
			foreach ($newleaks as $leak)  {
				$this->load->model('org_model');			
				$org = $this->org_model->GetOrgs(array('id' => $leak->leak_org));
				if ($org->org_status == 'live' || $org->org_status == 'claimed' || $org->org_status == 'verified') {
					//we have a leak for a living org - email it
					if ($updatedleak = $this->leak_model->UpdateLeak(array('leak_id' => $leak->leak_id, 'leak_status' => 'released'))) {
						$this->load->library('email');
						$config['newline'] = '\r\n';
						$this->email->initialize($config);
						$this->email->from('support@localeaks.com', 'Localeaks');
						$this->email->to($org->org_email);
						$this->email->subject('You have a new localeak . . .');
						if ($org->org_status == 'live') {
							$message = "But you have not yet claimed your account. You may do so at https://localeaks.com/account/create \r\n\r\n Localeaks is a web service which allows citizens of the U.S. to provide secure, anonymous tips to their local and state news organizations.";				
						} elseif ($org->org_status == 'claimed' || $org->org_status == 'verified') {
							$message = "You may access the tip at https://localeaks.com/account/inbox";						
						}
						$optout = 'https://localeaks.com/account/disable/'.md5($org->org_email).'/'.$org->org_token;
						$this->email->message("A new tip was submitted to Localeaks. \r\n \r\n $message \r\n \r\n \r\n ------------------------------------------------ \r\n To stop future emails to this organziation, click the link below: \r\n\r\n".$optout);
						$this->email->send();							
					}
				} else {
					$updatedleak = $this->leak_model->UpdateLeak(array('leak_id' => $leak->leak_id, 'leak_status' => 'released'));				
				}
			}
	}

	function releaseorg($orgid)
	{
			$this->load->model('leak_model');
			$newleaks = $this->leak_model->GetLeaks(array('status' => 'new', 'org' => $orgid));
			$notified = array();
			foreach ($newleaks as $leak)  {
				$this->load->model('org_model');			
				$org = $this->org_model->GetOrgs(array('id' => $leak->leak_org));
				//echo in_array ($leak->leak_org , $notified);
				//echo print_r($notified, true)."<br /><br /><br /><br />";
				if ($org->org_status == 'live' || $org->org_status == 'claimed' || $org->org_status == 'verified') {
					//we have a leak for a living org - email it
					if ($updatedleak = $this->leak_model->UpdateLeak(array('leak_id' => $leak->leak_id, 'leak_status' => 'released'))) {
						if (in_array ($leak->leak_org , $notified)) {
							continue;
						} else {					
						$this->load->library('email');
						$config['newline'] = '\r\n';
						$this->email->initialize($config);
						$this->email->from('support@localeaks.com', 'Localeaks');
						$this->email->to($org->org_email);
						$this->email->subject('You have a new localeak . . .');
						if ($org->org_status == 'live') {
							$message = "But you have not yet claimed your account. You may do so at https://localeaks.com/account/create \r\n \r\n You may designate an alternative email for your Localeaks account by contacting support@localeaks.com";				
						} elseif ($org->org_status == 'claimed' || $org->org_status == 'verified') {
							$message = "You may access the leak at https://localeaks.com/account/inbox";						
						}
						$optout = 'https://localeaks.com/account/disable/'.md5($org->org_email).'/'.$org->org_token;
						$this->email->message("A new tip was submitted to Localeaks. \r\n \r\n $message \r\n \r\n \r\n ------------------------------------------------ \r\n To stop future emails to this organziation, click the link below: \r\n\r\n".$optout);
						$this->email->send(); 
						$notified[] = $leak->leak_org;
						}
					}
				}  else {
					$updatedleak = $this->leak_model->UpdateLeak(array('leak_id' => $leak->leak_id, 'leak_status' => 'released'));				
				}
			}
	}

	function releasefile($filename)
	{
			$this->load->model('leak_model');
			$newleaks = $this->leak_model->GetLeaks(array('status' => 'new', 'file' => $filename));
			$notified = array();
			foreach ($newleaks as $leak)  {
				$this->load->model('org_model');			
				$org = $this->org_model->GetOrgs(array('id' => $leak->leak_org));
				//echo in_array ($leak->leak_org , $notified);
				//echo print_r($notified, true)."<br /><br /><br /><br />";
				if ($org->org_status == 'live' || $org->org_status == 'claimed' || $org->org_status == 'verified') {
					//we have a leak for a living org - email it
					if ($updatedleak = $this->leak_model->UpdateLeak(array('leak_id' => $leak->leak_id, 'leak_status' => 'released'))) {
						if (in_array ($leak->leak_org , $notified)) {
							continue;
						} else {					
						$this->load->library('email');
						$config['newline'] = '\r\n';
						$this->email->initialize($config);
						$this->email->from('support@localeaks.com', 'Localeaks');
						$this->email->to($org->org_email);
						$this->email->subject('You have a new localeak . . .');
						if ($org->org_status == 'live') {
							$message = "But you have not yet claimed your account. You may do so at https://localeaks.com/account/create \r\n \r\n You may designate an alternative email for your Localeaks account by contacting support@localeaks.com";				
						} elseif ($org->org_status == 'claimed' || $org->org_status == 'verified') {
							$message = "You may access the leak at https://localeaks.com/account/inbox";						
						}
						$optout = 'https://localeaks.com/account/disable/'.md5($org->org_email).'/'.$org->org_token;
						$this->email->message("A new tip was submitted to Localeaks. \r\n \r\n $message \r\n \r\n \r\n ------------------------------------------------ \r\n To stop future emails to this organziation, click the link below: \r\n\r\n".$optout);
						$this->email->send(); 
						$notified[] = $leak->leak_org;
						}
					}
				}  else {
					$updatedleak = $this->leak_model->UpdateLeak(array('leak_id' => $leak->leak_id, 'leak_status' => 'released'));				
				}
			}
	}

	function releasestate($state_abbrev)
	{
			$this->load->model('leak_model');
			$newleaks = $this->leak_model->GetLeaks(array('status' => 'new'));
			$notified = array();
			foreach ($newleaks as $leak)  {
				$this->load->model('org_model');			
				$org = $this->org_model->GetOrgs(array('id' => $leak->leak_org));
				//echo in_array ($leak->leak_org , $notified);
				//echo print_r($notified, true)."<br /><br /><br /><br />";
				if ($org->org_status == 'live' || $org->org_status == 'claimed' || $org->org_status == 'verified' && $org->org_state_abbrev == $state_abbrev) {
					//we have a leak for a living org - email it
					if ($updatedleak = $this->leak_model->UpdateLeak(array('leak_id' => $leak->leak_id, 'leak_status' => 'released'))) {
						if (in_array ($leak->leak_org , $notified)) {
							continue;
						} else {					
						$this->load->library('email');
						$config['newline'] = '\r\n';
						$this->email->initialize($config);
						$this->email->from('support@localeaks.com', 'Localeaks');
						$this->email->to($org->org_email);
						$this->email->subject('You have a new localeak . . .');
						if ($org->org_status == 'live') {
							$message = "But you have not yet claimed your account. You may do so at https://localeaks.com/account/create \r\n \r\n You may designate an alternative email for your Localeaks account by contacting support@localeaks.com";				
						} elseif ($org->org_status == 'claimed' || $org->org_status == 'verified') {
							$message = "You may access the leak at https://localeaks.com/account/inbox";						
						}
						$optout = 'https://localeaks.com/account/disable/'.md5($org->org_email).'/'.$org->org_token;
						$this->email->message("A new tip was submitted to Localeaks. \r\n \r\n $message \r\n \r\n \r\n ------------------------------------------------ \r\n To stop future emails to this organziation, click the link below: \r\n\r\n".$optout);
						$this->email->send(); 
						$notified[] = $leak->leak_org;
						}
					}
				}  else {
					$updatedleak = $this->leak_model->UpdateLeak(array('leak_id' => $leak->leak_id, 'leak_status' => 'released'));				
				}
			}
	}
	/*function createtokens()
	{
		$this->load->model('org_model');
		$orgs = $this->org_model->GetOrgs(array());
		//echo print_r($user, true);
		$count = 0;
		foreach ($orgs as $org) {
		    if ($this->org_model->UpdateOrg(array('org_id' => $org->org_id, 'org_token' => md5($org->org_name.$org->org_id)))) {
				$count++;
		    } else {
		    	echo $org->org_id;
		    } 
		}
		
		echo "COUNT:".$count;
	}*/

	
}

/* End of file cron.php */
/* Location: ./system/application/controllers/cron.php */