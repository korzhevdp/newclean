<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->helper("url");
		$this->load->model("usermodel");
		$this->load->model("logmodel");
		$this->load->model("typemodel");
	}

	public function index() {
		$this->startPage();
	}

	public function content() {
		print $this->load->view("usermode/login", array('header' => $this->typemodel->getHeaderContent(), "error" => ""), true);
	}

	private function startPage() {
		$data = array(
			'metrika'    => $this->load->view("usermode/metrika", array(), true),
			'content'    => $this->load->view("usermode/login", array('header' => $this->typemodel->getHeaderContent(), "error" => ""), true),
			'requestUrl' => (current_url() == base_url()) ? base_url()."welcome/content" : current_url()."/content"
		);
		$this->load->view("usermode/container", $data);
	}

	public function authenticate() {
		$authResult = $this->usermodel->AuthUser( $this->input->post() );
		if ($authResult['status']) {
			$this->session->unset_userdata("force_redirect");
		}
		print json_encode($authResult);
	}

	public function passwordrestore() {
		//TODO:
		$output = array();

	}

	public function changepassword() {
		$part = $this->usermodel->generatePassPart();
		if ( $this->input->post('newPassword') !== $this->input->post('newPasswordCheck')) {
			return false;
		}
		$result = $this->db->query("SELECT
		`users`.id
		FROM
		`users`
		WHERE
		`users`.`password` = MD5(CONCAT(`users`.`pass_part`, MD5(?)))", array(
			$this->input->post('currentPassword'))
		);
		if ( $result->num_rows() ) {
			$row = $result->row();
			if ( $row->id == $this->session->userdata("UID")) {
				$this->db->query("UPDATE
				`users`
				SET
				`users`.`password`  = ?,
				`users`.`pass_part` = ?
				WHERE `users`.`id`  = ?", array(
					$this->usermodel->preparePasswordString($part, $this->input->post('newPassword')),
					$part,
					$this->session->userdata("UID")
				));
				$this->logout();
				return true;
			}
			return false;
		}
		return false;
	}

	public function logout() {
		$this->session->sess_destroy();
		redirect("/");
	}
}