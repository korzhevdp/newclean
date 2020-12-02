<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Processmodel extends CI_Model {

	public function __construct() {
		parent::__construct();
		$this->load->model("logmodel");
	}
	
	private function sendInitMailToOriginator($messageID=0) {
		// 5 -- это запись в таблице mail_events, корреспондирующая уведомление пользователя
		if ($this->mailmodel->sendMailMessage($messageID, 5) ){
			$this->setStageProgress($messageID, __FUNCTION__);
			$this->logmodel->writeToLog($messageID, "Отправлено уведомление о регистрации обращения его автору");
			return true;
		}
		$this->logmodel->writeToLog($messageID, "Не удалось отправить уведомление о регистрации обращения его автору");
		return false;
	}

	/* задача "постановка в поток исполнения" */
	private function setMessageToFlow($messageID=0) {
		if ( !(int)$messageID ) {
			return false;
		}
		$result = $this->db->query("SELECT `messageProgress`.id FROM `messageProgress` WHERE `messageProgress`.`messageID` = ?", array($messageID) );
		if ( $result->num_rows()) {
			$this->setStageProgress($messageID, __FUNCTION__);
			$this->logmodel->writeToLog($messageID, "Проигнорирована повторная попытка поставить сообщение в поток исполнения");
			return true;
		}
		$output = array();
		$result = $this->db->query("SELECT
		`processes`.`function`,
		`processes`.`order`
		FROM
		`processes`
		WHERE
		`processes`.`id` = ( SELECT `messages`.`category_id` FROM `messages` WHERE `messages`.`id` = ?)", array($messageID) );
		if ( $result->num_rows() ) {
			foreach ( $result->result() as $row ) {
				array_push($output, "(".$messageID.", '".$row->function."', 0, ".$row->order.")");
			}
			$this->db->query("INSERT INTO
			`messageProgress`(
				`messageProgress`.messageID,
				`messageProgress`.stage,
				`messageProgress`.state,
				`messageProgress`.order
			) VALUES ".implode($output, ",") );
			if ($this->db->affected_rows()) {
				$this->setStageProgress($messageID, __FUNCTION__);										// устанавливаем отметку в таблице прогресса о прохождении проверки этой функцией
				$this->logmodel->writeToLog($messageID, "Сообщение поставлено в поток исполнения");		// записываем в лог итог работы
				return true;
			}
			$this->logmodel->writeToLog($messageID, "Ошибка при постановке сообщения в поток исполнения");
			return false;
		}
		// вставляем в базу протокол ведения задачи
		$this->logmodel->writeToLog($messageID, "Для этой категории сообщений ещё не существует диаграммы исполнения");
		return false;
	}

	/* Валидация задачи или отклонение по формальным признакам */
	private function validateTask($messageID) {
		$result = $this->db->query("SELECT 
		`messages`.id
		FROM
		`messages`
		WHERE `messages`.`id` = ?
		AND `messages`.`taskValid`", array($messageID) );
		if ( $result->num_rows() ) {
			$this->setStageProgress($messageID, __FUNCTION__);
			$result2 = $this->db->query("SELECT 
			`message_answers`.message_id
			FROM
			`message_answers`
			WHERE `message_answers`.message_id = ?
			AND LENGTH(TRIM(`message_answers`.`answer`))
			AND `message_answers`.`state` = 'validation'", array($messageID) );
			if ( $result2->num_rows() ) {
				$this->logmodel->writeToLog($messageID, "Задача в обращении признана валидной. Выполнение продолжено. Добавлен комментарий к валидации");
				return true;
			}
			$this->logmodel->writeToLog($messageID, "Задача в обращении признана валидной. Выполнение продолжено");
			return true;
		}
		$this->logmodel->writeToLog($messageID, "Задача в обращении не признана валидной. Выполнение остановлено. Больше информации может быть в комментарии");
		return false;
	}

	private function checkForSubCategory($messageID) {
		$result = $this->db->query( "SELECT
		`message_category`.id
		FROM
		`message_category`
		INNER JOIN `messages` ON (`message_category`.parent = `messages`.category_id)
		WHERE `messages`.`id` = ?", array($messageID) );
		if ( !$result->num_rows() ) {
			$this->setStageProgress($messageID, __FUNCTION__);
			$this->logmodel->writeToLog($messageID, "У категории не существует подкатегорий. Можно продолжать");
			return true;
		}
		$result = $this->db->query( "SELECT IF (
		(SELECT
		`messages`.`subcategoryID`
		FROM `messages`
		WHERE `messages`.`subcategoryID` IN (
			SELECT `message_category`.`id`
			FROM `message_category`
			WHERE `message_category`.`parent` = (
				SELECT
				`messages`.`category_id`
				FROM
				`messages`
				WHERE
				`messages`.`id` = ?)
			)
		) , 1,0) AS subcategoryIsSet", array($messageID) );
		if ( $result->num_rows() ) {
			$row = $result->row();
			if ( $row->subcategoryIsSet ) {
				$this->setStageProgress($messageID, __FUNCTION__);
				$this->logmodel->writeToLog($messageID, "Подкатегория выбрана корректно");
				return true;
			}
		}
		$this->logmodel->writeToLog($messageID, "Подкатегория не выбрана или выбрана некорректно");
	}

	private function setExecutive($messageID) {
		$result = $this->db->query("SELECT 
		`messages`.executiveValid,
		`messages`.org_id
		FROM
		`messages`
		WHERE `messages`.id = ?", array($messageID) );
		if ( $result->num_rows() ) {
			$row = $result->row();
			if ( $row->executiveValid && $row->org_id ) {
				$this->setStageProgress($messageID, __FUNCTION__);
				$this->logmodel->writeToLog($messageID, "Валидация исполнителя прошла успешно. Исполнитель подтверждён.");
				return true;
			}
		}
		$this->logmodel->writeToLog($messageID, "Валидация исполнителя не удалась. Исполнитель не подтверждён.");
		return false;
	}

	private function setController($messageID) {
		$result = $this->db->query("SELECT
		`messages`.controllerValid,
		`messages`.depart_id
		FROM
		`messages`
		WHERE `messages`.id = ?", array($messageID) );
		if ( $result->num_rows() ) {
			$row = $result->row();
			if ( $row->controllerValid && $row->depart_id ) {
				$this->setStageProgress($messageID, __FUNCTION__);
				$this->logmodel->writeToLog($messageID, "Валидация контролирующего органа прошла успешно.");
				return true;
			}
		}
		$this->logmodel->writeToLog($messageID, "Валидация не удалась. Контролирующий орган не подтверждён.");
		return false;
	}

	private function sendMailToController($messageID) {
		// 9 -- это запись в таблице mail_events, корреспондирующая уведомление контрольного органа
		if ($this->mailmodel->sendMailMessage($messageID, 9) ){
			$this->setStageProgress($messageID, __FUNCTION__);
			$this->logmodel->writeToLog($messageID, "Контролирующему органу выслано уведомление о начале обработки обращения");
			return true;
		}
		$this->logmodel->writeToLog($messageID, "Не удалось выслать уведомление контролирующему органу о начале обработки обращения");
		return false;
	}

	private function sendMailToExecutive($messageID) {
		// 10 -- это запись в таблице mail_events, корреспондирующая уведомление организации
		if ($this->mailmodel->sendMailMessage($messageID, 10) ){
			$this->setStageProgress($messageID, __FUNCTION__);
			$this->logmodel->writeToLog($messageID, "Исполнителю выслано уведомление о начале обработки обращения");
			return true;
		}
		$this->logmodel->writeToLog($messageID, "Не удалось выслать исполнителю уведомление о начале обработки обращения");
		return false;
	}

	public function postToSED($messageID) {
		/* Имитация пребывания с СЭД Дело Отправка */
		$this->setStageProgress($messageID, __FUNCTION__);
		$this->logmodel->writeToLog($messageID, "По итогам валидации отправлено в СЭД Дело адресатам: ... Номер РК: ...");
		return true;
	}

	public function getFromSED($messageID) {
		/* Имитация пребывания с СЭД Дело Возврат */
		$this->setStageProgress($messageID, __FUNCTION__);
		$this->logmodel->writeToLog($messageID, "Получено из СЭД Дело");
		return true;
	}

	public function validateClosure() {
		return false;
	}
	public function sendMailMemoToController() {}
	public function sendMailClosureToOriginator() {}
	public function makeReport() {}
	public function sendMailClosureToExecutive() {}
	public function closeTask() {}

	private function setStageProgress($messageID, $alias) {
		$user = ($this->session->userdata("UID")) ? $this->session->userdata("UID") : 0;
		$this->db->query("UPDATE
		messageProgress
		SET
		`messageProgress`.state    = 1,
		`messageProgress`.`setBy`  = ?
		WHERE
		(messageProgress.messageID = ?)
		AND stage = (SELECT `processTasks`.`id` FROM `processTasks` WHERE `processTasks`.`function` = ?)", array(
			$user,
			$messageID,
			$alias
		));
		return $this->db->affected_rows();
	}

	public function processMessage($messageID) {
		$progress = array();
		$result = $this->getMessageProgressData($messageID);
		if ( $result->num_rows() ) {
			foreach ( $result->result() as $row ) {
				if ( !$row->state ) {
					$result = $this->{$row->function}($messageID);
					if ( !$result ) {
						print "Stopped at ".$row->function;
						return false;
					}
				}
			}
		}
		$this->setMessageToFlow($messageID);
		redirect("/management/viewmessage/".$messageID);
	}

	#********************
	# "Внешние" функции
	#********************

	private function getMessageProgressData($messageID) {
		return $this->db->query("SELECT 
		messageProgress.`date`,
		messageProgress.id,
		messageProgress.stage,
		messageProgress.state,
		messageProgress.setBy,
		processTasks.function,
		processTasks.name
		FROM
		processTasks
		RIGHT OUTER JOIN messageProgress ON (processTasks.id = messageProgress.stage)
		WHERE
		(messageProgress.`messageID` = ?)
		ORDER BY
		messageProgress.order", array($messageID));
	}

	/*
		Функция для отслеживания прогресса
		Отображение в массив
	*/

	public function getMessageProgressArray($messageID) {
		$output = array();
		$result = $this->getMessageProgressData($messageID);
		if ( $result->num_rows() ) {
			foreach ( $result->result() as $row ) {
				$output[$row->stage] = $row->state;
			}
		}
		return $output;
	}

	/*
		Функция для отслеживания прогресса
		Отображение в таблицу
	*/

	public function getMessageProgressTable($messageID) {
		// вставка шагов процесса происходит по порядку,
		// можно сортировать по ID записи
		$output = array();
		$result = $this->getMessageProgressData($messageID);
		if ( $result->num_rows() ) {
			foreach ( $result->result() as $row ) {
				$string = '<tr>
					<td>'.$row->name.'</td>
					<td>'.$row->setBy.'</td>
					<td>'.(($row->state) ? "Да" : "Нет").'</td>
				</tr>';
				array_push($output, $string);
			}
			return implode($output, "\n\t\t\t");
		}
		return '<tr><td colspan=4><a href="/processes/processmessage/'.$messageID.'">Создать трассировку сообщения</a></td></tr>';
	}
	
}