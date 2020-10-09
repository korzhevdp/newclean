<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы для Вас недоступен.'); ?>
<?php

$arResult = array();
$arResult['status'] = false;
$arResult['message'] = 'Не удалось определить округ';
$arResult['distr_name'] = '';

$authResult = array();

if(isset($_POST['X']) && isset($_POST['Y']))
{
    $arDistricts = MSystem::GetAllSpecialDistrictData();
    foreach($arDistricts as $key => $dist)
    {
        if(AreaPoint($_POST['X'],$_POST['Y'],json_decode($dist['coordinates'])))
        {
            $arResult['status'] = true;
            $arResult['message'] = 'Данные успешно получены';
            $arResult['distr_name'] = $dist['name'];
            break;
        }
    }
}

echo json_encode($arResult);

?>