<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ProductModel extends Models {
    private $table1     = 'product';
    private $MetaSuffix = 'product';  // field suffix
    private $MetaRef    = 'product_id'; //Ref field
    
    public function __construct() {
        parent::__construct();
    }
    
    /**/
    public function CreateProduct(){
        $db = $this->conn;
        $param             = $_REQUEST; 
        $param             = $this->custom_sanitize($param); 
        $auth_token        = isset($param['auth_token']) && !empty($param['auth_token']) ?$param['auth_token']:''; 
        $product_name      = isset($param['product_name']) && !empty($param['product_name']) ?$param['product_name']:'';  
        $product_desc      = isset($param['product_desc']) && !empty($param['product_desc']) ?$param['product_desc']:'';  
        $product_price     = isset($param['product_price']) && !empty($param['product_price']) ?$param['product_price']:'';  
        $product_sales_qty = isset($param['product_sales_qty']) && !empty($param['product_sales_qty']) ?$param['product_sales_qty']:'';  
        $product_sales_price   = isset($param['product_sales_price']) && !empty($param['product_sales_price']) ?$param['product_sales_price']:'';   
                                
        $ExchangeArray = array();
        $ExchangeArray["product_name"]          = $product_name;
        $ExchangeArray["product_desc"]          = $product_desc;
        $ExchangeArray["product_price"]         = $product_price;
        $ExchangeArray["product_sales_qty"]     = $product_sales_qty;
        $ExchangeArray["product_sales_price"]   = $product_sales_price;   
        $UniqueProductName                      = $this->UniqueName($product_name,$ID=Null); 
                                
        if(!empty($UniqueProductName) ){
            $result['error'] = "Product Name  already exist";
            return $this->convert_to_json($result);
        }
        if(!empty($auth_token) && !empty($product_name) &&  !empty($product_desc) && !empty($product_sales_qty) && !empty($product_sales_price)   ):
        $TokenDetails  = $this->GetTokenStatus($auth_token); 
        $TokenDetailsObj = json_decode($TokenDetails);
        if(!isset($TokenDetailsObj->token_user_id)){
             $result['error'] = "invalid Token";
              return $this->convert_to_json($result);
        }else{
            $EmpID   = $TokenDetailsObj->token_user_id;                   
           
            $CreatedProductId  = $this->insert($this->table1, $ExchangeArray, array('%s','%s','%s','%s','%s'));
            if((int) $CreatedProductId == 0):
               $result['error'] = "Unable to  insert the record";
               $result['error_code'] = "1004";
               return $this->convert_to_json($result);
            endif;
            $result['success'] = "inserted the record successfully"; 
            $result['product_id'] = $CreatedProductId; 
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
       return array("product_name","product_desc","product_sales_qty","product_sales_qty","product_sales_price");
    }
    
    
    public function update_field() {
       return array("product_name","product_desc","product_price","product_sales_price");
    }
    
    
    /**/
    public function GetProductList(){
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
        $orderby_field = isset($param['orderby_field']) && !empty($param['orderby_field']) ?$param['orderby_field']:'product_id'; 
        $orderby_dir   = isset($param['orderby_dir']) && !empty($param['orderby_dir']) ?$param['orderby_dir']:'desc'; 
        
        //-------------------------------------OutPut----------------------------------------------------------------- 
        $OutPut   = isset($param['export']) && !empty($param['export']) ?'csv':'json'; 
        
        $SearchArr     = array();
        foreach ($param as $key => $value) {
            if (in_array($key, $search_field_array) && !empty($value)) {
                $SearchArr[$key]= $value;
            }
        }
        $SearchArr['product_is_deleted'] = 0; 
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
                        . " `product_id`, `product_name`, `product_desc`, `product_price`, `product_sales_qty`, `product_sales_price`"
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
    public function GetProductDetails($arr= array()){
        $db = $this->conn; 
        $ParseInput = $this->prepare_where_clause($arr); 
        $filedsStr = isset($ParseInput['fileds']) && !empty($ParseInput['fileds'])?$ParseInput['fileds']:'';
        $valuesStr = isset($ParseInput['values']) && !empty($ParseInput['values'])?$ParseInput['values']:'';
        $filedsType =  $this->getFieldType($arr);
        
        $is_deleted = 0;
        $result   =  array(); 
        $query = "select "
                . "`product_id`, `product_name`, `product_desc`, `product_price`, `product_sales_qty`, `product_sales_price`,  `product_is_active`, `product_is_deleted` "
                . "from ".$this->table1." where   `product_is_deleted` = ?   " ; 
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
    public function GetProductMeta($SkipAuthToken = Null) {
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

        $ID = isset($param['product_id']) && !empty($param['product_id']) ? $param['product_id'] : '';
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

    public function UniqueName($Name,$ID=Null){
        $db = $this->conn;  
        $is_deleted = 0;
        $result  = $arr  = $filedsType  =  array(); 
        
        $arr[]      = $is_deleted; 
        $arr[]      = $Name; 
        $filedsType[] = 'i'; 
        $filedsType[] = 's'; 
        
        $query = "select "
                . "`product_id`, `product_name`, `product_desc`, `product_price`, `product_sales_qty`, `product_sales_price`" 
                . "from ".$this->table1." where   `product_is_deleted` = ?  and product_name = ?  " ; 
        if(!empty($ID)){
          $query = $query.   " and product_id != ?";
          $arr[] = $ID;
          $filedsType[] = 'i';
        } 
        $res =    $this->select($query, $arr,$filedsType );
        return isset($res[0]) && is_array($res[0]) ? $this->convert_to_json($res[0]):'';
    }
                                    
    public function ImportProduct(){
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
            $empData =  $this->GetEmploeeDetails(array("product_id"=>$EmpID));
                                
                                
            $res = $this->ImportCsv();    
                 if(isset($res["error"])){
                    return $this->convert_to_json($res);
                 }elseif(isset($res["fileData"]) && is_array($res["fileData"])){
                         $resOP = array();
                     foreach ($res["fileData"] as $fileRow) {
                         if(!empty($fileRow[0])):
                         $product_name   = isset($fileRow[0])?$fileRow[0]:'';
                         $product_desc  = isset($fileRow[1])?$fileRow[1]:'';
                         $product_price  = isset($fileRow[2])?$fileRow[2]:'';
                         $product_sales_qty  = isset($fileRow[3])?$fileRow[3]:'';
                         $product_sales_price = isset($fileRow[4])?$fileRow[4]:''; 
                         $UniqueProductName          = $this->UniqueName($product_name,$ID=Null); 
                         $ExchangeArray = array();
                            $ExchangeArray["product_name"]         = $product_name;
                            $ExchangeArray["product_desc"]         = $product_desc;
                            $ExchangeArray["product_price"]        = $product_price;
                            $ExchangeArray["product_sales_qty"]    = $product_sales_qty;
                            $ExchangeArray["product_sales_price"]  = $product_sales_price;  
                         
                         //-----------------------Validation -----------------------//
                         if(empty($UniqueProductName) && !empty($product_name) &&   !empty($product_desc) &&   !empty($product_sales_qty) &&   !empty($product_sales_price) && !empty($product_price)   ){
                           $CreatedProductId  = $this->insert($this->table1, $ExchangeArray, array('%s','%s','%i','%i','%i'));
                           $resOP[] = "insert-id = $CreatedProductId";
                         }elseif(!empty($UniqueProductName)){
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
    public function UpdateProduct() {  
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
                                
                                
        $ID = isset($param['product_id']) && !empty($param['product_id']) ? $param['product_id'] : '';
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
        $update = $this->update($this->table1, $UpdateArr, $format, array('product_id'=>$ID), array('%i'));
        if($update){
            $result['success'] = "records updated successfully";
            return $this->convert_to_json($result);
        }
        $result['error'] = "records Not updated successfully";
            return $this->convert_to_json($result);
    }
     
    /**/
    public function DeleteProduct() { 
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
                                
        $ID = isset($param['product_id']) && !empty($param['product_id']) ? $param['product_id'] : '';
        if (empty($ID)) {
            $arr = array("error" => "Ref. Id is not supply");
            return $this->convert_to_json($arr);
        }
        
        $UpdateArr     = array();
        $UpdateArr['product_is_deleted']= 1;
        $format[] ='%i';                        
        $format_str = @implode(',', $format);  
        $update = $this->update($this->table1, $UpdateArr, $format, array('product_id'=>$ID), array('%i'));
        if($update){
            $result['success'] = "records deleted successfully";
            return $this->convert_to_json($result);
        }
        $result['error'] = "records Not deleted successfully";
            return $this->convert_to_json($result);
    }
        
    /**/
    public function ChangeProductStatus() { 
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
        $empData =  $this->GetEmploeeDetails(array("product_id"=>$EmpID));
        $empObj  = '';
        if($empData):
                $empObj = json_decode($empData);
            endif;
        
                                
        $ID = isset($param['product_id']) && !empty($param['product_id']) ? $param['product_id'] : '';
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
        $UpdateArr['product_is_active']= $active_status;
        $format[] ='%i';                        
        $format_str = @implode(',', $format); 
        $update = $this->update($this->table1, $UpdateArr, $format, array('product_id'=>$ID), array('%i'));
        if($update){
            $result['success'] = "records updated successfully";
            return $this->convert_to_json($result);
        }
        $result['error'] = "records Not updated successfully";
            return $this->convert_to_json($result);
    }
   
 }

