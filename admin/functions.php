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


function CharacterFilter($str) 
{
    $res = strip_tags($str);
    $res = htmlspecialchars($res);
	
    return $res;
}


function SymbolSecur($str)
{
  $result = true;
    if (preg_match( "/[\<|\>]/", $str))
        $result = false;
  return $result;
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
	$result['message'] = 'При обработке изображений произошла ошибка.';
    $content = array();
    
    foreach($files as $key=>$file)
	{
        if($file_path = SaveOneFile($file['value']))
            if($file_path = resize($file_path, 2, null, null))
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

?>
