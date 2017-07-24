<?php

class RouterEngine{

    public $sid = null;
    public $UUID = null;

    public $TSP = null;
    function getResult($params){
      
        $this->__initParams($params);
 
        $inputPath = $params['data'];
        include(APP."lib".DS."routeApi.php");
        include(APP."lib".DS."Polyline.php");
        $engine = routeApi::getApi(ROUTE_API);
        
        $routeMatrix = $this->__routeMatrix($inputPath,$engine);
         
        $this->__checkInValidWaypoint($routeMatrix['rows'][0]);
        
        include(APP."lib".DS."tsp.php");
        $this->TSP = new TSP;
    
 
        $this->__addLocationsToTSP($routeMatrix['rows']);
     //   $this->showDetail($this->TSP);
     //   common::log($this->TSP->routes());

        $second_of_min = 60;
        $output = array(
            'status'=>ROUTE_API_STATUS_SUCCESS,
            'path'=>$this->__getShortestPath($inputPath),
            'total_distance'=>$this->TSP->total_distance(),
            'total_time'=>($this->TSP->total_duration()*$second_of_min)*DRIVING_TIME_GOOGLE_OFFSET_RATE,
        );

        $this->__updateTokenResult($output);
      
    }

    private function __checkInValidWaypoint($firstRow){

            foreach($firstRow['elements'] as $col){
                if($col['status'] != 'OK'){
                    $this->__updateTokenResult(array(
                        'status'=>ROUTE_API_STATUS_FAILURE,
                        'error' =>ROUTE_API_ERROR_MSG_WAYPOINT_INVAILD,
                        ),$firstRow);
         
                }
            }
        
    }
    private function __routeMatrix($inputPath,$engine){
      
        $polyline = Polyline::encode($inputPath);
        $routeMatrix =  $engine->getRouteByPolyline($polyline);
       //$routeMatrix =  $engine->getRoute($inputPath);

        if($routeMatrix===false){
            $this->__updateTokenResult(array(
                'status'=>ROUTE_API_STATUS_FAILURE,
                'error' =>ROUTE_API_ERROR_MSG_NO_RETURN
            ));
        }
        if($routeMatrix['status'] !== 'OK'){
            
            $this->__updateTokenResult(array(
                'status'=>ROUTE_API_STATUS_FAILURE,
                'error' =>ROUTE_API_ERROR_MSG_RETURN_ERROR
            ),$routeMatrix);
        }
        return $routeMatrix;

    }


    private function __getShortestPath($inputPath){
        $shortest_route = $this->TSP->shortest_route();
        $shortestPath = array();
        foreach($shortest_route as $v){
            $shortestPath[]=$inputPath[$v];
        }
        return $shortestPath;

    }
    private function __initParams($params){
        if(isset($params['UUID'])==false ||
            isset($params['session_id'])==false ||
            isset($params['data'])==false ){
             
            $this->__updateTokenResult(array(
                'status'=>ROUTE_API_STATUS_FAILURE,
                'error' =>ROUTE_API_ERROR_MSG_PARAMS_INVAILD
            ),$params);    
        }
        
        $this->UUID = $params['UUID'];
        $this->sid = $params['session_id'];
    }



    private function showDetail($tsp){
            echo 'Shortest Cost: '.$tsp->shortest_cost();
            echo '<br />total_distance: '.$tsp->total_distance();
            echo '<br />total_duration: '.$tsp->total_duration();
            echo '<br />Shortest Route: ';
            echo '<pre>';
            print_r($tsp->shortest_route());
            echo '</pre>';
            echo '<br />Num Routes: '.count($tsp->routes());

            echo '<br />Matching shortest Routes: ';
            echo '<pre>';
            print_r($tsp->matching_shortest_routes());
            echo '</pre>';
            echo '<br />All Routes: ';
            echo '<pre>';
            print_r($tsp->routes());
            echo '</pre>';


    }
    private function __updateTokenResult($data,$errorLog = false){
        if($errorLog){
            common::log($errorLog);
        }
        common::sessionStartById($this->sid);
        $_SESSION[$this->UUID] = $data;
        exit();
    }




    private function __addLocationsToTSP($locationsMatrix){
       foreach($locationsMatrix as $k=>$row){
           $distanceMap = array();
           $durationMap = array();
           foreach($row['elements'] as $colK=>$colV){
                $distanceMap[$colK]=$colV['distance']['value'];
                $durationMap[$colK]=$colV['duration']['value'];
           }
        
           $this->TSP->addByDistance($k,$distanceMap,$durationMap);
   
       }  
      $this->TSP->setStartPoint(0);
       $this->TSP->compute('duration');
   }

  

}