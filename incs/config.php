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

$siteOptions["siteTitle"] = "";  //the name of the site.  This will appear as the heading on all pages

$siteOptions["surveyArea"] = "";  //the area the survey covers.  This will appear in the site text (e.g. on the registration page)

$siteOptions["launchDate"] = "";  //the date the site launches.  Only used as the earliest date on the 'project-staff' page to generate stats

$siteOptions["siteLogo"] = "";  //the filename of the logo for the site, which should be saved in the 'graphics' directory.  If supplied will appear in the heading on all pages

$siteOptions["dbPrefix"] = "";  //a prefix given to all database tables.  Optional.  E.g. setting $siteOptions["dbPrefix"] to "roi_" will result in table names such as "roi_activity"

$siteOptions["subDirectory"] = "";  //if the site is not running in the root directory of the website enter the path to the site here.  e.g. if the site is running at https://speakforyersel.glasgow.ac.uk/roi/ then enter 'roi'

$siteOptions["defaultLatLng"] = "";  //the midpoint of all maps that are loaded.  Lat and then Lng.
$siteOptions["defaultZoom"] = "";  //the default zoom level that all maps will be loaded at

$siteOptions["gTag"] = "";  //if you're using Google Analytics enter the Tag ID here

$siteOptions["regionID"] = "";  //the name of the property in the regions.json file that should be used as the unique identifier for the region.  Must be an integer

$siteOptions["regionLabel"] = "";  //the name of the property in the regions.json file that should be used when displaying region name in the site maps

$siteOptions["attributionText"] = "";  //the contents of the map attribution popup

$siteOptions["otherLanguageQuestion"] = false;  //whether the user registration form should include a question about bilingualism

$siteOptions["otherLanguage"] = "";  //the other language that will be asked about (leave blank if no other language)
