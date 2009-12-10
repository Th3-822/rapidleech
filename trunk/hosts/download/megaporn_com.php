<?php
if (! defined ( 'RAPIDLEECH' ))
{
	require_once ("index.html");
	exit ();
}

class megaporn_com extends DownloadClass
{
	public function Download( $link )
	{
		global $premium_acc;
		$this->DownloadFree($link);
	}

	private function DownloadFree($link)
	{
		global $nn, $PHP_SELF, $pauth;
		$page = $this->GetPage($link);
				
		$un = trim ( cut_str ( $page, 'flashvars.un = "', '";' ) );
		$k1 = trim ( cut_str ( $page, 'flashvars.k1 = "', '";' ) );
		$k2 = trim ( cut_str ( $page, 'flashvars.k2 = "', '";' ) );
		$s = trim ( cut_str ( $page, 'flashvars.s = "', '";' ) );
		
		$id = $this->decrypt($un, $k1, $k2 );	
		$Href = "http://www".$s.".megaporn.com/files/".$id."/";
				
		$FileName = $id.".flv";
		$this->RedirectDownload( $Href, $FileName );
		exit ();
	}
	
	private function decrypt($str, $key1, $key2 )
	{
		$a = array();
		$b = array();
		
		$strArray = str_split( $str );
		for( $i=0; $i<sizeof($strArray); $i++ )
		{
			$hexToDec = hexdec( $strArray[$i] );
			$decToBin = decbin( $hexToDec );
			$v = $decToBin;
			while( strlen( $v ) < 4 ) $v="0".$v;
			array_push( $a , $v );
		}
		
		$arryStr = join("", $a);
		$a = str_split( $arryStr );
			
		for( $i=0; $i<384; $i++ )
		{
			$key1=($key1*11+77213 )%81371;
			$key2=($key2*17+92717)%192811;
			$b[$i]=($key1+$key2)%128;
		}
	
		for( $i=256; $i>=0; $i-- )
		{
			$c = $b[$i];
			$d = $i%128;
			$e = $a[$c];
			$a[$c] = $a[$d];
			$a[$d] = $e;
		}
		
		for( $i=0; $i<128; $i++ )
		{
			$a[$i] = $a[$i]^$b[$i+256]&1;
		}
		
		$f = join("", $a);
		$b = array();
		
		for( $i=0; $i<strlen( $f ); $i+=4 )
		{
			$_loc9 = substr( $f , $i , 4 );
			array_push( $b , $_loc9 );
		}
		
		$f = array();
		for( $i=0; $i<sizeof($b); $i++ )
		{
			$binToDec = bindec ( $b[$i] );
			$decToHex = dechex ( $binToDec );
			array_push( $f , $decToHex );
		}
		return join("", $f);
	}
}	

// written by kaox  25 April 09
// update and converted in oop by rajmalhotra  09 Dec 2009
?>