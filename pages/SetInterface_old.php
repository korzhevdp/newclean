<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы для Вас недоступен.'); ?>

<?php require_once('pages/header.php'); ?>

<div class="container first-page">
	<div class="gray-cont">
		<a href="#" class="btn icon-street-view submt" id="to_auth">Вход для граждан<i>Вы сможете сообщить о проблеме</i></a>
		<a href="/admin/" class="btn icon-lock submt only-for-big-screen">В раздел администрирования<i>Для контролирующих органов, ответственных подраздел. и т.п.</i></a>
		<a href="#" class="btn submt">Электронный референдум<i>Голосования (в разработке)</i></a>
	</div>
		<?php
			$allUserCount = 0;
			$all = 0;
			$status1 = 0;
			$status2 = 0;
	
			$arStat = MSystem::GetMessageCountByStatus();
			if(isset($arStat['ALL_COUNT']))
			{
				$all = $arStat['ALL_COUNT'];
			}
			if(isset($arStat['4']))
			{
				$status1 = $arStat['4'];
			}
			if(isset($arStat['2']))
			{
				$status2 = $arStat['2'];
			}
		

			$arUsersStat = MSystem::GetUsersStat();

			
			$allUserCount = count($arUsersStat['LIST']);

			?>
			
			<div class="stat-items">
				<p class="center">Общая статистика <br>(только активные категории)</p>
				<div class="stat-item-1">
					<i><?php echo $all; ?></i>
					<b>Всего сообщений</b>
				</div>
				<div class="stat-item-2">
					<i><?php echo $status1; ?></i>
					<b>В работе</b>
				</div>
				<div class="stat-item-3">
					<i><?php echo $status2; ?></i>
					<b>Проблем устранено</b>
				</div>
			</div>
				<?php
					//if($_SERVER['REMOTE_ADDR']=='192.168.51.144' || $_SERVER['REMOTE_ADDR']=='188.170.83.77')
					//{
					?>
						<div class="stat-items">
							<div class="separ"></div>
							<p class="center">Всего пользователей</p>
							<div class="stat-one-item users">
								<i><?php echo $allUserCount; ?></i>
							</div>
						</div>
					<?php
					//}
					?>
			
			<?php// if($_SERVER['REMOTE_ADDR']=='192.168.51.144' || $_SERVER['REMOTE_ADDR']=='188.170.83.77'): ?>
					<div class="main-policy-info">
						<a href="/policy/" target="_blank">Политика конфиденциальности</a>
					</div>
			<?php// endif; ?>
		
</div>

<script type="text/javascript">

	document.addEventListener('DOMContentLoaded', function() {
		userAuthByKey();
	}, false);

</script>