<?php
/*-------------------------------------------------------------------------------------------
|   ____ __        __  ____               _         _        
|  / ___|\ \      / / / ___|   ___  _ __ (_) _ __  | |_  ___ 
|  \___ \ \ \ /\ / /  \___ \  / __|| '__|| || '_ \ | __|/ __|
|   ___) | \ V  V /    ___) || (__ | |   | || |_) || |_ \__ \
|  |____/   \_/\_/    |____/  \___||_|   |_|| .__/  \__||___/
|                                           |_|              
|
|   CubeCart CSV Import Module
|   December 2, 2006
|   Version 1.0.9
|   Copyright 2005-2006 - SWScripts.com and Sir William.  All rights reserved.
|   http://www.swscripts.com/
|   bill@swscripts.com
|  	
|   CubeCart and CubeCart3 Copyright Devellion Limited
|   http://www.devellion.com/
-------------------------------------------------------------------------------------------*/

if (ereg(".inc.php",$HTTP_SERVER_VARS['PHP_SELF']) OR ereg(".inc.php",$_SERVER['PHP_SELF'])) {
	echo "<html>\r\n<head>\r\n<title>Forbidden 403</title>\r\n</head>\r\n<body><h3>Forbidden 403</h3>\r\nThe document you are requesting is forbidden.\r\n</body>\r\n</html>";
	exit;
}

// static variables (is that possible?)
$catfile = "./import_files/categories";
$prodfile = "./import_files/products";

// $default -- This is an array to store default insert values for any fields that
//             aren't specified in the input file.  For instance if you never use
//             Stock Level, then set this to 0.  These are the same as the DB defaults.
$default['showFeatured'] = 1;
$default['taxType'] = 1;
$default['useStockLevel'] = 1;

// $enablepurges -- If you set these to 'TRUE', you will be able to purge your database
//                  completely of categories or products.
//                  Do not do this unless you are willing to empty your database.
$enablepurgecats = FALSE;
$enablepurgeprods = FALSE;

// $enablebackuptions -- This will stop the script from auto-deleting the backups when
//                       the session is timed out.  It will also allow multiple people
//                       to log in without causing problems and erasing the backups.
$enablebackupoptions = FALSE;

// $fillallfathers -- If this is set to 'TRUE', your products will also be placed in all
//                    parent categories above it's primary category.  This allows your
//                    category selections to be a "drill down" or narrowing selection
//                    instead of changing.  In other words, the main category will show
//                    all direct products and ALL products from its sub-categories.  So
//                    as you go into sub-cats, you'll see fewer and fewer products
//                    thereby narrowing your search.
$fillallfathers = FALSE;

// $basestep -- This is the number of file lines or products imported at a time.  When 
//              this number is reached, the script will stop and have you click a link
//              to go to the next set of products and pick up where it left off.  This
//              is here to keep the import script from timing out.  You should be able
//              to import thousands at a time, but on a slow or very busy server, you
//              may need to set this to a lower number.
$basestep = 5000;

// The $nameLookup array is a list of more easily readable names/descriptions of the
// current inventory fields in the database.  I've included all current CubeCart fields
// as well as a few added by some of the more common mods such as Rukiman's SEO mod.
// If you have a custom field added, you can use this to change the displayed name from
// the field name to something more descriptive.

$nameLookup = array(
	'productId' => 'Product ID',
	'productCode' => 'Product Code',
	'quantity' => 'Quantity',
	'description' => 'Description',
	'image' => 'Image',
	'noImages' => 'Number of Images',
	'price' => 'Price',
	'name' => 'Product Name',
	'cat_id' => 'Category(s)',
	'popularity' => 'Popularity',
	'sale_price' => 'Sale Price',
	'stock_level' => 'Stock Level',
	'useStockLevel' => 'Use Stock Level',
	'digital' => 'Digital',
	'digitalDir' => 'Digital Directory',
	'prodWeight' => 'Product Weight',
	'taxType' => 'Tax Type',
	'showFeatured' => 'Show Featured',
	'prod_metatitle' => 'SEO Title',
	'prod_metadesc' => 'SEO Description',
	'prod_metakeywords' => 'SEO Keywords'
);

?>