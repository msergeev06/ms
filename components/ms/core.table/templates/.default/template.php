<?php if(!defined('MS_PROLOG_INCLUDED')||MS_PROLOG_INCLUDED!==true)die();
/**
 * Компонент ядра ms:table
 *
 * @package Ms\Core
 * @subpackage Entity\Components
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2018 Mikhail Sergeev
 */

$arParams = &$this->arParams;
$arResult = &$this->arResult;

?>
<table class="table table-hover">
	<thead>
	<tr class="info">
		<?foreach($arParams['TABLE_HEADER'] as $header):?>
			<td<?=$header['TD']?>><?=$header['VALUE']?></td>
		<?endforeach;?>
	</tr>
	</thead>
	<tbody>
	<?foreach($arResult['TABLE_DATA'] as $arData):?>
		<tr>
			<?foreach($arData as $code=>$ar_data):?>
				<td<?=$arParams['TABLE_HEADER'][$code]['TD']?>><?=$ar_data['VALUE']?></td>
			<?endforeach;?>
		</tr>
	<?endforeach;?>
	</tbody>
	<?if(isset($arResult['TABLE_FOOTER'])):?>
		<tfoot>
		<?foreach($arResult['TABLE_FOOTER'] as $i=>$arTr):?>
			<tr>
				<?foreach($arTr as $code=>$arFooter):?>
					<td<?=$arFooter['TD']?>><?=$arFooter['VALUES']?></td>
				<?endforeach;?>
			</tr>
		<?endforeach;?>
		</tfoot>
	<?endif;?>
</table>