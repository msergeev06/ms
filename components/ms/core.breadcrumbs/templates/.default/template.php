<?php
/**
 * Компонент ядра ms:code.breadcrumbs
 *
 * @package    Ms\Core
 * @subpackage Entity\Components
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2018 Mikhail Sergeev
 */

use \Ms\Core\Entity\System\Breadcrumbs;

$arNav = Breadcrumbs::getInstance()->getNavArray();

?>
<?if (!empty($arNav)):?>
	<ol class="breadcrumb">
	<?foreach ($arNav as $i=>$nav):?>
		<?if(isset($arNav[$i+1])):?>
			<li><a href="<?=(isset($nav['URL']))?$nav['URL']:'#'?>"><?=$nav['TITLE']?></a></li>
		<?else:?>
			<li class="active"><?=$nav['TITLE']?></li>
		<?endif;?>
	<?endforeach;?>
	</ol>
<?endif;?>

