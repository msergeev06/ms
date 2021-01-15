<?if(!defined('MS_PROLOG_INCLUDED')||MS_PROLOG_INCLUDED!==true)die('Access denied');
$application = \Ms\Core\Entity\System\Application::getInstance();
//define("SHOW_SQL_WORK_TIME",true);
$application->includePlugin('bootstrap-css-min');
$application->includePlugin('bootstrap-js-min');
?>
<!DOCTYPE html>
<html>
<head>
	<title><?$application->showTitle();?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<?$application->showMeta();?>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
</head>
<body>
