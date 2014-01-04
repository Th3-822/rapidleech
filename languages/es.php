<?php

if (!defined('RAPIDLEECH')) {
    require('../deny.php');
    exit;
}
// The Spanish language file

$lang[1] = 'Acceso denegado';
$lang[2] = 'El servidor se ha negado a cumplir con tu solicitud';
$lang[3] = 'No haz escrito una dirección de correo electrónico válida';
$lang[4] = 'El tamaño de las piezas no es un número';
$lang[5] = 'URL de tipo desconocida, <span class="font-black">Solo se puede usar protocolo <span class="font-blue">http</span> o <span class="font-blue">https</span> o <span class="font-blue">ftp</span></span>';
$lang[6] = 'Ruta de acceso no se especificada para guardar este archivo';
$lang[7] = 'No se te permite descargar desde <span class="font-black">%1$s (%2$s)</span>'; // %1$s = nombre host %2$s = ip del host
$lang[8] = 'Redireccionando a:';
$lang[9] = 'No se pudo actualizar la lista de archivos';
$lang[10] = 'Archivo <b>%1$s</b> (<b>%2$s</b>) ¡Guardado!<br />Tiempo: <b>%3$s</b><br />Velocidad Promedio: <b>%4$s KB/s</b><br />'; // %1$s = nombre de archivo %2$s = tamaño de archivo %3$s = tiempo de descarga %4$s = velocidad
$lang[11] = '<script>mail("El archivo fue enviado a esta dirección<b>%1$s</b>.", "%2$s");</script>'; // %1$s = dirección de E-mail %2$s = nombre de archivo
$lang[12] = '¡Error al enviar el archivo!';
$lang[13] = 'Volver a la página principal';
$lang[14] = 'Conexión perdida, archivo borrado.';
$lang[15] = 'Actualizar';
$lang[16] = 'Por favor, cambia el modo de depuración a <b>1</b>';
$lang[17] = 'El Número Máximo (%1$s) de enlaces ha sido alcanzado.'; // %1$s = número máximo de enlaces
$lang[18] = '%1$s Enlace%2$s verificados en %3$s segundos. (Método: <b>%4$s</b>)'; // %1$s = Number of links %2$s = Plural form %3$s = seconds %4$s = method for checking links
$lang[19] = 's'; // End of a plural
$lang[20] = 'Dirección del servidor proxy incorrecta';
$lang[21] = 'Enlace';
$lang[22] = 'Estado';
$lang[23] = 'Esperando';
$lang[24] = 'URL no válida';
$lang[25] = 'Preparando';
$lang[26] = 'Iniciado';
$lang[27] = 'Conexión perdida';
$lang[28] = 'Terminado';
$lang[29] = 'Iniciar Transferencia automática';
$lang[30] = 'Frames no soportados, actualice su navegador';
$lang[31] = 'Añadir enlaces';
$lang[32] = 'Enlaces';
$lang[33] = 'Opciones';
$lang[34] = 'Transload archivos';
$lang[35] = 'Usar proxy';
$lang[36] = 'Proxy';
$lang[37] = 'Usuario';
$lang[38] = 'Contraseña';
$lang[39] = 'Usar cuenta de Imageshack';
$lang[40] = 'Guardar en';
$lang[41] = 'Ruta';
$lang[42] = 'Usar Cuenta Premium';
$lang[43] = 'Ejecutar en el servidor';
$lang[44] = 'Tiempo de retardo';
$lang[45] = 'Retardo (en segundos)';
$lang[46] = 'No hay archivos o hosts seleccionados para subir';
$lang[47] = 'Selecciona Hosts para Subida';
$lang[48] = '¡Ningún Servicio de Subida Soportado!';
$lang[49] = 'Ventanas de subida';
$lang[50] = 'Formato de guardado de enlace';
$lang[51] = 'Default';
$lang[52] = 'Seleccionar todos';
$lang[53] = 'Deseleccionar todos';
$lang[54] = 'Invertir Selección';
$lang[55] = 'Nombre';
$lang[56] = 'Tamaño';
$lang[57] = 'No se encontraron archivos';
$lang[58] = 'Leyenda para formato de guardado de enlaces: (sensible a mayúsculas y minúsculas)';
$lang[59] = 'El enlace para la descarga ';
$lang[60] = 'El nombre del archivo';
$lang[61] = 'Estilo predeterminado del enlace';
$lang[62] = 'Todo excepto los indicados anteriormente serán tratados como texto, no puedes hacer formato multi línea ahora, una nueva linea será insertada por cada enlace.';
$lang[63] = 'Subiendo archivo %1$s a %2$s'; // %1$s = nombre de archivo %2$s = nombre de host para el archivo
$lang[64] = 'El archivo %1$s no existe.'; // %1$s = nombre de archivo
$lang[65] = 'El archivo %1$s no puede ser leído por el script.'; // %1$s = nombre de archivo
$lang[66] = 'Tamaño del archivo demasiado grande para subir al host.';
$lang[67] = 'Servicio de subida no permitido';
$lang[68] = 'Enlace de descarga';
$lang[69] = 'Enlace para eliminar';
$lang[70] = 'Enlace de estado';
$lang[71] = 'Enlace de admin';
$lang[72] = 'ID de Usuario';
$lang[73] = 'Subida por FTP';
$lang[74] = 'Contraseña';
$lang[75] = 'Rapidleech PlugMod - Enlaces de Subida';
$lang[76] = '<div class="linktitle"> Enlaces para <strong>%1$s</strong> - <span class="bluefont"> Tamaño: <strong>%2$s</strong> </span> </div> '; //%1$s = nombre de archivo %2$s = tamaño de archivo
$lang[77] = 'Terminado';
$lang[78] = 'Regresar';
$lang[79] = 'No se pudo establecer una conexión con el servidor %1$s.'; // %1$s = nombre de servidor FTP
$lang[80] = 'Nombre de usuario y/o contraseña incorrectos';
$lang[81] = 'Conectado a: <b>%1$s</b>...'; // %1$s = nombre del servidor FTP
$lang[82] = 'No se permite descargar el tipo de archivo %1$s'; // %1$s = Tipo de archivo
$lang[83] = 'Archivo <b>%1$s</b>, Tamaño <b>%2$s</b>...'; // %1$s = nombre de archivo %2$s = tamaño del archivo
$lang[84] = 'Error obteniendo el enlace';
$lang[85] = '¡Texto pasado como contador no es un número!';
$lang[86] = 'ERROR: Por favor, activa JavaScript.';
$lang[87] = 'Por favor espera <b>%1$s</b> segundos...'; // %1$s = número de segundos
$lang[88] = 'No fue posible conectarse a %1$s en el puerto %2$s'; // %1$s = nombre de host %2$s = puerto
$lang[89] = 'Conectado al proxy: <b>%1$s</b> en el puerto <b>%2$s</b>...'; // %1$s = host del proxy %2$s = puerto del proxy
$lang[90] = 'Conectado a: <b>%1$s</b> en el puerto <b>%2$s</b>...'; // %1$s = host %2$s = puerto
$lang[91] = 'No se recivió el encabezado';
$lang[92] = '¡No tienes permiso de acceder a la página!';
$lang[93] = '¡La página no fue encontrada!';
$lang[94] = '¡La página fue prohibida o no encontrada!';
$lang[95] = '¡Error! ha sido redireccionado a [%1$s]'; // %1$s = dirección de redirección
$lang[96] = 'Este sitio requiere autorización. Para indicar el nombre de usuario y contraseña use una url como esta:<br />http://<b>login:password@</b>www.sitio.com/archivo.exe';
$lang[97] = 'Resume superado el límite ';
$lang[98] = 'Este servidor no soporta reanudar';
$lang[99] = 'Descargar';
$lang[100] = 'Esta cuenta premium ya está en uso con otra ip.';
$lang[101] = 'El archivo %1$s no se puede guardar en el directorio %2$s'; // %1$s = nombre de archivo %2$s = nombre de directorio
$lang[102] = 'Prueba de la carpeta a chmod 777.';
$lang[103] = 'Intentar de nuevo';
$lang[104] = 'Archivo';
$lang[105] = 'No es posible crear un registro en el archivo %1$s'; // %1$s = nombre de archivo
$lang[106] = 'URL no válida o se ha producido un error desconocido';
$lang[107] = 'Ha alcanzado el límite para usarios Gratuitos.';
$lang[108] = 'La sesión de descarga ha expirado';
$lang[109] = 'Código de acceso incorrecto.';
$lang[110] = 'Ha introducido un código incorrecto demasiadas veces';
$lang[111] = 'Límite de descarga superado';
$lang[112] = 'Error al LEER Datos';
$lang[113] = 'Error al ENVIAR datos';
$lang[114] = 'Activo';
$lang[115] = 'No disponible';
$lang[116] = 'Muerto';
$lang[117] = 'Necesitas cargar/activar la extensión cURL (http://www.php.net/cURL) o puedes establecer \'fgc\' => 1 en config.php.';
$lang[118] = 'CURL está activado';
$lang[119] = 'Se recomienda PHP versión 5, aunque no es obligatorio';
$lang[120] = 'Compruebe si el modo safe mode esta desactivado pues el script no puede funcionar con safe mode activado';
$lang[121] = 'Enviando archivo <b>%1$s</b>'; // %1$s = nombre de archivo
$lang[122] = 'No hay necesidad de dividir, Envía un solo correo';
$lang[123] = 'Partiendo en partes de %1$s'; // %1$s = tamaño de las partes
$lang[124] = 'Método';
$lang[125] = 'Enviando parte <b>%1$s</b>'; //%1$s = número de parte
$lang[126] = 'No hay necesidad de dividir, Envía un solo correo';
$lang[127] = 'No se encontró el archivo de host';
$lang[128] = 'No se puede crear el archivo de hosts';
$lang[129] = 'horas'; // Plural
$lang[130] = 'hora';
$lang[131] = 'minutos'; // Plural
$lang[132] = 'minuto';
$lang[133] = 'segundos'; // Plural
$lang[134] = 'segundo';
$lang[135] = 'getCpuUsage (): no se puede acceder a la ruta de STAT o el archivo STAT no es válido';
$lang[136] = 'Carga de CPU';
$lang[137] = 'Se ha producido un error';
$lang[138] = 'Selecciona al menos un archivo.';
$lang[139] = 'Emails';
$lang[140] = 'Enviar';
$lang[141] = 'Eliminar envíos exitosos';
$lang[142] = 'Dividir en partes';
$lang[143] = 'Tamaño de las partes';
$lang[144] = '<b>%1$s</b> - Dirección de E-mail no válida'; // %1$s = dirección de e-mail
$lang[145] = 'Archivo <b>%1$s</b> ¡no encontrado!'; // %1$s = nombre de archivo
$lang[146] = 'No se pudo actualizar la lista de archivos';
$lang[147] = '¡La eliminación de archivos está desactivada!';
$lang[148] = 'Eliminar archivos';
$lang[149] = 'Sí';
$lang[150] = 'No';
$lang[151] = 'Archivo <b>%1$s</b> Borrado'; // %1$s = nombre de archivo
$lang[152] = 'Error al borrar el archivo <b>%1$s</b>!'; // %1$s = nombre de archivo
$lang[153] = 'Host';
$lang[154] = 'Puerto';
$lang[155] = 'Directorio';
$lang[156] = 'Borrar archivo de origen después de subida exitosa';
$lang[157] = 'Guardar datos FTP';
$lang[158] = 'Borrar datos FTP';
$lang[159] = 'No se puede encontrar el directorio <b>%1$s</b>'; // %1$s nombre del directorio
$lang[160] = 'Archivo %1$s ¡subido correctamente!'; // %1$s = nombre de archivo
$lang[161] = 'Tiempo';
$lang[162] = 'Velocidad promedio';
$lang[163] = 'No ha sido posible subir el archivo <b>%1$s</b>!'; // %1$s = nombre de archivo
$lang[164] = 'Email';
$lang[165] = 'Borrar envíos exitosos';
$lang[166] = 'Dirección de E-mail no válida';
$lang[167] = '¡Por favor selecciona solamente el archivo .crc o .001!';
$lang[168] = '¡Por favor selecciona el archivo .crc!';
$lang[169] = '¡Por favor selecciona el archivo .crc o .001!';
$lang[170] = '¿Realizar verificación de CRC? (recomendado)';
$lang[171] = 'Modo de verificación CRC32';
$lang[172] = 'Usar hash_file (Recomendado)';
$lang[173] = 'Leer archivo a la memoria';
$lang[174] = 'crc falso';
$lang[175] = 'Borrar archivos de origen después de unirlos correctamente';
$lang[176] = 'Nota:';
$lang[177] = 'El tamaño de archivo y el crc32 no se verificarán';
$lang[178] = '¡No es sposible leer el archivo .crc!';
$lang[179] = 'Error, el archivo de salida ya existe <b>%1$s</b>'; // %1$s = nombre de archivo
$lang[180] = 'Error, partes faltantes o incompletas';
$lang[181] = 'Error, el tipo de archivo %1$s está prohibido'; // Tipo de archivo
$lang[182] = 'No es posible abrir el archivo de destino <b>%1$s</b>'; // %1$s = nombre de archivo
$lang[183] = 'Error al escribir el archivo <b>%1$s</b>!'; // %1$s = nombre de archivo
$lang[184] = '¡La verificación de CRC32 no coincide!';
$lang[185] = 'Archivo <b>%1$s</b> unido correctamente'; // %1$s = nombre de archivo
$lang[186] = 'borrado';
$lang[187] = 'no borrado';
$lang[188] = 'Añadir extensión';
$lang[189] = 'sin';
$lang[190] = 'a';
$lang[191] = '¿Renombrar?';
$lang[192] = 'Cancelar';
$lang[193] = 'Error al renombrar el archivo <b>%1$s</b>'; // %1$s = nombre de archivo
$lang[194] = 'El archivo <b>%1$s</b> ha sido renombrado a <b>%2$s</b>'; // %1$s = nombre original del archivo %2$s = nombre nuevo del archivo
$lang[195] = 'Nombre de archivo';
$lang[196] = '¡Por favor introduce un nombre de archivo!';
$lang[197] = 'Error, el archivo no ha sido creado.';
$lang[198] = 'El archivo %1$s fue empaquetado'; // %1$s = nombre de archivo
$lang[199] = 'Empaquetado en el archivo <b>%1$s</b>'; // %1$s = nombre de archivo
$lang[200] = 'Error, el archivo está vacio.';
$lang[201] = 'Nuevo nombre';
$lang[202] = 'No se ha podido renombrar el archivo <b>%1$s</b>!'; // %1$s = nombre de archivo
$lang[203] = 'Eliminar archivos origen después de dividir correctamente';
$lang[204] = 'archivos y directorios';
$lang[205] = 'Descomprimir';
$lang[206] = 'Selector de Formato de Video de Youtube';
$lang[207] = 'Enlace a transferir';
$lang[208] = 'Referrer';
$lang[209] = 'Transferir archivo';
$lang[210] = 'Usuario &amp; Contraseña (HTTP/FTP)';
$lang[211] = 'Usuario';
$lang[212] = 'Contraseña';
$lang[213] = 'Agregar Comentario';
$lang[214] = 'Opciones de Plugin';
$lang[215] = 'Deshabilitar Todos los Plugins';
$lang[216] = 'Selector de Formato de Video de Youtube';
$lang[217] = 'Enlace Directo';
$lang[218] = '&amp;fmt=';
$lang[219] = 'Escojer automáticamente la mejor calidad disponible';
$lang[220] = '17 [Video: 3GP 176x144 | Audio: AAC 2ch 44.10kHz]';
$lang[221] = '5 [Video: FLV 400x240 | Audio: MP3 1ch 22.05kHz]';
$lang[222] = '34 [Video: FLV 640x360 | Audio: AAC 2ch 44.10kHz]';
$lang[223] = '35 [Video: FLV 854x480 | Audio: AAC 2ch 44.10kHz]';
$lang[224] = '43 [Video: WebM 640x360 | Audio: Vorbis 2ch 44.10kHz]';
$lang[225] = '45 [Video: WebM 1280x720 | Audio: Vorbis 2ch 44.10kHz]';
$lang[226] = '18 [Video: MP4 480x360 | Audio: AAC 2ch 44.10kHz]';
$lang[227] = '22 [Video: MP4 1280x720 | Audio: AAC 2ch 44.10kHz]';
$lang[228] = '37 [Video: MP4 1920x1080 | Audio: AAC 2ch 44.10kHz]';
$lang[229] = 'TorrentService de ImageShack&reg;';
$lang[230] = 'Nombre de usuario';
$lang[231] = 'Contraseña';
$lang[232] = 'Valor de cookie de Megaupload.com';
$lang[233] = 'usuario';
$lang[234] = 'Usar Plugin de vBulletin';
$lang[235] = 'Valor adicional de Cookie';
$lang[236] = 'Clave=Valor';
$lang[237] = 'Enviar archivo a Email';
$lang[238] = 'Email';
$lang[239] = 'Dividir archivos';
$lang[240] = 'Método';
$lang[241] = 'Total Commander';
$lang[242] = 'RFC 2046';
$lang[243] = 'Tamaño de las partes';
$lang[244] = 'MB';
$lang[245] = 'Usar configuración de Proxy';
$lang[246] = 'Proxy';
$lang[247] = 'Nombre de usuario';
$lang[248] = 'Contraseña';
$lang[249] = 'Usar Cuenta Premium';
$lang[250] = 'Nombre de usuario';
$lang[251] = 'Contraseña';
$lang[252] = 'Guardar en';
$lang[253] = 'Ruta';
$lang[254] = 'Guardar Opciones';
$lang[255] = 'Borrar Opciones Actuales';
$lang[256] = 'Marcar Todos';
$lang[257] = 'Desmarcar Todos';
$lang[258] = 'Invertir Seleción';
$lang[259] = 'Mostrar';
$lang[260] = 'Descargados';
$lang[261] = 'Todos';
$lang[262] = 'Nombre';
$lang[263] = 'Tamaño';
$lang[264] = 'Comentarios';
$lang[265] = 'Fecha';
$lang[266] = 'No se encontraron archivos';
$lang[267] = 'Funciona con';
$lang[268] = 'Elimina';
$lang[269] = 'Modo de Depuración';
$lang[270] = 'Solamente Mostrar Enlaces';
$lang[271] = 'Solamente Eliminar Enlaces';
$lang[272] = 'Verificar Enlaces';
$lang[273] = 'Cargando...';
$lang[274] = 'Procesando, por favor espere...';
$lang[275] = 'Espacio del Servidor';
$lang[276] = 'En Uso';
$lang[277] = 'Espacio Disponible';
$lang[278] = 'Espacio de Disco';
$lang[279] = 'CPU';
$lang[280] = 'Hora del Servidor';
$lang[281] = 'Hora Local';
$lang[282] = 'Auto - Borrado';
$lang[283] = 'Horas Después de la Transferencia';
$lang[284] = 'Minutos Después de la Transferencia';
$lang[285] = 'Acción';
$lang[286] = 'Subir';
$lang[287] = 'Enviar por FTP';
$lang[288] = 'E-Mail';
$lang[289] = 'E-mail Masivo';
$lang[290] = 'Dividir Archivos';
$lang[291] = 'Juntar Archivos';
$lang[292] = 'Hash MD5';
$lang[293] = 'Empaquetar archivos';
$lang[294] = 'Comprimir Archivos en ZIP';
$lang[295] = 'Descomprimir Archivos en ZIP';
$lang[296] = 'Renombrar';
$lang[297] = 'Renombrar Masivo';
$lang[298] = 'Borrar';
$lang[299] = 'Listar Enlaces';
$lang[300] = 'Retrieving download page';
$lang[301] = 'Teclea';
$lang[302] = 'aquí';
$lang[303] = 'Descargar Archivo';
$lang[304] = 'No se ha podido escribir en configs/files.lst asegúrate de que sus permisos sean 777';
$lang[305] = '&nbsp; ha sido seleccionado como tu ruta de descarga y no es escribible. Por favor, cambia sus permisos a 777';
$lang[306] = 'Uniendo Archivo';
$lang[307] = 'Esperando';
$lang[308] = 'Pasó';
$lang[309] = 'Falló';
$lang[310] = 'Podrías ver advertencias con esto desactivado';
$lang[311] = 'Quizá no seas capaz de activar la información del servidor';
$lang[312] = 'Puede que tu servidor no soporte archivos mayores de 2 GB';
$lang[313] = 'Script Verificador de Rapidleech';
$lang[314] = 'fsockopen';
$lang[315] = 'memory_limit';
$lang[316] = 'safe_mode';
$lang[317] = 'cURL';
$lang[318] = 'allow_url_fopen';
$lang[319] = 'Versión de PHP - ';
$lang[320] = 'allow_call_time_pass_reference';
$lang[321] = 'passthru';
$lang[322] = 'Funciones de Espacio de disco';
$lang[323] = 'Versión de Apache - ';
$lang[324] = 'Dirección de Proxy introducida es errónea';
$lang[325] = 'Archivo guardado correctamente';
$lang[326] = 'Guardar notas';
$lang[327] = 'Notas';
$lang[328] = 'Acciones Desactivadas';
$lang[329] = 'Principal';
$lang[330] = 'Opciones';
$lang[331] = 'Archivos en Servidor';
$lang[332] = 'Verificar enlaces';
$lang[333] = 'Plugins';
$lang[334] = 'Auto Transferir';
$lang[335] = 'Auto Subir';
$lang[336] = 'El Tamaño del archivo está limitado a ';
$lang[337] = 'Límite de tamaño de archivo: ';
$lang[338] = 'Comprimir Archivos en Rar';
$lang[339] = 'Extraer Archivos en Rar';
$lang[340] = 'Error detectado';
$lang[341] = 'click aquí para expandir';
$lang[342] = 'Puedes arrastrar la ventana desde aquí';
$lang[343] = 'No se encuentra "rar"<br />Puede que necesites bajarlo y extraer "rar" al directorio "/rar/"';
$lang[344] = 'Ficheros que serán archivados:';
$lang[345] = 'Nombre del archivo:';
$lang[346] = 'Opciones:';
$lang[347] = 'Nivel de compresión:';
$lang[348] = 'No comprimir';
$lang[349] = 'Muy rápida';
$lang[350] = 'Rápida';
$lang[351] = 'Normal';
$lang[352] = 'Buena';
$lang[353] = 'La mejor';
$lang[354] = 'Crear volumenes';
$lang[355] = 'Eliminar ficheros tras la compresión';
$lang[356] = 'Crear un archivo sólido';
$lang[357] = 'Añadir Registro de Recuperación';
$lang[358] = 'Verificar ficheros comprimidos';
$lang[359] = 'Usar contraseña';
$lang[360] = 'Codificar nombres de fichero';
$lang[361] = 'Especificar ruta dentro del archivo';
$lang[362] = 'Comprimir Archivos en Rar';
$lang[363] = 'Creando archivo: <b>%1$s</b>';
$lang[364] = 'Esperando...';
$lang[365] = 'Volver a la lista de archivos';
$lang[366] = '<b>Ficheros de %1$s</b>:';
$lang[367] = 'No se encuentra "unrar"';
$lang[368] = 'Contraseña necesaria para listar ficheros:';
$lang[369] = 'Contraseña necesaria para descomprimir ficheros:';
$lang[370] = 'Error:%1$s';
$lang[371] = 'Intentar listar de nuevo';
$lang[372] = 'Descomprimir ficheros seleccionados';
$lang[373] = '<b>Extrayendo ficheros desde %1$s</b>:';
$lang[374] = 'Estado:';
$lang[375] = 'Seleccionar texto';
$lang[376] = 'Cuentas Premium :';
$lang[377] = '38 [Video: MP4 4096x3072 | Audio: AAC 2ch 44.10kHz]';
$lang[378] = 'Cerrar ventana';
$lang[379] = 'Archivos';
$lang[380] = 'El cambio de MD5 solo debeser aplicado a formatos conocidos que funcionen con él(p.e. .rar o .zip)<br />¿Deseas continuar?';
$lang[381] = 'MD5 del archivo <b>%1$s</b> cambiado'; // %1$s = filename
$lang[382] = 'Error cambiando el MD5 del archivo <b>%1$s</b>!'; // %1$s = filename
$lang[383] = 'Cambio de MD5';
$lang[384] = 'Coincidir texto';
$lang[385] = 'Coincidir';
$lang[386] = 'Ignorar mayúsculas y minúsculas';
$lang[387] = 'Colocar cada fichero en archivos separados';
$lang[388] = 'OpenSSL';
$lang[389] = '44 [Video: WebM 854x480 | Audio: Vorbis 2ch 44.10kHz]';
$lang[390] = 'Hash CRC32';
$lang[391] = 'El CRC32 encontrado en nombre del archivo es válido';
$lang[392] = 'El CRC32 encontrado en nombre del archivo (&quot;%1$s&quot;) no concuerda con el CRC32 calculado';
$lang[390] = 'Hash SHA1';
?>