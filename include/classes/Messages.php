<?php 

class Messages {
    

    public static function GetFullMessagesList()
    {
        $result = false;
        $query = "SELECT
            m.`message`,
            m.`id`,
            m.`archive`,
            m.`files`,
            m.`coord_x`,
            m.`coord_y`,
            m.`user_id`,
            us.`alias` as user_alias,
            us.`id` as user_id,
            m.`address` as address,
            m.`category_id`,
            cat.`name` as category_name,
            m.`status_id`,
            st.`name` as status_name,
            answer.`answer` as answer,
            answer.`file_path` as answer_file_path,
            m.`district_id`,
            distr.`name` as district_name,
            m.`org_id`,
            m.`depart_id`,
            org.`name` as org_name,
            distr.`name` as district,
            dep2.`name` as depart_name,
            m.`update_time`,
            m.`create_time`,
            m.`result_time`,
            st.`icon` as st_icon,
            st.`status_color` as st_color,
            dep.`name` as responsible
        FROM `messages` as m
            LEFT JOIN `city_districts` as distr ON distr.`id` = m.`district_id`
            LEFT JOIN `message_category` as cat ON cat.`id` = m.`category_id`
            LEFT JOIN `users` as us ON us.`id` = m.`user_id`
            LEFT JOIN `organization` as org ON org.`id` = m.`org_id`
            LEFT JOIN `departments` as dep ON dep.`id` = distr.`responsible`
            LEFT JOIN `departments` as dep2 ON dep2.`id` = m.`depart_id`
            LEFT JOIN `message_status` as st ON st.`id` = m.`status_id`
            LEFT JOIN `message_answers` as answer ON answer.`message_id` = m.`id` 
        ORDER BY m.`id` DESC";
        
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
    
    
    
    public static function getOneMessage($UserId = 0,$MessageId,$public = 0) {
        $result = false;
        $arMessage = array();
        $query = "
            SELECT
                m.`message`,
                m.`id`,
                m.`files`,
                m.`coord_x`,
                m.`coord_y`,
                m.`user_id`,
                us.`alias` as user_alias,
                m.`address` as address,
                m.`category_id`,
                m.`status_id`,
                answer.`answer` as answer,
                answer.`file_path` as answer_file_path,
                dep_com.`comment` as dep_comment,
                dep_com.`datetime` as comment_datetime,
                m.`district_id`,
                m.`org_id`,
                m.`depart_id`,
                org.`name` as org_name,
                org.`id` as org_id,
                distr.`name` as district,
                dep2.`name` as depart_name,
                cat.`name` as cat,
                m.`update_time`,
                m.`create_time`,
                m.`result_time`,
                st.`name` as status,
                st.`icon` as st_icon,
                st.`status_color` as st_color,
                cat.`yandex_icon` as icon_type,
                dep.`name` as responsible
            FROM `messages` as m
                LEFT JOIN `city_districts` as distr ON distr.`id` = m.`district_id`
                LEFT JOIN `message_category` as cat ON cat.`id` = m.`category_id`
                LEFT JOIN `users` as us ON us.`id` = m.`user_id`
                LEFT JOIN `organization` as org ON org.`id` = m.`org_id`
                LEFT JOIN `departments` as dep ON dep.`id` = distr.`responsible`
                LEFT JOIN `departments` as dep2 ON dep2.`id` = m.`depart_id`
                LEFT JOIN `message_status` as st ON st.`id` = m.`status_id`
                LEFT JOIN `message_answers` as answer ON answer.`message_id` = m.`id`
                LEFT JOIN `message_depart_comment` as dep_com ON dep_com.`message_id` = m.`id`
            WHERE m.`id` = '".$MessageId."'
            ";
            
            if($UserId!=0)
            {
               $query .=" AND m.`user_id`='".$UserId."'";
            }
        
        
        if($results = mysqli_query(DataBase::Connect(),$query))
        {
            if($row = mysqli_fetch_assoc($results))
            {
                if($row['district']==null) $row['district'] = '';
                
                $answer = $row['answer'];
                
                if($answer==null)
                    $answer = '';
                    
                $answer_file_path = $row['answer_file_path'];
                
                $address = $row['address'];
                if($address==null)
                    $address = '';
                else
                {
                    if($row['district']!='')  
                        $address =  'Адрес: '.$row['district'].', '.$address;
                }
                
                $responsible1 = $row['responsible'];
                $responsible2 = $row['org_name'];
                $responsible3 = $row['depart_name'];
                
                if($responsible1!=null)
                    $responsible1 = 'Ответственное подразделение: <br/><b>'.$responsible1.'</b>';
                else
                    $responsible1 = 'Ответственное подразделение: <br/>Не определено';
                
                if($responsible2 != null)
                    $responsible2 = '<b>'.$responsible2.'</b>';
                else
                    $responsible2 = 'Организация не выбрана';
                    
                if($responsible3 != null)
                {
                    $responsible3 = '<b>'.$responsible3.'</b>';
                    if(isset($row['depart_id']) && !$public)
                    {
                        $arUsers = Messages::getUsersByDepart($row['depart_id']);
                        $uscount = 0;
                        foreach($arUsers as $key => $usr)
                        {
                            if($uscount>2)
                                break;
                            if(isset($usr['alias']))
                            {
                                $responsible3 .= "<div>".$usr['alias'];
                                if(isset($usr['phone']) && $usr['phone']!='')
                                {
                                    $responsible3 .= " (".$usr['phone'].")";
                                }
                                $responsible3 .= "</div>";
                            }
                            $uscount++;
                        }
                    }
                }
                else
                {
                    $responsible3 = 'Департамент не закреплен';
                }
                
                $arChat = array();
                $arChat = array();
                $query = "SELECT `id`,`main_unit_id`,`user_id`,`depart_id`,`org_id`,`active` FROM chat WHERE `message_id` = '".$MessageId."'";
                if($results = mysqli_query(DataBase::Connect(),$query))
                {
                    if($chat = mysqli_fetch_assoc($results))
                    {
                        $unit_id = 0;
                        if($chat['user_id']!=0) $unit_id = $chat['user_id'];
                        if($chat['depart_id']!=0) $unit_id = $chat['depart_id'];
                        if($chat['org_id']!=0) $unit_id = $chat['org_id'];
                            
                        $arChat[$chat['main_unit_id']]['active'] = $chat['active'];
                        $arChat[$chat['main_unit_id']]['unit_id'] = $unit_id;
                    }
                }
                
                $result_time = $row['result_time'];
                $result_time_sys = null;
                if($result_time!=null)
                {
                    $result_time = date("d.m.Y в H:i",strtotime($row['result_time']));
                    $result_time_sys = date("Y-m-d H:i:s",strtotime($row['result_time']));
                }
                else
                    $result_time = ' Не определено';
                
                $files = json_decode($row['files']);
                    
                $arfiles = array();
                foreach($files as $key => $fl)
                {
                    //if(file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$fl))
                    //{
                        $img_size = getimagesize($_SERVER['DOCUMENT_ROOT'].'/'.$fl);
                        $arfiles[$key]['path'] = '/'.$fl;
                        $arfiles[$key]['w'] = $img_size[0];
                        $arfiles[$key]['h'] = $img_size[1];
                    //}
                }
                
                $arMessage[$row['id']]['id'] = $row['id'];
                $arMessage[$row['id']]['center'][] = $row['coord_x'];
                $arMessage[$row['id']]['center'][] = $row['coord_y'];
                $arMessage[$row['id']]['text'] = $row['message'];
                $arMessage[$row['id']]['category_id'] = $row['category_id'];
                $arMessage[$row['id']]['category'] = $row['cat'];
                $arMessage[$row['id']]['user_id'] = $row['user_id'];
                $arMessage[$row['id']]['user_alias'] = $row['user_alias'];
                $arMessage[$row['id']]['district'] = $row['district'];
                $arMessage[$row['id']]['district_id'] = $row['district_id'];
                $arMessage[$row['id']]['org_id'] = $row['org_id'];
                $arMessage[$row['id']]['depart_id'] = $row['depart_id'];
                $arMessage[$row['id']]['org_name_only'] = $row['org_name'];
                $arMessage[$row['id']]['address'] =$address;
                $arMessage[$row['id']]['responsible'] =$responsible1;
                $arMessage[$row['id']]['org_name'] = $responsible2;
                $arMessage[$row['id']]['depart_name'] = $responsible3;
                $arMessage[$row['id']]['status'] = $row['status'];
                $arMessage[$row['id']]['answer'] = $answer;
                $arMessage[$row['id']]['answer_file_path'] = $answer_file_path;
                $arMessage[$row['id']]['dep_comment'] = $row['dep_comment'];
                
                $arMessage[$row['id']]['comment_datetime']  = '';
                if($row['comment_datetime'])
                {
                    $arMessage[$row['id']]['comment_datetime'] = date("d.m.Y в H:i",strtotime($row['comment_datetime']));
                }
                $arMessage[$row['id']]['status_id'] = $row['status_id'];
                $arMessage[$row['id']]['update_time'] = date("d.m.Y в H:i",strtotime($row['update_time']));
                $arMessage[$row['id']]['create_time'] = date("d.m.Y в H:i",strtotime($row['create_time']));
                $arMessage[$row['id']]['result_time'] = $result_time;
                $arMessage[$row['id']]['result_time_sys'] = $result_time_sys;
                $arMessage[$row['id']]['status_icon'] = $row['st_icon'];
                $arMessage[$row['id']]['icon_type'] = $row['icon_type'];
                $arMessage[$row['id']]['status_color'] = $row['st_color'];
                $arMessage[$row['id']]['files'] = $arfiles;
                $arMessage[$row['id']]['chat'] = $arChat;
            }
            
            $result = $arMessage;
        }

        return $result;
    }
    
    
    
    //public static function GetMessagesUnit($MessageId)
    //{
        
    //}
    
    public static function GetMessages($UserId = 0, $UserType = 1, $MessageId = 0,$DepartId = 0)
    {
        $result = false;
        $option = '';
        if($MessageId==0)
        {
            if(($UserId != 0) && ($UserType!=6) && ($UserType!=7) && is_numeric($UserId))
            {
                $option = " AND m.`user_id`='".$UserId."'";
            }
            else
            {
                if($UserType == 0)
                {
                    $option = " AND ((m.`status_id`!='5') OR (m.`user_id`='".$_SESSION['UID']."'))";
                }
                
                if($UserType == 6)
                {
                    $orgId = 0;
                    $query = "SELECT `org_id` FROM `users` WHERE `id`='".$_SESSION['UID']."'";
                    if($results = mysqli_query(DataBase::Connect(),$query))
                    {
                        if($row = mysqli_fetch_assoc($results))
                        {
                            $orgId = $row['org_id'];
                        }
                    }
                    if($orgId!=0)
                    {
                        $option = " AND ((m.`status_id`!='5' AND m.`org_id`='".$orgId."') OR (m.`user_id`='".$_SESSION['UID']."'))";
                    }
                    else
                    {
                         $option = " AND (m.`user_id`='".$_SESSION['UID']."')";
                    }
                    
                }
                
                if($UserType == 7)
                {
                    $departId = 0;
                    $query = "SELECT `department_id` FROM `users` WHERE `id`='".$_SESSION['UID']."'";
                    if($results = mysqli_query(DataBase::Connect(),$query))
                    {
                        if($row = mysqli_fetch_assoc($results))
                        {
                            $departId = $row['department_id'];
                        }
                    }
                    if($departId!=0)
                    {
                        $option = " AND ((m.`status_id`!='5' AND m.`depart_id`='".$departId."') OR (m.`user_id`='".$_SESSION['UID']."'))";
                    }
                    else
                    {
                         $option = " AND (m.`user_id`='".$_SESSION['UID']."')";
                    }
                }
            }
        }
        else
        {
            $option = " AND m.`id`='".$MessageId."'";
        }
        
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
                
                $option .= " AND (distr.`id` IN ($arDistrictStr))"; // /* AND m.`status_id`!='6'*/)
                
            }
        }
        
        $remove_archive = '';
        
        if($UserType == 0 && $DepartId==0)
        {
            $remove_archive = " m.`removed` = '0' ";
        }
        else
        {
            //if($UserType == 0)
            $remove_archive = " m.`archive` = '0' ";
        }
        

        
        
        $userOptions = Users::getUserOptions($_SESSION['UID']);
        if(isset($userOptions[3]) && $MessageId==0 && $UserType !=0)
        {
            $option .= " AND (m.`district_id` = '0')"; // /* AND m.`status_id`!='6'*/)
        }
        if(isset($userOptions[5]['value']) && ($userOptions[5]['value']!=null) && $MessageId==0 && $UserType !=0)
        {
            if($userOptions[5]['value']=='overdue')
            {
                $option .= " AND (m.`result_time` IS NOT NULL) AND (m.`result_time` < NOW()) AND (m.`status_id` NOT IN (2,5))"; // /* AND m.`status_id`!='6'*/)
            }
            else
            {
                $option .= " AND (m.`status_id` = '".$userOptions[5]['value']."')"; // /* AND m.`status_id`!='6'*/)
            }
        }
        if(isset($userOptions[6]['value']) && ($userOptions[6]['value']!=null) && $MessageId==0 && $UserType !=0)
        {
            {
                $option .= " AND (m.`category_id` = '".$userOptions[6]['value']."')"; // /* AND m.`status_id`!='6'*/)
            }
        }
        if(isset($userOptions[7]['value']) && ($userOptions[7]['value']!=null) && $MessageId==0 && $UserType !=0)
        {
            {
                $option .= " AND (m.`district_id` = '".$userOptions[7]['value']."')"; // /* AND m.`status_id`!='6'*/)
            }
        }
        
        
        $query = "SELECT
                m.`id`,
                m.`message`,
                m.`coord_x`,
                m.`coord_y`,
                ah.`time` as update_time,
                m.`create_time`,
                st.`id` as status_id,
                st.`name` as status,
                st.`icon` as st_icon,
                st.`status_color` as st_color,
                cat.`name` as cat,
                cat.`yandex_icon` as icon_type,
                distr.`id` as district
            FROM `messages` as m
                LEFT JOIN `city_districts` as distr ON distr.`id` = m.`district_id`
                LEFT JOIN `users` as us ON us.`id` = m.`user_id`
                LEFT JOIN `message_category` as cat ON cat.`id` = m.`category_id`
                LEFT JOIN `message_status` as st ON st.`id` = m.`status_id`
                LEFT JOIN `action_history` as ah ON ah.`message_id` = m.`id`
            WHERE  ".$remove_archive." ".$option." ORDER BY m.`id` ASC ";
    
    
        if($results = mysqli_query(DataBase::Connect(),$query))
        {
            $arMessages = array();
            while($row = mysqli_fetch_assoc($results))
            {
                $update_time = $row['update_time'];
                if($update_time==null)
                {
                    $update_time = $row['create_time'];
                }
                $arMessages[$row['id']]['id'] = $row['id'];
                $arMessages[$row['id']]['center'][] = $row['coord_x'];
                $arMessages[$row['id']]['center'][] = $row['coord_y'];
                $arMessages[$row['id']]['text'] = $row['message'];
                $arMessages[$row['id']]['category'] = $row['cat'];;
                $arMessages[$row['id']]['status'] = $row['status'];
                $arMessages[$row['id']]['status_id'] = $row['status_id'];
                $arMessages[$row['id']]['update_time'] = date("d.m.Y в H:i",strtotime($update_time));
                $arMessages[$row['id']]['create_time'] = date("d.m.Y в H:i",strtotime($row['create_time']));
                $arMessages[$row['id']]['status_icon'] = $row['st_icon'];
                $arMessages[$row['id']]['icon_type'] = $row['icon_type'];
                $arMessages[$row['id']]['status_color'] = $row['st_color'];
                $arMessages[$row['id']]['district'] = $row['district'];
            }
            
            $result = $arMessages;
        } 
        
        return $result;
    }

    
      
    public static function NewMessage($arData)
    {
        $result['status'] = false;
        $result['message'] = '';
        $result['data'] = $arData;
        
        if(isset($arData['user_id']) && isset($arData['coord_x']) && isset($arData['coord_y']) &&
        isset($arData['address']) && isset($arData['district_id']) && isset($arData['org_id']) &&
        isset($arData['depart_id']) && isset($arData['message']) && isset($arData['files']) && isset($arData['category_id']))
        {
            $category_info = Messages::GetCategory($arData['category_id']);
            $deadline = '';
            $deadline_days = 0;
            $deadline_field = '';
            
            if(isset($category_info[$arData['category_id']]['deadline']))
            {
                $deadline_days = $category_info[$arData['category_id']]['deadline'];
            }
            if($deadline_days<>0)
            {
                $deadline_field = ",`result_time`";
                $deadline = ",'".date('Y-m-d', strtotime("+".$deadline_days." days"))."'";
            }
            
            
            
            $query = "INSERT INTO messages (
            `user_id`,
            `coord_x`,
            `coord_y`,
            `address`,
            `district_id`,
            `depart_id`,
            `org_id`,
            `message`,
            `files`,
            `create_time`,
            `category_id`".$deadline_field."
            ) VALUES('"
            .$arData['user_id']."','"
            .$arData['coord_x']."','"
            .$arData['coord_y']."','"
            .$arData['address']."','"
            .$arData['district_id']."','"
            .$arData['depart_id']."','"
            .$arData['org_id']."','"
            .$arData['message']."','"
            .$arData['files']."',
            NOW(),'"
            .$arData['category_id']."'"
            .$deadline.
            ");";
            
            if($results = mysqli_query(DataBase::Connect(),$query))
            {
                $result['status'] = true;
                $arUser = Users::GetUserById($_SESSION['UID']);
                $arUser = $arUser[$_SESSION['UID']];
                $mailParams = array(
                    'MESSAGE_TEXT' => $arData['message'],
                    'MESSAGE_ADRESS' => $arData['address']
                );
                /*
                MEvents::smtpmail('truhinsg@arhcity.ru', 'cleancity@arhcity.ru', 'Пришло новое сообщение', 'Тест');
                
                MEvents::SendMailMessage('truhinsg@arhcity.ru',9,array());
                */
                $result['sendmail'] = MEvents::SendMailMessage($arUser['email'],5,$mailParams);
                if(is_numeric($arData['district_id']))
                {

                    $arDepart = Messages::GetDepartByDistrictId($arData['district_id']);
                    $contactDep = Messages::GetDepartamentContactsByID($arData['depart_id']);
                    
                    if($contactDep["EMAIL"] !="") 
                    {
                        //file_put_contents($_SERVER["DOCUMENT_ROOT"]."/log.txt", var_export($contactDep, true), FILE_APPEND);
                        
                        MEvents::SendMailMessage($contactDep['EMAIL'],9,array());
                        
                    }

                    
                    if(isset($arDepart['USERS']))
                    {
                        //file_put_contents($_SERVER["DOCUMENT_ROOT"]."/log.txt", var_export($arDepart['USERS'], true), FILE_APPEND);
                        MSystem::sendMessageForArray($arDepart['USERS'],7);
                    }
                    else
                    {
                        /*$contactDep = Messages::GetDepartamentContactsByID($arData['district_id']);
                        
                        if($contactDep["USERS"][0]["EMAIL"] != "")
                        {
                            
                            MSystem::sendMessageForArray($contactDep["USERS"],7);
                        }*/
                    }
                }
                if(is_numeric($arData['org_id']) && $arData['org_id']>0)
                {

                    $arEmailList = MSystem::GetUserListByOrgId($arData['org_id']);
                    //file_put_contents($_SERVER["DOCUMENT_ROOT"]."/log.txt", var_export($arEmailList, true), FILE_APPEND);
                    MSystem::sendMessageForArray($arEmailList['USERS'],8);
                }
            }
            else
            {
                $result['message'] = 'При сохранении данных возникла ошибка.';
            }
        }
        else
        {
            $result['message'] = 'При проверке набора данных были выявлены ошибки.';
        }
        
        return $result;
    }

    public static function GetDepartamentContactsByID($id = 1)
    {
        $query = "SELECT C.*, D.name FROM `depart_contacts` AS C JOIN `departments` AS D ON C.dep_id = D.id WHERE `dep_id` = '".$id."'";
        $result = false;
        if($results = mysqli_query(DataBase::Connect(),$query))
        {
            $contacts = array();
            if($row = mysqli_fetch_assoc($results))
            {

                $contacts["EMAIL"] = $row['email'];
                $contacts["phone"] = $row['phone'];
                
                
            }
            else
            {
                $contacts = false;
            }
        }
        $result = $contacts;
        
        return $result;

    }
    public static function GetCategory($id = 0, $DepartId = 0)
    {
        $result = false;
        $option = '';
        if(($id != 0) && is_numeric($id))
        {
            $option = "AND `id`='".$id."'";
        }

        $query = "
        SELECT
            `name`,
            `caption`,
            `description`,
            `deadline`,
            `id`,
            `icon` as icon
        FROM `message_category`
        WHERE `activity` = 1 ".$option;

        if($results = mysqli_query(DataBase::Connect(),$query))
        {
            $arMessageCategory = array();
            while($row = mysqli_fetch_assoc($results))
            {
                $arMessageCategory[$row['id']]['name'] = $row['name'];
                $arMessageCategory[$row['id']]['caption'] = " ".$row['caption'];
                $arMessageCategory[$row['id']]['description'] = " ".$row['description'];
                $arMessageCategory[$row['id']]['icon'] = " ".$row['icon'];
                $arMessageCategory[$row['id']]['deadline'] = $row['deadline'];
            }
        }
        $result = $arMessageCategory;
        
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
    
    
    
    public static function GetDepartByDistrictId($id)
    {
        $result = false;

        if($id!=0)
        {
            $query = "SELECT dep.`name` as department_name, us.`id` as user_id, us.`email` as user_email, us.`alias` as user_name FROM `city_districts` as distr
            LEFT JOIN `departments` as dep ON dep.`id`= distr.`responsible`
            LEFT JOIN `users` as us ON us.`department_id`= dep.`id`
            WHERE distr.`id`='".$id."'";
            if($results = mysqli_query(DataBase::Connect(),$query))
            {
                $arDepartment = array();
                while($row = mysqli_fetch_assoc($results))
                {
                    $arDepartment['NAME'] = $row['department_name'];
                    $arDepartment['USERS'][$row['user_id']]['EMAIL'] = $row['user_email'];
                    $arDepartment['USERS'][$row['user_id']]['USER_NAME'] = $row['user_name'];
                    
                }
            }
            if(count($arDepartment)>0)
            {
                $result = $arDepartment;
            }

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
                    $query = "SELECT `id` FROM `messages` WHERE `id`= '".$id."' AND `result_time` IS NOT NULL AND `result_time` < NOW()";
                    $option = "`status_id`='".$value."', `expired`='0'";
                    
                    if($results = mysqli_query(DataBase::Connect(),$query))
                    {
                        if($row = mysqli_fetch_assoc($results))
                        {
                            if(isset($row['id']))
                            {
                                $option = "`status_id`='".$value."', `expired`='1'";
                            }
                        }
                    }
                    
                    $option = "`status_id`='".$value."'";
                }
                else
                {
                    $errorIndex = true;
                }
                break;
            }
            case 'depart-comment':
            {
                $query = "SELECT `id` FROM `message_depart_comment` WHERE `message_id` = '".$id."'";

                if($results = mysqli_query(DataBase::Connect(),$query))
                {
                    //print_r($results);
                    if($row = mysqli_fetch_assoc($results))
                    {

                        $query = "UPDATE `message_depart_comment`  SET `user_id` = '".$_SESSION['UID']."', `comment` = '".$answer."', `datetime` = NOW()  WHERE `message_id` = '".$id."'";

                        if($results = mysqli_query(DataBase::Connect(),$query))
                        {
                            $result[$id]['comment'] = $answer;
                            return $result;
                        }
                    }
                    else
                    {

                        $query = "INSERT INTO `message_depart_comment` (
                            `message_id`,
                            `user_id`,
                            `comment`,
                            `datetime`
                        ) VALUES (
                        '".$id."',
                        '".$_SESSION['UID']."',
                        '".$answer."',
                        NOW()
                        )";
                        
                        if($results = mysqli_query(DataBase::Connect(),$query))
                        {
                            $result[$id]['comment'] = $answer;
                            return $result;
                        }
                    }
                }
                

                break;
            }
            case 'district': 
                $option = "`district_id`='".$value."', `org_id` = '0'";
                break;
            case 'org':
                $option = "`org_id`='".$value."'";
                break;
            case 'depart':
                $option = "`depart_id`='".$value."'";
                break;
            case 'time':
                if($value!='')
                    $option = "`result_time`='".$value."'";
                else
                    $option = "`result_time`=NULL";
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
                $arResult = self::getOneMessage(0,$id);
                $result = $arResult;
                switch ($type)
                {
                    case 'status':
                        MSystem::SaveActionHistory($_SESSION['UID'],$id,'statusChange','Обновлен статус сообщения',$value);
                        break;
                    case 'depart-comment':
                        MSystem::SaveActionHistory($_SESSION['UID'],$id,'departCommentChange','Обновлен комментарий департамента',$value);
                        break;
                    case 'district':
                        MSystem::SaveActionHistory($_SESSION['UID'],$id,'respUnit','Назначено ответственное подразделение',$value);
                        $arDepart = Messages::GetDepartByDistrictId($value);
                        if(isset($arDepart['USERS']))
                        {
                            MSystem::sendMessageForArray($arDepart['USERS'],7);
                        }
                        break;
                    case 'org':
                        MSystem::SaveActionHistory($_SESSION['UID'],$id,'respOrganization','Назначена ответственная организация',$value);
                        break;
                    case 'depart':
                        MSystem::SaveActionHistory($_SESSION['UID'],$id,'respDepartment','Назначен контролирующий департамент',$value);
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
    
    public static function getUsersByDepart($depart_id)
    {
        $result = false;        
        if(is_numeric($depart_id) && $depart_id>0)
        {
            $curDepart = 0;
            $arUser = Users::GetUserById($_SESSION['UID']);
            $arUser = $arUser[$_SESSION['UID']];
            if(isset($arUser['department_id']))
            {
                $curDepart = $arUser['department_id'];
            }
            $query = "SELECT `id`,`alias`,`phone` FROM `users` WHERE `department_id` = '".$depart_id."' AND `department_id`<>'".$curDepart."' AND email<>'mailtesttestovich@gmail.com' AND `activity`='1'";
            if($results = mysqli_query(DataBase::Connect(),$query))
            {
                $arUsers = array();
                while($row = mysqli_fetch_assoc($results))
                {
                    $arUsers[] = $row;
                }
                $result = $arUsers;
            }
        }
        
        return $result;
    }
    

}


?>
