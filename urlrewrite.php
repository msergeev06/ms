<?php
include (dirname(__FILE__).'/core/tools/urlrewrite.php');
if(file_exists($_SERVER['DOCUMENT_ROOT'].'/404.php'))
	include_once($_SERVER['DOCUMENT_ROOT'].'/404.php');
