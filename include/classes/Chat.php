<?php 

class Chat {
    

    public static function sendMessage($messageID,$userID,$departID,$orgID,$text)
    {
        $result['status'] = false;
        $result['message'] = 'При отправке сообщения возникла ошибка';
        $result['data']['text'] = '';
        $result['data']['time'] = '';
         $result['data']['user_alias'] = '';
        $mainID = MSystem::getDistrictByMessageId($messageID);
        if(is_numeric($messageID) && is_numeric($userID) && is_numeric($departID) &&
        is_numeric($orgID) && is_numeric($mainID))
        {
            
            $query = "
            SELECT `id`,`active` FROM `chat`
            WHERE
            `message_id`='".$messageID."'
            AND `main_unit_id`='".$mainID."'
            AND `user_id`='".$userID."'
            AND `depart_id`='".$departID."'
            AND `org_id`='".$orgID."'";
            
            $text = CharacterFilter($text);
            if($text=='')
            {
                $result['message'] = 'Текст сообщения имеет недопустимый формат';
                return $result;
            }
            
            if($results = mysqli_query(DataBase::Connect(),$query))
            {
                if($row = mysqli_fetch_assoc($results)) // если чат уже создан ранее
                {
                    if(isset($row['active']) && $row['active'])
                    {
                        $query = "INSERT INTO `chat_messages` (`chat_id`,`user_id`,`text`) VALUES ('".$row['id']."','".$_SESSION['UID']."','".$text."')";
                        if($results = mysqli_query(DataBase::Connect(),$query))
                        {
                            $arUser = Users::GetUserById($_SESSION['UID']);
                            $result['status'] = true;
                            $result['message'] = 'Сообщение сохранено';
                            $result['data']['text'] = $text;
                            $result['data']['time'] = date('Y.m.d в H:i');
                            $result['data']['user_alias'] = $arUser[$_SESSION['UID']]['user_name'];
                            return $result;
                        }
                        else 
                        {
                            $result['message'] = 'При сохранении данных возникла ошибка';
                            return $result;
                        }
                    }
                    else
                    {
                        $result['message'] = 'Чат заблокирован, вы не можете отправить сообщение';
                        return $result;
                    }
                }
                else // если необходимо создать новый чат
                {
                    $query = "INSERT INTO `chat` (`message_id`,`main_unit_id`,`user_id`,`depart_id`,`org_id`) VALUES ('".$messageID."','".$mainID."','".$userID."','".$departID."','".$orgID."')";
                    if($results = mysqli_query(DataBase::Connect(),$query))
                    {
                        $query = "
                            SELECT `id`,`active` FROM `chat`
                            WHERE
                            `message_id`='".$messageID."'
                            AND `main_unit_id`='".$mainID."'
                            AND `user_id`='".$userID."'
                            AND `depart_id`='".$departID."'
                            AND `org_id`='".$orgID."'";
                        $chatID = 0;
                        if($results = mysqli_query(DataBase::Connect(),$query))
                        {
                            if($row = mysqli_fetch_assoc($results)) // если чат уже создан ранее
                            {
                                $chatID = $row['id'];
                            }
                        }
                            
                        
                        if($chatID>0)
                        {
                            $query = "INSERT INTO `chat_messages` (`chat_id`,`user_id`,`text`) VALUES ('".$chatID."','".$_SESSION['UID']."','".$text."')";
                            if($results = mysqli_query(DataBase::Connect(),$query))
                            {
                                $result['status'] = true;
                                $result['message'] = 'Сообщение сохранено';
                                $result['data']['text'] = $text;
                                $result['data']['time'] = date('Y.m.d в H:i');
                                return $result;
                            }
                        }
                        else
                        {
                            $result['message'] = 'При создании чата возникла системная ошибка';
                            return $result;
                        }
                    }
                }
            }

        }
        else
        {
            $result['message'] = 'При проверке входящих параметров выявлены ошибки';
        }
        
        return $result;
    }
    
    
    public static function getChatInfo($messageID,$userID,$departID,$orgID)
    {
        $result['status'] = false;
        $result['message'] = 'При открытии чата возникла ошибка';
        $result['unit_name'] = '';
        $result['data']['messages'] = array();
        $type = 0;
        $mainID = 0;
        $chatID = 0;
        
        if($userID!=0 && $departID==0 && $orgID==0)
        {
            $type = 1;
        }
        elseif($departID!=0 && $userID==0 && $orgID==0)
        {
            $type = 2;
        }
        elseif($orgID!=0 && $departID==0 && $userID==0)
        {
            $type = 3;
        }
        
        $successChat = false;
        if($type)
        {
            $query = "
                SELECT `id`,`district_id` FROM `messages`
                WHERE `id` = '".$messageID."' AND (`user_id`='".$userID."' OR `depart_id`='".$departID."' OR `org_id`='".$orgID."')";
            
            if($results = mysqli_query(DataBase::Connect(),$query))
            {
                if($row = mysqli_fetch_assoc($results)) // если чат уже создан ранее
                {
                    $mainID = $row['district_id'];
                    $successChat = true;
                }
            }
        }
        
        if($successChat)
        {
           
            if($type==1)
            {
                $result['unit_name'] = 'Гражданин';
            }
            elseif($type==2)
            {
                $query = "SELECT `name` FROM `departments` WHERE `id` = '".$departID."'";
            }
            elseif($type==3)
            {
                $query = "SELECT `name` FROM `organization` WHERE `id` = '".$orgID."'";
            }
            
            if($type!=1)
            {
                if($results = mysqli_query(DataBase::Connect(),$query))
                {
                    if($row = mysqli_fetch_assoc($results)) // если чат уже создан ранее
                    {
                        if(isset($row['name']) && $row['name'])
                            $result['unit_name'] = $row['name'];
                    }
                }
            }
            
            $query = "
            SELECT `id`,`active` FROM `chat`
            WHERE
            `message_id`='".$messageID."'
            AND `main_unit_id`='".$mainID."'
            AND `user_id`='".$userID."'
            AND `depart_id`='".$departID."'
            AND `org_id`='".$orgID."'";
            if($results = mysqli_query(DataBase::Connect(),$query))
            {
                if($row = mysqli_fetch_assoc($results)) // если чат уже создан ранее
                {
                    $chatID = $row['id'];
                }
            }
            
            if($chatID>0)
            {
                $arMessages = array();
                $query = "SELECT chm.`id`,chm.`user_id`,us.`department_id`,us.`alias` as alias,chm.`text`,chm.`date` FROM `chat_messages` as chm
                LEFT JOIN `users` AS us ON us.`id` = chm.`user_id` WHERE chm.`chat_id` = '".$chatID."' ORDER BY chm.`date`";
                if($results = mysqli_query(DataBase::Connect(),$query))
                {
                    while($row = mysqli_fetch_assoc($results)) // если чат уже создан ранее
                    {
                        $arMessages[$row['id']] = $row;
                    }
                }
                
                $result['data']['messages'] = $arMessages;
            }
            
            
            $result['status'] = true;
            $result['message'] = 'Чат доступен';
            
        }
        else
        {
            $result['message'] = 'Невозможно открыть чат из-за несоответствия входных данных';
        }
        
        return $result;
    }
    
    
    
    public static function getChatRightByUserGroup($userGroupID)
    {
        $result = array();
        $query = "SELECT `group_2` FROM `chat_rights` WHERE `group_1` = '".$userGroupID."'";
        if($results = mysqli_query(DataBase::Connect(),$query))
        {
            while($row = mysqli_fetch_assoc($results)) 
            {
                $result[$row['group_2']] = true;
            }
        }
        return $result;
    }
    
    
    public static function getChatMainUnit($userID)
    {
        $result = array();
        $result['main_unit_id'] = 0;
        $result['type'] = 0;
        $query = "SELECT `id`,`group_id`,`department_id`,`org_id`,`activity` FROM `users` WHERE `id` = '".$userID."'";
        if($results = mysqli_query(DataBase::Connect(),$query))
        {
            if($row = mysqli_fetch_assoc($results)) 
            {
                if($row['group_id']>0)
                {
                    $result['type'] = 2;
                }
            }
        }
        return $result;
    }
    
    
    public static function createChatForAllMessages() // создает пустые чаты для всех сообщений, у которых они отсутствуют
    {
        $result = array();
        $query = "SELECT `group_2` FROM `chat_rights` WHERE `group_1` = '".$userGroupID."'";
        if($results = mysqli_query(DataBase::Connect(),$query))
        {
            while($row = mysqli_fetch_assoc($results)) 
            {
                $result[$row['group_2']] = true;
            }
        }
        return $result;
    }

}


?>
