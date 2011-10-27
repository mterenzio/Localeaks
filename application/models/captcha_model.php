<?php

/**
 * Captcha_Model
 * 
 * @package Captcha
 */

class Captcha_Model extends CI_Model
{

	function GetCaptcha($id = null)
	{
		if ($id == null) {
			$id = rand(1, 2);
		}
		$this->db->where('captcha_id', $id);			
		$query = $this->db->get("captcha");
		return $query->row(0);
	}
	
}

