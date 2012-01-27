<?php

if (!defined('RAPIDLEECH')) {
    require('../deny.php');
    exit;
}
// The Simplified Chinese language file
// These words has been html entitied to prevent wrong encodings

$lang[1] = '拒绝访问';
$lang[2] = '服务器拒绝执行您的请求';
$lang[3] = '您没有输入一个有效的e-mail地址';
$lang[4] = '分段大小不是数字';
$lang[5] = '未知URL类型，<span class="font-black">只使用<span class="font-blue">http</span> 或 <span class="font-blue">https</span> 或 <span class="font-blue">ftp</span> 协议</span>';
$lang[6] = '未指定保存此文件路径';
$lang[7] = '你不被允许从<span class="font-black">%1$s (%2$s)</span> 上下载内容 '; // %1$s = host name %2$s = host ip
$lang[8] = '重定向到';
$lang[9] = '无法更新的文件列表';
$lang[10] = '文件<b>%1$s</b> (<b>%2$s</b>) 以保存！<br />用时： <b>%3$s</b><br />平均速度： <b>%4$s KB/s</b><br /> '; // %1$s = filename %2$s = filesize %3$s = time of download %4$s = speed
$lang[11] = '<script>mail("文件将被发送到地址<b>%1$s</b>。", "%2$s");</script> '; // %1$s = E-mail address %2$s = filename
$lang[12] = '发送文件时出错！';
$lang[13] = '回到主页';
$lang[14] = '连接丢失，文件已删除。';
$lang[15] = '重新加载';
$lang[16] = '请更改调试模式到 <b>1</b>';
$lang[17] = '已达到最大连接数（%1$s）。 '; // %1$s = Number of maximum links
$lang[18] = '%1$s 链接%2$s 被检测到，用时 %3$s 秒。（方法：<b>%4$s</b>） '; // %1$s = Number of links %2$s = Plural form %3$s = seconds %4$s = method for checking links
$lang[19] = ''; // End of a plural
$lang[20] = '错误的代理服务器地址';
$lang[21] = '链接';
$lang[22] = '状态';
$lang[23] = '请稍候';
$lang[24] = '错误的URL地址';
$lang[25] = '准备';
$lang[26] = '已启动';
$lang[27] = '连接丢失';
$lang[28] = '已完成';
$lang[29] = '启动自动Transload';
$lang[30] = '不支持框架，请更新你的浏览器';
$lang[31] = '添加链接';
$lang[32] = '链接';
$lang[33] = '选项';
$lang[34] = 'Transload文件';
$lang[35] = '使用代理设置';
$lang[36] = '代理';
$lang[37] = '用戶名';
$lang[38] = '密码';
$lang[39] = '使用Imageshack帐户';
$lang[40] = '保存到';
$lang[41] = '路径';
$lang[42] = '使用高级帐户';
$lang[43] = '在服务器端运行';
$lang[44] = '延迟时间';
$lang[45] = '延迟（秒）';
$lang[46] = '没有选择上传的文件或主机';
$lang[47] = '选择要上传的主机';
$lang[48] = '不被支持的上传服务！';
$lang[49] = '上传窗口';
$lang[50] = '链接保存格式';
$lang[51] = '默认';
$lang[52] = '全部选中';
$lang[53] = '取消全部选中';
$lang[54] = '反向选择';
$lang[55] = '名称';
$lang[56] = '大小';
$lang[57] = '没有发现文件';
$lang[58] = '链接保存格式示例：（区分大小写）';
$lang[59] = '下载链接';
$lang[60] = '文件的名称';
$lang[61] = '默认链接样式';
$lang[62] = '除了上面以外的任何东西都将被视为字符串，你现在无法使用多行格式，新行将被插入到每个链接。';
$lang[63] = '上传文件 %1$s 到 %2$s '; // %1$s = filename %2$s = file host name
$lang[64] = '文件 %1$s 不存在。 '; // %1$s = filename
$lang[65] = '文件 %1$s 不可以用脚本读取。 '; // %1$s = filename
$lang[66] = '上传到主机的文件过大。';
$lang[67] = '上传服务不被允许';
$lang[68] = '下载链接';
$lang[69] = '删除链接';
$lang[70] = '状态链接';
$lang[71] = '管理员链接';
$lang[72] = '用户名';
$lang[73] = 'FTP上传';
$lang[74] = '密码';
$lang[75] = 'Rapidleech PlugMod - 上传链接';
$lang[76] = '<div class="linktitle">上传链接 <strong>%1$s</strong> - <span class="bluefont">大小： <strong>%2$s</strong></span></div> '; // %1$s = file name %2$s = file size
$lang[77] = '完成';
$lang[78] = '后退';
$lang[79] = '无法建立与服务器%1$s 的连接。 '; // %1$s = FTP server name
$lang[80] = '错误的用户名和/或密码。';
$lang[81] = '已连接到： <b>%1$s</b>... '; // %1$s = FTP server name
$lang[82] = '文件类型 %1$s 是禁止下载的 '; // %1$s = File type
$lang[83] = '文件 <b>%1$s</b>，大小 <b>%2$s</b>... '; // %1$s = file name %2$s = file size
$lang[84] = '检索链接时出错';
$lang[85] = '文本作为计数器字符串传递！';
$lang[86] = '错误：请启用JavaScript。';
$lang[87] = '请等待 <b>%1$s</b> 秒... '; // %1$s = number of seconds
$lang[88] = '无法连接到 %1$s 端口 %2$s '; // %1$s = host name %2$s = port
$lang[89] = '连接到代理： <b>%1$s</b> 端口 <b>%2$s</b>... '; // %1$s = Proxy host %2$s = Proxy port
$lang[90] = '已连接到： <b>%1$s</b> 端口 <b>%2$s</b>... '; // %1$s = host %2$s = port
$lang[91] = '没有收到标题';
$lang[92] = '你被禁止访问页面！';
$lang[93] = '该网页未找到！';
$lang[94] = '页面被禁止访问或没有找到！';
$lang[95] = '错误！它被重定向到 [%1$s] '; // %1$s = redirected address
$lang[96] = '这个网站需要授权。要指定访问的用户和密码需要使用类似以下格式的URL：<br />http:';
$lang[97] = '恢复超出限制';
$lang[98] = '此服务器不支持恢复';
$lang[99] = '下载';
$lang[100] = '白金帐号已用于另一个IP。';
$lang[101] = '不能保存文件 %1$s 到目录 %2$s 中 '; // %1$s = file name %2$s = directory name
$lang[102] = '尝试用chmod更改文件夹到777权限。';
$lang[103] = '重试';
$lang[104] = '文件';
$lang[105] = '不能完成记录到文件 %1$s '; // %1$s = file name
$lang[106] = '无效的网址或发生未知错误';
$lang[107] = '您已经达到了免费用户的限制。';
$lang[108] = '下载会话已过期';
$lang[109] = '错误的访问密钥';
$lang[110] = '你输入了错误的密钥的次数太多了';
$lang[111] = '超出下载限制';
$lang[112] = '读取数据错误';
$lang[113] = '发送数据错误';
$lang[114] = '活动';
$lang[115] = '不可用';
$lang[116] = '失效';
$lang[117] = '你需要加载/激活 cURL 扩展(http:';
$lang[118] = 'cURL启用';
$lang[119] = '推荐使用PHP version 5 但这不是强制的';
$lang[120] = '检查如果你的安全模式是关闭状态下，脚本将不能使用安全模式';
$lang[121] = '发送文件<b>%1$s</b> '; // %1$s = filename
$lang[122] = '不需要分割，发送单个邮件';
$lang[123] = '按 %1$s 大小进行分割 '; // %1$s = part size
$lang[124] = '方法';
$lang[125] = '发送部分 <b>%1$s</b> '; //%1$s = part number
$lang[126] = '不需要分割，发送单个邮件';
$lang[127] = '没有发现主机文件';
$lang[128] = '无法创建主机文件';
$lang[129] = '时 '; // Plural
$lang[130] = '时';
$lang[131] = '分 '; // Plural
$lang[132] = '分';
$lang[133] = '秒 '; // Plural
$lang[134] = '秒';
$lang[135] = 'getCpuUsage(): 无法访问STAT路径或STAT文件错误';
$lang[136] = 'CPU负载';
$lang[137] = '发生错误';
$lang[138] = '至少选择一个文件。';
$lang[139] = '电子邮件';
$lang[140] = '发送';
$lang[141] = '删除成功提交';
$lang[142] = '按分段分割';
$lang[143] = '分段大小';
$lang[144] = '<b>%1$s</b> - 无效的E-mail地址。 '; // %1$s = email address
$lang[145] = '未找到文件 <b>%1$s</b> ！'; // %1$s = filename
$lang[146] = '无法更新文件列表！';
$lang[147] = '禁用文件删除';
$lang[148] = '删除文件';
$lang[149] = '是';
$lang[150] = '否';
$lang[151] = '文件<b>%1$s</b> 已删除 '; // %1$s = filename
$lang[152] = '删除文件 <b>%1$s</b> 时遇到错误！ '; // %1$s = filename
$lang[153] = '主机';
$lang[154] = '端口';
$lang[155] = '文件夹';
$lang[156] = '上传成功后删除源文件';
$lang[157] = '保存FTP数据';
$lang[158] = '删除FTP数据';
$lang[159] = '无法找到文件夹 <b>%1$s</b> '; // %1$s = directory name
$lang[160] = '成功上传文件 %1$s ！'; // %1$s = filename
$lang[161] = '时间';
$lang[162] = '平均时速';
$lang[163] = '无法上传文件 <b>%1$s</b>！ '; // %1$s = filename
$lang[164] = '电子邮件';
$lang[165] = '删除成功提交';
$lang[166] = '错误的电子邮件地址';
$lang[167] = '请只选择 .crc 或 .001 文件！';
$lang[168] = '请选择 .crc 文件！';
$lang[169] = '请选择 .crc 或 .001 文件！';
$lang[170] = '执行CRC校验？（推荐）';
$lang[171] = 'CRC32校验模式';
$lang[172] = '使用 哈希文件（推荐）';
$lang[173] = '读文件到内存';
$lang[174] = '假CRC';
$lang[175] = '成功合并后删除源文件';
$lang[176] = '公告';
$lang[177] = '不会检查此文件的大小和CRC32';
$lang[178] = '无法读取 .CRC 文件！';
$lang[179] = '错误，输出文件已经存在 <b>%1$s</b> '; // %1$s = filename
$lang[180] = '错误，缺失或不完整的分段';
$lang[181] = '错误，文件类型 %1$s 是禁止的 '; // Filetype
$lang[182] = '目标文件 <b>%1$s</b> 是不可能打开的 '; // %1$s = filename
$lang[183] = '写入文件 <b>%1$s</b> 错误！'; // %1$s = filename
$lang[184] = 'CRC32校验不匹配！';
$lang[185] = '成功合并文件 <b>%1$s</b> '; // %1$s = filename
$lang[186] = '删除';
$lang[187] = '未删除';
$lang[188] = '添加扩展';
$lang[189] = '不包括';
$lang[190] = '到';
$lang[191] = '重命名？';
$lang[192] = '取消';
$lang[193] = '重命名文件 <b>%1$s</b> 错误 '; // %1$s = filename
$lang[194] = '文件 <b>%1$s</b> 已更名为 <b>%2$s</b> '; // %1$s = original filename %2$s = renamed filename
$lang[195] = '存档名称';
$lang[196] = '请输入存档名称！';
$lang[197] = '错误 存档没有创建。';
$lang[198] = '文件 %1$s 是一个打包 '; // %1$s = filename
$lang[199] = '打包在存档 <b>%1$s</b> 里 '; // %1$s = filename
$lang[200] = '错误，存档是空的。';
$lang[201] = '新名称';
$lang[202] = '无法重命名文件 <b>%1$s</b>！'; // %1$s = filename
$lang[203] = '成功分割后删除源文件';
$lang[204] = '文件和文件夹';
$lang[205] = '解压';
$lang[206] = 'YouTube 视频格式选择器';
$lang[207] = '链接到Transload';
$lang[208] = '引用';
$lang[209] = 'Transload文件';
$lang[210] = '用户名 &amp; 密码 (HTTP/FTP)';
$lang[211] = '用户';
$lang[212] = '密码';
$lang[213] = '添加注释';
$lang[214] = '插件选项';
$lang[215] = '禁用所有插件';
$lang[216] = 'YouTube 视频格式选择器';
$lang[217] = '直接链接';
$lang[218] = '&amp;fmt=';
$lang[219] = '自动获取可用的最高质量格式';
$lang[220] = '17 [视频： 3GP 176x144 | 音频： AAC 2ch 44.10kHz]';
$lang[221] = '5 [视频： FLV 400x240 | 音频： MP3 1ch 22.05kHz]';
$lang[222] = '34 [视频： FLV 640x360 | 音频： AAC 2ch 44.10kHz]';
$lang[223] = '35 [视频： FLV 854x480 | 音频： AAC 2ch 44.10kHz]';
$lang[224] = '43 [视频： WebM 640x360 | 音频： Vorbis 2ch 44.10kHz]';
$lang[225] = '45 [视频： WebM 1280x720 | 音频： Vorbis 2ch 44.10kHz]';
$lang[226] = '18 [视频： MP4 480x360 | 音频： AAC 2ch 44.10kHz]';
$lang[227] = '22 [视频： MP4 1280x720 | 音频： AAC 2ch 44.10kHz]';
$lang[228] = '37 [视频： MP4 1920x1080 | 音频： AAC 2ch 44.10kHz]';
$lang[229] = 'ImageShack&reg; Torrent 服务';
$lang[230] = '用户名';
$lang[231] = '密码';
$lang[232] = 'Megaupload.com Cookie 值';
$lang[233] = '用户';
$lang[234] = '使用 vBulletin 插件';
$lang[235] = '附加Cookie值';
$lang[236] = 'Key=Value';
$lang[237] = '发送文件到电子邮件';
$lang[238] = '电子邮件';
$lang[239] = '分割文件';
$lang[240] = '方法';
$lang[241] = 'Total Commander';
$lang[242] = 'RFC 2046';
$lang[243] = '分段大小';
$lang[244] = 'MB';
$lang[245] = '使用代理设置';
$lang[246] = '代理';
$lang[247] = '用户名';
$lang[248] = '密码';
$lang[249] = '使用高级帐户';
$lang[250] = '用户名';
$lang[251] = '密码';
$lang[252] = '保存到';
$lang[253] = '路径';
$lang[254] = '保存设置';
$lang[255] = '清除当前设置';
$lang[256] = '全部选中';
$lang[257] = '取消全部选中';
$lang[258] = '反向选择';
$lang[259] = '显示';
$lang[260] = '下载';
$lang[261] = '所有';
$lang[262] = '名称';
$lang[263] = '大小';
$lang[264] = '注释';
$lang[265] = '日期';
$lang[266] = '没有发现文件';
$lang[267] = '支持以下网站';
$lang[268] = '排除以下支持';
$lang[269] = '调试模式';
$lang[270] = '只显示链接';
$lang[271] = '只显示删除链接';
$lang[272] = '检查链接';
$lang[273] = '载入中...';
$lang[274] = '处理中，请稍候...';
$lang[275] = '服务器空间';
$lang[276] = '在使用';
$lang[277] = '剩余空间';
$lang[278] = '磁盘空间';
$lang[279] = 'CPU';
$lang[280] = '服务器时间';
$lang[281] = '本地时间';
$lang[282] = '自动删除';
$lang[283] = '小时后Transload';
$lang[284] = '分钟后 Transload';
$lang[285] = '动作';
$lang[286] = '上传';
$lang[287] = 'FTP文件';
$lang[288] = '电子邮件';
$lang[289] = '大量电子邮件';
$lang[290] = '分割文件';
$lang[291] = '合并文件';
$lang[292] = 'MD5哈希';
$lang[293] = '包文件';
$lang[294] = '制作ZIP文件';
$lang[295] = '解压Zip文件';
$lang[296] = '重命名';
$lang[297] = '批量重命名';
$lang[298] = '删除';
$lang[299] = '链接列表';
$lang[300] = '检索下载页面';
$lang[301] = '输入';
$lang[302] = '这里';
$lang[303] = '下载文件';
$lang[304] = 'configs/files.lst 无法写入，请确认chmod 权限到 777';
$lang[305] = '&nbsp; 选的的下载路径无法写入。请CHMOD到777';
$lang[306] = '合并文件';
$lang[307] = '请稍候';
$lang[308] = '通过';
$lang[309] = '失败';
$lang[310] = '没有开启这个你可能会收到警告';
$lang[311] = '您可能无法查看服务器的信息';
$lang[312] = '你的服务器可能不支持超过2G的文件';
$lang[313] = 'Rapidleech检查脚本';
$lang[314] = 'fsockopen';
$lang[315] = 'memory_limit';
$lang[316] = 'safe_mode';
$lang[317] = 'cURL';
$lang[318] = 'allow_url_fopen';
$lang[319] = 'PHP版本 -';
$lang[320] = 'allow_call_time_pass_reference';
$lang[321] = 'passthru';
$lang[322] = '磁盘空间功能';
$lang[323] = 'Apache版本 -';
$lang[324] = '输入代理地址错误';
$lang[325] = '文件保存成功！';
$lang[326] = '保存笔记';
$lang[327] = 'Notes';
$lang[328] = '禁止动作';
$lang[329] = '主窗口';
$lang[330] = '设置';
$lang[331] = '服务器文件';
$lang[332] = '链接检查器';
$lang[333] = '插件';
$lang[334] = '自动Transload';
$lang[335] = '自动上传';
$lang[336] = '文件大小是有限制的在';
$lang[337] = '文件大小限制：';
$lang[338] = '制作RAR文件';
$lang[339] = '解压RAR文件';
$lang[340] = '检测到错误';
$lang[341] = '点击这里展开';
$lang[342] = '你可以从这里拖动窗口';
$lang[343] = '无法找到"rar"<br /> 你需要下载“RAR”并解压它到"/rar/" 文件夹下';
$lang[344] = '文件将会被存档：';
$lang[345] = '存档名称:';
$lang[346] = '选项：';
$lang[347] = '压缩等级：';
$lang[348] = '储存';
$lang[349] = '最快';
$lang[350] = '快速';
$lang[351] = '标准';
$lang[352] = '较好';
$lang[353] = '最好';
$lang[354] = '创建卷';
$lang[355] = '存档后删除文件';
$lang[356] = '创建固实存档';
$lang[357] = '创建恢复记录';
$lang[358] = '压缩完后测试存档';
$lang[359] = '使用密码';
$lang[360] = '加密文件名';
$lang[361] = '设置存档内部路径';
$lang[362] = 'RAR';
$lang[363] = '创建存档： <b>%1$s</b>';
$lang[364] = '请稍候';
$lang[365] = '返回到文件列表';
$lang[366] = '<b>从文件 %1$s</b>：';
$lang[367] = '无法找到 “unrar”';
$lang[368] = '显示文件列表需要密码：';
$lang[369] = '解压文件需要密码：';
$lang[370] = '错误：%1$s';
$lang[371] = '再次尝试显示列表';
$lang[372] = '选择非rar';
$lang[373] = '<b>解压缩文件 %1$s</b>：';
$lang[374] = '状态：';
$lang[375] = '选择文本';
$lang[376] = '高级帐户：';
$lang[377] = '38 [视频： MP4 4096x3072 | 音频： AAC 2ch 44.10kHz]';
$lang[378] = '关闭窗口';
$lang[379] = '文件';
$lang[380] = 'MD5 更改只能应用于已知可操作格式（如 .rar 或 .zip）上，<br />你要继续吗？';
$lang[381] = '文件 <b>%1$s</b> 的MD5已更改 '; // %1$s = filename
$lang[382] = '更改文件 <b>%1$s</b> 的MD5出错！ '; // %1$s = filename
$lang[383] = 'MD5更改';
$lang[384] = '按文本匹配';
$lang[385] = '匹配';
$lang[386] = '忽略大小写';
$lang[387] = '每个文件放在一个单独的存档中';
$lang[388] = 'OpenSSL';
$lang[389] = '44 [视频： WebM 854x480 | 音频： Vorbis 2ch 44.10kHz]';
?>