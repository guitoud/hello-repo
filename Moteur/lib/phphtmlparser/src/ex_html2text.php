<?
    // Example: html2text
    // Converts HTML to formatted ASCII text.
    // Run with: php < ex_html2text.php

    include ("html2text.inc");

//    $htmlText = "Html2text is a tool that allows you to<br>" .
//                "convert HTML to text.<p>" .
//                "Does it work?";
				
	$htmlText = 			"<div align=\"justify\">
<span class=\"AR11NO\">Ainsi, contrairement &agrave; l'id&eacute;e re&ccedil;ue tr&egrave;s courante,
le ph&eacute;nom&egrave;ne des saisons n'a rien &agrave; voir avec la distance moyenne
Terre-Soleil, qui est de 150 millions de kms avec une variation
annuelle de millions de kms (<u>soit 1,6%</u>). La Terre est au plus proche du
Soleil (p&eacute;rih&eacute;lie) vers le 3 janvier, soit aux alentours du solstice
d'hiver (h&eacute;misph&egrave;re nord) et au plus loin (aph&eacute;lie) vers le 3 juillet,
soit peu apr&egrave;s le solstice d'&eacute;t&eacute; (h&eacute;misph&egrave;re nord). De fait, la seule
chose attribuable &agrave; la distance Terre-Soleil dans le ph&eacute;nom&egrave;ne des
saisons est le contraste plus grand qu'elles devraient avoir.</span>
</div>";

    $htmlToText = new Html2Text ($htmlText, 15);
    $text = $htmlToText->convert();
    echo "Conversion follows:\r\n";
    echo "-------------------\r\n";
    echo $text;

?>
