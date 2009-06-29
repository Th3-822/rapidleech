<?php
if (! defined ( 'RAPIDLEECH' )) {
	require_once ("index.html");
	exit ();
}

$LINK = trim ( urldecode ( $_GET ["image"] ) );
$Url = parse_url ( $LINK );
$Referer = ($_GET ["referer"] ? trim ( urldecode ( $_GET ["referer"] ) ) : $LINK);
$Cookie = ($_GET ["cookie"] ? trim ( urldecode ( $_GET ["cookie"] ) ) : 0);
//die(print_r($Url));
$Image = geturl ( $Url ["host"], $Url ["port"] ? $Url ["port"] : 80, $Url ["path"] . ($Url ["query"] ? "?" . $Url ["query"] : ""), $Referer, $Cookie, 0, 0, $_GET ["proxy"], $pauth, $auth, $Url ["scheme"] );

if ($Image) {
	if (stristr ( $Image, "\n\n" )) {
		$det = "\n\n";
	} elseif (stristr ( $Image, $nn . $nn )) {
		$det = $nn . $nn;
	}
	if ($det) {
		$Image = explode ( $det, $Image );
		$header = explode ( "\n", $Image [0] );
		foreach ( $header as $value ) {
			header ( $value );
		}
		echo $Image [1];
	}
}
?>