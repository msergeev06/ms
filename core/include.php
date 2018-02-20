<?php
/**
 * Основной подключаемый файл ядра
 *
 * @package MSergeev\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2017 Mikhail Sergeev
 */

use MSergeev\Core\Entity\Application;

$app = Application::getInstance();
$coreRoot = $app->getSettings()->getCoreRoot();
require($coreRoot.'/exception/system.php');
require($coreRoot.'/exception/class_not_found.php');
//require($coreRoot.'/entity/error.php');
//require($coreRoot.'/entity/error_collection.php');
require($coreRoot.'/lib/modules.php');
require($coreRoot.'/lib/loader.php');
\MSergeev\Core\Lib\Loader::init();
\MSergeev\Core\Lib\Loader::addAutoLoadClasses(
	array(
		/** Exceptions all*/
		'MSergeev\Core\Exception\AccessDeniedException'         => $coreRoot.'/exception/access_denied.php',
		'MSergeev\Core\Exception\ArgumentException'             => $coreRoot.'/exception/argument.php',
		'MSergeev\Core\Exception\ArgumentNullException'         => $coreRoot.'/exception/argument_null.php',
		'MSergeev\Core\Exception\ArgumentOutOfRangeException'   => $coreRoot.'/exception/argument_out_of_range.php',
		'MSergeev\Core\Exception\ArgumentTypeException'         => $coreRoot.'/exception/argument_type.php',
		'MSergeev\Core\Exception\InvalidOperationException'     => $coreRoot.'/exception/invalid_operation.php',
		'MSergeev\Core\Exception\NotImplementedException'       => $coreRoot.'/exception/not_implemented.php',
		'MSergeev\Core\Exception\NotSupportedException'         => $coreRoot.'/exception/not_supported.php',
		'MSergeev\Core\Exception\ObjectException'               => $coreRoot.'/exception/object.php',
		'MSergeev\Core\Exception\ObjectNotFoundException'       => $coreRoot.'/exception/object_not_found.php',
		'MSergeev\Core\Exception\ObjectPropertyException'       => $coreRoot.'/exception/object_property.php',
		'MSergeev\Core\Exception\Db\DbException'                => $coreRoot.'/exception/db/db.php',
		'MSergeev\Core\Exception\Db\ConnectionException'        => $coreRoot.'/exception/db/connection.php',
		'MSergeev\Core\Exception\Db\SqlException'               => $coreRoot.'/exception/db/sql.php',
		'MSergeev\Core\Exception\Db\SqlQueryException'          => $coreRoot.'/exception/db/sql_query.php',
		'MSergeev\Core\Exception\Io\IoException'                => $coreRoot.'/exception/io/io.php',
		'MSergeev\Core\Exception\Io\FileDeleteException'        => $coreRoot.'/exception/io/file_delete.php',
		'MSergeev\Core\Exception\Io\FileNotFoundException'      => $coreRoot.'/exception/io/file_not_found.php',
		'MSergeev\Core\Exception\Io\FileNotOpenedException'     => $coreRoot.'/exception/io/file_not_opened.php',
		'MSergeev\Core\Exception\Io\FileOpenException'          => $coreRoot.'/exception/io/file_open.php',
		'MSergeev\Core\Exception\Io\InvalidPathException'       => $coreRoot.'/exception/io/invalid_path.php',
		/** Type all*/
		'MSergeev\Core\Entity\Type\Dictionary'                  => $coreRoot.'/entity/type/dictionary.php',
		'MSergeev\Core\Entity\Type\ParameterDictionary'         => $coreRoot.'/entity/type/parameter_dictionary.php',
		'MSergeev\Core\Entity\Type\Date'                        => $coreRoot.'/entity/type/date.php',
		/** Db\Fields all*/
		'MSergeev\Core\Entity\Db\Fields\Field'                  => $coreRoot.'/entity/db/fields/field.php',
		'MSergeev\Core\Entity\Db\Fields\ScalarField'            => $coreRoot.'/entity/db/fields/scalar_field.php',
		'MSergeev\Core\Entity\Db\Fields\IntegerField'           => $coreRoot.'/entity/db/fields/integer_field.php',
		'MSergeev\Core\Entity\Db\Fields\BigIntField'            => $coreRoot.'/entity/db/fields/big_int_field.php',
		'MSergeev\Core\Entity\Db\Fields\BooleanField'           => $coreRoot.'/entity/db/fields/boolean_field.php',
		'MSergeev\Core\Entity\Db\Fields\DateTimeField'          => $coreRoot.'/entity/db/fields/date_time_field.php',
		'MSergeev\Core\Entity\Db\Fields\DateField'              => $coreRoot.'/entity/db/fields/date_field.php',
		'MSergeev\Core\Entity\Db\Fields\TimeField'              => $coreRoot.'/entity/db/fields/time_field.php',
		'MSergeev\Core\Entity\Db\Fields\FloatField'             => $coreRoot.'/entity/db/fields/float_field.php',
		'MSergeev\Core\Entity\Db\Fields\StringField'            => $coreRoot.'/entity/db/fields/string_field.php',
		'MSergeev\Core\Entity\Db\Fields\TextField'              => $coreRoot.'/entity/db/fields/text_field.php',
		'MSergeev\Core\Entity\Db\Fields\LongtextField'          => $coreRoot.'/entity/db/fields/longtext_field.php',
		//'MSergeev\Core\Entity\Db\Fields\ReferenceField'       => $coreRoot.'/entity/db/fields/reference_field.php',
		//'MSergeev\Core\Entity\Db\Fields\EnumField'            => $coreRoot.'/entity/db/fields/enum_field.php',
		//'MSergeev\Core\Entity\Db\Fields\ExpressionField'      => $coreRoot.'/entity/db/fields/expression_field.php',
		/** Lib */
		'MSergeev\Core\Lib\Agents'      => $coreRoot.'/lib/agents.php',
		'MSergeev\Core\Lib\Buffer'      => $coreRoot.'/lib/buffer.php',
		'MSergeev\Core\Lib\DataManager' => $coreRoot.'/lib/data_manager.php',
		'MSergeev\Core\Lib\ErrorHandler'=> $coreRoot.'/lib/error_handler.php',
		'MSergeev\Core\Lib\Events'      => $coreRoot.'/lib/events.php',
		'MSergeev\Core\Lib\File'        => $coreRoot.'/lib/file.php',
		'MSergeev\Core\Lib\Form'        => $coreRoot.'/lib/form.php',
		'MSergeev\Core\Lib\Installer'   => $coreRoot.'/lib/installer.php',
		'MSergeev\Core\Lib\Loc'         => $coreRoot.'/lib/loc.php',
		'MSergeev\Core\Lib\Options'     => $coreRoot.'/lib/options.php',
		'MSergeev\Core\Lib\Sections'    => $coreRoot.'/lib/sections.php',
		'MSergeev\Core\Lib\ShellTools'  => $coreRoot.'/lib/shell_tools.php',
		'MSergeev\Core\Lib\TableHelper' => $coreRoot.'/lib/table_helper.php',
		'MSergeev\Core\Lib\Tools'       => $coreRoot.'/lib/tools.php',
		'MSergeev\Core\Lib\Users'       => $coreRoot.'/lib/users.php',
		'MSergeev\Core\Lib\Webix'       => $coreRoot.'/lib/webix.php',
		/** Lib\IO */
		'MSergeev\Core\Lib\IO\Files'    => $coreRoot.'/lib/i_o/files.php',
		'MSergeev\Core\Lib\IO\Path'     => $coreRoot.'/lib/i_o/path.php',
		/** Lib\Text */
		'MSergeev\Core\Lib\Text\BinaryString'   => $coreRoot.'/lib/text/binary_string.php',
		'MSergeev\Core\Lib\Text\Encoding'       => $coreRoot.'/lib/text/encoding.php',
		'MSergeev\Core\Lib\Text\UtfSafeString'  => $coreRoot.'/lib/text/utf_safe_string.php',
		/** Db */
		'MSergeev\Core\Entity\Db\SqlHelper'     => $coreRoot.'/entity/db/sql_helper.php',
		'MSergeev\Core\Entity\Db\SqlHelperDate' => $coreRoot.'/entity/db/sql_helper_date.php',
		'MSergeev\Core\Entity\Db\SqlHelperMath' => $coreRoot.'/entity/db/sql_helper_math.php',
		'MSergeev\Core\Entity\Db\SqlHelperStr'  => $coreRoot.'/entity/db/sql_helper_str.php',
		'MSergeev\Core\Entity\Db\DBResult'      => $coreRoot.'/entity/db/d_b_result.php',
		'MSergeev\Core\Entity\Db\QueryBase'     => $coreRoot.'/entity/db/query_base.php',
		'MSergeev\Core\Entity\Db\Query'         => $coreRoot.'/entity/db/query.php',
		'MSergeev\Core\Entity\Db\DataBase'      => $coreRoot.'/entity/db/data_base.php',
		/** Db\Query */
		'MSergeev\Core\Entity\Db\Query\QueryBase'   => $coreRoot.'/entity/db/query/query_base.php',
		'MSergeev\Core\Entity\Db\Query\QueryCreate' => $coreRoot.'/entity/db/query/query_create.php',
		'MSergeev\Core\Entity\Db\Query\QueryDelete' => $coreRoot.'/entity/db/query/query_delete.php',
		'MSergeev\Core\Entity\Db\Query\QueryDrop'   => $coreRoot.'/entity/db/query/query_drop.php',
		'MSergeev\Core\Entity\Db\Query\QueryInsert' => $coreRoot.'/entity/db/query/query_insert.php',
		'MSergeev\Core\Entity\Db\Query\QuerySelect' => $coreRoot.'/entity/db/query/query_select.php',
		'MSergeev\Core\Entity\Db\Query\QueryUpdate' => $coreRoot.'/entity/db/query/query_update.php',
		/** Entity */
		'MSergeev\Core\Entity\User'                 => $coreRoot.'/entity/user.php',
		'MSergeev\Core\Entity\Context'              => $coreRoot.'/entity/context.php',
		'MSergeev\Core\Entity\Request'              => $coreRoot.'/entity/request.php',
		'MSergeev\Core\Entity\HttpRequest'          => $coreRoot.'/entity/http_request.php',
		'MSergeev\Core\Entity\Environment'          => $coreRoot.'/entity/environment.php',
		'MSergeev\Core\Entity\Server'               => $coreRoot.'/entity/server.php',
		'MSergeev\Core\Entity\Component'            => $coreRoot.'/entity/component.php',
		'MSergeev\Core\Entity\ComponentParameter'   => $coreRoot.'/entity/component_parameter.php',
		'MSergeev\Core\Entity\WebixHelper'          => $coreRoot.'/entity/webix_helper.php',
		'MSergeev\Core\Entity\Module'               => $coreRoot.'/entity/module.php',
		/** Form */
		'MSergeev\Core\Entity\Form\Field'               => $coreRoot.'/entity/form/field.php',
		'MSergeev\Core\Entity\Form\Hidden'              => $coreRoot.'/entity/form/hidden.php',
		'MSergeev\Core\Entity\Form\InputColor'          => $coreRoot.'/entity/form/input_color.php',
		'MSergeev\Core\Entity\Form\InputDate'           => $coreRoot.'/entity/form/input_date.php',
		'MSergeev\Core\Entity\Form\InputDateTime'       => $coreRoot.'/entity/form/input_date_time.php',
		'MSergeev\Core\Entity\Form\InputDateTimeLocal'  => $coreRoot.'/entity/form/input_date_time_local.php',
		'MSergeev\Core\Entity\Form\InputEmail'          => $coreRoot.'/entity/form/input_email.php',
		'MSergeev\Core\Entity\Form\InputFile'           => $coreRoot.'/entity/form/input_file.php',
		'MSergeev\Core\Entity\Form\InputMonth'          => $coreRoot.'/entity/form/input_month.php',
		'MSergeev\Core\Entity\Form\InputNumber'         => $coreRoot.'/entity/form/input_number.php',
		'MSergeev\Core\Entity\Form\InputPassword'       => $coreRoot.'/entity/form/input_password.php',
		'MSergeev\Core\Entity\Form\InputRange'          => $coreRoot.'/entity/form/input_range.php',
		'MSergeev\Core\Entity\Form\InputSearch'         => $coreRoot.'/entity/form/input_search.php',
		'MSergeev\Core\Entity\Form\InputTel'            => $coreRoot.'/entity/form/input_tel.php',
		'MSergeev\Core\Entity\Form\InputText'           => $coreRoot.'/entity/form/input_text.php',
		'MSergeev\Core\Entity\Form\InputTime'           => $coreRoot.'/entity/form/input_time.php',
		'MSergeev\Core\Entity\Form\InputUrl'            => $coreRoot.'/entity/form/input_url.php',
		'MSergeev\Core\Entity\Form\InputWeek'           => $coreRoot.'/entity/form/input_week.php',
		'MSergeev\Core\Entity\Form\Select'              => $coreRoot.'/entity/form/select.php',
		'MSergeev\Core\Entity\Form\SelectBool'          => $coreRoot.'/entity/form/select_bool.php',
		'MSergeev\Core\Entity\Form\SelectMulti'         => $coreRoot.'/entity/form/select_multi.php',
		'MSergeev\Core\Entity\Form\TextArea'            => $coreRoot.'/entity/form/text_area.php',
		/** Tables */
		'MSergeev\Core\Tables\AgentsTable'          => $coreRoot.'/tables/agents.php',
		'MSergeev\Core\Tables\EventHandlersTable'   => $coreRoot.'/tables/event_handlers.php',
		'MSergeev\Core\Tables\FileTable'            => $coreRoot.'/tables/file.php',
		'MSergeev\Core\Tables\OptionsTable'         => $coreRoot.'/tables/options.php',
		'MSergeev\Core\Tables\SectionsTable'        => $coreRoot.'/tables/sections.php',
		'MSergeev\Core\Tables\UsersTable'           => $coreRoot.'/tables/users.php',
		'MSergeev\Core\Tables\UsersPropertiesTable' => $coreRoot.'/tables/users_properties.php'
	)
);

spl_autoload_register('\MSergeev\Core\Lib\Loader::autoLoadClasses');

include_once($coreRoot.'/tools/tools.html.php');

$app->initializeExtendedKernel(
	array(
		'server'    => $_SERVER,
		'get'       => $_GET,
		'post'      => $_POST,
		'files'     => $_FILES,
		'cookie'    => $_COOKIE,
		'env'       => $_ENV
	)
);
$app->initializeBasicKernel();

