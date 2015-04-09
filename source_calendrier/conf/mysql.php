<?php
     $ress_mysql = mysql_connect('localhost', 'ria01', 'ria01');
     $db = mysql_select_db('riasogeti', $ress_mysql) or die ("Connexion impossible");
?>