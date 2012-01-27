<?php

if (!defined('RAPIDLEECH')) {
    require('../deny.php');
    exit;
}
// Translated by Laztrix www.diviksfilm.com
// The Turkish language file
// You should always use this file as a template for translating

$lang[1] = 'Erişim Engellendi.';
$lang[2] = 'Sunucu isteğinizi geri çevirdi';
$lang[3] = 'Gerçerli bir e-mail adresi belirtmediniz.';
$lang[4] = 'Parçaların boyutları numerik değil';
$lang[5] = 'Bilinmeyen URL biçimi, <span class="font-black">Sadece <span class="font-blue">http</span> ya da <span class="font-blue">https</span> or <span class="font-blue">ftp</span> Protokolü kullanınız.</span>';
$lang[6] = 'Dosyanın kaydedilecek yolu belirtilmemiş';
$lang[7] = 'şu adresten indirmek için yeterli izniniz bulunmuyor: <span class="font-black">%1$s (%2$s)</span>'; // %1$s = host name %2$s = host ip
$lang[8] = 'Adrese Yönlendiriliyor:';
$lang[9] = 'Dosya listesi güncellenemedi.';
$lang[10] = 'Dosya(lar) <b>%1$s</b> (<b>%2$s</b>) Kaydedildi!<br />Süre: <b>%3$s</b><br />Ortalama Hız: <b>%4$s KB/s</b><br />'; // %1$s = filename %2$s = filesize %3$s = time of download %4$s = speed
$lang[11] = '<script>mail("Dosya E-Mail Adresine Gönerildi.<b>%1$s</b>.", "%2$s");</script>'; // %1$s = E-mail address %2$s = filename
$lang[12] = 'Dosya Gönerimi Başarısız!';
$lang[13] = 'Başa Dön';
$lang[14] = 'Bağlantı Kaybedildi, Dosya yitirildi.';
$lang[15] = 'Tekrar Yükle';
$lang[16] = 'Debug Modunu şununla değiştiriniz: <b>1</b>';
$lang[17] = 'Azami dosya (%1$s) sayısına erişildi.'; // %1$s = Number of maximum links
$lang[18] = '%1$s Bağlantı%2$lar %3$ sürede kontrol edildi. (Yöntem: <b>%4$s</b>)'; // %1$s = Number of links %2$s = Plural form %3$s = seconds %4$s = method for checking links
$lang[19] = 'lar'; // End of a plural
$lang[20] = 'Hatalı vekil sunucu adresi';
$lang[21] = 'Bağlantı';
$lang[22] = 'Durum';
$lang[23] = 'Bekleme';
$lang[24] = 'Gerçersiz Bağlantı';
$lang[25] = 'Hazırlanıyor';
$lang[26] = 'Başladı';
$lang[27] = 'Bağlantı Yitirildi';
$lang[28] = 'Tamamlandı';
$lang[29] = 'Otomatik Indirmeyi Başlat';
$lang[30] = 'Tarayıcınız Çerçeve (Frame) desteklemiyor. Tarayıcınızı değiştiriniz.';
$lang[31] = 'Bağlantı Ekle';
$lang[32] = 'Bağlantılar';
$lang[33] = 'Seçenekler';
$lang[34] = 'Dosyaları Indir';
$lang[35] = 'Ğroxy Sunucu Ayarı Kullan';
$lang[36] = 'Proxy';
$lang[37] = 'Kullanıcı Adı';
$lang[38] = 'Şifre';
$lang[39] = 'Imageshack Hesabını Kullan';
$lang[40] = 'Kaydedilecek Yer';
$lang[41] = 'Dosya Yolu';
$lang[42] = 'Premium Hesap Kullan';
$lang[43] = 'Server Tarafından Yürüt';
$lang[44] = 'Gecikme Süresi';
$lang[45] = 'Gecikme (saniye bazında)';
$lang[46] = 'Yüklemek için herhangi bir dosya seçilmedi';
$lang[47] = 'Host Seçimini Yapınız';
$lang[48] = 'Desteklenen Yükleme Servisi Bulunmuyor!';
$lang[49] = 'Yükleme Penceresi';
$lang[50] = 'Link Kaydetme Formatı';
$lang[51] = 'Default';
$lang[52] = 'Hepsini Seç';
$lang[53] = 'Seçimi Kaldır';
$lang[54] = 'Seçimi Tersine Döndür';
$lang[55] = 'İsim';
$lang[56] = 'Boyut';
$lang[57] = 'Dosya Bulunamadı';
$lang[58] = 'Legend for link saving format: (case sensitive)';
$lang[59] = 'Indirme Bağlantısı';
$lang[60] = 'Dosya Adı';
$lang[61] = 'Varsayılan Bağlantı Biçimi';
$lang[62] = 'Anything besides the ones stated above will be treated as string, you are unable to do multi line format now, a new line will be inserted for each link.';
$lang[63] = '%1$s Dosyası %2$s adresine yükleniyor...'; // %1$s = filename %2$s = file host name
$lang[64] = '%1$s Dosyası mevcut değil'; // %1$s = filename
$lang[65] = '%1$s Dosyası yazılım tarafından algılanamadı (tanınamadı).'; // %1$s = filename
$lang[66] = 'Dosya boyutu izin verilen boyutları aşıyor.';
$lang[67] = 'Upload izni yok';
$lang[68] = 'Indirme Bağlantısı';
$lang[69] = 'Silme Bağlantısı';
$lang[70] = 'Istatistik Bağlantısı';
$lang[71] = 'Yönetici Bağlantısı';
$lang[72] = 'Kullanıcı Adı';
$lang[73] = 'FTP ile yükleme';
$lang[74] = 'Şifre';
$lang[75] = 'Rapidleech PlugMod - Upload Linkler';
$lang[76] = '<div class="linktitle">%1$s<strong> için upload linkleri </strong> - <span class="bluefont">Boyut: <strong>%2$s</strong></span></div>'; // %1$s = file name %2$s = file size
$lang[77] = 'Tamamlandı';
$lang[78] = 'Geri Dön';
$lang[79] = '%1$s Sunucusu ile bağlantı kurulamadı.'; // %1$s = FTP server name
$lang[80] = 'Hatalı şifre ya da kullanıcı adı.';
$lang[81] = 'Bağlanılan Sunucu: <b>%1$s</b>...'; // %1$s = FTP server name
$lang[82] = '%1$s dosya türünün indirilmesi yasaklanmıış.'; // %1$s = File type
$lang[83] = 'Dosya <b>%1$s</b>, Boyut <b>%2$s</b>...'; // %1$s = file name %2$s = file size
$lang[84] = 'Işlem Hatası...';
$lang[85] = 'Text passed as counter is string!';
$lang[86] = 'Hata: Lütfen JavaScript Etkinleştiriniz.';
$lang[87] = 'Lütfen <b>%1$s</b> saniye bekleyiniz...'; // %1$s = number of seconds
$lang[88] = '%1$s sunucusuna %2$s portu üzerinden bağlanılamadı.'; // %1$s = host name %2$s = port
$lang[89] = 'Veekil sunucusuna bağlanıldı: <b>%1$s</b> port numarası <b>%2$s</b>...'; // %1$s = Proxy host %2$s = Proxy port
$lang[90] = '<b>%1$s</b> Sunusuna portu üzerinden bağlanıldı. <b>%2$s</b>...'; // %1$s = host %2$s = port
$lang[91] = 'Bağlığa ulaşılamadı';
$lang[92] = 'Bu sayfaya erişiminiz durduruldu!';
$lang[93] = 'Sayfa bulunamadı!';
$lang[94] = 'bu sayfa ya yasaklı ya da bulunamadı!';
$lang[95] = 'Hata! [%1$s] adresine yönlendirildi'; // %1$s = redirected address
$lang[96] = 'Bu site kullanıcı doğrulaması istemektedir. Kullanıcı adı ve şifreyi şu formatta kullanarak erişimde bulunabilirsiniz:<br />http://<b>login:password@</b>www.sitenizin adresi.com/dosyaadi.exe';
$lang[97] = 'Azami boyuta erişildi (indirme limiti)';
$lang[98] = 'Bu sunucu sürdürme özelliğini desteklemiyor';
$lang[99] = 'Indirme';
$lang[100] = 'Bu premium hesabı başka bir IP üzerinden halihazırda kullanılıyor.';
$lang[101] = '%1$s Dosyası %2$s dizinine kaydedilemiyor.'; // %1$s = file name %2$s = directory name
$lang[102] = 'Klasörün yazma izinlerini 777 olarak ayarlayınız.';
$lang[103] = 'Tekrar Deneyiniz.';
$lang[104] = 'Dosya';
$lang[105] = 'It is not possible to carry out a record in the file %1$s'; // %1$s = file name
$lang[106] = 'Hatalı bağlantı girildi ya da bilinmeyen bir hata oluştu';
$lang[107] = 'Ücretsiz kullanıcılar için azami indirme boyutuna eriştiniz.';
$lang[108] = 'Indirme oturumu sona erdi.';
$lang[109] = 'Hatalı giriş kodu.';
$lang[110] = 'Giriş kodunu birçok kez yanlış girdiniz';
$lang[111] = 'Indirme Limiti Aşıldı';
$lang[112] = 'Verinin Okunmasında Hata Oluştu';
$lang[113] = 'Verinin Gönderiminde Hata Oluştu';
$lang[114] = 'Etkin';
$lang[115] = 'Erişim şu An Için Mümkün değil';
$lang[116] = 'Ölü';
$lang[117] = 'Sunucunuzda CURL özelliği etkin olması gerekiyor (http://www.php.net/cURL) yada  config.php yi \'fgc\' => 1 şeklinde ayarlayın .';
$lang[118] = 'cURL Etkin';
$lang[119] = 'PHP 5 sürümü tavsiye ediliyor ancak yazılımın çalışması için bir zorunluluğu yok';
$lang[120] = 'Sunucunuzda Güvenlik Kipi\'nin (Safe Mode) kapalı olduğundan emin olunuz. Yazılım güvenlik kipi açıkken çalışmamamktadır. ';
$lang[121] = 'Dosya gönderiliyor <b>%1$s</b>'; // %1$s = filename
$lang[122] = 'Parçalamaya gerek yok, tek e-mail gönder';
$lang[123] = '%1$s boyutları ile barçalanıyor'; // %1$s = part size
$lang[124] = 'Yöntem';
$lang[125] = 'Parça Gönderiliyor <b>%1$s</b>'; //%1$s = part number
$lang[126] = 'Parçalamaya gerek yok, tek e-mail gönder';
$lang[127] = 'Host Dosya bulunamadı';
$lang[128] = 'Host dosyaları oluşturulamıyor';
$lang[129] = 'saatler'; // Plural
$lang[130] = 'saat';
$lang[131] = 'dakikalar'; // Plural
$lang[132] = 'dakika';
$lang[133] = 'saniyeler'; // Plural
$lang[134] = 'saniye';
$lang[135] = 'getCpuUsage(): Durum için gerekli dosya yoluna yada dosyasına ulaşılamıyor';
$lang[136] = 'Sunucu Yükü (CPU)';
$lang[137] = 'Bir hata oluştu';
$lang[138] = 'En azından bir dosya seçiniz.';
$lang[139] = 'E-Mailler';
$lang[140] = 'Gönder';
$lang[141] = 'Başarılı Gönderimleri Sil';
$lang[142] = 'Parçalara Böl';
$lang[143] = 'Parça Boyutu';
$lang[144] = '<b>%1$s</b> - Geçersiz E-Mail adresi.'; // %1$s = email address
$lang[145] = '<b>%1$s</b> dosyası bulunamadı!'; // %1$s = filename
$lang[146] = 'Dosya listesi yenilenemedi!';
$lang[147] = 'Dosya silinmesine izin verilmiyor';
$lang[148] = 'Dosyaları Sil';
$lang[149] = 'Evet';
$lang[150] = 'Hayır';
$lang[151] = '<b>%1$s</b> Dosyası silindi.'; // %1$s = filename
$lang[152] = '<b>%1$s</b> dosyasını silerken hata oluştu!'; // %1$s = filename
$lang[153] = 'Host';
$lang[154] = 'Port';
$lang[155] = 'Dizin';
$lang[156] = 'Kaynak dosyalarını başarılı yükleme sonrası sil';
$lang[157] = 'Dosyaları Kopyala';
$lang[158] = 'Dosyaları Taşı';
$lang[159] = '<b>%1$s</b> Dizini Tesbit Edilemedi.'; // %1$s = directory name
$lang[160] = '%1$s dosyası başarılı bir şekilde yüklendi!'; // %1$s = filename
$lang[161] = 'Zaman';
$lang[162] = 'Ortalama Hız';
$lang[163] = '<b>%1$s</b> Dosyası yüklenilemedi!'; // %1$s = filename
$lang[164] = 'Email';
$lang[165] = 'Başarılı gönderimleri sil';
$lang[166] = 'Hatalı E-Mail Adresi';
$lang[167] = 'Lütfen Sadece .crc ya da .001 dosyasını seçiniz!';
$lang[168] = 'lütfen.crc dosyasını seçiniz!';
$lang[169] = 'Lütfen .crc ya da .001 dosyasını seçiniz!';
$lang[170] = 'CRC Testi Yap? (önerilen)';
$lang[171] = 'CRC32 Kontrol modu';
$lang[172] = 'hash_file Kullan(önerilen)';
$lang[173] = 'Dosya hafızasını oku';
$lang[174] = 'Sahte crc';
$lang[175] = 'Kaynak dosyalarını başarılı birleştirme sonrasında sil';
$lang[176] = 'Not';
$lang[177] = 'Dosya boyutu ile crc32 dosyası uyum göstermiyor';
$lang[178] = '.crc dosyası okunamıyor!';
$lang[179] = 'Hata, <b>%1$s</b> hedefe çıkarılacak dosya halihazırda mevcut'; // %1$s = filename
$lang[180] = 'Hata, hatalı ya da eksik parça';
$lang[181] = 'Hata, %1$s dosa biçimi sistem tarafından engelli.'; // Filetype
$lang[182] = '<b>%1$s</b> hedefe dosyanın oluşturması mümkün değil'; // %1$s = filename
$lang[183] = '<b>%1$s</b> dosyasını yazarken hata!'; // %1$s = filename
$lang[184] = 'CRC32 checksum uymuyor!';
$lang[185] = '<b>%1$s</b> dosyası başarılı bir şekilde birleştirildi'; // %1$s = filename
$lang[186] = 'silindi';
$lang[187] = 'silinmedi';
$lang[188] = 'Uzantı Ekle';
$lang[189] = 'siz';
$lang[190] = 'ye';
$lang[191] = 'Ad Değiştir?';
$lang[192] = 'Iptal';
$lang[193] = '<b>%1$s</b> dosyasını isimlendirirken hata'; // %1$s = filename
$lang[194] = '<b>%1$s</b> dosyasıının adı <b>%2$s</b> olarak değiştirildi.'; // %1$s = original filename %2$s = renamed filename
$lang[195] = 'Arşiv Adı';
$lang[196] = 'Lütfen bir arşiv adı giriniz!';
$lang[197] = 'Hata, arşiv oluşturulamadı.';
$lang[198] = '%1$s dosyası paketlendi.'; // %1$s = filename
$lang[199] = 'Packed in archive <b>%1$s</b>'; // %1$s = filename
$lang[200] = 'Hata, arşiv boş.';
$lang[201] = 'Yeni Ad';
$lang[202] = '<b>%1$s</b> dosyasının ismi değiştirilemedi!'; // %1$s = filename
$lang[203] = 'Başarılı bölme işleminden sonra kaynak dosyayı sil';
$lang[204] = 'dosyalar ve dizinler';
$lang[205] = 'Unzip';
$lang[206] = 'YouTube Video Format Seçimi';
$lang[207] = 'Sunucuya indirilecek Link';
$lang[208] = 'Yönlendirme';
$lang[209] = 'Sunucuya indir';
$lang[210] = 'Kullanıcı adı &amp; Şifre (HTTP/FTP)';
$lang[211] = 'Kullanıcı adı';
$lang[212] = 'Şifre';
$lang[213] = 'Açıklama ekle';
$lang[214] = 'Eklenti ayarları';
$lang[215] = 'Bütün eklentileri iptal et';
$lang[216] = 'YouTube Video Format Seçici';
$lang[217] = 'Direk Link';
$lang[218] = '&amp;fmt=';
$lang[219] = 'Otomatik en yüksek kalitede format seçimi';
$lang[220] = '17 [Video: 3GP 176x144 | Audio: AAC 2ch 44.10kHz]';
$lang[221] = '5 [Video: FLV 400x240 | Audio: MP3 1ch 22.05kHz]';
$lang[222] = '34 [Video: FLV 640x360 | Audio: AAC 2ch 44.10kHz]';
$lang[223] = '35 [Video: FLV 854x480 | Audio: AAC 2ch 44.10kHz]';
$lang[224] = '43 [Video: WebM 640x360 | Audio: Vorbis 2ch 44.10kHz]';
$lang[225] = '45 [Video: WebM 1280x720 | Audio: Vorbis 2ch 44.10kHz]';
$lang[226] = '18 [Video: MP4 480x360 | Audio: AAC 2ch 44.10kHz]';
$lang[227] = '22 [Video: MP4 1280x720 | Audio: AAC 2ch 44.10kHz]';
$lang[228] = '37 [Video: MP4 1920x1080 | Audio: AAC 2ch 44.10kHz]';
$lang[229] = 'ImageShack&reg; TorrentService';
$lang[230] = 'Kullanıcı Adı';
$lang[231] = 'Şifre';
$lang[232] = 'Megaupload.com Cookie Değeri';
$lang[233] = 'Kullanıcı';
$lang[234] = 'vBulletin eklentisi kullan';
$lang[235] = 'Ek çerez değeri';
$lang[236] = 'Ahahtar=Değer';
$lang[237] = 'Dosyayı emaile gönder';
$lang[238] = 'Email';
$lang[239] = 'Dosyayı böl';
$lang[240] = 'Metod';
$lang[241] = 'Total Commander';
$lang[242] = 'RFC 2046';
$lang[243] = 'Parça boyutu';
$lang[244] = 'MB';
$lang[245] = 'Proxy ayarlarını kullan';
$lang[246] = 'Proxy';
$lang[247] = 'Kullanıcı adı';
$lang[248] = 'Şifre';
$lang[249] = 'Premium hesap kullan';
$lang[250] = 'Kullanıcı adı';
$lang[251] = 'Şifre';
$lang[252] = 'Dosya kayıt yeri';
$lang[253] = 'Yol';
$lang[254] = 'Ayarları Kaydet';
$lang[255] = 'Geçerli Ayarları Temizle';
$lang[256] = 'Tümünü İşaretle';
$lang[257] = 'Bütün işaretleri Kaldır';
$lang[258] = 'Tersine Çevir';
$lang[259] = 'İndirilenleri';
$lang[260] = 'Göster';
$lang[261] = 'Göster,';
$lang[262] = 'Ad';
$lang[263] = 'Boyut';
$lang[264] = 'Açıklama';
$lang[265] = 'Tarih';
$lang[266] = 'Dosya bulunamadı';
$lang[267] = 'İle çalışır';
$lang[268] = 'Temizle';
$lang[269] = 'Debug Mode';
$lang[270] = 'Sadece Linkleri göster';
$lang[271] = 'Sadece Linkleri temzile';
$lang[272] = 'Linkleri Kontrol et';
$lang[273] = 'Yükleniyor...';
$lang[274] = 'İşlem sürüyor, Lütfen bekleyin...';
$lang[275] = 'Server Alanı';
$lang[276] = 'Kullanılan';
$lang[277] = 'Boş Alan';
$lang[278] = 'Disk Alanı';
$lang[279] = 'CPU';
$lang[280] = 'Server Saati';
$lang[281] = 'Yerel Saat';
$lang[282] = 'Otomatik sil';
$lang[283] = 'Hours After Transload';
$lang[284] = 'Minutes After Transload';
$lang[285] = 'İşlem';
$lang[286] = 'Yükle';
$lang[287] = 'FTP Yükleme';
$lang[288] = 'E-Mail';
$lang[289] = 'Toplu e-posta';
$lang[290] = 'Dosyaları Böl';
$lang[291] = 'Dosyaları Birleştir';
$lang[292] = 'MD5 Hash';
$lang[293] = 'Dosya Paketle';
$lang[294] = 'Dosya ziple';
$lang[295] = 'Dosya Unziple';
$lang[296] = 'Tekrar adlandır';
$lang[297] = 'Toplu ad değiştirme';
$lang[298] = 'Sil';
$lang[299] = 'Linkleri Listele';
$lang[300] = 'Retrieving download page';
$lang[301] = 'Giriş';
$lang[302] = 'Buraya';
$lang[303] = 'Dosyayı indir';
$lang[304] = 'configs/files.lst yazılabilir değil, chmod 777 olduğuna emin olun';
$lang[305] = '&nbsp; Seçtiğiniz &nbsp; yolu yazlılabilir değil. chmod  777 olduğuna emin olun';
$lang[306] = 'Dosya birleştiriliyor';
$lang[307] = 'Bekliyor';
$lang[308] = 'Geçildi';
$lang[309] = 'Başarısız';
$lang[310] = 'You might see warnings without this turned on';
$lang[311] = 'Server durumunu çalıştıramıyabilirsiniz.';
$lang[312] = 'Serveriniz 2 gb dan büyük dosyaları desteklemiyor olabilir';
$lang[313] = 'Rapidleech Kontrol Script';
$lang[314] = 'fsockopen';
$lang[315] = 'memory_limit';
$lang[316] = 'safe_mode';
$lang[317] = 'cURL';
$lang[318] = 'allow_url_fopen';
$lang[319] = 'PHP Version - ';
$lang[320] = 'allow_call_time_pass_reference';
$lang[321] = 'passthru';
$lang[322] = 'Disk Space Functions';
$lang[323] = 'Apache Version - ';
$lang[324] = 'Yanlış proxy adresi girildi';
$lang[325] = 'Dosya başarılı kaydedildi!';
$lang[326] = 'Notu kaydet';
$lang[327] = 'Notlar';
$lang[328] = 'Eylemleri İptal et';
$lang[329] = 'Ana Sayfa';
$lang[330] = 'Ayarlar';
$lang[331] = 'Server Dosyaları';
$lang[332] = 'Link Kontrol';
$lang[333] = 'Eklenti';
$lang[334] = 'Toplu indirme';
$lang[335] = 'Toplu yükleme';
$lang[336] = 'Dosya boyutu sınırlandırma ';
$lang[337] = 'Dosya Boyut Sınırı: ';
$lang[338] = 'Rarla';
$lang[339] = 'Çıkart';
$lang[340] = 'Hata Bulundu';
$lang[341] = 'Genişletmek İçin tıkla';
$lang[342] = 'Pencereyi Buradan Sürükleyebilirsiniz';
$lang[343] = 'Can not find "rar"<br />You may need to download it and extract "rar" to "/rar/" directory';
$lang[344] = 'Arşivlenecek Dosya Adları:';
$lang[345] = 'Arşiv Adı:';
$lang[346] = 'Seçenekler:';
$lang[347] = 'Sıkıştırma Hızı:';
$lang[348] = 'ÇokYavaş';
$lang[349] = 'En Hızlı';
$lang[350] = 'Hızlı';
$lang[351] = 'Normal';
$lang[352] = 'İyi';
$lang[353] = 'En İyi';
$lang[354] = 'Parçala';
$lang[355] = 'Arşiv Oluşturduktan Sonra Dosyayı Sil';
$lang[356] = 'Tek Arşiv Oluştur';
$lang[357] = 'Kurtarma Kaydı Oluştur';
$lang[358] = 'Sıkıştırma Akabinde Kontrol Et';
$lang[359] = 'Şifre Kullan';
$lang[360] = 'Dosya Adlarını Kriptola';
$lang[361] = 'Arşiv İçinde Dosya Yolu Göster';
$lang[362] = 'Rar';
$lang[363] = 'Arşiv Oluşturuluyor: <b>%1$s</b>';
$lang[364] = 'Bekleniyor...';
$lang[365] = 'Dosya Listesine Geri Dön';
$lang[366] = '<b>Dosylar %1$s</b>:';
$lang[367] = '"unrar" Bulunamıyor';
$lang[368] = 'Dosyaların Listelenmesi İçin Şifre Gerekiyor:';
$lang[369] = 'Dosyaları Çıkartmak İçin Şifre Gerekiyor:';
$lang[370] = 'Hata:%1$s';
$lang[371] = 'Tekrar Listelemeye Çalış';
$lang[372] = 'Rardan Çıkart';
$lang[373] = '<b>%1$s Arşivinden Dosya Çıkartılıyor</b>:';
$lang[374] = 'Durum:';
$lang[375] = 'Metni Seç';
$lang[376] = 'Premium Accounts :';
$lang[377] = '38 [Video: MP4 4096x3072 | Audio: AAC 2ch 44.10kHz]';
$lang[378] = 'Pencereyi Kapat';
$lang[379] = 'Dosyalar';
$lang[380] = 'MD5 change should only be applied to known working formats(i.e. .rar or .zip)<br />Do you want to continue?';
$lang[381] = 'MD5 of file <b>%1$s</b> changed'; // %1$s = filename
$lang[382] = 'Error changing the MD5 of the file <b>%1$s</b>!'; // %1$s = filename
$lang[383] = 'MD5 Değiştirme';
$lang[384] = 'Metni Eşleştir';
$lang[385] = 'Eşleştir';
$lang[386] = 'Durumu Gözardı Et';
$lang[387] = 'Her Dosyayı Ayrı Bir Arşive koy';
$lang[388] = 'OpenSSL';
$lang[389] = '44 [Video: WebM 854x480 | Audio: Vorbis 2ch 44.10kHz]';
?>