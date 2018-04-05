<?php
if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}
class cafebazaar_ir extends DownloadClass {
	public function Download($link) {
        $page = $this->GetPage($link);
        if(!preg_match('/\?id=(.+)/', $link, $packagename) && !preg_match('/\/app\/(.+)\//', $link, $packagename)) html_error('Url not valid!');
        $packagename = $packagename[1];
        $ch = curl_init('http://ad.cafebazaar.ir/json/getAppDownloadInfo');
        curl_setopt($ch, CURLOPT_POSTFIELDS, '{"id":1,"hash":"'.sha1('{"7cc78271-e338-4edc-849c-b105c5d51ba5":["getAppDownloadInfo","'.$packagename.'"'.',19]}').'","packed":"S\/NNuBO0LXIyFIIo2UZ2gMhvQHttPoXiqAp3Z43Fz\/rUOSgphpIT+7Gx1fNYhqSm4zFG5Bx+jU1yW8\/FVZAnJAAYFf4bJuaABojX7OPQNqigm0wzRuq7b1TJuwpY0jam","iv":"mosLEvk1Ti0pNGEw0mW0tfRTuEuCoUBy\/prQyL4Xy5gujrp69k4OKHf6GxE9LLxcZjBKQuwswoxzGnMXpxwqNamE49LsP30Sd7i+ZPCT8N8uDiQos8h1kfUB02KDoPpQGsXktpEugQjxHFxoHve+25uAuU4WANND7KI\/LN3gI9A=","p2":"Cpo0+8o2CyXOlTd41Z\/3IaDOHy5ByDbmMBMRtHEVJfDvJCTgXpJFNlr7GTOZ5JMqI5jFm8xGtL9noYTiiIk5NUCDl27w3U3wXOCucTzulmLM+68Iigu4f9B2371liFsnLZr+i0IjnffAI63sQXLxh2njpfcCuKuUQneX\/LeSsqs=","p1":"aZaq4qYY32qIvnqI7svHcznKx1Pq0VuYQIpg9dCmI+2KHDRTu6hUlc7tfICcy0vn9YpSIl6vtsM1687c7As\/lSWoxYXVjQYgx2XvJko\/+vbboXZAhEnsUPaME3IQ97jGTLBsWY4ds4ZrR0iNR2uVyT+rGXiqGxaKxHgmyFwZd3E=","method":"getAppDownloadInfo","non_enc_params":"{\"device\":{\"mn\":16,\"abi\":\"x86\",\"sd\":19,\"bv\":\"7.5.1\",\"us\":{},\"ct\":\"\",\"id\":\"6cAUX_eAThCrjoUbSxgISg\",\"dd\":\"android\",\"co\":\"\",\"mc\":310,\"dm\":\"bignox\",\"do\":\"Nexus\",\"dpi\":240,\"abi2\":\"armeabi-v7a\",\"sz\":\"l\",\"dp\":\"nox\",\"pr\":\"\"},\"referer\":{\"name\":\"page_home|!EX!None_experiment|!VA!None_variation|row-0-Best New Apps and Games|0|not_initiated\"}}","params":[]}');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $json = json_decode(curl_exec($ch), true);
        curl_close($ch);

        preg_match('/<meta property="og:title".*?content="(.*?)"\/>/su', $page, $appname);
        $appname = str_replace('_', ' ', $appname[1]).'.apk';
        $appname = str_replace(['آ', 'ا', 'ب', 'پ', 'ت', 'ث', 'ج', 'چ', 'ح', 'خ', 'د', 'ذ', 'ر', 'ز', 'ژ', 'س', 'ش', 'ص', 'ض', 'ط', 'ظ', 'ع', 'غ', 'ف', 'ق', 'ک', 'گ', 'ل', 'م', 'ن', 'و', 'ه', 'ی', 'ي', 'ئ', 'أ', 'ة', 'ك', 'ء', '؟', 'إ', ' ', '‌'], ['a', 'a', 'b', 'p', 't', 's', 'j', 'ch', 'h', 'kh', 'd', 'z', 'r', 'z', 'zh', 's', 'sh', 's', 'z', 't', 'z', 'a', 'gh', 'f', 'gh', 'k', 'g', 'l', 'm', 'n', 'v', 'h', 'y', 'i', 'e', 'a', 't', 'k', 'e', '?', 'e', '-', ''], $appname);
        
        if(isset($json['result']['error'])) {
          html_error('App not found, or it\'s paid!');
        }
        $dlink = $json['result']['cp'][0].'apks/'.$json['result']['t'].'.apk';
        $this->RedirectDownload($dlink, $appname, 0, 0, $link);
    }
}

// [26-09-2017] Written by NimaH79.