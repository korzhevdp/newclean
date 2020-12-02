<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mailmodel extends CI_Model {

	public function sendMessageForArray( $addresses, $messEventTypeId, $mailParams = array() ) {
		foreach ( $addresses as $user ) {
			$this->sendMailMessage( $user['EMAIL'], $messEventTypeId, $mailParams );
		}
		return true;
	}

	public function sendMailMessage($messageID = 0, $eventId = 0, $params = array()) {
		if ( !$messageID || !$eventId ) {
			return false;
		}

		$result = $this->db->query("SELECT
			mail_events.event_name,
			mail_events.subject,
			mail_events.`text`,
			mail_events.link,
			mail_events.link_text,
			mail_events.from_email,
			mail_events.activity,
			mail_events.update_time,
			(SELECT `users`.email FROM `users` RIGHT OUTER JOIN messages ON (`users`.id = messages.user_id) WHERE (messages.id = ?)) AS `mailTo`
			FROM `mail_events`
			WHERE `activity`     = 1
			AND `mail_events`.id = ?
			LIMIT 1", array($messageID, $eventId));
		if ($result->num_rows()) {
			$row = $result->row();
			if ( $this->sendMail($row->mailTo, $row) ) {
				return true;
			}
		}
		return false;
	}

	private function sendMail($mailTo, $data) {
		// Basic fields
		$encoding    = "utf-8";
		$fromName    = "ИС Чистый город";
		$fromMail    = "cleancity@arhcity.ru";
		$bcc         = "";

		// Mail headers
		$header  = "Content-type: text/html; charset=".$encoding." \r\n";
		$header .= "From: ".$fromName." <".$fromMail."> \r\nBcc: ".$bcc."\r\n";
		$header .= "MIME-Version: 1.0 \r\n";
		$header .= "Content-Transfer-Encoding: 8bit \r\n";
		$header .= "Return-Path: <cleancity@arhcity.ru>\r\n";
		$header .= "Reply-To: <cleancity@arhcity.ru>\r\n";
		$header .= "Date: ".date("r (T)")." \r\n";
		//$header .= "Subject: =?".$encoding."?".$data->subject."?=";
		

		/* дополнительные поля спецификации */
		//'LINK_HREF'			=> $data->link, // Ссылка на ключевой объект (если необходимо куда-то направить пользователя)
		//'LINK_TEXT'			=> $data->link_text, // Текст ссылки
		//'ADDITIONAL_TEXT'		=> "Система &laquo;Чистый город&raquo; представляет собой инструмент для взаимодействия жителей города Архангельска и местных органов управления Администрации города. Она предназначена для фиксации нарушений и проблемных участков на территории города.", // Дополнительный текст (после ссылки, если она существует)
		//'BOTTON_TEXT'			=> 'Вы можете перейти к системе по ссылке: './/"С уважением, МУ МО «Город Архангельск» «Центр информационных технологий»"  // Завершающий текст в нижней части письма

		// Send mail...
		// temp Disabled
		//mail($mailTo, $data->subject, $data->text, $header);
		return true;
	}
}