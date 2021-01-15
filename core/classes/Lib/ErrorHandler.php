<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2018 Mikhail Sergeev
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Lib;

use Ms\Core\Entity\System\Application;

/**
 * Класс Ms\Core\Lib\ErrorHandler
 * Внутренний обработчик ошибок
 */
class ErrorHandler
{
    /**
     * Обработчик пользовательских ошибок
     *
     * @param      $errNo
     * @param      $errStr
     * @param null $errFile
     * @param null $errLine
     * @param null $errContext
     *
     * @return bool
     */
    public static function handler ($errNo, $errStr, $errFile = null, $errLine = null, $errContext = null)
    {

        /*		if (!(error_reporting() & $errNo)) {

                    // Этот код ошибки не включен в error_reporting,
                    // так что пусть обрабатываются стандартным обработчиком ошибок PHP
                    return false;
                }*/

        echo '<p><strong>';
        //echo $errNo;
        switch ($errNo)
        {
            case E_USER_ERROR:
                echo 'ERROR: ';
                break;
            case E_USER_WARNING:
                echo 'WARNING: ';
                break;
            case E_USER_NOTICE:
                echo 'NOTICE: ';
                break;
            default:
                echo 'Error[', $errNo, ']: ';
                break;
        }
        echo $errStr, '</strong><br>', 'On: ', $errFile, ':', $errLine, '<br>';
        if (!is_null($errContext))
        {
            echo '<pre>', print_r($errContext, true), '</pre>';
        }
        $backtrace = debug_backtrace();
        echo 'BackTrace:', '<br>';
        foreach ($backtrace as $back)
        {
            if ($back['file'] != '')
            {
                echo $back['file'], ':', $back['line'], '<br>';
            }
        }
        echo '</p>';

        return true;
    }

    public static function exceptionHandler (\Throwable $e)
    {
        $filename = Application::getInstance()->getSettings()->getSystemLogFile();
        if ($f1 = @fopen($filename, 'a'))
        {
            $tmp = explode(' ', microtime());
            fwrite($f1, date("H:i:s ") . $tmp[0] . "\n");
            fwrite($f1, 'Error[' . $e->getCode() . ']: ' . $e->getMessage() . "\n");
            fwrite($f1, "Stack trace:\n");
            fwrite($f1, $e->getTraceAsString() . "\n");
            fwrite($f1, $e->getFile() . ": " . $e->getLine());
            fwrite($f1, "\n------------------\n");
            fclose($f1);
            @chmod($filename, 0644);
        }

        if (Application::getInstance()->getSettings()->isDebugMode())
        {
            $html = "<pre><b>Error[{$e->getCode()}]:</b> {$e->getMessage()}\n";
            $html .= "<b>Stack trace:</b>\n{$e->getTraceAsString()}\n";
            $html .= "<b>{$e->getFile()}: {$e->getLine()}</b></pre>";

            die($html);
        }

        die();
    }

    public function userErrorHandler ($errno, $errmsg, $filename, $linenum, $vars) {
        // дата и время для записи об ошибке
        $dt = date("Y-m-d H:i:s (T)");

        // определение ассоциативного массива строк ошибок
        // на самом деле следует рассматривать только элементы 2,8,256,512 и 1024
        $errortype = array (
            1   =>  "Ошибка",
            2   =>  "Предупреждение",
            4   =>  "Ошибка синтаксического анализа",
            8   =>  "Замечание",
            16  =>  "Ошибка ядра",
            32  =>  "Предупреждение ядра",
            64  =>  "Ошибка компиляции",
            128 =>  "Предупреждение компиляции",
            256 =>  "Ошибка пользователя",
            512 =>  "Предупреждение пользователя",
            1024=>  "Замечание пользователя"
        );
        // набор ошибок, для которого будут сохраняться значения переменных
        $user_errors = array(E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE);

        $err  = $dt." ".$errortype[$errno]." № ".$errno."\n";
        $err .= $errmsg."\n";
        $err .= "Вызов из ".$filename." строка № ".$linenum."\n";

        ob_end_clean();
        ob_start();
        print_r($vars);
        $v = "Переменные:".ob_get_contents()."\n";
        ob_end_clean();
        ob_start("error_callback");

        // сохранить протокол ошибок и отправить его мылом
        // mail('error@htmlweb.ru', 'PHP error report', $err.$v, "Content-Type: text/plain; charset=windows-1251" ) or die("Ошибка при отправке сообщения об ошибке");
        // error_log($err."\n", 3, dirname(__FILE__) . "/log/error.log") or die("Ошибка записи сообщения об ошибке в файл");
    }

    function error_callback($buffer) {
        // если на экран будет выведено сообщение о фатальной ошибке, мы его сможем обработать
        if (preg_match("/<b>error</b>.*/",$buffer,$regs)) {
            $regs[0] = date("Y-m-d H:i:s (T)")."\n".preg_replace("/<[/]?b(r /)?>/","",$regs[0])."\n";
            // mail('error@htmlweb.ru', 'PHP error report', $regs[0], "Content-Type: text/plain; charset=windows-1251" ) or die("Ошибка при отправке сообщения об ошибке");
            // error_log($regs[0], 3, dirname(__FILE__) . "/log/error.log") or die("Ошибка записи сообщения об ошибке в файл");
            return "Ошибка, выполнение прервано";
        }
        else
            return $buffer;
    }
}