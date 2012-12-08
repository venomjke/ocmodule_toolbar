<?php
/**
* @author - alex.strigin <apstrigin@gmail.com>
*/
class ModelToolbarToolbar extends Model{
	
	/*
	* Добавление элемента
	*/
	public function addItem($data)
	{
		$sql = "INSERT INTO  `".DB_PREFIX."toolbar` (`id` ,`title` , `img` ,`route` , `order`)VALUES (NULL ,  '".$this->db->escape($data['title'])."',  '".$this->db->escape($data['img'])."',  '".$this->db->escape($data['route'])."',  '".$this->db->escape($data['order'])."');";
		$this->db->query($sql);
	}

	/*
	* Редактирование элемента
	*/
	public function editItem($id,$data)
	{
		$sql = "UPDATE  `".DB_PREFIX."toolbar` SET  `title` = '".$this->db->escape($data['title'])."', `img` = '".$this->db->escape($data['img'])."', `route` =  '".$this->db->escape($data['route'])."', `order` = '".$this->db->escape($data['order'])."' WHERE  `".DB_PREFIX."toolbar`.`id` =".(int)$id.";";
		$this->db->query($sql);
	}

	/*
	* Выбор элементов
	*/
	public function getItems()
	{
		$sql = "SELECT * FROM `".DB_PREFIX."toolbar` ORDER BY `order` ASC";
		$result = $this->db->query($sql);
		return $result->rows;
	}

	/*
	* Выбор элемента
	*/
	public function getItem($id)
	{
		$sql = "SELECT * FROM `".DB_PREFIX."toolbar` WHERE id=".(int)$id;
		$result = $this->db->query($sql);
		return $result->row;
	}

	/*
	* Удаление элемента
	*/
	public function delItem($id)
	{
		$sql = "DELETE FROM `".DB_PREFIX."toolbar` WHERE id=".(int)$id;
		$this->db->query($sql);
	}


	/*
	* Проверяем, установлен ли модуль
	*/
	public function isModuleInstall()
	{
		$sql = "SELECT * FROM `".DB_PREFIX."extension` WHERE type='module' AND code='toolbar' LIMIT 1";
		$result = $this->db->query($sql);
		if(!empty($result->row))
			return true;	
		return false;
	}

	public function install()
	{
		$toolbar = 'CREATE TABLE IF NOT EXISTS `'.DB_PREFIX.'toolbar` (
								  `id` int(11) NOT NULL AUTO_INCREMENT,
								  `title` varchar(512) NOT NULL,
								  `img` varchar(512) NOT NULL,
								  `route` varchar(512) NOT NULL,
								  `order` tinyint(4) NOT NULL,
								  PRIMARY KEY (`id`)
								) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;';
		$this->db->query($toolbar);
	}

	public function uninstall()
	{
		$toolbar = 'DROP TABLE `'.DB_PREFIX.'toolbar`';
		$this->db->query($toolbar);
	}
}