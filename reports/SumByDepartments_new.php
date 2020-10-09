<?php
	include_once($_SERVER["DOCUMENT_ROOT"]."/db/db.php");
	include_once($_SERVER["DOCUMENT_ROOT"]."/functions.php");
	include_once($_SERVER["DOCUMENT_ROOT"]."/include/classes/MSystem.php");
	include_once($_SERVER["DOCUMENT_ROOT"]."/include/classes/User.php");
	
	session_start();
	
	if(!isset($_SESSION['UID'])) exit('У вас недостаточно прав для выполнения данной операции');

	$arUser = Users::GetUserById($_SESSION['UID']);
	if(!Users::UserLawByGroup($arUser[$_SESSION['UID']]['group_id'],'law7')) exit('У вас недостаточно прав для выполнения данной операции');
	
	$arStat = MSystem::GetMessageDepartStatistic();
	$arDepartment = array();
	
	$arDepartment = $arStat['DEPARTMENT'];
	

	
	$filename = 'CleancityDepartmentStat.xls';
	
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
			<h1 style="padding: 15px;">Чистый город - Свод по деятельности администраций территориальных округов</h1>
			<h2 style="padding: 15px;">На <?php echo (date("Y.m.d")) ?></h2>
			<table>
				<thead>
					<tr>
						<th>Округ</th>
						<th width="50px">Всего сообщений</th>
						<th width="200px">Наименование категории</th>
						<th width="50px">Всего по категории</th>
						
						<th width="50px">Новые</th>
						<th width="50px">На исполнении</th>
						<th width="50px">Исполнено</th>
						
						<th width="70px">исп. по категории (%)</th>
						<th width="70px">Общий % исполнения (на текущий момент)</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($arDepartment as $key => $depart): ?>
							<?php
							$category_index = 0;
							$arData = MSystem::GetStatDepartmentCategory($key);
							//echo $key; print_r($arData); echo "<br>";
							
															
									foreach($arData[$key]['CATEGORY'] as $k => $cat):
										$category_index++;
										$percent = 0;
										if(isset($cat['DATA']['STATUS'][2]['COUNT']) && $cat['COUNT']>0)
											$percent = round(($cat['DATA']['STATUS'][2]['COUNT'] / $cat['COUNT']*100),1);
											$percent_1 = round(($arData[$key]['SUCCESS_COUNT'] / $arData[$key]['ALL_COUNT']*100),1);
									?>
									<tr>
									<?php if($category_index==1): ?>
											<td<?php echo (count($arData[$key]['CATEGORY'])>1) ? ' rowspan="'.count($arData[$key]['CATEGORY']).'"':'' ?>>
													<?php echo $depart['NAME'] ?>
											</td>
											<td<?php echo (count($arData[$key]['CATEGORY'])>1) ? ' rowspan="'.count($arData[$key]['CATEGORY']).'"':'' ?>>
													<?php echo $arData[$key]['ALL_COUNT'] ?>
											</td>
									<?php endif ?>
									<td><?php echo $cat['NAME'] ?></td>
									<td><?php echo $cat['COUNT'] ?></td>

									<td><?php echo (isset($cat['DATA']['STATUS'][6]['COUNT'])) ? $cat['DATA']['STATUS'][6]['COUNT']: '0' ?></td>
									<td><?php echo (isset($cat['DATA']['STATUS'][4]['COUNT'])) ? $cat['DATA']['STATUS'][4]['COUNT']: '0' ?></td>
									<td><?php echo (isset($cat['DATA']['STATUS'][2]['COUNT'])) ? $cat['DATA']['STATUS'][2]['COUNT']: '0' ?></td>
									<td><?php echo $percent ?></td>
									<?php if($category_index==1): ?>
											<td<?php echo (count($arData[$key]['CATEGORY'])>1) ? ' rowspan="'.count($arData[$key]['CATEGORY']).'"':'' ?>>
													<?php echo $percent_1; ?>
											</td>
									<?php endif ?>
									</tr>
								<?php endforeach; ?>
						<?php endforeach; ?>
				</tbody>
			</table>
		</body>
	</html>