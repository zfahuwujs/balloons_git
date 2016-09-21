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
include("../../includes/sef_urls.inc.php");
if(permission("csv","read")==FALSE){
	header("Location: ".$GLOBALS['rootRel']."admin/401.php");
	exit;
}

include('../includes/header.inc.php');
?>

<?php

if(isset($_POST["upload"])){
	if(!empty($_FILES["filename"])){
			if($_FILES["filename"]["error"] == 0){
				$msg = "";
				$row = 1;
				$error = 0;
				$handle = fopen($_FILES["filename"]["tmp_name"], "r");
					
				while(($data = fgetcsv($handle, 1000000, ",")) !== FALSE){
					if($data[0] != ""){
						$output = "";
						$rowData = explode(" ", $data[0]);
						
						for($i=0; $i<count($rowData); $i++){
							if($i != (count($rowData)-1)){
								$output .= $rowData[$i]." ";
							}else{
								$getId = explode("&", $rowData[$i]);
								$idType = explode("=", $getId[1]);
								
								if($idType[0] == "productId"){
									$output .= $glob["storeURL"]."/".generateProductUrl($idType[1]);
								}else{
									$output .= $glob["storeURL"]."/".generateCategoryUrl($idType[1]);
								}
								
							}
						}
					}
						
					echo $output."<br />";	
					$row++;
				}
				
				$msg = '<p class="infoText"><u>Import Report:</u><br />Rows found: '.($row-1).'<br />Header rows: 1<br />Successful tranfers: '.($row-2-$error).'<br />Failed transfers: '.$error.'</p>'.$msg;
			}else
				$msg = '<p class="warnText">An unexpected error has occured.</p>';
	}else
		$msg = '<p class="warnText">No file found.</p>';
}

?>

<table width="100%"  border="0" cellspacing="0" cellpadding="0">
	<tr>
    	<td nowrap='nowrap'><p class="pageTitle">Update URLs</p></td>
  	</tr>
</table>

<form method="post" action="<?php echo $GLOBALS['rootRel']; ?>admin/csvImport/urls.php" enctype="multipart/form-data">
	<input type="file" name="filename" />
	<input type="submit" name="upload" value="Upload File" />
</form>
<br />

<?php

echo $msg ? $msg : "";

include ('../includes/footer.inc.php');

?>