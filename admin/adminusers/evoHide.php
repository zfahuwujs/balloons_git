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
if(evo()==FALSE){
	header("Location: ".$GLOBALS['rootRel']."admin/401.php");
	exit;
}

$results = $db->select("SELECT * FROM ".$glob['dbprefix']."evo_hide ORDER BY groupId, groupMaster DESC, element");

//update options by preset
if(isset($_GET['preset']) && isset($_GET['presetChk'])){
	$preset = $db->select("SELECT * FROM ".$glob['dbprefix']."evo_hide");
	if($preset == TRUE){
		for($i=0; $i<count($results); $i++) {
			//update preset option
			$presetId=$_GET['preset'];
			if($presetId=='show'){
				$record["hide"] = 0;
			}elseif($presetId=='hide'){
				$record["hide"] = 1;
			}else{
				$record["hide"] = $results[$i]['preset'.$presetId];
			}
			$where = "id = ".$results[$i]['id'];
			$update =$db->update($glob['dbprefix']."evo_hide", $record, $where);
		}
		header("Location: ".$GLOBALS['rootRel']."admin/adminusers/evoHide.php");
		exit;
	}
}

//update db on submit
if($results == TRUE && isset($_POST['submit'])){
	for($i=0; $i<count($results); $i++) {
		//update if option changed
		$id=$results[$i]['id'];
		$master = $db->select("SELECT id, hide FROM ".$glob['dbprefix']."evo_hide WHERE groupId =".$results[$i]['groupId']." AND groupMaster = 1");
		if($results[$i]['hide']!=$_POST['element_'.$id] || $master[0]['hide']==1){
			if($master[0]['hide']==1 && $master[0]['id']!=$results[$i]['id']){
				$record["hide"] = 1;
			}else{
				$record["hide"] = $db->mySQLSafe($_POST['element_'.$id]);
			}
			$where = "id = ".$id;
			$update =$db->update($glob['dbprefix']."evo_hide", $record, $where);
		}
	}
	header("Location: ".$GLOBALS['rootRel']."admin/adminusers/evoHide.php");
	exit;
}

include("../includes/header.inc.php");
?>

<p class="pageTitle">Evo Package Manager</p>
<p class="copyText">Set these options to show/hide them from non-evo users.</p>
<form action="<?php echo $GLOBALS['rootRel'];?>admin/adminusers/evoHide.php" target="_self" method="get" language="javascript">
<table align="right" border="0" cellspacing="0" cellpadding="3">
  <tr>
    <td>
    	<select name="filter">
    	<?php
			if($results == TRUE){
				for($i=0; $i<count($results); $i++) {
					if($results[$i]['groupId']!=$lastGrp){
						$lastGrp=$results[$i]['groupId'];
						echo'<option value="'.$results[$i]['groupId'].'"'; if(isset($_GET['filter']) && $results[$i]['groupId']==$_GET['filter']){echo'selected="selected"';} echo'>'.$results[$i]['section'].'</option>';
					}
				}
			}
		?>
        </select>
    </td>
    <td><input type="submit" class="submit" value="Filter" /></td>
    <td><input name="Button" type="button" onclick="MM_goToURL('parent','evoHide.php');return document.MM_returnValue" value="Reset" class="submit" /></td>
  </tr>
</table>
</form>
<?php
	//filter results by section
	if(isset($_GET['filter'])){
		$results = $db->select("SELECT * FROM ".$glob['dbprefix']."evo_hide WHERE groupId = ".$db->mySQLSafe($_GET['filter'])." ORDER BY groupId, groupMaster DESC, element");
	}
?>
<table align="left" border="0" cellspacing="0" cellpadding="3" class="mainTable">
	<tr>
    	<td class="tdTitle" colspan="2" align="center">Presets</td>
    </tr>
	<tr>
    	<td class="tdTitle">CMS</td>
        <td class="tdOdd copyText" >
        <a href="<?php echo $GLOBALS['rootRel'];?>admin/adminusers/evoHide.php?preset=1" class="txtLink">Option 1</a>
         | 
        <a href="<?php echo $GLOBALS['rootRel'];?>admin/adminusers/evoHide.php?preset=2" class="txtLink">Option 2</a>
         | 
        <a href="<?php echo $GLOBALS['rootRel'];?>admin/adminusers/evoHide.php?preset=3" class="txtLink">Option 3</a>
        </td>
    </tr>
    <tr>
    	<td class="tdTitle">E-Com</td>
        <td class="tdEven copyText" >
        <a href="<?php echo $GLOBALS['rootRel'];?>admin/adminusers/evoHide.php?preset=4" class="txtLink">Online</a>
         | 
        <a href="<?php echo $GLOBALS['rootRel'];?>admin/adminusers/evoHide.php?preset=5" class="txtLink">Advanced Online</a>
        </td>
    </tr>
    <tr>
    	<td class="tdTitle">Other</td>
        <td class="tdEven copyText" >
        <a href="<?php echo $GLOBALS['rootRel'];?>admin/adminusers/evoHide.php?preset=show" class="txtLink">Show All</a>
         | 
        <a href="<?php echo $GLOBALS['rootRel'];?>admin/adminusers/evoHide.php?preset=hide" class="txtLink">Hide All</a>
        </td>
    </tr>
    <?php if(isset($_GET['preset'])){ ?>
	<tr>
		<td class="tdTitle">Confirm</td>
		<td class="tdEven copyText">
        This will overwrite your current setting, Continue? 
        <a href="<?php echo $GLOBALS['rootRel'];?>admin/adminusers/evoHide.php?preset=<?php echo $_GET['preset'].'&amp;presetChk=1'; ?>" class="txtLink">Yes</a>
        <a href="<?php echo $GLOBALS['rootRel'];?>admin/adminusers/evoHide.php" class="txtLink">No</a>
        </td>
	</tr>
	<?php }	?>
</table>
<br clear="all" />
<br />
<form action="<?php echo $GLOBALS['rootRel'];?>admin/adminusers/evoHide.php<?php if(isset($_GET['filter'])){echo'?filter='.$_GET['filter'];} ?>" target="_self" method="post" language="javascript">
<table width="100%"  border="0" cellspacing="0" cellpadding="3" class="mainTable">
  <tr>
  	<td class="tdTitle" width="10%">Element ID</td>
    <td align="center" class="tdTitle" width="10%">Show/Hide</td>
    <td class="tdTitle" width="20px"></td>
    <td class="tdTitle" width="30%">Element Name</td>
    <td class="tdTitle" width="10%">Section</td>
    <td class="tdTitle" width="40%">Preset Starting Package</td>
  </tr>

<?php
if($results == TRUE){
	for($i=0; $i<count($results); $i++) {
		$cellColor = "";
		$cellColor = cellColor($i);
?>

  <tr>
  	<td class="<?php echo $cellColor; ?> copyText"><?php echo $results[$i]['id']; ?></td>
    <td align="center" class="<?php echo $cellColor; ?> copyText"><?php
		echo'
		<select name="element_'.$results[$i]['id'].'">
			<option value="0">Show</option>
			<option value="1"'; if($results[$i]['hide']==1){echo'selected="selected"';} echo'>Hide</option>
		</select>
		';
	?></td>
    <td align="center" class="<?php echo $cellColor; ?>"><?php
		if($results[$i]['hide']==1){
        	echo'<img src="../images/0.gif" alt="" title="Hide" />';
		}else{
			echo'<img src="../images/1.gif" alt="" title="Show" />';
		}
		?>
    </td>
  	<td class="<?php echo $cellColor; ?> copyText"><?php if($results[$i]['groupMaster']==1){echo'<strong>Section Master:</strong> ';} echo $results[$i]['element']; ?></td>
  	<td class="<?php echo $cellColor; ?> copyText"><?php echo $results[$i]['section']; ?></td>
    <td class="<?php echo $cellColor; ?> copyText"><?php 
		if($results[$i]['preset1']==0){
			echo 'CMS - Option 1'; 
		}elseif($results[$i]['preset2']==0){
			echo 'CMS - Option 2';
		}elseif($results[$i]['preset3']==0){
			echo 'CMS - Option 3';
		}elseif($results[$i]['preset4']==0){
			echo 'E-Com - Online';
		}elseif($results[$i]['preset5']==0){
			echo 'E-Com - Advanced Online';
		}else{
			echo 'Not Included As Standard';	
		}
	?></td>
  </tr>
  
<?php } 
}
?>
  <tr>
    <td></td>
	<td class="tdRichText" align="center"><input name="submit" type="submit" id="submit" class="submit" value="Update" /></td>
    <td></td>
    <td></td>
  </tr>
</table>
</form>
<p class="copyText"><?php echo $db->paginate($numrows, $rowsPerPage, $page, "page"); ?></p>
<?php include("../includes/footer.inc.php"); ?>