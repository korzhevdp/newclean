<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы для Вас недоступен.');

if(!$law9 && !$law7)
{
	exit('<div class="pg10px">Модуль "Чаты" для Вас недоступен. Обратитесь к администратору системы.</div>');
}


//$userOptions = Users::getUserOptions($_SESSION['UID']);

$messageID = 0;
$userID = 0;
$departID = 0;
$orgID = 0;

$dataSuccess = false;
if(isset($_POST['message_id']) && is_numeric($_POST['message_id']) && isset($_POST['user_id']) && is_numeric($_POST['user_id'])
&& isset($_POST['depart_id']) && is_numeric($_POST['depart_id']) && isset($_POST['org_id']) && is_numeric($_POST['org_id']))
{
	$dataSuccess = true;
}

if(!$dataSuccess)
{
	exit('Данные, отправляемые в запросе, имеют некорректный формат. Обратитесь к системному администратору.');
}


$messageID = $_POST['message_id'];
$userID = $_POST['user_id'];
$departID = $_POST['depart_id'];
$orgID = $_POST['org_id'];

$chatInfo = Chat::getChatInfo($messageID,$userID,$departID,$orgID);
//print_r($chatInfo);
if(!$chatInfo['status'])
{
	exit('Чат недоступен');
}

//print_r($chatInfo['data']['messages']);

$arUser = Users::GetUserById($_SESSION['UID']);
$arUser = $arUser[$_SESSION['UID']];
//print_r($arUser);

$arMessage = Messages::getOneMessage(0,$messageID);
$arMessage = $arMessage[$messageID];
?>

<div class="pd-panel-cont chat-panel active">
	<div class="chat-header">
		<?php echo $arMessage['text'] ?>
		<span><?php echo $arMessage['address'] ?></span>
	</div>

	<div class="line-caption">Участники беседы</div>
	<div class="chat-users">
		<div class="icon-user user1-color"><?php echo $arUser['department'] ?></div>
		<div class="separ"></div>
		<div class="icon-user user2-color"><?php echo $chatInfo['unit_name'] ?></div>
	</div>
	<div class="line-caption">Сообщения в беседе</div>
	<div class="chat-scroller">
		<div class="chat-messages-block">
			<?php if(isset($chatInfo['data']['messages']) && is_array($chatInfo['data']['messages']) && count($chatInfo['data']['messages'])>0): ?>
				<?php foreach($chatInfo['data']['messages'] as $key => $message): ?>
				<?php
					$positionClass = 'left';
					if($message['department_id']==$arUser['department_id'])
					{
						$positionClass = 'right';
					}
					
					
				?>
					<div class="<?php echo $positionClass ?>">
						<span><b><?php echo $message['alias'] ?></b><?php echo $message['text'] ?><i>от <?php echo $message['date'] ?></i></span>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>
	<div class="chat-input form">
		<textarea placeholder="Введите сообщение..." name="chat_message" id="chat_message"></textarea>
		<!--<a href="#" class="chat-send-btn">Отпр.</a>-->
	</div>
	<script>
		var chat_messages_block_h = window.innerHeight-$('.chat-header').height()-$('.line-caption').height()-$('.chat-input').height()-$('.chat-users').height()-170;
    $('.chat-scroller').css('height',chat_messages_block_h+'px');
		$('.chat-scroller').mCustomScrollbar();
		$('.chat-scroller').mCustomScrollbar("scrollTo","bottom");
		
		$('#chat_message').on('keydown',function(e) {
        if( event.which == 13 )
				{
					var message = $(this).val();
					if(message!='')
					{
						var data = {
							page: 135,
							message_id: <?php echo ($messageID) ? $messageID: 0 ?>,
							user_id: <?php echo ($userID) ? $userID: 0 ?>,
							depart_id: <?php echo ($departID) ? $departID: 0 ?>,
							org_id: <?php echo ($orgID) ? $orgID: 0 ?>,
							text: message
						};
						
						sendChatMessage(data);
					}
					return false;
				}
    });
		
		function sendChatMessage(data) {
				var sendMessage = SendDataJSON(data);
        sendMessage.done(function(res){
            if(res.status)
            {
								if(res.data.text != '' && res.data.time != '' && res.data.user_alias!='')
								{
									$('#chat_message').val(null);
									var newMessContent = '<div class="right"><span><b>'+res.data.user_alias+'</b>'+res.data.text+'<i>от '+res.data.time+'</i></span></div>';
									$('.chat-messages-block').append(newMessContent);
									$('.chat-scroller').mCustomScrollbar("scrollTo","bottom");
								}
								else
								{
									alert('С сервера вернулся некорректный ответ.');
								}
            }
            else
            {
                alert(res.message);
            }
        });
        
        sendMessage.fail(function(jqXHR, code, textStatus) {
            serverResultError(textStatus);
        });
		}
	</script>
<?php
	

?>
</div>