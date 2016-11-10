<?php 
class Note {
	
    private $connection;
	
	function __construct($mysqli){
		$this->connection = $mysqli;
	}
	
	/* KLASSI FUNKTSIOONID */
    
    function saveNote($note, $color) {
		
		$stmt = $this->connection->prepare("INSERT INTO colorNotes (note, color) VALUES (?, ?)");
		echo $this->connection->error;
		
		$stmt->bind_param("ss", $note, $color );
		if ( $stmt->execute() ) {
			echo "salvestamine õnnestus";	
		} else {	
			echo "ERROR ".$stmt->error;
		}
		
	}
	
	
	function getAllNotes($q, $sort, $order) {
		
		$allowedSort = ["id", "note", "color"];
		
		if (!in_array($sort, $allowedSort)){
			$sort = "id";
		}
		
		$orderBy = "ASC";
		
		if($order == "DESC"){
			$orderBy = "DESC";
		}
		
		echo "sorteerin ".$sort." ".$orderBy." ";
		
		//otsime
		if($q != "") {
			
			echo "Otsin: ".$q;
			
			$stmt = $this->connection->prepare("
				SELECT id, note, color
				FROM colorNotes
				WHERE deleted IS NULL
				AND (note LIKE ? OR color LIKE ?)
				ORDER BY $sort $orderBy
			");
			$searchWord = "%".$q."%";
			$stmt->bind_param("ss", $searchWord, $searchWord);
		
		}else{
			//ei otsi
			$stmt = $this->connection->prepare("
				SELECT id, note, color
				FROM colorNotes
				WHERE deleted IS NULL
			");
		}
		
		$stmt->bind_result($id, $note, $color);
		$stmt->execute();
		
		$result = array();
		
		// tsükkel töötab seni, kuni saab uue rea AB'i
		// nii mitu korda palju SELECT lausega tuli
		while ($stmt->fetch()) {
			//echo $note."<br>";
			
			$object = new StdClass();
			$object->id = $id;
			$object->note = $note;
			$object->noteColor = $color;
			
			
			array_push($result, $object);
			
		}
		
		return $result;
		
	}
	
	function getSingleNoteData($edit_id){
    		
		$stmt = $this->connection->prepare("SELECT note, color FROM colorNotes WHERE id=? AND deleted IS NULL");
		$stmt->bind_param("i", $edit_id);
		$stmt->bind_result($note, $color);
		$stmt->execute();
		
		//tekitan objekti
		$n = new Stdclass();
		
		//saime ühe rea andmeid
		if($stmt->fetch()){
			// saan siin alles kasutada bind_result muutujaid
			$n->note = $note;
			$n->color = $color;
			
			
		}else{
			// ei saanud rida andmeid kätte
			// sellist id'd ei ole olemas
			// see rida võib olla kustutatud
			header("Location: data.php");
			exit();
		}
		
		$stmt->close();		
		return $n;
		
	}
	function updateNote($id, $note, $color){
				
		$stmt = $this->connection->prepare("UPDATE colorNotes SET note=?, color=? WHERE id=? AND deleted IS NULL");
		$stmt->bind_param("ssi",$note, $color, $id);
		
		// kas õnnestus salvestada
		if($stmt->execute()){
			// õnnestus
			echo "salvestus õnnestus!";
		}
		
		$stmt->close();
		
	}
	
	function deleteNote($id){
		
		$stmt = $this->connection->prepare("
			UPDATE colorNotes 
			SET deleted=NOW() 
			WHERE id=? AND deleted IS NULL
		");
		$stmt->bind_param("i", $id);
		
		// kas õnnestus salvestada
		if($stmt->execute()){
			// õnnestus
			echo "salvestus õnnestus!";
		}
		
		$stmt->close();
		
	}
} 
?>