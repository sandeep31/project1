<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

global $db;

class Models {

    protected $conn = '';
    //----------------DB-----------------
    private $db_user       = 'root';
    private $db_password   = '';
    private $db_host       = 'localhost';
    private $db_name       = 'greatweb_salestracker_v1';
    private $metaTable     = array();
    private $UploadPath    =  '';
    private $FilePath      =  '';
    private $FilePathRel   =  '';
    private $DocumentUrl   =  'http://salestracker.greatwebsoft.local';

    private $AuthTable     = 'auth_token';
    private $employeeTbl   = 'employee';
    
    public function __construct() {
        $this->UploadPath = $_SERVER['DOCUMENT_ROOT']."/uploads/"; 
        global $db;
        if (empty($db)):
            $this->conn = $db = new mysqli($this->db_host, $this->db_user, $this->db_password, $this->db_name);
        endif;
        $this->MetaTables($param=NULL);
        $this->dircreation();
    }
    
    public function dircreation(){
        $uploadPath = $this->UploadPath ;
        $Y = date("Y");
        $M = date("m");
        if (!is_dir($uploadPath . $Y)):
            mkdir($uploadPath . $Y, 0777, true);
        endif;

        if (!is_dir($uploadPath . $Y . '/' . $M)):
            mkdir($uploadPath . $Y . '/' . $M, 0777, true);
        endif;

        if (is_dir($uploadPath . $Y . '/' . $M)):
            global $currentUploadPath;
            global $fileDirPath;
            $currentUploadPath = $uploadPath . $Y . '/' . $M;
            $fileDirPath = '/' .$Y . '/' . $M;
            $this->FilePath = $currentUploadPath;
            $this->FilePathRel = $fileDirPath;
        endif;
    }
    
    public function MetaTables($param=NULL) {
        $arr = array();
        $arr['employee'] = 'employee_meta';
        $arr['client']   = 'client_meta';
        $arr['order']    = 'order_meta';
        $arr['payment']  = 'payment_meta';
        $arr['task']     = 'task_meta';
        $this->metaTable = $arr;
    }
    function __destruct() {
        
    }

    public function custom_sanitize($param = array()) {
        $res = array();
        if (count($param) > 0):
            foreach ($param as $key => $value) {
                $res[$key] = addslashes($value);
            }
        endif;
        return $res;
    }

    public function convert_to_json($param = array()) {
        return json_encode($param);
    }

    public function query($query) {
        $db = $this->conn;
        $result = $db->query($query);

        while ($row = $result->fetch_object()) {
            $results[] = $row;
        }

        return $results;
    }

    public function insert($table, $data, $format) {
        // Check for $table or $data not set
        if (empty($table) || empty($data)) {
            return false;
        }

        // Connect to the database
        $db = $this->conn;

        // Cast $data and $format to arrays
        $data = (array) $data;
        $format = (array) $format;

        // Build format string
        $format = implode('', $format);
        $format = str_replace('%', '', $format);

        list( $fields, $placeholders, $values ) = $this->prep_query($data);

        // Prepend $format onto $values
        array_unshift($values, $format);
        // Prepary our query for binding
        $stmt = $db->prepare("INSERT INTO {$table} ({$fields}) VALUES ({$placeholders})");
        // Dynamically bind values
        call_user_func_array(array($stmt, 'bind_param'), $this->ref_values($values));

        // Execute the query
        $stmt->execute();

        // Check for successful insertion
        if ($stmt->affected_rows) {
            return $stmt->insert_id;
        }

        return false;
    }

    public function update($table, $data, $format, $where, $where_format) {
        // Check for $table or $data not set
        if (empty($table) || empty($data)) {
            return false;
        }

        // Connect to the database
        $db = $this->conn;

        // Cast $data and $format to arrays
        $data = (array) $data;
        $format = (array) $format;

        // Build format array
        $format = implode('', $format);
        $format = str_replace('%', '', $format);
        $where_format = implode('', $where_format);
        $where_format = str_replace('%', '', $where_format);
        $format .= $where_format;

        list( $fields, $placeholders, $values ) = $this->prep_query($data, 'update');

        //Format where clause
        $where_clause = '';
        $where_values = '';
        $count = 0;

        foreach ($where as $field => $value) {
            if ($count > 0) {
                $where_clause .= ' AND ';
            }

            $where_clause .= $field . '=?';
            $where_values[] = $value;

            $count++;
        }
        // Prepend $format onto $values
        array_unshift($values, $format);
        $values = array_merge($values, $where_values);
        // Prepary our query for binding 
        $stmt = $db->prepare("UPDATE {$table} SET {$placeholders} WHERE {$where_clause}");

        // Dynamically bind values
        call_user_func_array(array($stmt, 'bind_param'), $this->ref_values($values));

        // Execute the query
        $stmt->execute();

        // Check for successful insertion
        if ($stmt->affected_rows) {
            return true;
        }

        return false;
    }

    public function select($query, $data, $format) {
        
        // Connect to the database
        $db = $this->conn;

        //Prepare our query for binding
        $stmt = $db->prepare($query); 
        if(!$stmt){
            return FALSE;
        }
        //Normalize format
        $format = implode('', $format);
        $format = str_replace('%', '', $format);

        // Prepend $format onto $values
        array_unshift($data, $format);
        //Dynamically bind values
        call_user_func_array(array($stmt, 'bind_param'), $this->ref_values($data));

        //Execute the query
        $stmt->execute(); 
        //Fetch results
        $result = $stmt->get_result();
        $results = array();
        //Create results object
        while ($row = $result->fetch_assoc()) {
            $results[] = $row;
        } 
        return $results;
    }

    public function delete($table, $id) {
        // Connect to the database
        $db = $this->conn;

        // Prepary our query for binding
        $stmt = $db->prepare("DELETE FROM {$table} WHERE ID = ?");

        // Dynamically bind values
        $stmt->bind_param('d', $id);

        // Execute the query
        $stmt->execute();

        // Check for successful insertion
        if ($stmt->affected_rows) {
            return true;
        }
    }

    private function prep_query($data, $type = 'insert') {
        // Instantiate $fields and $placeholders for looping
        $fields = '';
        $placeholders = '';
        $values = array();

        // Loop through $data and build $fields, $placeholders, and $values			
        foreach ($data as $field => $value) {
            $fields .= "{$field},";
            $values[] = $value;

            if ($type == 'update') {
                $placeholders .= $field . '=?,';
            } else {
                $placeholders .= '?,';
            }
        }

        // Normalize $fields and $placeholders for inserting
        $fields = substr($fields, 0, -1);
        $placeholders = substr($placeholders, 0, -1);

        return array($fields, $placeholders, $values);
    }

    private function ref_values($array) {
        $refs = array();
        foreach ($array as $key => $value) {
            $refs[$key] = &$array[$key];
        }
        return $refs;
    }

    public function getFieldType($params = array()){ 
        $ConvertedArray = array();
        $i =0;
        foreach($params as $param)  {
            $i++; 
            if (is_int($param)) {
                // Integer
                $types = 'i';
            } elseif (is_float($param)) {
                // Double
                $types = 'd';
            } elseif (is_string($param)) {
                // String
                $types = 's';
            } else {
                // Blob and Unknown
                $types = 'b';
            }
          $ConvertedArray[] = $types;  
        } 
       return  $ConvertedArray;
    }
    
    public function prepare_where_clause($arr = array()){
        $FieldString = "";
        $ValueString = "";
        foreach ($arr as $key => $value) {
            $ResArray[$key] = $value;
            if(!empty($key)){
                $FieldString = $FieldString . " and ".$key ." = ? ";
                $ValueString = $ValueString . ",".$value;
            }
        } 
        return array('fileds'=>$FieldString,'values'=>$ValueString);
    }
    
    public function GetMetaData($suffix='',$ref='',$refID,$metakey='',$metaVal='') {
        if(empty($suffix) || empty($ref) || empty($refID)){
            return false;
        }
        $MetaKeyField = $suffix."_meta_key";
        $MetaValField = $suffix."_meta_val";
        $_MetaTable   = isset($this->metaTable[$suffix])?$this->metaTable[$suffix]:'';
        if(empty($_MetaTable) ){
            return false;
        }
        $SearchArr     = array();
        $SearchArr[$ref] = $refID;
        if($metakey){  $SearchArr[$MetaKeyField] = $metakey; }
        if($metaVal){  $SearchArr[$MetaValField] = $metaVal; }
        
        if(count($SearchArr) >0 ){
            $ParseInput = $this->prepare_where_clause($SearchArr); 
            $filedsStr = isset($ParseInput['fileds']) && !empty($ParseInput['fileds'])?$ParseInput['fileds']:'';
            $valuesStr = isset($ParseInput['values']) && !empty($ParseInput['values'])?$ParseInput['values']:'';
            $filedsType =  $this->getFieldType($SearchArr);
            $result   =  array(); 
        }
        $query = "select * from ".$_MetaTable." where   1=1  [WEHER_STATMENT] " ; 
        if(!empty($filedsStr) && !empty($valuesStr) && is_array($filedsType)){ 
            $query = str_replace("[WEHER_STATMENT]", $filedsStr, $query);
            $filedsType = array_merge($filedsType );  
            $res =    $this->select($query, $SearchArr,$filedsType );
            if($res):
              return $res;
            endif;
        }
        $result['error'] = "Not found";
        return $this->convert_to_json($result); 
   }
   
    public function BuiltCsv($param=array(),$header=Null){
       $fileName =  time()."_".uniqid().".csv";
       $file = $this->FilePath."/".$fileName;
       if(count($param)==0){
           return false;
       }
       $file = fopen($file,"w");
       if($header){
           $keys = array_keys($param[0]);
           fputcsv($file,$keys);
       }
       foreach ($param as $row) {
           fputcsv($file,$row);
       }
       fclose($file);
       return $this->DocumentUrl.$this->FilePathRel."/".$fileName;
   }
   
    public function ImportCsv(){
        $arr = array();
        if(empty($_FILES["csvfile"]["name"])){
             $arr['error'] = "file is not uploaded" ;
             return $arr;
        }
        $fileName = date("dmY").'_'.time().'_'.$_FILES["csvfile"]["name"];
        if (file_exists($this->FilePath."/".$fileName)) {
            return $arr['error'] = " already exists. ";
             return $arr;
        }else{
            $upload = move_uploaded_file($_FILES["csvfile"]["tmp_name"], $this->FilePath."/".$fileName);
            if($upload){
                $arr["filename"] =  $this->FilePath."/".$fileName;
                $arr["fileData"] =  array();
                $file = fopen($arr["filename"],"r");
                while(! feof($file)){
                  $arr["fileData"][] = fgetcsv($file);
                }
                array_shift($arr["fileData"]);
                fclose($file);
                return $arr;
            }
        }  
    }
   
   
    public function GetEmploeeDetails($arr= array()){
        $db = $this->conn; 
        $ParseInput = $this->prepare_where_clause($arr); 
        $filedsStr = isset($ParseInput['fileds']) && !empty($ParseInput['fileds'])?$ParseInput['fileds']:'';
        $valuesStr = isset($ParseInput['values']) && !empty($ParseInput['values'])?$ParseInput['values']:'';
        $filedsType =  $this->getFieldType($arr);
        
        $is_deleted = 0;
        $result   =  array(); 
        $query = "select "
                . "employee_id,employee_code,employee_fname,employee_lname,employee_email,employee_role_id,"
                . "employee_mobile_no,employee_phone_no,employee_is_active "
                . "from ".$this->employeeTbl." where   `employee_is_deleted` = ?   " ; 
        if(!empty($filedsStr) && !empty($valuesStr) && is_array($filedsType)){
          $query = $query.   $filedsStr;
        }
             if(!empty($filedsStr) && !empty($valuesStr) && is_array($filedsType)){
                 $arr        = array_merge(array($is_deleted),$arr);  
                 $filedsType = array_merge(array('i'),$filedsType );  
                   $res =    $this->select($query, $arr,$filedsType );
                   return isset($res[0]) && is_array($res[0]) ? $this->convert_to_json($res[0]):'';
              }
         $result['error'] = "Not found";
         return $this->convert_to_json($result);   
    }
    
    public function GetTokenStatus($auth_token=null){
        $db = $this->conn;
        $param              = $_REQUEST; 
        $param              = $this->custom_sanitize($param); 
        $auth_token    = isset($param['auth_token']) && !empty($param['auth_token']) ?$param['auth_token']:$auth_token;  
        $result             = array(); 
        $query = "select auth_user_id   from ".$this->AuthTable." where `auth_token_expire` > ? "
                . "AND `auth_token` = ? "
                . "AND `auth_token_status` = ? " ; 
         try{
                
                   if($stmt = $db->prepare($query)):
                    $active         = 'active';
                    $TokenCheckTime = date("Y:m:d H:i:s");
                    $stmt->bind_param('sss', $TokenCheckTime, $auth_token,$active);
                    $stmt->execute();
                    $stmt->store_result();
                    $stmt->bind_result($auth_user_id);
                    $stmt->fetch();
                    //var_dump($this->preparedQuery($query,array($TokenCheckTime, $auth_token,$active)));
                    if((int)$auth_user_id ==0){
                        $result['error'] = "invalid Token";
                        return $this->convert_to_json($result);
                    } 
                    else{
                        $result['status'] = "active";
                        $result['token_user_id'] = $auth_user_id;
                        return $this->convert_to_json($result);
                    }
                    
                    
                    $stmt->close(); 
                endif;
                
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
         $result['error'] = "invalid Token";
         return $this->convert_to_json($result);
    }
}
