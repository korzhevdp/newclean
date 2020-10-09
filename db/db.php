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