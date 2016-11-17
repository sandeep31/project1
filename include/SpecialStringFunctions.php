<?php

////
//$text: Text containt
//$num: Return No of word.
//Return: Num of word from Text paragraph. (Return Short Description.)
////
function _abstract($text, $num)
{ 
 if (preg_match_all('/\s+/', $text, $junk) <= $num) return $num; 
 $text = preg_replace_callback('/(<\/?[^>]+\s+[^>]*>)/', 
 '_abstractProtect', $text); 
 $words = 0; 
 $out = array(); 
 $stack = array(); 
 $tok = strtok($text, "\n\t "); 
 while ($tok!== false and strlen($tok)) { 
  if (preg_match_all('/<(\/?[^\x01>]+)([^>]*)>/', 
    $tok, 
    $matches, 
    PREG_SET_ORDER)) { 
    foreach ($matches as $tag) _recordTag($stack, $tag[1], $tag[2]); 
  } 
  $out[] = $tok; 
  if (! preg_match('/^(<[^>]+>)+$/', $tok)) ++$words; 
  if ($words == $num) break; 
  $tok = strtok("\n\t "); 
 } 
 $abstract = _abstractRestore(implode(' ', $out)); 
 foreach ($stack as $tag) { $abstract .= "</$tag>"; } 
 return $abstract; 
} 


function _abstractProtect($match) { 
 return preg_replace('/\s/', "\x01", $match[0]); 
} 


function _abstractRestore($strings) { 
 return preg_replace('/\x01/', ' ', $strings); 
} 


function _recordTag(&$stack, $tag, $args) { 
 // XHTML 
 if (strlen($args) and $args[strlen($args) - 1] == '/') { 
   return; 
 } 
 else if ($tag[0] == '/') { 
   $tag = substr($tag, 1); 
   for ($i=count($stack) -1; $i >= 0; $i--) { 
     if ($stack[$i] == $tag) { 
       array_splice($stack, $i, 1); 
       return; 
     } 
   } 
   return; 
 } 
 else if (in_array($tag, array('p', 'li', 'ul', 'ol', 'div', 'span', 'a'))) { 
   $stack[] = $tag; 
 } 
 else { 
   // no-op 
 } 
}



////
//Create Slug.
////
function getSlug($string)
{
   $slug = preg_replace('/[^A-Za-z0-9-]+/', '-', $string);
   $slug = strtolower($slug); //If slug need in lower case.
   $slug = str_replace('--', '-' ,$slug);
   
   return $slug;
}
//


////
//Return Rename File
//
function get_rename_file($newname , $filename)
{
  	$extention = get_file_extension($filename);
	
	$newFileName =  $newname . "." . $extention;
	return $newFileName;
}
//


////
//Return File Extension
//
function get_file_extension($file_name)
{
  return substr(strrchr($file_name,'.'),1);
}
//

?>