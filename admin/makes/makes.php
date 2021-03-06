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
include("../includes/rte/fckeditor.php");
include("../includes/auth.inc.php");
if(permission("documents","read")==FALSE){
	header("Location: ".$GLOBALS['rootRel']."admin/401.php");
	exit;
}


//delete

if(isset($_GET['delete'])){

	$where = "id = ".$db->mySQLSafe($_GET['delete']);	
	$delete = $db->delete($glob['dbprefix']."CubeCart_make", $where, ""); 
	if($delete == TRUE){
		$msg = "<p class = 'infoText'>Delete successful</p>";
	} else {
		$msg = "<p class =' warnText'>Something went wrong</p>";
	}
	
}


//on form submission
if(isset($_POST['id'])){
	$record["make"] = $db->mySQLSafe($_POST['make']);	
	
	//update
	if($_POST['id'] > 0){
		$record["last_modified"] = $db->mySQLSafe(time());
		$where = "id = ".$db->mySQLSafe($_POST['id']);
		$update =$db->update($glob['dbprefix']."CubeCart_make", $record, $where);
		
		if($update == TRUE){
			$msg = '<p class = "infoText">'.$_POST['make'].' updated successfully</p>';
		} else {
			$msg = '<p class = "warnText">Something went wrong</p>';			
		}
		
	//insert
	} else {
		$record["date_created"] = $db->mySQLSafe(time());		
		$insert = $db->insert($glob['dbprefix']."CubeCart_make", $record);

		if($insert == TRUE){
			$msg = '<p class = "infoText">'.$_POST['make'].' added successfully</p>';			
		} else {
			$msg = '<p class = "warnText">Something went wrong</p>';			
		}

	}
}


#	get info from DB
if (isset($_GET['page'])) {
	$page = treatGet($_GET['page']);
} else {
	$page = 0;
}
$numberPerPage = 30;

if(!isset($_GET['mode'])){
	//edit query
	if(isset($_GET['edit'])){
		$query = "SELECT * FROM ".$glob['dbprefix']."CubeCart_make WHERE id = ".$db->mySQLSafe($_GET['edit']);	
	
	//searching query
	}else if(isset($_GET['searchStr'])){
		$searchQuery = $db->mySQLSafe($_GET['searchStr']);
		$query = 'SELECT * FROM '.$glob['dbprefix'].'CubeCart_make 
									WHERE make like '.$db->mySQLSafe('%'.$_GET['searchStr'].'%');
	//everything query
	} else {
		$query = "SELECT * FROM ".$glob['dbprefix']."CubeCart_make ORDER BY make ASC";	
		
	}
	$results = $db->select($query, $numberPerPage, $page);
	
}
$numrows = $db->numrows($query);
$pagination = $db->paginate($numrows, $numberPerPage, $page, 'page');


/*
echo "<pre>";
var_dump($results);
echo "</pre>";
*/

include("../includes/header.inc.php"); ?>


<table width="100%"  border="0" cellspacing="0" cellpadding="0">
	<tr>
	    <td nowrap='nowrap'><p class="pageTitle">Makes</p></td>
        <td align="right"><a href = "makes.php?mode=new" class = "submit">Add new</a></td>
    </tr>
    <tr>
	    <td nowrap='nowrap' colspan="2"><?php if(isset($msg)){echo $msg;} ?></td>
    </tr>
    
</table>

<div style="display:inline-block; float:right;">
    <form name="form1" method="get" action="/admin/makes/makes.php">
        <label for="searchStr" class = "copyText">Search</label>
        <input type="text" name="searchStr" id = "searchStr" value="<?= $_GET['searchStr']?>" class = "textbox">
        <input type="submit" name="searchBtn" value="Search" class = "submit">
        <a href = "/admin/services/" class = "submit">Reset</a>
    </form>
</div>
<div style="clear:both;"></div>

<script type="text/javascript">
$(document).ready(function(){
	$('#form1').submit(function(){
			if ( $('#make').val() == ""){
				alert('Please enter a make');
				return false;
			}
			
			return true;
	});
});
</script>

<?php

if((isset($_GET['mode'])) || (isset($_GET['edit']))){
	
	
?>
<form name="form1" id="form1" method="post" action="/admin/makes/makes.php">
    <table width="100%"  border="0" cellspacing="0" cellpadding="3" class = "mainTable copyText">
    	<tr>
        	<td class = "tdTitle" colspan="2"><?= $title;?></td>
        </tr>
        <tr>
            <td width="20%"><p class = "copyText">Make</p></td>
            <td><input type="text" name="make" id="make" class = "textbox" value="<?php if(isset($results[0]['make'])){ echo $results[0]['make'];} ?>"></td>
        </tr>
        

        
        <tr>
        	<td>
            	<input type="hidden" value="<?= $results[0]['id'];?>" name="id" >
	            <input type="submit" name="submit" value="<?php if(isset($_GET['edit'])){echo 'Update';}else{echo 'Add';} ?>" class = "submit">
            </td>
        </tr>
        
    </table>
</form>
	
<?php	
} else if($results == TRUE){
?>	
	
    <table width="100%"  border="0" cellspacing="0" cellpadding="3" class = "copyText mainTable">
    <?= '<p class = "copyText">'.$pagination.'</p>';?>
        <tr>
            <td class = "tdTitle">ID</td>
            <td class = "tdTitle">Make</td>
            <td class = "tdTitle" align="right">Actions</td>
        </tr>
        <?php
		
		for ($i = 0; $i < count($results); $i++){
			$cellColor = cellColor($i);
		?>
        <tr>
        	<td class = "<?= $cellColor;?>"><?= $results[$i]['id']; ?></td>
            <td class = "<?= $cellColor;?>"><?= $results[$i]['make']; ?></td>
            <td class = "<?= $cellColor;?>" align="right"><a href = "?edit=<?= $results[$i]['id']; ?>" class = "txtLink">Edit</a> | <a href = "javascript: decision('Are you sure?', '?delete=<?= $results[$i]['id']; ?>')" class = "txtLink">Delete</a> </td>
        </tr>
        
        <?php
		
		}//end for loop
		
		?>
    </table>
	
	
<?php
} else {
?>

	<p class= "copyText">There is nothing in the database. </p>


<?php	
}


?>


<table width="100%"  border="0" cellspacing="0" cellpadding="3">
</table>


















<?php include("../includes/footer.inc.php"); ?>