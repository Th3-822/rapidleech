<?php

if (!defined('RAPIDLEECH'))
  {
  require_once("index.html");
  exit;
  }
####### Free Account Info. ###########
$btaccel_login = ""; //  Set your username
$btaccel_pass = ""; //  Set your password
##############################
		
           $usr=$btaccel_login;
			$pass=$btaccel_pass;
			if (empty($usr) || empty($pass))html_error("Login/Pass not inserted",0);
			else{
			$lg=parse_url("http://www.btaccel.com/login/");
			$post["email"]=$usr;
			$post["password"]=$pass;
			$page = geturl($lg["host"], $lg["port"] ? $lg["port"] : 80, $lg["path"].($lg["query"] ? "?".$lg["query"] : ""), "http://www.btaccel.com/", 0, $post, 0, $_GET["proxy"],$pauth);			
			$cookies=GetCookies($page);
		    $hom=parse_url("http://www.btaccel.com/home/");
		    $page = geturl($hom["host"], $hom["port"] ? $hom["port"] : 80, $hom["path"].($hom["query"] ? "?".$hom["query"] : ""), "http://www.btaccel.com/home/", $cookies, 0, 0, $_GET["proxy"],$pauth);			
            
           if(strpos($page,$usr) !== false){
<<<HTML
<table width=600 align=center>
    </td>
    </tr>
    <tr>
        <td align=center>
        <div id=info width=100% align=center>Retrive upload ID</div>
HTML;
            }else{
                html_error("Login error", 0);
               
            }
?>
<table border="1" align="center" cellspacing="5" cellpadding="5">
<form id="tavola"  >
<?php
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookies, 0, 0, $_GET["proxy"],$pauth); 
$frmfiles=cut_str($page,'<table id="files"','</table>');
preg_match_all('%http://.+/getfile\?info_hash=[^\'"]+%i',$frmfiles,$files) ;
$cc=1 ;

foreach($files[0] as $tmp){
    $tmp2=cut_str($tmp,'&file_name=','\n');
    $ff=explode("/",$tmp2);
     $fi=$ff[count($ff)-1];
$fil=urldecode($fi);
$file=str_replace(":80/getfile?","/getfile/?",$tmp);
$namefile=$fil;
echo "<tr><td><input type=checkbox id=cs$cc ></td><td><input type=hidden id=lin$cc value=$file ></td><td id=link$cc >$namefile</td></tr>";
$cc++;
}

}
?>
<tr><td><input type='checkbox' name='checkall' id='checkall' onclick='checkedAll();'> all<td><td align=center><input type=button onclick='selt(<?php echo $cc-1 ?>)' value='Step1 select and click'></tr>
</form>
</table>
<script type="text/javascript" language="javascript">
    function selt(cc)
    {
        var pp;
        var lks="";    
        var ck;
        var tmp;
		var tmp2;
        for(pp=1;pp<=cc;pp++){
        ck=    document.getElementById("cs"+ pp );
        if (ck.checked) {
        tmp=document.getElementById("lin"+ pp );
		tmp2=tmp.value.replace(/%/g,"%25");
        lks= lks + tmp2 + "\r\n";}
        }
	document.write('<form action=audl.php?GO=GO method=post >');
    document.write('<Input type=Hidden name=links value= "' + lks + '" >');	
	document.write('<center><Input type=submit name=submit value="Step2 click for send selected to Autodownloader"></center>');	
	document.write('</form>');
	
    }
checked=false;
function checkedAll (frm1) {
	var aa= document.getElementById('tavola');
	 if (checked == false)
          {
           checked = true
          }
        else
          {
          checked = false
          }
	for (var i =0; i < aa.elements.length; i++) 
	{
	 aa.elements[i].checked = checked;
	}
      }

	</script>	
<?php
flush();
/*************************\  
WRITTEN by kaox 21-jul-2009
UPDATED by kaox 04-oct-2009
\*************************/
?>