<?php

// Development Configuration
define('WEBCHAT_EMAIL_DBCONFIG_SERVER', 'ds1-accsmm');
define('WEBCHAT_EMAIL_DBCONFIG_USER', 'accsmmcorp');
define('WEBCHAT_EMAIL_DBCONFIG_PASSWORD', 'eciovllet');
define('WEBCHAT_EMAIL_DBCONFIG_DBNAME', 'ds1-accsmm/ORCL');
/*
// Production Configuration
    define('WEBCHAT_EMAIL_DBCONFIG_SERVER', 'ds1-accsmm');
    define('WEBCHAT_EMAIL_DBCONFIG_USER', 'workflow_app');
    define('WEBCHAT_EMAIL_DBCONFIG_PASSWORD', 'password');
    define('WEBCHAT_EMAIL_DBCONFIG_DBNAME', 'ds1-accsmm/accsmdb');                //      primary
//  define('WEBCHAT_EMAIL_DBCONFIG_DBNAME', 'ds2-accsmm/accsmdb');                //      standby
*/

class CDatabase{
	

	
	private $connection = null;
	private $host = WEBCHAT_EMAIL_DBCONFIG_SERVER;
	private $databaseName = WEBCHAT_EMAIL_DBCONFIG_DBNAME;
	private $username = WEBCHAT_EMAIL_DBCONFIG_USER;
	private $password = WEBCHAT_EMAIL_DBCONFIG_PASSWORD;

	function setConnection($host, $databaseName, $username, $password){

		$this->host = $host;
		$this->databaseName = $databaseName;
		$this->username = $username;
		$this->password = $password;
	}

	function Connect(){
		try{
			$this->connection =  oci_connect($this->username, $this->password, $this->databaseName,'AL32UTF8');
		}catch(Exception $e){}
		return $this->connection;
	}

	function Disconnect(){
		//oci_close($this->connection);
		$this->connection = null;
	}

	// i2 for update OraDB
	function UpdateORADB($sqlString) {
			if($this->connection){
					try{
						$stmt = oci_parse($this->connection, "ALTER SESSION SET CURRENT_SCHEMA = ACCSMMCORP");
						oci_execute($stmt);
						$stmt = oci_parse($this->connection, $sqlString);
						oci_execute($stmt);
					}catch(Exception $e){
						return $e;
					}
		}
	}

	function ExecuteNonQuery($sqlString, $value){
		if($this->connection){
			try{

				$stmt = oci_parse($this->connection, "ALTER SESSION SET CURRENT_SCHEMA = ACCSMMCORP");
				oci_execute($stmt);

				$stmt = oci_parse($this->connection, $sqlString);
				foreach($value as $key => $data){
					try {
						oci_bind_by_name($stmt, $key, $value[$key]);	
					} catch (Exception $e) {
						print_r( $e->getMessage());		
					}
					
				}

				oci_execute($stmt);
				return true;
			}catch(Exception $e){
				return $e->getMessage();
			}
		}
	}

	function InsertThenReturnLastId($sqlString, $value){
		if($this->connection){
			try{

				$stmt = oci_parse($this->connection, "ALTER SESSION SET CURRENT_SCHEMA = ACCSMMCORP");
				oci_execute($stmt);

				$stmt = oci_parse($this->connection, $sqlString );
				foreach($value as $key => $data)
				{
					oci_bind_by_name($stmt, $key, $value[$key]);	
				}
				oci_bind_by_name($stmt, ":ID", $id, 32);
				
				oci_execute($stmt);
				
				return $id;
				
			}catch(Exception $e){
				write_log('Exception error near line '.(__LINE__).': "'. PHP_EOL . $e->getMessage().'"' . PHP_EOL . $sqlString, 'error_InsertThenReturnLastId_GMCallerController');
			}
		}
	}

	function ExecuteReader($sqlString, $value){
		if($this->connection){
			try{

				$stmt = oci_parse($this->connection, "ALTER SESSION SET CURRENT_SCHEMA = ACCSMMCORP");
				oci_execute($stmt);

				$stmt = oci_parse($this->connection, $sqlString);
				foreach($value as $key => $data){
					oci_bind_by_name($stmt, $key, $value[$key]);
				}
				oci_execute($stmt);
				//$nrows = oci_fetch_all($stmt, $res, null, null, OCI_ASSOC);
				//return $res;

				$rows = oci_fetch_array($stmt, OCI_ASSOC);
				if(count($rows) > 0){
					if(!is_array(@$rows[0])){
						$rows = array($rows);
					}
				}

				return $rows;

			}catch(Exception $e){
				return false;
			}
		}
	}

	function ExecuteReaderMoreRows($sqlString, $value){
//print "<br/>in ExecuteReaderMoreRows<br/>";
		if($this->connection){
			try{

				$stmt = oci_parse($this->connection, "ALTER SESSION SET CURRENT_SCHEMA = ACCSMMCORP");
				oci_execute($stmt);

				$stmt = oci_parse($this->connection, $sqlString);
				foreach($value as $key => $data){
//print $key." = ".$value[$key]."<br/>";
					oci_bind_by_name($stmt, $key, $value[$key]);
				}
				oci_execute($stmt);
				$nrows = oci_fetch_all($stmt, $res, null, null, OCI_ASSOC);
//print "ExecuteReaderMoreRows done!!!!<br/>---------------------------------------------<br/>";
				return $res;

			}catch(Exception $e){
				return false;
			}
		}
	}

	function ExecuteReader2($sqlString, $value){
		if($this->connection){
			try{

				$stmt = oci_parse($this->connection, "ALTER SESSION SET CURRENT_SCHEMA = ACCSMMCORP");
				oci_execute($stmt);

				$stmt = oci_parse($this->connection, $sqlString);
				foreach($value as $key => $data)
				{	
					oci_bind_by_name($stmt, $key, $value[$key]);	
				}
				oci_execute($stmt);

				$nrows = oci_fetch_all($stmt, $res, null, null, OCI_FETCHSTATEMENT_BY_ROW | OCI_ASSOC);

				return $res;

			}catch(Exception $e){
				print_r($e->getMessage());
			}
		} 

	}

}



?>