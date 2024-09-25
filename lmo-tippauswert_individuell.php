<?php

/*-------------------------------------------------------------------------------
// Individuelle Auswertung der Tippspielligen für LMO 4
// Autor: Marcus - www.bcerlbach.de
//-------------------------------------------------------------------------------
// Download unter: http://forum.bcerlbach.de/downloads.php?cat=7

  * Wer immer über Updates informiert werden will, der sollte sich
  * im BCE-Forum registrieren. Denn dann hat man die Möglichkeit einen
  * Download zu abonieren. D.h. wenn etwas daran geändert wurde,
  * so wird umgehend eine E-Mail verschickt.

  *
  * This program is free software; you can redistribute it and/or
  * modify it under the terms of the GNU General Public License as
  * published by the Free Software Foundation; either version 2 of
  * the License, or (at your option) any later version.
  *
  * This program is distributed in the hope that it will be useful,
  * but WITHOUT ANY WARRANTY; without even the implied warranty of
  * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
  * General Public License for more details.
  *
  * REMOVING OR CHANGING THE COPYRIGHT NOTICES IS NOT ALLOWED!
  *
-------------------------------------------------------------------------------*/



/*************** ab hier können Einstellungen vorgenommen werden ***************/

// Schriftart - bei Standardschrift Feld leer lassen -> $fontfamily = "";
$fontfamily = "Verdana, Arial, Courier, MS Serif";

// Größe der Tabellenschrift in Punkt
$fontsize = "10";

// Schriftfarbe
$fontcolor = "#000000";

// Schriftfarbe bei Fehlermeldungenim
$fontfehl = "#800000";

//Schriftgröße der Überschrift in Pixel
$headlinefontsize = "20";

// Farbe des Tabellenkopfes
$tablehaedercolor = "#C7CFD6";

// Farbe des Tabellenschriftfarbe
$tablefontcolor = "#123456";

// Farbe des Tabellenintergrundes
$tablebackcolor = "#C7CFD6";

// Tabellenschriftgröße
$tablefontsize = "10";

// Zellenhintergrundfarbe für Platz 1 bis 3
$colorplatz1 = "#efed25";  // wenn nicht dann frei lassen -> $colorplatz1="";
$colorplatz2 = "#bab4a2";  // wenn nicht dann frei lassen -> $colorplatz2="";
$colorplatz3 = "#cc9b18";  // wenn nicht dann frei lassen -> $colorplatz3="";

// Anzahl der anzuzeigenden Tipper festlegen
$showtipper = -1;  // -1=keine Begrenzung

// sollen Tipper die noch keinen Tipp abgegeben haben angezeigt werden?
$shownichttipper = 0;  // 0=nein - 1=ja

// Was soll bei der Auswertung angezeigt werden?  1 = anzeigen; 0 nicht anzeigen
$show_sp_ges = 1;   // Anzahl Spiele getippt
$show_sp_proz = 1;  // Quote richtiger Tipps - oder Punkte pro Spiel
$show_joker = 1;    // Jokerpunkte
$show_punkte = 1;   // Anzahl Punkte -> hier ist die 1 empfohlen
$show_team = 1;     // Teamnamen anzeigen

// Zeichen im Tabellenkopf bei der Ausgabe einstellen - Variablen anpassen
$var_spiele = "Sp";         // Anzahl Spiele getippt - Standard "Sp"
$var_joker = "JP";          // durch Joker dazugewonnene Punkte - standard "JP"
$var_tippsrichtig = "Pkt";  // Anzahl Tipps richtig - Standard "P"
$var_team = "MS";           // Team Mannschaft der man angehört

// Seitentitel
$title = "www.bcerlbach.de - Individuelle Auswertung der Tippspielligen";

// Statusleistentext - falls nicht gewünscht frei lassen -> $status = "";
$status = "www.bcerlbach.de - Individuelle Auswertung der Tippspielligen";


/************************* ab hier nichts mehr ändern **************************/

require_once(dirname(__FILE__).'/init.php');

// falls die Auswertung nur eingeloggte User zu Gesicht bekommen sollen
/*
if ($_SESSION["lmotipperok"] != 5) {
  die("Sorry, aber die Auswertung ist nur für eingeloggte User!");
}
*/

// Datei gesamt.aus in Array einlesen... evtl. Pfad anpassen
$auswertdatei = PATH_TO_ADDONDIR."/tipp/tipps/auswert/gesamt.aus";

// Prüfen ob die Datei vorhanden ist
if (is_file($auswertdatei)) {
  $array = @file($auswertdatei);
}
else {
  //Skript abbrechen wenn Datei nicht vorhanden
  die("Datei $auswertdatei nicht vorhanden - Tippspiel neu auswerten!");
}

// Tippmodus aus congig-datei auslesen
$tippmodus = @file(PATH_TO_LMO."/config/tipp/cfg.txt");
$tippmodus = substr($tippmodus[34], 10, 1);  // 0=Tendenz  1=Ergebnis

if ($tippmodus == 0) {
  $var_prozrichtig = "Sp%";  // Prozent Spieltipp richtig - Standard "Sp%"
}
else {
  $var_prozrichtig = "Sp&Oslash;";  // Punkte pro Spiel bei Ergebnistipp
}

// Anzahl der Tipp-Ligen ermitteln
$zeile = trim($array[1]);  // unnötige Zeilenumbrüche ... entfernen
$anzligen = substr($zeile, 9, strlen($zeile));  // -> eigentlich immer ab der 10. Stelle

// Anzahl der Sportsfreunde (Tipper)
$anztipper = count( file( PATH_TO_ADDONDIR."/tipp/".$tipp_tippauthtxt ));

// Version dieses Scriptes
$ver = "1.9.1";

// Zurück Button
$zurueck = "<b><a href=\"javascript:history.go(-1);\">Zurück</a></b><br>";


//------------------------------------------------------------------------------
// Eigene Funktion zum ermitteln des Dateinames
function dateiname($zeile) {
  //$zeile = trim($datei);
  $pos = strpos($zeile, "=");  // suche nach dem =
  if ($pos++ !== false) {
    $dateiname = substr($zeile, $pos++, strlen($zeile));  //ligenname
  }

  $dateiname = str_replace('.aus', '.l98', $dateiname);
  return $dateiname;  // z.b. liga1.l98
}  // function dateiname end
//------------------------------------------------------------------------------


//------------------------------------------------------------------------------
// Eigene Funktion zum ermitteln der Ligen-Info
function dateiinfo($datei) {
  $dateiname = $datei;                    // wird benötigt, falls Datei nicht vorhanden
  $datei = getcwd() . "/ligen/". $datei;  // Ligen-Pfad

  // überprüfen, ob Ligen-Datei existiert
  if (is_file($datei)) {
    $liga = file($datei);                                   // Datei wird in Array eingelesen
    $dateiinfo = str_replace('Name=', '', trim($liga[2]));  // Liga-Info in der 3. Zeile
    $liga = '';
  }
  else {
    // wenn die Datei nicht vorhanden ist -> Dateiname als Info verwenden
    $dateiinfo = $dateiname;
    $dateiname = "";
  }
  return $dateiinfo;
} // function Ligen-Info end
//------------------------------------------------------------------------------


/* Eingabemaske zusammenbasteln und ausgeben */
$htmlhead = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
      "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
 <title>'.$title.'</title>
<style type="text/css">
body {
font-size: '. $fontsize .'pt;
font-family: '. $fontfamily .';
color: '. $fontcolor .';
background-color: #B9C4CC;
  /* Farbe der Scrollbalken */
  scrollbar-face-color: #B9C4CC;
  scrollbar-track-color: #B9C4CC;
  scrollbar-highlight-color: #B9C4CC;
  scrollbar-3dlight-color: #B9C4CC;
  scrollbar-darkshadow-color: #B9C4CC;
  scrollbar-base-color:#B9C4CC;
  scrollbar-arrow-color:  #889CB0;
  scrollbar-shadow-color: #889CB0;
}

p {
  font-size: '. $fontsize .'pt;
}

h2 {
  margin-top: 5px;
  margin-bottom: '. $headlinefontsize .'px;
  color: #D8E4EC;
  background-color: #889CB0;
  text-align: center;
}

a { text-decoration: overline,underline; color: #3E4753; font-size: 10pt;}
a:visited  { text-decoration: underline; color: #3E4753; }
a:hover    { text-decoration: underline; color: #104E8B; }
a:active  { text-decoration: underline; color: #D8E4EC; }

table.auswert {
  font-size: '. $tablefontsize .'pt;
  color: '. $tablefontcolor .';
  background-color: '. $tablebackcolor .';
  text-align: center;
  BORDER: #8CA0B4 1px dotted;
}
th.auswert {
  background-color: '. $tablehaedercolor .';
}

hr {
  height: 0px;
  border: dashed #525E6E 0px;
  border-top-width: 1px;
}

input {
  color : #000000;
  background-color: #B9C4CC;
  font-size: 10pt;
}

acronym {
  cursor:help;
  border-bottom:1px dotted;
}

font.foot {
  font-size: 8pt;
}

font.fehler {
  font-size: '. $fontfehl .';
}

a.foot { text-decoration: overline,underline; color: #3E4753; font-size: 8pt;}

</style>

<script type="text/javascript" language="javascript1.2">
<!--
';

if (strlen($status) != 0) {
    $htmlhead.='window.status=\' '.$status.' \'
';
}

$htmlhead .= 'function select_switch(status)
{
   for (i = 0; i < document.formular.length; i++)
   {
      document.formular.elements[i].checked = status;
   }
}
// -->
</script>
</head>';


$htmlfoot = '<hr width="195" align="right" />
<p style="line-height:12px; margin-top:0px" align="right">
<font class="foot"><a href="http://forum.bcerlbach.de/downloads.php?cat=7" class="foot" target="_blank" title="zum Download">Auswertskript</a> v'. $ver .' &copy; by <a href="http://www.bcerlbach.de" class="foot" target="_blank" title="zur Homepage">Marcus</a></font></p>
</body></html>';


/*------------------------------------------------------------------------------
/* Formular anzeigen, noch nichts wurde ausgewählt
/*------------------------------------------------------------------------------*/
if (!$_POST["iswas"]) {

  $htmlbody .= '
  <body>
  <h2>Individuelle Auswertung</h2>
  <table border="1" cellspacing="0" cellpadding="5" align="center"><tr><td align="center">
    <form name="formular" method="post" action="'.$_SERVER["REQUEST_URI"].'">
  <table border="0" cellspacing="0" cellpadding="0" align="center">
    <tr> <td colspan="2" align="left"><br />Die gewünschten Ligen markieren.<br />Anschließend Button klicken.<br>&nbsp;
    </td> </tr>
    ';

// for stellt das Formular zusammen in dem die Ligen ausgewählt werden können
for ($i = 0; $i < $anzligen; $i++) {

    $z = $i+1;   // for beginnt mit 0, die Ausgabe aber mit 1
    $zl = $i+2;  // wird benötigt, da die Ligen in der Datei ab der 3ten Zeile stehen

    $dateiname = Dateiname(trim($array[$zl]));  // liefert ligenname

  $htmlbody .= '  <tr><td colspan="2" height="30" align="left"> <input type="checkbox" value="'.$z.'" name="liga'.$z.'" class="checkbox" checked>&nbsp;&nbsp;'.dateiinfo($dateiname)/*funktionsaufruf um ligeninfo zu ermittlen und auszugeben*/.' </td> </tr>
  ';
}  // end-for


$htmlbody .= '<tr><td align="center" nowrap="nowrap">
<a href="javascript:select_switch(true);"><font size="1">Alle auswählen</font></a>
<a href="javascript:select_switch(false);"><font size="1">Auswahl aufheben</font></a>
  </td></tr>

<tr><td align="center" nowrap="nowrap"><br><input type="submit"  value="Ergebnis anzeigen" name="submit" /><input type="hidden" name="iswas" value="1" /></td></tr>
</table></form></table>
<p align="center"><a href="lmo.php?action=tipp">zurück zum Tippspiel</a></p>';


//******************************************************************************
}
else {  // wenn der OK Button geklickt wurde
  $zeit1 = microtime();  // Zeit nehmen Start

  // eingeloggten User ermitteln
  $username = "";
  if ( (isset($_SESSION['lmotippername']) && $_SESSION['lmotippername'] != "") && (isset($_SESSION['lmotipperok']) && $_SESSION['lmotipperok'] > 0) ) {
    //echo "...mach dies, wenn eingeloggt... ". $_SESSION['lmotippername'];
    $username = "[". $_SESSION['lmotippername'] ."]";
  }

  $ligenkurz = array();  // Beinhaltet Kürzel für Ligen
  $anzgetipptkurz = array();  // Beinhaltet Kürzel für Anzahl getippter Spiele
  $jokerkurz = array();  // Beinhaltet Punkte für Joker

  // for Schaut der Reihe nach welche Liga markiert wurde, daraufhin...
  // werden kleine Arrays erstellt die die Kürzel für die Auswertung beinhalten
  for ($i = 1; $i <= $anzligen; $i++) {
    $ligavar = "liga".$i;
    // checken welche Ligen gewählt wurden
    if ($_POST[$ligavar] == $i)  {
      array_push($ligenkurz, 'TP'.$i);     //-- ligenkürzel = 'TP'.$i
      array_push($anzgetipptkurz, 'SG'.$i);  //-- kürzel = 'SG'.$i
      array_push($jokerkurz, 'P'.$i);      //-- joker Punkte
    }
  }  // end-for

  $zligen = count($ligenkurz);  // zähler der gewählten ligen

  // Überprüfen dass mind. eine Liga ausgewählt wurde
  if ($zligen > 0) {

    $auswert = array();  //ausgefiltertes Array
    $goal = array(array(),array());  // 2dimensionales Array anlegen

    $anzgoal = -1;

    // for durchläuft jede Zeile der Auswertungsdatei
    for ($i = $anzligen+3; $i < sizeof($array); $i++) {
      // Usernamen ermitteln, wenn gefunden in Array speichern
      $posname = strpos($array[$i], "[");
      if ($posname !== false) {
        // gefundenen Namen ins Array speichern
        $goal[++$anzgoal][0] = $array[$i];
      }

      // foreach1 ermittelt die erzielten Punkte
      foreach ($ligenkurz as $value) {
        $value = $value."=";  // = muss stehen da bei TP1 auch TP10 TP11 erfasst
        $pos1 = strpos($array[$i], $value);
        if ($pos1 !== false) {
          //punkte gleich Array dazu addieren
          $goal[$anzgoal][1] += ltrim(strrchr($array[$i],'='),'=');
        }
      }  // foreach1 end

      //foreach2 ermittelt die Anzahl an getippten Spielen
      foreach ($anzgetipptkurz as $value) {
        $value = $value."=";  // = muss stehen da bei TP1 auch TP10 TP11 erfasst
        $pos1 = strpos($array[$i], $value);
        if ($pos1 !== false) {
          // Anzahl getippter Spiele gleich Array dazu addieren
          $goal[$anzgoal][2] += ltrim(strrchr($array[$i],'='),'=');
        }
      }  // foreach2 end

      // wird nur benötigt, wenn die Jokerpunkte angezeigt werden sollen
      if ($show_joker == 1) {
        // foreach3 ermittelt Jokerpunkte
        foreach ($jokerkurz as $value) {
          $value = $value."=";  // = muss stehen da bei TP1 auch TP10 TP11 erfasst
          $pos1 = strpos($array[$i], $value);
          if ($pos1 !== false) {
            // Anzahl getippter Spiele gleich Array dazu addieren
            $goal[$anzgoal][3] += ltrim(strrchr($array[$i],'='),'=');
            //var. zeigt ob jokerpunkte genutzt werden, wenn ja joker=1
            $joker = 1;
          }
        }  // foreach3 end
      }  // if joker end

      // wird nur benötigt, wenn Teams angezeigt werden sollen
      if ($show_team == 1) {
        // Teamname ermitteln, wenn gefunden in Array speichern
        $posname = strpos($array[$i], "Team=");
        if ($posname !== false)  {
          //gefundenen namen ins Array speichern
          $goal[$anzgoal][4] = ltrim(strrchr($array[$i],'='),'=');
          //var. zeigt ob teams genutzt werden muss, wenn ja team=1
          if (strlen($goal[$anzgoal][4]) != 1) {
            $team = 1;
          }
        }
      }  // if showteam end
    }  // for Durchlauf Auswertungsdatei end


    /*--------------------------------------------------------------------------
    /* BUBBLE SORT  des zweidimensionalen Arrays
    /*--------------------------------------------------------------------------*/

    $anzahl_elemente = count($goal);  //Anzal der Elemente ermittlen. -1 da Arrays mit 0 beginnen! ;o)

    // Schleife wird entsprechend der Anzahl der Elemente im Array $zahlen wiederholt
    for($y = 0; $y < $anzahl_elemente; $y++) {
      //Jedes Element wird einzelen angesprochen und verschoben wenn das linke Element grösser ist als der rechte
      for($x = 0; $x < $anzahl_elemente; $x++) {
        // In diesem Beispiel aufsteigend.
        // Möchte man absteigend sortieren, einfach das grösser Zeichen mit einem kleiner Zeichen tauschen

        // tauschen wenn:
        // 1. erzielte Punkte unterschiedlich  oder
        // 2. erzielte pkte gleich + erzielte pkte>0 + anz. Tipp unterschiedlich
        if (($goal[$x][1] < $goal[$x+1][1])
          or (($goal[$x][1] == $goal[$x+1][1])
          and ($goal[$x][1] > 0)
          and ($goal[$x][2] > $goal[$x+1][2]))) {
            // Punkte werden zwischengespeichert...
            $grosser_wert = $goal[$x][1];
            $kleiner_wert = $goal[$x+1][1];
            // Namen ebenfalls
            $grosser_name = $goal[$x][0];
            $kleiner_name = $goal[$x+1][0];
            // Anzahl getippter Spiele
            $grosse_anz = $goal[$x][2];
            $kleine_anz = $goal[$x+1][2];
            // Joker
            $grosse_joker = $goal[$x][3];
            $kleine_joker = $goal[$x+1][3];
            // Team
            $grosse_team = $goal[$x][4];
            $kleine_team = $goal[$x+1][4];

            //... und anschließen werte vertauschen
            $goal[$x][1] = $kleiner_wert;
            $goal[$x+1][1] = $grosser_wert;
            // Namen tauschen
            $goal[$x][0] = $kleiner_name;
            $goal[$x+1][0] = $grosser_name;
            // Anzahl getippter Spiele tauschen
            $goal[$x][2] = $kleine_anz;
            $goal[$x+1][2] = $grosse_anz;
            // Joker
            $goal[$x][3] = $kleine_joker;
            $goal[$x+1][3] = $grosse_joker;
            // Team
            $goal[$x][4] = $kleine_team;
            $goal[$x+1][4] = $grosse_team;
        }  // if end
      }  // for 2 end
    }  // for 1 end

    /*--------------------------------------------------------------------------
    /* aufbereiten für endausgabe des sortierten Arrays
    /*------------------------------------------------------------------------*/

    $htmlbody .= '
<body>
<h2>Ergebnis der Auswertung</h2>
<p align="center">('.$zligen.' Ligen zusammengezählt)</p>
  <table align="center" class="auswert">
     <tr>
      <th class="auswert">Platz</th><th class="auswert">Name</th>';

    // Anzahl getippter Spiele ausgeben?
    if ($show_sp_ges == 1) {
      $htmlbody.= '<th class="auswert"><acronym title="Anzahl Spiele getippt"><u>'.$var_spiele.'</u></acronym></th>';
    }

    // Werden Jokerpunkte zugelassen? wenn ja, Spalte einblenden - ja oder nein?
    if (($show_joker == 1) and ($joker == 1)) {
      $htmlbody .= '<th class="auswert"><acronym title="durch Joker dazugewonnene Punkte"><u>'.$var_joker.'</u></acronym></th>';
    }

    // Quote richtiger Spiele ausgeben - ja oder nein?
    if ($show_sp_proz == 1) {
      if ($tippmodus == 0) {  // Tendenz
        $htmlbody .= '<th class="auswert"><acronym title="Prozent Spieltipp richtig%"><u>'.$var_prozrichtig.'</u></acronym></th>';
      }
      elseif ($tippmodus == 1) {  // Ergebnis
        $htmlbody .= '<th class="auswert"><acronym title="Punkte pro Spiel"><u>'.$var_prozrichtig.'</u></acronym></th>';
      }
    }

    // Anzahl Tipps ausgeben - ja oder nein?
    if ($show_punkte == 1) {
      $htmlbody.= '<th class="auswert"><acronym title="Anzahl Tipps richtig"><u>'.$var_tippsrichtig.'</u></acronym></th>';
    }

    // Team ausgeben - ja oder nein?
    if (($show_team == 1) and ($team == 1)) {
      $htmlbody.= '<th class="auswert"><acronym title="Teamzugehörigkeit"><u>'.$var_team.'</u></acronym></th>';
    }

    $htmlbody .= '</tr>';

    $platz = 0;
    $platz2 = 0;

    // für HTML aufbereiten
    for ($i = 0; $i <= count($goal)-1; $i++) {
    //for ($i = 0; $i <= $anztipper; $i++) {

      // begrenzung Anzahl Tipper
      if ($i == $showtipper) {
        break;
      }

      // Bedingung, die alle Nicht-Tipper rausfiltert falls gewünscht
      if (($shownichttipper != 0) or ($goal[$i][2] != 0)) {

        // Wert im Array mit username vergleichen -> fett darstellen
        if (chop($goal[$i][0]) == $username) {
          $goal[$i][0] = "<b>". $goal[$i][0] ."</b>";
        }

        $platz++;

        // Ausgabe der Platzierung wenn:
        // 1. Punkte ungleich dem vorgänger
        // 2. Punkte gleich, aber Anzahl getippter Spiele unterschiedlich
        if (($goal[$i][1] != $goal[$i-1][1]) or (($goal[$i][1] == $goal[$i-1][1]) and (($goal[$i][2] != $goal[$i-1][2])))) {

          $platz2++;

          if ($platz == 1) {
            $htmlbody .= '<tr bgcolor="'.$colorplatz1.'"> <td>';
          }
          else if ($platz == 2) {
            $htmlbody .= '<tr bgcolor="'.$colorplatz2.'"> <td>';
          }
          else if ($platz == 3) {
            $htmlbody .= '<tr bgcolor="'.$colorplatz3.'"> <td>';
          }
          else {
            $htmlbody .= '<tr bgcolor="'.$tablebackcolor.'"> <td>';
          }
          $htmlbody .= $platz;
        }
        else {
          if ($platz2 == 1) {
            $htmlbody .= '<tr bgcolor="'.$colorplatz1.'"> <td>';
          }
          else if ($platz2 == 2) {
            $htmlbody .= '<tr bgcolor="'.$colorplatz2.'"> <td>';
          }
          else if ($platz2 == 3) {
            $htmlbody .= '<tr bgcolor="'.$colorplatz3.'"> <td>';
          }
          else {
            $htmlbody .= '<tr bgcolor="'.$tablebackcolor.'"> <td>';
          }
          //$platz2 = $platz-1;
          $htmlbody .= '&nbsp;';
        }

/*        // Wenn unterschieldliche Punkte und getippte Spiele -> Ausgabe Platz
        if ($goal[$i][1] != $goal[$i-1][1]) {
          $htmlbody .= $platz;
        }
        // Prüfen auf gleiche Anzahl an getippten Spielen:
        else if ($goal[$i][2] == $goal[$i-1][2]) {
          $htmlbody .= '&nbsp;';  // wenn ja, wird Leerzeichen ausgegeben
        }
        else {  // Platz auch ausgeben, wenn Punkte gleich aber unterschiedlich viele Spiele getippt
          $htmlbody .= $platz;  // wenn nein, wird platz ausgeben
        }*/

        // Erfolgsquote in Prozent oder in Punkte pro Spiel
        if ($goal[$i][2] > 0) {
          if ($tippmodus == 0) {  // Tendenz
            $quote = $goal[$i][1] / $goal[$i][2] * 100;
          }
          else {                 // Ergebnis
            $quote = $goal[$i][1] / $goal[$i][2];
          }
        }
        else {
          $quote = 0;
        }

        $htmlbody .= '</td> <td>'.$goal[$i][0].'</td>';

        // Die gesamten getippten Spiele ausgeben?
        if ($show_sp_ges == 1) {
          $htmlbody .= '<td>'.$goal[$i][2].'&nbsp;</td>';
        }

        // werden Jokerpunkte zugelassen? wenn ja, Spalte einblenden - ja oder nein?
        if (($show_joker == 1) and ($joker == 1)) {
          $htmlbody .= '<td>'.$goal[$i][3].'&nbsp;</td>';
        }

        // Quote richtig getippte Spiele ausgeben - ja oder nein?
        if ($show_sp_proz == 1) {
          $htmlbody .= '<td>'.round($quote, 2).'&nbsp;</td>';
        }

        // Anzahl Tipps ausgeben - ja oder nein?
        if ($show_punkte == 1) {
          $htmlbody .= '<td>'.$goal[$i][1].'&nbsp;</td>';
        }

        // Anzahl Tipps ausgeben - ja oder nein?
        if (($show_team == 1) and ($team == 1)) {
          $htmlbody .= '<td>'.$goal[$i][4].'&nbsp;</td>';
        }

        $htmlbody .= '</tr>';

      }  // end Filter Nicht-Tipper
    }  // end for HTML aufbereiten

    $htmlbody .= '</table>
<p align="center">Anzahl der Tipper: '. $anztipper;  // count($goal);

    if ($shownichttipper == 0) {
      $htmlbody .= '<br><font class="foot">(Nicht-Tipper werden nicht dargestellt)</font>';
    }

    $htmlbody .= '<p align="center">Stand vom '.date("d.m.Y").' - '.date("H:i") .' Uhr</p>
<p align="center"><a href="lmo-tippauswert_individuell.php">zurück zur Auswahl</a>&nbsp;|&nbsp;<a href="lmo.php?action=tipp">zurück zum Tippspiel</a></p>';

    $zeit2 = microtime();  //stopp //echo "<br>Berechnung dauerte" . ($zeit2-$zeit1);

  }
  else {  // keine liga gewählt
    $htmlbody .= '<p align="center"><br><br><font class="fehler">keine Liga gewählt</font><br><br>'.$zurueck.'</p>';
  }  // else $zligen>0
}  // end-else - wenn OK Button geklickt wurde

// Ausgabe HTML Code an den Browser
echo $htmlhead . $htmlbody . $htmlfoot;

clearstatcache();

?>