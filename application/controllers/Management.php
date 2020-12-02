<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Management extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->helper("url");
		$this->load->model("usermodel");
		$this->load->model("messagemodel");
		$this->load->model("typemodel");
		$this->load->model("processmodel");
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

	private function statBoard() {
		
		//`messages`.`create_time` > DATE('2020-9-1') 
		$result = $this->db->query("SELECT 
		COUNT(messages.id) AS totalMessages,
		(SELECT COUNT(`messages`.`id`) FROM `messages` WHERE `messages`.`status_id` = 2) AS finalized,
		(SELECT COUNT(`messages`.`id`) FROM `messages` WHERE `messages`.`status_id` = 4) AS inProgress,
		(SELECT COUNT(`messages`.`id`) FROM `messages` WHERE `messages`.`status_id` IN(8,9)) AS declined,
		(SELECT COUNT(`messages`.`id`) FROM `messages` WHERE `messages`.`status_id` = 6) AS `new`,
		(SELECT COUNT(`users`.id) FROM `users`) AS totalUsers,
		(SELECT COUNT(`messages`.user_id) FROM `messages` WHERE `messages`.`create_time` > DATE_SUB(NOW(), INTERVAL 1 YEAR)) AS activeUsers,
		(SELECT COUNT(`users`.id) FROM `users` WHERE `users`.`reg_date` > DATE_SUB(NOW(), INTERVAL 1 YEAR)) AS `newUsers`
		FROM messages");
		if ( $result->num_rows() ) {
			return $result->row_array();
		}
		return array(
			"totalMessages" => 0,
			"finalized"     => 0,
			"inProgress"    => 0,
			"declined"      => 0,
			"new"           => 0,
			"totalUsers"    => 0,
			"activeUsers"   => 0,
			"newUsers"      => 0
		);
	}

	private function startPage() {

		$data = array(
			'menu'       => $this->load->view("management/menu", array(), true),
			'content'    => $this->load->view("management/dashboard", $this->statBoard(), true),
			'requestUrl' => "",
			'header'     => "Статистика Чистого Города"
		);
		$this->load->view("management/container", $data);
	}

	private function makeBreadcrumbsNav($userCount, $show = 100, $url) {
		$show = ( (int) $show ) ? $show : 100;
		$pages = ceil($userCount / $show);
		$output = array();
		for ($a = 1; $a <= $pages; $a++){
			array_push($output, '<span><a href="'.$url.'/'.$a.'/'.$show.'">'.$a.'</a></span>');
		}
		return implode($output, "&nbsp;&nbsp;");
	}

	##### USERS

	public function saveuser() {
		$this->db->query( "UPDATE
		`users`
		SET
		`users`.alias         = TRIM(?),
		`users`.email         = TRIM(?),
		`users`.phone         = TRIM(?),
		`users`.group_id      = ?,
		`users`.department_id = ?,
		`users`.org_id        = ?,
		`users`.activity      = ?
		WHERE
		`users`.id            = ?", array(
			htmlspecialchars($this->input->post("alias")),
			$this->input->post("email"),
			$this->input->post("phone"),
			$this->input->post("groupID"),
			$this->input->post("departmentID"),
			$this->input->post("organizationID"),
			$this->input->post("active"),
			$this->input->post("id")
		));
		print $this->db->affected_rows();
	}

	private function getUserList($page = 1, $show = 100) {
		$output = array();
		$groupList         = $this->messagemodel->getListResult('usergroups');
		$organizationsList = $this->messagemodel->getListResult('organizations');
		$departmentsList   = $this->messagemodel->getListResult('departments');
		$result = $this->db->query( "SELECT 
		`users`.id,
		`users`.email,
		`users`.alias,
		`users`.phone,
		`users`.department_id								AS `departmentID`,
		`users`.group_id									AS `groupID`,
		`users`.org_id										AS `organizationID`,
		`users`.activity									AS `active`,
		DATE_FORMAT(`users`.reg_date, '%d.%m:%Y')			AS `registrationDate`,
		DATE_FORMAT(`users`.auth_date, '%d.%m:%Y %H:%i:%s')	AS `authDate`,
		(SELECT COUNT(`users`.id) FROM `users`)				AS `userCount`
		FROM
		`users`
		ORDER BY FIELD(`users`.group_id,3,7,4,6,5,2,1), `users`.alias
		LIMIT ?, ? ", array(
			( $page - 1 ) * $show,
			(int) $show
		));
		if ( $result->num_rows() ) {
			foreach ( $result->result_array() as $row ) {
				$row['groupList']         = $this->messagemodel->makeDropdownList($groupList, $row['groupID']);
				$row['departmentsList']   = $this->messagemodel->makeDropdownList($departmentsList, $row['departmentID']);
				$row['organizationsList'] = $this->messagemodel->makeDropdownList($organizationsList, $row['organizationID']);
				$row['executive']         = ($row['groupID'] != "1")  ? "executive" : "";
				$row['activeSW']          = ($row['active'])          ? " checked"  : "";
				$row['disabled']          = ($row['groupID'] === "1") ? " disabled" : "";
				$string = $this->load->view("management/tablerows/userlistrow", $row, true);
				array_push($output, $string);
			}
		}
		$pageList = $this->makeBreadcrumbsNav($row['userCount'], $show, "/management/users");
		return $this->load->view("management/usertable", array( 'table' => implode($output, "\n\t\t\t"), 'bcrumbs' => $pageList ), true);
	}

	public function users($page = 1, $show = 100) {
		//$this->output->enable_profiler(true);
		$output = array();
		$data = array(
			'menu'       => $this->load->view("management/menu", array(), true),
			'content'    => $this->getUserList($page, $show),
			'requestUrl' => "",
			'header'     => "Пользователи системы"
		);
		$this->load->view("management/container", $data);
	}

	##### USERGROUPS +

	private function getEmptyUserGroup(){
		return array(
			'id'      => 0,
			'name'    => "Добавить группу",
			'caption' => "",
			'law1'    => 0,
			'law2'    => 0,
			'law3'    => 0,
			'law4'    => 0,
			'law8'    => 0,
			'law5'    => 0,
			'law6'    => 0,
			'law7'    => 0,
			'law9'    => 0,
			'law10'   => 0
		);
	}

	private function getUserGroupRow($row) {
		$row['rights'] = array(
			'law1'  => 'Просмотр обращений',
			'law2'  => 'Просмотр модерированных обращений',
			'law3'  => 'Смена статуса обращений',
			'law4'  => 'Назначение организации',
			'law8'  => 'Назначение срока выполнения',
			'law5'  => 'Архивация обращения',
			'law6'  => 'Просмотр статистики сообщений',
			'law7'  => 'Администрирование',
			'law9'  => 'Системные настройки',
			'law10' => 'Назначение департамента'
		);
		$row['buttonLabel'] = ($row['id']) ? "Изменить" : "+&nbsp;Добавить";
		return $this->load->view("management/tablerows/usergrouprow", $row, true);
	}

	private function getUserGroupsList() {
		$output = array();
		$result = $this->db->query( "SELECT 
		`user_groups`.id,
		`user_groups`.name,
		`user_groups`.caption,
		`user_groups`.law1,
		`user_groups`.law2,
		`user_groups`.law3,
		`user_groups`.law4,
		`user_groups`.law4_1 AS `law10`,
		`user_groups`.law8,
		`user_groups`.law5,
		`user_groups`.law6,
		`user_groups`.law7,
		`user_groups`.law9
		FROM
		`user_groups`", array() );
		if ( $result->num_rows() ) {
			foreach ( $result->result_array() as $row ) {
				array_push($output, $this->getUserGroupRow($row));
			}
		}
		array_push($output, $this->getUserGroupRow($this->getEmptyUserGroup()));
		return $this->load->view("management/usergroupstable", array('table' => implode($output, "\n\t\t\t")), true);
	}

	private function updateUserGroup() {
		$this->db->query( "UPDATE
		`user_groups`
		SET
		`user_groups`.name    = TRIM(?),
		`user_groups`.caption = TRIM(?),
		`user_groups`.law1    = ?,
		`user_groups`.law2    = ?,
		`user_groups`.law3    = ?,
		`user_groups`.law4    = ?,
		`user_groups`.law4_1  = ?,
		`user_groups`.law5    = ?,
		`user_groups`.law6    = ?,
		`user_groups`.law7    = ?,
		`user_groups`.law8    = ?,
		`user_groups`.law9    = ?
		WHERE
		`user_groups`.id      = ?", array(
			htmlspecialchars($this->input->post("name")),
			htmlspecialchars($this->input->post("caption")),
			$this->input->post("law1"),
			$this->input->post("law2"),
			$this->input->post("law3"),
			$this->input->post("law4"),
			$this->input->post("law4_1"),
			$this->input->post("law5"),
			$this->input->post("law5"),
			$this->input->post("law7"),
			$this->input->post("law8"),
			$this->input->post("law9"),
			$this->input->post("id")
		));
	}

	private function createUserGroup() {
		$this->db->query( "INSERT INTO
		`user_groups` (
		`user_groups`.name,
		`user_groups`.caption,
		`user_groups`.law1,
		`user_groups`.law2,
		`user_groups`.law3,
		`user_groups`.law4,
		`user_groups`.law4_1,
		`user_groups`.law5,
		`user_groups`.law6,
		`user_groups`.law7,
		`user_groups`.law8,
		`user_groups`.law9
		) VALUES ( TRIM(?), TRIM(?), ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )", array(
			htmlspecialchars($this->input->post("name")),
			htmlspecialchars($this->input->post("caption")),
			$this->input->post("law1"),
			$this->input->post("law2"),
			$this->input->post("law3"),
			$this->input->post("law4"),
			$this->input->post("law4_1"),
			$this->input->post("law5"),
			$this->input->post("law5"),
			$this->input->post("law7"),
			$this->input->post("law8"),
			$this->input->post("law9")
		));
	}

	public function usergroups() {
		$output = array();
		$data = array(
			'menu'       => $this->load->view("management/menu", array(), true),
			'content'    => $this->getUserGroupsList(),
			'requestUrl' => "",
			'header'     => "Группы пользователей"
		);
		$this->load->view("management/container", $data);
	}

	public function saveusergroup() {
		if ($this->input->post("id")) {
			$this->updateUserGroup();
			print $this->db->affected_rows();
			return true;
		}
		$this->createUserGroup();
		print $this->db->affected_rows();
	}

	##### ORGANIZATIONS

	private function getEmptyOrganization() {
		return array(
			"id"             => 0,
			"name"           => "Новая организация",
			"full_name"      => "",
			"address"        => "",
			"inn"            => "",
			"phone"          => "",
			"email"          => "",
			"boss"           => "",
			"house_count"    => "",
			"personal_count" => "",
			"activity"       => 0,
			"department"     => 0,
			'ifDepartment'   => "",
			'departmentSW'   => "",
			'activeSW'       => ""
		);
	}

	private function getOrganizationRow($row) {
		$row['ifDepartment'] = ($row['department']) ? " departmentColor" : "";
		$row['departmentSW'] = ($row['department']) ? " checked" : "";
		$row['activeSW']     = ($row['activity'])   ? " checked" : "";
		$row['buttonLabel']  = ($row['id'])         ? "Изменить" : "+&nbsp;Добавить";
		return $this->load->view("management/tablerows/organizationrow", $row, true);
	}

	private function getOrganizationsList() {
		$output = array();
		$result = $this->db->query( "SELECT 
		`organization`.id,
		`organization`.name,
		`organization`.full_name,
		`organization`.address,
		`organization`.inn,
		`organization`.phone,
		`organization`.email,
		`organization`.boss,
		`organization`.house_count,
		`organization`.personal_count,
		`organization`.activity,
		`organization`.department
		FROM
		`organization` 
		ORDER BY `organization`.department DESC, `organization`.name");
		if ( $result->num_rows() ) {
			foreach ( $result->result_array() as $row ) {
				array_push($output, $this->getOrganizationRow($row));
			}
		}
		array_push($output, $this->getOrganizationRow($this->getEmptyOrganization()));
		return $this->load->view("management/organizationstable", array( 'table' => implode($output, "\n\t\t\t")), true);
	}

	public function saveorganization() {
		if ($this->input->post("id")) {
			$this->updateOrganization();
			print $this->db->affected_rows();
			return true;
		}
		$this->createOrganization();
		print $this->db->affected_rows();
	}

	private function updateOrganization() {
		$this->db->query( "UPDATE
		`organization`
		SET
		`organization`.name           = TRIM(?),
		`organization`.full_name      = TRIM(?),
		`organization`.address        = TRIM(?),
		`organization`.inn            = TRIM(?),
		`organization`.phone          = TRIM(?),
		`organization`.email          = TRIM(?),
		`organization`.boss           = TRIM(?),
		`organization`.house_count    = TRIM(?),
		`organization`.personal_count = TRIM(?),
		`organization`.date_update    = NOW(),
		`organization`.activity       = ?,
		`organization`.department     = ?
		WHERE 
		`organization`.id             = ?", array(
			htmlspecialchars($this->input->post("name")),
			htmlspecialchars($this->input->post("full_name")),
			$this->input->post("address"),
			$this->input->post("inn"),
			$this->input->post("phone"),
			$this->input->post("email"),
			$this->input->post("boss"),
			$this->input->post("house_count"),
			$this->input->post("personal_count"),
			$this->input->post("active"),
			$this->input->post("department"),
			$this->input->post("id")
		));
	}

	private function createOrganization() {
		$this->db->query( "INSERT INTO
		`organization` (
			`organization`.name,
			`organization`.full_name,
			`organization`.address,
			`organization`.inn,
			`organization`.phone,
			`organization`.email,
			`organization`.boss,
			`organization`.house_count,
			`organization`.personal_count,
			`organization`.date_update,
			`organization`.activity,
			`organization`.department
		) VALUES ( TRIM(?), TRIM(?), TRIM(?), TRIM(?), TRIM(?), TRIM(?), TRIM(?), TRIM(?), TRIM(?), NOW(), ?, ? )", array(
			htmlspecialchars($this->input->post("name")),
			htmlspecialchars($this->input->post("full_name")),
			$this->input->post("address"),
			$this->input->post("inn"),
			$this->input->post("phone"),
			$this->input->post("email"),
			$this->input->post("boss"),
			$this->input->post("house_count"),
			$this->input->post("personal_count"),
			$this->input->post("active"),
			$this->input->post("department")
		));
	}

	public function organizations() {
		$output = array();
		$data = array(
			'menu'       => $this->load->view("management/menu", array(), true),
			'content'    => $this->getOrganizationsList(),
			'requestUrl' => "",
			'header'     => "Организации"
		);
		$this->load->view("management/container", $data);
	}

	##### MESSAGE CATEGORIES

	private function getEmptyMessageCategory() {
		return array(
			"id"             => 0,
			"name"           => "Новая категория",
			"caption"        => "",
			"description"    => "",
			"deadline"       => "",
			"icon"           => "",
			"yandex_icon"    => "",
			"departmentID"   => 0,
			"organizationID" => 0,
			"districtID"     => 0,
			"active"         => 0
		);
	}

	private function getMessageCategoryRow($row, $districtsList, $organizationsList, $departmentsList) {
		$row['departmentsList']   = $this->messagemodel->makeDropdownList($departmentsList, $row['departmentID']);
		$row['organizationsList'] = $this->messagemodel->makeDropdownList($organizationsList, $row['organizationID']);
		$row['districtsList']     = $this->messagemodel->makeDropdownList($districtsList, $row['districtID']);
		$row['buttonLabel']       = ($row['id'])     ? "Изменить" : "+&nbsp;Добавить";
		$row['activeSW']          = ($row['active']) ? " checked" : "";
		$row['inactiveClass']     = ($row['active']) ? ""         : " inactive";
		return $this->load->view("management/tablerows/messagecategoryrow", $row, true);
	}

	private function getMessageCategoriesList() {
		$output = array();
		$districtsList     = $this->messagemodel->getListResult('districts');
		$organizationsList = $this->messagemodel->getListResult('organizations');
		$departmentsList   = $this->messagemodel->getListResult('departments');
		$result = $this->db->query( "SELECT 
		`message_category`.id,
		`message_category`.name,
		`message_category`.caption,
		`message_category`.description,
		`message_category`.deadline,
		`message_category`.icon,
		`message_category`.yandex_icon,
		`message_category`.depart_id	AS departmentID,
		`message_category`.org_id		AS organizationID,
		`message_category`.distr_resp	AS districtID,
		`message_category`.activity		AS active
		FROM
		`message_category`
		WHERE 
		`message_category`.parent = 0
		ORDER BY
		`message_category`.activity DESC,
		`message_category`.name");
		if ( $result->num_rows() ) {
			foreach ( $result->result_array() as $row ) {
				array_push($output, $this->getMessageCategoryRow($row, $districtsList, $organizationsList, $departmentsList));
			}
		}
		array_push($output,$this->getMessageCategoryRow($this->getEmptyMessageCategory(), $districtsList, $organizationsList, $departmentsList));
		return $this->load->view("management/messagecategories", array( 'table' => implode($output, "\n\t\t\t")), true);
	}

	private function createMessageCategory() {
		$this->db->query( "INSERT INTO
		`message_category` (
			`message_category`.name,
			`message_category`.caption,
			`message_category`.description,
			`message_category`.deadline,
			`message_category`.icon,
			`message_category`.yandex_icon,
			`message_category`.depart_id,
			`message_category`.org_id,
			`message_category`.distr_resp,
			`message_category`.activity
		) VALUES ( TRIM(?), TRIM(?),TRIM(?), ?, TRIM(?), TRIM(?), ?, ?, ?, ? )", array(
			htmlspecialchars($this->input->post("name")),
			htmlspecialchars($this->input->post("caption")),
			$this->input->post("description"),
			$this->input->post("deadline"),
			$this->input->post("icon"),
			$this->input->post("yandex_icon"),
			$this->input->post("departmentID"),
			$this->input->post("organizationID"),
			$this->input->post("districtID"),
			$this->input->post("active")
		));
		print $this->db->affected_rows();
	}

	private function updateMessageCategory() {
		$this->db->query( "UPDATE
		`message_category`
		SET
		`message_category`.name        = TRIM(?),
		`message_category`.caption     = TRIM(?),
		`message_category`.description = TRIM(?),
		`message_category`.deadline    = ?,
		`message_category`.icon        = TRIM(?),
		`message_category`.yandex_icon = TRIM(?),
		`message_category`.depart_id   = ?,
		`message_category`.org_id      = ?,
		`message_category`.distr_resp  = ?,
		`message_category`.activity    = ?
		WHERE
		`message_category`.id = ?", array(
			htmlspecialchars($this->input->post("name")),
			htmlspecialchars($this->input->post("caption")),
			$this->input->post("description"),
			$this->input->post("deadline"),
			$this->input->post("icon"),
			$this->input->post("yandex_icon"),
			$this->input->post("departmentID"),
			$this->input->post("organizationID"),
			$this->input->post("districtID"),
			$this->input->post("active"),
			$this->input->post("id")
		));
		print $this->db->affected_rows();
	}

	public function messagecategories() {
		$output = array();
		$data = array(
			'menu'       => $this->load->view("management/menu", array(), true),
			'content'    => $this->getMessageCategoriesList(),
			'requestUrl' => "",
			'header'     => "Категории обращений"
		);
		$this->load->view("management/container", $data);
	}

	public function savemessagecategory() {
		if ($this->input->post("id")) {
			$this->updateMessageCategory();
			print $this->db->affected_rows();
			return true;
		}
		$this->createMessageCategory();
		print $this->db->affected_rows();
	}

	##### SUBCATEGORIES
	public function subcategories() {
		$output = array();
		$data = array(
			'menu'       => $this->load->view("management/menu", array(), true),
			'content'    => $this->getMessageSubCategoriesList(),
			'requestUrl' => "",
			'header'     => "Подкатегории обращений"
		);
		$this->load->view("management/container", $data);
	}

	private function getEmptyMessageSubCategory() {
		return array(
			"id"             => 0,
			"parentName"     => "",
			"name"           => "Новая подкатегория",
			"caption"        => "",
			"description"    => "",
			"deadline"       => "",
			"icon"           => "",
			"yandex_icon"    => "",
			"departmentID"   => 0,
			"organizationID" => 0,
			"districtID"     => 0,
			"active"         => 1,
			"parentID"       => 0
		);
	}

	private function getMessageSubCategoryRow($row, $districtsList, $organizationsList, $departmentsList, $categoriesList) {
		$row['departmentsList']   = $this->messagemodel->makeDropdownList($departmentsList, $row['departmentID']);
		$row['organizationsList'] = $this->messagemodel->makeDropdownList($organizationsList, $row['organizationID']);
		$row['districtsList']     = $this->messagemodel->makeDropdownList($districtsList, $row['districtID']);
		$row['categoriesList']    = $this->messagemodel->makeDropdownList($categoriesList, $row['parentID']);
		$row['buttonLabel']       = ($row['id'])     ? "Изменить" : "+&nbsp;Добавить";
		$row['activeSW']          = ($row['active']) ? " checked" : "";
		$row['inactiveClass']     = ($row['active']) ? ""         : " inactive";
		return $this->load->view("management/tablerows/messagesubcategoryrow", $row, true);
	}

	private function getMessageSubCategoriesList() {
		$output = array();
		$districtsList     = $this->messagemodel->getListResult('districts');
		$organizationsList = $this->messagemodel->getListResult('organizations');
		$departmentsList   = $this->messagemodel->getListResult('departments');
		$categoriesList    = $this->messagemodel->getListResult('categories');
		$result = $this->db->query( "SELECT 
		message_category.id,
		message_category.`name`     AS `name`,
		CONCAT(`mc2`.`name`, ' / ') AS `parentName`,
		message_category.caption,
		message_category.description,
		message_category.deadline,
		message_category.icon,
		message_category.yandex_icon,
		message_category.parent     AS parentID,
		message_category.depart_id  AS departmentID,
		message_category.org_id     AS organizationID,
		message_category.distr_resp AS districtID,
		message_category.activity   AS active

		FROM
		`message_category`
		RIGHT OUTER JOIN message_category AS `mc2` ON (`mc2`.id = message_category.parent)
		WHERE
		(message_category.parent > 0)", array() );
		if ( $result->num_rows() ) {
			foreach ( $result->result_array() as $row ) {
				array_push($output, $this->getMessageSubCategoryRow($row, $districtsList, $organizationsList, $departmentsList, $categoriesList));
			}
		}
		array_push($output, $this->getMessageSubCategoryRow($this->getEmptyMessageSubCategory(), $districtsList, $organizationsList, $departmentsList, $categoriesList));
		return $this->load->view("management/messagesubcategories", array( 'table' => implode($output, "\n\t\t\t")), true);
	}

	public function savemessagesubcategory() {
		if( !$this->input->post("parentID") ) {
			return false;
		}
		if ($this->input->post("id")) {
			$this->updateMessageSubCategory();
			print $this->db->affected_rows();
			return true;
		}
		$this->createMessageSubCategory();
		print $this->db->affected_rows();
	}

	private function createMessageSubCategory() {
		$this->db->query( "INSERT INTO
		`message_category` (
			`message_category`.name,
			`message_category`.caption,
			`message_category`.description,
			`message_category`.deadline,
			`message_category`.icon,
			`message_category`.yandex_icon,
			`message_category`.depart_id,
			`message_category`.org_id,
			`message_category`.distr_resp,
			`message_category`.activity,
			`message_category`.parent
		) VALUES ( TRIM(?), TRIM(?),TRIM(?), ?, TRIM(?), TRIM(?), ?, ?, ?, ?, ? )", array(
			htmlspecialchars($this->input->post("name")),
			htmlspecialchars($this->input->post("caption")),
			$this->input->post("description"),
			$this->input->post("deadline"),
			$this->input->post("icon"),
			$this->input->post("yandex_icon"),
			$this->input->post("departmentID"),
			$this->input->post("organizationID"),
			$this->input->post("districtID"),
			$this->input->post("active"),
			$this->input->post("parentID")
		));
		print $this->db->affected_rows();
	}

	private function updateMessageSubCategory() {
		$this->db->query( "UPDATE
		`message_category`
		SET
		`message_category`.name        = TRIM(?),
		`message_category`.caption     = TRIM(?),
		`message_category`.description = TRIM(?),
		`message_category`.deadline    = ?,
		`message_category`.icon        = TRIM(?),
		`message_category`.yandex_icon = TRIM(?),
		`message_category`.depart_id   = ?,
		`message_category`.org_id      = ?,
		`message_category`.distr_resp  = ?,
		`message_category`.activity    = ?,
		`message_category`.parent      = ?
		WHERE
		`message_category`.id = ?", array(
			htmlspecialchars($this->input->post("name")),
			htmlspecialchars($this->input->post("caption")),
			$this->input->post("description"),
			$this->input->post("deadline"),
			$this->input->post("icon"),
			$this->input->post("yandex_icon"),
			$this->input->post("departmentID"),
			$this->input->post("organizationID"),
			$this->input->post("districtID"),
			$this->input->post("active"),
			$this->input->post("parentID"),
			$this->input->post("id")
		));
		print $this->db->affected_rows();
	}


	##### Status

	private function getEmptyMessageStatusRow() {
		return array(
			'id'          => 0,
			'statusName'  => 'Новый статус',
			'statusIcon'  => '',
			'styleColor'  => '',
			'webColor'    => '',
			'active'      => 1,
			'final'       => 0
		);

	}

	private function getMessageStatusRow($row) {
		$row['buttonLabel']       = ($row['id'])     ? "Изменить" : "+&nbsp;Добавить";
		$row['activeSW']          = ($row['active']) ? " checked" : "";
		$row['finalSW']           = ($row['final'])  ? " checked" : "";
		$row['inactiveClass']     = ($row['active']) ? ""         : " inactive";
		return $this->load->view("management/tablerows/statusrow", $row, true);
	}

	private function getStatiiTable() {
		$output = array();
		$result = $this->db->query("SELECT 
		`message_status`.id,
		`message_status`.name AS statusName,
		`message_status`.icon AS statusIcon,
		`message_status`.status_color AS styleColor,
		`message_status`.web_color AS webColor,
		`message_status`.activity AS active,
		`message_status`.answer_index,
		`message_status`.file_index,
		`message_status`.include_statisic,
		`message_status`.short_name,
		`message_status`.`final`
		FROM
		`message_status`
		ORDER BY
		`message_status`.activity DESC");
		if ( $result->num_rows() ) {
			foreach ( $result->result_array() as $row ) {
				array_push($output, $this->getMessageStatusRow($row));
			}
		}
		array_push($output, $this->getMessageStatusRow($this->getEmptyMessageStatusRow()));
		return $this->load->view("management/statiitable", array('statii' => implode($output, "\n\t\t\t")), true);
	}
	

	public function messagestatii() {
		$output = array();
		$data = array(
			'menu'       => $this->load->view("management/menu", array(), true),
			'content'    => $this->getStatiiTable(),
			'requestUrl' => "",
			'header'     => "Статусы сообщения"
		);
		$this->load->view("management/container", $data);
	}

	public function savemessagestatus() {
		if ($this->input->post("statusID")) {
			$this->updateMessageStatus();
			print $this->db->affected_rows();
			return true;
		}
		$this->createMessageStatus();
		print $this->db->affected_rows();
	}

	private function createMessageStatus() {
		$this->db->query( "INSERT INTO
		`message_status`(
			`message_status`.name,
			`message_status`.icon,
			`message_status`.status_color,
			`message_status`.web_color,
			`message_status`.activity,
			`message_status`.final,
			`message_status`.create_time
		) VALUES( ?, ?, ?, ?, ?, ?, NOW() )", array(
			htmlspecialchars(trim($this->input->post("statusName", true))),
			htmlspecialchars(trim($this->input->post("statusColor", true))),
			htmlspecialchars(trim($this->input->post("statusIcon", true))),
			trim($this->input->post("webColor", true)),
			(int) $this->input->post("active", true),
			(int) $this->input->post("finalization", true),
		));
		print $this->db->affected_rows();
	}

	private function updateMessageStatus() {
		$this->db->query( "UPDATE
		`message_status`
		SET
		`message_status`.name         = ?,
		`message_status`.icon         = ?,
		`message_status`.status_color = ?,
		`message_status`.web_color    = ?,
		`message_status`.activity     = ?,
		`message_status`.final        = ?
		WHERE 
		`message_status`.id           = ?", array(
			htmlspecialchars(trim($this->input->post("statusName", true))),
			htmlspecialchars(trim($this->input->post("statusIcon", true))),
			htmlspecialchars(trim($this->input->post("statusColor", true))),
			trim($this->input->post("webColor", true)),
			(int) $this->input->post("active", true),
			(int) $this->input->post("finalization", true),
			$this->input->post("statusID")
		));
		print $this->db->affected_rows();
	}
	
	##### OTHERS
	public function logs() {
		$output = array();
		$data = array(
			'menu'       => $this->load->view("management/menu", array(), true),
			'content'    => "",
			'requestUrl' => "",
			'header'     => "История работы"
		);
		$this->load->view("management/container", $data);
	}

	public function replies() {
		$output = array();
		$data = array(
			'menu'       => $this->load->view("management/menu", array(), true),
			'content'    => "",
			'requestUrl' => "",
			'header'     => "Системные сообщения"
		);
		$this->load->view("management/container", $data);
	}
	
	##### MAILEVENTS // AKA 
	
	private function getMailEventsList() {
		$output = array();
		$result = $this->db->query( "SELECT 
		`mail_events`.event_name,
		`mail_events`.subject,
		`mail_events`.`text`,
		`mail_events`.link,
		`mail_events`.link_text,
		`mail_events`.from_email,
		`mail_events`.activity,
		`mail_events`.update_time,
		`mail_events`.id
		FROM
		`mail_events`");
		if ( $result->num_rows() ) {
			foreach ( $result->result_array() as $row ) {
				$row['activeSW'] = ($row['activity']) ? " checked" : "";
				$row['buttonLabel']       = ($row['id'])     ? "Изменить" : "+&nbsp;Добавить";
				$string = $this->load->view("management/tablerows/maileventrow", $row, true);
				array_push($output, $string);
			}
			return $this->load->view("management/mailsettingstable", array( 'table' => implode($output,"\n\t\t\t")), true);
		}
		return "<tr><td colspan=7>Ничего не найдено<td></tr>";
	}

	public function mailsettings() {
		$output = array();
		$data = array(
			'menu'       => $this->load->view("management/menu", array(), true),
			'content'    => $this->getMailEventsList(),
			'requestUrl' => "",
			'header'     => "Настройки писем"
		);
		$this->load->view("management/container", $data);
	}

	public function savemessageevent() {
		$this->db->query( "UPDATE
		`mail_events`
		SET
		`mail_events`.event_name  = TRIM(?),
		`mail_events`.subject     = TRIM(?),
		`mail_events`.`text`      = TRIM(?),
		`mail_events`.link        = TRIM(?),
		`mail_events`.link_text   = TRIM(?),
		`mail_events`.from_email  = TRIM(?),
		`mail_events`.activity    = ?,
		`mail_events`.update_time = NOW()
		WHERE
		`mail_events`.id          = ?", array(
			$this->input->post("event_name"),
			$this->input->post("subject"),
			$this->input->post("text"),
			$this->input->post("link"),
			$this->input->post("link_text"),
			$this->input->post("from_email"),
			$this->input->post("active"),
			$this->input->post("id")
		));
		print $this->db->affected_rows();
	}

	private function getMessageList($page = 1, $show = 100) {
		$output = array();
		$groupList         = $this->messagemodel->getListResult('usergroups');
		$organizationsList = $this->messagemodel->getListResult('organizations');
		$departmentsList   = $this->messagemodel->getListResult('departments');
		$result = $this->db->query( "SELECT
		messages.user_id			AS userID,
		messages.id					AS messageID,
		messages.address			AS address,
		messages.status_id			AS statisID,
		DATE_FORMAT(messages.create_time, '%d.%m:%Y %H:%i')	AS createTime,
		messages.archive,
		message_status.name			AS statusName,
		message_status.web_color	AS labelColor,
		city_districts.name			AS districtName,
		message_category.name		AS categoryName,
		`users`.alias				AS userName,
		(SELECT COUNT(messages.id) FROM messages)			AS `messageCount`
		FROM
		message_category
		RIGHT OUTER JOIN messages		ON (message_category.id		= messages.category_id)
		LEFT OUTER JOIN city_districts	ON (messages.district_id	= city_districts.id)
		LEFT OUTER JOIN message_status	ON (messages.status_id		= message_status.id)
		LEFT OUTER JOIN `users`			ON (messages.user_id		= `users`.id)
		ORDER BY messages.id DESC
		LIMIT ?, ? ", array(
			( $page - 1 ) * $show,
			(int) $show
		));
		if ( $result->num_rows() ) {
			foreach ( $result->result_array() as $row ) {
				$string = '<tr style="background-color:'.$row['labelColor'].'">
					<td>'.$row['userName'].'</td>
					<td>'.$row['categoryName'].'</td>
					<td>'.$row['address'].'</td>
					<td>'.$row['districtName'].'</td>
					<td>'.$row['createTime'].'</td>
					<td><a href="/management/viewmessage/'.$row['messageID'].'" target="_blank">Открыть</a></td>
				</tr>';
				array_push($output, $string);
			}
		}
		$pageList = $this->makeBreadcrumbsNav($row['messageCount'], $show, "/management/messages");
		return $this->load->view("management/messagestable", array( 'table' => implode($output, "\n\t\t\t"), 'bcrumbs' => $pageList ), true);
	}

	public function messages($page=1, $show=100) {
		$output = array();
		$data = array(
			'menu'       => $this->load->view("management/menu", array(), true),
			'content'    => $this->getMessageList($page, $show),
			'requestUrl' => "",
			'header'     => "Список поданных обращений"
		);
		$this->load->view("management/container", $data);
	}

	public function diagram($diagram=0) {
		$output = array();
		$categories = $this->messagemodel->getDropdownList('categories', $diagram);
		$data = array(
			'menu'       => $this->load->view("management/menu", array(), true),
			'content'    => $this->load->view("management/diagram", array('categories' => $categories), true),
			'requestUrl' => "",
			'header'     => "Диаграммы прохождения заявок"
		);
		$this->load->view("management/container", $data);
	}

	private function getHistoryData($messageID) {
		$output = array();
		$result = $this->db->query( "SELECT
		`action_history`.`comment`,
		`action_history`.id,
		DATE_FORMAT(`action_history`.`time`, '%d.%m.%Y %H:%i') AS eventTime
		FROM
		`action_history`
		WHERE `action_history`.`messageID` = ?
		ORDER BY `action_history`.`time` ASC", array($messageID) );
		if ( $result->num_rows() ) {
			foreach ( $result->result() as $row ) {
				$string = "<tr><td>".$row->eventTime."</td><td>".$row->comment."</td></tr>";
				array_push($output, $string);
			}
		}
		return implode($output, "\n\t\t\t\t");
	}

	private function getCommentsData($messageID) {
		$output = array('<tr><th colspan=4>Комментарии сотрудников</th></tr>');
		$result = $this->db->query( "SELECT
		message_depart_comment.`comment`,
		DATE_FORMAT(message_depart_comment.`datetime`, '%d.%m.%Y %H:%i') AS commentDate,
		`users`.alias,
		`organization`.name AS organizationName
		FROM
		users
		RIGHT OUTER JOIN message_depart_comment ON (users.id = message_depart_comment.user_id)
		LEFT OUTER JOIN organization ON (users.org_id = organization.id)
		WHERE
		(message_depart_comment.message_id = ?)", array($messageID) );
		if ( $result->num_rows() ) {
			foreach ( $result->result() as $row ) {
				$string = "<tr><td>".$row->commentDate."</td><td>".$row->comment."</td></tr><tr><td colspan=2>".$row->alias." ".$row->organizationName."</td></tr>";
				array_push($output, $string);
			}
		}
		array_push($output, '<tr><th colspan=4>Комментарии к обработке</th></tr>');
		$result = $this->db->query("SELECT 
		`message_answers`.user_id AS userID,
		`message_answers`.answer,
		DATE_FORMAT(`message_answers`.`datetime`, '%d.%m.%Y %H:%i') AS commentDate
		FROM
		`message_answers`
		WHERE `message_answers`.`message_id` = ?", array($messageID) );
		if ( $result->num_rows() ) {
			foreach ( $result->result() as $row ) {
				$string = "<tr><td>".$row->commentDate."</td><td>".$row->answer."</td></tr><tr><td colspan=2>".$row->userID."</td></tr>";
				array_push($output, $string);
			}
		}
		return implode($output, "\n\t\t\t\t");
	}

	public function viewmessage($messageID=0) {
		$messageInfo = "<h3>Сообщение с номером ".$messageID." не найдено</h3>";
		$result = $this->messagemodel->getMessageDetailsData($messageID, 'management');
		if ( $result->num_rows() ) {
			$row = $result->row_array();
			$row['taskValid']            = ($row['statusID'] == "6") ? 1 : 0;
			$row['header']               = "";
			$row['organizationList']     = $this->messagemodel->getDropdownList('organizations', $row['organizationID']);
			$row['controlList']          = $this->messagemodel->getDropdownList('departments',   $row['controlID']);
			$row['statusList']           = $this->messagemodel->getDropdownList('statii',        $row['statusID']);
			$row['categoriesList']       = $this->messagemodel->getDropdownList('subcategories', $row['categoryID']);
			$row['subcategoriesList']    = $this->messagemodel->getDropdownList('subcategories', $row['subcategoryID'], $row['categoryID']);
			$row['files']                = $this->messagemodel->makeImagesList($row['files']);
			$row['messageProgressArray'] = $this->processmodel->getMessageProgressArray($messageID);
			$row['messageProgressTable'] = $this->processmodel->getMessageProgressTable($messageID);
			$row['historyData']          = $this->getHistoryData($messageID);
			$row['commentsData']         = $this->getCommentsData($messageID);
			//exit;
			$this->session->set_userdata("messageID", $messageID);
			$messageInfo = $this->load->view("management/messagedetails", $row, true);
		}
		$data = array(
			'menu'       => $this->load->view("management/menu", array(), true),
			'content'    => $messageInfo,
			'requestUrl' => "",
			'header'     => "Информация об обращении"
		);
		$this->load->view("management/container", $data);
	}

}