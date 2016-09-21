<?php

include("../includes/ini.inc.php");
include_once("../includes/global.inc.php");
include_once("../classes/db.inc.php");
$db = new db();
include_once("../includes/functions.inc.php");
$config = fetchDbConfig("config");
include_once("../language/".$config['defaultLang']."/lang.inc.php");
$enableSSl = 1;
include_once("../includes/sslSwitch.inc.php");
include_once("includes/auth.inc.php");
include_once("includes/header.inc.php");


	
?>
</br><table align="center" width="95%" border="0" cellspacing="0" cellpadding="0">
<?php
echo"
      <li><a  href=\"reviews.php?status=0\" class=\"txtLink\">View New Reviews</a></li><li><a  href=\"reviews.php?status=1\" class=\"txtLink\">View Public Reviews</a></li>";

      $status=$_GET['status'];

if ($status==0)   
echo"<h2> New Reviews </h2> "; 
else if ($status==1)
echo"<h2> Public Reviews </h2>";  


if ($status==10) 
{ 
//$sql_count = "SELECT * FROM ".$glob['dbprefix']."CubeCart_store_ratings WHERE id='$id'";
//$result_count = mysql_query ($sql_count);
//$total = mysql_num_rows($result_count);

//if ($total>0)
 //{
     
 $page=$_GET['page'];    
 $id=$_GET['id'];
 $result = "DELETE FROM ".$glob['dbprefix']."CubeCart_store_ratings WHERE status='10'";
 $row = mysql_query($result);
echo"<h2>Deleted Reviews</h2><align=\"left\"><font color=\"#009900\"><b>Review Deleted Successfully</b></font><br/><br/>";

 }       

if($_GET['task'] !== "order")
{
 $status=$_GET['status'];  
 $sql_select = mysql_query( "SELECT * FROM ".$glob['dbprefix']."CubeCart_store_ratings WHERE status='$status' ORDER BY id DESC");
 $totalrows = mysql_num_rows($sql_select);
 
 if($totalrows==0)
  {echo"<tr><td><p align=\"center\"><font size=\"3\" color=\"#990000\"><b>There Are No Reviews To Display</b></font></p></td></tr>";}
  
 if($totalrows!==0)
 { 
	
	// set limit value for number of records to be shown per page 
	// query database to find total number of records to display  
	$limit = 5; 
    $page=$_GET['page'];
	if(empty($page)) 
			$page = 1; 
	$limitvalue = $page * $limit - ($limit); 
	$query = "SELECT * FROM ".$glob['dbprefix']."CubeCart_store_ratings where status='$status' LIMIT $limitvalue, $limit "; 
	$results = mysql_query($query) or die("Error: " . mysql_error()); 
	$count_result = mysql_num_rows($results);	 
 
	// Display links at the top to indicate current page and number of pages displayed
   
	$numofpages = ceil($totalrows / $limit);

	$from=$limit*$page-$limit+1;
	$to=$from + $count_result-1;

	echo "<tr align=\"right\"><td>"; 

	$upper_limit = $page + 5;
	$lower_limit = $page - 2;

	if($page != 1){
	$pageprev = $page - 1; 
	
	echo("<a href=\"$PHP_SELF?page=$pageprev&status=$status\"><< </a>&nbsp;"); 
	} 

	$lower_dots = $page - $lowerlimit;

	if($lower_dots > 3){
	echo("...");}

	for($i = 1; $i <= $numofpages; $i++){ 
	if($numofpages>1){

	if($i == $page){
	echo("&nbsp;<b>[".$i."]</b>&nbsp;");} 

	if(($i != $page)&&($i < $upper_limit)&&($i >= $lower_limit)){
	echo("&nbsp;<a href=\"$PHP_SELF?page=$i&status=$status\">$i</a>&nbsp;");}
	}}

	$upper_dots = $numofpages - 2;

	if($page < $upper_dots){
	echo("...&nbsp;");}

	if(($totalrows - ($limit * $page)) > 0){ 
	$pagenext = $page + 1; 
	echo("<a href=\"$PHP_SELF?page=$pagenext&status=$status\"> >></a>");
	}
	echo"</br></br></tr></td>";

// end records per page
  
  while ($row = mysql_fetch_array($results))
  {
  $name=$row["cust_name"]; 
  $productId=$row["product_id"];
  //$productname=$row["product_name"]; 
  //
  //// query database for product id and name 
$result = $db->select("SELECT name FROM ".$glob['dbprefix']."CubeCart_inventory WHERE productId = $productId"); 
        if($lang_folder !== $config['defaultLang']){
        $foreignVal = $db->select("SELECT name FROM ".$glob['dbprefix']."CubeCart_inv_lang WHERE prod_master_id =  $productId AND prod_lang=".$db->mySQLSafe($lang_folder));
        if($foreignVal==TRUE){
            $result[0]['name'] = $foreignVal[0]['name'];          
            } 
        }
		$productname = $result[0]['name'];
  //
  $stars=$row["stars"]; 
  $comments=$row["comments"]; 
  $value=$row["status"]; 
  $id=$row["id"];
  $ip_address=$row["ip_address"]; 
  $date=$row['date'];
  
  
  $name = stripslashes($name);
  $comments = stripslashes($comments);
 
echo"<table width=\"95%\" border=\"1\" align=\"center\" cellpadding=\"3\" cellspacing=\"0\" >
        <tr> 
          <td  colspan=\"4\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
            <tr> 
               <td width=\"80%\"><strong>Product: <a href=\"../index.php?act=viewProd&amp;productId=$productId\" target=\"_blank\" class=\"txtLink\">  $productname</a></strong><br />
<strong>Product ID: $productId</strong> </td>
               <td><div align=\"right\"><strong>";
            if ($_GET['status']==0)
             echo $lang['admin']['review']['comment_not_public'];
            else if ($_GET['status']==1)
             echo $lang['admin']['review']['comment_public'];  
            echo"</strong></div></td>
             </tr>
           </table></td>
        </tr>
        <tr> 
          <td width=\"25%\" height=\"30px\">
                 <p><strong>Name : </strong>$name</td>"; 
          echo"<td    width=\"25%\" height=\"30px\">";
                   if ($stars==0)
                     {echo "<strong>Rating : </strong><img src=\"../images/stars-1.gif\" width=\"80\" height=\"16\">";}
                else if ($stars==1)
                     {echo "<strong>Rating : </strong><img src=\"../images/stars-2.gif\" width=\"80\" height=\"16\">" ;}
                else if ($stars==2)
                     {echo "<strong>Rating : </strong><img src=\"../images/stars-3.gif\" width=\"80\" height=\"16\">" ;}
                else if ($stars==3) 
                     {echo "<strong>Rating : </strong><img src=\"../images/stars-4.gif\" width=\"80\" height=\"16\">" ;}
                else if ($stars==4) 
                     {echo "<strong>Rating : </strong><img src=\"../images/stars-5.gif\" width=\"80\" height=\"16\">" ;}
                     echo"</td> 
                    <td   width=\"25%\" height=\"30px\"><p><strong>Date Added : </strong>$date</td>
                    <td   width=\"25%\" height=\"30px\"><p><strong>Ip : </strong><a href=\"javascript:;\" class=\"txtLink\" onclick=\"openPopUp('misc/lookupip.php?ip= $ip_address','misc',300,130)\">$ip_address</a></td>
               <tr><td colspan=\"4\"  >";
             echo "<strong>Review Comment : </strong>$comments  
            <p align=\"right\"><a href=\"reviews.php?task=order&id=$id&title=$productname&productId=$productId&status=$status&page=$page\" class=\"txtLink\"><strong>Change Review</strong></a></p></td>       
        </td></tr>
        </tr>
    </table><br/>";
   
  } 
 }
} 
    
if($_GET['task']=="order"){
$status=$_GET['status'];
echo"<p align=\"center\"><a href=\"javascript:history.back()\">Back to reviews without saving</a></p>";

//save comment information
$page=$_GET['page'];
$id=$_GET['id'];
if($_GET['save']=="yes")
{ 
 $name = addslashes($_POST['name']);
  $productId = addslashes($_POST['productId']);
 $stars = ($_POST['stars']);
 $status = ($_POST['status']);
 $comments = addslashes($_POST['comments']);
   if ($status!=2) {
 $update =("UPDATE ".$glob['dbprefix']."CubeCart_store_ratings SET product_name='$productname', product_id='$productId', cust_name='$name', stars='$stars', status='$status', comments='$comments' WHERE id='$id'");
  mysql_query($update) or die(mysql_error());
 } else  if ($status==2) {
 
     $date = date('Y/m/d');
	 $ipaddress = $_SERVER['REMOTE_ADDR'];
	  $status = '0';
   $query= "INSERT INTO ".$glob['dbprefix']."CubeCart_store_ratings (cust_name, product_id, product_name, stars, status, comments, date, ip_address) VALUES ('$name', '$productId', '$productname', '$stars', '0', '$comments', '$date', '$ipaddress')";
     mysql_query($query) or die(mysql_error());
	  }


  
  echo"<p align=\"center\">Renew Comment</p>";
 if($note){
 echo "<script language=\"javascript\">window.location=\"reviews.php?status=$status&page=$page\"</script>";
 }
 if(empty($note)){
 echo "<script language=\"javascript\">window.location=\"reviews.php?status=$status&page=$page\"</script>";
 }    
  }
$id=$_GET['id'];
$sql_select = mysql_query( "SELECT * FROM ".$glob['dbprefix']."CubeCart_store_ratings WHERE id='$id'");
$totalrows = mysql_num_rows($sql_select);  
  
if(empty($id)){echo"<br><br><p align=\"center\">It appears you have arrived at this page by accident</p>";}

if($totalrows!==0) {

while ($row = mysql_fetch_array($sql_select)){
  $name=$row["cust_name"]; 
  $productId=$row["product_id"];
  //$productname=$row["product_name"];
  //// query database for product id and name 
$result = $db->select("SELECT name FROM ".$glob['dbprefix']."CubeCart_inventory WHERE productId = $productId"); 
        if($lang_folder !== $config['defaultLang']){
        $foreignVal = $db->select("SELECT name FROM ".$glob['dbprefix']."CubeCart_inv_lang WHERE prod_master_id =  $productId AND prod_lang=".$db->mySQLSafe($lang_folder));
        if($foreignVal==TRUE){
            $result[0]['name'] = $foreignVal[0]['name'];          
            } 
        }
		$productname = $result[0]['name'];
  //
  $stars=$row["stars"]; 
  $comments=$row["comments"]; 
  $status=$row["status"]; 
  $ip_address=$row["ip_address"]; 
  $date=$row['date'];
    
  $id=$row["id"]; 
  
  $name = stripslashes($name);
  $comments = stripslashes($comments);
  }
 }
// admin change customer review form
// echo "<form method=\"post\" name=\"comments\" action=\"reviews.php?save=yes&status=$status\">
 echo "<form method=\"post\" name=\"comments\" action=\"reviews.php?save=yes&task=order&status=$status&id=$id&title=$productname&productId=$productId&page=$page\">
 
    <table width=\"600\" border=\"1\" align=\"center\" cellpadding=\"4\" cellspacing=\"0\" >
    <tr> 
      <td ><strong>Product: <a href=\"../index.php?act=viewProd&amp;productId=$productId\" target=\"_blank\">$productname</a></strong></td>
    </tr>
    <tr>
   <td>
  <table width=\"60%\" align=\"center\" border=\"0\" cellspacing=\"5\" cellpadding=\"0\">
       <tr>
           <td valign=\"top\"><b>Product ID Number:</b></td>
           <td><input type=\"text\" class=\"textbox\" name=\"productId\" value=\"$productId\" size=\"40\" maxlength=\"80\"></td>
       </tr>
	   <tr>
           <td valign=\"top\"><b>Name:</b></td>
           <td><input type=\"text\" class=\"textbox\" name=\"name\" value=\"$name\" size=\"40\" maxlength=\"80\"></td>
       </tr>
       <tr>
           <td valign=\"top\"><b>Comment:</b></td>
           <td><textarea name=\"comments\" cols=\"65\" rows=\"10\" value=\"comments\">$comments</textarea></td>
       </tr>
       <tr>
           <td valign=\"top\"><b>Options:</b></td>
           <td><select class=\"textbox\" name=\"status\" >
            <option value=\"0\"";if($status==0){echo "selected";}echo">Not Public</option>
      <option value=\"1\"";if($status==1){echo "selected";}echo">Make Public</option>
      <option value=\"2\"";if($status==2){echo "selected";}echo">Copy to New Review</option>
      <option value=\"10\"";if($status==10){echo "selected";}echo">Delete</option>
      </select></td>
       </tr>
          <tr>
           <td valign=\"top\"><b>Rating:</b></td>
           <td><select class=\"textbox\" name=\"stars\" >
      <option value=\"0\"";if($stars==0){echo "selected";}echo">Bad</option>
      <option value=\"1\"";if($stars==1){echo "selected";}echo">Ok</option>
      <option value=\"2\"";if($stars==2){echo "selected";}echo">Good</option>
      <option value=\"3\"";if($stars==3){echo "selected";}echo">Very Good</option>
      <option value=\"4\"";if($stars==4){echo "selected";}echo">Perfect</option>
      </select></td>
       </tr>
       <tr>
           <td>&nbsp;</td>
           <td><input type=\"submit\" name=\"submit\" class=\"submit\" value=\"Update & Save Review\"></td>
       </tr>
     </table>
     </td>
    </tr>
    </table>
      </form>";
	  
//}      
//}
} 

include ("includes/footer.inc.php");
    
?> 