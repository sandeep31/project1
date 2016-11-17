<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ClientModel extends Models {
    private $table1     = 'client';
    private $MetaSuffix = 'client';  // field suffix
    private $MetaRef    = 'client_id'; //Ref field
    
    public function __construct() {
        parent::__construct();
    }
    
    /**/
    public function CreateClient(){
        $db = $this->conn;
        $param            = $_REQUEST; 
        $param            = $this->custom_sanitize($param); 
        $auth_token       = isset($param['auth_token']) && !empty($param['auth_token']) ?$param['auth_token']:''; 
        $client_name      = isset($param['client_name']) && !empty($param['client_name']) ?$param['client_name']:'';  
        $client_mobile_no = isset($param['client_mobile_no']) && !empty($param['client_mobile_no']) ?$param['client_mobile_no']:'';  
        $client_phone_no  = isset($param['client_phone_no']) && !empty($param['client_phone_no']) ?$param['client_phone_no']:'';  
        $client_email     = isset($param['client_email']) && !empty($param['client_email']) ?$param['client_email']:'';  
        $client_address   = isset($param['client_address']) && !empty($param['client_address']) ?$param['client_address']:'';   
        $client_lat       = isset($param['client_lat']) && !empty($param['client_lat']) ?$param['client_lat']:'';   
        $client_lan       = isset($param['client_lan']) && !empty($param['client_lan']) ?$param['client_lan']:'';   
        
        $ExchangeArray = array();
        $ExchangeArray["client_name"]         = $client_name;
        $ExchangeArray["client_mobile_no"]    = $client_mobile_no;
        $ExchangeArray["client_phone_no"]     = $client_phone_no;
        $ExchangeArray["client_email"]        = $client_email;
        $ExchangeArray["client_address"]      = $client_address;  
        $ExchangeArray["client_lat"]          = $client_lat;  
        $ExchangeArray["client_lan"]          = $client_lan;  
        $email_val          = $this->UniqueEmail($client_email,$ID=Null); 
        if(!empty($email_val) ){
            $result['error'] = "Email-ID  already exist";
            return $this->convert_to_json($result);
        }
        if(!empty($auth_token) && !empty($client_name) &&  !empty($client_mobile_no) && !empty($client_email) && !empty($client_address) &&  !empty($client_lat) &&  !empty($client_lan) ):
        $TokenDetails  = $this->GetTokenStatus($auth_token); 
        $TokenDetailsObj = json_decode($TokenDetails);
        if(!isset($TokenDetailsObj->token_user_id)){
             $result['error'] = "invalid Token";
              return $this->convert_to_json($result);
        }else{
            $EmpID   = $TokenDetailsObj->token_user_id;
            $ExchangeArray["created_by"]      = $EmpID;                      
           
            $CreatedClientId  = $this->insert($this->table1, $ExchangeArray, array('%s','%s','%s','%s','%s','%s','%s','%i'));
            if((int) $CreatedClientId == 0):
               $result['error'] = "Unable to  insert the record";
               $result['error_code'] = "1004";
               return $this->convert_to_json($result);
            endif;
            $result['success'] = "inserted the record successfully"; 
            $result['client_id'] = $CreatedClientId; 
            return $this->convert_to_json($result);
        }
        else :
            $result['error'] = "Mandetary Fields are not supplied";
            $result['error_code'] = "1006";
            return $this->convert_to_json($result);
        endif;
        
        $result['error'] = "invalid Token";
        return $this->convert_to_json($result);
    }
    
    
    /**/
    public function search_field() {
       return array("client_name","client_mobile_no","client_email","client_email","client_address");
    }
    
    
    public function update_field() {
       return array("client_name","client_mobile_no","client_phone_no","client_address","client_lat","client_lan");
    }
    
    
    /**/
    public function GetClientList(){
        $db = $this->conn;
        $param         = $_REQUEST; 
        $param         = $this->custom_sanitize($param); 
        $search_field_array = (array)$this->search_field();
        //---------------------------------limit Option--------------------------//
        $auth_token    = isset($param['auth_token']) && !empty($param['auth_token']) ?$param['auth_token']:''; 
        //---------------------------------limit Option--------------------------//
        $fetch_limit   = isset($param['fetch_limit']) && !empty($param['fetch_limit']) ?$param['fetch_limit']:'50'; 
        $fetch_offset  = isset($param['fetch_offset']) && !empty($param['fetch_offset']) ?$param['fetch_offset']:'0'; 
        //---------------------------------Order By--------------------------//
        $orderby_field = isset($param['orderby_field']) && !empty($param['orderby_field']) ?$param['orderby_field']:'client_id'; 
        $orderby_dir   = isset($param['orderby_dir']) && !empty($param['orderby_dir']) ?$param['orderby_dir']:'desc'; 
        
        //-------------------------------------OutPut----------------------------------------------------------------- 
        $OutPut   = isset($param['export']) && !empty($param['export']) ?'csv':'json'; 
        
        $SearchArr     = array();
        foreach ($param as $key => $value) {
            if (in_array($key, $search_field_array) && !empty($value)) {
                $SearchArr[$key]= $value;
            }
        }
        $SearchArr['client_is_deleted'] = 0; 
        if(!empty($auth_token) ):
            $TokenDetails  = $this->GetTokenStatus($auth_token); 
            $TokenDetailsObj = json_decode($TokenDetails);
            if(!isset($TokenDetailsObj->token_user_id)){
                 $result['error'] = "invalid Token";
                  return $this->convert_to_json($result);
            }else{
                $ParseInput = $this->prepare_where_clause($SearchArr); 
                $filedsStr = isset($ParseInput['fileds']) && !empty($ParseInput['fileds'])?$ParseInput['fileds']:'';
                $valuesStr = isset($ParseInput['values']) && !empty($ParseInput['values'])?$ParseInput['values']:'';
                $filedsType =  $this->getFieldType($SearchArr);
         
                $is_deleted = 0;
                $result   =  array(); 
                $query = "select "
                        . " `client_id`, `client_name`, `client_mobile_no`, `client_phone_no`, `client_email`, `client_address`, `client_lat`, `client_lan`, `created_by`"
                        . "from ".$this->table1." where   1=1  [WEHER_STATMENT] ORDER BY  ".$orderby_field."  $orderby_dir" ; 
                
                    if(!empty($filedsStr) && !empty($valuesStr) && is_array($filedsType)){ 
                        $query = str_replace("[WEHER_STATMENT]", $filedsStr, $query);
                        $filedsType = array_merge($filedsType );  
                         $res =    $this->select($query, $SearchArr,$filedsType );
                         if($res):
                            
                             switch ($OutPut) {
                                case 'csv':
                                    $res =  $this->BuiltCsv($res,true);
                                    if(!empty($res)){
                                         return $this->convert_to_json(array('dl_link'=>$res));   
                                    }
                                break;

                                default:
                                    return isset($res[0]) && is_array($res[0]) ? $this->convert_to_json($res):'';
                                break;
                             }
                         
                         endif;
                    }
                    $result['error'] = "Not found";
                    return $this->convert_to_json($result);   
            }
        else :
            $result['error'] = "Mandetary Fields are not supplied";
            $result['error_code'] = "1006";
            return $this->convert_to_json($result);
        endif;
        
        $result['error'] = "invalid Token";
        return $this->convert_to_json($result);
    }
                                  
    /**/
    public function GetClientDetails($arr= array()){
        $db = $this->conn; 
        $ParseInput = $this->prepare_where_clause($arr); 
        $filedsStr = isset($ParseInput['fileds']) && !empty($ParseInput['fileds'])?$ParseInput['fileds']:'';
        $valuesStr = isset($ParseInput['values']) && !empty($ParseInput['values'])?$ParseInput['values']:'';
        $filedsType =  $this->getFieldType($arr);
        
        $is_deleted = 0;
        $result   =  array(); 
        $query = "select "
                . "`client_id`, `client_name`, `client_mobile_no`, `client_phone_no`, `client_email`, `client_address`, `client_lat`, `client_lan`, `client_is_active`, `client_is_deleted`, `created_by` "
                . "from ".$this->table1." where   `client_is_deleted` = ?   " ; 
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
 
    /**/
    public function GetClientMeta($SkipAuthToken = Null) {
        $db = $this->conn;
        $param = $_REQUEST;
        $param = $this->custom_sanitize($param);
        $search_field_array = (array) $this->search_field();
        $result = array();

        $auth_token = isset($param['auth_token']) && !empty($param['auth_token']) ? $param['auth_token'] : '';
        if ($SkipAuthToken == NULL) {
            $TokenDetails = $this->GetTokenStatus($auth_token);
            $TokenDetailsObj = json_decode($TokenDetails);
            if (!isset($TokenDetailsObj->token_user_id)) {
                $result['error'] = "invalid Token";
                return $this->convert_to_json($result);
            }
        }

        $ID = isset($param['client_id']) && !empty($param['client_id']) ? $param['client_id'] : '';
        if (empty($ID)) {
            $arr = array("error" => "Ref. Id is not supply");
            return $this->convert_to_json($arr);
        }

        $metaKey = isset($param['metakey']) && !empty($param['metakey']) ? $param['metakey'] : '';
        $metaVal = isset($param['metaval']) && !empty($param['metaval']) ? $param['metaval'] : '';

        $res = $this->GetMetaData($this->MetaSuffix, $this->MetaRef, $ID, $metaKey, $metaVal);
        if($res) {
            return $this->convert_to_json($res);
        }
        $result['res'] = "No Data Found";
        return $this->convert_to_json($result);
    }

    public function UniqueEmail($Email,$ID=Null){
        $db = $this->conn;  
        $is_deleted = 0;
        $result  = $arr  = $filedsType  =  array(); 
        
        $arr[]      = $is_deleted; 
        $arr[]      = $Email; 
        $filedsType[] = 'i'; 
        $filedsType[] = 's'; 
        
        $query = "select "
                . "`client_id`, `client_name`, `client_mobile_no`, `client_phone_no`, `client_email`, `client_address`, `client_lat`, `client_lan`, `created_by`" 
                . "from ".$this->table1." where   `client_is_deleted` = ?  and client_email = ?  " ; 
        if(!empty($ID)){
          $query = $query.   " and client_id != ?";
          $arr[] = $ID;
          $filedsType[] = 'i';
        } 
        $res =    $this->select($query, $arr,$filedsType );
        return isset($res[0]) && is_array($res[0]) ? $this->convert_to_json($res[0]):'';
    }
                                    
    public function ImportClient(){
        $db            = $this->conn;
        $param         = $_REQUEST; 
        $param         = $this->custom_sanitize($param); 
        $auth_token    = isset($param['auth_token']) && !empty($param['auth_token']) ?$param['auth_token']:'';    
        $TokenDetails  = $this->GetTokenStatus($auth_token); 
        $TokenDetailsObj = json_decode($TokenDetails);
        if(!isset($TokenDetailsObj->token_user_id)){
             $result['error'] = "invalid Token";
              return $this->convert_to_json($result);
        }else{
            $EmpID   = $TokenDetailsObj->token_user_id;
            $empData =  $this->GetEmploeeDetails(array("client_id"=>$EmpID));
                                
                                
            $res = $this->ImportCsv();    
                 if(isset($res["error"])){
                    return $this->convert_to_json($res);
                 }elseif(isset($res["fileData"]) && is_array($res["fileData"])){
                         $resOP = array();
                     foreach ($res["fileData"] as $fileRow) {
                         if(!empty($fileRow[0])):
                         $client_name   = isset($fileRow[0])?$fileRow[0]:'';
                         $client_mobile_no  = isset($fileRow[1])?$fileRow[1]:'';
                         $client_phone_no  = isset($fileRow[2])?$fileRow[2]:'';
                         $client_email  = isset($fileRow[3])?$fileRow[3]:'';
                         $client_address = isset($fileRow[4])?$fileRow[4]:'';
                         $client_lat   = isset($fileRow[5])?$fileRow[5]:'';
                         $client_lan   = isset($fileRow[5])?$fileRow[6]:'';
                         $email_val          = $this->UniqueEmail($client_email,$ID=Null); 
                         $ExchangeArray = array();
                         $ExchangeArray["client_name"]         = $client_name;
                            $ExchangeArray["client_mobile_no"]    = $client_mobile_no;
                            $ExchangeArray["client_phone_no"]     = $client_phone_no;
                            $ExchangeArray["client_email"]        = $client_email;
                            $ExchangeArray["client_address"]      = $client_address;  
                            $ExchangeArray["client_lat"]          = $client_lat;  
                            $ExchangeArray["client_lan"]          = $client_lan;  
                            $ExchangeArray["created_by"]          = $EmpID;      
                         
                         //-----------------------Validation -----------------------//
                         if(empty($email_val) && !empty($client_name) &&   !empty($client_mobile_no) &&   !empty($client_email) &&   !empty($client_address) && !empty($client_lat)  && !empty($client_lan)){
                           $CreatedClientId  = $this->insert($this->table1, $ExchangeArray, array('%s','%s','%s','%s','%s','%s','%s','%i'));
                           $resOP[] = "insert-id = $CreatedClientId";
                         }elseif(!empty($email_val)){
                             $resOP[] = "duplicate record";
                         }
                         else{
                             $resOP[] = "";
                         }
                         
                         endif;
                     }
                     return $this->convert_to_json($resOP);
                }
        }
    }
    
     /**/
    public function UpdateClient() {  
        $db = $this->conn;
        $param = $_REQUEST;
        $param = $this->custom_sanitize($param);
        $update_field_array = (array) $this->update_field();
        $result = $format = array();
        
        $auth_token    = isset($param['auth_token']) && !empty($param['auth_token']) ?$param['auth_token']:''; 
        $TokenDetails  = $this->GetTokenStatus($auth_token); 
        $TokenDetailsObj = json_decode($TokenDetails);
        if(!isset($TokenDetailsObj->token_user_id)){
             $result['error'] = "invalid Token";
              return $this->convert_to_json($result);
        }
        $EmpID   = $TokenDetailsObj->token_user_id;
                                
                                
        $ID = isset($param['client_id']) && !empty($param['client_id']) ? $param['client_id'] : '';
        if (empty($ID)) {
            $arr = array("error" => "Ref. Id is not supply");
            return $this->convert_to_json($arr);
        }
        
        $UpdateArr     = array();
        foreach ($param as $key => $value) {
            if (in_array($key, $update_field_array) && !empty($value)) {
                $UpdateArr[$key]= $value;
                $format[] ='%s';
            }
        } 
        $format_str = @implode(',', $format); 
        $update = $this->update($this->table1, $UpdateArr, $format, array('client_id'=>$ID), array('%i'));
        if($update){
            $result['success'] = "records updated successfully";
            return $this->convert_to_json($result);
        }
        $result['error'] = "records Not updated successfully";
            return $this->convert_to_json($result);
    }
     
    /**/
    public function DeleteClient() { 
        $db = $this->conn;
        $param = $_REQUEST;
        $param = $this->custom_sanitize($param);
        $update_field_array = (array) $this->update_field();
        $result = $format = array();
        
        $auth_token    = isset($param['auth_token']) && !empty($param['auth_token']) ?$param['auth_token']:''; 
        $TokenDetails  = $this->GetTokenStatus($auth_token); 
        $TokenDetailsObj = json_decode($TokenDetails);
        if(!isset($TokenDetailsObj->token_user_id)){
             $result['error'] = "invalid Token";
              return $this->convert_to_json($result);
        } 
                                
        $ID = isset($param['client_id']) && !empty($param['client_id']) ? $param['client_id'] : '';
        if (empty($ID)) {
            $arr = array("error" => "Ref. Id is not supply");
            return $this->convert_to_json($arr);
        }
        
        $UpdateArr     = array();
        $UpdateArr['client_is_deleted']= 1;
        $format[] ='%i';                        
        $format_str = @implode(',', $format);  
        $update = $this->update($this->table1, $UpdateArr, $format, array('client_id'=>$ID), array('%i'));
        if($update){
            $result['success'] = "records deleted successfully";
            return $this->convert_to_json($result);
        }
        $result['error'] = "records Not deleted successfully";
            return $this->convert_to_json($result);
    }
        
    /**/
    public function ChangeClientStatus() { 
        $db = $this->conn;
        $param = $_REQUEST;
        $param = $this->custom_sanitize($param);
        $update_field_array = (array) $this->update_field();
        $result = $format = array();
        
        $auth_token    = isset($param['auth_token']) && !empty($param['auth_token']) ?$param['auth_token']:''; 
        $TokenDetails  = $this->GetTokenStatus($auth_token); 
        $TokenDetailsObj = json_decode($TokenDetails);
        if(!isset($TokenDetailsObj->token_user_id)){
             $result['error'] = "invalid Token";
              return $this->convert_to_json($result);
        }
        $EmpID   = $TokenDetailsObj->token_user_id;
        $empData =  $this->GetEmploeeDetails(array("client_id"=>$EmpID));
        $empObj  = '';
        if($empData):
                $empObj = json_decode($empData);
            endif;
        
                                
        $ID = isset($param['client_id']) && !empty($param['client_id']) ? $param['client_id'] : '';
        $status = isset($param['status']) && !empty($param['status']) ? $param['status'] : '';
        if (empty($ID)) {
            $arr = array("error" => "Ref. Id is not supply");
            return $this->convert_to_json($arr);
        }
        $active_status = '1';
        if($status=='inactive'){
           $active_status = '0';
        }elseif ($status=='active') {
           $active_status = '1';
        }   
        
        $UpdateArr     = array();
        $UpdateArr['client_is_active']= $active_status;
        $format[] ='%i';                        
        $format_str = @implode(',', $format); 
        $update = $this->update($this->table1, $UpdateArr, $format, array('client_id'=>$ID), array('%i'));
        if($update){
            $result['success'] = "records updated successfully";
            return $this->convert_to_json($result);
        }
        $result['error'] = "records Not updated successfully";
            return $this->convert_to_json($result);
    }
   
 }

