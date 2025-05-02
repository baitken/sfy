# Speak For Yersel survey setup instructions

## 1\. Introduction

Speak For Yersel is a geospatial linguistic survey tool that enables internet users from a designated geographical area to answer multiple choice questions, with answers then being incorporated into interactive maps in real-time.  The tool provides a setup facility through which one or more morphology, lexis and phonology surveys featuring any number of questions can be created, plus facilities for exporting data from the tool once it is in use.  This document provides step-by-step instructions to creating the data structures the tool requires, the setup facility, customisation and exporting data. A PDF version of this file is also available in the repository.  

A complete set of example data for Wales, consisting of region and area GeoJSON files, settlement lists, survey questions, sound files and illustrative images can be found in the 'example' directory.  

## 2\. The data

The tool requires geospatial data about the geographical area of study and survey questions with associated multiple-choice answers.  Survey questions may also include images used to illustrate the questions and phonological survey questions may also include sound files.  Data should be prepared prior to beginning the installation of the tool.

A complete set of sample data for Wales is included in the ‘example’ directory.  Installing a test version of the tool using this sample data is a helpful way to understand both the tool and the data structures.  Instructions for installing the tool can be found in Section 3 below.

### 2.1 Geospatial data

Geospatial data requires three hierarchical levels of data:

1. Regions: These are the broadest geographical areas and they appear on the interactive maps, dividing the overall area of study into a limited number of broad areas, for example ‘Borders’ in Scotland.  Depending on the area of study there should ideally be no more than 10-20 regions and users will be able to highlight a region on the maps, which displays the region name and makes it easy to identify answer markers located within the region.    
   Region data must be stored as polygons within a single geojson file named ‘regions.json’, which must be placed in the tool’s ‘json’ directory.  Each polygon must have an ID and a label.  The names of these fields as found in the geojson file must be specified in the tool’s config.php file located in the ‘incs’ directory.  Update the value of the $siteOptions\["regionID"\] and $siteOptions\["regionLabel"\] variables in this file to match the relevant field names in the geojson file.  See below for more details about the config file.  
2. Areas:  These are more specific geographical areas within a region, such as postcode areas.  Areas must also be stored as polygons within a single geojson file which does not need to be given a specific filename.  The area geojson file will be imported into the system during the setup process as described below.  Each region may have any number of associated areas, for example ‘Borders’ in Scotland features 19 areas.  Areas are not displayed on any interactive maps but are used as boundaries when generating a random location for a survey answer.  
3. Settlements:  These are the actual named settlements located within an area and an area may have one or more associated settlements.  Settlements must be stored in a CSV file that is uploaded during the setup process as described below.  In addition to the settlement names the CSV also includes fields to connect the settlements to areas and areas to regions.  Settlements are employed when a user registers with the survey.  During this process the user must state where they lived up to the age of 18 and as they begin to type, the settlements that match the letters will appear in a drop-down list.  Once a settlement is selected and the registration process is completed the user is then associated with the settlement, its area and region and the system is then able to generate a random location within the user’s area each time a survey question is answered.  
   The CSV should be saved as UTF-8 text with the following columns.  Headings must appear in the first row:  
1. Area: the name of the area, which must exactly match the area name as it appears in the areas geojson file  
2. Region: the name of the region, which must exactly match the region name is it appears in regions.json  
3. Additional group 1: if areas have multiple groupings in addition to region one can be added here.  This column must appear, even if it is blank  
4. Additional group 2: as above  
5. Columns from E onwards may contain settlement names (one per column with as many columns as required).  Settlements should either appear on their own (e.g. ‘Aberfeldy’) or with a county name, separated from the settlement name with a comma (e.g. ‘Aberfeldy, Perth and Kinross’)

### 2.2 Survey data

The tool features three survey types: morphology, lexis and phonology.  Multiple surveys of each type can be set up and not all survey types need to be present.  Surveys are created and populated with questions and answer options during the setup process as described below.  Each survey’s data is imported during this process via CSVs, which should be saved as UTF-8 text featuring headings in the first row.  The structures of each survey type CSV and a description of the column contents appears below.  Note that where a column is optional it must still appear in the CSV, even if it contains no data.

#### *Morphology*

1. ID: a unique identifier for the question.  This must be unique across all surveys and should only consist of letters, numbers, dashes and underscores, for example ‘morphology-001’.  
2. Order: the order the question will appear in.  This must be an integer and mainly exists in the event that questions need to be reordered directly through the database at a later date.  
3. Variable: An optional technical description of the feature.  Is not publicly visible but may be of use to researchers when exporting the data  
4. Question: The question that will be presented to users in the survey.  May include HTML, such as \<b\>bold\</b\> or \<i\>italic\</i\> text  
5. Map name: The text that will appear as a button to identify the question in the list of maps  
6. Max answers: an integer defining how many answer options a user can select. ‘1’ should be the default.  
7. Picture filename: the filename (including extension) of a picture if the question should have an accompanying image.  Optional.  Image files should be placed in the ‘media’ directory of the tool.  Be aware that filenames and their file extensions are case sensitive.  
8. Picture alt text: Descriptive text that is associated with the image, for example ‘An ice cream cone’.  Only visible to screen readers or when the image fails to load. Optional  
9. Picture credit: A credit for the image, if required.  Will be displayed underneath the image and may include HTML (e.g. a link to the image owner’s website). Optional  
10. Xrefs: The IDs of one or more questions in the same survey or another survey, with multiple IDs separated by a bar character (|).  Not publicly visible but may be useful for researchers.  Optional  
11. Notes: Notes relating to the question for researchers to reference.  Not publicly visible.  Optional  
12. Columns L onwards should include the answer options with one per column.  Will appear as the text of the answer option buttons in the survey.

#### *Lexis*

1. ID: a unique identifier for the question.  This must be unique across all surveys and should only consist of letters, numbers, dashes and underscores, for example ‘lexis-001’.  
2. Order: the order the question will appear in.  This must be an integer and mainly exists in the event that questions need to be reordered directly through the database at a later date.  
3. Variable: An optional technical description of the feature.  Is not publicly visible but may be of use to researchers when exporting the data  
4. Question: The question that will be presented to users in the survey.  May include HTML, such as \<b\>bold\</b\> or \<i\>italic\</i\> text  
5. Map name: The text that will appear as a button to identify the question in the list of maps  
6. Max answers: an integer defining how many answer options a user can select. ‘1’ should be the default.  
7. Picture filename: the filename (including extension) of a picture if the question should have an accompanying image.  Optional.  Image files should be placed in the ‘media’ directory of the tool.  Be aware that filenames and their file extensions are case sensitive.  
8. Picture alt text: Descriptive text that is associated with the image, for example ‘An ice cream cone’.  Only visible to screen readers or when the image fails to load. Optional  
9. Picture credit: A credit for the image, if required.  Will be displayed underneath the image and may include HTML (e.g. a link to the image owner’s website). Optional  
10. Xrefs: The IDs of one or more questions in the same survey or another survey, with multiple IDs separated by a bar character (|).  Not publicly visible but may be useful for researchers.  Optional  
11. Notes: Notes relating to the question for researchers to reference.  Not publicly visible.  Optional  
12. Part of speech: The part of speech of the word associated with the question. Not publicly visible.  Optional  
13. Change: a description of the associated linguistic change.  Not publicly visible.  Optional  
14. Columns N onwards should include the answer options with one per column.  Will appear as the text of the answer option buttons in the survey.

#### *Phonology*

1. ID: a unique identifier for the question.  This must be unique across all surveys and should only consist of letters, numbers, dashes and underscores, for example ‘phonology-001’.  
2. Order: the order the question will appear in.  This must be an integer and mainly exists in the event that questions need to be reordered directly through the database at a later date.  
3. Variable: An optional technical description of the feature.  Is not publicly visible but may be of use to researchers when exporting the data  
4. Question: The question that will be presented to users in the survey.  May include HTML, such as \<b\>bold\</b\> or \<i\>italic\</i\> text  
5. Map name: The text that will appear as a button to identify the question in the list of maps  
6. Max answers: an integer defining how many answer options a user can select. ‘1’ should be the default.  
7. Picture filename: the filename (including extension) of a picture if the question should have an accompanying image.  Optional.  Image files should be placed in the ‘media’ directory of the tool.  Be aware that filenames and their file extensions are case sensitive.  
8. Picture alt text: Descriptive text that is associated with the image, for example ‘An ice cream cone’.  Only visible to screen readers or when the image fails to load. Optional  
9. Picture credit: A credit for the image, if required.  Will be displayed underneath the image and may include HTML (e.g. a link to the image owner’s website). Optional  
10. Xrefs: The IDs of one or more questions in the same survey or another survey, with multiple IDs separated by a bar character (|).  Not publicly visible but may be useful for researchers.  Optional  
11. Notes: Notes relating to the question for researchers to reference.  Not publicly visible.  Optional  
12. Audio filename stem: If the question has an associated set of audio files (one per answer option) these must have a standardised filename consisting of a single stem followed by a dash followed by the number of the answer option.  Enter the stem in this column.  For example, if the question relates to the pronunciation of ‘bat’ and there are four answer options / audio files then the stem may be ‘bat’.  The audio files (which must by MP3) should then be given the filenames ‘bat-1.mp3’, ‘bat-2.mp3’, bat-3.mp3’ and ‘bat-4.mp3’ and placed in the tool’s ‘media’ directory.  Be aware that filenames and their file extensions are case sensitive.  
13. Columns M onwards should include the answer options with one per column.  Will appear as the text of the answer option buttons in the survey unless an audio stem is given, in which case a ‘play’ icon will appear on each button and the text provided will not be publicly visible, but will appear in the exported data.

## 3\. Setting up the tool

### 3.1 Technical requirements

The tool requires a web server, the PHP scripting language and a relational database such as MySQL.  It uses the Bootstrap frontend toolkit, the jQuery JavaScript library, the Leaflet JavaScript mapping library and the Turf JavaScript geospatial analysis library.

### 3.2 Initial setup

The tool’s files and directories can be placed in the web-accessible root directory of a website on your server or within a subdirectory.  A database and associated user must also be created for the tool.  The user must have privileges to create and alter database tables for the database.  Database credentials (host, database name, database user and password) must be added to the db.php file located in the tool’s ‘incs’ directory.

### 3.3 Configuration options

Options for your instance of the tool should be added to the ‘config.php’ file located in the tool’s ‘incs’ directory prior to beginning setup.  These options can be updated at a later date, but updating some options (e.g. the database prefix) may cause the tool to stop working.  

The following options are included:

* $siteOptions\["siteTitle"\]: The name of the site, which will appear in the heading of every page  
* $siteOptions\["surveyArea"\]: The area the survey covers.  Appears in various places throughout the site, such as the registration page  
* $siteOptions\["launchDate"\]: Used on the ‘project staff’ page when limiting statistics and data exports.  Must use the format “yyyy-mm-dd”  
* $siteOptions\["siteLogo"\]: The filename (including extension) of the logo used in the site header (if applicable).  The corresponding image file should be added to the tool’s ‘graphics’ directory.  
* $siteOptions\["dbPrefix"\]: An optional prefix that will be added to all database tables the tool creates (e.g. ‘roi\_’).  Useful if multiple surveys are stored in one database.  Should only include alphanumeric characters, dashes and underscores  
* $siteOptions\["subDirectory"\]: If the site is being set up somewhere other than the web accessible root directory on the server enter the path here, without a final slash.  E.g. ‘roi’.  
* $siteOptions\["defaultLatLng"\]: The location all maps will centre on by default.  Latitude then longitude as floats, separated by a comma.  E.g. "54.6088, \-6.8852"  
* $siteOptions\["defaultZoom"\]: The default zoom (integer or float) at which all maps will load, for example ‘8’  
* $siteOptions\["gTag"\]: If your site uses Google Analytics supply the GA tag ID here  
* $siteOptions\["regionID"\]: The field containing each region’s unique identifier in your ‘regions.json’ file (see the ‘data’ section above)  
* $siteOptions\["regionLabel"\]: The field containing each region’s label in your ‘regions.json’ file (as above)  
* $siteOptions\["attributionText"\]: The text that will appear in the map’s ‘attribution’ popup.  Can contain HTML, but ensure backslashes are used before quotation marks, e.g. target=\\"\_blank\\"  
* $siteOptions\[“otherLanguageQuestion”\]: Set this to ‘true’ if the survey area features another language and you wish to record speakers’ bilingualism  
* $siteOptions\[“otherLanguage”\]: The name of the other language, for example ‘Welsh’.

### 3.4 Other configuration options

#### *Base map*

The original Speak For Yersel used a MapBox base map, which requires a user account and an access token.  By default the tool uses a free alternative.  You can replace this by opening the ‘sfy.js’ file located in the tool’s ‘js’ directory in a text editor and updating the ‘base map’ details that appear near the top of the file.

#### *Marker colours*

Marker colours use a gradient for morphology surveys and a selection of different colours for the other two survey types.  To update these open the ‘sfy.js’ file located in the tool’s ‘js’ directory in a text editor and find the ‘ratingColours’ and ‘lexicalColours’ arrays near the top of the file.  These can be replaced with any other colour hex codes.  Be sure to leave the first array element blank as this is not used.  

You will also need to update the corresponding colours in the tool’s CSS file, ‘sfy.css’ located in the tool’s ‘css’ directory.  These are named ‘.rating-‘ or ‘.lexical-‘ with a number corresponding to the array element in the JavaScript file.

#### *Site text*

The text for the site’s homepage, ‘about’ page and ‘resources’ page is located in the ‘index.php’ file in the root directory of the tool.  The relevant sections are clearly labelled and HTML text can be added to the appropriate sections as required.

### 3.5 Running the setup script

#### *Stage 1*

Once you have created the geospatial and survey data, have entered your database details into ‘incs/db.php’ and have created the required options in the ‘incs/config.php’ you are ready to bring everything together and create a survey instance.  

Open the file ‘setup/index.php’ in a text editor and delete the characters ‘/\*’ from the line above ‘require("../incs/db.php");’.  Save the file and ensure all of the tool’s files are uploaded to your web server.  Then open the setup script in a web browser: [https://your-domain/subdomain-if-specified/setup/](https://your-domain/subdomain-if-specified/setup/) 

You should be presented with a screen like the following:

![SFY Setup Stage 1](https://github.com/baitken/sfy/blob/main/example/screenshots/sfy-tool-01.png?raw=true)

If any of the listed options are incorrect edit the ‘incs/config.php’ file and then reload the page.  Once all is correct press the ‘continue’ button and the script will create all of the required database tables.

#### *Stage 2*

Once the database tables have been created a page like the one below will load:

![SFY Setup Stage 2](https://github.com/baitken/sfy/blob/main/example/screenshots/sfy-tool-02.png?raw=true)

You can now create all of the individual surveys that are required.  As discussed above, there are three survey types (morphology, lexis and phonology) and any number of individual surveys can be created, for example multiple phonology surveys, or omitting a morphology survey.  Using the form in Stage 2 you can create each survey that is required.  Select the ‘Survey type’ and supply a ‘Survey name’.  This will be displayed in the website and can be whatever text you prefer.  For example, the original SFY survey named the ‘phonology’ survey ‘Sounds about right’.  

You must also supply a page URL for the survey.  This must be unique and must only include alphanumeric characters, dashes and underscores (i.e. no spaces).  Introductory text must also be supplied.  This will appear on the survey’s introduction page.  HTML tags can be entered into this box.

Once you have entered all of the required information press the ‘Continue’ button and your survey will be saved.  The form will reload allowing you to create another survey.  Once you have created all required surveys press the ‘Continue to stage 3’ button.

#### *Stage 3*

In stage 3 you will upload the survey questions and answer options for each of your created surveys.  Your surveys will be listed on a page like the following:

![SFY Setup Stage 3](https://github.com/baitken/sfy/blob/main/example/screenshots/sfy-tool-03.png?raw=true)

You should have a CSV file containing your questions and answer options for each survey, as discussed above.  Use the ‘Browse…’ option beside each survey to find and attach the relevant CSV file.  Once you’ve attached them all press the ‘Upload Questions’ button and a summary page should load such as the following:

![SFY Setup Stage 3 with questions added](https://github.com/baitken/sfy/blob/main/example/screenshots/sfy-tool-04.png?raw=true)

#### *Stage 4*

Upon pressing the ‘Continue’ button the script will then process the ‘regions.json’ file (as discussed above) to extract and list the regions.  A page such as the following will load:

![SFY Setup Stage 4](https://github.com/baitken/sfy/blob/main/example/screenshots/sfy-tool-05.png?raw=true)

You can now import your area geoJSON file (as discussed above).  To do so use the ‘Browse…’ option to attach your geoJSON file then enter the names of the fields that contain each area’s ID and name in the geoJSON data.  Note that these field names are case sensitive.  For example, in our Northern Ireland areas geoJSON file the area ID field is named ‘OBJECTID’ and the area name field is ‘Name’ so these would be added to the corresponding boxes in the form.

Press the ‘Continue’ button and the areas will be extracted from your geoJSON file.

#### *Stage 5*

This stage will list all of the areas that have been created, and will also display any errors encountered when parsing the geoJSON.  A page such as the following will be displayed:

![SFY Setup Stage 5](https://github.com/baitken/sfy/blob/main/example/screenshots/sfy-tool-06.png?raw=true)

With the areas created you can now upload the settlements by attaching your settlements CSV file (see above) to the form and pressing ‘Continue’.  This will then process your settlements and associate them with areas and regions.  A log of all updates will be displayed, including any errors encountered.  A small section of such a page can be viewed below:

![SFY Setup Stage 5 settlements](https://github.com/baitken/sfy/blob/main/example/screenshots/sfy-tool-07.png?raw=true)

#### *Post-setup*

The setup process is now complete and you should test the site (registering as a user and completing the surveys) to ensure that all processes completed successfully.  If they didn’t you should delete all database tables, make any required changes to your data and begin the setup process again.

Once you are satisfied with the survey you should either delete the ‘setup’ directory from your web server or ensure the script is not executable.  To achieve the latter simply reinstate the ‘/\*’ characters in the line above ‘require("../incs/db.php");’ and save the file on your web server.

## 4\. Viewing stats and exporting data

Statistics about completed survey questions together with facilities to export survey data can be accessed by loading the page ‘project-staff’ in your web browser.  A page similar to the following will load:

![SFY Stats](https://github.com/baitken/sfy/blob/main/example/screenshots/sfy-tool-08.png?raw=true)

Be aware that by default this page is not password protected and you should create your own password protection for it if required, for example setting up an Apache .htpasswd file.