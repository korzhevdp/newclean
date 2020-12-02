<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->helper("url");
		$this->load->model("usermodel");
		$this->load->model("typemodel");
		$this->load->model("messagemodel");
		$this->load->model("processmodel");
		$this->load->model("mailmodel");
		$this->authorizationCheck();
	}

	private function authorizationCheck() {
		$this->session->set_userdata('force_redirect', str_replace("/content", "", uri_string()));
		if (!$this->usermodel->isAuthorized()) {
			redirect("login");
		}
		return true;
	}

	public function index() {
		$this->startPage();
	}

	public function appeal( $messageID ) {
		$this->startPage( $messageID );
	}


	private function startPage($messageID = 0) {
		$userdata     = $this->session->userdata();
		$rights       = $userdata['rights'];
		$data         = array(
			'content'       => "",
			'districts'     => $this->messagemodel->getDropdownList('districts'),
			'categories'    => $this->messagemodel->getDropdownList('categories'),
			'organizations' => $this->messagemodel->getDropdownList('organizations'),
			'departments'   => $this->messagemodel->getDropdownList('departments'),
			'statii'        => $this->messagemodel->getDropdownList('statii'),
			'userdata'      => $userdata,
			'messageID'     => $messageID,
			'messages'      => $this->messagemodel->getMessagesForAdmin()
		);
		$this->load->view("adminmode/main", $data);
	}

	public function getFilteredMessages() {
		$conditions = array();
		$values = array();
		if ( $this->input->post("categoryID") ) {
			array_push($conditions, "messages.category_id = ?");
			array_push($values, $this->input->post("categoryID"));
		}
		if ( $this->input->post("districtID") ) {
			array_push($conditions, "messages.district_id = ?");
			array_push($values, $this->input->post("districtID"));
		}
		if ( $this->input->post("departmentID") ) {
			array_push($conditions, "messages.depart_id = ?");
			array_push($values, $this->input->post("departmentID"));
		}
		if ( $this->input->post("organizationID") ) {
			array_push($conditions, "messages.org_id = ?");
			array_push($values, $this->input->post("organizationID"));
		}
		if ( $this->input->post("statusID") ) {
			array_push($conditions, "messages.status_id = ?");
			array_push($values, $this->input->post("statusID"));
		}
		$result = $this->db->query( "SELECT
		messages.address,
		messages.coord_x,
		messages.coord_y,
		messages.id,
		IF(LENGTH(messages.message) > 150, CONCAT(LEFT(messages.message, 147), '...'), messages.message) AS message,
		message_category.name AS categoryName
		FROM
		messages
		LEFT OUTER JOIN `message_category` ON (messages.category_id = `message_category`.id)
		".((sizeof($conditions)) ? "\n\t\tWHERE\n\t\t".implode($conditions, "\n\t\tAND "): "")."
		ORDER BY
		messages.id DESC
		LIMIT 300", $values );
		if ( $result->num_rows() ) {
			$output = array();
			foreach ( $result->result() as $row ) {
				$string = '<div class="messageItem" cx="'.$row->coord_x.'" cy="'.$row->coord_y.'" ref="'.$row->id.'">'.$row->categoryName.'<br>'.$row->message.'<br>'.$row->address.'</div>';
				array_push($output, $string);
			}
			print implode($output, "\n");
			return true;
		}
		print "Ничего не найдено";
	}

	public function getAdmMessageDetails($messageID) {
		$result = $this->messagemodel->getMessageDetailsData($messageID, 'adminmode');

		if ( $result->num_rows() ) {
			$row = $result->row_array();
			//print_r( $row );
			$row['header']           = "";
			$row['organizationList'] = $this->messagemodel->getDropdownList('organizations', $row['organizationID']);
			$row['controlList']      = $this->messagemodel->getDropdownList('departments',   $row['controlID']);
			$row['statusList']       = $this->messagemodel->getDropdownList('statii',        $row['statusID']);
			$row['files']            = $this->messagemodel->makeImagesList($row['files']);
			
			//exit;
			$this->session->set_userdata("messageID", $messageID);
			$this->load->view("adminmode/messagedetails", $row);
			return true;
		}
	}
	
	public function saveadmmessagedetails() {
		if ( (int) $this->input->post("messageID") != (int) $this->session->userdata("messageID") ) {
			print 0;
			return false;
		}
		$this->db->query("UPDATE
		`messages`
		SET
		`messages`.depart_id   = ?,
		`messages`.org_id      = ?,
		`messages`.status_id   = ?,
		`messages`.archive     = ?,
		`messages`.update_time = NOW()
		WHERE
		`messages`.id          = ?", array(
			$this->input->post("controlID"),
			$this->input->post("organizationID"),
			$this->input->post("statusID"),
			$this->input->post("archive"),
			$this->input->post("messageID")
		));
		print $this->db->affected_rows();
	}

	public function setValidateInfoOnAMessage() {
		$this->db->query("UPDATE
		messages
		SET
		messages.taskValid   = ?,
		messages.update_time = NOW()
		WHERE
		(messages.id         = ?)", array(
			$this->input->post('validness'),
			$this->input->post('messageID')
		));
		if ($this->db->affected_rows()){
			$this->db->query("INSERT INTO
			`message_answers`(
				`message_answers`.state,
				`message_answers`.message_id,
				`message_answers`.user_id,
				`message_answers`.answer,
				`message_answers`.`datetime`
			) VALUES ( 'validation', ?, ? , ?, NOW())", array(
				$this->input->post('messageID'),
				$this->session->userdata('UID'),
				$this->input->post('text')
			));
			$this->processmodel->processMessage($this->input->post('messageID'));
		}
	}

	public function setSubcategory() {
		$this->db->query("UPDATE
		messages
		SET
		messages.subcategoryID = ?,
		messages.update_time   = NOW()
		WHERE
		(messages.id           = ?)", array(
			$this->input->post('subcategory'),
			$this->input->post('messageID')
		));
		$this->processmodel->processMessage($this->input->post('messageID'));
	}

	public function validateOrganization() {
		$this->db->query("UPDATE
		messages
		SET
		messages.org_id         = ?,
		messages.executiveValid = 1,
		messages.update_time    = NOW()
		WHERE
		(messages.id           = ?)", array(
			$this->input->post('organizationID'),
			$this->input->post('messageID')
		));
		$this->processmodel->processMessage($this->input->post('messageID'));
	}

	public function validateController() {
		$this->db->query("UPDATE
		messages
		SET
		messages.depart_id         = ?,
		messages.controllerValid = 1,
		messages.update_time    = NOW()
		WHERE
		(messages.id           = ?)", array(
			$this->input->post('controllerID'),
			$this->input->post('messageID')
		));
		$this->processmodel->processMessage($this->input->post('messageID'));
	}

}
