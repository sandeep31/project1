<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class TaskController extends Controller{
    private  $model = array('task');
    private  $modelObj = array();
    private  $TaskObj = '';
    
    public function __construct() {
        $this->modelObj =  $this->loadModel($this->model);
        $this->TaskObj = isset($this->modelObj['task']) && is_object($this->modelObj['task'])?$this->modelObj['task']:'';
    }
     
    public function ListAction(){
        if(!empty($this->TaskObj)){
            return $this->TaskObj->GetTaskList();
        }
    }
    
    public function MetadataAction(){
        if(!empty($this->TaskObj)){
            return $this->TaskObj->GetTaskMeta();
        }
    }
    
    public function CreatetaskAction(){ 
        if(!empty($this->TaskObj)){
            return $this->TaskObj->CreateTask();
        }
    }
    //---------------- Import Csv-------------------//
    public function ImporttaskAction(){ 
        if(!empty($this->TaskObj)){
            return $this->TaskObj->ImportTask();
        }
    }
    
    /*--------------Update---------------*/
    public function UpdateAction(){
        if(!empty($this->TaskObj)){
            return $this->TaskObj->UpdateTask();
        } 
    }
    
    /*--------------Delete---------------*/
    public function DeleteAction(){
        if(!empty($this->TaskObj)){
            return $this->TaskObj->DeleteTask();
        } 
    }
    /*--------------Actve / Inactive---------------*/
    public function UpdatestatusAction(){
        if(!empty($this->TaskObj)){
            return $this->TaskObj->ChangeTaskStatus();
        } 
    }
}

