<div class="big-logo">
	<?php if(Users::isAuthorized()): ?>
		<a href="#" class="top-menu-btn icon-bars"></a>
		<a href="#" class="top-feedback-btn icon-comments"></a>
	<?php endif; ?>
	<div class="img"></div>
</div>
<p class="center gray little-text">В рамках системы <br>Активный гражданин<p>
<?php if(Users::isAuthorized()): ?>
	<!--<p class="center gray little-text">Если у Вас возникли проблемы при работе,<br>пожалуйста, <a href="#" class="default" id="feedback">сообщите об этом</a><p>-->
<?php endif; ?>
<p class="center gray little-text only-for-big-screen">Текущая версия ориентирована на использование в мобильных устройствах. <br>В ближайшем будущем мы разработаем полноценный интерфейс для широкоформатных устройств.<p>
<!--
<div id="togglemenu" class="sidetogglemenu">
	<div style="padding: 15px;">Скоро тут появится меню</div>
  <ul class="mainnav">
			<a href="#" class="btn-menu-closer icon-left-open-big"></a>
  </ul>
</div>
-->
<script>
	/*
$(function(){ // on DOM load
	menu = new sidetogglemenu({  // initialize first menu example
		id: 'togglemenu',
		marginoffset: 10
	});
	
});
*/
</script>