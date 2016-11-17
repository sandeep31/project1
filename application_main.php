<?php
// Report all errors except E_NOTICE
// This is the default value set in php.ini
error_reporting(E_ALL ^ E_NOTICE);
session_start();
ob_start();

include_once("include/DefineKeys.php");
include_once("include/DatabaseTables.php");

include_once("include/mysqlDB.php");

global $objDB;

$objDB = new DB;
$mysql = new Mysql($objDB);


include_once("include/CommonFunction.php");
include_once("include/formvalidation.php");
include_once("include/sqlInjection.php"); 

include_once("include/AllFunction.php");
 
include_once("include/SpecialStringFunctions.php");

 

?>
