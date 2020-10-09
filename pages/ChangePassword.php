<?php if(!isset($AccessIndex)) exit('Контент данной страницы для вас недоступен.'); ?>

<div class="header-panel sticky">
    <a href="#" class="icon-left-open-big slide-back" id="back_lk">Назад</a>
    <div>Новый пароль</div>
</div>

<?php

$email = '';

if(isset($_GET['recovery_key']))
{
	$arUser = Users::getUserLoginByReqKey($_GET['recovery_key']);
	if(is_array($arUser))
	{
		$email = $arUser['email'];
	}
}

?>

<div class="container change-password form">
	<p>Восстановление доступа к личному кабинету</p>
	<div class="gray-cont">
		<?php if(isset($_GET['recovery_key'])): ?>
			<input type="hidden" name="recovery_key" value="<?php echo $_GET['recovery_key'];?>">
		<?php endif; ?>
		<input type="password" placeholder="Пароль" name="password1">
		<input type="password" placeholder="Повторить пароль" name="password2">
		<div class="form-error"></div>
		<a href="#" class="btn submt" id="new_password">Сохранить пароль</a>
	</div>
</div>