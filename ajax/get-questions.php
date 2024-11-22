<?php
/*******************************************
* Speak For Yersel                         *
* Script to get question data for a survey *
* Brian Aitken                             *
* February 2024                            *
*******************************************/
$data = [];
if(isset($_GET["id"]) && is_numeric($_GET["id"])){
	
	require("../incs/config.php");
	require("../incs/db.php");
	
	$id = $_GET["id"];
	
	try{
		//first see if the order should be random or not
		$survey = "select randomorder from ".$siteOptions["dbPrefix"]."survey where surveyid = :id";
		
		$stmt = DB::getInstance()->prepare($survey);
		$stmt->bindParam(':id',$id,PDO::PARAM_INT);

		$stmt->execute();

		$random = $stmt->fetch(PDO::FETCH_ASSOC);

		$order = "qorder";
		
		if($random["randomorder"]=="Yes")
			$order = "rand()";
		
		$q = "select * from ".$siteOptions["dbPrefix"]."question where surveyid = :id order by ".$order;
			
		//echo($q."<br />");
		
		$stmt = DB::getInstance()->prepare($q);
		$stmt->bindParam(':id',$id,PDO::PARAM_INT);

		$stmt->execute();

		$qs = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		//as 'qorder' is displayed if the question order is random we need to reassign this
		if($random["randomorder"]=="Yes"){
			$nq = count($qs);
			
			for($i=0;$i<$nq;$i++){
				$j = $i+1;
				$qs[$i]["qorder"] = $j;
			}
		}
		

		foreach($qs as $question){
			//answer options
			$aq = "select aoid, atext from ".$siteOptions["dbPrefix"]."answeroption where qid = :qid order by aoid";
			
			$stmt = DB::getInstance()->prepare($aq);
			$stmt->bindParam(':qid',$question["qid"],PDO::PARAM_STR);

			$stmt->execute();

			$aq = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
			$question["answerOptions"] = $aq;
			
			$data[] = $question;
		}
	}
	catch(PDOException $e){
		$data[] = "There has been an error connecting to the database.";
	}
	$data = json_encode($data);
}

header("Content-Type: application/json");
echo($data);
