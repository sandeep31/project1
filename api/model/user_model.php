<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class UserModel extends Models {
    private $table1     = 'employee';
    private $table2     = 'auth_token';
    private $table3     = 'employee_tracking_data';
    private $MetaSuffix = 'employee';  // field suffix
    private $MetaRef    = 'employee_id'; //Ref field
    
    private $admin_role    = '1';
    private $manager_role  = '2';
    private $salesman_role = '3';

    public function __construct() {
        parent::__construct();
    }
    
    /**/
    public function GetAuthToken(){
        $db = $this->conn;
        $param              = $_REQUEST; 
        $param              = $this->custom_sanitize($param); 
        $employee_email     = isset($param['email']) && !empty($param['email']) ?$param['email']:'';
        $employee_password  = isset($param['password']) && !empty($param['password']) ?md5($param['password']):'';
        
        $result             = array(); 
        $query = "select employee_id from ".$this->table1." where `employee_email` = ? "
                . "AND `employee_password` = ? "
                . "AND `employee_is_active` = ? "
                . "AND employee_is_deleted = ? "; 
        
        $iQurey = "insert into ".$this->table2." (auth_token,auth_token_start,auth_token_expire, auth_user_id,auth_token_status)"
                . "  VALUES (?,?,?,?,?)";
        
        try{
                
                if($stmt = $db->prepare($query)):
                    $active = 1;
                    $deleted = 0;
                    $stmt->bind_param('ssii', $employee_email, $employee_password,$active,$deleted);
                    $stmt->execute();
                    $stmt->store_result();
                    $stmt->bind_result($employee_id);
                    $stmt->fetch();
                    if((int)$employee_id ==0){
                        $result['error'] = "invalid credntials";
                        return $this->convert_to_json($result);
                    }
                    $TokenStart = date("Y:m:d H:i:s");
                    $TokenExpire = date("Y:m:d H:i:s",strtotime("+5 hours"));
                    $Token = $this->Randomtoken();
                    $TokenStatus = 'active';
                    $stmt = $db->prepare($iQurey);
                    $stmt->bind_param("sssis",$Token,$TokenStart,$TokenExpire,$employee_id,$TokenStatus);
                    $stmt->execute();
                    $id = $stmt->insert_id;
                    if($id){ 
                        $result['access_token'] = $Token;
                        $result['access_token_expire_at'] = $TokenExpire;
                        return $this->convert_to_json($result);
                    }
                    
                    
                    $stmt->close(); 
                endif;
                
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
         $result['error'] = "Unable to create token";
         return $this->convert_to_json($result);
    }

    /**/
    
    /**/
    public function CreateEmployee(){
        $db = $this->conn;
        $param         = $_REQUEST; 
        $param         = $this->custom_sanitize($param); 
        $auth_token    = isset($param['auth_token']) && !empty($param['auth_token']) ?$param['auth_token']:''; 
        $emp_code      = isset($param['emp_code']) && !empty($param['emp_code']) ?$param['emp_code']:''; 
        $emp_password  = isset($param['emp_password']) && !empty($param['emp_password']) ?md5($param['emp_password']):''; 
        $emp_fname     = isset($param['emp_fname']) && !empty($param['emp_fname']) ?$param['emp_fname']:'';  
        $emp_lname     = isset($param['emp_lname']) && !empty($param['emp_lname']) ?$param['emp_lname']:'';  
        $emp_email     = isset($param['emp_email']) && !empty($param['emp_email']) ?$param['emp_email']:'';  
        $emp_role_id   = isset($param['emp_role_id']) && !empty($param['emp_role_id']) ?$param['emp_role_id']:'';  
        $emp_mobile_no = isset($param['emp_mobile_no']) && !empty($param['emp_mobile_no']) ?$param['emp_mobile_no']:'';  
        $emp_phone_no  = isset($param['emp_phone_no']) && !empty($param['emp_phone_no']) ?$param['emp_phone_no']:Null;  
        
        $ExchangeArray = array();
        $ExchangeArray["employee_code"]     = $emp_code;
        $ExchangeArray["employee_password"] = $emp_password;
        $ExchangeArray["employee_fname"]    = $emp_fname;
        $ExchangeArray["employee_lname"]    = $emp_lname;
        $ExchangeArray["employee_email"]    = $emp_email;
        $ExchangeArray["employee_role_id"]  = $emp_role_id;
        $ExchangeArray["employee_mobile_no"]= $emp_mobile_no;
        $ExchangeArray["employee_phone_no"] = $emp_phone_no;  
        $email_val          = $this->UniqueEmail($emp_email,$ID=Null);
        $EemployeeCode_val  = $this->UniqueEemployeeCode($emp_code,$ID=Null);
        if(!empty($email_val) || !empty($EemployeeCode_val)){
            $result['error'] = "Email-ID or Employee Code already exist";
            return $this->convert_to_json($result);
        }
        if(!empty($auth_token) && !empty($emp_code) &&  !empty($emp_fname) && !empty($emp_lname) && !empty($emp_email) &&  !empty($emp_role_id) &&  !empty($emp_mobile_no) &&  !empty($emp_password)):
        $TokenDetails  = $this->GetTokenStatus($auth_token); 
        $TokenDetailsObj = json_decode($TokenDetails);
        if(!isset($TokenDetailsObj->token_user_id)){
             $result['error'] = "invalid Token";
              return $this->convert_to_json($result);
        }else{
            $EmpID   = $TokenDetailsObj->token_user_id;
            $empData =  $this->GetEmploeeDetails(array("employee_id"=>$EmpID));
            $empObj  = '';
            
            if($empData):
                $empObj = json_decode($empData);
            endif;
            
            if(!isset($empObj->employee_id)){
              $result['error'] = "invalid Token";
              $result['error_code'] = "1002";
              return $this->convert_to_json($result);
            } 
            $empRole = $empObj->employee_role_id;
            if(!$this->ManageEmploeeAccess($empRole)){
              $result['error'] = "No acceess to create  user ";
              $result['error_code'] = "1003";
              return $this->convert_to_json($result);
            }else{
                  $data =  $this->UniqueEmail($emp_email);
                  if(!empty($data)){
                     $result['error'] = "Email Id already in use ";
                     $result['error_code'] = "1004";
                     return $this->convert_to_json($result);
                  }
                  $CreatedEmployeeId  = $this->insert($this->table1, $ExchangeArray, array('%s','%s','%s','%s','%s','%i','%s','%s'));
                  if((int) $CreatedEmployeeId == 0):
                       $result['error'] = "Email Id already in use ";
                     $result['error_code'] = "1004";
                     return $this->convert_to_json($result);
                  endif;
            }
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
       return array("employee_code","employee_fname","employee_lname","employee_email","employee_mobile_no","employee_phone_no");
    }
    
    /**/
    public function update_field() {
       return array("employee_fname","employee_lname","employee_email","employee_mobile_no","employee_phone_no","employee_password");
    }
    
    
    /**/
    public function GetEmployeeList(){
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
        $orderby_field = isset($param['orderby_field']) && !empty($param['orderby_field']) ?$param['orderby_field']:'employee_id'; 
        $orderby_dir   = isset($param['orderby_dir']) && !empty($param['orderby_dir']) ?$param['orderby_dir']:'desc'; 
        
        //-------------------------------------OutPut----------------------------------------------------------------- 
        $OutPut   = isset($param['export']) && !empty($param['export']) ?'csv':'json'; 
        
        $SearchArr     = array();
        foreach ($param as $key => $value) {
            if (in_array($key, $search_field_array) && !empty($value)) {
                $SearchArr[$key]= $value;
            }
        }
        $SearchArr['employee_is_deleted'] = 0; 
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
                        . "employee_id,employee_code,employee_fname,employee_lname,employee_email,employee_role_id,employee_mobile_no,employee_phone_no,employee_is_active "
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
    public function ManageEmploeeAccess($UserRole=null){
        $AccessArray = array($this->manager_role,$this->admin_role,);
        if(!empty($UserRole) && in_array($UserRole,$AccessArray)){
            return true ;
        }
        return false;
    }
    
    /**/
    
    public function UpdateEmployee() { 
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
        $empData =  $this->GetEmploeeDetails(array("employee_id"=>$EmpID));
        $empObj  = '';
         if($empData):
                $empObj = json_decode($empData);
            endif;
        
        $empRole = $empObj->employee_role_id;
            if(!$this->ManageEmploeeAccess($empRole)){
              $result['error'] = "No acceess to create  user ";
              $result['error_code'] = "1003";
              return $this->convert_to_json($result);
            }
        $ID = isset($param['employee_id']) && !empty($param['employee_id']) ? $param['employee_id'] : '';
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
        $update = $this->update($this->table1, $UpdateArr, $format, array('employee_id'=>$ID), array('%i'));
        if($update){
            $result['success'] = "records updated successfully";
            return $this->convert_to_json($result);
        }
        $result['error'] = "records Not updated successfully";
            return $this->convert_to_json($result);
    }
     
    /**/
    public function DeleteEmployee() { 
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
        $empData =  $this->GetEmploeeDetails(array("employee_id"=>$EmpID));
        $empObj  = '';
        
         if($empData):
                $empObj = json_decode($empData);
            endif;
        $empRole = $empObj->employee_role_id;
            if(!$this->ManageEmploeeAccess($empRole)){
              $result['error'] = "No acceess to create  user ";
              $result['error_code'] = "1003";
              return $this->convert_to_json($result);
            }
        $ID = isset($param['employee_id']) && !empty($param['employee_id']) ? $param['employee_id'] : '';
        if (empty($ID)) {
            $arr = array("error" => "Ref. Id is not supply");
            return $this->convert_to_json($arr);
        }
        
        $UpdateArr     = array();
        $UpdateArr['employee_is_deleted']= 1;
        $format[] ='%i';                        
        $format_str = @implode(',', $format);  
        $update = $this->update($this->table1, $UpdateArr, $format, array('employee_id'=>$ID), array('%i'));
        if($update){
            $result['success'] = "records deleted successfully";
            return $this->convert_to_json($result);
        }
        $result['error'] = "records Not deleted successfully";
            return $this->convert_to_json($result);
    }
        
    /**/
    public function ChangeEmployeeStatus() { 
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
        $empData =  $this->GetEmploeeDetails(array("employee_id"=>$EmpID));
        $empObj  = '';
        if($empData):
                $empObj = json_decode($empData);
            endif;
        
        $empRole = $empObj->employee_role_id;
            if(!$this->ManageEmploeeAccess($empRole)){
              $result['error'] = "No acceess to create  user ";
              $result['error_code'] = "1003";
              return $this->convert_to_json($result);
            }
        $ID = isset($param['employee_id']) && !empty($param['employee_id']) ? $param['employee_id'] : '';
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
        $UpdateArr['employee_is_active']= $active_status;
        $format[] ='%i';                        
        $format_str = @implode(',', $format); 
        $update = $this->update($this->table1, $UpdateArr, $format, array('employee_id'=>$ID), array('%i'));
        if($update){
            $result['success'] = "records updated successfully";
            return $this->convert_to_json($result);
        }
        $result['error'] = "records Not updated successfully";
            return $this->convert_to_json($result);
    }
    /**/
    
    public function GetEmployeeMeta($SkipAuthToken = Null) {
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

        $ID = isset($param['employee_id']) && !empty($param['employee_id']) ? $param['employee_id'] : '';
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

    /**/
    public function Randomtoken(){ 
        $randnum = rand(1111111111,9999999999);
        return   uniqid().time().uniqid().(time()-2).$randnum;
    }
    
    /**/
    public function UniqueEmail($Email,$ID=Null){
        $db = $this->conn;  
        $is_deleted = 0;
        $result  = $arr  = $filedsType  =  array(); 
        
        $arr[]      = $is_deleted; 
        $arr[]      = $Email; 
        $filedsType[] = 'i'; 
        $filedsType[] = 's'; 
        
        $query = "select "
                . "employee_id,employee_code,employee_fname,employee_lname,employee_email,employee_role_id,"
                . "employee_mobile_no,employee_phone_no,employee_is_active "
                . "from ".$this->table1." where   `employee_is_deleted` = ?  and employee_email = ?  " ; 
        if(!empty($ID)){
          $query = $query.   " and employee_id != ?";
          $arr[] = $ID;
          $filedsType[] = 'i';
        } 
        $res =    $this->select($query, $arr,$filedsType );
        return isset($res[0]) && is_array($res[0]) ? $this->convert_to_json($res[0]):'';
    }
    
    public function UniqueEemployeeCode($employee_code,$ID=Null){
        $db = $this->conn;  
        $is_deleted = 0;
        $result  = $arr  = $filedsType  =  array(); 
        
        $arr[]      = $is_deleted; 
        $arr[]      = $employee_code; 
        $filedsType[] = 'i'; 
        $filedsType[] = 's'; 
        
        $query = "select "
                . "employee_id,employee_code,employee_fname,employee_lname,employee_email,employee_role_id,"
                . "employee_mobile_no,employee_phone_no,employee_is_active "
                . "from ".$this->table1." where   `employee_is_deleted` = ?  and employee_code = ?  " ; 
        if(!empty($ID)){
          $query = $query.   " and employee_id != ?";
          $arr[] = $ID;
          $filedsType[] = 'i';
        } 
        $res =    $this->select($query, $arr,$filedsType );
        return isset($res[0]) && is_array($res[0]) ? $this->convert_to_json($res[0]):'';
    }
    
    public function ImportEmployee(){
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
            $empData =  $this->GetEmploeeDetails(array("employee_id"=>$EmpID));
            $empObj  = '';
            
            if($empData):
                $empObj = json_decode($empData);
            endif;
            
            if(!isset($empObj->employee_id)){
              $result['error'] = "invalid Token";
              $result['error_code'] = "1002";
              return $this->convert_to_json($result);
            } 
            $empRole = $empObj->employee_role_id;
            if(!$this->ManageEmploeeAccess($empRole)){
              $result['error'] = "No acceess to create  user ";
              $result['error_code'] = "1003";
              return $this->convert_to_json($result);
            }else{
                 $res = $this->ImportCsv();    
                 if(isset($res["error"])){
                    return $this->convert_to_json($res);
                 }elseif(isset($res["fileData"]) && is_array($res["fileData"])){
                         $resOP = array();
                     foreach ($res["fileData"] as $fileRow) {
                         if(!empty($fileRow[0])):
                         $emp_code   = isset($fileRow[0])?$fileRow[0]:'';
                         $emp_fname  = isset($fileRow[1])?$fileRow[1]:'';
                         $emp_lname  = isset($fileRow[2])?$fileRow[2]:'';
                         $emp_email  = isset($fileRow[3])?$fileRow[3]:'';
                         $emp_mobile = isset($fileRow[4])?$fileRow[4]:'';
                         $emp_role   = isset($fileRow[5])?$fileRow[5]:'';
                        
                         $ExchangeArray = array();
                         
                         //-----------------------Validation -----------------------//
                        $email_val          = $this->UniqueEmail($emp_email,$ID=Null);
                        $EemployeeCode_val  = $this->UniqueEemployeeCode($emp_code,$ID=Null);
                         if(!empty($emp_code) &&  !empty($emp_fname) &&   !empty($emp_lname) &&   !empty($emp_email) &&   !empty($emp_mobile) && !empty($emp_role)  && empty($email_val) &&  empty($EemployeeCode_val)){
                            $ExchangeArray["employee_code"]     = $emp_code;
                            $ExchangeArray["employee_password"] = md5($emp_code.$emp_mobile);
                            $ExchangeArray["employee_fname"]    = $emp_fname;
                            $ExchangeArray["employee_lname"]    = $emp_lname;
                            $ExchangeArray["employee_email"]    = $emp_email;
                            $ExchangeArray["employee_mobile_no"]= $emp_mobile;
                            $ExchangeArray["employee_role_id"]  = $emp_role;
                            
                            $CreatedEmployeeId  = $this->insert($this->table1, $ExchangeArray, array('%s','%s','%s','%s','%s','%s','%i'));
                            $resOP[] = "insert-id = $CreatedEmployeeId";
                         }else{
                             $resOP[] = "duplicate record";
                         }
                         endif;
                     }
                     return $this->convert_to_json($resOP);
                }
                 
            }        
        }
    }  
        
    /**/
    public function UpdateLocation($skipAuth=null) { 
        $db = $this->conn;
        $param = $_REQUEST;
        $param = $this->custom_sanitize($param);
        $update_field_array = (array) $this->update_field();
        $result = $format = array();
        if(empty($skipAuth)){
             $auth_token    = isset($param['auth_token']) && !empty($param['auth_token']) ?$param['auth_token']:''; 
        $TokenDetails  = $this->GetTokenStatus($auth_token); 
        $TokenDetailsObj = json_decode($TokenDetails);
        if(!isset($TokenDetailsObj->token_user_id)){
             $result['error'] = "invalid Token";
              return $this->convert_to_json($result);
        }
        $EmpID   = $TokenDetailsObj->token_user_id;
        $empData =  $this->GetEmploeeDetails(array("employee_id"=>$EmpID));
        $empObj  = '';
        if($empData):
                $empObj = json_decode($empData);
            endif;
        
        $empRole = $empObj->employee_role_id;
            if(!$this->ManageEmploeeAccess($empRole)){
              $result['error'] = "No acceess to create  user ";
              $result['error_code'] = "1003";
              return $this->convert_to_json($result);
            } 
        }
      
        $ID = isset($param['employee_id']) && !empty($param['employee_id']) ? $param['employee_id'] : '';
        $lat = isset($param['lat']) && !empty($param['lat']) ? $param['lat'] : '';
        $lan = isset($param['lan']) && !empty($param['lan']) ? $param['lan'] : '';
        $location = isset($param['location']) && !empty($param['location']) ? $param['location'] : '';
        if (empty($ID)) {
            $arr = array("error" => "Ref. Id is not supply");
            return $this->convert_to_json($arr);
        }
                                
        $UpdateArr     = array();
        $UpdateArr['employee_id']= $ID;
        $UpdateArr['lat']= $lat;
        $UpdateArr['lan']= $lan;
        $UpdateArr['postdate']= date("Y-m-d H:i:s");              
        $UpdateArr['location']= $location;              
        
        if(!empty($ID) && !empty($lat) &&  !empty($lan) && !empty($location) && !empty($UpdateArr['postdate']) ):
                                
                                
                  $CreatedEmployeeId  = $this->insert($this->table3, $UpdateArr, array('%i','%s','%s','%s','%s'));
                  if((int) $CreatedEmployeeId == 0):
                     $result['error'] = "Email Id already in use ";
                     $result['error_code'] = "1004";
                     return $this->convert_to_json($result);
                  else:
                      $result['success'] = "record inserted Successfully";
                                
                     return $this->convert_to_json($result);
                  endif;
                                
                                
        else :
            $result['error'] = "Mandetary Fields are not supplied";
            $result['error_code'] = "1006";
            return $this->convert_to_json($result);
        endif;    
    }
 }

