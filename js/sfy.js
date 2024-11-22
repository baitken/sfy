$(function() {
	//first get some info about the site
	var localStem = $("#siteSubDirectory").text();
	localStem+="_";
	var subDir = $("#siteSubDirectory").text();
	if(subDir)
		subDir = "/"+subDir;
	var siteTitle = $("#siteTitle").text();
	var siteSurveyArea = $("#siteSurveyArea").text();
	var defll = $("#siteDefaultLatLng").text();
	defll = defll.split(",");
	var siteDefaultLat = parseFloat(defll[0]);
	var siteDefaultLng = parseFloat(defll[1]);
	var siteDefaultZoom = parseFloat($("#siteDefaultZoom").text());
	var siteRegionLabel = $("#siteRegionLabel").text();
	var siteOtherLanguageQuestion = $("#siteOtherLanguageQuestion").text();
	var siteOtherLanguage = $("#siteOtherLanguage").text();
	
	//map variables (used on survey and map pages)
	var map;
	var legend;
	var ratingLayers = [];
	var mapLoaded = false;
	var mapFiltersVisible = false;
	
	//set up the base map
	var baseMap = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}',{attribution: '<a href="#" id="openAttribution" data-bs-toggle="modal" data-bs-target="#attributionBox">Attribution and copyright</a>',
		maxZoom: 12,
		minZoom: 6
	});
	
	//set the colours used on the maps
	//if changing these you need to change the marker colours in the CSS file too ('.rating-' and '.lexical-' styles)
	
	//these colours are used for the morphology survey type (gradient ratings 1-4)
	var ratingColors = ["","#1A3066","#5e7fb4","#87AECB","#C9E0F2"];

	//these colours are used for the other two survey types (different colours)
	var lexicalColors = ['','#1A3066','#39A357','#9763C7','#A88C2F','#7D9399','#832232','#0F7173','#DB7258','#b1f0ed','#83cab9','#b1c8c0','#a9d8da','#c5e6dc','#c5e6dc'];
	
	//sets the size of the map markers
	var radius = 5;
	
	//see which page we're on
	var bits = window.location.href;
	bits = bits.split("/");
	
	//console.log("bits = "+bits);
	
	var section = "";
	var page = "";
	var selectedMap = "";
	
	if(bits[bits.length-4]=="explore-maps"){
		section = bits[bits.length-4];
		page = bits[bits.length-3];
		selectedMap = bits[bits.length-2];
	}
	else{
		var test = bits[bits.length-3];
		
		var section = "";
		var page = bits[bits.length-2];
		
		if(test=="survey" || test == "explore-maps" || test=="about"){
			section = test;
		}
	
	}
	
	//console.log("section = "+section+" and page = "+page+" and map = "+selectedMap);
	
	//URLs of AJAX data
	var locationURL = subDir+"/ajax/get-location.php";
	var registerUserURL = subDir+"/ajax/register-user.php";
	var surveyURL = subDir+"/ajax/get-surveys.php";
	var questionURL = subDir+"/ajax/get-questions.php";
	var answerURL = subDir+"/ajax/set-data.php";
	var regionsURL = subDir+"/json/regions.json";
	var mapDataURL = subDir+"/ajax/get-map-data.php";

	//User management
	var registered = false;
	if (typeof(Storage) !== "undefined") {
	  //see if the user is registered
	  if(localStorage.getItem(localStem+"pid")){
		  $("#navBar").append("<a href=\""+subDir+"/register/\" class=\"user userYes\"><i class=\"fas fa-user-check\"></i></a>");
		  registered = true;
	  }
	  else{
		  $("#navBar").append("<a href=\""+subDir+"/register/\" class=\"user userNo\"><i class=\"fas fa-user-times\"></i></a>");
		  if(section=="survey" || page=="survey")
			  window.location.href = subDir+'/register/';
	  }
	  
	} else {
	  $("#mainPageArea").html("<h2 class=\"alignCenter\"><i class=\"fas fa-frown\"></i></h2><p class=\"alignCenter\">This resource requires a web browser with HTML5 Local Storage turned on.  Please try a different browser if you can.</p>");
	  page = "";
	}

	//process content for the specific pages
	if(page=="register"){
		//user registration / sign out here
		if(registered){
			var content = "<h2 class=\"alignCenter\">About you</h2><p>What year were you born? <b>"+localStorage[localStem+"yearBorn"]+"</b></p><p>Where did you live between 0-18? <b>"+localStorage[localStem+"locationName"]+"</b></p><p>Did/do you go to university? <b>"+localStorage[localStem+"uni"]+"</b></p><p>What is your gender? <b>"+localStorage[localStem+"gender"]+"</b></p>";
			
			if(siteOtherLanguageQuestion){
				content+="<p>Do you speak "+siteOtherLanguage+"? <b>";
				if(localStorage[localStem+"otherLanguage"]==1)
					content+="Daily";
				else if(localStorage[localStem+"otherLanguage"]==2)
					content+="Sometimes";
				else if(localStorage[localStem+"otherLanguage"]==3)
					content+="Rarely";
				else if(localStorage[localStem+"otherLanguage"]==4)
					content+="I don't speak "+siteOtherLanguage;
				content+="</b></p>";
			}
			
			content+="<p>Not you?  Please <a href=\"#\" id=\"signOut\" class=\"btn btn-sm btn-primary\">sign out</a> and fill in your own details.</p>";
		}
		else{
			var content = "<h2 class=\"alignCenter\">About you</h2><p>Before you begin the surveys, please complete the following questions.</p><form id=\"registerForm\" action=\"./\" method=\"get\"><div class=\"mb-3\"><label for=\"yearBorn\">What year were you born?</label><select class=\"form-control\" id=\"yearBorn\"><option value=\"-1\">Please select...</option>";
			
			//most recent is current year minus 5
			var now = new Date();
			var year = now.getFullYear();
			
			var first = year-5;
			
			for(var i=first;i>1939;i--){
				content+="<option>"+i+"</option>";
			}
			
			content+="<option value=\"1939\">Before 1940</option></select><div id=\"yearBornValidate\" class=\"registerValidation\"></div></div><div class=\"mb-3\"><label for=\"whereLive\">Type the main place you lived up to the age of 18. If you lived somewhere outside "+siteSurveyArea+", then please type ‘outside’ and select ‘Outside "+siteSurveyArea+"’</label><input type=\"text\" id=\"whereLive\" class=\"form-control\" placeholder=\"Start typing...\" /><input type=\"hidden\" id=\"lid\" /><div id=\"whereLiveValidate\" class=\"registerValidation\"></div></div><div class=\"mb-3\"><label for=\"uni\">Did/do you go to university?</label><select class=\"form-control\" id=\"uni\"><option value=\"-1\">Please select...</option><option value=\"Y\">Yes</option><option value=\"N\">No</option><option value=\"School\">Still at school</option></select><div id=\"uniValidate\" class=\"registerValidation\"></div></div><div class=\"mb-3\"><label for=\"gender\">What is your gender?</label><select class=\"form-control\" id=\"gender\"><option value=\"-1\">Please select...</option><option value=\"Female\">Female</option><option value=\"Male\">Male</option><option value=\"Other\">Other/Non-Binary/Prefer not to say</option></select><div id=\"genderValidate\" class=\"registerValidation\"></div></div>";
			
			if(siteOtherLanguageQuestion){
				content+="<div class=\"mb-3\"><label for=\"otherLanguage\">Do you speak "+siteOtherLanguage+"?</label><select class=\"form-control\" id=\"otherLanguage\"><option value=\"-1\">Please select...</option><option value=\"1\">Daily</option><option value=\"2\">Sometimes</option><option value=\"3\">Rarely</option><option value=\"4\">I don't speak "+siteOtherLanguage+"</option></select><div id=\"otherLanguageValidate\" class=\"registerValidation\"></div></div>";
			}
			
			content+="<p><b>By pressing Register, you agree that your anonymous responses to these activities can be included on the "+siteTitle+" website. For more information on the information we collect and how we store and use it, please view our <a href=\""+subDir+"/privacy/\">Privacy Page</a>.</p><p><button type=\"submit\" class=\"btn btn-primary\">Register</button></p></form>";
		}
		$("#mainPageArea").html(content);
		
		//set up the location autocomplete
		$( "#whereLive" ).autocomplete({
		  source: locationURL,
		  minLength: 2,
		  select: function( event, ui ) {
			$("#lid").val(ui.item.id);
		  }
		});
		
		$(document).on('click',"#signOut", function(){
			localStorage.removeItem(localStem+"pid");
			localStorage.removeItem(localStem+"lid");
			localStorage.removeItem(localStem+"locationName");
			localStorage.removeItem(localStem+"areaname");
			localStorage.removeItem(localStem+"areaid");
			localStorage.removeItem(localStem+"geojson");
			localStorage.removeItem(localStem+"createDate");
			localStorage.removeItem(localStem+"yearBorn");
			localStorage.removeItem(localStem+"uni");
			localStorage.removeItem(localStem+"gender");
			localStorage.removeItem(localStem+"otherLanguage");
			localStorage.removeItem(localStem+"surveysCompleted");
			
			$(".user").removeClass("userYes");
			$(".user").addClass("userNo");
			window.location.href = subDir+'/register/';
			return false;
		});
		
		$(document).on('submit',"#registerForm", function(){
			//check that necessary info has been completed
			var proceed = true;
			if($("#yearBorn option:selected").text()=="Please select..."){
				$("#yearBornValidate").text("Please select a year");
				proceed = false;
			}
			else
				$("#yearBornValidate").empty();
			
			if(!$("#lid").val()){
				$("#whereLiveValidate").text("Please choose a location");
				proceed = false;
			}
			else
				$("#whereLiveValidate").empty();
			
			if($("#uni option:selected").text()=="Please select..."){
				$("#uniValidate").text("Please choose an option");
				proceed = false;
			}
			else
				$("#uniValidate").empty();
			
			if($("#gender option:selected").text()=="Please select..."){
				$("#genderValidate").text("Please choose an option");
				proceed = false;
			}
			else
				$("#genderValidate").empty();
			
			if(siteOtherLanguageQuestion){
				if($("#otherLanguage option:selected").text()=="Please select..."){
					$("#otherLanguageValidate").text("Please choose an option");
					proceed = false;
				}
				else
					$("#otherLanguageValidate").empty();
			}
			
			if(proceed){
				//submit data and register user
				var registerURL = registerUserURL+"?submit=Y&";
				
				var yearBorn = $("#yearBorn option:selected").val();
				var whereLive = $("#lid").val();
				var uni = $("#uni option:selected").val();
				var uniText = "No";
				if(uni=="Y")
					uniText = "Yes";
				else if(uni=="School")
					uniText = "Still at school";
				var gender = $("#gender option:selected").val();
				var otherLanguage = null;
				if(siteOtherLanguageQuestion)
					otherLanguage = $("#otherLanguage option:selected").val();
				
				registerURL+="yearBorn="+yearBorn+"&lid="+whereLive+"&uni="+uni+"&gender="+gender+"&otherLanguage="+otherLanguage;
				
				$.getJSON( registerURL, function( data ) {
					
					var pid = data[0].pid;
					pid = pid.split("-");
					var createDate = pid[1];
					pid = pid[0];
				
					localStorage[localStem+"pid"] = pid;
					localStorage[localStem+"lid"] = data[0].lid;
					localStorage[localStem+"locationName"] = data[0].locationName;
					localStorage[localStem+"areaid"] = data[0].areaid;
					localStorage[localStem+"areaname"] = data[0].areaname;
					localStorage[localStem+"geojson"] = JSON.stringify(data[0].geojson);
					localStorage[localStem+"createDate"] = createDate;
					if(yearBorn>1939)
						localStorage[localStem+"yearBorn"] = yearBorn;
					else
						localStorage[localStem+"yearBorn"] = "Before 1940";
					localStorage[localStem+"uni"] = uniText;
					if(siteOtherLanguageQuestion)
						localStorage[localStem+"otherLanguage"] = otherLanguage;
					localStorage[localStem+"gender"] = gender;
					localStorage[localStem+"completedSurveys"] = JSON.stringify([]);
					
					$(".user").removeClass("userNo");
					$(".user").addClass("userYes");

					
					window.location.href = subDir+'/survey/';
				});
			}
			event.preventDefault();			
			return false;
		});
	}
	else if(section=="survey" || section=="explore-maps" || page=="survey" || page=="explore-maps"){		
		//get the information about surveys
		$.getJSON(surveyURL, function( surveys ) {	
			var numSurveys = surveys.length;	
			if(page=="survey"){
				//survey intro page
				var cs = JSON.parse(localStorage[localStem+"completedSurveys"]);
				var content = "<h3 class=\"alignRight\">"+cs.length+" out of "+numSurveys+" surveys complete</h3><p class=\"alignCenter\">";
				for(var i=0;i<numSurveys;i++){
					content+="<a href=\"./"+surveys[i].url+"/\" class=\"btn btn-primary beginExercise\" id=\"survey-"+surveys[i].surveyid+"\">";
					if(cs.includes(surveys[i].surveyid))
						content+="<i class=\"fa-solid fa-circle-check green\"></i> ";
					content+=surveys[i].surveyname+" <i class=\"fas fa-arrow-right\"></i></a><br />";
				}
				content+="</p>";
				$("#mainPageArea").html(content);
			}
			else if(section=="survey"){
				//variables needed for the survey
				var surveyID = 0;
				var surveyName = "";
				var surveyType = "";
				var surveyIntro = "";
				var currentQ = 1;
				var qid = 0;
				var questions = [];
				var tot = 0;
				var numMultipleSelected = 0;
				
				//check that the page corresponds to an available survey and if so proceed
				for(var i=0;i<numSurveys;i++){
					if(page==surveys[i].url){
						surveyID = surveys[i].surveyid;
						surveyName = surveys[i].surveyname;
						surveyType = surveys[i].surveytype;
						surveyIntro = surveys[i].initialtext;
						break;
					}
				}
				
				if(surveyID){
					//get the questions and display the intro page
					$.getJSON(questionURL+"?id="+surveyID, function( data ) {
						questions = data;
						tot = questions.length;
						var content = "<div class=\"alignCenter\"><h2>"+surveyName+"</h2>"+surveyIntro;
						content+="<p><a href=\"#\" id=\"beginActivity\" class=\"btn btn-primary btn-lg\">Let’s go <i class=\"fas fa-arrow-right\"></i></a></p></div>";
						$("#mainPageArea").html(content);
					});
				}
				
				$(document).on("click","#beginActivity",function(){
					//set up the page structure
					var content = "<div class=\"row\"><div class=\"col-md-6\"><h2>"+surveyName+"</h2></div><div class=\"col-md-6 alignRight\" id=\"progressTally\"></div></div></div><div class=\"row\" id=\"progressBarRow\"><div class=\"col-md-12\"><div id=\"progressBar\"><span id=\"progressDone\"></span></div></div></div><div class=\"row\" id=\"exerciseSpaceRow\"><div class=\"col-md-4\" id=\"exerciseSpace\"></div><div class=\"col-md-8\" id=\"mapSpace\"></div></div><div class=\"row\" id=\"loadNext\"></div>";
					
					$("#mainPageArea").slideUp("slow",function(){
						$("#mainPageArea").html(content);
						loadQuestion(currentQ);
						$("#mainPageArea").slideDown("slow");
					});
					return false;
				});
				
				//process answer and load map
				$(document).on("click",".answer",function(){
					
					if($(this).hasClass("multiple")){
						//we don't immediately process the map with multiple selections
						
						//get the max number of selectable answers
						var cls = $(this).attr("class");
						cls = cls.split(" ");
						var maxAnswers = 0;
						for(var i=0;i<cls.length;i++){
							if(cls[i].slice(0,9)=="multiple-"){
								var multipleCls = cls[i].split("-");
								maxAnswers = parseInt(multipleCls[1]);
							}
						}
						if(maxAnswers){
							//see whether this is already selected
							if($(this).hasClass("answerSelected")){
								$(this).removeClass("answerSelected");
								numMultipleSelected--;
								
								if(numMultipleSelected<1)
									$("#loadNext").empty();
							}
							else{
								if(numMultipleSelected<maxAnswers){
									numMultipleSelected++;
									$(this).addClass("answerSelected");
									
									//but add in the button to load the map if it's not already there
									if($("#loadNext").is(":empty")){
										$("#loadNext").html("<div class=\"col-md-12\"><a href=\"#\" class=\"btn btn-primary btn-lg btn-block alignRight continueToMap\">Continue <i class=\"fas fa-arrow-right\"></i></a></div>");
									}
								}
							}
						}
					}
					else{
						processAnswer([$(this).attr("id")]);
					}
					
					return false;
				});
				
				//load the next question (or finish the survey)
				$(document).on("click","#loadNextButton",function(){
					if($(this).hasClass("surveyComplete")){
						//note the survey as completed (if not already done so)
						//console.log("Survey ID = "+surveyID);
						var surveyDone = false;
						var cs = JSON.parse(localStorage[localStem+"completedSurveys"]);
						if(cs.includes(surveyID))
							surveyDone = true;
						if(!surveyDone){
							cs[cs.length] = surveyID;
							localStorage[localStem+"completedSurveys"] = JSON.stringify(cs);
						}
						//console.log(localStorage[localStem+"completedSurveys"]);
						
						$("#exerciseSpaceRow").slideUp("slow",function(){
							$("#progressBar").hide();
							$("#progressTally").hide();
							$("#loadNext").hide();
							var content = "<div class=\"col-md-12\"><h3>Survey complete!</h3>You have completed this survey. "+cs.length+" out of "+numSurveys+" surveys are now complete. What next?</p><p><a href=\"../\" class=\"btn btn-primary btn-lg\">Choose a different survey <i class=\"fas fa-arrow-right\"></i></a><br /><a href=\""+subDir+"/explore-maps/"+page+"\" class=\"btn btn-primary btn-lg\">Explore the maps <i class=\"fas fa-arrow-right\"></i></a><br /></p></div>";
							
							$("#exerciseSpaceRow").html(content);
							$("#exerciseSpaceRow").slideDown("slow");
						});
					}
					else
						loadQuestion(currentQ);
					
					return false;
				});
				
				//submit answer for questions that allow multiple answers
				$(document).on("click",".continueToMap",function(){
					
					//save the answers	
					var answerIDs = [];
					$(".answerSelected").each(function(){
						answerIDs[answerIDs.length] = $(this).attr("id");				
					});

					processAnswer(answerIDs);

					return false;
				});
				
				/************************************
				* Functions used by the survey page *
				************************************/
				
				//Load the next question
				function loadQuestion(n){
					$("#exerciseSpaceRow").slideUp("slow",function(){
						$("#mapSpace").hide();						
						$("#loadNext").empty();
						
						var i = n-1;
						
						numMultipleSelected = 0;
						
						processProgress(n, tot);

						qid = questions[i].qid;
						
						$("#progressTally").html("<h3>Question <span id=\"qNum\">"+questions[i].qorder+"</span> of "+tot+"</h3>");
						
						var content = "";
						
						if(surveyType=="phonology" || surveyType=="lexis")
							content+="<p><b>Remember! The answers you give are when you're talking to family and friends.</b></p>";
						else
							content+="<p><b>Remember! Here we want to see if a sentence sounds ok to you.</b></p>";
						
						content+="<p>"+questions[i].qtext+"</p>";
						
						if(surveyType=="morphology"){							
							content+="<div class=\"row align-items-stretch\">";
							for(var j=0;j<questions[i].answerOptions.length;j++){
								content+="<div class=\"col-lg-3 answerCol\">";
								content+="<a href=\"#\" class=\"btn btn-primary btn-lg btn-block answer\" id=\"answer-"+questions[i].answerOptions[j].aoid+"\">"+questions[i].answerOptions[j].atext+"</a></div>";
							}
							content+="</div>";
							
							//image displayed below the options for this question type
							if(questions[i].ifilename){
								content+="<div class=\"row\"><div class=\"col-md-12\"><img src=\""+subDir+"/media/"+questions[i].ifilename+"\" alt=\""+questions[i].ialttext+"\" class=\"choiceImage\" />";
								if(questions[i].icredit)
									content+="<p class=\"small alignRight\">"+questions[i].icredit+"</p>";
								content+="</div></div>";
							}
						}
						
						else if(surveyType=="lexis" || (surveyType=="phonology" && !questions[i].audiostem)){
							
							var numChoice = questions[i].answerOptions.length;
							
							content+="<div class=\"row\">";
							
							//image displayed to the left for this question type
							if(questions[i].ifilename){
								var imgBit = "<img src=\""+subDir+"/media/"+questions[i].ifilename+"\" alt=\""+questions[i].ialttext+"\" class=\"choiceImage\" />";
								if(questions[i].icredit)
									imgBit+="<p class=\"small alignRight\">"+questions[i].icredit+"</p>";
								if(numChoice>4)
									content+="<div class=\"col-lg-4\">"+imgBit+"</div><div class=\"col-lg-4\">";
								else
									content+="<div class=\"col-lg-6\">"+imgBit+"</div><div class=\"col-lg-6\">";
							}
							else{
								if(numChoice>4)
									content+="<div class=\"col-lg-6\">";
								else
									content+="<div class=\"col-lg-12\">";					
							}
							
							var col2 = false;
							
							var test = "";
							
							for(var j=0;j<numChoice;j++){
								content+="<a href=\"#\" class=\"btn btn-primary btn-lg answer wordChoice";
								
								if(questions[i].maxanswers>1)
									content+=" multiple multiple-"+questions[i].maxanswers;
								
								content+="\" id=\"answer-"+questions[i].answerOptions[j].aoid+"\">"+questions[i].answerOptions[j].atext+"</a><br />";
								
								test = (numChoice)/2;
								
								if(numChoice>4 && j>=test-1 && !col2){
									col2 = true;
									if(questions[i].ifilename)
										content+="</div><div class=\"col-lg-4\">";
									else
										content+="</div><div class=\"col-lg-6\">";
								}
							}	
							
							content+="</div></div>";

						}
						else if(surveyType=="phonology" && questions[i].audiostem){
							var numChoice = questions[i].answerOptions.length;
							
							content+="<div class=\"row\"><audio src=\"\" id=\"player\"></audio>";
							
							//image displayed to the left for this question type
							if(questions[i].ifilename){
								var imgBit = "<img src=\""+subDir+"/media/"+questions[i].ifilename+"\" alt=\""+questions[i].ialttext+"\" class=\"choiceImage\" />";
								if(questions[i].icredit)
									imgBit+="<p class=\"small alignRight\">"+questions[i].icredit+"</p>";
								if(numChoice>4)
									content+="<div class=\"col-lg-4\">"+imgBit+"</div><div class=\"col-lg-4\">";
								else
									content+="<div class=\"col-lg-6\">"+imgBit+"</div><div class=\"col-lg-6\">";
							}
							else{
								if(numChoice>4)
									content+="<div class=\"col-lg-6\">";
								else
									content+="<div class=\"col-lg-12\">";					
							}
							
							var col2 = false;
							
							var test = "";
							
							for(var j=0;j<numChoice;j++){
								var k = j+1;
								content+="<a href=\"#\" class=\"btn btn-primary btn-lg soundChoicePlay\" id=\""+questions[i].audiostem+"-"+k+"\"><i class=\"fas fa-play\"></i> Play</a> <a href=\"#\" class=\"btn btn-primary btn-lg answer soundChoice";
								
								if(questions[i].maxanswers>1)
									content+=" multiple multiple-"+questions[i].maxanswers;
								
								content+="\" id=\"answer-"+questions[i].answerOptions[j].aoid+"\">"+k+"</a><br />";
								
								test = (numChoice)/2;
								
								if(numChoice>4 && j>=test-1 && !col2){
									col2 = true;
									if(questions[i].ifilename)
										content+="</div><div class=\"col-lg-4\">";
									else
										content+="</div><div class=\"col-lg-6\">";
								}
							}	
							
							content+="</div></div>";
						}
						
						$("#exerciseSpace").html(content);
						$("#exerciseSpaceRow").slideDown("slow",function(){
						});
						
					});
				}
				
				//set the correct position on the progree bar
				function processProgress(n,tot){
					var progress = (n/tot)*100;
			
					if(progress>100)
						progress = 100;
					
					$("#progressDone").css("width",progress+"%");
					if(progress==100){
						$("#progressDone").css("border-top-right-radius","10px");
						$("#progressDone").css("border-bottom-right-radius","10px");
					}
				}
				
				//process a survey answer, including submitting response and displaying map
				function processAnswer(ids){
					//submit the answer
					$(".answer").addClass("disabled");
					
					for(var i=0;i<ids.length;i++){
						if(!$("#"+ids[i]).hasClass("answerSelected"))
							$("#"+ids[i]).addClass("answerSelected");
						submitAnswerForMap(ids[i]);
					}
					
					//remove map data here so we don't have a map loading with the previous data before the new load replaces it
					for(var i=1;i<ratingLayers.length;i++){
						if(ratingLayers[i] && map.hasLayer(ratingLayers[i])){		
							map.removeLayer(ratingLayers[i]);
						}
					}
					
					if (legend != undefined) {
						legend.remove(map);
						legend = undefined;
					}

					$("#mapSpace").slideDown("slow",function(){
						generateMap(qid,null,null,null);
						//if there is a next question add in the button to load it
						if(questions[currentQ]){
							$("#loadNext").html("<div class=\"col-md-12\"><a href=\"#\" class=\"btn btn-primary btn-lg btn-block\" id=\"loadNextButton\">Next <i class=\"fas fa-arrow-right\"></i></a></div>");
							currentQ++;
						}
						else{
							//load the end page here
							$("#loadNext").html("<div class=\"col-md-12\"><a href=\"#\" class=\"btn btn-primary btn-lg btn-block surveyComplete\" id=\"loadNextButton\">End survey <i class=\"fas fa-arrow-right\"></i></a></div>");
						}

						$("html, body").animate({ scrollTop: $("#mapSpace").offset().top }, "slow");						
					});
				}
				
				//save a selected survey answer
				function submitAnswerForMap(answer){
					answer = answer.split("-");
					answer = answer[1];
					
					if(localStorage[localStem+"lid"]!=9999){
						var gj = JSON.parse(localStorage[localStem+"geojson"]);
						
						//generate a marker for this answer
						var coords = generateMarker(gj);
						
						//console.log("coords = "+coords);
					}
					else{
						var coords = [0,0];
					}
					
					$.ajax({url: answerURL+"?id="+answer+"&pid="+localStorage[localStem+"pid"]+"&lat="+coords[0]+"&lng="+coords[1], success: function(response){
						//console.log(response);
					}});
				}
				
				//assign a random position for an answer marker within the user's area
				function generateMarker(sampleArea){	
					var test = L.geoJSON(sampleArea);
								
					var bounds = test.getBounds(); 
					
					var x_max = bounds.getEast();
					var x_min = bounds.getWest();
					var y_max = bounds.getSouth();
					var y_min = bounds.getNorth();
				
					var lat = y_min + (Math.random() * (y_max - y_min));		
					var lng = x_min + (Math.random() * (x_max - x_min));
					
					var point  = turf.point([lng, lat]);
					var inside = turf.booleanPointInPolygon(point, sampleArea);
					
					if(inside)
						return [lat,lng];
					else
						return generateMarker(sampleArea);
				}
			}
			else if(page=="explore-maps"){
				//maps intro page
				var content = "<div class=\"alignCenter\"><h2>Explore the maps</h2><p>";
				for(var i=0;i<numSurveys;i++){
					content+="<a href=\"./"+surveys[i].url+"/\" class=\"btn btn-primary beginExercise\" id=\"survey-"+surveys[i].surveyid+"\">"+surveys[i].surveyname+" <i class=\"fas fa-arrow-right\"></i></a><br />";
				}
				content+="</p></div>";
				$("#mainPageArea").html(content);
			}
			else if(section=="explore-maps"){
				//variables needed for the map
				var surveyID = 0;
				var surveyName = "";
				var surveyType = "";
				
				//check that the page corresponds to an available survey and if so proceed
				for(var i=0;i<numSurveys;i++){
					if(page==surveys[i].url){
						surveyID = surveys[i].surveyid;
						surveyName = surveys[i].surveyname;
						surveyType = surveys[i].surveytype;
						break;
					}
				}
				
				if(surveyID){
					//get the questions and display the map selection buttons
					$.getJSON(questionURL+"?id="+surveyID, function( data ) {
						questions = data;
						tot = questions.length;
						var content = "<h2>"+surveyName+"</h2><div class=\"row\" id=\"exerciseSpaceRow\"><div class=\"col-md-4\" id=\"exerciseSpace\"><div class=\"list-group\">";
						
						for(var i=0;i<tot;i++){
							content+="<a href=\""+subDir+"/explore-maps/"+page+"/"+questions[i].qid+"/\" class=\"list-group-item list-group-item-action exploreMapButton\" id=\"exploreMap_"+questions[i].qid+"\">"+questions[i].mapname+"</a>";
						}
						
						content+="</div></div><div class=\"col-md-8\" id=\"mapSpace\"></div></div>";
						
						$("#mainPageArea").html(content);
						
						if(selectedMap){
							$("#exploreMap_"+selectedMap).addClass("exploreMapSelected");
							generateMap(selectedMap,null,null,null);
							$("html, body").animate({ scrollTop: $("#mapSpace").offset().top }, "slow");					
						}
					});
					
					/*
					//load one of the 'explore' maps
					$(document).on("click",".exploreMapButton",function(){
						$(".exploreMapButton").removeClass("exploreMapSelected");
						$(this).addClass("exploreMapSelected");
						var id = $(this).attr("id");
						id = id.split("_");
						id = id[1];
						generateMap(id,null,null,null);
						$("html, body").animate({ scrollTop: $("#mapSpace").offset().top }, "slow");
	
						return false;
					});
					*/
				}				
			}
		});
		
		//play / pause audio clips
		$(document).on("click",".soundChoicePlay",function(){
			var audio = $('#player')[0];
			audio.pause();
			
			var id = $(this).attr("id");
			
			var cur = false;
			
			if($("#"+id+" > i").hasClass("fa-play"))
				 cur = true;
			 
			$(".soundChoicePlay").html("<i class=\"fas fa-play\"></i> Play");
			 
			if(cur){
				$("#player").attr("src",subDir+"/media/"+id+".mp3");
				audio.play();
				$(this).html("<i class=\"fas fa-stop\"></i> Stop");

			}
			else{
				audio.pause();
				$("#"+id).html("<i class=\"fas fa-play\"></i> Play");
			}
			
			$('#player').on('ended', function() {
				$(".soundChoicePlay").html("<i class=\"fas fa-play\"></i> Play");
			});
			
			return false;
		});
		
		//handle playing audio from within the map legend
		$(document).on("click",".soundChoiceMapPlay",function(){
			var audio = $('#player')[0];
			audio.pause();
			
			var id = $(this).attr("id");
			
			var cur = false;
			
			if($("#"+id+" > i").hasClass("fa-play"))
				 cur = true;
			 
			$(".soundChoiceMapPlay").html("<i class=\"fas fa-play\"></i>");
			 
			if(cur){
				$("#player").attr("src",subDir+"/media/"+id+".mp3");
				audio.play();
				$(this).html("<i class=\"fas fa-stop\"></i>");

			}
			else{
				audio.pause();
				$("#"+id).html("<i class=\"fas fa-play\"></i>");
			}
			
			$('#player').on('ended', function() {
				$(".soundChoiceMapPlay").html("<i class=\"fas fa-play\"></i>");
			});
			
			return false;
		});
		
		//generate the map on the survey or maps page
		function generateMap(id,age,education, gender){			
			if(!mapLoaded){
				var content = "<div class=\"col-md-12\" id=\"mapSpaceInner\">";
				
				if(!$("#player").length)
					content+="<audio src=\"\" id=\"player\"></audio>";
				
				content+="<div id=\"map\"\"><p id=\"regionLabel\"></p></div></div>";
				
				$("#mapSpace").html(content);
				
				var defaultLatLng = new L.LatLng(siteDefaultLat,siteDefaultLng);
				
				//set up the blank map
				map = L.map('map',{zoomSnap:0.25, zoomDelta: 0.25, wheelPxPerZoomLevel: 180, zoomControl:false, fullscreenControl: true, fullscreenControlOptions: {position: 'bottomright'}});
				map.setView(defaultLatLng, siteDefaultZoom);
				

				baseMap.addTo(map);

				//add the zoom control to the bottom right
				L.control.zoom({
					 position:'bottomright'
				}).addTo(map);	
				
				var areaData = [];
				
				//areas
				$.getJSON(regionsURL, function( data ) {
					
					//style the areas
					function whereStyle(feature) {
						return {
							weight: 0,
							opacity: 1,
							color: "#F5F9FA",
							dashArray: '',
							fillOpacity: 0.5,
							"className": "whereArea"
						};
					}
					
					var regionLayer = L.geoJSON(data, {
						style: whereStyle,
						onEachFeature: function(feature, layer){
							layer.on('mouseover', function(){
								$("#regionLabel").text(feature.properties[siteRegionLabel]);
								$("#regionLabel").show();
							});
							layer.on('mouseout',function(){
								$("#regionLabel").hide();
								$("#regionLabel").text("");
							});
						}
					}).addTo(map);
					regionLayer.bringToBack();
					
					$("#regionLabel").hide();

				});			
				
				mapLoaded = true;
			}
			else{
				//remove all existing map data
				
				//first remove content(if there is any)
				for(var i=1;i<ratingLayers.length;i++){
					if(ratingLayers[i] && map.hasLayer(ratingLayers[i])){		
						map.removeLayer(ratingLayers[i]);
					}
				}
				
				if (legend != undefined) {
					legend.remove(map);
					legend = undefined;
				}
			}
			
			
			//reset the map height
			var windowH = $( window ).height();
			
			var mapH = windowH - 130;

			//console.log(windowH+" and "+mapH);
			
			$("#map").height(mapH);	
			map.invalidateSize();
			
			var lexicalLabels = [];
			var audioIDs = [];
			

			var ratings = [];
			ratingLayers = [];
			
			var overlayMaps = {};
			
			//load in the data
			var mdu = mapDataURL+"?id="+id;
			
			//age, education and gender
			mdu+="&age="+age+"&education="+education+"&gender="+gender;
			
			//console.log(mdu);
			
			$.getJSON( mdu, function( data ) {					
				for(var i=0;i<data.answers.length;i++){
					if(data.surveytype=="morphology"){
						var cname = "rating-"+data.answers[i].anum;
						var clrs = ratingColors;
					}
					else{
						var cname = "lexical-"+data.answers[i].anum;
						var clrs = lexicalColors;
					}
					if(!ratings[data.answers[i].anum])
						ratings[data.answers[i].anum] = [];
					ratings[data.answers[i].anum][ratings[data.answers[i].anum].length] = L.circleMarker([data.answers[i].answerlat,data.answers[i].answerlng],{radius:radius,className: cname});
					
					lexicalLabels[data.answers[i].anum] = data.answers[i].atext;
					if(data.audiostem)
						audioIDs[data.answers[i].anum] = data.audiostem+"-"+data.answers[i].anum;
				}
				//console.log(ratings);
				for(var j=1;j<=ratings.length;j++){
					if(ratings[j] && ratings[j].length>0){
						ratingLayers[j] = L.layerGroup(ratings[j]);
						ratingLayers[j].addTo(map);
						if(data.audiostem.length){
							overlayMaps["<span style='color: #FFF; display: inline-block;'><img style='background-color:"+clrs[j]+"; width:15px;' src='"+subDir+"/graphics/circle.png' /></span> <a href=\"#\" class=\"soundChoiceMapPlay\" id=\""+audioIDs[j]+"\"><i class=\"fas fa-play\"></i></a> "+j] = ratingLayers[j]
						}
						else{
							overlayMaps["<span style='color: #FFF; display: inline-block;'><img style='background-color:"+clrs[j]+"; width:15px;' src='"+subDir+"/graphics/circle.png' /></span> "+lexicalLabels[j]] = ratingLayers[j];
						}
					}
				}						
				
				legend = L.control.layers("",overlayMaps, {
					collapsed: false,
					position:'topleft'
				}).addTo(map);
				
				//if this is the 'explore maps' section then we want to add in a map title
				if(section=="explore-maps"){
					var mapTitle = $(".exploreMapSelected").html();
					
					$(".leaflet-control-layers").prepend("<b>"+mapTitle+"</b>");
					
				}
				
				
				//age selector
				var ageContent = "<span class=\"noShow\" id=\"mapID\">"+id+"</span><b>Age:</b><br /><a href=\"#\" class=\"btn btn-primary ageSelect ageSelectLeft ";
				if(!age)
					ageContent+="btn-selected age-selected";
				ageContent+="\" id=\"age-0\">All</a><a href=\"#\" class=\"btn btn-primary ageSelect ";
				if(age==1)
					ageContent+="btn-selected age-selected";
				ageContent+="\" id=\"age-1\"><19</a><a href=\"#\" class=\"btn btn-primary ageSelect ";
				if(age==2)
					ageContent+="btn-selected age-selected";
				ageContent+="\" id=\"age-2\">19-35</a><a href=\"#\" class=\"btn btn-primary ageSelect ";
				if(age==3)
					ageContent+="btn-selected age-selected";
				ageContent+="\" id=\"age-3\">36-65</a><a href=\"#\" class=\"btn btn-primary ageSelect ageSelectRight ";
				if(age==4)
					ageContent+="btn-selected age-selected";
				ageContent+="\" id=\"age-4\">66+</a>";
				
				//education selector
				var educationContent = "<br /><b>Education:</b><br /><a href=\"#\" class=\"btn btn-primary educationSelect educationSelectLeft ";
				if(!education)
					educationContent+="btn-selected education-selected";
				educationContent+="\" id=\"education-0\">All</a><a href=\"#\" class=\"btn btn-primary educationSelect ";
				if(education==1)
					educationContent+="btn-selected education-selected";
				educationContent+="\" id=\"education-1\">Uni</a><a href=\"#\" class=\"btn btn-primary educationSelect ";
				if(education==2)
					educationContent+="btn-selected education-selected";
				educationContent+="\" id=\"education-2\">No Uni</a><a href=\"#\" class=\"btn btn-primary educationSelect educationSelectRight ";
				if(education==3)
					educationContent+="btn-selected education-selected";
				educationContent+="\" id=\"education-3\">At school</a>";
				
				//gender selector
				var genderContent = "<br /><b>Gender:</b><br /><a href=\"#\" class=\"btn btn-primary genderSelect genderSelectLeft ";
				if(!gender)
					genderContent+="btn-selected gender-selected";
				genderContent+="\" id=\"gender-0\">All</a><a href=\"#\" class=\"btn btn-primary genderSelect ";
				if(gender==1)
					genderContent+="btn-selected gender-selected";
				genderContent+="\" id=\"gender-1\">Female</a><a href=\"#\" class=\"btn btn-primary genderSelect ";
				if(gender==2)
					genderContent+="btn-selected gender-selected";
				genderContent+="\" id=\"gender-2\">Male</a><a href=\"#\" class=\"btn btn-primary genderSelect genderSelectRight ";
				if(gender==3)
					genderContent+="btn-selected gender-selected";
				genderContent+="\" id=\"gender-3\">Other</a>";
				
				var filterContent = "<a href=\"#\" id=\"sfyMapFilterToggle\">";
				
				if(!mapFiltersVisible)
					filterContent+="<i class=\"fas fa-caret-down\"></i> Show filters";
				else
					filterContent+="<i class=\"fas fa-caret-up\"></i> Hide filters";
				
				filterContent+="</a><div id=\"sfyMapFilters\">"+ageContent+educationContent+genderContent+"</div>";
				
				$(".leaflet-control-layers").append(filterContent);
				
				if(!mapFiltersVisible)
					$("#sfyMapFilters").hide();
				
				//resize control layers and make scrollable if it's now bigger then the map
				var controlH = $(".leaflet-control-layers").height();
				
				
				if(controlH > mapH){
					//map height is minimum 300
					if(mapH<300)
						controlH = 280;
					else
						controlH = mapH-20;
					$(".leaflet-control-layers").css({"height":controlH+"px","overflow-y":"auto"});
				}
				
			});
		}
		
		//handle showing and hiding map filters
		$(document).on("click","#sfyMapFilterToggle",function(){
			var windowH = $( window ).height();
			
			var mapH = windowH - 130;
			
			if(mapFiltersVisible){
				$("#sfyMapFilterToggle").html("<i class=\"fas fa-caret-down\"></i> Show filters");
				
				$("#sfyMapFilters").slideUp("slow",function(){
					
					//resize control layers and make scrollable if it's now bigger then the map
					var controlH = $(".leaflet-control-layers").height();
					
					
					if(controlH > mapH){
						//map height is minimum 300
						if(mapH<300)
							controlH = 280;
						else
							controlH = mapH-20;
						$(".leaflet-control-layers").css({"height":controlH+"px","overflow-y":"auto"});
					}
					
				});
				
				mapFiltersVisible = false;
			}
			else{
				$("#sfyMapFilterToggle").html("<i class=\"fas fa-caret-up\"></i> Hide filters");
				
				$("#sfyMapFilters").slideDown("slow",function(){
					
					//resize control layers and make scrollable if it's now bigger then the map
					var controlH = $(".leaflet-control-layers").height();
					
					
					if(controlH > mapH){
						//map height is minimum 300
						if(mapH<300)
							controlH = 280;
						else
							controlH = mapH-20;
						$(".leaflet-control-layers").css({"height":controlH+"px","overflow-y":"auto"});
					}
					
				});
				
				mapFiltersVisible = true;
			}

			
			return false;
		});
		
		//handle changing age types on maps
		$(document).on("click",".ageSelect",function(){
			var mapID = $("#mapID").text();
			var bits = $(this).attr("id");
			bits = bits.split("-");
			
			var age = parseInt(bits[1]);
			
			var education = $(".education-selected").attr("id");
			education = education.split("-");
			education = parseInt(education[1]);
			
			var gender = $(".gender-selected").attr("id");
			gender = gender.split("-");
			gender = parseInt(gender[1]);
			
			generateMap(mapID,age,education,gender);		
			
			return false;
		});
		
		//handle changing education types on maps
		$(document).on("click",".educationSelect",function(){
			var mapID = $("#mapID").text();
			var bits = $(this).attr("id");
			bits = bits.split("-");
			var education = parseInt(bits[1]);
			
			var age = $(".age-selected").attr("id");
			age = age.split("-");
			age = parseInt(age[1]);
			
			var gender = $(".gender-selected").attr("id");
			gender = gender.split("-");
			gender = parseInt(gender[1]);
			
			generateMap(mapID,age,education,gender);	
			
			return false;
		});
		
		//handle changing gender types on maps
		$(document).on("click",".genderSelect",function(){
			var mapID = $("#mapID").text();
			var bits = $(this).attr("id");
			bits = bits.split("-");
			var gender = parseInt(bits[1]);
			
			var age = $(".age-selected").attr("id");
			age = age.split("-");
			age = parseInt(age[1]);
			
			var education = $(".education-selected").attr("id");
			education = education.split("-");
			education = parseInt(education[1]);

			generateMap(mapID,age,education,gender);		
			
			return false;
		});
		
	}
	
});