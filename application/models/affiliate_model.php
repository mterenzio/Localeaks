<?php

/**
 * Affiliate_Model
 * 
 * @package affiliates
 */

class Affiliate_Model extends CI_Model
{

	public $prefs = array();

	function __construct()
	{
		parent::__construct();
		if ($this->config->item('subdomain') == "") {
			//$logo = "/img/logo.png";
			//$tag = "A service for concerned citizens of the U.S. to provide anonymous tips to their local and state news organizations <a href=\"http://about.lob.by/localeaks/\">Learn More</a>";
			$this->prefs['affiliate'] = FALSE;
		} else {
			$this->load->model('org_model');	
			$affiliate = $this->org_model->GetOrgs(array('subdomain' => $this->config->item('subdomain')));
			if ($affiliate) {
				$this->prefs['affiliate'] = TRUE;
			} else {
				$this->prefs['affiliate'] = FALSE;
			}
		}
		if ($this->prefs['affiliate'] == TRUE) {
			$this->prefs['logo'] = '/img/affiliate/'.$affiliate->org_subdomain.'.png';	
			$this->prefs['state'] = $affiliate->org_state_abbrev;
			$this->prefs['id'] = $affiliate->org_id;
			$this->prefs['website'] = $affiliate->org_website;
			if ($affiliate->org_tagline == NULL || $affiliate->org_tagline == '') {
				$this->prefs['tagline'] = "A service for concerned citizens of the U.S. to provide anonymous tips to their local and state news organizations <a href=\"/about/\">Learn More</a>";
			} else {
				$this->prefs['tagline'] = $affiliate->org_tagline;
			}
		} else {
			//use default
			$this->prefs['website'] = 'https://localeaks.com';
			$this->prefs['logo'] = '/img/logo.png';
			$this->prefs['tagline'] = "A service for concerned citizens of the U.S. to provide anonymous tips to their local and state news organizations <a href=\"/about/\">Learn More</a>";
		}
	}
	
	
}

