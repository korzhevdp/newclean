<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
	<meta name="description" content="">
	<title>Чистый Город · <?=$header;?></title>

	<link rel="canonical" href="">

	<!-- Bootstrap core CSS -->
	<link href="/styles/bootstrap.min.css" rel="stylesheet">
	<!-- Custom styles for this template -->
	<link href="/styles/dashboard.css" rel="stylesheet">
	<link href="/styles/management.css" rel="stylesheet">


	<style>

	</style>

</head>
<body>
	<nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
		<a class="navbar-brand col-md-3 col-lg-2 mr-0 px-3" href="#">Чистый Город</a>
		<button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-toggle="collapse" data-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Включить навигацию">
			<span class="navbar-toggler-icon"></span>
		</button>
		<input class="form-control form-control-dark w-100" id="filterPanel" type="text" placeholder="Фильтр" aria-label="Фильтр">
	</nav>
	<script src="/scripts/jquery.js"></script>
	<div class="container-fluid">
		<div class="row">

			<?=$menu;?>

		<div role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
		  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
			<h1 class="h2"><?=$header;?></h1>
			<div class="pull-right">
				<a href="/profile"><?=$this->session->userdata("alias");?> / <?=$this->session->userdata("roleCaption");?></a>
			</div>
			<!-- <div class="btn-toolbar mb-2 mb-md-0">
			  <div class="btn-group mr-2">
				<button type="button" class="btn btn-sm btn-outline-secondary">Share</button>
				<button type="button" class="btn btn-sm btn-outline-secondary">Экспорт</button>
			  </div>
			  <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
				<span data-feather="calendar"></span>
				Неделя
			  </button>
			</div> -->
		  </div>

		<?=$content;?>
    </div>
  </div>
</div>
<script type="text/javascript">
	$(".navbar-toggler").click(function(){
		if ($("#sidebarMenu").hasClass("show")) {
			$("#sidebarMenu").removeClass("show");
			return true;
		}
		$("#sidebarMenu").addClass("show");
	});

	$("#filterPanel").keyup(function(){
		var val = $(this).val().toLowerCase();
		if ( val.length < 3 ) {
			$(".settingCaptionRow").removeClass("hide");
			return false;
		}
		$(".settingCaption").each(function(){
			var ref  = $(this).attr('ref'),
				text = $(this).text().toLowerCase();
				//console.log(text, ref);
			if ( ~text.search(val) ) {
				$(".settingCaptionRow[ref=" + ref + "]").removeClass("hide");
				return true;
			}
			$(".settingCaptionRow[ref=" + ref + "]").addClass("hide");
		})
		//console.log(val)
	});
</script>
</html>
