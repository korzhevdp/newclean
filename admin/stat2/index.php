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
	echo("<h2>Статистика по заявкам с момента внедрения системы</h2>");
	
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

		GROUP BY mescat.name

		";
        if($results = mysqli_query(DataBase::Connect(),$query))
        {
			echo "<table>";
			echo  "<td>Категория</td><td>Всего поступило</td><td>Обработано</td><td>В работе</td><td>Отказано в рассмотрении</td><td>Новое</td><td>Не по тематике</td>";
			$i_novye = 0;
			$i_vsego = 0;
			$i_obrabotano = 0;
			$i_vRabote = 0;
			$i_otkazanoVRassm = 0;
			$i_nePoTematike = 0;
		    while ($row = mysqli_fetch_assoc($results)) {
				echo "<tr>";
					$novye = loadData($row['idCat'],6);
					$obrabotano = loadData($row['idCat'],2);
					$vRabote = loadData($row['idCat'],4);
					$otkazanoVRassm = loadData($row['idCat'],5);
					$nePoTematike = loadData($row['idCat'],8);
					
					echo  "<th>" . $row['Category'] . "</th><th>" . $row['Vsego'] ."</th><th>" . $obrabotano ."</th><th>" . $vRabote ."</th><th>" . $otkazanoVRassm ."</th><th>" . $novye ."</th><th>" . $nePoTematike ."</th>";
					$i_novye = $i_novye + $novye;
					$i_vsego = $i_vsego + $row['Vsego'];
					$i_obrabotano = $i_obrabotano + $obrabotano; 
					$i_vRabote = $i_vRabote + $vRabote;
					$i_otkazanoVRassm = $i_otkazanoVRassm + $otkazanoVRassm;
					$i_nePoTematike = $i_nePoTematike + $nePoTematike;
					
				echo "</tr>";
			}
			echo  "<th>Всего</th><th>" . $i_vsego ."</th><th>" . $i_obrabotano ."</th><th>" . $i_vRabote ."</th><th>" . $i_otkazanoVRassm ."</th><th>" . $i_novye ."</th><th>" . $i_nePoTematike ."</th>";
        	echo "</table>";
		}
		function loadData($idCat,$idStat){
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
			category_id in (" . $idCat . ")
			";
			if($results = mysqli_query(DataBase::Connect(),$query))
			{
				$row = mysqli_fetch_assoc($results); 
					return $row['Vsego'];
			}	
		}

		
		
		// Отношение новых пользователей и поступивших сообщений
		echo "<h2>Активность появления новых сообщений по месяцам в соотношении с кол-вом зарегистрированных пользователей</h2>";
		echo("
		
		<h4 style='margin-left:calc((100% - 600px)/2)'>
		<div style='float: left; width: 50px;height: 20px;background: #0088cc;'></div> <span style='float: left; margin-left:15px;'> Зарегистрировано пользователей </span>
		<div style='margin-left:15px; float: left; width: 50px;height: 20px;background: #734ba9;'></div> <span style='float: left; margin-left:15px;'> Добавлено сообщений </span>
		</h4><br>
		
		");
		$query = "SELECT COUNT( id ) as count, DATE_FORMAT( create_time,  '%Y-%m' ) as date FROM messages WHERE removed = '0' GROUP BY DATE_FORMAT( create_time,  '%Y-%m' )";
				if($results = mysqli_query(DataBase::Connect(),$query))
				{
					while($row = mysqli_fetch_assoc($results))
					{
						$result[$row['date']]['messages'] = $row['count'];
					}
				}
				
				$query = "SELECT COUNT( id ) as count, DATE_FORMAT( reg_date,  '%Y-%m' ) as date FROM users WHERE reg_date > '0000-00-00' GROUP BY DATE_FORMAT( reg_date,  '%Y-%m' )";
				if($results = mysqli_query(DataBase::Connect(),$query))
				{
					while($row = mysqli_fetch_assoc($results))
					{
						$result[$row['date']]['users'] = $row['count'];
					}
				}
				$arStat = $result;
				$strDat = "";
				echo("
				<div class='chart chart-md' id='morrisLine'></div>
										<script type='text/javascript'>
						
											var morrisLineData = [
				");
				
				$i = 0;
				foreach($arStat as $key => $value){
					
					$strDat = $strDat . "{";
					$strDat = $strDat . "y: '" . $key . "',";
					$strDat = $strDat . "a: " . $value['messages'] . ",";
					$strDat = $strDat . "b: " . $value['users'] . ",";
					$i++;
					if ($i == count($arStat)){
						$strDat = $strDat . "} ";
					}else{
						$strDat = $strDat . "}, ";
					}
				}
				echo $strDat . "];</script>";
				
		
		//Статистика по пользователям
		
		$query = "
			SELECT
			count(id)
			FROM users
			";
			if($results = mysqli_query(DataBase::Connect(),$query))
			{
				$row = mysqli_fetch_assoc($results); 
					echo("<h2>Статистика по пользователям</h2> <h4>(Всего - " . $row['count(id)'] . ")</h4>");
			}		
		//Годовой прирост пользователей
		$query = "
			SELECT
			YEAR(reg_date) AS Y,
			count(reg_date) AS Vsego
			FROM users 
			where reg_date > '0000-00-00'
			group by Y
			order by reg_date
			";
			if($results = mysqli_query(DataBase::Connect(),$query))   
			{
				$strUs = "";
				$i = 0; $ii = 0;
				$arStat = $results;
				echo("	
					<center><div class='chart chart-md' id='flotPie' style='width:350px; height:350px' ></div></center>
					<script type='text/javascript'>
						var flotPieData = ["
					);
								
				foreach($arStat as $key => $value){$ii++;}
				foreach($arStat as $key => $value){
					$i++;
					if ($i == 1){ $color = '#0088cc';}
					if ($i == 2){ $color = '#2baab1';}
					if ($i == 3){ $color = '#734ba9';}
					if ($i == 4){ $color = '#E36159';}
					$strUs = $strUs . "{label:'" . $value['Y'] . ": " . $value['Vsego'] . " пользователей" ."', data:[" . "[1," . $value['Vsego']."]" . "],color: '" . $color . "'";
					if ($i == $ii){
						$strUs = $strUs . "} ";
					}else{
						$strUs = $strUs . "}, ";
					}
				}
				
				echo($strUs . "];</script>");
				
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
		<script src="charts.js"></script>
		

		