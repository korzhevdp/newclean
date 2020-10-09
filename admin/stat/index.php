<style>
/* Style the tab */
.tab {
    overflow: hidden;
    border: 1px solid #ccc;
    background-color: #f1f1f1;
}

/* Style the buttons that are used to open the tab content */
.tab button {
    background-color: inherit;
    float: left;
    border: none;
    outline: none;
    cursor: pointer;
    padding: 14px 16px;
    transition: 0.3s;
}

/* Change background color of buttons on hover */
.tab button:hover {
    background-color: #ddd;
}

/* Create an active/current tablink class */
.tab button.active {
    background-color: #ccc;
}

/* Style the tab content */
.tabcontent {
    display: none;
    padding: 6px 12px;
    border: 1px solid #ccc;
    border-top: none;
}

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
<script>
function openCity(evt, cityName) {
    // Declare all variables
    var i, tabcontent, tablinks;

    // Get all elements with class="tabcontent" and hide them
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }

    // Get all elements with class="tablinks" and remove the class "active"
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }

    // Show the current tab, and add an "active" class to the button that opened the tab
    document.getElementById(cityName).style.display = "block";
    evt.currentTarget.className += " active";
}
</script>
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
?>

<!-- Tab links -->
<div class="tab">
  <button class="tablinks" onclick="openCity(event, 'okr')">Администрации территориальных округов</button>
  <button class="tablinks" onclick="openCity(event, 'dep')">Департаменты</button>
</div>

<!-- Tab content -->
<div id="okr" class="tabcontent">
  <h2>Администрации территориальных округов</h2>
<?php include('okr.php'); ?>
</div>

<div id="dep" class="tabcontent">
  <h2>Департаменты</h2>
 <?php include('dep.php'); ?> 
</div>

