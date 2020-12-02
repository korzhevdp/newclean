<?=$header;?>

<div class="container auth">
	<div class="gray-cont">
		<a href="#" ref="/appeal/category" class="btn icon-mail submit" id="message">Сообщить о нарушении</a>
	</div>
</div>

<div class="header-panel sticky">
	<div>Мои сообщения</div>
	<a href="#" class="add-btn" id="message"></a>
</div>

<div class="search icon-search">
	<input type="text" placeholder="Поиск..."/>
</div>

<div class="container">
	<?=$myMessages;?>
	<a href="/appeal/usermap" class="btn icon-pin" target="_blank">Мои сообщения на карте (<?=$messageCount; ?>)</a>
</div>


<div class="container mg20px">
	<a href="/about"  class="item-link icon-info" target="_blank">О сервисе</a>
	<a href="/login/logout" class="item-link icon-lock">Выход</a>
</div>


<script>
	if ($(location).attr('href') != "<?=$link;?>") {
		window.history.pushState("", "Чистый город - подача обращения", "<?=$link;?>");
	}
</script>
