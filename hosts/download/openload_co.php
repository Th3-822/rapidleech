<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}

class openload_co extends DownloadClass {
	private $page, $cookie = array(), $elink, $pA, $DLRegexp = '@https?://\w+\.(?:openload\.co|oloadcdn\.net)/dl/[^\t\r\n\'\"<>\?]+@i';
	private $ignoreApiDLCaptcha = false;
	public function Download($link) {
		if (!preg_match('@https?://openload\.co/f/([\w-]+)@i', str_ireplace(array('://www.openload.co', '/embed/'), array('://openload.co', '/f/'), $link), $fid)) html_error('Invalid link?.');
		$this->link = $GLOBALS['Referer'] = str_ireplace('http://', 'https://', $fid[0]);
		$this->elink = str_ireplace('/f/', '/embed/', $this->link);
		$this->fid = $fid[1];

		if (empty($_POST['step'])) $this->testLink();
		else if ($_POST['step'] == '1') return $this->ApiDLPost();
		else if ($_POST['step'] == '42') return $this->processAnswer();

		$this->pA = (empty($_REQUEST['premium_user']) || empty($_REQUEST['premium_pass']) ? false : true);
		if (($_REQUEST['premium_acc'] == 'on' && ($this->pA || (!empty($GLOBALS['premium_acc']['openload_co']['user']) && !empty($GLOBALS['premium_acc']['openload_co']['pass']))))) {
			$user = ($this->pA ? $_REQUEST['premium_user'] : $GLOBALS['premium_acc']['openload_co']['user']);
			$pass = ($this->pA ? $_REQUEST['premium_pass'] : $GLOBALS['premium_acc']['openload_co']['pass']);
			if ($this->pA && !empty($_POST['pA_encrypted'])) {
				$user = decrypt(urldecode($user));
				$pass = decrypt(urldecode($pass));
				unset($_POST['pA_encrypted']);
			}
			return $this->Login($user, $pass);
		} else return $this->AnonDL();
	}

	private function testLink() {
		$this->page = $this->GetPage($this->link, $this->cookie);
		$this->cookie = GetCookiesArr($this->page, $this->cookie);
		if (stripos($this->page, 'We can\'t find the file you are looking for.') !== false) {
			$this->page = $this->GetPage($this->elink, $this->cookie);
			is_present($this->page, 'We can\'t find the file you are looking for.', 'File Not Found.');
			$this->cookie = GetCookiesArr($this->page, $this->cookie);
		}
	}

	private function processAnswer() {
		if (empty($_POST['streamurl'])) return html_error('Decoded Download-Link Empty');
		else if (($DL = $this->testStreamToken($_POST['streamurl']))) return $this->RedirectDownload($DL, urldecode(parse_url($DL, PHP_URL_PATH)));
		return html_error('Decoded Download-Link Invalid or Not Found');
	}

	private function AnonDL() {
		// ApiDL
		try {
			return $this->tryApiDL();
		} catch (Exception $e) {
			$this->changeMesg(sprintf('<br /><b>Anon_ApiDL Failed: "%s"</b>', htmlspecialchars($e->getMessage(), ENT_QUOTES)), true);
		}
		// WebDL
		return $this->WebDL();
	}

	private function tryApiDL($login = 0, $key = 0) {
		$query = array('file' => $this->fid);
		if (!empty($login) && !empty($key)) {
			$query['login'] = $login;
			$query['key'] = $key;
		}
		$ticket = $this->ApiReq('file/dlticket', $query);
		if (empty($ticket['ticket'])) throw new Exception('Token not Found');
		if (!empty($ticket['wait_time']) && $ticket['wait_time'] > 0) $this->CountDown($ticket['wait_time']);
		if (!empty($ticket['captcha_url'])) {
			// Got CAPTCHA
			if ($this->ignoreApiDLCaptcha) throw new Exception('ignoreApiDLCaptcha is true');
			$data = $this->DefaultParamArr($this->link);
			$data['step'] = '1';
			$data['ticket'] = $ticket['ticket'];
			list($headers, $imgBody) = explode("\r\n\r\n", $this->GetPage($ticket['captcha_url']), 2);
			if (substr($headers, 9, 3) != '200') throw new Exception('Error downloading captcha img');
			$mimetype = (preg_match('@image/[\w+]+@', $headers, $mimetype) ? $mimetype[0] : 'image/png');
			return $this->EnterCaptcha("data:$mimetype;base64," . base64_encode($imgBody), $data);
		}
		// Download
		unset($query['login'], $query['key']);
		$query['ticket'] = $ticket['ticket'];
		$DL = $this->ApiReq('file/dl', $query);
		if (empty($DL['url'])) throw new Exception('Download Link not Found');
		return $this->RedirectDownload($DL['url'], 'T8_ol_adl');
	}

	private function ApiDLPost() {
		if (empty($_POST['ticket'])) html_error('ApiDLPost: Ticket not Found.');
		if (empty($_POST['captcha'])) html_error('You didn\'t enter the image verification code.');
		try {
			$DL = $this->ApiReq('file/dl', array('file' => $this->fid, 'ticket' => $_POST['ticket'], 'captcha_response' => $_POST['captcha']));
		} catch (Exception $e) {
			html_error(sprintf('ApiDLPost Error: "%s"</b>', htmlspecialchars($e->getMessage(), ENT_QUOTES)));
		}
		if (empty($DL['url'])) html_error('ApiDLPost: Download Link not Found.');
		return $this->RedirectDownload($DL['url'], 'T8_ol_adl_c');
	}

	private function getAAScript($page) {
		if (preg_match_all('@ﾟωﾟﾉ.+?\(\'_\'\);@i', $page, $scripts)) {
			foreach ($scripts[0] as $script) {
				$script = (new AADecoder)->decode($script);
				if (stripos($script, 'String.fromCharCode') !== false && stripos($script, '#streamurl') !== false) {
					return $script;
				}
			}
		}
		return false;
	}

	private function testStreamToken($token) {
		if (!preg_match($this->DLRegexp, $token, $DL)) {
			if (!preg_match("@{$this->fid}~\d{10}~(?:(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)\.){3}(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)~[\w\-]{8}(?:\?|$)@", $token)) return false;
			$page = $this->GetPage("https://openload.co/stream/$token?mime=true");
			if (!preg_match($this->DLRegexp, $page, $DL)) return html_error('Stream Download-Link Not Found.');
			if ($this->fid != 'KDA_8nZ2av4' && strpos($DL[0], '/KDA_8nZ2av4/x.mp4') !== false) return false; // :P
		}
		return $DL[0];
	}

	private function WebDL() {
		if (!($script = $this->getAAScript($this->page))) html_error('Obsfuscated JS Not Found.');
		# if (preg_match('@var\s+secondsdl\s*=\s*(\d+)\s*;@', $this->page, $cD) && $cD[1] > 0) $this->CountDown($cD[1]);
		$data = $this->DefaultParamArr($this->link);
		$data['step'] = '42';
		$data['streamurl'] = '';
		$T8 = '';
		if (preg_match_all('@\$[\s\t]*\([\s\t]*[\"\']#([a-z][\w\.\-]*)[\"\'][\s\t]*\)@i', $script, $ids) && count($ids[0]) > 1) {
			foreach (array_unique($ids[1]) as $id) {
				if ($id == 'streamurl') continue;
				if (!preg_match("@<([a-z][a-z\d]*)\s*(?:\w+\s*=\s*[\"\'][^\"\'<>]*[\"\']\s*)*(?:\s*id\s*=[\"\']{$id}[\"\']\s*)(?:\w+\s*=\s*[\"\'][^\"\'<>]*[\"\']\s*)*(?:\s*>([!-~]+)</\\1>|(/)?\s*>)@i", $this->page, $tag)) break; // If it doesn't found the tag the decode will fail, im not sure if break or continue...
				$T8 .= (!empty($tag[2]) ? $tag[0] : (empty($tag[3]) ? $tag[0].'</'.$tag[1].'>' : $tag[0]));
			}
		}
		$script = preg_replace('@\.text\(\s*(?!\))@', '.val(', str_replace('streamurl', 'T8_streamurl', $script));
		echo "\n<div style='display:none;'>$T8</div>\n<form name='ol_dcode' action='{$_SERVER['SCRIPT_NAME']}' method='POST'><br />\n";
		foreach ($data as $name => $input) echo "<input type='hidden' name='$name' id='T8_$name' value='" . htmlspecialchars($input, ENT_QUOTES) . "' />\n";
		echo "</form>\n<span id='T8_emsg' class='htmlerror' style='text-align:center;display:none;'></span>\n<noscript><span class='htmlerror'><b>Sorry, this code needs JavaScript enabled to work.</b></span></noscript>\n<script type='text/javascript'>/* <![CDATA[ Th3-822 */\n\tvar T8 = true;\n\ttry {\n{$script}\n\t} catch(e) {\n\t\t$('#T8_emsg').html('<b>Cannot decode link: ['+e.name+'] '+e.message+'</b>').show();\n\t\tT8 = false;\n\t}\n\tif (T8) window.setTimeout(\"$('form[name=ol_dcode]').submit();\", 500); // 500 µs to make sure that the value was decoded and added.\n/* ]]> */</script>\n\n</body>\n</html>";
		exit;
	}

	private function Login($user, $pass) {
		$isApiLogin = (strpos($user, '@') === false ? true : false);

		// ApiDL
		if ($isApiLogin || (!$this->pA && !empty($GLOBALS['premium_acc']['openload_co']['apiuser']) && !empty($GLOBALS['premium_acc']['openload_co']['apipass']))) {
			try {
				if ($isApiLogin) return $this->tryApiDL($user, $pass);
				else return $this->tryApiDL($GLOBALS['premium_acc']['openload_co']['apiuser'], $GLOBALS['premium_acc']['openload_co']['apipass']);
			} catch (Exception $e) {
				if ($this->pA) html_error('Login_ApiDL Failed: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES));
				else $this->changeMesg(sprintf('<br /><b>Login_ApiDL Failed: "%s"</b>', htmlspecialchars($e->getMessage(), ENT_QUOTES)), true);
			}
		}

		// Login
		if (!preg_match('@<meta name="csrf-token" content="([\w+/=]+)"@i', $this->page, $token)) {
			$page = $this->GetPage('https://openload.co/login', $this->cookie);
			$this->cookie = GetCookiesArr($page, $this->cookie);
			if (!preg_match('@<meta name="csrf-token" content="([\w+/=]+)"@i', $page, $token)) html_error('Login CSRF Token not Found.');
		}
		$post = array('_csrf' => $token[1]);
		$post['LoginForm%5Bemail%5D'] = urlencode($user);
		$post['LoginForm%5Bpassword%5D'] = urlencode($pass);
		$post['LoginForm%5BrememberMe%5D'] = 1;

		$page = $this->GetPage('https://openload.co/login', $this->cookie, $post);
		is_present($page, 'Incorrect username or password.', 'Login Error: Wrong Email/Password.');
		$this->cookie = GetCookiesArr($page, $this->cookie);
		if (empty($this->cookie['_identity'])) html_error('Login Error: Cookie "_identity" not Found.');

		// Update $this->page
		$this->testLink();
		// WebDL
		return $this->WebDL();
	}

	private function is_present($lpage, $mystr, $strerror = '') {
		if (stripos($lpage, $mystr) !== false) throw new Exception(!empty($strerror) ? $strerror : $mystr);
	}

	private function ApiReq($path, $query = array()) {
		if (!is_array($query)) $query = array();

		$query = !empty($query) ? '?'.http_build_query($query, '', '&') : '';
		$page = $this->GetPage("https://api.openload.co/1/$path$query", 0, 0, 'https://openload.co/');
		$reply = $this->json2array($page, "ApiReq($path) Error");

		switch ($reply['status']) {
			case 200: break;
			case 404: case 451: return html_error("[ApiReq($path)] File Deleted or Not Found.");
			case 509: throw new Exception(stripos($reply['msg'], 'bandwidth usage too high (peak hours)') !== false ? 'BW Usage Too High' : 'BW Limit Reached');
			case 403:
				$this->is_present($reply['msg'], 'Authentication failed', 'Incorrect API Login');
				$this->is_present($reply['msg'], 'Captcha not solved correctly', 'Incorrect CAPTCHA Answer');
				$this->is_present($reply['msg'], 'the owner of this file doesn\'t allow API', 'API Download Disabled for This File');
			default: throw new Exception("[ApiReq($path) Error {$reply['status']}] " . htmlspecialchars($reply['msg'], ENT_QUOTES));
		}

		return $reply['result'];
	}
}

/**
 * Class AADecoder
 * @author Andrey Izman <izmanw@gmail.com>
 * @link https://github.com/mervick/php-aaencoder
 * @license MIT
 */

/*
The MIT License (MIT)

Copyright (c) 2015 Andrey Izman <izmanw@gmail.com>

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

/**
 * Class AADecoder
 */
class AADecoder
{
	const BEGIN_CODE = "ﾟωﾟﾉ=/｀ｍ´）ﾉ~┻━┻/['_'];o=(ﾟｰﾟ)=_=3;c=(ﾟΘﾟ)=(ﾟｰﾟ)-(ﾟｰﾟ);(ﾟДﾟ)=(ﾟΘﾟ)=(o^_^o)/(o^_^o);(ﾟДﾟ)={ﾟΘﾟ:'_',ﾟωﾟﾉ:((ﾟωﾟﾉ==3)+'_')[ﾟΘﾟ],ﾟｰﾟﾉ:(ﾟωﾟﾉ+'_')[o^_^o-(ﾟΘﾟ)],ﾟДﾟﾉ:((ﾟｰﾟ==3)+'_')[ﾟｰﾟ]};(ﾟДﾟ)[ﾟΘﾟ]=((ﾟωﾟﾉ==3)+'_')[c^_^o];(ﾟДﾟ)['c']=((ﾟДﾟ)+'_')[(ﾟｰﾟ)+(ﾟｰﾟ)-(ﾟΘﾟ)];(ﾟДﾟ)['o']=((ﾟДﾟ)+'_')[ﾟΘﾟ];(ﾟoﾟ)=(ﾟДﾟ)['c']+(ﾟДﾟ)['o']+(ﾟωﾟﾉ+'_')[ﾟΘﾟ]+((ﾟωﾟﾉ==3)+'_')[ﾟｰﾟ]+((ﾟДﾟ)+'_')[(ﾟｰﾟ)+(ﾟｰﾟ)]+((ﾟｰﾟ==3)+'_')[ﾟΘﾟ]+((ﾟｰﾟ==3)+'_')[(ﾟｰﾟ)-(ﾟΘﾟ)]+(ﾟДﾟ)['c']+((ﾟДﾟ)+'_')[(ﾟｰﾟ)+(ﾟｰﾟ)]+(ﾟДﾟ)['o']+((ﾟｰﾟ==3)+'_')[ﾟΘﾟ];(ﾟДﾟ)['_']=(o^_^o)[ﾟoﾟ][ﾟoﾟ];(ﾟεﾟ)=((ﾟｰﾟ==3)+'_')[ﾟΘﾟ]+(ﾟДﾟ).ﾟДﾟﾉ+((ﾟДﾟ)+'_')[(ﾟｰﾟ)+(ﾟｰﾟ)]+((ﾟｰﾟ==3)+'_')[o^_^o-ﾟΘﾟ]+((ﾟｰﾟ==3)+'_')[ﾟΘﾟ]+(ﾟωﾟﾉ+'_')[ﾟΘﾟ];(ﾟｰﾟ)+=(ﾟΘﾟ);(ﾟДﾟ)[ﾟεﾟ]='\\\\';(ﾟДﾟ).ﾟΘﾟﾉ=(ﾟДﾟ+ﾟｰﾟ)[o^_^o-(ﾟΘﾟ)];(oﾟｰﾟo)=(ﾟωﾟﾉ+'_')[c^_^o];(ﾟДﾟ)[ﾟoﾟ]='\\\"';(ﾟДﾟ)['_']((ﾟДﾟ)['_'](ﾟεﾟ+(ﾟДﾟ)[ﾟoﾟ]+";

	const END_CODE = "(ﾟДﾟ)[ﾟoﾟ])(ﾟΘﾟ))('_');";


	/**
	 * Decode encoded-as-aaencode JavaScript code.
	 * @param string $js
	 * @return string
	 */
	public static function decode($js)
	{
		if (self::hasAAEncoded($js, $start, $next, $encoded)) {
			$decoded = self::deobfuscate($encoded);
			if (substr(rtrim($decoded), -1) !== ';') {
				$decoded .= ';';
			}
			return mb_substr($js, 0, $start, 'UTF-8') . $decoded . self::decode(mb_substr($js, $next, null, 'UTF-8'));
		}
		return $js;
	}

	/**
	 * @param string $js
	 * @return string
	 */
	protected static function deobfuscate($js)
	{
		$bytes = array(
			9 => '((ﾟｰﾟ)+(ﾟｰﾟ)+(ﾟΘﾟ))',
			6 => '((o^_^o)+(o^_^o))',
			2 => '((o^_^o)-(ﾟΘﾟ))',
			7 => '((ﾟｰﾟ)+(o^_^o))',
			5 => '((ﾟｰﾟ)+(ﾟΘﾟ))',
			8 => '((ﾟｰﾟ)+(ﾟｰﾟ))',
			10 => '(ﾟДﾟ).ﾟωﾟﾉ',
			11 => '(ﾟДﾟ).ﾟΘﾟﾉ',
			12 => '(ﾟДﾟ)[\'c\']',
			13 => '(ﾟДﾟ).ﾟｰﾟﾉ',
			14 => '(ﾟДﾟ).ﾟДﾟﾉ',
			15 => '(ﾟДﾟ)[ﾟΘﾟ]',
			3 => '(o^_^o)',
			0 => '(c^_^o)',
			4 => '(ﾟｰﾟ)',
			1 => '(ﾟΘﾟ)',
		);
		$native = array(
			'-~' => '1+',
			'!' => '1',
			'[]' => '0',
		);
		$native = array(
			array_keys($native),
			array_values($native),
		);
		$chars = array();
		$hex = '(oﾟｰﾟo)+';
		$hexLen = mb_strlen($hex, 'UTF-8');
		$calc = function($expr) {
			return eval("return $expr;");
		};
		$convert = function ($block, $func) use ($bytes, $calc) {
			while (preg_match('/\([0-9\-\+\*\/]+\)/', $block)) {
				$block = preg_replace_callback('/\([0-9\-\+\*\/]+\)/', function($matches) use ($calc) {
					return $calc($matches[0]);
				}, $block);
			}
			$split = array();
			foreach (explode('+', trim($block, '+')) as $num) {
				if ($num === '') continue;
				$split[] = $func(intval(trim($num)));
			}
			return implode('', $split);
		};
		foreach ($bytes as $byte => $search) {
			$js = implode($byte, mb_split(preg_quote($search), $js));
		}
		foreach (mb_split(preg_quote('(ﾟДﾟ)[ﾟεﾟ]+'), $js) as $block) {
			$block = trim(trim(str_replace($native[0], $native[1], $block), '+'));
			if ($block === '') continue;
			if (mb_substr($block, 0, $hexLen, 'UTF-8') === $hex) {
				$code = hexdec($convert(mb_substr($block, $hexLen, null, 'UTF-8'), 'dechex'));
			}
			else {
				$code = octdec($convert($block, 'decoct'));
			}
			$chars[] = mb_convert_encoding('&#' . intval($code) . ';', 'UTF-8', 'HTML-ENTITIES');
		}
		return implode('', $chars);
	}

	/**
	 * Detect aaencoded JavaScript code.
	 * @param string $js
	 * @param null|int $start
	 * @param null|int $next
	 * @param null|string $encoded
	 * @return bool
	 */
	public static function hasAAEncoded($js, &$start=null, &$next=null, &$encoded=null)
	{
		$find = function($haystack, $needle, $offset=0) {
			$matches = array();
			for ($i = 0; $i < 6 && $offset !== false; $i ++) {
				if (($offset = mb_strpos($haystack, $needle, $offset, 'UTF-8')) !== false) {
					$matches[$i] = $offset;
					$offset ++;
				}
			}
			return count($matches) >= 6 ? array($matches[4], $matches[5]) : false;
		};
		$start = -1;
		while (($start = mb_strpos($js, 'ﾟωﾟﾉ', $start + 1, 'UTF-8')) !== false) {
			$clear = preg_replace('/\/\*.+?\*\//', '', preg_replace('/[\x03-\x20]/', '', $code = mb_substr($js, $start, null, 'UTF-8')));
			$len = mb_strlen(self::BEGIN_CODE, 'UTF-8');
			if (mb_substr($clear, 0, $len, 'UTF-8') === self::BEGIN_CODE &&
				mb_strpos($clear, self::END_CODE, $len, 'UTF-8') &&
				($matches = $find($js, 'ﾟoﾟ', $start))
			) {
				list($beginAt, $endAt) = $matches;
				$beginAt = mb_strpos($js, '+', $beginAt, 'UTF-8');
				$endAt = mb_strrpos($js, '(', - mb_strlen($js, 'UTF-8') + $endAt, 'UTF-8');
				$next = mb_strpos($js, ';', $endAt + 1, 'UTF-8') + 1;
				$encoded = preg_replace('/[\x03-\x20]/', '', mb_substr($js, $beginAt, $endAt - $beginAt, 'UTF-8'));
				return true;
			}
		}
		return false;
	}
}

//[21-04-2016]  Written by Th3-822. (Using mervick's AADecoder class)
//[25-11-2016]  Rewritten Decoding Functions. - Th3-822

?>