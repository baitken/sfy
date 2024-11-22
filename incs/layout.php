<?php
function formatTop($section,$title){
	global $siteOptions;
	
	$base = "/";
	
	if($siteOptions["subDirectory"])
		$base.=$siteOptions["subDirectory"]."/";
	
	//navigation
	$nav[1][0] = $base;
	$nav[1][1] = "Home";
	$nav[2][0] = $base."survey/";
	$nav[2][1] = "Survey";
	$nav[3][0] = $base."explore-maps/";
	$nav[3][1] = "Maps";
	$nav[4][0] = $base."about/";
	$nav[4][1] = "About";
	
	$num = count($nav);
	
	//page title (if not supplied then default to nav title)
	if($title)
		$pageTitle = $title;
	else
		$pageTitle = $nav[$section][1];
	
	?>
	
<!doctype html>
<html lang="en">
  <head>
<?php
if($siteOptions["gTag"]){
?>	
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo($siteOptions["gTag"]);?>"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', '<?php echo($siteOptions["gTag"]);?>');
</script>
<?php
}
?>

<link rel="icon" href="<?php echo($base."graphics/".$siteOptions["siteLogo"]);?>" sizes="32x32" />
<link rel="icon" href="<?php echo($base."graphics/".$siteOptions["siteLogo"]);?>" sizes="192x192" />
<link rel="apple-touch-icon" href="<?php echo($base."graphics/".$siteOptions["siteLogo"]);?>" />
<meta name="msapplication-TileImage" content="<?php echo($base."graphics/".$siteOptions["siteLogo"]);?>" />

  <title><?php echo($siteOptions["siteTitle"]);?> :: <?php echo($pageTitle);?></title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />	
	<link rel="stylesheet" media="all" href="<?php echo($base);?>plugins/css/Control.FullScreen.css" />	
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400;500;600&family=Noto+Serif:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet"> 
	<link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css" />
	<link rel="stylesheet" href="<?php echo($base);?>css/sfy.css?20240306">
	
	<!-- JavaScript -->
	<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
	<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
	<script src="<?php echo($base);?>plugins/js/Control.Coordinates.js"></script>
	<script src="<?php echo($base);?>plugins/js/Control.FullScreen.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/@turf/turf@7/turf.min.js"></script>
	<script src="https://code.jquery.com/ui/1.14.1/jquery-ui.min.js" integrity="sha256-AlTido85uXPlSyyaZNsjJXeCs07eSv3r43kyCVc8ChI=" crossorigin="anonymous"></script>

	<script src="<?php echo($base);?>js/sfy.js?20240515"></script>

  </head>
  <body>
<div class="container-fluid" id="regionSwitcher">
<ul class="nav nav-tabs">
  <li class="nav-item">
    <a class="nav-link" href="/">Scotland</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="/ni">Northern Ireland</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="/roi">Republic of Ireland</a>
  </li>
  <li class="nav-item">
    <a class="nav-link active" aria-current="page" href="/wales">Wales</a>
  </li>
</ul>
</div>
<div class="container-fluid" id="mainPage">  
<div class="row" id="headerBar">
<div class="col-sm-6"><a href="<?php echo($base);?>" id="headerText">
<?php if($siteOptions["siteLogo"]){
	?>
	<img src="<?php echo($base);?>graphics/<?php echo($siteOptions["siteLogo"]);?>" alt="<?php echo($siteOptions["siteTitle"]);?>" />
	<?php
}
	echo($siteOptions["siteTitle"]);?></a></div>
<div class="col-sm-6 alignRight" id="navBar">
<?php

for($i=1;$i<=$num;$i++){
	echo("<a href=\"".$nav[$i][0]."\" class=\"siteNav");
	if($i==$section)
		echo(" siteNavActive");
	echo("\">".$nav[$i][1]."</a>");
}
?>
</div>
</div>
	<?php
}

function formatBottom($section=0){
	global $siteOptions;
	
	$base = "/";
	
	if($siteOptions["subDirectory"])
		$base.=$siteOptions["subDirectory"]."/";
	
	?>
<div class="modal fade" id="attributionBox" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-dialog modal-dialog-centered" role="document"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Map Attribution and Copyright</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body"><?php echo($siteOptions["attributionText"]);?></div></div></div></div>
<div class="row" id="footer">
<div class="col-md-12 alignCenter">
<p class="alignRight small"><a href="<?php echo($base);?>privacy">Privacy notice</a></p>
<p><a href="https://www.gla.ac.uk/" target="_blank"><img src="<?php echo($base);?>graphics/UoG_logo.jpg" alt="University of Glasgow" /></a><a href="https://www.cardiff.ac.uk/" target="_blank"><img src="<?php echo($base);?>graphics/UoC_logo.jpg" alt="Cardiff University" /></a></p>
</div>
<div id="siteSubDirectory" class="noShow"><?php echo($siteOptions["subDirectory"]);?></div>
<div id="siteSurveyArea" class="noShow"><?php echo($siteOptions["surveyArea"]);?></div>
<div id="siteTitle" class="noShow"><?php echo($siteOptions["siteTitle"]);?></div>
<div id="siteDefaultLatLng" class="noShow"><?php echo($siteOptions["defaultLatLng"]);?></div>
<div id="siteDefaultZoom" class="noShow"><?php echo($siteOptions["defaultZoom"]);?></div>
<div id="siteRegionLabel" class="noShow"><?php echo($siteOptions["regionLabel"]);?></div>
<div id="siteOtherLanguageQuestion" class="noShow"><?php echo($siteOptions["otherLanguageQuestion"]);?></div>
<div id="siteOtherLanguage" class="noShow"><?php echo($siteOptions["otherLanguage"]);?></div>
</div>
</div>
  </body>
</html>
<?php
}

?>