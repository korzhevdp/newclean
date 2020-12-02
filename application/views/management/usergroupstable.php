<table class="table table-sm" id="userGroupRightsTable">
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
			url      : '/management/saveusergroup',
			type     : "POST",
			data     : {
				id      : ref,
				name    : $(".formField[ref="+ref+"][role=name]").val(),
				caption : $(".formField[ref="+ref+"][role=caption]").val(),
				law1    : ($(".formField[ref="+ref+"][role=law1]").prop("checked"))   ? 1 : 0,
				law2    : ($(".formField[ref="+ref+"][role=law2]").prop("checked"))   ? 1 : 0,
				law3    : ($(".formField[ref="+ref+"][role=law3]").prop("checked"))   ? 1 : 0,
				law4    : ($(".formField[ref="+ref+"][role=law4]").prop("checked"))   ? 1 : 0,
				law5    : ($(".formField[ref="+ref+"][role=law5]").prop("checked"))   ? 1 : 0,
				law6    : ($(".formField[ref="+ref+"][role=law6]").prop("checked"))   ? 1 : 0,
				law7    : ($(".formField[ref="+ref+"][role=law7]").prop("checked"))   ? 1 : 0,
				law8    : ($(".formField[ref="+ref+"][role=law8]").prop("checked"))   ? 1 : 0,
				law9    : ($(".formField[ref="+ref+"][role=law9]").prop("checked"))   ? 1 : 0,
				law4_1  : ($(".formField[ref="+ref+"][role=law4_1]").prop("checked")) ? 1 : 0
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
