<?php

class DrivingRouter{

    function getToken(){
            $isValid = $this->__checkDataValid();
            if($isValid!==true){
                $this->__response(array(
                'error'=>$isValid,
                ));
            }
            

            $data = common::$request['post']['paths'];
            $token = common::genUUID();
            $path = "/RouterEngine";
            $params = array(
                'session_id'=>session_id(),
                'UUID'=>$token,
                'data'=>$data,
            );
        
            $_SESSION[$params['UUID']] = array('status'=>ROUTE_API_STATUS_PROGRESS,'timeStamp'=>time());

            $data = common::callPhpAsynchronous($path,$params);
             $this->__response(array(
                'token'=>$token,
            ));

    }
    private function __checkDataValid(){

        if($this->robotPrevent()==false){
            return GET_TOKEN_ERROR_MSG_ROBOT_CHECK;
                
        }
        if(isset(common::$request['post'])==false ||
             isset(common::$request['post']['paths'])==false ||
             is_array(common::$request['post']['paths']) == false
         ){
             return GET_TOKEN_ERROR_MSG_NO_WAYPOINT;
        }
        if(count(common::$request['post']['paths'])<2){
                 return GET_TOKEN_ERROR_MSG_WAYPOINT_MIN_2;
        }
        if(count(common::$request['post']['paths'])>WAYPOINT_MAX){
                 return GET_TOKEN_ERROR_MSG_WAYPOINT_MAX_10;
        }
        return true;
    }
    function getResult($token){
        $output = array(
            'status'=>ROUTE_API_STATUS_FAILURE,
            'error' =>ROUTE_API_ERROR_MSG_TOKEN_NOT_EXIST
        );
        if(isset($_SESSION[$token])){
        
            $output = $_SESSION[$token];
           
        }
        if($output['status']==ROUTE_API_STATUS_PROGRESS &&
            time() > ($output['timeStamp'] + PROGRESS_TIMEOUT_SECOND)
         ){
            $output = array(
                'status'=>ROUTE_API_STATUS_FAILURE,
                'error' =>ROUTE_API_ERROR_MSG_TIMEOUT
            );
        }
        unset($output['timeStamp']);
        $this->__response($output);


    }

    private function __response($output){

       header('Content-Type: application/json');
       echo json_encode($output);
       exit();
    }


    function getHttpRequestData(){
        $data = array(
        array("22.372081", "114.107877"),
        array("22.284419", "114.159510"),
        array("22.326442", "114.167811"),
        );
        return $data;
    }
    function robotPrevent(){
        return true;
    }

}