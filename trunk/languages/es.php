<?php
if (!defined('RAPIDLEECH')) {
 require('../deny.php');
 exit;
}
// The Spanish language file
// Need to verify context of: 50, 58, 139

$lang[1] = 'Acceso denegado';
$lang[2] = 'El servidor se ha negado a cumplir con tu solicitud';
$lang[3] = 'No haz escrito una direcci&oacute;n de correo electr&oacute;nico v&aacute;lida';
$lang[4] = 'El tama&ntilde;o de las piezas no es un n&uacute;mero';
$lang[5] = 'URL de tipo desconocida, <span class="font-black">Solo se puede usar protocolo <span class="font-blue">http</span> o <span class="font-blue">https</span> o <span class="font-blue">ftp</span></span>';
$lang[6] = 'Ruta de acceso no se especifica para guardar este archivo';
$lang[7] = 'No se te permite descargar desde <span class="font-black">%1$s (%2$s)</span>'; // %1$s = nombre host %2$s = ip del host
$lang[8] = 'Redireccionando a:';
$lang[9] = 'No se pudo actualizar la lista de archivos';
$lang[10] = 'Archivo <b>%1$s</b> (<b>%2$s</b>) ¡Guardado!<br />Tiempo: <b>%3$s</b><br />Velocidad Promedio: <b>%4$s KB/s</b><br />'; // %1$s = nombre de archivo %2$s = tamaño de archivo %3$s = tiempo de descarga %4$s = velocidad
$lang[11] = '<script>mail("El archivo fue enviado a esta direcci&oacute;n<b>%1$s</b>.", "%2$s");</script>'; // %1$s = dirección de E-mail %2$s = nombre de archivo
$lang[12] = '¡Error al enviar el archivo!';
$lang[13] = 'Volver al inicio';
$lang[14] = 'Conexi&oacute;n perdida, archivo borrado.';
$lang[15] = 'Actualizar';
$lang[16] = 'Por favor, cambia el modo de depuraci&oacute;n a <b>1</b>';
$lang[17] = 'El N&uacute;mero M&aacute;ximo (%1$s) de enlaces ha sido alcanzado.'; // %1$s = número máximo de enlaces
$lang[18] = '%1$s Enlace%2$s verificados en %3$s segundos. (M&eacute;todo: <b>%4$s</b>)'; // %1$s = Number of links %2$s = Plural form %3$s = seconds %4$s = method for checking links
$lang[19] = 's'; // End of a plural
$lang[20] = 'Direcci&oacute;n del servidor proxy incorrecta';
$lang[21] = 'Enlace';
$lang[22] = 'Estado';
$lang[23] = 'Esperando';
$lang[24] = 'URL no v&aacute;lida';
$lang[25] = 'Preparando';
$lang[26] = 'Iniciado';
$lang[27] = 'Conexi&oacute;n perdida';
$lang[28] = 'Terminado';
$lang[29] = 'Iniciar Transferencia autom&aacute;tica';
$lang[30] = 'Frames no soportados, actualice su navegador';
$lang[31] = 'A&ntilde;adir enlaces';
$lang[32] = 'Enlaces';
$lang[33] = 'Opciones';
$lang[34] = 'Transload archivos';
$lang[35] = 'Usar proxy';
$lang[36] = 'Proxy';
$lang[37] = 'Usuario';
$lang[38] = 'Contrase&ntilde;a';
$lang[39] = 'Usar cuenta de Imageshack';
$lang[40] = 'Guardar en';
$lang[41] = 'Ruta';
$lang[42] = 'Usar Cuenta Premium';
$lang[43] = 'Ejecutar en el servidor';
$lang[44] = 'Tiempo de retardo';
$lang[45] = 'Retardo (en segundos)';
$lang[46] = 'No hay archivos o hosts seleccionados para subir';
$lang[47] = 'Selecciona Hosts para Subida';
$lang[48] = '¡Ning&uacute;n Servicio de Subida Soportado!';
$lang[49] = 'Ventanas de subida';
$lang[50] = 'Formato de guardado de enlace';
$lang[51] = 'Predeterminado';
$lang[52] = 'Seleccionar todos';
$lang[53] = 'Deseleccionar todos';
$lang[54] = 'Invertir Selecci&oacute;n';
$lang[55] = 'Nombre';
$lang[56] = 'Tama&ntilde;o';
$lang[57] = 'No se encontraron archivos';
$lang[58] = 'Texto para formato de guardado de enlaces: (sensible a may&uacute;sculas y min&uacute;sculas)';
$lang[59] = 'El enlace para la descarga ';
$lang[60] = 'El nombre del archivo';
$lang[61] = 'Estilo predeterminado del enlace';
$lang[62] = 'Todo excepto los indicados anteriormente ser&aacute;n tratados como texto, no puedes hacer formato multi l&iacute;nea ahora, una nueva linea ser&aacute; insertada por cada enlace.';
$lang[63] = 'Subiendo archivo %1$s a %2$s'; // %1$s = nombre de archivo %2$s = nombre de host para el archivo
$lang[64] = 'El archivo %1$s no existe.'; // %1$s = nombre de archivo
$lang[65] = 'El archivo %1$s no puede ser le&iacute;do por el script.'; // %1$s = nombre de archivo
$lang[66] = 'Tama&ntilde;o del archivo demasiado grande para subir al host.';
$lang[67] = 'Servicio de subida no permitido';
$lang[68] = 'Enlace de descarga';
$lang[69] = 'Enlance para eliminar';
$lang[70] = 'Enlace de estado';
$lang[71] = 'Enlace de admin';
$lang[72] = 'ID de Usuario';
$lang[73] = 'Subida por FTP';
$lang[74] = 'Contrase&ntilde;a';
$lang[75] = 'Rapidleech PlugMod - Enlaces de Subida';
$lang[76] = 'Subir class="linktitle"><div Enlaces para <strong>%1$s</strong> - <span class="bluefont"> Tama&ntilde;o: <strong>%2$s</strong> </span> </div> '; //%1$s = nombre de archivo %2$s = tama&ntilde;o de archivo 
$lang[77] = 'Terminado';
$lang[78] = 'Regresar';
$lang[79] = 'No se pudo establecer una conecci&oacute;n con el servidor %1$s.'; // %1$s = nombre de servidor FTP
$lang[80] = 'Nombre de usuario y/o contrase&ntilde;a incorrectos';
$lang[81] = 'Conectado a: <b>%1$s</b>...'; // %1$s = nombre del servidor FTP
$lang[82] = 'No se permite descargar el tipo de archivo %1$s'; // %1$s = Tipo de archivo
$lang[83] = 'Archivo <b>%1$s</b>, Tama&ntilde;o <b>%2$s</b>...'; // %1$s = nombre de archivo %2$s = tamaño del archivo
$lang[84] = 'Error obteniendo el enlace';
$lang[85] = '¡Texto pasado como contador no es un n&uacute;mero!';
$lang[86] = 'ERROR: Por favor, activa JavaScript.';
$lang[87] = 'Por favor espera <b>%1$s</b> segundos...'; // %1$s = número de segundos
$lang[88] = 'No fue posible conectarse a %1$s en el puerto %2$s'; // %1$s = nombre de host %2$s = puerto
$lang[89] = 'Conectado al proxy: <b>%1$s</b> en el puerto <b>%2$s</b>...'; // %1$s = host del proxy %2$s = puerto del proxy
$lang[90] = 'Conectado a: <b>%1$s</b> en el puerto <b>%2$s</b>...'; // %1$s = host %2$s = puerto
$lang[91] = 'no se recivi&oacute; encabezado';
$lang[92] = '¡No tienes permiso de acceder a la p&aacute;gina!';
$lang[93] = '¡La p&aacute;gina no fue encontrada!';
$lang[94] = '¡La p&aacute;gina fue prohibida o no encontrada!';
$lang[95] = '¡Error! ha sido redireccionado a [%1$s]'; // %1$s = dirección de redirección
$lang[96] = 'Este sitio requiere autorizaci&oacute;n. Para indicar el nombre de usuario y contrase&ntilde;a use una url como esta:<br />http://<b>login:password@</b>www.sitio.com/archivo.exe';
$lang[97] = 'Resume superado el l&iacute;mite ';
$lang[98] = 'Este servidor no soporta reanudar';
$lang[99] = 'Descargar';
$lang[100] = 'Esta cuenta premium ya est&aacute; en uso con otra ip.';
$lang[101] = 'El archivo %1$s no se puede guardar en el directorio %2$s'; // %1$s = nombre de archivo %2$s = nombre de directorio
$lang[102] = 'Prueba de la carpeta a chmod 777.';
$lang[103] = 'Intentar de nuevo';
$lang[104] = 'Archivo';
$lang[105] = 'No es posible crear un registro en el archivo %1$s'; // %1$s = nombre de archivo
$lang[106] = 'URL no v&aacute;lida o se ha producido un error desconocido';
$lang[107] = 'Ha alcanzado el l&iacute;mite para usarios Gratuitos.';
$lang[108] = 'La sesi&oacute;n de descarga ha expirado';
$lang[109] = 'C&oacute;digo de acceso incorrecto.';
$lang[110] = 'Ha introducido un c&oacute;digo incorrecto demasiadas veces';
$lang[111] = 'L&iacute;mite de descarga superado';
$lang[112] = 'Error al LEER Datos';
$lang[113] = 'Error al ENVIAR datos';
$lang[114] = 'Activo';
$lang[115] = 'No disponible';
$lang[116] = 'Muerto';
$lang[117] = 'Necesitas cargar/activar la extensi&oacute;n cURL (http://www.php.net/cURL) o puedes establecer $fgc = 1 en config.php.';
$lang[118] = 'CURL est&aacute; activado';
$lang[119] = 'Se recomienda PHP versi&oacute;n 5, aunque no es obligatorio';
$lang[120] = 'Compruebe si el modo safe mode esta desactivado pues el script no puede funcionar con safe mode activado';
$lang[121] = 'Enviando archivo <b>%1$s</b>'; // %1$s = nombre de archivo
$lang[122] = 'No hay necesidad de dividir, Env&iacute;a un solo correo';
$lang[123] = 'Partiendo en partes de %1$s'; // %1$s = tama&ntilde;o de las partes
$lang[124] = 'M&eacute;todo';
$lang[125] = 'Enviando parte <b>%1$s</b>'; //%1$s = n&uacute;mero de parte
$lang[126] = 'No hay necesidad de dividir, Env&iacute;a un solo correo';
$lang[127] = 'No se encontr&oacute; el archivo de host';
$lang[128] = 'No se puede crear el archivo de hosts';
$lang[129] = 'horas'; // Plural
$lang[130] = 'hora';
$lang[131] = 'minutos'; // Plural
$lang[132] = 'minuto';
$lang[133] = 'segundos'; // Plural
$lang[134] = 'segundo';
$lang[135] = 'getCpuUsage (): no se puede acceder a la ruta de STAT o el archivo STAT no es v&aacute;lido';
$lang[136] = 'carga de CPU';
$lang[137] = 'Se ha producido un error';
$lang[138] = 'Selecciona al menos un archivo.';
$lang[139] = 'Emails';
$lang[140] = 'Enviar';
$lang[141] = 'Eliminar env&iacute;os exitosos';
$lang[142] = 'Dividir en partes';
$lang[143] = 'Tama&ntilde;o de las partes';
$lang[144] = '<b>%1$s</b> - Direcci&oacute;n de E-mail no v&aacute;lida'; // %1$s = dirección de e-mail
$lang[145] = 'Archivo <b>%1$s</b> ¡no encontrado!'; // %1$s = nombre de archivo
$lang[146] = 'No se pudo actualizar la lista de archivos';
$lang[147] = '¡La eliminaci&oacute;n de archivos est&aacute; desactivada!';
$lang[148] = 'Eliminar archivos';
$lang[149] = 'S&iacute;';
$lang[150] = 'No';
$lang[151] = 'Archivo <b>%1$s</b> Borrado'; // %1$s = nombre de archivo
$lang[152] = 'Error al borrar el archivo <b>%1$s</b>!'; // %1$s = nombre de archivo
$lang[153] = 'Host';
$lang[154] = 'Puerto';
$lang[155] = 'Directorio';
$lang[156] = 'Borrar archivo de origen despu&eacute;s de subida exitosa';
$lang[157] = 'Copiar archivos';
$lang[158] = 'Mover archivos';
$lang[159] = 'No se puede encontrar el directorio <b>%1$s</b>'; // %1$s nombre del directorio
$lang[160] = 'Archivo %1$s ¡subido correctamente!'; // %1$s = nombre de archivo
$lang[161] = 'Tiempo';
$lang[162] = 'Velocidad promedio';
$lang[163] = 'No ha sido posible subir el archivo <b>%1$s</b>!'; // %1$s = nombre de archivo
$lang[164] = 'Email';
$lang[165] = 'Borrar env&iacute;os exitosos';
$lang[166] = 'Direcci&oacute;n de E-mail no v&aacute;lida';
$lang[167] = '¡Por favor selecciona solamente el archivo .crc o .001!';
$lang[168] = '¡Por favor selecciona el archivo .crc!';
$lang[169] = '¡Por favor selecciona el archivo .crc o .001!';
$lang[170] = 'Realizar verificaci&oacute;n de CRC? (recomendado)';
$lang[171] = 'Modo de verificaci&oacute;n CRC32';
$lang[172] = 'Usar hash_file (Recomendado)';
$lang[173] = 'Leer archivo a la memoria';
$lang[174] = 'crc falso';
$lang[175] = 'Borrar archivos de origen despu&eacute;s de unirlos correctamente';
$lang[176] = 'Nota:';
$lang[177] = 'El tama&ntilde;o de archivo y el crc32 no se verificar&aacute;n';
$lang[178] = '¡No es sposible leer el archivo .crc!';
$lang[179] = 'Error, el archivo de salida ya existe <b>%1$s</b>';  // %1$s = nombre de archivo
$lang[180] = 'Error, partes faltantes o incompletas';
$lang[181] = 'Error, el tipo de archivo %1$s est&aacute; prohibido';  // Tipo de archivo
$lang[182] = 'No es posible abrir el archivo de destino <b>%1$s</b>'; // %1$s = nombre de archivo
$lang[183] = 'Error al escribir el archivo <b>%1$s</b>!'; // %1$s = nombre de archivo
$lang[184] = '¡La verificaci&oacute;n de CRC32 no coincide!';
$lang[185] = 'Archivo <b>%1$s</b> unido correctamente'; // %1$s = nombre de archivo
$lang[186] = 'borrado';
$lang[187] = 'no borrado';
$lang[188] = 'A&ntilde;adir extensi&oacute;n';
$lang[189] = 'sin';
$lang[190] = 'a';
$lang[191] = '¿Renombrar?';
$lang[192] = 'Cancelar';
$lang[193] = 'Error al renombrar el archivo <b>%1$s</b>'; // %1$s = nombre de archivo
$lang[194] = 'El archivo <b>%1$s</b> ha sido renombrado a <b>%2$s</b>';   // %1$s = nombre original del archivo %2$s = nombre nuevo del archivo
$lang[195] = 'Nombre de archivo';
$lang[196] = '¡Por favor introduce un nombre de archivo!';
$lang[197] = 'Error, el archivo no ha sido creado.';
$lang[198] = 'El archivo %1$s fue empaquetado'; // %1$s = nombre de archivo
$lang[199] = 'Empaquetado en el archivo <b>%1$s</b>';        // %1$s = nombre de archivo
$lang[200] = 'Error, el archivo est&aacute; vacio.';
$lang[201] = 'Nuevo nombre';
$lang[202] = 'No se ha podido renombrar el archivo <b>%1$s</b>!';       // %1$s = nombre de archivo
$lang[203] = 'Eliminar archivos origen despu&eacute;s de dividir correctamente';
$lang[204] = 'archivos y directorios';
$lang[205] = 'Descomprimir';
$lang[206] = 'Youtube Video Formato Selector';
$lang[207]	=	'Link to Transload';
$lang[208]	=	'Referrer';
$lang[209]	=	'Transload File';
$lang[210]	=	'User & Pass (HTTP/FTP)';
$lang[211]	=	'User';
$lang[212]	=	'Pass';
$lang[213]	=	'Add Comments';
$lang[214]	=	'PluginOptions';
$lang[215]	=	'Disable All Plugins';
$lang[216]	=	'YouTube Video Format Selector';
$lang[217]	=	'Direct Link';
$lang[218]	=	'&fmt=';
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

?>
