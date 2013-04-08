<?php

namespace App\Model;

class ProductTypesMapper extends AbstractMapper {

protected $_productTypes = array();

protected $_conn;

public function __construct() {
			$this->_conn = parent::connect(); 
}

public function fetchTypeName( $ptype_id ) {

	$stmt = $this->_conn->prepare( "SELECT type_name FROM product_types WHERE ptype_id = :ptype_id" );
	$stmt->bindParam( ":ptype_id", $ptype_id );
	
	if( $stmt->execute() ) {
			return $row = $stmt->fetch();
				
		} else {
			print_r( $stmt->errorInfo() );
			return null;
		}
	
}
	
public function fetchPTypes() {
		$this->_productTypes = array();
		$sql = "SELECT * FROM product_types"; 
		$dbh = $this->_conn->query( $sql);
	
		while ( $row = $dbh->fetch() )	{
				$this->_productTypes[] = new ProductTypes( $row["ptype_id"], $row["parent_ptype_id"], $row["type_name"] );
		  }	
		 
		return $this->_productTypes;
	}
	
/*
*	This function gets the main product types. The main product types are the ones who's parent_ptype_id 
*	is set to NULL.
*  	@param	none
*	@return	$this->_productTypes	array containing product types data.  
*/	
public function fetchMPTypes() {
		
		$this->_productTypes = array();
		$sql = "SELECT * FROM product_types WHERE parent_ptype_id IS NULL"; 
		$dbh = $this->_conn->query( $sql);
	
		while ( $row = $dbh->fetch() )	{
				$this->_productTypes[] = new ProductTypes( $row["ptype_id"], $row["parent_ptype_id"], $row["type_name"] );
		  }	
		 
		return $this->_productTypes;
	}
	
	
public function savePType( ProductTypes $PType ) {
	  
	   $stmt = $this->_conn->prepare("INSERT INTO product_types( parent_ptype_id, type_name ) 	VALUES ( :parent_ptype_id, :type_name )" );
		( empty( $PType->parent_ptype_id ) )	? $PType->parent_ptype_id = null : $PType->parent_ptype_id;	
		$stmt->bindParam( ":parent_ptype_id", $PType->parent_ptype_id );	
		$stmt->bindParam( ":type_name", $PType->type_name );
		
		if( !$stmt->execute() ) {
			print_r( $stmt->errorInfo() );
			return null;
		}
	}

}
