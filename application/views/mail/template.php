<!doctype html>
<html>
	<head>
		<meta name="viewport" content="width=device-width">
		<meta http-equiv="Content-Type" content="text/html; charset='.$content_charset.'">
		<title><?=$event_name;?></title>
		<style>
			@media only screen and (max-width: 620px) {
				table[class=body] h1 {
					font-size: 28px !important;
					margin-bottom: 10px !important;
				}
				table[class=body] p,
				table[class=body] ul,
				table[class=body] ol,
				table[class=body] td,
				table[class=body] span,
				table[class=body] a {
					font-size: 16px !important;
				}
				table[class=body] .wrapper,
							table[class=body] .article {
					padding: 10px !important;
				}
				table[class=body] .content {
					padding: 0 !important;
				}
				table[class=body] .container {
					padding: 0 !important;
					width: 100% !important;
				}
				table[class=body] .main {
					border-left-width: 0 !important;
					border-radius: 0 !important;
					border-right-width: 0 !important;
				}
				table[class=body] .btn table {
					width: 100% !important;
				}
				table[class=body] .btn a {
					width: 100% !important;
				}
				table[class=body] .img-responsive {
					height: auto !important;
					max-width: 100% !important;
					width: auto !important;
				}
			}
	
			@media all {
				.ExternalClass {
					width: 100%;
				}
				.ExternalClass,
				.ExternalClass p,
				.ExternalClass span,
				.ExternalClass font,
				.ExternalClass td,
				.ExternalClass div {
					line-height: 100%;
				}
				.apple-link {
					color: #999999;
					font-size: 12px;
					text-align: center;
				}
				.apple-link a {
					color: inherit !important;
					font-family: inherit !important;
					font-size: inherit !important;
					font-weight: inherit !important;
					line-height: inherit !important;
					text-decoration: none !important;
				}
				.btn-primary:hover,
				.btn-primary:hover a {
					background-color: #05498e !important;
				}
				
				.btn-primary a:hover {
					background-color: #05498e !important;
					border: solid 1px #05498e !important;
				}
			}
			body {
				background-color: #f6f6f6;
				font-family: sans-serif;
				-webkit-font-smoothing: antialiased;
				font-size: 14px;
				line-height: 1.4;
				margin: 0;
				padding: 0;
				-ms-text-size-adjust: 100%;
				-webkit-text-size-adjust: 100%;
			}
			.container {
				font-family: sans-serif;
				font-size: 14px;
				vertical-align: top;
				display: block;
				Margin: 0 auto;
				max-width: 580px;
				padding: 10px;
				width: 580px;
			}
			table.main {
				border-collapse: separate;
				mso-table-lspace: 0pt;
				mso-table-rspace: 0pt;
				width: 100%;
				background: #ffffff;
				border-radius: 3px;
				border-top: 5px solid #0954a0;
			}
			table.body {
				border-collapse: separate;
				mso-table-lspace: 0pt;
				mso-table-rspace: 0pt;
				width: 100%;
				background-color: #f6f6f6;
				padding: 0;
				border-spacing: 0;
				border:none;
			}
			div.content {
				box-sizing: border-box;
				display: block;
				margin: 0 auto;
				max-width: 580px;
				padding: 10px;
			}
			div.content-block {
				font-family: sans-serif;
				vertical-align: top;
				padding-bottom: 10px;
				padding-top: 10px;
				font-size: 12px;
				color: #999999;
				text-align: center;
			}
			div.footer {
				clear: both;
				Margin-top: 10px;
				text-align: center;
				width: 100%;
			}
			.messageBody {
				padding:10px;
				color: #6b6b6b;
				border: 1px solid 
			}
		</style>
	</head>

	<body>
		<table class="body">
			<tr>
				<td>&nbsp;</td>
				<td class="container">
					<div class="content">

						<!-- START CENTERED WHITE CONTAINER -->
						<table class="main">
							<!-- START MAIN CONTENT AREA -->
							<tr>
								<td class="wrapper" style="box-sizing: border-box; padding: 20px;">
									<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;">
										<tr>
											<td>
												<div style="padding-bottom: 15px; margin-bottom: 15px; border-bottom: 1px solid #eaeaea;text-align:center;">
													<img src="<?=base_url();?>img/big-logo-2.jpg" height="250" border="0" alt="">
													<div style="text-align: center; width: 100%; font-size: 16px;">Архангельск</div>
												</div>
												<p style="font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">
													<?=$eventText;?>
												</p>
												<?=$linkSet;?>
												<p style="font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">
													<div style="padding: 15px 0; margin-bottom: 15px; border-top: 1px solid #eaeaea;">
														<p>Дополнительные возможности:</p>
														&rsaquo;&nbsp;<a href="<?=base_url();?>">Изменить пароль</a><br>
														&rsaquo;&nbsp;<a href="<?=base_url();?>">Написать в техподдержку</a><br>
														&rsaquo;&nbsp;<a href="<?=base_url();?>">Ознакомиться с инструкцией</a><br>
													</div>

													<div style="padding: 15px 0; margin-bottom: 15px; border-top: 1px solid #eaeaea; color: #000;"><?=$furtherText;?></div>
												</p>
												<p style="font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">
													<div style="border-top: 1px solid #eaeaea; padding-top: 15px; color: #000;"><?=$footerText;?></div>
												</p>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						<!-- END MAIN CONTENT AREA -->
						</table>

						<!-- START FOOTER -->
						<div class="footer">
							<table class="main">
								<tr>
									<td class="content-block">
										<span class="apple-link">Администрация МО "Город Архангельск" - МУ "ЦИТ", пл. Ленина 5</span><br>
										Чистый город (<a href="<?=base_url();?>">перейти к системе</a>).
									</td>
								</tr>
							</table>
						</div>
						<!-- END FOOTER -->
					<!-- END CENTERED WHITE CONTAINER -->
					</div>
				</td>
				<td>&nbsp;</td>
			</tr>
		</table>
	</body>
</html>