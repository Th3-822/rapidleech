<?php
if (! defined ( 'RAPIDLEECH' ))
{
require_once ("index.html");
exit ();
}

require_once (HOST_DIR . 'download/' . "uploaded_to.php");
$link = str_replace("ul.to", "uploaded.to/file", $LINK);
$hostClass = new uploaded_to();
$hostClass->Download( $link );

/**************************************************\
Updated by rajmalhotra 07 Feb 2010
Updated by vdhdevil for new site layout 16 March 2011 refer to this thread http://www.rapidleech.com/topic/11197-new-uploadedto-plugin/
\**************************************************/
?>