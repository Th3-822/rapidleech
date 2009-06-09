<?php
error_reporting(0);
# АВТОР: TRiTON4ik.
# Плагин сделан по просьбе: Pensal.
# UPLOAD PLUGIN ONLY FOR PHP RAPID GET.
if ($_REQUEST['action'] == "delete"){
	$cookies=$_REQUEST['cookies'];$action=$_REQUEST['filepage'];$token=$_REQUEST['authenticity_token'];

	$action=parse_url($action);$referer='http://rghost.ru/';$resulturl=parse_url($referer);

	$post["_method"]='delete';$post["authenticity_token"]=$token;

	$dpage = geturl($action["host"],defport($action), $action["path"].($action["query"] ? "?".$action["query"] : ""),$referer, $cookies, $post, 0, "");
	is_page($dpage);
	$cookies = GetCookies($dpage);unset($post);

	$result = geturl($resulturl["host"],defport($resulturl), $resulturl["path"].($resulturl["query"] ? "?".$resulturl["query"] : ""),$_REQUEST['filepage'], $cookies, $post, 0, "");
	is_present($result,"Р¤Р°Р№Р» СѓРґР°Р»РµРЅ","File was be deleted.");
	is_notpresent($result,"Р¤Р°Р№Р» СѓРґР°Р»РµРЅ","The file was not removed.");
exit;
	}
?>
<table width=600 align=center>
<tr><td align=center>
<?php
$continue_up=false;if ($_REQUEST['action'] == "OK"){$continue_up=true;}
else{	?><form method=post>
<div>Не используйте LetiT-Bit. Вот Вам <a href="http://rghost.ru/">альтернатива</a>.</div>
<input type=hidden value=uploaded value='<?php $_REQUEST['uploaded']?>'>
<input type=hidden name=filename value='<?php echo base64_encode($_REQUEST['filename']); ?>'>
TAGS:<input name=tags value='' type=input style="width:160px;"><br>
COMMENTS:<textarea name=comments rows="3"style="width:160px;"></textarea><br>
DELETE PASSWORD:<input name=dpass value='' type=input style="width:160px;"><br>
DOWNLOAD PASSWORD:<input name=pass value='' type=input style="width:160px;" disabled><br>
<input type=hidden name=action value='OK'><br>
<input type=submit value='Upload'><br>
</table>
</form>
	<?php
	exit;
	}
if($continue_up==true){
					/* Config */
					$tags=$_REQUEST['tags']; $comments=$_REQUEST['comments']; $dpass=$_REQUEST['dpass']; $pass=$_REQUEST['pass'];
						//if (!$tegs) html_error("No tegs!<br>");
						if ($pass) echo "Password used for download!<br>";
					//Готовим параметры для загрузки файла.
					$uploadurl=parse_url('http://rghost.ru/files');$referer='http://rghost.ru/';$paramurl=parse_url($referer);

					$page = geturl($paramurl["host"],defport($paramurl), $paramurl["path"].($paramurl["query"] ? "?".$paramurl["query"] : ""),$referer, 0, 0, 0, "");
					is_page($page);

					$cookies = GetCookies($page);
					$post["authenticity_token"]=$authenticity_token=cut_str($page,'<input name="authenticity_token" type="hidden" value="','" />');
					$post["commit"]='Отправить';

					//Делаем загрузку на сервер.
					$upfiles=upfile($uploadurl["host"],$uploadurl["port"] ? $uploadurl["port"] : 80, $uploadurl["path"].($uploadurl["query"] ? "?".$uploadurl["query"] : "") ,$referer, $cookies, $post, $lfile, $lname, "file");

					//Получаем куки.
					$cookies = GetCookies($upfiles);
					//Смотрим номер файла.
					$filepage=cut_str($upfiles,'Location: ',"\r\n");
					//Подтверждаем загрузку.
					$furl=parse_url($filepage);
					unset($post);
					$post["_method"]='put';
					$post["authenticity_token"]=$authenticity_token;
					$post["fileitem[tags]"]="quick_search,".$tags;
					$post["fileitem[description]"]=$comments;
					$post["fileitem[removal_code]"]=$dpass;
					$post["fileitem[password]"]=$pass;
					$post["commit"]='Обновить';

					$fpage = geturl($furl["host"],defport($furl), $furl["path"].($furl["query"] ? "?".$furl["query"] : ""),$referer, $cookies, $post, 0, "");
					is_page($fpage);

					$cookies = GetCookies($fpage);
					unset($post);

					$lpage = geturl($furl["host"],defport($furl), $furl["path"].($furl["query"] ? "?".$furl["query"] : ""),0, $cookies, 0, 0, "");
					is_page($lpage);

					$cookies = GetCookies($lpage);
					$downloadlink=cut_str($lpage,'<div id="file_edit">','</div>');
					$downloadlink=cut_str($downloadlink,'<a href="','"');
                    echo "<div>";
					$download_link=$filepage." and ".$referer.$downloadlink;
                    echo "<br><form method=post>";
					echo "<input name='filepage' type='hidden' value=".$filepage." />";
					echo "<input name='cookies' type='text' value=".$cookies[0]." />";
					echo "<input name='authenticity_token' type='hidden' value=".$authenticity_token." />";
					echo "<input type='hidden' value='uploaded' value=".$_REQUEST['uploaded']." />";
					echo "<input type='hidden' name='filename' value=".base64_encode($_REQUEST['filename'])." />";
					echo "<input type='hidden' name='action' value='delete'>";
					echo "<input type='submit' value='Delete on rghost.ru' />";
					echo "</form></div>";
					}
?><script>document.getElementById('progressblock').style.display='none';</script></td></tr></table>