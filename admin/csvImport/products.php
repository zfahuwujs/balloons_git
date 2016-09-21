<?php

include("../../includes/ini.inc.php");
include("../../includes/global.inc.php");
require_once("../../classes/db.inc.php");
$db = new db();
include_once("../../includes/functions.inc.php");
$config = fetchDbConfig("config");
include_once("../../language/".$config['defaultLang']."/lang.inc.php");
$enableSSl = 1;
include_once("../../includes/sslSwitch.inc.php");
include("../includes/auth.inc.php");
if(permission("csv","read")==FALSE){
	header("Location: ".$GLOBALS['rootRel']."admin/401.php");
	exit;
}

include('../includes/header.inc.php');
?>

<?php

if(isset($_POST["upload"])){
	if(!empty($_FILES["filename"])){
		if($_FILES["filename"]["type"] === "application/vnd.ms-excel"){
			if($_FILES["filename"]["error"] == 0){
				$msg = "";
				$fields = array();
				$row = 1;
				$error = 0;
				$handle = fopen($_FILES["filename"]["tmp_name"], "r");
					
				while(($data = fgetcsv($handle, 1000000, ",")) !== FALSE){
					if($row == 1){
						$optionsPos = 0;
						$optionsNo = 0;
						$catPos = 0;
						$productNew = 1;
						$catUpdate = 0;
						
						for($i=0; $i<count($data); $i++){
							if($data[$i] == "productId"){
								$productMarker = $i;
								$productNew = 1;
							}
							
							if($data[$i] == "Product Options")
								$optionsPos = $i;
								
							if($data[$i] == "cat_id")
								$catPos = $i;
								
							if($i > $optionsPos && $optionsPos != 0)
								$optionsNo++;
								
							array_push($fields, $data[$i]);
						}
						
						if($optionsPos == 0){
							$optionsPos = count($data);
						}
					}else{
						
						for($i=0; $i<$optionsPos; $i++){
							if($i == $productMarker){
								if($data[$i] != ""){
									$existingProduct = $db->select("
									SELECT productId, cat_id
									FROM ".$glob['dbprefix']."CubeCart_inventory
									WHERE productId = ".$db->mySQLSafe($data[$i]));
									
									if($existingProduct == TRUE && count($existingProduct) == 1){
										$productNew = 0;
										$productUpdate = $data[$i];
										$catUpdate = $existingProduct[0]["cat_id"];
									}
								}
							}else{
								$record[$fields[$i]] = $db->mySqlSafe(html_entity_decode($data[$i]));
							}
						}
						
						if(isset($record)){ //Anton added this 25.02.2011
							try{
								if($productNew == 1){
									$insert = $db->insert($glob['dbprefix']."CubeCart_inventory", $record);
								}else{
									$db->update($glob['dbprefix']."CubeCart_inventory", $record, "productId = ".$db->mySQLSafe($productUpdate));
									$insert = TRUE;
								}
							}catch(Exception $e){
								$insert = FALSE;
							}
						}
						
						
						if($insert == TRUE){
							if($productNew == 1){
								$insert_id = mysql_insert_id();
								
								$record2["cat_id"] = $data[$catPos];
								$record2["productId"] = $insert_id;
								
								$insert = $db->insert($glob['dbprefix']."CubeCart_cats_idx", $record2);
							}else{
								$insert_id = $productUpdate;
								
								if($data[$catPos] != $catUpdate){
									$record2["cat_id"] = $data[$catPos];
									
									$rob = $db->update($glob['dbprefix']."CubeCart_cats_idx", $record2, 
									"productId = ".$db->mySQLSafe($insert_id)." AND cat_id = ".$db->mySQLSafe($catUpdate));
								}
								
								$insert == TRUE;
							}
							
							if($insert == TRUE){
								if($productNew == 0){
									$db->delete($glob['dbprefix']."CubeCart_options_bot", "product = ".$db->mySQLSafe($insert_id));
								}
								
								if($data[$optionsPos] != ""){
									$options = explode("]", $data[$optionsPos]);
									$optionsBlock = array();
									
									if(count($options) > 1){
										for($i=0; $i<count($options)-1; $i++){
											$temp = explode("[", $options[$i]);
											array_push($optionsBlock, $temp[1]);	
										}
									}
												
									$optionCounter = 0;
												
									for($i=($optionsPos+1); $i<count($data); $i=$i+3){
										$subError = 0;
										
										$optionId = $db->select("SELECT option_id FROM ".$glob['dbprefix']."CubeCart_options_top WHERE option_name = '".$optionsBlock[$optionCounter]."'");
										if($optionId == TRUE){
											$values = explode("]", $data[$i]);
											$prices = explode("]", $data[$i+1]);
											$symbols = explode("]", $data[$i+2]);
											
											if(count($values) > 1){
												for($j=0; $j<count($values)-1; $j++){
													$temp = explode("[", $values[$j]);
														
													$valueId = $db->select("SELECT value_id FROM ".$glob['dbprefix']."CubeCart_options_mid WHERE value_name = '".$temp[1]."' AND father_id = ".$optionId[0]['option_id']);
													if($valueId == TRUE){
														$optionRecord["product"] = $db->mySqlSafe($insert_id);
														$optionRecord["option_id"] = $db->mySqlSafe($optionId[0]['option_id']);
														$optionRecord["value_id"] = $db->mySqlSafe($valueId[0]['value_id']);
														$temp = explode("[", $prices[$j]);
														$optionRecord["option_price"] = $db->mySqlSafe($temp[1]);
														$temp = explode("[", $symbols[$j]);
														$optionRecord["option_symbol"] = $db->mySqlSafe($temp[1]);
														
														$insert = $db->insert($glob['dbprefix']."CubeCart_options_bot", $optionRecord);
														
														if($insert != TRUE)
															$error++;
													}else{
														$msg .= '<p class="warnText">An error has occured in row '.$row.'. Unable to find the value specified.</p>';
														$subError++;
														$db->delete($glob['dbprefix']."CubeCart_inventory", "productId = ".$insert_id);
														$db->delete($glob['dbprefix']."CubeCart_options_bot", "product = ".$insert_id);
														break;
													}
												}
											}
										}else{
											$msg .= '<p class="warnText">An error has occured in row '.$row.'. Unable to find the option specified.</p>';	
											$subError++;
											$db->delete($glob['dbprefix']."CubeCart_inventory", "productId = ".$insert_id);
											$db->delete($glob['dbprefix']."CubeCart_options_bot", "product = ".$insert_id);
											break;
										}
										$optionCounter++;
									}
									if($subError > 0)
										$error++;
								}
							}else{
								$msg .= '<p class="warnText">An unexpected error has occured in row '.$row.'.</p>';	
								$error++;
								$db->delete($glob['dbprefix']."CubeCart_inventory", "productId = ".$insert_id);
							}
						}else{
							$msg .= '<p class="warnText">An error has occured in row '.$row.'. Unable to insert product details.</p>';
							$error++;
						}
					}
					$row++;
				}
				
				$msg = '<p class="infoText"><u>Import Report:</u><br />Rows found: '.($row-1).'<br />Header rows: 1<br />Successful tranfers: '.($row-2-$error).'<br />Failed transfers: '.$error.'</p>'.$msg;
			}else
				$msg = '<p class="warnText">An unexpected error has occured.</p>';
		}else
			$msg = '<p class="warnText">Please upload a CSV file. ('.$_FILES["filename"]["type"].')</p>';
	}else
		$msg = '<p class="warnText">No file found.</p>';
}

?>

<table width="100%"  border="0" cellspacing="0" cellpadding="0">
	<tr>
    	<td nowrap='nowrap'><p class="pageTitle">Import Products</p></td>
  	</tr>
	<tr>
    	<td><p class="tdText">Import products uses a CSV file to insert or update products on the database.<br />
		In order to insert new products, please leave the first column (productId) blank.<br />If you would like to update the existing product, fill in the first column (productId) with the corresponding unique product identifier.<br />
		Please contact your Account Manager for further clarifications.<br /><br />
		<strong>Please bear in mind that this feature will only insert or update products.<br /> It will not delete any products that are stored in the database and are not present in the CSV file.</strong></p><br /></td>
  	</tr>
</table>

<form method="post" action="<?php echo $GLOBALS['rootRel']; ?>admin/csvImport/products.php" enctype="multipart/form-data">
	<input type="file" name="filename" />
	<input type="submit" name="upload" value="Upload File" />
</form>
<br />

<?php

echo $msg ? $msg : "";

include ('../includes/footer.inc.php');

?>