<?php
if (!defined('RAPIDLEECH')) {
  require('../deny.php');
  exit;
}
// Arquivo para o idioma Português-BR
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
$lang[10]	=	'Arquivo <b>%1$s</b> (<b>%2$s</b>) Salvo!<br />Tempo: <b>%3$s</b><br />Velocidade M&eacute;dia: <b>%4$s KB/s</b><br />';	// %1$s = filename %2$s = filesize %3$s = time of download %4$s = speed
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
$lang[47]	=	'Selecione os Hosts para upload';
$lang[48]	=	'N&atilde;o suporta servi&ccedil;os de upload!';
$lang[49]	=	'Upload em janelas';
$lang[50]	=	'Link para o formato salvo';
$lang[51]	=	'Default';
$lang[52]	=	'Marcar Todos';
$lang[53]	=	'Desmarcar Todos';
$lang[54]	=	'Inverter Sele&ccedil;&atilde;o';
$lang[55]	=	'Nome';
$lang[56]	=	'Tamanho';
$lang[57]	=	'N&atilde;o foram encontrados arquivos';
$lang[58]	=	'Leganda para o link salvo no formato: (mai&uacute;sculas e min&uacute;sculas)';
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
$lang[79]	=	'N&atilde;o foi poss&iacute;vel estabelecer uma conex&atilde;o com o servidor %1$s.';		// %1$s = FTP server name
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
$lang[94]	=	'A p&aacute;gina foi proibida ou n&atilde;o &eacute; encontrada!';
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
$lang[117]	=	'Voc&ecirc; precisa carregar/ativar a extens&atilde;o cURL (http://www.php.net/cURL) ou voc&ecirc; pode definir \'fgc\' => 1 em config.php.';
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
$lang[157]	=	'Salvar dados de FTP';
$lang[158]	=	'Apagar dados de FTP';
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
$lang[210]	=	'Usu&aacute;rio &amp; Senha (HTTP/FTP)';
$lang[211]	=	'Usu&aacute;rio';
$lang[212]	=	'Senha';
$lang[213]	=	'Adicionar Coment&aacute;rios';
$lang[214]	=	'Op&ccedil;&otilde;es de Plugin';
$lang[215]	=	'Desabilitar Todos os Plugins';
$lang[216]	=	'YouTube Formato de V&iacute;deo Seletor';
$lang[217]	=	'Link direto';
$lang[218]	=	'&amp;fmt=';
$lang[219]	=	'Auto-obter o formato de alta qualidade dispon&iacute;vel';
$lang[220]	=	'17 [V&iacute;deo: 3GP 176x144 | &Aacute;udio: AAC 2ch 44.10kHz]';
$lang[221]	=	'5 [V&iacute;deo: FLV 400x240 | &Aacute;udio: MP3 1ch 22.05kHz]';
$lang[222]	=	'34 [V&iacute;deo: FLV 640x360 | &Aacute;udio: AAC 2ch 44.10kHz]';
$lang[223]	=	'35 [V&iacute;deo: FLV 854x480 | &Aacute;udio: AAC 2ch 44.10kHz]';
$lang[224]	=	'43 [V&iacute;deo: WebM 640x360 | &Aacute;udio: Vorbis 2ch 44.10kHz]';
$lang[225]	=	'45 [V&iacute;deo: WebM 1280x720 | &Aacute;udio: Vorbis 2ch 44.10kHz]';
$lang[226]	=	'18 [V&iacute;deo: MP4 480x360 | &Aacute;udio: AAC 2ch 44.10kHz]';
$lang[227]	=	'22 [V&iacute;deo: MP4 1280x720 | &Aacute;udio: AAC 2ch 44.10kHz]';
$lang[228]	=	'37 [V&iacute;deo: MP4 1920×1080 | &Aacute;udio: AAC 2ch 44.10kHz]';
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
$lang[297]	=	'Adicionar extens&atilde;o';
$lang[298]	=	'Excluir';
$lang[299]	=	'Lista de Links';
$lang[300]	=	'Recuperando p&aacute;gina de download';
$lang[301]	=	'Digite';
$lang[302]	=	'aqui';
$lang[303]	=	'Baixar Arquivo';
$lang[304]	=	'configs/files.lst n&atilde;o &eacute; grav&aacute;vel, por favor, certifique-se que &eacute; chmod para 777';
$lang[305]	=	'&nbsp;&eacute; selecionado como o caminho de download e n&atilde;o &eacute; grav&aacute;vel. Por favor chmod para 777';
$lang[306]	=	'Juntando Arquivos';
$lang[307]	=	'Esperando';
$lang[308]	=	'Passou';
$lang[309]	=	'Falhou';
$lang[310]	=	'Voc&ecirc; pode ver os avisos sem esta ligado';
$lang[311]	=	'Voc&ecirc; pode n&atilde;o ser capaz de transformar em estat&iacute;sticas do servidor';
$lang[312]	=	'O servidor pode n&atilde;o ser capaz de suportar arquivos com tamanho superior a 2GB';
$lang[313]	=	'Rapidleech Checker Script';
$lang[314]	=	'fsockopen';
$lang[315]	=	'limite de mem&oacute;ria';
$lang[316]	=	'safe_mode';
$lang[317]	=	'cURL';
$lang[318]	=	'permitir url fopen';
$lang[319]	=	'Vers&atilde;o do PHP &#45;';
$lang[320]	=	'permitir passar tempo de perman&ecirc;ncia de refer&ecirc;ncia';
$lang[321]	=	'Passar';
$lang[322]	=	'Fun&ccedil;&otilde;es de Espa&ccedil;o de Disco';
$lang[323]	=	'Apache vers&atilde;o &#45; ';
$lang[324]	=	'Endere&ccedil;o de proxy errado';
$lang[325]	=	'Arquivo salvo com sucesso!';
$lang[326]	=	'Salvar Notas';
$lang[327]	=	'Notas';
$lang[328]	=	'A&ccedil;&otilde;es Desabilitadas';
$lang[329]	=	'In&iacute;cio';
$lang[330]	=	'Configura&ccedil;&otilde;es';
$lang[331]	=	'Arquivos no Servidor';
$lang[332]	=	'Verificador de Links';
$lang[333]	=	'Plugins';
$lang[334]	=	'Auto Baixar';
$lang[335]	=	'Auto Enviar';
$lang[336]	=	'Tamanho do arquivo &eacute; limitado a ';
$lang[337]	=	'Tamanho limite do arquivo: ';
$lang[338]	=	'Rar Files';
$lang[339]	=	'Unrar Files';
$lang[340]	=	'Erro detectado';
$lang[341]	=	'Clique aqui para ampliar';
$lang[342]	=	'Voc&ecirc; pode arrastar a janela daqui';
$lang[343]	=	'N&atilde;o &eacute; poss&iacute;vel encontrar &quot;rar&quot;<br />Voc&ecirc; pode precisar fazer o download e extrair &quot;rar&quot; para o diret&oacute;rio &quot;/rar/&quot;';
$lang[344]	=	'Os arquivos que ser&atilde;o arquivados:';
$lang[345]	=	'Nome do arquivo:';
$lang[346]	=	'Op&ccedil;&otilde;es:';
$lang[347]	=	'N&iacute;vel de compress&atilde;o:';
$lang[348]	=	'Armazenar';
$lang[349]	=	'Mais r&aacute;pido';
$lang[350]	=	'R&aacute;pido';
$lang[351]	=	'Normal';
$lang[352]	=	'Bom';
$lang[353]	=	'&Oacute;timo';
$lang[354]	=	'Criar volumes';
$lang[355]	=	'Excluir arquivos ap&oacute;s arquivamento';
$lang[356]	=	'Criar arquivo s&oacute;lido';
$lang[357]	=	'Criar um registro de recupera&ccedil;&atilde;o';
$lang[358]	=	'Teste de arquivo ap&oacute;s a compress&atilde;o';
$lang[359]	=	'Use a senha';
$lang[360]	=	'Criptografar os nomes de arquivo';
$lang[361]	=	'Definir o caminho dentro de arquivo';
$lang[362]	=	'Rar';
$lang[363]	=	'Cria&ccedil;&atilde;o do arquivo: <b>%1$s</b>';
$lang[364]	=	'Aguarde...';
$lang[365]	=	'Voltar para a lista de arquivos';
$lang[366]	=	'<b>Arquivos de %1$s</b>:';
$lang[367]	=	'N&atilde;o &eacute; poss&iacute;vel encontrar "unrar"';
$lang[368]	=	'Senha necess&aacute;ria para listar os arquivos:';
$lang[369]	=	'Senha necess&aacute;ria para extrair os arquivos:';
$lang[370]	=	'Erro:%1$s';
$lang[371]	=	'Tente novamente listar';
$lang[372]	=	'Unrar selecionados';
$lang[373]	=	'<b>Extraindo arquivos de %1$s</b>:';
$lang[374]	=	'Status:';
$lang[375]	=	'Selecione o texto';
$lang[376]  =   'Contas Premium :';
$lang[377]	=	'38 [V&iacute;deo: MP4 4096×3072 | &Aacute;udio: AAC 2ch 44.10kHz]';
$lang[378]	=	'Fechar a janela';
$lang[379]	=	'Arquivos';
$lang[380]	=	'Mudan&ccedil;a de MD5 deve ser aplicado apenas para os formatos conhecidos de trabalho (ie. rar ou. zip)<br />Voc&ecirc; quer continuar?';
$lang[381]	=	'MD5 do arquivo <b>%1$s</b> alterado';	// %1$s = filename
$lang[382]	=	'Erro ao alterar o MD5 do arquivo <b>%1$s</b>!';	// %1$s = filename
$lang[383]	=	'MD5 mudan&ccedil;a';
$lang[384]	=	'Match text';
$lang[385]	=	'Match';
$lang[386]	=	'Ignore caso';
$lang[387]	=	'Coloque cada arquivo em um arquivo separado';
?>
