<?PHP

phpExtension();

$results = $db->select( "SELECT id, brandName, brandImage FROM " . $glob['dbprefix'] . "CubeCart_brands" );

$box_content = new XTemplate ("skins/".$config['skinDir']."/styleTemplates/boxes/brands.tpl");

if ( $results == TRUE )
{

	for ( $i = 0; $i < count ( $results ); $i++ )
	{
		
		$results[$i]['brandName'] = validHTML($results[$i]['brandName']);
		$box_content->assign("DATA",$results[$i]);
		$box_content->parse("brands.brand_div");
	
	}

}

$box_content->parse("brands");
$box_content = $box_content->text("brands");

?>