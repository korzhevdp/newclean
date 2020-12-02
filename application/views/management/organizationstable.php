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
			url      : '/management/saveorganization',
			type     : "POST",
			data     : {
				id             : ref,
				name           : $(".formField[ref="+ref+"][role=name]").val(),
				full_name      : $(".formField[ref="+ref+"][role=full_name]").val(),
				address        : $(".formField[ref="+ref+"][role=address]").val(),
				inn            : $(".formField[ref="+ref+"][role=inn]").val(),
				phone          : $(".formField[ref="+ref+"][role=phone]").val(),
				email          : $(".formField[ref="+ref+"][role=email]").val(),
				boss           : $(".formField[ref="+ref+"][role=boss]").val(),
				house_count    : $(".formField[ref="+ref+"][role=houseCount]").val(),
				personal_count : $(".formField[ref="+ref+"][role=personnelCount]").val(),
				department     : ($(".formField[ref="+ref+"][role=department]").prop("checked")) ? 1 : 0,
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
