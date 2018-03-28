<?php

//___write2Log($_SERVER,'All Server');
/*
$referer = $_SERVER['HTTP_REFERER'];
$requestScheme = $_SERVER['REQUEST_SCHEME'];
$serverName = $_SERVER['SERVER_NAME'];
$serverPort = $_SERVER['SERVER_PORT'];
$uri = str_replace($requestScheme,'',$referer);
$uri = str_replace('://','',$uri);
if (strpos($uri,$serverName.':'.$serverPort)!==false)
{
	$uri = str_replace($serverName.':'.$serverPort,'',$uri);
}
else
{
	$uri = str_replace($serverName,'',$uri);
}
$_SERVER['REQUEST_URI'] = $uri;
*/

if(file_exists($_SERVER['DOCUMENT_ROOT']."/urlrewrite.php"))
{
	$arUrlRewrite = include($_SERVER['DOCUMENT_ROOT']."/urlrewrite.php");
}
if (!empty($arUrlRewrite))
{
	foreach ($arUrlRewrite as $arRule)
	{
		if (preg_match($arRule['CONDITION'],$_SERVER['REQUEST_URI']))
		{
//			___write2Log('OK');
			if (strlen($arRule["RULE"]) > 0)
			{
				$url = preg_replace($arRule["CONDITION"], (strlen($arRule["PATH"]) > 0 ? $arRule["PATH"]."?" : "").$arRule["RULE"], $_SERVER["REQUEST_URI"]);
			}
			else
			{
				$url = $arRule["PATH"];
			}

			if(($pos=strpos($url, "?"))!==false)
			{
				$params = substr($url, $pos+1);
				parse_str($params, $vars);

				$_GET += $vars;
				$_REQUEST += $vars;
				$GLOBALS += $vars;
				$_SERVER["QUERY_STRING"] = $QUERY_STRING = $params;
				$url = substr($url, 0, $pos);
			}
//			___write2Log($_SERVER['DOCUMENT_ROOT'].$url);
//			___write2Log(is_file($_SERVER['DOCUMENT_ROOT'].$url));

			if(!file_exists($_SERVER['DOCUMENT_ROOT'].$url) || !is_file($_SERVER['DOCUMENT_ROOT'].$url))
			{
				continue;
			}

			HttpSetStatus("200 OK");

			$_SERVER['REDIRECT_STATUS'] = "200";

			$_SERVER["REAL_FILE_PATH"] = $url;

			include_once($_SERVER['DOCUMENT_ROOT'].$url);

			die();

		}
	}
}


function HttpSetStatus ($status)
{
	$sapi_type = php_sapi_name();
	if (substr($sapi_type, 0, 3) == 'cgi')
	{
		header("Status: ".$status);
	}
	else
	{
		header($_SERVER["SERVER_PROTOCOL"]." ".$status);
	}
}
function ___write2Log ($mess, $name=null)
{
	$f1 = fopen('/var/www/html/logs/rewrite_'.date('Ymd').'.log','a');
	if (is_array($mess))
	{
		$mess = print_r($mess,TRUE);
	}
	elseif (is_bool($mess))
	{
		$mess = ($mess) ? 'Y' : 'N';
	}
	if (!is_null($name))
	{
		$mess = $name.":\n".$mess;
	}
	fwrite($f1,$mess."\n---------------------------\n");
	fclose($f1);
}