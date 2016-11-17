<?php

if(!function_exists("CreateSelectBox"))
{

	function CreateSelectBox($ComboName, $SqlQuery, $LableField, $ValueField, $SelectedId ,$CSSClassName='', $CallFunction='', $Required='required' , $Attributes='')
	  {
		global $objDB;
		
		$res = $objDB->query($SqlQuery);
 		
		  $CallFunction = ($CallFunction==='') ? '' : " onchange= \"$CallFunction\" ";
		  
		  $CSSClassName = ($CSSClassName==='') ? '' : " class=\"$CSSClassName\" ";
	  		
		  $Required = ($Required=='') ? '' : " required=\"required\" ";
	
		$resultcombo = "<select name=\"$ComboName\"  id=\"$ComboName\"  $CSSClassName  $CallFunction  $Required $Attributes >";
		
		$resultcombo .= "<option value=\"\">-- Select One--</option>"; 
		
		if($res && $objDB->num_rows($res) > 0)
		{				
			while($row = $objDB->fetch_array($res))
			{
			   $OptionValue = __($row[$ValueField]);
			   $OptionName  = __($row[$LableField]);
		
			   $selected = "";
			   
			   if($SelectedId==$OptionValue) {
					$selected = " selected";
				}
				
			   $resultcombo .= "<option value=\"$OptionValue\" $selected >$OptionName</option>";
			   
			 }//end while.
		 }//end if.
		 
		$resultcombo .= "</select>";
		
		return $resultcombo;
	  }
  
}//End. 
////
//  


if(!function_exists("CreateSelectBoxByArray"))
{

	function CreateSelectBoxByArray($ComboName, $dataArray, $SelectedId ,$CSSClassName='', $CallFunction='', $Required='required' , $Attributes='')
	  {
		
		  $CallFunction = ($CallFunction==='') ? '' : " onchange= \"$CallFunction\" ";
		  
		  $CSSClassName = ($CSSClassName==='') ? '' : " class=\"$CSSClassName\" ";
	  		
		  $Required = ($Required=='') ? '' : " required=\"required\" ";
	
		$resultcombo = "<select name=\"$ComboName\"  id=\"$ComboName\"  $CSSClassName  $CallFunction  $Required $Attributes >";
		
		$resultcombo .= "<option value=\"\">-- Select One--</option>"; 
		
		if(is_array($dataArray))
		{				
			foreach($dataArray as $OptionValue=>$OptionName)
			{
			   
			   $selected = "";
			   
			   if($SelectedId==$OptionValue) {
					$selected = " selected";
				}
				
			   $resultcombo .= "<option value=\"$OptionValue\" $selected >$OptionName</option>";
			   
			 }//end while.
		 }//end if.
		 
		$resultcombo .= "</select>";
		
		return $resultcombo;
	  }
  
}//End. 
////
//  

////
//
if(!function_exists(stripInjectArray))
{
	function stripInjectArray($array)
	{
	
		if(is_array($array))
		{
			foreach($array as $key=>$value)
			{
				$newArray[$key]= __($value);
			}
			
			return $newArray;
			
		} else {
			 
			return false;
		}
	
	}

}
//
////



 

////
function __Rupee($Rs)
{
	$symbol = '<span class="WebRupee" >Rs. </span>';
	$Rs = round($Rs);
	if(trim($Rs) == 0 || trim($Rs) == "")
	{
		echo $symbol."NILL";
	} else {
		echo $symbol.$Rs;
	}
	
}
//


////
//
function getDirectoryList($directory) 
{
    // create an array to hold directory list
    $results = array();

    // create a handler for the directory.
   $handler = opendir($directory);

    // open directory and walk through the filenames
    while ($file = readdir($handler)) 
	{
		  // if file isn't this directory or its parent, add it to the results
		  if ($file != "." && $file != "..")
		  {
			  $results[] = $file;
		  }// end if.

    }// end while.

    // tidy up: close the handler
    closedir($handler);

    // done!
    return $results;

}// end function.
//


////
//
function check_empty_folder( $folder )
{
	$files = array ();
	if ( $handle = opendir ( $folder ) ) {
		while ( false !== ( $file = readdir ( $handle ) ) ) {
			if ( $file != "." && $file != ".." ) {
				$files [] = $file;
			}
		}
		closedir ( $handle );
	}
	return ( count ( $files ) > 0 ) ? 0 : 1;
}
//










?>
