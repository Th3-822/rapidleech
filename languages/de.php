<?php
if (!defined('RAPIDLEECH')) {
  require('../deny.php');
  exit;
}
// Die Deutsche sprach Datei

$lang[1]        =       'Zugriff verweigert';
$lang[2]        =       'Der Server hat deine Anfrage abgelehnt';
$lang[3]        =       'Sie haben eine ung&uuml;ltige Email-Adresse eingegeben';
$lang[4]        =       'Die Gr&ouml;sse der Parts ist nicht numerisch';
$lang[5]        =       'Unbekannter Link-Typ, <span class="font-black">Benutze nur <span class="font-blue">http</span> oder <span class="font-blue">https</span> oder das <span class="font-blue">ftp</span> Protokoll</span>';
$lang[6]        =       'Kein Pfad festgelegt, um diese Datei zu speichern';
$lang[7]        =       'Du bist nicht berechtig von <span class="font-black">%1$s (%2$s)</span> zu leechen';   // %1$s = host name %2$s = host ip
$lang[8]        =       'Weiterleitung nach:';
$lang[9]        =       'Konnte die Datei-Liste nicht aktualisieren';
$lang[10]       =       'Datei <b>%1$s</b> (<b>%2$s</b>) gespeichert!<br />Zeit: <b>%3$s</b><br />Durchschnittsgeschwindigkeit: <b>%4$s KB/s</b><br />';        // %1$s = Dateiname %2$s = Dateigrösse %3$s = time of download %4$s = geschwindigkeit
$lang[11]       =       '<script>mail("Die Datei wurde zu dieser Adresse gesendet<b>%1$s</b>.", "%2$s");</script>';     // %1$s = E-mail addresse %2$s = Dateiname
$lang[12]       =       'Fehler beim Senden der Datei!';
$lang[13]       =       'Zur&uuml;ck zur Startseite';
$lang[14]       =       'Verbindung abgebrochen, Datei gel&ouml;scht.';
$lang[15]       =       'Neu laden';
$lang[16]       =       'Bitte wechsle den Debug Modus zu <b>1</b>';
$lang[17]       =       'Maximale Anzahl (%1$s) von Links wurden erreicht';     // %1$s = Number von maximalen links
$lang[18]       =       '%1$s Links &uuml;berpr&uuml;ft in %3$s Sekunden. (Methode: <b>%4$s</b>)';      // %1$s = Nummer von links %2$s = Plural form %3$s = seconds %4$s = methode zum links überprüfen
$lang[19]       =       'en';   // Ende von plural
$lang[20]       =       'Fehlerhafe Proxy-Adresse';
$lang[21]       =       'Link';
$lang[22]       =       'Status';
$lang[23]       =       'Warten';
$lang[24]       =       'Falscher Link';
$lang[25]       =       'Vorbereitung...';
$lang[26]       =       'Gestartet';
$lang[27]       =       'Verbindung abgebrochen';
$lang[28]       =       'Beendet';
$lang[29]       =       'Start';
$lang[30]       =       'Frames werden nicht unterst&uuml;tzt. Bitte aktualisiere deinen Browser';
$lang[31]       =       'Links hinzuf&uuml;gen';
$lang[32]       =       'Links';
$lang[33]       =       'Optionen';
$lang[34]       =       'Lade Dateien';
$lang[35]       =       'Proxyeinstellungen nutzen';
$lang[36]       =       'Proxy';
$lang[37]       =       'Benutzername';
$lang[38]       =       'Passwort';
$lang[39]       =       'Imageshack Account nutzen';
$lang[40]       =       'Speichern unter';
$lang[41]       =       'Pfad';
$lang[42]       =       'Premium Account nutzen';
$lang[43]       =       'Serverseitig ausf&uuml;hren';
$lang[44]       =       'Aufschubszeit';
$lang[45]       =       'Aufschub (in seconds)';
$lang[46]       =       'Es wurde(n) keine Datei(en) zum uploaden ausgew&auml;hlt';
$lang[47]       =       'W&auml;hle bitte den gew&uuml;nschten Filehoster aus.';
$lang[48]       =       'Kein unterst&uuml;tzter Dienst zum uploaden!';
$lang[49]       =       'Upload-Fenster';
$lang[50]       =       'Link-Speicherformat';
$lang[51]       =       'Standard';
$lang[52]       =       'Markiere alles';
$lang[53]       =       'Demarkiere alles';
$lang[54]       =       'Auswahl umkehren';
$lang[55]       =       'Name';
$lang[56]       =       'Gr&ouml;sse';
$lang[57]       =       'Keine Dateien gefunden';
$lang[58]       =       'Legende f&uuml;r Link Speicherungsformat: (Gross-und Kleinschreibung)';
$lang[59]       =       'Link';
$lang[60]       =       'Name der Datei';
$lang[61]       =       'Standard-Linkstil';
$lang[62]       =       'Alles, ausser die oben angegebenen werden als String behandelt. Du bist momentan nicht in der lage das Multi-line Format auszuw&auml;hlen. F&uuml;r jeden neuen Link wird eine neue Zeile eingef&uuml;gt';
$lang[63]       =       'Uploade Datei %1$s auf %2$s'; // %1$s = Dateiname %2$s = Datei host name
$lang[64]       =       'Datei %1$s existiert nicht.';  // %1$s = Dateiname
$lang[65]       =       'Datei %1$s ist nicht lesbar vom Script.';      // %1$s = Dateiname
$lang[66]       =       'Datei ist zu gross zum uploaden.';
$lang[67]       =       'Upload-Dienst ist nicht verf&uuml;gbar';
$lang[68]       =       'Link';
$lang[69]       =       'L&ouml;sch-Link';
$lang[70]       =       'Stat-Link';
$lang[71]       =       'Admin-Link';
$lang[72]       =       'BENUTZER-ID';
$lang[73]       =       'FTP-Upload';
$lang[74]       =       'Passwort';
$lang[75]       =       'Rapidleech PlugMod - Links Hochladen';
$lang[76]       =       '<div class="linktitle">Lade Link hoch zu <strong>%1$s</strong> - <span class="bluefont">Gr&ouml;sse: <strong>%2$s</strong></span></div>';      // %1$s = Datei name %2$s = Datei Grösse
$lang[77]       =       'ERLEDIGT';
$lang[78]       =       'Zur&uuml;ck';
$lang[79]       =       'Konnte keine Verbindung zum FTP-Server %1$s herstellen.';          // %1$s = FTP server name
$lang[80]       =       'Fehlerhafter Benutzername und/oder Passwort.';
$lang[81]       =       'Verbunden mit: <b>%1$s</b>...';        // %1$s = FTP server name
$lang[82]       =       'Es ist nicht gestattet Dateien des Typs %1$s herunterzuladen';     // %1$s = Datei type
$lang[83]       =       'Datei <b>%1$s</b>, Gr&ouml;sse <b>%2$s</b>...';        // %1$s = Datei name %2$s = Datei Gr&ouml;sse
$lang[84]       =       'Fehler beim erhalten des Download-Links';
$lang[85]       =       'Text bestanden wie der Counter-String!';
$lang[86]       =       'Fehler! Bitte Javascript aktivieren.';
$lang[87]       =       'Bitte warte <b>%1$s</b> Sekunden...';   // %1$s = nummer von seconden
$lang[88]       =       'Verbindung mit %1$s &uuml;ber den Port %2$s nicht m&ouml;glich';      // %1$s = host name %2$s = port
$lang[89]       =       'Verbunden mit Proxy: <b>%1$s</b> &uuml;ber den Port <b>%2$s</b>...';   // %1$s = Proxy host %2$s = Proxy port
$lang[90]       =       'Verbunden mit: <b>%1$s</b> &uuml;ber den Port <b>%2$s</b>...'; // %1$s = host %2$s = port
$lang[91]       =       'Kein Header erhalten';
$lang[92]       =       'Du bist nicht berechtigt auf diese Seite zuzugreifen!';
$lang[93]       =       'Die Seite wurde nicht gefunden!';
$lang[94]       =       'Die Seite wurde nicht gefunden oder ist unzul&auml;ssig!';
$lang[95]       =       'Fehler! es wird weitergeleitet nach [%1$s]';    // %1$s = weiterleitungsadresse
$lang[96]       =       'Diese Seite ben&ouml;tigt eine Authentifizierung. F&uuml;r die Anzeige ist es erforderlich Benutzername und Passwort so anzugeben:<br />http://<b>benutzername:passwort@</b>www.site.com/Datei.exe';
$lang[97]       =       'Wiederaufnahme-Limit erreicht';
$lang[98]       =       'Dieser Server unterst&uuml;tzt keine Wiederaufnahme von Downloads';
$lang[99]       =       'Downloaden';
$lang[100]      =       'Dieser Premium-Account wird schon von einer anderen IP-Adresse genutzt.';
$lang[101]      =       'Datei %1$s konnte nicht im Verzeichnis %2$s gespeichert werden';       // %1$s = Datei name %2$s = Ordner name
$lang[102]      =       'Versuche den Ordner auf chmod 777 zu setzen.';
$lang[103]      =       'Erneut versuchen';
$lang[104]      =       'Datei';
$lang[105]      =       'Es ist nicht m&ouml;glich eine Aufzeichnung in der Datei %1$s durchzuf&uuml;hren';       // %1$s = Datei name
$lang[106]      =       'Falscher Link ... unbekannter Fehler aufgetreten';
$lang[107]      =       'Du hast das Limit f&uuml;r Free-User erreicht.';
$lang[108]      =       'Die Download-Session ist abgelaufen';
$lang[109]      =       'Falscher Zugriffscode.';
$lang[110]      =       'Du hast zu oft einen falschen Code eingegeben';
$lang[111]      =       'Download-Limit ausgesch&ouml;pft';
$lang[112]      =       'Error READ Data';
$lang[113]      =       'Error SEND Data';
$lang[114]      =       'aktiv';
$lang[115]      =       'Nicht verf&uuml;gbar';
$lang[116]      =       'tot';
$lang[117]      =       'Du musst die load/activate cURL-Erweiterung aktivieren (http://www.php.net/cURL). Du kannst aber auch in der config.php \'fgc\' => 1 auf 1 setzen.';
$lang[118]      =       'cURL ist aktiviert';
$lang[119]      =       'PHP5 wird empfohlen, wird aber nicht unbedingt ben&ouml;tigt';
$lang[120]      =       '&Uuml;berpr&uuml;fe, ob der Safe-Mode ausgeschaltet ist. Das Script funktioniert nicht wenn der Safe-Mode eingeschaltet ist';
$lang[121]      =       'Sende Datei <b>%1$s</b>';      // %1$s = Dateiname
$lang[122]      =       'Splitten wird nicht ben&ouml;tigt. Eine einzige Email schicken';
$lang[123]      =       'In %1$s Teile splitten';      // %1$s = part Grösse
$lang[124]      =       'Methode';
$lang[125]      =       'Sende Part <b>%1$s</b>';       //%1$s = teil number
$lang[126]      =       'Splitten wird nicht ben&ouml;tigt. Eine einzige Email schicken';
$lang[127]      =       'Keine hosts Datei gefunden';
$lang[128]      =       'Konnte die hosts Datei nicht erstellen';
$lang[129]      =       'Stunden';      // Plural
$lang[130]      =       'Stunde';
$lang[131]      =       'Minuten';      // Plural
$lang[132]      =       'Minute';
$lang[133]      =       'Sekunden';     // Plural
$lang[134]      =       'Sekunde';
$lang[135]      =       'getCpuUsage(): konnte nicht auf den STAT Pfad zugreifen oder die STAT-Datei ist fehlerhaft';
$lang[136]      =       'CPU-Auslastung';
$lang[137]      =       'Ein Fehler ist aufgetreten';
$lang[138]      =       'W&auml;hle mindestens eine Datei aus.';
$lang[139]      =       'Emails';
$lang[140]      =       'Senden';
$lang[141]      =       'L&ouml;sche erfolgreiche &Uuml;bertragungen';
$lang[142]      =       'Teile in Parts auf';
$lang[143]      =       'Part-Gr&ouml;sse';
$lang[144]      =       '<b>%1$s</b> - Falsche Email-Addresse.';      // %1$s = email addresse
$lang[145]      =       'Datei <b>%1$s</b> wurde nicht gefunden!';      // %1$s = Dateiname
$lang[146]      =       'Konnte die Dateiliste nicht aktualisieren!';
$lang[147]      =       'Dateien l&ouml;schen wurde deaktiviert';
$lang[148]      =       'L&ouml;sche Dateien';
$lang[149]      =       'Ja';
$lang[150]      =       'Nein';
$lang[151]      =       'Datei <b>%1$s</b> gel&ouml;scht';    // %1$s = Dateiname
$lang[152]      =       'Fehler beim l&ouml;schen der Datei <b>%1$s</b>!';      // %1$s = Dateiname
$lang[153]      =       'Host';
$lang[154]      =       'Port';
$lang[155]      =       'Ordner';
$lang[156]      =       'L&ouml;sche Quelldatei nach erfolgreichem Hochladen';
$lang[157]      =       'Speichere FTP-Daten';
$lang[158]      =       'L&ouml;sche FTP-Daten';
$lang[159]      =       'Konnte den Ordner <b>%1$s</b> nicht finden';     // %1$s = ordner name
$lang[160]      =       'Datei %1$s erfolgreich hochgeladen!';  // %1$s = Dateiname
$lang[161]      =       'Zeit';
$lang[162]      =       'Durchschnittsgeschwindigkeit';
$lang[163]      =       'Konnte die Datei <b>%1$s</b> nicht hochladen!';        // %1$s = Dateiname
$lang[164]      =       'Email';
$lang[165]      =       'L&ouml;sche erfolgreiche &Uuml;bertragungen';
$lang[166]      =       'Falsche Email-Addresse';
$lang[167]      =       'Bitte w&auml;hle nur die .crc oder die .001 Datei!';
$lang[168]      =       'Bitte w&auml;hle die .crc Datei!';
$lang[169]      =       'Bitte w&auml;hle nur die .crc oder die .001 Datei!';
$lang[170]      =       'CRC &uuml;berpr&uuml;fen? (empfohlen)';
$lang[171]      =       'CRC32 &Uuml;berpr&uuml;fmodus';
$lang[172]      =       'Benutze hash_file (empfohlen)';
$lang[173]      =       'Lese Datei in den Speicher';
$lang[174]      =       'F&auml;lsche crc';
$lang[175]      =       'L&ouml;sche Quelldatei nach erfolgreichem mergen';
$lang[176]      =       'Notiere';
$lang[177]      =       'Die Dateigr&ouml;sse und crc32 wird nicht &uuml;berpr&uuml;ft';
$lang[178]      =       '.crc-Datei nicht lesbar!';
$lang[179]      =       'Fehler! Die Ziel-Datei existiert bereits <b>%1$s</b>';  // %1$s = Dateiname
$lang[180]      =       'Fehler! Fehlende oder nicht komplette Parts';
$lang[181]      =       'Fehler! Dieser Dateityp %1$s ist verboten!';        // Dateitype
$lang[182]      =       '&Ouml;ffnen der Datei <b>%1$s</b> nicht m&ouml;glich';   // %1$s = Dateiname
$lang[183]      =       'Fehler beim Schreiben der Datei <b>%1$s</b>!'; // %1$s = Dateiname
$lang[184]      =       'CRC32-Checksumme passt nicht!';
$lang[185]      =       'Datei <b>%1$s</b> erfolgreich gemerged';   // %1$s = Dateiname
$lang[186]      =       'gel&ouml;scht';
$lang[187]      =       'nicht gel&ouml;scht';
$lang[188]      =       'Erweiterung hinzuf&uuml;gen';
$lang[189]      =       'ohne';
$lang[190]      =       'zu';
$lang[191]      =       'Umbenennen?';
$lang[192]      =       'Abbrechen';
$lang[193]      =       'Fehler beim umbenennen der Datei <b>%1$s</b>'; // %1$s = Dateiname
$lang[194]      =       'Datei <b>%1$s</b> wurde umbenannt in <b>%2$s</b>';     // %1$s = originaler Dateiname %2$s = Umbenannter Dateiname
$lang[195]      =       'Archivname';
$lang[196]      =       'Bitte einen Archivnamen eingeben!';
$lang[197]      =       'Fehler! Das Archiv wurde nicht erstellt.';
$lang[198]      =       'Datei %1$s wurde gepackt';     // %1$s = Dateiname
$lang[199]      =       'Verpacke Dateien in das Archiv <b>%1$s</b>';    // %1$s = Dateiname
$lang[200]      =       'Fehler! Das Archiv ist leer.';
$lang[201]      =       'Neuer Name';
$lang[202]      =       'Konnte die Datei nicht umbenennen <b>%1$s</b>!';       // %1$s = Dateiname
$lang[203]      =       'L&ouml;sche Quelldatei nach erfolgreichem splitten';
$lang[204]      =       'Dateien und Ordner';
$lang[205]      =       'Unzip';
$lang[206]      =       'YouTube-Format';
$lang[207]      =       'Link';
$lang[208]      =       'Referrer';
$lang[209]      =       'Datei downloaden';
$lang[210]      =       'Benutzer &amp; Passwort (HTTP/FTP)';
$lang[211]      =       'Benutzer';
$lang[212]      =       'Passwort';
$lang[213]      =       'Kommentar hinzuf&uuml;gen';
$lang[214]      =       'Erweiterungs-Optionen';
$lang[215]      =       'Deaktiviere alle Add-Ons';
$lang[216]      =       'YouTube-Format';
$lang[217]      =       'Direktlink';
$lang[218]      =       '&amp;fmt=';
$lang[219]      =       'Automatisch die bestm&ouml;gliche Qualit&auml;t nehmen';
$lang[220]      =       '0 [Video: FLV H263 251kbps 320x180 @ 29.896fps | Audio: MP3 64kbps 1ch @ 22.05kHz]';
$lang[221]      =       '5 [Video: FLV H263 251kbps 320x180 @ 29.885fps | Audio: MP3 64kbps 1ch @ 22.05kHz]';
$lang[222]      =       '6 [Video: FLV H263 892kbps 480x270 @ 29.887fps | Audio: MP3 96kbps 1ch @ 44.10kHz]';
$lang[223]      =       '13 [Video: 3GP H263 77kbps 176x144 @ 15.000fps | Audio: AMR 13kbps 1ch @ 8.000kHz]';
$lang[224]      =       '17 [Video: 3GP XVID 55kbps 176x144 @ 12.000fps | Audio: AAC 29kbps 1ch @ 22.05kHz]';
$lang[225]      =       '18 [Video: MP4 H264 505kbps 480x270 @ 29.886fps | Audio: AAC 125kbps 2ch @ 44.10kHz]';
$lang[226]      =       '22 [Video: MP4 H264 2001kbps 1280x720 @ 29.918fps | Audio: AAC 198kbps 2ch @ 44.10kHz]';
$lang[227]      =       '34 [Video: FLV H264 256kbps 320x180 @ 29.906fps | Audio: AAC 62kbps 2ch @ 22.05kHz]';
$lang[228]      =       '35 [Video: FLV H264 831kbps 640x360 @ 29.942fps | Audio: AAC 107kbps 2ch @ 44.10kHz]';
$lang[229]      =       'ImageShack&reg; TorrentDienst';
$lang[230]      =       'Benutzername';
$lang[231]      =       'Passwort';
$lang[232]      =       'Megaupload.com Cookie';
$lang[233]      =       'Benutzer';
$lang[234]      =       'Benutze vBulletin-Erweiterung';
$lang[235]      =       'Zus&auml;tzlicher Cookie';
$lang[236]      =       'Key=Wert';
$lang[237]      =       'Sende Datei per Email';
$lang[238]      =       'Email';
$lang[239]      =       'Teile Dateien';
$lang[240]      =       'Methode';
$lang[241]      =       'Total Commander';
$lang[242]      =       'RFC 2046';
$lang[243]      =       'Part-Gr&ouml;sse';
$lang[244]      =       'MB';
$lang[245]      =       'Benutze Proxy-Einstellungen';
$lang[246]      =       'Proxy';
$lang[247]      =       'Benutzername';
$lang[248]      =       'Passwort';
$lang[249]      =       'Benutze Premium-Account';
$lang[250]      =       'Benutzername';
$lang[251]      =       'Passwort';
$lang[252]      =       'Speichern unter';
$lang[253]      =       'Pfad';
$lang[254]      =       'Einstellungen speichern';
$lang[255]      =       'Aktuelle Einstellungen l&ouml;schen';
$lang[256]      =       'Alles markieren';
$lang[257]      =       'Alles demarkieren';
$lang[258]      =       'Auswahl umkehren';
$lang[259]      =       'Zeige';
$lang[260]      =       'Downloads';
$lang[261]      =       'alles';
$lang[262]      =       'Name';
$lang[263]      =       'Gr&ouml;sse';
$lang[264]      =       'Kommentare';
$lang[265]      =       'Datum';
$lang[266]      =       'Keine Dateien gefunden';
$lang[267]      =       'Funktioniert mit';
$lang[268]      =       'Killt';
$lang[269]      =       'Debug-Modus';
$lang[270]      =       'Nur Links anzeigen';
$lang[271]      =       'Nur Links killen';
$lang[272]      =       'Links &uuml;berpr&uuml;fen';
$lang[273]      =       'Lade...';
$lang[274]      =       'Lade, bitte warten...';
$lang[275]      =       'Server-Speicherplatz';
$lang[276]      =       'Genutzter Speicherplatz';
$lang[277]      =       'Freier Speicherplatz';
$lang[278]      =       'Festplatten-Gr&ouml;sse';
$lang[279]      =       'CPU';
$lang[280]      =       'Server-Zeit';
$lang[281]      =       'Lokale-Zeit';
$lang[282]      =       'Auto-L&ouml;schung';
$lang[283]      =       'Stunden nach Download';
$lang[284]      =       'Minuten nach Download';
$lang[285]      =       'Aktion';
$lang[286]      =       'Auf Filehoster uploaden';
$lang[287]      =       'FTP-Upload';
$lang[288]      =       'An Email senden';
$lang[289]      =       'Massen-Email';
$lang[290]      =       'Datei splitten';
$lang[291]      =       'Dateien zusammenfassen';
$lang[292]      =       'MD5-Hash anzeigen';
$lang[293]      =       'Datei(en) verpacken (tar, tar.gz, tar.bz)';
$lang[294]      =       'Datei(en) verpacken (zip)';
$lang[295]      =       'Datei(en) entpacken (zip)';
$lang[296]      =       'Datei umbenennen';
$lang[297]      =       'Mehrere Dateien umbenennen';
$lang[298]      =       'Datei(en) l&ouml;schen';
$lang[299]      =       'Links auflisten';
$lang[300]      =       'Verbindung wird aufgebaut...';
$lang[301]      =       'Enter';
$lang[302]      =       'Hier';
$lang[303]      =       'Datei herunterladen';
$lang[304]      =       'configs/files ist ist nicht beschreibar, bitte versichere dir ob die Berechtigung chmod 777 ist.';
$lang[305]      =       ' ist als dein Download-Pfad angegeben und der Ordner ist nicht beschreibar. Bitte wechsle chmod zu 777';
$lang[306]      =       'F&uuml;hre Dateien zusammen';
$lang[307]      =       'Wartend';
$lang[308]      =       'Erfolgreich';
$lang[309]      =       'Gescheitert';
$lang[310]      =       'Sie sehen m&ouml;glicherweise Warnungen, ohne diese Funktion eingeschaltet zu haben';
$lang[311]      =       'Diese Info kannst du auf diesem Server evt. nicht ansehen.';
$lang[312]      =       'Dein Server unterst&uuml;tzt m&ouml;glicherweise keine Dateien die gr&ouml;sser als 2GB sind';
$lang[313]      =       'Rapidleech Check-Script';
$lang[314]      =       'fsockopen';
$lang[315]      =       'memory_limit';
$lang[316]      =       'safe_mode';
$lang[317]      =       'cURL';
$lang[318]      =       'allow_url_fopen';
$lang[319]      =       'PHP Version - ';
$lang[320]      =       'allow_call_time_pass_reference';
$lang[321]      =       'durchlaufen';
$lang[322]      =       'Festplattenspeicher funktionen';
$lang[323]      =       'Apache Version - ';
$lang[324]      =       'Falsche Proxyadresse eingegeben';
$lang[325]      =       'Datei erfolgreich Gespeichert!';
$lang[326]      =       'Notizen speichern';
$lang[327]      =       'Notizen';
$lang[328]      =       'Aktion deaktiviert';
$lang[329]      =       'Hauptfenster';
$lang[330]      =       'Einstellungen';
$lang[331]      =       'Dateiverzeichnis';
$lang[332]      =       'Link-Checker ';
$lang[333]      =       'Plugins';
$lang[334]      =       'MULTiDOWNLOAD';
$lang[335]      =       'MULTiUPLOAD';
$lang[336]      =       'Datei-Gr&ouml;sse ist limitiert bis ';
$lang[337]      =       'Datei-Gr&ouml;ssen-Limit: ';
$lang[338]      =       'Datei(en) verpacken (rar)';
$lang[339]      =       'Datei(en) entpacken (rar)';
$lang[340]      =       'Fehler entdeckt';
$lang[341]      =       'Klicken Sie hier, um zu erweitern';
$lang[342]      =       'Fenster verschieben';
$lang[343]      =       'Konnte "rar" nicht finden<br />Eventuell m&uuml;ssen Sie "plusrar" aus dem <a href="http://www.rapidleech.com/index.php/topic/4977-plusrar/"> Rapidleech-Forum</a> laden und in das Verzeichnis "/rar/" kopieren';
$lang[344]      =       'Zu verpackende Dateien:';
$lang[345]      =       'Archivname:';
$lang[346]      =       'Optionen:';
$lang[347]      =       'Kompressions-Level:';
$lang[348]      =       'Speichere';
$lang[349]      =       'Am schnellsten';
$lang[350]      =       'Schnell';
$lang[351]      =       'Normal';
$lang[352]      =       'Gut';
$lang[353]      =       'Am besten';
$lang[354]      =       'Archiv erstellen';
$lang[355]      =       'Dateien nach der Archivierung l&ouml;schen';
$lang[356]      =       'Kompaktes Archiv erstellen';
$lang[357]      =       'Recovery-Volume erstellen';
$lang[358]      =       'Archiv nach der Kompression testen';
$lang[359]      =       'Passwort benutzen';
$lang[360]      =       'Dateinamen verschl&uuml;sseln';
$lang[361]      =       'Pfad innerhalb des Archivs setzen';
$lang[362]      =       'Verpacken';
$lang[363]      =       'Erstelle Archiv: <b>%1$s</b>';
$lang[364]      =       'Warte...';
$lang[365]      =       'Zur&uuml;ck zur Dateiliste';
$lang[366]      =       '<b>Dateien des Archivs %1$s</b>:';
$lang[367]      =       'Konnte "unrar" nicht finden';
$lang[368]      =       'Bitte Passwort eingeben um die Dateien aufzulisten:';
$lang[369]      =       'Bitte Passwort eingeben um die Dateien zu entpacken:';
$lang[370]      =       'Fehler:%1$s';
$lang[371]      =       'Erneut auflisten';
$lang[372]      =       'Auswahl entpacken';
$lang[373]      =       '<b>Entpacke Dateien von %1$s</b>:';
$lang[374]      =       'Status:';
$lang[375]      =       'Ausgew&auml;hlter Text';
$lang[376]  	=  	'Premium Accounts:';
$lang[377]      =       '37 [Video: MP4 1920Ã—1080 | Audio: AAC 2ch 44.10kHz]';
$lang[378]      =       'Fenster schlie&szlig;en';
$lang[379]      =       'Dateien';
$lang[380]      =       'MD5-Hash-Ver&auml;nderungen sollten nur bei getesteten Formaten durchgef&uuml;hrt werden(z.B. .rar oder .zip)<br />M&ouml;chten Sie fortfahren?';
$lang[381]      =       'MD5-Hash der Datei <b>%1$s</b> wurde ver&auml;ndert';     // %1$s = Dateiname
$lang[382]      =       'Fehler beim ver&ouml;ndern von MD5-Hash der Datei <b>%1$s</b>!';       // %1$s = Dateiname
$lang[383]      =       'MD5-Hash ver&auml;ndern';
$lang[384]      =       'Gleichen Text suchen';
$lang[385]      =       'suchen';
$lang[386]      =       'Gro&szlig;- und Kleinschreibung ignorieren';
$lang[387]      =       'Setze jede Datei in ein seperates Archiv';
$lang[388]      =       'OpenSSL';
$lang[389]      =       '44 [Video: WebM 854x480 | Audio: Vorbis 2ch 44.10kHz]';
?>
