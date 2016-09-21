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

To install, create a new folder called "import" in the base directory of your CubeCart 3.x.x
store then upload the entire contents of the zip file to that new directory.  There will be an
additional directory inside called "import_files" in that directory.  This is where you'll upload
your files to import.

After you upload the files, you'll need to change the permissions on "sw_decoder.php" file to 777.
If you forget to do this, the script will prompt you.

To call the script, go to http://www.yourdomain.com/[store_dir/]import/import.php and enter
your registration key.  Then, simply click to login with your admin username and password.
Note: Only accounts with Super User status will be allowed.

The first time the script is run, it will create a new table in your database called
"CubeCart_sw_import_config".  It will scan your inventory table, read all the fields, and put them
into the config table along with your license key.

Rather than force you to read more in a text file, I've included online documentation within the
module itself.  If you have any questions, refer there first.  If you still have difficulty,
please don't hesitate to contact me.  I've gone to great efforts to make this application as easy
to use as possible while still being flexible and powerful.  I hope I've achieved this.

God Bless and Fly Low!

Sir William


Upgrade Note:
-------------
If you have a version prior to 1.0.5, you'll need to run the "dropdb.php" file in the import directory. 

If you have any version prior to 1.0.9, you can rescan your inventory table by choosing "Relearn
Inventory Fields" in the config screen.  Also, if you have made any changes to your "config.inc.php"
file such as enabling the backup options or purge options, you'll want to make those same changes to
the new version prior to uploading it.