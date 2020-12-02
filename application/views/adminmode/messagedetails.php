<div class="messageInfoCard">
	<div class="date">
		Дата обращения: <?=$createTime?><br>
	</div>
	<h3>Обращение №<?=$messageID?></h3>
	<div class="fixedInfo">
		Территориальный округ: <?=$districtName;?><br>
		Категория обращения: <?=$categoryName?><br>
		Время обработки обращения: <?=$deadline?> дней<br>
	</div>

	<div class="mainText">
		<?=$message;?><hr>
		<?=$files;?>
	</div>

	Ответственная организация:
	<select id="boxOrganizationID"><?=$organizationList;?></select>
	На контроле у:
	<select id="boxControlID"><?=$controlList;?></select>
	Статус:
	<select id="boxStatusID"><?=$statusList?></select>
	Статус изменился: <?=$updateTime?>
	<input type="hidden" id="boxMessageID" value="<?=$messageID;?>">
	<table style="width:100%;margin: 10px -4px;">
	<tr>
		<td>В работе:</td>
		<td style="width:65px;"><label class="switch"><input type="checkbox" style="margin-top:20px;" id="boxArchive"<?=(($archive) ? '' : ' checked="checked"');?>><span class="slider round"></span></label></td>
	</tr>
	</table>
	
	<br><br>
	<div class="saveItem btn">Сохранить</div>
</div>
<script type="text/javascript">
	coords = <?=$coords;?>;
	$(".saveItem").unbind().click(function() {
		//console.log($("#boxOrganizationID").val(), $("#boxControlID").val(), $("#boxStatusID").val(), $("#boxMessageID").val());
		//return false;
		$.ajax({
			url      : "/admin/saveadmmessagedetails",
			type     : "POST",
			data     : {
				organizationID : $("#boxOrganizationID").val(),
				controlID      : $("#boxControlID").val(),
				statusID       : $("#boxStatusID").val(),
				messageID      : $("#boxMessageID").val(),
				archive        : $("#boxArchive").prop("checked") ? 0 : 1
			},
			dataType : 'html',
			success  : function(data) {
				
			},
			error    : function( data, stat, err ) {
				console.log([ data, stat, err ].join("\n"));
			}
		});
	});
</script>