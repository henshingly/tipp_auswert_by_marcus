<?php

/*------------------------------------------------------------------------------
// Individuelle Auswertung der Tippspielligen für LMO 4
// Autor: Marcus - www.bcerlbach.de
//------------------------------------------------------------------------------
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
------------------------------------------------------------------------------*/



/***** ab hier können Einstellungen vorgenommen werden ************************/
	
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

//Tabellenschriftgröße
$tablefontsize = "10";

// Zellenhintergrundfarbe für Platz 1 bis 3
$colorplatz1 = "#efed25"; // wenn nicht dann frei lassen -> $colorplatz1="";
$colorplatz2 = "#bab4a2"; // wenn nicht dann frei lassen -> $colorplatz2="";
$colorplatz3 = "#cc9b18"; // wenn nicht dann frei lassen -> $colorplatz3="";

// Anzahl der anzuzeigenden Tipper festlegen
$showtipper = -1; // -1=keine Begrenzung

//sollen tipper die noch keinen tipp abgegeben haben angezeigt werden?
$shownichttipper = 0; // 0=nein - 1=ja

//was soll bei der auswertung angezeigt werden?  1 = anzeigen; 0 nicht anzeigen
$show_sp_ges = 1;//Anzahl Spiele getippt
$show_sp_proz = 1;//quote richtiger tipps - oder punkte pro spiel
$show_joker = 1;//jokerpunkte 
$show_punkte = 1;//anzahl punkte -> hier ist die 1 empfohlen
$show_team = 1;//teamnamen anzeigen

//zeichen im tabellenkopf bei der ausgabe einstellen - Variablen anpassen
$var_spiele = "Sp";//Anzahl Spiele getippt - Standard "Sp"
$var_joker = "JP";//durch Joker dazugewonnene Punkte - standard "JP"

$var_tippsrichtig = "Pkt";//Anzahl Tipps richtig - Standard "P"
$var_team = "MS";//Team Mannschaft der man angehört

// seitentitel
$title = "www.bcerlbach.de - Individuelle Auswertung der Tippspielligen";

// statusleistentext - falls nicht gewünscht frei lassen -> $status = "";
$status = "www.bcerlbach.de - Individuelle Auswertung der Tippspielligen";


/***** ab hier nichts mehr ändern *********************************************/

require_once(dirname(__FILE__).'/init.php');

/* falls die Auswertung nur eingeloggt User zu Gesicht bekommen sollen: */
/*if ($_SESSION["lmotipperok"] != 5) {
		die("Sorry, aber der Zugang ist nur für eingeloggte User möglich!");
}*/

//datei gesamt.aus in array einlesen... evtl. Pfad anpassen
$auswertdatei = PATH_TO_ADDONDIR."/tipp/tipps/auswert/gesamt.aus";

//prüfen ob Datei vorhanden ist
if (is_file($auswertdatei)) {
    $array = @file($auswertdatei);
}
else {
	 //Skript abbrechen wenn Datei nicht vorhanden
	 die("Datei $auswertdatei nicht vorhanden - Tippspiel neu auswerten!");
	 }

//tippmodus aus congig-datei auslesen
$tippmodus = @file(PATH_TO_LMO."/config/tipp/cfg.txt");
$tippmodus = substr($tippmodus[34], 10, 1); // 0=Tendenz  1=Ergebnis

if ($tippmodus == 0) {
    $var_prozrichtig = "Sp%"; // Prozent Spieltipp richtig - Standard "Sp%"
}
else {
     $var_prozrichtig = "Sp&Oslash;"; // Punkte pro Spiel bei Ergebnistipp
     }

/* anzahl der tipp-ligen ermitteln */
$zeile = trim($array[1]); // unnötige zeilenumbrüche ... entfernen
$anzligen = substr($zeile, 9, strlen($zeile));//->eigentlich immer ab 10. stelle

//anzahl der sportsfreunde
$anztipper = count( file( PATH_TO_ADDONDIR."/tipp/".$tipp_tippauthtxt ));

//version
$ver = "1.9"; 

//zurück-button
$zurueck = "<b><a href=\"javascript:history.go(-1);\">zur&uuml;ck</a></b><br>";

		
//------------------------------------------------------------------------------
/* eigene funktion zum ermitteln des dateinames */
function dateiname($zeile) {
	//$zeile = trim($datei); 
	$pos = strpos($zeile, "="); // suche nach dem =
	if ($pos++ !== false) {
		$dateiname = substr($zeile, $pos++, strlen($zeile)); //ligenname
	}    
		 
	$dateiname = str_replace('.aus', '.l98', $dateiname);
    return $dateiname; // z.b. liga1.l98
}//end-function    
//------------------------------------------------------------------------------


//------------------------------------------------------------------------------
/* eigene funktion zum ermitteln der ligen-info */
function dateiinfo($datei) {
    $dateiname = $datei;//wird benötigt, falls datei nicht vorhanden
	$datei = getcwd() . "/ligen/". $datei; // ligen-pfad
	
	//überprüfen, ob ligen-datei existiert 
	if (is_file($datei)) {
	    $liga = file($datei);//file wird in array eingelesen
	    $dateiinfo = str_replace('Name=', '', trim($liga[2]));//liga-info in 3ter zeile
		$liga = ''; 
	}
	else {
 	     //wenn datei nicht vorhanden -> dateiname als info verwenden
         $dateiinfo = $dateiname;
         $dateiname = "";
    }
		
    return $dateiinfo; 
}//end-function    
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
a:visited	{ text-decoration: underline; color: #3E4753; }
a:hover		{ text-decoration: underline; color: #104E8B; }
a:active	{ text-decoration: underline; color: #D8E4EC; }

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
/* formular anzeigen, noch nichts geklickt
/*-------------------------------------------*/
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
	  
//for stellt die form zusammen wo die ligen ausgewählt werden können
for ($i = 0; $i < $anzligen; $i++) {
    
    $z = $i+1; //for beginnt mit 0, die ausgabe aber mit 1 
    $zl = $i+2; //wird benötigt, da die ligen in der datei ab der 3ten zeile stehen
    
    $dateiname = dateiname(trim($array[$zl])); // liefert ligen-name

	$htmlbody .= '	<tr><td colspan="2" height="30" align="left"> <input type="checkbox" value="'.$z.'" name="liga'.$z.'" class="checkbox" checked>&nbsp;&nbsp;'.dateiinfo($dateiname)/*funktionsaufruf um ligeninfo zu ermittlen und auszugeben*/.' </td> </tr>
	';
}//end-for

 
$htmlbody .= '<tr><td align="center" nowrap="nowrap">
<a href="javascript:select_switch(true);"><font size="1">Alle auswählen</font></a> 
<a href="javascript:select_switch(false);"><font size="1">Auswahl aufheben</font></a>
	</td></tr>

<tr><td align="center" nowrap="nowrap"><br><input type="submit"  value="Ergebnis anzeigen" name="submit" /><input type="hidden" name="iswas" value="1" /></td></tr>
</table></form></table>
<p align="center"><a href="lmo.php?action=tipp">zurück zum Tippspiel</a></p>';


//******************************************************************************
}else /* wenn der anzeige-button geklickt wurde */  
	{
	$zeit1 = microtime(); //zeit nehmen start
	    
	/* eingeloggten user ermitteln */
	$username = "";
	if ( (isset($_SESSION['lmotippername']) && $_SESSION['lmotippername'] != "") && (isset($_SESSION['lmotipperok']) && $_SESSION['lmotipperok'] > 0) ) { 	
	    //echo "...mach dies, wenn eingeloggt... ". $_SESSION['lmotippername'];
	    $username = "[". $_SESSION['lmotippername'] ."]";
	} 

	$ligenkurz = array(); //beinhaltet kürzel für ligen
	$anzgetipptkurz = array(); //beinhaltet kürzel für anzahl getippter spiele
	$jokerkurz = array(); //beinhaltet punkte für joker
	
	//for schaut der reihe nach welche liga markiert wurde, daraufhin...
	//werden kleine arrays erstellt die die kürzel für die auswertung beinhalten
    for ($i = 1; $i <= $anzligen; $i++) {
	    $ligavar = "liga".$i;
	    // checken welche ligen gewählt wurden
	    if ($_POST[$ligavar] == $i)	{
			array_push($ligenkurz, 'TP'.$i); 		//-- ligenkürzel = 'TP'.$i
			array_push($anzgetipptkurz, 'SG'.$i);	//-- kürzel = 'SG'.$i
			array_push($jokerkurz, 'P'.$i);			//-- joker punkte
		}    
	}//end-for   
	
    $zligen = count($ligenkurz);//zähler der gewählten ligen
    
//überprüfen dass mind. eine liga gewählt wurde
if ($zligen > 0) {

	$auswert = array(); //ausgefiltertes array
	$goal = array(array(),array()); // 2dimensionales array anlegen

	$anzgoal = -1;
    
	/* for durchläuft jede zeile der auswertungsdatei */
	for ($i = $anzligen+3; $i < sizeof($array); $i++) 
		{
		//usernamen ermitteln, wenn gefunden in array speichern
		$posname = strpos($array[$i], "["); 
		if ($posname !== false) {
			//gefundenen namen ins array speichern
			$goal[++$anzgoal][0] = $array[$i];
		}

 	    //foreach1 ermittelt die erzielten punkte
		foreach ($ligenkurz as $value) 
		{
		    $value = $value."="; // = muss stehen da bei TP1 auch TP10 TP11 erfasst
			$pos1 = strpos($array[$i], $value); 
			if ($pos1 !== false) {
			     //punkte gleich array dazu addieren
			     $goal[$anzgoal][1] += ltrim(strrchr($array[$i],'='),'=');
		 	}
		}//foreach1 end
			
		//foreach2 ermittelt die anzahl an getippten spielen
		foreach ($anzgetipptkurz as $value) 
		{
		    $value = $value."="; // = muss stehen da bei TP1 auch TP10 TP11 erfasst
			$pos1 = strpos($array[$i], $value); 
			if ($pos1 !== false) {
				//anzahl getippter spiele gleich array dazu addieren
				$goal[$anzgoal][2] += ltrim(strrchr($array[$i],'='),'=');
		 	}
		}//foreach2 end
		
		//wird nur benötigt, wenn die joker-punkte angezeigt werden sollen
		if ($show_joker == 1)
			{
			//foreach3 ermittelt jokerpunkte
			foreach ($jokerkurz as $value) 
			{
			    $value = $value."="; // = muss stehen da bei TP1 auch TP10 TP11 erfasst
				$pos1 = strpos($array[$i], $value); 
				if ($pos1 !== false) {
			    	//anzahl getippter spiele gleich array dazu addieren
			    	 $goal[$anzgoal][3] += ltrim(strrchr($array[$i],'='),'=');
			    	//var. zeigt ob jokerpunkte genutzt werden, wenn ja joker=1
			    	 $joker = 1;
			 	}
			}//foreach3 end
		}//if joker		

		//wird nur benötigt, wenn teams angezeigt werden sollen
		if ($show_team == 1)
			{				
			//teamname ermitteln, wenn gefunden in array speichern
			$posname = strpos($array[$i], "Team="); 
			if ($posname !== false)	{
				//gefundenen namen ins array speichern
				$goal[$anzgoal][4] = ltrim(strrchr($array[$i],'='),'=');
				//var. zeigt ob teams genutzt werden muss, wenn ja team=1
				if (strlen($goal[$anzgoal][4]) != 1) { 
				    $team = 1; 
				}
			}
		}//if
							
	}//end for	
	
/*for ($i=0; $i < count($goal); $i++){
	echo "$i  ".$goal[$i][0]."  ".$goal[$i][1]." ".$goal[$i][2]."  ".$goal[$i][3]."  ".$goal[$i][4]."<br>";
	}*/


/*------------------------------------------------------------------------------
/* BUBBLE SORT  des zweidimensionalen arrays */
/*-------------------------------------------*/

$anzahl_elemente = count($goal); //Anzal der Elemente ermittlen. -1 da Arrays mit 0 beginnen! ;o) 

//Schleife wird entsprechend der Anzahl der Elemente im Array $zahlen wiederholt 
for($y = 0; $y < $anzahl_elemente; $y++) 
     { 
     //Jedes Element wird einzelen angesprochen und verschoben wenn das linke Element grösser ist als der rechte 
     for($x = 0; $x < $anzahl_elemente; $x++) 
          { 
          //In diesem Beispiel aufsteigend. 
          //Möchte man absteigend sortieren, einfach das grösser Zeichen mit einem kleiner Zeichen tauschen 

		// tauschen wenn:
		// 1. erzielte punkte unterschiedlich  oder
		// 2. erzielte pkte gleich + erzielte pkte>0 + anz. tipp unterschiedlich
          if (($goal[$x][1] < $goal[$x+1][1])
          	 or (($goal[$x][1] == $goal[$x+1][1])
				and ($goal[$x][1] > 0)
				and ($goal[$x][2] > $goal[$x+1][2]))) { 
				    
              //Punkte werden zwischengespeichert... 
              $grosser_wert = $goal[$x][1]; 
              $kleiner_wert = $goal[$x+1][1];
			  //-namen ebenfalls
		  	  $grosser_name = $goal[$x][0]; 
              $kleiner_name = $goal[$x+1][0];
              //-anzahl getippter spiele
              $grosse_anz = $goal[$x][2];
			  $kleine_anz = $goal[$x+1][2];
			  //-joker
              $grosse_joker = $goal[$x][3];
			  $kleine_joker = $goal[$x+1][3];
			  //-team
              $grosse_team = $goal[$x][4];
			  $kleine_team = $goal[$x+1][4];
			  
              //... und anschließen werte vertauschen
              $goal[$x][1] = $kleiner_wert; 
              $goal[$x+1][1] = $grosser_wert; 
              //-namen tauschen
              $goal[$x][0] = $kleiner_name; 
              $goal[$x+1][0] = $grosser_name; 
              //-anzahl getippter spiele tauschen
			  $goal[$x][2] = $kleine_anz;
			  $goal[$x+1][2] = $grosse_anz;              
			  //-joker
			  $goal[$x][3] = $kleine_joker;
			  $goal[$x+1][3] = $grosse_joker; 
  			  //-team
			  $goal[$x][4] = $kleine_team;
			  $goal[$x+1][4] = $grosse_team;   			  
          }//if
	}//for2 
}//for1

/*------------------------------------------------------------------------------
/* aufbereiten für endausgabe des sortierten arrays          
/*----------------------------------------------------------------------------*/

$htmlbody .= '
<body>
<h2>Ergebnis der Auswertung</h2>
<p align="center">('.$zligen.' Ligen zusammengezählt)</p>
	<table align="center" class="auswert">
	   <tr>
	    <th class="auswert">Platz</th><th class="auswert">Name</th>';

//Anzahl getippter spiele ausgeben?		
if ($show_sp_ges == 1) { 
    $htmlbody.= '<th class="auswert"><acronym title="Anzahl Spiele getippt"><u>'.$var_spiele.'</u></acronym></th>'; 
}

//werden jokerpunkte zugelassen? wenn ja, spalte einblenden	 ja oder nein?   
if (($show_joker == 1) and ($joker == 1)) { 
    $htmlbody .= '<th class="auswert"><acronym title="durch Joker dazugewonnene Punkte"><u>'.$var_joker.'</u></acronym></th>'; 
}

//quote richtiger spiele ausgeben ja oder nein?
if ($show_sp_proz == 1) { 
    if ($tippmodus == 0) { // tendenz
	    $htmlbody .= '<th class="auswert"><acronym title="Prozent Spieltipp richtig%"><u>'.$var_prozrichtig.'</u></acronym></th>';
	}
	elseif ($tippmodus == 1) { // ergebnis
		$htmlbody .= '<th class="auswert"><acronym title="Punkte pro Spiel"><u>'.$var_prozrichtig.'</u></acronym></th>';
	}
}

//anzahl tipps ausgeben ja oder nein?		
if ($show_punkte == 1) { 
    $htmlbody.= '<th class="auswert"><acronym title="Anzahl Tipps richtig"><u>'.$var_tippsrichtig.'</u></acronym></th>'; 
}

//team ausgeben ja oder nein?
if (($show_team == 1) and ($team == 1)) { 
    $htmlbody.= '<th class="auswert"><acronym title="Teamzugehörigkeit"><u>'.$var_team.'</u></acronym></th>'; 
}
		
$htmlbody .= '</tr>
';

$platz = 0;
$platz2 = 0;
   
//für html aufbereiten
for ($i = 0; $i <= count($goal)-1; $i++) {
//for ($i = 0; $i <= $anztipper; $i++) {    
    
    // begrenzung anzahl tipper
    if ($i == $showtipper) { 
	    break; 
	} 
	
	//bedingung, die alle nicht-tipper rausfiltert falls gewünscht
	if (($shownichttipper != 0) or ($goal[$i][2] != 0)) {
	
		//wert im array mit username vergleichen -> fett darstellen
	    if (chop($goal[$i][0]) == $username) { 
		    $goal[$i][0] = "<b>". $goal[$i][0] ."</b>"; 
		}
	    
	    $platz++;
	    
	    //ausgabe der platzierung wenn: 
		//  1. punkte ungleich dem vorgänger
	    //  2. punkte gleich, aber anzahl getippter spiele unterschiedlich
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
	    
/*	    //wenn unterschieldliche punkte und getippte spiele -> ausgabe platz
	    if ($goal[$i][1] != $goal[$i-1][1]) {
			$htmlbody .= $platz;
		}
		//prüfen auf gleiche anzahl an getippten spielen:		
		else if ($goal[$i][2] == $goal[$i-1][2]) {
				$htmlbody .= '&nbsp;'; // wenn ja, wird leerzeichen ausgegeben
			 }
			 else { // platz auch ausgeben, wenn punkte gleich aber unterschiedlich viele spiele getippt
			      $htmlbody .= $platz; // wenn nein, wird platz ausgeben
			      }*/
		
		//erfolgsquote in prozent oder in punkte pro spiel
		if ($goal[$i][2] > 0) { 
		    if ($tippmodus == 0) { // tendenz
				$quote = $goal[$i][1] / $goal[$i][2] * 100;
			}
			else { // ergebnis
			     $quote = $goal[$i][1] / $goal[$i][2];
			}
		}
		else { 
		     $quote = 0; 
			 }
		
		$htmlbody .= '</td> <td>'.$goal[$i][0].'</td>';
				
		//gesamten getippter spiele ausgeben?		
		if ($show_sp_ges == 1) { 
		    $htmlbody .= '<td>'.$goal[$i][2].'&nbsp;</td>'; 
		}
		
		//werden jokerpunkte zugelassen? wenn ja, spalte einblenden	 ja oder nein?   
		if (($show_joker == 1) and ($joker == 1)) { 
		    $htmlbody .= '<td>'.$goal[$i][3].'&nbsp;</td>'; 
		}
		
		//quote richtiger spieler ausgeben ja oder nein?
		if ($show_sp_proz == 1) { 
		    $htmlbody .= '<td>'.round($quote, 2).'&nbsp;</td>'; 
		}
		
		//anzahl tipps ausgeben ja oder nein?		
		if ($show_punkte == 1) { 
		    $htmlbody .= '<td>'.$goal[$i][1].'&nbsp;</td>'; 
		}

		//anzahl tipps ausgeben ja oder nein?		
		if (($show_team == 1) and ($team == 1)) { 
		    $htmlbody .= '<td>'.$goal[$i][4].'&nbsp;</td>'; 
		}
		
		$htmlbody .= '</tr>
		';	
						 
		}//end filter nicht-tipper
}//for
   
$htmlbody .= '</table>
<p align="center">Anzahl der Tipper: '. $anztipper;//count($goal);

if ($shownichttipper == 0) { 
    $htmlbody .= '<br><font class="foot">(Nicht-Tipper werden nicht dargestellt)</font>'; 
}

$htmlbody .= '<p align="center">Stand vom '.date("d.m.Y").' - '.date("H:i") .' Uhr</p>
<p align="center"><a href="auswert.php">zurück zur Auswahl</a>&nbsp;|&nbsp;<a href="lmo.php?action=tipp">zurück zum Tippspiel</a></p>';

$zeit2 = microtime(); //stopp //echo "<br>Berechnung dauerte" . ($zeit2-$zeit1);
		
		}
		else /* keine liga gewählt */
		{
		$htmlbody .= '<p align="center"><br><br><font class="fehler">keine Liga gewählt</font><br><br>'.$zurueck.'</p>';
		}//else $zligen>0
    
}//end-else - wenn ok-button geklickt wurde


// ausgabe html code an browser
echo $htmlhead . $htmlbody . $htmlfoot;

clearstatcache();

?>