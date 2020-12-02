	<div class="page-container">
	<link rel="stylesheet" type="text/css" href="/styles/bootstrap-extended.css">
	<style type="text/css">
	.block {
	display: block;
	width: 100%;
	}
	.card-header {
		background-color: inherit;
	}
	.page-container {
		max-width:1000px
	}
	</style>
	<link rel="stylesheet" type="text/css" href="/styles/stat.style.min.css">
	<!-- "totalMessages" => 0,
		"finalized"     => 0,
		"inProgress"    => 0,
		"declined"      => 0,
		"new"           => 0,
		"totalUsers"    => 0,
		"activeUsers"   => 0 -->
	 <div class="app-content content">
		<div class="content-wrapper">
		   <div class="content-body">
			  <section id="grouped-stats">
				 <div class="row">
					<div class="col-12">
					   <div class="card">
						  <div class="card-content grouped-multiple-statistics-card">
							 <div class="card-body">
								<div class="row">
								   <div class="col-lg-3 col-md-6 col-12 border-right-blue-grey border-right-lighten-3 ">
									  <div class="float-left pl-2">
										 <span class="font-large-3 text-bold-300"><?=$totalMessages;?></span>
									  </div>
									  <div class="float-left mt-2 ml-1">
										 <span class="blue-grey darken-1 block">Всего</span>
										 <span class="blue-grey darken-1 block">обращений</span>
									  </div>
								   </div>
								   <div class="col-lg-3 col-md-6 col-12 border-right-blue-grey border-right-lighten-3">
									  <div class="float-left pl-2">
										 <span class="font-large-3 text-bold-300"><?=$finalized;?></span>
									  </div>
									  <div class="float-left mt-2 ml-1">
										 <span class="blue-grey darken-1 block">Выполнено</span>
										 <span class="blue-grey darken-1 block"></span>
									  </div>
								   </div>
								   <div class="col-lg-3 col-md-6 col-12 border-right-blue-grey border-right-lighten-3">
									  <div class="float-left pl-2">
										 <span class="font-large-3 text-bold-300"><?=$inProgress;?></span>
									  </div>
									  <div class="float-left mt-2 ml-1">
										 <span class="blue-grey darken-1 block">В работе</span>
										 <span class="blue-grey darken-1 block"></span>
									  </div>
								   </div>
								   <div class="col-lg-3 col-md-6 col-12">
									  <div class="float-left pl-2">
										 <span class="font-large-3 text-bold-300"><?=$declined;?></span>
									  </div>
									  <div class="float-left mt-2 ml-1">
										 <span class="blue-grey darken-1 block">Не принято</span>
										 <span class="blue-grey darken-1 block"></span>
									  </div>
								   </div>
								</div>
							 </div>
						  </div>
					   </div>
					</div>
				 </div>
				 <div class="row">
					<div class="col-12">
					   <div class="card">
						  <div class="card-content">
							 <div class="row">
								<div class="col-lg-4 col-md-12 col-sm-12 border-right-blue-grey border-right-lighten-5">
								   <div class="card-body text-center">
									  <div class="card-header mb-2">
										 <span class="success">Обработано</span>
										 <h3 class="font-large-2 blue-grey darken-1"><?=$finalized;?></h3>
									  </div>
									  <div class="card-content">
										 <div style="display:inline;width:150px;height:150px;">
											<input type="text" value="69" class="knob hide-value responsive angle-offset" data-angleoffset="0" data-thickness=".15" data-linecap="round" data-width="150" data-height="150" data-inputcolor="#e1e1e1" data-readonly="true" data-fgcolor="#16D39A" data-knob-icon="icon-note" readonly="readonly" style="width: 79px; height: 50px; position: absolute; vertical-align: middle; margin-top: 50px; margin-left: -114px; border: 0px; background: none; font: bold 30px Arial; text-align: center; color: rgb(225, 225, 225); padding: 0px; appearance: none; display: none;"><i class="knob-center-icon icon-note" style="width: 79px; height: 50px; position: absolute; vertical-align: middle; margin-top: 50px; margin-left: -114px; border: 0px; background: none; font: normal 30px Arial; text-align: center; color: rgb(225, 225, 225); padding: 0px; appearance: none;font-size: 50px;"></i>
										 </div>
										 <ul class="list-inline clearfix mt-2">
											<li>
											   <h1 class="blue-grey darken-1 text-bold-400"><?=round($finalized / $totalMessages, 2) * 100;?> %</h1>
											   <span class="success"><i class="icon-like"></i> В полном объеме</span>
											</li>
										 </ul>
									  </div>
								   </div>
								</div>
								<div class="col-lg-4 col-md-12 col-sm-12 border-right-blue-grey border-right-lighten-5">
								   <div class="card-body text-center">
									  <div class="card-header mb-2">
										 <span class="warning darken-2">В работе</span>
										 <h3 class="font-large-2 blue-grey darken-1"><?=$inProgress;?></h3>
									  </div>
									  <div class="card-content">
										 <div style="display:inline;width:150px;height:150px;">
											<input type="text" value="23" class="knob hide-value responsive angle-offset" data-angleoffset="0" data-thickness=".15" data-linecap="round" data-width="150" data-height="150" data-inputcolor="#e1e1e1" data-readonly="true" data-fgcolor="#FFA87D" data-knob-icon="icon-user" readonly="readonly" style="width: 79px; height: 50px; position: absolute; vertical-align: middle; margin-top: 50px; margin-left: -114px; border: 0px; background: none; font: bold 30px Arial; text-align: center; color: rgb(225, 225, 225); padding: 0px; appearance: none; display: none;"><i class="knob-center-icon icon-user" style="width: 79px; height: 50px; position: absolute; vertical-align: middle; margin-top: 50px; margin-left: -114px; border: 0px; background: none; font: normal 30px Arial; text-align: center; color: rgb(225, 225, 225); padding: 0px; appearance: none;font-size: 50px;"></i>
										 </div>
										 <ul class="list-inline clearfix mt-2">
											<li>
											   <h1 class="blue-grey darken-1 text-bold-400"><?=round($inProgress / $totalMessages, 2) * 100;?> %</h1>
											   <span class="warning darken-2"><i class="icon-head"></i> В том числе новые заявки</span>
											</li>
										 </ul>
									  </div>
								   </div>
								</div>
								<div class="col-lg-4 col-md-12 col-sm-12 border-right-blue-grey border-right-lighten-5">
								   <div class="card-body text-center">
									  <div class="card-header mb-2">
										 <span class="danger">Не принято</span>
										 <h3 class="font-large-2 blue-grey darken-1"><?=$declined;?></h3>
									  </div>
									  <div class="card-content">
										 <div style="display:inline;width:150px;height:150px;">
											<input type="text" value="10" class="knob hide-value responsive angle-offset" data-linecap="round" data-width="150" data-height="150" data-thickness=".15" data-inputcolor="#e1e1e1" data-readonly="true" data-fgcolor="#FF7588" data-knob-icon="icon-users" readonly="readonly" style="width: 79px; height: 50px; position: absolute; vertical-align: middle; margin-top: 50px; margin-left: -114px; border: 0px; background: none; font: bold 30px Arial; text-align: center; color: rgb(225, 225, 225); padding: 0px; appearance: none; display: none;"><i class="knob-center-icon icon-close" style="width: 79px; height: 50px; position: absolute; vertical-align: middle; margin-top: 50px; margin-left: -114px; border: 0px; background: none; font: normal 30px Arial; text-align: center; color: rgb(225, 225, 225); padding: 0px; appearance: none;font-size: 50px;"></i>
										 </div>
										 <ul class="list-inline clearfix mt-2">
											<li>
											   <h1 class="blue-grey darken-1 text-bold-400"><?=round($declined / $totalMessages, 2) * 100;?> %</h1>
											   <span class="danger darken-2"><i class="icon-head"></i> "Отказано" и "Не по тематике"</span>
											</li>
										 </ul>
									  </div>
								   </div>
								</div>
							 </div>
						  </div>
					   </div>
					</div>
				 </div>
				 <div class="row">
					<div class="col-12">
					   <div class="card">
						  <div class="card-content">
							 <div class="card-body">
								<div class="row">
								   <div class="col-lg-3 col-sm-12 border-right-blue-grey border-right-lighten-5">
									  <div class="media d-flex p-2">
										 <div class="align-self-center">
											<i class="icon-camera font-large-1 blue-grey d-block mb-1"></i>
											<span class="text-muted text-right">Загруженных фотографий</span>
										 </div>
										 <div class="media-body text-right">
											<span class="font-large-2 text-bold-300 primary">2202</span>
										 </div>
									  </div>
									  <div class="progress mt-1 mb-0" style="height: 7px;">
										 <div class="progress-bar bg-primary" role="progressbar" style="width: 80%" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
									  </div>
								   </div>
								   <div class="col-lg-3 col-sm-12 border-right-blue-grey border-right-lighten-5">
									  <div class="media d-flex p-2">
										 <div class="align-self-center">
											<i class="icon-user font-large-1 blue-grey d-block mb-1"></i>
											<span class="text-muted text-right">Всего пользователей</span>
										 </div>
										 <div class="media-body text-right">
											<span class="font-large-2 text-bold-300 danger"><?=$totalUsers;?></span>
										 </div>
									  </div>
									  <div class="progress mt-1 mb-0" style="height: 7px;">
										 <div class="progress-bar bg-danger" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
									  </div>
								   </div>
								   <div class="col-lg-3 col-sm-12 border-right-blue-grey border-right-lighten-5">
									  <div class="media d-flex p-2">
										 <div class="align-self-center">
											<i class="icon-bulb font-large-1 blue-grey d-block mb-1"></i>
											<span class="text-muted text-right">Активных граждан за 12 мес</span>
										 </div>
										 <div class="media-body text-right">
											<span class="font-large-2 text-bold-300 success"><?=$activeUsers;?></span>
										 </div>
									  </div>
									  <div class="progress mt-1 mb-0" style="height: 7px;">
										 <div class="progress-bar bg-success" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
									  </div>
								   </div>
								   <div class="col-lg-3 col-sm-12">
									  <div class="media d-flex p-2">
										 <div class="align-self-center">
											<i class="icon-user font-large-1 blue-grey d-block mb-1"></i>
											<span class="text-muted text-right">Новых пользователей за 12 мес</span>
										 </div>
										 <div class="media-body text-right">
											<span class="font-large-2 text-bold-300 warning"><?=$newUsers;?></span>
										 </div>
									  </div>
									  <div class="progress mt-1 mb-0" style="height: 7px;">
										 <div class="progress-bar bg-warning" role="progressbar" style="width: 40%" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
									  </div>
								   </div>
								</div>
							 </div>
						  </div>
					   </div>
					</div>
				 </div>
				 <div class="row">
					<div class="col-12">
					   <div class="card">
						  <div class="card-content mx-1 my-1 ">
							 <div class="row">
								<div class="col-lg-3 col-md-6 col-12 border-right-blue-grey border-right-lighten-3">
								   <div class="float-left pl-2">
									  <span class="font-large-3 text-bold-300 primary">6</span>
								   </div>
								   <div class="float-left mt-2 ml-1">
									  <span class="blue-grey darken-1 block">Департаментов</span>
								   </div>
								</div>
								<div class="col-lg-3 col-md-6 col-12 border-right-blue-grey border-right-lighten-3">
								   <div class="float-left pl-2">
									  <span class="font-large-3 text-bold-300 danger">60</span>
								   </div>
								   <div class="float-left mt-2 ml-1">
									  <span class="blue-grey darken-1 block">Зарегистрированных</span>
									  <span class="blue-grey darken-1 block">Организаций</span>
								   </div>
								</div>
								<div class="col-lg-3 col-md-6 col-12">
								   <div class="float-left pl-2">
									  <span class="font-large-3 text-bold-300 warning">59</span>
								   </div>
								   <div class="float-left mt-2 ml-1">
									  <span class="blue-grey darken-1 block">Пользователей от</span>
									  <span class="blue-grey darken-1 block">отв. подразделений</span>
								   </div>
								</div>
								<div class="col-lg-3 col-md-6 col-12 border-right-blue-grey border-right-lighten-3">
								   <div class="float-left pl-2">
									  <span class="font-large-3 text-bold-300 success">91</span>
								   </div>
								   <div class="float-left mt-2 ml-1">
									  <span class="blue-grey darken-1 block">Обращений в</span>
									  <span class="blue-grey darken-1 block">тех. поддержку</span>
								   </div>
								</div>
							 </div>
						  </div>
					   </div>
					</div>
				 </div>
			  </section>
			  <script src="/scripts/stat.jquery.knob.min.js"></script>
			 <script src="/scripts/card-statistics.js"></script>
			</div>
		</div>
	 </div>

	</div>
