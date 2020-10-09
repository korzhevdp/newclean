<style type="text/css">
   TABLE {
    
    border-collapse: collapse; /* Убираем двойные линии между ячейками */
	    margin: auto;
   }
   TD, TH {
    padding: 3px; /* Поля вокруг содержимого таблицы */
    border: 1px solid black; /* Параметры рамки */
   }
   TD {
    background: #b0e0e6; /* Цвет фона */
   }
    html {
    text-align:center;
	    margin: auto;
   }
  </style>
<?php 



class DataBase
{
    public static $mConnect;
    public static $mSelectDB;	

    public static function Connect($host = 'localhost', $user='economic', $pass=';flbyf', $name = 'cleancity')
	{
		self::$mConnect = mysqli_connect($host, $user, $pass, $name);
		
		if(!self::$mConnect)
		{
			echo "<p><b>К сожалению, не удалось подключиться к базе данных</b></p>";
			exit();
			return false;
		}
		
        //mysql_query ("set_client='utf8'");
        mysqli_query (self::$mConnect,"set character_set_results='utf8'");
        mysqli_query (self::$mConnect,"set collation_connection='utf8_general_ci'");
        mysqli_query (self::$mConnect,"SET NAMES utf8");
        
		return self::$mConnect;
	}

	public static function Close()
	{
		return mysqli_close(self::$mConnect);
	}
}


	//Статистика за весь период по категориям и статусам
	echo("<h2>Статистика по просроченным обращениям</h2>");
	$district_id = 1;
	for ($district_id = 1; $district_id <= 9 ; $district_id++) {
		
	$result = false;
	
	
        $query = "
		
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
               mescat.activity = 1
		GROUP BY mescat.name

		";
        if($results = mysqli_query(DataBase::Connect(),$query))
        {
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
						$novye = loadData($district_id,$idCat,6);
						$vRabote = loadData($district_id,$idCat,4);
					$nevypolneno = $novye + $vRabote;
					
					
					$dat = date("Y-m");  
					
					$asdint12 =  loadOfData($district_id,$idCat,4,'INTERVAL 13 DAY') +  loadOfData($district_id,$idCat,6,'INTERVAL 13 DAY');
					$asdint1month =  loadOfData($district_id,$idCat,4,'INTERVAL 1 MONTH') +  loadOfData($district_id,$idCat,6,'INTERVAL 1 MONTH');
					$asdint3month =  loadOfData($district_id,$idCat,4,'INTERVAL 3 MONTH') +  loadOfData($district_id,$idCat,6,'INTERVAL 3 MONTH');
					$asdint6month =  loadOfData($district_id,$idCat,4,'INTERVAL 6 MONTH') +  loadOfData($district_id,$idCat,6,'INTERVAL 6 MONTH');
					
					
					if ( $nevypolneno != 0  ) {
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
				
			}
			
			
			
			echo  "<th>Всего</th><th>" . $i_nevypolneno . "</th><th>" . $i_asdint12 . "</th><th>" . $i_asdint1month . "</th><th>" . $i_asdint3month . "</th><th>" . $i_asdint6month . "</th>";
        	echo "</table>";
		}
		
	}
	
	
		function loadData($district_id,$idCat,$idStat){
			$query = "
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
			
			if($results = mysqli_query(DataBase::Connect(),$query))
			{
				$row = mysqli_fetch_assoc($results); 
					return $row['Vsego'];
			}	
		}

		
		function loadOfData($district_id,$idCat,$idStat,$dat){
			$query = "
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
			
			if($results = mysqli_query(DataBase::Connect(),$query))
			{
				$row = mysqli_fetch_assoc($results); 
					return $row['Vsego'];
			}	
		}	

		function loadDistrict($district_id){
			$query = "
			select id, name
			from city_districts
			where id = " . $district_id;
			
			if($results = mysqli_query(DataBase::Connect(),$query))
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
		

		