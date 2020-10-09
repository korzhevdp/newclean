<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы для Вас недоступен.'); ?>
<?php

$DepartId = 0;
$UserResponsibleUnit = Users::isResponsibleUnit($arUser['group_id']); // если пользователь относится к группе - ответственные подразделения
//$UserEditRight = Users::isAllEditRight($arUser['group_id']);
$UserOrganization = Users::isOrganization($arUser['group_id']);
$supervisoryAuthority = Users::isSupervisoryByGroup($arUser['group_id']);
$isAdmin = Users::isAdmin($arUser['group_id']);
$globalOptions = MSystem::GetGlobalSysOptions();
$userOptions = Users::getUserOptions($_SESSION['UID']);
$userChatRight = Chat::getChatRightByUserGroup($arUser['group_id']);

$isDepartment = false;
if($arUser['group_id']==7)
{
	$isDepartment = true;
}

// если пользователь представитель ответственного подразделения
$getCoordFromOSM = false;
$linkToOSM = '';
$districtCoord = '';
$districtName = $arUser['district'];
$districtFullName = '';


$allDistrictsData = array();

if(isset($userOptions[4])) {
	$allDistrictsData['data'] = MSystem::GetAllSpecialDistrictData();
	$allDistrictsData['length'] = count($allDistrictsData['data']);
}

if($UserResponsibleUnit)
{
    $DepartId = $arUser['department_id'];
	
	// получение координат границ округов
	
	if(isset($globalOptions['GetCoordinatesFromOSM']) && $globalOptions['GetCoordinatesFromOSM']['value']=='1')
	{
		if(isset($globalOptions['OpenStreetMapLink']) && $globalOptions['OpenStreetMapLink']['value']!='')
		{
			$linkToOSM = $globalOptions['OpenStreetMapLink']['value'];
			$getCoordFromOSM = true;
		}
	}
	
	if(!isset($allDistrictsData['data']))
	{
		$districtData = MSystem::GetSpecialDistrictData($arUser['district_id']);
	}
	
	
	if(isset($districtData['coordinates']) && $districtData['coordinates']!='')
	{
		if(!$getCoordFromOSM)
		{
			$districtCoord = $districtData['coordinates'];
		}
		$districtFullName = $districtData['full_name'];
	}
	/*
	else
	{
		if(count($allDistrictsData)>0)
		{
			
		}
	}
	*/
	
}


$arMessages = array();
$userOptions = array();

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

//echo $DepartId;
$userOptions = Users::getUserOptions($_SESSION['UID']);

?>

<div id="map"></div>
<script src="//api-maps.yandex.ru/2.1/?lang=ru-RU" type="text/javascript"></script>
<script src="//yandex.st/jquery/2.2.3/jquery.min.js" type="text/javascript"></script>
<script>
var UserResponsibleUnit = '<?php echo $UserResponsibleUnit ?>';
var UserOrganization = '<?php echo $UserOrganization ?>';
var UserDepartment = '<?php echo $isDepartment ?>';
var supervisoryAuthority = '<?php echo $supervisoryAuthority ?>';
var isAdmin = '<?php echo $isAdmin ?>';
<?php
echo "var g_messages = false;\n";
echo "var g_options = false;\n";
echo "var arDistrictsData = false;\n";
if(count($arMessages)>0)
{
	echo "g_messages = ".json_encode($arMessages).";\n";
	echo "g_options = ".json_encode($userOptions).";\n";
	echo "g_chat = ".json_encode($userChatRight).";\n";
}


if(isset($allDistrictsData['data']))
{
	echo "arDistrictsData = ".json_encode($allDistrictsData).";\n";
}
?>
//var UserEditRight = '<?php echo Users::isAllEditRight($arUser['group_id']) ?>';


var getCoordFromOSM = '<?php echo $getCoordFromOSM ?>';
var linkToOSM = '<?php echo $linkToOSM ?>';
<?php if($districtCoord!=''): ?>
var districtCoord  = <?php  echo $districtCoord ?>;
<?php else: ?>
var districtCoord  = '';
<?php endif; ?>

var districtName = '<?php  echo $districtName ?>';
var districtFullName = '<?php echo $districtFullName ?>';



var law1 = '<?php echo $law1 ?>'; 
var law2 = '<?php echo $law2 ?>'; 
var law3 = '<?php echo $law3 ?>'; 
var law4 = '<?php echo $law4 ?>';
var law4_1 = '<?php echo $law4_1 ?>'; 
var law5 = '<?php echo $law5 ?>'; 
var law6 = '<?php echo $law6 ?>'; 
var law7 = '<?php echo $law7 ?>';
var law8 = '<?php echo $law8 ?>';

</script>