<?php

$Result['status'] = false;
$Result['message'] = '';
$DATA = array();

if(isset($_POST['func']) && is_numeric($_POST['func']))
{
    $FUNC = $_POST['func'];
    
    if(isset($_POST['data']) && is_array($_POST['data']))
	{
        $DATA = $_POST['data'];
    }
	session_start();
	include_once("db.php");
	include_once("../include/classes/MSystem.php");
	include_once("../include/classes/User.php");
	include_once("../include/classes/Messages.php");
    
    switch ($FUNC)
	{
        case 1:
            $Result = Users::RegUser($DATA);
            break;  
        case 2:
            $Result = Users::AuthUser($DATA);
            break;
		case 3:
            $Result = Users::RegAdmin($DATA);
            break;
		case 4:
			if(isset($DATA['message_id']))
			{
				$Result = Messages::MessageDeleteForUser($DATA['message_id']);
			}
            break; 
        default:
            $Result['message'] = 'Запрос не определен';
            break;
    }
    
} else $Result['message'] = 'Запрос некорректен';


echo json_encode($Result); 

?>