<?php

namespace App\Model;

abstract class AbstractMapper {
		
	
  public function __construct() {
    
     }
 	
  protected function connect()
  {

	try
	{
		$DBSettings = parse_ini_file( 'db_settings.ini', TRUE );
		$conn = new \PDO( $DBSettings['db_settings']['DB_DSN'], $DBSettings['db_settings']['DB_USERNAME'], 
						   $DBSettings['db_settings']['DB_PASSWORD'] );
		$conn->setAttribute( \PDO::ATTR_PERSISTENT, true );		
		$conn->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );

	} 
	catch ( PDOException $e )
	{
		die( "Connection failed: ". $e->getMessage() );				
	}	
				//echo "connection established!";	
	return $conn;
   }
		
	protected function disconnect( $conn )
	{
		$conn = "";		
	}
	
	}
