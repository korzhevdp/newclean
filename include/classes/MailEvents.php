<?php

include_once($_SERVER['DOCUMENT_ROOT']."/include/mail-templates/mail_template.php");

/*

Возможные параметры в тексте письма:
1. USER_EMAIL
2. MESSAGE_ID
3. MESSAGE_STATUS_TEXT
4. MESSAGE_CATEGORY_TEXT
5. MESSAGE_TEXT
6. MESSAGE_ADRESS
 */

class  MEvents {
	
	
	public static function SendMailMessage($mailTo = null, $eventId = null, $params = array()) {
		$result['status'] = false;
		$result['message'] = 'Не удалось отправить письмо';
		$query = "SELECT * FROM `mail_events` WHERE `activity`='1'";
		$eventParams = array();
		if($results = mysqli_query(DataBase::Connect(),$query))
		{
			while($row = mysqli_fetch_assoc($results))
			{
				if($row['id'] == $eventId)
				{
					$eventParams = $row;
				}
			}
		}
		if(count($eventParams)>0)
		{
			
			$text = self::SetMailTextParameters($eventParams['text'],$params);
			$mail_to = $mailTo;
			$mail_from = $eventParams['from_email'];
			$subject = self::SetMailTextParameters($eventParams['subject'],$params);
			$link = self::SetMailTextParameters($eventParams['link'],$params);
			$link_text = self::SetMailTextParameters($eventParams['link_text'],$params);
			
			if($text!='' && $mail_to!='' && $mail_from!='' && $subject!='')
			{
				$arOptions = array();
				$arOptions = array(
								'CHARSET' 			=> 'utf-8', // Устанавливает кодировку
								'INNER_TITLE' 		=> $subject, // Устанавливает значение тега <TITLE> в структуре
								'MAIN_TEXT'   		=> $text, // Основной текст сообщения
								'LINK_HREF'   		=>  $link, // Ссылка на ключевой объект (если необходимо куда-то направить пользователя)
								'LINK_TEXT'   		=> $link_text, // Текст ссылки
								'ADDITIONAL_TEXT' 	=> "Система &laquo;Чистый город&raquo; представляет собой инструмент для взаимодействия жителей города Архангельска и местных органов управления Администрации города. Она предназначена для фиксации нарушений и проблемных участков на территории города.", // Дополнительный текст (после ссылки, если она существует)
								'BOTTON_TEXT'  		=> 'Вы можете перейти к системе по ссылке: https://gorod.arhcity.ru'//"С уважением, МУ МО «Город Архангельск» «Центр информационных технологий»"  // Завершающий текст в нижней части письма
							);
				
				$mail_content = getMailContent($arOptions); // функция возвращает контент письма
				$result = self::smtpmail($mail_to, $mail_from, $subject, $mail_content);
			}
		}
		else
		{
			$result['message'] = 'Извините, не удалось отправить письмо';
		}
		
		return $result;
	}
	
	
	static function SetMailTextParameters($text,$params) {
		if(count($params)>0)
		{
			foreach($params as $key => $param)
			{
				$text = str_replace('#'.$key.'#',$param,$text);
			}
		}
		return $text;
	}
	

	
	public static function smtpmail($mail_to = '', $mail_from = '', $subject = '', $message = '') {
			$config['smtp_port'] = '25'; 
			$config['smtp_host'] =  'mail.arhcity.ru';//'212.14.176.40';  
			$config['smtp_charset'] = 'utf-8';
			$config['from_info'] = 'Чистый город';
			$config['from_username'] = ($mail_from!='') ? $mail_from: 'cleancity@arhcity.ru';
			
			$result['status'] = false;
			$result['message'] = '';
			
			if($mail_to=='' || $mail_from=='' || $subject=='' || $message=='')
			{
				$result['message'] = 'Не все параметры письма были заполнены';
				return $result;
			}
	
			
			$to = '';
			$SEND =	"Date: ".date("D, d M Y H:i:s") . " UT\r\n";
			$SEND .= 'Subject: =?'.$config['smtp_charset'].'?B?'.base64_encode($subject)."=?=\r\n";
			$SEND .= "Reply-To: ".$config['from_username']."\r\n";
			$SEND .= "To: \"=?".$config['smtp_charset']."?B?".base64_encode($mail_to)."=?=\" <$mail_to>\r\n";
			$SEND .= "MIME-Version: 1.0\r\n";
			$SEND .= "Content-Type: text/html; charset=\"".$config['smtp_charset']."\"\r\n";
			$SEND .= "Content-Transfer-Encoding: 8bit\r\n";
			$SEND .= "From: \"=?".$config['smtp_charset']."?B?".base64_encode($config['from_info'])."=?=\" <".$config['from_username'].">\r\n";
			$SEND .= "X-Priority: 3\r\n\r\n";
	
			$SEND .=  $message."\r\n";
			if( !$socket = fsockopen($config['smtp_host'], $config['smtp_port'], $errno, $errstr, 30) )
			{
				$result['message'] = $errno.": ".$errstr;
				return $result;
			}
		
			if (!self::server_parse($config, $socket, "220", __LINE__))
			{
				$result['message'] = 'При отправке пиьсьма произошла ошибка';
				return $result;
			}
		
			fputs($socket, "HELO " . $config['smtp_host'] . "\r\n");
			if (!self::server_parse($config, $socket, "250", __LINE__))
			{
				if ($config['smtp_debug']) echo '<p>Не могу отправить HELO!</p>';
				fclose($socket);
				return false;
			}
	
			fputs($socket, "MAIL FROM: <".$config['from_username'].">\r\n");
			if (!self::server_parse($config, $socket, "250", __LINE__))
			{
				fclose($socket);
				$result['message'] = 'Не удалось успешно отправить комманду MAIL FROM:';
				return $result;
			}
			
			fputs($socket, "RCPT TO: <" . $mail_to . ">\r\n");
			if (!self::server_parse($config, $socket, "250", __LINE__))
			{
				fclose($socket);
				$result['message'] = 'Не удалось успешно отправить комманду RCPT TO:';
				return $result;
			}
			
			fputs($socket, "DATA\r\n");
			if (!self::server_parse($config, $socket, "354", __LINE__))
			{
				fclose($socket);
				$result['message'] = 'Не удалось успешно отправить комманду DATA:';
				return $result;
			}
			
			fputs($socket, $SEND."\r\n.\r\n");
			if (!self::server_parse($config, $socket, "250", __LINE__))
			{
				fclose($socket);
				$result['message'] = 'Не удалось отправить тело письма. Письмо не было отправленно!';
				return $result;
			}
			
			fputs($socket, "QUIT\r\n");
			fclose($socket);
			$result['status'] = true;
			$result['message'] = 'Письмо успешно отправлено';
			
			return $result;
	}

	static function server_parse($config, $socket, $response, $line = __LINE__)
	{
		while (@substr($server_response, 3, 1) != ' ')
		{
			if (!($server_response = fgets($socket, 256)))
			{
				return false;
			}
		}
		if (!(substr($server_response, 0, 3) == $response))
		{
			return false;
		}
		return true;
	}
	
}

?>
