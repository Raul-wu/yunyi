<?php
/**
 * Created by PhpStorm.
 * User: hexi
 * Date: 15-2-4
 * Time: 上午11:46
 */

?>
<div id="main">
	<div class="content">
		<form method="post" target="_self" action="<?= $url ?>" id="form"></form>
	</div>
</div>
<script>
	console.log(document.getElementById('form').submit());
</script>