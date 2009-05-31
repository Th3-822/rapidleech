<?php if (!defined('RAPIDLEECH'))
  {
  require_once("index.html");
  exit;
  }
				if(!$disable_action)
				  {
                  switch($_GET["act"])
                    {
                      case "upload":
						if(count($_GET["files"]) < 1)
							{
								echo "Select atleast one file.<br><br>";
							}
								else
							{
								$d = opendir(HOST_DIR."upload/");
					    		while (false !== ($modules = readdir($d)))
					    			{
										if($modules!="." && $modules!="..")
											{
												if(is_file(HOST_DIR."upload/".$modules))
													{
														if (strpos($modules,".index.php")) include_once(HOST_DIR."upload/".$modules);
													}
											}
									}
							
								if (empty($upload_services)) 
									{
										echo "<span style='color:#FF6600'><b>No Supported Upload Services!</b></span>";
									}
										else
									{
										sort($upload_services); reset($upload_services);
										$cc=0;
										foreach($upload_services as $upl)
											{
												$uploadtype.="\tupservice[".($cc++)."]=new Array('".$upl."','".(str_replace("_"," ",$upl)." (".($max_file_size[$upl]==false ? "Unlim" : $max_file_size[$upl]."Mb").")")."');\n";
											}
											?>
										<script>
										var upservice = new Array();

										function fill_option(id)
											{
												var elem=document.getElementById(id);
												
												for (var i=0; i<upservice.length;i++)
													{
														elem.options[elem.options.length]=new Option(upservice[i][1]);
														elem.options[elem.options.length-1].value=upservice[i][0];
													}
											}

										<?php echo $uploadtype; ?>

										function openwinup(id)
											{
												var options = "width=700,height=250,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,copyhistory=no";
												win=window.open('', id, options);
												win.focus();
												return true;
											}
										</script>
										<table align="center">
										<?php
										for($i = 0; $i < count($_GET["files"]); $i++)
											{
												$file = $list[($_GET["files"][$i])];
												$tid=md5(time()."_file".$_GET["files"][$i]);
										?>                                      
										<tr><form action=upload.php method=get target=<?php echo $tid ?> onSubmit="return openwinup('<?php echo $tid ?>');">
										<td><b><?php echo basename($file["name"])."</b>  , ". $file["size"] ?></td>
										<td><select name=uploaded id=d_<?php echo $tid; ?>></select><script>fill_option('d_<?php echo $tid; ?>');</script></td>
										<td><input type=submit value=Upload></td>
										</tr>
										<tr><td colspan="3" align="center">
										<input type=hidden name=filename value='<?php echo base64_encode($file["name"]); ?>'>
										</td>
										</form></tr>
										<?php
											}
										?>
										</table>
										<?php
									}
							}     
                      break;
						
						
                      case "delete":
                        if(count($_GET["files"]) < 1)
                          {
                            echo "Please select at least one file<br><br>";
                          }
						  elseif($disable_deleting)
						  {
							echo "File deletion is disabled";
						  }
                        else
                          {
                            ?>
                              <form method="post">
                              <input type="hidden" name="act" value="delete_go">
                              File<?php echo count($_GET["files"]) > 1 ? "s" : ""; ?>:
                              <?php
                              for($i = 0; $i < count($_GET["files"]); $i++)
                                {
                                  $file = $list[$_GET["files"][$i]];
                                  ?>
                                  <input type="hidden" name="files[]" value="<?php echo $_GET["files"][$i]; ?>">
                                  <b><?php echo basename($file["name"]); ?></b><?php echo $i == count($_GET["files"]) - 1 ? "." : ",&nbsp"; ?>
                                  <?php
                                }
                              ?><br>Delete<?php echo count($_GET["files"]) > 1 ? " These Files" : " This File"; ?>?<br>
                              <table>
                                <tr>
                                  <td>
                                    <input type="submit" name="yes" style="width:33px; height:23px" value="Yes">
                                  </td>
                                  <td>
                                    &nbsp;&nbsp;&nbsp;
                                  </td>
                                  <td>
                                    <input type="submit" name="no" style="width:33px; height:23px" value="No">
                                  </td>
                                </tr>
                              </table>
                              </form>
                            <?php
                          }
                      break;

                      case "delete_go":
                        if($_GET["yes"])
                          {
                            for($i = 0; $i < count($_GET["files"]); $i++)
                              {
                                $file = $list[$_GET["files"][$i]];
                                if(file_exists($file["name"]))
                                  {
                                    if(@unlink($file["name"]))
                                      {
                                        echo "File <b>".$file["name"]."</b> Deleted<br><br>";
                                        unset($list[$_GET["files"][$i]]);
                                      }
                                    else
                                      {
                                        echo "Error deleting the file <b>".$file["name"]."</b>!<br><br>";
                                      }
                                  }
                                else
                                  {
                                    echo "File <b>".$file["name"]."</b> Not Found!<br><br>";
                                  }
                              }
                            if(!updateListInFile($list))
                              {
                                  echo "Error in updating the list!<br><br>";
                              }
                          }
                        else
                          {
                            ?>
                              <script  language="JavaScript">
                                location.href="<?php echo $PHP_SELF."?act=files"; ?>";
                              </script>
                            <?php
                          }
                      break;

                      case "mail":
                        if(count($_GET["files"]) < 1)
                          {
                            echo "Select at least one file.<br><br>";
                          }
                        else
                          {
                            ?>
                              <form method="post">
                              <input type="hidden" name="act" value="mail_go">
                              File<?php echo count($_GET["files"]) > 1 ? "s" : ""; ?>:
                              <?php
                              for($i = 0; $i < count($_GET["files"]); $i++)
                                {
                                  $file = $list[($_GET["files"][$i])];
                                  ?>
                                  <input type="hidden" name="files[]" value="<?php echo $_GET["files"][$i]; ?>">
                                  <b><?php echo basename($file["name"]); ?></b><?php echo $i == count($_GET["files"]) - 1 ? "." : ",&nbsp"; ?>
                                  <?php
                                }
                              ?><br><br>
                              <table align="center">
                                <tr>
                                  <td>
                                    Email:&nbsp;<input type="text" name="email" value="<?php echo ($_COOKIE["email"] ? $_COOKIE["email"] : ""); ?>">
                                  </td>
                                  <td>
                                    <input type="submit" value="Send">
                                  </td>
                                </tr>
                                <tr>
                                 <td>
                                    <input type="checkbox" name="del_ok" <?php if(!$disable_deleting) echo "checked"; ?> <?php if($disable_deleting) echo "disabled"; ?>>&nbsp;Delete successful submits
                                 </td>
                                </tr>
                                <tr>
                                  <td>
                                  </td>
                                </tr>
                                <tr>
                                  <table>
                                    <tr>
                                      <td>
                                        <input id="splitchkbox" type="checkbox" name="split" onClick="javascript:var displ=this.checked?'':'none';document.getElementById('methodtd2').style.display=displ;"<?php echo $_COOKIE["split"] ? " checked" : ""; ?>>&nbsp;Split by Parts
                                      </td>
                                      <td>&nbsp;

                                      </td>
                                      <td id="methodtd2"<?php echo $_COOKIE["split"] ? "" : " style=\"display: none;\""; ?>>
                                        <table>
                                          <tr>
                                            <td>
                                              Method:&nbsp;<select name="method"><option value="tc"<?php echo $_COOKIE["method"] == "tc" ? " selected" : ""; ?>>Total Commander</option><option value="rfc"<?php echo $_COOKIE["method"] == "rfc" ? " selected" : ""; ?>>RFC 2046</option></select>
                                            </td>
                                          </tr>
                                          <tr>
                                            <td>
                                              Parts Size:&nbsp;<input type="text" name="partSize" size="2" value="<?php echo $_COOKIE["partSize"] ? $_COOKIE["partSize"] : 10; ?>">&nbsp;MB
                                            </td>
                                          </tr>
                                        </table>
                                      </td>
                                    </tr>
                                  </table>
                              </form>
                            <?php
                          }
                      break;

                      case "mail_go":
                        require_once(CLASS_DIR."mail.php");
                        if(!checkmail($_GET["email"]))
                          {
                            echo "Invalid E-mail Address.<br><br>";
                          }
                        else
                          {
                            $_GET["partSize"] = ((isset($_GET["partSize"]) & $_GET["split"] == "on") ? $_GET["partSize"] * 1024 * 1024 : FALSE);
                            for($i = 0; $i < count($_GET["files"]); $i++)
                              {
                                $file = $list[$_GET["files"][$i]];
                                if(file_exists($file["name"]))
                                  {
                                    if(xmail("$fromaddr", $_GET[email], "File ".basename($file["name"]), "File: ".basename($file["name"])."\r\n"."Link: ".$file["link"].($file["comment"] ? "\r\nComments: ".str_replace("\\r\\n", "\r\n", $file["comment"]) : ""), $file["name"], $_GET["partSize"], $_GET["method"]))
                                      {
                                       if ($_GET["del_ok"] && !$disable_deleting)
                                          {
                                           if(@unlink($file["name"]))
                                             {
                                              $v_ads=" and deleted.";
                                              unset($list[$_GET["files"][$i]]);
                                             }
                                           else
                                             {
                                              $v_ads=", but <b>not deleted!</b>";
                                             };
                                          } else $v_ads=" !";
                                        echo "<script language=\"JavaScript\">mail('File <b>".basename($file["name"])."</b> it is sent for the address <b>".$_GET["email"]."</b>".$v_ads."', '".md5(basename($file["name"]))."');</script>\r\n<br>";
                                      }
                                    else
                                      {
                                        echo "Error sending file!<br>";
                                      }
                                  }
                                else
                                  {
                                    echo "File <b>".$file["name"]."</b> not found!<br><br>";
                                  }
                              }
                          }
                      break;

                      case "boxes":
                        if(count($_GET["files"]) < 1)
                          {
                            echo "Select at least one file.<br><br>";
                          }
                        else
                          {
                            ?>
                              <form method="post">
                              <input type="hidden" name="act" value="boxes_go">
                              <?php
                             echo count($_GET["files"])." file".(count($_GET["files"]) > 1 ? "s" : "").":<br>";
                              for($i = 0; $i < count($_GET["files"]); $i++)
                                {
                                  $file = $list[($_GET["files"][$i])];
                                  ?>
                                  <input type="hidden" name="files[]" value="<?php echo $_GET["files"][$i]; ?>">
                                  <b><?php echo basename($file["name"]); ?></b><?php echo $i == count($_GET["files"]) - 1 ? "." : ",&nbsp"; ?>
                                  <?php
                                }
                              ?><br><br>
                              <table align="center">
                                <tr>
                                  <td>
                                    Emails:&nbsp;<textarea name="emails" cols="30" rows="8"><?php if ($_COOKIE["email"]) echo $_COOKIE["email"]; ?></textarea>
                                  </td>
                                  <td>
                                    <input type="submit" value="Send">
                                  </td>
                                </tr>
                                <tr>
                                 <td>
                                    <input type="checkbox" name="del_ok" <?php if(!$disable_deleting) echo "checked"; ?> <?php if($disable_deleting) echo "disabled"; ?>>&nbsp;Delete successful submits
                                 </td>
                                </tr>
                                <tr>
                                 <td>
                                 </td>
                                </tr>
                                <tr>
                                  <table>
                                    <tr>
                                      <td>
                                        <input id="splitchkbox" type="checkbox" name="split" onClick="javascript:var displ=this.checked?'':'none';document.getElementById('methodtd2').style.display=displ;"<?php echo $_COOKIE["split"] ? " checked" : ""; ?>>&nbsp;Split by Parts
                                      </td>
                                      <td>&nbsp;

                                      </td>
                                      <td id=methodtd2<?php echo $_COOKIE["split"] ? "" : " style=\"display: none;\""; ?>>
                                        <table>
                                          <tr>
                                            <td>
                                              Method:&nbsp;<select name="method"><option value="tc"<?php echo $_COOKIE["method"] == "tc" ? " selected" : ""; ?>>Total Commander</option><option value="rfc"<?php echo $_COOKIE["method"] == "rfc" ? " selected" : ""; ?>>RFC 2046</option></select>
                                            </td>
                                          </tr>
                                          <tr>
                                            <td>
                                              Parts Size:&nbsp;<input type="text" name="partSize" size="2" value="<?php echo ($_COOKIE["partSize"] ? $_COOKIE["partSize"] : 10); ?>">&nbsp;MB
                                            </td>
                                          </tr>
                                        </table>
                                      </td>
                                    </tr>
                                  </table>
                              </form>
                            <?php
                          }
                      break;

                      case "boxes_go":
                          {
                            require_once(CLASS_DIR."mail.php");
                            $_GET["partSize"] = ((isset($_GET["partSize"]) & $_GET["split"] == "on") ? $_GET["partSize"] * 1024 * 1024 : FALSE);
                            $v_mails = explode("\n",$emails);
                            $v_min=count((count($_GET["files"])<count($v_mails)) ? $_GET["files"] : $v_mails);

                            for($i = 0; $i < $v_min; $i++)
                              {
                                $file = $list[$_GET["files"][$i]];

                                $v_mail = trim($v_mails[$i]);
                              if(!checkmail($v_mail))
                               {
                                echo "<b>$v_mail</b> - Invalid E-mail Address.<br><br>";
                               }
                              elseif(file_exists($file["name"]))
                                  {
                                    if(xmail("$fromaddr", $v_mail, "File ".basename($file["name"]), "File: ".basename($file["name"])."\r\n"."Link: ".$file["link"].($file["comment"] ? "\r\nComments: ".str_replace("\\r\\n", "\r\n", $file["comment"]) : ""), $file["name"], $_GET["partSize"], $_GET["method"]))
                                      {
                                        if ($_GET["del_ok"] && !$disable_deleting)
                                          {
                                           if(@unlink($file["name"]))
                                             {
                                              $v_ads=" and deleted !";
                                              unset($list[$_GET["files"][$i]]);
                                             }
                                           else
                                             {
                                              $v_ads=", but <b>not deleted !</b>";
                                             };
                                          } else $v_ads=" !";
                                        echo "<script language=\"JavaScript\">mail('File <b>".basename($file["name"])."</b> it is sent for the address <b>".$v_mail."</b>".$v_ads."', '".md5(basename($file["name"]))."');</script>\r\n<br>";
                                      }
                                    else
                                      {
                                        echo "Error sending file!<br>";
                                      }
                                  }
                                else
                                  {
                                    echo "File <b>".$file["name"]."</b> Not Found!<br><br>";
                                  }
                              }

                             if (count($_GET["files"])<count($v_mails))
                              {
                               echo "<b>Внимание!</b> Вы написали лишние адреса.<br><br><b>";
                               for($i = count($_GET["files"]); $i < count($v_mails); $i++)
                                {
                                  $v_mail = trim($v_mails[$i]);
                                  echo "$v_mail.</b><br><br>";
                                };
                                echo "</b><br>";
                              }
                         elseif (count($_GET["files"])>count($v_mails))
                              {
                               echo "<b>Внимание!</b> Вы не написали адреса для следуйщий файлов:<br><br><b>";
                               for($i = count($v_mails); $i < count($_GET["files"]); $i++)
                                {
                                $file = $list[$_GET["files"][$i]];
                                if(file_exists($file["name"]))
                                  {
                                    echo $file["name"]."<br><br>";
                                  }
                                else
                                  {
                                    echo "</b>Файл <b>".$file["name"]."</b> не найден!<b><br><br>";
                                  }
                                }
                               echo "</b><br>";
                              };

                           if ($_GET["del_ok"])
                            {
                             if(!updateListInFile($list))
                               {
                                  echo "Couldn't Update!<br><br>";
                               }
                            }

                          }
                      break;
					  
					  case "md5":
                        if(count($_GET["files"]) < 1)
                          {
                            echo "Select atleast one file.<br><br>";
                          }
                         else
                          {
                                  ?>
                           <table align="center" border=0 cellspacing="2" cellpadding="4">
                            <tr bgcolor="#243E4A"><td align=center>File<td align=center>Size<td align=center>MD5</tr>
                            <?php

                                  for($i = 0; $i < count($_GET["files"]); $i++)
                                          {
                                                  $file = $list[($_GET["files"][$i])];

                                                  if (file_exists($file["name"]))
                                                          {
                                                  ?>
                                                  <tr bgcolor="#2B3C43">
                                                  <td nowrap><b>&nbsp;<?php echo basename($file["name"])."</b><td align=center>&nbsp;". $file["size"] ?>&nbsp;</td>
                                                  <td nowrap><b>&nbsp;<font style="font-family: monospace; color:#FFA300"><?php echo md5_file($file["name"]) ?></font>&nbsp;</b></tr>
                                                  <?php
                                                          }
                                          }
                            ?>
                            </table><br>
                            <?php
                          }
                      break;
					  
					  case "unzip":
                        if(count($_GET["files"]) < 1)
                          {
                            echo "Select at least one file.<br><br>";
                          }
                        else
                          {
                            ?>
                            <form method="post">
                              <input type="hidden" name="act" value="unzip_go">
                               <table align="center">
                                <tr>
                                  <td>
                                    <table>
                              <?php
                                for($i = 0; $i < count($_GET["files"]); $i++)
                                  {
                                    $file = $list[$_GET["files"][$i]];
                                    ?>
                                      <input type="hidden" name="files[]" value="<?php echo $_GET["files"][$i]; ?>">
                                          <tr>
                                            <td align="center"><b><?php echo basename($file["name"]); ?></b></td>
                                          </tr>
                                          <tr>
                                            <td>
                                            </td>
                                          </tr>
                                    <?php
                                  }
                              ?>
                                    </table>
                                  </td>
                                  <td>
                                    <input type="submit" value="Unzip">
                                  </td>
                                </tr>
                                <tr>
                                  <td>
                                  </td>
                                </tr>
                              </table>
                            </form>
                            <?php
                          }
                      break;

                      case "unzip_go":
                         $unzip_file = FALSE;
						 require_once(CLASS_DIR."unzip.php");
                         for($i = 0; $i < count($_GET["files"]); $i++)
                          {
						  $file = $list[$_GET["files"][$i]];
                            if(file_exists($file["name"]))
                              {
								//$zip_dir = basename($file["name"], ".zip");
								/*if(!@mkdir($download_dir.$zip_dir, 0777))
								{
									html_error('Error : Unable to create director', 0);
								}*/
								$zip = new dUnzip2($file["name"]);
								//$zip->debug = true;
								
								if($check_these_before_unzipping)
									{
									$allf = $zip->getList();
									foreach($allf as $file => $property)
										{
										$zfiletype = strrchr($property['file_name'], ".");
										if (is_array($forbidden_filetypes) && in_array(strtolower($zfiletype), $forbidden_filetypes))
											{
											exit("The filetype $zfiletype is forbidden to be unzipped<script>alert('The filetype $zfiletype is forbidden to be unzipped');window.location.replace('http://{$_SERVER['SERVER_NAME']}')</script>");
											}
										
										}
									}
								$zip->unzipAll($download_dir);
								if($zip->getList() != false){
									echo '<b>'.$file["name"].'</b>&nbsp;unzipped successfully<br>';
								}
                                $unzip_file = TRUE;
                              }
                            else
                             {
                               echo "File <b>".$file["name"]."</b> not found!<br><br>";
                             }
                          }
                         if($unzip_file)
                           {
                             if(!updateListInFile($list))
                              {
                                  echo "Couldn't Update<br><br>";
                              }
                           }
                      break;

                      case "split":
                        if(count($_GET["files"]) < 1)
                          {
                            echo "Select at least one file.<br><br>";
                          }
                        else
                          {
                            ?>
                            <form method="post">
                              <input type="hidden" name="act" value="split_go">
                               <table align="center">
                                <tr>
                                  <td>
                                    <table>
                              <?php
                                for($i = 0; $i < count($_GET["files"]); $i++)
                                  {
                                    $file = $list[$_GET["files"][$i]];
                                    ?>
                                      <input type="hidden" name="files[]" value="<?php echo $_GET["files"][$i]; ?>">
                                          <tr>
                                            <td align="center"><b><?php echo basename($file["name"]); ?></b></td>
                                          </tr>
                                          <tr>
                                            <td>
                                              Parts Size:&nbsp;<input type="text" name="partSize[]" size="2" value="<?php echo ($_COOKIE["partSize"] ? $_COOKIE["partSize"] : 10); ?>">&nbsp;MB
                                            </td>
                                          </tr>
                                          <tr>
                                            <td>
                                              Save To:&nbsp;<input type="text" name="saveTo[]" size="40" value="<?php echo addslashes(dirname($file["name"])); ?>">
                                            </td>
                                          </tr>
                                          <tr>
                                            <td>
                                              <input type="checkbox" name="del_ok" <?php if(!$disable_deleting) echo "checked"; ?> <?php if($disable_deleting) echo "disabled"; ?>>&nbsp;Delete source file after successful split
                                            </td>
                                          </tr>
                                          <tr>
                                            <td>
                                            </td>
                                          </tr>
                                    <?php
                                  }
                              ?>
                                    </table>
                                  </td>
                                  <td>
                                    <input type="submit" value="Split">
                                  </td>
                                </tr>
                                <tr>
                                  <td>
                                  </td>
                                </tr>
                              </table>
                            </form>
                            <?php
                          }
                      break;

                      case "split_go":
                        for($i = 0; $i < count($_GET["files"]); $i++)
                          {
                            $split_ok = true;
                            $file = $list[$_GET["files"][$i]];
                            $partSize = urldecode($_GET["partSize"][$i]) * 1024 * 1024;
                            $saveTo = urldecode($_GET["saveTo"][$i]);
                            while(stristr($saveTo, "\\\\"))
                              {
                              $saveTo = str_replace("\\\\", "\\", $saveTo);				
                              }
                            $partSize = round($partSize);
							$fileSize = filesize($file["name"]);
							$fp = fopen($file["name"], "rb");
							//flock($fp, LOCK_SH);
                            $buffer_size = 25000000; //adjust with server memory //15mb - 9mb worked
                            //$crc = strtoupper(dechex(crc32($fileContents)));
							$crc = "111111";
                            $crc = str_repeat("0", 8 - strlen($crc)).$crc;
                            if(file_exists($file["name"]))
                            {
                            echo "Started to split file <b>".basename($file["name"])."</b> of parts ".bytesToKbOrMbOrGb($partSize).", Using Method - Total Commander...<br>";
                            $totalParts = ceil($fileSize / $partSize);
                            echo "Total Parts: <b>".$totalParts."</b><br><br>";
                            $fileName=basename($file["name"]);
                            /*$fileTmp = $fileNamePerman = basename($file["name"]);
                            while(strpos($fileTmp, "."))
                              {
                                $fileName .= substr($fileTmp, 0, strpos($fileTmp, ".") + 1);
                                $fileTmp = substr($fileTmp, strpos($fileTmp, ".") + 1);
                              }
                            $fileName = substr($fileName, 0, -1);*/
                            $path = $saveTo.(strstr(ROOT_DIR, "\\") ? "\\" : "/");
                            for($j = 0; $j < $totalParts; $j++)
                              {
                                if($j == 0)
                                  {

                                    if(!@write_file($path.$fileName.".crc", "filename=".basename($file["name"])."\r\n"."size=".$fileSize."\r\n"."crc32=".$crc."\r\n"))
                                      {
                                        echo "It is not possible to split the file. CRC Error<b>".$fileName.".crc"."</b> !<br><br>";
                                        $split_ok = false;
                                      }
                                    else
                                      {
                                        $time = explode(" ", microtime());
                                        $time = str_replace("0.", $time[1], $time[0]);
                                        $list[$time] = array("name"    => $path.$fileName.".crc",
                                                             "size"    => bytesToKbOrMbOrGb(strlen(read_file($path.$fileName.".crc"))),
                                                             "date"    => time(),
                                                             "comment" => "CRC file of ".$fileNamePerman);
                                      }

                                      $total_reads=ceil($partSize / $buffer_size);
                                      fseek($fp, 0, SEEK_SET);
				    for ($p = 0; $p < $total_reads; $p++)
				    {


					if ($p!=$total_reads-1)
					{
						$fileChunk=fread($fp, $buffer_size);
						}
					else
					{
						$fileChunk=fread($fp, $partSize - ($total_reads-1)*$buffer_size);
						}
					
                                    	if(!@write_file($path.$fileName.".001", $fileChunk, 0))
                                      	{
                                        	echo "It was not possible to split the file <b>".$fileName.".001"."</b> !<br><br>";
                                        	$split_ok = false;
                                      	}
                                    	
                                    }
                                    $time = explode(" ", microtime());
                                        	$time = str_replace("0.", $time[1], $time[0]);
                                        	$list[$time] = array("name"    => $path.$fileName.".001",

                                                             	"size"    => bytesToKbOrMbOrGb($partSize),
                                                             	"date"    => time(),
                                                             	"comment" => "Part ".($j + 1)."/".$totalParts." of ".$fileNamePerman);
                                  }

                                elseif($j == $totalParts - 1)
                                  {

                                  fseek($fp, $j * $partSize, SEEK_SET);
                                    $total_reads =ceil(($fileSize - $j * $partSize) / $buffer_size);
                                    for ($p = 0; $p < $total_reads; $p++)
                                    {
                                    if ($p!=$total_reads-1)
                                    {
						$fileChunk=fread($fp, $buffer_size);
						}
					else
					{
						$fileChunk=fread($fp, ($fileSize - $j * $partSize) - ($total_reads-1)*$buffer_size);
						}
					
                                    $num = strlen($j + 1) == 2 ? "0".($j + 1) : (strlen($j + 1) == 1 ? "00".($j + 1) : ($j + 1));
                                    if(!@write_file($path.$fileName.".".$num, $fileChunk, 0))
                                      {
                                        echo "It was not possible to split the file <b>".$fileName.".".$num."</b> !<br><br>";
                                        $split_ok = false;
                                      }


                                    }
                                    $time = explode(" ", microtime());
                                        $time = str_replace("0.", $time[1], $time[0]);
                                        $list[$time] = array("name"    => $path.$fileName.".".$num,

                                                             "size"    => bytesToKbOrMbOrGb(($fileSize - $j * $partSize)),
                                                             "date"    => time(),
                                                             "comment" => "Part ".($j + 1)."/".$totalParts." of ".$fileNamePerman);
                                  }
                                else
                                  {
                                    fseek($fp, $j * $partSize, SEEK_SET);
                                    $total_reads =ceil($partSize / $buffer_size);
                                    for ($p = 0; $p < $total_reads; $p++)
                                    {
                                    if ($p!=$total_reads-1)
                                    {
						$fileChunk=fread($fp, $buffer_size);
						}
					else
					{

						$fileChunk=fread($fp, $partSize - ($total_reads-1)*$buffer_size);
						}
					
                                    $num = strlen($j + 1) == 2 ? "0".($j + 1) : (strlen($j + 1) == 1 ? "00".($j + 1) : ($j + 1));
                                    if(!@write_file($path.$fileName.".".$num, $fileChunk, 0))
                                      {
                                      echo "It was not possible to split the file <b>".$fileName.".".$num."</b> !<br><br>";
                                      $split_ok = false;
                                      }


                                    }
                                    $time = explode(" ", microtime());
                                      $time = str_replace("0.", $time[1], $time[0]);
                                      $list[$time] = array("name"    => $path.$fileName.".".$num,

                                                           "size"    => bytesToKbOrMbOrGb($partSize),
                                                           "date"    => time(),
                                                           "comment" => "Part ".($j + 1)."/".$totalParts." of ".$fileNamePerman);
                                  }
                              }

                              
				  fclose($fp);
                            unset($fileName);

                            if ($_GET["del_ok"] && !$disable_deleting)
                             {
                               if (!$split_ok )
                                {
                                 echo "An error occured. Source file not deleted!<br><br>";
                                }
                           elseif(@unlink($file["name"]))
                                {
                                  unset($list[$_GET["files"][$i]]);
                                  echo "Source file deleted.<br><br>";
                                }
                               else
                                {
                                 echo "Source file is<b>not deleted!</b><br><br>";
                                };
                             };
                            if(!updateListInFile($list))
                              {
                                  echo "Couldn't update. File already exists!<br><br>";
                              }
                             } //if(file_exists($file["name"]))
                          }
                      break;

                      case "merge":
						if (count($_GET["files"]) !== 1)
							{
							echo "Please select only the .crc file!<br><br>";
							}
						else
							{
							$file = $list[$_GET["files"][0]];
							if (substr($file["name"], -4) !== ".crc")
								{
								echo "Please select the .crc file!<br><br>";
								}
							else
								{
								$fs = @fopen($file["name"], "rb");
								if (!$fs)
									{
									echo "Can't read the .crc file!<br><br>";
									}
								else
									{
									flock($fs, LOCK_SH);
									while(!feof($fs))
										{
										$data .= trim(fgets($fs, 1024));
										if ($data === false) {break;}
										}
									flock($fs, LOCK_UN);
									fclose($fs);
									$tmp = explode("=", $data);
									$crc = array($tmp[0] => substr($tmp[1],0,-4), substr($tmp[1],-4) => substr($tmp[2],0,-5), substr($tmp[2],-5) => $tmp[3]);
									$dir = dir(DOWNLOAD_DIR);
									$filename = substr($crc["filename"], 0, strrpos($crc["filename"], "."));
									while(($f = $dir->read()) !== false)
										{
										if (ereg("$filename.([0-9]{3})", $f))
											{
											$files[]= $f;
											}
										}
									$dir->close();
									if (!is_array($files))
										{
										echo "The files needed to merge are not found!<br><br>";
										}
									else
										{
										$fs = @fopen(DOWNLOAD_DIR.$crc["filename"], "wb");
										if (!$fs)
											{
											echo "The file can't be opened for writing!<br><br>";
											}
										else
											{
											flock($fs, LOCK_EX);
											foreach ($files as $fn)
												{
												$fp = @fopen(DOWNLOAD_DIR.$fn, "rb");
												flock($fp, LOCK_SH);
												while (!feof($fp))
													{
													$data = fgets($fp, 1024);
													if ($data === false) {break;}
													else {fwrite($fs, $data);}
													}
												flock($fp, LOCK_UN);
												fclose($fp);
												}
											flock($fs, LOCK_UN);
											fclose($fs);
											$fs = filesize(DOWNLOAD_DIR.$crc["filename"]);
											if ($fs != $crc["size"])
												{
												echo "Filesize doesn't match!<br><br>";
												}
											else
												{
												?>
<form method="post">
<input type="hidden" name="act" value="merge_go">
<input type="hidden" name="filename" value="<?php echo $crc["filename"]; ?>">
<input type="hidden" name="path" value="<?php echo dirname($file["name"]); ?>">
<input type="hidden" name="size" value="<?php echo $crc["size"]; ?>">
<input type="hidden" name="crc32" value="<?php echo $crc["crc32"]; ?>">
Do you want to perform a CRC check?<br>(recommended)<br>
<table>
<tr>
<td>
<input type="submit" name="yes" style="width:33px; height:23px" value="Yes">
</td>
<td>
&nbsp;&nbsp;&nbsp;
</td>
<td>
<input type="submit" name="no" style="width:33px; height:23px" value="No">
</td>
</tr>
</table>
</form>
												<?php
												}
											}
										}
									}
								}
							}
                      break;
                      
                      case "merge_go":
						if ($_POST["yes"])
							{
							$fileContents = read_file(DOWNLOAD_DIR.$_POST["filename"]);
							$fc = strtoupper(dechex(crc32($fileContents)));
							$fc = str_repeat("0", 8 - strlen($fc)).$fc;
							if ($fc != $_POST["crc32"])
								{
								echo "CRC32 checksum doesn't match!<br><br>";
								}
							else
								{
								echo "File <b>".$_POST["filename"]."</b> successfully merged!<br><br>";
								}
							}
						else
							{
							echo "File <b>".$_POST["filename"]."</b> successfully merged, but not tested!<br><br>";
							}
            $time = explode(" ", microtime());
						$time = str_replace("0.", $time[1], $time[0]);
						$list[$time] = array("name"    => $_POST["path"].PATH_SPLITTER.$_POST["filename"],
                                 "size"    => bytesToKbOrMbOrGb($_POST["size"]),
                                 "date"    => time());
						if (!updateListInFile($list))
							{
							echo "Couldn't update the list. File already exists!<br><br>";
							}
                      break;
                      
                      case "rename":
                        if(count($_GET["files"]) < 1)
                          {
                            echo "Select at least one file.<br><br>";
                          }elseif($disable_deleting)
						  {
							echo "you don't have permission to rename files";
						  }
                        else
                          {
                            ?>
                            <form method="post">
                              <input type="hidden" name="act" value="rename_go">
                               <table align="center">
                                <tr>
                                  <td>
                                    <table>
                              <?php
                                for($i = 0; $i < count($_GET["files"]); $i++)
                                  {
                                    $file = $list[$_GET["files"][$i]];
                                    ?>
                                      <input type="hidden" name="files[]" value="<?php echo $_GET["files"][$i]; ?>">
                                          <tr>
                                            <td align="center"><b><?php echo basename($file["name"]); ?></b></td>
                                          </tr>
                                          <tr>
                                            <td>
                                              New name:&nbsp;<input type="text" name="newName[]" size="25" value="<?php echo basename($file["name"]); ?>">
                                            </td>
                                          </tr>
                                          <tr>
                                            <td>
                                            </td>
                                          </tr>
                                    <?php
                                  }
                              ?>
                                    </table>
                                  </td>
                                  <td>
                                    <input type="submit" value="Rename">
                                  </td>
                                </tr>
                                <tr>
                                  <td>
                                  </td>
                                </tr>
                              </table>
                            </form>
                            <?php
                          }
                      break;

                      case "rename_go":
                         $smthExists = FALSE;
                         for($i = 0; $i < count($_GET["files"]); $i++)
                          {
                            $file = $list[$_GET["files"][$i]];
                            
                            if(file_exists($file["name"]))
                              {
                                $smthExists = TRUE;
                                $newName = dirname($file["name"]).PATH_SPLITTER.$_GET["newName"][$i];
                                $filetype = strrchr($newName, ".");
                      
								if (is_array($forbidden_filetypes) && in_array(strtolower($filetype), $forbidden_filetypes))
									{
									print "The filetype $filetype is forbidden to be renamed<br><br>";
									}
								else
									{
									if(@rename($file["name"], $newName))
										{
										echo "File <b>".$file["name"]."</b> renamed to <b>".basename($newName)."</b><br><br>";
										$list[$_GET["files"][$i]]["name"] = $newName;
										}
									else
										{
										echo "Couldn't rename the file <b>".$file["name"]."</b>!<br><br>";
										}
									}
                              }
                            else
                             {
                               echo "File <b>".$file["name"]."</b> not found!<br><br>";
                             }
                          }
                         if($smthExists)
                           {
                             if(!updateListInFile($list))
                              {
                                  echo "Couldn't Update<br><br>";
                              }
                           }
                      break;
					  
						//MassRename
						case "mrename":
                        if(count($_GET["files"]) < 1)
                          {
                            echo "Please select atleast one file<br><br>";
                          }
                        else
                          {
                            ?>
                              <form method="post">
                              <input type="hidden" name="act" value="mrename_go">
                              File<?php echo count($_GET["files"]) > 1 ? "s" : ""; ?>:
                              <?php
                              for($i = 0; $i < count($_GET["files"]); $i++)
                                {
                                  $file = $list[$_GET["files"][$i]];
                                  ?>
                                  <input type="hidden" name="files[]" value="<?php echo $_GET["files"][$i]; ?>">
                                  <b><?php echo basename($file["name"]); ?></b><?php echo $i == count($_GET["files"]) - 1 ? "." : ",&nbsp"; ?>
                                  <?php
                                }
                              ?>
                              <table>
                                <tr><td valign="center"><b>Add extension&nbsp;</b><font size=2 color="yellow">without&nbsp; <b>.</b>&nbsp; (dot)</font><b><input type=input name="extension" style="width:60px; height:23px" value=''>&nbsp;to <?php echo count($_GET["files"]) > 1 ? " files" : " file"; ?>.</b>&nbsp;<input name="yes" type="submit" style="height:23px" value="Rename?">&nbsp;&nbsp;<input name="no" type="submit" style="height:23px" value="Cancel"></td></tr>
                              </table>
                              </form>
                            <?php
                          }
                      break;

                      case "mrename_go":
					  
					
						if($_GET["yes"] && @trim($_REQUEST[extension]))
							{	
								$_REQUEST[extension]=@trim($_REQUEST[extension]);

								while ($_REQUEST[extension][0] == '.') $_REQUEST[extension]=substr($_REQUEST[extension],1);
								
								if ($_REQUEST[extension])
									{
										for($i = 0; $i < count($_GET["files"]); $i++)
											{
												$file = $list[$_GET["files"][$i]];
												if(file_exists($file["name"]))
													{
													if (is_array($forbidden_filetypes) && in_array('.'.strtolower($_REQUEST[extension]), $forbidden_filetypes))
														{
														print "The filetype $filetype is forbidden to be renamed<br><br>";
														}
													else
														{
														if(@rename($file["name"],fixfilename($file["name"].".$_REQUEST[extension]")))
															{
																echo "<font color=yellow>File</font> <b>".basename($file["name"])."</b> <font color=yellow>rename to</font> <b>".fixfilename(basename($file["name"].".$_REQUEST[extension]"))."</b><br>";
																$list[$_GET["files"][$i]]["name"].='.'.$_REQUEST[extension];
																$list[$_GET["files"][$i]]["name"]=fixfilename($list[$_GET["files"][$i]]["name"]);
															}
																else
															{
																echo "<font color=red>Error rename the file</font><b>".basename($file["name"])."</b>!<br>";
															}
														}
													}
														else
													{
														echo "<font color=red>File</font> <b>".basename($file["name"])."</b> <font color=red>Not Found!</font><br>";
													}
											}
										if(!updateListInFile($list)) echo "Error in updating the list!<br>";
									}
							}
                        else
                          {
                            ?>
                              <script>
                                location.href="<?php echo substr($PHP_SELF, 0, strlen($PHP_SELF) - strlen(strstr($PHP_SELF, "?")))."?act=files"; ?>";
                              </script>
                            <?php
                          }

                      break;
						
						//end MassRename
						
                      case "ftp":
                        if(count($_GET["files"]) < 1)
                          {
                            echo "Select at least one file.<br><br>";
                          }
                        else
                          {
                            ?>
                              <form method="post">
                              <input type="hidden" name="act" value="ftp_go">
                              File<?php echo count($_GET["files"]) > 1 ? "s" : ""; ?>:
                              <?php
                              for($i = 0; $i < count($_GET["files"]); $i++)
                                {
                                  $file = $list[($_GET["files"][$i])];
                                  ?>
                                  <input type="hidden" name="files[]" value="<?php echo $_GET["files"][$i]; ?>">
                                  <b><?php echo basename($file["name"]); ?></b><?php echo $i == count($_GET["files"]) - 1 ? "." : ",&nbsp"; ?>
                                  <?php
                                }
                              ?><br><br>
                              <table align="center">
                                <tr>
                                  <td>
                                    <table>
                                      <tr>
                                        <td>
                                          Host:
                                        </td>
                                        <td>
                                          <input type="text" name="host" id="host"<?php echo $_COOKIE["host"] ? " value=\"".$_COOKIE["host"]."\"" : ""; ?> size="23">
                                        </td>
                                      </tr>
                                      <tr>
                                        <td>
                                         Port:
                                        </td>
                                        <td>
                                          <input type="text" name="port" id="port"<?php echo $_COOKIE["port"] ? " value=\"".$_COOKIE["port"]."\"" : " value=\"21\""; ?> size="4">
                                        </td>
                                      </tr>
                                      <tr>
                                        <td>
                                          Username:
                                        </td>
                                        <td>
                                          <input type="text" name="login" id="login"<?php echo $_COOKIE["login"] ? " value=\"".$_COOKIE["login"]."\"" : ""; ?> size="23">
                                        </td>
                                      </tr>
                                      <tr>
                                        <td>
                                          Password:
                                        </td>
                                        <td>
                                          <input type="password" name="password" id="password"<?php echo $_COOKIE["password"] ? " value=\"".$_COOKIE["password"]."\"" : ""; ?> size="23">
                                        </td>
                                      </tr>
                                      <tr>
                                        <td>
                                          Directory:
                                        </td>
                                        <td>
                                          <input type="text" name="dir" id="dir"<?php echo $_COOKIE["dir"] ? " value=\"".$_COOKIE["dir"]."\"" : " value=\"/\""; ?>  size="23">
                                        </td>
                                      </tr>
                                      <tr>
                                        <td>
                                          <input type="checkbox" name="del_ok" <?php if($disable_deleting) echo "disabled"; ?>>&nbsp;Delete source file after successful upload
                                        </td>
                                      </tr>
                                    </table>
                                  </td>
                                  <td>&nbsp;

                                  </td>
                                  <td>
                                    <table>
                                      <tr align="center">
                                        <td>
                                          <input type="submit" value="Upload">
                                        </td>
                                      </tr>
                                      <tr align="center">
                                        <td>
                                          Options
                                        </td>
                                      </tr>
                                      <tr align="center">
                                        <td>
                                          <script language="JavaScript">
                                          document.write(
                                            '<a href="javascript:setFtpParams();" id="hrefSetFtpParams" style="color: ' + (getCookie('ftpParams') == 1 ? '#808080' : '#0000FF') + ';">Copy Files</a> | ' +
                                            '<a href="javascript:delFtpParams();" id="hrefDelFtpParams" style="color: ' + (getCookie('ftpParams') == 1 ? '#0000FF' : '#808080') + '";">Move Files</a>'
                                          );
                                          </script>
                                        </td>
                                      </tr>
                                    </table>
                                  </td>
                                </tr>
                              </table>
                              </form>
                            <?php
                          }
                      break;

                      case "ftp_go":
                        require_once(CLASS_DIR."ftp.php");
                        $ftp = new ftp();
                        if(!$ftp->SetServer($_POST["host"], (int)$_POST["port"]))
                          {
                              $ftp->quit();
                              echo "Couldn't connect to the server".$_POST["host"].":".$_POST["port"].".<br>".
                                   "<a href=\"javascript:history.back(-1);\">Go Back</a><br><br>";
                          }
                        else
                          {
                              if(!$ftp->connect())
                                {
                                    $ftp->quit();
                                    echo "<br>Couldn't connect to the server ".$_POST["host"].":".$_POST["port"].".<br>".
                                         "<a href=\"javascript:history.back(-1);\">Go Back</a><br><br>";
                                }
                              else
                                {
									echo "Connected to: <b>ftp://".$_POST["host"]."</b> at port <b>".$_POST["port"]."</b>";
                                    if (!$ftp->login($_POST["login"], $_POST["password"]))
                                      {
                                          $ftp->quit();
                                          echo "<br>Wrong username and/or password <b>".$_POST["login"].":".$_POST["password"]."</b>.<br>".
                                                "<a href=\"javascript:history.back(-1);\">Go Back</a><br><br>";
                                      }
                                    else
                                      {
                                          //$ftp->Passive(FALSE);
                                          if(!$ftp->chdir($_POST["dir"]))
                                            {
                                                $ftp->quit();
                                                echo "<br>Cannot locate the folder<b>".$_POST["dir"]."</b>.<br>".
                                                      "<a href=\"javascript:history.back(-1);\">Go Back</a><br><br>";
                                            }
                                          else
                                            {
                                                ?>
<br>
<div id="status"></div><br>
<table cellspacing="0" cellpadding="0">
<tr>
<td></td>
<td>
<div style='border:#BBBBBB 1px solid; width:300px; height:10px;'>
<div id="progress" style='background-color:#000099; margin:1px; width:0%; height:8px;'>
</div>
</div>
</td>
<td></td>
<tr>
<tr>
<td align="left" id="received">0 KB</td>
<td align="center" id="percent">0%</td>
<td align="right" id="speed">0 KB/s</td>
</tr>
</table>
<br>
<?php
                                                $FtpUpload = TRUE;
                                                for($i = 0; $i < count($_GET["files"]); $i++)
                                                  {
                                                      $file = $list[$_GET["files"][$i]];
                                                      echo "<script>changeStatus('".basename($file["name"])."', '".$file["size"]."');</script>";
                                                      $FtpBytesTotal = filesize($file["name"]);
                                                      $FtpChunkSize = round($FtpBytesTotal / 333);
                                                      $FtpTimeStart = getmicrotime();
                                                      if($ftp->put($file["name"], basename($file["name"])))
                                                        {
                                                            $time = round(getmicrotime() - $FtpTimeStart);
                                                            $speed = round($FtpBytesTotal / 1024 / $time, 2);
                                                            echo "<script>pr(100, '".bytesToKbOrMbOrGb($FtpBytesTotal)."', ".$speed.")</script>\r\n";
                                                            flush();

                                                            if($_GET["del_ok"] && !$disable_deleting)
                                                             {
                                                              if(@unlink($file["name"]))
                                                               {
                                                                $v_ads=" and deleted ";
                                                                unset($list[$_GET["files"][$i]]);
                                                               }
                                                              else
                                                               {
                                                                $v_ads=", but <b>not deleted </b>";
                                                               };
                                                             } else $v_ads="";

                                                            echo "File <a href=\"ftp://".$_POST["login"].":".$_POST["password"]."@".$_POST["host"].":".$_POST["port"].
                                                                  $_POST["dir"]."/".basename($file["name"])."\"><b>".basename($file["name"])."</b></a> successfully uploaded$v_ads!".
                                                                  "<br>Time: <b>".sec2time($time)."</b><br>Average speed: <b>".$speed." KB/s</b><br><br>";
                                                        }
                                                      else
                                                        {
                                                            echo "Couldn't upload the file <b>".basename($file["name"])."</b>!<br>";
                                                        }
                                                  }
                                                $ftp->quit();
                                            }
                                      }
                                }

                          }
                      break;
                      
                      case "zip":
                       if (count($_GET["files"]) < 1)
							{
							echo "Select at least one file.<br><br>";
							}
						else
							{
							for($i = 0; $i < count($_GET["files"]); $i++)
                                {
                                $file = $list[($_GET["files"][$i])];
                                }
						print "What do you want to do?<br><br>";
						?>
<form name="ziplist" method="post">
<table cellspacing="5">
<tr>
<td align="center">
<select name="act" id="act" onChange="zip();">
<option selected>Select an Action</option>
<option value="zip_add">Add files to a ZIP archive</option>
</select>
</td>
<td>
</td>
<td id="add" align="center" style="DISPLAY: none;">
<table>
<tr><td>Archive Name:&nbsp;<input type="text" name="archive" size="25" value=".zip"><br></td></tr>
<tr><td><input type="checkbox" name="no_compression">&nbsp;Do not use compression<br></td></tr>
<tr><td><input type="checkbox" name="remove_path">&nbsp;Do not include directories<br></td></tr>
</table>
<table>
<tr><td><input type="submit" value="Add Files"></td></tr>
</table>
</td>
</tr>
</table>
<?php
                        echo "<br>Selected File".(count($_GET["files"]) > 1 ? "s" : "").": ";
                        for($i = 0; $i < count($_GET["files"]); $i++)
							{
                            $file = $list[($_GET["files"][$i])];
                            print "<input type=\"hidden\" name=\"files[]\" value=\"{$_GET[files][$i]}\">\r\n";
                            echo "<b>".basename($file["name"])."</b>";
                            echo ($i == count($_GET["files"]) - 1) ? "." : ",&nbsp;";
                            }
                        ?>
</form>
<?php
							}
                      break;
                      
                      case "zip_add":
						$_GET["archive"] = (strlen(trim(urldecode($_GET["archive"]))) > 4 && substr(trim(urldecode($_GET["archive"])), -4) == ".zip") ? trim(urldecode($_GET["archive"])) : "archive.zip";
						for($i = 0; $i < count($_GET["files"]); $i++)
							{
							$files[]= $list[($_GET["files"][$i])];
							}
						foreach ($files as $file)
							{
							$CurrDir = ROOT_DIR;
							
							$inCurrDir = stristr(dirname($file["name"]), $CurrDir) ? TRUE : FALSE;
							
							if ($inCurrDir)
								{
								$add_files[]= substr($file["name"], (strlen($CurrDir) + 1));
								}
							}
						require_once(CLASS_DIR."pclzip.php");
						$archive = new PclZip($_GET["archive"]);
						$no_compression = ($_GET["no_compression"] == "on") ? PCLZIP_OPT_NO_COMPRESSION : 77777;
						$remove_path = ($_GET["remove_path"] == "on") ? PCLZIP_OPT_REMOVE_ALL_PATH : 77777;
						if (file_exists($_GET["archive"]))
							{
							$v_list = $archive->add($add_files, $no_compression, $remove_path);
							}
						else
							{
							$v_list = $archive->create($add_files, $no_compression, $remove_path);
							}
						if ($v_list == 0) {
							echo "Error: ".$archive->errorInfo(true)."<br><br>";
							}
						else {
							echo "Archive <b>".$_GET["archive"]."</b> successfully created!<br><br>";
							}
					  break;
                      
                      case "pack":
                    if(count($_GET["files"]) < 1)
                          {
                            echo "Select at least one file.<br><br>";
                          }
                        else
                          {
                            ?>
                              <form method="post">
                              <input type="hidden" name="act" value="pack_go">
                              <?php
                              echo count($_GET["files"])." file".(count($_GET["files"]) > 1 ? "s" : "").":<br>";

                              for($i = 0; $i < count($_GET["files"]); $i++)
                                {
                                  $file = $list[$_GET["files"][$i]];
                                  ?>
                                  <input type="hidden" name="files[]" value="<?php echo $_GET["files"][$i]; ?>">
                                  <b><?php echo basename($file["name"]); ?></b><?php echo $i == count($_GET["files"]) - 1 ? "." : ",&nbsp;";
                                }
                              ?><br><br>
                               <table align="center">
                                 <tr>
                                   <td>
                                     Archive Name:&nbsp;<input type="text" name="arc_name" size="30">
                                   </td>
                                   <td>
                                     <input type="submit" value="Pack">
                                   </td>
                                 </tr>
                                 <tr>
                                   <td>
                                     Save To:&nbsp;<input type="text" name="path" size="30" value="<?php echo ($_COOKIE["path"] ? $_COOKIE["path"] : (strstr(ROOT_DIR, "\\") ? addslashes(dirname(__FILE__)) : dirname(__FILE__))); ?>">
                                   </td>
                                 </tr>
                               </table>
                               <table align="center">
                                <tr>
                                  <td>
                                    For use compress gz or bz2 write extension as Tar.gz or Tar.bz2;<br>
                                    Else this archive will be uncompress Tar<br>
                                  </td>
                                </tr>
                               </table>
                              </form>
                            <?php
                          }
                      break;

                      case "pack_go":
                          $smthExists=true;
                          if(count($_GET["files"]) < 1)
                           { echo "Select at least one file.<br><br>"; break; }

                          $arc_name=$_GET["arc_name"];
                          if (!$arc_name)
                           { echo "Please enter an archive name!<br><br>"; break; };

                          if (file_exists($arc_name))
                           { echo "File <b>".$arc_name."</b> already exists!<br><br>"; break; }

                          for($i = 0; $i < count($_GET["files"]); $i++)
                           {
                            $file = $list[$_GET["files"][$i]];
                            if(file_exists($file["name"]))
                             {
                              $v_list[] = $file["name"];
                             } else
                             {
                               echo "File <b>".$file["name"]."</b> not found!<br><br>";
                             }
                           }
                          if (!$v_list)
                           { echo "An error occured!<br><br>"; break; }
                          $arc_name = $path.'/'.$arc_name;
                          //$arc_name = dirname($arc_name).PATH_SPLITTER.$arc_name;


                          require_once(CLASS_DIR."tar.php");
                          $tar = new Archive_Tar($arc_name);
                          $tar->create($v_list,$arc_method);
                          if (!file_exists($arc_name))
                           { echo "Error! Archive not created.<br><br>"; break; }

                          if (count($v_list  =  $tar->listContent()) > 0)
                           {
                             echo "File"; echo count($v_list)>1 ? "s" : ""; echo "<br>";
                              for ($i=0; $i<sizeof($v_list); $i++)
                               {
                                 /* для фичи с удалением нужно подредактровать код. не работает пока.
                                 if ($_GET["val_del_ok"] && !$disable_deleting)
                                  {
                                   if(@unlink($v_list["name"]))
                                   $v_ads=" and deleted";
                                  }
                                 else  $v_ads=", but not deleted !</b>";*/
                                 echo "File ".$v_list[$i]["filename"]." was packed <br>";
                               }
                              echo "Packed in archive <b>$arc_name</b><br>";


                              $stmp=strtolower($arc_name);
                              if (strrchr($stmp,"tar.gz"  )+5==strlen($stmp))
                                 {  $arc_method="Tar.gz"; }
                          elseif (strrchr($stmp,"tar.bz2" )+6==strlen($stmp))
                                 {  $arc_method="Tar.bz2";}
                          else   {  $arc_method="Tar";    };
                              unset($stmp);

                              $time = explode(" ", microtime());
                              $time = str_replace("0.", $time[1], $time[0]);
                              $list[$time] = array("name"    => $arc_name,
                                                   "size"    => bytesToKbOrMbOrGb(filesize($arc_name)),
                                                   "date"    => $time,
                                                   "link"    => "",
                                                   "comment" => "archive ".$arc_method);
                            } else { echo "Error! Archive is Empty.<br><br>"; }
                          if(!updateListInFile($list))
                            {
                             echo "Couldn't Update!<br><br>";
                            }
                          break;
                    }
				  }
?>