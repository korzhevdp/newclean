<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы для Вас недоступен.'); ?>
<?php
$arUser = Users::GetUserById($_SESSION['UID']);
$arUser = $arUser[$_SESSION['UID']];
    
if(!Users::isAdmin($arUser['group_id']))
	exit('Извините, у вас недостаточно прав для доступа к данному разделу.  Панель настроект находится на стадии разработки.');

$errorIndex = 1;
if((isset($_POST['table_id'])) && (is_numeric($_POST['table_id'])))
{
	$errorIndex = 0;
}

$tableId = $_POST['table_id'];

if($errorIndex==1) exit('При проверке данных возникли ошибки');

$arTables = array(
		"1" => "users",
		"2" => "user_groups",
		"3" => "departments",
		"4" => "organization",
		"5" => "message_category",
		"6" => "message_status",
		"7" => "developer_notes",
		"8" => "messages",
		"9" => "mail_events",
		"10" => "feedback"
);

$arData = $_POST['data'];

$arResult = MSystem::AddOptionsDataRow($arTables[$tableId],$arData);


print json_encode($arResult);

?>