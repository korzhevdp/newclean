<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы для Вас недоступен.'); ?>
<div class="pd-panel-cont active">
<?php

if(!isset($law6))
{
	exit('<div class="pg10px">Общая статистика сообщений для вас недоступна. Обратитесь к администратору системы.</div>');
}

$arStat = array();
$arDistr = array();
$arStatus = array();
$arCat = array();

$arStat = MSystem::GetMessageStatistic();
$arUsersStat = MSystem::GetUsersStat();
$arDistr = $arStat['DISTRICTS'];
$arStatus = $arStat['STATUSES'];
$arCat = $arStat['CATEGORIES'];
$arDepartment = $arStat['DEPARTMENT'];

$messCount =  0;
$toArchive = 0;
$ActiveMessage = 0;
$allUserCount = 0;

if(isset($arStat['MESSAGES']))
{
		$messCount =  count($arStat['MESSAGES']);
		foreach($arStat['MESSAGES'] as $key => $message)
		{
			if($message['ARCHIVE']==1)
				$toArchive++;
		}
}
$ActiveMessage = $messCount - $toArchive;
$allUserCount = count($arUsersStat['LIST']);


echo "<div class='line-item'>Всего сообщений <b>".$messCount."</b></div>";
echo "<div class='line-item'>из них в архиве <b>".$toArchive."</b></div>";
echo "<div class='line-item'>из них в работе <b>".$ActiveMessage."</b></div>";
echo "<div class='separ'></div>";
echo "<div class='line-item'>Пользователей зарегистрировано <b>".$allUserCount."</b></div>";

echo "<div class='line-caption'>Отчетные формы <b></b><i>Сохранение и вывод на печать отчетов</i></div>";
echo "<a href='/reports/SumByTerritorialCounties.php' class='report-link line-item lnk'>Общий свод по территориальным округам (все категории сообщений) (Excel)<i class='icon-file-text-o'></i></a>";
echo "<a href='/reports/SumByDepartments.php' class='report-link line-item lnk'>Свод по деятельности администраций территориальных округов (учитываются только те проблемы, которые контролирует администрация округа) (Excel)<i class='icon-file-text-o'></i></a>";
//echo "<a class='line-item lnk'>Свод по категориям сообщений<i class='icon-file-text-o'></i></a>";

echo "<div class='line-caption'>Сообщений по категориям<i>Представлены только активные категории</i></div>";
$index = 0;
foreach($arCat as $key => $cat)
{
	$index++;
	echo "<div class='line-item'>".$cat['NAME']."<b>".$cat['COUNT']."</b></div>";
	if($index!=count($arCat))
	{
		echo "<div class='separ'></div>";
	}
}



echo "<div class='line-caption'>Показатели эффективности работы Администраций территориальных округов<i>Показатели по сообщениям, находящимся в ответственности администраций округов</i></div>";
$arDepartStat = array();
foreach($arDepartment as $key => $depart)
{
	$arData = MSystem::GetStatDepartmentByStatus($key);
	echo "<a class='line-item lnk sl'>".$depart['NAME']."<i class='icon-right-open-big'></i><b>".$arData[$key]['ALL_COUNT']."</b>";
	echo "<div class='stat-detail'>";
	foreach($arData[$key]['STATUS'] as $k => $data)
	{
		$percent = round(($data['COUNT'] / $arData[$key]['ALL_COUNT']*100),1);
		if($k==6)
		{
			$caption = 'Новые, нерассмотренные';
			echo "<div>".$caption."&nbsp;".$percent."% (".$data['COUNT']." сообщ.)</div>";
		} 
		if($k==2)
		{
			$caption = 'Проблем устранено';
			echo "<div>".$caption."&nbsp;".$percent."% (".$data['COUNT']." сообщ.)</div>";
		}
		if($k==4)
		{
			$caption = 'В работе';
			echo "<div>".$caption."&nbsp;".$percent."% (".$data['COUNT']." сообщ.)</div>";
		}
		if($k==5)
		{
			$caption = 'Отказано в рассмотрении';
			echo "<div>".$caption."&nbsp;".$percent."% (".$data['COUNT']." сообщ.)</div>";
		}
	}
	
	echo "</div>";
	echo "</a>";
}



echo "<div class='line-caption'>Общая статистика по районам города<i>Показатели отражают полную статистику обработки сообщений по территориальной принадлежности (всех исполнители)</i></div>";
$index = 0;
foreach($arDistr as $key => $distr)
{
	$index++;
	echo "<a class='line-item lnk sl'>".$distr['NAME']."<i class='icon-right-open-big'></i><b>".$distr['COUNT']."</b>";
	echo "<div class='stat-detail'>";
	$arDistrData = array();
	$arDistrData = MSystem::GetStatByDistrictId($key);
	foreach($arDistrData as $data)
	{
		$percent = round(($data['COUNT'] / $distr['COUNT']*100),1);
		if($data['STATUS_ID']==6)
		{
			$caption = 'Новые, нерассмотренные';
			echo "<div>".$caption."&nbsp;".$percent."% (".$data['COUNT']." сообщ.)</div>";
		} 
		if($data['STATUS_ID']==2)
		{
			$caption = 'Проблем устранено';
			echo "<div>".$caption."&nbsp;".$percent."% (".$data['COUNT']." сообщ.)</div>";
		}
		if($data['STATUS_ID']==4)
		{
			$caption = 'В работе';
			echo "<div>".$caption."&nbsp;".$percent."% (".$data['COUNT']." сообщ.)</div>";
		}
		if($data['STATUS_ID']==5)
		{
			$caption = 'Отказано в рассмотрении';
			echo "<div>".$caption."&nbsp;".$percent."% (".$data['COUNT']." сообщ.)</div>";
		}
	}
	echo "</div>";
	echo "</a>";
	if($index!=count($arDistr))
	{
		echo "<div class='separ'></div>";
	}
}

echo "<div class='line-caption'>Общая статистика по статусам<i>Представлены все статусы, включая неактивные</i></div>";
$index = 0;
foreach($arStatus as $key => $status)
{
	$index++;
	echo "<div class='line-item'>".$status['NAME']."<b>".$status['COUNT']."</b></div>";
	if($index!=count($arStatus))
		echo "<div class='separ'></div>";
}


?>
</div>