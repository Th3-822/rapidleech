<?php
if (!defined('RAPIDLEECH')) {
	require('../deny.php');
	exit;
}
// file per la lingua italiana

$lang[1]	=	'Accesso Negato';
$lang[2]	=	'Il server ha rifiutato di eseguire la tua richiesta';
$lang[3]	=	'Non hai introdotto un indirizzo e-mail valido';
$lang[4]	=	'La dimensione delle parti non è numerico';
$lang[5]	=	'Tipo URL Sconosciuto, <span class="font-black">Usa Solo Il Protocollo<span class="font-blue">http</span> oppure <span class="font-blue">https</span> oppure <span class="font-blue">ftp</span></span>';
$lang[6]	=	'Non è stata specificata un percorso per questo file';
$lang[7]	=	'Non hai il permesso di scaricare da <span class="font-black">%1$s (%2$s)</span>';	// %1$s = nome del server %2$s = ip del server
$lang[8]	=	'Reindirizzato a:';
$lang[9]	=	'Non posso aggiornare la lista dei files';
$lang[10]	=	'File <b>%1$s</b> (<b>%2$s</b>) Salvato!<br />Tempo: <b>%3$s</b><br />Velocità media: <b>%4$s KB/s</b><br />';	// %1$s = nome del file %2$s = dimensione %3$s = tempo di download %4$s = velocità
$lang[11]	=	'<script>mail("Il file è stato inviato a questo indirizzo<b>%1$s</b>.", "%2$s");</script>';	// %1$s = indirizzo E-mail  %2$s = nome del file
$lang[12]	=	'Errore nell\'invio del file!';
$lang[13]	=	'Torna alla pagina principale';
$lang[14]	=	'Connessione persa, file cancellato.';
$lang[15]	=	'Ricarica';
$lang[16]	=	'Per favore cambia la modalità debugger a <b>1</b>';
$lang[17]	=	'Il numero massimo di indirizzi (%1$s) è stato raggiunto.';	// %1$s = Numero massimo di indirizzi
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
$lang[38]	=	'Parola d\'ordine';
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
$lang[51]	=	'Predeterminato';
$lang[52]	=	'Spunta tutto';
$lang[53]	=	'Togli la spunta a tutto';
$lang[54]	=	'Inverti la selezione';
$lang[55]	=	'Nome';
$lang[56]	=	'Dimensione';
$lang[57]	=	'Files non trovati';
$lang[58]	=	'Legenda per il formato dei link salvati: (Sensibile al carattere)';
$lang[59]	=	'L\'indirizzo per il download';
$lang[60]	=	'Il nome del file';
$lang[61]	=	'Stile degli indirizzi predeterminato';
$lang[62]	=	'Qualsiasi aggiunta sopra sarà trattata come testo, non puoi fare il formato multilinea , una nuova linea sarà inserita per ogni indirizzo.';
$lang[63]	=	'Upload file %1$s to %2$s';	// %1$s = nome del file %2$s = nome del server
$lang[64]	=	'Il file %1$s non esiste.';	// %1$s = nome del file
$lang[65]	=	'Il file %1$s non è leggibile dallo script.';	// %1$s = nome del file
$lang[66]	=	'La dimensione del file è troppo grande per caricarlo su questo server.';
$lang[67]	=	'Servizio di upload non permesso';
$lang[68]	=	'Indirizzo di download';
$lang[69]	=	'Indirizzo di cancellazione';
$lang[70]	=	'Indirizzo di stato';
$lang[71]	=	'Indirizzo di amministrazione';
$lang[72]	=	'ID-UTENTE';
$lang[73]	=	'Upload FTP';
$lang[74]	=	'Parola d\'ordine';
$lang[75]	=	'Rapidleech PlugMod - Indirizzi Upload';
$lang[76]	=	'<div class="linktitle">Indirizzi di upload <strong>%1$s</strong> - <span class="bluefont">Dimensione: <strong>%2$s</strong></span></div>';	// %1$s = nome del file %2$s = dimensione file
$lang[77]	=	'DONE';
$lang[78]	=	'Go Back';
$lang[79]	=	'Couldn\'t establish connection with the server %1$s.';		// %1$s = nome del server FTP
$lang[80]	=	'Incorrect username and/or password.';
$lang[81]	=	'Connected to: <b>%1$s</b>...';	// %1$s = nome del server FTP
$lang[82]	=	'The filetype %1$s is forbidden to be downloaded';	// %1$s = File type
$lang[83]	=	'File <b>%1$s</b>, Size <b>%2$s</b>...';	// %1$s = nome del file %2$s = dimensione file
$lang[84]	=	'Error retriving the link';
$lang[85]	=	'Text passed as counter is string!';
$lang[86]	=	'ERROR: Please enable JavaScript.';
$lang[87]	=	'Please wait <b>%1$s</b> seconds...';	// %1$s = number of seconds
$lang[88]	=	'Couldn\'t connect to %1$s at port %2$s';	// %1$s = host name %2$s = port
$lang[89]	=	'Connected to proxy: <b>%1$s</b> at port <b>%2$s</b>...';	// %1$s = Proxy host %2$s = Proxy port
$lang[90]	=	'Connected to: <b>%1$s</b> at port <b>%2$s</b>...';	// %1$s = host %2$s = port
$lang[91]	=	'No header received';
$lang[92]	=	'You are forbidden to access the page!';
$lang[93]	=	'The page was not found!';
$lang[94]	=	'The page was either forbidden or not found!';
$lang[95]	=	'Error! it is redirected to [%1$s]';	// %1$s = redirected address
$lang[96]	=	'This site requires authorization. For the indication of username and password of access it is necessary to use similar url:<br />http://<b>login:password@</b>www.site.com/file.exe';
$lang[97]	=	'Resume limit exceeded';
$lang[98]	=	'This server doesn\'t support resume';
$lang[99]	=	'Download';
$lang[100]	=	'This premium account is already in use with another ip.';
$lang[101]	=	'File %1$s cannot be saved in directory %2$s';	// %1$s = nome del file %2$s = directory name
$lang[102]	=	'Try to chmod the folder to 777.';
$lang[103]	=	'Try again';
$lang[104]	=	'File';
$lang[105]	=	'It is not possible to carry out a record in the file %1$s';	// %1$s = nome del file
$lang[106]	=	'Invalid URL or unknown error occured';
$lang[107]	=	'You have reached the limit for Free users.';
$lang[108]	=	'The download session has expired';
$lang[109]	=	'Wrong access code.';
$lang[110]	=	'You have entered a wrong code too many times';
$lang[111]	=	'Download limit exceeded';
$lang[112]	=	'Error READ Data';
$lang[113]	=	'Error SEND Data';
$lang[114]	=	'Active';
$lang[115]	=	'Unavailable';
$lang[116]	=	'Dead';
$lang[117]	=	'You need to load/activate the cURL extension (http://www.php.net/cURL) or you can set $fgc = 1 in config.php.';
$lang[118]	=	'cURL is enabled';
$lang[119]	=	'PHP version 5 is recommended although it is not obligatory';
$lang[120]	=	'Check if your safe mode is turned off as the script cannot work with safe mode on';
$lang[121]	=	'Sending file <b>%1$s</b>';	// %1$s = nome del file
$lang[122]	=	'No need spliting, Send single mail';
$lang[123]	=	'Spliting into %1$s part size';	// %1$s = part size
$lang[124]	=	'Method';
$lang[125]	=	'Sending part <b>%1$s</b>';	//%1$s = part number
$lang[126]	=	'No need spliting, Send single mail';
$lang[127]	=	'No host file found';
$lang[128]	=	'Cannot create hosts file';
$lang[129]	=	'hours';	// Plural
$lang[130]	=	'hour';
$lang[131]	=	'minutes';	// Plural
$lang[132]	=	'minute';
$lang[133]	=	'seconds';	// Plural
$lang[134]	=	'second';
$lang[135]	=	'getCpuUsage(): couldn\'t access STAT path or STAT file invalid';
$lang[136]	=	'CPU Load';
$lang[137]	=	'An error occured';
$lang[138]	=	'Select at least one file.';
$lang[139]	=	'Emails';
$lang[140]	=	'Send';
$lang[141]	=	'Delete successful submits';
$lang[142]	=	'Split by Parts';
$lang[143]	=	'Parts Size';
$lang[144]	=	'<b>%1$s</b> - Invalid E-mail Address.';	// %1$s = email address
$lang[145]	=	'File <b>%1$s</b> is not found!';	// %1$s = nome del file
$lang[146]	=	'Couldn\'t update files list!';
$lang[147]	=	'File deletion is disabled';
$lang[148]	=	'Delete files';
$lang[149]	=	'Yes';
$lang[150]	=	'No';
$lang[151]	=	'File <b>%1$s</b> Deleted';	// %1$s = nome del file
$lang[152]	=	'Error deleting the file <b>%1$s</b>!';	// %1$s = nome del file
$lang[153]	=	'Host';
$lang[154]	=	'Port';
$lang[155]	=	'Directory';
$lang[156]	=	'Delete source file after successful upload';
$lang[157]	=	'Copy Files';
$lang[158]	=	'Move Files';
$lang[159]	=	'Cannot locate the folder <b>%1$s</b>';	// %1$s = directory name
$lang[160]	=	'File %1$s successfully uploaded!';	// %1$s = nome del file
$lang[161]	=	'Time';
$lang[162]	=	'Average speed';
$lang[163]	=	'Couldn\'t upload the file <b>%1$s</b>!';	// %1$s = nome del file
$lang[164]	=	'Email';
$lang[165]	=	'Delete	successful submits';
$lang[166]	=	'Invalid E-mail Address';
$lang[167]	=	'Please select only the .crc or .001 file!';
$lang[168]	=	'Please select the .crc file!';
$lang[169]	=	'Please select the .crc or .001 file!';
$lang[170]	=	'Perform a CRC check? (recommended)';
$lang[171]	=	'CRC32 check mode';
$lang[172]	=	'Use hash_file (Recommended)';
$lang[173]	=	'Read file to memory';
$lang[174]	=	'Fake crc';
$lang[175]	=	'Delete source file after successful merge';
$lang[176]	=	'Notice';
$lang[177]	=	'The file size and crc32 won\'t be check';
$lang[178]	=	'Can\'t read the .crc file!';
$lang[179]	=	'Error, Output file already exists <b>%1$s</b>';	// %1$s = nome del file
$lang[180]	=	'Error, missing or incomplete parts';
$lang[181]	=	'Error, The filetype %1$s is forbidden';	// Filetype
$lang[182]	=	'It is not possible to open destination file <b>%1$s</b>';	// %1$s = nome del file
$lang[183]	=	'Error writing the file <b>%1$s</b>!';	// %1$s = nome del file
$lang[184]	=	'CRC32 checksum doesn\'t match!';
$lang[185]	=	'File <b>%1$s</b> successfully merged';	// %1$s = nome del file
$lang[186]	=	'deleted';
$lang[187]	=	'not deleted';
$lang[188]	=	'Add extension';
$lang[189]	=	'without';
$lang[190]	=	'to';
$lang[191]	=	'Rename?';
$lang[192]	=	'Cancel';
$lang[193]	=	'Error renaming file <b>%1$s</b>';	// %1$s = nome del file
$lang[194]	=	'File <b>%1$s</b> has been renamed to <b>%2$s</b>';	// %1$s = original filename %2$s = renamed filename
$lang[195]	=	'Archive Name';
$lang[196]	=	'Please enter an archive name!';
$lang[197]	=	'Error the archive has not been created.';
$lang[198]	=	'File %1$s was packed';	// %1$s = nome del file
$lang[199]	=	'Packed in archive <b>%1$s</b>';	// %1$s = nome del file
$lang[200]	=	'Error, the archive is empty.';
$lang[201]	=	'New name';
$lang[202]	=	'Couldn\'t rename the file <b>%1$s</b>!';	// %1$s = nome del file
$lang[203]	=	'Delete source file after successful split';
$lang[204]	=	'files and folders';
$lang[205]	=	'Unzip';
$lang[206]	=	'YouTube Video Format Selector';
$lang[207]	=	'Link to Transload';
$lang[208]	=	'Referrer';
$lang[209]	=	'Transload File';
$lang[210]	=	'User &amp; Pass (HTTP/FTP)';
$lang[211]	=	'User';
$lang[212]	=	'Pass';
$lang[213]	=	'Add Comments';
$lang[214]	=	'PluginOptions';
$lang[215]	=	'Disable All Plugins';
$lang[216]	=	'YouTube Video Format Selector';
$lang[217]	=	'Direct Link';
$lang[218]	=	'&amp;fmt=';
$lang[219]	=	'Auto-get the highest quality format available';
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
$lang[230]	=	'Username';
$lang[231]	=	'Password';
$lang[232]	=	'Megaupload.com Cookie Value';
$lang[233]	=	'user';
$lang[234]	=	'Use vBulletin Plugin';
$lang[235]	=	'Additional Cookie Value';
$lang[236]	=	'Key=Value';
$lang[237]	=	'Send File to Email';
$lang[238]	=	'Email';
$lang[239]	=	'Split Files';
$lang[240]	=	'Method';
$lang[241]	=	'Total Commander';
$lang[242]	=	'RFC 2046';
$lang[243]	=	'Parts Size';
$lang[244]	=	'MB';
$lang[245]	=	'Use Proxy Settings';
$lang[246]	=	'Proxy';
$lang[247]	=	'Username';
$lang[248]	=	'Password';
$lang[249]	=	'Use Premium Account';
$lang[250]	=	'Username';
$lang[251]	=	'Password';
$lang[252]	=	'Save To';
$lang[253]	=	'Path';
$lang[254]	=	'Save Settings';
$lang[255]	=	'Clear Current Settings';
$lang[256]	=	'Check All';
$lang[257]	=	'Un-Check All';
$lang[258]	=	'Invert Selection';
$lang[259]	=	'Show';
$lang[260]	=	'Downloaded';
$lang[261]	=	'Everything';
$lang[262]	=	'Name';
$lang[263]	=	'Size';
$lang[264]	=	'Comments';
$lang[265]	=	'Date';
$lang[266]	=	'No files found';
$lang[267]	=	'Works With';
$lang[268]	=	'Kills';
$lang[269]	=	'Debug Mode';
$lang[270]	=	'Display Links Only';
$lang[271]	=	'Kill Links Only';
$lang[272]	=	'Check Links';
$lang[273]	=	'Loading...';
$lang[274]	=	'Processing, please wait...';
$lang[275]	=	'Server Space';
$lang[276]	=	'In Use';
$lang[277]	=	'Free Space';
$lang[278]	=	'Disk Space';
$lang[279]	=	'CPU';
$lang[280]	=	'Server Time';
$lang[281]	=	'Local Time';
$lang[282]	=	'Auto-Delete';
$lang[283]	=	'Hours After Transload';
$lang[284]	=	'Minutes After Transload';
$lang[285]	=	'Action';
$lang[286]	=	'Upload';
$lang[287]	=	'FTP File';
$lang[288]	=	'E-Mail';
$lang[289]	=	'Mass E-mail';
$lang[290]	=	'Split Files';
$lang[291]	=	'Merge Files';
$lang[292]	=	'MD5 Hash';
$lang[293]	=	'Pack Files';
$lang[294]	=	'ZIP Files';
$lang[295]	=	'Unzip Files';
$lang[296]	=	'Rename';
$lang[297]	=	'Mass Rename';
$lang[298]	=	'Delete';
$lang[299]	=	'List Links';
$lang[300]	=	'Retrieving download page';
$lang[301]	=	'Enter';
$lang[302]	=	'here';
$lang[303]  =   'Download File';
$lang[304]  =   'configs/files.lst is not writable, please make sure it is chmod to 777';
$lang[305]  =   '&nbsp;is selected as your download path and it is not writable. Please chmod it to 777';
$lang[306]  =   'Merging File';
$lang[307]  =   'Waiting';
$lang[308]  =   'Passed';
$lang[309]  =   'Failed';
$lang[310]  =   'You might see warnings without this turned on';
$lang[311]  =   'You might not be able to turn on server stats';
$lang[312]  =   'Your server might not be able to support files more than 2 GB';
$lang[313]  =   'Rapidleech Checker Script';
$lang[314]  =   'fsockopen';
$lang[315]  =   'memory_limit';
$lang[316]  =   'safe_mode';
$lang[317]  =   'cURL';
$lang[318]  =   'allow_url_fopen';
$lang[319]  =   'PHP Version - ';
$lang[320]  =   'allow_call_time_pass_reference';
$lang[321]  =   'passthru';
$lang[322]  =   'Disk Space Functions';
$lang[323]  =   'Apache Version - ';
$lang[324]  =   'Wrong proxy address entered';
$lang[325]  =   'File successfully saved!';
$lang[326]  =   'Save Notes';
$lang[327]  =   'Notes';
$lang[328]  =   'Actions Disabled';

?>
