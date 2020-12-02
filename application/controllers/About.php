<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class About extends CI_Controller {

	public function index() {
		$data = array(
			'metrika'    => $this->load->view("usermode/metrika", array(), true),
			'content'    => $this->load->view("usermode/about", array(), true)
		);
		$this->load->view("usermode/container", $data);
	}
	
	public function policy() {
		$this->load->view("usermode/policy");
	}

}
