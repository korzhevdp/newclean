<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы для Вас недоступен.'); ?>

<?php //require_once('include/header.php'); ?>
<?php require_once('pages/header.php'); ?>

<div class="container auth">
	<div class="gray-cont">
		<h2>Восстановление пароля</h2>
		<p>Восстановление доступа к личному кабинету</p>
		<?php if(isset($_GET['recovery_key'])): ?>
			<input type="hidden" name="recovery_key" value="<?php echo $_GET['recovery_key'];?>">
		<?php endif; ?>
		<input type="password" placeholder="Пароль" name="password1">
		<input type="password" placeholder="Повторить пароль" name="password2">
		<div class="form-error"></div>
		<a href="#" class="btn submt" id="new_password">Сохранить пароль</a>
	</div>
</div>
<a href="/admin/" class="bl-link icon-key">Войти в личный кабинет</a>
<div class="copyright">© МУ &laquo;Центр информационных технологий&raquo; 2018</div>

<?php //require_once('include/footer.php'); ?>