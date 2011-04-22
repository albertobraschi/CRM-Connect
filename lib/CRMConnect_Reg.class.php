<?php

class CRMConnect_Reg {
    
	public $main;
	
	function  __construct($main) {
		$this->main = &$main;
	}

	public function enqueueRegStyle(){
		if($_REQUEST["action"]=="register")
			echo '<link rel="stylesheet" type="text/css" href="' . $this->main->url . '/css/crmconnect_reg.css" />';
	}

	public function getFields(){
		$options = unserialize(get_option('crmconnect_options'));
		if($fields = get_option("crmconnect_module_fields")){
			$module_fields = unserialize($fields);
		}
		$res = $options["regForm"];
		$res->formFields = new stdClass();
		foreach($res->fields as $field){
			eval("\$res->formFields->{$field} = \$module_fields->{$field};");
		}
		return $res;
	}

	public function showOtherRegFields(){
		$res = $this->getFields();
		foreach($res->formFields as $field):
		$field->label = str_replace(":","",$field->label);
		    ?>
		    <p>
			    <label><?php _e($field->label) ?><br />
			    <input type="text" name="<?php echo $field->name; ?>" id="<?php echo $field->name; ?>" class="crmconnect_input" value="<?php echo esc_attr(stripslashes($_POST[$field->name])); ?>" size="20" tabindex="30" /></label>
		    </p>
		    <?php
		endforeach;
	}

	public function checkOtherRegFields($login, $email, $errors) {
		if (empty($_POST['first_name'])) {
			$errors->add( 'empty_first_name', __( '<strong>ERROR</strong>: Please type your first name.' ) );
		}
		if (empty($_POST['last_name'])) {
			$errors->add( 'empty_last_name', __( '<strong>ERROR</strong>: Please type your last name.' ) );
		}
	}

	public function setOtherRegFields($user_id, $password="", $meta=array())  {
		    $fname = $_POST['first_name'];
		    $lname = $_POST['last_name'];
		    $userdata = array("ID" => $user_id);
		    $userdata['first_name'] = $fname;
		    $userdata['last_name'] = $lname;
		    wp_update_user($userdata);

		    $user = get_userdata($user_id);
		    if($user){
			    $suserData= array(
				    array('name' => 'first_name', 'value' => $fname),
				    array('name' => 'last_name', 'value' => $lname),
				    array('name' => 'email1', 'value' => $user->user_email)
			    );
			    $connect = new CRMConnect_Connect();
			    $connect->setEntry($suserData);
		    }
	    }
}

?>
