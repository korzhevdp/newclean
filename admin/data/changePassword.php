<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы для Вас недоступен.'); ?>
<?php
$arResult = array();
$arResult['status'] = false;
$arResult['message'] = '';

if(isset($_SESSION['UID'])) {


	$arUser = Users::GetUserById($_SESSION['UID']);
	$arUser = $arUser[$_SESSION['UID']];

	if(isset($arUser['user_id']))
	{
			if(isset($_POST['current_password']) && isset($_POST['new_password']))
			{
					
					$changePassword = Users::changePassword($arUser['email'],$arUser['user_id'],$_POST['current_password'],$_POST['new_password']);
					//$arResult['status'] =
					if($changePassword['status'] == true)
					{
						$arResult['status'] = true;
						$arResult['message'] = 'Пароль успешно изменен. В следующий раз, заходя в систему, введите новый пароль.';
					}
					else
					{
						$arResult['message'] = $changePassword['message'];
					}
			}
			else
			{
				$arResult['message'] = 'Сервер получает некорректный запрос.';
			}
	} else $arResult['message'] = 'Пользователь неопределен';
}
else
{
		$arResult['message'] = 'Не определена сессия пользователя';
}
echo json_encode($arResult);
?>