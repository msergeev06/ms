<?php

$arSettings = [
    'System' => [
        'Protocol'      => 'http',
        'SiteUrl'       => '192.168.0.30',
        'Lang'          => 'ru',
        'Charset'       => 'UTF-8',
        'Template'      => '.default',
        'NeedCheckAuth' => true, //Будет изменено ниже
        'HomeNetwork'   => '192.168.0.*',
        'CookiePrefix'  => 'dobrozhil',
        'DisableIconv'  => false
    ],
    'Paths' => [
        'DocumentRoot'  => '/var/www/html', //Будет изменено ниже
        'InstalledDir'  => '', //Если система установлена в корне, оставить пустым
        'MsRoot'        => '{Paths:DocumentRoot}{Paths:InstalledDir}/ms',
        'LocalRoot'     => '{Paths:DocumentRoot}{Paths:InstalledDir}/local',
        'UploadDir'     => '{Paths:LocalRoot}/upload',
        'CacheDir'      => '{Paths:LocalRoot}/cache',
        'BackupFiles'   => '{Paths:LocalRoot}/backup',
        'DirBackupDb'   => '{Paths:LocalRoot}/backup_db',
        'DirLogs'       => '{Paths:LocalRoot}/logs',
    ],
    'Files' => [
        'ChmodFile'     => 0666,
        'ChmodDir'      => 0777
    ],
    'Backup' => [
        'ExpireBackupFiles' => 5,
    ],
    'Debug' => [
        'DebugMode' => false,
        'ExpireSystemLogFiles' => 14,
        'ExpireDailyLogFiles' => 14,
        'ExpireMonthlyLogFiles' => 6,
        'SystemLogFile' => '{Paths:DirLogs}/sys_' . date('Ymd') . '.log'
    ],
    'Time' => [
        'Timezone'     => 'Europe/Moscow',
        'SiteDate'     => 'd.m.Y',
        'SiteTime'     => 'H:i:s',
        'SiteDateTime' => '{Time:SiteDate} {Time:SiteTime}'
    ],
    'DataBase' => [
        'Driver' => \Ms\Core\Entity\Db\Drivers\MySqliDriver::class,
        'Host' => 'localhost',
        'Base' => 'dobro',
        'User' => 'root',
        'Password' => 'rootpwd'
    ]
];

$bEmptyDocumentRoot = false;
if (empty($_SERVER["DOCUMENT_ROOT"]))
{
    $bEmptyDocumentRoot = true;
    $_SERVER["DOCUMENT_ROOT"] = str_replace($arSettings['Paths']['InstalledDir'] . '/ms', '', dirname(__FILE__));
}
$arSettings['Paths']['DocumentRoot'] = $_SERVER["DOCUMENT_ROOT"];
$arSettings['System']['NeedCheckAuth'] = !$bEmptyDocumentRoot;

return $arSettings;