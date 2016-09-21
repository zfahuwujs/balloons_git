<?php
phpExtension();

$box_content = new XTemplate ("skins/".$config['skinDir']."/styleTemplates/boxes/fontResize.tpl");
if(evoHideBol(104)==false){
	$box_content->parse("font_resize");
	$box_content = $box_content->text("font_resize");
}
?>