<?php
class CRMConnect_Connect {

	private $entryUrl;
	private $sugarUser;
	private $sugarPass;
	private $curl;
	public $session = 0;

	function  __construct($data=false) {
		if(!$data)
			$data = unserialize(get_option('crmconnect_api_access'));
		$this->entryUrl = $data['sugarcrm_url'];
		$this->sugarUser = $data['sugarcrm_user'];
		$this->sugarPass = $data['sugarcrm_pass'];

		if(!empty($this->entryUrl) && !empty($this->sugarUser) && !empty($this->sugarPass))
			$this->connect();
	}

	private function connect() {
		$this->curl = curl_init($this->entryUrl);
		curl_setopt($this->curl, CURLOPT_POST, true);
		curl_setopt($this->curl, CURLOPT_HEADER, false);
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
		$parameters = array(
			'user_auth' => array(
					'user_name' => $this->sugarUser,
					'password' => md5($this->sugarPass)
			),
		);
		$json = json_encode($parameters);

		$postArgs = 'method=login&input_type=JSON&response_type=JSON&rest_data=' . $json;
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, $postArgs);
		$response = curl_exec($this->curl);
		//curl_close( $this->curl );
		$result = json_decode($response);
		$this->session = $result->id;
	}

	public function getModuleList() {
		$parameters = array(
			'session' => $this->session
		);
		$json = json_encode($parameters);
		$postArgs = 'method=get_available_modules&input_type=JSON&response_type=JSON&rest_data=' . $json;
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, $postArgs);
		$response = curl_exec($this->curl);
		$result = json_decode($response);
		return $result;
	}

	public function storeModuleList() {
		$db = new CRMConnect_DB();
		$local_modules = $db->get_modules();
		$live_modules = $this->getModuleList();
		$live_modules = $live_modules->modules;
		foreach($live_modules as $module){
			if(!in_array($module, $local_modules))
				$db->insert_module($module);
		}
	}

	public function getModuleFields($module) {
		$parameters = array(
			'session' => $this->session,
			'module_name' => $module
		);
		$json = json_encode($parameters);
		$postArgs = 'method=get_module_fields&input_type=JSON&response_type=JSON&rest_data=' . $json;
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, $postArgs);
		$response = curl_exec($this->curl);
		$result = json_decode($response);
		return $result;
	}

	public function setEntry($userData) {
		$module = unserialize(get_option('crmconnect_options'));
		$module = $module['userModule'];
		$parameters = array(
			'session' => $this->session,
			'module_name' => $module,
			'name_value_list' => $userData
		);
		$json = json_encode($parameters);
		$postArgs = 'method=set_entry&input_type=JSON&response_type=JSON&rest_data=' . $json;
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, $postArgs);
		$response = curl_exec($this->curl);
		$result = json_decode($response);
		return $result;
	}
}
?>