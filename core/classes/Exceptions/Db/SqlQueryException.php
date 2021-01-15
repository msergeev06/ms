<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2017 Mikhail Sergeev
 */

namespace Ms\Core\Exceptions\Db;

/**
 * Класс Ms\Core\Exceptions\Db\SqlQueryException
 * Класс для исключений, возникающих при ошибке SQL запроса
 */
class SqlQueryException extends SqlException
{
    /**
     * @var string Текст SQL запроса
     */
    protected $query = "";

    /**
     * Конструктор. Создает объект исключения
     *
     * @param string          $message              Сообщение исключения
     *                                              Необязательный, по-умолчанию пустая строка
     * @param string          $databaseMessage      Сообщение базы данных
     *                                              Необязательный, по-умолчанию пустая строка
     * @param string          $query                Текст sql-запроса
     *                                              Необязательный, по-умолчанию пустая строка
     * @param \Exception|null $previous             Предыдущее исключение
     *                                              Необязательный, по-умолчанию null
     */
    public function __construct ($message = "", $databaseMessage = "", $query = "", \Exception $previous = null)
    {
        parent::__construct($message, $databaseMessage, $previous);

        $this->query = $query;
    }

    /**
     * Возвращает текст SQL-запроса
     *
     * @return string
     */
    public function getQuery ()
    {
        return $this->query;
    }

    /**
     * Возвращает текст исключения в виде html-кода
     *
     * @return string
     */
    public function showException ()
    {
        $html = '<pre><b><i>' . $this->getClassName() . ':</i></b> "' . $this->getMessage() . '"' . "\n";
        //$html .= '<b>DataBase message:</b> "'.$this->getDatabaseMessage().'"'."\n";
        $html .= "<b>Query:</b>\n" . '"' . $this->getQuery() . '"' . "\n";
        $html .= "<b>Stack trace:</b>\n" . $this->getTraceAsString() . "\n";
        $html .= "<b>" . $this->getFile() . " " . $this->getLine() . "</b>";
        $html .= "</pre>";

        return $html;
    }
}
