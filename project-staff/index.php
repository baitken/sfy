<?php

/******************************
* Speak For Yersel Tool       *
* Staff page                  *
* Brian Aitken                *
* February 2024               *
******************************/

require("../incs/config.php");
require("../incs/db.php");
require("../incs/layout.php");

$content = "";

//work out the time period
$from = $siteOptions["launchDate"];
$fromTimestamp = 1653668449;

$fromBits = explode("-",$from);
if(is_numeric($fromBits[0]) && is_numeric($fromBits[1]) && is_numeric($fromBits[2])){
	$from = $fromBits[0]."-".$fromBits[1]."-".$fromBits[2];
	$fromTimestamp = mktime(0,0,0,$fromBits[1],$fromBits[2],$fromBits[0]);
}

if(isset($_GET["from"])){
	$fromBits = explode("-",$_GET["from"]);
	if(is_numeric($fromBits[0]) && is_numeric($fromBits[1]) && is_numeric($fromBits[2])){
		$from = $fromBits[0]."-".$fromBits[1]."-".$fromBits[2];
		$fromTimestamp = mktime(0,0,0,$fromBits[1],$fromBits[2],$fromBits[0]);
	}
}

$toTimestamp = time();
$to = date("Y-m-d");

if(isset($_GET["to"])){
	$toBits = explode("-",$_GET["to"]);
	if(is_numeric($toBits[0]) && is_numeric($toBits[1]) && is_numeric($toBits[2])){
		$to = $toBits[0]."-".$toBits[1]."-".$toBits[2];
		$toTimestamp = mktime(23,59,59,$toBits[1],$toBits[2],$toBits[0]);
	}
}

//process data download
if(isset($_GET["download"]) && $_GET["download"]=="Y" && isset($_GET["type"])){
	try{
		$filename = "";
		$external = "";
		if($_GET["type"]=="users"){
			if(!isset($_GET["outside"]))
				$q = "select ".$siteOptions["dbPrefix"]."person.pid, ".$siteOptions["dbPrefix"]."person.lid, born, age, gender, uni, otherlang, ".$siteOptions["dbPrefix"]."person.createdate, ".$siteOptions["dbPrefix"]."area_location.areaid, lname, lcounty, ".$siteOptions["dbPrefix"]."area.rid, areaname, region from ".$siteOptions["dbPrefix"]."person, ".$siteOptions["dbPrefix"]."area_location, ".$siteOptions["dbPrefix"]."area, ".$siteOptions["dbPrefix"]."region where ".$siteOptions["dbPrefix"]."person.lid = ".$siteOptions["dbPrefix"]."area_location.lid and ".$siteOptions["dbPrefix"]."area_location.areaid = ".$siteOptions["dbPrefix"]."area.areaid and ".$siteOptions["dbPrefix"]."area.rid = ".$siteOptions["dbPrefix"]."region.rid and ".$siteOptions["dbPrefix"]."person.createdate >= :from and ".$siteOptions["dbPrefix"]."person.createdate <= :to order by createdate";
			else{
				$q = "select ".$siteOptions["dbPrefix"]."person.pid, ".$siteOptions["dbPrefix"]."person.lid, born, age, gender, uni, otherlang, ".$siteOptions["dbPrefix"]."person.createdate from ".$siteOptions["dbPrefix"]."person where lid = 9999 and ".$siteOptions["dbPrefix"]."person.createdate >= :from and ".$siteOptions["dbPrefix"]."person.createdate <= :to order by createdate";
				$external = "elsewhere_";
			}
			
			$stmt = DB::getInstance()->prepare($q);
			
			$stmt->bindParam(':from',$fromTimestamp,PDO::PARAM_INT,11);
			$stmt->bindParam(':to',$toTimestamp,PDO::PARAM_INT,11);

			$stmt->execute();

			$data = $stmt->fetchAll(PDO::FETCH_ASSOC);	
			
			$filename = 'SFY-'.$siteOptions["subDirectory"]."_users_".$external.$from.'-'.$to.'.csv';
		}
		else if(is_numeric($_GET["type"])){
			if(!isset($_GET["outside"]))
				$q = "select ".$siteOptions["dbPrefix"]."person.pid, ".$siteOptions["dbPrefix"]."person.lid, born, age, gender, uni, otherlang, ".$siteOptions["dbPrefix"]."person.createdate, ".$siteOptions["dbPrefix"]."area_location.areaid, lname, lcounty, ".$siteOptions["dbPrefix"]."area.rid, areaname, region, answerlat, answerlng, submitdate, ".$siteOptions["dbPrefix"]."answeroption.qid, ".$siteOptions["dbPrefix"]."answeroption.aoid, atext, qorder, ".$siteOptions["dbPrefix"]."question.surveyid, qvariable, qtext, mapname, surveytype, surveyname  from ".$siteOptions["dbPrefix"]."person, ".$siteOptions["dbPrefix"]."area_location, ".$siteOptions["dbPrefix"]."area, ".$siteOptions["dbPrefix"]."region, ".$siteOptions["dbPrefix"]."answer, ".$siteOptions["dbPrefix"]."answeroption, ".$siteOptions["dbPrefix"]."question, ".$siteOptions["dbPrefix"]."survey where ".$siteOptions["dbPrefix"]."person.lid = ".$siteOptions["dbPrefix"]."area_location.lid and ".$siteOptions["dbPrefix"]."area_location.areaid = ".$siteOptions["dbPrefix"]."area.areaid and ".$siteOptions["dbPrefix"]."area.rid = ".$siteOptions["dbPrefix"]."region.rid and ".$siteOptions["dbPrefix"]."answer.pid = ".$siteOptions["dbPrefix"]."person.pid and ".$siteOptions["dbPrefix"]."answer.aoid = ".$siteOptions["dbPrefix"]."answeroption.aoid and ".$siteOptions["dbPrefix"]."answeroption.qid = ".$siteOptions["dbPrefix"]."question.qid and ".$siteOptions["dbPrefix"]."question.surveyid = ".$siteOptions["dbPrefix"]."survey.surveyid and ".$siteOptions["dbPrefix"]."survey.surveyid = :id and submitdate >= :from and submitdate <= :to order by ".$siteOptions["dbPrefix"]."question.qid, submitdate";
			else{
				$q = "select ".$siteOptions["dbPrefix"]."person.pid, ".$siteOptions["dbPrefix"]."person.lid, born, age, gender, uni, otherlang, ".$siteOptions["dbPrefix"]."person.createdate, submitdate, ".$siteOptions["dbPrefix"]."answeroption.qid, ".$siteOptions["dbPrefix"]."answeroption.aoid, atext, qorder, ".$siteOptions["dbPrefix"]."question.surveyid, qvariable, qtext, mapname, surveytype, surveyname  from ".$siteOptions["dbPrefix"]."person, ".$siteOptions["dbPrefix"]."answer, ".$siteOptions["dbPrefix"]."answeroption, ".$siteOptions["dbPrefix"]."question, ".$siteOptions["dbPrefix"]."survey where lid = 9999 and ".$siteOptions["dbPrefix"]."answer.pid = ".$siteOptions["dbPrefix"]."person.pid and ".$siteOptions["dbPrefix"]."answer.aoid = ".$siteOptions["dbPrefix"]."answeroption.aoid and ".$siteOptions["dbPrefix"]."answeroption.qid = ".$siteOptions["dbPrefix"]."question.qid and ".$siteOptions["dbPrefix"]."question.surveyid = ".$siteOptions["dbPrefix"]."survey.surveyid and ".$siteOptions["dbPrefix"]."survey.surveyid = :id and submitdate >= :from and submitdate <= :to order by ".$siteOptions["dbPrefix"]."question.qid, submitdate";
				$external = "elsewhere_";
			}
			$stmt = DB::getInstance()->prepare($q);
			
			$stmt->bindParam(':id',$_GET["type"],PDO::PARAM_INT,11);
			$stmt->bindParam(':from',$fromTimestamp,PDO::PARAM_INT,11);
			$stmt->bindParam(':to',$toTimestamp,PDO::PARAM_INT,11);

			$stmt->execute();

			$data = $stmt->fetchAll(PDO::FETCH_ASSOC);	
			
			$filename = 'SFY-'.$siteOptions["subDirectory"]."_survey-".$_GET["type"]."_".$external.$from.'-'.$to.'.csv';			
		}

		if($filename && isset($data) && count($data)>0){
			header("Content-Type: text/csv");
			header('Content-Disposition: attachment; filename="'.$filename.'"');
			$csv = fopen("php://output", 'w');	
			
			//add the column heading
			$keys = array_keys($data[0]);
			fputcsv($csv,$keys);
			
			foreach($data as $d){
				$d["createdate"] = date("Y-m-d",$d["createdate"]);
				if(is_numeric($_GET["type"]))
					$d["submitdate"] = date("Y-m-d",$d["submitdate"]);
				
				if($d["otherlang"]==1)
					$d["otherlang"] = "Daily";
				else if($d["otherlang"]==2)
					$d["otherlang"] = "Sometimes";
				else if($d["otherlang"]==3)
					$d["otherlang"] = "Rarely";
				else if($d["otherlang"]==4)
					$d["otherlang"] = "I don't speak ".$siteOptions["otherLanguage"];
					
				fputcsv($csv,$d);
			}
			
			fclose($csv);
			exit;
		}			
	}
	catch(PDOException $e){
		$content = "Database error ".$e;
	}
}

$content = "";
//number of users 
try{
	//within the area of study
	$q = "select count(".$siteOptions["dbPrefix"]."person.pid) as num from ".$siteOptions["dbPrefix"]."person where ".$siteOptions["dbPrefix"]."person.lid !=9999 and ".$siteOptions["dbPrefix"]."person.createdate >= :from and ".$siteOptions["dbPrefix"]."person.createdate <= :to";
	
	$stmt = DB::getInstance()->prepare($q);
	
	$stmt->bindParam(':from',$fromTimestamp,PDO::PARAM_INT,11);
	$stmt->bindParam(':to',$toTimestamp,PDO::PARAM_INT,11);

	$stmt->execute();

	$within = $stmt->fetch(PDO::FETCH_ASSOC);
	
	//without the area of study
	$q = "select count(".$siteOptions["dbPrefix"]."person.pid) as num from ".$siteOptions["dbPrefix"]."person where ".$siteOptions["dbPrefix"]."person.lid =9999 and ".$siteOptions["dbPrefix"]."person.createdate >= :from and ".$siteOptions["dbPrefix"]."person.createdate <= :to";
	
	$stmt = DB::getInstance()->prepare($q);
	
	$stmt->bindParam(':from',$fromTimestamp,PDO::PARAM_INT,11);
	$stmt->bindParam(':to',$toTimestamp,PDO::PARAM_INT,11);

	$stmt->execute();

	$without = $stmt->fetch(PDO::FETCH_ASSOC);
	
	$content = "<h3>Total users</h3><div class=\"statsBox\"><p>A total of <b>".$within["num"]."</b> people have registered within ".$siteOptions["surveyArea"]." and <b>".$without["num"]."</b> from elsewhere.</p><p><a href=\"./?download=Y&type=users\" class=\"btn btn-primary\"><i class=\"fa-solid fa-file-arrow-down\"></i> Download users from within ".$siteOptions["surveyArea"]." (CSV)</a><br /><a href=\"./?download=Y&type=users&outside=Y\" class=\"btn btn-primary\"><i class=\"fa-solid fa-file-arrow-down\"></i> Download users from elsewhere (CSV)</a></p></div>";
	
	$content.="<h3>Survey answers</h3>";
	
	$surveys = "select * from ".$siteOptions["dbPrefix"]."survey";
	
	$stmt = DB::getInstance()->prepare($surveys);

	$stmt->execute();

	$surveys = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	foreach($surveys as $survey){
	
		$q = "select count(*) as num from ".$siteOptions["dbPrefix"]."answer, ".$siteOptions["dbPrefix"]."answeroption, ".$siteOptions["dbPrefix"]."question, ".$siteOptions["dbPrefix"]."person where ".$siteOptions["dbPrefix"]."answer.aoid = ".$siteOptions["dbPrefix"]."answeroption.aoid and ".$siteOptions["dbPrefix"]."answeroption.qid = ".$siteOptions["dbPrefix"]."question.qid and surveyid = :surveyid and answerlat !=0 and ".$siteOptions["dbPrefix"]."answer.pid = ".$siteOptions["dbPrefix"]."person.pid and ".$siteOptions["dbPrefix"]."person.createdate >= :from and ".$siteOptions["dbPrefix"]."person.createdate <= :to";
		
		$stmt = DB::getInstance()->prepare($q);
		
		$stmt->bindParam(':surveyid',$survey["surveyid"],PDO::PARAM_INT,11);
		$stmt->bindParam(':from',$fromTimestamp,PDO::PARAM_INT,11);
		$stmt->bindParam(':to',$toTimestamp,PDO::PARAM_INT,11);

		$stmt->execute();

		$within = $stmt->fetch(PDO::FETCH_ASSOC);
		
		$q = "select count(*) as num from ".$siteOptions["dbPrefix"]."answer, ".$siteOptions["dbPrefix"]."answeroption, ".$siteOptions["dbPrefix"]."question, ".$siteOptions["dbPrefix"]."person where ".$siteOptions["dbPrefix"]."answer.aoid = ".$siteOptions["dbPrefix"]."answeroption.aoid and ".$siteOptions["dbPrefix"]."answeroption.qid = ".$siteOptions["dbPrefix"]."question.qid and surveyid = :surveyid and answerlat =0 and ".$siteOptions["dbPrefix"]."answer.pid = ".$siteOptions["dbPrefix"]."person.pid  and ".$siteOptions["dbPrefix"]."person.createdate >= :from and ".$siteOptions["dbPrefix"]."person.createdate <= :to";
		
		$stmt = DB::getInstance()->prepare($q);
		
		$stmt->bindParam(':surveyid',$survey["surveyid"],PDO::PARAM_INT,11);
		$stmt->bindParam(':from',$fromTimestamp,PDO::PARAM_INT,11);
		$stmt->bindParam(':to',$toTimestamp,PDO::PARAM_INT,11);

		$stmt->execute();

		$without = $stmt->fetch(PDO::FETCH_ASSOC);
		
		$content.="<div class=\"statsBox\"><h4>".$survey["surveyname"]." (".$survey["surveytype"].")</h4><p>There have been <b>".$within["num"]."</b> submitted answers from within ".$siteOptions["surveyArea"]." and <b>".$without["num"]."</b> from elsewhere.</p><p><a href=\"./?download=Y&type=".$survey["surveyid"]."\" class=\"btn btn-primary\"><i class=\"fa-solid fa-file-arrow-down\"></i> Download answers from within ".$siteOptions["surveyArea"]." (CSV)</a><br /><a href=\"./?download=Y&type=".$survey["surveyid"]."&outside=Y\" class=\"btn btn-primary\"><i class=\"fa-solid fa-file-arrow-down\"></i> Download answers from elsewhere (CSV)</a></p></div>";
	
	}
}
catch(PDOException $e){
	$content = "Database error ".$e;
}

formatTop(null,"Staff");

?>
<div class="container-fluid" id="mainPageArea"> 
<h2 class="alignCenter">Stats and data download facilities</h2>
<div class="row">
<div class="col-sm-12">
<h3>Period</h3>
<p>Note that to get data for a specific day use the same date as 'from' and 'to'.</p>
<form method="get" action="./">
<div class="row">
    <div class="col">
      <label for="inputFrom">From</label>
      <input type="text" class="form-control" id="inputFrom" name="from" value="<?php echo($from);?>">
	  <small class="form-text text-muted">Use the format yyyy-mm-dd</small>
    </div>
    <div class="col">
      <label for="inputTo">To</label>
 <input type="text" class="form-control" id="inputTo" name="to" value="<?php echo($to);?>">
 <small class="form-text text-muted">Use the format yyyy-mm-dd</small>
    </div>
    <div class="col"><button type="submit" class="btn btn-primary" style="margin-top:25px;">Update</button>
    </div>
</div>
</form>
<p></p><hr>
</div>
</div>
<div class="row">
<div class="col-sm-12">
<p>The number of users listed is the number of users who registered in the selected period.  The number of answers listed are the number of answers submitted in the selected period.  Be aware that a user may have registered outside of your selected period but may have submitted answers inside your selected period.</p>
<?php
echo($content);
?>
</div>
</div>
</div>
</div>
<?php

formatBottom();

