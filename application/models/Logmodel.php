<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Logmodel extends CI_Model {

	public function writeToLog( $messageID, $text ) {
		$result = $this->db->query("INSERT INTO
		`action_history`(
			`action_history`.`userID`,
			`action_history`.`messageID`,
			`action_history`.`userIP`,
			`action_history`.`comment`,
			`action_history`.`time`
		) VALUES ( ?, ?, ?, ?, NOW() )", array(
			$this->session->userdata("UID"),
			$messageID,
			$this->input->ip_address(),
			$text,
		));
		if ( $this->db->affected_rows() ) {
			return true;
		}
		return false;
	}
}