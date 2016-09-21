<?php

/*

+--------------------------------------------------------------------------

|   CubeCart v3.0.15

|   ========================================

|   by Alistair Brookbanks

|	CubeCart is a Trade Mark of Devellion Limited

|   Copyright Devellion Limited 2005 - 2006. All rights reserved.

|   Devellion Limited,

|   22 Thomas Heskin Court,

|   Station Road,

|   Bishops Stortford,

|   HERTFORDSHIRE.

|   CM23 3EE

|   UNITED KINGDOM

|   http://www.devellion.com

|	UK Private Limited Company No. 5323904

|   ========================================

|   Web: http://www.cubecart.com

|   Date: Thursday, 4th January 2007

|   Email: sales (at) cubecart (dot) com

|	License Type: CubeCart is NOT Open Source Software and Limitations Apply 

|   Licence Info: http://www.cubecart.com/site/faq/license.php

+--------------------------------------------------------------------------

|	customers.php

|   ========================================

|	Manage Customers Accounts

+--------------------------------------------------------------------------

*/

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



if(permission("customers","read")==FALSE){

	header("Location: ".$GLOBALS['rootRel']."admin/401.php");

	exit;

}



include("../includes/header.inc.php");



$rowsPerPage = 25;



if(isset($_GET["delete"]) && $_GET["delete"]>0){

// instantiate db class

$where = "customer_id=".$db->mySQLSafe($_GET["delete"]);

$delete = $db->delete($glob['dbprefix']."CubeCart_customer", $where);

		

		if($delete == TRUE){

			$msg = "<p class='infoText'>".$lang['admin']['customers']['delete_success']."</p>";

		} else {

			$msg = "<p class='warnText'>".$lang['admin']['customers']['delete_success']."</p>";

		}



} elseif(isset($_POST['customer_id'])) {

// instantiate db class



	$record["title"] = $db->mySQLSafe($_POST['title']);		

	$record["firstName"] = $db->mySQLSafe($_POST['firstName']);	

	$record["lastName"] = $db->mySQLSafe($_POST['lastName']);
	
	if($config['usernameType']==1){$record["nickname"] = $db->mySQLSafe($_POST['nickname']);}

	$record["email"] = $db->mySQLSafe($_POST['email']);  

	$record["add_1"] = $db->mySQLSafe($_POST['add_1']); 

	$record["add_2"] = $db->mySQLSafe($_POST['add_2']); 

	$record["town"] = $db->mySQLSafe($_POST['town']);

	$record["postcode"] = $db->mySQLSafe($_POST['postcode']);

	$record["county"] = $db->mySQLSafe($_POST['county']);

	$record["country"] = $db->mySQLSafe($_POST['country']);

	$record["phone"] = $db->mySQLSafe($_POST['phone']);
	
	$record["company"] = $db->mySQLSafe($_POST['company']);

	$record["trade"] = $db->mySQLSafe($_POST['trade']);

	$zoneId = $db->select("SELECT * FROM ".$glob['dbprefix']."CubeCart_iso_counties WHERE (abbrev LIKE ".$db->mySQLSafe($_POST['county'])." OR name LIKE ".$db->mySQLSafe($_POST['county']).")");

		

	if($zoneId[0]['id']>0){



		$record["zoneId"] = $zoneId[0]['id'];



	} else {

	

		$record["zoneId"] = 0;

		

	}

	

	$where = "customer_id=".$db->mySQLSafe($_POST['customer_id']);

	$update = $db->update($glob['dbprefix']."CubeCart_customer", $record, $where);

	

	if($update == TRUE){

			$msg = "<p class='infoText'>".$lang['admin']['customers']['update_success']."</p>";

	} else {

			$msg = "<p class='warnText'>".$lang['admin']['customers']['update_fail']."</p>";

	}



}



	if(isset($_GET['edit']) && $_GET['edit']>0){

		

		$query = sprintf("SELECT * FROM ".$glob['dbprefix']."CubeCart_customer WHERE customer_id = %s AND type = 1", $db->mySQLSafe($_GET['edit'])); 

	

	} elseif(isset($_GET['searchStr'])) {

		

		

	$searchwords = split ( "[ ,]", $_GET['searchStr']);   

	foreach($searchwords as $word){

		$searchArray[]=$word;

	}



	$noKeys = count($searchArray);

	

	$like = "";

	

	for ($i=0; $i<$noKeys;$i++) {

		

		$ucSearchTerm = strtoupper($searchArray[$i]);

		if(($ucSearchTerm!=="AND")AND($ucSearchTerm!=="OR")){

			

			$like .= "(email LIKE '%".$searchArray[$i]."%' OR title LIKE '%".$searchArray[$i]."%' OR nickname LIKE '%".$searchArray[$i]."%' OR  firstName LIKE '%".$searchArray[$i]."%' OR lastName LIKE '%".$searchArray[$i]."%' OR add_1 LIKE '%".$searchArray[$i]."%' OR  add_2 LIKE '%".$searchArray[$i]."%' OR town LIKE '%".$searchArray[$i]."%' OR county LIKE '%".$searchArray[$i]."%' OR  postcode LIKE '%".$searchArray[$i]."%' OR country LIKE '%".$searchArray[$i]."%' OR phone LIKE '%".$searchArray[$i]."%' OR  ipAddress LIKE '%".$searchArray[$i]."%') OR ";

			

		} else {

			$like = substr($like,0,strlen($like)-3);

			$like .= $ucSearchTerm;

		}  



	}

	$like = substr($like,0,strlen($like)-3);

	if(isset($_GET['cusSet'])){
		if($_GET['cusSet']==0){
			$cusSet='type = 1 AND';
		}elseif($_GET['cusSet']==1){
			$cusSet='type = 0 AND optIn1st = 1 AND';
		}elseif($_GET['cusSet']==2){
			$cusSet='';
		}elseif($_GET['cusSet']==3){
			$cusSet=' optIn1st = 1 AND';
		}elseif($_GET['cusSet']==4){
			$cusSet=' optIn1st = 0 AND';
		}elseif($_GET['cusSet']==5){
			$cusSet=' trade = 1 AND type = 1 AND';
		}elseif($_GET['cusSet']==6){
			$cusSet=' trade = 0 AND type = 1 AND';
		}
	}else{
		$cusSet='type = 1 AND ';
	}

	$query = "SELECT * FROM ".$glob['dbprefix']."CubeCart_customer WHERE ".$cusSet.$like;

	

	} else {

		

		$query = "SELECT * FROM ".$glob['dbprefix']."CubeCart_customer WHERE type = 1 ORDER BY regTime DESC";

	

	}

	

	if(isset($_GET['page'])){

		$page = $_GET['page'];

	} else {

		$page = 0;

	}

	

	// query database

	$results = $db->select($query, $rowsPerPage, $page);

	$numrows = $db->numrows($query);

	$pagination = $db->paginate($numrows, $rowsPerPage, $page, "page");

?>

<p class="pageTitle">Customer Details</p>

<?php if(evo()==true){echo '<p class="copyText" style="font-weight:bold;">Evo Staff Message: please note that the evo test accounts (test@test.com &amp; trade@test.com) are not visible or accessable to clients</p>';} ?>

<?php if(isset($msg)){ echo stripslashes($msg); }?>

<?php

if(!isset($_GET['mode']) && !isset($_GET['edit'])){

?>



<form name="filter" method="get" action="<?php echo $GLOBALS['rootRel'];?>admin/customers/index.php">

 	<p align="right" class="copyText">

	<?php echo $lang['admin']['customers']['search_term']; ?>

    <input type="text" name="searchStr" class="textbox" value="<?php if(isset($_GET['searchStr'])) echo $_GET['searchStr']; ?>" />    
    
    <?php if(evoHideBol(10)==false || evoHideBol(33)==false){ ?>
    <select name="cusSet">
    	<option value="0">Registerd Users</option>
        <?php
		if(evoHideBol(10)==false){
			echo'
				<option value="1" '; if(isset($_GET['cusSet']) && $_GET['cusSet']==1){echo 'selected="selected"';} echo'>Newletter Sign-Ups</option>
				<option value="2" '; if(isset($_GET['cusSet']) && $_GET['cusSet']==2){echo 'selected="selected"';} echo'>All Users</option>
        		<option value="3" '; if(isset($_GET['cusSet']) && $_GET['cusSet']==3){echo 'selected="selected"';} echo'>All Users On Mailing List</option>
       			<option value="4" '; if(isset($_GET['cusSet']) && $_GET['cusSet']==4){echo 'selected="selected"';} echo'>All Users Not On Mailing List</option>
        	';	
		}
		if(evoHideBol(33)==false){
			echo'
				<option value="5" '; if(isset($_GET['cusSet']) && $_GET['cusSet']==5){echo 'selected="selected"';} echo'>Trade Users</option>
        		<option value="6" '; if(isset($_GET['cusSet']) && $_GET['cusSet']==6){echo 'selected="selected"';} echo'>Non-Trade Users</option>
			';	
		}
		?>
    </select>
    <?php } ?>

    <input type="submit" name="Submit" class="submit" value="Filter" />

    <input name="Button" type="button" onclick="MM_goToURL('parent','index.php');return document.MM_returnValue" value="<?php echo $lang['admin']['customers']['reset']; ?>" class="submit" />

	</p>

</form>





<p class="copyText"><?php echo $pagination; ?></p>


<table width="100%" border="0" cellspacing="0" cellpadding="3" class="mainTable">

  <tr align="center">

    <td align="left" class="tdTitle"><?php echo $lang['admin']['customers']['name']; ?></td>
    
    <?php if($config['usernameType']==1){echo '<td align="left" class="tdTitle">Username</td>';} ?>

    <td align="left" class="tdTitle"><?php echo $lang['admin']['customers']['email']; ?></td>

    <td align="left" class="tdTitle"><?php echo $lang['admin']['customers']['invoice_add']; ?></td>

    <td align="left" class="tdTitle"><?php echo $lang['admin']['customers']['phone']; ?></td>

    <td align="left" class="tdTitle"><?php echo $lang['admin']['customers']['reg_ip']; ?></td>
    
    <td align="center" class="tdTitle <?php evoHideAlt(33); ?>">Customer Type</td>
    
    <td align="center" class="tdTitle <?php evoHideAlt(10); ?>">Newsletter</td>

    <td class="tdTitle <?php evoHideAlt(11); ?>"><?php echo $lang['admin']['customers']['no_orders']; ?></td>

    <td class="tdTitle" align="center">Edit</td>
    <td class="tdTitle" align="center">Delete</td>

  </tr>

<?php 

if($results == TRUE){ 

	

	for ($i=0; $i<count($results); $i++){ 
		if(($results[$i]['customer_id']==3 || $results[$i]['customer_id']==4) && evo()==false){
			//do not display admin test accounts unless accessed by evo staff
		}else{

	

	$cellColor = cellColor($i);

?>

  <tr>

    <td class="<?php echo $cellColor; ?>"><span class="tdText"><?php echo $results[$i]['title']; ?> <?php echo $results[$i]['firstName']; ?> <?php echo $results[$i]['lastName']; ?></span></td>
    
    <?php if($config['usernameType']==1){echo '<td class="'.$cellColor.' tdText">'.$results[$i]['nickname'].'</td>';} ?>

    <td class="<?php echo $cellColor; ?>"><a href="mailto:<?php echo $results[$i]['email']; ?>" class="txtLink"><?php echo $results[$i]['email']; ?></a></td>

    <td class="<?php echo $cellColor; ?>">

	<span class="tdText"><?php 

	if(!empty($results[$i]['add_1'])) echo $results[$i]['add_1'].", "; 

	if(!empty($results[$i]['add_2'])) echo $results[$i]['add_2'].", "; 

	if(!empty($results[$i]['town'])) echo $results[$i]['town'].", ";

	if(!empty($results[$i]['county'])) echo $results[$i]['county'].", ";

	if(!empty($results[$i]['postcode'])) echo $results[$i]['postcode'].", "; 	

	if(!empty($results[$i]['country'])) echo countryName($results[$i]['country']);

	?>

	</span></td>

    <td class="<?php echo $cellColor; ?>"><span class="tdText"><?php echo $results[$i]['phone']; ?></span></td>

    <td nowrap='nowrap' class="<?php echo $cellColor; ?>"><span class="tdText">

		<?php echo formatTime($results[$i]['regTime']); ?><br />

		<a href="javascript:;" class="txtLink" onclick="openPopUp('../misc/lookupip.php?ip=<?php echo $results[$i]['ipAddress']; ?>','misc',300,120)"><?php echo $results[$i]['ipAddress']; ?></a></span>

	</td>
    
    <td align="center" class="<?php echo $cellColor; ?> <?php evoHideAlt(33); ?>">
    
    	<?php if($results[$i]['trade'] > 0){?>

			<span class="tdText">Trade</span>
        
        <?php } else { ?>
        	
            <span class="tdText">Normal</span>
        
        <?php } ?>

	</td>
    
    <td align="center" class="<?php echo $cellColor; ?> <?php evoHideAlt(10); ?> tdText">
	<?php 
	if($results[$i]['optIn1st']==1) { 
		echo 'Yes';
	}else{
		echo 'No';
	}
	?>
	</td>

    <td align="center" class="<?php echo $cellColor; ?> <?php evoHideAlt(11); ?>">

	<?php if($results[$i]['noOrders']>0) { ?>

	<a href="../orders/index.php?customer_id=<?php echo $results[$i]['customer_id']; ?>" class="txtLink"><?php echo $results[$i]['noOrders']; ?></a>

	<?php } else { ?>

	<span class="tdText"><?php echo $results[$i]['noOrders']; ?></span>

	<?php } ?>

	</td>

    <td class="<?php echo $cellColor; ?>"  align="center"><a <?php if(permission("customers","edit")==TRUE){?>href="?edit=<?php echo $results[$i]['customer_id']; ?>" class="txtLink"<?php } else { echo $link401; } ?>><img src="/admin/images/edit.gif" alt="Edit" title="Edit" width="16" border="0" /></a></td>

    <td align="center" class="<?php echo $cellColor; ?>"><a <?php if(permission("customers","delete")==TRUE){?>href="javascript:decision('<?php echo $lang['admin']['delete_q']; ?>','?delete=<?php echo $results[$i]['customer_id']; ?>');" class="txtLink"<?php } else { echo $link401; } ?>><img src="/admin/images/del.gif" alt="Delete" title="Delete" border="0" /></a></td>

  </tr>

<?php 
		}
  		} // end loop  

	} else { ?>

   <tr>

    <td colspan="6" class="tdText"><?php echo $lang['admin']['customers']['no_cust_exist']; ?></td>

  </tr>

<?php

  } 

?>

</table>

<p class="copyText"><?php echo $pagination; ?></p>

<?php } elseif($_GET["mode"]=="new" OR $_GET["edit"]>0){ ?>

<form name="editCustomer" method="post" action="<?php echo $GLOBALS['rootRel'];?>admin/customers/index.php">

<table  border="0" cellspacing="0" cellpadding="3" class="mainTable">

  <tr>

    <td colspan="2" class="tdTitle"><?php echo $lang['admin']['customers']['edit_below']; ?></td>

    </tr>

  <tr>

    <td width="175" class="tdText"><?php echo $lang['admin']['customers']['title']; ?></td>

    <td width="175">

      <input name="title" type="text" id="title" value="<?php echo $results[0]['title']; ?>" class="textbox" />

    </td>

  </tr>

  <tr>

    <td width="175" class="tdText"><?php echo $lang['admin']['customers']['first_name']; ?></td>

    <td width="175"><input name="firstName" type="text" id="firstName" value="<?php echo $results[0]['firstName']; ?>" class="textbox" /></td>

  </tr>

  <tr>

    <td width="175" class="tdText"><?php echo $lang['admin']['customers']['last_name']; ?></td>

    <td width="175"><input name="lastName" type="text" id="lastName" value="<?php echo $results[0]['lastName']; ?>" class="textbox" /></td>

  </tr>
  
  <?php if($config['usernameType']==1){?>
  
  <tr>

    <td width="175" class="tdText">Username</td>

    <td width="175"><input name="nickname" type="text" id="nickname" value="<?php echo $results[0]['nickname']; ?>" class="textbox" /></td>

  </tr>
  
  <?php } ?>

  <tr>

    <td width="175" class="tdText"><?php echo $lang['admin']['customers']['email2']; ?></td>

    <td width="175"><input name="email" type="text" id="email" value="<?php echo $results[0]['email']; ?>" class="textbox" /></td>

  </tr>

  <tr>

    <td width="175" class="tdText"><?php echo $lang['admin']['customers']['address']; ?></td>

    <td width="175"><input name="add_1" type="text" id="add_1" value="<?php echo $results[0]['add_1']; ?>" class="textbox" /></td>

  </tr>

  <tr>

    <td width="175">&nbsp;</td>

    <td width="175"><input name="add_2" type="text" id="add_2" value="<?php echo $results[0]['add_2']; ?>" class="textbox" /></td>

  </tr>

  <tr>

    <td width="175" class="tdText"><?php echo $lang['admin']['customers']['town']; ?></td>

    <td width="175"><input name="town" type="text" id="town" value="<?php echo $results[0]['town']; ?>" class="textbox" /></td>

  </tr>

  <tr>

    <td width="175" class="tdText"><?php echo $lang['admin']['customers']['county']; ?></td>

    <td width="175"><input name="county" type="text" id="county" value="<?php echo $results[0]['county']; ?>" class="textbox" /></td>

  </tr>

  <tr>

    <td width="175" class="tdText"><?php echo $lang['admin']['customers']['postcode']; ?></td>

    <td width="175"><input name="postcode" type="text" id="postcode" value="<?php echo $results[0]['postcode']; ?>" class="textbox" /></td>

  </tr>

  <tr>

    <td width="175" class="tdText"><?php echo $lang['admin']['customers']['country']; ?></td>

    <td width="175">

	<?php 

	  $countries = $db->select("SELECT * FROM ".$glob['dbprefix']."CubeCart_iso_countries"); 

	  ?>

	

	<select name="country">

	<?php

	for($i=0; $i<count($countries); $i++){

	?>

	<option value="<?php echo $countries[$i]['id']; ?>" <?php if($countries[$i]['id'] == $results[0]['country']) echo "selected='selected'"; ?>><?php echo $countries[$i]['printable_name']; ?></option>

	<?php } ?>

	</select>

	</td>

  </tr>

  <tr>

    <td width="175" class="tdText"><?php echo $lang['admin']['customers']['phone2']; ?></td>

    <td width="175"><input name="phone" type="text" id="phone" value="<?php echo $results[0]['phone']; ?>" class="textbox" /></td>

  </tr>
  
  <tr>

    <td width="175" class="tdText"><?php echo $lang['admin']['customers']['company']; ?></td>

    <td width="175"><input name="company" type="text" id="company" value="<?php echo $results[0]['company']; ?>" class="textbox" /></td>

  </tr>
  
  <tr <?php evoHide(33);?>>
	<?php 
	$tradeAccounts = $db->select("SELECT * FROM ".$glob['dbprefix']."CubeCart_trade_accounts"); 
	?>
    <td width="175" class="tdText">Customer Type:</td>

    <td width="175">
    
	<select name="trade">
        <option value="0" <?php if($results[0]['trade'] == 0) echo "selected='selected'"; ?>>Normal</option>
        <?php 
				if($tradeAccounts == TRUE){
					foreach($tradeAccounts as $account){
						$out = '<option value="'.$account['tradeAccId'].'"';
						if($results[0]['trade']==$account['tradeAccId']){
							$out .= ' selected ';
						}
						$out .='>'.$account['name'].'('.$account['discount'].'%)</option>';
						echo $out;
					}
				}
				?>
	</select>
	</td>

  </tr>

  <tr>

    <td width="175">&nbsp;</td>

    <td width="175">

	<input type="hidden" name="customer_id" value="<?php echo $results[0]['customer_id']; ?>" />

	<input type="submit" name="Submit" class="submit" value="<?php echo $lang['admin']['customers']['edit_customer']; ?>" />

	</td>

  </tr>

</table>

</form>

<?php } ?>



<?php include("../includes/footer.inc.php"); ?>