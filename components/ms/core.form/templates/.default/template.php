<? if(!defined('MS_PROLOG_INCLUDED')||MS_PROLOG_INCLUDED!==true) die();
/**
 * Компонент ядра ms:form
 *
 * @package Ms\Core
 * @subpackage Entity\Components
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2018 Mikhail Sergeev
 * @since 0.2.0
 */

use Ms\Core\Entity;

$arResult = &$this->arResult;
$arParams = &$this->arParams;
//msDebug($arResult);
//msDebug($arParams);
//msDebug($this);
$app = Entity\Application::getInstance();
$componentPath = $app->getSitePath($app->getSettings()->getComponentsRoot())
    . '/' . $this->namespace . '/' . $this->componentName;
//msDebug($componentPath);
?>
<form class="form-<?=$arParams['FORM_NAME']?> <?=$arParams['FORM_CLASS']?>" role="form" name="<?=$arParams['FORM_NAME']?>" method="<?=$arParams['FORM_METHOD']?>"
      action="<?=$arParams['FORM_ACTION']?>">
	<?
	/**
	 * @var Entity\Form\Field $field
	 */
	foreach($arParams['FORM_FIELDS'] as $field)
	{
		if(isset($arResult['FIELDS_VALUES'][$field->getName()]))
		{
			$value = $arResult['FIELDS_VALUES'][$field->getName()];
		}
		else
		{
			$value = null;
		}
		echo $field->showField($value);
	}?>
	<script type="text/javascript">
		$(document).on('ready',function(){
			$('.help-block').each(function(){
				if (this.text=='')
				{
					this.hide();
				}
			});
		});
	</script>
	<input type="hidden" name="form_action" value="1">
	<?if(array_key_exists('FORM_HANDLER',$arParams)):?>
	<div class="col-sm-10">
		<span class="form-error-block"></span>
	</div>
    <?endif;?>
    <?if (!isset($arParams['SHOW_BUTTONS']) || $arParams['SHOW_BUTTONS'] === true):?>
	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<button type="submit" class="<?=$arParams['FORM_SUBMIT_CLASS']?>"<?if(array_key_exists('FORM_HANDLER',$arParams)):?> onclick="checkForm();return false;"<?endif;?>>
				<?=$arParams['FORM_SUBMIT_NAME']?>
			</button>
		</div>
	</div>
    <?endif;?>
</form>
<?if(array_key_exists('FORM_HANDLER',$arParams)):?>
<style>
.form-error-block {
	display: none;
	color: red;
}
</style>
<?endif;?>
<?if(array_key_exists('FORM_HANDLER',$arParams) && isset($arParams['FORM_HANDLER'][0]) && isset($arParams['FORM_HANDLER'][1])):?>
<script type="text/javascript">
	function checkForm (){

		var data = {
			<?foreach ($arParams['FORM_FIELDS'] as $field):?>
			<?
				/*$data = $this->getData($field->getName());
				$default = $field->getDefaultValue();
				if (is_null($data) && !is_null($default))
				{
					$data = $default;
				}
				else
				{
					$data = '';
				}*/
			?>
			<?=$field->getName()?>: $('#<?=$field->getName()?>').val(),
			<?endforeach;?>
			class_name: "<?=addslashes($arParams['FORM_HANDLER'][0])?>",
			method_name: "<?=$arParams['FORM_HANDLER'][1]?>",
            session_id: "<?=ms_sessid()?>"
		};
		// console.log(data);

		$.ajax({
			type: "POST",
			url: '<?=$componentPath?>/check_form.php',
			data: data,
			success: function(json){
				console.log(json);
				if (json.status == 'error')
				{
					$('.form-error-block').html(json.error_html).show();
				}
				else if (json.status == 'OK')
				{
					$('.form-error-block').text('').hide();
					$('.form-<?=$arParams['FORM_NAME']?>').submit();
				}
			},
			dataType: "JSON"
		});

	}
</script>
<?endif;?>