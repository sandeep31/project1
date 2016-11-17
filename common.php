<?php

function getCity($cityId='', $stateId='')
{
	global $SQL;
        $sqlString = '';
        $resultType='RecordArray';
	 
   if($stateId > 0 && $stateId != '') {
	   $sqlString .= " AND state_id = '$stateId' ";
	   $resultType = 'RecordArray';
   } 
	
    if($cityId > 0 && $cityId != '') {
	   $sqlString .= "  AND city_id = '$cityId' ";
	   $resultType='Record';
   } 
	 
   
    $sqlQuery = "SELECT city_id , city_name, state_id 
			FROM city 
			WHERE is_active = '1'  $sqlString 
			ORDER BY city_name ";
   
	$data = $SQL->getSqlData( $sqlQuery , $resultType );
	 
	if(is_array($data))
	{
		foreach($data as $key=>$row)
		{
			if($resultType == "Record") {
				$result = __x($data['city_name']);
			} else {
				$result[$city_id] = __x($data['city_name']);
			}
		}
		 
		return $result;
	}
	
}//end function getCity().


function getCityName($cityId)
{
	global $SQL;
	 
    $sqlQuery = "SELECT  city_name 
			FROM city 
			WHERE  city_id = '$cityId' ";
   
	$data = $SQL->getSqlData( $sqlQuery , 'Record' );
	 
		return  __x($data['city_name']);
 
	
}//end function getCity().

function getState($stateId='', $countryId='')
{
	global $SQL;
	 $sqlString = '';
	 $resultType='RecordArray';
	 
   if($stateId > 0 && $stateId != '') {
	   $sqlString .= " AND state_id = '$stateId' ";
	   $resultType='Record';
   } 
	
	if($countryId > 0 && $countryId != '') {
	   $sqlString .= "  AND country_id = '$countryId' ";
	   $resultType='RecordArray';
   } 
   
    $sqlQuery = "SELECT state_id , state_name 
				FROM state 
				WHERE is_active = '1' $sqlString 
				ORDER BY state_name ";
   
	$data = $SQL->getSqlData( $sqlQuery , $resultType );
	 
	if(is_array($data))
	{
		foreach($data as $key=>$row)
		{
			if($resultType == "Record") {
				$result = __x($data['state_name']);
			} else {
				$result[$state_id] = __x($data['state_name']);
			}
		}
		
		return $result;
	}
	
}//end function getState().


/*
*
*/
function getCountry($countryId=1 )
{
	global $SQL;
	 $sqlString = '';
	 
   if($countryId > 0 && $countryId != '') {
	   $sqlString .= " AND country_id = '$countryId' ";
   } 
   
   $sqlQuery = "SELECT country_id , country_name 
				FROM country 
				WHERE is_active = '1' $sqlString 
				ORDER BY country_name ";
   
	$data = $SQL->getSqlData( $sqlQuery );

	if(is_array($data))
	{
		foreach($data as $key=>$row)
		{
			 
			$result[$data['country_id']] = __x($data['country_name']);
			 
		}
		
		return $result;
	}
	
}//end function getCountry().





function getStateData($countryId='')
{
	global $SQL;
	 
  $country = ($countryId == '') ? 1 : $countryId;
   
   $sqlQuery = "SELECT state_id , state_name FROM state WHERE is_active = '1' AND country_id = '$country' ORDER BY state_name ";
   
	$data = $SQL->getSqlData( $sqlQuery , $resultType='RecordArray' );

	if(is_array($data))
	{
		foreach($data as $key=>$row)
		{
			extract($row);
			
			$stateArray[$state_id] = $state_name;
		}
		
		return $stateArray;
	}
	
}//end function getStateData().


function getCityData($cityIds='', $state_id='')
{
	global $SQL;
	 $whereState = $whereCity ="";
	 
	 if($cityIds != '') {
		 
		if(is_array($cityIds))
		{
			$city_ids = join(',' , $cityIds);
			
			$whereCity = " AND city_id IN ($city_ids) ";
			
		} else {
			
			$whereCity = " AND city_id IN ($cityIds) ";
			
		}
		
	 } else if($state_id != ''){
		 
		$whereState = " AND state_id = '$state_id' ";
	 }
   
   echo $sqlQuery = "SELECT city_id , city_name FROM city WHERE is_active = '1' $whereCity $whereState ORDER BY city_name ";
   
	$data = $SQL->getSqlData( $sqlQuery , $resultType='RecordArray' );

	if(is_array($data))
	{
		foreach($data as $key=>$row)
		{
			extract($row);
			
			$dataArray[$city_id] = $city_name;
		}
		
		return $dataArray;
	}
	
}//end function




function getCountries()
{
	global $SQL;
	
	$sqlQuery = "SELECT country_name, country_id FROM country WHERE is_active = '1' ORDER BY country_name";
	
	$data = $SQL->getSqlData( $sqlQuery , $resultType='RecordArray' );

	if(is_array($data))
	{
		foreach($data as $key=>$row)
		{
			extract($row);
			
			$countryArray[$country_id] = $country_name;
		}
		
		return $countryArray;
	}
}



function getJobApplicationCount($job_id)
{
    global $SQL;
    $sqlQuery = "SELECT count(*) num FROM job_seeker_post_detail where job_post_detail_id = '$job_id' and is_active = '1' ";
    $rec = $SQL->getSqlData( $sqlQuery , $resultType='Record' );
    return ($rec['num']) ? $rec['num'] : 0;
}










?>
