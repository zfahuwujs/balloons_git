<?php

if(!isset($config)){
	echo "<html>\r\n<head>\r\n<title>Forbidden 403</title>\r\n</head>\r\n<body><h3>Forbidden 403</h3>\r\nThe document you are requesting is forbidden.\r\n</body>\r\n</html>";
	exit;
}

//set SQL staatement
$query = "SELECT * FROM ".$glob['dbprefix']."CubeCart_video ORDER BY title ASC";

//query database
$results = $db->select($query);

$view_video = new XTemplate ("skins/".$config['skinDir']."/styleTemplates/content/viewVideo.tpl");
if($results){
	for($i=0; $i<count($results); $i++){
		$view_video->assign("TITLE",$results[$i]['title']);
		$view_video->assign("DESC",$results[$i]['text']);
		$view_video->assign("EMBED",$results[$i]['embed']);
		$view_video->parse("view_video.video_true.video_items");
	}
	$view_video->parse("view_video.video_true");
}else{
	$view_video->parse("view_video.video_false");
}

$view_video->parse("view_video");
$page_content = $view_video->text("view_video");
?>
