<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */ 
$config = array();
$config['api_version']= '1.0.1';
//------------------ Core Classes---------------
$config["core"][] = 'controller';
$config["core"][] = 'model';

//------------------Controller---------------
$config['controller'][]= 'client';
$config['controller'][]= 'comapny';
$config['controller'][]= 'order'; //
$config['controller'][]= 'payment';
$config['controller'][]= 'product'; // product ,
$config['controller'][]= 'user'; //employeee 
$config['controller'][]= 'task'; 
$config['controller'][]= 'report';

//------------------- Model---------------------
$config['model'][]= 'user'; //employeee 
$config['model'][]= 'product'; // product ,
$config['model'][]= 'order'; //
$config['model'][]= 'payment';
$config['model'][]= 'comapny';
$config['model'][]= 'client';
$config['model'][]= 'task'; 
$config['model'][]= 'report';

