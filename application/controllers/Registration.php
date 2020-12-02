<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Registration extends CI_Controller {

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
		print $this->load->view("usermode/userregistration", array('header' => $this->typemodel->getHeaderContent()), true);
	}

	private function startPage() {
		$data = array(
			'metrika'    => $this->load->view("usermode/metrika", array(), true),
			'content'    => "",
			'requestUrl' => (current_url() == base_url()) ? base_url()."welcome/content" : current_url()."/content"
		);
		$this->load->view("usermode/container", $data);
	}

	public function newUser() {
		$result = $this->db->query("INSERT INTO
		`users`(
			`users`.firstname,
			`users`.secondname,
			`users`.lastname,
			`users`.email,
			`users`.alias,
			`users`.phone,
			`users`.pass_part,
			`users`.password,
			`users`.reg_date,
			`users`.group_id,
			`users`.department_id,
			`users`.org_id,
			`users`.activity
		) VALUES( ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, 0 )", array(
			$this->input->post('firstname', true),
				$this->input->post('secondname', true),
				$this->input->post('lastname', true),
				$this->input->post('email', true),
				$this->input->post('alias', true),
				$this->input->post('phone', true),
				$this->input->post('password', true),
				$this->input->post('group_id', true),
				$this->input->post('department_id', true),
				$this->input->post('org_id', true),) );
		if ( $result->num_rows() ) {
			foreach ( $result->result() as $row ) {

			}
		}
	}

}
