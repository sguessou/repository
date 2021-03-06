<?php
namespace App\Controller;


use App\Model\ProductTypesMapper;
use App\Model\ProductMapper;
use App\Model\ProductTypes;
use App\Model\Product;
use App\Util\Auth;
use App\Util\Session;
use App\Model\UserMapper;
use App\Model\AccessLogMapper;

class Admin extends AbstractController {

	public function index() 
	{
		$user = new UserMapper();
		$this->viewVars->user = NULL;
		
		$auth = new Auth();		
		$session = new Session();
		$this->viewVars->path = $session->get('path');	

		$this->viewVars->loggedIn = NULL;
		$this->viewVars->admin = NULL;
	
		if( $auth->isLogged() )
		{
			$this->viewVars->loggedIn = TRUE;
			$this->viewVars->msg = "Welcome Back";
			
				//Checks for admin credentials
				if( $auth->isAdmin( $auth->getLogin() ) )
				{
					$this->viewVars->admin = TRUE;
					$this->viewVars->user = $user->fetchUser( $auth->getLogin() );
				}
		}
		else { $auth->logout(); }
													
	}//End function index
			
		
	public function addproduct() 
	{
		$PTMapper = new ProductTypesMapper();
		$this->viewVars->MPTypes = $PTMapper->fetchMPTypes();	
		
		$auth = new Auth();		
		$session = new Session();
		$this->viewVars->path = $session->get('path');	

		$this->viewVars->loggedIn = NULL;
		$this->viewVars->admin = NULL;
	
		if( $auth->isLogged() )
		{
			$this->viewVars->loggedIn = TRUE;
			//$this->viewVars->msg = "Welcome Back !";
			
			//Checks for admin credentials
			if( $auth->isAdmin( $auth->getLogin() ) )
			{
				$this->viewVars->admin = TRUE;
			}
		}
		else { $auth->logout(); }										
	}
			
	public function saveproduct() 
	{
		$auth = new Auth();		
		$session = new Session();
		$this->viewVars->path = $session->get('path');	

		$this->viewVars->loggedIn = NULL;
		$this->viewVars->admin = NULL;
	
		if( $auth->isLogged() )
		{
			$this->viewVars->loggedIn = TRUE;
			$this->viewVars->msg = "Welcome Back !";
			
				//Checks for admin credentials
				if( $auth->isAdmin( $auth->getLogin() ) )
				{
					$this->viewVars->admin = TRUE;
				}
		 }
		 else { $auth->logout(); }							
		
			$PMapper = new ProductMapper();	
			$PMapper->insertProduct( new Product( 0, $_POST["ptype_id"], $_POST["title"], $_POST["price"], $_POST["language"],
																	 $_POST["description"], $_POST["author"], $_POST["isbn10"] ) );
			
	}	

	public function ptypes() 
	{
		$PTMapper = new ProductTypesMapper();
		
		$auth = new Auth();		
		$session = new Session();
		$this->viewVars->path = $session->get('path');	

		$this->viewVars->loggedIn = NULL;
		$this->viewVars->admin = NULL;
	
		if( $auth->isLogged() )
		{
			$this->viewVars->loggedIn = TRUE;
			//$this->viewVars->msg = "Welcome Back !";
			
				//Checks for admin credentials
				if( $auth->isAdmin( $auth->getLogin() ) )
				{
					$this->viewVars->admin = TRUE;
				}
		}						
		else { $auth->logout(); }
			
		if( isset( $_POST['type_name'] ) && $_POST['type_name'] )
		{
			$PTMapper->savePType( new ProductTypes( null, $_POST["parent_ptype_id"], $_POST["type_name"] ) );
			unset( $_POST['type_name'] );
		}	
		
		$this->viewVars->PTypes = $PTMapper->fetchPTypes();
	}	
	
		/**
   * var $logs is assigned array returned by class AccessLogMapper method getAll
   * @param none
   * @access public
   */	
		public function viewLog()
		{
			$auth = new Auth();		
			$session = new Session();
			$this->viewVars->path = $session->get('path');
			
			$this->viewVars->loggedIn = NULL;
			$this->viewVars->admin = NULL;
			
			if( $auth->isLogged() )
			{
				$this->viewVars->loggedIn = TRUE;
				
					//Checks for admin credentials
					if( $auth->isAdmin( $auth->getLogin() ) )
					{
						$this->viewVars->admin = TRUE;
					}
			}
			else { $auth->logout(); }
			
			$accessLogs = new AccessLogMapper();
			if( isset( $_POST['date'] ) )
			{
				$accessLogs->deleteLogs( $_POST['date'] );
			}
		
			if( isset( $_GET["start"] ) )
			{
				$this->viewVars->start  = (int)$_GET["start"];
				$this->viewVars->first = (int)$_GET["first"];
			}
			else
			{
				//initialize var first
				$this->viewVars->first = 1;
				$this->viewVars->start = 0;
			} 
			
			$this->viewVars->PAGE_SIZE = $this->_pageSize;
			list( $this->viewVars->logs, $this->viewVars->totalRows ) = $accessLogs->getAll( 'lastAccess', $this->viewVars->start, $this->_pageSize );
		
			
		}//End method viewLog	
		
		public function viewProducts()
		{
			$auth = new Auth();		
			$session = new Session();
			$this->viewVars->path = $session->get('path');
			
			$this->viewVars->loggedIn = NULL;
			$this->viewVars->admin = NULL;
			
			if( $auth->isLogged() )
			{
				$this->viewVars->loggedIn = TRUE;
				
					//Checks for admin credentials
					if( $auth->isAdmin( $auth->getLogin() ) )
					{
						$this->viewVars->admin = TRUE;
					}
			}
			else { $auth->logout(); }
			
			$PrdTypMapper = new ProductTypesMapper();
			$this->viewVars->PrdTypes = $PrdTypMapper->fetchPTypes();
			$this->viewVars->products = NULL;
			
			$PrdMapper = new ProductMapper();
			$PSize = 10;
			$this->viewVars->PAGE_SIZE = $PSize;
			
			if(isset($_POST['PType']))
			{		
				//initialize var first
				$this->viewVars->first = 1;
				$this->viewVars->start = 0;
				$this->viewVars->pt = $_POST['PType'];
				list( $this->viewVars->products, $this->viewVars->totalRows ) = $PrdMapper->fetchProducts((int)$_POST['PType'], 'product_id', $this->viewVars->start, $PSize);
				//unset($_POST);
				//return;
			}
			
			if( isset( $_GET["start"] ) )
			{
				$this->viewVars->start  = (int)$_GET["start"];
				$this->viewVars->first = (int)$_GET["first"];
				$this->viewVars->pt = $_GET['ptid'];
			    list( $this->viewVars->products, $this->viewVars->totalRows ) = $PrdMapper->fetchProducts((int)$_GET['ptid'], 'product_id', $this->viewVars->start, $PSize);
				//return;
			} 

		}//End method viewProduct
		
		/*
    *	This function updates product associated data in database. 
    *	@param: none
    *	@return: none
    */
		public function update_product()
		{
			$auth = new Auth();		
			$session = new Session();
			$this->viewVars->path = $session->get('path');
			
			$this->viewVars->loggedIn = NULL;
			$this->viewVars->admin = NULL;
			
			if( $auth->isLogged() )
			{
				$this->viewVars->loggedIn = TRUE;
				
					//Checks for admin credentials
					if( $auth->isAdmin( $auth->getLogin() ) )
					{
						$this->viewVars->admin = TRUE;
					}
			}
			else { $auth->logout(); }
			
			$PTMapper = new ProductTypesMapper();
			$this->viewVars->MPTypes = $PTMapper->fetchMPTypes();	
			
			$PrdMapper = new ProductMapper();
			
			$this->viewVars->product_data = NULL;
			
			//We get products id in order to display it.
			if(isset($_GET['product_id']))
			{
				$this->viewVars->product_data = $PrdMapper->getProduct((int)$_GET['product_id']);
			}
			
			$this->viewVars->updated = NULL;
			//If $_POST['product_id'] is set, the form has been submitted for update.
			if(isset($_POST['product_id']))
			{
				$var = $PrdMapper->update_product(new Product($_POST["product_id"], $_POST["ptype_id"], $_POST["title"], 
															  $_POST["price"], $_POST["language"], $_POST["description"], 
															  $_POST["author"], $_POST["isbn10"]));	
				if($var) 
				{
					$this->viewVars->updated = TRUE;
					$this->viewVars->product_data = $PrdMapper->getProduct((int)$_POST['product_id']);			
				}
			}		
		}//End method update_product
		
		/*
    *	This function displays users and their associated data. 
    *	@param: none
    *	@return: none
    */
		public function manage_users()
		{
			$auth = new Auth();		
			$session = new Session();
			$this->viewVars->path = $session->get('path');
			
			$this->viewVars->loggedIn = NULL;
			$this->viewVars->admin = NULL;
			
			if( $auth->isLogged() )
			{
				$this->viewVars->loggedIn = TRUE;
				
					//Checks for admin credentials
					if( $auth->isAdmin( $auth->getLogin() ) )
					{
						$this->viewVars->admin = TRUE;
					}
			}
			else { $auth->logout(); }
			
			$user_mapper = new UserMapper();
			$this->viewVars->users_data = $user_mapper->fetchUsers();
			
			$this->viewVars->user_data = NULL;
			if(isset($_GET['login']))
			{
				$this->viewVars->user_data = $user_mapper->fetchUser($_GET['login']);
			}
			
			
			
		}//End method manage_users				
	
 }//End Class Admin
