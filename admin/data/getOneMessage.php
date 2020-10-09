<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы Вам недоступен.'); ?>
<?php
$arResult = array();
$arResult['status'] = false;
$arResult['data'] = array();
$arResult['message'] = 'При выполнении запроса произошла ошибка.';

if(isset($_POST['message_id']) && is_numeric($_POST['message_id']))
{
	if($law7) // проверка доступа к содержимому сообщения
	{
			$result = Messages::getOneMessage(0,$_POST['message_id']);
			if(count($result)>0)
			{
				usleep(300000);
				$arResult['data'] = $result[$_POST['message_id']];
				$arResult['status'] = true;
				$arResult['message'] = 'Данные успешно получены.';
			}
	}
	else
	{
			$arResult['message'] = 'У Вас недостаточно прав для получения данной информации.';
	}
}
else
{
	$arResult['message'] = 'Сервер получил некорректный запрос.';
}
echo json_encode($arResult);
?>
