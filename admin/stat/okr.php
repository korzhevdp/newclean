
<?php 






	//Статистика за весь период по категориям и статусам
	$district_id = 1;
	for ($district_id = 1; $district_id <= 9 ; $district_id++) {
		
	$result = false;
	
	
        $query2 = "
		
		SELECT
		mescat.id AS idCat,
		mescat.name AS Category,
		COUNT(mescat.name) AS Vsego
		FROM messages as mes

		INNER JOIN message_category AS mescat
		ON mes.category_id = mescat.id
		INNER JOIN message_status AS messtat
		ON mes.status_id = messtat.id
		where district_id = " . $district_id . " and
        mes.status_id in (4,6) and
		mes.removed = 0 and
        mescat.activity = 1
		GROUP BY mescat.name

		";
        if($results = mysqli_query(DataBase::Connect(),$query2))
        {
			$row_cnt = mysqli_num_rows($results);
			if ($row_cnt > 0){
			echo("<h2>" . loadDistrict($district_id) . "<h2>");
			echo "<table>";
			echo  "<tr><td rowspan ='2'><center>Категории</center></td><td colspan='5'><center>Возраст подачи обращения</center></td></tr>";
			echo  "<td><center>В текущий момент <br> на исполнении (в т.ч. новые)</center></td><td><center>более 12 дней</center></td><td><center> от 1 до 3 месяцев</center></td><td><center>от 3 до 6 месяцев</center></td><td><center>более 6 месяцев</center></td>";
			
			$asdint12 = 0;
			$asdint1month = 0;
			$asdint3month = 0;
			$asdint6month = 0;
			
			$i_nevypolneno = 0;
			$i_asdint12 = 0;
			$i_asdint1month = 0;
			$i_asdint3month = 0;
			$i_asdint6month = 0;
		    while ($row = mysqli_fetch_assoc($results)) {
				
						$idCat = $row['idCat'];
						$novye = loadData2($district_id,$idCat,6);
						$vRabote = loadData2($district_id,$idCat,4);
					$nevypolneno = $novye + $vRabote;
					
					
					$dat = date("Y-m");  
					
					$asdint12 =  loadOfData2($district_id,$idCat,4,'INTERVAL 13 DAY') +  loadOfData2($district_id,$idCat,6,'INTERVAL 13 DAY');
					$asdint1month =  loadOfData2($district_id,$idCat,4,'INTERVAL 1 MONTH') +  loadOfData2($district_id,$idCat,6,'INTERVAL 1 MONTH');
					$asdint3month =  loadOfData2($district_id,$idCat,4,'INTERVAL 3 MONTH') +  loadOfData2($district_id,$idCat,6,'INTERVAL 3 MONTH');
					$asdint6month =  loadOfData2($district_id,$idCat,4,'INTERVAL 6 MONTH') +  loadOfData2($district_id,$idCat,6,'INTERVAL 6 MONTH');
					
					
					
					echo "<tr>";
						$asdint3month = $asdint3month - $asdint6month;
						$asdint1month = $asdint1month - $asdint3month - $asdint6month;
						$asdint12 = $asdint12 - $asdint1month - $asdint3month - $asdint6month;;
						
						echo  "<th>" . $row['Category'] . "</th><th>" . $nevypolneno ."</th><th>" . $asdint12 . "</th><th>" . $asdint1month . "</th><th>" . $asdint3month  . "</th><th>" . $asdint6month . "</th>";
						$i_nevypolneno = $i_nevypolneno + $nevypolneno;
						$i_asdint12 = $i_asdint12 + $asdint12;
						$i_asdint1month = $i_asdint1month + $asdint1month;
						$i_asdint3month = $i_asdint3month + $asdint3month;
						$i_asdint6month = $i_asdint6month + $asdint6month;
					echo "</tr>";
					
				
			}
			
			
			
			echo  "<th>Всего</th><th>" . $i_nevypolneno . "</th><th>" . $i_asdint12 . "</th><th>" . $i_asdint1month . "</th><th>" . $i_asdint3month . "</th><th>" . $i_asdint6month . "</th>";
        	echo "</table>";
		}
		}
		
	}
	
	
		function loadData2($district_id,$idCat,$idStat){
			$query2 = "
			SELECT
			mescat.id AS idCat,
			COUNT(mescat.name) AS Vsego
			FROM messages as mes

			INNER JOIN message_category AS mescat
			ON mes.category_id = mescat.id
			INNER JOIN message_status AS messtat
			ON mes.status_id = messtat.id
			
			WHERE status_id in (" . $idStat . ") AND
			district_id = " . $district_id . " AND
			category_id in (" . $idCat . ") and
               mescat.activity = 1 and
			   mes.archive != 1 and
			   mes.removed != 1";
			
			if($results = mysqli_query(DataBase::Connect(),$query2))
			{
				$row = mysqli_fetch_assoc($results); 
					return $row['Vsego'];
			}	
		}

		
		function loadOfData2($district_id,$idCat,$idStat,$dat){
			$query2 = "
			SELECT
		mescat.id AS idCat,
		mescat.name AS Category,
		COUNT(mescat.name) AS Vsego
		FROM messages as mes

		INNER JOIN message_category AS mescat
		ON mes.category_id = mescat.id
		INNER JOIN message_status AS messtat
		ON mes.status_id = messtat.id
		where 
		status_id in (" . $idStat . ") AND
		district_id = " . $district_id . " and
		 category_id in (" . $idCat . ") and
                mes.create_time < LAST_DAY(CURDATE() - " . $dat . ") and
               mescat.activity = 1 and
			   mes.archive != 1 and
			   mes.removed != 1
		GROUP BY mescat.name";
			
			if($results = mysqli_query(DataBase::Connect(),$query2))
			{
				$row = mysqli_fetch_assoc($results); 
					return $row['Vsego'];
			}	
		}	

		function loadDistrict($district_id){
			$query2 = "
			select id, name
			from city_districts
			where id = " . $district_id;
			
			if($results = mysqli_query(DataBase::Connect(),$query2))
			{
				$row = mysqli_fetch_assoc($results); 
					return $row['name'];
			}	
		}					
		
											
				
				
 ?>
 
 
		<link rel="stylesheet" href="morris.css" />
		
		
		<script src="jquery.js"></script>
		<script src="raphael.js"></script>
		<script src="morris.js"></script>
		<script src="flot/jquery.flot.js"></script>
		<script src="flot-tooltip/jquery.flot.tooltip.js"></script>
		<script src="flot/jquery.flot.pie.js"></script>
		<script src="flot/jquery.flot.categories.js"></script>
		<script src="flot/jquery.flot.resize.js"></script>
		<!--<script src="charts.js"></script>-->
		

		