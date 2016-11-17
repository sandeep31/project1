<?php

//for replacing htmlspecilchars to normal format
function unhtmlspecialchars( $string )
{
  $string = str_replace ( '&amp;', '&', $string );
  $string = str_replace ( '&#039;', '\'', $string );
  $string = str_replace ( '&quot;', '"', $string );
  $string = str_replace ( '&lt;', '<', $string );
  $string = str_replace ( '&gt;', '>', $string );
  $string = str_replace ( '&uuml;', '??',$string );
  $string = str_replace ( '&Uuml;', '?', $string );
  $string = str_replace ( '&auml;', '?', $string );
  $string = str_replace ( '&Auml;', 'h,', $string );
  $string = str_replace ( '&ouml;', '??',$string );
  $string = str_replace ( '&Ouml;', '?,', $string ); 
  return $string;
} 
 

//************combo box function*********************//
////
//
function fillpackagecombo($comboname, $sqlstr, $namefield, $valuefield, $selectedid ,$styleclassname, $callfunction, $tabindex , $attr='')
  {
	global $objDB;
	
	$res = $objDB->query($sqlstr);
	$num = $objDB->num_rows($res);
	
	if($callfunction!="") $callfunction = ' onchange= "'.$callfunction.'"';
	if($tabindex!="") $tabindex = ' tabindex = "'.$tabindex.'"'; 

	$resultcombo = '<select name="'.$comboname.'"  class="'.$styleclassname.'"  '.$callfunction.'  id="'.$comboname.'"  '.$tabindex.' '.$attr.' >';
	
	$resultcombo .= "<option value=''>-- Please Select --</option>"; 
					
	while($rec = $objDB->fetch_array($res))
	{
	   $optionvalue = $rec[$valuefield];
	   $optionname = stripslashes($rec[$namefield]);
	   $optionlable = stripslashes($rec[$lablefield]);

	   $selected = "";
	   if($selectedid==$optionvalue)
			$selected = " selected";

	   $resultcombo .= "<option value=\"$optionvalue\" $selected >$optionname</option>";
	 }
	 
	$resultcombo .= "</select>";
	
	return $resultcombo;
  }
 //  
	
 
////
//
function fillpackagecombomulti_forsearch($comboname, $sqlstr, $namefield, $valuefield, $selectedid ,$styleclassname, $callfunction, $comboId )
  {
	global $objDB;
	
	$res = $objDB->query($sqlstr);
	$num = $objDB->num_rows($res);
	if($callfunction!="")
		$callfunction = " onchange= '".$callfunction."'";
		
	if($comboId!="")
		$comboId = " id= '".$comboId."'";

	$resultcombo = 	"<select name=$comboname class=$styleclassname $callfunction  multiple size='3' $comboId>
	<option value='-1'>Any</option>";
	while($rec = $objDB->fetch_array($res))
	{
	   $optionvalue = $rec[$valuefield];
	   $optionname = stripslashes($rec[$namefield]);

	   $selected = "";
	   if($selectedid==$optionvalue)
			$selected = " selected";

	   $resultcombo .= "<option value=$optionvalue $selected > $optionname </option>";
	}

	$resultcombo .= "</select>";
	return $resultcombo;
  }
//
 
////
//
function fillpackagecombomulti($comboname, $sqlstr, $namefield, $valuefield, $selectedid ,$styleclassname, $callfunction)
{
	global $objDB;
	
	$res = $objDB->query($sqlstr);
	$num = $objDB->num_rows($res);
	if($callfunction!="")
		$callfunction = " onchange= '".$callfunction."'";


	$resultcombo = 	"<select name=$comboname  class=$styleclassname $callfunction  multiple size='3'>    
					<option value='-1'>--NA--</option>";
	while($rec = $objDB->fetch_array($res))
	{

	   $optionvalue = $rec[$valuefield];
	   $optionname = stripslashes($rec[$namefield]);

	   $selected = "";
	   if($selectedid==$optionvalue)
			$selected = " selected";

	   $resultcombo .= "<option value=$optionvalue $selected >$optionname</option>";
	}
	$resultcombo .= "</select>";
	return $resultcombo;
  }
//


function fillpackagecombonew($comboname, $sqlstr, $namefield, $valuefield, $selectedid ,$styleclassname, $callfunction)
{
	global $objDB;
	
	$res = $objDB->query($sqlstr);
	$num = $objDB->num_rows($res);
	
	if($callfunction!="")
		$callfunction = " onchange= '".$callfunction."'";


	$resultcombo = 	"<select name=$comboname  class=$styleclassname $callfunction>
					<option value='-1'>--Please Select---</option>";
	while($rec = $objDB->fetch_array($res))
	{

	   $optionvalue = $rec[$valuefield];
	   $optionname = stripslashes($rec[$namefield]);
		$optionname =strtolower($optionname);
		$optionname =ucfirst($optionname);

	   $selected = "";
	   if($selectedid==$optionvalue)
			$selected = " selected";
		 
	   $resultcombo .= "<option value=$optionvalue $selected >$optionname</option>";
	}
	$resultcombo .= "</select>";
	return $resultcombo;
  }



function fillpackagecombo2($comboname, $sqlstr, $namefield, $valuefield, $selectedid ,$styleclassname, $callfunction)
{
	global $objDB;
	
	$res = $objDB->query($sqlstr);
	$num = $objDB->num_rows($res);
	if($callfunction!="")
		$callfunction = " onchange= '".$callfunction."'";


	$resultcombo = 	"<select name=$comboname  class=$styleclassname $callfunction>
					<option value='-1'>-- Select All ---</option>";
	while($rec = $objDB->fetch_array($res))
	{

	   $optionvalue = $rec[$valuefield];
	   $optionname = stripslashes($rec[$namefield]);

	   $selected = "";
	   if($selectedid==$optionvalue)
			$selected = " selected";

	   $resultcombo .= "<option value=$optionvalue $selected >$optionname</option>";
	}
	$resultcombo .= "</select>";
	return $resultcombo;
  }
//*******************END********************"

////
//
function fillpackagecombo_optiongroup($comboname, $sqlstr, $namefield, $valuefield, $selectedid ,$styleclassname, $callfunction, $tabindex)
{
	global $objDB;
	
	$res = $objDB->query($sqlstr);
	$num = $objDB->num_rows($res);
	
	if($callfunction!="") $callfunction = ' onchange= "'.$callfunction.'"';
	if($tabindex!="") $tabindex = ' tabindex = "'.$tabindex.'"'; 

	$resultcombo = '<select name="'.$comboname.'"  class="'.$styleclassname.'"  '.$callfunction.'  id="'.$comboname.'"  '.$tabindex.' >';
	
	$resultcombo .= "<option value=\"-1\">-- Please Select --</option>"; 
					
	while($rec = $objDB->fetch_array($res))
	{
	   $optionvalue = $rec[$valuefield];
	   $optionname = stripslashes($rec[$namefield]);
	   $optionlable = stripslashes($rec[$lablefield]);

	   $selected = "";
	   if($selectedid==$optionvalue)
			$selected = " selected";

	   $resultcombo .= "<option value=\"$optionvalue\" $selected >$optionname</option>";
	 }
	 
	$resultcombo .= "</select>";
	return $resultcombo;
  }
 //  
	
function date_ddmmyy_to_yymmdd($ddate)
{
	 
	$d=substr($ddate,0,2);
	$m=substr($ddate,3,2);
	$y=substr($ddate,6,4);
	$t=substr($ddate,11,8);
	 
	if(strtotime("$d-$m-$y") == "") { return false; }
	
	 $condt= "$y-$m-$d";
		
	if(!empty($t)) { $condt .= " $t"; }
	
	return($condt);
}

function date_in_yyyymmdd_new($ddate)
{
	 
	//$condt=date("m-d-y",strtotime($ddate));
	$m=substr($ddate,0,2);
	$d=substr($ddate,3,2);
	$y=substr($ddate,6,4);
	//$t=substr($ddate,11,8);
	
	if(strtotime("$d-$m-$y") == "") { return false; }
	$condt=$y."-".$m."-".$d;//." ".$t;
	return($condt);
}

//************combo box function for des*********************//

function filldescombo($comboname, $sqlstr, $namefield, $valuefield, $selectedid ,$styleclassname, $callfunction)
{
	global $objDB;
	
	$res = $objDB->query($sqlstr);
	$num = $objDB->num_rows($res);
	if($callfunction!="")
		$callfunction = " onchange= '".$callfunction."'";


	$resultcombo = 	"<select name=$comboname  class=$styleclassname $callfunction>
					<option value='-1'>Choisir un jour ---</option>";
	while($rec = $objDB->fetch_array($res))
	{

	   $optionvalue = $rec[$valuefield];
	   $optionname = stripslashes($rec[$namefield]);

	   $selected = "";
	   if($selectedid==$optionvalue)
			$selected = " selected";

	   $resultcombo .= "<option value=$optionvalue $selected >$optionname</option>";
	}
	$resultcombo .= "</select>";
	return $resultcombo;
}



//////////////////////////////////////////////////////////////////////
function TimeComboOption($selectedValue='')
{

	for($t=0 ; $t <= 23; $t++)
	{
		$SuFix = "AM";
		if($t >= 12) { $SuFix = "PM"; }
		
		if($t > 12)  { $Ti = $t-12; } else { $Ti = $t; }
		
		if($Ti <= 9) { $Time = "0$Ti"; } else { $Time = $Ti; }
		
		$timeArray[] = "$Time:00 $SuFix";
		$timeArray[] = "$Time:15 $SuFix";
		$timeArray[] = "$Time:30 $SuFix";
		$timeArray[] = "$Time:45 $SuFix";
	
	}

	foreach($timeArray as $Time)
	{
		if($selectedValue == $Time)
		{
			$TimeOption .= "<option value='$Time' selected >$Time</option>";
		} else {
			$TimeOption .= "<option value='$Time'>$Time</option>";
		}
	}
	
	return $TimeOption;
}
///////////////////////////////////////////////////////////////////////	


//include("include/mysqlDB.php");
function date_in_mmddyyyy($ddate)
{
	 
	//$condt=date("d-m-y",strtotime($ddate));
	$y=substr($ddate,0,4);
	$m=substr($ddate,6,2);
	$d=substr($ddate,8,2);
	//$t=substr($ddate,11,8);
	if(strtotime("$d-$m-$y") == "") { return false; }
	$condt=$m."/".$d."/".$y;//." ".$t;
	return($condt);
}

 function introduceHTMLBR4CR($p_str)
  {
   $lstr="";
   for($i=0;$i<strlen($p_str);$i++)
	{
      $ch=substr($p_str, $i, 1);
	  if($ch=="\r") 
		 $lstr=$lstr."<br>";
	  else
         $lstr=$lstr.$ch;
	} 
	return $lstr;
 } //End of function 


function date_in_ddmmyyyy($ddate)
{
	 
	//$condt=date("d-m-y",strtotime($ddate));
	$y=substr($ddate,0,4);
	$m=substr($ddate,5,2);
	$d=substr($ddate,8,2);
	//$t=substr($ddate,11,8);
	if(strtotime("$d-$m-$y") == "") { return false; }
	$condt=$d."-".$m."-".$y;//." ".$t;
	return($condt);
}

function date_in_ddMonthyyyy($ddate)
{
	 
	//$condt=date("d-m-y",strtotime($ddate));
	$y=substr($ddate,0,4);
	$m=substr($ddate,5,2);
	$d=substr($ddate,8,2);
	//$t=substr($ddate,11,8);
	if(strtotime("$d-$m-$y") == "") { return false; }
	$condt=$d." ".getmonth($m)." ".$y;//." ".$t;
	return($condt);
}

function date_calendar_ddmmyyyy($ddate)
{
	//$condt=date("d-m-y",strtotime($ddate));
	$y=substr($ddate,0,4);
	$m=substr($ddate,5,2);
	$d=substr($ddate,8,2);
	//$t=substr($ddate,11,8);
	if(strtotime("$d-$m-$y") == "") { return false; }
	$condt=$d."/".$m."/".$y;//." ".$t;
	return($condt);
}



function datetime_in_ddmmyyyy($ddate)
{
 	$condt=date("d-m-y",strtotime($ddate));
	$y=substr($ddate,0,4);
	$m=getmonth(substr($ddate,5,2));
	$d=substr($ddate,8,2);
	$t=substr($ddate,11,8);
	
	if(strtotime("$d-$m-$y") == "") { return false; }
	$condt=$d." ".$m." ".$y." ".$t;
	return($condt);
}

function dateMonthNameYear_ddmmyyyy($ddate)
{
 	$condt=date("d-m-y",strtotime($ddate));
	$y=substr($ddate,0,4);
	$m=getmonth(substr($ddate,5,2));
	$d=substr($ddate,8,2);
	$t=substr($ddate,11,8);
	if(strtotime("$d-$m-$y") == "") { return false; }
	$condt=$d." ".$m." ".$y;
	return($condt);
}


function date_in_yyyymmdd($ddate)
{
	
	//$condt=date("d-m-y",strtotime($ddate));
	$d=substr($ddate,0,2);
	$m=substr($ddate,3,2);
	$y=substr($ddate,6,4);
	
	if(strtotime("$d-$m-$y") == "") { return false; }
	
	//$t=substr($ddate,11,8);
	 $condt=$y."-".$m."-".$d;//." ".$t;
	return($condt);
}

function date_in_yyyymm($ddate)
{
	 
	//$condt=date("d-m-y",strtotime($ddate));
	$d=substr($ddate,0,2);
	$m=substr($ddate,3,2);
	$y=substr($ddate,6,4);
	//$t=substr($ddate,11,8);
	
	if(strtotime("$d-$m-$y") == "") { return false; }
	$condt=$y.$m; 
	return($condt);
}

function date_in_yyyymmdd2($ddate)//input date in mm-dd-yyyy format
{
 	//$condt=date("d-m-y",strtotime($ddate));
	$m=substr($ddate,0,2);
	$d=substr($ddate,3,2);
	$y=substr($ddate,6,4);
	//$t=substr($ddate,11,8);
	if(strtotime("$d-$m-$y") == "") { return false; }
	$condt=$y."/".$m."/".$d;//." ".$t;
	return($condt);
}

////
//PARA: Date in [YYYMMDD TIME] Format.
//PARA: $para = [Y,M,D,T]
//RETURN: Separate Value From Date
////
function get_YMDT_FromDate($ddateTime , $para='M')//input date in mm-dd-yyyy format
{
	switch($para)
	{
		case 'T':
			$result=substr($ddate,11,8);
		break;
		case 'Y':
			$result=substr($ddate,6,4);
		break;
		case 'D':
			$result=substr($ddate,3,2);
		break;
		case 'M':
		default:
			$result=substr($ddate,0,2);
		break;
	}//end switch.
	//
	
	return($result);
}
//

function year()
{
	//$condt=date("d-m-y",strtotime($ddate));
	$y=date(Y);

	return($y);
}

function date_in_yyyymmdd1($ddate)//04/28/2005
{
 	$m=substr($ddate,0,2);
	$d=substr($ddate,3,2);
	$y=substr($ddate,6,4);
	if(strtotime("$d-$m-$y") == "") { return false; }
	$condt=$y."-".$m."-".$d;
	return($condt);
}


function date_for_db($ddate)//04/28/2005
{
 	 
	
	$d=substr($ddate,0,2);
	$m=substr($ddate,3,2);
	$y=substr($ddate,6,4);
	 
	$condt=$y."-".$m."-".$d;
	return($condt);
}


function shortString($str)
{
	if(strlen($str)>100)
		$str = substr($str,0,95)."...";
	else
		$str=$str;
	return $str;
}

function shortString_new($str)
{
	if(strlen($str)>205)
		$str = substr($str,0,200)."...";
	else
		$str=$str;
	return $str;
}


function get_dayname($date)
{
	if($date=="")
	{
		$weekday = date('l');//Return current today name
	}
	else
	{
		$weekday = date('l', strtotime($date)); // Return dayname of provided Date.
	}
	
	return $weekday;
}


function populate_combo($sql_combo,$str_label,$str_value, $cbo_name, $size, $multiple, $style, $selected_values,$str_caption="",$jscript_fun="",$extrafield="")
{
	global $objDB;
	
//	GLOBAL $database;
	//$rs_sql_combo = mysql_query ($sql_combo) or die("Unable to populate dropdown ".$cbo_name." because ".mysql_error());
	//echo'hii'.$sql_combo;
	//exit;
	$rs_sql_combo = $objDB->query($sql_combo); 
	
	//if "," is found in the values supplied then split it and store it in array
	$is_arr = "false";
	if(strpos($selected_values,",") !== false)
	{
		$arr_selected = split (",", $selected_values);
		$is_arr = "true";
	}
	else
	{
		$arr_selected = $selected_values;
		$is_arr = "false";
	}

	//echo "arr_selected " .$arr_selected;

	//Checking for "," in $str_label.If found, then split it else directly use it
	$is_label_arr = "false";

	if(strpos($str_label,",") !== false)
	{
		$arr_label = split (", ", $str_label);
		$is_label_arr = "true";
	}
	else
	{
		$arr_label = $str_label;
		$is_label_arr = "false";
	}
	
	$str = "<select name='$cbo_name' style='$style' size='$size' $multiple ' $jscript_fun>";
	$str .= "<option value='0' >Select </option>";
	if($str_caption !="")
		$str .= "<option value='0' >$str_caption</option>";
	while ($row_sql_combo = $objDB->fetch_array($rs_sql_combo))
	{
		$selected = "";
		
		$lbl = "";
		if ($is_label_arr == "true")
		{
			for ($j=0;$j<count($arr_label);$j++)
			{
				$lbl .= $row_sql_combo[$arr_label[$j]] . " ";
			}
		}
		else
		{
			
			$lbl = $row_sql_combo[$arr_label];
			
		}
		
		$vl = $row_sql_combo[$str_value];
		
		$exvl = $row_sql_combo[$extrafield];
		if ($is_arr == "true")
		{
			for ($i=0;$i<count($arr_selected);$i++)
			{
				if ($vl == $arr_selected[$i])
				{
					$selected = "selected";
					break;
				}
			}
		}
		else if ($is_arr == "false")
		{
			if ($vl == $arr_selected)
			{
				$selected = "selected";
			}
		}
		if($extrafield !="")
		{
			$exvl = 'ô'.$exvl;
		}
		$str .= "<option value='$vl$exvl' $selected>$lbl</option>";
	}
	$str .= "</select>";
	return $str;
}



////
//
//
////
function getLastDateOfTheMonth($month='' , $year='')
{
	$month=($month=="") ? date('m') : $month;
	$year =($year=="") ? date('Y') : $year;
	
	switch($month)
	{	
		case "1":
		case "01":
		case "3":
		case "03":
		case "5":
		case "05":
		case "7":
		case "07":
		case "8":
		case "08":
		case "10":
		case "12":
			$days=31;
		break;
		
		case "2":
		case "02":
			//Check Year Is Leap Or Not
			if(($year%4==0 && $year%100!=0) || ($year%100==0 && $year%400==0))
			{
				$days=29;
			} else {
				$days=28;
			}
		break;
		
		case "4":
		case "04":
		case "6":
		case "06":
		case "9":
		case "09":
		case "11":
			$days=30;
		break;
		
	}//end switch.
	return $days;
}
////


function getDisplayDate($dt_stamp, $fmt, $separator)
{
  $dt_format_arr=array();

//FORMAT   :   01-Jan-2004 hh:mi:ss AM
  $dt_format_arr[1]='d'.$separator.'M'.$separator.'Y';   // SET AS DEFAULT
  $dt_format_arr[2]='d'.$separator.'M'.$separator.'Y h:i';
  $dt_format_arr[3]='d'.$separator.'M'.$separator.'Y h:i:s';
  $dt_format_arr[4]='d'.$separator.'M'.$separator.'Y h:i A';
  $dt_format_arr[5]='d'.$separator.'M'.$separator.'Y h:i:s A';

//FORMAT   :  mm-dd-yyyy hh:mi:ss AM
  $dt_format_arr[6]='m'.$separator.'d'.$separator.'Y';
  $dt_format_arr[7]='m'.$separator.'d'.$separator.'Y h:i';
  $dt_format_arr[8]='m'.$separator.'d'.$separator.'Y h:i:s';
  $dt_format_arr[9]='m'.$separator.'d'.$separator.'Y h:i A';
  $dt_format_arr[10]='m'.$separator.'d'.$separator.'Y h:i:s A';

// FORMAT  :  1st Jan 2004 hh:mi:ss AM
  $dt_format_arr[11]='dS'.$separator.'M'.$separator.'Y';
  $dt_format_arr[12]='dS'.$separator.'M'.$separator.'Y h:i';
  $dt_format_arr[13]='dS'.$separator.'M'.$separator.'Y h:i:s';
  $dt_format_arr[14]='dS'.$separator.'M'.$separator.'Y h:i A';
  $dt_format_arr[15]='dS'.$separator.'M'.$separator.'Y h:i:s A'; 
  
  if($fmt>15)
	  $fmt=1;
  
  return date($dt_format_arr[$fmt], $dt_stamp);
}


////
//
//
////
function getmonth($month , $fullformat='1')
{
	if($fullformat){
		$mt = date('F' , mktime(0, 0, 0, $month, 1, 2000));
	} else {
		$mt = date('M' , mktime(0, 0, 0, $month, 1, 2000));
	}
	
	return $mt;
}




function getImageResize ($pic,$largestside)
{
		
	// This is the size of the LARGEST SIZE we want either our width or height to be
	$size = GetImageSize ("$pic");  

	// Get the actual width/height of the image...

	$width = $size[0];
	$height = $size[1];

	if ($width == $height) 
	{             
		// If the height == width
		$dimensions[0] = $largestside;      
		// Assign both width and height the value of $largestsize
		$dimensions[1] = $largestside;
	}
	elseif ($width > $height) 
	{               
		// If the width is greater than the height
		$divisor = $width / $largestside;
		$height = $height / $divisor;

		$dimensions[0] = $largestside;        // Assign $largestsize to width
		$dimensions[1] = intval($height);   

		// and assign $height a proportionate value to $height
	} 
	elseif ($width < $height) 
	{   // If width is less than height
		$divisor = $height / 120;
		$width = $width / $divisor;
		$dimensions[0] = intval ($width);        // Set width to be proportionate to height
		$dimensions[1] = 120;                // Set height to be $largestsize
	}

	return $dimensions;
	
}

function CreateFileWith_ext($file_name)
{
	$file_directory = "../"; //the directory you want to store the new file in

	$file_name = strip_tags($file_name);//the file's name, stripped of any dangerous tags

	$file_ext = strip_tags($file_ext); //the file's extension, stripped of any dangerous tags

	$file = $file_directory.$file_name; //this is the entire filename

	$create_file = fopen($file, "w+"); //create the new file



	if(!$create_file)

	{

	die("There was an error creating/opening the file! Make sure you have the correct permissions!\n");

	}

	$chmod = chmod($file, 0755); //set the appropriate permissions.

	//attempt to set the permissions

	if(!$chmod) 

	{ 

	echo("There was an error changing the permissions of the new file!\n"); //error changing the file's permissions

	}

	if (fwrite($create_file, "Content goes here!") === FALSE) 
	{
		echo "Error writing to file: ($file)\n";
	}

	fclose($create_file);
}
//


////
//Return Past date according to send parameter.
//PARA: No_OF_DAY , No_OF_MONTH , No_OF_YEARS
function get_previous_date( $no_of_previous_day=0 , $no_of_previous_month=0 , $no_of_previous_years=0)
{

$fday = $no_of_previous_day;
$fmonth = $no_of_previous_month;
$fyear = $no_of_previous_years;

	$d=date(j)- $fday;
	$m=date(n)- $fmonth;
	$y=date(Y)- $fyear;

	if($d > 31)
	{
		while($d > 31)
		{
			$d-=31;
			$m+=1;
		}
	}
	if($m>12)
	{
		while($m>12)
		{
			$m-=12;
			$y+=1;
		}
	}
	//below sets sequence: day/month/year
	if($d <10) $d='0'.$d;
	
	$r = getmonth($m).'-'.$d.'-'.$y;
	
	return $r;

}
//
////


////
//
//get_future_date(number_of_days,number_of_months,number_of_years);
//below returns a future date (day/month/year)  
//$expire_date = get_future_date(0,3,0);  //a bit of theory
//Return Future date according to send parameter.
//PARA: No_OF_DAY , No_OF_MONTH , No_OF_YEARS
function get_feature_date( $no_of_feature_day=0 , $no_of_feature_month=0 , $no_of_feature_years=0)
{

$fday = $no_of_feature_day;
$fmonth = $no_of_feature_month;
$fyear = $no_of_feature_years;

	$d=date(j)+$fday;
	$m=date(n)+$fmonth;
	$y=date(Y)+$fyear;

	if($d > 31)
	{
		while($d > 31)
		{
			$d-=31;
			$m+=1;
		}
	}
	if($m>12)
	{
		while($m>12)
		{
			$m-=12;
			$y+=1;
		}
	}
	//below sets sequence: day/month/year
	if($d <10) $d='0'.$d;
	
	$r = "$y-$m-$d";
	
	return $r;

}
//
////


////
//
//
function get_next_date($theDate='' , $afterDays=1)
{
	if($theDate=="") { $theDate = date('Y-m-d'); }
	
	 $timeStamp = StrToTime($theDate);
	 $indays = StrToTime('+'.$afterDays.' days', $timeStamp);
	 $nextDate = date('Y-m-d', $indays); 

	return $nextDate;
}


////




////(Format: //$mt="January" Or //$mt="Jan";)
//
function getmonth_number($mtname)
{
	$month = 0;
	switch($mtname)
	{		
		case "Jan":
		case "January":
			$month = "01";
		break;
		
		case "Feb":
		case "February":
		 	$month="02";
		break;
		
		case "Mar":
		case "March":
			$month="03";
		break;
		
		case "Apr":
		case "April":
		 	$month="04";
		break;
		
		case "May":
		 	$month="05";
		break;
		
		case "Jun":
		case "June":
		 	$month="06";
		break;
		
		case "Jul":
		case "Jully":
		 	$month="07";
		break;
		
		case "Aug":
		case "August":
		 	$month="08";
		break;
		
		case "Sept":
		case "September":
		 	$month="09";
		break;
		
		case "Oct":
		case "October":
		 	$month="10";
		break;
		
		case "Nov":
		case "November":
		 	$month="11";
		break;
		
		case "Dec":
		case "December":
			$month="12";
		break;
		
	}
	
	return $month;
}


////
//
function isValidMonth( $month )
{
 	if( !empty($month) && $month > 0 && $month <= 12) { return true; } else { return false; }
}
//
////

////
//
function isValidYear( $year )
{
 	if( strlen($year) == 4 && is_numeric( $year ) ) { return true; } else { return false; }
}
//
////



////
//Generate Random Password.
//
////
function generateRandpassword($size=6, $power=8) {
    $vowels = 'aeuy';
    $randconstant = 'bdghjmnpqrstvz';
    if ($power & 1) {
        $randconstant .= 'BDGHJLMNPQRSTVWXZ';
    }
    if ($power & 2) {
        $vowels .= "AEUY";
    }
    if ($power & 4) {
        $randconstant .= '23456789';
    }
    if ($power & 8) {
        $randconstant .= '@#$%';
    }

    $Randpassword = '';
    $alt = time() % 2;
    for ($i = 0; $i < $size; $i++) {
        if ($alt == 1) {
            $Randpassword .= $randconstant[(rand() % strlen($randconstant))];
            $alt = 0;
        } else {
            $Randpassword .= $vowels[(rand() % strlen($vowels))];
            $alt = 1;
        }
    }
    return $Randpassword;
}



////
//Age Calculate
////
function get_age($dob) {

    list($y,$m,$d) = explode('-', $dob);
    
    if (($m = (date('m') - $m)) < 0) {
        $y++;
    } elseif ($m == 0 && date('d') - $d < 0) {
        $y++;
    }
    
    return date('Y') - $y;
    
}



function dateTimeDiff($data_ref){

// Get the current date
$current_date = date('Y-m-d H:i:s');

// Extract from $current_date
$current_year = substr($current_date,0,4);
$current_month = substr($current_date,5,2);
$current_day = substr($current_date,8,2);

// Extract from $data_ref
$ref_year = substr($data_ref,0,4);
$ref_month = substr($data_ref,5,2);
$ref_day = substr($data_ref,8,2);

// create a string yyyymmdd 20071021
$tempMaxDate = $current_year . $current_month . $current_day;
$tempDataRef = $ref_year . $ref_month . $ref_day;

$tempDifference = $tempMaxDate-$tempDataRef;

// If the difference is GT 10 days show the date
if($tempDifference >= 10){
echo $data_ref;
} else {

// Extract $current_date H:m:ss
$current_hour = substr($current_date,11,2);
$current_min = substr($current_date,14,2);
$current_seconds = substr($current_date,17,2);

// Extract $data_ref Date H:m:ss
$ref_hour = substr($data_ref,11,2);
$ref_min = substr($data_ref,14,2);
$ref_seconds = substr($data_ref,17,2);

$hDf = $current_hour-$ref_hour;
$mDf = $current_min-$ref_min;
$sDf = $current_seconds-$ref_seconds;

// Show time difference ex: 2 min 54 sec ago.
if($dDf<1){
if($hDf>0){
if($mDf<0){
$mDf = 60 + $mDf;
$hDf = $hDf - 1;
echo $mDf . ' min ago';
} else {
echo $hDf. ' hr ' . $mDf . ' min ago';
}
} else {
if($mDf>0){
echo $mDf . ' min ' . $sDf . ' sec ago';
} else {
echo $sDf . ' sec ago';
}
}
} else {
echo $dDf . ' days ago';
}


}//End else.


}


////
//PARA: Start Date FORMAT (YYYY-MM-DD)
//PARA: Start Date FORMAT (YYYY-MM-DD)
////
function dateDiff($startDate, $endDate) 
{

	$start_ts = strtotime($startDate);
	
	$end_ts = strtotime($endDate);
	
	$diff = $end_ts - $start_ts;
	
	return round($diff / 86400);

}


function Randpasswd( $length=8 ) {

$chars = "abcdefghijklmnopqrstuvwxyz*~)@(><?+^{}[]!%ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
return substr(str_shuffle($chars),0,$length);

}



function IsvalidEmailId($email) 
{
	if(preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $email))
		return true;
	return false;
}



function NumberFormat($number , $decimal_point=2 )
{
	$english_format_number = number_format($number, $decimal_point, '.', ',');
}

 

function sp_trim($str)
{
	return $str = str_replace( ' ' , '' , $str);
}





function sumArray($array)
{
	if(is_array($array))
	{
		//$sumValue = 0;
		foreach($array as $key=>$value)
		{
			if(!is_numeric($value))
			{
				 $value = (int)$value;
			}
			
			 $sumValue += $value; 
			
		}//end foreach.

	}//end if.
	return $sumValue;
}

//--------------------New Date Functions -------------------------

function last_day_of_feb($year , $dateFormat ='Y-m-d') {
# The 0th day of a month is the same as the last day of the month before
        $ultimo_feb_str = $year . "-03-00"; 
         $ultimo_feb_date = date_create($ultimo_feb_str);
        $return = date_format($ultimo_feb_date, $dateFormat);
        return $return;
}


////
//
function DaysInMonth($month , $year)
{
	$day = '01';
	
	$date = date_create("$year-$month-$day");
	
	return $noofdays = date_format($date, 't');
}
//
////






///////////////////////////////////////////////////////////////////////////////////
// #Format: Y-m-d H:i:s 		=> 	#output: 2012-03-24 17:45:12
// #Format: Y-m-d h:i A			=> 	#output: 2012-03-24 05:45 PM
// #Format: d/m/Y H:i:s 		=> 	#output: 24/03/2012 17:45:12
// #Format: d/m/Y 				=> 	#output: 24/03/2012
// #Format: g:i A 				=> 	#output: 5:45 PM
// #Format: h:ia 				=> 	#output: 05:45pm
// #Format: g:ia \o\n l jS F Y 	=> 	#output: 5:45pm on Saturday 24th March 2012
// #Format: l jS F Y 			=> 	#output: Saturday 24th March 2012
// #Format: D jS M Y 			=> 	#output: Sat 24th Mar 2012
// #Format: jS F Y g:ia			=> 	#output: 24th March 2012 5:45pm
// #Format: j F Y				=> 	#output: 24 March 2012
// #Format: j M y				=> 	#output: 24 Mar 12
// #Format: F j					=> 	#output: March 24 
// #Format: F Y					=> 	#output: March 2012
/////////////////////////////////////////////////////////////////////////////////////
function DateTimeFormat($dateTime , $dateFormat = 'jS M Y' )
{
	$date = date_create($dateTime);
	
	$newDateFormat = date_format($date, $dateFormat);
	
	$newDateFormat = str_replace('th ' , '<sup>th </sup>' , $newDateFormat);
	$newDateFormat = str_replace('1st ' , '1<sup>st </sup>' , $newDateFormat);
	$newDateFormat = str_replace('nd ' , '<sup>nd </sup>' , $newDateFormat);
	$newDateFormat = str_replace('rd ' , '<sup>rd </sup>' , $newDateFormat);
	
	return $newDateFormat;
}
//
////





//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//PARA: Date Should In YYYY-MM-DD Format
//RESULT FORMAT:
// '%y Year %m Month %d Day %h Hours %i Minute %s Seconds'		=>  1 Year 3 Month 14 Day 11 Hours 49 Minute 36 Seconds
// '%y Year %m Month %d Day'									=>  1 Year 3 Month 14 Days
// '%m Month %d Day'											=>  3 Month 14 Day
// '%d Day %h Hours'											=>  14 Day 11 Hours
// '%d Day'														=>  14 Days
// '%h Hours %i Minute %s Seconds'								=>  11 Hours 49 Minute 36 Seconds
// '%i Minute %s Seconds'										=>  49 Minute 36 Seconds
// '%h Hours													=>  11 Hours
// '%a Days														=>  468 Days
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function dateDifference($date_1 , $date_2 , $differenceFormat = '%a' )
{
	$datetime1 = date_create($date_1);
	$datetime2 = date_create($date_2);
	
	$interval = date_diff($datetime1, $datetime2);
	
	return $interval->format($differenceFormat);
	
}
//
////



function Redirection($url)
{
?>
<script type="text/javascript">

window.location = "<?php echo $url ?>" ;

</script>

<?php } ?>

