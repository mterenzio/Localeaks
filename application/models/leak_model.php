<?php

/**
 * Leak_Model
 * 
 * @package leaks
 */

class Leak_Model extends CI_Model
{

	/** Utility Methods **/
	function _required($required, $data)
	{
		foreach($required as $field)
			if(!isset($data[$field])) return false;
			
		return true;
	}
	
	function GetLeaks($options = array())
	{
		// Qualification
		if(isset($options['org']))
			$this->db->where('leak_org', $options['org']);
		if(isset($options['file']))
			$this->db->where('leak_file', $options['file']);
		if(isset($options['status']))
			$this->db->where('leak_status', $options['status']);
		if(isset($options['id']))
			$this->db->where('leak_id', $options['id']);				
		// limit / offset
		if(isset($options['limit']) && isset($options['offset']))
			$this->db->limit($options['limit'], $options['offset']);
		else if(isset($options['limit']))
			$this->db->limit($options['limit']);
			
		// sort
		if(isset($options['sortBy']) && isset($options['sortDirection']))
			$this->db->order_by($options['sortBy'], $options['sortDirection']);
			
		$query = $this->db->get("leaks");
		
		if(isset($options['id']))
			return $query->row(0);
			
		return $query->result();
	}	

	function GetStats($org)
	{
		$query = $this->db->query('SELECT * FROM leaks WHERE leak_org = '.$org);
		$stats = array();
		$stats['numleaks'] = $query->num_rows();
		$countviewed = 0;
		foreach ($query->result() as $row) {
			if ($row->leak_status == 'viewed') {
				$countviewed++;			
			}
		}
		$stats['viewedleaks'] = $countviewed;
		return $stats;
	}

	function GetLeakStats($leak)
	{
		$query = $this->db->query('SELECT * FROM leaks WHERE leak_id = '.$leak);
		$stats = array();
		$stats['numleaks'] = $query->num_rows();
		$countviewed = 0;
		foreach ($query->result() as $row) {
			if ($row->leak_status == 'viewed') {
				$countviewed++;			
			}
		}
		$stats['viewedleaks'] = $countviewed;
		return $stats;
	}


	function AddLeak($options = array())
	{
		// required values
		if(!$this->_required(
			array('leak_org', 'leak_file'),
			$options)
		) return false;		
		
		$this->db->insert('leaks', $options);
		
		return $this->db->insert_id();
	}
	
	function UpdateLeak($options = array())
	{
		if(!$this->_required(array('leak_id'),$options)) {
			return false;
	    } else {
			$this->db->where('leak_id', $options['leak_id']);	    
	    }

		if(isset($options['leak_status']))
			$this->db->set('leak_status', $options['leak_status']);			
			
		$this->db->update('leaks');		
		return $this->db->affected_rows();
	}
	
}

