<?php
if (!defined('RAPIDLEECH')) {
  require('../deny.php');
  exit;
}
// The English language file
// You should always use this file as a template for translating

$lang[1]	=	'Access Denied';
$lang[2]	=	'The server has refused to fulfill your request';
$lang[3]	=	'You didn\'t enter a valid e-mail address';
$lang[4]	=	'Size of parts are not numeric';
$lang[5]	=	'Unknown URL Type, <span class="font-black">Only Use <span class="font-blue">http</span> or <span class="font-blue">https</span> or <span class="font-blue">ftp</span> Protocol</span>';
$lang[6]	=	'Path is not specified for saving this file';
$lang[7]	=	'You are not allowed to leech from <span class="font-black">%1$s (%2$s)</span>';	// %1$s = host name %2$s = host ip
$lang[8]	=	'Redirecting to:';
$lang[9]	=	'Couldn\'t update the files list';
$lang[10]	=	'File <b>%1$s</b> (<b>%2$s</b>) Saved!<br />Time: <b>%3$s</b><br />Average Speed: <b>%4$s KB/s</b><br />';	// %1$s = filename %2$s = filesize %3$s = time of download %4$s = speed
$lang[11]	=	'<script>mail("File was sent to this address<b>%1$s</b>.", "%2$s");</script>';	// %1$s = E-mail address %2$s = filename
$lang[12]	=	'Error sending file!';
$lang[13]	=	'Go back to main';
$lang[14]	=	'Connection lost, file deleted.';
$lang[15]	=	'Reload';
$lang[16]	=	'Please change the debug mode to <b>1</b>';
$lang[17]	=	'Maximum No (%1$s) Of links have been reached.';	// %1$s = Number of maximum links
$lang[18]	=	'%1$s Link%2$s checked in %3$s seconds. (Method: <b>%4$s</b>)';	// %1$s = Number of links %2$s = Plural form %3$s = seconds %4$s = method for checking links
$lang[19]	=	's';	// End of a plural
$lang[20]	=	'Bad proxy server address';
$lang[21]	=	'Link';
$lang[22]	=	'Status';
$lang[23]	=	'Waiting';
$lang[24]	=	'Invalid URL';
$lang[25]	=	'Preparing';
$lang[26]	=	'Started';
$lang[27]	=	'Connection lost';
$lang[28]	=	'Finished';
$lang[29]	=	'Start auto Transload';
$lang[30]	=	'Frames not supported, update your browser';
$lang[31]	=	'Add links';
$lang[32]	=	'Links';
$lang[33]	=	'Options';
$lang[34]	=	'Transload files';
$lang[35]	=	'Use Proxy Settings';
$lang[36]	=	'Proxy';
$lang[37]	=	'UserName';
$lang[38]	=	'Password';
$lang[39]	=	'Use Imageshack Account';
$lang[40]	=	'Save To';
$lang[41]	=	'Path';
$lang[42]	=	'Use Premium Account';
$lang[43]	=	'Run Server Side';
$lang[44]	=	'Delay Time';
$lang[45]	=	'Delay (in seconds)';
$lang[46]	=	'No files or hosts selected for upload';
$lang[47]	=	'Select Hosts to Upload';
$lang[48]	=	'No Supported Upload Services!';
$lang[49]	=	'Upload windows';
$lang[50]	=	'Link save format';
$lang[51]	=	'Default';
$lang[52]	=	'Check All';
$lang[53]	=	'Un-Check All';
$lang[54]	=	'Invert Selection';
$lang[55]	=	'Name';
$lang[56]	=	'Size';
$lang[57]	=	'No files found';
$lang[58]	=	'Legend for link saving format: (case sensitive)';
$lang[59]	=	'The link for the download';
$lang[60]	=	'The name of the file';
$lang[61]	=	'Default link style';
$lang[62]	=	'Anything besides the ones stated above will be treated as string, you are unable to do multi line format now, a new line will be inserted for each link.';
$lang[63]	=	'Uploading file %1$s to %2$s';	// %1$s = filename %2$s = file host name
$lang[64]	=	'File %1$s does not exist.';	// %1$s = filename
$lang[65]	=	'File %1$s is not readable by script.';	// %1$s = filename
$lang[66]	=	'Filesize too big to upload to host.';
$lang[67]	=	'Upload service not allowed';
$lang[68]	=	'Download-Link';
$lang[69]	=	'Delete-Link';
$lang[70]	=	'Stat-Link';
$lang[71]	=	'Admin-Link';
$lang[72]	=	'USER-ID';
$lang[73]	=	'FTP upload';
$lang[74]	=	'Password';
$lang[75]	=	'Rapidleech PlugMod - Upload Links';
$lang[76]	=	'<div class="linktitle">Upload Links for <strong>%1$s</strong> - <span class="bluefont">Size: <strong>%2$s</strong></span></div>';	// %1$s = file name %2$s = file size
$lang[77]	=	'DONE';
$lang[78]	=	'Go Back';
$lang[79]	=	'Couldn\'t establish connection with the server %1$s.';		// %1$s = FTP server name
$lang[80]	=	'Incorrect username and/or password.';
$lang[81]	=	'Connected to: <b>%1$s</b>...';	// %1$s = FTP server name
$lang[82]	=	'The filetype %1$s is forbidden to be downloaded';	// %1$s = File type
$lang[83]	=	'File <b>%1$s</b>, Size <b>%2$s</b>...';	// %1$s = file name %2$s = file size
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
$lang[101]	=	'File %1$s cannot be saved in directory %2$s';	// %1$s = file name %2$s = directory name
$lang[102]	=	'Try to chmod the folder to 777.';
$lang[103]	=	'Try again';
$lang[104]	=	'File';
$lang[105]	=	'It is not possible to carry out a record in the file %1$s';	// %1$s = file name
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
$lang[117]	=	'You need to load/activate the cURL extension (http://www.php.net/cURL) or you can set \'fgc\' => 1 in config.php.';
$lang[118]	=	'cURL is enabled';
$lang[119]	=	'PHP version 5 is recommended although it is not obligatory';
$lang[120]	=	'Check if your safe mode is turned off as the script cannot work with safe mode on';
$lang[121]	=	'Sending file <b>%1$s</b>';	// %1$s = filename
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
$lang[145]	=	'File <b>%1$s</b> is not found!';	// %1$s = filename
$lang[146]	=	'Couldn\'t update files list!';
$lang[147]	=	'File deletion is disabled';
$lang[148]	=	'Delete files';
$lang[149]	=	'Yes';
$lang[150]	=	'No';
$lang[151]	=	'File <b>%1$s</b> Deleted';	// %1$s = filename
$lang[152]	=	'Error deleting the file <b>%1$s</b>!';	// %1$s = filename
$lang[153]	=	'Host';
$lang[154]	=	'Port';
$lang[155]	=	'Directory';
$lang[156]	=	'Delete source file after successful upload';
$lang[157]	=	'Save FTP data';
$lang[158]	=	'Delete FTP data';
$lang[159]	=	'Cannot locate the folder <b>%1$s</b>';	// %1$s = directory name
$lang[160]	=	'File %1$s successfully uploaded!';	// %1$s = filename
$lang[161]	=	'Time';
$lang[162]	=	'Average speed';
$lang[163]	=	'Couldn\'t upload the file <b>%1$s</b>!';	// %1$s = filename
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
$lang[179]	=	'Error, Output file already exists <b>%1$s</b>';	// %1$s = filename
$lang[180]	=	'Error, missing or incomplete parts';
$lang[181]	=	'Error, The filetype %1$s is forbidden';	// Filetype
$lang[182]	=	'It is not possible to open destination file <b>%1$s</b>';	// %1$s = filename
$lang[183]	=	'Error writing the file <b>%1$s</b>!';	// %1$s = filename
$lang[184]	=	'CRC32 checksum doesn\'t match!';
$lang[185]	=	'File <b>%1$s</b> successfully merged';	// %1$s = filename
$lang[186]	=	'deleted';
$lang[187]	=	'not deleted';
$lang[188]	=	'Add extension';
$lang[189]	=	'without';
$lang[190]	=	'to';
$lang[191]	=	'Rename?';
$lang[192]	=	'Cancel';
$lang[193]	=	'Error renaming file <b>%1$s</b>';	// %1$s = filename
$lang[194]	=	'File <b>%1$s</b> has been renamed to <b>%2$s</b>';	// %1$s = original filename %2$s = renamed filename
$lang[195]	=	'Archive Name';
$lang[196]	=	'Please enter an archive name!';
$lang[197]	=	'Error the archive has not been created.';
$lang[198]	=	'File %1$s was packed';	// %1$s = filename
$lang[199]	=	'Packed in archive <b>%1$s</b>';	// %1$s = filename
$lang[200]	=	'Error, the archive is empty.';
$lang[201]	=	'New name';
$lang[202]	=	'Couldn\'t rename the file <b>%1$s</b>!';	// %1$s = filename
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
$lang[303]	=	'Download File';
$lang[304]	=	'configs/files.lst is not writable, please make sure it is chmod to 777';
$lang[305]	=	'&nbsp;is selected as your download path and it is not writable. Please chmod it to 777';
$lang[306]	=	'Merging File';
$lang[307]	=	'Waiting';
$lang[308]	=	'Passed';
$lang[309]	=	'Failed';
$lang[310]	=	'You might see warnings without this turned on';
$lang[311]	=	'You might not be able to turn on Server Info';
$lang[312]	=	'Your server might not be able to support files more than 2 GB';
$lang[313]	=	'Rapidleech Checker Script';
$lang[314]	=	'fsockopen';
$lang[315]	=	'memory_limit';
$lang[316]	=	'safe_mode';
$lang[317]	=	'cURL';
$lang[318]	=	'allow_url_fopen';
$lang[319]	=	'PHP Version - ';
$lang[320]	=	'allow_call_time_pass_reference';
$lang[321]	=	'passthru';
$lang[322]	=	'Disk Space Functions';
$lang[323]	=	'Apache Version - ';
$lang[324]	=	'Wrong proxy address entered';
$lang[325]	=	'File successfully saved!';
$lang[326]	=	'Save Notes';
$lang[327]	=	'Notes';
$lang[328]	=	'Actions Disabled';
$lang[329]	=	'Main Window';
$lang[330]	=	'Settings';
$lang[331]	=	'Server Files';
$lang[332]	=	'Link Checker';
$lang[333]	=	'Plugins';
$lang[334]	=	'Auto Transload';
$lang[335]	=	'Auto Upload';
$lang[336]	=	'File size is limited at ';
$lang[337]	=	'File Size Limit: ';
$lang[338]	=	'Rar Files';
$lang[339]	=	'Unrar Files';
$lang[340]	=	'Error detected';
$lang[341]	=	'click here to expand';
$lang[342]	=	'You can drag window from here';
$lang[343]	=	'Can not find "rar"<br />You may need to download it and extract "rar" to "/rar/" directory';
$lang[344]	=	'Files that will be on the archive:';
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
?>