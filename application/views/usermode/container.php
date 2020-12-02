<!doctype html>
<html class="user-view">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
	<meta name="MobileOptimzied" content="width">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-title" content="Чистый город - Архангельск">
	<meta name="mobile-web-app-title" content="Чистый город - Архангельск">
	<meta name="apple-mobile-web-app-status-bar-style" content="default">
	<meta name="keywords" content="Чистый город, уборка территории в архангельске, мусорные контейнеры в архангельске, сообщить о проблеме в архангельске">
	<meta name="description" content="Система «Чистый город» предназначена для фиксации нарушений или проблемных участков на территории Архангельска. Проект предоставляет возможность жителям города взаимодействовать с местными органами управления Администрации города.">
	<link rel="apple-touch-icon-precomposed" href="/img/favicon-x128.png">
	<link rel="touch-icon-precomposed" href="/img/favicon-x128.png">
	
	<title>Чистый город - Архангельск</title>
	<link rel="stylesheet" href="/styles/style.css">
	<link rel="stylesheet" href="/styles/icons/icons.css">
	<script type="text/javascript" src="/scripts/jquery.js"></script>
</head>
<body>

	
	<div id="appContent">
		<?=$content;?>
	</div>

	<div class="main-policy-info">
		<a href="/about/policy" target="_blank">Политика конфиденциальности</a>
	</div>

	<script type="text/javascript">
		var category = 0,
			requestUrl = "<?=$requestUrl?>";
		if (requestUrl.length) {
			$.ajax({
				url      : requestUrl,
				type     : "POST",
				data     : {},
				dataType : "html",
				success  : function(data) {
					$("#appContent").html(data);
					setListener();
				},
				error: function(data,stat,err) {
					console.log(data,stat,err);
				}
			});
		}

		function retrievePage(URL){
			$.ajax({
				url      : URL,
				type     : "POST",
				data     : {},
				dataType : "html",
				success  : function(data) {
					$("#appContent").html(data);
					setListener();
					return true;
				},
				error: function(data,stat,err) {
					console.log(data,stat,err);
				}
			});
		}

		function setListener(){
			$(".submit").unbind().click(function(e){
				e.preventDefault();
				//console.log(1)
				requestUrl = $(this).attr("ref") + "/content";
				retrievePage(requestUrl);
			});
		}

		$(window).on('popstate', function(event) {
			window.location.reload();
		});
	</script>


	<?//=//$metrika;?>
</body>
</html>