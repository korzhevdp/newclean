<?php
	session_start();
	include_once("../db/db.php");
	include_once("../classes/User.php");
	include_once("../classes/Messages.php");
	$arResult = array();
	
	
	switch ($_POST['action'])
  {
			case  1: {
				$arResult = Users::AuthUser($_POST);
				break;
			}
			case  2: {
				$arResult = Users::isAuthorized($_POST['key']);
				break;
			}
			case  3: {
				$arResult = Users::RegUser($_POST);
				break;
			}
			case  4: {
				$arResult = Messages::GetCategoryList($_POST['key']);
				break;
			}
			case  5: {
				$arResult = Messages::setMapLocation($_POST);
				break;
			}
			default:
        //require_once('pages/404.php');
        break;
			
	}
	
	
	
	//$arResult['status'] = true;
	//$arResult['message'] = "Данные успешно отправлены";
	
	echo json_encode($arResult);
?>