<?php
/**
 * Основной подключаемый файл ядра
 *
 * @package Ms\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2017 Mikhail Sergeev
 */

use Ms\Core\Entity\Application;

$app = Application::getInstance();
$coreRoot = $app->getSettings()->getCoreRoot();
$coreNamespace = 'Ms\Core';
require($coreRoot.'/exception/system.php');
require($coreRoot.'/exception/class_not_found.php');
//require($coreRoot.'/entity/error.php');
//require($coreRoot.'/entity/error_collection.php');
require($coreRoot.'/lib/modules.php');
require($coreRoot.'/entity/type/date.php');
require($coreRoot.'/lib/loader.php');
\Ms\Core\Lib\Loader::init();
\Ms\Core\Lib\Loader::addAutoLoadClasses(
	array(
		/** Exceptions all*/
		$coreNamespace.'\Exception\AccessDeniedException'         => $coreRoot.'/exception/access_denied.php',
		$coreNamespace.'\Exception\ArgumentException'             => $coreRoot.'/exception/argument.php',
		$coreNamespace.'\Exception\ArgumentNullException'         => $coreRoot.'/exception/argument_null.php',
		$coreNamespace.'\Exception\ArgumentOutOfRangeException'   => $coreRoot.'/exception/argument_out_of_range.php',
		$coreNamespace.'\Exception\ArgumentTypeException'         => $coreRoot.'/exception/argument_type.php',
		$coreNamespace.'\Exception\InvalidOperationException'     => $coreRoot.'/exception/invalid_operation.php',
		$coreNamespace.'\Exception\NotImplementedException'       => $coreRoot.'/exception/not_implemented.php',
		$coreNamespace.'\Exception\NotSupportedException'         => $coreRoot.'/exception/not_supported.php',
		$coreNamespace.'\Exception\ObjectException'               => $coreRoot.'/exception/object.php',
		$coreNamespace.'\Exception\ObjectNotFoundException'       => $coreRoot.'/exception/object_not_found.php',
		$coreNamespace.'\Exception\ObjectPropertyException'       => $coreRoot.'/exception/object_property.php',
		$coreNamespace.'\Exception\Db\DbException'                => $coreRoot.'/exception/db/db.php',
		$coreNamespace.'\Exception\Db\ConnectionException'        => $coreRoot.'/exception/db/connection.php',
		$coreNamespace.'\Exception\Db\SqlException'               => $coreRoot.'/exception/db/sql.php',
		$coreNamespace.'\Exception\Db\SqlQueryException'          => $coreRoot.'/exception/db/sql_query.php',
		$coreNamespace.'\Exception\Io\IoException'                => $coreRoot.'/exception/io/io.php',
		$coreNamespace.'\Exception\Io\FileDeleteException'        => $coreRoot.'/exception/io/file_delete.php',
		$coreNamespace.'\Exception\Io\FileNotFoundException'      => $coreRoot.'/exception/io/file_not_found.php',
		$coreNamespace.'\Exception\Io\FileNotOpenedException'     => $coreRoot.'/exception/io/file_not_opened.php',
		$coreNamespace.'\Exception\Io\FileOpenException'          => $coreRoot.'/exception/io/file_open.php',
		$coreNamespace.'\Exception\Io\InvalidPathException'       => $coreRoot.'/exception/io/invalid_path.php',
		/** Interfaces */
		$coreNamespace.'\Interfaces\AllErrors' => $coreRoot.'/interfaces/all_errors.php',
		/** Lib */
		$coreNamespace.'\Lib\DataManager' => $coreRoot.'/lib/data_manager.php',
		$coreNamespace.'\Lib\ErrorHandler'=> $coreRoot.'/lib/error_handler.php',
		$coreNamespace.'\Lib\Errors'      => $coreRoot.'/lib/errors.php',
		$coreNamespace.'\Lib\Events'      => $coreRoot.'/lib/events.php',
		$coreNamespace.'\Lib\File'        => $coreRoot.'/lib/file.php',
		$coreNamespace.'\Lib\Form'        => $coreRoot.'/lib/form.php',
		$coreNamespace.'\Lib\Installer'   => $coreRoot.'/lib/installer.php',
		$coreNamespace.'\Lib\Loc'         => $coreRoot.'/lib/loc.php',
		$coreNamespace.'\Lib\Logs'        => $coreRoot.'/lib/logs.php',
		$coreNamespace.'\Lib\Options'     => $coreRoot.'/lib/options.php',
		$coreNamespace.'\Lib\ShellTools'  => $coreRoot.'/lib/shell_tools.php',
		$coreNamespace.'\Lib\TableHelper' => $coreRoot.'/lib/table_helper.php',
		$coreNamespace.'\Lib\Tools'       => $coreRoot.'/lib/tools.php',
		$coreNamespace.'\Lib\Urlrewrite'  => $coreRoot.'/lib/urlrewrite.php',
		$coreNamespace.'\Lib\Users'       => $coreRoot.'/lib/users.php',
		/** Lib\IO */
		$coreNamespace.'\Lib\IO\Files'    => $coreRoot.'/lib/i_o/files.php',
		$coreNamespace.'\Lib\IO\Path'     => $coreRoot.'/lib/i_o/path.php',
		/** Lib\Text */
		$coreNamespace.'\Lib\Text\BinaryString'   => $coreRoot.'/lib/text/binary_string.php',
		$coreNamespace.'\Lib\Text\Encoding'       => $coreRoot.'/lib/text/encoding.php',
		$coreNamespace.'\Lib\Text\UtfSafeString'  => $coreRoot.'/lib/text/utf_safe_string.php',
		/** Entity */
		$coreNamespace.'\Entity\Breadcrumbs'          => $coreRoot.'/entity/breadcrumbs.php',
		$coreNamespace.'\Entity\Component'            => $coreRoot.'/entity/component.php',
		$coreNamespace.'\Entity\ComponentParameter'   => $coreRoot.'/entity/component_parameter.php',
		$coreNamespace.'\Entity\Context'              => $coreRoot.'/entity/context.php',
		$coreNamespace.'\Entity\Error'                => $coreRoot.'/entity/error.php',
		$coreNamespace.'\Entity\ErrorCollection'      => $coreRoot.'/entity/error_collection.php',
		$coreNamespace.'\Entity\HttpRequest'          => $coreRoot.'/entity/http_request.php',
		$coreNamespace.'\Entity\Request'              => $coreRoot.'/entity/request.php',
		$coreNamespace.'\Entity\User'                 => $coreRoot.'/entity/user.php',
		$coreNamespace.'\Entity\Server'               => $coreRoot.'/entity/server.php',
		/** Entity\Db */
		$coreNamespace.'\Entity\Db\SqlHelper'       => $coreRoot.'/entity/db/sql_helper.php',
		$coreNamespace.'\Entity\Db\DBResult'        => $coreRoot.'/entity/db/d_b_result.php',
		$coreNamespace.'\Entity\Db\DataBase'        => $coreRoot.'/entity/db/data_base.php',
		/** Entity\Db\Fields all*/
		$coreNamespace.'\Entity\Db\Fields\Field'                  => $coreRoot.'/entity/db/fields/field.php',
		$coreNamespace.'\Entity\Db\Fields\ScalarField'            => $coreRoot.'/entity/db/fields/scalar_field.php',
		$coreNamespace.'\Entity\Db\Fields\IntegerField'           => $coreRoot.'/entity/db/fields/integer_field.php',
		$coreNamespace.'\Entity\Db\Fields\BigIntField'            => $coreRoot.'/entity/db/fields/big_int_field.php',
		$coreNamespace.'\Entity\Db\Fields\BooleanField'           => $coreRoot.'/entity/db/fields/boolean_field.php',
		$coreNamespace.'\Entity\Db\Fields\DateTimeField'          => $coreRoot.'/entity/db/fields/date_time_field.php',
		$coreNamespace.'\Entity\Db\Fields\DateField'              => $coreRoot.'/entity/db/fields/date_field.php',
		$coreNamespace.'\Entity\Db\Fields\TimeField'              => $coreRoot.'/entity/db/fields/time_field.php',
		$coreNamespace.'\Entity\Db\Fields\FloatField'             => $coreRoot.'/entity/db/fields/float_field.php',
		$coreNamespace.'\Entity\Db\Fields\StringField'            => $coreRoot.'/entity/db/fields/string_field.php',
		$coreNamespace.'\Entity\Db\Fields\TextField'              => $coreRoot.'/entity/db/fields/text_field.php',
		$coreNamespace.'\Entity\Db\Fields\LongtextField'          => $coreRoot.'/entity/db/fields/longtext_field.php',
		/** Entity\Db\Query */
		$coreNamespace.'\Entity\Db\Query\QueryBase'   => $coreRoot.'/entity/db/query/query_base.php',
		$coreNamespace.'\Entity\Db\Query\QueryCreate' => $coreRoot.'/entity/db/query/query_create.php',
		$coreNamespace.'\Entity\Db\Query\QueryDelete' => $coreRoot.'/entity/db/query/query_delete.php',
		$coreNamespace.'\Entity\Db\Query\QueryDrop'   => $coreRoot.'/entity/db/query/query_drop.php',
		$coreNamespace.'\Entity\Db\Query\QueryInsert' => $coreRoot.'/entity/db/query/query_insert.php',
		$coreNamespace.'\Entity\Db\Query\QuerySelect' => $coreRoot.'/entity/db/query/query_select.php',
		$coreNamespace.'\Entity\Db\Query\QueryUpdate' => $coreRoot.'/entity/db/query/query_update.php',
		/** Entity\Type all*/
		$coreNamespace.'\Entity\Type\Dictionary'                  => $coreRoot.'/entity/type/dictionary.php',
		$coreNamespace.'\Entity\Type\ParameterDictionary'         => $coreRoot.'/entity/type/parameter_dictionary.php',
//		$coreNamespace.'\Entity\Type\Date'                        => $coreRoot.'/entity/type/date.php',
		/** Form */
		$coreNamespace.'\Entity\Form\Field'               => $coreRoot.'/entity/form/field.php',
		$coreNamespace.'\Entity\Form\Hidden'              => $coreRoot.'/entity/form/hidden.php',
		$coreNamespace.'\Entity\Form\InputCheckbox'       => $coreRoot.'/entity/form/input_checkbox.php',
		$coreNamespace.'\Entity\Form\InputCheckboxBool'   => $coreRoot.'/entity/form/input_checkbox_bool.php',
		$coreNamespace.'\Entity\Form\InputColor'          => $coreRoot.'/entity/form/input_color.php',
		$coreNamespace.'\Entity\Form\InputDate'           => $coreRoot.'/entity/form/input_date.php',
		$coreNamespace.'\Entity\Form\InputDateTime'       => $coreRoot.'/entity/form/input_date_time.php',
		$coreNamespace.'\Entity\Form\InputDateTimeLocal'  => $coreRoot.'/entity/form/input_date_time_local.php',
		$coreNamespace.'\Entity\Form\InputEmail'          => $coreRoot.'/entity/form/input_email.php',
		$coreNamespace.'\Entity\Form\InputFile'           => $coreRoot.'/entity/form/input_file.php',
		$coreNamespace.'\Entity\Form\InputMonth'          => $coreRoot.'/entity/form/input_month.php',
		$coreNamespace.'\Entity\Form\InputNumber'         => $coreRoot.'/entity/form/input_number.php',
		$coreNamespace.'\Entity\Form\InputPassword'       => $coreRoot.'/entity/form/input_password.php',
		$coreNamespace.'\Entity\Form\InputRadio'          => $coreRoot.'/entity/form/input_radio.php',
		$coreNamespace.'\Entity\Form\InputRange'          => $coreRoot.'/entity/form/input_range.php',
		$coreNamespace.'\Entity\Form\InputSearch'         => $coreRoot.'/entity/form/input_search.php',
		$coreNamespace.'\Entity\Form\InputTel'            => $coreRoot.'/entity/form/input_tel.php',
		$coreNamespace.'\Entity\Form\InputText'           => $coreRoot.'/entity/form/input_text.php',
		$coreNamespace.'\Entity\Form\InputTime'           => $coreRoot.'/entity/form/input_time.php',
		$coreNamespace.'\Entity\Form\InputUrl'            => $coreRoot.'/entity/form/input_url.php',
		$coreNamespace.'\Entity\Form\InputWeek'           => $coreRoot.'/entity/form/input_week.php',
		$coreNamespace.'\Entity\Form\Select'              => $coreRoot.'/entity/form/select.php',
		$coreNamespace.'\Entity\Form\SelectBool'          => $coreRoot.'/entity/form/select_bool.php',
		$coreNamespace.'\Entity\Form\SelectMulti'         => $coreRoot.'/entity/form/select_multi.php',
		$coreNamespace.'\Entity\Form\TextArea'            => $coreRoot.'/entity/form/text_area.php',
		/** Tables */
		$coreNamespace.'\Tables\AgentsTable'          => $coreRoot.'/tables/agents.php',
		$coreNamespace.'\Tables\EventHandlersTable'   => $coreRoot.'/tables/event_handlers.php',
		$coreNamespace.'\Tables\FileTable'            => $coreRoot.'/tables/file.php',
		$coreNamespace.'\Tables\OptionsTable'         => $coreRoot.'/tables/options.php',
		$coreNamespace.'\Tables\SectionsTable'        => $coreRoot.'/tables/sections.php',
		$coreNamespace.'\Tables\UrlrewriteTable'      => $coreRoot.'/tables/urlrewrite.php',
		$coreNamespace.'\Tables\UserGroupAccessTable' => $coreRoot.'/tables/user_group_access.php',
		$coreNamespace.'\Tables\UserGroupsTable'      => $coreRoot.'/tables/user_groups.php',
		$coreNamespace.'\Tables\UserOptionsTable'     => $coreRoot.'/tables/user_options.php',
		$coreNamespace.'\Tables\UserToGroupTable'     => $coreRoot.'/tables/user_to_group.php',
		$coreNamespace.'\Tables\UsersTable'           => $coreRoot.'/tables/users.php',
		$coreNamespace.'\Tables\UsersPropertiesTable' => $coreRoot.'/tables/users_properties.php'
	)
);

spl_autoload_register('\Ms\Core\Lib\Loader::autoLoadClasses');

include_once($coreRoot.'/tools/tools.main.php');
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

