<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

 
if(!function_exists('routing')):
    function routing(){
        $result = array("error"=>"Invalid API call");
        $controller  = 'index';
        $action       = 'index';
        $array = array(); 
        $REQUEST_URI = parse_url($_SERVER['REQUEST_URI']);
        $array = explode('/',$REQUEST_URI['path']); 
        $controller = isset($array[2]) && !empty($array[2])?$array[2]:$controller;
        $action      = isset($array[3]) && !empty($array[3])?$array[3]:$model;
        
        if(!empty($controller) && !empty($action) ):
             $ControllerClass = ucfirst($controller).'Controller';
             $ActionClass = ucfirst($action).'Action';
            if(class_exists($ControllerClass)):
                $Object = new $ControllerClass;   
                if(is_callable(array($Object,$ActionClass),false, $callable_name)===true) : 
                      return $Object->{$ActionClass}();
                endif;  
            endif;
        endif;    
        return json_encode($result);
    }
   
endif;