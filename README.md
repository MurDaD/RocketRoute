# RocketRoute
for RocketRoute

## Demo
http://li1430-197.members.linode.com/RocketRoute/index.php

## Instructions
You most likely will need to look up definitions for NOTAM and ICAO code in Wikipedia.
Working for RocketRoute you will often be confronted with new aviation specific abbreviation.

For this task you need to:
1) register at www.rocketroute.com for a user account with username: 
max_shakh@yahoo.com (PHP)

2) read our API documentation at www.rocketroute.com/develop ers
Note that we provide a sandbox where you can test the API input/output
3) You will also need an MD5 Key which for you is: 
Iw8DfRlZfPqHbW3bocNJ

The task is:
-To develop a web page displaying google maps
-Add a button
-Add a user input field for 4 letter ICAO code
-Upon click of that button make API call to our NOTAM API and do a search for the NOTAm for the 4 letter ICAO code that user entered
For example hgere are some ICAO codes:
EGLL
EGGW
EGLF
EGHI
EGKA
EGMD
EGMC
KLAX 
SBSP

The Geo Location for the NOTAM are held in field ItemQ

Then place an icon on that location
(Use similar icon to this icon: http://www.clker.com/cli parts/H/Z/0/R/f/S/warning-icon -th.png)

When click on such an icon show a  text box onto the position showing the NOTAM string received via our API (ItemE)
You can google the description of NOTAM

Please host the resulting web page on any server you like. 
Attach the source code to your reply or provide a link to the repository.

The task will test your ability to:
-implement an API call using our documentation and
-the ability to implement onto the google MAPS API and
-place an icon(s) on a google map geo location with that result using the google API
- write acceptance, functional and unit tests;
- write reusable code;
- comply SOLID principles
