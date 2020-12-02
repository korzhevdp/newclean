
<?=$header;?>

<div class="container change-password form">
	<div class="gray-cont">
		<p>Для восстановления пароля введите Ваш E-mail, указанный при регистрации</p>
		<input type="text" placeholder="Email" data-type="email" id="email">
		<div class="form-error"></div>
		<p>На указанный E-mail будет выслана ссылка для восстановления пароля</p>
		<a href="#" class="btn" id="restoreMe">Восстановить</a>
	</div>
	

	
</div>

<a href="#" ref="/registration" class="bl-link icon-users submit">Регистрация пользователя</a>
<a href="#" ref="/welcome" class="bl-link icon-bars submit">Перейти в главный раздел</a>

<script type="text/javascript">
	window.history.pushState("", "Чистый город - логин", "<?=base_url();?>passwordrestore");
	$("#restoreMe").unbind().click(function(e) {
		e.preventDefault();
		return false;
		$.ajax({
			url      : '/login/passwordrestore',
			type     : "POST",
			data     : {
				email    : $("#email").val()
			},
			dataType : "html",
			success  : function(data) {
				$("#appContent").html(data);
				setListener();
			},
			error: function(data,stat,err) {
				console.log(data,stat,err);
			}
		});

	});
</script>