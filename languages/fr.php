<?php
if (!defined('RAPIDLEECH')) {
  require('../deny.php');
  exit;
}
// Le fichier de langue anglaise
// Vous devriez toujours utiliser ce fichier comme mod&egrave;le pour la traduction

$lang[1]	=	'Acc&egrave;s refus&eacute;';
$lang[2]	=	'Le serveur a refus&eacute; de r&eacute;pondre &agrave; votre demande';
$lang[3]	=	'Vous n\'avez pas entr&eacute; une adresse e-mail valide';
$lang[4]	=	'La taille des pi&egrave;ces n\'est num&eacute;rique';
$lang[5]	=	'Type d\'URL inconnu, <span class=&quot;font-black&quot;> Utilser ubiquement les protocoles <span class=&quot;font-blue&quot;> http </ span> ou <span class=&quot;font-blue&quot;> https </ span> ou <span class=&quot;font-blue&quot;> FTP </ span>';
$lang[6]	=	'Le chemin pour enregistrer ce fichier n\'est pas d&eacute;fini';
$lang[7]	=	'Vous n\'&ecirc;tes pas autoris&eacute; &agrave; t&eacute;l&eacute;charger &agrave; partir de <span class=&quot;font-black&quot;>%1$s (%2$s)</span>';	// %1$s = nom d'h&ocirc;te %2$s = ip de l'h&ocirc;te
$lang[8]	=	'Redirection vers:';
$lang[9]	=	'Actualisation de la liste des fichiers impossible';
$lang[10]	=	'Fichier <b>%1$s</b> (<b>%2$s</b>) sauvegard&eacute;!<br />Dur&eacute;e: <b>%3$s<br />Vitesse moyenne : <b>%4$s KB/s</b><br />';	// %1$s = nom du fichier %2$s = filesize %3$s = time of download %4$s = speed
$lang[11]	=	'<script>mail(&quot;Le fichier a &eacute;t&eacute; envoy&eacute; &agrave; cette adresse<b>%1$s</b>.&quot;, &quot;%2$s&quot;);</ script>';	// %1$s = E-mail address %2$s = nom du fichier
$lang[12]	=	'Erreur lors de l\'envoi du fichier!';
$lang[13]	=	'Revenir au menu principal';
$lang[14]	=	'Connexion perdue, fichier supprim&eacute;.';
$lang[15]	=	'Recharger';
$lang[16]	=	'S\'il vous pla&icirc;t, changer le mode de d&eacute;bogage &agrave; <b>1</b>';
$lang[17]	=	'Le nombre maximum (%1$s) de liens a &eacute;t&eacute; atteint.';	// %1$s = Nombre de liens maximum
$lang[18]	=	'%1$s lien%2$s v&eacute;rifi&eacute;%2$s en %3$s seconde%2$s. (M&eacute;thode: <b>%4$s</b>)';	//%1$s = Nombre de liens%, %2$s = forme plurielle, 3$s = secondes, %4$s = m&eacute;thode pour v&eacute;rifier les liens
$lang[19]	=	's';	// caract&egrave;re de fin d'un pluriel
$lang[20]	=	'Mauvaise adresse du serveur proxy';
$lang[21]	=	'Lien';
$lang[22]	=	'Statut';
$lang[23]	=	'attente';
$lang[24]	=	'URL Invalide';
$lang[25]	=	'Pr&eacute;paration';
$lang[26]	=	'Commenc&eacute;';
$lang[27]	=	'Connexion perdue';
$lang[28]	=	'Termin&eacute;';
$lang[29]	=	'D&eacute;marrer le transfert automatique';
$lang[30]	=	'Frames pas pris en charge, mettre &agrave; jour votre navigateur';
$lang[31]	=	'Ajouter des liens';
$lang[32]	=	'Liens';
$lang[33]	=	'Options';
$lang[34]	=	'Transf&eacute;rer des fichiers';
$lang[35]	=	'Utiliser les param&egrave;tres du Proxy';
$lang[36]	=	'Proxy';
$lang[37]	=	'Utilisateur';
$lang[38]	=	'Mot de passe';
$lang[39]	=	'Utiliser le compte ImagesHack';
$lang[40]	=	'Enregistrer dans';
$lang[41]	=	'Chemin';
$lang[42]	=	'Utiliser le compte Premium';
$lang[43]	=	'Ex&eacute;cuter c&ocirc;t&eacute; serveur';
$lang[44]	=	'D&eacute;lai';
$lang[45]	=	'D&eacute;lai (en secondes)';
$lang[46]	=	'Pas de fichiers ou d\'h&ocirc;tes s&eacute;lectionn&eacute;s pour l\'envoi';
$lang[47]	=	'S&eacute;lectionnez les h&ocirc;tes pour l\'envoi';
$lang[48]	=	'Aucun service de t&eacute;l&eacute;chargement pris en charge!';
$lang[49]	=	'Fen&ecirc;tre d\'envoi';
$lang[50]	=	'Format du lien de sauvegarde';
$lang[51]	=	'Default';
$lang[52]	=	'Tout s&eacute;lectionner';
$lang[53]	=	'Tout d&eacute;s&eacute;lectionner';
$lang[54]	=	'Inverser la s&eacute;lection';
$lang[55]	=	'Nom';
$lang[56]	=	'Taille';
$lang[57]	=	'Aucun fichier trouv&eacute;';
$lang[58]	=	'L&eacute;gende pour le format du lien de sauvegarde : (sensible &agrave; la casse)';
$lang[59]	=	'Le lien pour le t&eacute;l&eacute;chargement';
$lang[60]	=	'Le nom du fichier';
$lang[61]	=	'Style par d&eacute;faut du lien';
$lang[62]	=	'Toutes choses autres que celles indiqu&eacute;es ci-dessus seront trait&eacute;es comme des cha&icirc;nes, actuellement le format multi-ligne est impossible, une nouvelle ligne sera ins&eacute;r&eacute;e pour chaque lien.';
$lang[63]	=	'Envoi du fichier %1$s &agrave; %2$s';	// %1$s = nom du fichier, %2$s = nom du fichier h&ocirc;te
$lang[64]	=	'Le fichier %1$s n\'existe pas.';	// %1$s = nom du fichier
$lang[65]	=	'Le fichier %1$s n\'est pas lisible par le script.';	// %1$s = nom du fichier
$lang[66]	=	'Taille du fichier trop grande pour envoyer &agrave; l\'h&ocirc;te.';
$lang[67]	=	'Service de t&eacute;l&eacute;chargement non autoris&eacute;';
$lang[68]	=	'Lien de t&eacute;l&eacute;chargement';
$lang[69]	=	'Supprimer le lien';
$lang[70]	=	'Stat-Link';
$lang[71]	=	'Admin-Link';
$lang[72]	=	'User-ID';
$lang[73]	=	'upload FTP';
$lang[74]	=	'Mot de passe';
$lang[75]	=	'rapidleech PlugMod - Envoyez les liens';
$lang[76]	=	'<div class=&quot;linktitle&quot;>Liens de t&eacute;l&eacute;chargement pour <strong>%1$s</strong> - <span class=&quot;bluefont&quot;>Taille : <strong>%2$s</strong></span></div>';	// %1$s = nom de fichier, %2$s = taille de fichier
$lang[77]	=	'Termin&eacute;';
$lang[78]	=	'Retour';
$lang[79]	=	'La connexion avec le serveur %1$s n\'a pu &ecirc;tre &eacute;tablie.';	// %1$s = nom du serveur FTP
$lang[80]	=	'Nom d\'utilisateur et/ou mot de passe incorrect.';
$lang[81]	=	'Connect&eacute; &agrave;: <b>%1$s</ b >...';	// %1$s = nom du serveur FTP
$lang[82]	=	'Le type de fichier %1$s est interdit au t&eacute;l&eacute;chargement';	// %1$s = type de fichier
$lang[83]	=	'Fichier <b>%1$s</ b>, Taille <b>%2$s</ b >...';	// %1$s = nom de fichier, %2$s = taille de fichier
$lang[84]	=	'Erreur lors de la r&eacute;cup&eacute;ration du lien';
$lang[85]	=	'Le texte pass&eacute; au compteur est une chaine de caract&egrave;res !';
$lang[86]	=	'ERREUR : S\'il vous pla&icirc;t, activer JavaScript.';
$lang[87]	=	'S\'il vous pla&icirc;t, patienter <b>%1$s</ b> secondes ...';	// %1$s = nombre de secondes
$lang[88]	=	'Connection &agrave; %1$s, port : %2$s impossible';	// %1$s = nom de l'h&ocirc;te, %2$s = port
$lang[89]	=	'Connect&eacute; au proxy: <b>%1$s</ b> sur le port <b>%2$s</ b >...';	// %1$s = Proxy h&ocirc;te, %2$s = Port Proxy
$lang[90]	=	'Connect&eacute; &agrave;: <b>%1$s</ b> sur le port <b>%2$s</ b >...';	// %1$s = h&ocirc;te, %2$s = port
$lang[91]	=	'Aucun ent&ecirc;te re&ccedil;u';
$lang[92]	=	'Il vous est interdit d\'acc&eacute;der &agrave; la page!';
$lang[93]	=	'La page est introuvable!';
$lang[94]	=	'Cette page est soit interdite, soit introuvable!';
$lang[95]	=	'Erreur! Cette adresse est redirig&eacute;e vers [%1$s]';	// %1$s = adresse redirig&eacute;e
$lang[96]	=	'Ce site n&eacute;cessite une autorisation. Pour indiquer un nom d\'utilisateur et un mot de passe d\'acc&egrave;s, il est n&eacute;cessaire d\'utiliser des url similaires &agrave; : <br />http://<b>login:password@</b>www.site.com/fichier.extension';
$lang[97]	=	'Limite de reprise du t&eacute;l&eacute;chargement d&eacute;pass&eacute;e';
$lang[98]	=	'Ce serveur ne supporte pas la reprise du t&eacute;l&eacute;chargement';
$lang[99]	=	'T&eacute;l&eacute;charger';
$lang[100]	=	'Ce compte Premium est d&eacute;j&agrave; utilis&eacute; avec une autre adresse ip.';
$lang[101]	=	'Le fichier %1$s ne peut &ecirc;tre sauvegard&eacute; dans le r&eacute;pertoire %2$s';	// %1$s = file name, %2$s = nom de r&eacute;pertoire
$lang[102]	=	'Essayez de chmoder le dossier &agrave; 777.';
$lang[103]	=	'R&eacute;essayer';
$lang[104]	=	'Fichier';
$lang[105]	=	'Il n\'est pas possible de proc&eacute;der &agrave; un enregistrement dans le fichier %1$s';	// %1$s = nom de fichier
$lang[106]	=	'URL non valide ou une erreur inconnue s\'est produite';
$lang[107]	=	'Vous avez atteint la limite pour les utilisateurs &agrave; titre gratuit.';
$lang[108]	=	'La session de t&eacute;l&eacute;chargement a expir&eacute;';
$lang[109]	=	'Code d\'acc&egrave;s erron&eacute;.';
$lang[110]	=	'Vous avez entr&eacute; un mauvais code trop de fois';
$lang[111]	=	'Quota de t&eacute;l&eacute;chargement d&eacute;pass&eacute;';
$lang[112]	=	'Erreur lors de la lecture des donn&eacute;es';
$lang[113]	=	'Erreur lors de l\'envoi des donn&eacute;es';
$lang[114]	=	'Actif';
$lang[115]	=	'Non disponible';
$lang[116]	=	'Mort';
$lang[117]	=	'Vous avez besoin de charger/activer l\'extension cURL (http://www.php.net/cURL) ou vous pouvez d&eacute;finir $fgc = 1 dans config.php.';
$lang[118]	=	'Curl est activ&eacute;';
$lang[119]	=	'PHP version 5 est recommand&eacute;, m&ecirc;me s\'il n\'est pas obligatoire';
$lang[120]	=	'V&eacute;rifier que Safe Mode est d&eacute;sactiv&eacute; car le script ne peut pas fonctionner avec Safe Mode activ&eacute;';
$lang[121]	=	'Envoi du fichier <b>%1$s</ b>';	// %1$s = nom du fichier
$lang[122]	=	'D&eacute;coupe inutile, envoi d\'un message unique';
$lang[123]	=	'D&eacute;coupage en morceaux de %1$s';	// %1$s = taille du morceau
$lang[124]	=	'M&eacute;thode';
$lang[125]	=	'Envoi du morceau <b>%1$s</ b>';	// %1$s = num&eacute;ro du morceau
$lang[126]	=	'D&eacute;coupe inutile, envoi d\'un message unique';
$lang[127]	=	'Fichier h&ocirc;te non trouv&eacute;';
$lang[128]	=	'Impossible de cr&eacute;er le fichier hosts';
$lang[129]	=	'heures';	// Pluriel
$lang[130]	=	'Heure';
$lang[131]	=	'minutes';	// Pluriel
$lang[132]	=	'minute';
$lang[133]	=	'secondes';	// Pluriel
$lang[134]	=	'seconde';
$lang[135]	=	'getCpuUsage () : chemin d\'acc&egrave;s inaccessible ou fichier STAT non valide';
$lang[136]	=	'Charge CPU';
$lang[137]	=	'Une erreur s\'est produite';
$lang[138]	=	'S&eacute;lectionner au moins un fichier.';
$lang[139]	=	'Courriels';
$lang[140]	=	'Envoyer';
$lang[141]	=	'Supprimer les soumissions r&eacute;ussies';
$lang[142]	=	'D&eacute;couper en morceaux';
$lang[143]	=	'Taille des morceaux';
$lang[144]	=	'<b>%1$s</ b> - Adresse de courriel invalide.';	// %1$s = adresse e-mail
$lang[145]	=	'Le fichier <b>%1$s</ b> est introuvable!';	// %1$s = nom du fichier
$lang[146]	=	'Mise &agrave; jour de la liste des fichiers impossible !';
$lang[147]	=	'La suppression de fichier est d&eacute;sactiv&eacute;e';
$lang[148]	=	'Supprimer les fichiers';
$lang[149]	=	'Oui';
$lang[150]	=	'Non';
$lang[151]	=	'Fichier <b>%1$s</ b> supprim&eacute;';	// %1$s = nom du fichier
$lang[152]	=	'Erreur lors de la suppression du fichier <b>%1$s</ b> !';	// %1$s = nom du fichier
$lang[153]	=	'H&ocirc;te';
$lang[154]	=	'Port';
$lang[155]	=	'R&eacute;pertoire';
$lang[156]	=	'Supprimer le fichier source apr&egrave;s le transfert';
$lang[157]	=	'Copier les fichiers';
$lang[158]	=	'D&eacute;placer des fichiers';
$lang[159]	=	'Impossible de localiser le dossier <b>%1$s</ b>';	// %1$s = nom de r&eacute;pertoire
$lang[160]	=	'Le fichier %1$s a &eacute;t&eacute; transf&eacute;r&eacute; avec succ&egrave;s !';	//  %1$s = nom du fichier
$lang[161]	=	'Temps';
$lang[162]	=	'Vitesse moyenne';
$lang[163]	=	'Transfert du fichier <b>%1$s</ b> impossible !';	// %1$s = nom du fichier
$lang[164]	=	'Courriel';
$lang[165]	=	'Supprimer les soumissions r&eacute;ussies';
$lang[166]	=	'Adresse de courriel invalide';
$lang[167]	=	'S\'il vous pla&icirc;t, s&eacute;lectionnez uniquement les fichiers. crc ou .001 !';
$lang[168]	=	'S\'il vous pla&icirc;t, s&eacute;lectionnez le fichier. CRC !';
$lang[169]	=	'S\'il vous pla&icirc;t, s&eacute;lectionnez le fichier. crc ou .001 !';
$lang[170]	=	'Effectuer une v&eacute;rification CRC ? (recommand&eacute;)';
$lang[171]	=	'Mode de contr&ocirc;le CRC32';
$lang[172]	=	'Utiliser le fichier hash ? (recommand&eacute;)';
$lang[173]	=	'Lire le fichier en m&eacute;moire';
$lang[174]	=	'CRC Faux';
$lang[175]	=	'Supprimer le fichier source si la fusion a r&eacute;ussi';
$lang[176]	=	'Notification';
$lang[177]	=	'La taille du fichier et le CRC32 ne seront pas v&eacute;rifi&eacute;s';
$lang[178]	=	'Le fichier CRC ne peut pas &ecirc;tre lu !';
$lang[179]	=	'Erreur, le fichier de sortie existe d&eacute;j&agrave; <b>%1$s</ b>';	// %1$s = nom du fichier
$lang[180]	=	'Erreur, morceaux manquants ou incomplets';
$lang[181]	=	'Error, le type de fichier %1$s est interdit';	// Type de fichier
$lang[182]	=	'Impossible d\'ouvrir de fichier de destination <b>%1$s</ b>';	// %1$s = nom du fichier
$lang[183]	=	'Erreur lors de l\'&eacute;criture du fichier <b>%1$s</ b> !';	// %1$s = nom du fichier
$lang[184]	=	'La somme de cont&ocirc;le CRC32 est diff&eacute;rente !';
$lang[185]	=	'Le fichier <b>%1$s</ b> a fusionn&eacute; avec succ&egrave;s.';	// %1$s = nom du fichier
$lang[186]	=	'supprim&eacute;';
$lang[187]	=	'non supprim&eacute;';
$lang[188]	=	'Ajouter une extension';
$lang[189]	=	'sans';
$lang[190]	=	'vers';
$lang[191]	=	'Renommer ?';
$lang[192]	=	'Annuler';
$lang[193]	=	'Erreur lors du renommage du fichier <b>%1$s</ b>';	// %1$s = nom du fichier
$lang[194]	=	'Le fichier <b>%1$s</ b> a &eacute;t&eacute; renomm&eacute; en <b>%2$s</ b>';	// %1$s = nom de fichier original, %2$s = nom du fichier renomm&eacute;
$lang[195]	=	'Nom de l\'archive';
$lang[196]	=	'S\'il vous pla&icirc;t, entrer un nom d\'archive!';
$lang[197]	=	'Erreur, l\'archive n\'a pas &eacute;t&eacute; cr&eacute;&eacute;e.';
$lang[198]	=	'Le fichier %1$s a &eacute;t&eacute; compress&eacute;';	// %1$s = nom du fichier
$lang[199]	=	'compress&eacute; dans l\'archives <b>%1$s</ b>';	// %1$s = nom du fichier
$lang[200]	=	'Erreur, l\'archive est vide.';
$lang[201]	=	'Nouveau nom';
$lang[202]	=	'Impossible de renommer le fichier <b>%1$s</ b>!';	// %1$s = nom du fichier
$lang[203]	=	'Supprimer le fichier source en cas de d&eacute;coupe r&eacute;ussie';
$lang[204]	=	'des fichiers et des r&eacute;pertoires';
$lang[205]	=	'D&eacute;zipper';
$lang[206]	=	'Liste de choix du format de vid&eacute;o YouTube';
$lang[207]	=	'Lien &agrave; transf&eacute;rer';
$lang[208]	=	'Referrer';
$lang[209]	=	'Transfert du fichier';
$lang[210]	=	'Utilisateur &amp; Mot de passe (HTTP/FTP)';
$lang[211]	=	'Utilisateur';
$lang[212]	=	'Mot de passe';
$lang[213]	=	'Ajouter un commentaire';
$lang[214]	=	'Options du Plugin';
$lang[215]	=	'D&eacute;sactiver tous les Plugins';
$lang[216]	=	'Liste de choix du format de vid&eacute;o YouTube';
$lang[217]	=	'Lien direct';
$lang[218]	=	'&amp;fmt=';
$lang[219]	=	'Acqu&eacute;rir automatiquement la plus haute qualit&eacute; disponible';
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
$lang[230]	=	'Utilisateur';
$lang[231]	=	'Mot de passe';
$lang[232]	=	'Valeur du Cookie Megaupload.com';
$lang[233]	=	'Utilisateur';
$lang[234]	=	'Utiliser le plugin vBulletin';
$lang[235]	=	'Valeur du Cookie suppl&eacute;mentaire';
$lang[236]	=	'Key=Value';
$lang[237]	=	'Envoyer un fichier par messagerie';
$lang[238]	=	'Courriel';
$lang[239]	=	'D&eacute;couper les fichiers';
$lang[240]	=	'M&eacute;thode';
$lang[241]	=	'Total Commander';
$lang[242]	=	'RFC 2046';
$lang[243]	=	'Taille des morceaux';
$lang[244]	=	'Mo';
$lang[245]	=	'Utiliser les param&egrave;tres du proxy';
$lang[246]	=	'proxy';
$lang[247]	=	'Utilisateur';
$lang[248]	=	'Mot de passe';
$lang[249]	=	'Utiliser le compte Premium';
$lang[250]	=	'Utilisateur';
$lang[251]	=	'Mot de passe';
$lang[252]	=	'Enregistrer dans';
$lang[253]	=	'Chemin';
$lang[254]	=	'Enregistrer les param&egrave;tres';
$lang[255]	=	'Effacer les param&egrave;tres actuels';
$lang[256]	=	'Tout cocher';
$lang[257]	=	'Tout d&eacute;cocher';
$lang[258]	=	'Inverser la s&eacute;lection';
$lang[259]	=	'Afficher';
$lang[260]	=	'T&eacute;l&eacute;charg&eacute;';
$lang[261]	=	'Tout';
$lang[262]	=	'Nom';
$lang[263]	=	'Taille';
$lang[264]	=	'Commentaires';
$lang[265]	=	'Date';
$lang[266]	=	'Aucun fichier trouv&eacute;';
$lang[267]	=	'fonctionne avec';
$lang[268]	=	'Supprimer';
$lang[269]	=	'Mode de d&eacute;bogage';
$lang[270]	=	'Afficher les liens uniquement';
$lang[271]	=	'Supprimer les liens uniquement';
$lang[272]	=	'V&eacute;rifier les liens';
$lang[273]	=	'Chargement en cours ...';
$lang[274]	=	'Traitement en cours, patientez s\'il vous pla&icirc;t ...';
$lang[275]	=	'Espace Serveur';
$lang[276]	=	'Occup&eacute;';
$lang[277]	=	'Espace libre';
$lang[278]	=	'Espace disque';
$lang[279]	=	'CPU';
$lang[280]	=	'Heure du Serveur';
$lang[281]	=	'Heure locale';
$lang[282]	=	'Auto-Supprimer';
$lang[283]	=	'Heures apr&egrave;s le transfert';
$lang[284]	=	'Minutes apr&egrave;s le transfert';
$lang[285]	=	'Action';
$lang[286]	=	'Envoyer';
$lang[287]	=	'Fichier FTP';
$lang[288]	=	'Courriel';
$lang[289]	=	'Courriel en masse';
$lang[290]	=	'D&eacute;couper le fichier';
$lang[291]	=	'Fusionner les fichiers';
$lang[292]	=	'MD5 Hash';
$lang[293]	=	'Zipper les fichiers';
$lang[294]	=	'Fichiers ZIP';
$lang[295]	=	'D&eacute;zipper les fichiers';
$lang[296]	=	'Renommer';
$lang[297]	=	'Renommer en masse';
$lang[298]	=	'Supprimer';
$lang[299]	=	'Liste des liens';
$lang[300]	=	'R&eacute;cup&eacute;rer la page de t&eacute;l&eacute;chargement';
$lang[301]	=	'Entrer';
$lang[302]	=	'ici';
$lang[303]	=	'T&eacute;l&eacute;charger le fichier';
$lang[304]	=	'configs/files.lst n\'est pas accessible en &eacute;criture, s\'il vous pla&icirc;t, assurez-vous qu\'il est chmod&eacute; &agrave; 777';
$lang[305]	=	'&nbsp;est s&eacute;lectionn&eacute; comme chemin de t&eacute;l&eacute;chargement et il n\'est pas accessible en &eacute;criture. S\'il vous pla&icirc;t, mettre le chmod &agrave; 777';
$lang[306]	=	'Fusion du fichier';
$lang[307]	=	'En attente';
$lang[308]	=	'R&eacute;ussi';
$lang[309]	=	'Rat&eacute;';
$lang[310]	=	'Vous pourriez voir des avertissements sans qu\'ils soient activ&eacute;s';
$lang[311]	=	'Vous pourriez ne pas &ecirc;tre en mesure d\'activer les statistiques du serveur';
$lang[312]	=	'Votre serveur peut ne pas &ecirc;tre en mesure de supporter les fichiers de 2 Go';
$lang[313]	=	'Rapidleech Checker Script';
$lang[314]	=	'fsockopen';
$lang[315]	=	'memory_limit';
$lang[316]	=	'safe_mode';
$lang[317]	=	'curl';
$lang[318]	=	'allow_url_fopen';
$lang[319]	=	'PHP Version - ';
$lang[320]	=	'allow_call_time_pass_reference';
$lang[321]	=	'passthru';
$lang[322]	=	'Fonctions Espace Disque';
$lang[323]	=	'Apache version -';
$lang[324]	=	'Mauvaise adresse de Proxy indiqu&eacute;e';
$lang[325]	=	'Le fichier a &eacute;t&eacute; enregistr&eacute; avec succ&egrave;s !';
$lang[326]	=	'Enregistrer les notes';
$lang[327]	=	'Notes';
$lang[328]	=	'Actions D&eacute;sactiv&eacute;es';
$lang[329]	=	'Main Window';
$lang[330]	=	'Settings';
$lang[331]	=	'Server Files';
$lang[332]	=	'Link Checker';
$lang[333]	=	'Plugins';
$lang[334]	=	'Auto Transload';
$lang[335]	=	'Auto Upload';
?>
