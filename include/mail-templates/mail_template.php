<?php
/*
$arOptions = array();
		$arOptions = array(
						'CHARSET' 			=> 'utf-8', // Устанавливает кодировку
						'INNER_TITLE' 		=> 'Регистрация пользователя ', // Устанавливает значение тега <TITLE> в структуре
						'MAIN_TEXT'   		=> 'Поздравляем, Вы успешно зарегистрировались в системе "Чистый город". Ваш email - это логин для входа в систему.', // Основной текст сообщения
						'LINK_HREF'   		=> 'cleancity.arhcity.ru', // Ссылка на ключевой объект (если необходимо куда-то направить пользователя)
						'LINK_TEXT'   		=> 'Перейти в личный кабинет', // Текст ссылки
						'ADDITIONAL_TEXT' 	=> '', // Дополнительный текст (после ссылки, если она существует)
						'BOTTON_TEXT'  		=> 'С уважением, Администрация МО "Город Архангельск"'  // Завершающий текст в нижней части письма
					);
		
		$mail_content = getMailContent($arOptions); // функция возвращает контент письма

		echo $mail_content;
	*/
function getMailContent($arOptions = array()) {
	
	
	$content_charset = (isset($arOptions['CHARSET']) && $arOptions['CHARSET']!='') ? $arOptions['CHARSET'] : 'UTF-8';
	$mail_template_name = (isset($arOptions['INNER_TITLE']) && $arOptions['INNER_TITLE']!='') ? $arOptions['INNER_TITLE'] : 'Напоминание';
	$main_mail_text = (isset($arOptions['MAIN_TEXT']) && $arOptions['MAIN_TEXT']!='') ? $arOptions['MAIN_TEXT'] : '';
	
	$mail_link_href = (isset($arOptions['LINK_HREF']) && $arOptions['LINK_HREF']!='') ? $arOptions['LINK_HREF'] : '';
	$mail_link_text = (isset($arOptions['LINK_TEXT']) && $arOptions['LINK_TEXT']!='') ? $arOptions['LINK_TEXT'] : '';

	$add_mail_text = (isset($arOptions['ADDITIONAL_TEXT']) && $arOptions['ADDITIONAL_TEXT']!='') ? $arOptions['ADDITIONAL_TEXT'] : '';
	$bottom_mail_text = (isset($arOptions['BOTTON_TEXT']) && $arOptions['BOTTON_TEXT']!='') ? $arOptions['BOTTON_TEXT'] : '';
	$bound = "content-data";
	
	
	/**************************************  HEADER  ****************************************/
	$content = '
	<!doctype html>
	<html>
		<head>
			<meta name="viewport" content="width=device-width">
			<meta http-equiv="Content-Type" content="text/html; charset='.$content_charset.'">
			<title>'.$mail_template_name.'</title>
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
			</style>
		</head>
	';
	
	/**************************************  BODY START  ****************************************/
	$logo_path = $_SERVER['DOCUMENT_ROOT']."/img/big-logo.jpg";
	$content .= '
	<body class="" style="background-color: #f6f6f6; font-family: sans-serif; -webkit-font-smoothing: antialiased; font-size: 14px; line-height: 1.4; margin: 0; padding: 0; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">
    <table border="0" cellpadding="0" cellspacing="0" class="body" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; background-color: #f6f6f6;">
      <tr>
        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">&nbsp;</td>
        <td class="container" style="font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; Margin: 0 auto; max-width: 580px; padding: 10px; width: 580px;">
          <div class="content" style="box-sizing: border-box; display: block; Margin: 0 auto; max-width: 580px; padding: 10px;">

            <!-- START CENTERED WHITE CONTAINER -->
            <table class="main" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; background: #ffffff; border-radius: 3px; border-top: 5px solid #0954a0;">

              <!-- START MAIN CONTENT AREA -->
              <tr>
                <td class="wrapper" style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;">
                  <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;">
                    <tr>
                      <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">
													<div style="padding-bottom: 15px; margin-bottom: 15px; border-bottom: 1px solid #eaeaea;">
														<div style="text-align: center; width: 100%; font-size: 16px;">Архангельск</div>
														<div style="text-align: center; width: 100%; font-size: 20px;">Чистый город</div>
													</div>
                        <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">
	';
	

	/**************************************  MAIN MAIL TEXT  ****************************************/
	$content .= $main_mail_text;
	
	/**************************************  CONTINUATION OF CONTENT  ****************************************/
	$content .= '
	</p>
	<table border="0" cellpadding="0" cellspacing="0" class="btn" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; box-sizing: border-box;">
                          <tbody>
                            <tr>
                              <td align="left" style="font-family: sans-serif; font-size: 14px; vertical-align: top; padding-bottom: 15px;">
                                <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: auto;">
                                  <tbody>       
	';
	
	
	
	

	if($mail_link_href!='' && $mail_link_text!='')
	{
		/**************************************  MAIL BUTTON LINK  ****************************************/
		$content .= '<tr><td class="btn-primary" style="font-family: sans-serif; font-size: 14px; vertical-align: top; background-color: #0954a0; border-radius: 5px; text-align: center;">
		<a href="'.$mail_link_href.'" target="_blank" style="display: inline-block; color: #ffffff; background-color: #0954a0; border: solid 1px #0954a0; border-radius: 5px; box-sizing: border-box; cursor: pointer; text-decoration: none; font-size: 16px; font-weight: bold; margin: 0; padding: 12px 25px; border-color: #0954a0;">'.$mail_link_text.'</a></td></tr>';
		
		/**************************************  MAIL DEFAULT LINK  ****************************************/
		$content .= '<tr class="pr"><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">
		<br/>или пройдите по следующей <a href="'.$mail_link_href.'" target="_blank" style="">ссылке.</a></td></tr>';
	}
	
	
	
	
	/**************************************  CONTINUATION OF CONTENT  ****************************************/
	$content .= '
																			
                                    </tr>
                                  </tbody>
                                </table>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                        <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">
	';
	
	
	/*****************************************  MORE LINKS TEXT  ******************************************/
	
	/*
	$content .= "<div style=\"padding: 15px 0; margin-bottom: 15px; border-top: 1px solid #eaeaea; \">";
	$content .= "<p>Дополнительные возможности:</p>";
	$content .= "&rsaquo;&nbsp;<a href=\"#\">Изменить пароль</a><br>";
	$content .= "&rsaquo;&nbsp;<a href=\"#\">Написать в техподдержку</a><br>";
	$content .= "&rsaquo;&nbsp;<a href=\"#\">Ознакомиться с инструкцией</a><br>";
	$content .= "</div>";
	*/

	/**************************************  ADDITIONAL MAIL TEXT  ****************************************/
	if($add_mail_text!='')
	$content .= "<div style=\"padding: 15px 0; margin-bottom: 15px; border-top: 1px solid #eaeaea; color: #000; \">".$add_mail_text."</div>";
	
	
	
	
	
	/**************************************  CONTINUATION OF CONTENT  ****************************************/
	$content .= '</p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">';
	
	
	
	/**************************************  BOTTOM MAIL TEXT  ****************************************/
	if($bottom_mail_text!='')
		$content .= "<div style=\"border-top: 1px solid #eaeaea; padding-top: 15px; color: #000;\">".$bottom_mail_text."</div>";
	
	
	/**************************************  CONTINUATION OF CONTENT  ****************************************/
	$content .= '
				</p>
													</td>
												</tr>
											</table>
										</td>
									</tr>
		
								<!-- END MAIN CONTENT AREA -->
								</table>
		
								<!-- START FOOTER -->
								<div class="footer" style="clear: both; Margin-top: 10px; text-align: center; width: 100%;">
									<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;">
										<tr>
											<td class="content-block" style="font-family: sans-serif; vertical-align: top; padding-bottom: 10px; padding-top: 10px; font-size: 12px; color: #999999; text-align: center;">
												<span class="apple-link" style="color: #999999; font-size: 12px; text-align: center;">Администрация МО "Город Архангельск" - МУ "ЦИТ", пл. Ленина 5</span>
												<br>Чистый город (<a href="http://gorod.arhcity.ru" style="text-decoration: underline; color: #999999; font-size: 12px; text-align: center;">перейти к системе</a>).
											</td>
										</tr>
									</table>
								</div>
								<!-- END FOOTER -->
		
							<!-- END CENTERED WHITE CONTAINER -->
							</div>
						</td>
						<td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">&nbsp;</td>
					</tr>
				</table>
			</body>
		</html>
	';
	return $content;
}



?>
