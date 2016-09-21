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

|	navigation.inc.php

|   ========================================

|	Admin Navigation links

+--------------------------------------------------------------------------

*/

phpExtension();
$link401 = "href=\"javascript:alert('" . $lang['admin']['nav']['permission_error'] ."');\" class=\"txtNullLink\"";

//sub-pages included in active link in left navigation
$admin1=array('/admin/settings/index.php');
$admin2=array('/admin/adminusers/administrators.php','/admin/adminusers/sessions.php','/admin/categories/fixCatCount.php','/admin/fixthumbs/fixthumbs.php');
$cust1=array('/admin/orders/index.php','/admin/adminusers/sessionsNew.php');
$cust2=array('/admin/customers/index.php','/admin/customers/email.php','/admin/mail_templates/index.php','/admin/mail_groups/index.php');
$cont1=array('/admin/docs/siteDocs.php','/admin/docs/news.php','/admin/categories/addDocCat.php','/admin/slider/index.php','/admin/navigation/index.php');
$cont2=array('/admin/filemanager/index.php','/admin/filemanager/index.php');
$cata1=array('/admin/categories/index.php','/admin/categories/categoryOrder.php');
$makes=array('/admin/makes/makes.php','/admin/makes/models.php');
$cata2=array('/admin/products/index.php','/admin/products/options.php','/admin/products/reviews.php','/admin/products/stockControl.php');
$cata3=array('/admin/brands/index.php');
$cata4=array('/admin/customers/coupons.php');
$cata5=array('/admin/csvImport/products.php');
$shop1=array('/admin/settings/currency.php');
$shop2=array('/admin/settings/tax.php');
$shop3=array('/admin/modules/shipping/index.php');
$shop4=array('/admin/modules/gateway/index.php');
$dev1=array('/admin/adminusers/evoHide.php');
?>

<table width="180" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left" class="noPad"><img src="<?php echo $GLOBALS['rootRel']; ?>admin/images/evo/adminLogo.png" alt="" width="177" height="57" border="0" title="" /></td>
  </tr>
  <tr>
    <td class="navMenu">
     
      <!-- ADMIN -->
      <div <?php evoHide(1); ?>>
      <?php if (permission("settings", "read") == true) { ?>
      <span class="navTitle">Admin</span>
      <ul>
        <li><a <?php if (permission("settings", "read") == true) { ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/settings/index.php?page=info" class="txtLink <?php if(in_array($_SERVER['PHP_SELF'],$admin1)){echo 'leftNavActive';} ?>"<?php } else { echo $link401; } ?>>General Settings</a></li>
        <li <?php evoHide(53); ?>><a <?php if(permission("administrators", "read") == true){ ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/adminusers/administrators.php" class="txtLink <?php if(in_array($_SERVER['PHP_SELF'],$admin2)){echo 'leftNavActive';} ?>"<?php }else{ echo $link401;} ?>>Admin Settings</a></li>
      </ul>
      <?php } ?>
      </div>
      <!-- END ADMIN -->
      
      
      <!-- CUSTOMERS -->
      <div <?php evoHide(8); ?>>
      <?php if (permission("customers", "read") == true or permission("orders", "read") == true) { ?>
      <span class="navTitle"><?php echo $lang['admin']['nav']['customers']; ?></span>
      <ul>
      	<li <?php evoHide(11); ?>><a <?php if (permission("orders", "read") == true) { ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/orders/" class="txtLink <?php if(in_array($_SERVER['PHP_SELF'],$cust1)){echo 'leftNavActive';} ?>"<?php } else { echo $link401; } ?>>Order Manager</a></li>
        <li <?php evoHide(9); ?>><a <?php if (permission("customers", "read") == true) { ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/customers/"<?php } else { echo $link401; } ?> class="txtLink <?php if(in_array($_SERVER['PHP_SELF'],$cust2)){echo 'leftNavActive';} ?>">Customer Details</a></li>
      </ul>
      <?php } ?>
      </div>
      <!-- END CUSTOMERS -->
      
      
      <!-- CONTENT -->
      
      <div <?php evoHide(12); ?>>
      <?php if (permission("homepage", "read") == true or permission("documents", "read") == true) { ?>
      <span class="navTitle">Content</span>
      <ul>
        <li <?php evoHide(18); ?>><a <?php if (permission("documents", "read") == true) { ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/docs/siteDocs.php" class="txtLink <?php if(in_array($_SERVER['PHP_SELF'],$cont1)){echo 'leftNavActive';} ?>"<?php } else { echo $link401;} ?>>General Content</a></li>
        <li <?php evoHide(44); ?>><a <?php if (permission("filemanager", "read") == true) { ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/filemanager/" class="txtLink <?php if(in_array($_SERVER['PHP_SELF'],$cont2)){echo 'leftNavActive';} ?>"<?php } else { echo $link401; } ?>>File Manager</a></li>
      </ul>
      <?php } ?>
      </div>
      <!-- END CONTENT -->
      
      
      <!-- CATALOGUE -->
      <div <?php evoHide(24); ?>>
      <?php if (permission("products", "read") == true or permission("categories",
"read") == true) { ?>
	  <span class="navTitle">Catalogue</span>
      <ul>
      	 <!--<li <?php evoHide(34); ?>><a <?php if (permission("categories", "read") == true) { ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/makes/makes.php" class="txtLink <?php if(in_array($_SERVER['PHP_SELF'],$makes)){echo 'leftNavActive';} ?>"<?php } else { echo $link401; } ?>>Makes &amp; Models</a></li>-->
         <li <?php evoHide(34); ?>><a <?php if (permission("categories", "read") == true) { ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/categories/" class="txtLink <?php if(in_array($_SERVER['PHP_SELF'],$cata1)){echo 'leftNavActive';} ?>"<?php } else { echo $link401; } ?>>Category Manager</a></li>
         <li <?php evoHide(35); ?>><a <?php if (permission("products", "read") == true) { ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/products/" class="txtLink <?php if(in_array($_SERVER['PHP_SELF'],$cata2)){echo 'leftNavActive';} ?>"<?php } else { echo $link401; } ?>>Product Manager</a></li>
         <li <?php evoHide(25); ?>><a <?php if (permission("brands", "read") == true) { ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/brands/" class="txtLink <?php if(in_array($_SERVER['PHP_SELF'],$cata3)){echo 'leftNavActive';} ?>"<?php } else { echo $link401; } ?>>Brand Manager</a></li>
         <li <?php evoHide(39); ?>><a <?php if (permission("gateways", "read") == true) { ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/customers/coupons.php" class="txtLink <?php if(in_array($_SERVER['PHP_SELF'],$cata4)){echo 'leftNavActive';} ?>"<?php } else { echo $link401; } ?>>Discount Manager</a></li>
         <li <?php evoHide(51); ?>><a <?php if(permission("csv","write")==TRUE){ ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/csvImport/products.php" class="txtLink <?php if(in_array($_SERVER['PHP_SELF'],$cata5)){echo 'leftNavActive';} ?>"<?php } else { echo $link401; } ?>>CSV Options</a></li>
         </ul>
      <?php } ?>
      </div>
      <!-- END CATALOGUE -->
      
      
      <!-- SHOP -->
      <div <?php evoHide(36); ?>>
      <?php if (permission("shipping", "read") == true or permission("gateways",
"read") == true) { ?>
      <span class="navTitle">Shop Set-Up</span>
      <ul>
      	 <li <?php evoHide(37); ?>><a <?php if (permission("settings", "read") == true) { ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/settings/currency.php" class="txtLink <?php if(in_array($_SERVER['PHP_SELF'],$shop1)){echo 'leftNavActive';} ?>"<?php } else { echo $link401; } ?>>Currency Manager</a></li>
         <li <?php evoHide(41); ?>><a <?php if (permission("settings", "read") == true) { ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/settings/tax.php" class="txtLink <?php if(in_array($_SERVER['PHP_SELF'],$shop2)){echo 'leftNavActive';} ?>"<?php } else { echo $link401; } ?>>Tax Manager</a></li>
         <li <?php evoHide(38); ?>><a <?php if (permission("shipping", "read") == true) { ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/modules/shipping/" class="txtLink <?php if(in_array($_SERVER['PHP_SELF'],$shop3)){echo 'leftNavActive';} ?>"<?php } else { echo $link401; } ?>>Delivery Set-Up</a></li>
         <li <?php evoHide(40); ?>><a <?php if (permission("gateways", "read") == true) { ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/modules/gateway/" class="txtLink <?php if(in_array($_SERVER['PHP_SELF'],$shop4)){echo 'leftNavActive';} ?>"<?php } else { echo $link401; } ?>>Gateway Set-Up</a></li>
      </ul>
      <?php } ?>
      </div>
      <!-- END SHOP -->
      
      
      <!-- HELP DOCUMENTS -->
      <?php /*?><?php if (permission("homepage", "read") == true or permission("documents", "read") == true) { ?>
      <span class="navTitle"><?php echo $lang['admin']['nav']['help']; ?></span>
      <ul>
        <li><a <?php if (permission("homepage", "edit") == true) { ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/help/index.php" class="txtLink"<?php } else {
    echo $link401;
} ?>><?php echo
$lang['admin']['nav']['help_documents']; ?></a></li>
      </ul>
      <?php } ?><?php */?>
      <!-- END HELP DOCUMENTS -->
      
      
      <?php if(evo()==true){ ?>
      <span class="navTitle">Developers Only</span>
      <ul>
      	<li><a <?php if (permission("categories", "write") == true) { ?>href="<?php echo $GLOBALS['rootRel'];?>admin/adminusers/evoHide.php" class="txtLink <?php if(in_array($_SERVER['PHP_SELF'],$dev1)){echo 'leftNavActive';} ?>"<?php } else { echo $link401; } ?>>Package Manager</a></li>
      </ul>
      <?php }?>
    </td>
  </tr>
</table>
