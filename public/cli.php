<?php

if(php_sapi_name() !=='cli'){
    exit();
}
if(isset($_SERVER['argv'][1]) == false){
    exit();
}
if(isset($_SERVER['argv'][2]) == false){
    exit();
}

include("/application/public/Config/core.php");

   
 $routerLogic = function(){
 
    $path = urldecode($_SERVER['argv'][1]);
    
    $prarms = json_decode(urldecode($_SERVER['argv'][2]),1);
    
    include("Controller".DS."RouterEngine.php");
      
    $api = new RouterEngine();
    $api->getResult($prarms);

 };

 common::Dispatcher($routerLogic);
