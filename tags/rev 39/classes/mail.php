<?php
if (!defined('RAPIDLEECH'))
  {
  require_once("index.html");
  exit;
  }

function xmail($from, $to, $subj, $text, $filename, $partSize = FALSE, $method = FALSE) {
  global $un;
  $fileContents = read_file($filename);
  $fileSize = strlen($fileContents);
  
	if ($partSize != FALSE & $method == "tc")
  		{
			$crc = strtoupper(dechex(crc32($fileContents)));
			$crc = str_repeat("0", 8 - strlen($crc)).$crc;
  		}
  			else
  		{
			$file = base64_encode($fileContents);
			$file = chunk_split($file);
			unset($fileContents);
		}
			

	if(!$file && !$fileContents) { return FALSE; }

	echo "Sending file <b>".basename($filename)."</b>...<br>";
	flush();
	sleep(1);
	for($i = 0; $i < strlen($subj); $i++)
		{
			$subzh .= "=".strtoupper(dechex(ord(substr($subj, $i, 1))));
		}

	$subj = "=?Windows-1251?Q?".$subzh.'?=';
	$un = strtoupper(uniqid(time()));
	$head = "From: ".$from."\n".
			"X-Mailer: PHP RapidLeech PlugMod\n".
			"Reply-To: ".$from."\n".
			"Mime-Version: 1.0\n".
			"Content-Type: multipart/mixed; boundary=\"----------".$un."\"\n\n";
	$zag = "------------".$un."\nContent-Type: text/plain; charset=Windows-1251\n".
			"Content-Transfer-Encoding: 8bit\n\n".$text."\n\n".
			"------------".$un."\n".
			"Content-Type: application/octet-stream; name=\"".basename($filename)."\"\n".
			"Content-Transfer-Encoding: base64\n".
			"Content-Disposition: attachment; filename=\"".basename($filename)."\"\n\n";
	echo "<span id=mailPart.".md5(basename($filename))."></span><br>";
	flush();
	if($partSize)
		{
			$partSize = round($partSize);
			if($method == "rfc")
				{
					$multiHeadMain = 	"From: ".$from."\n".
										"X-Mailer: PHP RapidLeech PlugMod\n".
										"Reply-To: ".$from."\n".
										"Mime-Version: 1.0\n".
										"Content-Type: message/partial; ";
					$totalParts = ceil(strlen($file) / $partSize);
					
					if ($totalParts == 1)
						{
							echo "No need spliting, Send single mail...<br>";
							flush();
							return mail($to, $subj, $zag.$file, $head) ? TRUE : FALSE;
						}
					
					echo "Spliting into Parts ".bytesToKbOrMbOrGb($partSize).", Method - RFC 2046...<br>";
					echo "Total Parts: <b>".$totalParts."</b><br>";
					$mailed = TRUE;
					for($i = 0; $i < $totalParts; $i++)
						{
							$multiHead = $multiHeadMain."id=\"".$filename."\"; number=".($i + 1)."; total=".$totalParts."\n\n";
							if($i == 0)
								{
									$multiHead = $multiHead.$head;
									$fileChunk = $zag.substr($file, 0, $partSize);
								}
									elseif($i == $totalParts - 1)
										{
											$fileChunk = substr($file, $i * $partSize);
										}
											else
										{
											$fileChunk = substr($file, $i * $partSize, $partSize);
										}
							echo "<script>mail('Sending parts¹ <b>".($i + 1)."</b>...', '".md5(basename($filename))."');</script>\r\n";
							flush();
							$mailed = $mailed & mail($to, $subj, $fileChunk, $multiHead);
							if (!$mailed) {return false;}
						}
				}
					elseif($method == "tc")
				{
					$totalParts = ceil($fileSize / $partSize);
					
					if ($totalParts == 1)
						{
							echo "No need spliting, Send single mail...<br>";
							flush();
							return mail($to, $subj, $zag.chunk_split(base64_encode($fileContents)), $head) ? TRUE : FALSE;
						}
									
					echo "Spliting into parts".bytesToKbOrMbOrGb($partSize).", Method - Total Commander...<br>";
					echo "Total Parts: <b>".$totalParts."</b><br>";
					$mailed = TRUE;
					$fileTmp = $filename;
					while(strpos($fileTmp, "."))
						{
							$fileName .= substr($fileTmp, 0, strpos($fileTmp, ".") + 1);
							$fileTmp = substr($fileTmp, strpos($fileTmp, ".") + 1);
						}
					$fileName = substr($fileName, 0, -1);
					for($i = 0; $i < $totalParts; $i++)
						{
							if($i == 0)
								{
									$fileChunk = substr($fileContents, 0, $partSize);
									$addHeads = addAdditionalHeaders(array("msg" => $text."\r\n"."File ".basename($filename)." (÷àñòü ".($i + 1)." èç ".$totalParts .").", "file" => array("filename" => $fileName.".crc", "stream" => chunk_split(base64_encode("filename=".basename($filename)."\r\n"."size=".$fileSize."\r\n"."crc32=".$crc."\r\n")))));
									$addHeads .= addAdditionalHeaders(array("file" => array("filename" => $fileName.".001", "stream" => chunk_split(base64_encode($fileChunk)))));
									//write_file($fileName.".crc", "filename=".basename($filename)."\r\n"."size=".$fileSize."\r\n"."crc32=".$crc."\r\n");
									//write_file($fileName.".001", $fileChunk);
								}
									elseif($i == $totalParts - 1)
										{
											$fileChunk = substr($fileContents, $i * $partSize);
											$addHeads =  addAdditionalHeaders(array("msg" => "File ".basename($filename)." (parts ".($i + 1)." from ".$totalParts .").",
											"file" => array("filename" => $fileName.".".(strlen($i + 1) == 2 ? "0".($i + 1) : (strlen($i + 1) == 1 ? "00".($i + 1) : ($i + 1))),
											"stream" => chunk_split(base64_encode($fileChunk)))));
											//write_file($fileName.".".(strlen($i + 1) == 2 ? "0".($i + 1) : (strlen($i + 1) == 1 ? "00".($i + 1) : ($i + 1))), $fileChunk);
										}
											else
										{
											$fileChunk = substr($fileContents, $i * $partSize, $partSize);
											$addHeads =  addAdditionalHeaders(array("msg" => "File ".basename($filename)." (parts ".($i + 1)." from ".$totalParts .").",
											"file" => array("filename" => $fileName.".".(strlen($i + 1) == 2 ? "0".($i + 1) : (strlen($i + 1) == 1 ? "00".($i + 1) : ($i + 1))),
											"stream" => chunk_split(base64_encode($fileChunk)))));
											//write_file($fileName.".".(strlen($i + 1) == 2 ? "0".($i + 1) : (strlen($i + 1) == 1 ? "00".($i + 1) : ($i + 1))), $fileChunk);
										}
							echo "<script>mail('Sending Parts ¹ <b>".($i + 1)."</b>...', '".md5(basename($filename))."');</script>\r\n";
							flush();
							$mailed = $mailed & mail($to, $subj, $addHeads, $head);
							if (!$mailed) {return false;}
						}
				}
		}
			else
		{
			return mail($to, $subj, $zag.$file, $head) ? TRUE : FALSE;
		}

	return $mailed ? TRUE : FALSE;
}

function addAdditionalHeaders($head) {
global $un;
if($head["msg"])
  {
    $ret = "------------".$un.
           "\nContent-Type: text/plain; charset=Windows-1251\n".
           "Content-Transfer-Encoding: 8bit\n\n".$head["msg"]."\n\n";
  }
if($head["file"]["filename"])
  {
    $ret .= "------------".$un."\n".
            "Content-Type: application/octet-stream; name=\"".basename($head["file"]["filename"])."\"\n".
            "Content-Transfer-Encoding: base64\n".
            "Content-Disposition: attachment; filename=\"".basename($head["file"]["filename"])."\"\n\n".
            $head["file"]["stream"]."\n\n";
  }
return $ret;
}
?>