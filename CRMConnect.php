<?php
/*
Plugin Name: CRM Connect
Plugin URI:  http://bhamrick.com/crm-connect/
Description: Adds self-registration integration with SugarCRM
Version: 0.1
Author: Bryce Hamrick
Author URI: http://bhamrick.com
*/

require_once dirname(__FILE__) . "/lib/CRMConnect.class.php";

$CRMC = new CRMConnect(__FILE__);
?>