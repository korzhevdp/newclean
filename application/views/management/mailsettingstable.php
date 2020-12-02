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
			url      : '/management/savemessageevent',
			type     : "POST",
			data     : {
				id         : ref,
				event_name : $(".formField[ref="+ref+"][role=eventName]").val(),
				subject    : $(".formField[ref="+ref+"][role=subject]").val(),
				text       : $(".formField[ref="+ref+"][role=text]").val(),
				link       : $(".formField[ref="+ref+"][role=link]").val(),
				link_text  : $(".formField[ref="+ref+"][role=link_text]").val(),
				from_email : $(".formField[ref="+ref+"][role=from_email]").val(),
				active     : ($(".formField[ref="+ref+"][role=active]").prop("checked")) ? 1 : 0
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
