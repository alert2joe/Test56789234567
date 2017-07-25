<?php
class tsp {


	private $locations 	= array();		// all locations to visit
	private $longitudes = array();
	private $latitudes 	= array();
	private $shortest_route = array();	// holds the shortest route
	private $shortest_routes = array();	// any matching shortest routes
	private $shortest_cost = 0;		// holds the shortest distance
	private $all_routes = array();		// array of all the possible combinations and there distances
    private $startPoint = null;
	private $total_distance = 0;
	private $total_duration = 0;



	public function addByDistance($name,$distanceMap,$durationMap){
		$this->locations[$name] = array('distance'=>$distanceMap,'duration'=>$durationMap) ;
	}

	public function setStartPoint($name){
		$this->startPoint = $name;
	}
    
	// the main function that des the calculations
	public function compute($cost){
		$locations = $this->locations;

		$locations = array_keys($locations);


		$this->all_routes = $this->array_permutations($locations);
     
		foreach ($this->all_routes as $key=>$perms){
			$i=0;
			$total = 0;
			$total_distance = 0;
			$total_duration = 0;
			foreach ($perms as $value){
				if ($i<count($this->locations)-1){
						$total_distance += $this->getCost($perms[$i],$perms[$i+1],'distance');
						$total_duration += $this->getCost($perms[$i],$perms[$i+1],'duration');
						if($cost == 'distance' ){
							$total= $total_distance;
						}
						if($cost == 'duration' ){
							$total= $total_duration;
						}
                    
					//$total+=$this->distance($this->latitudes[$perms[$i]],$this->longitudes[$perms[$i]],$this->latitudes[$perms[$i+1]],$this->longitudes[$perms[$i+1]]);
				}
				$i++;
			}
			$this->all_routes[$key]['cost'] = $total;
			$this->all_routes[$key]['distance'] = $total_distance;
			$this->all_routes[$key]['duration'] = $total_duration;
			if ($total<$this->shortest_cost || $this->shortest_cost ==0){
				$this->shortest_cost = $total;
				$this->shortest_route = $perms;
				$this->shortest_routes = array();
				$this->total_distance = $total_distance;
				$this->total_duration = $total_duration;
			}
			if ($total == $this->shortest_cost){
				$this->shortest_routes[] = $perms;
			}
		}
	}

    private function getCost($to,$fr,$cost){
		if($cost == 'distance' ){
        	return $this->locations[$to]['distance'][$fr];
		}
	
		if($cost == 'duration' ){
        	return $this->locations[$to]['duration'][$fr];
		}
    }

	// work out all the possible different permutations of an array of data
	private function array_permutations($items, $perms = array( )) {
		static $all_permutations;
		if (empty($items)) {
            if(is_null($this->startPoint)===true || $perms[0]==$this->startPoint){
			$all_permutations[] = $perms;
            }
		}  else {
			for ($i = count($items) - 1; $i >= 0; --$i) {
				$newitems = $items;
				$newperms = $perms;
				list($foo) = array_splice($newitems, $i, 1);
				array_unshift($newperms, $foo);
				$this->array_permutations($newitems, $newperms);
			}
		}
		return $all_permutations;
	}
	// return an array of the shortest possible route
	public function shortest_route(){
		return $this->shortest_route;
	}
	// returns an array of any routes that are exactly the same distance as the shortest (ie the shortest backwards normally)
	public function matching_shortest_routes(){
		return $this->shortest_routes;
	}
	// the shortest possible distance to travel
	public function shortest_cost(){
		return $this->shortest_cost;
	}
	// returns an array of all the possible routes
	public function routes(){
		return $this->all_routes;
	}
	public function total_distance(){
		return $this->total_distance;
	}
	public function total_duration(){
		return $this->total_duration;
	}
}
