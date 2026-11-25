# Individuelle Tipp-Auswertung (by Marcus)

Individuelle Auswertung der LMO-Tippspielligen: Der Besucher wählt im Formular **eine oder mehrere Ligen**
aus, danach erscheint eine Tabelle mit der Auswertung nur für diese Ligen (Platz, Tipper, Spiele, Punkte pro Spiel,
Joker, Punkte, optional das Team). So lassen sich z. B. mehrere Wettbewerbe zu einer eigenen Wertung kombinieren.

Autor: Marcus ([BC Erlbach](http://www.bcerlbach.de/)), Version 1.9, hier für **PHP 8** und die aktuelle
LMO-Version überarbeitet.

## Voraussetzungen

- LMO (PHP-8-Version) mit eingerichtetem und ausgewertetem Tippspiel (Addon `tipp`)
- Die Datei `gesamt.aus` (Tipp-Admin: „Tippspiel neu auswerten“). Sie wird nur gelesen, ein Backup ist nicht nötig.

## Installation

1. `lmo-tippauswert_individuell.php` in das **LMO-Hauptverzeichnis** kopieren (dorthin, wo `init.php` liegt).
2. Optional die Einstellungen am Dateianfang anpassen (siehe unten).
3. Aufrufen: `https://deine-domain/lmo/lmo-tippauswert_individuell.php`

Der Pfad zur `gesamt.aus` und der Tippmodus (Tendenz/Ergebnis) werden automatisch aus der Konfiguration des
Tipp-Addons gelesen.

## Bedienung

1. Die gewünschten Ligen anhaken.
2. Auswerten – die Tabelle zeigt nur die Punkte aus den gewählten Ligen.

Ohne Auswahl wird keine Auswertung erzeugt. Eingeloggte Tipper werden in der Tabelle hervorgehoben.

## Einstellungen

Im Block `ab hier können Einstellungen vorgenommen werden` am Dateianfang:

| Einstellung                              | Bedeutung                                              |
|------------------------------------------|--------------------------------------------------------|
| `$fontfamily`, `$fontsize`, `$fontcolor` | Schrift, Größe, Farbe                                  |
| `$fontfehl`, `$headlinefontsize`         | Farbe für Fehlermeldungen, Größe der Überschrift       |
| `$tablehaedercolor`, `$tablebackcolor`, `$tablefontcolor`, `$tablefontsize` | Tabellenfarben und -schrift |
| `$colorplatz1` … `$colorplatz3`          | Hintergrund für Platz 1–3 (leer lassen = keine Farbe)  |
| `$showtipper`                            | Anzahl angezeigter Tipper (`-1` = alle)                |
| `$shownichttipper`                       | Tipper ohne Tipp anzeigen (`1`) oder nicht (`0`)       |
| `$show_sp_ges`, `$show_sp_proz`, `$show_joker`, `$show_punkte`, `$show_team` | Spalten ein-/ausblenden (1/0) |
| `$var_spiele`, `$var_joker`, `$var_tippsrichtig`, `$var_team` | Spaltenüberschriften              |
| `$title`, `$status`                      | Seitentitel / Statusleistentext                        |

Nur für eingeloggte Tipper sichtbar machen: im Quelltext den auskommentierten Block unter
`falls die Auswertung nur eingeloggte User zu Gesicht bekommen sollen` aktivieren.
Unterhalb von `ab hier nichts mehr ändern` sollte nichts angepasst werden.

## Änderungen für PHP 8

- Nicht gesetzte Variablen, Array-Schlüssel und `$_POST`-Werte werden geprüft (`isset`, `??`)
- Sortierung und Schleifen mit Grenzprüfung, Typ-Umwandlungen bei Zahlen aus der Textdatei
- Alle Ausgaben HTML-sicher
- Pfad der Auswertungsdatei und Tippmodus aus der Addon-Konfiguration statt fest eingebaut
- PSR-12-formatiert (php-cs-fixer), UTF-8, LF-Zeilenenden

## Dateien

| Datei                                          | Zweck                                    |
|------------------------------------------------|------------------------------------------|
| `lmo-tippauswert_individuell.php`              | das Auswertungsskript                    |
| `ReadMe_lmo-tippauswert_individuell.txt/.htm`  | ursprüngliche Beschreibung (Version 1.9) |
| `LICENSE`                                      | GNU General Public License               |

## Lizenz

GNU General Public License, Version 2 oder höher – siehe [LICENSE](LICENSE).
Die Urheberhinweise im Quelltext dürfen nicht entfernt oder geändert werden.
