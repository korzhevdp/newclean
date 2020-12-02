<style type="text/css">
	#userGroupRightsTable td {
	 text-align:center;
	 vertical-align:middle;
	}
</style>
<?=$bcrumbs;?>
<table class="table table-hover table-bordered table-sm" id="userGroupRightsTable">
	<tr>
		<th>Ф.И.О.</th>
		<th style="max-width:250px;">E-mail</th>
		<th style="max-width:150px;">Телефон</th>
		<th style="width:150px;">Группа</th>
		<th style="width:200px;">Департамент</th>
		<th style="width:200px;">Организация</th>
		<th style="width:100px;">Регистрация</th>
		<th style="width:100px;">Последний вход</th>
		<th style="width:70px;">Активен</th>
		<th style="width:70px;"></th>
	</tr>
	<?=$table;?>
</table>
<?=$bcrumbs;?>
<br><br><br><br><br>
<script type="text/javascript">
	$(".saveItem").click(function(){
		var ref = $(this).attr("ref");
		$.ajax({
			url      : '/management/saveuser',
			type     : "POST",
			data     : {
				id             : ref,
				alias          : $(".formField[ref="+ref+"][role=alias]").val(),
				email          : $(".formField[ref="+ref+"][role=email]").val(),
				phone          : $(".formField[ref="+ref+"][role=phone]").val(),
				groupID        : $(".formField[ref="+ref+"][role=groupID]").val(),
				departmentID   : $(".formField[ref="+ref+"][role=departmentID]").val(),
				organizationID : $(".formField[ref="+ref+"][role=organizationID]").val(),
				active         : ($(".formField[ref="+ref+"][role=active]").prop("checked")) ? 1 : 0
			},
			dataType : "html",
			success  : function(data) {
				window.location.reload();
			},
			error: function(data,stat,err) {
				console.log(data,stat,err);
			}
		});
	});
</script>
