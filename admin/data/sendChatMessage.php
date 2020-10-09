<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы Вам недоступен.'); ?>
<?php
$arResult = array();
$arResult['status'] = false;
$arResult['message'] = '';

$dataSuccess = false;
if(isset($_POST['message_id']) && is_numeric($_POST['message_id']) && isset($_POST['user_id']) && is_numeric($_POST['user_id'])
&& isset($_POST['depart_id']) && is_numeric($_POST['depart_id']) && isset($_POST['org_id']) && is_numeric($_POST['org_id']))
{
	$dataSuccess = true;
}

if(!$dataSuccess)
{
	$arResult['message'] = 'Данные, отправляемые в запросе, имеют некорректный формат. Обратитесь к системному администратору.';
	echo json_encode($arResult);
	exit();
}

$arResult = Chat::sendMessage($_POST['message_id'],$_POST['user_id'],$_POST['depart_id'],$_POST['org_id'],$_POST['text']);

echo json_encode($arResult);
?>
