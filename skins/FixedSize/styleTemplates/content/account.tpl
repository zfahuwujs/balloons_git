<!-- BEGIN: account -->

<div class="boxContent">
  <h1>{LANG_YOUR_ACCOUNT}</h1>
  
  <!-- BEGIN: session_true -->
  <div align="center">
    <table border="0">
      <tr>
        <td align="center" valign="bottom" background=""><a href="index.php?act=profile"><img src="images/Contacts.png" alt="Personal Info" border="0" width="120" /></a><br />
          <a href="index.php?act=profile" class="txtDefault">Personal Info</a></td>
        <td align="center" valign="bottom" background=""><a href="cart.php?act=viewOrders"><img src="images/Inbox.png" alt="Order History" border="0" width="120" /></a><br />
          <a href="cart.php?act=viewOrders" class="txtDefault">Order History</a></td>
        <td align="center" valign="bottom" background=""><a href="index.php?act=changePass"><img src="images/Locker.png" alt="Change Password" border="0" width="120" /></a><br />
          <a href="index.php?act=changePass" class="txtDefault">Change Password</a></td>
        <td align="center" valign="bottom" background=""><a href="index.php?act=newsletter"><img src="images/All-mail.png" alt="Newsletter" border="0" width="120" /></a><br />
          <a href="index.php?act=newsletter" class="txtDefault">Newsletter</a></td>
        <!-- BEGIN: wishlist --> 
        <td align="center" valign="bottom" background=""><a href="index.php?act=newsletter"><img src="images/wishlist.png" alt="Newsletter" border="0" width="120" /></a><br />
          <a href="index.php?act=viewCat&catId=wishlist" class="txtDefault">Wishlist</a></td>
        <!-- END: wishlist --> 
      </tr>
    </table>
  </div>
  <!-- END: session_true --> 
  
  <!-- BEGIN: session_false -->
  <p>{LANG_LOGIN_REQUIRED}</p>
  <!-- END: session_false --> 
  
</div>
<!-- END: account -->