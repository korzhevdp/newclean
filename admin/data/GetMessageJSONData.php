<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы Вам недоступен.'); ?>
<?php
$arResult = array();
$arResult['status'] = false;
$arResult['message'] = 'Не удалось получить данные о сообщениях.';
$arResult['data'] = array();

if(!isset($_SESSION['UID']))
    exit('<div class="big-logo"><div class="img"></div></div><p class="page-error">Не удалось определить пользователя.<a href="?logout=yes" class="icon-left-open-big">Вернуться обратно</a></p>');
		
$arUser = Users::GetUserById($_SESSION['UID']);
$arUser = $arUser[$_SESSION['UID']];
$UserOrganization = Users::isOrganization($arUser['group_id']);

$law1 = Users::UserLawByGroup($arUser['group_id'],"law1"); // Просмотр всех сообщений, зарегистрированных в системе
$law2 = Users::UserLawByGroup($arUser['group_id'],"law2"); // Просмотр всех сообщений, проверенных модератором и опубликованных в системе
$law6 = Users::UserLawByGroup($arUser['group_id'],"law6"); // Просмотр общей статистики сообщений по различным критериям
$law7 = Users::UserLawByGroup($arUser['group_id'],"law7"); // Доступ в раздел администрирования. Данное право определяет возможность входа пользователя в раздел '/admin/' (Без указания дополнительных прав будет только просмотр)


if(!$law7)
{
    $_SESSION['SSUID'] = null;
    $_COOKIE['SSUID'] = null;
    exit('<div class="big-logo"><div class="img"></div></div><p class="page-error">Извините, но у Вас недостаточно прав доступа к данному разделу системы.<a href="#" id="reload" onclick="window.location.reload();" class="icon-left-open-big">Вернуться обратно</a></p>');
}

$DepartId = 0;
$UserResponsibleUnit = Users::isResponsibleUnit($arUser['group_id']); // если пользователь относится к группе - ответственные подразделения
//$UserEditRight = Users::isAllEditRight($arUser['group_id']);
$UserOrganization = Users::isOrganization($arUser['group_id']);
$supervisoryAuthority = Users::isSupervisoryByGroup($arUser['group_id']);
$isAdmin = Users::isAdmin($arUser['group_id']);

if($UserResponsibleUnit)
{
    $DepartId = $arUser['department_id'];
}


$arMessages = array();
if($law2 || $law1 || $law6)
{
	if($law1)
	{
		$arMessages = Messages::GetMessages(0,1,0,$DepartId);
	}
	else
	{
		if($UserOrganization)
		{
			$arMessages = Messages::GetMessages(0,6,0,0);
		}
		else
		{
			if($arUser['group_id']==7)
			{
				$arMessages = Messages::GetMessages(0,7,0,$DepartId);
			}
			else
			{
				if($arUser['group_id']==5)
				{
					$arMessages = Messages::GetMessages(0,5,0,$DepartId);
				}
				else
				{
					$arMessages = Messages::GetMessages(0,0,0,$DepartId);
				}
			}
		}
		
	}
}

$userOptions = array();
$allDistrictsData = array();

$userOptions = Users::getUserOptions($_SESSION['UID']);

if(isset($userOptions[4])) {
	$allDistrictsData['data'] = MSystem::GetAllSpecialDistrictData();
	$allDistrictsData['length'] = count($allDistrictsData['data']);
}

if(is_array($arMessages))
{
	$arResult['status'] = true;
	$arResult['message'] = 'Данные успешно получены';
	$arResult['data'] = $arMessages;
	$arResult['options'] = $userOptions;
	$arResult['distr_data'] = $allDistrictsData;
}

echo json_encode($arResult);
?>
