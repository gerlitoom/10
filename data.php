<?php 
	// et saada ligi sessioonile
	require("functions.php");
	
    require("Helper.class.php");
	$Helper = new Helper();
	
	require("Note.class.php");
	$Note = new Note($mysqli);
	
	//ei ole sisseloginud, suunan login lehele
	if(!isset ($_SESSION["userId"])) {
		header("Location: login.php");
		exit();
	}
	
	//kas kasutaja tahab välja logida
	// kas aadressireal on logout olemas
	if (isset($_GET["logout"])) {
		
		session_destroy();
		
		header("Location: login.php");
		exit();
	}
	
	if (	isset($_POST["note"]) && 
			isset($_POST["color"]) && 
			!empty($_POST["note"]) && 
			!empty($_POST["color"]) 
	) {
		
		$note = $Helper->cleanInput($_POST["note"]);
		$color = $Helper->cleanInput($_POST["color"]);
		
		$Note->saveNote($note, $color);
		
	}
	
	$q = "";
	if(isset($_GET["q"])){
		$q = $Helper->cleanInput($_GET["q"]);
	}
	
	$sort = "id";
	$order = "ASC";
	
	if(isset($_GET["sort"]) && isset($_GET["order"])) {
		$sort = $_GET["sort"];
		$order = $_GET["order"];
	}
	
	$notes = $Note->getAllNotes($q, $sort, $order);
	
	//echo "<pre>";
	//var_dump($notes);
	//echo "</pre>";
?>

<h1>Data</h1>
<p>
	Tere tulemast <a href="user.php"><?=$_SESSION["userEmail"];?></a>!
	<a href="?logout=1">Logi välja</a>
</p>
<form method="POST">
<textarea name="note" rows="4" cols="50" value="text"></textarea>
<br>
<input name="color" type="color" style="width: 70px; height: 30px" value="#bbbbbb">
<br><br>
 <input type="submit">
 </form>
 <br>
 
<h2>Otsing</h2>

<form>
	<input type="search" name="q" value="<?=$q;?>">
	<input type="submit" value="Otsi">
</form><br><br>

<h2>Kommentaarid</h2>


<?php
 
	foreach ($notes as $n) {
		$style = "width:370px; min-height:50px; border: 1px solid grey; background-color:".$n->noteColor.";";
		echo "<p style='  ".$style."  '>".$n->note."</p>";
	}
 ?><br><br>


<h2 style="clear:both;">Tabel</h2>
<?php 
	$html = "<table>";
		
		$html .= "<tr>";
			
			$orderId = "ASC";
			if (isset($_GET["order"]) &&
				$_GET["order"] == "ASC" &&
				$_GET["sort"] == "id" ) {
				$orderId = "DESC";
			}
		
			$orderNote = "ASC";
			if (isset($_GET["order"]) &&
				$_GET["order"] == "ASC" &&
				$_GET["sort"] == "märkus" ) {
				$orderNote = "DESC";
			}
		
			$orderColor = "ASC";
			if (isset($_GET["order"]) &&
				$_GET["order"] == "ASC" &&
				$_GET["sort"] == "värv" ) {
				$orderColor = "DESC";
			}
		
			$html .= "<th>
			
				<a href='?q=".$q."&sort=id&order=".$orderId."'>
					id
				</a>
			</th>";
			$html .= "<th>
				<a href='?q=".$q."&sort=märkus&order=".$orderNote."'>
					märkus
				</a>
			</th>";
			$html .= "<th>
				<a href='?q=".$q."&sort=värv&order=".$orderColor."'>
					värv
				</a>
			</th>";
		$html .= "</tr>";
	foreach ($notes as $note) {
		$html .= "<tr>";
			$html .= "<td>".$note->id."</td>";
			$html .= "<td>".$note->note."</td>";
			$html .= "<td>".$note->noteColor."</td>";
			$html .= "<td><a href='edit.php?id=".$note->id."'>edit.php</a></td>";
		$html .= "</tr>";
	}
	
	$html .= "</table>";
	
	echo $html;
?>
