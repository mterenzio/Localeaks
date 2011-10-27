<?php

/**
 * State_Model
 * 
 * @package States
 */

class State_Model extends CI_Model
{
	function GetStates($options = array())
	{
		// Qualification
		if(isset($options['capital']))
			$this->db->where('state_capital', $options['capital']);
		if(isset($options['abrrev']))
			$this->db->where('state_abbrev', $options['abbrev']);
		if(isset($options['name']))
			$this->db->where('state_name', $options['name']);
		if(isset($options['id']))
			$this->db->where('state_id', $options['id']);				
		// limit / offset
		if(isset($options['limit']) && isset($options['offset']))
			$this->db->limit($options['limit'], $options['offset']);
		else if(isset($options['limit']))
			$this->db->limit($options['limit']);
			
		// sort
		if(isset($options['sortBy']) && isset($options['sortDirection']))
			$this->db->order_by($options['sortBy'], $options['sortDirection']);
			
		$query = $this->db->get("states");
		
		if(isset($options['id']) || isset($options['abbrev']) || isset($options['capital']))
			return $query->row(0);
			
		return $query->result();
	}
}

