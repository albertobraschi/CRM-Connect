<?php

class CRMConnect_DB {
    
	private $module_table;
	private $bean_table;
	private $db;

	function __construct() {
		global $wpdb;
		$this->db = &$wpdb;
		$this->module_table = $this->db->prefix."crmconnect_modules";
		$this->field_table = $this->db->prefix."crmconnect_fields";
	}

	public function install() {
		if($this->table_exists($this->module_table) && $this->table_exists($this->bean_table))
			return;

		$charset_collate = '';
		if($this->db->has_cap('collation')) {
			if(!empty($this->db->charset))
				$charset_collate = "DEFAULT CHARACTER SET {$this->db->charset}";
			if(!empty($this->db->collate))
				$charset_collate .= " COLLATE {$this->db->collate}";
		}

		$this->db->query("CREATE TABLE IF NOT EXISTS {$this->module_table} (
			id int NOT NULL auto_increment,
			title varchar(200) NOT NULL default '',
			PRIMARY KEY (id)) $charset_collate;");

		$this->db->query("CREATE TABLE IF NOT EXISTS {$this->field_table} (
			id int NOT NULL auto_increment,
			name varchar(125) NOT NULL,
			type varchar(125) NOT NULL,
			label varchar(125) NOT NULL,
			required bit,
			options longtext,
			PRIMARY KEY (id)) $charset_collate;" );

		if(!$this->table_exists($this->module_table) || !$this->table_exists($this->bean_table))
			return false;
	}

	function table_exists($table) {
		return strtolower($this->db->get_var("SHOW TABLES LIKE '$table'"))==strtolower($table);
	}

	public function get_modules() {
		$local_modules = $this->db->get_col("SELECT title FROM " . $this->module_table);

		return $local_modules;
	}

	public function insert_module($module) {
		$this->db->insert($this->module_table,array('title'=>$module));
	}

	public function insert_fields($fields) {
		foreach($fields as $field) {
			$this->db->query($this->db->prepare("INSERT INTO %s
			( name, type, label, required, options ) VALUES ( %s, %s, %s, %d, %s )",
			$this->field_table, $field->name, $field->type, $field->label, $field->required, serialize($field->options)));
		}
	}

	public function get_fields() {
		$this->db->get_results("SELECT * from $this->field_table");
	}

}

?>