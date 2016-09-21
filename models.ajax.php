<?php
  // INCLUDE CORE VARIABLES & FUNCTIONS
  include_once("includes/global.inc.php");
  include_once("classes/db.inc.php");
  $db = new db();
  include_once("includes/functions.inc.php");
  $config = fetchDbConfig("config");


  $query = "SELECT id, model FROM {$glob['dbprefix']}CubeCart_model WHERE make_id = ".$db->mySQLSafe($_GET['make_id'])." ORDER BY model";
  $models = $db->select($query);

  $options = '<option value="0">select</option>';

	if($models){
		for($i = 0; $i < count($models); $i++){
			$options .= '<option value="'.$models[$i]['id'].'">'.$models[$i]['model'].'</option>'."\n";
		}
	}else{
		$options .= '<option value="0">Select</option>'."\n";
	}
echo $options;
?>