<?if(!defined('MS_PROLOG_INCLUDED')||MS_PROLOG_INCLUDED!==true)die('Access denied');
$app = \MSergeev\Core\Entity\Application::getInstance();
?>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		<?$app->showDownJs();?>
	});
</script>
</body>
</html>
