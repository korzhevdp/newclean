<table class="table table-sm" id="mailSettingsTable">
	<?=$table;?>
</table>
<script type="text/javascript">
	$(".editItem").click(function(){
		var ref = $(this).attr("ref");
		$( ".settingSection, .saveItem").addClass("hide");
		$(".editItem").removeClass("hide");
		$( this ).addClass("hide");
		$(".saveItem[ref=" + ref + "]").removeClass("hide");
		if ( $(".settingSection[ref=" + ref + "]" ).hasClass("hide") ) {
			 $(".settingSection[ref=" + ref + "]" ).removeClass("hide");
		}
	});
	$(".saveItem").click(function(){
		var ref = $(this).attr("ref");
		$.ajax({
			url      : '/management/savemessagesubcategory',
			type     : "POST",
			data     : {
				id             : ref,
				name           : $(".formField[ref="+ref+"][role=name]").val(),
				caption        : $(".formField[ref="+ref+"][role=caption]").val(),
				description    : $(".formField[ref="+ref+"][role=description]").val(),
				deadline       : $(".formField[ref="+ref+"][role=deadline]").val(),
				icon           : $(".formField[ref="+ref+"][role=icon]").val(),
				yandex_icon    : $(".formField[ref="+ref+"][role=yandex_icon]").val(),
				parentID       : $(".formField[ref="+ref+"][role=category]").val(),
				departmentID   : $(".formField[ref="+ref+"][role=department]").val(),
				organizationID : $(".formField[ref="+ref+"][role=organization]").val(),
				districtID     : $(".formField[ref="+ref+"][role=district]").val(),
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
