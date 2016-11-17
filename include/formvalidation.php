<?PHP
//server side validation.
class FormValidator

	{

	//

	// private variables

	//

	var $_errorList;
  
	// constructor

	// reset error list

	function FormValidator()

	{

	$this->resetErrorList();

	}


 
	// function to get the value of a variable (field)

	function _getValue($field)
	{
		ini_set("register_globals","1");
		global ${$field};
		return ${$field};
		//return $_REQUEST[$field];
	}

	// check whether input is empty

	function isEmpty($field, $msg)
	{

		$value = $this->_getValue($field);

		if (trim($value) == "")

		{

		$this->_errorList[] = array("field" => $field,"value" => $value, "msg" => $msg);

		return false;

		}

		else

		{

		return true;

		}

	}

	function isComboEmpty($field, $msg)
	{

		$value = $this->_getValue($field);

		if (trim($value) == "-1")
		{

		$this->_errorList[] = array("field" => $field,"value" => $value, "msg" => $msg);

		return false;

		}

		else

		{

		return true;

		}

	}
	

 
// check whether input is a valid email address

	function isEmailAddressValid($field, $msg)

	{

	$value =  $field;


	$pattern = "/^[a-zA-Z0-9_\.\-]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-\.]+$/";
	

	if(preg_match($pattern, $value))

	{

	return true;

	}

	else

	{

	$this->_errorList[] = array("field" => $field,

	"value" => $value, "msg" => $msg);

	return false;

	}

 }



// check whether input is a valid email address

	function isEmailAddress($field, $msg)

	{

	$value = $this->_getValue($field);


	$pattern = "/^[a-zA-Z0-9_\.\-]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-\.]+$/";


	if(preg_match($pattern, $value))

	{

	return true;

	}

	else

	{

	$this->_errorList[] = array("field" => $field,

	"value" => $value, "msg" => $msg);

	return false;

	}

 }


function IsCompareEmail($field1,$field2, $msg)
{

	$value1 = $this->_getValue($field1);
	$value2 = $this->_getValue($field2);
	if (trim($value1) != trim($value2))

	{

	$this->_errorList[] = array("field" => $field1,"value" => $value1, "msg" => $msg);

	return false;

	}

	else

	{

	return true;

	}

}

function GetLength($field, $msg)
{

	$value = $this->_getValue($field);
	
	$len = strlen($value);

	if ($len<6)

	{

	$this->_errorList[] = array("field" => $field1,"value" => $value1, "msg" => $msg);

	return false;

	}

	else

	{

	return true;

	}

}


	// check whether any errors have occurred in validation

	// returns Boolean

	function isError()

	{

		if (sizeof($this->_errorList) > 0)
	
		{
	
		return true;
	
		}
	
		else
	
		{
	
		return false;
	
		}

	}





	// return the current list of errors

	function getErrorList()

	{

		return $this->_errorList;

	}

 
	// reset the error list

	function resetErrorList()

	{

		$this->_errorList = array();

	}

}
?>