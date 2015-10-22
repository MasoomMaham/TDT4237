<?php

namespace tdt4237\webapp\validation;

class sqlValidation
{
	//private $validationErrors = [];

	/*public function inputFieldCheck($input)
	{
		if
	}*/

	public static function whiteBlackListSQL($input)
	{
		$input = strtolower($input);
		$whiteList = "abcdefghijklmnopqrstuvwxyzæøå1234567890!@#£¤$%&/(){}[]?±*^~§|¦:.,-_ ";
		//$blackList = [" and "," or "," concat "," = "," ; "," from users ", " from posts ", " from comments "];

		/*for($i = 0; $i < sizeof($blackList); $i++) {
			if((strpos($input, $blackList[$i])) === true){
				return false;
			}
		}*/

		/*for($i = 0; $i < sizeof($whiteList); $i++) {
			if(!(strpos($input, $whiteList[$i])) === false){
				return false;
			}
		}*/


		return true;

	}
}