<?php 

class Users {
    
    static function SendRegMessage($mailTo,$userId) {
		$result['status'] = false;
		$result['message'] = '';
		// тестирование
		$mail_to = $mailTo;//'vishnyakovns@gmail.com';
		$mail_from = 'cleancity@arhcity.ru';
		$subject = 'Успешная регистрация';
		//$message = 'Текст пиьсма';
		
		$arOptions = array();
		$arOptions = array(
						'CHARSET' 			=> 'utf-8', // Устанавливает кодировку
						'INNER_TITLE' 		=> 'Успешная регистрация', // Устанавливает значение тега <TITLE> в структуре
						'MAIN_TEXT'   		=> 'Поздравляем, Вы успешно зарегистрировались в системе &laquo;Чистый город&raquo;. Ваш email ('.$mailTo.') - это логин для входа в систему.', // Основной текст сообщения
						'LINK_HREF'   		=> 'http://cleancity.arhcity.ru', // Ссылка на ключевой объект (если необходимо куда-то направить пользователя)
						'LINK_TEXT'   		=> 'Перейти в личный кабинет', // Текст ссылки
						'ADDITIONAL_TEXT' 	=> 'Система &laquo;Чистый город&raquo; представляет собой онлайн-инструмент, позволяющий простым жителям взаимодействовать с Администрацией города, сообщая о выявленных нарушениях уборки территории и других проблемах на территории Архангельска.', // Дополнительный текст (после ссылки, если она существует)
						'BOTTON_TEXT'  		=> 'С уважением, Администрация МО "Город Архангельск"'  // Завершающий текст в нижней части письма
					);
		
		$mail_content = getMailContent($arOptions); // функция возвращает контент письма
		
		$result = self::smtpmail($mail_to, $mail_from, $subject, $mail_content);

		return $result;
	}
	
	
    public static function AuthUser($Data)
    {
        $result['status'] = false;
		$result['key'] = false;
        $query = "SELECT `password`,`pass_part`,`id` FROM `users` WHERE `email`='".$Data['login']."'";
        if($results = mysqli_query(DataBase::Connect(),$query))
        {
            if($row = mysqli_fetch_assoc($results))
            {
                $password = md5($row['pass_part'].md5($Data['password']));
                if($password == $row['password'])
                {
					
					if(self::isActiveUser($row['id']))
					{
						if(!isset($_SESSION)) {
							session_start();
						}
						
						$SSID = session_id();
						//self::SendActivationMessage();
						$SSID = md5($SSID);
						$_SESSION['SSUID'] = $SSID;
						if(setcookie('SSUID',$SSID,time()+36000000,'/'))
						{
							$result['key'] = $SSID;
							$_SESSION['UID'] = $row['id'];
							if(self::SetUserAuthTime($row['id'],$SSID))
							{
								$result['status'] = true;
								$result['message'] = 'Успешная аутентификация';
							}
							else
							{
								$result['message'] = 'В процессе аутентификации пользователя возникла ошибка';
							}
						}
					}
					else
					{
						$result['message'] = 'Ваша учетная запись еще не активирована. На ваш email было отправлено письмо для активации.';
					}
                }
                else
                $result['message'] = 'Пароль или email введены неверно';
            }
            else
            $result['message'] = 'Пароль или email введены неверно';
        }
        else
        $result['message'] = 'При попытке входа произошла системная ошибка';
        
        return $result;
    }
	
	
	public static function RegUser($Data)
    {
        $result['status'] = false;
		$result['key'] = false;
		$CaptchaData = array();
        if(isset($Data['login']) && isset($Data['alias']) && isset($Data['password']))
        {
            if(!filter_var($Data['login'], FILTER_VALIDATE_EMAIL))
            { 
                $result['message'] = 'Проверьте введенный email';
            }
            else
            {
                $query = "SELECT `id` FROM `users` WHERE `email` = '".$Data['login']."'";
                if($results = mysqli_query(DataBase::Connect(),$query))
                {
                    if($row = mysqli_fetch_assoc($results))
                    {
                        $result['message'] = 'Пользователь с таким email уже существует. Введите другой email или зайдите в личный кабинет.';
                    }
                    else
                    {
						if(isset($Data['captcha']))
						{
							$CaptchaData = array(
								"secret" => "6LcOjUkUAAAAALsw4QGuMYiTnoguhhuEnST8hS7d",
								"response" => $Data['captcha']
							);
						}
						
						
							$part = mt_rand(10000, 99999);
							$password = md5($part.md5($Data['password']));
			
							$query = "INSERT INTO users (`alias`,`email`,`password`,`pass_part`) VALUES('".self::CharacterFilter($Data['alias'])."','".self::CharacterFilter($Data['login'])."','".$password."','".$part."')";
							if($results = mysqli_query(DataBase::Connect(),$query))
							{
								
								//$mailResult = self::SendRegMessage($Data['login'],mysqli_insert_id(DataBase::Connect()));
								$result['status'] = true;
								$result['message'] = "Вы успешно зарегистрированы в системе. Если Вам необходим доступ к администритвной части, пожалуйста, позвоните по номеру 607-506 для назначения специальных прав.";
								
								$result = self::AuthUser($Data);
							}
							else
							{
								$result['message'] = "При регистрации произошла ошибка: '".$DB->error."'. Пожалуйста, попробуйте повторить позже";
							}

                    }
                }
            }
        }
        else
		{
			$result['message'] = 'Входные параметры не соответствуют ожидаемым.';
		}
        
        
        return $result;
    }
	
	
    public static function SetUserAuthTime($UserId,$key)
    {
        $result = false;
        $query = "UPDATE `users` SET `auth_date`=NOW(), `auth_key`='".$key."' WHERE `id` = '".$UserId."'";
		if($results = mysqli_query(DataBase::Connect(),$query))
		{
            $result = true;
        }
        return $result;
    }
    
	
	public static function isActiveUser($id)
    {
        $result = false;
        $query = "SELECT * FROM `users` WHERE `id` = '".$id."' AND `activity`='1'";
        if($results = mysqli_query(DataBase::Connect(),$query))
        {
            if($row = mysqli_fetch_assoc($results))
            {
                $result = true;
            }
        }
        
        return $result;
    }
	

	public static function CharacterFilter($str)
    {
        $res = htmlspecialchars($str);
        $res = mysqli_real_escape_string(DataBase::Connect(),$res);
        return $res;
    }
	
	
	public static function GetUserById($id = 0)
    {
        $result = false;

        $query = "SELECT
            user.`id` as user_id,
            user.`alias` as user_name,
			user.`email` as email,
            dep.`name` as department,
            user.`department_id` as department_id,
            gp.`id` as group_id,
            gp.`caption` as group_caption,
            distr.`id` as district_id,
            distr.`name` as district
        FROM `users` as user LEFT JOIN `departments` as dep ON dep.`id` = user.`department_id`
        LEFT JOIN `user_groups` as gp ON gp.`id` = user.`group_id`
        LEFT JOIN `city_districts` as distr ON distr.`responsible` = dep.`id`
        WHERE user.`id`='".$id."'";
        if($results = mysqli_query(DataBase::Connect(),$query))
        {
            $arUser = array();
            if($row = mysqli_fetch_assoc($results))
            {
                $arUser[$row['user_id']] = $row;
            }
        } 
        $result = $arUser;
        
        return $result;
    }
	
	public static function isAuthorized($key) // проверка авторизации пользователя
    {
		$result['status'] = false;
		if($key==null && $key =='')
		{
			return $result;
		}
		$query = "SELECT * FROM `users` WHERE `auth_key` = '".$key."' AND `activity`='1'";
        if($results = mysqli_query(DataBase::Connect(),$query))
        {
            if($row = mysqli_fetch_assoc($results))
            {
               $result['status'] = true;
            }
        }
        
        return $result;
    }

}


?>