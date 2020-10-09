<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы для Вас недоступен.'); ?>
<?php

$arResult = array();
$arResult['status'] = false;
$arResult['message'] = 'Не удалось отправить сообщение на указанный email';


$authResult = array();

if(isset($_POST['email']))
{
    $userType = 0;
    if(isset($_POST['type']) && is_numeric($_POST['type']))
    {
        $userType = $_POST['type'];
    }
    $recResult = Users::userPasswordRecovery($_POST['email'],$userType);
    if(isset($recResult['status']) && isset($recResult['message']))
    {
        $arResult['status'] = $recResult['status'];
        $arResult['message'] = $recResult['message'];
    }
}

echo json_encode($arResult);

?>