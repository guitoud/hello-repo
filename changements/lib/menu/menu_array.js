/* 
 Milonic DHTML Website Navigation Menu - Version 3.x
 Written by Andy Woolley - Copyright 2002 (c) Milonic Solutions Limited. All Rights Reserved.
 Please visit http://www.milonic.co.uk/menu or e-mail menu3@milonic.com for more information.
 
 The Free use of this menu is only available to Non-Profit, Educational & Personal web sites.
 Commercial and Corporate licenses  are available for use on all other web sites & Intranets.
 All Copyright notices MUST remain in place at ALL times and, please keep us informed of your 
 intentions to use the menu and send us your URL.
*/

// Menu Modifié et adapté Par Sentenza, ne revendique aucun droits sur les modification, et merci a Milonic pour le script original.


//The following line is critical for menu operation, and MUST APPEAR ONLY ONCE. If you have more than one menu_array.js file rem out this line in subsequent files
menunum=0;menus=new Array();_d=document;function addmenu(){menunum++;menus[menunum]=menu;}function dumpmenus(){mt="<scr"+"ipt language=javascript>";for(a=1;a<menus.length;a++){mt+=" menu"+a+"=menus["+a+"];"}mt+="<\/scr"+"ipt>";_d.write(mt)}
//Please leave the above line intact. The above also needs to be enabled if it not already enabled unless this file is part of a multi pack.



////////////////////////////////////
// Editable properties START here //
////////////////////////////////////

// Special effect string for IE5.5 or above please visit http://www.milonic.co.uk/menu/filters_sample.php for more filters
effect = "Fade(duration=0.2);Alpha(style=0,opacity=88);Shadow(color='#777777', Direction=135, Strength=5)"


timegap=500			// The time delay for menus to remain visible
followspeed=5		// Follow Scrolling speed
followrate=40		// Follow Scrolling Rate
suboffset_top=4;	// Sub menu offset Top position 
suboffset_left=6;	// Sub menu offset Left position
closeOnClick = true

style1=[			// style1 is an array of properties. You can have as many property arrays as you need. This means that menus can have their own style.
"FFFFFF",				// Mouse Off Font Color
"006498",			// Mouse Off Background Color
"FF9933",			// Mouse On Font Color
"",			// Mouse On Background Color
"ffffff",			// Menu Border Color 
12,					// Font Size in pixels
"normal",			// Font Style (italic or normal)
"bold",				// Font Weight (bold or normal)
"Verdana, Arial",	// Font Name
4,					// Menu Item Padding
"",		// Sub Menu Image (Leave this blank if not needed)
,					// 3D Border & Separator bar
"ffffff",			// 3D High Color
"000000",			// 3D Low Color
"",			// Current Page Item Font Color (leave this blank to disable)
"",				// Current Page Item Background Color (leave this blank to disable)
"",		// Top Bar image (Leave this blank to disable)
"",			// Menu Header Font Color (Leave blank if headers are not needed)
"",			// Menu Header Background Color (Leave blank if headers are not needed)
"",				// Menu Item Separator Color
]


addmenu(menu=[		// This is the array that contains your menu properties and details
"mainmenu",			// Menu Name - This is needed in order for the menu to be called
10,					// Menu Top - The Top position of the menu in pixels
,				// Menu Left - The Left position of the menu in pixels
110,					// Menu Width - Menus width in pixels
0,					// Menu Border Width 
"center",					// Screen Position - here you can use "center;left;right;middle;top;bottom" or a combination of "center:middle"
style1,				// Properties Array - this is set higher up, as above
1,					// Always Visible - allows the menu item to be visible at all time (1=on/0=off)
"left",				// Alignment - sets the menu elements text alignment, values valid here are: left, right or center
,					// Filter - Text variable for setting transitional effects on menu activation - see above for more info
0,					// Follow Scrolling - Tells the menu item to follow the user down the screen (visible at all times) (1=on/0=off)
1, 					// Horizontal Menu - Tells the menu to become horizontal instead of top to bottom style (1=on/0=off)
,					// Keep Alive - Keeps the menu visible until the user moves over another menu or clicks elsewhere on the page (1=on/0=off)
,					// Position of TOP sub image left:center:right
,					// Set the Overall Width of Horizontal Menu to 100% and height to the specified amount (Leave blank to disable)
,					// Right To Left - Used in Hebrew for example. (1=on/0=off)
,					// Open the Menus OnClick - leave blank for OnMouseover (1=on/0=off)
,					// ID of the div you want to hide on MouseOver (useful for hiding form elements)
"img/bouton.jpg",					// Background image for menu when BGColor set to transparent.
,					// Scrollable Menu
,					// Reserved for future use
,".:Daemon:.","./index.php?cible=1",,"Daemon",0
,".:Rapport:.","./index.php?cible=26",,"Rapport",0
,".:NBU:.","./index.php?cible=21",,"Erreurs NBU",0
,".:Reboot:.","./index.php?cible=23",,"Gestion reboot",0
,".:MDP:.","./index.php?cible=3",,"Pass Root Expired",0
,".:Omnivision:.","./index.php?cible=24",,"Omnivision",0
,".:SRV Down:.","./index.php?cible=25",,"SRV Down",0
,".:Nagios:.","./index.php?cible=4",,"",0
,".:Volumetrie:.","./index.php?cible=22",,"Volumetrie",0

])
//,"&nbsp;.:test:.","show-menu=download","./index.php?cible=20","test",0

	addmenu(menu=["download",,,100,1,"",style1,0,"left",effect,0,,,,,,,,,,,
	,"Truc","./index.php?cible=20",,,1
	,"toto","./index.php?cible=20",,,1
	])

dumpmenus()
