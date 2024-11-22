<?php

/******************************
* Speak For Yersel            *
* Config file                 *
* Brian Aitken                *
* University of Glasgow       *
* February 2024               *
******************************/

//Please set these options before running the setup script
//You may also change these options after setup if required

$siteOptions = [];

$siteOptions["siteTitle"] = "Speak For Yersel: Wales";  //the name of the site.  This will appear as the heading on all pages

$siteOptions["surveyArea"] = "Wales";  //the area the survey covers.  This will appear in the site text (e.g. on the registration page)

$siteOptions["launchDate"] = "2024-04-01";  //the date the site launches.  Only used as the earliest date on the 'project-staff' page to generate stats

$siteOptions["siteLogo"] = "SFY_wales.png";  //the filename of the logo for the site, which should be saved in the 'graphics' directory.  If supplied will appear in the heading on all pages

$siteOptions["dbPrefix"] = "wales_";  //a prefix given to all database tables.  Optional.  E.g. setting $siteOptions["dbPrefix"] to "roi_" will result in table names such as "roi_activity"

$siteOptions["subDirectory"] = "wales";  //if the site is not running in the root directory of the website enter the path to the site here.  e.g. if the site is running at https://speakforyersel.glasgow.ac.uk/roi/ then enter 'roi'

$siteOptions["defaultLatLng"] = "52.2959, -3.9195";  //the midpoint of all maps that are loaded.  Lat and then Lng.
$siteOptions["defaultZoom"] = "8";  //the default zoom level that all maps will be loaded at

$siteOptions["gTag"] = "";  //if you're using Google Analytics enter the Tag ID here

$siteOptions["regionID"] = "OBJECTID";  //the name of the property in the regions.json file that should be used as the unique identifier for the region.  Must be an integer

$siteOptions["regionLabel"] = "Name";  //the name of the property in the regions.json file that should be used when displaying region name in the site maps

$siteOptions["attributionText"] = "<h3>Linguistic data</h3><p>The linguistic data featured in this resource are copyright of the University of Glasgow.</p><h3>Base map data</h3><p>© <a href=\"https://openstreetmap.org\" target=\"_blank\">OpenStreetMap</a> contributors, <a href=\"https://creativecommons.org/licenses/by-sa/2.0/\" target=\"_blank\">CC-BY-SA</a>, Imagery © <a href=\"https://mapbox.com\" target=\"_blank\">Mapbox</a></p>";  //the contents of the map attribution popup

$siteOptions["otherLanguageQuestion"] = true;  //whether the user registration form should include a question about bilingualism

$siteOptions["otherLanguage"] = "Welsh";  //the other language that will be asked about (leave blank if no other language)
