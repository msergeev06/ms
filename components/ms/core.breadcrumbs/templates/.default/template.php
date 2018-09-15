<?php

$arNav = \Ms\Core\Entity\Application::getInstance()->getBreadcrumbs()->getNavArray();

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

