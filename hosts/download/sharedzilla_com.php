<?php
if (!defined('RAPIDLEECH')) {
    require_once ('index.html');
    exit;
}

class sharedzilla_com extends DownloadClass {
    
    public function Download($link) {
        $page = $this->GetPage($link);
        is_present($page, 'File not found');
        if (!preg_match_all('%<a href="([^"]+)" target="_blank">%', $page, $rdl)) html_error('Files still queued for uploading!');
        // get filehost redirect link
        foreach ($rdl[1] as $k => $v) {
            $temp .= $this->GetPage($v); 
        }
        if (!preg_match_all('@Location: (http:\/\/[^\r\n]+)@i', $temp, $redir)) html_error('No mirror link found!');
        $this->Submit($redir[1]);
        exit();
    }
    
    private function Submit($links) {
        global $PHP_SELF;
        if (!is_array($links) && count($links) < 1) html_error("No links found or \$links isn't an array.");
        echo "\n<center><form name='multilink_form' action='$PHP_SELF' method='post' >\n";
        echo "\n<h4>Select a host for download this file:</h4><br />\n";
        echo "<select name='link' style='width:160px;height:20px;'>\n";
        foreach ($links as $Name => $Link) echo "\t<option value='" . urlencode($Link) . "'>" . htmlentities($Link) . "</option>\n";
        echo "</select><br />\n";
        $defdata = $this->DefaultParamArr($link);
        foreach ($defdata as $name => $val) {
            echo "<input type='hidden' name='$name' id='$name' value='$val' />\n";
        }
        echo '<br /><input type="checkbox" name="premium_acc" id="premium_acc" onclick="javascript:var displ=this.checked?\'\':\'none\';document.getElementById(\'premiumblock\').style.display=displ;" checked="checked" />&nbsp;' . lang(249) . '<br /><div id="premiumblock" style="display: none;"><br /><table width="150" border="0"><tr><td>' . lang(250) . ':&nbsp;</td><td><input type="text" name="premium_user" id="premium_user" size="15" value="" /></td></tr><tr><td>' . lang(251) . ':&nbsp;</td><td><input type="password" name="premium_pass" id="premium_pass" size="15" value="" /></td></tr></table></div><br />';
        echo "<input type='submit' value='Download File' />\n";
        echo "\n</form></center>\n</body>\n</html>";
        exit;
    }
}

/*
 * by Ruud v.Tony 13-02-2012 (taken multiform submit link by Th3-822)
 */

?>