<?php
namespace App\Controller;

abstract class AbstractController{


	// container for variables needed by
	// the view scripts
	protected $viewVars;
	
	protected $_pageSize;

	public $viewScript;

	// stores messages associated with errors in forms.
	protected $errorMessages = array();

	public function __construct(){
		$this->viewVars = new \stdClass();
		$this->_pageSize = 40;
	}
	

	public function index(){

	}

	public function render(){

		// create local variables of all
		// properties in the viewVars object
		foreach($this->viewVars as $key=>$value){
		$$key = $value;
		}

		// build the name of the view script
		// based on controller class
		$cls = substr( get_class($this), strrpos(get_class($this), '\\')+1 );
		$scriptPath = 'App/View/'.strtolower($cls).'/'.strtolower($this->viewScript).'.php.html';
		require_once $scriptPath;
	}

	/**
	 * Return true if a POST request has been sent
	 */
	public function isPost(){
		return $_SERVER['REQUEST_METHOD'] === 'POST';
	}

	/**
	 * Returns the data from POST as an associative array or an empty array if no post data is available
	 * if $key is defined, returns the value of that key or an empty string if no value was found
	 * @return array [description]
	 */
	public function getPost($key=null){
		if($this->isPost()){
			// if key exists in post data, return value for that
			if($key != null && array_key_exists($key, $_POST)){
				return $_POST[$key];
			} else {
				// return entire post array
				return $_POST;
			}

		} else {
			if($key != null){
				return "";
			} else {
				return array();
			}

		}
	}

	/**
	 * Utility function to return any messages associated with a form field.
	 * This assumes that a controller has stored messages into the messages variable
	 * @param  [type] $fieldname [description]
	 * @return [type]            [description]
	 */
	public function getError($fieldname){
		if(array_key_exists($fieldname, $this->errorMessages)){
			$error = $this->errorMessages[$fieldname];
			// if the error message itself is an array, return the first message only for now
			if(is_array($error)){
				return $error[0];
			} else {
				return $error;
			}
		} else {
			return "";
		}
	}


	/**
	 * Redirects the browser to a new controller and action
	 *
	 * @param  [type] $controller Name of controller to redirect to
	 * @param  [type] $action     name of action in target controller. If left out or null, uses default index action
	 * @param  [type] $params     an associative array of URL parameters to add to the redirect or null if no params should be added
	 * @return [type]             [description]
	 */
	public function redirect($controller, $action=null, $params=null){

		// figure out the root folder of our app (the part after the domain name, but before the script name)
		$path = pathinfo($_SERVER["PHP_SELF"]);

		// redirecting is done using the header function in PHP.
		// The function takes a string parameter that defines the header to be sent, which must be formatted in a specifc way
		$redirectStr = 'Location: http://'.$_SERVER['HTTP_HOST'].$path["dirname"].'/index.php?controller='.$controller;

		// append the action to the URI if defined
		if($action != null && is_string($action)){
			$redirectStr .= '&action='.$action;
		}

		// append any parameters to the URI if defined
		if($params != null && is_array($params)){
			$redirectStr .= '&'.http_build_query($params);
		}

		// HTTP response codes (303 in this case) tell the browser what is happening and how it shoudl react.
		// See list of all response codes http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html.
		header($redirectStr, false, 303);

		// just to make sure we don't output anything after this which might cause problems
		exit;
	}
	
	
}
?>
