<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы для Вас недоступен.'); ?>
<?php

$arResult = array();
$arResult['status'] = false;
$arResult['message'] = '';


$arUser = Users::GetUserById($_SESSION['UID']);
$arUser = $arUser[$_SESSION['UID']];


if(!Users::UserLawByGroup($arUser['group_id'],'law7')) // проверка наличия прав на выполнение операции
{
    $arResult['message'] = 'Извините, у вас недостаточно прав для выполнения данной операции.';
}
else
{
    if(isset($_POST['option_id']) && is_numeric($_POST['option_id']) && isset($_POST['option_value']) && is_numeric($_POST['option_value']))
    {
        if($_POST['option_value']==0)
        {
            $setOption = Users::setUserOptions($_SESSION['UID'],$_POST['option_id'],0); // отменить настройку
        }
        else
        {
            $setOption = Users::setUserOptions($_SESSION['UID'],$_POST['option_id'],1); // активировать настройку
        }
        
        if($setOption)
        {
            $arResult['status'] = true;
            $arResult['message'] = 'Настройка успешно сохранена';
        }
    }
    else
    {
        $arResult['message'] = 'Сервер получает недопустимые значения отправляемых данных. Обратитесь к администратору системы.';
    }
    
    
}

echo json_encode($arResult);

?>