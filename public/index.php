<?php
session_start();
//error_reporting(E_ALL & ~E_NOTICE);


 include("Config/core.php");



 $routerLogic = function(){

    $paths = explode('/',$_SERVER['REQUEST_URI']);
        $realPath = array();
        foreach($paths as $k=>$v){
            if($v){
            $realPath[] = $v;
            }
        }
        if($realPath[0]=='route' && count($realPath)==1){
            include("Controller".DS."DrivingRouter.php");
             $api = new DrivingRouter();
             $api->getToken();
             exit();
    //             include("Controller".DS."RouterEngine.php");
    //   $prarms = array();
    // $api = new RouterEngine();
    // $api->getResult($prarms);
    // exit();
        }
        if($realPath[0]=='route' && count($realPath)==2){
            include("Controller".DS."DrivingRouter.php");
        
             $api = new DrivingRouter();
             $api->getResult($realPath[1]);
              exit();
        }
        header("HTTP/1.0 404 Not Found");
        exit();
 };

 common::Dispatcher($routerLogic);

