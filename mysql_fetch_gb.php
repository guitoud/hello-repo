<?php

//redirection si acces dirrect
if (substr_count($_SERVER['PHP_SELF'], 'index.php')==0) {
    header("Location: ../");
    exit();
}
$j=0;
$ENV =substr($_SERVER["SCRIPT_FILENAME"], 1, 1);
require_once("./cf/fonctions.php");
require_once("./cf/autre_fonctions.php");
require("./cf/conf_outil_icdc.php");

$id = "0";

$sql_dossier = "select * from dossier";

$req=mysql_query($sql_dossier) or die ('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
while ($reponseDossier=mysql_fetch_array($req)) {
    $arrayDossier[] = $reponseDossier['name'];
}

foreach ($arrayDossier as $dossier) {
    ++$id;
    $staticB = "";
    $sql_url = "
    select 
    exploit.id, 
    exploit.name as name, 
    exploit.url as url, 
    exploit.ext 
    from exploit,     dossier,     ressource 
    where 
    exploit.id = ressource.id_exploit 
    and 
    dossier.id = ressource.id_dossier 
    and 
    dossier.name = \"$dossier\" 
    order by 2 ";
    
    $req2=mysql_query($sql_url) or die ('Erreur SQL !<br />'.$sql_url.'<br />'.mysql_error());
    while ($reponseArray=mysql_fetch_array($req2)) {
        $kidA = array(
            'url' => $reponseArray['url'],
            'name' => $reponseArray['name'],
            'id' => $id . "." . $reponseArray['id'],
            'ext' => $reponseArray['ext'],
            'position' => "centre",
            'type' => "url" );
            $staticB[] = $kidA;
    }

     $concat[] =  array (
                             'name' => $dossier,
                             'id' => $id,
                             'type' => 'rep',
                             'children' => $staticB );
}

$final = array (
    'identifier' => 'id',
    'label'    => 'name',
    'items'    => $concat
);

$json =  json_encode($final);
//echo stripslashes($json) . "\n";
$handle=fopen('work.json', "w");
fwrite($handle, stripslashes($json));
fclose($handle);
