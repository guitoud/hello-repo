<html>
<head>
</title>Calendrier</title>
<style type="text/css">
#
.gestion-calendar {
width: 203px;
border: 1px solid #000000;
background-color: #FFFFFF;
}
#
.gestion-calendar-month {
border-bottom: 1px solid #000000;
background-color: #F0E68C;
font-family: Helvetica;
font-size: 11pt;
font-weight: bold;
text-align: center;
vertical-align: middle;
color: #000000;
}
#
.gestion-calendar-day {
width: 25px;
height: 25px;
background-color: #F6F6F6;
font-family: Helvetica;
font-size: 10pt;
font-weight: normal;
text-align: center;
vertical-align: middle;
margin: 0px;
padding: 0px;
}
#
.gestion-calendar-today {
width: 25px;
height: 25px;
background-color: #F0E68C;
font-family: Helvetica;
font-size: 10pt;
font-weight: normal;
text-align: center;
vertical-align: middle;
margin: 0px;
padding: 0px;
}
#
.gestion-calendar-dayname {
width: 25px;
height: 25px;
background-color: #F6F6F6;
font-family: Helvetica;
font-size: 10pt;
font-weight: bold;
text-align: center;
vertical-align: middle;
margin: 0px;
padding: 0px;
}
#
.gestion-calendar-date {
text-align: center;
vertical-align: middle;
padding: 3px;
}
#
</style>
</head>
<body>
<?php
$bixestile = (date("L")) ? 1 : 0;
$nb_jours = date("t");
$mois = (int) date("n");
$nb_jours = ($mois == 2) ? $nb_jours + $bixestile : $nb_jours;
$compteur = 1;
$debut = 1;
$depart = date("w", mktime(0, 0, 0, $mois, 1, date("Y")));
if ($depart == 0) { $depart = 7; }
for ($l=1; $l<7; $l++) {
for ($c=1; $c<8; $c++) {
if ($debut >= $depart) {
if ($compteur <= $nb_jours) {
if ($compteur == (int) date("d")) {
$Day[$l][$c] = '<td class="gestion-calendar-today">'.$compteur.'</td>';
} else {
$Day[$l][$c] = '<td class="gestion-calendar-day">'.$compteur.'</td>';
}
} else {
$Day[$l][$c] = '<td class="gestion-calendar-day">&nbsp;</td>';
}
$compteur++;
} else {
$Day[$l][$c] = '<td class="gestion-calendar-day">&nbsp;</td>';
}
$debut++;
}
}
$jsem = date("w", time());
$jmois = date("j", time());
$mois = date("n", time());
$annee = date("Y", time());
$tabjour=array("Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi");
$tabmois=array("0", "janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août", "septembre", "octobre", "novembre", "décembre");
$aujourdhui = $tabjour[$jsem]." $jmois ".$tabmois[$mois]." $annee";
?>
<table border="0" cellpadding="0" cellspacing="2" align="center" class="gestion-calendar">
<tr>
<td class="gestion-calendar-month" colspan="7"><?php echo $tabmois[$mois]." ".date("Y"); ?></td>
</tr>
<tr>
<td class="gestion-calendar-dayname">Lu</td>
<td class="gestion-calendar-dayname">Ma</td>
<td class="gestion-calendar-dayname">Me</td>
<td class="gestion-calendar-dayname">Je</td>
<td class="gestion-calendar-dayname">Ve</td>
<td class="gestion-calendar-dayname">Sa</td>
<td class="gestion-calendar-dayname">Di</td>
</tr>
<tr>
<?php echo @$Day[1][1]; ?>
<?php echo @$Day[1][2]; ?>
<?php echo @$Day[1][3]; ?>
<?php echo @$Day[1][4]; ?>
<?php echo @$Day[1][5]; ?>
<?php echo @$Day[1][6]; ?>
<?php echo @$Day[1][7]; ?>
</tr>
<tr>
<?php echo @$Day[2][1]; ?>
<?php echo @$Day[2][2]; ?>
<?php echo @$Day[2][3]; ?>
<?php echo @$Day[2][4]; ?>
<?php echo @$Day[2][5]; ?>
<?php echo @$Day[2][6]; ?>
<?php echo @$Day[2][7]; ?>
</tr>
<tr>
<?php echo @$Day[3][1]; ?>
<?php echo @$Day[3][2]; ?>
<?php echo @$Day[3][3]; ?>
<?php echo @$Day[3][4]; ?>
<?php echo @$Day[3][5]; ?>
<?php echo @$Day[3][6]; ?>
<?php echo @$Day[3][7]; ?>
</tr>
<tr>
<?php echo @$Day[4][1]; ?>
<?php echo @$Day[4][2]; ?>
<?php echo @$Day[4][3]; ?>
<?php echo @$Day[4][4]; ?>
<?php echo @$Day[4][5]; ?>
<?php echo @$Day[4][6]; ?>
<?php echo @$Day[4][7]; ?>
</tr>
<tr>
<?php echo @$Day[5][1]; ?>
<?php echo @$Day[5][2]; ?>
<?php echo @$Day[5][3]; ?>
<?php echo @$Day[5][4]; ?>
<?php echo @$Day[5][5]; ?>
<?php echo @$Day[5][6]; ?>
<?php echo @$Day[5][7]; ?>
</tr>
<tr>
<?php echo @$Day[6][1]; ?>
<?php echo @$Day[6][2]; ?>
<?php echo @$Day[6][3]; ?>
<?php echo @$Day[6][4]; ?>
<?php echo @$Day[6][5]; ?>
<?php echo @$Day[6][6]; ?>
<?php echo @$Day[6][7]; ?>
</tr>
<tr>
<td class="gestion-calendar-date" colspan="7">Aujourd'hui nous sommes le <?php echo $aujourdhui; ?>. Il est <?php echo date("H")."h".date("i"); ?>.</td>
</tr>
</table>
</body> 