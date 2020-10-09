<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы для Вас недоступен.'); ?>
<?php

$arResult = array();
$arResult['status'] = false;
$arResult['message'] = 'Не удалось изменить пароль';


$recResult = array();

if(isset($_POST['data']['password']) && isset($_POST['data']['recovery_key']))
{
    $recResult = Users::newPasswordAfterRecovery($_POST['data']['password'],$_POST['data']['recovery_key']);
    if(isset($recResult['status']) && isset($recResult['message']))
    {
        $arResult['status'] = $recResult['status'];
        $arResult['message'] = $recResult['message'];
    }
}

echo json_encode($arResult);

?>