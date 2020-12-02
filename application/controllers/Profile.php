<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->helper("url");
		$this->load->model("usermodel");
		$this->load->model("logmodel");
		$this->load->model("typemodel");
		$this->authorizationCheck();
	}

	private function authorizationCheck() {
		$this->session->set_userdata('force_redirect', uri_string());
		if (!$this->usermodel->isAuthorized()) {
			redirect("login");
		}
		return true;
	}


	public function index() {
		$this->startPage();
	}

	public function content() {
		$result = $this->db->query("SELECT 
		`users`.firstname,
		`users`.secondname,
		`users`.lastname,
		`users`.email,
		`users`.phone,
		`users`.alias
		FROM
		`users`
		WHERE users.id = ?
		LIMIT 1", array($this->session->userdata("UID")) );
		if ( $result->num_rows() ) {
			$row = $result->row_array();
			$row['header'] = $this->typemodel->getHeaderContent();
			$row['error']  = "";
		}
		print $this->load->view("usermode/profile", $row, true);
	}

	public function saveprofile() {
		$this->db->query("UPDATE
		`users`
		SET
		`users`.firstname  = TRIM(?),
		`users`.secondname = TRIM(?),
		`users`.lastname   = TRIM(?),
		`users`.phone      = TRIM(?),
		`users`.alias      = TRIM(?)
		WHERE users.id     = ?", array(
			$this->input->post("firstname", true),
			$this->input->post("secondname", true),
			$this->input->post("lastname", true),
			$this->input->post("phone", true),
			$this->input->post("alias", true),
			$this->session->userdata("UID")
		) );
		$this->session->set_userdata("alias", $this->input->post("alias", true));
		print $this->db->affected_rows();
	}

	private function startPage() {
		$data = array(
			'metrika'    => $this->load->view("usermode/metrika", array(), true),
			'content'    => "",
			'requestUrl' => (current_url() == base_url()) ? base_url()."welcome/content" : current_url()."/content"
		);
		$this->load->view("usermode/container", $data);
	}

}