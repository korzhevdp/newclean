<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->helper("url");
		$this->load->model("usermodel");
		$this->load->model("typemodel");
	}

	public function index() {
		$this->startPage();
	}

	public function content() {
		$this->load->view("usermode/start", array('header' => $this->typemodel->getHeaderContent()));
	}

	private function startPage() {
		$data = array(
			'metrika'    => $this->load->view("usermode/metrika", array(), true),
			'content'    => "",
			'requestUrl' => (current_url() == base_url()) ? base_url()."welcome/content" : current_url()."content"
		);
		$this->load->view("usermode/container", $data);
	}

	

}
