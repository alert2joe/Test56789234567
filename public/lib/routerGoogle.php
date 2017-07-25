<?php
if(class_exists('getApi')===false){
 //   include(APP."lib".DS."routeApi.php");
}

class routerGoogle extends routeApi{
    public $apiResult = null;
    public $TSP = null;
  

    public function getRoute($points){
        $pt =$this->__pt2txt_recursive($points);
        $json = $this->__callDistanceMatrix($pt);
        if(isJson($json)==false){
            return false;
        }
        $this->apiResult = json_decode($json,1);
        return $this->apiResult;

    }

    public function getRouteByPolyline($polylin){
        $polylin="enc:{$polylin}:";
        $json = $this->__callDistanceMatrix($polylin);
        if(isJson($json)==false){
            return false;
        }
     
        $this->apiResult = json_decode($json,1);
        return $this->apiResult;

    }

   private function __callDistanceMatrix($points){
        
        
        $data = array(
            'origins' => $points,
            'destinations' => $points,
            'mode' => 'driving',
            'transit'=>'departure_time',
            'key' => GOOGLE_API_KEY
        );
        $url='https://maps.googleapis.com/maps/api/distancematrix/json?';
     
       return @file_get_contents($url.http_build_query($data));


   }




   private function __pt2txt_recursive($pts){
       $tmpPt = array();
       foreach($pts as $v){
            $tmpPt[]=$this->__pt2txt($v);

       }
       
        return implode('|',$tmpPt);
   }
   private function __pt2txt($pt){
        return implode(',',$pt);
   }
}