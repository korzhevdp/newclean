<div class="header-panel sticky">
	<a href="<?=base_url();?>/appeal/dashboard" class="icon-left-open-big slide-back">Назад</a>
	<div>Выбор категории</div>
</div>
<?=$ifEmpty;?>
<div class="container">
	<p><?php echo $caption ?></p>
</div>
<div class="items gr-border">
	<?=$categories;?>
</div>

<a href="#" ref="/welcome" class="bl-link icon-bars submit">Перейти в главный раздел</a>

<script>
		if ($(location).attr('href') != "<?=$link;?>") {
			window.history.pushState("", "Чистый город - подача обращения", "<?=$link;?>");
		}
		$(".mes-category").unbind().click(function(e){
			e.preventDefault();
			var ref = $(this).attr("data-id");
			$(".mes-category, .mess-cat-description").removeClass("active");
			$(this).addClass("active");
			$(".mess-cat-description[data-cat=" + ref + "]").addClass("active");
			category = ref;
		});

		$(".cat-selection").unbind().click(function(e){
			e.preventDefault();
			$.ajax({
				url      : '/appeal/setcategory',
				type     : "POST",
				data     : {
					categoryID : category
				},
				dataType : "html",
				success  : function(data) {
					$("#appContent").html(data);
					setListener();
				},
				error: function(data,stat,err) {
					console.log(data,stat,err);
				}
			});
		});
</script>