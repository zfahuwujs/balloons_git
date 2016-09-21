<?php

include("../../includes/global.inc.php");
require_once("../../classes/db.inc.php");
$db = new db();

$query = "
SELECT * 
FROM ".$glob['dbprefix']."CubeCart_options_mid  
WHERE father_id = ".$db->mySQLSafe($_GET['option'])." 
ORDER BY value_name ASC";

$attributes = $db->select($query);

$output = '';

if($attributes){
	
	$output = '<select name="attribute" id="attribute" class="textbox">';
	
	for ($i=0; $i<count($attributes); $i++){
		$output .= '<option value="'.$attributes[$i]["value_id"].'">'.$attributes[$i]["value_name"].'</option>';
	}
	
	$output .= '</select>';
	
}else{

	$output = "No Attributes";
	
}

echo $output;


/*<select name="attribute" id="attribute" class="textbox">

<?php for ($i=0; $i<count($fullAttributes); $i++){ ?>

<option value="<?php echo $fullAttributes[$i]['value_id'];?>" <?php if($fullAttributes[$i]['value_id']==$existingOptions[0]['value_id']) echo "selected='selected'"; ?>><?php echo $fullAttributes[$i]['value_name'];?></option>

<?php } ?>    

</select>*/




?>