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
|	settings.php
|   ========================================
|	Manage Main Store Settings	
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
include("../includes/functions.inc.php");

if(permission("settings","read")==FALSE){
	header("Location: ".$GLOBALS['rootRel']."admin/401.php");
	exit;
}

if(isset($_POST['config'])){
	$config = fetchDbConfig("config");
	$msg = writeDbConf($_POST['config'],"config", $config, "config");
}
$config = fetchDbConfig("config");

$jsScript = jsGeoLocation("siteCountry", "siteCounty", "-- ".$lang['admin']['na']." --");
  
//setting categories - return true if on the corrent page
function page($show){
	if(isset($_GET['page'])){
		if($show==$_GET['page']){
			return true;
		}else{
			return false;	
		}
	}else{
		return false;
	}
};

if(page('admin') && !evo()){header( "Location: /admin/settings/index.php?page=info" );}
  
include("../includes/header.inc.php");

if($_GET['page']=='info'){$pageTitle='Site Information';}
elseif($_GET['page']=='prodMod'){$pageTitle='Product Modules';}
elseif($_GET['page']=='shop'){$pageTitle='Shop Settings';}
elseif($_GET['page']=='site'){$pageTitle='Site Wide Settings';}
elseif($_GET['page']=='image'){$pageTitle='Image Settings';}
elseif($_GET['page']=='admin'){$pageTitle='Developer Settings';}
else{$pageTitle='General Settings';}
echo '<p class="pageTitle">'.$pageTitle.'</p>';
?>


<?php
if(isset($msg)){ 
	echo stripslashes($msg); 
} else { 
?>
<p class="copyText">Please edit your site configuration setings below:</p>
<?php } ?>
<form name="updateSettings" method="post" enctype="multipart/form-data" target="_self" action="index.php<?php if(isset($_GET['page'])){echo '?page='.$_GET['page'];}else{ echo '?page=info';} ?>">
  <table border="0" cellspacing="0" cellpadding="4" class="mainTable" width="100%">
  
  
  <!-- META DATA -->
  <?php if(page('info')){ ?>
    <tr <?php evoHide(63); ?>>
      <td colspan="2" class="tdTitle"><strong>Meta Data</strong></td>
    </tr>
    <tr <?php evoHide(63); ?>>
      <td width="30%" class="tdText"><strong>Meta Title: </strong></td>
      <td align="left" class="tdText"><input name="config[siteTitle]" class="textbox" id="titCount" type="text" size="35" class="textbox" value="<?php echo $config['siteTitle']; ?>" /> <span class="tdText" id="titDisplay"></span></td>
    </tr>
    <tr <?php evoHide(63); ?>>
      <td width="30%" align="left" valign="top" class="tdText"><strong>Meta Description: </strong></td>
      <td align="left" class="tdText"><textarea name="config[metaDescription]" id="desCount" cols="35" rows="3" class="textarea"><?php echo $config['metaDescription']; ?></textarea> <span class="tdText" id="desDisplay"></span></td>
    </tr>
    <script>
    //meta title
    var charLength = $('#titCount').val().length;
    $('#titDisplay').html(charLength + ' characters');
    $('#titCount').keyup(function() {
        var charLength = $(this).val().length;
        $('#titDisplay').html(charLength + ' characters');
    });
    //meta description
    var charLength = $('#desCount').val().length;
    $('#desDisplay').html(charLength + ' characters');
    $('#desCount').keyup(function() {
        var charLength = $(this).val().length;
        $('#desDisplay').html(charLength + ' characters');
    });
    </script>
    <tr <?php evoHide(63); ?>>
      <td width="30%" align="left" valign="top" class="tdText"><strong>Meta Keywords: </strong> (Comma Separated)</td>
      <td align="left"  class="tdText"><textarea name="config[metaKeyWords]" cols="35" rows="3" class="textarea"><?php echo $config['metaKeyWords']; ?></textarea></td>
    </tr>
    <tr <?php evoHide(63); ?>>
      <td width="30%" align="left" valign="top" class="tdText"><strong>Preview: </strong>(Google)<br />Title Limit: 69<br />Description Limit: 155</td>
      <td align="left"  class="tdText">
          <?php echo substr($config['siteTitle'],0,69); ?><br />
        <?php echo substr($config['metaDescription'],0,155); ?>
      </td>
    </tr>
    <tr <?php evoHide(63); ?>>
      <td width="30%" align="left" valign="top" class="tdText"><strong>Preview: </strong>(Yahoo)<br />Title Limit: 72<br />Description Limit: 161</td>
      <td align="left"  class="tdText">
          <?php echo substr($config['siteTitle'],0,72); ?><br />
        <?php echo substr($config['metaDescription'],0,161); ?>
      </td>
    </tr>
    <tr <?php evoHide(63); ?>>
      <td width="30%" align="left" valign="top" class="tdText"><strong>Preview: </strong>(Bing)<br />Title Limit: 65<br />Description Limit: 150</td>
      <td align="left"  class="tdText">
          <?php echo substr($config['siteTitle'],0,65); ?><br />
        <?php echo substr($config['metaDescription'],0,150); ?>
      </td>
    </tr>
    <tr>
      <td width="30%" class="tdText"><strong>Google Analytics UID:</strong><br />
        (e.g. UA-1234567-8)</td>
      <td align="left"  class="tdText"><input type="text" size="20" class="textbox" name="config[analytics_uid]" value="<?php echo $config['analytics_uid']; ?>" /></td>
    </tr>
  <? } ?>
  <!-- END META DATA -->
  
  
  <!-- COMPANY ADDRESS -->
  <?php if(page('info')){ ?>
  	<tr <?php evoHide(78); ?>>
      <td colspan="2" class="tdTitle"><strong>Company Address</strong></td>
    </tr>
    <tr <?php evoHide(78); ?>>
      <td width="30%" class="tdText"><strong>Company Name: </strong></td>
      <td class="tdText"><input name="config[storeName]" type="text" size="35" class="textbox" value="<?php echo $config['storeName']; ?>" /></td>
    </tr>
    <tr <?php evoHide(78); ?>>
      <td width="30%" class="tdText"><strong>Address: </strong></td>
      <td class="tdText"><textarea name="config[storeAddress]" cols="35" rows="3" class="textbox"><?php echo $config['storeAddress']; ?></textarea></td>
    </tr>
    <tr <?php evoHide(78); ?>>
      <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings']['country'];?></strong></td>
      <td class="tdText"><?php 
	  $countries = $db->select("SELECT * FROM ".$glob['dbprefix']."CubeCart_iso_countries"); 
	  ?>
        <select name="config[siteCountry]" id="siteCountry" onChange="updateCounty(this.form);" class="textbox">
          <?php
	for($i=0; $i<count($countries); $i++){
	?>
          <option value="<?php echo $countries[$i]['id']; ?>" <?php if($countries[$i]['id'] == $config['siteCountry']) echo "selected='selected'"; ?>><?php echo $countries[$i]['printable_name']; ?></option>
          <?php } ?>
        </select>
      </td>
    </tr>
    <tr <?php evoHide(78); ?>>
      <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings']['zone'];?></strong></td>
      <td class="tdText"><?php 
	  $counties = $db->select("SELECT * FROM ".$glob['dbprefix']."CubeCart_iso_counties WHERE countryId = '".$config['siteCountry']."'"); 
	  ?>
        <select name="config[siteCounty]" id="siteCounty" class="textbox">
          <option value="" <?php if(empty($config['siteCounty'])) echo "selected='selected'"; ?>>-- <?php echo $lang['admin']['na'];?> --</option>
          <?php
	  if($counties == TRUE){
	  ?>
          <?php for($i=0; $i<count($counties); $i++){ ?>
          <option value="<?php echo $counties[$i]['id']; ?>" <?php if($counties[$i]['id']==$config['siteCounty']) echo "selected='selected'"; ?>><?php echo $counties[$i]['name']; ?></option>
          <?php } ?>
          <?php } ?>
        </select></td>
    </tr>
    <tr <?php evoHide(99); ?>>
      <td width="30%" class="tdText"><strong>Phone Number: </strong></td>
      <td class="tdText"><input name="config[masterPhone]" type="text" size="35" class="textbox" value="<?php echo $config['masterPhone']; ?>" /></td>
    </tr>
    <? } ?>
    <!-- END COMPANY ADDRESS -->
    
    
    <!-- MAIL SETTINGS -->
    <?php if(page('info')){ ?>
    <tr <?php evoHide(75); ?>>
      <td colspan="2" class="tdTitle"><strong>Mail Settings</strong></td>
    </tr>
    <tr <?php evoHide(75); ?>>
      <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings']['email_name'];?></strong><br />
        <?php echo $lang['admin']['settings']['email_name_desc'];?></td>
      <td align="left"  class="tdText"><input type="text" size="35" class="textbox" name="config[masterName]" value="<?php echo $config['masterName']; ?>" /></td>
    </tr>
    <tr <?php evoHide(75); ?>>
      <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings']['email_address'];?></strong><br />
        <?php echo $lang['admin']['settings']['email_address_desc'];?></td>
      <td align="left"  class="tdText"><input type="text" size="35" class="textbox" name="config[masterEmail]" value="<?php echo $config['masterEmail']; ?>" /></td>
    </tr>
    <tr <?php evoHide(75); ?>>
      <td width="30%" class="tdText"><strong>Webmail Location: </strong><br />(Clear this field once your site is live to set-up your webmail link)</td>
      <td align="left"  class="tdText"><input type="text" size="35" class="textbox" name="config[webmail]" value="<?php if($config['webmail']==''){ echo $stripped_string = preg_replace(';((http)://|www3?\.);', 'http://webmail.', $GLOBALS['storeURL']); }else{ echo $config['webmail']; } ?>" /></td>
    </tr>
    <tr <?php evoHide(75); ?>>
      <td width="30%" class="tdText"><strong>User Confirmation Email for Contact Form: </strong></td>
      <td align="left"  class="tdText"><select name="config[contactFormConfirmation]" class="textbox">
      	  <option value="1" <?php if($config['contactFormConfirmation']==1) echo "selected='selected'"; ?>><?php echo $lang['admin']['yes']; ?></option>
          <option value="0" <?php if($config['contactFormConfirmation']==0) echo "selected='selected'"; ?>><?php echo $lang['admin']['no']; ?></option>
        </select>
      </td>
    </tr>
    <tr <?php evoHide(75); ?>>
      <td width="30%" class="tdText"><strong>Notification upon Registration: </strong><br />
        <?php echo $lang['admin']['settings']['email_name_desc'];?></td>
      <td align="left"  class="tdText"><select name="config[notifyAdminRegistration]" class="textbox">
      	  <option value="1" <?php if($config['notifyAdminRegistration']==1) echo "selected='selected'"; ?>><?php echo $lang['admin']['yes']; ?></option>
          <option value="0" <?php if($config['notifyAdminRegistration']==0) echo "selected='selected'"; ?>><?php echo $lang['admin']['no']; ?></option>
        </select>
      </td>
    </tr>
    <tr <?php evoHide(105); ?>>
        <td colspan="2" class="tdTitle"><strong>Contact Form Departments</strong></td>
    </tr>
    <?php if($config['numOfDeps']==''){$config['numOfDeps']=3;} ?>
    <tr <?php evoHide(105); ?>>
        <td width="30%" class="tdText"><strong>Number of departments:</strong> </td>
        <td align="left"  class="tdText"><input type="text" size="35" class="textbox" name="config[numOfDeps]" value="<?php echo $config['numOfDeps']; ?>" /></td>
    </tr>
	<?php 
		for($i=1; $i<=$config['numOfDeps']; $i++){ ?>
    		<tr <?php evoHide(105); ?>>
        		<td width="30%" class="tdText"><strong>Department <?=$i;?>:</strong> </td>
                <td align="left"  class="tdText">
                    Name: <input type="text" size="35" class="textbox" name="config[contactName<?=$i;?>]" value="<?php echo $config['contactName'.$i]; ?>" />
                    Email: <input type="text" size="35" class="textbox" name="config[contactEmail<?=$i;?>]" value="<?php echo $config['contactEmail'.$i]; ?>" />
                </td>
            </tr>
    	<? } ?>
    <? } ?>
    <!-- END MAIL SETTINGS -->
    
    
    <!-- SOCIAL NETWORKING -->
    <?php if(page('info')){ ?>
    <tr <?php evoHide(65); ?>>
      <td colspan="2" class="tdTitle"><strong>Social Networking</strong></td>
    </tr>
    <tr <?php evoHide(66); ?>>
      <td width="30%" class="tdText"><strong>Facebook Profile Link: </strong></td>
      <td align="left"  class="tdText"><input name="config[facebook]" type="text" size="35" class="textbox" value="<?php echo $config['facebook']; ?>" /></td>
    </tr>
    <tr <?php evoHide(69); ?>>
      <td width="30%" class="tdText"><strong>Linkedin Profile Link: </strong></td>
      <td align="left"  class="tdText"><input name="config[linkedin]" type="text" size="35" class="textbox" value="<?php echo $config['linkedin']; ?>" /></td>
    </tr>
    <tr <?php evoHide(67); ?>>
      <td width="30%" class="tdText"><strong>Twitter Profile Link: </strong></td>
      <td align="left"  class="tdText"><input name="config[twitter]" type="text" size="35" class="textbox" value="<?php echo $config['twitter']; ?>" /></td>
    </tr>
    <tr <?php evoHide(68); ?>>
      <td width="30%" class="tdText"><strong>Youtube Profile Link: </strong></td>
      <td align="left"  class="tdText"><input name="config[youtube]" type="text" size="35" class="textbox" value="<?php echo $config['youtube']; ?>" /></td>
    </tr>
    
    <?php if($config['left_col_no']){
		for($l=1;$l<$config['left_col_no']+1;$l++){
	?>
    <tr>
      <td width="30%" class="tdText"><strong>Left Column Image <?php echo $l; ?> : </strong></td>
      <td align="left"  class="tdText"><input name="config[lci<?php echo $l; ?>]" type="text" size="35" class="textbox" value="<?php echo $config['lci'.$l]; ?>" /></td>
    </tr>    
    <tr>
      <td width="30%" class="tdText"><strong>Left Column Link <?php echo $l; ?> : </strong></td>
      <td align="left"  class="tdText"><input name="config[lcl<?php echo $l; ?>]" type="text" size="35" class="textbox" value="<?php echo $config['lcl'.$l]; ?>" /></td>
    </tr>    
    
    <?php			
		}
	}
	?>

    
    <? } ?>
  <!-- END SOCIAL NETWORKING -->
    
    
    <!-- DIRECTORIES AND FOLDERS -->
    <?php if(page('admin')){ ?>
    
    <tr>
      <td width="30%" class="tdText"><strong>Number of Left Column Images</strong></td>
      <td align="left"  class="tdText"><input type="text" size="35" class="textbox" name="config[left_col_no]" value="<?php echo $config['left_col_no']; ?>" /></td>
    </tr>
    
    <tr <?php evoHide(3); ?>>
      <td colspan="2" class="tdTitle"><strong>Directories & Folders - DO NOT EDIT</strong></td>
    </tr>
    <!--
	<tr <?php evoHide(3); ?>>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings']['rootRel'];?></strong> <?php echo $lang['admin']['settings']['include_slash'];?></td>
		<td align="left"  class="tdText"><input type="text" size="35" class="textbox" name="config[rootRel]" value="<?php echo $glob['rootRel']; ?>" /></td>
	</tr>
	<tr <?php evoHide(3); ?>>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings']['storeURL'];?></strong> <?php echo $lang['admin']['settings']['include_slash'];?><br />
	  <?php echo $lang['admin']['settings']['eg_domain_com'];?> </td>
		<td align="left"  class="tdText"><input type="text" size="35" class="textbox" name="config[storeURL]" value="<?php echo $glob['storeURL']; ?>" /></td>
	</tr>
	<tr <?php evoHide(3); ?>>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings']['rootDir'];?></strong><br />
	  <?php echo $lang['admin']['settings']['eg_root_path'];?>
	</td>
		<td align="left"  class="tdText"><input type="text" size="35" class="textbox" name="config[rootDir]" value="<?php echo $glob['rootDir']; ?>" /></td>
	</tr>
	-->
    <tr <?php evoHide(3); ?>>
      <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings']['rootRel_SSL'];?></strong> <br />
        <?php echo $lang['admin']['settings']['include_slash'];?> </td>
      <td align="left"  class="tdText"><input type="text" size="35" class="textbox" name="config[rootRel_SSL]" value="<?php echo $config['rootRel_SSL']; ?>" /></td>
    </tr>
    <tr <?php evoHide(3); ?>>
      <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings']['storeURL_SSL'];?></strong> <br />
        <?php echo $lang['admin']['settings']['eg_domain_SSL'];?></td>
      <td align="left"  class="tdText"><input type="text" size="35" class="textbox" name="config[storeURL_SSL]" value="<?php echo $config['storeURL_SSL']; ?>" /></td>
    </tr>
    <!--
	<tr <?php evoHide(3); ?>>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings']['rootDir_SSL'];?></strong><br />
	  <?php echo $lang['admin']['settings']['eg_root_path_secure'];?></td>
		<td align="left"  class="tdText"><input type="text" size="35" class="textbox" name="config[rootDir_SSL]" value="<?php echo $config['rootDir_SSL']; ?>" /></td>
	</tr>
	-->
    <tr <?php evoHide(3); ?>>
      <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings']['enable_ssl'];?></strong><br />
        <?php echo $lang['admin']['settings']['ssl_warn'];?> </td>
      <td align="left"  class="tdText"><select name="config[ssl]" class="textbox">
          <option value="1" <?php if($config['ssl']==1) echo "selected='selected'"; ?>><?php echo $lang['admin']['yes'];?></option>
          <option value="0" <?php if($config['ssl']==0) echo "selected='selected'"; ?>><?php echo $lang['admin']['no'];?></option>
        </select></td>
    </tr>
    <tr <?php evoHide(3); ?>>
      <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings']['dir_symbol'];?></strong></td>
      <td align="left"  class="tdText"><input type="text" size="20" class="textbox" name="config[dirSymbol]" value="<?php echo $config['dirSymbol']; ?>" /></td>
    </tr>
    <? } ?>
    <!-- END DIRECTORIES AND FOLDERS -->
    
    
    <!-- DIGITAL DOWNLOADS -->
    <?php if(page('admin')){ ?>
    <tr <?php evoHide(2); ?>>
      <td colspan="2" class="tdTitle"><strong>Digital Downloads - DO NOT EDIT</strong></td>
    </tr>
    <tr <?php evoHide(2); ?>>
      <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings']['download_expire_time'];?></strong><br/>
        <?php echo $lang['admin']['settings']['seconds'];?></td>
      <td align="left"  class="tdText"><input type="text" size="35" class="textbox" name="config[dnLoadExpire]" value="<?php echo $config['dnLoadExpire']; ?>" /></td>
    </tr>
    <tr <?php evoHide(2); ?>>
      <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings']['download_attempts'];?></strong><br />
        <?php echo $lang['admin']['settings']['attempts_desc'];?></td>
      <td align="left"  class="tdText"><input type="text" size="35" class="textbox" name="config[dnLoadTimes]" value="<?php echo $config['dnLoadTimes']; ?>" /></td>
    </tr>
    <? } ?>
    <!-- END DIGITAL DOWNLOADS -->
    
    
    <!-- HOMEPAGE CATEGORIES -->
    <?php if(page('prodMod')){ ?>
    <tr <?php evoHide(74); ?>>
      <td colspan="2" class="tdTitle"><strong>Homepage Categories</strong></td>
    </tr>
    <tr <?php evoHide(74); ?>>
      <td width="30%" class="tdText"><strong>Show Homepage Categories: </strong></td>
      <td align="left"  class="tdText"><select class="textbox" name="config[showHomePageCats]">
          <option value="0" <?php if($config['showHomePageCats']==0) echo "selected='selected'"; ?>><?php echo $lang['admin']['no'];?></option>
          <option value="1" <?php if($config['showHomePageCats']==1) echo "selected='selected'"; ?>><?php echo $lang['admin']['yes'];?></option>
        </select>
      </td>
    </tr>
    <tr <?php evoHide(74); ?>>
      <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings']['no_cats_per_row'];?></strong></td>
      <td align="left"  class="tdText"><input type="text" size="3" class="textbox" name="config[displaycatRows]" value="<?php echo $config['displaycatRows']; ?>" /></td>
    </tr>
    <? } ?>
    <!-- END HOMEPAGE CATEGORIES -->
    
    <!-- LATEST PRODUCTS -->
    <?php if(page('prodMod')){ ?>
    <tr <?php evoHide(27); ?>>
      <td colspan="2" class="tdTitle"><strong>Latest Products</strong></td>
    </tr>
    <tr <?php evoHide(28); ?>>
    	<td colspan="2" class="tdText">Ordered by date and time when each product was last modified in descending order</td>
    </tr>
    <tr <?php evoHide(27); ?>>
      <td width="30%" class="tdText"><strong>Homepage Module: </strong></td>
      <td align="left"  class="tdText"><select class="textbox" name="config[latestProdsHome]">
          <option value="0" <?php if($config['latestProdsHome']==0) echo "selected='selected'"; ?>><?php echo $lang['admin']['no'];?></option>
          <option value="1" <?php if($config['latestProdsHome']==1) echo "selected='selected'"; ?>><?php echo $lang['admin']['yes'];?></option>
        </select>
      </td>
    </tr>
    <tr <?php evoHide(27); ?>>
      <td width="30%" class="tdText"><strong>Side Module: </strong></td>
      <td align="left"  class="tdText"><select class="textbox" name="config[latestProdsSide]">
          <option value="0" <?php if($config['latestProdsSide']==0) echo "selected='selected'"; ?>><?php echo $lang['admin']['no'];?></option>
          <option value="1" <?php if($config['latestProdsSide']==1) echo "selected='selected'"; ?>><?php echo $lang['admin']['yes'];?></option>
        </select>
      </td>
    </tr>
    <tr <?php evoHide(27); ?>>
      <td width="30%" class="tdText"><strong>Number Of Products In Module: </strong></td>
      <td align="left"  class="tdText"><input type="text" class="textbox" size="3" name="config[noLatestProds]" value="<?php echo $config['noLatestProds']; ?>" />
      </td>
    </tr>
    <tr <?php evoHide(27); ?>>
      <td width="30%" class="tdText"><strong>Show As Category: </strong></td>
      <td align="left"  class="tdText"><select class="textbox" name="config[latestProdsCat]">
          <option value="0" <?php if($config['latestProdsCat']==0) echo "selected='selected'"; ?>><?php echo $lang['admin']['no'];?></option>
          <option value="1" <?php if($config['latestProdsCat']==1) echo "selected='selected'"; ?>><?php echo $lang['admin']['yes'];?></option>
        </select>
      </td>
    </tr>
    <? } ?>
    <!-- END LATEST PRODUCTS -->
    
    
    <!-- FEATURED PRODUCTS -->
    <?php if(page('prodMod')){ ?>
    <tr <?php evoHide(26); ?>>
      <td colspan="2" class="tdTitle"><strong>Featured Products</strong></td>
    </tr>
    <tr <?php evoHide(28); ?>>
    	<td colspan="2" class="tdText">Ordered by name in ascending order</td>
    </tr>
    <tr <?php evoHide(26); ?>>
      <td width="30%" class="tdText"><strong>Homepage Module: </strong></td>
      <td align="left"  class="tdText"><select class="textbox" name="config[featuredProdsHome]">
          <option value="0" <?php if($config['featuredProdsHome']==0) echo "selected='selected'"; ?>><?php echo $lang['admin']['no'];?></option>
          <option value="1" <?php if($config['featuredProdsHome']==1) echo "selected='selected'"; ?>><?php echo $lang['admin']['yes'];?></option>
        </select>
      </td>
    </tr>
    <tr <?php evoHide(26); ?>>
      <td width="30%" class="tdText"><strong>Side Module: </strong></td>
      <td align="left"  class="tdText"><select class="textbox" name="config[featuredProdsSide]">
          <option value="0" <?php if($config['featuredProdsSide']==0) echo "selected='selected'"; ?>><?php echo $lang['admin']['no'];?></option>
          <option value="1" <?php if($config['featuredProdsSide']==1) echo "selected='selected'"; ?>><?php echo $lang['admin']['yes'];?></option>
        </select>
      </td>
    </tr>
    <tr <?php evoHide(26); ?>>
      <td width="30%" class="tdText"><strong>Number Of Products In Module: </strong></td>
      <td align="left"  class="tdText"><input type="text" class="textbox" size="3" name="config[noFeaturedProds]" value="<?php echo $config['noFeaturedProds']; ?>" />
      </td>
    </tr>
    <tr <?php evoHide(26); ?>>
      <td width="30%" class="tdText"><strong>Show As Category: </strong></td>
      <td align="left"  class="tdText"><select class="textbox" name="config[featuredProdsCat]">
          <option value="0" <?php if($config['featuredProdsCat']==0) echo "selected='selected'"; ?>><?php echo $lang['admin']['no'];?></option>
          <option value="1" <?php if($config['featuredProdsCat']==1) echo "selected='selected'"; ?>><?php echo $lang['admin']['yes'];?></option>
        </select>
      </td>
    </tr>
    <? } ?>
    <!-- END FEATURED PRODUCTS -->
    
    
    <!-- POPULAR PRODUCTS -->
    <?php if(page('prodMod')){ ?>
    <tr <?php evoHide(28); ?>>
      <td colspan="2" class="tdTitle"><strong>Popular Products</strong></td>
    </tr>
    <tr <?php evoHide(28); ?>>
    	<td colspan="2" class="tdText">Ordered by the number of visits to product pages in descending order</td>
    </tr>
    <tr <?php evoHide(28); ?>>
      <td width="30%" class="tdText"><strong>Homepage Module: </strong></td>
      <td align="left"  class="tdText"><select class="textbox" name="config[popularProdsHome]">
          <option value="0" <?php if($config['popularProdsHome']==0) echo "selected='selected'"; ?>><?php echo $lang['admin']['no'];?></option>
          <option value="1" <?php if($config['popularProdsHome']==1) echo "selected='selected'"; ?>><?php echo $lang['admin']['yes'];?></option>
        </select>
      </td>
    </tr>
    <tr <?php evoHide(28); ?>>
      <td width="30%" class="tdText"><strong>Side Module: </strong></td>
      <td align="left"  class="tdText"><select class="textbox" name="config[popularProdsSide]">
          <option value="0" <?php if($config['popularProdsSide']==0) echo "selected='selected'"; ?>><?php echo $lang['admin']['no'];?></option>
          <option value="1" <?php if($config['popularProdsSide']==1) echo "selected='selected'"; ?>><?php echo $lang['admin']['yes'];?></option>
        </select>
      </td>
    </tr>
    <tr <?php evoHide(28); ?>>
      <td width="30%" class="tdText"><strong>Number Of Products In Module: </strong></td>
      <td align="left"  class="tdText"><input type="text" class="textbox" size="3" name="config[noPopularProds]" value="<?php echo $config['noPopularProds']; ?>" />
      </td>
    </tr>
    <tr <?php evoHide(28); ?>>
      <td width="30%" class="tdText"><strong>Show As Category: </strong></td>
      <td align="left"  class="tdText"><select class="textbox" name="config[popularProdsCat]">
          <option value="0" <?php if($config['popularProdsCat']==0) echo "selected='selected'"; ?>><?php echo $lang['admin']['no'];?></option>
          <option value="1" <?php if($config['popularProdsCat']==1) echo "selected='selected'"; ?>><?php echo $lang['admin']['yes'];?></option>
        </select>
      </td>
    </tr>
    <? } ?>
    <!-- END POPULAR PRODUCTS -->
    
    
    <!-- BEST SELLER PRODUCTS -->
    <?php if(page('prodMod')){ ?>
    <tr <?php evoHide(85); ?>>
      <td colspan="2" class="tdTitle"><strong>Best Seller Products</strong></td>
    </tr>
    <tr <?php evoHide(85); ?>>
    	<td colspan="2" class="tdText">Ordered by the number of each product sold in descending order</td>
    </tr>
    <tr <?php evoHide(85); ?>>
      <td width="30%" class="tdText"><strong>Homepage Module: </strong></td>
      <td align="left"  class="tdText"><select class="textbox" name="config[bestsellerProdsHome]">
          <option value="0" <?php if($config['bestsellerProdsHome']==0) echo "selected='selected'"; ?>><?php echo $lang['admin']['no'];?></option>
          <option value="1" <?php if($config['bestsellerProdsHome']==1) echo "selected='selected'"; ?>><?php echo $lang['admin']['yes'];?></option>
        </select>
      </td>
    </tr>
    <tr <?php evoHide(85); ?>>
      <td width="30%" class="tdText"><strong>Side Module: </strong></td>
      <td align="left"  class="tdText"><select class="textbox" name="config[bestsellerProdsSide]">
          <option value="0" <?php if($config['bestsellerProdsSide']==0) echo "selected='selected'"; ?>><?php echo $lang['admin']['no'];?></option>
          <option value="1" <?php if($config['bestsellerProdsSide']==1) echo "selected='selected'"; ?>><?php echo $lang['admin']['yes'];?></option>
        </select>
      </td>
    </tr>
    <tr <?php evoHide(85); ?>>
      <td width="30%" class="tdText"><strong>Number Of Products In Module: </strong></td>
      <td align="left"  class="tdText"><input type="text" class="textbox" size="3" name="config[noBestsellerProds]" value="<?php echo $config['noPopularProds']; ?>" />
      </td>
    </tr>
    <tr <?php evoHide(85); ?>>
      <td width="30%" class="tdText"><strong>Show As Category: </strong></td>
      <td align="left"  class="tdText"><select class="textbox" name="config[bestsellerProdsCat]">
          <option value="0" <?php if($config['bestsellerProdsCat']==0) echo "selected='selected'"; ?>><?php echo $lang['admin']['no'];?></option>
          <option value="1" <?php if($config['bestsellerProdsCat']==1) echo "selected='selected'"; ?>><?php echo $lang['admin']['yes'];?></option>
        </select>
      </td>
    </tr>
    <? } ?>
    <!-- END BEST SELLER PRODUCTS -->
    
    
    <!-- SALES PRODUCTS -->
    <?php if(page('prodMod')){ ?>
    <tr <?php evoHide(26); ?>>
      <td colspan="2" class="tdTitle"><strong>Sale Products</strong></td>
    </tr>
    <tr <?php evoHide(28); ?>>
    	<td colspan="2" class="tdText">Ordered by name in ascending order</td>
    </tr>
    <tr <?php evoHide(26); ?>>
      <td width="30%" class="tdText"><strong>Homepage Module: </strong></td>
      <td align="left"  class="tdText"><select class="textbox" name="config[saleProdsHome]">
          <option value="0" <?php if($config['saleProdsHome']==0) echo "selected='selected'"; ?>><?php echo $lang['admin']['no'];?></option>
          <option value="1" <?php if($config['saleProdsHome']==1) echo "selected='selected'"; ?>><?php echo $lang['admin']['yes'];?></option>
        </select>
      </td>
    </tr>
    <tr <?php evoHide(26); ?>>
      <td width="30%" class="tdText"><strong>Side Module: </strong></td>
      <td align="left"  class="tdText"><select class="textbox" name="config[saleProdsSide]">
          <option value="0" <?php if($config['saleProdsSide']==0) echo "selected='selected'"; ?>><?php echo $lang['admin']['no'];?></option>
          <option value="1" <?php if($config['saleProdsSide']==1) echo "selected='selected'"; ?>><?php echo $lang['admin']['yes'];?></option>
        </select>
      </td>
    </tr>
    <tr <?php evoHide(26); ?>>
      <td width="30%" class="tdText"><strong>Number Of Products In Module: </strong></td>
      <td align="left"  class="tdText"><input type="text" class="textbox" size="3" name="config[noSaleProds]" value="<?php echo $config['noSaleProds']; ?>" />
      </td>
    </tr>
    <tr <?php evoHide(26); ?>>
      <td width="30%" class="tdText"><strong>Show As Category: </strong></td>
      <td align="left"  class="tdText"><select class="textbox" name="config[saleProdsCat]">
          <option value="0" <?php if($config['saleProdsCat']==0) echo "selected='selected'"; ?>><?php echo $lang['admin']['no'];?></option>
          <option value="1" <?php if($config['saleProdsCat']==1) echo "selected='selected'"; ?>><?php echo $lang['admin']['yes'];?></option>
        </select>
      </td>
    </tr>
    <? } ?>
    <!-- END SALES PRODUCTS -->
    
    
    <!-- PRODUCT SETTINGS -->
    <?php if(page('shop')){ ?>
    <tr <?php evoHide(6); ?>>
      <td colspan="2" class="tdTitle"><strong>Product Settings</strong></td>
    </tr>
    <tr <?php evoHide(6); ?>>
      <td width="30%" class="tdText"><strong>Number of Products Per Page: </strong></td>
      <td align="left"  class="tdText"><input type="text" size="3" class="textbox" name="config[productPages]" value="<?php echo $config['productPages']; ?>" /></td>
    </tr>
    <tr <?php evoHide(6); ?>>
      <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings']['precis_length'];?></strong><?php echo $lang['admin']['settings']['chars'];?></td>
      <td align="left"  class="tdText"><input type="text" size="3" class="textbox" name="config[productPrecis]" value="<?php echo $config['productPrecis']; ?>" /></td>
    </tr>
    <? } ?>
    <!-- END PRODUCT SETTINGS -->
    
    
    <!-- GALLERY -->
    <?php if(page('image')){ ?>
    <tr <?php evoHide(80); ?>>
      <td colspan="2" class="tdTitle"><strong>Gallery</strong></td>
    </tr>
    <tr <?php evoHide(80); ?>>
      <td width="30%" class="tdText"><strong>Gallery Type: </strong></td>
      <td align="left"  class="tdText">
      	<select name="config[galleryType]" class="textbox">
          <option value="0" <?php if($config['galleryType']==0) echo "selected='selected'"; ?>>Lightbox</option>
          <option value="1" <?php if($config['galleryType']==1) echo "selected='selected'"; ?>>Galleria</option>
        </select>
      </td>
    </tr>
    <? } ?>
    <!-- GALLERY -->
    
    
    <!-- IMAGE SETTINGS -->
    <?php if(page('image')){ ?>
    <tr <?php evoHide(4); ?>>
      <td colspan="2" class="tdTitle"><strong>Image Settings - DO NOT EDIT</strong></td>
    </tr>
    <tr <?php evoHide(4); ?>>
      <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings']['max_upload_size'];?></strong><br />
        <?php echo $lang['admin']['settings']['under_x_recom'];?></td>
      <td align="left"  class="tdText"><input type="text" size="10" class="textbox" name="config[maxImageUploadSize]" value="<?php echo $config['maxImageUploadSize']; ?>" /></td>
    </tr>
    <tr <?php evoHide(4); ?>>
      <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings']['gd_ver'];?></strong></td>
      <td align="left"  class="tdText"><select name="config[gdversion]" class="textbox">
          <option value="2" <?php if($config['gdversion']==2) echo "selected='selected'"; ?>>2</option>
          <option value="1" <?php if($config['gdversion']==1) echo "selected='selected'"; ?>>1</option>
          <option value="0" <?php if($config['gdversion']==0) echo "selected='selected'"; ?>><?php echo $lang['admin']['na']; ?></option>
        </select>
      </td>
    </tr>
    <tr <?php evoHide(4); ?>>
      <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings']['gd_img_quality'];?></strong><br />
        <?php echo $lang['admin']['settings']['recom_quality'];?></td>
      <td align="left"  class="tdText"><input type="text" size="3" class="textbox" name="config[gdquality]" value="<?php echo $config['gdquality']; ?>" /></td>
    </tr>
    <tr <?php evoHide(4); ?>>
      <td width="30%" class="tdText"><strong>Allow GIF Support:</strong><br />(Please make sure this is enabled on your server)</td>
      <td align="left"  class="tdText"><select name="config[gdGifSupport]" class="textbox">
          <option value="0" <?php if($config['gdGifSupport']==0) echo "selected='selected'"; ?>><?php echo $lang['admin']['no'];?></option>
          <option value="1" <?php if($config['gdGifSupport']==1) echo "selected='selected'"; ?>><?php echo $lang['admin']['yes'];?></option>
        </select>
      </td>
    </tr>
    <tr <?php evoHide(4); ?>>
      <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings']['gd_max_img_size'];?></strong></td>
      <td align="left"  class="tdText"><input type="text" size="4" class="textbox" name="config[gdmaxImgSize]" value="<?php echo $config['gdmaxImgSize']; ?>" /></td>
    </tr>
    <tr <?php evoHide(4); ?>>
      <td width="30%" class="tdText"><strong>Quick Upload Width &amp; Height:</strong><br />(Creates square thumbnails)</td>
      <td align="left"  class="tdText"><input type="text" size="4" class="textbox" name="config[gdthumbSize]" value="<?php echo $config['gdthumbSize']; ?>" /></td>
    </tr>
    <tr <?php evoHide(4); ?>>
      <td width="30%" class="tdText"><strong>Slider Image Width:</strong></td>
      <td align="left"  class="tdText"><input type="text" size="4" class="textbox" name="config[slideImgWid]" value="<?php echo $config['slideImgWid']; ?>" /></td>
    </tr>
    <tr <?php evoHide(4); ?>>
      <td width="30%" class="tdText"><strong>Slider Image Height:</strong></td>
      <td align="left"  class="tdText"><input type="text" size="4" class="textbox" name="config[slideImgHei]" value="<?php echo $config['slideImgHei']; ?>" /></td>
    </tr>
    <tr <?php evoHide(4); ?>>
      <td width="30%" class="tdText"><strong>Product Image Width:</strong></td>
      <td align="left"  class="tdText"><input type="text" size="4" class="textbox" name="config[prodImgWid]" value="<?php echo $config['prodImgWid']; ?>" /></td>
    </tr>
    <tr <?php evoHide(4); ?>>
      <td width="30%" class="tdText"><strong>Product Image Height:</strong></td>
      <td align="left"  class="tdText"><input type="text" size="4" class="textbox" name="config[prodImgHei]" value="<?php echo $config['prodImgHei']; ?>" /></td>
    </tr>
    <tr <?php evoHide(4); ?>>
      <td width="30%" class="tdText"><strong>Category Image Width:</strong></td>
      <td align="left"  class="tdText"><input type="text" size="4" class="textbox" name="config[catImgWid]" value="<?php echo $config['catImgWid']; ?>" /></td>
    </tr>
    <tr <?php evoHide(4); ?>>
      <td width="30%" class="tdText"><strong>Category Image Height:</strong></td>
      <td align="left"  class="tdText"><input type="text" size="4" class="textbox" name="config[catImgHei]" value="<?php echo $config['catImgHei']; ?>" /></td>
    </tr>
    <tr <?php evoHide(4); ?>>
      <td width="30%" class="tdText"><strong>Brand Image Width:</strong></td>
      <td align="left"  class="tdText"><input type="text" size="4" class="textbox" name="config[brandImgWid]" value="<?php echo $config['brandImgWid']; ?>" /></td>
    </tr>
    <tr <?php evoHide(4); ?>>
      <td width="30%" class="tdText"><strong>Brand Image Height:</strong></td>
      <td align="left"  class="tdText"><input type="text" size="4" class="textbox" name="config[brandImgHei]" value="<?php echo $config['brandImgHei']; ?>" /></td>
    </tr>
    <tr <?php evoHide(4); ?>>
      <td width="30%" class="tdText"><strong>News Image Width:</strong></td>
      <td align="left"  class="tdText"><input type="text" size="4" class="textbox" name="config[newsImgWid]" value="<?php echo $config['newsImgWid']; ?>" /></td>
    </tr>
    <tr <?php evoHide(4); ?>>
      <td width="30%" class="tdText"><strong>News Image Height:</strong></td>
      <td align="left"  class="tdText"><input type="text" size="4" class="textbox" name="config[newsImgHei]" value="<?php echo $config['newsImgHei']; ?>" /></td>
    </tr>
    <tr <?php evoHide(4); ?>>
      <td width="30%" class="tdText"><strong>Gallery Image Width:</strong></td>
      <td align="left"  class="tdText"><input type="text" size="4" class="textbox" name="config[galImgWid]" value="<?php echo $config['galImgWid']; ?>" /></td>
    </tr>
    <tr <?php evoHide(4); ?>>
      <td width="30%" class="tdText"><strong>Gallery Image Height:</strong></td>
      <td align="left"  class="tdText"><input type="text" size="4" class="textbox" name="config[galImgHei]" value="<?php echo $config['galImgHei']; ?>" /></td>
    </tr>
    
    <? } ?>
    <!-- END IMAGE SETTINGS -->
    
    
    <!-- STOCK SETTINGS -->
    <?php if(page('shop')){ ?>
    <tr <?php evoHide(5); ?>>
      <td colspan="2" class="tdTitle"><strong>Stock Settings</strong></td>
    </tr>
    <tr <?php evoHide(5); ?>>
      <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings']['use_stock'];?></strong></td>
      <td align="left"  class="tdText"><select name="config[stockLevel]" class="textbox">
          <option value="1" <?php if($config['stockLevel']==1) echo "selected='selected'"; ?>><?php echo $lang['admin']['yes'];?></option>
          <option value="0" <?php if($config['stockLevel']==0) echo "selected='selected'"; ?>><?php echo $lang['admin']['no'];?></option>
        </select>
      </td>
    </tr>
    <tr <?php evoHide(5); ?>>
      <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings']['allow_out_of_stock_purchases'];?></strong></td>
      <td align="left"  class="tdText"><select name="config[outofstockPurchase]" class="textbox">
          <option value="1" <?php if($config['outofstockPurchase']==1) echo "selected='selected'"; ?>><?php echo $lang['admin']['yes'];?></option>
          <option value="0" <?php if($config['outofstockPurchase']==0) echo "selected='selected'"; ?>><?php echo $lang['admin']['no'];?></option>
        </select>
      </td>
    </tr>
    <tr <?php evoHide(5); ?>>
      <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings']['weight_unit'];?></strong></td>
      <td align="left"  class="tdText"><select name="config[weightUnit]" class="textbox">
          <option value="Lbs" <?php if($config['weightUnit']=="Lb") echo "selected='selected'"; ?>>Lbs</option>
          <option value="Kg" <?php if($config['weightUnit']=="Kg") echo "selected='selected'"; ?>>Kg</option>
        </select></td>
    </tr>
    <tr <?php evoHide(5); ?>>
      <td width="30%" class="tdText"><strong>Stock Warning Level</strong></td>
      <td align="left"  class="tdText"><input type="text" size="20" class="textbox" name="config[stock_warn]" value="<?php echo $config['stock_warn']; ?>" /></td>
    </tr>
    <? } ?>
    <!-- END STOCK SETTINGS -->
    
    <!-- TIME AND DATE -->
    <?php if(page('admin')){ ?>
    <tr <?php evoHide(7); ?>>
      <td colspan="2" class="tdTitle"><strong>Time &amp; Date - DO NOT EDIT</strong></td>
    </tr>
    <tr <?php evoHide(7); ?>>
      <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings']['time_format'];?></strong><br />
        <?php echo $lang['admin']['settings']['time_format_desc'];?></td>
      <td align="left"  class="tdText"><input type="text" size="20" class="textbox" name="config[timeFormat]" value="<?php echo $config['timeFormat']; ?>" /></td>
    </tr>
    <tr <?php evoHide(7); ?>>
      <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings']['time_offset'];?></strong><br />
        <?php echo $lang['admin']['settings']['time_offset_desc'];?></td>
      <td align="left"  class="tdText"><input name="config[timeOffset]" type="text" class="textbox" value="<?php echo $config['timeOffset']; ?>" size="20" /></td>
    </tr>
    <tr <?php evoHide(7); ?>>
      <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings']['date_format'];?></strong><br />
        <?php echo $lang['admin']['settings']['date_format_desc'];?></td>
      <td align="left"  class="tdText"><input type="text" size="35" class="textbox" name="config[dateFormat]" value="<?php echo $config['dateFormat']; ?>" /></td>
    </tr>
    <tr <?php evoHide(7); ?>>
      <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings']['max_sess_length'];?></strong><br />
        <?php echo $lang['admin']['settings']['seconds'];?></td>
      <td align="left"  class="tdText"><input type="text" size="10" class="textbox" name="config[sqlSessionExpiry]" value="<?php echo $config['sqlSessionExpiry']; ?>" /></td>
    </tr>
    <? } ?>
    <!-- END TIME AND DATE -->
    
    
    <!-- Limit Site Documents -->
    <?php if(page('admin')){ ?>
    <tr <?php evoHide(7); ?>>
      <td colspan="2" class="tdTitle"><strong>Limit Settings</strong></td>
    </tr>
    <tr <?php evoHide(7); ?>>
      <td width="30%" class="tdText"><strong>Limit Documents: </strong></td>
      <td align="left"  class="tdText"><input type="text" size="20" class="textbox" name="config[pageLimit]" value="<?php echo $config['pageLimit']; ?>" /></td>
    </tr>
    <? } ?>
    <!-- Limit Site Documents -->
    
    
    <!-- Login Type -->
    <?php if(page('admin')){ ?>
    <tr <?php evoHide(7); ?>>
      <td colspan="2" class="tdTitle"><strong>Front-End Login</strong></td>
    </tr>
    <tr <?php evoHide(7); ?>>
      <td width="30%" class="tdText"><strong>Username Type: </strong></td>
      <td align="left"  class="tdText">
      	<select name="config[usernameType]" class="textbox">
          <option value="0" <?php if($config['usernameType']==0) echo "selected='selected'"; ?>>Email Address</option>
          <option value="1" <?php if($config['usernameType']==1) echo "selected='selected'"; ?>>Nickname</option>
        </select>
      </td>
    </tr>
    <? } ?>
    <!-- Login Type -->
    
    
    <!-- SITEWIDE SHOP SETTINGS -->
    <?php if(page('shop')){ ?>
    <tr <?php evoHide(77); ?>>
      <td colspan="2" class="tdTitle">Sitewide Shop Settings</td>
    </tr>
    <tr <?php evoHide(77); ?>>
      <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings']['default_language'];?></strong></td>
      <td align="left"  class="tdText"><?php
	$path = $GLOBALS['rootDir']."/language";
	if ($dir = opendir($path)) {
		?>
        <select class="textbox" name="config[defaultLang]">
          <?php
	
		while (false !== ($folder = readdir($dir))) {
			
			if(!eregi($folder,array(".",".."))){
			
			include($path."/".$folder."/config.inc.php");
			?>
          <option value="<?php echo $folder; ?>" <?php if($config['defaultLang']==$folder) echo "selected='selected'"; ?>><?php echo $langName; ?></option>
          <?php 
			}
		} 
		?>
        </select>
        <?php } ?>
      </td>
    </tr>
    <tr <?php evoHide(77); ?>>
      <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings']['default_currency'];?></strong></td>
      <td align="left"  class="tdText"><?php
	  $currencies = $db->select("SELECT name, code FROM ".$glob['dbprefix']."CubeCart_currencies WHERE active = 1 ORDER BY name ASC");
		?>
        <select class="textbox" name="config[defaultCurrency]">
          <?php
		for($i=0; $i<count($currencies); $i++){
		?>
          <option value="<?php echo $currencies[$i]['code']; ?>" <?php if($currencies[$i]['code']==$config['defaultCurrency']) echo "selected='selected'"; ?>><?php echo $currencies[$i]['name']; ?></option>
          <?php
		}
	  ?>
        </select>
      </td>
    </tr>
    <tr <?php evoHide(77); ?>>
      <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings']['inc_tax_prices'];?></strong></td>
      <td align="left"  class="tdText"><select name="config[priceIncTax]" class="textbox">
          <option value="1" <?php if($config['priceIncTax']==1) echo "selected='selected'"; ?>><?php echo $lang['admin']['yes'];?></option>
          <option value="0" <?php if($config['priceIncTax']==0) echo "selected='selected'"; ?>><?php echo $lang['admin']['no'];?></option>
        </select>
      </td>
    </tr>
    <tr <?php evoHide(77); ?>>
      <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings']['sale_mode'];?></strong></td>
      <td align="left"  class="tdText"><select name="config[saleMode]" class="textbox">
          <option value="2" <?php if($config['saleMode']==2) echo "selected='selected'"; ?>><?php echo $lang['admin']['settings']['percent_of_all'];?></option>
          <option value="1" <?php if($config['saleMode']==1) echo "selected='selected'"; ?>><?php echo $lang['admin']['settings']['ind_sale_per_item'];?></option>
          <option value="0" <?php if($config['saleMode']==0) echo "selected='selected'"; ?>><?php echo $lang['admin']['settings']['sale_mode_off'];?></option>
        </select>
      </td>
    </tr>
    <tr <?php evoHide(77); ?>>
      <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings']['sale_per_off'];?></strong><br />
        <?php echo $lang['admin']['settings']['sale_per_off_desc'];?></td>
      <td align="left"  class="tdText"><input type="text" size="5" class="textbox" name="config[salePercentOff]" value="<?php echo $config['salePercentOff']; ?>" /></td>
    </tr>
    <? } ?>
    <!-- END SITEWIDE SHOP SETTINGS -->
    
    
    <!-- SITEWIDE SETTINGS -->
    <?php if(page('site')){ ?>
    <tr <?php evoHide(76); ?>>
      <td colspan="2" class="tdTitle">Sitewide Settings</td>
    </tr>
    <tr <?php evoHide(76); ?>>
      <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings']['diff_dispatch'];?></strong></td>
      <td align="left"  class="tdText"><select name="config[shipAddressLock]" class="textbox">
          <option value="0" <?php if($config['shipAddressLock']==0) echo "selected='selected'"; ?>><?php echo $lang['admin']['yes'];?></option>
          <option value="1" <?php if($config['shipAddressLock']==1) echo "selected='selected'"; ?>><?php echo $lang['admin']['no'];?></option>
        </select></td>
    </tr>
    <tr <?php evoHide(76); ?>>
      <td width="30%" class="tdText"><strong>Skin: </strong></td>
      <td align="left"  class="tdText"><?php
	$path = $GLOBALS['rootDir']."/skins";
	if ($dir = opendir($path)) {
		?>
        <select class="textbox" name="config[skinDir]">
          <?php
	
		while (false !== ($file = readdir($dir))) {
			
			if(!eregi($file,array(".",".."))){
			?>
          <option value="<?php echo $file; ?>" <?php if($file==$config['skinDir']) { echo "selected='selected'"; } ?>><?php echo $file; ?></option>
          <?php 
			}
		} 
		?>
        </select>
        <?php } ?>
      </td>
    </tr>
    <? } ?>
    <!-- END SITEWIDE SETTINGS -->
    
    
    <!-- OFFLINE SETTINGS -->
    <?php if(page('site')){ ?>
    <tr <?php evoHide(64); ?>>
      <td colspan="2" class="tdTitle">Offline Settings</td>
    </tr>
    <tr <?php evoHide(64); ?>>
      <td width="30%" class="tdText"><strong>Turn site off? </strong></td>
      <td align="left"  class="tdText"><select name="config[offLine]" class="textbox">
          <option value="1" <?php if($config['offLine']==1) echo "selected='selected'"; ?>><?php echo $lang['admin']['yes'];?></option>
          <option value="0" <?php if($config['offLine']==0) echo "selected='selected'"; ?>><?php echo $lang['admin']['no'];?></option>
        </select></td>
    </tr>
    <tr <?php evoHide(64); ?>>
      <td width="30%" class="tdText"><strong>Allow adminstrators to view site off line? (Requires admin session)</strong></td>
      <td align="left"  class="tdText"><select name="config[offLineAllowAdmin]" class="textbox">
          <option value="1" <?php if($config['offLineAllowAdmin']==1) echo "selected='selected'"; ?>><?php echo $lang['admin']['yes'];?></option>
          <option value="0" <?php if($config['offLineAllowAdmin']==0) echo "selected='selected'"; ?>><?php echo $lang['admin']['no'];?></option>
        </select></td>
    </tr>
    <tr <?php evoHide(64); ?>>
      <td width="30%" valign="top" class="tdText"><strong><?php echo $lang['admin']['settings']['off_line_content'];?></strong></td>
      <?php /*?><td class="tdText"><textarea id="editor1" name="config[offLineContent]"><?php echo stripslashes($config['offLineContent']) ?></textarea> <script type="text/javascript"> CKEDITOR.replace( 'editor1' ); </script></td><?php */?>
      <td align="left"  class="tdText"><?php
	  	include("../includes/rte/fckeditor.php");
		$oFCKeditor = new FCKeditor('config[offLineContent]');
		$oFCKeditor->BasePath = $GLOBALS['rootRel'].'admin/includes/rte/';
		$oFCKeditor->Value = stripslashes($config['offLineContent']);
		$oFCKeditor->Create();
	  ?>
      </td>
    </tr>
    <? } ?>
    <!-- END OFFLINE SETTINGS -->
    
    
    <!-- <rf> search engine friendly mod -->
    <?php
		// if field not found and we are requested to install it then do so now
		if(isset($_GET['sefmetainstall'])){
			if($sefMetaFieldfound != 1 && $_GET['sefmetainstall'] == 1) {
				$db->misc("ALTER TABLE `".$glob['dbprefix']."CubeCart_inventory` ADD `prod_metatitle` TEXT DEFAULT '' NOT NULL;");
				$db->misc("ALTER TABLE `".$glob['dbprefix']."CubeCart_inventory` ADD `prod_metadesc` TEXT DEFAULT '' NOT NULL;");
				$db->misc("ALTER TABLE `".$glob['dbprefix']."CubeCart_inventory` ADD `prod_metakeywords` TEXT DEFAULT '' NOT NULL;");
				$db->misc("ALTER TABLE `".$glob['dbprefix']."CubeCart_inventory` ADD `prod_sefurl` TEXT DEFAULT '' NOT NULL;");
				$db->misc("ALTER TABLE `".$glob['dbprefix']."CubeCart_category` ADD `cat_metatitle` TEXT DEFAULT '' NOT NULL;");
				$db->misc("ALTER TABLE `".$glob['dbprefix']."CubeCart_category` ADD `cat_metadesc` TEXT DEFAULT '' NOT NULL;");
				$db->misc("ALTER TABLE `".$glob['dbprefix']."CubeCart_category` ADD `cat_metakeywords` TEXT DEFAULT '' NOT NULL;");
				$db->misc("ALTER TABLE `".$glob['dbprefix']."CubeCart_category` ADD `cat_sefurl` TEXT DEFAULT '' NOT NULL;");			
				$db->misc("ALTER TABLE `".$glob['dbprefix']."CubeCart_docs` ADD `doc_metatitle` TEXT DEFAULT '' NOT NULL;");
				$db->misc("ALTER TABLE `".$glob['dbprefix']."CubeCart_docs` ADD `doc_metadesc` TEXT DEFAULT '' NOT NULL;");
				$db->misc("ALTER TABLE `".$glob['dbprefix']."CubeCart_docs` ADD `doc_metakeywords` TEXT DEFAULT '' NOT NULL;");
				$db->misc("ALTER TABLE `".$glob['dbprefix']."CubeCart_docs` ADD `doc_sefurl` TEXT DEFAULT '' NOT NULL;");
			} else if($sefURLFieldfound != 1 && $_GET['sefmetainstall'] == 2){
				$db->misc("ALTER TABLE `".$glob['dbprefix']."CubeCart_inventory` ADD `prod_sefurl` TEXT DEFAULT '' NOT NULL;");			
				$db->misc("ALTER TABLE `".$glob['dbprefix']."CubeCart_category` ADD `cat_sefurl` TEXT DEFAULT '' NOT NULL;");			
				$db->misc("ALTER TABLE `".$glob['dbprefix']."CubeCart_docs` ADD `doc_sefurl` TEXT DEFAULT '' NOT NULL;");			
			}			
		}

		// check to see if the keyword fields are added to the tables, lets just test one table for one new field
		$sefdb = new db();
		$sefMetaFieldfound = 0;
		$sefURLFieldfound = 0;
		$Productfields = $db->select("DESCRIBE `".$glob['dbprefix']."CubeCart_inventory`");		
		for($i=0; $i<count($Productfields); $i++) {
			if(strcasecmp($Productfields[$i]['Field'], "prod_metatitle") == 0) {
				$sefMetaFieldfound = 1;	
			}
			if(strcasecmp($Productfields[$i]['Field'], "prod_sefurl") == 0) {
				$sefURLFieldfound = 1;	
			}			
		}


$ip_array = array('213.123.178.167', '87.194.154.19');
if (page('admin')) {
?>
    <tr>
      <td colspan="2" class="tdTitle">SEO Settings</td>
    </tr>
    <tr>
      <td width="30%" class="tdText"><strong>Analytics Google E-Commerce ID:</strong><br />
        When added will begin tracking.</td>
      <td align="left"  class="tdText"><input type="text" size="20" class="textbox" name="config[analytics_eid]" value="<?php echo $config['analytics_eid']; ?>" /></td>
    </tr>
    <tr>
      <td width="30%" class="tdText"><strong>Enable E-Commerce Tracking:</strong><br />
        Customer will need to enable this within their analytics before this will track.</td>
      <td align="left"  class="tdText"><select name="config[analytics_ecommerce]" class="textbox">
          <option value="1" <?php if($config['analytics_ecommerce']==1) echo "selected='selected'"; ?>><?php echo $lang['admin']['yes'];?></option>
          <option value="0" <?php if($config['analytics_ecommerce']==0) echo "selected='selected'"; ?>><?php echo $lang['admin']['no'];?></option>
        </select>
      </td>
    </tr>
    <tr>
      <td width="30%" class="tdText"><strong><?php echo "Use search engine friendly URL? (also turns on session killing for search engines)"?></strong></td>
      <td align="left"  class="tdText"><select name="config[sef]" class="textbox">
          <option value="1" <?php if($config['sef']==1) echo "selected='selected'"; ?>><?php echo $lang['admin']['yes'];?></option>
          <option value="0" <?php if($config['sef']==0) echo "selected='selected'"; ?>><?php echo $lang['admin']['no'];?></option>
        </select></td>
    </tr>
    <?php if($config['sef']) { ?>
    <tr>
      <td width="30%" class="tdText"><strong><?php echo "Specify server configuration. Different servers are configured differently and a certain setup might not work for you. If none of these work, please read the README.TXT for further info. (recommended if server allows it [Apache RewriteRule supported] otherwise [Apache directory 'lookback' and ForceType supported]). Note if you are NOT running apache or can't get any configuration to work then try [Use generated PHP pages]"?></strong></td>
      <td align="left"  class="tdText"><select name="config[sefserverconfig]" class="textbox">
          <option value="3" <?php if($config['sefserverconfig']==3) echo "selected='selected'"; ?>><?php echo "Use generated PHP pages";?></option>
          <option value="2" <?php if($config['sefserverconfig']==2) echo "selected='selected'"; ?>><?php echo "Apache directory 'lookback' supported only";?></option>
          <option value="1" <?php if($config['sefserverconfig']==1) echo "selected='selected'"; ?>><?php echo "Apache directory 'lookback' and ForceType supported";?></option>
          <option value="0" <?php if($config['sefserverconfig']==0) echo "selected='selected'"; ?>><?php echo "Apache RewriteRule supported";?></option>
        </select></td>
    </tr>
    <?php if($config['sefserverconfig']==3) { ?>
    <tr>
      <td width="30%" class="tdText"><strong><?php echo "To use the generated PHP pages configuration you will need to first generate the pages. If you add new docs/categories/products or modify any of their titles you MUST generate the PHP pages again to be in sync"?></strong></td>
      <td align="left" class="tdText"><a href="sef_genpages.php">Click here</a> to generate the PHP pages. This can take a while, a message will be displayed when it is complete. Please make sure you have entered your FTP settings, see the README.TXT file for info. Note this will create directories and files of permission 755 in your cube cart folder. </td>
    </tr>
    <?php } ?>
    <?php if($sefURLFieldfound == 1) { ?>
    <tr>
      <td width="30%" class="tdText"><strong>Activate custom URLs (Will add custom URL field to sitedocs, categories and products)</strong></td>
      <td align="left"  class="tdText"><select name="config[sefcustomurl]" class="textbox">
          <option value="1" <?php if($config['sefcustomurl']==1) echo "selected='selected'"; ?>><?php echo $lang['admin']['yes'];?></option>
          <option value="0" <?php if($config['sefcustomurl']==0) echo "selected='selected'"; ?>><?php echo $lang['admin']['no'];?></option>
        </select></td>
    </tr>
    <?php } 	
  } ?>
    <?php 
		if($sefMetaFieldfound == 1) { ?>
    <tr>
      <td width="30%" class="tdText"><strong><?php echo "Specify behaviour of search engine friendly url meta tags (enabling this will add new fields to the admin pages of sitedocs, categories and products). Recommended setting [Combine with global meta tags]"?></strong></td>
      <td align="left"  class="tdText"><select name="config[seftags]" class="textbox">
          <option value="3" <?php if($config['seftags']==3) echo "selected='selected'"; ?>><?php echo "Override global meta tags (description, keywords and title)";?></option>
          <option value="2" <?php if($config['seftags']==2) echo "selected='selected'"; ?>><?php echo "Override global meta tags (description and keywords only)";?></option>
          <option value="1" <?php if($config['seftags']==1) echo "selected='selected'"; ?>><?php echo "Combine with global meta tags";?></option>
          <option value="0" <?php if($config['seftags']==0) echo "selected='selected'"; ?>><?php echo "Disable feature";?></option>
        </select></td>
    </tr>
    <?php if($config['seftags']) { ?>
    <tr>
      <td width="30%" class="tdText"><strong><?php echo "Specify behaviour of titles for category and product names"?></strong></td>
      <td align="left"  class="tdText"><select name="config[sefprodnamefirst]" class="textbox">
          <option value="1" <?php if($config['sefprodnamefirst']==1) echo "selected='selected'"; ?>><?php echo "product name->sub category->category";?></option>
          <option value="0" <?php if($config['sefprodnamefirst']==0) echo "selected='selected'"; ?>><?php echo "category->sub category->product name";?></option>
        </select></td>
    </tr>
    <?php } ?>
    <?php } else { ?>
    <tr>
      <td class="tdText" colspan ="2" bgcolor="#FF9999"><strong><?php echo "If you wish to use the search engine friendly URL meta tags OR the custom search engine friendly URLs then your database tables will need to be modified with new fields. Click <a href='index.php?sefmetainstall=1'>HERE</a> to install these new fields now. (Back up your database and run at your own risk)"?></strong></td>
    </tr>
    <?php } 
	  if($sefMetaFieldfound == 1 && $sefURLFieldfound != 1) { ?>
    <tr>
      <td class="tdText" colspan ="2" bgcolor="#FF9999"><strong><?php echo "If you wish to use the custom search engine friendly URLs then your database tables will need to be modified with new fields. Click <a href='index.php?sefmetainstall=2'>HERE</a> to install these new fields now. (Back up your database and run at your own risk)"?></strong></td>
    </tr>
    <?php }
}
?>
    <!-- <rf> end mod -->
    <tr>
      <td width="30%" class="tdText">&nbsp;</td>
      <td align="left"  class="tdText"><input name="submit" type="submit" class="submit" id="submit" value="<?php echo $lang['admin']['settings']['update_settings'];?>" /></td>
    </tr>
  </table>
</form>
<script>
jQuery(function() {
	var classSwitch = 0;
	jQuery('.mainTable tr').each(function (i) { 
		if(jQuery(this).children('td').is('.tdTitle')){
			classSwitch = 0;
		}
		if(classSwitch == 0){
			jQuery(this).children('.tdText').addClass('tdEven');
			classSwitch = 1;
		}else{
		 	jQuery(this).children('.tdText').addClass('tdOdd');
			classSwitch = 0;
		}
	});
});
</script>
<?php include("../includes/footer.inc.php"); ?>
