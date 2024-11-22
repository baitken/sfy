<?php
/*******************
* Speak For Yersel *
* Register user    *
* Brian Aitken     *
* February 2024    *
*******************/
$data = "[]";
if($_GET["submit"]=="Y" && is_numeric($_GET["yearBorn"]) && is_numeric($_GET["lid"]) && ($_GET["gender"]=="Female" || $_GET["gender"]=="Male" || $_GET["gender"]=="Other") && ($_GET["uni"]=="Y" || $_GET["uni"]=="N" || $_GET["uni"]=="School")){

	require("../incs/config.php");
	require("../incs/db.php");

	//create user and return a code consisting of the PID plus the timestamp

	$now = date("U");
	$year = date("Y");
	
	$age = $year - $_GET["yearBorn"];

	try{
		//if lid is 9999 then the user is outside of the area
		if($_GET["lid"]==9999){
			$locationName = "Outside ".$siteOptions["surveyArea"];
			$a = [];
			
			$a["areaid"] = 9999;
			$a["areaname"] = "Outside ".$siteOptions["surveyArea"];
			$a["geojson"] = "[]";
		}
		else{
		
			//get the area for use when generating random marker positions
			$q = "select ".$siteOptions["dbPrefix"]."area.areaid, geojson, areaname, lname, lcounty from ".$siteOptions["dbPrefix"]."area, ".$siteOptions["dbPrefix"]."area_location where ".$siteOptions["dbPrefix"]."area.areaid = ".$siteOptions["dbPrefix"]."area_location.areaid and lid = :id";
			
			//echo($q."<br />");
			
			$stmt = DB::getInstance()->prepare($q);
			$stmt->bindParam(':id',$_GET["lid"],PDO::PARAM_INT,4);
			

			$stmt->execute();
			
			//get results
			$a = $stmt->fetch();
			
			
			$locationName = $a["lname"];
			
			$locationName.=", ".$a["lcounty"];
		}
		$otherLanguage = null;
		if($siteOptions["otherLanguageQuestion"] && is_numeric($_GET["otherLanguage"]) && $_GET["otherLanguage"]>0 && $_GET["otherLanguage"]<5)
			$otherLanguage = $_GET["otherLanguage"];
		
		$q = "insert into ".$siteOptions["dbPrefix"]."person(lid, born, age, gender, uni, otherlang, createdate) values(:lid, :born,  :age, :gender, :uni, :otherlang, :createdate)";
		
		//echo($q."<br />");
		
		$stmt = DB::getInstance()->prepare($q);
		$stmt->bindParam(':lid',$_GET["lid"],PDO::PARAM_INT,4);
		$stmt->bindParam(':born',$_GET["yearBorn"],PDO::PARAM_INT,4);
		$stmt->bindParam(':age',$age,PDO::PARAM_INT,3);
		$stmt->bindParam(':uni',$_GET["uni"],PDO::PARAM_STR,6);
		$stmt->bindParam(':gender',$_GET["gender"],PDO::PARAM_STR,6);
		$stmt->bindParam(':otherlang',$otherLanguage,PDO::PARAM_INT,1);
		$stmt->bindParam(':createdate',$now,PDO::PARAM_INT);
		

		$stmt->execute();

		$id = DB::getInstance()->lastInsertId();
		
		$pid = $id."-".$now;
		
		$data = "[{\"pid\":\"".$pid."\", \"lid\":".$_GET["lid"].", \"locationName\":\"".$locationName."\", \"areaid\": ".$a["areaid"].", \"areaname\":\"".$a["areaname"]."\", \"geojson\": ".$a["geojson"]." }]";
		
	}
	catch(PDOException $e){
		$data = "[\"There has been an error connecting to the database.\"]";
	}
}

header("Content-Type: application/json");
echo($data);

