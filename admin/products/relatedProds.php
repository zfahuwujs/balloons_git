<?php
/*
+--------------------------------------------------------------------------
|   CubeCart v3.x
|   ========================================
|   by Alistair Brookbanks
|	CubeCart is a Trade Mark of Devellion Limited
|   (c) 2005 Devellion Limited
|   Devellion Limited,
|   Westfield Lodge,
|   Westland Green,
|   Little Hadham,
|   Nr Ware, HERTS.
|   SG11 2AL
|   UNITED KINGDOM
|   http://www.devellion.com
|	UK Private Limited Company No. 5323904
|   ========================================
|   Web: http://www.cubecart.com
|   Date: Thursday, 22 September 2005
|   Email: info (at) cubecart (dot) com
|	License Type: CubeCart is NOT Open Source Software and Limitations Apply 
|   Licence Info: http://www.cubecart.com/site/faq/license.php
+--------------------------------------------------------------------------
|	relatedProds.php
|   ========================================
|	Related Products with Smart Auto Search
|	Created by Estelle <estelle.winterflood@gmail.com>
|	v1.0.0 - October 23, 2005
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

function selected($var,$val)
{
	if ($var==$val)
	{
		echo "selected='selected'";
	}
}


//---- AUTOMATIC INSTALL ----//

$check = $db->select("CHECK TABLE ".$glob['dbprefix']."CubeCart_mod_related_prods QUICK");

$msg = "";
$first_time = false;

if($check[0]['Msg_type']=="error")
{
	$create = "CREATE TABLE `".$glob['dbprefix']."CubeCart_mod_related_prods`
		(
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `productId` int(11) NOT NULL default '1',
		  `relatedId` int(11) NOT NULL default '1',
		  PRIMARY KEY  (`id`)
		) TYPE=MyISAM;";

	$alter = "ALTER TABLE `".$glob['dbprefix']."CubeCart_inventory`
		ADD FULLTEXT fti_name_desc (name, description);";

	$result1 = $db->misc($create);
	$result2 = $db->misc($alter);

	if (!$result1 || !$result2) {
		$msg .= "<p class='warnText'>".$lang['admin']['related_prods']['install_failure']." Please contact estelle.winterflood@gmail.com</p>";
	} else {
		$msg .= "<p class='infoText'>".$lang['admin']['related_prods']['install_success']."</p>";
	}

	$first_time = true;
}

//---- UPDATE CONFIG ----//

$config_rp = fetchDbConfig("Related_Products");
if ($config_rp==FALSE)
{
	// setup default values for initial install
	$config_rp['status']=0;
	$config_rp['max_shown']=4;
	$config_rp['auto']=1;
	$config_rp['use_desc']=1;
	$config_rp['desc_up_to']="";
	$config_rp['ignore']="";
	include("../includes/functions.inc.php");
	writeDbConf($config_rp, "Related_Products", $config_rp);
}
else if(isset($_POST['config_rp']))
{
	include("../includes/functions.inc.php");
	$msg .= writeDbConf($_POST['config_rp'], "Related_Products", $config_rp);
	$config_rp = $_POST['config_rp'];
}


//---- LOOKUP ----//

// Category List
$query = "SELECT cat_id, cat_name, cat_father_id FROM ".$glob['dbprefix']."CubeCart_category ORDER BY cat_father_id DESC, cat_name ASC";

$categoryArray = $db->select($query);

// Product List
$query = "SELECT productId, name, cat_id, productCode, image, description FROM ".$glob['dbprefix']."CubeCart_inventory ORDER BY cat_id, productId ASC";

$productArray = $db->select($query);

// This Product
$found = false;
foreach ($productArray as $prod) {
	if ($prod['productId'] == $_GET['productId']) {
		$found = true;
		break;
	}
}
if (!$found) {
	$msg .= "<p class='warnText'>".$lang['admin']['related_prods']['invalid_product']."</p>";
}


//---- SETUP CURRENT PAGE ----//

if (isset($_GET['prevCat']) && !empty($_GET['prevCat'])) {
	$currentPage = $_SERVER['PHP_SELF']."?productId=".$_GET['productId']."&amp;prevCat=".$_GET['prevCat'];
} else {
	$currentPage = $_SERVER['PHP_SELF']."?productId=".$_GET['productId'];
}


//---- ADD ----//

$add_id = 0;
if (isset($_POST['enter_id']))
{
	// Check that id is valid
	for ($i=0; $i<count($productArray) && !empty($_POST['enter_id']); $i++)
	{
		if ($productArray[$i]['productId'] == $_POST['enter_id']
		    && $productArray[$i]['productId'] != $_GET['productId'])
		{
			$add_id = $productArray[$i]['productId'];
		}
	}
	if ($add_id == 0)
	{
		$msg .= "<p class='warnText'>".$lang['admin']['related_prods']['invalid_enter_id']."</p>";
	}
}
if ($add_id != 0)
{
	// Add record
	$record['productId'] = $db->mySQLSafe($_GET['productId']);  
	$record['relatedId'] = $db->mySQLSafe($add_id);

	$select = $db->select("SELECT relatedId FROM ".$glob['dbprefix']."CubeCart_mod_related_prods WHERE productId=".$record['productId']." AND relatedId=".$record['relatedId']);
	if ($select==FALSE)
	{
		$insert = $db->insert($glob['dbprefix']."CubeCart_mod_related_prods", $record);

		if($insert == TRUE) {
			$msg .= "<p class='infoText'>".$lang['admin']['related_prods']['add_success']."</p>";
		} else {
			$msg .= "<p class='warnText'>".$lang['admin']['related_prods']['add_failure']."</p>";
		}
	}
	else {
		$msg .= "<p class='warnText'>".$lang['admin']['related_prods']['add_exists']."</p>";
	}
}

//---- DELETE ----//

if (isset($_POST['remove_id']) && !empty($_POST['remove_id']))
{
	$where = "relatedId=".$db->mySQLSafe($_POST['remove_id'])." AND productId=".$db->mySQLSafe($_GET["productId"]);
	$delete = $db->delete($glob['dbprefix']."CubeCart_mod_related_prods", $where);
		
	if($delete == TRUE){
		$msg .= "<p class='infoText'>".$lang['admin']['related_prods']['delete_success']."</p>";
	} else {
		$msg = "<p class='warnText'>".$lang['admin']['related_prods']['delete_failure']."</p>";
	}
}


//---- SHOW RELATED PRODUCTS ----//

// Related Product List
$query = "SELECT relatedId, productCode, name, image, cat_id, description FROM ".$glob['dbprefix']."CubeCart_mod_related_prods as rel, ".$glob['dbprefix']."CubeCart_inventory as inv WHERE rel.relatedId=inv.productId AND rel.productId=".$db->mySQLSafe($_GET['productId'])." ORDER BY id";

$relatedArray = $db->select($query);


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" >
<HTML>
	<HEAD>
		<TITLE><?php echo $lang['admin']['related_prods']['manage_related_products'];?></TITLE>
		<LINK rel="stylesheet" type="text/css" href="../styles/style.css">
	</HEAD>
	<BODY onLoad="javascript:document.forms.addForm.enter_id.focus();">
	<p class="pageTitle"><?php echo $lang['admin']['related_prods']['manage_related_products'];?></p>

	<?php if(isset($msg)){ echo $msg; }?>


	<!-- Related Products - Configuration -->


	<form name="configForm" action="<?php echo $currentPage; ?>" method="post" enctype="multipart/form-data">
	<table border="0" width="100%" cellspacing="0" cellpadding="3" class="mainTable">
	<tr>
	  <td colspan="2" class="tdTitle">
	    <?php echo $lang['admin']['related_prods']['config'];?>
	  </td>
	</tr>
<?php
if ($_GET['edit']!="config" && !$first_time)
{
		//---- CONFIG SETTINGS - Hidden ----//
?>
	<tr valign="middle">
	  <td colspan="2" class="tdText">
	    <a href="<?php echo $currentPage; ?>&edit=config">
	      <?php echo $lang['admin']['related_prods']['show_config']; ?>
            </a>
	  </td>
	</tr>
<?php
} else {
		//---- CONFIG SETTINGS - Edit ----//
?>
	<tr valign="top">
	  <td colspan="2" class="tdText">
	    <em><?php echo $lang['admin']['related_prods']['config_not_unique']; ?></em>
	  </td>
	</tr>
	<tr valign="top">
	  <td class="tdText">
	    <?php echo $lang['admin']['related_prods']['status']; ?>
	  </td>
	  <td class="tdText">
	    <select name="config_rp[status]">
	      <option value="1" <?php selected($config_rp['status'],1); ?> >
	        <?php echo $lang['admin']['settings']['enable']; ?>
	      </option>
	      <option value="0" <?php selected($config_rp['status'],0); ?> >
	        <?php echo $lang['admin']['settings']['disable']; ?>
	      </option>
	    </select>
	  </td>
	</tr>
	<?php /*?><tr valign="top">
	  <td class="tdText">
	    <?php echo $lang['admin']['related_prods']['max_shown']; ?>
	  </td>
	  <td class="tdText">
	    <input type="text" name="config_rp[max_shown]" size="5" value="<?php echo $config_rp['max_shown']; ?>" >
	    <div id="moreInfo1" style="border: solid 1px #999; padding: 5px 10px; margin: 5px 10px 5px 0; display:none">
	      <?php echo $lang['admin']['related_prods']['max_shown_help']; ?>
	    </div>
	  </td>
	</tr>
	<tr valign="top">
	  <td class="tdText">
	    <?php echo $lang['admin']['related_prods']['auto_search']; ?>
	  </td>
	  <td class="tdText">
	    <select name="config_rp[auto]">
	      <option value="1" <?php selected($config_rp['auto'],1); ?> >
		<?php echo $lang['admin']['yes']; ?>
	      </option>
	      <option value="0" <?php selected($config_rp['auto'],0); ?> >
		<?php echo $lang['admin']['no']; ?>
	      </option>
	    </select>
	    <div id="moreInfo2" style="border: solid 1px #999; padding: 5px 10px; margin: 5px 10px 5px 0; display:none">
	      <?php echo $lang['admin']['related_prods']['auto_search_help']; ?>
	    </div>
	  </td>
	</tr>
	<tr valign="top">
	  <td class="tdText">
	    <?php echo $lang['admin']['related_prods']['desc']; ?>
	  </td>
	  <td class="tdText">
	    <select name="config_rp[use_desc]">
	      <option value="1" <?php selected($config_rp['use_desc'],1); ?> >
		<?php echo $lang['admin']['yes']; ?>
	      </option>
	      <option value="0" <?php selected($config_rp['use_desc'],0); ?> >
		<?php echo $lang['admin']['no']; ?>
	      </option>
	    </select>
	    <div id="moreInfo3" style="border: solid 1px #999; padding: 5px 10px; margin: 5px 10px 5px 0; display:none">
	      <?php echo $lang['admin']['related_prods']['desc_help']; ?>
	    </div>
	  </td>
	</tr>
	<tr valign="middle">
	  <td colspan="2" class="tdText">
	    <a href="#" onClick="javascript:
	      if (document.getElementById('moreInfo1').style.display=='none') {
	        document.getElementById('moreInfo1').style.display='block';
	        document.getElementById('moreInfo2').style.display='block';
	        document.getElementById('moreInfo3').style.display='block';
	      }
	      else {
	        document.getElementById('moreInfo1').style.display='none';
	        document.getElementById('moreInfo2').style.display='none';
	        document.getElementById('moreInfo3').style.display='none';
	      }
	      return false;">
	      <?php echo $lang['admin']['related_prods']['show_help']; ?>
            </a>
	  </td>
	</tr>
	<tr valign="top">
	  <td colspan="2" class="tdTitle">
	    <?php echo $lang['admin']['related_prods']['advanced_options']; ?>
	  </td>
	</tr>
	<tr valign="top">
	  <td class="tdText">
	    <?php echo $lang['admin']['related_prods']['desc_up_to']; ?>
	  </td>
	  <td class="tdText">
	    <input type="text" name="config_rp[desc_up_to]" value="<?php echo $config_rp['desc_up_to']; ?>" >
	    <?php echo $lang['admin']['related_prods']['comma_separated']; ?>
	    <div id="moreInfo4" style="border: solid 1px #999; padding: 5px 10px; margin: 5px 10px 5px 0; display:none">
	      <?php echo $lang['admin']['related_prods']['desc_up_to_help']; ?>
	    </div>
	  </td>
	</tr>
	<tr valign="top">
	  <td class="tdText">
	    <?php echo $lang['admin']['related_prods']['ignore']; ?><br/>
	    <?php echo $lang['admin']['related_prods']['comma_separated']; ?>
	  </td>
	  <td class="tdText">
	    <textarea name="config_rp[ignore]" rows="3" style="width:300px;"><?php echo $config_rp['ignore']; ?></textarea>
	    <div id="moreInfo5" style="border: solid 1px #999; padding: 5px 10px; margin: 5px 10px 5px 0; display:none">
	      <?php echo $lang['admin']['related_prods']['ignore_help']; ?>
	    </div>
	  </td>
	</tr>
	<tr valign="middle">
	  <td class="tdText">
	    <a href="#" onClick="javascript:
	      if (document.getElementById('moreInfo4').style.display=='none') {
	        document.getElementById('moreInfo4').style.display='block';
	        document.getElementById('moreInfo5').style.display='block';
	      } else {
	        document.getElementById('moreInfo4').style.display='none';
	        document.getElementById('moreInfo5').style.display='none';
	      }
	      return false;">
	      <?php echo $lang['admin']['related_prods']['show_help']; ?>
            </a>
	  </td><?php */?>
     <tr>
	  <td colspan="2" align="center" class="tdText">
	    <input type="submit" value="<?php echo $lang['admin']['related_prods']['update']; ?>">
	  </td>
	</tr>
	<tr valign="middle">
	  <td colspan="2" class="tdText">
	    <a href="<?php echo $currentPage; ?>">
	      <?php echo $lang['admin']['related_prods']['hide_config']; ?>
            </a>
	  </td>
	</tr>
<?php
} // end edit config
?>
	</table>
	</form>
	<br/>


	<!-- Related Products - Show Selected Product -->


	<?php /*?><table border="0" width="100%" cellspacing="0" cellpadding="3" class="mainTable">
        <tr>
          <td colspan="2" class="tdTitle">
	    <?php echo $lang['admin']['related_prods']['selected_prod']; ?>
          </td>
        </tr>
        <tr>
	  <td class="tdText">
<?php
	if(file_exists($GLOBALS['rootDir']."/images/uploads/thumbs/thumb_".$prod['image'])) {
		$src = $GLOBALS['rootRel']."images/uploads/thumbs/thumb_".$prod['image'];
	} else {
		$src = $GLOBALS['rootRel']."skins/".$config['skinDir']."/styleImages/thumb_nophoto.gif";
	}
?>
	    <img src="<?php echo $src; ?>" />
          </td>
	  <td class="tdText">
	    <span class="copyText">
	    <strong>
	      <?php echo $lang['admin']['related_prods']['auto_search_words']; ?>
	    </strong><br/>
<?php
	$name = strtolower(strip_tags($prod['name']));

	// cut words to be ignored
	$ignore = explode(",",strtolower($config_rp['ignore']));
	foreach($ignore as $term) {
		$ignore_terms[] = trim($term);
	}
	if (is_array($ignore_terms)) {
		$name = str_replace($ignore_terms, "", $name);
	}

	// cut junk characters, and cut all words shorter than 4 chars
	$words = preg_split("/(\W)+/",$name);
	$name = "";
	foreach ($words as $word) {
		if (strlen($word) >= 4) {
			$name .= $word." ";
		}
	}

	$desc = "";
	if ($config_rp['use_desc'])
	{
		$desc = strtolower($prod['description']);

		// search for "description terminators"
		$delimArray = explode(",",strtolower($config_rp['desc_up_to']));
		if ($delimArray[0]!="")
		{
			foreach ($delimArray as $delim)
			{
				$pos = strpos($desc, trim($delim));
				if ($pos > 0) {
					$delim_pos[] = $pos;
				}
			}
			// cut at first "terminator"
			if (isset($delim_pos))
			{
				if (count($delim_pos)>1) {
					$end = min($delim_pos);
				} else {
					$end = $delim_pos[0];
				}
				$desc = substr($desc,0,$end);
			}
		}

		$desc = strip_tags($desc);

		// cut words to be ignored
		if (is_array($ignore_terms)) {
			$desc = str_replace($ignore_terms, "", $desc);
		}

		// cut junk characters, and cut all words shorter than 4 chars
		$words = preg_split("/(\W)+/",$desc);
		$desc = "";
		foreach ($words as $word) {
			if (strlen($word) >= 4) {
				$desc .= $word." ";
			}
		}
	}

	echo "<strong>".$lang['admin']['related_prods']['from_name'];
	echo "</strong> ".$name."<br/>";
	if ($config_rp['use_desc']) {
		echo "<strong>".$lang['admin']['related_prods']['from_desc'];
		echo "</strong> ".$desc."<br/>";
	}
?>
	    </span>
          </td>
        </tr>
	<tr valign="middle">
	  <td colspan="2" class="tdText">
	    <div id="helpSelected" class="copyText" style="border: solid 1px #999; padding: 5px 10px; margin: 5px 10px 5px 0; display:none">
	      <?php echo $lang['admin']['related_prods']['help_selected']; ?>
	    </div>
	    <span class="copyText"><a href="#" onClick="javascript:
	      if (document.getElementById('helpSelected').style.display=='none') {
	        document.getElementById('helpSelected').style.display='block';
	      } else {
	        document.getElementById('helpSelected').style.display='none';
	      }
	      return false;">
	      <?php echo $lang['admin']['related_prods']['show_help']; ?>
            </a></span>
	  </td>
	</tr>
	</table><?php */?>
	<br/>


	<!-- Related Products - Add -->

	
	<table border="0" width="100%" cellspacing="0" cellpadding="3" class="mainTable">
        <tr>
          <td colspan="3" class="tdTitle">
	    <?php echo $lang['admin']['related_prods']['add']; ?>
          </td>
        </tr>
        <tr>
          <td class="tdText">
	    <?php echo $lang['admin']['related_prods']['enter_id']; ?>
          </td>
          <td class="tdText">
	    <form name="addForm" action="<?php echo $currentPage; ?>" method="post" enctype="multipart/form-data">
	    <input type="text" name="enter_id" id="enter_id" value="" />
          </td>
          <td class="tdText">
	    <input type="submit" value="<?php echo $lang['admin']['add']; ?>" />
	    </form>
          </td>
        </tr>
	<tr valign="middle">
	  <td colspan="3" class="tdText">
	    <div id="helpAdd" style="border: solid 1px #999; padding: 5px 10px; margin: 5px 10px 5px 0; display:none">
	      <?php echo $lang['admin']['related_prods']['help_add']; ?>
	    </div>
	    <a href="#" onClick="javascript:
	      if (document.getElementById('helpAdd').style.display=='none') {
	        document.getElementById('helpAdd').style.display='block';
	      } else {
	        document.getElementById('helpAdd').style.display='none';
	      }
	      return false;">
	      <?php echo $lang['admin']['related_prods']['show_help']; ?>
            </a>
	  </td>
	</tr>
	</table>
	<br/>


	<!-- Related Products - Remove -->

	
<?php
// if related products exist
if (is_array($relatedArray) && count($relatedArray)>0) {
?>
	<table border="0" width="100%" cellspacing="0" cellpadding="3" class="mainTable">
        <tr>
          <td colspan="3" class="tdTitle">
	    <?php echo $lang['admin']['related_prods']['view_or_remove']; ?>
          </td>
        </tr>
<?php
	// loop through related products
	for ($i=0; is_array($relatedArray) && $i<count($relatedArray); $i++)
	{
		$cellColor = "";
		$cellColor = cellColor($i);
?>
        <tr>
	  <td class="<?php echo $cellColor; ?>">
<?php
	if(file_exists($GLOBALS['rootDir']."/images/uploads/thumbs/thumb_".$relatedArray[$i]['image'])) {
		$src = $GLOBALS['rootRel']."images/uploads/thumbs/thumb_".$relatedArray[$i]['image'];
	} else {
		$src = $GLOBALS['rootRel']."skins/".$config['skinDir']."/styleImages/thumb_nophoto.gif";
	}
?>
	    <img src="<?php echo $src; ?>" />
          </td>
	  <td class="<?php echo $cellColor; ?>">
	    <form name="addForm" action="<?php echo $currentPage; ?>" method="post" enctype="multipart/form-data">
	    <input type="hidden" name="remove_id" value="<?php echo $relatedArray[$i]['relatedId']; ?>">
	    <span class="copyText">
	    <strong>
	      <?php echo $lang['admin']['related_prods']['name']; ?>
	    </strong>
	      <?php echo $relatedArray[$i]['name']; ?>
	    <br/><strong>
	      <?php echo $lang['admin']['related_prods']['description']; ?>
	    </strong>
<?php
	$desc = $relatedArray[$i]['description'];
	// strip tags
	$desc = strip_tags($desc);
	// if excessively long cut shorter for nice display
	if (strlen($desc) > 150) {
		$desc = substr($desc,0,150)."&hellip;";
	}
	echo $desc;
?>
	    </span>
          </td>
	  <td class="<?php echo $cellColor; ?>">
	    <input type="submit" value="<?php echo $lang['admin']['remove']; ?>">
	    </form>
          </td>
        </tr>
<?php
	} // end loop through related products

	$cellColor = cellColor($i);
?>
	</table>
<?php
} // end if related products exist
?>

	<p align="center"><a href="javascript:window.close();" class="txtLink"><?php echo $lang['admin']['related_prods']['close_window'];?></a></p>

</BODY>
</HTML>
