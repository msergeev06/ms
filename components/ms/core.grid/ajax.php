<?php include ($_SERVER['DOCUMENT_ROOT'].'/ms/core/prolog_before.php');
header('Content-Type: application/json');
/**
 * Ajax-обработчик компонента ms:core.grid
 *
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

if (
    !isset($_POST['session_id'])
    || $_POST['session_id'] != ms_sessid()
) {
    echo json_encode(
        [
            'status' => 'error',
            'message' => 'Неверный запрос'
        ]
    );

    die();
}

if (!isset($_POST['action']))
{
    echo json_encode (
        [
            'status' => 'error',
            'message' => 'Не указано действие'
        ]
    );

    die();
}

$handler = \Ms\Core\Grid\Handlers\AjaxHandler::getInstance();
if (!($handler instanceof \Ms\Core\Entity\Ajax\AjaxHandlerAbstract))
{
    echo json_encode(
        [
            'status' => 'error',
            'message' => 'Используется неверный обработчик Ajax-запросов'
        ]
    );

    die();
}

if (!method_exists($handler,$_POST['action']))
{
    echo json_encode(
        [
            'status' => 'error',
            'message' => 'Указано неверное действие'
        ]
    );

    die();
}

try
{
    $data = \Ms\Core\Entity\Ajax\AjaxHandler::getInstance()->processRequest(
        $_POST,
        __FILE__,
        __LINE__
    );
}
catch (Exception $e)
{
    echo json_encode(
        [
            'status' => 'error',
            'message' => $e->getMessage()
        ]
    );

    die();
}

$arReturn = [
    'status' => 'success',
    'data' => $data
];

echo json_encode($arReturn);
