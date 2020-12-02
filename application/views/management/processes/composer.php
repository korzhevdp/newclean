Процесс по категории обращения <select id="category">
	<?=$categoryList;?>
</select>

<button type="button" id="save">Сохранить</button>

<table class="table table-bordered composerTable">
<tr>
	<th>Тело процесса&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" id="clear">Очистить</button></th>
	<th>Доступные функции</th>
</tr>
<tr>
	<td id="processBody" style="width:50%;">
		<ul id="processTasks"></ul>
	</td>
	<td  style="width:50%;">
		<ul id="availableFunctions">
			<?=$functions;?>
		</ul>
	</td>
</tr>
</table>

<script>
	var tasks = <?=$processesTasks;?>

	$("#availableFunctions .processTaskItem").click(function() {
		var ref = $(this).attr('ref'),
			text = $(this).text();
		$("#processTasks").append('<li ref="' + ref + '" class="processTaskItem" title="Щелчок добавит задачу в процесс">' + text + '</li>');
		$("#processTasks .processTaskItem").unbind().click(function() {
			$(this).remove();
		});
	});
	$("#save").click(function(){
		var out = [];
		$("#processTasks .processTaskItem").each(function(){
			out.push($(this).attr("ref"));
		});
		$.ajax({
			url      : "/processes/saveprocess",
			type     : "POST",
			data     : {
				category : $("#category").val(),
				tasks    : out
			},
			dataType : 'html',
			success  : function(data) {
				window.location.reload();
			},
			error    : function( data, stat, err ) {
				console.log([ data, stat, err ].join("\n"));
			}
		});
	});
	$("#clear").click(function(){
		$("#processTasks").empty();
	});
	$("#category").change(function(){
		var process = $(this).val(),
			text;
		$("#processTasks").empty();
		for ( a in tasks[process] ) {
			text = $("#availableFunctions .processTaskItem[ref=" + tasks[process][a] + "]").text()
			$("#processTasks").append('<li ref="' + tasks[process][a] + '" class="processTaskItem" title="Щелчок удалит задачу из процесса">' + text + '</li>');
		}
	});
</script>