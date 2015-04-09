<head>
<style>
caption
{
margin: auto;
font-family: Arial, Times, "Times New Roman", serif;
font-weight: bold;
font-size: 1.2em;
color: #EFF3FF;
margin-bottom: 20px;

}

table
{
margin: auto;
border: 4px outset #EFF3FF;
border-collapse: collapse;
width: 100%;
}

th
{
background-color: #EFF3FF;
color: black;
font-size: 1em;
font-family: Arial, "Arial Black", Times, "Times New Roman", serif;
border: 1px
}

td
{
border: 1px solid black;
font-family: "Trebuchet MS", "Times", serif;
font-size: 1em;
text-align: center;
padding: 5px;
}

.lienCalendrierJour {
/* La cellule du jour actuel dans le calendrier */
background-color: #CDF0F0;
text-decoration: underline;
}
</style>

<?php

if (!isset($_GET['m'] ) && !isset($_GET['y'] )) {
    // Si on ne récupère rien dans l'url, on prend la date du 1er jour du mois actuel.
    $timestamp = mktime(0, 0, 0, date('m'), 1, date('Y'));
} else {
    // Sinon on récupère la date du 1er jour du mois donné.
    $timestamp = mktime(0, 0, 0, $_GET['m'], 1, $_GET['y']);
}

?>
<?php

/* Si le mois et l'année de la variable $timestamp correspondent 
au mois et à l'année d'aujourd'hui, on retient le jour actuel.
Sinon le jour actuel ne se situe pas dans le mois et on ne retient rien */

if (date('m', $timestamp) == date('m') && date('Y', $timestamp) == date('Y')) {
    $coloreNum = date('d');
}

?>
<?php

$m = array("01" => "Janvier", "02" => "Février", "03" => "Mars", "04" => "Avril");
$m += array("05" => "Mai", "06" => "Juin", "07" => "Juillet", "08" => "Août");
$m += array("09" => "Septembre", "10" => "Octobre", "11" => "Novembre", "12" => "Décembre");

$j = array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi');
// Souvenez-vous que les dates en PHP commencent par dimanche !

?>
<?php

$numero_mois = date('m', $timestamp);
$annee = date('Y', $timestamp);

if ($numero_mois == 12) {
    // Dans le cas du mois de décembre
    $annee_avant = $annee;
    $annee_apres = $annee + 1; // L'année d'après change
    $mois_avant = $numero_mois - 1;
    $mois_apres = 01; // Le mois d'après est janvier
} elseif ($numero_mois == 01) {
    // Dans le cas du mois de janvier
    $annee_avant = $annee - 1; // L'année d'avant change
    $annee_apres = $annee;
    $mois_avant = 12; // Le mois d'avant est décembre
    $mois_apres = $numero_mois + 1;
} else {
    // Il ne s'agit ni de janvier ni de décembre
    $annee_avant = $annee;
    $annee_apres = $annee;
    $mois_avant = $numero_mois - 1;
    $mois_apres = $numero_mois + 1;
}

/*
// Lien pour aller au mois précédent
echo '<a href="?m='.$mois_avant.'&amp;y='.$annee_avant.'"><<</a>';


// Affichage du mois et de l'année
echo ' '.$m[$numero_mois].' '.$annee.' ';


// Lien pour aller au mois suivant
echo '<a href="?m='.$mois_apres.'&amp;y='.$annee_apres.'">>></a>';
*/
?>
<?php

$numero_jour1er = date('w', $timestamp); // 0 => Dimanche, 1 => Lundi, 2 = > Mardi...

// Changement du numéro du jour car l'array commence à l'indice 0.
if ($numero_jour1er == 0) {
    /*
    Si c'est dimanche, on le place en 6e position
    (car on commencera notre boucle à 0)
    */
    $numero_jour1er = 6;
} else {
    // Sinon on met lundi à 0 ou mardi à 1 ou mercredi à 2...
    $numero_jour1er--;
}

?>
<table>
<caption>
<?php
// Lien pour aller au mois précédent
echo '<a href="?m='.$mois_avant.'&amp;y='.$annee_avant.'"><<</a>';


// Affichage du mois et de l'année
echo '<span style="color: #000000;"> '.$m[$numero_mois].' '.$annee.'</span> ';


// Lien pour aller au mois suivant
echo '<a href="?m='.$mois_apres.'&amp;y='.$annee_apres.'">>></a>';
?>
</caption>

<tr>
<th>Lu</th>
<th>Ma</th>
<th>Me</th>
<th>Je</th>
<th>Ve</th>
<th>Sa</th>
<th>Di</th>
</tr>

<?php

echo '<tr>'; // Nouvelle ligne du tableau (celle de la 1ère semaine, donc)

// Écriture de colonnes vides tant que le mois ne démarre pas.
for ($i = 0; $i < $numero_jour1er; $i++) {
    echo '<td></td>';
}

for ($i = 1; $i <= 7 - $numero_jour1er; $i++) {
    echo '<td><div class="';

    if (isset($coloreNum) && $coloreNum == $i) {
        echo 'lienCalendrierJour';
    } else {
        echo 'lienCalendrier';
    }

    echo '">'.$i.'</div></td>';
}

echo '</tr>';

?>
<?php
$nbLignes = ceil((date('t', $timestamp) - ($i-1)) / 7);
for ($ligne = 0; $ligne < $nbLignes; $ligne++) {
    echo '<tr>'; // Nouvelle ligne du tableau (celle de la nouvelle semaine)

    for ($colone = 0; $colone < 7; $colone++) {
        if ($i <= date('t', $timestamp)) {
            echo '<td><div class="';

            if (isset($coloreNum) && $coloreNum == $i) {
                echo 'lienCalendrierJour';
            } else {
                echo 'lienCalendrier';
            }
            echo '">'.$i.'</div></td>';
        } else {
            // On a fini d'écrire le mois on termine la tableau par des cellules vides
            echo '<td></td>';
        }
        $i = $i +1;
    }

    echo '</tr>';
}
