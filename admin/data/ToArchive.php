<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы для Вас недоступен.'); ?>
<?php
$arResult = array();
$arResult['status'] = false;
$arResult['message'] = '';
if(isset($_POST['id']) && is_numeric($_POST['id']))
{
	if($law5)
	{
		$arResult['status'] = Messages::MessageToArchive($_POST['id']);
		$arResult['message'] = 'Сообщение успешно отправлено в архив.';
	}
	else
	{
		$arResult['message'] = 'У вас нет прав для отправки сообщений в архив.';
	}
}
else
{
	$arResult['message'] = 'Сервер получает некорректный запрос.';
}
echo json_encode($arResult);
?>