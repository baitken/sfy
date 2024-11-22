<?php

/******************************
* Speak For Yersel Tool       *
* Home page                   *
* Brian Aitken                *
* February 2024               *
******************************/

require("incs/config.php");
require("incs/layout.php");

//decide which page to display and which menu item to highlight
if(isset($_GET["request"]))
	$request = rtrim($_GET["request"], '/');
else
	$request = null;

$pageID = 1;
$pageTitle = "";

if($request){
	$request = explode('/', $request);
	if($request[0]=="register"){
		$pageID = 0;
		$pageTitle = "Register";
	}
	else if($request[0]=="survey")
		$pageID = 2;
	else if($request[0]=="explore-maps")
		$pageID = 3;
	else if($request[0]=="about"){
		$pageID = 4;
		if(isset($request[1]) && $request[1]=="resources")
			$pageTitle = "More information and resources";
	}
	else if($request[0]=="privacy"){
		$pageID = 5;
		$pageTitle = "Privacy notice";
	}
	
}


formatTop($pageID,$pageTitle);

if($pageID==1){
	//Homepage content here
	?>
	<div class="container-fluid" id="mainPageArea"> 
	<div class="row">
	<div class="col-sm-7">
	<p>Would you ever say <i>it’s lush</i>? And what about <i>them trainers</i>? Do you say <i>daps</i> or <i>plimsols</i>? And do you pronounce <i>ear</i> more like <i>e-ah</i> or <i>yer</i>?</p>
<p>When you listen to other speakers, can you tell if someone is from Cardiff or Pontypridd? Or if they’re from north Wales or south Wales?</p> 
<p><i>Speak for Yersel</i> asks these questions in order to capture the different words, sounds and sentences used in English across Wales. But to do this, we need YOU!</p>
<p>In the <b>survey</b> you’ll be asked questions about your own voice, and what you think about the voices of others. Your answers will be recorded in real time on a map, providing a snapshot of English of voices from Wales from Anglesey to Barry and Aberystwyth to Wrexham, and everything in between.</p>
<p>A few notes before you start:</p>
<ol>
<li>Many of us speak differently, depending on who we’re talking to and where we are. In the following activities, we’re interested in finding out about <b>how you speak with friends and family</b>, rather than with teachers or at work.</li>
<li>The activities focus on the voices of those who live, or have lived, in Wales. You might not be from Wales, but we still think you’ll find <i>Speak for Yersel</i> fun to try out, so wherever you’re from, jump right in!</li><li>Ydych chi’n siarad Cymraeg? Rydyn ni’n gofyn am eich Saesneg yn yr arolwg hwn ond gobeithio y bydd modd inni gyhoeddi arolwg ar gyfer y Gymraeg yn y dyfodol.</li></ol>
	<p class="alignCenter"><a href="survey/" class="btn btn-primary btn-lg btn-exerciseSelect">Take survey <i class="fas fa-arrow-right"></i></a></p>
	</div>
	<div class="col-sm-5">
	<p><img src="media/wales-animated.gif" alt="Map of Wales with speech bubbles on it" id="homepageBubbles"></p>
	</div>
	</div>
	</div>
	<?php
}
else if($pageID==4){
	if(!isset($request[1])){
		//Main 'About' page here
		?>
		<div class="container-fluid" id="mainPageArea"> 
		<h2 class="alignCenter">About the Project</h2>
		<div class="row">
		<div class="col-sm-7">
<p><i>Speak for Yersel Wales</i> is a digital resource which maps the different varieties of Welsh English spoken across the country.   The website extends the original Arts and Humanities Research Council funded project on Scots (<a href="/">speakforyersel.ac.uk</a>) at the University of Glasgow.</p>
<h3>Project team</h3>
<p><b>Director:</b> Professor Jennifer Smith, University of Glasgow [<a href="https://www.gla.ac.uk/schools/critical/staff/jennifersmith/" target="_blank">website</a>]<br />
<b>Systems Developer:</b> Mr Brian Aitken, University of Glasgow,  [<a href="https://www.gla.ac.uk/schools/critical/staff/brianaitken/" target="_blank">website</a>]<br />
<b>Research Assistant:</b> Mr Marc Barnard, QMUL [<a href="https://www.qmul.ac.uk/sllf/linguistics/people/research-students/profiles/marc-elliot-barnard.html" target="_blank">website</a>]</p>

<p><b>Wales team:</b> Professor Mercedes Durham (Cardiff University) [<a href="https://profiles.cardiff.ac.uk/staff/durhamm" target="_blank">website</a>]; Dr Jonathan Morris (Cardiff University) [<a href="https://profiles.cardiff.ac.uk/staff/morrisj17" target="_blank">website</a>]</p>

<h3>How to cite</h3>
<p>Durham, Mercedes and Morris, Jonathan (2024) <i>Speak for Yersel  Wales</i>, Cardiff University <a href="https://speakforyersel.ac.uk/wales/">https://speakforyersel.ac.uk/wales/</a></p>

<h3>Contact</h3>
<p>If you have any questions about the research, please contact <a href="mailto:info@speakforyersel.ac.uk">info@speakforyersel.ac.uk</a>.</p>
<h3>Privacy</h3>
<p>Please read the Speak for Yersel <a href="../privacy">privacy notice</a> for more information.</p>
<h3>Thanks</h3>
<p>Thanks to our generous consultants: Lewis Greenaway, Angharad Naylor and Katharine Young. </p>
		</div>
		<div class="col-sm-5">
		<p><img src="../media/wales-animated.gif" alt="Map of Wales with speech bubbles on it" id="homepageBubbles"></p>
		</div>
		</div>
		</div>		
		<?php
	}
	else if($request[1]=="resources"){
		?>
		<div class="container-fluid" id="mainPageArea"> 
		<h2 class="alignCenter">More information and resources</h2>
		<div class="row">
		<div class="col-sm-9">
		<p>Resources page here</p>
		</div>
		<div class="col-sm-3">
		<p>Resources side-panel here</p>
		</div>
		</div>
		</div>
		<?php		
	}
}
else{
	//Default template structure
	?>
	<div class="container-fluid" id="mainPageArea"> 
	<h2 class="alignCenter"><?php echo($pageTitle);?></h2>
	<div class="row">
	<div class="col-sm-12">
 
	<h3>What will I be asked to do?</h3>
	<p><i>Speak for Yersel</i> investigates the ways in which language varies throughout <?php echo($siteOptions["surveyArea"]);?>. You'll be asked to answer questions about the words, sounds and sentences you use and your responses will be mapped in real time on the Speak for Yersel website.</p> 

	<p>You will also be asked some short demographic questions: age, gender, the main place you lived up to the age of 18, which languages you speak and whether you went to university or not.  None of this information can identify you as an individual, and neither can the answers you give. Your participation in completing the tasks is voluntary and you are free to stop the activities at any time.</p>
	 
	<h3>How will my responses be used?</h3>
	<p>Your responses will used by other users of Speak for Yersel to see the patterns of language use across <?php echo($siteOptions["surveyArea"]);?>. Researchers involved in the project, including colleagues at the University of Glasgow, Newcastle University and Cardiff University may also use the responses to understand more about how language is used in different areas. This study is run in compliance with the University of Glasgow’s <a href="https://www.gla.ac.uk/legal/privacy/">Privacy Notice</a> and will respect all your rights as described therein.</p>

	<p>If you have any questions about the research, please contact <a href="mailto:info@speakforyersel.ac.uk">info@speakforyersel.ac.uk</a></p>


	</div>
	</div>	
	</div>	
	<?php
}


	

formatBottom();

