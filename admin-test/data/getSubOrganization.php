<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы для Вас недоступен.'); ?>
<?php
$arResult = array();
$arResult['status'] = false;
$arResult['message'] = '';
$arResult['departments'] = array();
if(isset($_POST['org_id']) && is_numeric($_POST['org_id']))
{
	$result = MSystem::getSubOrganization($_POST['org_id']);
	if(is_array($result))
	{
		$arResult['status'] = true;
		$arResult['message'] = 'Получен перечень связанных подразделений';
		if(count($result))
		{
			$arResult['departments'] = $result;
		}
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