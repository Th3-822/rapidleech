<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}

class filecloud_io extends DownloadClass {
	
	public function Download($link) {
			echo 'Filecloud.io Download Plugin by <b>The Devil</b><br>';
			global $premium_acc;
			(!empty($premium_acc['filecloud_io']['user']) || !empty($premium_acc['filecloud_io']['pass']) || !empty($_REQUEST['premium_user']) || !empty($_REQUEST['premium_pass'])) ? html_error('Download From Premium Account Not Supported Yet') :'';
			if($_POST['Devil'] == "1") {
				$this->DownloadFree($link);
			} else {
				$this->GetIt($link);
			}
	}
	
	private function GetIt($link){
		
		$this->page = $this->GetPage($link);
		is_present($this->page,'The file at this URL was either removed or did not exist in the first place','File Deleted');
		$this->cookie = GetCookiesArr($this->page);
		$this->strcookie = CookiesToStr($this->cookie);
		$this->fname = cut_str($this->page,'title="','"'); 
		$devil = preg_match("@data:([\d\w\s :',\{\}]+)@", $this->page,$block);
		(!$devil)?html_error('[4]Error: Unable to Retrieve Data'):'';
		$block = substr(preg_replace('/\s+/', '', $block[1]),0,-1);
		$block = preg_replace("/'+/", '"', $block);
		$block = preg_replace("/response/", '"response"', $block);
		$data = $this->jsonreply($block);
		(empty($data['f2']) || empty($data['f1']) || empty($data['fkey']))?html_error('[2]Error: API Down/Check API'):'';
		$devil = preg_match("@'sitekey'([\d\w\s:']+)@",$this->page,$pubkeys);
		(!$devil)?html_error('[5]Error: Unable to Retrieve reCAPTCHAv2 Key'):'';
		$pubkey = str_replace(array("'",":",' '),'',$pubkeys[1]);
		$data['Devil'] = '1';
		$data['link'] = $link;
		$data['cookie']=$this->strcookie;
		$data['fname'] = $this->fname;
		$this->reCAPTCHAv2($pubkey,$data);
	}
	

	private function DownloadFree($link){
		$vcap = $this->verifyReCaptchav2(true);
		$go = $_POST['link'];
		$post = array('fkey'=>$_POST['fkey'],'f1'=>$_POST['f1'],'f2'=>$_POST['f2'],'r'=>$vcap);
		$requesturl = 'http://filecloud.io/?m=download&a=request';
		$this->devil = $this->GetPage($requesturl,$cookie,$post,$go);
		is_present($this->devil,'error','[3]Error: Contact Admin If Error Continues');
		$this->devil = $this->jsonreply($this->devil);
		(empty($this->devil['downloadUrl']))?html_error('[1]Error: Plugin Update Required!'):'';
		$this->RedirectDownload($this->devil['downloadUrl'],$_POST['fname']);
	}
	
	protected function retryReCaptchav2(){
		$data = array();
		$data['cookie'] = $_POST['cookie'];
		$data['fname'] = $_POST['fname'];
		$data['link'] = $_POST['link'];
		$data['Devil'] = '1';
		$data['fkey'] = $_POST['fkey'];
		$data['f1'] = $_POST['f1'];
		$data['f2'] = $_POST['f2'];
		$data['r'] = $_POST['r'];
		return $this->reCAPTCHAv2($_POST['recaptcha2_public_key'], $data, 0, 'Retry');
	}

	private function jsonreply($resp){
		$tmp = stristr($resp,'{');
		$json = json_decode($tmp,true);
		return $json;

	}

}

// Written by The Devil

?>


