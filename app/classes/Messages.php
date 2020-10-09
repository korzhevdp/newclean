<?php 

class Messages {
    

    public static function GetCategoryList($key)
    {
        $result['status'] = false;
        $result['message'] = 'Не удалось получить список категорий';
        $isAuth = Users::isAuthorized($key);
        if(!$isAuth['status'])
        {
            return $result;
        }

        $query = "
        SELECT
            `name`,
            `caption`,
            `description`,
            `id`,
            `icon` as icon
        FROM `message_category`
        WHERE `activity` = 1 ";

        if($results = mysqli_query(DataBase::Connect(),$query))
        {
            $arMessageCategory = array();
            while($row = mysqli_fetch_assoc($results))
            {
                $arMessageCategory[$row['id']]['name'] = $row['name'];
                $arMessageCategory[$row['id']]['caption'] = " ".$row['caption'];
                $arMessageCategory[$row['id']]['description'] = " ".$row['description'];
                $arMessageCategory[$row['id']]['icon'] = " ".$row['icon'];
            }
            $result['status'] = true;
            $result['message'] = 'Список категорий получен';
            $result['data'] = $arMessageCategory;
        } 
        
        return $result;
    }
    
    
    public static function GetStatus($id=0)
    {
        $result = false;
        $option = '';
        if(($id != 0) && is_numeric($id))
            $option = " `id`='".$id."'";
        
        $query = "SELECT
        `id`,
        `name`,
        `icon`,
        `status_color`,
        `answer_index`,
        `file_index`
        FROM `message_status`
        WHERE `activity` = '1' ".$option;
        
        if($results = mysqli_query(DataBase::Connect(),$query))
        {
            $arMessageStatus = array();
            while($row = mysqli_fetch_assoc($results))
            {
               $arMessageStatus[$row['id']]['name'] = $row['name'];
               $arMessageStatus[$row['id']]['icon'] = $row['icon'];
               $arMessageStatus[$row['id']]['status_color'] = $row['status_color'];
               $arMessageStatus[$row['id']]['answer'] = $row['answer_index'];
               $arMessageStatus[$row['id']]['file'] = $row['file_index'];
            }
        } 
        $result = $arMessageStatus;
        
        return $result;
    }
    
    
    public static function GetOrganizations($id=0) {
        $result = false;
        $option = '';
        if(($id != 0) && is_numeric($id))
            $option = " `id`='".$id."'";
        
        $query = "SELECT
        `id`,
        `name`
        FROM `organization`
        WHERE `activity` = '1' ".$option;
        
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
    
    
    public static function GetDistrict($id=0,$DepartId=0)
    {
        $result = false;
        $option = '';
        if(($id != 0) && is_numeric($id))
            $option = " AND distr.`id`='".$id."'";
            
        if($DepartId!=0)
        {
            $query = "SELECT id FROM `city_districts` WHERE `responsible`='".$DepartId."'";
            if($results = mysqli_query(DataBase::Connect(),$query))
            {
                $arDistrictStr = ''; $index = 0;
                while($row = mysqli_fetch_assoc($results))
                {
                    if($index==0) $arDistrictStr .= "'".$row['id']."'";
                    else $arDistrictStr .= ",'".$row['id']."'";
                    $index++;
                }
                
                if($option!='')
                    $option .= " AND distr.`id` IN ($arDistrictStr)";
                else
                    $option = " AND distr.`id` IN ($arDistrictStr)";
                
            }
        }
        
        $query = "SELECT distr.`name`,distr.`id` FROM `city_districts` as distr WHERE `activity` = '1' ".$option;
        if($results = mysqli_query(DataBase::Connect(),$query))
        {
            $arDistricts = array();
            while($row = mysqli_fetch_assoc($results))
            {
               $arDistricts[$row['id']] = $row['name'];
            }
            $result = $arDistricts;
        } 

        return $result;
    }
    
    
    public static function MessageToArchive($id)
    {
        $result = false;
        $query = "UPDATE `messages` SET `archive`='1' WHERE `id` = '".$id."'";
        if($results = mysqli_query(DataBase::Connect(),$query))
        {
            $result = true;
            MSystem::SaveActionHistory($_SESSION['UID'],$id,'sendToArchive','Сообщение отправлено в архив контролирующим подразделением');
        }
        return $result;
    }
    
    public static function MessageDeleteForUser($id)
    {
        $result['status'] = false;
        $result['message'] = 'Не удалось выполнить запрос на удаление сообщения.';
        $query = "UPDATE `messages` SET `archive`='1',`removed`='1' WHERE `id` = '".$id."' AND `user_id`=".$_SESSION['UID'];
        if($results = mysqli_query(DataBase::Connect(),$query))
        {
            $result['status'] = true;
            $result['message'] = 'Сообщение успешно перемещено в архив.';
            MSystem::SaveActionHistory($_SESSION['UID'],$id,'sendToArchive','Сообщение отправлено в архив создателем');
        }
        return $result;
    }
    
    public static function SaveMessageData($type,$value,$answer,$answer_file_path,$id)
    {
        $result = false;
        $errorIndex = false;
        
        switch ($type)
        {
            case 'status':
            {
                $query = "SELECT `id` FROM `message_answers` WHERE `message_id` = '".$id."'";
                if($results = mysqli_query(DataBase::Connect(),$query))
                {
                    if($row = mysqli_fetch_assoc($results))
                    {
                        $query = "UPDATE `message_answers`  SET `user_id` = '".$_SESSION['UID']."', `answer` = '".$answer."', `file_path` = '".$answer_file_path."', `datetime` = NOW()  WHERE `message_id` = '".$id."'";
                        //print_r($row);
                    }
                    else
                    {
                        $query = "INSERT INTO `message_answers` (
                            `message_id`,
                            `user_id`,
                            `answer`,
                            `file_path`,
                            `datetime`
                        ) VALUES (
                        '".$id."',
                        '".$_SESSION['UID']."',
                        '".$answer."',
                        '".$answer_file_path."',
                        NOW()
                        )";
                    }
                }
                
                
                if($results = mysqli_query(DataBase::Connect(),$query))
                {
                    $option = "`status_id`='".$value."'";
                }
                else
                {
                    $errorIndex = true;
                }
                break;
            }
            case 'district': 
                $option = "`district_id`='".$value."', `org_id` = '0'";
                break;
            case 'org':
                $option = "`org_id`='".$value."'";
                break;
            case 'time':
                $option = "`result_time`='".$value."'";
                break;
            default:
                $option = false;
                break;
        }
        
        if(($option) && (!$errorIndex))
        {
            $query = "UPDATE `messages` SET ".$option." WHERE `id` = '".$id."'";
            if($results = mysqli_query(DataBase::Connect(),$query))
            {
                $arResult = self::GetMessages(0,1,$id);
                $result = $arResult;
                switch ($type)
                {
                    case 'status':
                        MSystem::SaveActionHistory($_SESSION['UID'],$id,'statusChange','Обновлен статус сообщения',$value);
                        break;
                    case 'district':
                        MSystem::SaveActionHistory($_SESSION['UID'],$id,'respUnit','Назначено ответственное подразделение',$value);
                        break;
                    case 'org':
                        MSystem::SaveActionHistory($_SESSION['UID'],$id,'respOrganization','Назначена ответственная организация',$value);
                        break;
                    case 'time':
                        MSystem::SaveActionHistory($_SESSION['UID'],$id,'respTime','Назначен срок для исполнения. Устранить до '.$value);
                        break;
                    default:
                        $option = false;
                        break;
                }
            } 
        }
        
        return $result;
    }
    
    
    public static function UsersMessagesList($UserId = 0, $UserType = 1)
    {
        $result = false;
        $option = '';
        if(($UserId!=0) && is_numeric($UserId))
        {
            $option = "WHERE `user_id`='".$UserId."' AND m.`removed` = '0'";
        }
        else
        {
            if(($UserType != 1) && is_numeric($UserType)) 
                $option = "WHERE ((m.`status_id`<>'5' AND m.`status_id`<>'6') OR (m.`user_id`='".$_SESSION['UID']."'))";
        }
        
        $query = "SELECT
        m.`message`,
        m.`id`,
        m.`status_id`
        FROM `messages` as m
        ".$option." ORDER BY m.`id` DESC";
        if($results = mysqli_query(DataBase::Connect(),$query))
        {
            $arMessages = array();
            while($row = mysqli_fetch_assoc($results))
            {
                $arMessages[$row['id']] = $row;
            }
            $result = $arMessages;
        }
        
        return $result;
    }
    

}


?>
