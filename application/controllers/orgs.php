<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Orgs extends CI_Controller {

	function __construct()
	{
		parent::__construct();
	}

	function index()
	{
	//get where state =
	 //   if ($state == "VT") {
	//	$data['orgs'] = '<option value="VT">Free Press</option>'; //array('Free Press');
	//	 } elseif ($state == "CT") {
	//	$data['orgs'] = '<option value="CT">the hour</option>';
	//	}
	//	$this->load->view('api', $data);
	}
	
	function state($state = null)
	{
		if ($state == '') {
		$data['orgs'] = '';
		$this->load->vars($data);
		$this->load->view('api', $data);	
		} else {
		$this->load->model('org_model');
		//$options = array('state_abbrev' => $state, 'sortBy' => 'org_name', 'sortDirection' => 'ASC');		
		$data['orgs'] = $this->org_model->GetLiveOrClaimedOrgs($state);
		$this->load->vars($data);
		$this->load->view('api', $data);		
		}
	}
	
		function leakcount($orgid)
	{
		$this->load->model('leak_model');
		$stats = $this->leak_model->GetStats($orgid);
		$data['numleaks'] = $stats['numleaks'];
		$this->load->view('count', $data);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */