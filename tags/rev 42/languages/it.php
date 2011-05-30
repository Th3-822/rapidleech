<?php
if (!defined('RAPIDLEECH')) {
  require('../deny.php');
  exit;
}
// file per la lingua italiana

$lang[1]	=	'Accesso Negato';
$lang[2]	=	'Il server ha rifiutato di eseguire la tua richiesta';
$lang[3]	=	'Non hai introdotto un indirizzo e-mail valido';
$lang[4]	=	'La dimensione delle parti non &eacute; numerico';
$lang[5]	=	'Tipo URL Sconosciuto, <span class="font-black">Usa Solo Il Protocollo<span class="font-blue">http</span> oppure <span class="font-blue">https</span> oppure <span class="font-blue">ftp</span></span>';
$lang[6]	=	'Non &eacute; stata specificata un percorso per questo file';
$lang[7]	=	'Non hai il permesso di scaricare da <span class="font-black">%1$s (%2$s)</span>';	// %1$s = nome del server %2$s = ip del server
$lang[8]	=	'Reindirizzato a:';
$lang[9]	=	'Non posso aggiornare la lista dei files';
$lang[10]	=	'File <b>%1$s</b> (<b>%2$s</b>) Salvato!<br />Tempo: <b>%3$s</b><br />Velocit&agrave media: <b>%4$s KB/s</b><br />';	// %1$s = nome del file %2$s = dimensione %3$s = tempo di download %4$s = velocit&agrave
$lang[11]	=	'<script>mail("Il file &eacute; stato inviato a questo indirizzo<b>%1$s</b>.", "%2$s");</script>';	// %1$s = indirizzo E-mail  %2$s = nome del file
$lang[12]	=	'Errore nell\'invio del file!';
$lang[13]	=	'Torna alla pagina principale';
$lang[14]	=	'Connessione persa, file cancellato.';
$lang[15]	=	'Ricarica';
$lang[16]	=	'Per favore cambia la modalit&agrave debugger a <b>1</b>';
$lang[17]	=	'Il numero massimo di indirizzi (%1$s) &eacute; stato raggiunto.';	// %1$s = Numero massimo di indirizzi
$lang[18]	=	'%1$s Indirizzi%2$s controllati in %3$s secondi. (Metodo: <b>%4$s</b>)';	// %1$s = Numero di indirizzi %2$s = Forma plurale %3$s = secondi %4$s = metodo per controllare gli indirizzi
$lang[19]	=	's';	// End of a plural
$lang[20]	=	'Indirizzo del server proxy errato';
$lang[21]	=	'Indirizzo';
$lang[22]	=	'Stato';
$lang[23]	=	'Attendere';
$lang[24]	=	'URL non valido';
$lang[25]	=	'Sto iniziando';
$lang[26]	=	'Inizio';
$lang[27]	=	'Conessione persa';
$lang[28]	=	'Finito';
$lang[29]	=	'Inizio auto Trasferimento';
$lang[30]	=	'Frames not supporteti, aggiorna il browser';
$lang[31]	=	'Aggiungi gli indirizzi';
$lang[32]	=	'Indirizzi';
$lang[33]	=	'Opzioni';
$lang[34]	=	'Trasferisci i files';
$lang[35]	=	'Usa i dati di configurazione del proxy';
$lang[36]	=	'Proxy';
$lang[37]	=	'Nome utente';
$lang[38]	=	'Password';
$lang[39]	=	'Usa l\'account imageshack';
$lang[40]	=	'Salva a';
$lang[41]	=	'Percorso';
$lang[42]	=	'Usa l\'Account Premium';
$lang[43]	=	'Esegui Lato Server';
$lang[44]	=	'Tempo di ritardo';
$lang[45]	=	'Ritardo (in secondi)';
$lang[46]	=	'Nessun files o server sezionati per l\'upload';
$lang[47]	=	'Seleziona il server per l\'upload';
$lang[48]	=	'Servizio per l\'upload non supportato!';
$lang[49]	=	'Finestra dell\'upload';
$lang[50]	=	'Formato degli indirizzi salvati';
$lang[51]	=	'Default';
$lang[52]	=	'seleziona tutto';
$lang[53]	=	'Deseleziona tutto';
$lang[54]	=	'Inverti la selezione';
$lang[55]	=	'Nome';
$lang[56]	=	'Dimensione';
$lang[57]	=	'Files non trovati';
$lang[58]	=	'Legenda per il formato dei link salvati: (Sensibile al carattere)';
$lang[59]	=	'L\'indirizzo per il download';
$lang[60]	=	'Il nome del file';
$lang[61]	=	'Stile degli indirizzi predeterminato';
$lang[62]	=	'Qualsiasi aggiunta sopra sar&agrave trattata come testo, non puoi fare il formato multilinea , una nuova linea sar&agrave inserita per ogni indirizzo.';
$lang[63]	=	'Upload file %1$s to %2$s';	// %1$s = nome del file %2$s = nome del server
$lang[64]	=	'Il file %1$s non esiste.';	// %1$s = nome del file
$lang[65]	=	'Il file %1$s non &eacute; leggibile dallo script.';	// %1$s = nome del file
$lang[66]	=	'La dimensione del file &eacute; troppo grande per caricarlo su questo server.';
$lang[67]	=	'Servizio di upload non permesso';
$lang[68]	=	'Indirizzo di download';
$lang[69]	=	'Indirizzo di cancellazione';
$lang[70]	=	'Indirizzo di stato';
$lang[71]	=	'Indirizzo di amministrazione';
$lang[72]	=	'ID-UTENTE';
$lang[73]	=	'Upload FTP';
$lang[74]	=	'Password';
$lang[75]	=	'Rapidleech PlugMod - Indirizzi Upload';
$lang[76]	=	'<div class="linktitle">Indirizzi di upload <strong>%1$s</strong> - <span class="bluefont">Dimensione: <strong>%2$s</strong></span></div>';	// %1$s = nome del file %2$s = dimensione file
$lang[77]	=	'Eseguito';
$lang[78]	=	'Torna indietro';
$lang[79]	=	'Non posso stabilire una connessione con il server %1$s.';		// %1$s = nome del server FTP
$lang[80]	=	'Errato username e/o password.';
$lang[81]	=	'Connesso a: <b>%1$s</b>...';	// %1$s = nome del server FTP
$lang[82]	=	'Il tipo di file %1$s &eacute; vietato di essere scaricato';	// %1$s = File type
$lang[83]	=	'File <b>%1$s</b>, Dimensione <b>%2$s</b>...';	// %1$s = nome del file %2$s = dimensione file
$lang[84]	=	'Errore nel ricevere l\'indirizzo';
$lang[85]	=	'Il testo passato come contatore &eacute; di tipo stringa!';
$lang[86]	=	'ERRORE: Per piacere abilita JavaScript.';
$lang[87]	=	'Per piacere attendi <b>%1$s</b> secondi...';	// %1$s = numberp di secondi
$lang[88]	=	'Non posso connettermi a %1$s at port %2$s';	// %1$s = nome server %2$s = porta
$lang[89]	=	'Connesso al proxy: <b>%1$s</b> alla porta <b>%2$s</b>...';	// %1$s = Server proxy %2$s = Porta proxy
$lang[90]	=	'Connesso a: <b>%1$s</b> alla porta <b>%2$s</b>...';	// %1$s = server %2$s = porta
$lang[91]	=	'Nessuna intestazione rivevuta';
$lang[92]	=	'Non hai il permesso di accedere alla pagina!';
$lang[93]	=	'La pagina non &eacute; stata trovata!';
$lang[94]	=	'La pagina era vietata o inesistente!';
$lang[95]	=	'Errore! Esso &eacute; reindirizzato a [%1$s]';	// %1$s = nuovo indirizzo
$lang[96]	=	'Questo sito richiede l\'autorizzazione. Per inviare nome utente e Password bisogna usare il seguente formato:<br />http://<b>login:password@</b>www.sito.com/file.exe';
$lang[97]	=	'Il limite di resume &eacute; stato oltrepassato';
$lang[98]	=	'Questo server non supporta il resume';
$lang[99]	=	'Download';
$lang[100]	=	'Questo account premium &eacute; gi&agrave in uso da un\'altro ip.';
$lang[101]	=	'Il file %1$s non pu&ograve essere salvato nella directory %2$s';	// %1$s = nome del file %2$s = nome directory
$lang[102]	=	'Prova a cambiare il chmod della cartella a 777.';
$lang[103]	=	'Ritenta';
$lang[104]	=	'File';
$lang[105]	=	'Non &eacute; possibile creare un registro nel file %1$s';	// %1$s = nome del file
$lang[106]	=	'URL non valido o &eacute; avvenuto un errore sconosciuto';
$lang[107]	=	'Hai raggiunto il limite come utente Free.';
$lang[108]	=	'La sessione di download &eacute; scaduta';
$lang[109]	=	'Codice d\'accesso errato.';
$lang[110]	=	'Hai introdotto un codice errato troppe volte';
$lang[111]	=	'Limite di download oltrepassato';
$lang[112]	=	'Errore Nella Lettura Dei Dati';
$lang[113]	=	'Errore Nell\'Invio Dei Dati';
$lang[114]	=	'Attivo';
$lang[115]	=	'Non disponibile';
$lang[116]	=	'Inattivo';
$lang[117]	=	'Hai bisogno di caricare/attivare l\'estensione cURL (http://www.php.net/cURL) oppure puoi settare \'fgc\' => 1 in config.php.';
$lang[118]	=	'cURL &eacute; attivo';
$lang[119]	=	'&Eacute; raccomandata la versione 5 di PHP sebbene non sia obbligatoria';
$lang[120]	=	'Verifica se l\'opzione safe mode &eacute; disattivata poich&eacute; lo script non funziona con safe mode attivo';
$lang[121]	=	'Invio file <b>%1$s</b>';	// %1$s = nome del file
$lang[122]	=	'Non hai bisogno di dividere, Invia una sigola mail';
$lang[123]	=	'Sto dividendo in %1$s dimensione di ogni porzione';	// %1$s = dimensione porzione
$lang[124]	=	'Metodo';
$lang[125]	=	'Sto inviando la porzione <b>%1$s</b>';	//%1$s = numero porzioni
$lang[126]	=	'Non hai bisogno di dividere, Invia una sigola mail';
$lang[127]	=	'Nessun file server trovato';
$lang[128]	=	'Non posso creare il file dei server';
$lang[129]	=	'ore';	// Plural
$lang[130]	=	'ora';
$lang[131]	=	'minuti';	// Plural
$lang[132]	=	'minuto';
$lang[133]	=	'secondi';	// Plural
$lang[134]	=	'secondo';
$lang[135]	=	'getCpuUsage(): non posso accedere alla directory STAT o il file STAT non &eacute; valido';
$lang[136]	=	'Carico CPU';
$lang[137]	=	'&Eacute; avvenuto un errore';
$lang[138]	=	'Seleziona almeno un file.';
$lang[139]	=	'Emails';
$lang[140]	=	'Invio';
$lang[141]	=	'Cancella invii con successo';
$lang[142]	=	'Dividi in parti';
$lang[143]	=	'Dimnsione parti';
$lang[144]	=	'<b>%1$s</b> - Indirizzo E-mail errato.';	// %1$s = indirizzo email
$lang[145]	=	'File <b>%1$s</b> non trovato!';	// %1$s = nome del file
$lang[146]	=	'Non posso aggiornare la lista dei file!';
$lang[147]	=	'Cancellazione dei file disabilitata';
$lang[148]	=	'Cancella i files';
$lang[149]	=	'Si';
$lang[150]	=	'No';
$lang[151]	=	'File <b>%1$s</b> Cancellato';	// %1$s = nome del file
$lang[152]	=	'Errore nella cancellazione del file <b>%1$s</b>!';	// %1$s = nome del file
$lang[153]	=	'Server';
$lang[154]	=	'Porta';
$lang[155]	=	'Percorso';
$lang[156]	=	'Cancella il file sorgente dopo l\'upload con successo';
$lang[157]	=	'Salva i dati di FTP';
$lang[158]	=	'Eliminare i dati di FTP';
$lang[159]	=	'Non trovo il percorso della cartella <b>%1$s</b>';	// %1$s = nome percorso
$lang[160]	=	'File %1$s caricato con successo!';	// %1$s = nome del file
$lang[161]	=	'Tempo';
$lang[162]	=	'Velocit&agrave media';
$lang[163]	=	'Non posso caricare il file <b>%1$s</b>!';	// %1$s = nome del file
$lang[164]	=	'Email';
$lang[165]	=	'Cancella inviati con successo';
$lang[166]	=	'Indirizzo E-mail non valido';
$lang[167]	=	'Per piacere seleziona solo con estensione .crc oppure .001!';
$lang[168]	=	'Per piacere seleziona il file .crc !';
$lang[169]	=	'Per piacere seleziona il file con estensione .crc oppure .001!';
$lang[170]	=	'Esegui un controllo CRC? (Raccomandato)';
$lang[171]	=	'modalit&agrave controllo CRC32';
$lang[172]	=	'Usa l\'hash_file (Raccomandato)';
$lang[173]	=	'Leggi il file alla memoria';
$lang[174]	=	'crc alterato';
$lang[175]	=	'Cancella il file sorgente dopo l\'unione con successo';
$lang[176]	=	'Avviso';
$lang[177]	=	'La dimensione del file e il crc32 non sono stati controllati';
$lang[178]	=	'Non posso leggere il file .crc!';
$lang[179]	=	'Errore, Il file gi&agrave esiste <b>%1$s</b>';	// %1$s = nome del file
$lang[180]	=	'Errore, parti non trovate o incomplete';
$lang[181]	=	'Errore, Il tipo del file %1$s non &eacute; permesso';	// Filetype
$lang[182]	=	'Non &eacute; possibile aprire il file di destinazione <b>%1$s</b>';	// %1$s = nome del file
$lang[183]	=	'Errore scrivendo il file <b>%1$s</b>!';	// %1$s = nome del file
$lang[184]	=	'CRC32 checksum errato!';
$lang[185]	=	'File <b>%1$s</b> Unito con successo';	// %1$s = nome del file
$lang[186]	=	'cancellato';
$lang[187]	=	'non cancellato';
$lang[188]	=	'Aggiungi estensione';
$lang[189]	=	'senza';
$lang[190]	=	'a';
$lang[191]	=	'Rinomina?';
$lang[192]	=	'Cancella';
$lang[193]	=	'Errore nel rinominare il file <b>%1$s</b>';	// %1$s = nome del file
$lang[194]	=	'File <b>%1$s</b> &eacute; stato rinominato a <b>%2$s</b>';	// %1$s = nome file originale %2$s = nome file rinominato
$lang[195]	=	'Nome Archivio';
$lang[196]	=	'Per piacere introduci il nome dell\'archivio!';
$lang[197]	=	'Errore l\'archivio non &eacute; stato creato.';
$lang[198]	=	'File %1$s era impacchettato';	// %1$s = nome del file
$lang[199]	=	'Impacchettato nell\'archivio <b>%1$s</b>';	// %1$s = nome del file
$lang[200]	=	'Errore, l\'archivio &eacute; vuoto.';
$lang[201]	=	'Nuovo nome';
$lang[202]	=	'Non posso rinominare il file <b>%1$s</b>!';	// %1$s = nome del file
$lang[203]	=	'Cancella il file sorgente dopo la divisione con successo';
$lang[204]	=	'files e cartelle';
$lang[205]	=	'Unzip';
$lang[206]	=	'YouTube Selettore Formato Video';
$lang[207]	=	'Indirizzo di Trasferimento';
$lang[208]	=	'Riferimento';
$lang[209]	=	'Transferisci il File';
$lang[210]	=	'Utente &amp; Password (HTTP/FTP)';
$lang[211]	=	'Utente';
$lang[212]	=	'Password';
$lang[213]	=	'Aggiungi commenti';
$lang[214]	=	'Opzioni dei plugins';
$lang[215]	=	'Disabilita tutti i plugins';
$lang[216]	=	'YouTube Selettore Formato Video';
$lang[217]	=	'Indirizzo Diretto';
$lang[218]	=	'&amp;fmt=';
$lang[219]	=	'Richiedi automaticamente la miglior qualit&agrave disponibile';
$lang[220]	=	'0 [Video: FLV H263 251kbps 320x180 @ 29.896fps | Audio: MP3 64kbps 1ch @ 22.05kHz]';
$lang[221]	=	'5 [Video: FLV H263 251kbps 320x180 @ 29.885fps | Audio: MP3 64kbps 1ch @ 22.05kHz]';
$lang[222]	=	'6 [Video: FLV H263 892kbps 480x270 @ 29.887fps | Audio: MP3 96kbps 1ch @ 44.10kHz]';
$lang[223]	=	'13 [Video: 3GP H263 77kbps 176x144 @ 15.000fps | Audio: AMR 13kbps 1ch @ 8.000kHz]';
$lang[224]	=	'17 [Video: 3GP XVID 55kbps 176x144 @ 12.000fps | Audio: AAC 29kbps 1ch @ 22.05kHz]';
$lang[225]	=	'18 [Video: MP4 H264 505kbps 480x270 @ 29.886fps | Audio: AAC 125kbps 2ch @ 44.10kHz]';
$lang[226]	=	'22 [Video: MP4 H264 2001kbps 1280x720 @ 29.918fps | Audio: AAC 198kbps 2ch @ 44.10kHz]';
$lang[227]	=	'34 [Video: FLV H264 256kbps 320x180 @ 29.906fps | Audio: AAC 62kbps 2ch @ 22.05kHz]';
$lang[228]	=	'35 [Video: FLV H264 831kbps 640x360 @ 29.942fps | Audio: AAC 107kbps 2ch @ 44.10kHz]';
$lang[229]	=	'ImageShack&reg; TorrentService';
$lang[230]	=	'Utente';
$lang[231]	=	'Password';
$lang[232]	=	'Megaupload.com valore del Cookie';
$lang[233]	=	'Utente';
$lang[234]	=	'Usa il Plugin vBulletin';
$lang[235]	=	'Valore da aggiungere al Cookie';
$lang[236]	=	'Chiave=Valore';
$lang[237]	=	'Invia il File all\'Email';
$lang[238]	=	'Email';
$lang[239]	=	'Dividi Files';
$lang[240]	=	'Metodo';
$lang[241]	=	'Total Commander';
$lang[242]	=	'RFC 2046';
$lang[243]	=	'Dimensione Parti';
$lang[244]	=	'MB';
$lang[245]	=	'Usa la configurazione proxy';
$lang[246]	=	'Proxy';
$lang[247]	=	'Utente';
$lang[248]	=	'Password';
$lang[249]	=	'Usa l\'account Premium';
$lang[250]	=	'Utente';
$lang[251]	=	'Password';
$lang[252]	=	'Salva a';
$lang[253]	=	'Percorso';
$lang[254]	=	'Salva la configurazione';
$lang[255]	=	'Cancella la configurazione corrente';
$lang[256]	=	'Seleziona tutti';
$lang[257]	=	'Deseleziona tutti';
$lang[258]	=	'Inverti la selezione';
$lang[259]	=	'Visualizza';
$lang[260]	=	'Downloads Completi';
$lang[261]	=	'Tutto';
$lang[262]	=	'Nome';
$lang[263]	=	'Dimensione';
$lang[264]	=	'Commenti';
$lang[265]	=	'Data';
$lang[266]	=	'Files non trovati';
$lang[267]	=	'Funziona con';
$lang[268]	=	'Cancella';
$lang[269]	=	'Modalit&agrave Debug';
$lang[270]	=	'Visualizza solo indirizzi';
$lang[271]	=	'Cancella solo indirizzi';
$lang[272]	=	'Controlla indirizzi';
$lang[273]	=	'Sto caricando...';
$lang[274]	=	'Sto eseguendo, per piacere attendi...';
$lang[275]	=	'Spazio nel server';
$lang[276]	=	'In Uso';
$lang[277]	=	'Spazio libero';
$lang[278]	=	'Spazio del disco';
$lang[279]	=	'CPU';
$lang[280]	=	'Orario nel server';
$lang[281]	=	'Orario locale';
$lang[282]	=	'Auto-Cancella';
$lang[283]	=	'Ore Dopo Il Trasferimento';
$lang[284]	=	'Minuti Dopo Il Trasferimento';
$lang[285]	=	'Azione';
$lang[286]	=	'Upload';
$lang[287]	=	'FTP File';
$lang[288]	=	'E-Mail';
$lang[289]	=	'E-mail Multiple';
$lang[290]	=	'Dividi Files';
$lang[291]	=	'Unisci Files';
$lang[292]	=	'MD5 Hash';
$lang[293]	=	'Impacchetta Files';
$lang[294]	=	'ZIP Files';
$lang[295]	=	'Unzip Files';
$lang[296]	=	'Rinomina';
$lang[297]	=	'Rinomine multiple';
$lang[298]	=	'Cancella';
$lang[299]	=	'Lista indirizzi';
$lang[300]	=	'Sto ricevendo la pagina di download';
$lang[301]	=	'Introduci';
$lang[302]	=	'qui';
$lang[303]	=	'Download File';
$lang[304]	=	'configs/files.lst non &eacute; scrivibile, assicurati che il chmod &eacute; 777';
$lang[305]	=	'&nbsp;&eacute; selezionato come tuo percorso di download ed &eacute; non scrivibile. Per piacere setta il chmod a 777';
$lang[306]	=	'Sto unendo il File';
$lang[307]	=	'Attendi';
$lang[308]	=	'Passato';
$lang[309]	=	'Fallito';
$lang[310]	=	'Puoi vedere gli avvisi se questo non &eacute; settato on';
$lang[311]	=	'Non hai la possibilit&agrave di settare on le statistiche sul server';
$lang[312]	=	'Il tuo server non supporta file pi&ugrave grandi di 2 GB';
$lang[313]	=	'Rapidleech Script di verifica';
$lang[314]	=	'fsockopen';
$lang[315]	=	'memory_limit';
$lang[316]	=	'safe_mode';
$lang[317]	=	'cURL';
$lang[318]	=	'allow_url_fopen';
$lang[319]	=	'PHP Versione - ';
$lang[320]	=	'allow_call_time_pass_reference';
$lang[321]	=	'passthru';
$lang[322]	=	'Funzioni Sullo Spazio Del Disco';
$lang[323]	=	'Versione Apache - ';
$lang[324]	=	'Indirizzo proxy introdotto errato';
$lang[325]	=	'File salvato con successo!';
$lang[326]	=	'Salva Le Note';
$lang[327]	=	'Note';
$lang[328]	=	'Azione Disattivata';
$lang[329]	=	'Main Window';
$lang[330]	=	'Settings';
$lang[331]	=	'Server Files';
$lang[332]	=	'Link Checker';
$lang[333]	=	'Plugins';
$lang[334]	=	'Auto Transload';
$lang[335]	=	'Auto Upload';
$lang[336]	=	'Dimensioni del file &egrave; limitata a ';
$lang[337]	=	'File Size Limit: ';
$lang[338]	=	'Rar Files';
$lang[339]	=	'Unrar Files';
$lang[340]	=	'Error detected';
$lang[341]	=	'click here to expand';
$lang[342]	=	'You can drag window from here';
$lang[343]	=	'Can not find "rar"<br />You may need to download it and extract "rar" to "/rar/" directory';
$lang[344]	=	'Files that will be archived:';
$lang[345]	=	'Archive name:';
$lang[346]	=	'Options:';
$lang[347]	=	'Compresion level:';
$lang[348]	=	'Store';
$lang[349]	=	'Fastest';
$lang[350]	=	'Fast';
$lang[351]	=	'Normal';
$lang[352]	=	'Good';
$lang[353]	=	'Best';
$lang[354]	=	'Create volumes';
$lang[355]	=	'Delete files after archiving';
$lang[356]	=	'Create solid archive';
$lang[357]	=	'Create recovery record';
$lang[358]	=	'Test archive after compression';
$lang[359]	=	'Use password';
$lang[360]	=	'Encrypt file names';
$lang[361]	=	'Set path inside archive';
$lang[362]	=	'Rar';
$lang[363]	=	'Creating archive: <b>%1$s</b>';
$lang[364]	=	'Waiting...';
$lang[365]	=	'Go back to file list';
$lang[366]	=	'<b>Files from %1$s</b>:';
$lang[367]	=	'Can not find "unrar"';
$lang[368]	=	'Pasword needed to list files:';
$lang[369]	=	'Pasword needed to extract files:';
$lang[370]	=	'Error:%1$s';
$lang[371]	=	'Try to list again';
$lang[372]	=	'Unrar selected';
$lang[373]	=	'<b>Extracting files from %1$s</b>:';
$lang[374]	=	'Status:';
$lang[375]	=	'Select text';
$lang[376]  =   'Premium Accounts :';
$lang[377]	=	'37 [Video: MP4 1920×1080 | Audio: AAC 2ch 44.10kHz]';
$lang[378]	=	'Close window';
$lang[379]	=	'Files';
$lang[380]	=	'MD5 change should only be applied to known working formats(i.e. .rar or .zip)<br />Do you want to continue?';
$lang[381]	=	'MD5 of file <b>%1$s</b> changed';	// %1$s = filename
$lang[382]	=	'Error changing the MD5 of the file <b>%1$s</b>!';	// %1$s = filename
$lang[383]	=	'MD5 change';
$lang[384]	=	'Match text';
$lang[385]	=	'Match';
$lang[386]	=	'Ignore case';
$lang[387]	=	'Put each file on a separated archive';
?>