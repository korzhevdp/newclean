<?php 


class Users {
    
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
			org.`name` as org_name,
            distr.`id` as district_id,
            distr.`name` as district
        FROM `users` as user LEFT JOIN `departments` as dep ON dep.`id` = user.`department_id`
        LEFT JOIN `user_groups` as gp ON gp.`id` = user.`group_id`
        LEFT JOIN `city_districts` as distr ON distr.`responsible` = dep.`id`
		LEFT JOIN `organization` as org ON user.`org_id` = org.`id`
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
    
    
    public static function GeneratePassPart()
    {
        $length = 5;
        $str = '';
        for($i = 0; $i < $length; ++$i)
        {
            $first = $i ? 0 : 1;
            $n = mt_rand($first, 9);
            $str .= $n;
        }
        return $str;
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
    
    
    public static function SetUserAuthTime($UserId,$key)
    {
        $result = false;
        $query = "UPDATE `users` SET `auth_date`=NOW(), `auth_key`='".self::CharacterFilter($key)."' WHERE `id` = '".self::CharacterFilter($UserId)."'";
		if($results = mysqli_query(DataBase::Connect(),$query))
		{
            $result = true;
        }
        return $result;
    }
        
	
	
    public static function AuthUser($Data)
    {
        $result['status'] = false;
		$result['key'] = false;
        $query = "SELECT `password`,`pass_part`,`id` FROM `users` WHERE `email`='".$Data['email']."'";
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
						$SSID = md5($SSID.$Data['email']);
						$_SESSION['SSUID'] = $SSID;
						if(setcookie('SSUID',$SSID,time()+36000000,'/'))
						{
							$result['key'] = $SSID;
							$_SESSION['UID'] = $row['id'];
							if(self::SetUserAuthTime($row['id'],$SSID))
							{
								$result['status'] = true;
								$result['message'] = 'Успешная аутентификация';
								MSystem::SaveActionHistory($row['id'],0,'userAuth','Пользователь авторизовался');
							}
							else
							{
								$result['message'] = 'В процессе аутентификации пользователя возникла ошибка';
							}
						}
					}
					else
					{
						$result['message'] = 'Извините, Ваша учетная запись была заблокирована.';
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
    
	
	
	static function getCaptchaSuccess($CaptchaData) {
		$result = false;
		$ch = curl_init('https://www.google.com/recaptcha/api/siteverify');
		$data_string = "secret=".$CaptchaData['secret']."&response=".$CaptchaData['response'];
		curl_setopt($ch, CURLOPT_POST, true); //переключаем запрос в POST
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string); //Это POST данные
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //Отключим проверку сертификата https
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //из той же оперы
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER , true); 
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION , true);
		$CURL_Result = curl_exec($ch);
		$CURL_Error = curl_errno($ch);
	  
		if ($CURL_Error > 0)
		{
		  $result['message'] = 'cURL Error: '.$CURL_Error;
		}
		else
		{
		  $result = $CURL_Result;
		}
		curl_close($ch);
		
		return $result;
	}
	
	
	
    public static function RegUser($Data)
    {
        $result['status'] = false;
		$result['message'] = 'Не удалось зарегистрировать учетную запись';
        if(isset($Data['email']) && isset($Data['alias']) && isset($Data['password']) && isset($Data['captcha']))
        {
			
			if(!isset($_SESSION['CAPTCHA']))
			{
				$result['message'] = 'Не удалось определить CAPTCHA';
				return $result;
			}
			else
			{
				if(strtolower($Data['captcha'])!=strtolower($_SESSION['CAPTCHA']))
				{
					$result['message'] = 'Код с картинки введен неверно';
					return $result;
				}
				else
				{
					$_SESSION['CAPTCHA'] = md5($Data['email']);
				}
			}
			
            if(!filter_var($Data['email'], FILTER_VALIDATE_EMAIL))
            { 
                $result['message'] = 'Проверьте введенный email';
            }
            else
            {
                $query = "SELECT `id` FROM `users` WHERE `email` = '".$Data['email']."'";
                if($results = mysqli_query(DataBase::Connect(),$query))
                {
                    if($row = mysqli_fetch_assoc($results))
                    {
                        $result['message'] = 'Пользователь с таким email уже существует. Введите другой email или зайдите в личный кабинет';
                    }
                    else
                    {
						$CaptchaData = array(
							"secret" => "6LcOjUkUAAAAALsw4QGuMYiTnoguhhuEnST8hS7d",
							"response" => $Data['captcha']
						);

							$part = self::GeneratePassPart();
							$password = md5($part.md5($Data['password']));
							$current_datetime = date("Y-m-d H:i:s");
							$query = "INSERT INTO users (`alias`,`email`,`phone`,`password`,`pass_part`,`reg_date`) VALUES('".self::CharacterFilter($Data['alias'])."','".self::CharacterFilter($Data['email'])."','".self::CharacterFilter($Data['phone'])."','".$password."','".$part."','".$current_datetime."')";
							if($results = mysqli_query(DataBase::Connect(),$query))
							{
								
								//$mailResult = MEvents::SendRegMessage($Data['email'],0);
								$mailParams = array(
									"USER_EMAIL" => $Data['email']	
								);
								
								$mailResult = MEvents::SendMailMessage($Data['email'],1,$mailParams);
								$result['status'] = true;
								$result['message'] = "Вы успешно зарегистрировались в системе. Для создания нового сообщения о выявленных нарушениях войдите в личный кабинет";
								self::AuthUser($Data);
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
			$result['message'] = 'Входные параметры не соответствуют ожидаемым';
		}
        
        
        return $result;
    }
	
	
	
    public static function RegAdmin($Data)
    {
        $result['status'] = false;
		$result['message'] = 'Не удалось зарегистрировать учетную запись';
        if(isset($Data['email']) && isset($Data['alias']) && isset($Data['password']) && isset($Data['captcha']))
        {
			
			if(!isset($_SESSION['CAPTCHA']))
			{
				$result['message'] = 'Не удалось определить CAPTCHA';
				return $result;
			}
			else
			{
				if(strtolower($Data['captcha'])!=strtolower($_SESSION['CAPTCHA']))
				{
					$result['message'] = 'Код с картинки введен неверно';
					return $result;
				}
				else
				{
					$_SESSION['CAPTCHA'] = md5($Data['email']);
				}
			}
			
            if(!filter_var($Data['email'], FILTER_VALIDATE_EMAIL))
            { 
                $result['message'] = 'Проверьте введенный email';
            }
            else
            {
                $query = "SELECT `id` FROM `users` WHERE `email` = '".$Data['email']."'";
                if($results = mysqli_query(DataBase::Connect(),$query))
                {
                    if($row = mysqli_fetch_assoc($results))
                    {
                        $result['message'] = 'Пользователь с таким email уже существует. Введите другой email или зайдите в личный кабинет';
                    }
                    else
                    {
						$CaptchaData = array(
							"secret" => "6LcOjUkUAAAAALsw4QGuMYiTnoguhhuEnST8hS7d",
							"response" => $Data['captcha']
						);
						
							$part = self::GeneratePassPart();
							$password = md5($part.md5($Data['password']));
							$current_datetime = date("Y-m-d H:i:s");
							$query = "INSERT INTO users (`alias`,`email`,`phone`,`password`,`pass_part`,`reg_date`) VALUES('".self::CharacterFilter($Data['alias'])."','".self::CharacterFilter($Data['email'])."','".self::CharacterFilter($Data['phone'])."','".$password."','".$part."','".$current_datetime."')";

							if($results = mysqli_query(DataBase::Connect(),$query))
							{
								
								$mailParams = array(
									"USER_EMAIL" => $Data['email']	
								);
								
								$mailResult = MEvents::SendMailMessage($Data['email'],2,$mailParams);
								$result['status'] = true;
								$result['message'] = "Вы успешно зарегистрированы в системе. Если Вам необходим доступ к административной части, пожалуйста, позвоните по номеру 607-506 для назначения специальных прав.";
								self::AuthUser($Data);
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
			$result['message'] = 'Входные параметры не соответствуют ожидаемым';
		}
        
        
        return $result;
    }
    
    

	
	/*********************************************   MAIL FUNCTIONS   ***************************************************/
	
		
	
    public static function isSupervisoryByGroup($GroupId) // пользователи, которые видят все объекты на карте в административной части (например контролирующие органы)
    {
        $result = false;
        if(in_array($GroupId,array(4)))
        {
            $result = true;
        }
        
        return $result;
    }
    
    
    public static function isResponsibleUnit($GroupId) // пользователи, относящиеся к одному из ответственных подразделений
    { 
        $result = false;
        if(in_array($GroupId,array(2)))
        {
            $result = true;
        }
        
        return $result;
    }
	
	public static function isOrganization($GroupId) // пользователи, относящиеся к одной из ответственных организаций
    { 
        $result = false;
        if(in_array($GroupId,array(6)))
        {
            $result = true;
        }
        
        return $result;
    }
    
    
    public static function isAdmin($GroupId=null) // системные администраторы
    { 
        $result = false;
		if($GroupId==null)
		{
			if(isset($_SESSION['UID']) && $_SESSION['UID']!=null)
			{
				$arUser = self::GetUserById($_SESSION['UID']);
				$GroupId = $arUser[$_SESSION['UID']]['group_id'];
			}
		}
		
        if(in_array($GroupId,array(3)))
        {
            $result = true;
        }
        
        return $result;
    }
    
    public static function isAllEditRight($GroupId) // пользователи, имеющие возможность изменять любые свойства сообщений
    { 
        $result = false;
        if(in_array($GroupId,array(3,4)))
        {
            $result = true;
        }
        
        return $result;
    }
	
	public static function isAuthorized() // проверка авторизации пользователя
    { 
        $result = false;
        if(isset($_SESSION['SSUID']) && $_SESSION['SSUID']!=null)
		{
			$result = true;
		}
        
        return $result;
    }
	
	public static function isAuthorizedByKey($key) // проверка авторизации пользователя по ключу
    {
		$result['status'] = false;
		$result['id'] = false;
		if($key==null && $key =='')
		{
			return $result;
		}
		$query = "SELECT `id` FROM `users` WHERE `auth_key` = '".$key."' AND `activity`='1'";
        if($results = mysqli_query(DataBase::Connect(),$query))
        {
            if($row = mysqli_fetch_assoc($results))
            {
				$_SESSION['SSUID'] = $key;
               $result['status'] = true;
			   $result['id'] = $row['id'];
            }
        }
        
        return $result;
    }
	
	
	public static function userPasswordRecovery($email,$userType = 0) // проверка авторизации пользователя по ключу
    {
		$result['status'] = false;
		$result['message'] = 'Не удалось отправить письмо на указанный email';
		if(!self::isUserExist($email)) return $result;
		$current_date = date("Y-m-d H:i:s");
		$recovery_code = md5($email.$current_date.rand(1,5));
		$query = "UPDATE `users` SET `rec_pass_uid`='".$recovery_code."' WHERE `email`='".self::CharacterFilter($email)."' AND `activity`='1'";
        if($results = mysqli_query(DataBase::Connect(),$query))
        {
			$mailParams = array(
				"RECOVERY_KEY" => $recovery_code	
			);
			if($userType==2)
			{
				$mailResult = MEvents::SendMailMessage($email,6,$mailParams);
			}
			else
			{
				$mailResult = MEvents::SendMailMessage($email,3,$mailParams);
			}
			
            $result['status'] = $mailResult['status'];
			$result['message'] = $mailResult['message'];
        }
        
        return $result;
    }
	
	
	static function isUserExist($email) 
    {
		$result = false;
		$query = "SELECT `id` FROM `users` WHERE `email`='".$email."' AND `activity`='1'";
        if($results = mysqli_query(DataBase::Connect(),$query))
        {
            if($row = mysqli_fetch_assoc($results))
            {
				$result = true;
            }
        }
        
        return $result;
    }
	    
		
	static function userCount() 
    {
		$result = false;
		$query = "SELECT count(`id`) as count FROM `users` WHERE `activity`='1'";
        if($results = mysqli_query(DataBase::Connect(),$query))
        {
            if($row = mysqli_fetch_assoc($results))
            {
				$result = $row['count'];
            }
        }
        
        return $result;
    }
	
		
    public static function isMainUserByGroup($GroupId) // все, имеющие доступ к административной части
    {  
        $result = false;
		if(is_numeric($GroupId))
		{
			$query = "SELECT `id` FROM `user_groups` WHERE `law7`=1 AND `id`=".$GroupId;
			if($results = mysqli_query(DataBase::Connect(),$query))
			{
				$result = true;
			}
		}
        
        return $result;
    }
	
	
	
	public static function changePassword($login,$userId,$currentPassword,$newPassword) {
		$result['status'] = false;
		$result['message'] = '';
		$Data = array(
			"email" => $login,
			"password" => $currentPassword
		);
		
		$userAuth = self::AuthUser($Data);
		if($userAuth['status'])
		{
			//$part = self::GeneratePassPart();
			$part = mt_rand(10000, 99999);
			$password = md5($part.md5($newPassword));
	
			$query = "UPDATE `users` SET `password`='".$password."', `pass_part`='".$part."' WHERE `id` = '".$userId."'";

			if($results = mysqli_query(DataBase::Connect(),$query))
			{
				
				//$mailResult = self::SendRegMessage($Data['email'],mysqli_insert_id(DataBase::Connect()));
				$result['status'] = true;
				$result['message'] = "Пароль успешо изменен.";

			}
			else
			{
				$result['message'] = 'При попытке сохранения нового пароля произошла ошибка.';
			}
		}
		else
		{
			$result['message'] = $userAuth['message'];
		}
		
		return $result;
	}
	
	
	
	public static function newPasswordAfterRecovery($password,$recovery_key) {
		$result['status'] = false;
		$result['message'] = 'При попытке сохранения нового пароля произошла неизвестная ошибка';
				
		$part = mt_rand(10000, 99999);
		$password = md5($part.md5($password));
		
		$isKeyOk = self::getUserLoginByReqKey($recovery_key);
		
		if($isKeyOk)
		{
			$query = "UPDATE `users` SET `password`='".$password."', `pass_part`='".$part."', `rec_pass_uid`= NULL WHERE `rec_pass_uid`='".$recovery_key."'";
			if($results = mysqli_query(DataBase::Connect(),$query))
			{
				$result['status'] = true;
				$result['message'] = "Пароль успешо изменен";
			}
			else
			{
				$result['message'] = "При попытке сохранения пароля возникла ошибка (".mysqli_errno(DataBase::Connect()).")";
			}
		}
		else
		{
			$result['message'] = 'Ключь восстановления устарел. Попробуйте начать с первого шага.';
		}

		return $result;
	}
	

	public static function UserLawByGroup($GroupId,$lawType) // проверка прав пользователя на выполнение различных задач
    {  
        $result = false;
		if(is_numeric($GroupId))
		{
			 if(in_array($lawType,array("law1","law2","law3","law4","law4_1","law5","law6","law7","law8","law9")))
			 {
				$query = "SELECT `id` FROM `user_groups` WHERE `".$lawType."`='1' AND `id`='".$GroupId."'";
				if($results = mysqli_query(DataBase::Connect(),$query))
				{
					if($row = mysqli_fetch_assoc($results))
					{
						$result = true;
					}
				}
			 }
			
		}
        
        return $result;
    }
	
	
	
	
	public static function setUserOptions($user_id,$option_id,$value,$value_id=null) // активация опции пользователем
    {
        $result = false;
				
		$query = "SELECT id FROM `user_set_options` WHERE `user_id`='".$user_id."' AND `option_id`='".$option_id."'";
		/*if($value_id!=null)
		{
			$query .= " AND `value`='".$value_id."'";
		}
		*/
		if($results = mysqli_query(DataBase::Connect(),$query))
		{
			if($row = mysqli_fetch_assoc($results))
			{
				if($value==0 && ($value_id == null || $value_id == 0))
				{
					$query = "DELETE FROM `user_set_options` WHERE `user_id`='".$user_id."' AND `option_id`='".$option_id."'";
					if($results = mysqli_query(DataBase::Connect(),$query))
					{
						$result = true;
					}
				}
				else
				{
					$query = "UPDATE `user_set_options` SET `value`='".$value_id."' WHERE `user_id`='".$user_id."' AND `option_id`='".$option_id."'";
					if($results = mysqli_query(DataBase::Connect(),$query))
					{
						$result = true;
					}
				}
			}
			else
			{
				if($value!=0)
				{
					$query = "INSERT INTO `user_set_options` (`user_id`,`option_id`,`value`) VALUES ('".$user_id."','".$option_id."','".$value_id."')";
					if($results = mysqli_query(DataBase::Connect(),$query))
					{
						$result = true;
					}
				}
				
			}
		}

        return $result;
    }
	
	
	
	public static function getUserOptions($user_id) // все, имеющие доступ к административной части
    {  
        $result = false;
		if(is_numeric($user_id))
		{
			$arUserOptions = array();
			$query = "SELECT `id`,`option_id`,`value` FROM `user_set_options` WHERE `user_id`='".$user_id."'";
			if($results = mysqli_query(DataBase::Connect(),$query))
			{
				while($row = mysqli_fetch_assoc($results))
				{
					$arUserOptions[$row['option_id']]['value'] = $row['value'];
				}
			}
		}
		
		$result = $arUserOptions;
        
        return $result;
    }
	
	
	
	public static function getUserLoginByReqKey($key) 
    {  
        $result = false;
		$query = "SELECT `email`,`id` FROM `users` WHERE `rec_pass_uid`='".self::CharacterFilter($key)."'";
		if($results = mysqli_query(DataBase::Connect(),$query))
		{
			while($row = mysqli_fetch_assoc($results))
			{
				$result = $row;
			}
		}
        
        return $result;
    }

}


?>
