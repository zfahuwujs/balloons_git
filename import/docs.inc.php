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

Heading();

$linebreak = "<br><img src=\"../images/general/px.gif\" height=\"8\" width=\"1\"><br>";
?>
<table border="0" cellpadding="0" cellspacing="4" width="100%">
<tr>
<td width="165" valign="top">
<div class="navbar">
<div class="title">Actions</div>
<a href="import.php">Module Home</a><br><img src="../images/general/px.gif" height="7" width="1"><br>
<a href="?action=documentation">Documentation</a><br>
<a href="?action=catlist">Display Category List</a>
</div>
</td>
<td valign="top">
<p class="pageTitle" style="text-align: center;">Module Documentation</p>
<div class="copyText">
<div class="docSection">
<a href="#about">About This Module</a><br>
<a href="#csvinfo">What's a CSV File &amp; How Do I Get One?</a><br>
<a href="#prepare">Preparing Your Files</a><br>
<a href="#configure">Module Configuration</a><br>
<a href="#test">Test Product File</a><br>
<a href="#import">Import Products</a><br>
<a href="#importcats">Import Categories</a><br>
<a href="#other">Other Features</a><br>
<a href="#config">The Config File</a><br>
</div>
<p>

<div class="docSection"><a name="about"> </a>
About This Module</div>
This module is designed to allow you to import large numbers of products into your CubeCart 3.x.x database at one time. The idea is to save you time when both setting up your store and when adding products during routine maintenance.
It works by taking a pre-prepared file that you either get from a supplier or distributor or that you create manually and adds the information it contains to your existing CubeCart 3.x.x database.

<div class="docSection"><a name="csvinfo"> </a>
What's a CSV File &amp; How Do I Get One?</div>
CSV stands for "Comma Separated Values".  It's used kind of generically to describe a flat file list of data.
The files themselves are really just plain text files with either a .txt or .csv extension.  They can be created/edited
with any text editor including Notepad (Windows) and TextEdit (Mac).  However most people will likely use Microsoft
Excel or StarOffice to create the file.

<?php echo $linebreak; ?>
It is best to think ahead before writing your file to avoid any hiccups. If you are using Excel you have the option to save as a tab delimited .txt file or a comma delimited .csv file. Therefore you need to be careful what information you type in each field.
If you will need commas in your descriptions you should save as a tab delimited .txt file as Excel won't readily
surround output fields in quotes.  

<div class="docSection"><a name="prepare"> </a>
Preparing Your Files</div>
The script requires you to name your files <b>products.txt</b> and <b>categories.txt</b> (or products.csv and
categories.csv).  These files will need to be uploaded to the import_files subdirectory in the import folder via FTP.
<i>Future versions will include the ability to upload via web browser</i>.

<?php echo $linebreak; ?>
When using Excel or other spreadsheet program, most people like to have headings at the top of their columns, this helps to remind you of what information is required in each cell. This information is NOT required by the module so can be left out if you do not want to add it. It does help to keep your output file "human-readable".  You can leave the header row and tell the module it's there.  It will then ignore the first line.

<?php echo $linebreak; ?>
The product information fields that the script can process are name, product code, price, image name, description, category, sale price, stock level, use stock level, weight, tax type and show featured.  The script does not require
any of these fields, but I'd recommend a bare minimum of name, price, description and category.

<?php echo $linebreak; ?>
Here is a breakdown of all the fields and what they are.

<?php echo $linebreak; ?>
<table class="copyText" border="0" cellpadding="5" cellspacing="0">
<tr><td valign="top" width="100" class="tdOdd">Name</td>
<td class="tdOdd">The product name. Hint: keep it reasonably short and don't use HTML.</td></tr>

<tr><td valign="top" class="tdEven">Product Code</td>
<td class="tdEven">The product code and can be entered as any combination of numbers and letters or can be left blank in which case an automatic product code will be generated just as in CubeCart.</td></tr>

<tr><td valign="top" class="tdOdd">Price</td>
<td class="tdOdd">Quite simply the product's price. Should be entered as a decimal number rounded to two places, and should not contain any currency symbols.</td></tr>

<tr><td valign="top" class="tdEven">Image</td>
<td class="tdEven">If you wish to use an image for the product, simply put the filename here.  You'll then need to upload to CubeCart via either FTP to the images/uploads/ folder or using the upload image feature in the admin section of your CubeCart store. Example: sampleimagename.jpg<br><i>Note: ALL Linux/Unix servers have CaSe SeNsItIvE file names.</i></td></tr>

<tr><td valign="top" class="tdOdd">Description</td>
<td class="tdOdd">Items description. This can be quite long and detailed including HTML markup.  But be mindful as mentioned earlier in this document with commas or tabs.</td></tr>

<tr><td valign="top" class="tdEven">Category</td>
<td class="tdEven">The product's category or categories -- should be entered as a number. You can use the module's "Display Category List" feature to get a full category list with their respective IDs.</td></tr>

<tr><td valign="top" class="tdOdd">Sale Price</td>
<td class="tdOdd">The price for this item when you plan to use the "Individual Sale Price" sale mode that CubeCart 3.x.x offers. This should be entered as a decimal number rounded to two places, and should not contain any currency symbols.</td></tr>

<tr><td valign="top" class="tdEven">Stock Level</td>
<td class="tdEven">If you plan on using CubeCart's stock level feature enter the quantity on hand stock level here. Be sure to use only whole numbers.</td></tr>

<tr><td valign="top" class="tdOdd">Use Stock Level</td>
<td class="tdOdd">If you are using the stock level feature for this product, enter a 1 in this field or a 0 if you are not.  The global default value for this is adjustable in the <b>config.inc.php</b> file for the module.</td></tr>

<tr><td valign="top" class="tdEven">Weight</td>
<td class="tdEven">If you are using any of the shipping modules that come with CubeCart 3.x.x that require information on a product's weight, enter it here.  Remember to use the value appropriate to the settings you have in your store.</td></tr>

<tr><td valign="top" class="tdOdd">Tax Type</td>
<td class="tdOdd">If you're using taxes, this field will let you choose the tax type as defined in the "Taxes" section of your Store Admin.  The global default value for this is adjustable in the <b>config.inc.php</b> file for the module.</td></tr>

<tr><td valign="top" class="tdEven">Show Featured</td>
<td class="tdEven">Just as in the CubeCart Product page, this will set whether the product will be allowed to be shown in the "Featured" box in your store -- 1 for yes, 0 for no.  The global default value for this is adjustable in the <b>config.inc.php</b> file for the module.</td></tr>

<tr><td valign="top" class="tdEven">Digital</td>
<td class="tdEven">This specifies if the product is Digital file or not -- 1 for yes, 0 for no.  The database default for this field is '0'.</td></tr>
</table>

<?php echo $linebreak; ?>
Once you have populated your Excel table following these guidelines you are ready to save your file. Save as either a .txt or .csv file, bearing in mind the different file type options mentioned earlier.

<div class="docSection">Using a text editor</div>
This way takes more time to do, so I would recommend only doing this if you do not have a copy of Excel.
Using the same rules as the Excel method for the contents of each field, construct a text document like the examples
in this file: <a href="./import_files/examples.txt" target="_new">examples.txt</a>.


<div class="docSection"><a name="usage"> </a>
Using the Program</div>
Once you have properly prepared and checked your file, you need to upload your products and/or categories files
to the <b>import_files</b> folder in the <b>import</b> directory.  The script will tell you if it can find the file(s).

<?php echo $linebreak; ?>
The main page of the Import Module tells you the current database product and category count and import file line counts.  On the right, you'll see the backup status.  On the left you have the actions you can perform.

<?php echo $linebreak; ?>
If you haven't done so already, <b>PLEASE MAKE A FULL BACKUP OF YOUR STORE AND THE DATABASE AT THIS POINT!</b>

<?php echo $linebreak; ?>
The module will create a temporary backup of your database so that you can restore your products and categories should something go wrong. THIS IS NOT A PERMANENT BACKUP, but rather just a helpful method to get you out of a bind should your data have an error that thrashes your database. If that should ever happen, simply click the Restore Database link, and your data will be put back exactly as it was before you started monkeying around with it. :o) Be advised that this backup will be deleted next time you log in to the Import Module. So don't try to rely on it. If you make a mistake, restore it straight away.

<?php echo $linebreak; ?>
Before you can begin any import operations, you must click the <b>Backup Database</b> link in the Actions meny.  The script will create backup tables then show you more actions and your current backup status.

<?php echo $linebreak; ?>
Next it is important to configure the import module so click on <b>Configure Options</b> in the Actions box.

<div class="docSection"><a name="configure"> </a>
Module Configuration</div>
Since your files may come with fields in all sorts of different orders, the module is configurable to read the fields in the order you have them in.  The available fields are displayed down the left.  Just use the Up and Down links on the right to change the order to match your file.

<?php echo $linebreak; ?>
There are two columns of checkboxes labeled "In File" and "Include in Import".  For every field in your file, you'll need to put a check in the "In File" column.  For only the fields you wish to import, you simply check the "Include in Import" box.  Be advised that the only fields that really matter are the Include fields -- they will need to be properly mapped to your file's fields.  But if you have extra fields in your file that you don't need, any field can be used to denote that position to the import module.

<?php echo $linebreak; ?>
If you have a header row in your file (column titles) check the "Header Row in File" box. This will make the module ignore the first row of your file.

<?php echo $linebreak; ?>
If you enclosed your fields in quotes as mentioned above check the "Fields Enclosed in Quotes?" box to allow the module to read your file correctly.

<?php echo $linebreak; ?>
Next set the "Field Delimiter" to either Comma or Tab to match your file, and lastly mark which file extension you are using. Be aware that these two settings affect both the <b>products</b> and the <b>categories</b> files whereas everything else above affects the products only.

<?php echo $linebreak; ?>
Click "<b>Update Module Config</b>" to save your settings.

<?php echo $linebreak; ?>
As of version 1.0.9, there is a new option.  In the Config section, you'll see a new option in red entitled "<b>Relearn Inventory Fields</b>".  This option will store your current field preferences then dump your current config and rescan your CubeCart_inventory table to to save your settings.  You do this for times when you may add a new field to your inventory table for cost or retail price, etc.

<?php echo $linebreak; ?>
Also now, there is a small red 'X' to the left of each config field line.  This will delete that field from the config table if you don't use or care about that field.  The purpose of this is mainly just to make things easier to read with when you're looking at it.  By not displaying the fields you don't use, it's easier to visualize the ones you do.  Of course if you decide you need one of the fields you don't see, you can do a "Relearn" and it will bring back all the fields in your inventory table.

<?php echo $linebreak; ?>
We can now check that the module is reading your file correctly.

<div class="docSection"><a name="test"> </a>
Test Product File</div>
Click on the <b>Test Product File</b> link in the Actions menu to start the test. A page will be displayed with data from the first 4 of your products.  Check to make sure that the information is in the correct fields.  Simply click the link at the bottom to return to the main page.

<?php echo $linebreak; ?>
Only once you are sure that your file is being read correctly and you have a full backup should you continue to upload your file.

<div class="docSection"><a name="import"> </a>
Import Products</div>
Click the link and the module will begin uploading your file.  By default, the script will insert 5000 items at a time, requiring you to click "Next" to continue through the file until it finishes.  When it reaches the end, it will  confirm that your products were uploaded successfully. <b><u>DO NOT HIT THE BACK BUTTON</u></b>. Use the link to return to the main page.

<?php echo $linebreak; ?>
At this point open another browser window/tab and visit your store to verify that your items have been uploaded and all information is correct.  You can then purge (delete) the temporary backup file by clicking "<b>Purge Backups</b>".  If your products have not been added as you wanted or there are any problems simply click the "<b>Restore Database</b>" link in the main menu. This will return your database to just as it was before you started to upload.

<?php echo $linebreak; ?>
I've tested the module successfully with 40,000 items in the input file.  I tried 60,000, but reached the upper memory limit of the PHP installation and the script wouldn't even run.  So your results may vary.  If you have a very high number of products, I'd suggest doing them in two or more batches perhaps so as not to exceed the working memory limits of PHP -- 8MB per instance is the default I believe in most installations.

<div class="docSection"><a name="importcats"> </a>
Import Categories</div>
The module will also import categories for you.  The setup of the categories file is a bit different than the products file as it must be layed out in a specific way in order to work.  To illustrate the proper format, I've included a sample file here: <a href="./import_files/categories-example.txt" target="_new">categories-example.txt</a>.  There are a couple things to keep in mind.  First off, the first column, the cat_id, MUST be sequentially numbered and MUST start with next available category id in your database.  So if you have 8 categories now, your new file must start with 9.  If your database is empty, you would start numbering at 1.  You must NOT skip any numbers as the system will break.  This is not a fault of the Import Module but rather a feature of CubeCart.


<div class="docSection"><a name="other"> </a>
Other Features</div>
The CSV Import Module allows you to put your products in multiple categories at once simply by including the additional cats in the Category field. Separate the category IDs with the tilde '~'.  I chose that as it won't be interpreted as anything else by Excel. The dash, decimal and slash all have meanings of their own and will just confuse things. So to put an item in 5 categories, just put this in the category field: 1~4~36~18~12. Keep in mind that the first entry is the main entry for that product (duh).

<div class="docSection">Display Category List</div>
Another handy feature of the module is the "<b>Display Category List</b>" page. This page will generate a hierarchical list of all your categories with their IDs.  This is useful to print out and have handy when writing your products.csv or products.txt file.  <i>If you have my <a href="http://www.swscripts.com/index.php?act=viewProd&productId=2" target="_new"><b>Category/Product Sort Mod</b></a>, the list will be ordered the way you have already defined in that module.</i>

<div class="docSection"><a name="config"> </a>
The Config File</div>
The <b>config.inc.php</b> file in the import directory has a few things you will likely want to look at.  I've tried to document them in the file itself, but I'll touch on them here.

<?php echo $linebreak; ?>
You can set the default values for stock usage, tax type, and the show featured fields.  You can choose whether you want the ability to purge your database (useful for setting up brand new stores).  You can enable or disable the "fillallfathers" feature, and you can adjust how many products will be inserted at a time.

<?php echo $linebreak; ?>
The other option adjusts how backups are handled and deleted.  The default method is to delete previous backups anytime your session has expired and you log back in to the module.  This was done to prevent people from doing a backup then doing another a couple of weeks later and forgetting to delete the original backup.  This could leave an old backup in place that you wouldn't want to restore from.  By setting the option to TRUE, it will NOT auto-delete your backups and instead provide you with a "Purge Backups" link in the Actions menu.  Just be sure you purge them when you're done.
<p>
I hope most of this makes sense.  If you should have any questions, please don't hesitate to contact me directly at <a href="mailto:bill@swscripts.com">bill@swscripts.com</a> or on the CubeCart.com or CubeCart.org forums.
<p>
Have Fun!
<p>
Sir William
</div>
</td>
<td width="165" valign="top">
<?php echo $backupBlock; ?>
<br>
<?php echo $versionBlock; ?>
</td>
</tr>
</table>
<?php
Footer();
exit;
?>