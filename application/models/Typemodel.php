<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Typemodel extends CI_Model {

	public function getHeaderContent() {
		return $this->load->view("usermode/header", array('authorized' => $this->usermodel->isAuthorized()), true);
	}

}