<?php
include("../../../../includes/ini.inc.php");
include("../../../../includes/global.inc.php");
include("../../../../classes/db.inc.php");
$db = new db();
include_once("../../../../includes/functions.inc.php");
$config = fetchDbConfig("config");
include_once("../../../../language/".$config['defaultLang']."/lang.inc.php");
$enableSSl = 1;
include_once("../../../../includes/sslSwitch.inc.php");
include("../../../includes/auth.inc.php");
include("../../../includes/header.inc.php");
if(permission("settings","read")==FALSE){
	header("Location: ".$GLOBALS['rootRel']."admin/401.php");
	exit;
}
if(isset($_POST['module'])){
	include("../../status.php");
	include("../../../includes/functions.inc.php");
	$module = fetchDbConfig($_GET['folder']);
	$msg = writeDbConf($_POST['module'], $_GET['folder'], $module);
}
$module = fetchDbConfig($_GET['folder']);
?>

<p class="pageTitle">By Price</p>
<?php 
if(isset($msg)){ 
	echo stripslashes($msg); 
} 
?>

<p class="copyText">This shipping method is used to give free shipping on orders over a certain amount.</p>

<form action="<?php echo $GLOBALS['rootRel'];?>admin/modules/<?php echo $_GET['module']; ?>/<?php echo $_GET['folder']; ?>/index.php?module=<?php echo $_GET['module']; ?>&amp;folder=<?php echo $_GET['folder']; ?>" method="post" enctype="multipart/form-data">
<table border="0" cellspacing="0" cellpadding="3" class="mainTable">
  <tr>
    <td colspan="2" class="tdTitle">Configuration Settings </td>
  </tr>
  <tr>
    <td align="left" class="tdText" width="200"><strong>Status:</strong></td>
    <td class="tdText">
		<select name="module[status]">
			<option value="1" <?php if($module['status']==1) echo "selected='selected'"; ?>>Enabled</option>
			<option value="0" <?php if($module['status']==0) echo "selected='selected'"; ?>>Disabled</option>
	    </select>
	</td>
  </tr>
  <tr>
  	<td colspan="2" id="newRowPlaceholder">
		  <?php 
		  	$i=0;  
			while((isset($module['level_'.$i]) && $module['level_'.$i]!='') || $i==0){ 
			if($i>0){echo '<br />';}
		  ?>
          
          <table border="0" cellspacing="0" cellpadding="3">
          <tr>
              <td align="left" class="tdText" width="200"><strong><?php echo $i+1; ?>) Free Shipping Level:</strong></td>
              <td class="tdText"><input name="module[level_<?php echo $i; ?>]" type="text" value="<?php echo $module['level_'.$i]; ?>" class="textbox" size="10" /></td>
          </tr>
            <td align="left" class="tdText"><strong><?php echo $i+1; ?>) Shipping Cost:</strong></td>
            <td class="tdText"><input name="module[amount_<?php echo $i; ?>]" type="text" value="<?php echo $module['amount_'.$i]; ?>" class="textbox" size="10" /></td>
          </tr>
          </table>
          <?php $i++; } ?>
    </td>
  </tr>
  <tr>
  	<td></td>
    <td class="tdText"><span id="newRow" style="cursor:pointer;">[+] Add More</span></td>
  </tr>
  <tr>
    <td align="right" class="tdText">&nbsp;</td>
    <td class="tdText"><input type="submit" class="submit" value="Edit Config" /></td>
  </tr>
</table>
</form>
<p class="tdText">
	<strong>Free Shipping Level:</strong> Total price of order (up to)<br />
    <strong>Shipping Cost:</strong> Value to chage for shipping (enter 0.00 for free shipping)
</p>
<div id="newRowCount" style="display:none;"><?php echo $i+1; ?></div>
<div id="newRowHtml" style="display:none;">
<br />
<table border="0" cellspacing="0" cellpadding="3">
  <tr>
      <td align="left" class="tdText" width="200"><strong><span class="newRowId"></span>) Free Shipping Level:</strong></td>
      <td class="tdText"><input name="module[level_New]" type="text" value="" class="textbox" size="10" /></td>
  </tr>
    <td align="left" class="tdText"><strong><span class="newRowId"></span>) Shipping Cost:</strong></td>
    <td class="tdText"><input name="module[amount_New]" type="text" value="" class="textbox" size="10" /></td>
  </tr>
</table>
</div>
<script type="text/javascript">
	$('#newRow').click(function() {
		var newRowHtml=$('#newRowHtml').html();
		var newRowCount=parseInt($('#newRowCount').text());
	  	$('#newRowPlaceholder').append(newRowHtml);
		$('#newRowPlaceholder input[name=module[level_New]]').attr('name','module[level_'+(newRowCount-1)+']');
		$('#newRowPlaceholder input[name=module[amount_New]]').attr('name','module[amount_'+(newRowCount-1)+']');
		$('#newRowPlaceholder .newRowId').text(newRowCount);
		$('#newRowPlaceholder .newRowId').attr('class','');
		$('#newRowCount').text(newRowCount+1);
	});
</script>

<?php include("../../../includes/footer.inc.php"); ?>