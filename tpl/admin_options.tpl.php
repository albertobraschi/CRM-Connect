<?php
	$this->display("admin_header.tpl.php");
	$this->data = $this->admin->options;
	foreach($this->admin->config_fields as $this->field){
		$this->display("admin_field.tpl.php");
	}
	$this->display("admin_footer.tpl.php");
?>