<?php
/**
* @author - alex.strigin <apstrigin@gmail.com>
*/
class ControllerModuleToolbar extends Controller {

	private $error = array();

	/*
	* Модель панели инструментов
	*/
	private $toolbar;

	/*
	* Token сессии
	*/
	private $token;

	/*
	* Ссылка на модуль
	*/
	private $moduleUrl;

	public function __construct($registry)
	{
		parent::__construct($registry);

		$this->load->language('module/toolbar');
		$this->load->model('toolbar/toolbar');
		$this->load->model('tool/image');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->token = $this->session->data['token'];
		$this->toolbar = &$this->model_toolbar_toolbar;
		$this->moduleUrl = $this->url->link('module/toolbar','token='.$this->token,'SSL');

		$this->data['token'] = $this->token;
		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['breadcrumbs'] = array();

 		$this->data['breadcrumbs'][] = array(
     		'text'      => $this->language->get('text_home'),
				'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
    		'separator' => false
 		);

 		$this->data['breadcrumbs'][] = array(
     		'text'      => $this->language->get('text_module'),
				'href'   => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
    		'separator' => ' :: '
 		);
	
 		$this->data['breadcrumbs'][] = array(
     		'text'      => $this->language->get('heading_title'),
				'href'      => $this->url->link('module/toolbar', 'token=' . $this->session->data['token'], 'SSL'),
    		'separator' => ' :: '
		);
		$this->data['cancel'] = $this->moduleUrl;

	}

	/*
	* Страница со списком элементов
	*/
	public function index()
	{
		$this->data['items'] = $this->toolbar->getItems();
		$this->template = 'module/toolbar/index.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
		$this->response->setOutput($this->render());
	}

	/*
	* Страница с формой для добавления элемента панели
	*/
	public function add()
	{
		if($this->is_post() && $this->add_validation()){
			$this->toolbar->addItem($this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect($this->moduleUrl);
		}

		$this->set_form_field('title');
		$this->set_form_field('img');
		$this->set_form_field('route');
		$this->set_form_field('order');

		if(!empty($this->data['img'])){
			$this->data['preview'] = $this->model_tool_image->resize($this->data['img'], 100, 100);
		}else{
			$this->data['preview'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);
		}

		$this->data['action'] = $this->url->link('module/toolbar/add','token='.$this->token,'SSL');
		$this->template = 'module/toolbar/form.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
		$this->response->setOutput($this->render());	
	}

	/*
	* Страница с формой для редактирования элемента панели
	*/
	public function edit()
	{
	
		$itemId = !empty($this->request->get['itemid'])?$this->request->get['itemid']:$this->redirect($this->moduleUrl);
		$item = $this->toolbar->getItem($itemId);

		if(empty($item)) $this->redirect($this->moduleUrl);

		if($this->is_post() && $this->edit_validation()){
			$this->toolbar->editItem($itemId,$this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect($this->moduleUrl);
		}

		$this->set_form_field('title',$item);
		$this->set_form_field('img',$item);
		$this->set_form_field('route',$item);
		$this->set_form_field('order',$item);

		if(!empty($this->data['img'])){
			$this->data['preview'] = $this->model_tool_image->resize($this->data['img'], 100, 100);
		}else{
			$this->data['preview'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);
		}

		$this->data['action'] = $this->url->link('module/toolbar/edit','itemid='.$itemId.'&token='.$this->token,'SSL');

		$this->template = 'module/toolbar/form.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
		$this->response->setOutput($this->render());	
	}

	/*
	* Удаление элемента панели
	*/
	public function del()
	{
		$itemId = !empty($this->request->get['itemid'])?$this->request->get['itemid']:$this->redirect($this->moduleUrl);
		$this->toolbar->delItem($itemId);
		$this->redirect($this->moduleUrl);
	}

	/*
	* Валидаторы
	*/
	private function validation()
	{
		if(empty($this->request->post['title']) or mb_strlen($this->request->post['title']) > 512 ){
			$this->error['title'] = $this->language->get('error_title');
		}

		if(empty($this->request->post['route']) or mb_strlen($this->request->post['route']) > 512 ){
			$this->error['route'] = $this->language->get('error_route');
		}


		if(!empty($this->request->post['order']) && !is_numeric($this->request->post['order'])){
			$this->error['order'] = $this->language->get('error_order');
		}

		if(!empty($this->error)) return false;
		return true;
	}
	private function add_validation()
	{
		return $this->validation();
	}

	private function edit_validation()
	{
		return $this->validation();
	}

	/*
	* Установка / Удаление
	*/
	public function install()
	{
		$this->toolbar->install();
	}

	public function uninstall()
	{
		$this->toolbar->uninstall();
	}

	/*
	* Хелперы
	*/
	private function is_post()
	{
		return $this->request->server['REQUEST_METHOD'] == 'POST';
	}

	private function set_form_field($field,$item='')
	{
		if(!empty($this->request->post[$field])){
			$this->data[$field] = $this->request->post[$field];
		}else if(!empty($item) && !empty($item[$field])){
			$this->data[$field] = $item[$field];
		}else{
			$this->data[$field] = '';
		}

		if(!empty($this->error[$field])){
			$this->data["error_".$field] = $this->error[$field];
		}else{
			$this->data["error_".$field] = '';
		}
	}
}