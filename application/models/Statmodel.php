<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Statmodel extends CI_Model {

	public function index() {
		$this->load->view('admin/admin');
	}

	public function getCountByCategoryId($catId) {
		$result = $this->db->query("SELECT
		COUNT(`messages`.`id`) AS `count`
		FROM
		`messages`
		WHERE 
		`messages`.`category_id`= ?", array($catId));
		if ( $result->num_rows() ) {
			$row = $result->row();
			return $row->count;
		}
		return false;
	}

	public function getCountByStatusId($statusId) {
		$result = $this->db->query("SELECT
		COUNT(`messages`.`id`) AS `count`
		FROM
		`messages`
		WHERE
		`messages`.`status_id` = ?", array($statusId));
		if ( $result->num_rows() ) {
			$row = $result->row();
			return $row->count;
		}
		return false;
	}

	public function getCountByDistrictId($distrId) {
		$result = $this->db->query("SELECT
		COUNT(`messages`.`id`) AS `count`
		FROM `messages`
		WHERE 
		`messages`.`district_id`= ?", array($distrId));
		if ( $result->num_rows() ) {
			$row = $result->row();
			return $row->count;
		}
		return false;
	}

	public function getStatByDistrictId($distrId) {
		$output = array();
		$result = $this->db->query("SELECT
		`messages`.`status_id` AS `STATUS_ID`,
		COUNT(`messages`.id)   AS `COUNT`
		FROM 
		`messages`
		WHERE
		`district_id` = ?
		AND `removed` = 0 
		GROUP BY `status_id`", array($distrId));
		if ($result->num_rows()) {
			foreach($result->result() as $row) {
				array_push($output, $row);
			}
			return $output;
		}
		return false;
	}

	public function getDepartments() {
		$output = array();
		$result = $this->db->query("SELECT `id`,`name` FROM `departments` WHERE `is_depart`='1'");
		if ($result->num_rows()) {
			foreach($result->result() as $row) {
				$output[$row->id] = $row->name;
			}
			return $output;
		}
		return false;
	}

	public function getStatDepartmentByStatus($departId) {
		$output = array();
		$result = $this->db->query("SELECT
		`messages`.`status_id` AS `status_id`,
		COUNT(`messages`.`id`) AS `count`
		FROM `messages`
		LEFT JOIN `city_districts` ON `city_districts`.`id`     = `messages`.`district_id`
		LEFT JOIN `departments` ON `departments`.`id`           = `city_districts`.`responsible`
		LEFT JOIN `message_category` ON `message_category`.`id` = `messages`.`category_id`
		WHERE 
		`departments`.`id`                = ?
		AND `messages`.`status_id` NOT IN  (?)
		AND `message_category`.`activity` = 1
		AND `messages`.`removed`          = 0
		GROUP BY `messages`.`status_id`", array($departId, array(5, 6, 17) ) );
		if ( $result->num_rows() ) {
			$total = 0;
			foreach ( $result->result() as $row ) {
				$output[$row->status_id]['COUNT'] = $row->count;
				$total += $row->count;
			}
			$output[$departId]['STATUS']    = $output;
			$output[$departId]['ALL_COUNT'] = $total;
			return $output;
		}
		return false;
	}

	public function getStatDepartmentCategory($departId) {
		$result = $this->db->query("SELECT
			`messages`.`category_id`  AS category_id,
			`message_category`.`name` AS category_name,
			`messages`.`status_id`    AS status_id,
			`departments`.`id`        AS DEPARTMENT_ID,
			COUNT(`messages`.`id`)    AS count
		FROM `messages`
			LEFT JOIN `city_districts`   ON `city_districts`.`id`   = `messages`.`district_id`
			LEFT JOIN `departments`      ON `departments`.`id`      = `city_districts`.`responsible`
			LEFT JOIN `message_category` ON `message_category`.`id` = `messages`.`category_id`
		WHERE
		`departments`.`id`= ?
		AND `messages`.`status_id` NOT IN  (?)
		AND `message_category`.`activity` = 1
		AND `messages`.`removed`          = 0
		GROUP BY `messages`.`category_id`, `messages`.`status_id`", array(
			$departId,
			array(5,6,17)
		));
		if ($result->num_rows()) {
			$category   = array();
			$total      = 0;
			$success    = 0;
			foreach($result->result() as $row) {
				if ( !isset($category[$row->category_id]['COUNT']) ) {
					$category[$row->category_id]['COUNT'] = 0;
				}
				$category[$row->category_id]['DATA']['STATUS'][$row->status_id]['COUNT'] = $row->count;
				$category[$row->category_id]['NAME'] = $row->category_name;
				if ( $row->status_id == 2 ) {
					$success_count += $row->count;
				}

				$category[$row->category_id]['COUNT'] += $row->count;
				$total += $row->count;
			}
			return array(
				$departId => array(
					'CATEGORY'      => $category,
					'ALL_COUNT'     => $total,
					'SUCCESS_COUNT' => $success
				)
			);
		}
		return false;
	}

	public function getMessageCountByStatus() {
		$result = $this->db->query("SELECT
		COUNT(`messages`.`id`) AS count,
		`messages`.`status_id`
		FROM `messages`
		LEFT JOIN `message_category` ON `message_category`.`id` = `messages`.`category_id`
		LEFT JOIN `message_status`   ON `message_status`.`id`   = `messages`.`status_id`
		WHERE 
		`message_category`.`activity`   = 1
		AND `messages`.`removed`        = 0
		AND `message_status`.`activity` = 1
		GROUP BY `messages`.`status_id`");
		if ($result->num_rows()) {
			$output = array();
			$total  = 0;
			foreach ( $result->result() as $row ) {
				$output[$row->status_id] = $row->count;
				$total += $row->count;
			}
			$output['ALL_COUNT'] = $total;
			return $output;
		}
		return false;
	}

	public function getMessageStatistic() {
		$result = $this->db->query("SELECT
			`messages`.`id`                AS id,
			`messages`.`category_id`       AS category_id,
			`message_category`.`name`      AS category_name,
			`messages`.`district_id`       AS district_id,
			`city_districts`.`name`        AS district_name,
			`messages`.`status_id`         AS status_id,
			`departments`.`ID`             AS DEPARTMENT_ID,
			`departments`.`NAME`           AS DEPARTMENT_NAME,
			`message_status`.`name`        AS status_name,
			`messages`.`archive`           AS ARCHIVE,
			`action_history`.`action_code` AS ACTION,
			`action_history`.`value_id`
		FROM `messages`
			LEFT JOIN `message_category` ON `message_category`.`id`       = `messages`.`category_id`
			LEFT JOIN `city_districts`   ON `city_districts`.`id`         = `messages`.`district_id`
			LEFT JOIN `departments`      ON `departments`.`id`            = `city_districts`.`responsible`
			LEFT JOIN `message_status`   ON `message_status`.`id`         = `messages`.`status_id`
			LEFT JOIN `action_history`   ON `action_history`.`message_id` = `messages`.`id`
		WHERE
		`message_category`.`activity` = 1
		AND `messages`.`removed`      = 0");

		if ($result->num_rows()) {
			$output       = array();
			$arCategory   = array();
			$arDepartment = array();
			$arDistrict   = array();
			$arStatus     = array();
			foreach($result->result() as $row) {
				$output['MESSAGES'][$row->id] = $row;
				if ( $row->category_id && !isset($arCategory[$row->category_id]) ) {
					$arCategory[$row->category_id]['NAME']  = $row->category_name;
					$arCategory[$row->category_id]['COUNT'] = $this->getCountByCategoryId($row->category_id);
				}
				if ( $row->district_id && !isset($arDistrict[$row->district_id])) {
					$arDistrict[$row->district_id]['NAME']  = $row->district_name;
					$arDistrict[$row->district_id]['COUNT'] = $this->getCountByDistrictId($row->district_id);
				}
				if ( $row->status_id && !isset($arStatus[$row->status_id])) {
					$arStatus[$row->status_id]['NAME']      = $row->status_name;
					$arStatus[$row->status_id]['COUNT']     = $this->getCountByStatusId($row['STATUS_ID']);
				}
				if ( $row->department_id && !isset($arDepartment[$row->department_id]) ) {
					$arDepartment[$row->department_id]['NAME'] = $row->department_name;
					//$arDepartment[$row->department_id]['COUNT'] = self::GetCountByStatusId($row['STATUS_ID']);
				}
			}
			$output['CATEGORIES'] = $arCategory;
			$output['DISTRICTS']  = $arDistrict;
			$output['STATUSES']   = $arStatus;
			$output['DEPARTMENT'] = $arDepartment;
			return $output;
		}
		return false;
	}

	public function getMessageDepartStatistic() {
		$result = $this->db->query("SELECT
			`messages`.`id`                AS id,
			`messages`.`category_id`       AS category_id,
			`message_category`.`name`      AS category_name,
			`messages`.`district_id`       AS district_id,
			`city_districts`.`name`        AS district_name,
			`messages`.`status_id`         AS status_id,
			`departments`.`ID`             AS department_id,
			`departments`.`NAME`           AS department_name,
			`message_status`.`name`        AS status_name,
			`messages`.`archive`           AS ARCHIVE,
			`action_history`.`action_code` AS ACTION,
			`action_history`.`value_id`
		FROM `messages`
			LEFT JOIN `message_category` ON `message_category`.`id`       = `messages`.`category_id`
			LEFT JOIN `city_districts`   ON `city_districts`.`id`         = `messages`.`district_id`
			LEFT JOIN `departments`      ON `departments`.`id`            = `city_districts`.`responsible`
			LEFT JOIN `message_status`   ON `message_status`.`id`         = `messages`.`status_id`
			LEFT JOIN `action_history`   ON `action_history`.`message_id` = `messages`.`id`
		WHERE 
		`message_category`.`activity`      = 1
		AND `messages`.`status_id`        <> ? 
		AND `messages`.`category_id` NOT IN (?)", array(5, array (6, 17)));
		if ($result->num_rows()) {
			$output       = array();
			$arCategory   = array();
			$arDepartment = array();
			$arDistrict   = array();
			$arStatus     = array();
			foreach($result->result() as $row) {
				$output['MESSAGES'][$row->id] = $row;
				if ( $row->category_id && !isset($arCategory[$row->category_id])) {
					$arCategory[$row->category_id]['NAME']  = $row->category_name;
					$arCategory[$row->category_id]['COUNT'] = $this-getCountByCategoryId($row->category_id);
				}

				if ( $row->district_id && !isset($arDistrict[$row->district_id])) {
					$arDistrict[$row->district_id]['NAME']  = $row->district_name;
					$arDistrict[$row->district_id]['COUNT'] = $this-getCountByDistrictId($row->district_id);
				}

				if ( $row->status_id && !isset($arStatus[$row->status_id])) {
					$arStatus[$row->status_id]['NAME']      = $row->status_name;
					$arStatus[$row->status_id]['COUNT']     = $this-getCountByStatusId($row->status_id);
				}

				if ( $row->department_id && !isset($arDepartment[$row->department_id]) ) {
					$arDepartment[$row->department_id]['NAME'] = $row->department_name;
					//$arDepartment[$row->department_id]['COUNT'] = $this-getCountByStatusId($row->status_id);
				}
			}
			$output['CATEGORIES'] = $arCategory;
			$output['DISTRICTS']  = $arDistrict;
			$output['STATUSES']   = $arStatus;
			$output['DEPARTMENT'] = $arDepartment;
			return $output;
		}
		return false;
	}

	// информация о сроке выполненеия
	public function getMessageSuccsessInfo($id) {
		$result = $this->db->query("SELECT
		`messages`.`create_time`,
		`action_history`.`value_id`  AS status,
		MAX(`action_history`.`time`) AS time
		FROM
		`action_history`
		LEFT JOIN `messages` ON `action_history`.`message_id` = `messages`.`id`
		WHERE
		`action_history`.`message_id` = ?
		AND `action_history`.`action_code`='statusChange'", array($id));
		if ( $result->num_rows() ) {
			foreach( $result->result() as $row ) {
				if ( $row->status == 2 ) {
					return $row;
				}
			}
		}
		return false;
	}
}