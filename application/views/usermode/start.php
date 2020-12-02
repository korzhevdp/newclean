<?=$header;?>

<div class="container first-page">
	<div class="gray-cont">
		<a href="#" ref="/appeal" class="btn icon-street-view submit">Вход для граждан<i>Вы сможете сообщить о проблеме</i></a>
		<a href="/admin" class="btn icon-lock only-for-big-screen">В раздел администрирования<i>Для контролирующих органов, ответственных подраздел. и т.п.</i></a>
		<!--<a href="#" class="btn submt">Электронный референдум<i>Голосования (в разработке)</i></a>-->
		<a href="https://bus.arhcity.ru/" target="_blank" class="btn submt">Автобусы Архангельска<i>отслеживание автобусов online</i></a>
		<a href="https://www.arhcity.ru/?page=2234/2" target="_blank" class="btn submt">Уборочная техника (бета)<i>движение техники online</i></a>
	</div>
</div>

<script>
	if ($(location).attr('href') != "<?=base_url();?>") {
		window.history.pushState("", "Чистый город", "<?=base_url();?>");
	}
</script>