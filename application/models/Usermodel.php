<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usermodel extends CI_Model {

	public function getUserById($userID = 0) {
		$result = $this->db->query( "SELECT
		`users`.`id`            AS user_id,
		`users`.`alias`         AS user_name,
		`users`.`email` ,
		`users`.`department_id` AS department_id,
		`user_groups`.`id`      AS group_id,
		`user_groups`.`caption` AS group_caption,
		`departments`.`name`    AS department,
		`organization`.`name`   AS org_name,
		`city_districts`.`id`   AS district_id,
		`city_districts`.`name` AS district
		FROM `users`
		LEFT JOIN `departments`    ON `departments`.`id`             = `users`.`department_id`
		LEFT JOIN `user_groups`    ON `user_groups`.`id`             = `users`.`group_id`
		LEFT JOIN `city_districts` ON `city_districts`.`responsible` = `departments`.`id`
		LEFT JOIN `organization`   ON `users`.`org_id`               = `organization`.`id`
		WHERE
		`users`.`id` = ?
		LIMIT 1", array($userID) );
		if ( $result->num_rows() ) {
			return $result->row();
		}
		return false;
	}

	public function generatePassPart($length = 5) {
		$output = "";
		while (strlen($output < $length) ) {
			$output .= mt_rand(0, 9);
		}
		return $output;
	}

	public function isActiveUser($userID) {
		$result = $this->db->query( "SELECT
		`users`.id
		FROM 
		`users`
		WHERE `users`.`id`     = ?
		AND `users`.`activity` = 1", array($userID) );
		if ( $result->num_rows() ) {
			return true;
		}
		return false;
	}

	public function сharacterFilter($string) {
		//иногда валит запросы
		return htmlspecialchars($string);
	}

	private function startSession() {
		//empty
	}

	public function characterFilter ($str) {
		return htmlspecialchars($str);
	}


	

	public function getCaptchaSuccess($CaptchaData) {
		$cURL = curl_init('https://www.google.com/recaptcha/api/siteverify');
		curl_setopt($cURL, CURLOPT_POST, true);
		curl_setopt($cURL, CURLOPT_POSTFIELDS, "secret=".$CaptchaData['secret']."&response=".$CaptchaData['response']); //чота там про http_build_request...
		curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($cURL, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($cURL, CURLOPT_RETURNTRANSFER , true); 
		curl_setopt($cURL, CURLOPT_FOLLOWLOCATION , true);
		$CURL_Result = curl_exec($cURL);
		$CURL_Error  = curl_errno($cURL);

		if ($CURL_Error) {
			return 'cURL Error: '.$CURL_Error;
		}
		curl_close($cURL);
		return $CURL_Result;
	}

	private $captchaSecret = "6LcOjUkUAAAAALsw4QGuMYiTnoguhhuEnST8hS7d";

	public function RegUser($registrationData) {
		if (
			   !isset($registrationData['email'])
			|| !isset($registrationData['alias'])
			|| !isset($registrationData['password'])
			|| !isset($registrationData['captcha'])
		) {
			return 'Получены неполные данные для регистрации';
		}

		if ( !$this->session->userdata('captcha') ) {
			return 'Не удалось установить в память проверочный код CAPTCHA';
		}

		if ( strtolower($registrationData['captcha']) !== strtolower($this->session->userdata('captcha')) ) {
			$this->session->set_userdata('captcha', md5($registrationData['email']));
			return 'Код с картинки введен неверно';
		}

		if ( !filter_var($registrationData['email'], FILTER_VALIDATE_EMAIL) ) {
			return 'E-mail выглядит как некорректный';
		}

		$result = $this->db->query( "SELECT `users`.`id` FROM `users` WHERE TRIM(`users`.`email)` = ?", array(trim($registrationData['email'])) );
		if ( $result->num_rows() ) {
			return 'Пользователь с таким E-mail уже существует. Введите другой E-mail или зайдите в личный кабинет';
		}

		$CaptchaData = array(
			"secret"   => $this->captchaSecret,
			"response" => $registrationData['captcha']
		);

		$part = $this->generatePassPart();
		$result = $this->db->query( "INSERT INTO
		`users` (
			`users`.`alias`,
			`users`.`email`,
			`users`.`phone`,
			`users`.`password`,
			`users`.`pass_part`,
			`users`.`reg_date`
		) VALUES( ?, ?, ?, ?, ?, NOW() )", array(
			$this->characterFilter($registrationData['alias']),
			$this->characterFilter($registrationData['email']),
			$this->characterFilter($registrationData['phone']),
			$this->preparePasswordString($part, $registrationData['password']),
			$part,
		));
		if ( $this->db->affected_rows() ) {
			$this->mailmodel->sendMailMessage( $registrationData['email'], 1, array("USER_EMAIL" => trim($registrationData['email'])));
			$authResult = $this->AuthUser($registrationData);
			if ( $authResult['status'] ) {
				return "Вы успешно зарегистрировались в системе. Для создания нового сообщения о выявленных нарушениях войдите в личный кабинет";
			}
		}
		return "При регистрации произошла ошибка. Пожалуйста, попробуйте повторить позже";
	}

	public function RegAdmin($Data) {
		if (
			   !isset($Data['email'])
			|| !isset($Data['alias'])
			|| !isset($Data['password'])
			|| !isset($Data['captcha'])
		){
			return 'Получены неполные данные для регистрации';
		}
			
		if ( !$this->session->userdata('captcha') ) {
			return 'Не удалось создать проверочный код CAPTCHA';
		}

		if (strtolower($Data['captcha']) !== strtolower($this->session->userdata('captcha'))) {
			$this->session->set_userdata('captcha', md5($Data['email']));
			return  'Код с картинки введен неверно';
		}

		if ( !filter_var($Data['email'], FILTER_VALIDATE_EMAIL) ) {
			return 'E-mail выглядит как некорректный';
		}

		$result = $this->db->query( "SELECT `users`.`id` FROM `users` WHERE TRIM(`users`.`email)` = ?", array(trim($Data['email'])) );
		if ( $result->num_rows() ) {
			return 'Пользователь с таким E-mail уже существует. Введите другой E-mail или зайдите в личный кабинет';
		}

		$CaptchaData = array(
			"secret"   => $this->captchaSecret,
			"response" => $Data['captcha']
		);

		$part = $this->generatePassPart();
		$result = $this->db->query( "INSERT INTO
		`users` (
			`users`.`alias`,
			`users`.`email`,
			`users`.`phone`,
			`users`.`password`,
			`users`.`pass_part`,
			`users`.`reg_date`
		) VALUES( ?, ?, ?, ?, ?, NOW() )", array(
			$this->characterFilter($Data['alias']),
			$this->characterFilter($Data['email']),
			$this->characterFilter($Data['phone']),
			$this->preparePasswordString($part, $Data['password']),
			$part,
		));
		if ( $this->db->affected_rows() ) {
			$this->mailmodel->sendMailMessage( $Data['email'], 1, array("USER_EMAIL" => trim($Data['email'])));
			$authResult = $this->AuthUser($registrationData);
			if ( $authResult['status'] ) {
				return "Вы успешно зарегистрированы в системе. Если Вам необходим доступ к административной части, пожалуйста, позвоните по номеру 607-506 для назначения специальных прав";
			}
		}
		return "При регистрации произошла ошибка. Пожалуйста, попробуйте повторить позже";
	}

	/*********************************************   MAIL FUNCTIONS   ***************************************************/

	// пользователи, которые видят все объекты на карте в административной части (например контролирующие органы)

	/*
	Функции модифицируются в соответствии с новой структурой сессионных данных
	*/

	public function isSupervisoryByGroup() {
		if ( $this->session->userdata('groupID') == 4 ) {
			$result = true;
		}
		return false;
		//return ( $this->session->userdata('groupID') == 4 ) :))))
	}

	// пользователи, относящиеся к одному из ответственных подразделений
	public function isResponsibleUnit() {
		if ( $this->session->userdata('groupID') == 2 ) {
			return true;
		}
		return false;
		//return ( $this->session->userdata('groupID') == 2 ) :))))
	}

	// пользователи, относящиеся к одной из ответственных организаций
	public function isOrganization() {
		if ( $this->session->userdata('groupID') == 6 ) {
			return true;
		}
		return false;
		//return ( $this->session->userdata('groupID') == 6 ) :))))
	}

	// системные администраторы
	public function isAdmin() {
		if ( $this->session->userdata('groupID') == 3 ) {
			return true;
		}
		return false;
		//return ( $GroupId == 3 ) :))))
	}

	// пользователи, имеющие возможность изменять любые свойства сообщений
	public function isAllEditRight( $groupID ) {
		if ( $this->session->userdata('groupID') == 3 || $this->session->userdata('groupID') == 4 ) {
			return true;
		}
		return false;
		// return ( $groupID == 3 || $groupID == 4 )
	}

	// проверка авторизации пользователя
	public function isAuthorized() {
		if ( $this->session->userdata('SSUID') ) {
			return true;
		}
		return false;
	}

	// проверка авторизации пользователя по ключу
	public function isAuthorizedByKey($key) {
		$result = $this->db->query( "SELECT `users`.`id` FROM `users` WHERE `users`.`auth_key` = ? AND `users`.`activity` = '1'", array($key) );
		if ( $result->num_rows() ) {
			$this->session->set_userdata("SSUID", $key);
			return array(
				'status' => true,
				'id'     => $row->id
			);
		}
		return array(
			'status' => false,
			'id'     => false
		);
	}

	// восстановление пароля
	public function userPasswordRecovery( $email, $userType = 0) {
		if ( $this->isUserExist($email) ) {
			$recovery_code = md5($email.date("Y-m-d H:i:s").rand(1,5));
			$result = $this->db->query( "UPDATE
			`users`
			SET
			`users`.`rec_pass_uid`  =  ?
			WHERE 
			TRIM(`users`.`email`    =  ?
			AND  `users`.`activity` = '1'", array(
				$recovery_code,
				$this->characterFilter(trim($email))
			));
			if ( $this->db->affected_rows() ) {
				//if ( $userType == 2 ) {
					return $this->mailmodel->sendMailMessage( $email, 6, array("RECOVERY_KEY" => $recovery_code) );
				//}
				//return     $this->mailmodel->sendMailMessage( $email, 3, array("RECOVERY_KEY" => $recovery_code));
			}
		}
		return array(
			'status'  => false,
			'message' => 'Не удалось найти адрес E-mail'
		);
	}

	public function isUserExist($email) {
		$result = $this->db->query( "SELECT
		`users`.`id`
		FROM
		`users`
		WHERE
		TRIM(`users`.`email`) = ?
		AND `activity` = '1'", array( trim($email) ));
		if ( $result->num_rows() ) {
			return true;
		}
		return false;
	}

	//считает только активных пользователей
	public function userCount() {
		$result = $this->db->query( "SELECT COUNT(`users`.`id`) AS `count` FROM `users` WHERE `users`.`activity`='1'");
		if ( $result->num_rows() ) {
			$row = $result->row();
			return $row->count;
		}
		return 0;
	}

	// непонятная функция
	// все, имеющие доступ к административной части
	public function isMainUserByGroup() {
		$result = $this->db->query( "SELECT
		`user_groups`.`id`
		FROM
		`user_groups`
		WHERE
		`user_groups`.`law7`   = 1
		AND `user_groups`.`id` = ? ", array(
			$this->session->userdata('groupID')
		));
		if ( $result->num_rows() ) {
			// и фиг знает, что с этим дальше...
			return true;
		}
		return false;
	}

	public function changePassword( $login, $userID, $currentPassword, $newPassword ) {
		$passData = array(
			"email"    => $login,
			"password" => $currentPassword
		);
		// аутентифицирует пользователя с установкой сессии
		$authResult = $this->AuthUser($passData);
		if ( $authResult['status'] ) {
			$part = $this->generatePassPart();
			$result = $this->db->query("UPDATE
			`users`
			SET
			`password`  = ?,
			`pass_part` = ?
			WHERE `id`  = ?", array(
				$this->preparePasswordString($part, $newPassword),
				$part,
				$userID
			));
			if ( $this->db->affected_rows() ) {
				return array(
					'status'  => true,
					'message' => "Пароль успешно изменен"
				);
			}
			return 'При попытке сохранения нового пароля произошла ошибка';
		}
		return "Аутентификация с указанными E-mail  и паролем не удалась";
	}

	public function newPasswordAfterRecovery( $password, $recovery_key ) {
		$part     = $this->generatePassPart();
		if ( $this->getUserLoginByReqKey( $recovery_key ) ) {
			$result = $this->db->query( "UPDATE `users`
			SET
			`users`.`password`    = ?,
			`users`.`pass_part`   = ?,
			`users`.`rec_pass_uid`= ''
			WHERE
			`users`.`rec_pass_uid`= ?", array(
				$this->preparePasswordString($part, $password),
				$part,
				$recovery_key
			));
			if ( $this->db->affected_rows() ) {
				return "Пароль успешно изменен";
			}
			return "При попытке сохранения пароля возникла ошибка";
		}
		return 'Ключ восстановления устарел. Попробуйте начать с первого шага';
	}

	// проверка прав пользователя на выполнение различных задач
	public function UserLawByGroup( $groupID, $lawType ) {
		$result = $this->db->query( "SELECT 
		`user_groups`.law1,
		`user_groups`.law2,
		`user_groups`.law3,
		`user_groups`.law4,
		`user_groups`.law4_1,
		`user_groups`.law8,
		`user_groups`.law5,
		`user_groups`.law6,
		`user_groups`.law7,
		`user_groups`.law9
		FROM
		`user_groups`
		WHERE
		`user_groups`.id = ?
		LIMIT 1", array($groupID) );
		if ( $result->num_rows() ) {
			$row = $result->row();
			return $row->{$lawType};
		}
		return false;
	}

	// установка опций пользователя
	public function setUserOptions( $user_id, $option_id, $value, $value_id="" ) {
		$result = $this->db->query( "SELECT id FROM `user_set_options` WHERE `user_id` = ? AND `option_id` = ?", array( $user_id, $option_id ) );
		if ( $result->num_rows() ) {
			if ( $value && strlen($value_id) ) {
				$this->db->query( "DELETE FROM `user_set_options` WHERE `user_id` = ? AND `option_id` = ? ", array( $user_id, $option_id ) );
				return true;
			}
			$this->db->query("UPDATE
			`user_set_options`
			SET
			`user_set_options`.`value`         = ?
			WHERE
			`user_set_options`.`user_id`       = ?
			AND `user_set_options`.`option_id` = ?", array(
				$value_id,
				$user_id,
				$option_id
			));
			return true;
		}
		if ( $value ) {
			$this->db->query( "INSERT INTO `user_set_options` (
				`user_set_options`.`value`,
				`user_set_options`.`user_id`,
				`user_set_options`.`option_id`
			) VALUES ( ?, ?, ? )", array(
				$value_id,
				$user_id,
				$option_id
			));
			return true;
		}
		return false;
	}

	// все, имеющие доступ к административной части
	public function getUserOptions( $userID ) {
		$result = $this->db->query( "SELECT
		`user_set_options`.`id`,
		`user_set_options`.`option_id`,
		`user_set_options`.`value`
		FROM
		`user_set_options`
		WHERE
		`user_set_options`.`user_id` = ?", array($userID) );
		if ( $result->num_rows() ) {
			$output = array();
			foreach ( $result->result() as $row ) {
				$output[$row->option_id]['value'] = $row->value;
			}
			return $output;
		}
		return false;
	}

	public function getUserLoginByReqKey( $key ) {
		$result = $this->db->query("SELECT
		`users`.`email`,
		`users`.`id`
		FROM
		`users`
		WHERE
		`users`.`rec_pass_uid` = ?
		LIMIT 1", array( $this->characterFilter( $key ) ));
		if ( $result->num_rows() ) {
			return $result->row();
		}
		return false;
	}

	public function getUserListByOrgId($organizationID=0) {
		if ( !$organizationID ) {
			return false;
		}

		$result = $this->db->query("SELECT
		`organization`.`name` AS org_name,
		`users`.`id`          AS user_id,
		`users`.`email`       AS user_email,
		`users`.`alias`       AS user_name
		FROM `users`
		LEFT JOIN `organization` ON `organization`.`id` = `users`.`org_id`
		WHERE `organization`.`id` = ?", array($organizationID) );
		if ( $result->num_rows() ) {
			$output = array();
			foreach ( $result->result() as $row ) {
				$output['NAME'] = $row->org_name;
				$output['USERS'][$row->user_id]['EMAIL'] = $row->user_email;
				$output['USERS'][$row->user_id]['USER_NAME'] = $row->user_name;
			}
			return $output;
		}
		return false;
	}

	public function getUsersOrganizationList ( $depart_id = 0 ) {
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

	public function getDepartmentsList () {
		$output = array();
		$result = $this->db->query( "SELECT 
		departments.name,
		departments.id,
		departments.is_depart
		FROM
		departments
		ORDER BY departments.name" );
		if ( $result->num_rows() ) {
			foreach ( $result->result() as $row ) {
				$output[$row->id] = $row;
			}
			return $output;
		}
		return $output;
	}

	public function getStatiiList () {
		$output = array();
		$result = $this->db->query( "SELECT 
		`message_status`.id,
		`message_status`.name
		FROM
		`message_status`
		WHERE
		`message_status`.`activity`
		ORDER BY
		`message_status`.name ASC" );
		if ( $result->num_rows() ) {
			foreach ( $result->result() as $row ) {
				$output[$row->id] = $row->name;
			}
			return $output;
		}
		return $output;
	}

	public function getGlobalSysOptions () {
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

	public function getSpecialDistrictData ($id) {
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

	public function getAllSpecialDistrictData () {
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

	/* NOW IN USE */

	public function preparePasswordString($salt, $password) {
		return md5( $salt.md5( $password ) );
	}

	private function setUserAuthTime($userID, $key ) {
		$this->db->query( "UPDATE
		`users` 
		SET
		`users`.`auth_date`= NOW(),
		`users`.`auth_key` = ?
		WHERE 
		`users`.`id`       = ?", array(
			$key,
			$userID
		));
		if ( $this->db->affected_rows() ) {
			return true;
		}
		return false;
	}

	private function setSessionID($userID) {
		$this->db->query("UPDATE
		`userSessions`
		SET
		`userSessions`.sessionID = ?
		WHERE
		`userSessions`.`userID` = ?", array(session_id(), $userID) );
	}

	//аутентифицирует пользователя с установкой сессии
	public function AuthUser($data) {
		$result = $this->db->query("SELECT
		users.password,
		users.pass_part,
		users.phone,
		users.alias,
		users.email,
		users.id                 AS UID,
		users.activity           AS active,
		users.org_id             AS organizationID,
		users.department_id      AS departmentID,
		users.group_id           AS groupID,
		CONCAT_WS(',', '0', user_groups.law1, user_groups.law2, user_groups.law3, user_groups.law4, user_groups.law5, user_groups.law6, user_groups.law7, user_groups.law8, user_groups.law9, user_groups.law4_1) AS rights,
		departments.name         AS departmentName,
		user_groups.name         AS groupName,
		user_groups.caption      AS roleCaption,
		`organization`.name      AS organizationName,
		`organization`.full_name AS organizationFullName
		FROM
		user_groups
		RIGHT OUTER JOIN users         ON (user_groups.id      = users.group_id)
		LEFT OUTER JOIN departments    ON (users.department_id = departments.id)
		LEFT OUTER JOIN `organization` ON (users.org_id        = `organization`.id)
		WHERE
		( users.email = ? )
		LIMIT 1", array($data['email']) );

		if ( $result->num_rows() ) {
			$row = $result->row_array();
			if ( !$row['active'] ) {
				return array (
					'status'   => false,
					'message'  => 'Ваша учетная запись заблокирована',
					'redirect' => ''
				);
			}
			
			if( $this->preparePasswordString($row['pass_part'], $data['password']) == $row['password'] ) {
				$row['SSUID']  = md5(date("U").mt_rand());
				/*
				Неочевидная концепция прав $law* рефакторится в конструкцию session['rights']
				для удобства сохранена последовательность признаков.
				Так $law4 переходит в логическое значение по адресу $row['rights'][4]
				Однако $law4_1 => $row['rights'][10]
				*/
				$row['rights'] = explode(",", $row['rights']);
				//удаляем из сессии откровенно лишние там данные
				unset($row['pass_part']);
				unset($row['password']);
				//устанавливаем сессию
				$this->session->set_userdata($row);
				$this->setUserAuthTime($row['UID'], $row['SSUID']);
				$this->setSessionID($row['UID']);
				$this->logmodel->writeToLog( 0, 'Пользователь '.$row['alias'].' авторизовался');

				$redirect = (strlen($this->session->userdata("force_redirect"))) ? $this->session->userdata("force_redirect") : "/admin" ;
				return array (
					'status'   => true,
					'message'  => 'Успешная аутентификация',
					'redirect' => $redirect
				);
			}
			return array (
				'status'   => false,
				'message'  => 'Пароль или email введены неверно',
				'redirect' => ''
			);
		}
	}


}