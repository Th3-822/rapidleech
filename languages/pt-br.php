<?php
if (!defined('RAPIDLEECH')) {
	require('../deny.php');
	exit;
}
// The English language file
// You should always use this file as a template for translating

$lang[1]	=	'Acesso negado';
$lang[2]	=	'O servidor recusou-se a satisfazer o seu pedido';
$lang[3]	=	'Voc&ecirc; n&atilde;o inseriu um endere&ccedil;o de e-mail v&aacute;lido';
$lang[4]	=	'Tamanho das partes n&atilde;o s&atilde;o num&eacute;ricos';
$lang[5]	=	'Desconhecido Tipo de URL, <span class="font-black">Use apenas <span class="font-blue">http</span> ou <span class="font-blue">https</span> ou <span class="font-blue">ftp</span> Protocol</span>';
$lang[6]	=	'O caminho n&atilde;o especificado para salvar este arquivo';
$lang[7]	=	'Voc&ecirc; n&atilde;o est&aacute; autorizado a baixar de <span class="font-black">%1$s (%2$s)</span>';	// %1$s = host name %2$s = host ip
$lang[8]	=	'Redirecionando para:';
$lang[9]	=	'N&atilde;o foi poss&iacute;vel atualizar a lista de arquivos';
$lang[10]	=	'Arquivo <b>%1$s</b> (<b>%2$s</b>) Salvado!<br />Tempo: <b>%3$s</b><br />Velocidade M&eacute;dia: <b>%4$s KB/s</b><br />';	// %1$s = filename %2$s = filesize %3$s = time of download %4$s = speed
$lang[11]	=	'<script>E-mail("O arquivo foi enviado para este endere&ccedil;o<b>%1$s</b>.", "%2$s");</script>';	// %1$s = E-mail address %2$s = filename
$lang[12]	=	'Erro ao enviar arquivo!';
$lang[13]	=	'Voltar para a principal';
$lang[14]	=	'Conex&atilde;o perdida, arquivo foi exclu&iacute;do.';
$lang[15]	=	'Atualizar';
$lang[16]	=	'Por favor, altere o modo de depura&ccedil;&atilde;o para <b>1</b>';
$lang[17]	=	'No m&aacute;ximo (%1$s) dos links foram alcan&ccedil;ados.';	// %1$s = Number of maximum links
$lang[18]	=	'%1$s Link%2$s checados em %3$s segundos. (M&eacute;todo: <b>%4$s</b>)';	// %1$s = Number of links %2$s = Plural form %3$s = seconds %4$s = method for checking links
$lang[19]	=	's';	// End of a plural
$lang[20]	=	'Bad proxy server address';
$lang[21]	=	'Link';
$lang[22]	=	'Status';
$lang[23]	=	'Esperando';
$lang[24]	=	'URL Inv&aacute;lida';
$lang[25]	=	'Preparando';
$lang[26]	=	'Iniciado';
$lang[27]	=	'Conex&atilde;o perdida';
$lang[28]	=	'Conclu&iacute;do';
$lang[29]	=	'Baixar arquivos';
$lang[30]	=	'Frames sem suporte, atualize seu navegador';
$lang[31]	=	'Adicionar links';
$lang[32]	=	'Links';
$lang[33]	=	'Op&ccedil;&otilde;es';
$lang[34]	=	'Baixar arquivos';
$lang[35]	=	'Use configura&ccedil;&otilde;es de proxy';
$lang[36]	=	'Proxy';
$lang[37]	=	'Usu&aacute;rio';
$lang[38]	=	'Senha';
$lang[39]	=	'Utilizar Imageshack Conta';
$lang[40]	=	'Salvar em';
$lang[41]	=	'Caminho';
$lang[42]	=	'Use Conta Premium';
$lang[43]	=	'Executar Server Side';
$lang[44]	=	'Tempo de atraso';
$lang[45]	=	'Atraso (em segundos)';
$lang[46]	=	'Nenhum arquivo ou hosts selecionados para upload';
$lang[47]	=	'Selecione Hosts para upload';
$lang[48]	=	'N&atilde;o suporta servi&ccedil;os de upload!';
$lang[49]	=	'Upload em janelas';
$lang[50]	=	'Link para o formato salvo';
$lang[51]	=	'Padr&atilde;o';
$lang[52]	=	'Marcar Todos';
$lang[53]	=	'Desmarcar Todos';
$lang[54]	=	'Inverter Sele&ccedil;&atilde;o';
$lang[55]	=	'Nome';
$lang[56]	=	'Tamanho';
$lang[57]	=	'N&atilde;o foram encontrados arquivos';
$lang[58]	=	'Leganda para o link salvado formato: (mai&uacute;sculas e min&uacute;sculas)';
$lang[59]	=	'O link para baixar';
$lang[60]	=	'O nome do arquivo';
$lang[61]	=	'Padr&atilde;o de estilo link';
$lang[62]	=	'Qualquer coisa al&eacute;m do declarado acima ser&aacute; tratado como fio, voc&ecirc; n&atilde;o pode enfileirar multi formate agora, uma linha nova ser&aacute; inserida para cada link.';
$lang[63]	=	'Arquivo carregando %1$s to %2$s';	// %1$s = filename %2$s = file host name
$lang[64]	=	'Arquivo %1$s n&atilde;o existe.';	// %1$s = filename
$lang[65]	=	'Arquivo %1$s n&atilde;o &eacute; lido pelo script.';	// %1$s = filename
$lang[66]	=	'Tamanho do arquivo muito grande para fazer o upload no host.';
$lang[67]	=	'Servi&ccedil;o de upload n&atilde;o permitido';
$lang[68]	=	'Baixar-Link';
$lang[69]	=	'Deletar-Link';
$lang[70]	=	'Iniciar-Link';
$lang[71]	=	'Admin-Link';
$lang[72]	=	'USER-ID';
$lang[73]	=	'FTP upload';
$lang[74]	=	'Senha';
$lang[75]	=	'Rapidleech PlugMod - Upload Links';
$lang[76]	=	'<div class="linktitle">Links para upload <strong>%1$s</strong> - <span class="bluefont">Tamanho: <strong>%2$s</strong></span></div>';	// %1$s = file name %2$s = file size
$lang[77]	=	'Conclu&iacute;do';
$lang[78]	=	'Voltar';
$lang[79]	=	'N&atilde;o foi poss&iacute;vel estabelecer conex&atilde;o com o servidor %1$s.';		// %1$s = FTP server name
$lang[80]	=	'Nome de usu&aacute;rio incorreto e/ou senha.';
$lang[81]	=	'Conectado a: <b>%1$s</b>...';	// %1$s = FTP server name
$lang[82]	=	'O tipo de ficheiro %1$s &eacute; proibido de ser baixado';	// %1$s = File type
$lang[83]	=	'Arquivo <b>%1$s</b>, Tamanho <b>%2$s</b>...';	// %1$s = file name %2$s = file size
$lang[84]	=	'Erro ao recuperar o link';
$lang[85]	=	'Texto contador &eacute; passado como string!';
$lang[86]	=	'ERRO: Por favor habilite o JavaScript.';
$lang[87]	=	'Por favor, aguarde <b>%1$s</b> segundos...';	// %1$s = number of seconds
$lang[88]	=	'N&atilde;o foi poss&iacute;vel conectar %1$s na porta %2$s';	// %1$s = host name %2$s = port
$lang[89]	=	'Conectado ao proxy: <b>%1$s</b> na porta <b>%2$s</b>...';	// %1$s = Proxy host %2$s = Proxy port
$lang[90]	=	'Conectado a: <b>%1$s</b> na porta <b>%2$s</b>...';	// %1$s = host %2$s = port
$lang[91]	=	'N&atilde;o recebeu o cabe&ccedil;alho';
$lang[92]	=	'Voc&ecirc; est&aacute; proibido de acessar a p&aacute;gina!';
$lang[93]	=	'A p&aacute;gina n&atilde;o foi encontrada!';
$lang[94]	=	'A p&aacute;gina foi proibida ou n&atilde;o encontrada!';
$lang[95]	=	'Error! it is redirected to [%1$s]';	// %1$s = redirected address
$lang[96]	=	'Este site requer autoriza&ccedil;&atilde;o. Para a indica&ccedil;&atilde;o do nome de usu&aacute;rio e senha de acesso &eacute; necess&aacute;rio usar uma URL similar a essa:<br />http://<b>usu&aacute;rio:senha@</b>www.site.com/arquivo.exe';
$lang[97]	=	'O limite do resumo excedeu';
$lang[98]	=	'Este servidor n&atilde;o suporta resumo';
$lang[99]	=	'Baixar';
$lang[100]	=	'Est&aacute; conta premium j&aacute; est&aacute; em uso com outro ip.';
$lang[101]	=	'Arquivo %1$s n&atilde;o pode ser salvo no direct&oacute;rio %2$s';	// %1$s = file name %2$s = directory name
$lang[102]	=	'Tente chmod 777 para a pasta.';
$lang[103]	=	'Tentar novamente';
$lang[104]	=	'Arquivo';
$lang[105]	=	'N&atilde;o &eacute; poss&iacute;vel realizar um registro no arquivo %1$s';	// %1$s = file name
$lang[106]	=	'URL inv&aacute;lida ou erro desconhecido';
$lang[107]	=	'Voc&ecirc; atingiu o limite para os usu&aacute;rios free.';
$lang[108]	=	'A sess&atilde;o de download expirou';
$lang[109]	=	'C&oacute;digo de acesso errado.';
$lang[110]	=	'Voc&ecirc; digitou um c&oacute;digo errado muitas vezes';
$lang[111]	=	'Limite de transfer&ecirc;ncia excedido';
$lang[112]	=	'Erro de leitura de dados';
$lang[113]	=	'Erro ao enviar dados';
$lang[114]	=	'Ativo';
$lang[115]	=	'Indispon&iacute;vel';
$lang[116]	=	'Morto';
$lang[117]	=	'Voc&ecirc; precisa carregar/ativar a extens&atilde;o cURL (http://www.php.net/cURL) ou voc&ecirc; pode definir $fgc = 1 em config.php.';
$lang[118]	=	'cURL est&aacute; habilitado';
$lang[119]	=	'PHP vers&atilde;o 5 &eacute; recomendada, embora n&atilde;o seja obrigat&oacute;rio';
$lang[120]	=	'Verifique se o safe mode est&aacute; desativado pois o script n&atilde;o funciona com ele ativado';
$lang[121]	=	'Enviado arquivo <b>%1$s</b>';	// %1$s = filename
$lang[122]	=	'N&atilde;o h&aacute; necessidade de dividir, Enviar e-mail &uacute;nico';
$lang[123]	=	'Dividindo em partes no tamanho de %1$s';	// %1$s = part size
$lang[124]	=	'M&eacute;todo';
$lang[125]	=	'Enviado parte <b>%1$s</b>';	//%1$s = part number
$lang[126]	=	'N&atilde;o h&aacute; necessidade dividir, enviar e-mail &uacute;nico';
$lang[127]	=	'Arquivo n&atilde;o encontrado no host';
$lang[128]	=	'N&atilde;o &eacute; poss&iacute;vel criar o arquivo no host';
$lang[129]	=	'horas';	// Plural
$lang[130]	=	'hora';
$lang[131]	=	'minutos';	// Plural
$lang[132]	=	'minuto';
$lang[133]	=	'segundos';	// Plural
$lang[134]	=	'segundo';
$lang[135]	=	'getCpuUsage(): N&atilde;o foi poss&iacute;vel acessar o in&iacute;cio do caminho ou come&ccedil;o de arquivo inv&aacute;lido';
$lang[136]	=	'Carga da CPU';
$lang[137]	=	'Ocorreu um erro';
$lang[138]	=	'Selecione pelo menos um arquivo.';
$lang[139]	=	'Emails';
$lang[140]	=	'Enviar';
$lang[141]	=	'Exclus&atilde;o de submits com sucesso';
$lang[142]	=	'Dividir por partes';
$lang[143]	=	'Tamanho das Partes';
$lang[144]	=	'<b>%1$s</b> - E-mail Inv&aacute;lido.';	// %1$s = email address
$lang[145]	=	'Arquivo <b>%1$s</b> n&atilde;o &eacute; encontrado!';	// %1$s = filename
$lang[146]	=	'N&atilde;o foi poss&iacute;vel atualizar a lista de arquivos!';
$lang[147]	=	'Exclus&atilde;o de arquivos est&aacute; desativada';
$lang[148]	=	'Excluir arquivos';
$lang[149]	=	'Sim';
$lang[150]	=	'N&atilde;o';
$lang[151]	=	'Arquivo <b>%1$s</b> exclu&iacute;do';	// %1$s = filename
$lang[152]	=	'Erro ao excluir o arquivo <b>%1$s</b>!';	// %1$s = filename
$lang[153]	=	'Host';
$lang[154]	=	'Porta';
$lang[155]	=	'Diret&oacute;rio';
$lang[156]	=	'Exclus&atilde;o do arquivo de origem ap&oacute;s o upload com sucesso';
$lang[157]	=	'Copiar Arquivos';
$lang[158]	=	'Mover Arquivos';
$lang[159]	=	'N&atilde;o &eacute; poss&iacute;vel localizar a pasta <b>%1$s</b>';	// %1$s = directory name
$lang[160]	=	'Arquivo %1$s carregado com sucesso!';	// %1$s = filename
$lang[161]	=	'Tempo';
$lang[162]	=	'Velocidade m&eacute;dia';
$lang[163]	=	'N&atilde;o foi poss&iacute;vel carregar o arquivo <b>%1$s</b>!';	// %1$s = filename
$lang[164]	=	'Email';
$lang[165]	=	'Exclus&atilde;o de submits com sucesso';
$lang[166]	=	'E-mail Inv&aacute;lido';
$lang[167]	=	'Por favor, selecione apenas o .CRC ou .001 do arquivo!';
$lang[168]	=	'Por favor, selecione o arquivo .CRC!';
$lang[169]	=	'Por favor, selecione o arquivo .CRC ou .001 do arquivo!';
$lang[170]	=	'Realize uma verifica&ccedil;&atilde;o de CRC? (recomendado)';
$lang[171]	=	'CRC32 modo de verifica&ccedil;&atilde;o';
$lang[172]	=	'Use hash_file (Recomendado)';
$lang[173]	=	'Ler o arquivo para a mem&oacute;ria';
$lang[174]	=	'Falso crc';
$lang[175]	=	'Excluir o arquivo de origem ap&oacute;s a bem sucedida jun&ccedil;&atilde;o';
$lang[176]	=	'Aviso';
$lang[177]	=	'O tamanho do arquivo e CRC32 n&atilde;o ser&aacute; verificado';
$lang[178]	=	'N&atilde;o &eacute; poss&iacute;vel ler o arquivo .CRC!';
$lang[179]	=	'Erro, arquivo de sa&iacute;da j&aacute; existe <b>%1$s</b>';	// %1$s = filename
$lang[180]	=	'Erro, partes em falta ou incompletas';
$lang[181]	=	'Erro, O tipo de arquivo %1$s &eacute; proibido';	// Filetype
$lang[182]	=	'N&atilde;o &eacute; poss&iacute;vel abrir o arquivo de destino <b>%1$s</b>';	// %1$s = filename
$lang[183]	=	'Erro ao gravar o arquivo <b>%1$s</b>!';	// %1$s = filename
$lang[184]	=	'CRC32 checksum n&atilde;o corresponder!';
$lang[185]	=	'Arquivo <b>%1$s</b> unido com &ecirc;xito';	// %1$s = filename
$lang[186]	=	'exclu&iacute;do';
$lang[187]	=	'n&atilde;o exclu&iacute;do';
$lang[188]	=	'Adicionar extens&atilde;o';
$lang[189]	=	'externamente';
$lang[190]	=	'para';
$lang[191]	=	'Renomear?';
$lang[192]	=	'Cancelar';
$lang[193]	=	'Erro ao renomear arquivo <b>%1$s</b>';	// %1$s = filename
$lang[194]	=	'Arquivo <b>%1$s</b> foi renomeado para <b>%2$s</b>';	// %1$s = original filename %2$s = renamed filename
$lang[195]	=	'Nome do Arquivo';
$lang[196]	=	'Por favor, entre com o nome do arquivo!';
$lang[197]	=	'Erro o arquivo n&atilde;o foi criado.';
$lang[198]	=	'Arquivo %1$s foi packed';	// %1$s = filename
$lang[199]	=	'Packed no arquivo <b>%1$s</b>';	// %1$s = filename
$lang[200]	=	'Erro, o arquivo est&aacute; vazio.';
$lang[201]	=	'Novo nome';
$lang[202]	=	'N&atilde;o foi poss&iacute;vel renomear o arquivo <b>%1$s</b>!';	// %1$s = filename
$lang[203]	=	'Excluir o arquivo de origem depois de dividir com sucesso';
$lang[204]	=	'arquivos e pastas';
$lang[205]	=	'Unzip';
$lang[206]	=	'YouTube Formato de V&iacute;deo Seletor';
$lang[207]	=	'Link para Baixar';
$lang[208]	=	'Refer&ecirc;ncia';
$lang[209]	=	'Baixar Arquivo';
$lang[210]	=	'Usu&aacute;rio & Senha (HTTP/FTP)';
$lang[211]	=	'Usu&aacute;rio';
$lang[212]	=	'Senha';
$lang[213]	=	'Adicionar Coment&aacute;rios';
$lang[214]	=	'Op&ccedil;&otilde;es de Plugin';
$lang[215]	=	'Desabilitar Todos os Plugins';
$lang[216]	=	'YouTube Formato de V&iacute;deo Seletor';
$lang[217]	=	'Link direto';
$lang[218]	=	'&fmt=';
$lang[219]	=	'Auto-obter o formato de alta qualidade dispon&iacute;vel';
$lang[220]	=	'0 [V&iacute;deo: FLV H263 251kbps 320x180 @ 29.896fps | &Aacute;udio: MP3 64kbps 1ch @ 22.05kHz]';
$lang[221]	=	'5 [V&iacute;deo: FLV H263 251kbps 320x180 @ 29.885fps | &Aacute;udio: MP3 64kbps 1ch @ 22.05kHz]';
$lang[222]	=	'6 [V&iacute;deo: FLV H263 892kbps 480x270 @ 29.887fps | &Aacute;udio: MP3 96kbps 1ch @ 44.10kHz]';
$lang[223]	=	'13 [V&iacute;deo: 3GP H263 77kbps 176x144 @ 15.000fps | &Aacute;udio: AMR 13kbps 1ch @ 8.000kHz]';
$lang[224]	=	'17 [V&iacute;deo: 3GP XVID 55kbps 176x144 @ 12.000fps | &Aacute;udio: AAC 29kbps 1ch @ 22.05kHz]';
$lang[225]	=	'18 [V&iacute;deo: MP4 H264 505kbps 480x270 @ 29.886fps | &Aacute;udio: AAC 125kbps 2ch @ 44.10kHz]';
$lang[226]	=	'22 [V&iacute;deo: MP4 H264 2001kbps 1280x720 @ 29.918fps | &Aacute;udio: AAC 198kbps 2ch @ 44.10kHz]';
$lang[227]	=	'34 [V&iacute;deo: FLV H264 256kbps 320x180 @ 29.906fps | &Aacute;udio: AAC 62kbps 2ch @ 22.05kHz]';
$lang[228]	=	'35 [V&iacute;deo: FLV H264 831kbps 640x360 @ 29.942fps | &Aacute;udio: AAC 107kbps 2ch @ 44.10kHz]';
$lang[229]	=	'ImageShack&reg; Servi&ccedil;o de Torrent';
$lang[230]	=	'Usu&aacute;rio';
$lang[231]	=	'Senha';
$lang[232]	=	'Megaupload.com Valor de Cookie';
$lang[233]	=	'Usu&aacute;rio';
$lang[234]	=	'Use vBulletin Plugin';
$lang[235]	=	'Valor Adicional de Cookie';
$lang[236]	=	'Key=Valor';
$lang[237]	=	'Enviar arquivo para E-mail';
$lang[238]	=	'Email';
$lang[239]	=	'Dividir Arquivos';
$lang[240]	=	'M&eacute;todo';
$lang[241]	=	'Total Commander';
$lang[242]	=	'RFC 2046';
$lang[243]	=	'Tamanho das Partes';
$lang[244]	=	'MB';
$lang[245]	=	'Usar Configura&ccedil;&otilde;es Proxy 	';
$lang[246]	=	'Proxy';
$lang[247]	=	'Usu&aacute;rio';
$lang[248]	=	'Senha';
$lang[249]	=	'Usar Conta Premium';
$lang[250]	=	'Usu&aacute;rio';
$lang[251]	=	'Senha';
$lang[252]	=	'Salvar Em';
$lang[253]	=	'Caminho';
$lang[254]	=	'Salvar Configura&ccedil;&otilde;es';
$lang[255]	=	'Apagar Configura&ccedil;&atilde;o Atual';
$lang[256]	=	'Marcar Todos';
$lang[257]	=	'Desmarcar Todos';
$lang[258]	=	'Inverter Sele&ccedil;&atilde;o';
$lang[259]	=	'Mostrar';
$lang[260]	=	'Baixados';
$lang[261]	=	'Tudo';
$lang[262]	=	'Nome';
$lang[263]	=	'Tamanho';
$lang[264]	=	'Coment&aacute;rios';
$lang[265]	=	'Data';
$lang[266]	=	'N&atilde;o foram encontrados arquivos';
$lang[267]	=	'Funciona com';
$lang[268]	=	'Mortos';
$lang[269]	=	'Modo Depurar';
$lang[270]	=	'Mostrar Apenas Links';
$lang[271]	=	'Matar Apenas Links';
$lang[272]	=	'Verificar Links';
$lang[273]	=	'Carregando...';
$lang[274]	=	'Processando, aguarde...';
$lang[275]	=	'Espa&ccedil;o do Server';
$lang[276]	=	'Em uso';
$lang[277]	=	'Espa&ccedil;o Livre';
$lang[278]	=	'Espa&ccedil;o do Disco';
$lang[279]	=	'CPU';
$lang[280]	=	'Hora do Server';
$lang[281]	=	'Hora Local';
$lang[282]	=	'Auto-Delete';
$lang[283]	=	'Horas Ap&oacute;s Baixado';
$lang[284]	=	'Minutos Ap&oacute;s Baixado';
$lang[285]	=	'A&ccedil;&atilde;o';
$lang[286]	=	'Upload';
$lang[287]	=	'Arquivo FTP';
$lang[288]	=	'E-Mail';
$lang[289]	=	'E-mail em Massa';
$lang[290]	=	'Dividir Arquivos';
$lang[291]	=	'Juntar Arquivos';
$lang[292]	=	'MD5 Hash';
$lang[293]	=	'Pacote de Arquivos';
$lang[294]	=	'ZIP Arquivos';
$lang[295]	=	'Unzip Arquivos';
$lang[296]	=	'Renomear';
$lang[297]	=	'Renomear em Massa';
$lang[298]	=	'Excluir';
$lang[299]	=	'Lista de Links';
$lang[300]	=	'Recuperando p&aacute;gina de download';
$lang[301]	=	'Digite';
$lang[302]	=	'aqui';
$lang[303]  =   'Baixar Arquivo';

?>
