<?php
if (! defined ( 'RAPIDLEECH' ))
{
	require_once ("index.html");
	exit ();
}

	require_once (HOST_DIR . 'download/' . "uploaded_to.php");

	$id = substr( $LINK, 13, 5 );
	$link = "http://uploaded.to/file/".$id;
		
	$hostClass = new uploaded_to();
	$hostClass->Download( $link );

/**************************************************\  
Updated by rajmalhotra 02 Feb 2010
\**************************************************/
?>