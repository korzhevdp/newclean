<?

//var_dump(file_exists($_SERVER["DOCUMENT_ROOT"]."/log.txt"));

phpinfo();
$contacts['name'] = 'name';
$contacts['dep_id'] = 'dep_id';
$contacts["USERS"][0]["EMAIL"] = 'email';
$contacts["USERS"][0]["phone"] = 'phone';

 //echo "<pre>";print_r($contacts);echo "</pre>";

/*foreach($contacts["USERS"] as $user)
        {
            print_r($user["EMAIL"]);
        }*/

        //file_put_contents($_SERVER["DOCUMENT_ROOT"]."/log.txt", $contacts, FILE_APPEND);
?>