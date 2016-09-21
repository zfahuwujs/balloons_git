<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Contact Us</title>
<script type="text/JavaScript">
<!--
function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_validateForm() { //v4.0
  var i,p,q,nm,test,num,min,max,errors='',args=MM_validateForm.arguments;
  for (i=0; i<(args.length-2); i+=3) { test=args[i+2]; val=MM_findObj(args[i]);
    if (val) { nm=val.name; if ((val=val.value)!="") {
      if (test.indexOf('isEmail')!=-1) { p=val.indexOf('@');
        if (p<1 || p==(val.length-1)) errors+='- '+nm+' must contain an e-mail address.\n';
      } else if (test!='R') { num = parseFloat(val);
        if (isNaN(val)) errors+='- '+nm+' must contain a number.\n';
        if (test.indexOf('inRange') != -1) { p=test.indexOf(':');
          min=test.substring(8,p); max=test.substring(p+1);
          if (num<min || max<num) errors+='- '+nm+' must contain a number between '+min+' and '+max+'.\n';
    } } } else if (test.charAt(0) == 'R') errors += '- '+nm+' is required.\n'; }
  } if (errors) alert('The following error(s) occurred:\n'+errors);
  document.MM_returnValue = (errors == '');
}
//-->
</script>
</head>

<body>

 
<form action="mailer.php" method="post" name="form1" target="_top" id="form1" style="margin:0px; font-family:Verdana, Arial, Helvetica, sans-serif;font-size:11px; width:300px;" onsubmit="MM_validateForm('from','','RisEmail','subject','','R','verif_box','','R','message','','R');return document.MM_returnValue">

Your e-mail:<br />
<input name="from" type="text" id="from" style="padding:2px; border:1px solid #CCCCCC; width:180px; height:14px; font-family:Verdana, Arial, Helvetica, sans-serif;font-size:11px;" value="<?php echo $_GET['from'];?>"/>
<br />
<br />

Subject:<br />
<input name="subject" type="text" id="subject" style="padding:2px; border:1px solid #CCCCCC; width:180px; height:14px;font-family:Verdana, Arial, Helvetica, sans-serif; font-size:11px;" value="<?php echo $_GET['subject'];?>"/>
<br />
<br />

Type verification image:<br />
<input name="verif_box" type="text" id="verif_box" style="padding:2px; border:1px solid #CCCCCC; width:180px; height:14px;font-family:Verdana, Arial, Helvetica, sans-serif; font-size:11px;"/>
<img src="verificationimage.php?<?php echo rand(0,9999);?>" alt="verification image, type it in the box" width="50" height="24" align="absbottom" /><br />
<br />

<!-- if the variable "wrong_code" is sent from previous page then display the error field -->
<?php if(isset($_GET['wrong_code'])){?>
<div style="border:1px solid #990000; background-color:#D70000; color:#FFFFFF; padding:4px; padding-left:6px;width:295px;">Wrong verification code</div><br /> 
<?php ;}?>

Message:<br />
<textarea name="message" cols="6" rows="5" id="message" style="padding:2px; border:1px solid #CCCCCC; width:300px; height:100px; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:11px;"><?php echo $_GET['message'];?></textarea>

<input name="Submit" type="submit" style="margin-top:10px; display:block; border:1px solid #000000; width:100px; height:20px;font-family:Verdana, Arial, Helvetica, sans-serif; font-size:11px; padding-left:2px; padding-right:2px; padding-top:0px; padding-bottom:2px; line-height:14px; background-color:#EFEFEF;" value="Send Message"/>
</form>

</body>
</html>
