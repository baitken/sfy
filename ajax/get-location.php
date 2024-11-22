<?php
/*****************************************************
* Speak For Yersel                                   *
* Script to get data a location for the autocomplete *
* Brian Aitken                                       *
* February 2024                                      *
*****************************************************/
$data = "[]";
if(isset($_GET["term"]) && strlen($_GET["term"])>=2){

	require("../incs/config.php");
	require("../incs/db.php");
	
	$term = htmlspecialchars($_GET["term"]);
	$oTerm = $term;
	$term = "%".$term."%";
	
		try{
			$q = "select * from ".$siteOptions["dbPrefix"]."area_location where lname like :term or lcounty like :term order by lname";
			
			//echo($q."<br />");
			
			$stmt = DB::getInstance()->prepare($q);
			$stmt->bindParam(':term',$term,PDO::PARAM_STR);

			$stmt->execute();

			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
			
			$data = '[';
			
			foreach($rows as $row){
				
				$value = $row["lname"];
				
				if($row["lcounty"])
					$value.=", ".$row["lcounty"];
				
				$data.='{"id":'.$row["lid"].',"value":"'.$value.'"},';
			}
			if($data!="[")
				$data = substr($data,0,-1);
			
			
			//add in an 'outside ' option
			if(stristr("Outside ".$siteOptions["surveyArea"],$oTerm)){
				if($data!="[")
					$data.=",";
				$data.='{"id":9999,"value":"Outside '.$siteOptions["surveyArea"].'"}';
			}
						
			$data.="]";
			

		}
		catch(PDOException $e){
			$data = "[\"There has been an error connecting to the database.\"]";
		}

}
header("Content-Type: application/json");
echo($data);
