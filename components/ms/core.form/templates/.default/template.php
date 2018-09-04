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

$arResult = &$this->arResult;
$arParams = &$this->arParams;
$app = \Ms\Core\Entity\Application::getInstance();
?>
<form class="<?=$arParams['FORM_CLASS']?>" role="form" name="<?=$arParams['FORM_NAME']?>" method="<?=$arParams['FORM_METHOD']?>" action="<?=$arParams['FORM_ACTION']?>">
	<?
	/**
	 * @var \Ms\Core\Entity\Form\Field $field
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
	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<button type="submit" class="<?=$arParams['FORM_SUBMIT_CLASS']?>"><?=$arParams['FORM_SUBMIT_NAME']?></button>
		</div>
	</div>
</form>
