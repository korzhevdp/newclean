<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы для Вас недоступен.'); ?>
<?php
$arUser = Users::GetUserById($_SESSION['UID']);
$arUser = $arUser[$_SESSION['UID']];
    
if(!Users::isAdmin($arUser['group_id']))
	exit('Извините, у вас недостаточно прав для доступа к данному разделу.  Панель настроект находится на стадии разработки.');

$errorIndex = 1;
if(isset($_POST['id']) && (is_numeric($_POST['id'])) && (isset($_POST['table'])) && (is_numeric($_POST['table']))
&& (isset($_POST['field'])) && (isset($_POST['value'])) && (isset($_POST['datatype'])) && (is_numeric($_POST['datatype'])))
{
	$errorIndex = 0;
}

if($errorIndex==1) exit('При попытке обработки введенных данных произола ошибка.');

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


$arOptionReturn = array();
$arResult = array();
$field = CharacterFilter($_POST['field']);
$value = CharacterFilter($_POST['value']);

if($_POST['datatype']!=3)
{
	$arOptionReturn = MSystem::SetSysTableData($arTables[$_POST['table']],$_POST['id'],$field,$value);
}
else
{
	$arOptionReturn = MSystem::DeleteSysTableData($arTables[$_POST['table']],$_POST['id']);
}

if($arOptionReturn)
{
	$arResult = $arOptionReturn[$_POST['id']];
	switch ($_POST['datatype'])
	{
		case '1': // Если input
		{
			echo $arResult[$field];
			break;
		}
		case '2': // Если select
		{
			echo $arResult[$field];
			break;
		}
		case '3': // Если запрос на удаление записи
		{
			echo '1';
			break;
		}
		default:
		{
			echo "Не удалось получить значение поля";
			break;
		}
	}
}
else
{
	echo "Не возможно использовать данное значение, т.к. оно уже существует в данной таблице.";
}
?>