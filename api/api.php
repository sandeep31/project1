<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
apache_setenv("API_PATH", __DIR__); 

//------------- Config ------------------//
include_once __DIR__.'/config.php';

//------------- Bootstrap ---------------//
include_once __DIR__.'/inc/bootstrap.php';

//------------- Routing -----------------//
include_once __DIR__.'/inc/routing.php';

//------------ Json O/p------------------//
echo $res = routing();