<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы для Вас недоступен.'); ?>
<?php
$arUser = Users::GetUserById($_SESSION['UID']);
$arUser = $arUser[$_SESSION['UID']];
$law7 = Users::UserLawByGroup($arUser['group_id'],"law7");
//Users::isAdmin($arUser['group_id'])
if(!$law7)
{
	exit('<div class="pg10px">Извините, у вас недостаточно прав для доступа к данному разделу.  Панель настроек на стадии разработки и доступна только системным администраторам</div>');
}

?>
<div class="container default-options">
	<div class="item">
		<p class="header">Изменить пароль</p>
		<div class="form-container">
			<p>Для смены пароля введите свой текущий пароль и придумайте новый.</p>
			<div class="form-control field"><input type="password" name="password" placeholder="Введите текущий пароль"></div>
			<br>
			<div class="form-control field"><input type="password" name="password1" placeholder="Введите новый пароль"></div>
			<div class="form-control field"><input type="password" name="password2" placeholder="Повторите новый пароль"></div>
			<p class="error-line"></p>
			<p class="success-line"></p>
			<div class="form-control">
				<a href="#" class="form-button right" id="change_password_submit">Изменить пароль</a>
			</div>
			<div class="separ-line"></div>
		</div>
	</div>
	<div class="item">
		<p class="header">Персональные данные</p>
	</div>
	<div class="item">
		<p class="header">Отправка уведомлений</p>
	</div>

	
</div>
<script>
	var mwContainer = $(document).find('.mwindow');
	var options = mwContainer.find('.default-options .item');
	var mwContainerHeight = mwContainer.innerHeight();
	
	options.css('height',(mwContainerHeight-120)+'px');
</script>
