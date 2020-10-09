<?php

class MSystem {
	
	
	public static function GetReference()
    {
        $result = false;
        $query = "SELECT * FROM `reference` WHERE `active` = '1'";
        if($results = mysqli_query(DataBase::Connect(),$query))
        {
            $arRef = array();
            while($row = mysqli_fetch_assoc($results))
            {
               $arRef[$row['id']] = $row;
            }
            $result = $arRef;
        }
        
        return $result;
    }
	
	
	static function GetCountByCategoryId($catId)
	{
		$result = false;
		$query = "SELECT count(`id`) as count FROM `messages` WHERE `category_id`='".$catId."'"; //AND `archive`='0'
		if($results = mysqli_query(DataBase::Connect(),$query))
			if($row = mysqli_fetch_assoc($results))
				$result = $row['count'];
		return $result;
	}
	
	static function GetCountByStatusId($statusId)
	{
		$result = false;
		$query = "SELECT count(`id`) as count FROM `messages` WHERE `status_id`='".$statusId."'"; //AND `archive`='0'
		if($results = mysqli_query(DataBase::Connect(),$query))
			if($row = mysqli_fetch_assoc($results))
				$result = $row['count'];
		return $result;
	}
	
	static function GetCountByDistrictId($distrId)
	{
		$result = false;
		$query = "SELECT count(`id`) as count FROM `messages` WHERE `district_id`='".$distrId."'"; //AND `archive`='0'
		if($results = mysqli_query(DataBase::Connect(),$query))
			if($row = mysqli_fetch_assoc($results))
				$result = $row['count'];
		return $result;
	}
	
	public static function GetStatByDistrictId($distrId)
	{
		$result = false;
		$arResult = array();
		$query = "SELECT `status_id` as STATUS_ID, count(id) as COUNT FROM `messages` WHERE `district_id`='".$distrId."' && `removed`=0 GROUP BY `status_id`"; //AND `archive`='0'
		if($results = mysqli_query(DataBase::Connect(),$query))
		{
			while($row = mysqli_fetch_assoc($results))
			{
				$arResult[] = $row;
			}
			
			$result = $arResult;
		}
		
		return $result;
	}
	
	
	public static function GetDepartments()
  {
			$result = false;
			
			$query = "SELECT `id`,`name` FROM `departments` WHERE `is_depart`='1'";
			if($results = mysqli_query(DataBase::Connect(),$query))
			{
					$arDepart = array();
					while($row = mysqli_fetch_assoc($results))
					{
						 $arDepart[$row['id']] = $row['name'];
					}
					$result = $arDepart;
			} 
			return $result;
  }
	
	public static function GetStatDepartmentByStatus($departId)
	{
		$result = false;
		$arResult = array();
		$query = "SELECT m.`status_id` as STATUS_ID, dp.`id` as DEPARTMENT_ID, count(m.`id`) as COUNT FROM `messages` as m
		LEFT JOIN `city_districts` as cd ON cd.`id` = m.`district_id`
		LEFT JOIN `departments` as dp ON dp.`id` = cd.`responsible`
		LEFT JOIN `message_category` as mc ON mc.`id` = m.`category_id`
		WHERE dp.`id`='".$departId."' && m.`status_id`!='5' && m.`category_id`!='6'  && m.`category_id`!='17' && mc.`activity`='1' && m.`removed`=0
		GROUP BY m.`status_id`"; //AND `archive`='0'
		if($results = mysqli_query(DataBase::Connect(),$query))
		{
			$arStatus = array();
			$allcount = 0;
			while($row = mysqli_fetch_assoc($results))
			{
				$arStatus[$row['STATUS_ID']]['COUNT'] = $row['COUNT'];
				$allcount += $row['COUNT'];
			}
			$arResult[$departId]['STATUS'] = $arStatus;
			$arResult[$departId]['ALL_COUNT'] = $allcount;
			$result = $arResult;
		}
		
		return $result;
	}
	
	public static function GetStatDepartmentCategory($departId)
	{
		$result = false;
		$arResult = array();
		$query = "SELECT
			m.`category_id` as CATEGORY_ID,
			mc.`name` as CATEGORY_NAME,
			m.`status_id` as STATUS_ID,
			dp.`id` as DEPARTMENT_ID,
			count(m.`id`) as COUNT
		FROM `messages` as m
			LEFT JOIN `city_districts` as cd ON cd.`id` = m.`district_id`
			LEFT JOIN `departments` as dp ON dp.`id` = cd.`responsible`
			LEFT JOIN `message_category` as mc ON mc.`id` = m.`category_id`
		WHERE dp.`id`='".$departId."' && m.`status_id`!='5' && m.`category_id`!='6'  && m.`category_id`!='17' && mc.`activity`='1' && m.`removed`=0
		GROUP BY m.`category_id`, m.`status_id`"; //AND `archive`='0'
		if($results = mysqli_query(DataBase::Connect(),$query))
		{
			$arCategory = array();
			$allcount = 0;
			$success_count = 0;
			$time_success_count = 0;
			while($row = mysqli_fetch_assoc($results))
			{
				$arCategory[$row['CATEGORY_ID']]['DATA']['STATUS'][$row['STATUS_ID']]['COUNT'] = $row['COUNT'];
				$arCategory[$row['CATEGORY_ID']]['NAME'] = $row['CATEGORY_NAME'];
				if(!isset($arCategory[$row['CATEGORY_ID']]['COUNT']))
					$arCategory[$row['CATEGORY_ID']]['COUNT'] = 0;
					
				if($row['STATUS_ID']==2)
				{
					$success_count += $row['COUNT'];
				}

				$arCategory[$row['CATEGORY_ID']]['COUNT'] += $row['COUNT'];
				$allcount += $row['COUNT'];
			}
			$arResult[$departId]['CATEGORY'] = $arCategory;
			$arResult[$departId]['ALL_COUNT'] = $allcount;
			$arResult[$departId]['SUCCESS_COUNT'] = $success_count;
			$result = $arResult;
		}
		
		//print_r($arCategory);
		return $result;
	}
	
	
	public static function GetMessageCountByStatus() {
			$result = false;
			
			$query = "SELECT count(m.`id`) as count,m.`status_id` FROM `messages` as m
									LEFT JOIN `message_category` as cat ON cat.`id` = m.`category_id`
									LEFT JOIN `message_status` as st ON st.`id` = m.`status_id`
								WHERE cat.`activity` = 1 && m.`removed`=0 && st.`activity`=1
								GROUP BY m.`status_id`";
			$arResult = array();
			$all_count = 0;
			if($results = mysqli_query(DataBase::Connect(),$query))
			{
				while($row = mysqli_fetch_assoc($results))
				{
						$arResult[$row['status_id']] = $row['count'];
						$all_count += $row['count'];
				}
				$arResult['ALL_COUNT'] = $all_count;
				$result = $arResult;
			}
			
			return $result;
	}
	
	
	
	public static function GetMessageStatistic()
    {
        $result = false;
        $query = "SELECT
						m.`id` as ID,
						m.`category_id` as CATEGORY_ID,
						cat.`name` as CATEGORY_NAME,
						m.`district_id` as DISTRICT_ID,
						distr.`name` as DISTRICT_NAME,
						m.`status_id` as STATUS_ID,
						dep.`ID` as DEPARTMENT_ID,
						dep.`NAME` as DEPARTMENT_NAME,
						st.`name` as STATUS_NAME,
						m.`archive` as ARCHIVE,
						ah.`action_code` as ACTION,
						ah.`value_id`
					FROM `messages` as m
						LEFT JOIN `message_category` as cat ON cat.`id` = m.`category_id`
						LEFT JOIN `city_districts` as distr ON distr.`id` = m.`district_id`
						LEFT JOIN `departments` as dep ON dep.`id` = distr.`responsible`
						LEFT JOIN `message_status` as st ON st.`id` = m.`status_id`
						LEFT JOIN `action_history` as ah ON ah.`message_id` = m.`id`
					WHERE cat.`activity` = 1 && m.`removed`=0
					";
				$arStat = array();
        if($results = mysqli_query(DataBase::Connect(),$query))
        {
						$arCategory = array();
						$arDepartment = array();
						$arDistrict = array();
						$arStatus = array();
						while($row = mysqli_fetch_assoc($results))
						{
							$arStat['MESSAGES'][$row['ID']] = $row;
							if($row['CATEGORY_ID']!=0 && !isset($arCategory[$row['CATEGORY_ID']]))
							{
								$arCategory[$row['CATEGORY_ID']]['NAME'] = $row['CATEGORY_NAME'];
								$arCategory[$row['CATEGORY_ID']]['COUNT'] = self::GetCountByCategoryId($row['CATEGORY_ID']);
							}
							
							if($row['DISTRICT_ID']!=0 && !isset($arDistrict[$row['DISTRICT_ID']]))
							{
								$arDistrict[$row['DISTRICT_ID']]['NAME'] = $row['DISTRICT_NAME'];
								$arDistrict[$row['DISTRICT_ID']]['COUNT'] = self::GetCountByDistrictId($row['DISTRICT_ID']);
							}
							if($row['STATUS_ID']!=0 && !isset($arStatus[$row['STATUS_ID']]))
							{
								$arStatus[$row['STATUS_ID']]['NAME'] = $row['STATUS_NAME'];
								$arStatus[$row['STATUS_ID']]['COUNT'] = self::GetCountByStatusId($row['STATUS_ID']);
							}
							if($row['DEPARTMENT_ID']!=0 && !isset($arDepartment[$row['DEPARTMENT_ID']]))
							{
								$arDepartment[$row['DEPARTMENT_ID']]['NAME'] = $row['DEPARTMENT_NAME'];
								//$arDepartment[$row['DEPARTMENT_ID']]['COUNT'] = self::GetCountByStatusId($row['STATUS_ID']);
							}
							
						}
						$arStat['CATEGORIES'] = $arCategory;
						$arStat['DISTRICTS']  = $arDistrict;
						$arStat['STATUSES']   = $arStatus;
						$arStat['DEPARTMENT'] = $arDepartment;
						
						$result = $arStat;
				}

        return $result;
    }
	
	
	
	
	
		public static function GetMessageDepartStatistic()
    {
        $result = false; //						LEFT JOIN `departments` as dep1 ON dep1.`id` = m.`depart_id`
        $query = "SELECT
						m.`id` as ID,
						m.`category_id` as CATEGORY_ID,
						cat.`name` as CATEGORY_NAME,
						m.`district_id` as DISTRICT_ID,
						distr.`name` as DISTRICT_NAME,
						m.`status_id` as STATUS_ID,
						dep.`ID` as DEPARTMENT_ID,
						dep.`NAME` as DEPARTMENT_NAME,
						dep1.`ID` as DEPARTMENT_ID1,
						dep1.`NAME` as DEPARTMENT_NAME1,
						st.`name` as STATUS_NAME,
						m.`archive` as ARCHIVE,
						ah.`action_code` as ACTION,
						ah.`value_id`
					FROM `messages` as m
						LEFT JOIN `message_category` as cat ON cat.`id` = m.`category_id`
						LEFT JOIN `city_districts` as distr ON distr.`id` = m.`district_id`
						LEFT JOIN `departments` as dep ON dep.`id` = distr.`responsible`
						LEFT JOIN `departments` as dep1 ON dep1.`id` = m.`depart_id`
						LEFT JOIN `message_status` as st ON st.`id` = m.`status_id`
						LEFT JOIN `action_history` as ah ON ah.`message_id` = m.`id`
					WHERE cat.`activity` = 1 && m.`status_id`!='5' && m.`category_id`!='6'  && m.`category_id`!='17'
					";
				$arStat = array();
        if($results = mysqli_query(DataBase::Connect(),$query))
        {
						$arCategory = array();
						$arDepartment = array();
						$arDistrict = array();
						$arStatus = array();
						while($row = mysqli_fetch_assoc($results))
						{
							//print_r($row);
							$arStat['MESSAGES'][$row['ID']] = $row;
							if($row['CATEGORY_ID']!=0 && !isset($arCategory[$row['CATEGORY_ID']]))
							{
								$arCategory[$row['CATEGORY_ID']]['NAME'] = $row['CATEGORY_NAME'];
								$arCategory[$row['CATEGORY_ID']]['COUNT'] = self::GetCountByCategoryId($row['CATEGORY_ID']);
							}
							
							if($row['DISTRICT_ID']!=0 && !isset($arDistrict[$row['DISTRICT_ID']]))
							{
								$arDistrict[$row['DISTRICT_ID']]['NAME'] = $row['DISTRICT_NAME'];
								$arDistrict[$row['DISTRICT_ID']]['COUNT'] = self::GetCountByDistrictId($row['DISTRICT_ID']);
							}
							if($row['STATUS_ID']!=0 && !isset($arStatus[$row['STATUS_ID']]))
							{
								$arStatus[$row['STATUS_ID']]['NAME'] = $row['STATUS_NAME'];
								$arStatus[$row['STATUS_ID']]['COUNT'] = self::GetCountByStatusId($row['STATUS_ID']);
							}
							if($row['DEPARTMENT_ID']!=0 && !isset($arDepartment[$row['DEPARTMENT_ID']]))
							{
								$arDepartment[$row['DEPARTMENT_ID']]['NAME'] = $row['DEPARTMENT_NAME'];
								//$arDepartment[$row['DEPARTMENT_ID']]['COUNT'] = self::GetCountByStatusId($row['STATUS_ID']);
							}
							
						}
						$arStat['CATEGORIES'] = $arCategory;
						$arStat['DISTRICTS']  = $arDistrict;
						$arStat['STATUSES']   = $arStatus;
						$arStat['DEPARTMENT'] = $arDepartment;
						
						$result = $arStat;
				}

        return $result;
    }
		
		
	
		public static function GetMessageSuccsessInfo($id) // информация о сроке выполненеия
		{
				$result = false;
				$query = "
					SELECT
						m.`create_time`,
						ah.`value_id` as STATUS,
						MAX(ah.`time`) as TIME
					FROM `action_history` as ah
						LEFT JOIN `messages` as m ON ah.`message_id` = m.`id`
					WHERE ah.`message_id` = '".$id."' AND ah.`action_code`='statusChange'
				";
				$arResult = array();
        if($results = mysqli_query(DataBase::Connect(),$query))
        {
						if($row = mysqli_fetch_assoc($results))
						{
							if($row['STATUS']==2)
							{
								$arResult = $row;
								$result = $arResult;
							}
						}
				}

				
        return $result;
		}
	
	
		public static function SendFeedback($user_id,$subject,$text,$file_path = null)
    {
        $result['status'] = false;
				$result['message'] = 'При отправке сообщения возникли ошибки.';
				$user_device = $_SERVER["HTTP_USER_AGENT"];
				
				$query = "INSERT INTO `feedback` (`user_id`,`subject`,`text`,`file_path`,`device`) VALUES ('".$user_id."','".$subject."','".$text."','".$file_path."','".$user_device."')";
				if($results = mysqli_query(DataBase::Connect(),$query))
				{
						$result['status'] = true;
						$result['message'] = 'Сообщение успешно отправлено в техническую поддержку.';
				}
        
        return $result;
    }
		
		
		
		public static function SaveActionHistory($user_id,$message_id,$action_code,$comment = null,$value_id = null)
    {
        $result = false;
				
				$user_ip = $_SERVER['REMOTE_ADDR'];
				$user_device = $_SERVER["HTTP_USER_AGENT"];
				
				$query = "INSERT INTO `action_history` (`user_id`,`message_id`,`user_ip`,`user_device`,`action_code`,`comment`,`value_id`) VALUES ('".$user_id."','".$message_id."','".$user_ip."','".$user_device."','".$action_code."','".$comment."','".$value_id."')";
				if($results = mysqli_query(DataBase::Connect(),$query))
				{
						$result = true;
				}
        
        return $result;
    }
		
		
		//********************************   OPTIONS  ************************************//
		
		public static function GetMailEventsList()
    {
        $result = false;
        $query = "SELECT *
        FROM `mail_events`";
        
         if($results = mysqli_query(DataBase::Connect(),$query))
        {
            $arMessages = array();
            while($row = mysqli_fetch_assoc($results))
            {
                $arMessages[$row['id']] = $row;
            }
            
            if(count($arMessages)>0)
            {
                $result = $arMessages;
            }
        }
        
        return $result;
    }
		
		public static function GetFeedbackMessages()
    {
        $result = false;
        $query = "SELECT
            f.`id`,
            f.`user_id`,
						us.`alias`,
						us.`email`,
						f.`subject`,
						f.`text`,
						f.`file_path`,
						f.`answered`,
						f.`create_date`
        FROM `feedback` as f
            LEFT JOIN `users` as us ON us.`id` = f.`user_id`
        ORDER BY f.`id` DESC";
        
         if($results = mysqli_query(DataBase::Connect(),$query))
        {
            $arMessages = array();
            while($row = mysqli_fetch_assoc($results))
            {
                $arMessages[$row['id']] = $row;
            }
            
            if(count($arMessages)>0)
            {
                $result = $arMessages;
            }
        }
        
        return $result;
    }
		
		
		public static function GetUsersList()
    {
        $result = false;
				$arUsers = array();
        $query = "SELECT
					us.`id` as ID,
					us.`email` as EMAIL,
					us.`auth_date` as AUTH_DATE,
					us.`alias` as ALIAS,
					us.`phone` as PHONE,
					us.`group_id` as GROUP_ID,
					gr.`name` as GROUP_NAME,
					dep.`name` as DEPARTMENT_NAME,
					us.`department_id` as DEPARTMENT_ID,
					org.`name` as ORG_NAME,
					us.`org_id` as ORG_ID,
					us.`activity` as ACTIVE,
					(SELECT COUNT(id) FROM `messages` WHERE `user_id`= us.`id`) as MESSAGE_COUNT
				FROM `users` as us
				LEFT JOIN `departments` as dep ON dep.`id` = us.`department_id`
				LEFT JOIN `organization` as org ON org.`id` = us.`org_id`
				LEFT JOIN `user_groups` as gr ON gr.`id` = us.`group_id`
				ORDER BY GROUP_ID,MESSAGE_COUNT,us.`reg_date` DESC";
				if($results = mysqli_query(DataBase::Connect(),$query))
				{
					while($row = mysqli_fetch_assoc($results))
					{
						$arUsers[$row['ID']] = $row;
					}
					$result = $arUsers;
				}
        
        return $result;
    }
		
		
		public static function GetUsersStat()
    {
			$result = false;
			$query = "SELECT `id`,`group_id` FROM `users`";
			if($results = mysqli_query(DataBase::Connect(),$query))
			{
				
				$arUsers = array();
				$arUsers = array();
				$simpleUsersCount = 0;
				while($row = mysqli_fetch_assoc($results))
				{
					$arUsers['LIST'][$row['id']] = $row['group_id'];
					if($row['group_id']==1)
					{
						$simpleUsersCount++;
					}
					
					$arUsers['SIMPLE_USERS_CONT'] = $simpleUsersCount;
				}
				
				
				$result = $arUsers;
				
			}
			return $result;
		}
		
		
		
		public static function GetUsersGroupList()
		{
			$result = false;
			$query = "SELECT * FROM `user_groups`";
			if($results = mysqli_query(DataBase::Connect(),$query))
			{
				while($row = mysqli_fetch_assoc($results))
				{
					$result[$row['id']] = $row;
				}
			}
			return $result;
		}
		
		
		public static function GetDeveloperNotes()
		{
			$result = false;
			$query = "SELECT * FROM `developer_notes` ORDER BY `sort`,`priority` DESC";
			if($results = mysqli_query(DataBase::Connect(),$query))
			{
				while($row = mysqli_fetch_assoc($results))
				{
					$result[$row['id']] = $row;
				}
			}
			return $result;
		}
		
		public static function GetOrganizationList()
		{
			$result = false;
			$query = "SELECT
			org.`id`,
			org.`name`,
			org.`address`,
			org.`house_count`,
			org.`activity`,
			dep.`name` as department_name
			FROM `organization` org
			LEFT JOIN `sub_organizations` as sub ON sub.`org_id` = org.`id`
			LEFT JOIN `departments` as dep ON dep.`id` = sub.`depart_id`";
			if($results = mysqli_query(DataBase::Connect(),$query))
				while($row = mysqli_fetch_assoc($results)) {
					$result[$row['id']]['id'] = $row['id'];
					$result[$row['id']]['name'] = $row['name'];
					$result[$row['id']]['address'] = $row['address'];
					$result[$row['id']]['house_count'] = $row['house_count'];
					$result[$row['id']]['activity'] = $row['activity'];
					$result[$row['id']]['departments'][] = $row['department_name'];
				}
			return $result;
		}
		
		
		public static function GetDepartmentsList()
		{
			$result = false;
			$query = "SELECT * FROM `departments` ORDER BY `name`";
			if($results = mysqli_query(DataBase::Connect(),$query))
				while($row = mysqli_fetch_assoc($results))
					$result[$row['id']] = $row;
			return $result;
		}
		
		
		
		public static function GetCategoriesList()
		{
			$result = false;
			$query = "SELECT * FROM `message_category`";
			if($results = mysqli_query(DataBase::Connect(),$query))
				while($row = mysqli_fetch_assoc($results))
					$result[$row['id']] = $row;
			return $result;
		}
		
		
		public static function GetOrgByCategoryId($categoryId)
		{
			$result = false;
			if(is_numeric($categoryId))
			{
				$query = "SELECT `org_id`,`depart_id` FROM `message_category` WHERE `id` = '".$categoryId."'";
				if($results = mysqli_query(DataBase::Connect(),$query))
				{
					if($row = mysqli_fetch_assoc($results))
					{
						$result = $row;
					}
				}
			}
			return $result;
		}
		
		
		public static function GetCategoriesWithOrg()
		{
			$result = false;
			$query = "SELECT
				mc.`id` as id,
				mc.`name` as name,
				mc.`caption`,
				mc.`icon` as icon,
				mc.`yandex_icon` as yandex_icon,
				mc.`deadline` as deadline,
				mc.`description` as description,
				mc.`activity` as activity,
				mc.`create_time` as create_time,
				org.`id` as org_id,
				org.`name` as org_name,
				dep.`id` as depart_id,
				dep.`name` as depart_name
			FROM `message_category` mc
			LEFT JOIN `organization` as org ON org.`id` = mc.`org_id`
			LEFT JOIN `departments` as dep ON dep.`id` = mc.`depart_id`";
			if($results = mysqli_query(DataBase::Connect(),$query))
				while($row = mysqli_fetch_assoc($results))
					$result[$row['id']] = $row;
			return $result;
		}
		
		public static function GetMessageStatusList()
		{
			$result = false;
			$query = "SELECT * FROM `message_status`";
			if($results = mysqli_query(DataBase::Connect(),$query))
				while($row = mysqli_fetch_assoc($results))
					$result[$row['id']] = $row;
			return $result;
		}
		
		public static function GetGroupsList()
		{
			$result = false;
			$query = "SELECT * FROM `user_groups`";
			if($results = mysqli_query(DataBase::Connect(),$query))
			{
				while($row = mysqli_fetch_assoc($results))
				{
					$result[$row['id']] = $row;
				}
			}
			return $result;
		}
		
		
		public static function SetSysTableData($table,$id,$field,$value)
    {
        $result = false;
				$okIndex = 0;
				if($field=='email') {
					$query = "SELECT * FROM `".$table."` WHERE `".$field."` = '".$value."'";
					if($results = mysqli_query(DataBase::Connect(),$query))
					{
						if($row = mysqli_fetch_assoc($results))
						{
							return $result;
						}
					}
				}
				
				$query = "UPDATE `".$table."` SET `".$field."`='".$value."' WHERE `id` = '".$id."'";
				if($results = mysqli_query(DataBase::Connect(),$query))
				{
						$query = "SELECT * FROM `".$table."` WHERE `id` = '".$id."'";
						if($results = mysqli_query(DataBase::Connect(),$query))
						{
							if($row = mysqli_fetch_assoc($results))
							{
								$result[$row['id']] = $row;
							}
						}
						
				} 
        
        return $result;
    }
		
		
		public static function AddOptionsDataRow($table,$arData)
    {
        $result = false;
				$fieldsStr =  "";
				$valuesStr =  "";
				
				foreach($arData as $key => $field)
				{
					$fieldsStr .= "`".CharacterFilter($field['name'])."`,";
					$valuesStr .= "'".CharacterFilter($field['value'])."',";
				}
				
				$fieldsStr = substr($fieldsStr, 0, strlen($fieldsStr) - 1);
				$valuesStr = substr($valuesStr, 0, strlen($valuesStr) - 1);
				
				$query = "INSERT INTO `".$table."` (".$fieldsStr.") VALUES (".$valuesStr.")";
				if($results = mysqli_query(DataBase::Connect(),$query))
				{
						$result = true;
				}
				//$result = "(".$fieldsStr.") VALUES (".$valuesStr.")";				
				
        return $result;
    }
		
		
		public static function DeleteSysTableData($table,$id)
    {
        $result = false;
				
				$query = "DELETE FROM `".$table."` WHERE `id` = '".$id."'";
				if($results = mysqli_query(DataBase::Connect(),$query))
				{
						$result = true;
				} 
        
        return $result;
    }
		
		public static function GetGlobalSysOptions()
    {
        $result = false;
				
				$query = "SELECT * FROM `global_system_options`";
				if($results = mysqli_query(DataBase::Connect(),$query))
				{
					while($row = mysqli_fetch_assoc($results))
					{
						$result[$row['code']] = $row;
					}
				}
        
        return $result;
    }
		
		
		public static function GetSpecialDistrictData($id)
    {
        $result = false;
				
				$query = "SELECT `full_name`,`coordinates` FROM `city_districts` WHERE `id`='".$id."'";
				if($results = mysqli_query(DataBase::Connect(),$query))
				{
					if($row = mysqli_fetch_assoc($results))
					{
						$result = $row;
					}
				}
        
        return $result;
    }
		
		
		public static function GetAllSpecialDistrictData()
    {
        $result = false;
				$arDistrictData = array();
				
				$query = "SELECT `id`,`name`,`full_name`,`coordinates`,`color` FROM `city_districts`";
				if($results = mysqli_query(DataBase::Connect(),$query))
				{
					while($row = mysqli_fetch_assoc($results))
					{
						$arDistrictData[$row['id']] = $row;
					}
					
					$result = $arDistrictData;
				}

        return $result;
    }
		
		public static function setSubOrganization($depart_id,$org_id,$action_type) // привязка управляющей компании к администрации округа
    {
        $result = false;
				
				$query = "SELECT id FROM `sub_organizations` WHERE `org_id`='".$org_id."' AND `depart_id`='".$depart_id."'";
				if($results = mysqli_query(DataBase::Connect(),$query))
				{
					
							if($action_type==1)
							{
								if(!$row = mysqli_fetch_assoc($results))
								{
										$query = "INSERT INTO `sub_organizations` (`depart_id`,`org_id`) VALUES ('".$depart_id."','".$org_id."')";
										if($results = mysqli_query(DataBase::Connect(),$query))
										{
												$result = true;
										}
								}
							}
							else
							{
									$query = "DELETE FROM `sub_organizations` WHERE `org_id`='".$org_id."' AND `depart_id`='".$depart_id."'";
									if($results = mysqli_query(DataBase::Connect(),$query))
									{
											$result = true;
									}
							}
					
					
				}

        return $result;
    }
		
		
		
		
		public static function getSubOrganization($org_id)
    {
        $result = false;
				
				$query = "SELECT
										dep.id,
										dep.name
										FROM `sub_organizations` as sub
									LEFT JOIN `departments` as dep ON dep.`id` = sub.`depart_id`
									WHERE `org_id`='".$org_id."'";
				
				
				if($results = mysqli_query(DataBase::Connect(),$query))
				{
					$result = array();
					while($row = mysqli_fetch_assoc($results))
					{
						$result[$row['id']] = $row['name'];
					}
				}

        return $result;
    }
		
		
		public static function getDistrictByMessageId($id)
		{
			$result = false;
			$query = "SELECT `district_id` FROM `messages` WHERE `id`='".$id."'";
			if($results = mysqli_query(DataBase::Connect(),$query))
      {
					if($row = mysqli_fetch_assoc($results))
					{
						$result = $row['district_id'];
					}
			}
			return $result;
		}
		
		public static function getOrganizationData($id)
		{
			$result = false;
			$query = "SELECT * FROM `organization` WHERE `id`='".$id."'";
			if($results = mysqli_query(DataBase::Connect(),$query))
      {
					if($row = mysqli_fetch_assoc($results))
					{
						$result = $row;
					}
			}
			return $result;
		}
		
		public static function GetUsersOrganizationList($depart_id=0)
    {
        $result = false;
				$option = '';
        if(($depart_id != 0) && is_numeric($depart_id))
				{
						$query = "SELECT
												org.`id`,
												org.`name`
											FROM `organization` as org
											LEFT JOIN `sub_organizations` as sub ON sub.`org_id` = org.`id`
											WHERE sub.`depart_id`='".$depart_id."' ORDER BY org.`name`";
				}
				else
				{
					$query = "SELECT
						`id`,
						`name`
						FROM `organization`
						WHERE `activity` = '1'";
				}
				
				
				
				if($results = mysqli_query(DataBase::Connect(),$query))
        {
            $arOrg = array();
            while($row = mysqli_fetch_assoc($results))
            {
               $arOrg[$row['id']]['id'] = $row['id'];
               $arOrg[$row['id']]['name'] = $row['name'];
               //$arOrg[$row['id']]['department_id'] = $row['department_id'];
            }
        }
        $result = $arOrg;
				return $result;
		}
		
		
		public static function sendMessageForArray($arUsers,$messEventTypeId,$mailParams = array())
    {
				foreach($arUsers as $user)
        {
            MEvents::SendMailMessage($user['EMAIL'],$messEventTypeId,$mailParams);
        }
				return true;
    }
		
		
		
		// НОВАЯ СТАТИСТИКА
		public static function statByMounthAddActive()
    {
				$result = false;
				$query = "SELECT COUNT( id ) as count, DATE_FORMAT( create_time,  '%Y-%m' ) as date FROM messages WHERE removed = '0' GROUP BY DATE_FORMAT( create_time,  '%Y-%m' )";
				if($results = mysqli_query(DataBase::Connect(),$query))
				{
					while($row = mysqli_fetch_assoc($results))
					{
						$result[$row['date']]['messages'] = $row['count'];
					}
				}
				
				$query = "SELECT COUNT( id ) as count, DATE_FORMAT( reg_date,  '%Y-%m' ) as date FROM users WHERE reg_date > '0000-00-00' GROUP BY DATE_FORMAT( reg_date,  '%Y-%m' )";
				if($results = mysqli_query(DataBase::Connect(),$query))
				{
					while($row = mysqli_fetch_assoc($results))
					{
						$result[$row['date']]['users'] = $row['count'];
					}
				}
				
				return $result;
    }
		
		public static function statByMessagesStatus()
    {
				$result = false;
				$query = "
				SELECT st.`name` as status_name, st.`web_color` as color ,count(m.`id`) as count FROM `messages` as m 
					LEFT JOIN `message_status` as st ON st.`id` = m.`status_id`
					LEFT JOIN `message_category` as mc ON mc.`id` = m.`category_id`
					WHERE mc.`activity` = 1 
					GROUP BY m.`status_id`";
				if($results = mysqli_query(DataBase::Connect(),$query))
				{
					while($row = mysqli_fetch_assoc($results))
					{
						$result[$row['status_name']]['count'] = $row['count'];
						$result[$row['status_name']]['color'] = $row['color'];
					}
				}
				
				return $result;
    }
		
		public static function statDistrictActivity()
    {
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
				
				if($results = mysqli_query(DataBase::Connect(),$query))
				{
					$result = array();
					while($row = mysqli_fetch_assoc($results))
					{
						$result[$row['distr_id']] = $row;
					}
				}
				
				return $result;
    }

	    public static function getActualCategories($cat="all"){
	    	$result = false;
	    	if($cat=="all")
	    		{
	    			$query = "SELECT cat.`id` as id, cat.`caption` as name FROM `message_category` as cat WHERE cat.`activity` =1";
	    		}
	    	else
	    	{
	    		$query = "SELECT cat.`id` as id, cat.`caption` as name FROM `message_category` as cat WHERE ";
	    		$where = "( (cat.`activity` =1) AND (cat.id in (";    		
	    		
	    		$end_element = array_pop($cat);
	    		foreach ($cat as $key => $value) {
	    			$where = $where . sprintf(" '%d', ", $value);
	    		}

	    		$where = $where . sprintf(" '%d' ", $end_element);
	    		$where = $where . ")) );";
	    		
	    		$query = $query . $where;

	    	}
	    	if($results = mysqli_query(DataBase::Connect(),$query))
            {
            	$result = array();
            	while($row = mysqli_fetch_assoc($results))
					{
						$result[$row['id']] = $row["name"];
					}
            }
            return $result;
	    }

	    public static function getSelectedRegions($reg="all"){
	    	$result = false;

	    	if($reg=="all")
    		{
    			$query = "SELECT dist.id as id, dist.name as name FROM `city_districts`as dist LIMIT 0,30";
    		}
	    	else
	    	{
	    		$query = "SELECT dist.id as id, dist.name as name FROM `city_districts`as dist WHERE ";
	    		$where = "( (dist.id in (";    		
	    		$end_element = array_pop($reg);
	    		foreach ($reg as $key => $value) {
	    			$where = $where . sprintf(" '%d', ", $value);
	    		}

	    		$where = $where . sprintf(" '%d' ", $end_element);
	    		$where = $where . ")) );";
	    		
	    		$query = $query . $where;

	    	}


	    	if($results = mysqli_query(DataBase::Connect(),$query))
            {
            	$result = array();
            	while($row = mysqli_fetch_assoc($results))
					{
						$result[$row['id']] = $row["name"];
					}
            }
            return $result;
	    }

	    public static function getActualStatusMessages(){
	    	$result = false;

	    	$query = "SELECT sm.id as id, sm.short_name as name FROM `message_status`as sm WHERE sm.`include_statisic` =1";

	    	if($results = mysqli_query(DataBase::Connect(),$query))
            {
            	$result = array();
            	while($row = mysqli_fetch_assoc($results))
					{
						$result[$row['id']] = $row["name"];
					}
            }
            return $result;
	    }

	    public static function getAllStatisticsByCategories($reg="all", $cat="all", $date="all"){
	    	$result = false;

	    	$select = "SELECT m.district_id AS dist";

	    	$reg = self::getSelectedRegions($reg);
	    	$categories = self::getActualCategories($cat);
	    	$statuses = self::getActualStatusMessages();



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

	    	$select = $select . " FROM `messages` AS m " .$join . $where . "GROUP BY dist";
	    	//echo $select;

	    	$query = $select;

	    	if($results = mysqli_query(DataBase::Connect(),$query))
            {
            	$result = array();
            	while($row = mysqli_fetch_assoc($results))
					{
						$result["stat"][$row['dist']] = $row;
						//print_r($row);
					}
            }
            $result["reg"] = $reg;
	    	$result["statuses"] = $statuses;
	    	$result["categories"] = $categories;
            return $result;
	    }
		
		
		public static function GetUserListByOrgId($id)
    {
        $result = false;

        if($id!=0)
        {
            $query = "SELECT org.`name` as org_name, us.`id` as user_id, us.`email` as user_email, us.`alias` as user_name FROM `users` as us
            LEFT JOIN `organization` as org ON org.`id`= us.`org_id`
            WHERE org.`id`='".$id."'";
            if($results = mysqli_query(DataBase::Connect(),$query))
            {
                $arList = array();
                while($row = mysqli_fetch_assoc($results))
                {
                    $arList['NAME'] = $row['org_name'];
                    $arList['USERS'][$row['user_id']]['EMAIL'] = $row['user_email'];
                    $arList['USERS'][$row['user_id']]['USER_NAME'] = $row['user_name'];
                    
                }
            }
            if(count($arList)>0)
            {
                $result = $arList;
            }
        }
        return $result;
    }

}

?>