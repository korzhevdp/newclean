<table class="table table-sm">
	<?=$statii;?>
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

	$(".formField[role=webColor], .formField[role=statusColor]").change(function(){
		var ref = $(this).attr("ref");
			color = $(this).val();
		//console.log($(this).parent().html(), $(this).val());
		$(this).parent().css("background-color", $(this).val());
	});

	$(".formField[role=webColor], .formField[role=statusColor]").change();

	$(".saveItem").click(function(){
		var ref = $(this).attr("ref");
		$.ajax({
			url      : '/management/savemessagestatus',
			type     : "POST",
			data     : {
				statusID     : ref,
				statusName   : $(".formField[ref="+ref+"][role=statusName]").val(),
				statusColor  : $(".formField[ref="+ref+"][role=statusColor]").val(),
				statusIcon   : $(".formField[ref="+ref+"][role=statusIcon]").val(),
				webColor     : $(".formField[ref="+ref+"][role=webColor]").val(),
				active       : ($(".formField[ref="+ref+"][role=active]").prop('checked')) ? 1 : 0,
				finalization : ($(".formField[ref="+ref+"][role=final]").prop('checked'))  ? 1 : 0

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
