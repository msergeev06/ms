<? include ($_SERVER['DOCUMENT_ROOT'].'/ms/core/prolog_before.php');
header('Content-Type: application/json');

$arReturn = array(
	'status' => 'OK'
);
$data = $_POST;
//$arReturn['data'] = $_POST;
$arReturn['err_list'] = [];

$handler = $data['class_name'] . '::' . $data['method_name'];
$arReturn['handler'] = $handler;

//$bOk = true;
$bOk = $handler($data, $arReturn['err_list']);
if (!$bOk)
{
	$arReturn['status'] = 'error';
}
if ($data['session_id'] != ms_sessid())
{
	$arReturn['err_list'][] = 'Неверный ID сессии';
}

if (!empty($arReturn['err_list']))
{
	$html = '<ul>';

	foreach ($arReturn['err_list'] as $err)
	{
		$html .= '<li>' . $err . '</li>';
	}

	$html .= '</ul>';
	$arReturn['error_html'] = $html;
}


echo json_encode($arReturn);
