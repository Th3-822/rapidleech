<?php
if (!defined('RAPIDLEECH')) {
    require_once("index.html");
    exit();
}
class dl_free_fr extends DownloadClass {
    public function Download($link) {
        $page = $this->GetPage($link);
        if (preg_match('@Location: (http(s)?:\/\/[^\r\n]+)@i', $page, $redir)) {
            $link = trim($redir[1]);
            $page = $this->GetPage($link);
        }
		preg_match('/name="file" value="(.*?)"/', $page, $redirold);
		$linkold = $redirold[1];
		preg_match('#</span>(.*?) <br />#', $page, $redirfile);
		$cookie2 = GetCookies($page);
		$post = array('file' => $linkold);
		$page2 = $this->GetPage("http://dl.free.fr/getfile.pl", $cookie2, $post);
		if (preg_match('@Location: (http(s)?:\/\/[^\r\n]+)@i', $page2, $redirfinal)) {
            $dlink = trim($redirfinal[1]);
        } else {
		    $dlink = $link;
		}
        is_present($page, "Fichier inexistant.", "The file could not be found. Please check the download link.");
        is_present($page, "Le fichier demand&eacute; n'a pas &eacute;t&eacute; trouv&eacute;.", "The file could not be found. Please check the download link.");
        is_present($page, "Erreur 404 - Document non trouv&eacute;", "The file could not be found. Please check the download link.");
        is_present($page, "Appel incorrect.", "Incorrect link.");
        $cookie = GetCookies($page2);
        $filename = $redirfile[1];
        $this->RedirectDownload($dlink, $filename, $cookie, 0, $link);
        exit();
    }
}
?>
