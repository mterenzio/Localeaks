<?php

/**
 * Org_Model
 * 
 * @package Orgs
 */

class Org_Model extends CI_Model
{

	/** Utility Methods **/
	function _required($required, $data)
	{
		foreach($required as $field)
			if(!isset($data[$field])) return false;
			
		return true;
	}

	function GetOrgs($options = array())
	{
		// Qualification
		if(isset($options['name']))
			$this->db->where('org_name', $options['name']);
		if(isset($options['address']))
			$this->db->where('org_address', $options['address']);
		if(isset($options['city']))
			$this->db->where('org_city', $options['city']);
		if(isset($options['state']))
			$this->db->where('org_state', $options['state']);
		if(isset($options['state_abbrev']))
			$this->db->where('org_state_abbrev', $options['state_abbrev']);			
		if(isset($options['zip']))
			$this->db->where('org_zip', $options['zip']);
		if(isset($options['county']))
			$this->db->where('org_county', $options['county']);
		if(isset($options['email']))
			$this->db->where('org_email', $options['email']);
		if(isset($options['status']))
			$this->db->where('org_status', $options['status']);			
		if(isset($options['id']))
			$this->db->where('org_id', $options['id']);
		if(isset($options['token']))
			$this->db->where('org_token', $options['token']);
		if(isset($options['subdomain']))
			$this->db->where('org_subdomain', $options['subdomain']);			
		// limit / offset
		if(isset($options['limit']) && isset($options['offset']))
			$this->db->limit($options['limit'], $options['offset']);
		else if(isset($options['limit']))
			$this->db->limit($options['limit']);
			
		// sort
		if(isset($options['sortBy']) && isset($options['sortDirection']))
			$this->db->order_by($options['sortBy'], $options['sortDirection']);
			
		$query = $this->db->get("orgs");
		
		if(isset($options['id']) || isset($options['name']) || isset($options['token']) || isset($options['subdomain']))
			return $query->row(0);
			
		return $query->result();
	}

	function GetLiveOrClaimedOrgs($state)
	{
			$this->db->order_by('org_name', 'ASC');
//only active orgs here
		$where = "org_state_abbrev = '$state' AND (org_status='live' OR org_status='claimed' OR org_status='verified')";
		$this->db->where($where);	
		
		$query = $this->db->get("orgs");
		
		if(isset($options['id']) || isset($options['name']))
			return $query->row(0);
			
		return $query->result();
	}

	function UpdateOrg($options = array())
	{
		if(!$this->_required(array('org_id'),$options)) {
			return false;
	    } else {
			$this->db->where('org_id', $options['org_id']);	    
	    }

		if(isset($options['org_status']))
			$this->db->set('org_status', $options['org_status']);			
		if(isset($options['org_token']))
			$this->db->set('org_token', $options['org_token']);
		if(isset($options['org_subdomain']))
			$this->db->set('org_subdomain', $options['org_subdomain']);	
		if(isset($options['org_website']))
			$this->db->set('org_website', $options['org_website']);
		if(isset($options['org_tagline']))
			$this->db->set('org_tagline', $options['org_tagline']);				
		$this->db->update('orgs');		
		return $this->db->affected_rows();
	}

	
	function GetOrgsForSelect($state)
	{
			$this->db->order_by('org_name', 'ASC');
//only active orgs here
		$where = "org_state_abbrev = '$state' AND (org_status='live' OR org_status='claimed' OR org_status='verified')";
		$this->db->where($where);	
			
		$query = $this->db->get("orgs");
		$results = array();
		foreach ($query->result() as $org) {
			$results[$org->org_id] = $org->org_name;
		}
		return $results;
	}
	
}

