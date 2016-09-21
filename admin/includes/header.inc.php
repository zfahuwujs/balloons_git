<?php

/*

+--------------------------------------------------------------------------
|   CubeCart v3.0.15
|   ========================================
|   by Alistair Brookbanks
|
|	CubeCart is a Trade Mark of Devellion Limited
|   Copyright Devellion Limited 2005 - 2006. All rights reserved.
|   Devellion Limited,
|
|   22 Thomas Heskin Court,
|   Station Road,
|   Bishops Stortford,
|   HERTFORDSHIRE.
|   CM23 3EE
|   UNITED KINGDOM
|
|   http://www.devellion.com
|
|	UK Private Limited Company No. 5323904
|
|   ========================================
|
|   Web: http://www.cubecart.com
|   Date: Thursday, 4th January 2007
|   Email: sales (at) cubecart (dot) com
|	License Type: CubeCart is NOT Open Source Software and Limitations Apply 
|   Licence Info: http://www.cubecart.com/site/faq/license.php
|
+--------------------------------------------------------------------------
|
|	header.inc.php
|
|   ========================================
|
|	Admin Header
|
+--------------------------------------------------------------------------

*/

phpExtension();

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $charsetIso; ?>" />
<meta http-equiv="X-UA-Compatible" value="IE=8" content="IE=EmulateIE8">

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<link href="<?php echo $GLOBALS['rootRel']; ?>admin/styles/style.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="<?php echo $GLOBALS['rootRel']; ?>admin/js/jslibrary.js"></script>

<script language="javascript" src="/js/jquery.lightbox.min.js" type="text/javascript"></script>
<link href="/skins/FixedSize/styleSheets/jquery.lightbox.css" rel="stylesheet" type="text/css" />

	<script type="text/javascript" src="/admin/js/jquery.imgareaselect-0.3.min.js"></script>

<script>
$(function() {
        $('#product').lightBox();
		$('.galleryImg').lightBox();
		
    });

</script>
<?php 

if(isset($jsScript)) { 

?>

<script language="javascript">

<?php echo $jsScript; ?>

</script>

<?php } ?>

<title>Your Website - <?php echo $lang['admin']['incs']['administration'];?> Area</title>

</head>

<body>

<?php 

if(isset($_SESSION['ccAdmin']) && strpos(dirname($_SERVER['PHP_SELF']), $_SESSION['ccAdminPath']) == 0){ ?>

<!-- start wrapping table -->

<table width="100%" border="0" cellspacing="0" cellpadding="0">

  <tr>

    <td valign="top" width="180" rowspan="3" class="tdNav">

<?php include($GLOBALS['rootDir']."/admin/includes/navigation.inc.php"); ?> 

	</td>

  </tr>

  <tr>

  <td valign="top" class="tdContent">

<!-- end wrapping table -->

<div id="topBar">

<div id="loginBar">

	<span class="txtLogin"><?php echo $lang['admin']['incs']['logged_in_as'];?> <strong><?php echo $ccAdminData[0]['username']; ?></strong> 
    <br />
    <a href="<?php echo $GLOBALS['rootRel'];?>admin/adminusers/changePass.php" class="txtLink"><?php echo $lang['admin']['incs']['change_pass'];?></a>

</div>



<div id="dateBar">

	<ul>
    	<li><a href="<?php echo $GLOBALS['rootRel']; ?>admin/logout.php" class="addNew"><?php echo $lang['admin']['incs']['logout'];?></a></li>
        <li><a href="<?php echo $GLOBALS['rootRel']; ?>admin/index.php" target="_self" class="addNew">Back-Office Home</a></li>
        <li><a href="<?php echo $GLOBALS['rootRel']; ?>index.php" target="_blank" class="addNew">Website Home</a></li>
        <?php if($config['webmail']!=''){ ?><li><a href="<?php echo $config['webmail']; ?>" target="_blank" class="addNew">Webmail</a></li><?php } ?>
        <?php if($config['analytics_uid']!=''){ ?><li ><a href="https://www.google.com/analytics/settings/" target="_blank" class="addNew">Google Analytics</a></li><?php } ?>
      </ul>

</div>

</div>

<div id="subBar">

	<?php //Order Manager
	if($_SERVER['PHP_SELF']=='/admin/orders/index.php' || $_SERVER['PHP_SELF']=='/admin/adminusers/sessionsNew.php'){ ?>
	<ul>
    	<li <?php evoHide(11); ?>><a <?php if (permission("orders", "read") == true) { ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/orders/" class="txtLink <?php if($_SERVER['PHP_SELF']=='/admin/orders/index.php'){echo 'topNavActive';} ?>"<?php } else { echo $link401; } ?>>Order Manager</a></li>
        <li <?php evoHide(11); ?>><a href="<?php echo $GLOBALS['rootRel']; ?>admin/adminusers/sessionsNew.php" class="txtLink <?php if($_SERVER['PHP_SELF']=='/admin/adminusers/sessionsNew.php'){echo 'topNavActive';} ?>">Order Changes</a></li>
    </ul>
    <?php } ?>
    
    
    <?php //Customer Details
	if($_SERVER['PHP_SELF']=='/admin/customers/email.php' || $_SERVER['PHP_SELF']=='/admin/mail_templates/index.php' || $_SERVER['PHP_SELF']=='/admin/customers/index.php' || $_SERVER['PHP_SELF']=='/admin/mail_groups/index.php' || $_SERVER['PHP_SELF']=='/admin/products/tradeDiscounts.php' || $_SERVER['PHP_SELF']=='/admin/products/tradeDiscountsEdit.php'){ ?>
	<ul>
    	<li <?php evoHide(9); ?>><a <?php if (permission("customers", "read") == true) { ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/customers/"<?php } else { echo $link401; } ?> class="txtLink <?php if($_SERVER['PHP_SELF']=='/admin/customers/index.php'){echo 'topNavActive';} ?>">Customer Details</a></li>
    	<li <?php evoHide(10); ?>><a <?php if (permission("customers", "write") == true) { ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/customers/email.php" class="txtLink <?php if($_SERVER['PHP_SELF']=='/admin/customers/email.php'){echo 'topNavActive';} ?>"<?php } else { echo $link401; } ?>>Mail Customers</a></li>
        <li <?php evoHide(98); ?>><a <?php if(permission("filemanager","write")==TRUE){ ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/mail_templates/" class="txtLink <?php if($_SERVER['PHP_SELF']=='/admin/mail_templates/index.php'){echo 'topNavActive';} ?>"<?php } else { echo $link401; } ?>>Mail Templates</a></li>
        <li <?php evoHide(101); ?>><a <?php if(permission("filemanager","write")==TRUE){ ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/mail_groups/" class="txtLink <?php if($_SERVER['PHP_SELF']=='/admin/mail_groups/index.php'){echo 'topNavActive';} ?>"<?php } else { echo $link401; } ?>>Mail Groups</a></li>
        <li <?php evoHide(9); ?>><a <?php if (permission("customers", "read") == true) { ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/products/tradeDiscounts.php"<?php } else { echo $link401; } ?> class="txtLink <?php if($_SERVER['PHP_SELF']=='/admin/products/tradeDiscounts.php'){echo 'topNavActive';} ?>">Trade Accounts</a></li>
    </ul>
    <?php } ?>
    
    <?php //General Content
	if($_SERVER['PHP_SELF']=='/admin/docs/siteDocs.php' || $_SERVER['PHP_SELF']=='/admin/docs/home.php' || $_SERVER['PHP_SELF']=='/admin/docs/news.php' || $_SERVER['PHP_SELF']=='/admin/categories/addDocCat.php' || $_SERVER['PHP_SELF']=='/admin/docs/contact.php' || $_SERVER['PHP_SELF']=='/admin/slider/index.php' || $_SERVER['PHP_SELF']=='/admin/navigation/index.php' || $_SERVER['PHP_SELF']=='/admin/docs/galleryOrder.php'){ ?>
    <ul>
    	<li <?php evoHide(18); ?>><a <?php if (permission("documents", "read") == true) { ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/docs/siteDocs.php" class="txtLink <?php if($_SERVER['PHP_SELF']=='/admin/docs/siteDocs.php'){echo 'topNavActive';} ?>"<?php } else { echo $link401;} ?>>Documents</a></li>
        <li <?php evoHide(61); ?>><a <?php if (permission("documents", "read") == true) { ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/docs/news.php" class="txtLink <?php if($_SERVER['PHP_SELF']=='/admin/docs/news.php'){echo 'topNavActive';} ?>"<?php } else { echo $link401; } ?>>News</a></li>
    	<li <?php evoHide(14); ?>><a <?php if (permission("homepage", "edit") == true) { ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/categories/addDocCat.php" class="txtLink <?php if($_SERVER['PHP_SELF']=='/admin/categories/addDocCat.php'){echo 'topNavActive';} ?>"<?php } else { echo $link401; } ?>>Document Categories</a></li>     
	    <li <?php evoHide(20); ?>><a <?php if (permission("documents", "read") == true) { ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/slider/index.php" class="txtLink <?php if($_SERVER['PHP_SELF']=='/admin/slider/index.php'){echo 'topNavActive';} ?>"<?php } else { echo $link401; } ?>>Slider Manager</a></li>
		<li <?php evoHide(23); ?>><a <?php if(permission("topNavigation","write")==TRUE){ ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/navigation/index.php" class="txtLink <?php if($_SERVER['PHP_SELF']=='/admin/navigation/index.php'){echo 'topNavActive';} ?>"<?php } else { echo $link401; } ?>>Top Navigation Order</a></li>
        <li <?php evoHide(43); ?>><a <?php if(permission("topNavigation","write")==TRUE){ ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/docs/galleryOrder.php" class="txtLink <?php if($_SERVER['PHP_SELF']=='/admin/docs/galleryOrder.php'){echo 'topNavActive';} ?>"<?php } else { echo $link401; } ?>>Gallery Order</a></li>
    </ul>
    <?php } ?>
    
    
    <?php //Category Manager
	if($_SERVER['PHP_SELF']=='/admin/categories/index.php' || $_SERVER['PHP_SELF']=='/admin/categories/categoryOrder.php'){ ?>
    <ul>
    <li <?php evoHide(34); ?>><a <?php if (permission("categories", "read") == true) { ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/categories/" class="txtLink <?php if($_SERVER['PHP_SELF']=='/admin/categories/index.php'){echo 'topNavActive';} ?>"<?php } else { echo $link401; } ?>>Category Manager</a></li>
    <li <?php evoHide(34); ?>><a <?php if (permission("categories", "write") == true) { ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/categories/categoryOrder.php" class="txtLink <?php if($_SERVER['PHP_SELF']=='/admin/categories/categoryOrder.php'){echo 'topNavActive';} ?>"<?php } else { echo $link401; } ?>>Reorder Categories</a></li>
    </ul>
    <?php } ?>
    
    
    <?php //makes and models
	if($_SERVER['PHP_SELF']=='/admin/makes/makes.php' || $_SERVER['PHP_SELF']=='/admin/makes/models.php'){ ?>
    <ul>
    <li><a <?php if (permission("categories", "read") == true) { ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/makes/makes.php" class="txtLink <?php if($_SERVER['PHP_SELF']=='/admin/makes/makes.php'){echo 'topNavActive';} ?>"<?php } else { echo $link401; } ?>>Makes</a></li>
    <li><a <?php if (permission("categories", "write") == true) { ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/makes/models.php" class="txtLink <?php if($_SERVER['PHP_SELF']=='/admin/makes/models.php'){echo 'topNavActive';} ?>"<?php } else { echo $link401; } ?>>Models</a></li>
    </ul>
    <?php } ?>
    
    
    
    
    <?php //Product Manager
	if($_SERVER['PHP_SELF']=='/admin/products/index.php' || $_SERVER['PHP_SELF']=='/admin/products/options.php' || $_SERVER['PHP_SELF']=='/admin/products/reviews.php' || $_SERVER['PHP_SELF']=='/admin/products/stockControl.php'){ ?>
    <ul>
    <li <?php evoHide(35); ?>><a <?php if (permission("products", "read") == true) { ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/products/" class="txtLink <?php if($_SERVER['PHP_SELF']=='/admin/products/index.php'){echo 'topNavActive';} ?>"<?php } else { echo $link401; } ?>>Product Manager</a></li>
         <li <?php evoHide(29); ?>><a <?php if (permission("products", "write") == true) { ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/products/options.php" class="txtLink <?php if($_SERVER['PHP_SELF']=='/admin/products/options.php'){echo 'topNavActive';} ?>"<?php } else { echo $link401; } ?>>Product Options</a></li>
         <li <?php evoHide(30); ?>><a <?php if (permission("categories", "write") == true) { ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/products/reviews.php" class="txtLink <?php if($_SERVER['PHP_SELF']=='/admin/products/reviews.php'){echo 'topNavActive';} ?>"<?php } else { echo $link401; } ?>>Product Reviews</a></li>
         <li <?php evoHide(5); ?>><a <?php if (permission("categories", "write") == true) { ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/products/stockControl.php" class="txtLink <?php if($_SERVER['PHP_SELF']=='/admin/products/stockControl.php'){echo 'topNavActive';} ?>"<?php } else { echo $link401; } ?>>Stock Control</a></li>
    </ul>
    <?php } ?>
    
    
    <?php //File Manager
	if($_SERVER['PHP_SELF']=='/admin/filemanager/index.php' || $_SERVER['PHP_SELF']=='/admin/filemanager_pdf/index.php' || $_SERVER['PHP_SELF']=='/admin/filemanager/upload.php'){ ?>
    <ul>
        <li <?php evoHide(44); ?>><a <?php if (permission("filemanager", "read") == true) { ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/filemanager/" class="txtLink <?php if($_SERVER['PHP_SELF']=='/admin/filemanager/index.php'){echo 'topNavActive';} ?>"<?php } else { echo $link401; } ?>>Image Manager</a></li>
        <li <?php evoHide(45); ?>><a <?php if(permission("filemanager","read")==TRUE){ ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/filemanager_pdf/" class="txtLink <?php if($_SERVER['PHP_SELF']=='/admin/filemanager_pdf/index.php'){echo 'topNavActive';} ?>"<?php } else { echo $link401; } ?>>PDF Manager</a></li>
    </ul>
    <?php } ?>
    
    
    <?php //CSV Options
	if($_SERVER['PHP_SELF']=='/admin/csvImport/products.php' || $_SERVER['PHP_SELF']=='/admin/csvExport/products.php' || $_SERVER['PHP_SELF']=='/admin/csvExport/categories.php' || $_SERVER['PHP_SELF']=='/admin/csvExport/customers.php' || $_SERVER['PHP_SELF']=='/admin/csvExport/productsModel.php' || $_SERVER['PHP_SELF']=='/admin/csvExport/productOptions.php'){ ?>
    <ul>
      <li <?php evoHide(51); ?>><a <?php if(permission("csv","write")==TRUE){ ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/csvImport/products.php" class="txtLink <?php if($_SERVER['PHP_SELF']=='/admin/csvImport/products.php'){echo 'topNavActive';} ?>"<?php } else { echo $link401; } ?>>Import Products</a></li>
      <li <?php evoHide(52); ?>><a <?php if(permission("csv","write")==TRUE){ ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/csvExport/productsModel.php" class="txtLink"<?php } else { echo $link401; } ?>>Product Structure</a></li>
      <li <?php evoHide(50); ?>><a <?php if(permission("csv","write")==TRUE){ ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/csvExport/products.php" class="txtLink"<?php } else { echo $link401; } ?>>Export Products</a></li>
      <li <?php evoHide(49); ?>><a <?php if(permission("csv","write")==TRUE){ ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/csvExport/productOptions.php" class="txtLink"<?php } else { echo $link401; } ?>>Export Options</a></li>
      <li <?php evoHide(47); ?>><a <?php if(permission("csv","write")==TRUE){ ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/csvExport/categories.php" class="txtLink"<?php } else { echo $link401; } ?>>Export Categories</a></li>
      <li <?php evoHide(48); ?>><a <?php if(permission("csv","write")==TRUE){ ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/csvExport/customers.php" class="txtLink"<?php } else { echo $link401; } ?>>Export Customers</a></li>
      </ul>
      <?php } ?>
      
      <?php //General Settings
	if($_SERVER['PHP_SELF']=='/admin/settings/index.php' || $_SERVER['PHP_SELF']=='/admin/settings/countries.php' ){ ?>
    <ul>
      <li><a <?php if (permission("settings", "read") == true) { ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/settings/index.php?page=info" class="txtLink <?php if($_GET['page']=='info'){echo 'topNavActive';} ?>"<?php } else { echo $link401; } ?>>Site Information</a></li>
      <li <?php evoHide(6); ?>><a <?php if (permission("settings", "read") == true) { ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/settings/index.php?page=prodMod" class="txtLink <?php if($_GET['page']=='prodMod'){echo 'topNavActive';} ?>"<?php } else { echo $link401; } ?>>Product Modules</a></li>
      <li <?php evoHide(92); ?>><a <?php if (permission("settings", "read") == true) { ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/settings/index.php?page=shop" class="txtLink <?php if($_GET['page']=='shop'){echo 'topNavActive';} ?>"<?php } else { echo $link401; } ?>>Shop Settings</a></li>
      <li><a <?php if (permission("settings", "read") == true) { ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/settings/index.php?page=site" class="txtLink <?php if($_GET['page']=='site'){echo 'topNavActive';} ?>"<?php } else { echo $link401; } ?>>Site Wide Settings</a></li>
      <li <?php evoHide(93); ?>><a <?php if (permission("settings", "read") == true) { ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/settings/index.php?page=image" class="txtLink <?php if($_GET['page']=='image'){echo 'topNavActive';} ?>"<?php } else { echo $link401; } ?>>Image Settings</a></li>
     <li <?php evoHide(103); ?>><a <?php if (permission("settings", "read") == true) { ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/settings/countries.php" class="txtLink <?php if($_SERVER['PHP_SELF']=='/admin/settings/countries.php'){echo 'topNavActive';} ?>"<?php } else { echo $link401; } ?>>Country List</a></li>
     <?php if(evo()){ ?> <li><a <?php if (permission("settings", "read") == true) { ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/settings/index.php?page=admin" class="txtLink <?php if($_GET['page']=='admin'){echo 'topNavActive';} ?>"<?php } else { echo $link401; } ?>>Developer Settings</a></li><?php } ?>
      </ul>
      <?php } ?>
      
      
      <?php //Admin Settings
	if($_SERVER['PHP_SELF']=='/admin/adminusers/administrators.php' || $_SERVER['PHP_SELF']=='/admin/adminusers/sessions.php' || $_SERVER['PHP_SELF']=='/admin/categories/fixCatCount.php' || $_SERVER['PHP_SELF']=='/admin/fixthumbs/fixthumbs.php'){ ?>
      <ul>
      	<li <?php evoHide(53); ?>><a <?php if (permission("administrators", "read") == true) { ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/adminusers/administrators.php" class="txtLink <?php if($_SERVER['PHP_SELF']=='/admin/adminusers/administrators.php'){echo 'topNavActive';} ?>"<?php } else { echo $link401; } ?>>Admin Accounts</a></li>
        <li <?php evoHide(56); ?>><a href="<?php echo $GLOBALS['rootRel']; ?>admin/adminusers/sessions.php" class="txtLink <?php if($_SERVER['PHP_SELF']=='/admin/adminusers/sessions.php'){echo 'topNavActive';} ?>">Session Log</a></li>
        <li <?php evoHide(54); ?>><a <?php if (permission("categories", "write") == true) { ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/categories/fixCatCount.php" class="txtLink <?php if($_SERVER['PHP_SELF']=='/admin/categories/fixCatCount.php'){echo 'topNavActive';} ?>"<?php } else { echo $link401; } ?>>Fix Category Count</a></li>
         <li <?php evoHide(55); ?>><a <?php if (permission("categories", "write") == true) { ?>href="<?php echo $GLOBALS['rootRel']; ?>admin/fixthumbs/fixthumbs.php" class="txtLink <?php if($_SERVER['PHP_SELF']=='/admin/fixthumbs/fixthumbs.php'){echo 'topNavActive';} ?>"<?php } else { echo $link401; } ?>>Fix Thumbs</a></li>
      </ul>
      <?php } ?>
    
</div>

<!-- start of admin content -->

<div id="contentPad">

<?php } ?>