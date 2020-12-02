<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mail extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->helper("url");
		$this->load->model("usermodel");
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
		$this->load->view('admin/admin');
	}

	public function SendMailMessage($eventID = 5, $messageID = 1703) {
		$result = array(
			'status'  => false,
			'message' => 'Не удалось отправить письмо'
		);

		$result = $this->db->query("SELECT
			mail_events.event_name,
			mail_events.subject,
			mail_events.`text`,
			mail_events.link,
			mail_events.link_text,
			mail_events.from_email,
			mail_events.activity,
			mail_events.update_time,
			(SELECT `users`.email FROM `users` RIGHT OUTER JOIN messages ON (`users`.id = messages.user_id) WHERE (messages.id = ?)) AS `mailTo`,
			(SELECT `messages`.message FROM `messages` WHERE `messages`.id = ?) AS messageText,
			(SELECT `messages`.address FROM `messages` WHERE `messages`.id = ?) AS messageAddress,
			'' AS messageStatus,
			?  AS messageID
			FROM `mail_events`
			WHERE `activity`     = 1
			AND `mail_events`.id = ?
			LIMIT 1", array( $messageID, $messageID, $messageID, $messageID, $eventID ));
		if ($result->num_rows()) {
			$row = $result->row_array();
			$this->sendMail( $eventID, $row );
		}
	}
		
	private function sendMail( $eventID, $data ) {
		// Basic fields
		$mailSubject = $data['subject'];
		$encoding    = "utf-8";
		$fromName    = "ИС Чистый город";
		$fromMail    = "cleancity@arhcity.ru";
		$bcc         = "";

		//apppcds29@yandex.ru
		//bondarev@dvinabus.ru

		// Mail headers
		$header  = "Content-type: text/html; charset=".$encoding." \r\n";
		$header .= "From: ".$fromName." <".$fromMail."> \r\nBcc: ".$bcc."\r\n";
		$header .= "MIME-Version: 1.0 \r\n";
		$header .= "Content-Transfer-Encoding: 8bit \r\n";
		$header .= "Return-Path: <cleancity@arhcity.ru>\r\n";
		$header .= "Reply-To: <cleancity@arhcity.ru>\r\n";
		$header .= "Date: ".date("r (T)")." \r\n";
		//$header .= "Subject: =?".$encoding."?".$mailSubject."?=";
		
		//if ($test) {
			$data['recoveryAppendix'] = ($eventID)""
			$data['eventText']    = $this->load->view("mail/event".$eventID, $data, true);
			$data['linkSet']      = $this->load->view("mail/linkset", $data, true);
			$data['furtherText']  = "";
			$data['footerText']   = "";
			$this->load->view("mail/template", $data);
			return true;
		//}
		/* дополнительные поля спецификации */
		//'LINK_HREF'			=> $data->link,			// Ссылка на ключевой объект (если необходимо куда-то направить пользователя)
		//'LINK_TEXT'			=> $data->link_text,	// Текст ссылки
		//'ADDITIONAL_TEXT'		=> "Система &laquo;Чистый город&raquo; представляет собой инструмент для взаимодействия жителей города Архангельска и местных органов управления Администрации города. Она предназначена для фиксации нарушений и проблемных участков на территории города.", // Дополнительный текст (после ссылки, если она существует)
		//'BOTTON_TEXT'			=> 'Вы можете перейти к системе по ссылке: './/"С уважением, МУ МО «Город Архангельск» «Центр информационных технологий»"  // Завершающий текст в нижней части письма

		// Send mail...
		// temp Disabled
		//mail($mailTo, $mailSubject, $data->text, $header);
	}
}
