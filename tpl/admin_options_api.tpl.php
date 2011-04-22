<?php
	$this->display("admin_header.tpl.php");
	$this->data = unserialize(get_option('crmconnect_api_access'));
	foreach($this->admin->api_fields as $this->field){
		$this->display("admin_field.tpl.php");
	}
	$this->display("admin_footer.tpl.php");
?>