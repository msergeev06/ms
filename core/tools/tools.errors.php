<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

if (!function_exists('msErrorHandler'))
{
    function msErrorHandler ($errorNo, $errorMessage, $filename, $line, $vars)
    {
        // дата и время для записи об ошибке
        $dt = date("Y-m-d H:i:s (T)");

        // определение ассоциативного массива строк ошибок
        // на самом деле следует рассматривать только элементы 2,8,256,512 и 1024
        $errorType = [
            E_ERROR           => "Ошибка",
            E_WARNING         => "Предупреждение",
            E_PARSE           => "Ошибка синтаксического анализа",
            E_NOTICE          => "Замечание",
            E_CORE_ERROR      => "Ошибка ядра",
            E_CORE_WARNING    => "Предупреждение ядра",
            E_COMPILE_ERROR   => "Ошибка компиляции",
            E_COMPILE_WARNING => "Предупреждение компиляции",
            E_USER_ERROR      => "Ошибка пользователя",
            E_USER_WARNING    => "Предупреждение пользователя",
            E_USER_NOTICE     => "Замечание пользователя"//,
            // E_STRICT=>  "Строгие ошибки",
            // E_RECOVERABLE_ERROR=>  "Обрабатываемые ошибки",
            // E_DEPRECATED=>  "Отмечено устаревшим в PHP",
        ];
        // набор ошибок, для которого будут сохраняться значения переменных
        $userErrors = [E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE];

        $err = $dt . " " . $errorType[$errorNo] . " № " . $errorNo . "\n";
        $err .= $errorMessage . "\n";
        $err .= "Вызов из " . $filename . " строка № " . $line . "\n";

        ob_end_clean();
        ob_start();
        print_r($vars);
        $v = "Переменные:" . ob_get_contents() . "\n";
        ob_end_clean();
        ob_start("msErrorCallback");

        switch ($errorNo)
        {
            case E_ERROR:
                $class = 'error';
                break;
            case E_WARNING:
                $class = 'warning';
                break;
            case E_NOTICE:
                $class = 'info';
                break;
            default:
                $class = 'validation';
                break;
        }

        $echo = <<<EOL
        <style>
            .info, .warning, .error, .validation {
                font-family: Arial, Helvetica, sans-serif;
                font-size: 13px;
                border: 1px solid;
                margin: 10px 0px;
                padding: 15px 10px 15px 50px;
                background-repeat: no-repeat;
                background-position: 10px center;
            }
            .info {
                color: #00529B;
                background-color: #BDE5F8;
                background-image: url('/ms/core/img/info.png');
            }
            .warning {
                color: #9F6000;
                background-color: #FEEFB3;
                background-image: url('/ms/core/img/warning.png');
            }
            .error{
                color: #D8000C;
                background-color: #FFBABA;
                background-image: url('/ms/core/img/error.png');
            }
            .validation{
                color: #D63301;
                background-color: #FFCCBA;
                background-image: url('/ms/core/img/error.png');
            }
        </style>
        <div class="$class">
            <strong>$dt<br>Error: {$errorType[$errorNo]}</strong>&nbsp;№&nbsp;$errorNo<br>
            {nl2br($errorMessage)}<br>
            <strong>Вызов из $filename строка № $line</strong><br><br>
            <a href="#">Сообщить разработчику</a>&nbsp;|&nbsp;<a href="#">Отправить сообщение</a>
        </div><br>

EOL;

        if (!defined('RUN_CRONTAB_JOB') || RUN_CRONTAB_JOB !== true)
        {
            echo $echo;
        }

        // сохранить протокол ошибок и отправить его мылом
        // mail('error@htmlweb.ru', 'PHP error report', $err.$v, "Content-Type: text/plain; charset=windows-1251" ) or die("Ошибка при отправке сообщения об ошибке");
        // error_log($err."\n", 3, dirname(__FILE__) . "/log/error.log") or die("Ошибка записи сообщения об ошибке в файл");
    }
}

if (!function_exists('msErrorCallback'))
{
    function msErrorCallback($buffer)
    {
        // если на экран будет выведено сообщение о фатальной ошибке, мы его сможем обработать
        if (preg_match("/<b>error</b>.*/",$buffer,$regs))
        {
            $regs[0] = date("Y-m-d H:i:s (T)")."\n".preg_replace("/<[/]?b(r /)?>/","",$regs[0])."\n";
            // mail('error@htmlweb.ru', 'PHP error report', $regs[0], "Content-Type: text/plain; charset=windows-1251" ) or die("Ошибка при отправке сообщения об ошибке");
            // error_log($regs[0], 3, dirname(__FILE__) . "/log/error.log") or die("Ошибка записи сообщения об ошибке в файл");
            return "Ошибка, выполнение прервано";
        }
        else
            return $buffer;
    }
}

if (!function_exists('msShutdownHandler'))
{
    function msShutdownHandler ()
    {
        $e = @error_get_last();
        if (@is_array($e))
        {
            $code = isset($e['type']) ? $e['type'] : 0;
            $message = isset($e['message']) ? $e['message'] : '';
            $file = isset($e['file']) ? $e['file'] : '';
            $line = isset($e['line']) ? $e['line'] : '';

            if ((int)$code > 0)
            {
                if (function_exists('msErrorHandler'))
                {
                    msErrorHandler($code, $message, $file, $line, '');
                }
            }
        }
    }
}