<?php    


/*********DENY DIRECT ACCESS FROM BROWSER************/
phpExtension();

//// query database for product id and name 
$productId = treatGet($_GET['productId']) ;
$result = $db->select("SELECT name FROM ".$glob['dbprefix']."CubeCart_inventory WHERE productId = ".$db->mySQLSafe($_GET['productId'])); 
        if($lang_folder !== $config['defaultLang']){
    
        $foreignVal = $db->select("SELECT name FROM ".$glob['dbprefix']."CubeCart_inv_lang WHERE prod_master_id = ".$db->mySQLSafe($_GET['productId'])." AND prod_lang=".$db->mySQLSafe($lang_folder));
        
        if($foreignVal==TRUE){
        
            $result[0]['name'] = $foreignVal[0]['name'];          
            } 
             
        }

 
////check if ip has submitted a review for this product
$ipVar = $_SERVER['REMOTE_ADDR'];  $tdate = date('Y/m/d');
$get_ip = mysql_query ("SELECT ip_address FROM ".$glob['dbprefix']."CubeCart_store_ratings WHERE ip_address='$ipVar' AND product_id='$productId' AND date='$tdate'");
$ipcheck = mysql_num_rows($get_ip);

$review=new XTemplate ("skins/".$config['skinDir']."/styleTemplates/content/review.tpl");

$review->assign("REVIEW_TITLE",$lang['review']['review_title']);
$review->assign("CUSTOMER_NAME",$lang['review']['form']['cust_name']); 
$review->assign("NEW_COMMENT",$lang['review']['form']['comment']);
$review->assign("STAR_RATING",$lang['review']['form']['rating']);
$review->assign("STAR_1",$lang['review']['form']['star1']); 
$review->assign("STAR_2",$lang['review']['form']['star2']);
$review->assign("STAR_3",$lang['review']['form']['star3']);
$review->assign("STAR_4",$lang['review']['form']['star4']);
$review->assign("STAR_5",$lang['review']['form']['star5']);
$review->assign("STAR_CHOOSE",$lang['review']['form']['star_choose']);
$review->assign("SUBMIT_REVIEW",$lang['review']['form']['submit_review']);
$review->assign("ORDER_REQUIRED",$lang['review']['order_req']); 
$review->assign("FORM_NAME",$result[0]['name']);
$review->assign("PRODUCT_ID",$productId);
$review->assign("MESSAGE",$message);


// check user is registared - display prod name
if($ccUserData[0]['customer_id']>0 ) { 
 
    $review->assign("PROD_DESC",$result[0]['name']);  
    $review->assign("REVIEW_DESC",$lang['review']['fill_out_form']);  
}     
 
else {
    $review->assign("NEED_REG",$lang['review']['need_reg']); 
    }
  
// only write to database if name & message are filled in & customer is registared
  
if($ccUserData[0]['customer_id'] > 0 &&  isset($_POST['submit'])) { 

    if(empty($_POST['name']) || empty($_POST['message'])){
		$review->assign("NAME",$_POST['name']); 
		$review->assign("LOCATION",$_POST['location']);
		$review->assign("MESSAGE",$_POST['message']);
		$review->assign("ERROR_MESS",$lang['review']['error_mess']);
	}elseif ($ipcheck !==0){
		$review->assign("IP_ERROR",$lang['review']['ip_error_mess']);
	}elseif(!isset($_POST['stars'])){
		$review->assign("NAME",$_POST['name']); 
		$review->assign("LOCATION",$_POST['location']);
		$review->assign("MESSAGE",$_POST['message']);
		$review->assign("ERROR_MESS", "Please enter specify the rating for this product.");
	}else{
    
// assign variables    
      $name = addslashes($_POST["name"]);      
      $productname = $_POST['productname'];      
      $stars = $_POST["stars"];
      $message = addslashes($_POST["message"]);
      $ipaddress = $db->mySQLSafe($_SERVER['REMOTE_ADDR']);
      $date = date('Y/m/d');
      
// write/use variables
      $query= "INSERT INTO ".$glob['dbprefix']."CubeCart_store_ratings (cust_name, product_id, product_name, stars, status, comments, date, ip_address, id) VALUES ('$name', '$productId', '$productname', '$stars', 'NULL', '$message', '$date', $ipaddress, 'NULL')";
      mysql_query($query) or die(mysql_error());
// conformation message      
      $review->assign("SUCCESS",$lang['review']['success']); 
      
  }

}
 //end if submit  

$review->parse("review");
$page_content = $review->text("review"); 
?>