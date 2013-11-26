<?php
$forceSuccess = $modx->getOption('forceSuccess',$scriptProperties,false);
$success = $modx->getOption('success',$_REQUEST,NULL);

if ($forceSuccess === 'true') {
	$modx->regClientHTMLBlock('<script type="text/javascript">Shop.empty(true);</script>');
} else {
	if ($success === '1') {
		$modx->regClientHTMLBlock('<script type="text/javascript">Shop.empty(true);</script>');
	} else return false;
}