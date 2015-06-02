<?php

class World {

	// dimensions of world n*n
	public $numberOfCells;

	// world matrix
	public $world;

	public function __construct($configuration,$organisms) {
		$this->numberOfCells = $configuration['cells'];

		// create empty world matrix
		for($y = 0;$y<$this->numberOfCells;$y++) {
			for($x = 0;$x<$this->numberOfCells;$x++) {
				$this->world[$y][$x] = "0";
			}
		}

		// fill organisms into world
		foreach($organisms['organism'] as $organism) {
			$y = $organism['y_pos'];
			$x = $organism['x_pos'];
			$species = $organism['species'];

			if($this->world[$y][$x] == "0") {
				$this->world[$y][$x] = $species;
			}			
			else {

				// if two different species of organisms occupy the same element, one is chosen to die randomly
				if(mt_rand(0,1)) {
					$this->world[$y][$x] = $species;
				}
				else {
					continue;
				}
			}
		}				
	}


	function iterate() {
		// render for debug
		for($y = 0;$y<$this->numberOfCells;$y++) {
			for($x = 0;$x<$this->numberOfCells;$x++) {
				echo $this->world[$y][$x];
			}
			echo "\n";
		}

		// check condition for each iteration
		for($y = 0;$y<$this->numberOfCells;$y++) {
			for($x = 0;$x<$this->numberOfCells;$x++) {
				$cellOccupant = $this->world[$y][$x];

				// inspect neighbours of each cell
				$neighbours[$cellOccupant] = array(
					"top-left"     => ($x>0  && $y>0)? $this->world[$y-1][$x-1] : NULL,
					"left"         => ($x>0)?          $this->world[$y][$x-1]   : NULL,
					"bottom-left"  => ($x>0  && $y<5)? $this->world[$y+1][$x-1] : NULL,
					"bottom"       => ($y<5)?          $this->world[$y+1][$x]   : NULL,
					"bottom-right" => ($x<5  && $y<5)? $this->world[$y+1][$x+1] : NULL,
					"right"        => ($x<5)?          $this->world[$y][$x+1]   : NULL,
					"top-right"    => ($x<5  && $y>0)? $this->world[$y-1][$x+1] : NULL,
					"top"          => ($y>0)?          $this->world[$y-1][$x]   : NULL,
					);

				$neighbourDetails = array_filter($neighbours[$cellOccupant], function($var){return !is_null($var);});
				$neighbourDetailCount = array_count_values($neighbourDetails);
			}
		}
	



	}
}

?>