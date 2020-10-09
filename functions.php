<?php 

function CheckUserAuth()
{
    $result = false;
    if(isset($_SESSION['SSUID']) && isset($_COOKIE['SSUID']))
	{
        if(($_COOKIE['SSUID']!=null) && ($_SESSION['SSUID'] == $_COOKIE['SSUID']))
            $result = true;
    }
	
    return $result;
}


function SymbolSecur($str) // функция для проверки наличия недопустимых символов
{
  $result = true;
    if (preg_match( "/[\<|\>]/", $str))
        $result = false;
		
  return $result;
}


function CheckEmail($email) // проверка валидности email
{  
    $result = false;
    if((preg_match("~^([a-z0-9_\-\.])+@([a-z0-9_\-\.])+\.([a-z0-9])+$~i", $email) !== 0) && (strlen($email) >= 6))
		$result = true;
		
    return $result;
}


function CharacterFilter($str) 
{
    $res = htmlspecialchars($str);
	$res = mysqli_real_escape_string(DataBase::Connect(),$res);
    return $res;
}




function AreaPoint($x,$y,$d_point){
	$d_point = $d_point[0];
    $count = count($d_point);
    $j = $count - 1;
    $c = 0;
    for ($i = 0; $i < $count;$i++)
    {
        if(((($d_point[$i][1]<=$y) && ($y<$d_point[$j][1])) || (($d_point[$j][1]<=$y) && ($y<$d_point[$i][1]))) &&
        ($x > ($d_point[$j][0] - $d_point[$i][0]) * ($y - $d_point[$i][1]) / ($d_point[$j][1] - $d_point[$i][1]) + $d_point[$i][0]))
        {
            $c = !$c;
        }
        $j = $i;
    }
    return $c;
}



function resize($file_path, $type = 2, $rotate = null, $quality = null) // ресайз картинок без значительной потери качества
{
    $result = false;
	$max_thumb_size = 200;
	$max_size = 600;
	$tmp_path = 'uploads/tmp/';
	$public_path = 'uploads/img/';
	$path = $public_path;
	

	$file_name = uniqid().'.jpg';

	if($quality == null)
		$quality = 75;

	if($source = imagecreatefromjpeg($file_path))
	{
		if($rotate != null)
			$src = imagerotate($source, $rotate, 0);
		else
			$src = $source;
	
		$w_src = imagesx($src); 
		$h_src = imagesy($src);
	
		if($type == 1)
			$w = $max_thumb_size;
		elseif ($type == 2)
			$w = $max_size;
	
		if($w_src > $w)
		{
			$ratio = $w_src/$w;
			$w_dest = round($w_src/$ratio);
			$h_dest = round($h_src/$ratio);
			$dest = imagecreatetruecolor($w_dest, $h_dest);
			imagecopyresampled($dest, $src, 0, 0, 0, 0, $w_dest, $h_dest, $w_src, $h_src);
			imagejpeg($dest, $tmp_path.$file_name, $quality);
			imagedestroy($dest);
			imagedestroy($src);
			$result = $public_path.$file_name;
		}
		else
		{
			imagejpeg($src, $tmp_path.$file_name, $quality);
			imagedestroy($src);
			$result = $public_path.$file_name;
		}
	
		if($result!==false)
		{
			if(@copy($tmp_path.$file_name, $path.$file_name))
			{
				$result = $public_path.$file_name;
				unlink($tmp_path.$file_name);
				unlink($file_path);
			}
		}
	}
	
	return $result;
}


function SaveFiles($files) { // сохранение загружаемых изображений
    $result['status'] = false;
    $result['content'] = '';
	$result['message'] = 'При обработке изображений произошла ошибка';
    $content = array();
    
    //file_put_contents($_SERVER["DOCUMENT_ROOT"]."/log.txt", var_export($files, true), FILE_APPEND);
    foreach($files as $key=>$file)
	{
        if($file_path = SaveOneFile($file['value']))
            if($file_path /*= resize($file_path, 2, null, null)*/)
			{
                $content[] = $file_path;
			}
            else
			{
				$content = false; //Ошибка при загрузке файла
				break;
			}
    }
    
    $result['content'] = $content;
    if($content!=false)
	{
		if(count($content)>0)
		{
			$result['status'] = true;
			$result['message'] = '';
		}
		else
		{
			$result['message'] = 'Ни одно изображение не было загружено.';
		}
		
	}
		
	
	
    return $result;
}

function SaveOneFile($base64_file) {
    $result = false;
	
    $path_part = 'uploads/img/';
	$img = str_replace('data:image/png;base64,', '', $base64_file);
	$img = str_replace('data:image/gif;base64,', '', $img);
	$img = str_replace('data:image/jpg;base64,', '', $img);
	$img = str_replace('data:image/jpeg;base64,', '', $img);
	$img = str_replace(' ', '+', $img);
	$data = base64_decode($img);
	$file_path = $path_part.uniqid().'.jpg';
	$file = $file_path;
	$success = file_put_contents($file, $data);
	if($success)
		$result = $file_path; 
	
	return $result;
}

function substr_unicode($str, $s, $l = null) 
{
    return join("", array_slice(
        preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY), $s, $l));
}




?>