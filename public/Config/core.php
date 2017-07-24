<?php

define("APP","/application/public/");
define("DS","/");

 include(APP."Config".DS."config.php");
 include(APP."lib".DS."appRouter.php");
 include(APP."Config".DS."router.php");
 include(APP."lib".DS."common.php");



 if (!function_exists('pr')) {
 function pr($t){
    echo '<pre>';
    print_r($t);
    echo '</pre>';
 }
}

 if (!function_exists('stripslashes_deep')) {
	function stripslashes_deep($values) {
		if (is_array($values)) {
			foreach ($values as $key => $value) {
				$values[$key] = stripslashes_deep($value);
			}
		} else {
			$values = stripslashes($values);
		}
		return $values;
	}

}
if (!function_exists('isJson')) {
	function isJson($string) {
	json_decode($string);
	return (json_last_error() == JSON_ERROR_NONE);
	}
}
