<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
	<div class="sidebar-sticky pt-3">
		<ul class="nav flex-column">

			<h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
				Первое по списку меню
			</h6>

			<li class="nav-item">
				<a class="nav-link<?=((uri_string() == "management/mailsettings") ? " active" : "");?>" href="/management/mailsettings">Настройка почтовых событий</a>
			</li>

			<li class="nav-item">
				<a class="nav-link<?=((uri_string() == "management/messages") ? " active" : "");?>" href="/management/messages">Обращения граждан</a>
			</li>

			<li class="nav-item">
				<a class="nav-link<?=((uri_string() == "management/logs") ? " active" : "");?>" href="/management/logs">История операций</a>
			</li>

			<li class="nav-item">
				<a class="nav-link<?=((uri_string() == "processes") ? " active" : "");?>" href="/processes">Диаграммы обращений</a>
			</li>

		</ul>

		<h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
			Справочники
		</h6>

		<ul class="nav flex-column mb-2">
			<li class="nav-item">
				<a class="nav-link<?=((uri_string() == "management/users") ? " active" : "");?>" href="/management/users">Пользователи </a>
			</li>

			<li class="nav-item">
				<a class="nav-link<?=((uri_string() == "management/usergroups") ? " active" : "");?>" href="/management/usergroups">Группы пользователей</a>
			</li>

			<li class="nav-item">
				<a class="nav-link<?=((uri_string() == "management/messagestatii") ? " active" : "");?>" href="/management/messagestatii">Статусы сообщений</a>
			</li>

			<li class="nav-item">
				<a class="nav-link<?=((uri_string() == "management/organizations") ? " active" : "");?>" href="/management/organizations">Организации</a>
			</li>

			<li class="nav-item">
				<a class="nav-link<?=((uri_string() == "management/messagecategories") ? " active" : "");?>" href="/management/messagecategories">Категории сообщений</a>
			</li>

			<li class="nav-item">
				<a class="nav-link<?=((uri_string() == "management/subcategories") ? " active" : "");?>" href="/management/subcategories">Подкатегории сообщений</a>
			</li>

		</ul>
		<h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
			<span>Информация</span>
			<a class="d-flex align-items-center text-muted" href="#">
				<span data-feather="plus-circle"></span>
			</a>
		</h6>
		<ul class="nav flex-column mb-2">
			<li class="nav-item">
				<a class="nav-link<?=((uri_string() == "management") ? " active" : "");?>" href="/management">Статистика</a>
			</li>
		</ul>
		<h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
			<span>Завершение работы</span>
			<a class="d-flex align-items-center text-muted" href="#">
				<span data-feather="plus-circle"></span>
			</a>
		</h6>
		<ul class="nav flex-column mb-2">
			<li class="nav-item">
				<a class="nav-link" href="/login/logout">Выход</a>
			</li>
		</ul>
	</div>
</nav>