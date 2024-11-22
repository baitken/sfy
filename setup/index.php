<?php

$stage = 1;

/******************************
* Speak For Yersel            *
* Setup a new instance        *
* Brian Aitken                *
* University of Glasgow       *
* February 2024               *
******************************/

/*
require("../incs/db.php");
require("../incs/config.php");

$content = "";

if(isset($_POST["stage"])){
	if($_POST["stage"]==2){	
	$stage = 2;
	$proceed = true;
	if(isset($_POST["createDB"]) && $_POST["createDB"]=="Y"){
				//create the Database
				try{				
					//survey table
					$q = "CREATE TABLE `".$siteOptions["dbPrefix"]."survey` (
	  `surveyid` int(11) NOT NULL,
	   `surveytype` enum('morphology','lexis','phonology') NOT NULL,
	  `surveyname` varchar(50) NOT NULL,
	  `initialtext` text NOT NULL,
	  `url` varchar(50) NOT NULL,
	  `randomorder` enum('No','Yes') NOT NULL
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci; ALTER TABLE `".$siteOptions["dbPrefix"]."survey`
	  ADD PRIMARY KEY (`surveyid`); ALTER TABLE `".$siteOptions["dbPrefix"]."survey` ADD UNIQUE `url` (`url`);  ALTER TABLE `".$siteOptions["dbPrefix"]."survey`
	  MODIFY `surveyid` int(11) NOT NULL AUTO_INCREMENT";
					
					
					$stmt = DB::getInstance()->prepare($q);				

					$stmt->execute();
					
					
					//question table
					$q = "CREATE TABLE `".$siteOptions["dbPrefix"]."question` (
	  `qid` varchar(20) NOT NULL,
	  `qorder` int(3) NOT NULL,
	  `surveyid` int(11) NOT NULL,
	  `qvariable` varchar(100) DEFAULT NULL,
	  `qtext` text NOT NULL,
	  `mapname` varchar(255) NOT NULL,
	  `ifilename` varchar(50) DEFAULT NULL,
	  `ialttext` varchar(100) DEFAULT NULL,
	  `icredit` text DEFAULT NULL,
	  `xrefs` varchar(255) DEFAULT NULL,
	  `notes` text DEFAULT NULL,
	  `pos` varchar(20) DEFAULT NULL,
	  `lchange` varchar(255) DEFAULT NULL,
	  `audiostem` varchar(50) DEFAULT NULL,
	  `maxanswers` int(2) NOT NULL
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci; ALTER TABLE `".$siteOptions["dbPrefix"]."question`
	  ADD PRIMARY KEY (`qid`),
	  ADD KEY `surveyid` (`surveyid`);";
	
					$stmt = DB::getInstance()->prepare($q);				

					$stmt->execute();
					
					//answeroption table
					$q = "CREATE TABLE `".$siteOptions["dbPrefix"]."answeroption` (  
		`aoid` int(11) NOT NULL,
	  `qid` varchar(20) NOT NULL,
	  `atext` varchar(100) NOT NULL
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci; ALTER TABLE `".$siteOptions["dbPrefix"]."answeroption`
	  ADD PRIMARY KEY (`aoid`),
	  ADD KEY `qid` (`qid`); ALTER TABLE `".$siteOptions["dbPrefix"]."answeroption`
	   MODIFY `aoid` int(11) NOT NULL AUTO_INCREMENT";
					
					
					$stmt = DB::getInstance()->prepare($q);				

					$stmt->execute();
					
					//answer table
					$q = "CREATE TABLE `".$siteOptions["dbPrefix"]."answer` (  
	 `aid` int(11) NOT NULL,
	  `pid` int(11) NOT NULL,
	  `aoid` int(11) NOT NULL,
	  `answerlat` varchar(100) NOT NULL,
	  `answerlng` varchar(100) NOT NULL,
	  `submitdate` int(11) NOT NULL
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci; ALTER TABLE `".$siteOptions["dbPrefix"]."answer`
		ADD PRIMARY KEY (`aid`),
	  ADD KEY `aoid` (`aoid`); ALTER TABLE `".$siteOptions["dbPrefix"]."answer`
	   MODIFY `aid` int(11) NOT NULL AUTO_INCREMENT";
	   
					$stmt = DB::getInstance()->prepare($q);				

					$stmt->execute();
	   
					//person table
					$q = "CREATE TABLE `".$siteOptions["dbPrefix"]."person` (  
	 `pid` int(11) NOT NULL,
	  `lid` int(11) NOT NULL,
	  `born` int(4) NOT NULL,
	  `age` int(3) NOT NULL,
	  `gender` enum('Female','Male','Other') NOT NULL,
	  `uni` enum('N','Y','School') NOT NULL DEFAULT 'N',
	  `otherlang` enum('1', '2', '3', '4')  NULL,
	  `createdate` int(11) NOT NULL
					) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci; ALTER TABLE `".$siteOptions["dbPrefix"]."person`
						ADD PRIMARY KEY (`pid`); ALTER TABLE `".$siteOptions["dbPrefix"]."person`
					   MODIFY `pid` int(11) NOT NULL AUTO_INCREMENT";
					
					
					$stmt = DB::getInstance()->prepare($q);				

					$stmt->execute();
					
					//area_location table
					$q = "CREATE TABLE `".$siteOptions["dbPrefix"]."area_location` (  
	  `lid` int(11) NOT NULL,
	  `areaid` int(11) NOT NULL,
	  `lname` varchar(255) NOT NULL,
	  `lcounty` varchar(100) NOT NULL
					) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci; ALTER TABLE `".$siteOptions["dbPrefix"]."area_location`
						  ADD PRIMARY KEY (`lid`),
	  ADD KEY `lname` (`lname`),
	  ADD KEY `areaid` (`areaid`); ALTER TABLE `".$siteOptions["dbPrefix"]."area_location`
					   MODIFY `lid` int(11) NOT NULL AUTO_INCREMENT";
					
					
					$stmt = DB::getInstance()->prepare($q);				

					$stmt->execute();
					
					//area table
					$q = "CREATE TABLE `".$siteOptions["dbPrefix"]."area` (  
	   `areaid` int(11) NOT NULL,
	  `rid` int(11) DEFAULT NULL,
	  `geojsonid` int(11) NOT NULL,
	  `areaname` varchar(255) NOT NULL,
	  `additionalgroup1` varchar(255) DEFAULT NULL,
	  `additionalgroup2` varchar(255) DEFAULT NULL,
	  `geojson` longtext NOT NULL
					) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci; ALTER TABLE `".$siteOptions["dbPrefix"]."area`
							ADD PRIMARY KEY (`areaid`),
	  ADD KEY `lname` (`areaname`); ALTER TABLE `".$siteOptions["dbPrefix"]."area`
					   MODIFY `areaid` int(11) NOT NULL AUTO_INCREMENT";
					
					
					$stmt = DB::getInstance()->prepare($q);				

					$stmt->execute();
					
					//region table
					$q = "CREATE TABLE `".$siteOptions["dbPrefix"]."region` (  
	   `rid` int(11) NOT NULL,
	  `region` varchar(100) NOT NULL
					) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci; ALTER TABLE `".$siteOptions["dbPrefix"]."region`
							ADD PRIMARY KEY (`rid`);";
					
					
					$stmt = DB::getInstance()->prepare($q);				

					$stmt->execute();
					
					$content = "<p>Database tables have been successfully created.</p>";
					
				}
				catch(PDOException $e){
					$content = "<p class=\"error\"><b><i class=\"fa-solid fa-triangle-exclamation\"></i> Error:</b> Unable to create database tables.  Please check the details in <b>incs/db.php</b> and ensure the database user has permission to create tables.</p><p><a class=\"btn btn-primary\" href=\"./\">Try again</a></p>";
					$proceed = false;
				}
		}
		else if(isset($_POST["createSurvey"]) && $_POST["createSurvey"]=="Y"){
			//create a survey here
			if(isset($_POST["surveyType"]) && $_POST["surveyType"] !="" && isset($_POST["surveyName"]) && $_POST["surveyName"] !="" && isset($_POST["surveyURL"]) && $_POST["surveyURL"] !="" && isset($_POST["surveyIntro"]) && $_POST["surveyIntro"] !="" && isset($_POST["randomOrder"]) && ($_POST["randomOrder"] =="No" || $_POST["randomOrder"]=="Yes")){
				try{
					$q = "insert into ".$siteOptions["dbPrefix"]."survey(surveyType, surveyname, initialtext, url, randomorder) values(:surveyType, :surveyname, :initialtext, :url, :randomorder)";
					
					$surveyType = htmlspecialchars($_POST["surveyType"]);
					$surveyName = htmlspecialchars($_POST["surveyName"]);
					$surveyURL = htmlspecialchars($_POST["surveyURL"]);
					$surveyIntro = $_POST["surveyIntro"];
					
					$stmt = DB::getInstance()->prepare($q);
					$stmt->bindParam(':surveyType',$surveyType,PDO::PARAM_STR);
					$stmt->bindParam(':surveyname',$surveyName,PDO::PARAM_STR);
					$stmt->bindParam(':initialtext',$surveyIntro,PDO::PARAM_STR);
					$stmt->bindParam(':url',$surveyURL,PDO::PARAM_STR);		
					$stmt->bindParam(':randomorder',$_POST["randomOrder"],PDO::PARAM_STR);						

					$stmt->execute();	
					
					$content="<p>New survey created.  Create another survey below or</p><form method=\"post\" action=\"./\"><input type=\"hidden\" name=\"stage\" value=\"3\" /><p><button type=\"submit\" class=\"btn btn-primary\">Continue to stage 3</button></p></form>";
				}
				catch(PDOException $e){
					$content = "<p class=\"error\"><b><i class=\"fa-solid fa-triangle-exclamation\"></i> Error:</b> Unable to insert survey into database</p><form method=\"post\" action=\"./\"><input type=\"hidden\" name=\"stage\" value=\"2\" /><p><button type=\"submit\" class=\"btn btn-primary\">Try again</button></p></form>";
				}				
			}
			else{
				$content = "<p class=\"error\"><b><i class=\"fa-solid fa-triangle-exclamation\"></i> Error:</b> a field was left blank.</p><form method=\"post\" action=\"./\"><input type=\"hidden\" name=\"stage\" value=\"2\" /><p><button type=\"submit\" class=\"btn btn-primary\">Try again</button></p></form>";
			}
		}
	}
	else if($_POST["stage"]==3){
		$stage = 3;
		$proceed = true;
		
		if(isset($_FILES)){
			foreach($_FILES as $id => $data){
				$id = explode("-",$id);
				$id = $id[1];
				
				if(is_numeric($id)){
					//get the survey type as CSV structure differs
					try{
						$q = "select surveytype, surveyname from ".$siteOptions["dbPrefix"]."survey where surveyid = :surveyid";		
						
						$stmt = DB::getInstance()->prepare($q);	

						$stmt->bindParam(':surveyid',$id,PDO::PARAM_INT);						

						$stmt->execute();
						
						$survey = $stmt->fetch(PDO::FETCH_ASSOC);

						if(!$survey["surveytype"]){
							$content .= "<p class=\"error\"><b><i class=\"fa-solid fa-triangle-exclamation\"></i> Warning:</b> Unable to find a matching survey in the database (".$e.")</p>";
							$proceed = false;							
						}
						else{
							if (($handle = fopen($data["tmp_name"], "r")) !== FALSE) {
								$i=0;
								while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
									if(($i==0 && $row[1]=="Order") || $row[0]==""){
										//ignore row as it's the headings or has no ID
										;
									}
									else{
										//types have different structures
										$numCell = count($row);
										$optStart=0;
										$qid = $row[0];
										$order = $row[1];
										$variable = $row[2];
										$question = $row[3];
										$mapName = $row[4];
										$maxAnswer = $row[5];
										$iFile = $row[6];
										$iAlt = $row[7];
										$iCredit = $row[8];
										$xrefs = $row[9];
										$notes = $row[10];
										$pos = "";
										$change = "";
										$aStem = "";
										if($survey["surveytype"]=="morphology"){
											$optStart = 11;
										}
										else if($survey["surveytype"]=="lexis"){
											$optStart = 13;
											$pos = $row[11];
											$change = $row[12];
										}
										else if($survey["surveytype"]=="phonology"){
											$optStart = 12;
											$aStem = $row[11];
										}
										
										//upload the question
										$insert = "insert into ".$siteOptions["dbPrefix"]."question(qid, qorder, surveyid, qvariable, qtext, mapname, maxanswers, ifilename, ialttext, icredit, xrefs, notes, pos, lchange, audiostem) values(:qid, :qorder, :surveyid, :qvariable, :qtext, :mapname, :maxanswers, :ifilename, :ialttext, :icredit, :xrefs, :notes, :pos, :lchange, :audiostem)";
										
										$stmt = DB::getInstance()->prepare($insert);
										
										//bind parameters
										$stmt->bindParam(':qid',$qid,PDO::PARAM_STR);
										$stmt->bindParam(':qorder',$order,PDO::PARAM_INT);
										$stmt->bindParam(':surveyid',$id,PDO::PARAM_INT);
										$stmt->bindParam(':qvariable',$variable,PDO::PARAM_STR);
										$stmt->bindParam(':qtext',$question,PDO::PARAM_STR);
										$stmt->bindParam(':mapname',$mapName,PDO::PARAM_STR);
										$stmt->bindParam(':maxanswers',$maxAnswer,PDO::PARAM_INT);
										$stmt->bindParam(':ifilename',$iFile,PDO::PARAM_STR);
										$stmt->bindParam(':ialttext',$iAlt,PDO::PARAM_STR);
										$stmt->bindParam(':icredit',$iCredit,PDO::PARAM_STR);
										$stmt->bindParam(':xrefs',$xrefs,PDO::PARAM_STR);
										$stmt->bindParam(':notes',$notes,PDO::PARAM_STR);
										$stmt->bindParam(':pos',$pos,PDO::PARAM_STR);
										$stmt->bindParam(':lchange',$change,PDO::PARAM_STR);
										$stmt->bindParam(':audiostem',$aStem,PDO::PARAM_STR);
										
										//execute
										$stmt->execute();
										
										//now add in the answer options
										for($j=$optStart;$j<$numCell;$j++){
											if($row[$j]!=""){
												$insert = "insert into ".$siteOptions["dbPrefix"]."answeroption(qid, atext) values(:qid, :atext)";
												
												$stmt = DB::getInstance()->prepare($insert);
												
												//bind parameters
												$stmt->bindParam(':qid',$qid,PDO::PARAM_STR);
												$stmt->bindParam(':atext',$row[$j],PDO::PARAM_STR);
												
												//execute
												$stmt->execute();
											}											
										}
									}
									$i++;
								}
								fclose($handle);
							}
						}
					}
					catch(PDOException $e){
						$content .= "<p class=\"error\"><b><i class=\"fa-solid fa-triangle-exclamation\"></i> Warning:</b> Error uploading question ".$qid." (".$e.")</p>";
						$proceed = false;
					}
				}
			}
		}
	}
	else if($_POST["stage"]==4){
		//set up regions and areas
		$stage = 4;
		$proceed = true;
		if(!isset($_POST["uploadAreas"])){
			//extract the regions from regions.json and add to the database
			$regions = file_get_contents("../json/regions.json");
			
			$regions = json_decode($regions,true);
			try{
				$content = "<p>Creating regions:</p><ul>";
				foreach($regions["features"] as $f){		
					$q = "insert into ".$siteOptions["dbPrefix"]."region(rid, region) values(:rid, :region)";
					
					$stmt = DB::getInstance()->prepare($q);
					$stmt->bindParam(':rid',$f["properties"][$siteOptions["regionID"]],PDO::PARAM_INT);
					$stmt->bindParam(':region',$f["properties"][$siteOptions["regionLabel"]],PDO::PARAM_STR);				

					$stmt->execute();	
					
					$content.="<li>".$f["properties"][$siteOptions["regionID"]].": ".$f["properties"][$siteOptions["regionLabel"]]."</li>";
					
				}
				$content.="</ul>";
			}
			catch(PDOException $e){
				$content = "<p class=\"error\"><b><i class=\"fa-solid fa-triangle-exclamation\"></i> Error:</b> Unable to insert region into database</p><form method=\"post\" action=\"./\"><input type=\"hidden\" name=\"stage\" value=\"3\" /><p><button type=\"submit\" class=\"btn btn-primary\">Try again</button></p></form>";
				$proceed = false;
			}
		}
	}
		else if($_POST["stage"]==5){
			//upload areas
			$stage = 5;
			$proceed = true;
			if(isset($_FILES)){
				if(isset($_FILES["areaJSON"]) && isset($_FILES["areaJSON"]["tmp_name"]) && $_FILES["areaJSON"]["tmp_name"]!="" && isset($_POST["areaJSONID"]) && $_POST["areaJSONID"]!="" && isset($_POST["areaJSONName"]) && $_POST["areaJSONName"]!=""){
					try{
						$areas = file_get_contents($_FILES["areaJSON"]["tmp_name"]);
						$areas = json_decode($areas,true);

						$jID = $_POST["areaJSONID"];
						$jName = $_POST["areaJSONName"];
						$content = "<p>Creating areas:</p><ul>";
						foreach($areas["features"] as $f){
							$q = "insert into ".$siteOptions["dbPrefix"]."area(geojsonid, areaname, geojson) values(:geojsonid, :areaname, :geojson)";
							
							$geoJSON = json_encode($f);
							
							$stmt = DB::getInstance()->prepare($q);
							$stmt->bindParam(':geojsonid',$f["properties"][$jID],PDO::PARAM_INT);
							$stmt->bindParam(':areaname',$f["properties"][$jName],PDO::PARAM_STR);	
							$stmt->bindParam(':geojson',$geoJSON,PDO::PARAM_STR);							

							$stmt->execute();	
							
							$content.="<li>".$f["properties"][$jID].": ".$f["properties"][$jName]."</li>";
							
						}
						$content.="</ul>";
					}
					catch(PDOException $e){
						$content = "<p class=\"error\"><b><i class=\"fa-solid fa-triangle-exclamation\"></i> Error:</b> Unable to insert area into database (".$e.")</p><form method=\"post\" action=\"./\"><input type=\"hidden\" name=\"stage\" value=\"4\" /><p><button type=\"submit\" class=\"btn btn-primary\">Try again</button></p></form>";
						$proceed = false;
					}
				}
			}
		}
		else if($_POST["stage"]==6){
			//upload settlement data
			$stage = 6;
			$proceed = true;
			if(isset($_FILES)){
				if(isset($_FILES["settlementCSV"]) && isset($_FILES["settlementCSV"]["tmp_name"]) && $_FILES["settlementCSV"]["tmp_name"]!=""){
					if (($handle = fopen($_FILES["settlementCSV"]["tmp_name"], "r")) !== FALSE) {
						$i=0;
						try{
							$content = "<p>Associating CSV areas, joining to regions and adding settlements:</p><ul>";
							while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
								if(($i==0 && $row[1]=="Region") || $row[0]==""){
									//ignore row as it's the headings or has no Area name
									;
								}
								else{
									//types have different structures
									$numCell = count($row);
									$optStart=4;
									$aname = $row[0];
									$rname = $row[1];
									$add1 = $row[2];
									$add2 = $row[3];

									//now update the relevant row in the area database and join to the appropriate region
									
									//find ID for matching area
									$q = "select areaid from ".$siteOptions["dbPrefix"]."area where areaname = :areaname";
									
									$stmt = DB::getInstance()->prepare($q);
									
									//bind parameters
									$stmt->bindParam(':areaname',$aname,PDO::PARAM_STR);
									
									//execute
									$stmt->execute();
									
									$areaMatch = $stmt->fetchAll(PDO::FETCH_ASSOC);
									
									if(count($areaMatch)!=1){
										$content.="<li><b>ERROR:</b> Area ".$aname;
										if(count($areaMatch)>1){
											$content.="matches more than one area created from the GeoJSON file</li>";
										}
										else
											$content.=" was not created from the GeoJSON file<li>";
									}
									else{
										$areaid = $areaMatch[0]["areaid"];
										
										//find ID for matching region
										$q = "select rid from ".$siteOptions["dbPrefix"]."region where region = :rname";
										
										$stmt = DB::getInstance()->prepare($q);
										
										//bind parameters
										$stmt->bindParam(':rname',$rname,PDO::PARAM_STR);
										
										//execute
										$stmt->execute();
										
										$regionMatch = $stmt->fetchAll(PDO::FETCH_ASSOC);
										
										if(count($regionMatch)!=1){
											$content.="<li><b>ERROR:</b> Region ".$rname;
											if(count($regionMatch)>1){
												$content.="matches more than one region created from the GeoJSON file</li>";
											}
											else
												$content.=" was not created from the GeoJSON file<li>";
										}
										else{
											$rid = $regionMatch[0]["rid"];
											$q = "update ".$siteOptions["dbPrefix"]."area set rid = :rid, additionalgroup1 = :additionalgroup1, additionalgroup2 = :additionalgroup2 where areaid = :areaid";
											
											$stmt = DB::getInstance()->prepare($q);
											
											//bind parameters
											$stmt->bindParam(':areaid',$areaid,PDO::PARAM_INT);	
											$stmt->bindParam(':rid',$rid,PDO::PARAM_INT);	
											$stmt->bindParam(':additionalgroup1',$add1,PDO::PARAM_STR);	
											$stmt->bindParam(':additionalgroup2',$add2,PDO::PARAM_STR);	

											//execute
											$stmt->execute();
											
											$content.="<li>Data for ".$aname." merged with record from GeoJSON and associated with region '".$rname."'. Settlements:<ul>";
											
											//now add in settlements
											
											for($j=$optStart;$j<$numCell;$j++){
												if($row[$j]!=""){
													//split settlement into place and county
													//but first check there isn't a final comma
													$row[$j] = trim($row[$j]);
													if(substr($row[$j],-1)==",")
														$row[$j] = substr($row[$j],0,-1);
													$lBits = explode(",",$row[$j]);
													if(count($lBits)==1){
														$lname = $lBits[0];
														$lcounty = "";
													}
													else if(count($lBits)==2){
														$lname = $lBits[0];
														$lcounty = $lBits[1];
													}
													else{
														$lcounty = array_pop($lBits);
														$lname = implode(",",$lBits);
													}
													
													$insert = "insert into ".$siteOptions["dbPrefix"]."area_location(areaid, lname, lcounty) values(:areaid, :lname, :lcounty)";
													
													$stmt = DB::getInstance()->prepare($insert);
													
													//bind parameters
													$stmt->bindParam(':areaid',$areaid,PDO::PARAM_INT);
													$stmt->bindParam(':lname',$lname,PDO::PARAM_STR);
													$stmt->bindParam(':lcounty',$lcounty,PDO::PARAM_STR);
													
													//execute
													$stmt->execute();
													
													$content.="<li>".$lname." (".$lcounty.")</li>";
												}																				
											}
											
											$content.="</ul></li>";
										}										
									}
								}
								$i++;
							}
							fclose($handle);
							$content.="</ul>";
						}
						catch(PDOException $e){
							$content = "<p class=\"error\"><b><i class=\"fa-solid fa-triangle-exclamation\"></i> Error:</b> Unable to insert area into database (".$e.")</p><form method=\"post\" action=\"./\"><input type=\"hidden\" name=\"stage\" value=\"5\" /><p><button type=\"submit\" class=\"btn btn-primary\">Try again</button></p></form>";
							$proceed = false;
						}
					}					
				}
			}
		}
}


if($stage==1){
	
	$proceed = true;
	
	$content = "<h2 class=\"alignCenter\">Setup a new Speak For Yersel instance - Stage 1</h2><p>If you haven't done so already please ensure you have created a database for the project and have added the database name, username and password to the file <b>incs/db.php</b>.</p>";
	
	//check to see that the database details are available and work
	try{
		$q = "show tables";		
		
		$stmt = DB::getInstance()->prepare($q);				

		$stmt->execute();
		
		$dbTest = $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	catch(PDOException $e){
		$content .= "<p class=\"error\"><b><i class=\"fa-solid fa-triangle-exclamation\"></i> Warning:</b> Unable to connect to the database.  Please check the details in <b>incs/db.php</b> and reload this page.</p>";
		$proceed = false;
	}
	
	$content.="<p>Also ensure you have supplied any required details in the file <b>incs/config.php</b>.  The following details are currently saved:</p><ul>";
	
	foreach($siteOptions as $name => $value){
		$content.="<li><b>".$name.":</b> ".htmlspecialchars($value)."</li>";
	}
	
	$content.="</ul><p>If you wish to change these values update <b>incs/config.php</b> and reload this page.</p>";
	
	//check that the 'regions' file is also present
	clearstatcache();
	if(!file_exists("../json/regions.json")){
		$content .= "<p class=\"error\"><b><i class=\"fa-solid fa-triangle-exclamation\"></i> Warning:</b> The survey requires a GeoJSON file containing broad geographical regions.  This file must be called <b>regions.json</b> and must be saved in the survey's <b>json</b> directory.  The file does not appear to exist.  Please add the file and reload this page.</p>";
		$proceed = false;		
	}
	
	if($proceed){
		$content.="<form method=\"post\" action=\"./\"><input type=\"hidden\" name=\"stage\" value=\"2\" /><input type=\"hidden\" name=\"createDB\" value=\"Y\" /><p><button type=\"submit\" class=\"btn btn-primary\">Continue</button></p></form>";
	}
}
else if($stage==2){
	if($content)
		$content = "<h2 class=\"alignCenter\">Setup a new Speak For Yersel instance -  Stage 2</h2>".$content;
	else
		$content = "<h2 class=\"alignCenter\">Setup a new Speak For Yersel instance -  Stage 2</h2>";
	
	if($proceed){
		//now create the survey types
		$content.="<h3>Create a survey type</h3><form method=\"post\" action=\"./\"><input type=\"hidden\" name=\"stage\" value=\"2\" /><input type=\"hidden\" name=\"createSurvey\" value=\"Y\" /><div class=\"container-fluid\"><div class=\"row\"><div class=\"col-md-3 gy-5\"><label for=\"surveyType\" class=\"form-label\">Survey type</label><select class=\"form-select\" name=\"surveyType\" id=\"surveyType\"><option></option><option value=\"morphology\">Morphology</option><option value=\"lexis\">Lexis</option><option value=\"phonology\">Phonology</option></select></div><div class=\"col-md-3 gy-5\"><label for=\"surveyName\" class=\"form-label\">Survey name</label><input type=\"text\" class=\"form-control\" name=\"surveyName\" id=\"surveyName\"></div><div class=\"col-md-3 gy-5\"><label for=\"surveyURL\" class=\"form-label\">Page URL</label><input type=\"text\" class=\"form-control\" name=\"surveyURL\" id=\"surveyURL\"></div><div class=\"col-md-3 gy-5\"><label for=\"randomOrder\" class=\"form-label\">Randomise question order</label><select class=\"form-select\" name=\"randomOrder\" id=\"randomOrder\"><option>No</option><option>Yes</option></select></div><div class=\"col-md-12 gy-5\"><label for=\"surveyIntro\" class=\"form-label\">Introductory text</label><textarea class=\"form-control\" name=\"surveyIntro\" id=\"surveyIntro\"></textarea></div><div class=\"col-12 gy-5\"><p><button type=\"submit\" class=\"btn btn-primary\">Continue</button></p></div></div></div></form>";
	}
}
else if($stage==3){
	if($content)
		$content = "<h2 class=\"alignCenter\">Setup a new Speak For Yersel instance -  Stage 3</h2>".$content;
	else
		$content = "<h2 class=\"alignCenter\">Setup a new Speak For Yersel instance -  Stage 3</h2>";
	
		//first get a list of surveys and see whether they have questions yet
		try{
			$q = "select ".$siteOptions["dbPrefix"]."survey.surveyid, surveytype, surveyname, randomorder, (select count(*) from ".$siteOptions["dbPrefix"]."question where surveyid = ".$siteOptions["dbPrefix"]."survey.surveyid) as num from ".$siteOptions["dbPrefix"]."survey";		
			
			$stmt = DB::getInstance()->prepare($q);				

			$stmt->execute();
			
			$surveys = $stmt->fetchAll(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e){
			$content .= "<p class=\"error\"><b><i class=\"fa-solid fa-triangle-exclamation\"></i> Warning:</b> Unable to connect to the database.  Please check the details in <b>incs/db.php</b> and reload this page.</p>";
			$proceed = false;
		}
	
	if($proceed){
		//now upload survey questions
		
		$content.="<h3>Upload survey questions and answer options</h3><form method=\"post\" action=\"./\"  enctype=\"multipart/form-data\"><input type=\"hidden\" name=\"stage\" value=\"3\" /><input type=\"hidden\" name=\"uploadQuestions\" value=\"Y\" /><table class=\"table table-striped\"><thead><tr><th scope=\"col\">Survey Type</th><th scope=\"col\">Survey Name</th><th scope=\"col\">Random order</th><th scope=\"col\">Questions</th></tr></thead><tbody>";
		
		$alldone = true;
		foreach($surveys as $survey){
			$content.="<tr><td>".$survey["surveytype"]."</td><td>".$survey["surveyname"]."</td><td>".$survey["randomorder"]."</td><td>";
			if($survey["num"]>0)
				$content.=$survey["num"];
			else{
				$content.="<input type=\"file\" class=\"form-control\" name=\"questions-".$survey["surveyid"]."\">";
				$alldone = false;
			}
			$content.="</td></tr>";
		}
		
		$content.="</tbody></table>";
		
		if(!$alldone)
			$content.="<p><button type=\"submit\" class=\"btn btn-primary\">Upload Questions</button></p></form>";
		else
			$content.="</form><p>Questions have been uploaded for all surveys.</p><form method=\"post\" action=\"./\"><input type=\"hidden\" name=\"stage\" value=\"4\" /><p><button type=\"submit\" class=\"btn btn-primary\">Continue</button></p></form>";
		
	}
}
else if($stage==4){
	if($content)
		$content = "<h2 class=\"alignCenter\">Setup a new Speak For Yersel instance -  Stage 4</h2>".$content;
	else
		$content = "<h2 class=\"alignCenter\">Setup a new Speak For Yersel instance -  Stage 4</h2>";	
	
	if($proceed){
		//now upload Areas
		
		$content.="<h3>Upload Area JSON file</h3><form method=\"post\" action=\"./\"  enctype=\"multipart/form-data\"><input type=\"hidden\" name=\"stage\" value=\"5\" /><input type=\"hidden\" name=\"uploadAreas\" value=\"Y\" /><div class=\"mb-3\"><label for=\"areaJSON\" class=\"form-label\">Area GeoJSON file</label> <input class=\"form-control\" type=\"file\" id=\"areaJSON\" name=\"areaJSON\"></div><div class=\"mb-3\"><label for=\"areaJSONID\" class=\"form-label\">Property field containing area ID</label> <input class=\"form-control\" type=\"text\" id=\"areaJSONID\" name=\"areaJSONID\"></div><div class=\"mb-3\"><label for=\"areaJSONName\" class=\"form-label\">Property field containing area name</label> <input class=\"form-control\" type=\"text\" id=\"areaJSONName\" name=\"areaJSONName\"></div><p><button type=\"submit\" class=\"btn btn-primary\">Continue</button></p></form>";
	}
}
else if($stage==5){
	if($content)
		$content = "<h2 class=\"alignCenter\">Setup a new Speak For Yersel instance -  Stage 5</h2>".$content;
	else
		$content = "<h2 class=\"alignCenter\">Setup a new Speak For Yersel instance -  Stage 5</h2>";	
	
	if($proceed){
		//now upload Areas
		
		$content.="<h3>Upload Settlement CSV file</h3><form method=\"post\" action=\"./\"  enctype=\"multipart/form-data\"><input type=\"hidden\" name=\"stage\" value=\"6\" /><input type=\"hidden\" name=\"uploadAreas\" value=\"Y\" /><div class=\"mb-3\"><label for=\"settlementCSV\" class=\"form-label\">Settlement CSV file</label> <input class=\"form-control\" type=\"file\" id=\"settlementCSV\" name=\"settlementCSV\"></div><p><button type=\"submit\" class=\"btn btn-primary\">Continue</button></p></form>";
	}	
}
else if($stage==6){
	if($content)
		$content = "<h2 class=\"alignCenter\">Setup a new Speak For Yersel instance -  Complete</h2>".$content;
	else
		$content = "<h2 class=\"alignCenter\">Setup a new Speak For Yersel instance -  Complete</h2>";	
	
	if($proceed){
		//now upload Areas
		
		$content.="<h3>Setup complete</h3><p>Please test the site <a href=\"../\">here</a> and then delete or comment out this file.</p>";
	}		
}

setupFormatTop();
echo($content);
setupFormatBottom();

function setupFormatTop(){

?>
<!doctype html>
<html lang="en">
  <head>
  
  <title>Speak For Yersel :: Setup</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400;500;600&family=Noto+Serif:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet"> 
	<link rel="stylesheet" href="setup.css">
	
	<!-- JavaScript -->
	<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
	<script src="setup.js"></script>

  </head>
  <body>

<div class="container-fluid" id="mainPage">  
<div class="row" id="headerBar">
<div class="col-sm-12"><a href="./" id="headerText">Setup Speak For Yersel</a></div>
</div>
	<div class="container-fluid" id="mainPageArea"> 
	<?php
}

function setupFormatBottom(){
	?>
	
</div>
<div class="row" id="footer">
<div class="col-md-12 alignCenter">
<p>Developed at the University of Glasgow</p>
</div>
</div>
</div>
  </body>
</html>
<?php
exit;
}