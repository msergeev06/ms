<?php

$documentRoot = (($_SERVER["DOCUMENT_ROOT"]=='')?'/var/www':$_SERVER["DOCUMENT_ROOT"]);

return array(
	'Site' => array(
		'Protocol' => 'http',
		'SiteUrl' => $_SERVER['HTTP_HOST'].'',
		'DocumentRoot' => $documentRoot,
		'MsRoot' => $documentRoot.'/ms',
		'LocalRoot' => $documentRoot.'/local',
		'UploadDir' => $documentRoot.'/upload',
		'CoreRoot' => $documentRoot.'/ms/core',
		'TemplatesRoot' => $documentRoot.'/ms/templates',
		'ModulesRoot' => $documentRoot.'/ms/modules',
		'ComponentsRoot' => $documentRoot.'/ms/components',
		'Lang' => 'ru',
		'Charset' => 'UTF-8',
		'Template' => '.default'
	),
	'Files' => array(
		'ChmodFile' => 0666,
		'ChmodDir' => 0777,
		'CacheDir' => $documentRoot.'/ms/cached'
	),
	'Backup' => array(
		'DirBackupDb' => $documentRoot.'/ms/backup_db',
		'ExpireBackupFiles' => 5,
	),
	'Debug' => array(
		'DebugMode' => true,
		'DirLogs' => $documentRoot.'/logs',
		'SystemLogFile' => $documentRoot.'/logs/sys-log-'.date('Ymd').'.log',
		'ExpireLogFiles' => 10
	)
);