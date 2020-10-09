<?php
	include_once($_SERVER["DOCUMENT_ROOT"]."/db/db.php");
	include_once($_SERVER["DOCUMENT_ROOT"]."/functions.php");
	include_once($_SERVER["DOCUMENT_ROOT"]."/include/classes/MSystem.php");
	include_once($_SERVER["DOCUMENT_ROOT"]."/include/classes/User.php");
	
	session_start();
	
	if(!isset($_SESSION['UID'])) exit('У вас недостаточно прав для выполнения данной операции');

	$arUser = Users::GetUserById($_SESSION['UID']);
	if(!Users::UserLawByGroup($arUser[$_SESSION['UID']]['group_id'],'law7')) exit('У вас недостаточно прав для выполнения данной операции');
	
	$arMessagesStat = MSystem::GetMessageStatistic();
	//print_r($arMessagesStat);
	$arFinalStat = array();
	$arFinalStat['CATEGORIES'] = array();
	$arFinalStat['DISTRICTS'] = array();
	$arFinalStat['STATUSES'] = array();
	$arCategorySet = array(12);
	foreach($arMessagesStat['MESSAGES'] as $key => $message)
	{
		
		$arStatusInfo = array();

		if($message['DISTRICT_ID']!=0)
		{
			if(!isset($arFinalStat['DISTRICTS'][$message['DISTRICT_ID']]['MESSAGE_COUNT'])) $arFinalStat['DISTRICTS'][$message['DISTRICT_ID']]['MESSAGE_COUNT'] = 0;
			if(!isset($arFinalStat['DISTRICTS'][$message['DISTRICT_ID']]['CATEGORIES'][$message['CATEGORY_ID']]['MESSAGE_COUNT'])) $arFinalStat['DISTRICTS'][$message['DISTRICT_ID']]['CATEGORIES'][$message['CATEGORY_ID']]['MESSAGE_COUNT'] = 0;
			if(!isset($arFinalStat['DISTRICTS'][$message['DISTRICT_ID']]['OK_MESSAGE_COUNT'])) $arFinalStat['DISTRICTS'][$message['DISTRICT_ID']]['OK_MESSAGE_COUNT'] = 0;
			if(!isset($arFinalStat['DISTRICTS'][$message['DISTRICT_ID']]['OK_MESSAGE_SETTIME_COUNT'])) $arFinalStat['DISTRICTS'][$message['DISTRICT_ID']]['OK_MESSAGE_SETTIME_COUNT'] = 0;
			if(!isset($arFinalStat['DISTRICTS'][$message['DISTRICT_ID']]['OK_MESSAGE_TIMELY_COUNT'])) $arFinalStat['DISTRICTS'][$message['DISTRICT_ID']]['OK_MESSAGE_TIMELY_COUNT'] = 0;
			
			if(!isset($arFinalStat['DISTRICTS'][$message['DISTRICT_ID']]['CATEGORIES'][$message['CATEGORY_ID']]['STATUS'][$message['STATUS_ID']]['MESSAGE_COUNT'])) $arFinalStat['DISTRICTS'][$message['DISTRICT_ID']]['CATEGORIES'][$message['CATEGORY_ID']]['STATUS'][$message['STATUS_ID']]['MESSAGE_COUNT'] = 0;
		
			$arFinalStat['DISTRICTS'][$message['DISTRICT_ID']]['NAME'] = $message['DISTRICT_NAME'];
			$arFinalStat['DISTRICTS'][$message['DISTRICT_ID']]['MESSAGE_COUNT'] = $arFinalStat['DISTRICTS'][$message['DISTRICT_ID']]['MESSAGE_COUNT']+1;
			if($message['STATUS_ID']==2)
			{
				$arFinalStat['DISTRICTS'][$message['DISTRICT_ID']]['OK_MESSAGE_COUNT'] = $arFinalStat['DISTRICTS'][$message['DISTRICT_ID']]['OK_MESSAGE_COUNT']+1;
				$arStatusInfo = MSystem::GetMessageSuccsessInfo($key);
				
				if(count($arStatusInfo)>1)
				{
					$arFinalStat['DISTRICTS'][$message['DISTRICT_ID']]['OK_MESSAGE_SETTIME_COUNT'] = $arFinalStat['DISTRICTS'][$message['DISTRICT_ID']]['OK_MESSAGE_SETTIME_COUNT']+1;
					$time_limin = new DateTime($arStatusInfo['create_time']);
					$time_limin->add(new DateInterval('P15D'));
					$finish_time = new DateTime($arStatusInfo['TIME']);
					
					if($time_limin > $finish_time)
					{
						$arFinalStat['DISTRICTS'][$message['DISTRICT_ID']]['OK_MESSAGE_TIMELY_COUNT'] = $arFinalStat['DISTRICTS'][$message['DISTRICT_ID']]['OK_MESSAGE_TIMELY_COUNT']+1;
					}
				}
			}
			
			$arFinalStat['DISTRICTS'][$message['DISTRICT_ID']]['CATEGORIES'][$message['CATEGORY_ID']]['MESSAGE_COUNT'] = $arFinalStat['DISTRICTS'][$message['DISTRICT_ID']]['CATEGORIES'][$message['CATEGORY_ID']]['MESSAGE_COUNT']+1;
			$arFinalStat['DISTRICTS'][$message['DISTRICT_ID']]['CATEGORIES'][$message['CATEGORY_ID']]['NAME'] = $message['CATEGORY_NAME'];
			$arFinalStat['DISTRICTS'][$message['DISTRICT_ID']]['CATEGORIES'][$message['CATEGORY_ID']]['STATUS'][$message['STATUS_ID']]['NAME'] = $message['STATUS_NAME'];
			$arFinalStat['DISTRICTS'][$message['DISTRICT_ID']]['CATEGORIES'][$message['CATEGORY_ID']]['STATUS'][$message['STATUS_ID']]['MESSAGE_COUNT'] = $arFinalStat['DISTRICTS'][$message['DISTRICT_ID']]['CATEGORIES'][$message['CATEGORY_ID']]['STATUS'][$message['STATUS_ID']]['MESSAGE_COUNT']+1;
		}
	}
	
	
	$filename = 'CleancityDistrictStat.xls';
	
	header('Content-Type: text/html; charset=windows-1251');
	header('Content-type: application/excel');
	header('Content-Disposition: attachment; filename='.$filename);
	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Cache-Control: post-check=0, pre-check=0', FALSE);
	header('Pragma: no-cache');
	header('Content-transfer-encoding: binary');
	
	?>
	<html xmlns:x="urn:schemas-microsoft-com:office:excel">
		<head>
			<style>
				th,td {
					border: 1px solid #d4d4d4;
					padding: 10px;
					vertical-align: middle;
					font-size: 16px;
				}
				
				table {
					border-spacing: 0;
				}
				
				thead {
					background: #e0e0e0;
				}
			</style>
		</head>
		<body>
			<h1 style="padding: 15px;">Чистый город - Свод по округам</h1>
			<h2 style="padding: 15px;">На <?php echo (date("Y.m.d")) ?></h2>
			<table>
				<thead>
					<tr>
						<th>Округ</th>
						<th width="50px">Всего сообщений</th>
						<th width="200px">Наименование категории</th>
						<th width="50px">Всего по категории</th>
						<th width="50px">Новые</th>
						<th width="50px">Отказано в рассмотрении</th>
						<th width="50px">На исполнении</th>
						<th width="50px">Исполнено</th>
						<th width="70px">исп. по категории (%)</th>
						<th width="70px">Общий % исполнения</th>
						<th width="70px">из них вовремя исполнено (%)</th>
					</tr>
				</thead>
				<tbody>
				<?php foreach($arFinalStat['DISTRICTS'] as $key => $district): $category_index = 0; ?>
							<?php foreach($district['CATEGORIES'] as $key1 => $category): $category_index++; ?>
								<tr>
									<?php if($category_index==1): ?>
											<td<?php echo (count($district['CATEGORIES'])>1) ? ' rowspan="'.count($district['CATEGORIES']).'"':'' ?>>
													<?php echo $district['NAME'] ?>
											</td>
											<td<?php echo (count($district['CATEGORIES'])>1) ? ' rowspan="'.count($district['CATEGORIES']).'"':'' ?>>
													<?php echo $district['MESSAGE_COUNT'] ?>
											</td>
									<?php endif ?>
									<td><?php echo $category['NAME'] ?></td>
									<td><?php echo $category['MESSAGE_COUNT'] ?></td>
									<td><?php echo (isset($category['STATUS'][6])) ? $category['STATUS'][6]['MESSAGE_COUNT']:'0' ?></td>
									<td><?php echo (isset($category['STATUS'][5])) ? $category['STATUS'][5]['MESSAGE_COUNT']:'0' ?></td>
									<td><?php echo (isset($category['STATUS'][4])) ? $category['STATUS'][4]['MESSAGE_COUNT']:'0' ?></td>
									<td><?php echo (isset($category['STATUS'][2])) ? $category['STATUS'][2]['MESSAGE_COUNT']:'0' ?></td>
									<td><?php echo (isset($category['STATUS'][2]['MESSAGE_COUNT']) && $category['STATUS'][2]['MESSAGE_COUNT']>0) ? intval($category['STATUS'][2]['MESSAGE_COUNT']/$category['MESSAGE_COUNT']*100).' %': '0' ?></td>
									<?php if($category_index==1): ?>
											<td<?php echo (count($district['CATEGORIES'])>1) ? ' rowspan="'.count($district['CATEGORIES']).'"':'' ?>>
												<?php echo intval($district['OK_MESSAGE_COUNT'] / $district['MESSAGE_COUNT'] * 100).' %' ?>
											</td>
											<td<?php echo (count($district['CATEGORIES'])>1) ? ' rowspan="'.count($district['CATEGORIES']).'"':'' ?>>
												<?php if($district['OK_MESSAGE_SETTIME_COUNT']>0): ?>
													<?php echo intval($district['OK_MESSAGE_TIMELY_COUNT'] / $district['OK_MESSAGE_SETTIME_COUNT'] * 100).' %' ?>
												<?php else: ?>
														0 %
												<?php endif;  ?>
											</td>
									<?php endif ?>
								</tr>
							<?php endforeach ?>
								
				<?php endforeach ?>
				</tbody>
			</table>
		</body>
	</html>