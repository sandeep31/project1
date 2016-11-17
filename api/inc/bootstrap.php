<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


/*
 * inculdes Controllers 
 */
 $_SERVER["API_PATH"]= apache_getenv("API_PATH");
$CoreClasses = isset($config["core"]) && is_array($config["core"])?$config["core"]:'';
if(is_array($CoreClasses)):
    foreach ($CoreClasses as $CoreClass):
        if(!empty($CoreClass)):
            $CoreClassPath = $_SERVER["API_PATH"] .'/core/'.$CoreClass.'_class.php' ;
            if(file_exists($CoreClassPath)):
                include_once $CoreClassPath;
            endif;
        endif;
    endforeach;
endif;

/*
 * inculdes Controllers 
 */
$_controllers = isset($config["controller"]) && is_array($config["controller"])?$config["controller"]:'';
if(is_array($_controllers)):
    foreach ($_controllers as $_controller):
        if(!empty($_controller)):
            $_controller_file_path = $_SERVER["API_PATH"] .'/controller/'.$_controller.'_controller.php' ;
            if(file_exists($_controller_file_path)):
                include_once $_controller_file_path;
            endif;
        endif;
    endforeach;
endif;

if(!function_exists('load_model')  &&  class_exists('Models')):
    function load_model($model=null){
         if(empty($model)):
             return false;
         endif;   
         $_model_file_path = $_SERVER["API_PATH"] .'/model/'.$model.'_model.php' ;
         if(file_exists($_model_file_path)):
                include_once $_model_file_path;
         endif;
    }
endif;

