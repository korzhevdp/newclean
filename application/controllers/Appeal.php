<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Appeal extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->helper("url");
		$this->load->model("usermodel");
		$this->load->model("typemodel");
		$this->load->model("messagemodel");
		$this->authorizationCheck();
	}

	private function authorizationCheck() {
		$this->session->set_userdata('force_redirect', str_replace("/content", "", uri_string()));
		if (!$this->usermodel->isAuthorized()) {
			redirect("login");
		}
		return true;
	}

	public function index() {
		$this->startPage();
	}

	private function startPage() {
		$requestURL = base_url()."appeal/content";
		print $this->loadPage($requestURL);
	}

	private function getDistrictsCoordinatesObject() {
		$result = $this->db->query("SELECT 
		city_districts.id,
		city_districts.coordinates,
		city_districts.responsible,
		city_districts.name
		FROM
		city_districts", array() );
		if ( $result->num_rows() ) {
			$output = array();
			foreach ( $result->result() as $row ) {
				$string = $row->id." : { coords: ".substr($row->coordinates, 1, -1).", responsible: ".$row->responsible.", name : '".$row->name."' } ";
				array_push($output, $string);
			}
			return implode($output, ",\n\t\t");
		}
	}

	private function loadAppealInitialPage($page="dashboard") {
		$output = array();
		//print print_r($this->session->userdata(), true);
		//return false;
		$messages =  $this->messagemodel->userMessagesList($this->session->userdata('UID'), 0);
		if ( sizeof($messages) ) {
			foreach ( $messages as $key => $message ) {
				//print print_r($message,true)."<br>";
				$status_ok    = ( $message->status_id == 2 ) ? ' green-mlink' : "";
				$disabled     = ( $message->status_id == 5 || $message->status_id == 6 ) ? "" : "disabled";
				$message_text = ( strlen($message->message) > 32 ) ? mb_substr($message->message, 0, 30 ).'...' : $message->message;
				$string = '<div class="messageItem"><a href="/appeal/messagedetails/'.$key.'" class="icon-mail-1 messageLink" target="_blank">'.$message_text.'</a></div>';
				array_push($output, $string);
			}
		}

		$data = array(
			'header'       => $this->typemodel->getHeaderContent(),
			'link'         => base_url().strtolower(get_class())."/".$page,
			'myMessages'   => (sizeof($output)) ? implode($output, "\n") : "У Вас ещё не было обращений",
			'messageCount' => sizeof($output)
		);
		print $this->load->view("usermode/userdashboard", $data, true);
	}

	private function loadAppealCategoryPage($page="category") {
		$categoriesList = $this->messagemodel->getCategories();

		$output = array();
		foreach ($categoriesList as $id => $category) {
			$string = '<a href="#" data-id="'.$id.'" data-description="'.$category->description.'" class="mes-category '.$category->icon.'">'.$category->name.'</a>
			<div class="mess-cat-description" data-cat="'.$id.'">'.$category->description.'
				<a href="#" class="btn cat-selection" data-id="'.$id.'">Выбрать</a>
			</div>';
			array_push($output, $string);
		}
		$data = array(
			'categories' => implode($output,"\n"),
			'caption'    => ( sizeof($output) > 1 ) ? 'Выберите категорию сообщения в соответствии с правилами' : 'На данный момент доступна только одна категория',
			'ifEmpty'    => ( sizeof($output) )     ? "" : '<p class="empty-page">Нет доступных для выбора категорий</p>',
			'link'       => base_url().strtolower(get_class())."/".$page
		);
		print $this->load->view("usermode/inputform", $data, true);
	}

	private function loadAppealMapPage($page="map") {
		$category = $this->messagemodel->getCategory($this->session->userdata("categoryID"));
		if (!isset ($category->name) ) { 
			redirect("appeal/category");
		}
		$coords   = $this->session->userdata('coords');
		$data     = array(
			'categoryID'   => $this->session->userdata("categoryID"),
			'categoryName' => $category->name,
			'dCoordinates' => $this->getDistrictsCoordinatesObject(),
			'coords'       => ( isset($coords['lat']) && isset($coords['lng']) ) ? 'lat : '.$coords['lat'].', lng : '.$coords['lng'] : '',
			'caption'      => 'Укажите на карте, где Вы зафиксировали проблему. Добавьте щелчком или перетащите маркер в нужную точку',
			'link'         => base_url().strtolower(get_class())."/".$page
		);
		print $this->load->view("usermode/inputform2", $data, true);
	}

	private function loadAppealInfoPage($page="info") {
		//print_r($this->session->userdata(), true);
		if (
			   !$this->session->userdata("districtID")
			|| !$this->session->userdata("districtName")
			|| !$this->session->userdata("responsible")
			|| !$this->session->userdata("address")
		){
			redirect("appeal/map");
		}
		$data = array(
			'address'      => ($this->session->userdata('address')) ? $this->session->userdata('address') : "",
			'caption'      => 'Сообщите дополнительную информацию. Приложите фотографии',
			'link'         => base_url().strtolower(get_class())."/".$page
		);
		print $this->load->view("usermode/inputform3", $data, true);
	}

	private function loadPage($requestURL) {
		$data = array(
			'metrika'      => $this->load->view("usermode/metrika", array(), true),
			'content'      => "",
			'requestUrl'  => (current_url() == base_url())
				? base_url()."welcome/content"
				: $requestURL
		);
		return $this->load->view("usermode/container", $data, true);
	}

	private function showLoginPage() {
		$data = array(
			'metrika'    => $this->load->view("usermode/metrika", array(), true),
			'content'    => $this->load->view("usermode/login", array('header' => $this->typemodel->getHeaderContent()), true),
			'requestUrl' => base_url()."login/content"
		);
		$this->load->view("usermode/container", $data);
	}

	
	//извлечение контента страниц через ajax-запросы
	
	public function content($page = "dashboard") {
		$this->authorizationCheck();

		if ($page == "category") {
			$this->loadAppealCategoryPage($page);
			return true;
		}
		if ($page == "map") {
			$this->loadAppealMapPage($page);
			return true;
		}
		if ($page == "info") {
			$this->loadAppealInfoPage($page);
			return true;
		}
		$this->loadAppealInitialPage($page);
	}

	// функции прямого вызова страницы (переоткрытие по F5)
	// стартовая страница
	public function dashboard() {
		$this->authorizationCheck();
		$requestURL = base_url().strtolower(get_class())."/content/".__FUNCTION__;
		print $this->loadPage($requestURL);
	}
	// вторая страница обращения, с выбором категории
	public function category() {
		$this->authorizationCheck();
		$requestURL = base_url().strtolower(get_class())."/content/".__FUNCTION__;
		print $this->loadPage($requestURL);
	}
	// третья страница обращения, с картой
	public function map() {
		$this->authorizationCheck();
		$requestURL = base_url().strtolower(get_class())."/content/".__FUNCTION__;
		print $this->loadPage($requestURL);
	}
	// четвёртая страница: дополнительная информация и загрузка фото
	public function info() {
		$this->authorizationCheck();
		$requestURL = base_url().strtolower(get_class())."/content/".__FUNCTION__;
		print $this->loadPage($requestURL);
	}

	// функции обработчики активности
	// установка категории в сессию
	public function setcategory() {
		if ( !$this->usermodel->isAuthorized() ) {
			$this->showLoginPage();
			return false;
		}
		$this->session->set_userdata("categoryID", $this->input->post('categoryID'));
		$category = $this->messagemodel->getCategory($this->session->userdata('categoryID'));
		$this->session->set_userdata("categoryName", $category->name);

		$this->loadAppealMapPage();
	}

	//установка в сессию координаты, адреса, данных по ТО, попытка определить ответственных
	public function setlocation() {
		if ( !$this->usermodel->isAuthorized() ) {
			$this->showLoginPage();
			return false;
		}
		$this->session->set_userdata("districtID",   $this->input->post('districtID'));
		$this->session->set_userdata("districtName", $this->input->post('districtName'));
		$this->session->set_userdata("responsible",  $this->input->post('responsible'));
		$this->session->set_userdata("address",      $this->input->post('address'));
		$this->session->set_userdata("coords",       array("lat" => $this->input->post('lat'), "lng" => $this->input->post('lng')) );
		$this->loadAppealInfoPage();
	}

	// финальная страница
	public function finalizeappeal() {
		$this->session->set_userdata("moreInfo", $this->input->post('moreInfo'));
		$this->load->model("uploadmodel");
		$this->load->model("mailmodel");
		$messageData = $this->session->userdata();
		$messageData['files'] = array();
		foreach($_FILES as $file) {
			array_push($messageData['files'], $this->uploadmodel->accomodateFile($file));
		}
		//print print_r($messageData, true);
		//Вставка сообщения в базу
		$messageID = $this->messagemodel->NewMessage($messageData);

		if ( $messageID ) {
			print(base_url()."appeal/messagedetails/".$messageID);
		}
		// а что если нет?
		// ...
	}

	//возвращает данные заявки в пользовательском контексте
	public function messageDetails($messageID) {
		if ( !$this->usermodel->isAuthorized() ) {
			$this->showLoginPage();
			return false;
		}

		$result = $this->messagemodel->getMessageDetailsData($messageID);
		if ( $result->num_rows() ) {
			$row = $result->row_array();
			$row['header'] = $this->typemodel->getHeaderContent();
			$row['files']  = $this->messagemodel->makeImagesList($row['files']);
			$data = array(
				'metrika'    => $this->load->view("usermode/metrika", array(), true),
				'content'    => $this->load->view("usermode/messagedetails", $row, true),
				'requestUrl' => ""
			);
			$this->load->view("usermode/container", $data);
			return true;
		}
		$data = array(
			'metrika'    => $this->load->view("usermode/metrika", array(), true),
			'content'    => $this->typemodel->getHeaderContent()."<h2>Информация о сообщении недоступна</h2>",
			'requestUrl' => ""
		);
		$this->load->view("usermode/container", $data);
	}

	// вспомогательная функция работы с адресным планом
	// возвращает ближайший адрес по переданной координате в двухкомпонентном формате
	public function getnearestaddress() {
		$result = $this->db->query("SELECT
		`adplan`.house,
		`adplan`.block,
		`adplan`.bldg,
		`adplan`.street_name,
		`adplan`.id,
		SQRT(
			POWER(((`adplan`.`Lmax` + `adplan`.`Lmin`) / 2) - ?, 2) +
			POWER(((`adplan`.`Bmax` + `adplan`.`Bmin`) / 2) - ?, 2)
		) AS `dist`
		FROM
		`adplan`
		ORDER BY `dist` ASC
		LIMIT 1", array(
			$this->input->post("lng"),
			$this->input->post("lat")
		));
		if ( $result->num_rows() ) {
			$row = $result->row();
			$string = $row->street_name.", д. ".$row->house;
			$string .= ($row->block) ? ", к. ".$row->block  : "" ;
			$string .= ($row->bldg)  ? ", стр. ".$row->bldg : "" ;
			print $string;
			return true;
		}
		print "Ничего не найдено";
	}

	private function getUserAppealsOnMapData() {
		$result = $this->db->query("SELECT 
			messages.id,
			message_category.`id`									AS categoryID,
			message_category.name									AS categoryName,
			DATE_FORMAT(messages.create_time, '%d.%m.%Y, %H:%i')	AS createTime,
			DATE_FORMAT(messages.update_time, '%d.%m.%Y, %H:%i')	AS updateTime,
			CONCAT('{ lat: ', messages.coord_x,', lng: ',messages.coord_y,' }')	AS coords,
			city_districts.name										AS districtName,
			message_status.name										AS statusName
		FROM
			messages
			LEFT OUTER JOIN city_districts   ON (messages.district_id = city_districts.id)
			LEFT OUTER JOIN message_status   ON (messages.status_id   = message_status.id)
			LEFT OUTER JOIN message_category ON (messages.category_id = message_category.id)
		WHERE
			(messages.user_id = ?)
			AND (NOT (messages.removed))", array($this->session->userdata('UID')) );
		if ( $result->num_rows() ) {
			$output = array();
			foreach ( $result->result() as $row ) {
				$string = "{ id : ".$row->id.", categoryID : ".$row->categoryID.", categoryName : '".$row->categoryName."', createTime : '".$row->createTime."', updateTime : '".$row->updateTime."', coords : ".$row->coords.", districtName : '".$row->districtName."', statusName : '".$row->statusName."' }";
				array_push($output, $string);
			}
			return implode($output, ",\n");
		}
		return "[]";
	}

	public function usermap() {
		if ( !$this->usermodel->isAuthorized() ) {
			$this->showLoginPage();
			return false;
		}
		$data = array(
			'metrika'    => $this->load->view("usermode/metrika", array(), true),
			'content'    => $this->load->view("usermode/usermap", array('coords' => $this->getUserAppealsOnMapData()), true),
			
			'requestUrl' => ""
		);
		$this->load->view("usermode/container", $data);
	}
}
