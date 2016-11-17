<?php 
function isSqlInjected($object)
{
	 $objecVal = trim($_REQUEST[$object]);

	$objecVal =  preg_replace( "/\"\"/", "", $objecVal );
	$objecVal =  preg_replace( "/\'/", "''", $objecVal );

	$objecVal =   preg_replace( "/[\(\)\;\-\|]/", "", $objecVal );

	$objecVal = addslashes($objecVal);
	
	return $objecVal;
 }


function __($string)
{
	return $string = mysql_injection($string);
}


function __x($string)
{
	return $string = stripslashes( html_entity_decode( $string ) );
}


 
function mysql_injection($string)
{
	global $objDB;
	
	if($string=='') { return ''; }
	
	if(is_numeric($string)) { return $string; }
	
	$string = trim($string);
	
	if(empty($string)) { return ''; }
	
  if(get_magic_quotes_gpc())  // prevents duplicate backslashes
  {
    	$string = stripslashes($string);
  }

  if(phpversion() < '5.3.0')
  {
	$badWords = "(delete)|(update)|(union)|(insert)|(drop)|(http)|(--)";
	$string = eregi_replace($badWords, "", $string);
  }
  
  $string = addslashes( htmlentities($string) );
    
  $string = $objDB->real_escape_string($string);
  
  return $string;
  
}


//-----------------------------------------------------//
global $Mykey;

$Mykey =  'MahaBharat101';
////
//
function strEncript( $string )
{
	global $Mykey;
	
	return $encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), trim($string), MCRYPT_MODE_CBC, md5(md5($key))));

}

////
//
function strDecript( $EncriptString )
{
	global $Mykey;
	
 return $decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($EncriptString), MCRYPT_MODE_CBC, md5(md5($key))), "\0");

}


?>