<?php
class CRMConnect {
	public $path;
	public $basename;
	public $name;
	public $url;
	public $active;
	public static $allowedSugarTypes = array("text","varchar","enum","bool","phone");

	function __construct($file){
		$this->path = dirname($file);
		$this->basename = plugin_basename($file);
		$this->name = trim(dirname($this->basename),'/');
		$this->url = WP_PLUGIN_URL.'/'.$this->name;
		$this->active = get_option('crmconnect_active') ? true : false ;

		require_once $this->path . '/lib/CRMConnect_DB.class.php';
		require_once $this->path . '/lib/CRMConnect_Connect.class.php';
		require_once $this->path . '/lib/CRMConnect_Admin.class.php';
		require_once $this->path . '/lib/CRMConnect_Reg.class.php';
		require_once $this->path . '/lib/Savant3.php';
		
		$admin = new CRMConnect_Admin(&$this);
		$admin->onPost();
		add_action('admin_menu', array($admin,'admin_menu'));

		$reg = new CRMConnect_Reg(&$this);
		add_action('register_form',array($reg,'showOtherRegFields'));
		add_action('register_post',array($reg,'checkOtherRegFields'),10,3);
		add_action('user_register',array($reg,'setOtherRegFields'));
		add_action('login_head',array($reg,'enqueueRegStyle'));

		$db = new CRMConnect_DB();
		register_activation_hook( __FILE__, array($db,'install') );
		add_action('activate_'.$this->basename,array($db,'install'));
	}

}
?>