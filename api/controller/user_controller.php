<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class UserController extends Controller{
    private  $model = array('user');
    private  $modelObj = array();
    private  $UserObj = '';
    
    function __construct() {
        $this->modelObj =  $this->loadModel($this->model);
        $this->UserObj = isset($this->modelObj['user']) && is_object($this->modelObj['user'])?$this->modelObj['user']:'';
    }
     
    function ListAction(){
        if(!empty($this->UserObj)){
            return $this->UserObj->GetEmployeeList();
        }
    }
    
    function MetadataAction(){
        if(!empty($this->UserObj)){
            return $this->UserObj->GetEmployeeMeta();
        }
    }
    
    function GettokenAction(){ 
        if(!empty($this->UserObj)){
            return $this->UserObj->GetAuthToken();
        }
    }
    
    function GettokenstatusAction(){ 
        if(!empty($this->UserObj)){
            return $this->UserObj->GetTokenStatus();
        }
    }
    
    function CreateemployeeAction(){ 
        if(!empty($this->UserObj)){
            return $this->UserObj->CreateEmployee();
        }
    }
    
    function ImportemployeeAction(){ 
        if(!empty($this->UserObj)){
            return $this->UserObj->ImportEmployee();
        }
    }
    
    function UpdateAction(){
        if(!empty($this->UserObj)){
            return $this->UserObj->UpdateEmployee();
        } 
    }
    
    function DeleteAction(){
        if(!empty($this->UserObj)){
            return $this->UserObj->DeleteEmployee();
        } 
    }
    
    function UpdatestatusAction(){
        if(!empty($this->UserObj)){
            return $this->UserObj->ChangeEmployeeStatus();
        } 
    }
    
    function UpdatelocationAction(){
        if(!empty($this->UserObj)){
            return $this->UserObj->UpdateLocation(1);
        }
    }
}

