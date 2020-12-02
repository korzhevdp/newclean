<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Processes extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->helper("url");
		$this->load->model("mailmodel");
		$this->load->model("usermodel");
		$this->load->model("messagemodel");
		$this->load->model("processmodel");
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

	public function show($processID=0) {
		$this->startPage($processID);
	}

	public function setinitialprogress($messageID) {
		$this->processmodel->setInitialProgress($messageID);
		redirect("management/viewmessage/".$messageID);
	}

	private function getAvailableFunctions() {
		$output = array();
		$result = $this->db->query("SELECT 
		`processTasks`.name,
		`processTasks`.id
		FROM
		`processTasks`
		ORDER BY `processTasks`.id");
		if ( $result->num_rows() ) {
			foreach ( $result->result() as $row ) {
				array_push($output, '<li ref="'.$row->id.'" class="processTaskItem" title="Щелчок добавит задачу в процесс">#'.$row->id.' '.$row->name.'</li>');
			}
		}
		return implode($output, "\n\t\t\t");
	}

	private function getProcessesTasks() {
		$output = array();
		$result = $this->db->query("SELECT
		`processes`.`function`,
		`processes`.id,
		`processes`.`order`
		FROM
		`processes`
		ORDER BY `processes`.`order`", array() );
		if ( $result->num_rows() ) {
			foreach ( $result->result() as $row ) {
				if (!isset($output[$row->id])) {
					$output[$row->id] = array();
				}
				$output[$row->id][$row->order] = $row->function;
			}
		}
		$out = array();
		foreach ( $output as $process=>$tasks ) {
			array_push($out, $process.": [".implode($tasks, ",")."]");
		}
		return "{\n\t\t".implode($out, ",\n\t\t")."\n\t};";
	}

	private function startPage($process=0) {
		$availableFunctions = $this->getAvailableFunctions();
		$categoryList       = $this->messagemodel->getDropdownList('categories', $process);
		$processesTasks     = $this->getProcessesTasks();
		$data = array(
			'menu'       => $this->load->view("management/menu", array(), true),
			'content'    => $this->load->view("management/processes/composer", array( 
				'functions'      => $availableFunctions,
				'categoryList'   => $categoryList,
				'processesTasks' => $processesTasks
			), true),
			'requestUrl' => "",
			'header'     => "Управление рабочими процессами"
		);
		$this->load->view("management/container", $data);
	}
	/* содержимое процесса сохраняется в базу */
	public function saveprocess() {
		if (!$this->input->post('category')) {
			return false;
		}
		$this->db->query("DELETE FROM `processes` WHERE `processes`.id = ?", array($this->input->post('category')));
		$output = array();
		foreach ( $this->input->post('tasks') as $taskID) {
			array_push($output, "(".(int) $this->input->post('category').", ".$taskID.", ".sizeof($output).")");
		}
		$this->db->query("INSERT INTO `processes`( id, `function`, `order`) VALUES ".implode($output, ", ") );
	}

	public function processMessage($messageID) {
		$this->processmodel->processMessage($messageID);
	}

	public function clearProgress($messageID = 0) {
		if ( !$messageID ) {
			return false;
		}
		/* проверка на заброс данных в СЭД. Отказ в случае передачи дела */
		$result = $this->db->query("SELECT
		`messageProgress`.id
		FROM
		`messageProgress`
		WHERE `messageProgress`.`messageID` = ?
		AND   `messageProgress`.`stage`     = 9
		AND   `messageProgress`.`state`     = 1", array($messageID) );
		if ( $result->num_rows() ) {
			return false;
		}
		$this->db->query( "DELETE FROM `message_answers` WHERE `message_answers`.user_id = ? AND `message_answers`.message_id = ?", array($this->session->userdata("UID"), $messageID) );
		$this->db->query( "DELETE FROM `action_history` WHERE `action_history`.userID = ? AND `action_history`.messageID = ?", array($this->session->userdata("UID"), $messageID) );
		$this->db->query( "UPDATE `messages` SET `messages`.status_id = 4, `messages`.taskValid = 0, `messages`.executiveValid = 0, `messages`.controllerValid = 0 WHERE `messages`.id = ?", array($messageID) );
		$this->db->query( "UPDATE
		`messageProgress`
		SET
		`messageProgress`.`date` = NOW(),
		`messageProgress`.state = 0
		WHERE
		`messageProgress`.`messageID` = ?
		AND `messageProgress`.`stage` NOT IN (1, 2, 9, 10, 13)", array($messageID) );

	}

}