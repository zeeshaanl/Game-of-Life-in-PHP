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

		$dyingCellCoordinates = array();
		$bornCellCoordinates = array();

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

				// call dying cell coordinate determining function
				if($this->dyingCells($x, $y, $cellOccupant, $neighbourDetailCount) != "") {
					$dyingCellCoordinates[] = $this->dyingCells($x, $y, $cellOccupant, $neighbourDetailCount);
				}

				// call newborn cell coordinate determining function
				$bornCellCoordinates[] = $this->bornCells($x, $y, $cellOccupant, $neighbourDetailCount);
				//print_r($bornCellCoordinates);				
			}
		}

		foreach ($dyingCellCoordinates as $key => $value) {
			$this->world[$value['1']][$value['0']] = "0";
		}
		echo "\n\n";
		// render for debug
		for($y = 0;$y<$this->numberOfCells;$y++) {
			for($x = 0;$x<$this->numberOfCells;$x++) {
				echo $this->world[$y][$x];
			}
			echo "\n";
		}

	}

	// get co-ordinates of cells that will die
	function dyingCells($x, $y, $cellOccupant, $neighbourDetailCount) {

		// check if organism lives in cell
		if($cellOccupant != "0") {

			// get nearby species that are greater in number than 3
			$highSpecies = array_filter($neighbourDetailCount, function($var){return ($var > 3);});

			//ignore dead neighbours
			unset($highSpecies['0']);

			// kill cells that have more than 3 neighbours of the same species
			foreach ($highSpecies as $key => $value) {
				if($key == $cellOccupant) {
					$dyingCoordinates = array($x,$y);
					return $dyingCoordinates;
				}
			}
		}
	}

	// get co-ordinates of cells that will be born
	function bornCells($x, $y, $cellOccupant, $neighbourDetailCount) {

		// check if organism lives within cell
		if($cellOccupant == "0") {

			// find neighbours than number exactly 3
			//print_r($neighbourDetailCount);
			$lifeSpecies = array_filter($neighbourDetailCount, function($var){return ($var == 3);});
			
			// ignore dead neighbours
			unset($lifeSpecies['0']);

			//if exactly 3 of more than one species are neighbours, a random species is selected
			if( count($lifeSpecies) == 2 ) {
				if(mt_rand(0,1)) {
					array_splice($lifeSpecies, 0, 1);
				}
				else {
					array_splice($lifeSpecies, 1, 1);
				}
			}
						
			//give birth in cells which have 3 neighbours of the same species			
			foreach ($lifeSpecies as $key => $value) {
				$bornCoordinates = array($x,$y,$key);
				return $bornCoordinates;
			}
		}
	} 
}

?>