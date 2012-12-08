Панель инструментов, панель быстрого доступа и.т.п
================

Панель инструментов на главной странице администратора, очень хорошо подходит для организации ссылок на 
часто посещаемые разделы.

Install
================
Для стандартной сборки достаточно скачать модуль, и загрузить его в папку с opencart

Для нестандартной сборки понадобится проверить следующие файлы

1. admin/controller/common/header.php 

В этом файле нужно добавить в метод index()
$this->data['text_toolbar'] = $this->language->get('text_toolbar');

2. admin/controller/common/home.php

В метод index вставить в любое место.
/*
* Панель инструментов
*/
$this->load->model('toolbar/toolbar');
$this->load->model('tool/image');
$this->load->language('module/toolbar');
if($this->model_toolbar_toolbar->isModuleInstall()){
	$this->data['text_toolbar'] = $this->language->get('text_toolbar');
	$toolbarItems = $this->model_toolbar_toolbar->getItems();
	foreach($toolbarItems as &$toolbarItem){
		$toolbarItem['href'] = $this->url->link($toolbarItem['route'],'token='.$this->data['token'],'SSL');
		if(!empty($toolbarItem['img'])){
			$toolbarItem['thumb'] = $this->model_tool_image->resize($toolbarItem['img'],126,126);
		}else{
			$toolbarItem['thumb'] = $this->model_tool_image->resize('data/no_image.jpg',126,126);
		}
	}
	$this->data['toolbarItems'] = $toolbarItems;
}else{
	$this->data['toolbarItems'] = array();
};

//////////////////////////////////////////

3. admin/view/template/stylesheet/stylesheet.css

.toolbar{
  margin-bottom:15px;
}
.toolbar .toolbar-item{
	float:left;
	margin-left:10px;
	text-align: center;
	font-size:14pt;
}

.toolbar .toolbar-item a{
	text-decoration: none;
}

.toolbar .toolbar-item a:hover{
	text-decoration: underline;
}

4. admin/view/template/common/header.tpl

Вставить можно в любой <ul>, у меня по умолчанию добавлено в <li class="system"><a>..></a><ul> сюда </ul> </li,
<li><a href="<?php echo $toolbar?>"><?php echo $text_toolbar; ?></a></li>

5. admin/view/template/common/home.tpl
  <?php if(!empty($toolbarItems)):?>
  <div class="toolbar">
    <div class="dashboard-heading"> <?php echo $text_toolbar;?> </div>
    <div class="dashboard-content">
        <?php foreach($toolbarItems as $toolbarItem): ?>
          <div class="toolbar-item">
            <a href="<?php echo $toolbarItem['href']; ?>"><img src="<?php echo $toolbarItem['thumb']; ?>" /><br/><?php echo $toolbarItem['title']; ?></a>
          </div>
        <?php endforeach;?>
    </div>
  </div>
  <?php endif; ?>
6. admin/language/common/header.tpl

добавить строчку $_['text_toolbar'] = 'Панель инструментов';