<?php

class CRMConnect_Admin {
	public $api_fields = array(
		array('name' => 'sugarcrm_url', 'title' => 'SugarCRM URL', 'default' => '', 'type' => 'long-text'),
		array('name' => 'sugarcrm_user', 'title' => 'SugarCRM User', 'default' => '', 'type' => 'text'),
		array('name' => 'sugarcrm_pass', 'title' => 'SugarCRM Password', 'default' => '', 'type' => 'password')
	);

	public $config_fields = array(
		array('name' => 'userModule', 'title' => 'User Module', 'default' => '', 'type' => 'select', 'options' => '')
	);

	public $options;
	public $fields;
	public $username_select;
	public $email_select;
	public $selected_fields;
	public $avail_fields;
	public $main;
	public $postMessage;

	function  __construct($main) {
		$db = new CRMConnect_DB();
		$this->config_fields[0]['options'] = $db->get_modules();
		$this->options = unserialize(get_option('crmconnect_options'));
		$this->main = &$main;
	}

	function print_admin_page($form) {
		if (!current_user_can("manage_options"))  {
			wp_die( __("You do not have sufficient permissions to access this page.") );
		}
		$tpl = new Savant3();
		$tpl->setPath("template",$this->main->path."/tpl");
		$tpl->title = $form["title"];
		$tpl->admin = &$this;
		$tpl->action = $form["action"];
		$tpl->display($form["template"]);
		$this->postMessage = false;
	}

	public function options() {
		if($this->main->active){
			$args = array("title"=>"CRM Connect Options","template"=>"admin_options.tpl.php","action"=>"update");
		} else {
			$args = array("title"=>"CRM Connect Options","template"=>"admin_options_api.tpl.php","action"=>"activate");
		}
		$this->print_admin_page($args);
	}

	public function options_api() {
		$args = array("title"=>"API Access","template"=>"admin_options_api.tpl.php","action"=>"activate");
		$this->print_admin_page($args);
	}

	public function options_reg() {
		$this->get_module_fields();
		$args = array("title"=>"Registration Form Fields","template"=>"admin_options_reg.tpl.php","action"=>"reg_update");
		$this->print_admin_page($args);
	}

	public function onPost() {
		if($_REQUEST['crmconnect_action']){
			switch ($_REQUEST['crmconnect_action']){
				case "activate":
					$data = array();
					foreach($this->api_fields as $field){
						$data[$field['name']] = $_REQUEST[$field['name']];
					}
					update_option('crmconnect_api_access',  serialize($data));
					if($data['sugarcrm_url'] && $data['sugarcrm_user'] && $data['sugarcrm_pass']) {
						$wps = new CRMConnect_Connect($data);
						if($wps->session){
							update_option('crmconnect_active',1);
							$wps->storeModuleList();
							$this->main->active = true;
							$this->postMessage = array('class'=>'updated','message'=>'API Connection Established. Please configure CRM Connect!');
						} else {
							$this->main->active = false;
							$this->postMessage = array('class'=>'error','message'=>'API Connection Failed. Please verify the access information below and try again.');
						}
					}
					break;

				case "update":
					if($this->main->active){
						if($userModule = $_REQUEST['userModule']) {
							$connect = new CRMConnect_Connect();
							$fields = $connect->getModuleFields($userModule);
							$fields = $fields->module_fields;
							$userModule_fields = array();
							foreach($fields as $field){
								$userModule_fields[$field->name] = $field->label;
							}
							update_option('crmconnect_user_fields', serialize($userModule_fields));
						}
						foreach($this->config_fields as $field){
							$data[$field['name']] = $_REQUEST[$field['name']];
						}
						update_option('crmconnect_options',  serialize($data));
						$this->postMessage = array('class'=>'updated','message'=>'Configuration Saved');
					}
					break;
				case "reg_update":
					if($this->main->active){
						$fields = $_REQUEST["crmconnect_fields"];
						$fields = json_decode(stripslashes($fields));
						$this->options["regForm"] = $fields;
						update_option('crmconnect_options',  serialize($this->options));
						$this->postMessage = array('class'=>'updated','message'=>'Configuration Saved');
					}
					break;
			}
		}
	}

	function get_module_fields(){
		$db = new CRMConnect_DB();
		$fields = $db->get_fields();
		if($fields){
			$this->fields = $fields;
		} else {
			$connect = new CRMConnect_Connect();
			$fields = $connect->getModuleFields($this->options["userModule"]);
			$this->fields = new stdClass();
			foreach($fields->module_fields as $k => $f){
				if(in_array($f->type,CRMConnect::$allowedSugarTypes))
					$this->fields->{$k} = $f;
			}
			$db->insert_fields($this->fields);
		}
		$this->username_select = "<select id='username_select'><option></option>";
		$this->email_select = "<select id='email_select'><option></option>";
		$this->selected_fields = "";
		$this->avail_fields = "";
		foreach($this->fields as $field){
			$field->label = str_replace(":","",$field->label);
			$this->username_select .= "<option value='{$field->name}'" .
				(($this->options["regForm"]->username_select==$field->name) ? " selected='selected'" : "") .
				">{$field->label}</option>";
			$this->email_select .= "<option value='{$field->name}'" .
				(($this->options["regForm"]->email_select==$field->name) ? " selected='selected'" : "") .
				">{$field->label}</option>";
			if($this->options["regForm"]->fields && in_array($field->name,$this->options["regForm"]->fields))
				$this->selected_fields .= "<li class='sortable' id='{$field->name}'>{$field->label}</li>";
			else
				$this->avail_fields .= "<li class='sortable' id='{$field->name}'>{$field->label}</li>";
		}
		$this->username_select .= "</select>";
		$this->email_select .= "</select>";
	}

	function admin_menu() {
		add_menu_page( __( 'CRM Connect', 'crmconnect' ), __( 'CRM Connect', 'crmconnect' ),
		'manage_options', 'crmconnect', array($this,'options') );
		if($this->main->active){
			add_submenu_page( 'crmconnect', __( 'API Access', 'crmconnect_api' ), __( 'API Access', 'crmconnect_api' ),
				'manage_options', 'crmconnect_api', array($this,'options_api') );
			$regPage = add_submenu_page( 'crmconnect', __( 'Registration Form', 'crmconnect_form' ), __( 'Registration Form', 'crmconnect_form' ),
				'manage_options', 'crmconnect_form', array($this,'options_reg') );
		}

		add_action('admin_print_scripts-' . $regPage, array($this,'admin_reg_scripts'));
		add_action('admin_print_styles-' . $regPage, array($this,'admin_reg_styles'));
	}

	public function admin_reg_scripts() {
		wp_enqueue_script(array("jquery", "jquery-ui-core", "interface", "jquery-ui-sortable", "wp-lists", "jquery-ui-sortable", "json2"));
		wp_enqueue_script('crmconnect_admin_reg',$this->main->url."/js/crmconnect_admin_reg.js");
	}

	public function admin_reg_styles(){
		wp_enqueue_style('crmconnect_admin_reg',$this->main->url."/css/crmconnect_admin_reg.css");
	}

}
?>