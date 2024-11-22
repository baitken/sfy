<?php
/****************************************
* Speak For Yersel                      *
* Script to get a list of surveys       *
* Brian Aitken                          *
* February 2024                         *
****************************************/
$data = "[]";

require("../incs/config.php");
require("../incs/db.php");

try{
	$q = "select * from ".$siteOptions["dbPrefix"]."survey order by surveyid";
	
	$stmt = DB::getInstance()->prepare($q);

	$stmt->execute();

	$surveys = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	$data = json_encode($surveys);
}
catch(PDOException $e){
	$data = "[\"There has been an error connecting to the database.\"]";
}


header("Content-Type: application/json");
echo($data);
