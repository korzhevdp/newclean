<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы для Вас недоступен.'); ?>
<?php

$arResult = array();
$arResult['status'] = false;
$arResult['message'] = 'Не удалось авторизовать пользователя по ключу';


$authResult = array();

if(isset($_POST['key']))
{
    $authResult = Users::isAuthorizedByKey($_POST['key']);
    if(isset($authResult['status']) && $authResult['status']==true && isset($authResult['id']) && $authResult['id']>0)
    {
        //$_SESSION['SSUID'] = $SSID;
        $_SESSION['UID'] = $authResult['id'];
        $arResult['status'] = true;
        $arResult['message'] = 'Успешная авторизация по ключу';
    }
}

echo json_encode($arResult);

?>