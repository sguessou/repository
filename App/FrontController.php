<?php
namespace App;

// import used class namespaces

use App\Util\Session;


class FrontController{
	
	public $siteTitle = "Online Store";
	public $path;
	
	// contains the parsed content
	// for a certain page before output
	protected $_pageContent;
	
	// the name of the controller 
	// class we will create
	protected $_controllerName;
	// name of the action to call on the controller
	protected $_actionName;
	
	public function __construct(){
		
		spl_autoload_register( array($this, 'classLoader') );
	}
	
	protected function setController($name){
		$cls = ucfirst( strtolower($name) );
		$this->_controllerName = 'App\Controller\\'.$cls;
	}
	
	protected function setAction($name){
		$reflection = new \ReflectionClass($this->_controllerName);
		if($reflection->hasMethod($name)){
			$this->_actionName = $name;
		} else {
			throw new \Exception($name.' is not defined in the class '.$this->_controllerName);
		}
	}
	
	/**
	 * Called to parse the request and output a page
	 */
	public function run(){
		
		$session = new Session();
		date_default_timezone_set('Europe/Helsinki');	
		//session_start();
		
		//Store the www path in $path & in session, the var will be used as prefix to locate css file
		$this->path = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);
		$session->set( 'path', $this->path );
		
		$controller = isset($_GET["controller"]) ? $_GET["controller"] : 'index';
		$this->setController($controller);
		//die($this->_controllerName);
		$action = isset($_GET['action']) ? $_GET['action'] : 'index';
		$this->setAction($action);
		
		// start buffering the output
		ob_start();
		
		// create new controller instance and render
		$controllerObj = new $this->_controllerName();
		$controllerObj->viewScript = $this->_actionName;
		
		call_user_func_array(array($controllerObj,$this->_actionName),array());
		//$controllerObj->index();
		
		
		$controllerObj->render();
		
		
		$this->_pageContent = ob_get_contents();
		ob_end_clean();
		require_once 'App/View/layout.php.html';
	}
	
	/**
	 * Outputs the content for the current page
	 */
	public function getContent(){
		echo $this->_pageContent;
	}
	
	/**
	 * Loads the class definition for a requested class, 
	 * provided that it follows the same folder structure
	 * as the namespace indicates.
	 */	
	protected function classLoader($className){
		$classFile = str_replace('\\', '/', $className).'.php';
		//echo 'Loading '.$classFile.' via '.__METHOD__.'()<br \>';
		require_once($classFile);
		//die(var_dump($classFile));
	}
}
