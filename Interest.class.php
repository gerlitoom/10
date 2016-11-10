<?php 
class Interest {
	
    private $connection;
	
	function __construct($mysqli){
		$this->connection = $mysqli;
	}
	
	/* KLASSI FUNKTSIOONID */
    
    function saveInterest ($interest) {
		
		$stmt = $this->connection->prepare("INSERT INTO interests (interest) VALUES (?)");
	
		echo $this->connection->error;
		
		$stmt->bind_param("s", $interest);
		
		if($stmt->execute()) {
			echo "salvestamine õnnestus";
		} else {
		 	echo "ERROR ".$stmt->error;
		}
		
		$stmt->close();
		
	}
	
	function saveUserInterest ($interest) {
		
		$stmt = $this->connection->prepare("
			SELECT id FROM user_interests_3 
			WHERE user_id=? AND interest_id=?
		");
		echo $this->connection->error;
		$stmt->bind_param("ii", $_SESSION["userId"], $interest);
		
		$stmt->execute();
		
		//kas oli olemas
		if ($stmt->fetch()) {
			
			// oli olemas, ei salvesta
			echo "juba olemas";
			return; // see katkestab funktsiooni, edasi ei loe koodi
		}
		
		$stmt->close();
		// lähme edasi ja salvestamine
		
		$stmt = $this->connection->prepare("
			INSERT INTO user_interests_3 
			(user_id, interest_id) 
			VALUES (?, ?)
		");
	
		echo $this->connection->error;
		
		$stmt->bind_param("ii", $_SESSION["userId"], $interest);
		
		if($stmt->execute()) {
			echo "salvestamine õnnestus";
		} else {
		 	echo "ERROR ".$stmt->error;
		}
		
		$stmt->close();
		
	}
	
	function getAllInterests() {
				
		$stmt = $this->connection->prepare("
			SELECT id, interest
			FROM interests
		");
		echo $this->connection->error;
		
		$stmt->bind_result($id, $interest);
		$stmt->execute();
		
		
		//tekitan massiivi
		$result = array();
		
		// tee seda seni, kuni on rida andmeid
		// mis vastab select lausele
		while ($stmt->fetch()) {
			
			//tekitan objekti
			$i = new StdClass();
			
			$i->id = $id;
			$i->interest = $interest;
		
			array_push($result, $i);
		}
		
		$stmt->close();
		
		return $result;
	}
	
} 
?>