<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

	public function index() {
		$this->load->view('admin/admin');
	}

	public function getReference() {
		$output = array();
		$result = $this->db->query("SELECT
		reference.id,
		reference.caption,
		reference.`text`,
		reference.active
		FROM
		reference
		WHERE
		reference.active = 1");
		if ($result->num_rows()) {
			foreach ( $result->result() as $row ) {
				$output[$row->id] = $row;
			}
			return $output;
		}
		return false;
	}

	public function sendFeedback( $user_id, $subject, $text, $file_path = null ) {
		$result = $this->db->query("INSERT INTO `feedback` (
			`feedback`.`user_id`,
			`feedback`.`subject`,
			`feedback`.`text`,
			`feedback`.`file_path`,
			`feedback`.`device`
		) VALUES ( ?, ?, ?, ?, ? )", array(
			$user_id,
			$subject,
			$text,
			$file_path,
			$_SERVER["HTTP_USER_AGENT"]
		));
		if ($this->db->affected_rows()) {
			return array (
				'status'  => true;
				'message' => 'Сообщение успешно отправлено в техническую поддержку.'
			);
		}
		return array(
			'status'  => false,
			'message' => 'При отправке сообщения возникли ошибки.'
		);
	}

	public function getFeedbackMessages() {
		$output = array();
		$result = $this->db->query("SELECT
			`users`.`alias`,
			`users`.`email`,
			`feedback`.`id`,
			`feedback`.`user_id`,
			`feedback`.`subject`,
			`feedback`.`text`,
			`feedback`.`file_path`,
			`feedback`.`answered`,
			`feedback`.`create_date`
		FROM `feedback` 
		LEFT JOIN `users` ON `users`.`id` = `feedback`.`user_id`
		ORDER BY `feedback`.`id` DESC");
		if ( $result->num_rows() ) {
			foreach( $result->result() as $row ) {
				$output[$row->id] = $row;
			}
			return $output;
		}
		return false
	}



	//********************************   OPTIONS  ************************************//

	public function getMailEventsList() {
		$output = array();
		$result = $this->db->query("SELECT * FROM `mail_events`", array() );
		if ( $result->num_rows() ) {
			foreach( $result->result() as $row ) {
				$output[$row->id] = $row;
			}
			return $output;
		}
		return false;
	}

	public function getUsersList() {
		$output = array();

		$result = $this->db->query("SELECT
			`users`.`id`            AS id,
			`users`.`email`         AS EMAIL,
			`users`.`auth_date`     AS AUTH_DATE,
			`users`.`alias`         AS ALIAS,
			`users`.`phone`         AS PHONE,
			`users`.`group_id`      AS GROUP_ID,
			`user_groups`.`name`    AS GROUP_NAME,
			`departments`.`name`    AS DEPARTMENT_NAME,
			`users`.`department_id` AS DEPARTMENT_ID,
			`organization`.`name`   AS ORG_NAME,
			`users`.`org_id`        AS ORG_ID,
			`users`.`activity`      AS ACTIVE,
			(SELECT COUNT(id) FROM `messages` WHERE `user_id`= `users`.`id`) as MESSAGE_COUNT
		FROM `users`
		LEFT JOIN `departments`  ON `departments`.`id`  = `users`.`department_id`
		LEFT JOIN `organization` ON `organization`.`id` = `users`.`org_id`
		LEFT JOIN `user_groups`  ON `user_groups`.`id`  = `users`.`group_id`
		ORDER BY GROUP_ID, MESSAGE_COUNT, `users`.`reg_date` DESC");
		if ( $result->num_rows() ) {
			foreach ( $result->result() as $row ) {
				$output[$row->id] = $row;
			}
			return $output;
		}
		return false;
	}

	public function getUsersStat() {
		$output = array(
			'LIST' => array(),
			'SIMPLE_USERS_CONT' => 0
		);
		$result = $this->db->query( "SELECT `id`,`group_id` FROM `users`" );
		if ( $result->num_rows() ) {
			foreach ( $result->result() as $row ) {
				$output['LIST'][$row->id] = $row->group_id;
				if ( $row->group_id == 1 ) {
					$output['SIMPLE_USERS_CONT']++;
				}
			}
			return $output;
		}
		return false;
	}

	public function getUsersGroupList() {
		$output = array();
		$result = $this->db->query( "SELECT * FROM `user_groups`" );
		if ( $result->num_rows() ) {
			foreach ( $result->result() as $row ) {
				$output[$row->id] = $row;
			}
			return $output;
		}
		return false;
	}

	public function GetDeveloperNotes() {
		$output = array();
		$result = $this->db->query( "SELECT * FROM `developer_notes` ORDER BY `sort`,`priority` DESC" );
		if ( $result->num_rows() ) {
			foreach ( $result->result() as $row ) {
				$result[$row->id] = $row;
			}
			return $output;
		}
		return false;
	}

	public function getOrganizationList() {
		$output = array();
		$result = $this->db->query( "SELECT
		`organization`.`id`,
		`organization`.`name`,
		`organization`.`address`,
		`organization`.`house_count`,
		`organization`.`activity`,
		`organization`.`name` AS department_name
		FROM `organization`
		LEFT JOIN `sub_organizations` ON `sub_organizations`.`org_id` = `organization`.`id`
		LEFT JOIN `departments`       ON `departments`.`id`           = `sub_organizations`.`depart_id`");
		if ( $result->num_rows() ) {
			foreach ( $result->result() as $row ) {
				$output[$row->id] = array(
					'id'          => $row->id,
					'name'        => $row->name,
					'address'     => $row->address,
					'house_count' => $row->house_count,
					'activity'    => $row->activity,
					'departments' => $row->department_name
				);
			}
			return $output
		}
		return false;
	}

	public function getDepartmentsList() {
		$output = array();
		$result = $this->db->query( "SELECT * FROM `departments` ORDER BY `name`" );
		if ( $result->num_rows() ) {
			foreach ( $result->result() as $row ) {
				$output[$row->id] = $row;
			}
			return $output;
		}
		return false;
	}

	public function getCategoriesList() {
		$output = array();
		$result = $this->db->query( "SELECT * FROM `message_category`", array() );
		if ( $result->num_rows() ) {
			foreach ( $result->result() as $row ) {
				$output[$row->id] = $row;
			}
			return $output;
		}
		return false;
	}

	#!!!!!!!!!!!!!!!!!!!!!! ERRATA
	public function getOrgByCategoryId($categoryId) {
		$output = array();
		if ( is_numeric( $categoryId ) ) {
			$result = $this->db->query( "SELECT 
			`message_category`.`org_id`,
			`message_category`.`depart_id`
			FROM
			`message_category`
			WHERE `id` = ?", array($categoryId) );
			if ( $result->num_rows() ) {
				foreach ( $result->result() as $row ) {
					array_push($output, $row);
				}
				return $output;
			}
			return false;
		}
		return false;
	}

	public function getCategoriesWithOrg() {
		$output = array();
		$result = $this->db->query( "SELECT
			`message_category`.`id`          AS id,
			`message_category`.`name`        AS name,
			`message_category`.`caption`,
			`message_category`.`icon`        AS icon,
			`message_category`.`yandex_icon` AS yandex_icon,
			`message_category`.`deadline`    AS deadline,
			`message_category`.`description` AS description,
			`message_category`.`activity`    AS activity,
			`message_category`.`create_time` AS create_time,
			`organization`.`id`              AS org_id,
			`organization`.`name`            AS org_name,
			`departments`.`id`               AS depart_id,
			`departments`.`name`             AS depart_name
		FROM `message_category`
		LEFT JOIN `organization` ON `organization`.`id` = `message_category`.`org_id`
		LEFT JOIN `departments`  ON `departments`.`id`  = `message_category`.`depart_id`" );
		if ( $result->num_rows() ) {
			foreach ( $result->result() as $row ) {
				$output[$row->id] = $row;
			}
			return $output;
		}
		return false;
	}

	public function getMessageStatusList() {
		$output = array();
		$result = $this->db->query( "SELECT * FROM `message_status`" );
		if ( $result->num_rows() ) {
			foreach ( $result->result() as $row ) {
				$output[$row->id] = $row;
			}
			return $output;
		}
		return false;
	}
	
	public function getGroupsList() {
		$output = array();
		$result = $this->db->query( "SELECT * FROM `user_groups`" );
		if ( $result->num_rows() ) {
			foreach ( $result->result() as $row ) {
				$output[$row->id] = $row;
			}
			return $output;
		}
		return false;
	}
	#!!!!!!!!!!!!!!!!!!!!!! ERRATA
	public function setSysTableData( $table, $id, $field, $value ) {
		if ( $field === 'email' ) {
			$query = "SELECT * FROM `".$table."` WHERE `".$field."` = '".$value."'";
			if ( $results = mysqli_query(DataBase::Connect(), $query) ) {
				if ( $row = mysqli_fetch_assoc($results) ) {
					return false;
				}
			}
		}
		$query = "UPDATE `".$table."` SET `".$field."`='".$value."' WHERE `id` = '".$id."'";
		if ( $results = mysqli_query(DataBase::Connect(), $query)) {
			$query = "SELECT * FROM `".$table."` WHERE `id` = '".$id."'";
			if($results = mysqli_query(DataBase::Connect(),$query)) {
				if ( $row = mysqli_fetch_assoc($results) ) {
					$result[$row['id']] = $row;
				}
			}
		}
		return false;
	}

	#!!!!!!!!!!!!!!!!!!!!!! ERRATA
	public function addOptionsDataRow( $table, $fields ) {
		$fieldsStr = array();
		$valuesStr = array();

		foreach($fields as $key => $field) {
			array_push($fieldsStr, $this->commonmodel->CharacterFilter($field['name']));
			array_push($valuesStr, $this->commonmodel->CharacterFilter($field['value']));
		}

		$result = $this->db->query("INSERT INTO `".$table."` (".implode($fieldsStr, "`,`")."`) VALUES ('".implode($valuesStr, "','")."')");

		if ( $this->db->affected_rows() ) {
			return true;
		}

		return false;
	}
	#!!!!!!!!!!!!!!!!!!!!!! ERRATA
	public function deleteSysTableData( $table, $id ) {
		$query = "DELETE FROM `".$table."` WHERE `id` = '".$id."'";
		if ( $results = mysqli_query(DataBase::Connect(), $query ) ) {
			return true;
		}
		return false;
	}

	public function getGlobalSysOptions() {
		$output = array();
		$result = $this->db->query("SELECT * FROM `global_system_options`");
		if ( $result->num_rows() ) {
			foreach ( $result->result() as $row ) {
				$output[$row->code] = $row;
			}
			return $output;
		}
		return false;
	}

	public function getSpecialDistrictData($id) {
		$result = $this->db->query("SELECT
		`city_districts`.`full_name`,
		`city_districts`.`coordinates`
		FROM
		`city_districts`
		WHERE `city_districts`.`id` = ?", array($id) );
		if ( $result->num_rows() ) {
			$row = $result->row();
			return $row;
		}
		return false;
	}

	public function getAllSpecialDistrictData() {
		$output = array();
		$result = $this->db->query("SELECT `id`,`name`,`full_name`,`coordinates`,`color` FROM `city_districts`");
		if ( $result->num_rows() ) {
			foreach ( $result->result() as $row ) {
				$output[$row->id] = $row;
			}
			return $output;
		}
		return false;
	}

	// привязка управляющей компании к администрации округа
	// вставляет УК в список администрации
	public function setSubOrganization( $depart_id, $org_id, $action_type ) {
		$result = $this->db->query("SELECT
		`sub_organizations`.id
		FROM
		`sub_organizations`
		WHERE
		`sub_organizations`.`org_id`       = ?
		AND `sub_organizations`.`depart_id`= ?", array(
			$org_id,
			$depart_id
		));
		if ( $result->num_rows() ) {
			if ( $action_type == "1" ) {
				$result = $this->db->query("INSERT INTO `sub_organizations` (
					`sub_organizations`.`depart_id`,
					`sub_organizations`.`org_id`
				) VALUES ( ?, ? )", array(
					$depart_id,
					$org_id
				));
				if ( $this->db->affected_rows() ) {
					return true;
				}
				return false;
			}
			$result = $this->db->query("DELETE
			FROM `sub_organizations`
			WHERE 
			`sub_organizations`.`org_id`       = ?
			AND `sub_organizations`.`depart_id`= ?", array(
				$org_id,
				$depart_id
			));
			if ( $this->db->affected_rows() ) {
				return true;
			}
		}
		return $result;
	}

	//выборка УК по департаменту
	public function getSubOrganization( $org_id ) {
		$output = array();
		$result = $this->db->query("SELECT
		`departments`.id,
		`departments`.name
		FROM `sub_organizations`
		LEFT JOIN `departments` ON `departments`.`id` = `sub_organizations`.`depart_id`
		WHERE `sub_organizations`.`org_id` = ?", array($org_id));
		if ( $result->num_rows() ) {
			foreach ( $result->result() as $row ) {
				$output[$row->id] = $row->name;
			}
			return $output;
		}
		return false;
	}

	//выборка округа по идентификатора сообщения
	public function getDistrictByMessageId( $id ) {
		$result = $this->db->query("SELECT 
		`messages`.`district_id`
		FROM
		`messages`
		WHERE `messages`.`id`= ?", array($id));
		if ( $result->num_rows() ) {
			$row = $result->row();
			return $row->district_id;
		}
		return false;
	}

	// выборка всех полей организации
	public function getOrganizationData($id) {
		$result = $this->db->query("SELECT * FROM `organization` WHERE `organization`.`id`= ? ", array( $id ));
		if ( $result->num_rows() ) {
			$row = $result->row();
			return $row;
		}
		return false;
	}

	public function getUsersOrganizationList( $depart_id = 0 ) {
		$result = $this->db->query("SELECT `organization`.`id`, `organization`.`name` FROM `organization` WHERE `activity` = '1'");
		if ($depart_id) {
			$result = $this->db->query("SELECT
			`sub_organizations`.`id`,
			`sub_organizations`.`name`
			FROM `organization`
			LEFT JOIN `sub_organizations` ON `sub_organizations`.`org_id` = `organization`.`id`
			WHERE `sub_organizations`.`depart_id` = ?
			ORDER BY `organization`.`name`", array( $depart_id ) );
		}

		if ( $result->num_rows() ) {
			$output = array();
			foreach ( $result->result() as $row ) {
				$output[$row->id]['id'] = $row->id;
				$output[$row->id]['name'] = $row->name;
			  //$output[$row->id]['department_id'] = $row->department_id;
			}
			return $output;
		}
		return false;
	}

	// НОВАЯ СТАТИСТИКА
	// переименовать функцию
	public function statByMounthAddActive() {
		$output = array();
		$result = $this->db->query("SELECT
		COUNT( `messages`.id ) AS count,
		DATE_FORMAT( `messages`.create_time,  '%Y-%m' ) AS date
		FROM `messages`
		WHERE
		`messages`.removed = '0' 
		GROUP BY DATE_FORMAT( `messages`.create_time,  '%Y-%m' )");
		if ( $result->num_rows() ) {
			foreach ( $result->result() as $row ) {
				$output[$row->date]['messages'] = $row->count;
			}
		}

		$result = $this->db->query("SELECT
		COUNT( users.id ) AS count,
		DATE_FORMAT( users.reg_date,  '%Y-%m' ) AS date
		FROM users WHERE
		users.reg_date > '0000-00-00'
		GROUP BY DATE_FORMAT( reg_date,  '%Y-%m' )");

		if ( $result->num_rows() ) {
			foreach ( $result->result() as $row ) {
				$output[$row->date]['users'] = $row->count;
			}
		}
		return $output;
	}

	public function statByMessagesStatus() {
		$output = array();
		$result = $this->db->query("SELECT 
		`message_status`.`name`      AS status_name,
		`message_status`.`web_color` AS color,
		COUNT(`messages`.`id`)       AS count
		FROM `messages`
		LEFT JOIN `message_status`   ON `message_status`.`id`   = `messages`.`status_id`
		LEFT JOIN `message_category` ON `message_category`.`id` = `messages`.`category_id`
		WHERE `message_category`.`activity` = 1 
		GROUP BY `messages`.`status_id`");
		if ( $result->num_rows() ) {
			foreach ( $result->result() as $row ) {
				$output[$row->status_name]['count'] = $row->count;
				$output[$row->status_name]['color'] = $row->color;
			}
			return $output;
		}
		return false;
	}

	public function statDistrictActivity() {
		return false;
		/*
		$result = false;
		//UPDATE `messages` SET `expired`='1' WHERE `result_time` IS NOT NULL AND  `result_time` < NOW()
		
		$query = "
		SELECT at.distr_id,at.name,at.short_name,at.full_name,at.count as success,st2.count as st2, st4.count as st4 ,st5.count as st5, st6.count as st6, exp.count as exp_count FROM (
			SELECT cd.id as distr_id, COUNT(m.id) as count, cd.name as name, cd.short_name, cd.full_name FROM messages as m 
			LEFT JOIN city_districts cd ON cd.id = m.district_id
			LEFT JOIN message_category mc ON mc.id = m.category_id
			WHERE m.district_id > 0 AND mc.activity > 0 AND mc.distr_resp > 0
			GROUP BY cd.id) as at
			
			LEFT JOIN (
					SELECT cd.id as distr_id, COUNT(m.id) as count FROM messages as m 
			LEFT JOIN message_status ms ON ms.id = m.status_id
			LEFT JOIN city_districts cd ON cd.id = m.district_id
			LEFT JOIN message_category mc ON mc.id = m.category_id
			WHERE ms.id = 2 AND m.district_id > 0 AND mc.activity > 0 AND mc.distr_resp > 0
			GROUP BY cd.id) st2 ON st2.distr_id = at.distr_id 
			
			LEFT JOIN (
					SELECT cd.id as distr_id, COUNT(m.id) as count FROM messages as m 
			LEFT JOIN message_status ms ON ms.id = m.status_id
			LEFT JOIN city_districts cd ON cd.id = m.district_id
			LEFT JOIN message_category mc ON mc.id = m.category_id
			WHERE ms.id = 5 AND m.district_id > 0 AND mc.activity > 0 AND mc.distr_resp > 0
			GROUP BY cd.id) st5 ON st5.distr_id = at.distr_id 
			
			LEFT JOIN (
					SELECT cd.id as distr_id, COUNT(m.id) as count FROM messages as m 
			LEFT JOIN message_status ms ON ms.id = m.status_id
			LEFT JOIN city_districts cd ON cd.id = m.district_id
			LEFT JOIN message_category mc ON mc.id = m.category_id
			WHERE ms.id = 4 AND m.district_id > 0 AND mc.activity > 0 AND mc.distr_resp > 0
			GROUP BY cd.id) st4 ON st4.distr_id = at.distr_id 
			
			LEFT JOIN (
					SELECT cd.id as distr_id, COUNT(m.id) as count FROM messages as m 
			LEFT JOIN message_status ms ON ms.id = m.status_id
			LEFT JOIN city_districts cd ON cd.id = m.district_id
			LEFT JOIN message_category mc ON mc.id = m.category_id
			WHERE ms.id = 6 AND m.district_id > 0 AND mc.activity > 0 AND mc.distr_resp > 0
			GROUP BY cd.id) st6 ON st6.distr_id = at.distr_id
			
			LEFT JOIN (
					SELECT cd.id as distr_id, COUNT(m.id) as count FROM messages as m 
			LEFT JOIN message_status ms ON ms.id = m.status_id
			LEFT JOIN city_districts cd ON cd.id = m.district_id
			LEFT JOIN message_category mc ON mc.id = m.category_id
			WHERE expired > 0 AND m.district_id > 0 AND mc.activity > 0 AND mc.distr_resp > 0
			GROUP BY cd.id) exp ON exp.distr_id = at.distr_id
		";

		if ( $results = mysqli_query(DataBase::Connect(),$query) ) {
			$result = array();
			while ( $row = mysqli_fetch_assoc($results) ) {
				$result[$row['distr_id']] = $row;
			}
		}
		return $result;
		*/
	}

	//$cat -- array?

	private function getActualCategories( $cat="all" ) {
		$output = array();

		$query = "SELECT `message_category`.`id`, `message_category`.`caption` AS name FROM `message_category` WHERE `message_category`.`activity` = 1 AND `message_category`.id IN (?)";

		if ( $cat == "all" ) {
			$query = "SELECT `message_category`.`id`, `message_category`.`caption` AS name FROM `message_category` WHERE `message_category`.`activity` = 1";
		}

		$result = $this->db->query($query, array($cat));

		if ( $result->num_rows() ) {
			foreach ( $result->result() as $row ) {
				$output[$row->id] = $row->name;
			}
			return $output;
		}
		return false;
	}

	private function getSelectedRegions( $reg="all" ) {
		$output = array();

		$query = "SELECT `city_districts`.id, `city_districts`.name FROM `city_districts` WHERE `city_districts`.id IN (?)";
		if ($reg === "all") {
			$query = "SELECT `city_districts`.id, `city_districts`.name FROM `city_districts`";
		}

		$result = $this->db->query($query, array($reg));

		if ( $result->num_rows() ) {
			foreach ( $result->result() as $row ) {
				$output[$row->id] = $row->name;
			}
			return $output;
		}
		return false;
	}

	private function getActualStatusMessages() {
		$output = array();
		$result = $this->db->query("SELECT
		`message_status`.id,
		`message_status`.short_name AS name
		FROM `message_status`
		WHERE `message_status`.`include_statisic` = 1");
		if ( $result->num_rows() ) {
			foreach ( $result->result() as $row ) {
				$output[$row->id] = $row->name;
			}
			return $output;
		}
		return false;
	}
	
	//	IRRATIONAL!
	public static function getAllStatisticsByCategories($reg="all", $cat="all", $date="all"){
		return array (
			'stat'       => array(),
			'reg'        => $this->getSelectedRegions($reg),
			'categories' => $this->getActualCategories($cat);
			'statuses'   => $this->getActualStatusMessages()
		);

		//following is fully irrational

		$cnt_cat = 1;
		$cnt_stat = 1;
		$join = "";
		$where = "";

		foreach($categories as $k => $v){
			$left_join ="";
			foreach($statuses as $k2 =>$v2)
			{

				$left_join = "LEFT JOIN (";
				if($cnt_stat == 1) {
					$select = $select . ", COUNT( m.category_id ) AS category_".$k."_".$k2;
					$where = " WHERE ( (m.status_id = ".$k2.") AND (m.category_id =".$k.") AND (m.removed =0) ) ";
				}
				else 
					{
						//echo $k, " ",$k2, " ",$cnt_stat, "<br>";
						$select = $select . ", m_".$k."_".$k2.".category_".$k."_".$k2."  AS category_".$k."_".$k2." ";
						$left_join = $left_join . " SELECT m.district_id AS dist".$cnt_stat.", m.status_id AS status_".$k2.", COUNT( m.category_id ) AS category_".$k."_".$k2." 	FROM `messages` AS m WHERE ( (m.status_id = ".$k2.") AND (m.category_id =".$k.") AND (m.removed =0) ) 	GROUP BY dist".$cnt_stat. " ";
						$left_join = $left_join.") m_".$k."_".$k2." ON m_".$k."_".$k2.".dist".$cnt_stat." = m.district_id ";
						$join = $join . $left_join;
					}
				//$left_join =$left_join .$left_join;
				$cnt_stat++;
			}
			//$join = $join . $left_join;
			$cnt++;
		}

		$select = "SELECT m.district_id AS dist FROM `messages` AS m " .$join . $where . "GROUP BY dist";

		if($results = mysqli_query(DataBase::Connect(), $select)) {
			$result = array();
			while($row = mysqli_fetch_assoc($results)) {
				$result["stat"][$row['dist']] = $row;
			}
		}
		return $result;
	}


}
