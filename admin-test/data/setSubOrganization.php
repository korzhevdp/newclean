<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы для Вас недоступен.'); ?>
<?php
$arResult = array();
$arResult['status'] = false;
$arResult['message'] = '';
if(isset($_POST['depart_id']) && is_numeric($_POST['depart_id']) &&
isset($_POST['org_id']) && is_numeric($_POST['org_id']) &&
isset($_POST['action_type']) && is_numeric($_POST['action_type']))
{
	$result = MSystem::setSubOrganization($_POST['depart_id'],$_POST['org_id'],$_POST['action_type']);
	if($result)
	{
		$arResult['status'] = true;
		$arResult['message'] = 'Связь между подразделением и ответственной организацией успешно установлена.';
	}
	else
	{
		$arResult['message'] = 'Не удалось выполнить запрос';
	}
}
else
{
	$arResult['message'] = 'Сервер получил некорректный запрос.';
}
echo json_encode($arResult);
?>