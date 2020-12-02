<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Messagemodel extends CI_Model {

	public function index() {
		$this->load->view('admin/admin');
	}

	public function getFullMessagesList() {
		$result = $this->db->query("SELECT
		`messages`.`message`,
		`messages`.`id`,
		`messages`.`archive`,
		`messages`.`files`,
		`messages`.`coord_x`,
		`messages`.`coord_y`,
		`messages`.`user_id`,
		`messages`.`address`,
		`messages`.`category_id`,
		`messages`.`status_id`,
		`messages`.`org_id`,
		`messages`.`depart_id`,
		DATE_FORMAT(`messages`.`update_time`, '%e.%m.%Y в %H:%i')    AS `update_time`,
		DATE_FORMAT(`messages`.`create_time`, '%e.%m.%Y в %H:%i')    AS `create_time`,
		DATE_FORMAT(`messages`.`result_time`, '%e.%m.%Y в %H:%i')    AS `result_time`,
		DATE_FORMAT(`messages`.`result_time`, '%e.%m.%Y в %H:%i:%s') AS `result_time_sys`,
		`messages`.`district_id`,
		`users`.`alias`                 AS user_alias,
		`users`.`id`                    AS user_id,
		`message_category`.`name`       AS category_name,
		`message_status`.`icon`         AS st_icon,
		`message_status`.`status_color` AS st_color,
		`message_status`.`name`         AS status_name,
		`message_answers`.`answer`      AS answer,
		`message_answers`.`file_path`   AS answer_file_path,
		`city_districts`.`name`         AS district_name,
		`city_districts`.`name`         AS district,
		`organization`.`name`           AS org_name,
		`departments`.`name`            AS responsible
		 dep2.`name`                    AS depart_name,
		FROM `messages`
		LEFT JOIN `city_districts`      ON `city_districts`.`id`   = `messages`.`district_id`
		LEFT JOIN `message_category`    ON `message_category`.`id` = `messages`.`category_id`
		LEFT JOIN `users`               ON `users`.`id`            = `messages`.`user_id`
		LEFT JOIN `organization`        ON `organization`.`id`     = `messages`.`org_id`
		LEFT JOIN `departments`         ON `departments`.`id`      = `city_districts`.`responsible`
		LEFT JOIN `departments` AS dep2 ON  dep2.`id`              = `messages`.`depart_id`
		LEFT JOIN `message_status`      ON `message_status`.`id`   = `messages`.`status_id`
		LEFT JOIN `message_answers`     ON `message_answers`.`message_id` = `messages`.`id` 
		ORDER BY `messages`.`id` DESC");

		if ( $result->num_rows() ) {
			$output = array();
			foreach ( $result->result() as $row ) {
				$output[$row->id] = $row;
			}
			return $output;
		}
		return false;
	}

	private function getDepartmentName($row, $public) {
		if ( strlen( $row->depart_name ) ) {
			if ($public) {
				return '<b>'.$row->depart_name.'</b>';
			}
			$users    = $this->getUsersByDepart($row['depart_id']);
			$depUsers = array();
			foreach ( $users as $key => $user ) {
				if ( isset( $user->alias ) && strlen($depUsers) < 3 ) {
					array_push($depUsers, "<div>".$usr->alias.( (isset ( $usr->phone ) && strlen($usr->phone) ) ? "(".$usr->phone.")" : "" )."</div>");
				}
			}
			return '<b>'.$row->depart_name." ".implode($depUsers, "\n").'</b>';
		}
		return 'Департамент не закреплен';
	}

	private function getChatInfoOnMessage($MessageId) {
		$output = array();
		$result = $this->db->query("SELECT
		`chat`.`id`,
		`chat`.`main_unit_id`,
		`chat`.`user_id`,
		`chat`.`depart_id`,
		`chat`.`org_id`,
		`chat`.`active`
		FROM
		`chat`
		WHERE
		`chat`.`message_id` = ?
		LIMIT 1", array($MessageId));
		if ( $result->num_rows() ) {
			$row = $result->row();
			$unit_id = ($row->user_id)   ? $row->user_id   : 0;
			$unit_id = ($row->depart_id) ? $row->depart_id : $unit_id;
			$unit_id = ($row->org_id)    ? $row->org_id    : $unit_id;
				
			$output[$row->main_unit_id]['active']  = $row->active;
			$output[$row->main_unit_id]['unit_id'] = $unit_id;
		}
		return $output;
	}

	private function getMessageFiles($files) {
		$files  = json_decode($files);
		$output = array();
		foreach ($files as $key => $file) {
			//if ( file_exists( $_SERVER['DOCUMENT_ROOT'].'/'.$file ) ) {
			$img_size = getimagesize($_SERVER['DOCUMENT_ROOT'].'/'.$file);
			$output[$key] = array (
				'path' => '/'.$file,
				'w'    => $img_size[0],
				'h'    => $img_size[1]
			);
			//}
		}
		return $output;
	}

	// Now $UserId unused, cause of no effect :)
	public static function getOneMessage($UserId = 0, $MessageId, $public = 0) {
		$result = $this->db->query("
		SELECT
		`messages`.`message`,
		`messages`.`id`,
		`messages`.`files`,
		`messages`.`coord_x`,
		`messages`.`coord_y`,
		`messages`.`user_id`,
		`messages`.`district_id`,
		`messages`.`org_id`,
		`messages`.`depart_id`,
		`messages`.`address`,
		`messages`.`category_id`,
		`messages`.`status_id`,
		DATE_FORMAT(`messages`.`update_time`, '%e.%m.%Y в %H:%i') AS `update_time`,
		DATE_FORMAT(`messages`.`create_time`, '%e.%m.%Y в %H:%i') AS `create_time`,
		DATE_FORMAT(`messages`.`result_time`, '%e.%m.%Y в %H:%i') AS `result_time`,
		`message_answers`.`answer`,
		`message_answers`.`file_path`       AS answer_file_path,
		`message_depart_comment`.`comment`  AS dep_comment,
		DATE_FORMAT(`message_depart_comment`.`datetime`, '%e.%m.%Y в %H:%i:%s;') AS comment_datetime,
		`city_districts`.`name`             AS district,
		`organization`.`name`               AS org_name,
		`organization`.`id`                 AS org_id,
		`departments`.`name`                AS responsible
		`departments2`.`name`               AS depart_name,
		`users`.`alias`                     AS user_alias,
		`message_status`.`name`             AS status,
		`message_status`.`icon`             AS st_icon,
		`message_status`.`status_color`     AS st_color,
		`message_category`.`yandex_icon`    AS icon_type,
		`message_category`.`name`           AS cat,

		FROM `messages`
		LEFT JOIN `city_districts`                  ON `city_districts`.`id`                            = `messages`.`district_id`
		LEFT JOIN `message_category`                ON `message_category`.`id`                          = `messages`.`category_id`
		LEFT JOIN `users`                           ON `users`.`id`                                     = `messages`.`user_id`
		LEFT JOIN `organization`                    ON `organization`.`id`                              = `messages`.`org_id`
		LEFT JOIN `departments`                     ON `departments`.`id`                               = `city_districts`.`responsible`
		LEFT JOIN `departments` AS `departments2`   ON `departments2`.`id`                              = `messages`.`depart_id`
		LEFT JOIN `message_status` `message_status` ON `message_status`.`id`                            = `messages`.`status_id`
		LEFT JOIN `message_answers`                 ON `message_answers`.`message_id`                   = `messages`.`id`
		LEFT JOIN `message_depart_comment`          ON `message_depart_comment`.`messages`.`message_id` = `messages`.`id`
		WHERE `messages`.`id` = ?
		LIMIT 1", array($MessageId));
		if ( $result->num_rows() ) {
			$row = $result->row();
			return array(
				$row->id = array(
					'id'               => $row->id,
					'center'           => array($row->coord_x, $row->coord_y),
					'text'             => $row->message,
					'category_id'      => $row->category_id,
					'category'         => $row->cat,
					'user_id'          => $row->user_id,
					'user_alias'       => $row->user_alias,
					'district'         => $row->district,
					'district_id'      => $row->district_id,
					'org_id'           => $row->org_id,
					'depart_id'        => $row->depart_id,
					'org_name_only'    => $row->org_name,
					'address'          => (strlen($row->district))    ? 'Адрес: '.$row->district.', '.$row->address : $row->address,
					'responsible'      => (strlen($row->responsible)) ? 'Ответственное подразделение:<br><b>'.$row->responsible.'</b>' : 'Ответственное подразделение:<br>Не определено',
					'org_name'         => (strlen($row->org_name))    ? '<b>'.$row->org_name.'</b>' : 'Организация не выбрана',
					'depart_name'      => $this->formDepartmentName($row, $public),
					'status'           => $row->status,
					'answer'           => (strlen($row->answer))      ? $row->answer : "",
					'answer_file_path' => $row->answer_file_path,
					'dep_comment'      => $row->dep_comment,

					'comment_datetime' => $row->comment_datetime,
					'status_id'        => $row->status_id,
					'update_time'      => $row->update_time,
					'create_time'      => $row->create_time,
					'result_time'      => $row->result_time,
					'result_time_sys'  => $row->result_time_sys,
					'status_icon'      => $row->st_icon,
					'icon_type'        => $row->icon_type,
					'status_color'     => $row->st_color,
					'files'            => $this->getMessageFiles($row->files),
					'chat'             => $this->getChatInfoOnMessage($MessageId)
				)
			);
		}
		return false;
	}

	private function getMessageQueryByUserType6() {
		$result = $this->db->query("SELECT `org_id` FROM `users` WHERE `id` = ? LIMIT 1", array($this->session->userdata('UID')));
		if ( $result->num_rows() ) {
			$row = $result->row();
			if ( $row->org_id ) {
				return "AND ((`messages`.`status_id` <> '5' AND `messages`.`org_id`='".$row->org_id."') OR (`messages`.`user_id`='".$this->session->userdata('UID')."'))";
			}
		}
		return "AND (`messages`.`user_id`='".$this->session->userdata('UID')."')";
	}

	private function getMessageQueryByUserType7() {
		$result = $this->db->query("SELECT `department_id` FROM `users` WHERE `id` = ? LIMIT 1", array($this->session->userdata('UID')));
			if ( $result->num_rows() ) {
				$row = $result->row();
				if ( $row->department_id ) {
					return " AND ((`messages`.`status_id`!='5' AND `messages`.`depart_id`='".$departId."') OR (`messages`.`user_id`='".$this->session->userdata('UID')."'))";
				}
			}
			return " AND (`messages`.`user_id`='".$this->session->userdata('UID')."')";
	}

	private function getMessageQueryByUser($UserId = 0, $UserType = 1) {
		if ( $UserType == 0 ) {
			return "AND ((`messages`.`status_id` <> '5') OR (`messages`.`user_id`='".$this->session->userdata('UID')."'))";
		}
		if ( $UserType == 6 ) {
			return $this->getMessageQueryByUserType6();
		}
		if ( $UserType == 7 ) {
			return $this->getMessageQueryByUserType7();
		}
		if ( $UserId ) {
			return "AND `messages`.`user_id`='".$UserId."'";
		}
		return "AND `messages`.`id`='".$MessageId."'";
	}

	private function getMessageQueryByDepartment($departmentID) {
		$result = $this->db->query("SELECT id FROM `city_districts` WHERE `responsible`= ?", array($departmentID));
		if ( $result->num_rows() ) {
			$responsibles = array();
			foreach ( $result->result() as $row ) {
				array_push($responsibles, $row->id);
			}
			return " AND distr.`id` IN (".implode($responsibles, ", ").")";
		}
	}

	public function GetMessages( $UserId = 0, $UserType = 1,  $MessageId = 0, $departmentID = 0) {
		$options = array();
		if ( $MessageId ) {
			array_push($options, $this->getMessageQueryByUser($UserId, $UserType));
		}

		if ( $departmentID ) {
			array_push($options, $this->getMessageQueryByDepartment($departmentID));
		}

		$remove_archive = " `messages`.`removed` = '0' ";

		$userOptions = $this->usermodel->getUserOptions($this->session->userdata('UID'));

		if ( isset($userOptions[3]) && !$MessageId && $UserType ) {
			array_push($options, " AND (`messages`.`district_id` = '0')" );
		}

		if ( isset($userOptions[5]['value']) && !$MessageId && $UserType ) {
			if ( $userOptions[5]['value'] == 'overdue' ) {
				array_push($options, " AND `messages`.`result_time` IS NOT NULL AND `messages`.`result_time` < NOW() AND `messages`.`status_id` NOT IN (2,5)");
			} else {
				array_push($options, " AND (`messages`.`status_id` = '".$userOptions[5]['value']."')");
			}
		}

		if ( isset($userOptions[6]['value']) && !$MessageId && $UserType ) {
			array_push($options, " AND (`messages`.`category_id` = '".$userOptions[6]['value']."')");
		}

		if ( isset($userOptions[7]['value']) && !$MessageId && $UserType ) {
			array_push($options, " AND (`messages`.`district_id` = '".$userOptions[7]['value']."')");
		}

		$result = $this->db->query( "SELECT
			`messages`.`id`,
			`messages`.`message`,
			`messages`.`coord_x`,
			`messages`.`coord_y`,
			DATE_FORMAT(`messages`.`create_time`, '%e.%m.%Y в %H:%i')   AS `create_time`,
			DATE_FORMAT(`action_history`.`time`, '%e.%m.%Y в %H:%i')    AS `update_time`,
			`message_status`.`id`            AS status_id,
			`message_status`.`name`          AS status,
			`message_status`.`icon`          AS st_icon,
			`message_status`.`status_color`  AS st_color,
			`message_category`.`name`        AS cat,
			`message_category`.`yandex_icon` AS icon_type,

			`city_districts`.`id`            AS district
		FROM `messages`
			LEFT JOIN `city_districts`   ON `city_districts`.`id`         = `messages`.`district_id`
			LEFT JOIN `users`            ON `users`.`id`                  = `messages`.`user_id`
			LEFT JOIN `message_category` ON `message_category`.`id`       = `messages`.`category_id`
			LEFT JOIN `message_status`   ON `message_status`.`id`         = `messages`.`status_id`
			LEFT JOIN `action_history`   ON `action_history`.`message_id` = `messages`.`id`
		WHERE `messages`.`removed` = '0'
		".implode($options, ' ')."
		ORDER BY `messages`.`id` ASC");
		if ( $result->num_rows() ) {
			$output = array();
			foreach ( $result->result() as $row ) {
				$output[$row->id] = array(
					'id'           => $row->id,
					'center'       => array( $row->coord_x, $row->coord_y ),
					'text'         => $row->message,
					'category'     => $row->cat,
					'status'       => $row->status,
					'status_id'    => $row->status_id,
					'update_time'  => $row->update_time,
					'create_time'  => $row->create_time,
					'status_icon'  => $row->st_icon,
					'icon_type'    => $row->icon_type,
					'status_color' => $row->st_color,
					'district'     => $row->district
				);
			}
			return $output;
		}
		return false;
	}

	public function getMessagesForAdmin() {
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
		ORDER BY
		messages.id DESC
		LIMIT 300" );
		if ( $result->num_rows() ) {
			$output = array();
			foreach ( $result->result() as $row ) {
				$string = '<div class="messageItem" cx="'.$row->coord_x.'" cy="'.$row->coord_y.'" ref="'.$row->id.'">'.$row->categoryName.'<br>'.$row->message.'<br>'.$row->address.'</div>';
				array_push($output, $string);
			}
			return implode($output, "\n");
		}
		return array();
	}

	public function NewMessage($data) {
		//print print_r($data, true);
		if (
			   !isset($data['UID']) 
			|| !isset($data['coords']['lat'])
			|| !isset($data['coords']['lng'])
			|| !isset($data['address'])
			|| !isset($data['districtID'])
			|| !isset($data['organizationID']) 
			|| !isset($data['departmentID'])
			|| !isset($data['moreInfo'])
			|| !isset($data['files'])
			|| !isset($data['categoryID'])
		) {
			print "Неполные данные для сохранения обращения";
			return false;
		}
		//для подсчёта дедлайна
		$result = $this->db->query( "INSERT INTO `messages` (
			`messages`.`user_id`,
			`messages`.`coord_x`,
			`messages`.`coord_y`,
			`messages`.`address`,
			`messages`.`district_id`,
			`messages`.`depart_id`,
			`messages`.`org_id`,
			`messages`.`message`,
			`messages`.`files`,
			`messages`.`category_id`,
			`messages`.`create_time`,
			`messages`.`result_time`
		) VALUES( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL ( SELECT `message_category`.deadline FROM `message_category` WHERE `message_category`.`id` = ? ) DAY) )", array(
			$data['UID'],
			$data['coords']['lat'], // тут перепутано всё. И coord_x - это широта,
			$data['coords']['lng'], // аналогично, но наоборот: y - долгота...
			$data['address'],
			$data['districtID'],
			$data['departmentID'],
			$data['organizationID'],
			$data['moreInfo'],
			'["'.implode($data['files'], '","').'"]',
			$data['categoryID'],
			$data['categoryID'], //условие к подзапросу
		) );
		if ( $this->db->affected_rows() ) {
			$messageID = $this->db->insert_id();
			// рассылка писем Сначала отправляем виновнику торжества :)
			$userdata = $this->usermodel->getUserById($data['UID']);
			$this->mailmodel->sendMailMessage($userdata->email, 5, array('MESSAGE_TEXT' => $data['moreInfo'], 'MESSAGE_ADRESS' => $data['address'] ));
			
			// потом может выясниться, что и в округ и в департамент тоже надо отписаться
			if ( $data['districtID'] ) {
				// ну да, у нас есть округ...
				$arDepart   = $this->GetDepartByDistrictId( $data['districtID'] );
				$contactDep = $this->GetDepartmentContactsByID( $data['departmentID'] );

				if( strlen($contactDep["EMAIL"])) {
					$this->mailmodel->sendMailMessage( $contactDep['EMAIL'], 9, array() );
					
				}
				if ( isset( $arDepart['USERS'] )) {
					//file_put_contents($_SERVER["DOCUMENT_ROOT"]."/log.txt", var_export($arDepart['USERS'], true), FILE_APPEND);
					$this->mailmodel->sendMessageForArray( $arDepart['USERS'], 7, array() );
				}
			}
			// а потом у нас ещё и управляйку надо ткнуть вилкой
			if ( $data['organizationID'] ) {
				$arEmailList = $this->usermodel->getUserListByOrgId($data['organizationID']);
				//file_put_contents($_SERVER["DOCUMENT_ROOT"]."/log.txt", var_export($arEmailList, true), FILE_APPEND);
				$this->mailmodel->sendMessageForArray($arEmailList['USERS'],8);
			}
			/*Конец функции вставка удалась*/
			return $messageID;
		}
		/*Конец функции вставка НЕ удалась*/
		return true;
	}

	public function GetDepartmentContactsByID( $departmentID ) {
		$result = $this->db->query( "SELECT
		`depart_contacts`.`email`,
		`departments`.`name`
		FROM
		`depart_contacts`
		JOIN `departments` ON `depart_contacts`.`dep_id` = `departments`.`id`
		WHERE `dep_id` = ?
		LIMIT 1", array($departmentID) );
		if ( $result->num_rows() ) {
			$row = $result->row();
			return array(
				"EMAIL" => $row->email,
				"phone" => $row->phone
			);
		}
		return array(
			"EMAIL" => "",
			"phone" => ""
		);
	}

	public function getCategory( $categoryID=0 ) {
		if ( !$categoryID ) {
			return false;
		}
		$result = $this->db->query( "SELECT
		`message_category`.`name`,
		`message_category`.`caption`,
		`message_category`.`description`,
		`message_category`.`deadline`,
		`message_category`.`id`,
		`message_category`.`icon`
		FROM `message_category`
		WHERE `activity`            = 1
		AND `message_category`.`id` = ?
		LIMIT 1", array($categoryID) );
		if ( $result->num_rows() ) {
			return $result->row();
		}
		return false;
	}

	public function getCategories() {
		$result = $this->db->query( "SELECT
		`message_category`.`name`,
		`message_category`.`caption`,
		TRIM(`message_category`.`description`) AS `description`,
		`message_category`.`deadline`,
		`message_category`.`id`,
		`message_category`.`icon`
		FROM `message_category`
		WHERE `message_category`.`activity`
		AND `message_category`.`parent` = 0");
		if ( $result->num_rows() ) {
			$output = new stdClass();
			foreach ( $result->result() as $row ) {
				$output->{$row->id} = $row;
			}
			return $output;
		}
		return false;
	}

	public function getStatus( $messageStatus=0 ) {
		$result = $this->db->query( "SELECT
		`message_status`.`id`,
		`message_status`.`name`,
		`message_status`.`icon`,
		`message_status`.`status_color`,
		`message_status`.`answer_index` AS `answer`,
		`message_status`.`file_index`   AS `file`
		FROM `message_status`
		WHERE `message_status`.`activity` = '1'
		AND `message_status`.`id`         =  ?
		LIMIT 1", array( $messageStatus ) );
		if ( $result->num_rows() ) {
			return $result->row();
		}
		return false;
	}

	public function getStatuses() {
		$output = array();
		$result = $this->db->query( "SELECT
		`message_status`.`id`,
		`message_status`.`name`,
		`message_status`.`icon`,
		`message_status`.`status_color`,
		`message_status`.`answer_index` AS `answer`,
		`message_status`.`file_index`   AS `file`
		FROM `message_status`
		WHERE `message_status`.`activity` = '1'" );
		if ( $result->num_rows() ) {
			foreach ( $result->result() as $row ) {
				$output[$row->id] = $row;
			}
		}
		return $output;
	}

	public function GetOrganizations( $orgID=0 ) {
		$result = $this->db->query( "SELECT
		`organization`.`id`,
		`organization`.`name`
		FROM `organization`
		WHERE `organization`.`activity` = '1'
		AND `organization`.id = ?
		LIMIT 1", array($orgID) );
		if ( $result->num_rows() ) {
			return $result->row();
		}
		return false;
	}

	public function GetDistrict( $districtID=0, $departmentID=0 ) {
		if ( $departmentID ) {
			$result = $this->db->query( "SELECT 
			city_districts.name,
			city_districts.id
			FROM
			city_districts
			WHERE 
			city_districts.activity = '1'
			AND city_districts.id IN (
				SELECT id FROM `city_districts` WHERE `responsible`= ?
			)", array($departmentID) );
			if ( $result->num_rows() ) {
				$output = array();
				foreach ( $result->result() as $row ) {
					$output[$row->id] = $row->name;
				}
				return $output;
			}
		}
	}

	public function GetDistricts() {
		$output = array();
		$result = $this->db->query( "SELECT
		`city_districts`.`name`,
		`city_districts`.`id`
		FROM
		`city_districts`
		WHERE
		`city_districts`.`activity`" );
		if ( $result->num_rows() ) {
			foreach ( $result->result() as $row ) {
				$string = '<option value="'.$row->id.'">'.$row->name.'</option>';
				array_push($output, $string);
			}
			return implode($output, "\n");
		}
		return array();
	}

	public function GetDepartByDistrictId( $districtID ) {
		$result = $this->db->query( "SELECT
		`departments`.`name`,
		`users`.`id`         AS user_id,
		`users`.`email`      AS user_email,
		`users`.`alias`      AS user_name
		FROM `city_districts`
		LEFT JOIN `departments` ON `departments`.`id`      = `city_districts`.`responsible`
		LEFT JOIN `users`       ON `users`.`department_id` = `departments`.`id`
		WHERE
		`city_districts`.`id` = ?", array($districtID) );
		if ( $result->num_rows() ) {
			$output = array(
				'NAME'  => '',
				'USERS' => array()
			);
			foreach ( $result->result() as $row ) {
				$output['NAME'] = $row->name;
				$output['USERS'][$row->user_id] = array(
					'EMAIL'     => $row->user_email,
					'USER_NAME' => $row->user_name
				);
			}
		}
		return false;
	}

	public function MessageToArchive( $messageID ) {
		$result = $this->db->query( "UPDATE `messages` SET `messages`.`archive` = '1' WHERE `messages`.`id` = ?", array($messageID) );
		if ( $result->affected_rows() ) {
			$this->logmodel->saveActionHistory($this->session->userdata('UID'),$id,'sendToArchive','Сообщение отправлено в архив контролирующим подразделением');
			return true;
		}
		return false;
	}

	public function MessageDeleteForUser( $messageID ) {
		$result = $this->db->query( "UPDATE `messages`
		SET
		`messages`.`archive`    = '1',
		`messages`.`removed`    = '1'
		WHERE 
		`messages`.`id`         = ?
		AND `messages`.`user_id`= ?", array($messageID, $this->session->userdata('UID')) );
		if ( $result->affected_rows() ) {
			$this->logmodel->saveActionHistory( $this->session->userdata('UID'), $messageID, 'sendToArchive', 'Сообщение отправлено в архив инициатором' );
			return array(
				'status'  => true,
				'message' => 'Сообщение успешно перемещено в архив'
			);
		}
		return array(
			'status'  => false,
			'message' => 'Не удалось выполнить запрос на удаление сообщения'
		);
	}
	
	private function saveMessageDataStatus($messageID) {
		$result = $this->db->query( "SELECT `id` FROM `message_answers` WHERE `message_id` = ", array($messageID) );
		if ( $result->num_rows() ) {
			$this->db->query("UPDATE
			`message_answers`
			SET
			`message_answers`.`user_id`    = ?,
			`message_answers`.`answer`     = ?,
			`message_answers`.`file_path`  = ?,
			`message_answers`.`datetime`   = NOW()
			WHERE 
			`message_answers`.`message_id` = ? ", array(
				$this->session->userdata('UID'),
				$answer,
				$answer_file_path,
				$messageID
			));
		}
		$this->db->query( "INSERT INTO `message_answers` (
			`message_answers`.`message_id`,
			`message_answers`.`user_id`,
			`message_answers`.`answer`,
			`message_answers`.`file_path`,
			`message_answers`.`datetime`
		) VALUES ( ?, ?, ?, ?, NOW() )", array(
			$messageID,
			$this->session->userdata('UID'),
			$answer,
			$answer_file_path
		));
		#############################################################
				# on line 603
				# вроде бы этот кусок отвечает за очистку устаревших писем... но непонятно
                if($results = mysqli_query(DataBase::Connect(),$query))
                {
                    $query = "SELECT `id` FROM `messages` WHERE `id`= '".$id."' AND `result_time` IS NOT NULL AND `result_time` < NOW()";
                    
                    if($results = mysqli_query(DataBase::Connect(),$query))
                    {
                        if($row = mysqli_fetch_assoc($results))
                        {
                            if(isset($row['id']))
                            {
                                $option = "`status_id`='".$value."', `expired`='1'";
                            }
                        }
                    }
                    
                    $option = "`status_id`='".$value."'";
                }
	}

	private function saveMessageDataDepartmentComment($messageID, $answer) {
		$result = $this->db->query("SELECT `id` FROM `message_depart_comment` WHERE `message_id` = ? ", array($messageID));
		if ( $result->num_rows() ) {
			$this->db->query( "UPDATE `message_depart_comment`  SET `user_id` = ? , `comment` = ?, `datetime` = NOW()  WHERE `message_id` = ? ", array($this->session->userdata('UID'), $answer, $messageID) );
		}
		$result = $this->db->query("INSERT INTO
		`message_depart_comment` (
			`message_depart_comment`.`message_id`,
			`message_depart_comment`.`user_id`,
			`message_depart_comment`.`comment`,
			`message_depart_comment`.`datetime`
		) VALUES ( ?, ?, ?, NOW() )", array(
			$messageID,
			$this->session->userdata('UID'),
			$answer
		));
	}
	/* UNFINISHED */
	public function SaveMessageData($type, $value, $answer, $answer_file_path, $id) {
		if ($type == 'status') {
			$this->saveMessageDataStatus($id);
		}
		
		if ($type == 'depart-comment') {
			$this->saveMessageDataDepartmentComment($id, $answer);
		}

		switch ($type) {
			case 'status':
				$this->logmodel->saveActionHistory( $this->session->userdata('UID'), $id, 'statusChange', 'Обновлен статус сообщения', $value );
				break;
			case 'depart-comment':
				$this->logmodel->saveActionHistory( $this->session->userdata('UID'), $id, 'departCommentChange', 'Обновлен комментарий департамента', $value );
				break;
			case 'district':
				$this->logmodel->saveActionHistory( $this->session->userdata('UID'), $id, 'respUnit', 'Назначено ответственное подразделение', $value );
				$arDepart = Messages::GetDepartByDistrictId($value);
				if ( isset($arDepart['USERS']) ) {
					$this->mailmodel->sendMessageForArray( $arDepart['USERS'], 7 );
				}
				break;
			case 'org':
				$this->logmodel->saveActionHistory( $this->session->userdata('UID'), $id, 'respOrganization', 'Назначена ответственная организация', $value );
				break;
			case 'depart':
				$this->logmodel->saveActionHistory( $this->session->userdata('UID'), $id, 'respDepartment','Назначен контролирующий департамент', $value );
				break;
			case 'time':
				$this->logmodel->saveActionHistory( $this->session->userdata('UID'), $id, 'respTime', 'Назначен срок для исполнения. Устранить до '.$value);
				break;
			default:
				$option = false;
				break;
		}

		return $result;
	}

	public function userMessagesList($userID = 0, $UserType = 1) {
		if ( $userID ) {
			$result = $this->db->query("SELECT
			`messages`.`message`,
			`messages`.`id`,
			`messages`.`status_id`
			FROM `messages`
			WHERE `user_id`= ? 
			AND `messages`.`removed` = '0' 
			ORDER BY `messages`.`id` DESC", array($userID));
		}
		if ( $UserType > 1 ) {
			 $result = $this->db->query("SELECT
			`messages`.`message`,
			`messages`.`id`,
			`messages`.`status_id`
			FROM `messages` 
			WHERE (`messages`.`status_id` NOT IN ('5', '6')
			OR (`messages`.`user_id` = ?) )", array($userID));
		}
		if ( $result->num_rows() ) {
			$output = array();
			foreach ( $result->result() as $row ) {
				$output[$row->id] = $row;
			}
			return $output;
		}
		return array();
	}

	public static function getUsersByDepart($depart_id) {
		$user = $this->usermodel->getUserById($this->session->userdata('UID'));

		$result = $this->db->query("SELECT 
		`users`.`id`,
		`users`.`alias`,
		`users`.`phone`
		FROM `users`
		WHERE
		`users`.`department_id`      = ?
		AND `users`.`department_id` <> ?
		AND `users`.email <> 'mailtesttestovich@gmail.com'
		AND `users`.`activity`       = '1'", array(
			$depart_id,
			$user['department_id']
		));
		if ( $result->num_rows() ) {
			$output = array();
			foreach ( $result->result() as $row ) {
				array_push($output, $row);
			}
			return $output;
		}
		return false;
	}
	// Используются в новейшей редакции ЧГ
	public function getMessageDetailsData($messageID, $mode="usermode") {
		$searchParameters = array(
			$messageID
		);
		$userIDCondition  = "";
		if ( $mode === 'usermode' ) {
			$userIDCondition  = "\n\t\t(messages.user_id = ?)\n\t\tAND";
			$searchParameters = array(
				$this->session->userdata('UID'),
				$messageID
			);
		}
		return $this->db->query("SELECT 
		messages.message,
		messages.archive,
		messages.removed,
		messages.taskValid,
		messages.files,
		messages.subcategoryID														AS subcategoryID,
		messages.category_id														AS categoryID,
		messages.org_id																AS organizationID,
		messages.status_id															AS statusID,
		messages.depart_id															AS controlID,
		CONCAT('{ lat : ', messages.coord_x, ', lng : ', messages.coord_y, ' }')	AS coords,
		messages.id																	AS messageID,
		DATE_FORMAT(messages.create_time, '%d.%m.%Y, %H:%i')						AS createTime,
		DATE_FORMAT(messages.update_time, '%d.%m.%Y, %H:%i')						AS updateTime,
		message_category.deadline,
		message_category.name														AS categoryName,
		message_status.name															AS statusName,
		message_status.web_color													AS statusColor,
		IF(LENGTH(organization.name), organization.name, 'Исполнитель не назначен')	AS organizationName,
		city_districts.name															AS districtName,
		IF(LENGTH(departments.name), departments.name, 'Контролёр не назначен')		AS departmentName,
		`users`.email,
		`users`.alias,
		`users`.phone
		FROM
		messages
		LEFT OUTER JOIN city_districts ON (messages.district_id = city_districts.id)
		LEFT OUTER JOIN message_status ON (messages.status_id = message_status.id)
		LEFT OUTER JOIN organization ON (messages.org_id = organization.id)
		LEFT OUTER JOIN message_category ON (messages.category_id = message_category.id)
		LEFT OUTER JOIN departments ON (message_category.depart_id = departments.id)
		LEFT OUTER JOIN `users` ON (messages.user_id = `users`.id)
		WHERE".$userIDCondition." messages.id = ?
		LIMIT 1", $searchParameters);
	}

	public function makeImagesList($files) {
		$output = array();
		$files = json_decode($files);
		foreach ( $files as $url ) {
			$url    = str_replace( "//", "/", "/".$url );
			$string = '<img src="'.$url.'" width="500" border="0" title="Фото с места событий" alt="Фото с места событий" class="photoForMessage">';
			array_push($output, $string);
		}
		return implode($output, "\n\t\t");
	}
	
	public function getListResult($type, $condition=0) {
		$queries = array(
			'districts'     => 'SELECT `city_districts`.`id`,	TRIM(`city_districts`.`name`)	AS `name`	FROM `city_districts`	WHERE `city_districts`.`activity`	ORDER BY `city_districts`.`name`',
			'organizations' => 'SELECT `organization`.`id`,		TRIM(`organization`.`name`)		AS `name`	FROM `organization`		WHERE `organization`.`activity`		ORDER BY `organization`.`name`',

			'statii'        => 'SELECT `message_status`.`id`,	TRIM(`message_status`.`name`)	AS `name`, `message_status`.`final`	FROM `message_status`	WHERE `message_status`.`activity`	ORDER BY `message_status`.`final`, `message_status`.`name` ASC',

			'categories'    => 'SELECT `message_category`.`id`, `message_category`.`name` FROM `message_category` WHERE `message_category`.`activity` AND `message_category`.parent = 0	ORDER BY `message_category`.`name`',
			'subcategories' => 'SELECT `message_category`.`id`, `message_category`.`name` FROM `message_category` WHERE `message_category`.`activity` AND `message_category`.parent = ? ORDER BY `message_category`.`name`',
			'departments'   => 'SELECT `organization`.`id`, `organization`.`name` FROM `organization` WHERE `organization`.`department` AND `organization`.`activity` ORDER BY `organization`.`name`',
			'usergroups'    => 'SELECT `user_groups`.id, `user_groups`.name FROM `user_groups` ORDER BY `user_groups`.`name`'
		);
		return $this->db->query( $queries[$type], array($condition) );
	}
	
	public function makeDropdownList($result, $selectedID=0) {
		if ( $result->num_rows() ) {
			$output = array('<option value="0">Выберите из списка</option>');
			foreach ( $result->result() as $row ) {
				$params = array();
				foreach ( $row as $key=>$val ) {
					array_push($params, $key.'="'.htmlspecialchars($val).'"');
				}
				$selected = ( $row->id == $selectedID ) ? ' selected="selected"': "" ;
				$string   = '<option value="'.$row->id.'" '.implode($params, " ").' '.$selected.'>'.$row->name.'</option>';
				array_push($output, $string);
			}
			return implode($output, "\n");
		}
		return '<option value="-1">Не найдено</option>';
	}

	public function getDropdownList($type, $selectedID=0, $condition=0) {
		$result = $this->getListResult($type, $condition);
		return $this->makeDropdownList($result, $selectedID);
	}

}