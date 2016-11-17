<?php

	include_once('Config.php');
 
	class DB
	{
		private $Server;
		private $User;
		private $Pwd;
		private $Name;
		private $db;
		private $objDBConn;
		private $RsId;
		private $RecordSet;
		private $SqlQuery;
		private $dataArray;
		private $actionPara;

		public function __construct()
		{
		
		  /////// DB Connection Details ///////////////
			
				 $this->Server 	= SQL_HOSTNAME;
				 $this->User 	= SQL_USERNAME;
				 $this->Pwd 	= SQL_PASSWORD;
				 $this->Name 	= SQL_DBNAME;
	
				 $this->db_connect();
		}

		public function db_connect()
		{
 
			$connectionInfo = array( 
								"Database"=>$this->Name, 
								"UID"=>$this->User,
								"PWD"=>$this->Pwd
							);
			$this->objDBConn = sqlsrv_connect( $this->Server, $connectionInfo);

			if( $this->objDBConn ) {
				// echo "Connection established.<br />";
			} else {
				 echo "Connection could not be established.<br />";
				 die( print_r( sqlsrv_errors(), true));
			}
			 
		}
		
		
		public function query($strQuery , $parraArray='' , $queryAction='numRows' )
		{
			$this->SqlQuery = $strQuery;
			$this->dataArray = ($parraArray==='') ? array() : $parraArray;
			 
			switch($queryAction)
			{
				case 'insertData':
					$this->actionPara = array();
				break;
				
				case 'numRows':
					$this->actionPara = array( "Scrollable" => 'static' );
				break;
				
				case 'lastInsertId':
					/* special Transact-SQL addition to the SQL insert statement. It will return the last insert ID */
					$this->SqlQuery .= "; SELECT SCOPE_IDENTITY() AS IDENTITY_COLUMN_NAME"; 
					$this->actionPara = array();
				break;
				
				default:
					$this->actionPara = array();
					$this->dataArray = array();
			}
			
			
			$rsId = sqlsrv_query( $this->objDBConn , $this->SqlQuery , $this->dataArray , $this->actionPara);
			
			$this->rsId = $rsId;
			
			if($rsId)
			{
				return $this->rsId;
			} else {
				$this->error();
			}
		}
		
		public function queryInsert($strQuery , $parraArray)
		{
			$this->SqlQuery = $strQuery;
			$this->dataArray = $parraArray;
 			
			$rsId = sqlsrv_query( $this->objDBConn , $this->SqlQuery, $this->dataArray);
		 
			$this->rsId = $rsId;
			
			if($rsId)
			{
				return true;
			} else {
				$this->error();
			}
		}
		
		
		
		public function queryUpdate($sql , $parraArray ='')
		{
			$this->SqlQuery = $sql;
			$this->dataArray = $parraArray;
			/*
			if(is_array($parraArray))
			{
				foreach($parraArray as $key=>$value)
				{
					$keyValue['key'] = $value;
					$this->dataArray[] = &$keyValue['key'];
				}//end foreach.
			}//end if.*/
			
			$stmt = sqlsrv_prepare( $this->objDBConn, $this->SqlQuery , $this->dataArray );
  		
			if( sqlsrv_execute( $stmt ) === false ) {
				 die( print_r( sqlsrv_errors(), true) );
			}
			else
			{
				return $stmt;
			}
		
		}
		
		
		public function fetch_array($rst)
		{
 			if($rst) { 
				$this->RecordSet  = sqlsrv_fetch_array($rst);
				return $this->RecordSet;
			} else { $this->error(); }
		}
		
		public function fetch_object($rst)
		{
 			if($rst) {  
				$this->RecordSet  = sqlsrv_fetch_object($rst);
				return $this->RecordSet;
			} else { $this->error(); }
		}
		
		
		public function fetch_assoc($rst)
		{
			if($rst) { 
				$this->RecordSet  = sqlsrv_fetch_array( $rst, SQLSRV_FETCH_ASSOC);
				return $this->RecordSet;
			} else { $this->error(); }
		}

		public function num_rows($rst)
		{
			if($rst) {	
				$num = sqlsrv_num_rows($rst);
				
				return $num;
			} else { $this->error(); }
		}
 		
		public function affected_rows($rst)
		{
			$rows_affected = sqlsrv_rows_affected( $rst );
			return $rows_affected;
		}
		
		public function insert_id($rst)
		{
			sqlsrv_next_result($rst);

			sqlsrv_fetch($rst);

			$this->rsId = sqlsrv_get_field($rst, 0);
			
			return $this->rsId;
		}
		
		/*public function real_escape_string($string)
		{
 			if(phpversion() >= '4.3')
  			{
				$string = mysqli_real_escape_string( $this->objDBConn , $string);
				
			} else {
				
				$string = mysqli_escape_string( $this->objDBConn , $string);
			}
			
			return $string;
		}*/
		
		public function error()
		{
			$errorMsg = sqlsrv_errors();
			
			if(!empty($errorMsg))
			{
				echo "<div class='errormsg'><strong>Mysql Error: </strong>";
				print_r($errorMsg);
				echo "</div>";
				echo "<div>".$this->SqlQuery."</div>";
				exit;
			}
		}
		
		public function free_result($rst)
		{
			 sqlsrv_free_stmt( $rst );
		}
		
		public function free($rst)
		{
			 sqlsrv_free_stmt( $rst );
			 
		}

		public function __destruct()
		{
			if($this->objDBConn)
			{
				foreach ($this as $key => $value) 
				 { 
					 unset($this->key); 
				 } 
			}
			$this->free( $this->rsId );
		}
		
	}//End Class DB
	
	
	
	
class Mysql
{
 	public $objDB;
	public $sqlQuery;
	
	public function __construct($objDB)
	{
		$this->objDB = $objDB;
	}
	
	public function setData($dataArray , $dbTableName, $action='insert', $updatePara='')
	{
		if(is_array($dataArray))
		{
			foreach($dataArray as $key=>$value)
			{
				switch($action)
				{
					case "insert":	
					
						$fieldName[] = $key;
						
						$fieldValue[] = $value;
						
						$QRef[] = ' ?';
					
					break;
					
					case "update":
					
						$fieldNameWithData[] = " $key = ?";
						
						$fieldValue[] = "&$value";
						
					break;
				
				}//end switch.
			
			}//end foreach.
			
			 
			switch($action)
			{
				case "insert":	
				
					$strFieldName  = join(', ', $fieldName);
					$strValueReff  = join(', ', $QRef);
					$parraArray = $fieldValue;
					
					$this->sqlQuery = "INSERT INTO ".$dbTableName." ( $strFieldName ) VALUES ( $strValueReff )";
					
					$rec = $this->objDB->queryInsert($this->sqlQuery , $parraArray );
					
					if($rec) {
						return true;
					} else {
						echo "<pre>";
						$this->objDB->error();
						echo "</pre>";
					}
				
				break;
				
				case "update":
					
					if(is_array($updatePara))
					{
						foreach($updatePara as $idFieldName=>$idFieldValue)
						{
							$fieldNameArr[] = " $idFieldName = ?";
							$fieldValue[] = $idFieldValue;
						}//end foreach.
					}
					$strUpdateFieldNameValue = join(', ', $fieldNameWithData);
					$strUpdateWhereClause = join(', ', $fieldNameArr);
					
					 $this->sqlQuery = "UPDATE ".$dbTableName." SET $strUpdateFieldNameValue WHERE $strUpdateWhereClause ";
					 
					 $rec = $this->objDB->queryUpdate( $this->sqlQuery , $fieldValue );
				
				if($rec) {
						return true;
					} else {
						echo "<pre>";
						$this->objDB->error();
						echo "</pre>";
					}
				break;
			
			}//end switch.
 			
		}//end if.
	
	}//end method function.
	 

/*
	public function setSqlDataMultiple($dataArrayMulty , $dbTableName, $action='insert', $idFieldName='', $idFieldValue='')
	{
	
	 
	if(is_array($dataArrayMulty))
	{
		//print_r($dataArrayMulty);
	
		foreach($dataArrayMulty as $multiKey=>$dataArray)
		{
			$arrayLevel[] = $multiKey;
			
			foreach($dataArray as $key=>$value)
			{
				switch($action)
				{
					case "insert":	
					
						$fieldName[$multiKey][] = $key;
						
						$fieldValue[$multiKey][] = "'$value'";
					
					break;
					
					case "update":
					
						$fieldNameWithDate[$multiKey][] = "$key = '$value' ";
						
					break;
				
				}//end switch.
			
			}//end foreach.
			
		}//end foreach outer.	
			
			switch($action)
			{
				case "insert":	
				
				foreach($arrayLevel as $key=>$arrid)
				{
					$strFieldValue[] = '('. join(',', $fieldValue[$arrid]) . ')';
					$strFieldName  = join(',', $fieldName[$arrid]);
				}
					
				$sqlQuery = "INSERT INTO ".$dbTableName." ( $strFieldName ) VALUES ". join( ' , ' , $strFieldValue );
			
				$rec = $this->objDB->query($sqlQuery);
			
				break;
				
				case "update":
					
					foreach($idFieldValue as $key=>$updateId)
					{
						$strUpdateFieldNameValue = join(',', $fieldNameWithDate[$key]);
					
						$sqlQuery = "UPDATE ".$dbTableName." SET $strUpdateFieldNameValue WHERE $idFieldName = '$updateId' ";
						
						$rec = $this->objDB->query($sqlQuery);
					}
					
					
				break;
			
			}//end switch.
			
			 
			
			
			return $rec;
			
		}//end if.
	
	
	}
*/



	/************************************************************************
	//
	// PARA 2: resultType: 
	//				RecordArray :(Return Selected Records in Associate Array.) 
	//				Record : (Return single Record in Associate Array.) 
	//				Count : (Return count of selected record by query.)
	// PARA 1: Sql Query
	// RETURN: Return data as per mention PARA 2 Option.
	//
	**************************************************************************/
	public function getSqlData( $sqlQuery , $resultType='RecordArray' )
	{
		 if($sqlQuery!='')
		 {
			 $this->sqlQuery = $sqlQuery;
		 }
		 
		if($this->sqlQuery != "")
		{
			$rec = $this->objDB->query($sqlQuery);
			 
			if($rec)
			{
				if($num=$this->objDB->num_rows($rec))
				{
					switch($resultType)
					{
						//return only count of record selected
						case "Count":
						
							return $num;
							
						break;
						
						//Return single record
						case "Record":
						
						return	$row = $this->objDB->fetch_assoc($rec);
						
						break;
						
						//return multiple records in array format
						case "RecordArray":
						
							while($row = $this->objDB->fetch_assoc($rec))
							{
								$rowArray[] = $row;

							}//end while.
						
							return $rowArray;
							
						break;
					
					}//end switch.
				
				}//end if num.
				else
				{
					$data['error'] = "&Delta; No record found.";
					$data['result'] = false;
					$data['record'] = 'Not Found';
					return $data;
				}//end else.
			}//end if.
			else
			{
				$data['error'] = "&Delta; Sql query error.";
				$data['result'] = 'error';
				
				return $data;
			}//end else.
		}//end if.

	}//end method.
	
	
	public function generateSelectQuery($dbTableName , $tableFields='*', $whereCondition='', $orderBy='', $groupBy='', $limit=20 )
	{
		 
		if(!empty($tableFields) && is_array($tableFields))
		{
			$sqlFields = join(', ', $tableFields);
			
		}//end if.
		else{
			$sqlFields = " * ";
		}//end else.
		
		
		if($whereCondition != '')
		{
			$sqlWhere = $whereCondition;
		}//end if.
		
		
		if($orderBy != '')
		{
			$sqlOrderBy = $orderBy;
		}//end if.
		
		if($groupBy != '')
		{
			$sqlOrderBy =  $groupBy;
		}//end if.
		
		if($limit != '')
		{
			$sqlTop =  " TOP $limit ";
		}//end if.
		
		$sql = "SELECT $sqlTop $sqlFields FROM $dbTableName WHERE $sqlWhere $sqlOrderBy";
		
		return $sql;
		
	}
        
          public function  FetchField($param){
            $table = isset($param["table"])?$param["table"]:'';
            $fetchField = isset($param["fetchField"])?$param["fetchField"]:'';
            $matchField = isset($param["matchField"])?$param["matchField"]:'';
            $matchValue = isset($param["matchValue"])?$param["matchValue"]:'';
            
            if(!empty($table) && !empty($fetchField) && !empty($matchField) && !empty($matchValue)){
                $sql =" select $fetchField from $table where $matchField = $matchValue " ;
                    $row = $this->getSqlData( $sql , $resultType='RecordArray' );
		if(is_array($row) && count($row)){
			 foreach($row as $res){
				 return $res[$fetchField];
			 }//end foreach.
			 
                }
            }
            return false;
        }


}	
	
	
?>