<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Controller {

    function __construct() {
        
    }
    
    function loadModel($ModelArr = array()){
        $_arr = array();
        if(function_exists('load_model') && count($ModelArr) > 0):
            foreach ($ModelArr as $model) :
                    load_model($model);
                    $ModelClass = ucfirst($model).'Model';
                    if(class_exists($ModelClass)):
                     $_arr[$model] =   new $ModelClass;  
                    endif;
            endforeach;
        endif;
        return $_arr ;
    }
}

