<?php
/********************
* Speak For Yersel  *
* Saves an answer   *
* Brian Aitken      *
* February 2024     *
********************/
$content = "Error saving answer";
if($_GET["id"] && is_numeric($_GET["id"]) && $_GET["pid"] && is_numeric($_GET["pid"])){

	require("../incs/config.php");
	require("../incs/db.php");
	
	$now = date("U");
	
	$answerlat = htmlspecialchars($_GET["lat"]);
	$answerlng = htmlspecialchars($_GET["lng"]);
	try{
		$q = "insert into ".$siteOptions["dbPrefix"]."answer(pid, aoid, answerlat, answerlng, submitdate) values(:pid, :aoid, :answerlat, :answerlng,  :submitdate)";
		
		//echo($q."<br />");
		
		$stmt = DB::getInstance()->prepare($q);
		$stmt->bindParam(':pid',$_GET["pid"],PDO::PARAM_INT);
		$stmt->bindParam(':aoid',$_GET["id"],PDO::PARAM_INT);
		$stmt->bindParam(':answerlat',$answerlat,PDO::PARAM_STR);
		$stmt->bindParam(':answerlng',$answerlng,PDO::PARAM_STR);
		$stmt->bindParam(':submitdate',$now,PDO::PARAM_INT);
		

		$stmt->execute();

		$id = DB::getInstance()->lastInsertId();
		
		$content = "Answer ".$_GET["id"]." saved with id ".$id;
		
	}
	catch(PDOException $e){
		$content = "Database error when saving answer ";
	}
}

echo($content);