<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Chatmodel extends CI_Model {

	public function index() {
		$this->load->view('admin/admin');
	}

	public function sendMessage( $messageID, $userID, $departID, $orgID, $text ) {

		$mainID = $this->commonmodel->getDistrictByMessageId($messageID);
		if (   !is_numeric($messageID)
			|| !is_numeric($userID)
			|| !is_numeric($departID)
			|| !is_numeric($orgID)
			|| !is_numeric($mainID)
		){
			return array(
				'status'  => false,
				'message' => 'При проверке входящих параметров выявлены ошибки',
			);
		}

		$text = $this->commonmodel->characterFilter($text);
		if ( !strlen( trim( $text ) ) ) {
			return array(
				'status'  => false,
				'message' => 'Текст сообщения не может быть пуст',
			);
		}

		$result = $this->db->query("SELECT
		`chat`.`id`,
		`chat`.`active`
		FROM `chat`
		WHERE
		`chat`.`message_id`       = ?
		AND `chat`.`main_unit_id` = ?
		AND `chat`.`user_id`      = ?
		AND `chat`.`depart_id`    = ?
		AND `chat`.`org_id`       = ?
		AND `chat`.`active`       = 1", array(
			$messageID,
			$mainID,
			$userID,
			$departID,
			$orgID
		) );
		if ( $result->num_rows() ) {
			$this->db->query("INSERT INTO
			`chat_messages` (
				`chat_messages`.`chat_id`,
				`chat_messages`.`user_id`,
				`chat_messages`.`text`
			) VALUES ( ?, ?, ? )", array(
				$row->id,
				$this->session->userdata("UID"),
				$text
			));

			$user = $this->usermodel->getUserById($this->session->userdata("UID"));
			return array(
				'status'  => true,
				'message' => 'Сообщение сохранено',
				'data'    => array(
					'text' => $text,
					'time' => date('Y.m.d в H:i'),
					'user_alias' => $user['user_name']
				)
			);
		}
		return array(
			'status'  => false,
			'message' => 'Чат заблокирован, вы не можете отправить сообщение',
		);

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
        
		return array(
			'status'  => false,
			'message' => 'При отправке сообщения возникла ошибка'
		);
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