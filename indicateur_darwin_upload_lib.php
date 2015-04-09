<?php
// donne des information sur une date anne , mois , jour
function Return_info_date($date_en)
{
    if ($date_en=='') {
        $jour_en='';
        $mois_en='';
        $annee_en='';
        $date_en='';
    } else {
        $liste = explode('/', $date_en); // lecture du s&eacute;parateur ;
        $jour_en=$liste[0];
        $mois_en=$liste[1];
        $annee_en=$liste[2];
        $date_en=$annee_en.$mois_en.$jour_en;
    }
    return array($date_en,$jour_en,$mois_en,$annee_en);
}

function Return_info_date_2($date_en)
{
    if ($date_en=='') {
        $jour_en='';
        $mois_en='';
        $annee_en='';
        $date_en='';
    } else {
        $liste = explode('/', $date_en); // lecture du s&eacute;parateur ;
        switch ($liste[1])
    {
            case "Janv.":
                $nummois="01";
                break;
            case "Fev.":
                $nummois="02";
                break;
            case "Mar.":
                $nummois="03";
                break;
            case "Avr.":
                $nummois="04";
                break;
            case "Mai":
                $nummois="05";
                break;
            case "Juin":
                $nummois="06";
                break;
            case "Juillet":
                $nummois="07";
                break;
            case "Aout":
                $nummois="08";
                break;
            case "Sept.":
                $nummois="09";
                break;
            case "Oct.":
                $nummois="10";
                break;
            case "Nov.":
                $nummois="11";
                break;
            case "Dec.":
                $nummois="12";
                break;
        }
        $jour_en=$liste[0];
        //	$mois_en=$liste[1];
        $mois_nom_en=$liste[1];
        $annee_en=$liste[2];
        $date_en=$annee_en.$nummois.$jour_en;
    }
    return array($date_en,$jour_en,$nummois,$annee_en);
}
// donne le temps en minutes
function Return_minutes($TEMPS_MINUTES)
{
    $TEMPS_MINUTES_OK=$TEMPS_MINUTES;
    if ($TEMPS_MINUTES=='') {
        $TEMPS_MINUTES_OK=15;
    }
    
    if ($TEMPS_MINUTES<=15) {
        $TEMPS_MINUTES_OK=15;
    }
    
    return $TEMPS_MINUTES_OK;
}
// donne l'acteur et si sogeti
function Return_acteur($ASSIGNE, $mysql_link)
{
    if ($ASSIGNE=='') {
        $total_ligne_rq_info=0;
    } else {
        $liste = explode('_', $ASSIGNE); // lecture du s&eacute;parateur ;
        $PRENOM=substr(strtoupper($liste[1]), 0, 1);
        $NOM=strtoupper($liste[0]);
        $liste = explode('-', $PRENOM); // lecture du s&eacute;parateur ;
        $PRENOM=strtoupper($liste[0]);
        $rq_info="
        SELECT LEFT(UPPER(`LOGIN`),3) AS `LOGIN` ,UPPER(`SOCIETE`) AS `SOCIETE`
        FROM `moteur_utilisateur` 
        WHERE UPPER(`NOM`)='".$NOM."' AND UPPER(`PRENOM`) LIKE '".$PRENOM."%'
        LIMIT 1";
        $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
        $tab_rq_info = mysql_fetch_assoc($res_rq_info);
        $total_ligne_rq_info=mysql_num_rows($res_rq_info);
        mysql_free_result($res_rq_info);
    }
    if ($total_ligne_rq_info==0) {
        $SOGETI="N";
    } else {
        if ($tab_rq_info['SOCIETE']=='SOGETI') {
            $SOGETI="Y";
        } else {
            $SOGETI="N";
        }
    }
    return array($ASSIGNE,$SOGETI);
}

// donne le poids en valeur
function Return_poids_valeur($NATURE, $mysql_link)
{
    if ($NATURE=='') {
        $total_ligne_rq_info=0;
    } else {
        $rq_info="
        SELECT `INDICATEUR_REGLE_POIDS` 
        FROM `indicateur_regles` 
        WHERE `INDICATEUR_REGLE_TYPE`='QC' AND 
        UPPER(`INDICATEUR_REGLE_INFO`)=UPPER('".$NATURE."') AND 
        `ENABLE`=0
        LIMIT 1";
        $res_rq_info = mysql_query($rq_info, $mysql_link) or die(mysql_error());
        $tab_rq_info = mysql_fetch_assoc($res_rq_info);
        $total_ligne_rq_info=mysql_num_rows($res_rq_info);
        mysql_free_result($res_rq_info);
    }
    if ($total_ligne_rq_info==0) {
        $VALEUR=0;
    } else {
        $VALEUR=$tab_rq_info['INDICATEUR_REGLE_POIDS'];
    }
    return $VALEUR;
}
// donne le poids et le niveau
function Return_NIVEAU_POIDS($DUREE, $mysql_link)
{
    if ($DUREE==0) {
        $NIVEAU='VIDE';
        $POIDS=0;
    } else {
        $rq_complex_info="
        SELECT * FROM `indicateur_duree` WHERE '".
            $DUREE."' >= `INDICATEUR_DUREE_TEMPS_MINI` AND '".
            $DUREE."' < `INDICATEUR_DUREE_TEMPS_MAX` AND `ENABLE` =0 LIMIT 1 ";
        $res_rq_complex_info = mysql_query($rq_complex_info, $mysql_link) or die(mysql_error());
        $tab_rq_complex_info = mysql_fetch_assoc($res_rq_complex_info);
        $total_ligne_rq_complex_info=mysql_num_rows($res_rq_complex_info);
        if ($total_ligne_rq_complex_info==0) {
            $NIVEAU='VIDE';
            $POIDS=0;
        } else {
            $NIVEAU=$tab_rq_complex_info['INDICATEUR_DUREE_LIB'];
            $POIDS=$tab_rq_complex_info['INDICATEUR_DUREE_POIDS'];
        }
        mysql_free_result($res_rq_complex_info);
    }
    return array($NIVEAU,$POIDS);
}
// donne l'appli et le processus
function Return_app_proc($APPLICATION_PROCESSUS, $mysql_link)
{
    if ($APPLICATION_PROCESSUS=='') {
        $APPLICATION='';
        $PROCESSUS='';
    } else {
        if (substr_count($APPLICATION_PROCESSUS, '-')==0) {
            $APPLICATION=$APPLICATION_PROCESSUS;
            $PROCESSUS='';
        } else {
            $liste = explode('-', $APPLICATION_PROCESSUS); // lecture du s&eacute;parateur ;
            $APPLICATION=strtoupper($liste[0]);
            $PROCESSUS=strtoupper($liste[1]);
        }
    }
    return array($APPLICATION,$PROCESSUS);
}
