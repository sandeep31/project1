<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class ProductController extends Controller{
    private  $model = array('product');
    private  $modelObj = array();
    private  $ProductObj = '';
    
    public function __construct() {
        $this->modelObj =  $this->loadModel($this->model);
        $this->ProductObj = isset($this->modelObj['product']) && is_object($this->modelObj['product'])?$this->modelObj['product']:'';
    }
     
    public function ListAction(){
        if(!empty($this->ProductObj)){
            return $this->ProductObj->GetProductList();
        }
    }
    
    public function MetadataAction(){
        if(!empty($this->ProductObj)){
            return $this->ProductObj->GetProductMeta();
        }
    }
    
    public function CreateproductAction(){ 
        if(!empty($this->ProductObj)){
            return $this->ProductObj->CreateProduct();
        }
    }
    //---------------- Import Csv-------------------//
    public function ImportproductAction(){ 
        if(!empty($this->ProductObj)){
            return $this->ProductObj->ImportProduct();
        }
    }
    
    /*--------------Update---------------*/
    public function UpdateAction(){
        if(!empty($this->ProductObj)){
            return $this->ProductObj->UpdateProduct();
        } 
    }
    
    /*--------------Delete---------------*/
    public function DeleteAction(){
        if(!empty($this->ProductObj)){
            return $this->ProductObj->DeleteProduct();
        } 
    }
    /*--------------Actve / Inactive---------------*/
    public function UpdatestatusAction(){
        if(!empty($this->ProductObj)){
            return $this->ProductObj->ChangeProductStatus();
        } 
    }
}

