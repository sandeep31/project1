<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ClientController extends Controller{
    private  $model = array('client');
    private  $modelObj = array();
    private  $ClientObj = '';
    
    public function __construct() {
        $this->modelObj =  $this->loadModel($this->model);
        $this->ClientObj = isset($this->modelObj['client']) && is_object($this->modelObj['client'])?$this->modelObj['client']:'';
    }
     
    public function ListAction(){
        if(!empty($this->ClientObj)){
            return $this->ClientObj->GetClientList();
        }
    }
    
    public function MetadataAction(){
        if(!empty($this->ClientObj)){
            return $this->ClientObj->GetClientMeta();
        }
    }
    
    public function CreateclientAction(){ 
        if(!empty($this->ClientObj)){
            return $this->ClientObj->CreateClient();
        }
    }
    //---------------- Import Csv-------------------//
    public function ImportclientAction(){ 
        if(!empty($this->ClientObj)){
            return $this->ClientObj->ImportClient();
        }
    }
    
    /*--------------Update---------------*/
    public function UpdateAction(){
        if(!empty($this->ClientObj)){
            return $this->ClientObj->UpdateClient();
        } 
    }
    
    /*--------------Delete---------------*/
    public function DeleteAction(){
        if(!empty($this->ClientObj)){
            return $this->ClientObj->DeleteClient();
        } 
    }
    /*--------------Actve / Inactive---------------*/
    public function UpdatestatusAction(){
        if(!empty($this->ClientObj)){
            return $this->ClientObj->ChangeClientStatus();
        } 
    }
}

