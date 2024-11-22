<?php
/**********************************
* Speak For Yersel                *
* Script to get data for the maps *
* Brian Aitken                    *
* February 2024                   *
**********************************/
$data = [];
if(isset($_GET["id"])){
	
	require("../incs/config.php");
	require("../incs/db.php");
	
	$qid = htmlspecialchars($_GET["id"]);

	try{
		//get the question info
		$q = "select surveytype, qid, qorder, ".$siteOptions["dbPrefix"]."question.surveyid, qvariable, qtext, mapname, ifilename, ialttext, icredit, audiostem, maxanswers from  ".$siteOptions["dbPrefix"]."survey, ".$siteOptions["dbPrefix"]."question where ".$siteOptions["dbPrefix"]."survey.surveyid = ".$siteOptions["dbPrefix"]."question.surveyid and qid = :qid";
		
		$stmt = DB::getInstance()->prepare($q);
		$stmt->bindParam(':qid',$qid,PDO::PARAM_STR);

		$stmt->execute();

		$data = $stmt->fetch(PDO::FETCH_ASSOC);
		
		//get all of the possible answer options to ensure consistent colours
		$q = "select atext from  ".$siteOptions["dbPrefix"]."answeroption where qid = :qid order by aoid";
		
		$stmt = DB::getInstance()->prepare($q);
		$stmt->bindParam(':qid',$qid,PDO::PARAM_STR);

		$stmt->execute();

		$answerOptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		$ao = [""];
		
		foreach($answerOptions as $a){
			$ao[] = $a["atext"];
		}
		
		$data["answerOptions"] = $ao;
		
		//get the answers
		//deal with age, education and gender if passed
		$ageBit = "";
		if(isset($_GET["age"]) && is_numeric($_GET["age"]) && $_GET["age"]>0 && $_GET["age"]<5){
			if($_GET["age"]==1)
				$ageBit = " and age < 19";
			else if($_GET["age"]==2)
				$ageBit = " and age >=19 and age <=35";
			else if($_GET["age"]==3)
				$ageBit = " and age >=36 and age <=65";
			else
				$ageBit = " and age >=66";
		}
		
		$educationBit = "";
		if(isset($_GET["education"]) && is_numeric($_GET["education"]) && $_GET["education"]>0 && $_GET["education"]<4){
			if($_GET["education"]==1)
				$educationBit = " and uni ='Y'";
			else if($_GET["education"]==2)
				$educationBit = " and uni ='N'";
			else
				$educationBit = " and uni ='School'";
		}
		
		$genderBit = "";
		if(isset($_GET["gender"]) && is_numeric($_GET["gender"]) && $_GET["gender"]>0 && $_GET["gender"]<4){
			if($_GET["gender"]==1)
				$genderBit = " and gender ='Female'";
			else if($_GET["gender"]==2)
				$genderBit = " and gender ='Male'";
			else
				$genderBit = " and gender ='Other'";
		}
		
		//answers
		$q = "SELECT ".$siteOptions["dbPrefix"]."answer.aoid, atext, answerlat, answerlng, born, age, gender, uni FROM ".$siteOptions["dbPrefix"]."answeroption, ".$siteOptions["dbPrefix"]."answer, ".$siteOptions["dbPrefix"]."person WHERE ".$siteOptions["dbPrefix"]."answeroption.aoid = ".$siteOptions["dbPrefix"]."answer.aoid and ".$siteOptions["dbPrefix"]."answer.pid = ".$siteOptions["dbPrefix"]."person.pid and ".$siteOptions["dbPrefix"]."answeroption.qid = :qid and answerlat!=0".$ageBit.$educationBit.$genderBit." order by aoid";
		
		//echo($q."<br />");
		
		$stmt = DB::getInstance()->prepare($q);
		$stmt->bindParam(':qid',$qid,PDO::PARAM_STR);

		$stmt->execute();

		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		$nr = count($rows);
		for($i=0;$i<$nr;$i++){
			$rows[$i]["anum"] = array_search($rows[$i]["atext"],$ao);
		}
		$data["answers"] = $rows;
	}
	catch(PDOException $e){
		$data = "[\"There has been an error connecting to the database.\"]";
	}
	$data = json_encode($data);
}

header("Content-Type: application/json");
echo($data);
