<?php
////////////////////////////////////////////
//    SET your database access info here  //
////////////////////////////////////////////

$database="DATABASE NAME";
$username="USERNAME";
$password="PASSWORD";


////////////////////////////////////////////////////////
//    Ensure your database table is named correctly ! //
////////////////////////////////////////////////////////

if (!($connection = mysql_connect(localhost, "$username", "$password")))
die("could not connect");
if (!(mysql_select_db("$database", $connection)))
mysql_error();
$timer = time();

require("XPath.class.php"); 
$xp = new XPath(); 
$xp->importFromFile("http://www.ecb.int/stats/eurofxref/eurofxref-daily.xml"); 
$gbp = $xp->getAttributes("//Cube[@currency='GBP']","rate"); 
$aud = $xp->getAttributes("//Cube[@currency='AUD']","rate");
$cad = $xp->getAttributes("//Cube[@currency='CAD']","rate");
$cny = $xp->getAttributes("//Cube[@currency='CNY']","rate");
$jpy = $xp->getAttributes("//Cube[@currency='JPY']","rate");
$rub = $xp->getAttributes("//Cube[@currency='RUB']","rate");
$zar = $xp->getAttributes("//Cube[@currency='ZAR']","rate");
$chf = $xp->getAttributes("//Cube[@currency='CHF']","rate");
$usd = $xp->getAttributes("//Cube[@currency='USD']","rate");
{
$eur = '1';
$result = mysql_query ("SELECT * from ".$glob['dbprefix']."CubeCart_currencies where value='1.00000'");
$row = mysql_fetch_array($result);
if ($row)
{
$defcurr=$row["code"];
if ($defcurr=='GBP')
$incurr=$gbp;
if ($defcurr=='USD')
$incurr=$usd;
if ($defcurr=='EUR')
$incurr=$eur;
if ($defcurr=='JPY')
$incurr=$jpy;
if ($defcurr=='CAD')
$incurr=$cad;
if ($defcurr=='AUD')
$incurr=$aud;
if ($defcurr=='CHF')
$incurr=$chf;
if ($defcurr=='RUB')
$incurr=$rub;
if ($defcurr=='CNY')
$incurr=$cny;
if ($defcurr=='ZAR')
$incurr=$zar;


//     Euro Update

$pound = ('1'/$incurr);
$exchanged = round($pound, 5); 
$timer = time();
$sql = mysql_query("update ".$glob['dbprefix']."CubeCart_currencies set value='$exchanged',lastUpdated=$timer where name='Euro'");

if ($sql)
{
echo "Euro Currency updated $timer
";
}
else
echo "ERROR updating database  $timer";

//     UK Pounds update

$gbp = ($pound*$gbp);
$exchanged = round($gbp, 5); 
$timer = time();
$sql = mysql_query("update ".$glob['dbprefix']."CubeCart_currencies set value='$exchanged',lastUpdated=$timer where name='US Dollars'");

if ($sql)
{
echo "GBP Currency updated $timer
";
}
else
echo "ERROR updating database  $timer";

//     US Dollars update

$usd = ($pound*$usd);
$exchanged = round($usd, 5); 
$timer = time();
$sql = mysql_query("update ".$glob['dbprefix']."CubeCart_currencies set value='$exchanged',lastUpdated=$timer where name='US Dollars'");

if ($sql)
{
echo "USD Currency updated $timer
";
}
else
echo "ERROR updating database  $timer";

//      Australian Dollars update

$aud = ($pound*$aud);
$exchanged = round($aud, 5); 
$timer = time();
$sql = mysql_query("update ".$glob['dbprefix']."CubeCart_currencies set value='$exchanged',lastUpdated=$timer where name='Australian Dollars'");

if ($sql)
{
echo "AUD Currency updated $timer
";
}
else
echo "ERROR updating database  $timer";

//      Japanese Yen update

$jpy = ($pound*$jpy);
$exchanged = round($jpy, 5); 
$timer = time();
$sql = mysql_query("update ".$glob['dbprefix']."CubeCart_currencies set value='$exchanged',lastUpdated=$timer where name='Japanese Yen'");

if ($sql)
{
echo "JPY Currency updated $timer
";
}
else
echo "ERROR updating database  $timer";

//      Canadian Dollars update

$cad = ($pound*$cad);
$exchanged = round($cad, 5); 
$timer = time();
$sql = mysql_query("update ".$glob['dbprefix']."CubeCart_currencies set value='$exchanged',lastUpdated=$timer where name='Canadian Dollars'");

if ($sql)
{
echo "CAD Currency updated $timer
";
}
else
echo "ERROR updating database  $timer";

//      Swiss Francs update

$chf = ($pound*$chf);
$exchanged = round($chf, 5); 
$timer = time();
$sql = mysql_query("update ".$glob['dbprefix']."CubeCart_currencies set value='$exchanged',lastUpdated=$timer where name='Swiss Francs'");

if ($sql)
{
echo "CHF Currency updated $timer
";
}
else
echo "ERROR updating database  $timer";

//      Russian Rubles update

$rub = ($pound*$rub);
$exchanged = round($rub, 5); 
$timer = time();
$sql = mysql_query("update ".$glob['dbprefix']."CubeCart_currencies set value='$exchanged',lastUpdated=$timer where name='Russian Rubles'");

if ($sql)
{
echo "RUB Currency updated $timer
";
}
else
echo "ERROR updating database  $timer";

//      Chinese Yuan update

$cny = ($pound*$cny);
$exchanged = round($cny, 5); 
$timer = time();
$sql = mysql_query("update ".$glob['dbprefix']."CubeCart_currencies set value='$exchanged',lastUpdated=$timer where name='Chinese Yuan'");

if ($sql)
{
echo "CNY Currency updated $timer
";
}
else
echo "ERROR updating database  $timer";

//      South African Rand update

$zar = ($pound*$zar);
$exchanged = round($zar, 5); 
$timer = time();
$sql = mysql_query("update ".$glob['dbprefix']."CubeCart_currencies set value='$exchanged',lastUpdated=$timer where name='South African Rand'");

if ($sql)
{
echo "ZAR Currency updated $timer
";
}
else
echo "ERROR updating database  $timer";

}

else 
echo "ERROR - At least one currency must be set to value 1.00000";
}

?> 


www.toucanwebdesign.com

