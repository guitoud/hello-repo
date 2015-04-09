<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="ROBOTS" content="INDEX, FOLLOW" />

    <title>PORTAIL DEV IAB</title>

    <link rel="stylesheet" type="text/css" href="js/dijit/themes/claro/claro.css" />

    <style type="text/css">
        body,
        html {
            font-family: helvetica, arial, sans-serif;
            font-size: 90%;
        }
    </style>


    <script type="text/javascript" src="dojo/dojo/dojo.js" djConfig='isDebug: false, parseOnLoad: true, debugAtAllCosts: false'>
    </script>

    <script type="text/javascript">
        dojo.require("dojo.data.ItemFileReadStore");
        dojo.require("dijit.Tree");

        function envoieRequete(url, id) {

            var targetNode = dojo.byId("centre");
            var past = url;

            var xhrArgs = {
                url: past,

                load: function(data) {
                    targetNode.innerHTML = data;
                },
                error: function(error) {
                    targetNode.innerHTML = "Fichier introuvable : Erreur Cat sans cat : \n&nbsp;&nbsp;&nbsp;" + error;
                }
            }
            var defered = dojo.xhrGet(xhrArgs);
        }
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico"></link>
    <style type="text/css" media="screen">
        body {
            font-family: Verdana, Arial, Helvetica, sans-serif;
            font-size: 0.8em;
            margin: 0;
            padding: 0;
        }
        #header {
            width: 100%;
            background-color: #FFFFFF;
            background-image: url(sog_header.png);
            background-repeat: no-repeat;
            background-attachment: fixed;
            padding: 0px;
        }
        #conteneur {
            position: fixed;
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            background-color: #D3D3D3;
        }
        #centre {
            background-color: white;
            margin-left: 250px;
            margin-top: 0px;
        }
        #gauche {
            position: absolute;
            background-color: #f2643e;
            width: 250px;
            margin-top: 30px;
            border: thin solid black;
            height: 100%;
            overflow: auto;
            background-image: url("Sogeti_Logo.PNG");
            background-repeat: no-repeat;
            background-position: left bottom;
            background-attachment: fixed;
        }
    </style>
    <script type="text/javascript">
        envoieRequete("mysql_fetch.php", "centre");
    </script>
</head>

<body class="claro">
    <div id="conteneur">

        <div dojoType='dojo.data.ItemFileReadStore' jsId='continentStore' url='work.json'>
        </div>

        <div dojoType="dijit.tree.ForestStoreModel" jsId="continentModel" store='continentStore' query="{type:'rep'}" rootId="continentRoot" rootLabel="RacideDesArbres" childrenAttrs="children">
        </div>
        <div id="cont_gauche">
            <div dojoType="dijit.Tree" id="gauche" model="continentModel" openOnClick="true" showRoot="false">
                <script type="dojo/method" event="onClick" args="item">
                    $ext = continentStore.getValue(item, 'ext');
                    if ($ext == "f") {
                        envoieRequete("menuX.php?src=" + continentStore.getValue(item, 'url'), continentStore.getValues(item, "position"));
                    } else {
                        // windows.href = continentStore.getValue(item, 'url');
                        window.open(continentStore.getValue(item, 'url'), '_blank');
                    }
                </script>
            </div>
        </div>

        <div id="header" margin: 0, padding: 0>
            <div id="logo">
                <div>
                </div>
            </div>
            <div id="centre">
            </div>
        </div>
    </div>
</body>
</html>

