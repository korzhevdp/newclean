<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы для Вас недоступен.'); ?>
<?php

$arUser = Users::GetUserById($_SESSION['UID']);
$arUser = $arUser[$_SESSION['UID']];
    
if(!Users::isMainUserByGroup($arUser['group_id']))
{
        $_SESSION['SSUID'] = null;
        $_COOKIE['SSUID'] = null;
        exit('Извините, у вас недостаточно прав для доступа к данному разделу.');
}

if(isset($_POST['type']) && isset($_POST['value']) && isset($_POST['answer'])
&& (SymbolSecur($_POST['answer'])) && isset($_POST['id']) && is_numeric($_POST['id'])
&& isset($_POST['filedata']) && SymbolSecur($_POST['filedata']))
{
	$type = $_POST['type'];
	$value = $_POST['value'];
	$answer = $_POST['answer'];
	$file[]['value'] = $_POST['filedata'];
	$answer_file_path = '';
	$id = $_POST['id'];
	

	if($type == 'status' && !$law3) {
		exit('У вас нет прав для изменения статуса сообщений.');
	}
	if($type == 'org' && !$law4) {
		exit('У вас нет прав для назначения ответственной организации.');
	}
	if($type == 'depart' && !$law4) {
		exit('У вас нет прав для закрепления департамента.');
	}
	if($type == 'time' && !$law8) {
		exit('У вас нет права назначать срок исполнения.');
	}
	if($type == 'district' && !Users::isSupervisoryByGroup($arUser['group_id']) && !Users::isAdmin($arUser['group_id'])) {
		exit('У вас нет права назначать ответственное подразделение.');
	}
	
	if($_POST['filedata']!='')
	{
		$file_result = SaveFiles($file);
		if($file_result['status'])
		{
			if(isset($file_result['content'][0]))
			{
				$answer_file_path = $file_result['content'][0];
			}
		}
	}

	
	$arOptionReturn = Messages::SaveMessageData($type,$value,$answer,$answer_file_path,$id);
	$arResult = $arOptionReturn[$id];
	$strAnswer = '';
	$strAnswerFile = '';
	
	if($arResult)
	{
		switch ($type)
		{
			case 'status':
			{
				$orgName = $arResult['org_name_only'];
				if($orgName!=null)
					$orgName = ' - '.$orgName;
				$strResult = '<div class="status '.$arResult['status_icon'].' '.$arResult['status_color'].'">'.$arResult['status'].'</div>'; //$orgName
				if(isset($arResult['answer']) and $arResult['answer']!='')
				{
					if($arResult['answer_file_path']!='')
					{
						$strAnswerFile = '<div class="preview-answer-img" style="background-image: url(\''.$arResult['answer_file_path'].'\');"></div>';
					}
					
					$strAnswer = '<p class="answer-caption"><b>Ответ, который видит пользователь:</b></p><div class="answer">'.$arResult['answer'].'</div>'.$strAnswerFile;
				}
				$button_class = 'change-status';
				break;
			}
			case 'district':
			{
				$strResult = '<i class="little">'.$arResult['responsible'].'</i>';
				$button_class = 'change-responsible';
				break;
			}
			case 'org':
			{
				$strResult = '<i class="little">'.$arResult['org_name'].'</i>';
				$button_class = 'change-org';
				break;
			}
			case 'depart':
			{
				$strResult = '<i class="little">'.$arResult['depart_name'].'</i>';
				$button_class = 'change-depart';
				break;
			}
			case 'time':
			{
				$strResult = '<i class="little">Устранить до: <b>'.$arResult['result_time'].'</b></i>';
				$button_class = 'change-time';
				break;
			}
			default:
			{
				$strResult = 'Не удалось определить свойство объекта';
				$button_class = '';
				break;
			}
		}
		echo $strResult.'<a href="#" class="action-btn '.$button_class.'">Изменить</a>'.$strAnswer;
		
	} else
	{
		echo 'В результате выполнения запроса возникла ошибка';
	}
	
}
else
echo 'Не удалось применить изменение т.к. данные запроса некорректны';

?>