<?php

/**
 * User_Model
 * 
 * @package Users
 */

class User_Model extends CI_Model
{
	
	/** Utility Methods **/
	function _required($required, $data)
	{
		foreach($required as $field)
			if(!isset($data[$field])) return false;
			
		return true;
	}
	
	function _default($defaults, $options)
	{
		return array_merge($defaults, $options);
	}
	
	/** User Methods **/
	
	/**
	 * AddUser method creates a record in the users table
	 * 
	 * Option: Values
	 * --------------
	 * userEmail
	 * userPassword
	 * userStatus
	 * userToken
	 * 
	 * @param array $options
	 * @result int insert_id()
	 */
	function AddUser($options = array())
	{
		// required values
		if(!$this->_required(
			array('userEmail', 'userPassword', 'userName', 'userToken'),
			$options)
		) return false;
		
		//$options = $this->_default(array('userStatus', 'active'), $options);
		$onedayfromnow = time() + 86400;
		$this->db->set('userTokenExpire', $onedayfromnow);	
		
		$this->db->insert('users', $options);
		
		return $this->db->insert_id();
	}
	
	/**
	 * UpdateUser method updates a record in the users table
	 * 
	 * Option: Values
	 * --------------
	 * userId			required
	 * userEmail
	 * userPassword
	 * userStatus
	 * 
	 * @param array $options
	 * @return int affected_rows()
	 */
	function UpdateUser($options = array())
	{
		// required values
		if(!$this->_required(array('userId'),$options)) {
			return false;
	    } else {
			$this->db->where('userId', $options['userId']);	    
	    }

		if(isset($options['userEmail']))
			$this->db->set('userEmail', $options['userEmail']);
			
		if(isset($options['userPassword']))
			$this->db->set('userPassword', $options['userPassword']);

		if(isset($options['userEmailVerified']))
			$this->db->set('userEmailVerified', $options['userEmailVerified']);
			
		if(isset($options['userPasswordToken'])) {
			$this->db->set('userPasswordToken', $options['userPasswordToken']);
			$onedayfromnow = time() + 86400;
			$this->db->set('userPasswordTokenExpire', $onedayfromnow);	
		}
			
		$this->db->update('users');		
		return $this->db->affected_rows();
	}
	
	/**
	 * GetUsers method returns a qualified list of users from the users table
	 * 
	 * Options: Values
	 * ---------------
	 * userId
	 * userEmail
	 * userPassword
	 * userStatus
	 * limit			limit the returned records
	 * offset			bypass this many records
	 * sortBy			sort by this column
	 * sortDirection	(asc, desc)
	 * 
	 * Returned Object (array of)
	 * --------------------------
	 * userId
	 * userEmail
	 * userPassword
	 * userStatus
	 * 
	 * @param array $options 
	 * @return array of objects
	 * 
	 */
	function GetUsers($options = array())
	{
		// Qualification
		if(isset($options['userId']))
			$this->db->where('userId', $options['userId']);
		if(isset($options['userEmail']))
			$this->db->where('userEmail', $options['userEmail']);
		if(isset($options['userPassword']))
			$this->db->where('userPassword', $options['userPassword']);
		if(isset($options['userName']))
			$this->db->where('userName', $options['userName']);			
		if(isset($options['userStatus']))
			$this->db->where('userStatus', $options['userStatus']);
		if(isset($options['userToken']))
			$this->db->where('userToken', $options['userToken']);
		if(isset($options['userPasswordToken']))
			$this->db->where('userPasswordToken', $options['userPasswordToken']);			
		// limit / offset
		if(isset($options['limit']) && isset($options['offset']))
			$this->db->limit($options['limit'], $options['offset']);
		else if(isset($options['limit']))
			$this->db->limit($options['limit']);
			
		// sort
		if(isset($options['sortBy']) && isset($options['sortDirection']))
			$this->db->order_by($options['sortBy'], $options['sortDirection']);
			
		$query = $this->db->get("users");
		
		if(isset($options['userId']) || isset($options['userEmail']) || isset($options['userName']) || isset($options['userToken']) || isset($options['userPasswordToken']))
			return $query->row(0);
			
		return $query->result();
	}
	
	/** authentication methods **/
	
	/**
	 * The login method adds user information from the database to session data.
	 * 
	 * Option: Values
	 * --------------
	 * userEmail
	 * userPassword
	 *
	 * @param array $options
	 * @return object result()
	 */
	function Login($options = array())
	{
		// required values
		if(!$this->_required(
			array('userEmail', 'userPassword'),
			$options)
		) return false;
		
		$user = $this->GetUsers(array('userEmail' => $options['userEmail'], 'userPassword' => md5($options['userPassword'])));
		if(!$user) return false;
		
		$this->session->set_userdata('userEmail', $user->userEmail);
		$this->session->set_userdata('userId', $user->userId);
		
		return true;
	}
	
	function Auth()
	{
		if ($this->session->userdata('userId')) {
			return true;
		} else {
			$this->session->set_userdata('return', current_url());
			return false;
		}
	}
}

