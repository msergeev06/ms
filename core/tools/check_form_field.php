<? include ($_SERVER['DOCUMENT_ROOT'].'/ms/core/prolog_before.php');
use MSergeev\Core\Lib\Loader;
use MSergeev\Core\Lib\Modules;

$app = \MSergeev\Core\Entity\Application::getInstance();
$request = $app->getContext()->getRequest();
header('Content-Type: application/json');

$arReturn = array(
	'status' => 'OK'
);
$namespace = $request->getPost('namespace');
$function = $request->getPost('func');
$value = $request->getPost('value');
$arReturn['namespace'] = $namespace;
$arReturn['function'] = $function;
$arReturn['value'] = $value;
if (!$namespace || !$function)
{
	$arReturn['exit'] = 'no_namespace_function';
	echo json_encode($arReturn);
}
elseif (!Loader::includeModule(Modules::getModuleFromNamespace ($namespace)))
{
	$arReturn['exit'] = 'no_include_module'.Modules::getModuleFromNamespace ($namespace);
	echo json_encode($arReturn);
}
else{
	$res = call_user_func(array($namespace,$function),$value);
	if ($res === true)
	{
		$arReturn['exit'] = 'ok';
		echo json_encode($arReturn);
	}
	else
	{
		$arReturn['exit'] = 'error';
		$arReturn['status'] = 'error';
		$arReturn['err'] = $res;
		echo json_encode($arReturn);
	}
}


