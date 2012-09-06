<?php
session_start();
?><?php
/*
Nutcracker: RGB Effects Builder
Copyright (C) 2012  Sean Meighan
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
require_once('../conf/header.php');
if( isset($_SESSION['ERRMSG_ARR']) && is_array($_SESSION['ERRMSG_ARR']) && count($_SESSION['ERRMSG_ARR']) >0 ) {
	echo '<ul class="err">';
	foreach($_SESSION['ERRMSG_ARR'] as $msg) {
		echo '<li>',$msg,'</li>'; 
	}
	echo '</ul>';
	unset($_SESSION['ERRMSG_ARR']);
}
?>
<form id="loginForm" name="loginForm" method="post" action="register-exec.php">
<h2>Nutcracker registration Form</h2>
  <table width="300" border="0" align="center" cellpadding="2" cellspacing="0">
    <tr>
      <th>First Name </th>
      <td><input name="fname" type="text" class="textfield" id="fname" /></td>
    </tr>
    <tr>
      <th>Last Name </th>
      <td><input name="lname" type="text" class="textfield" id="lname" /></td>
    </tr>
    <tr>
      <th width="124">Login</th>
      <td width="168"><input name="login" type="text" class="textfield" id="login" /></td>
    </tr>
    <tr>
      <th>Password</th>
      <td><input name="password" type="password" class="textfield" id="password" /></td>
    </tr>
    <tr>
      <th>Confirm Password </th>
      <td><input name="cpassword" type="password" class="textfield" id="cpassword" /></td>
    </tr>
 <tr>
      <th>Select All Sequencers you will need </th>
      <td><br/>
	LSP 1.8<input type="checkbox" name="sequencers[]" value="LSP1_8" ><br/>
	LSP 2.0<input type="checkbox" name="sequencers[]" value="LSP2_0" ><br/>
	LSP 3.0<input type="checkbox" name="sequencers[]" value="LSP3_0" ><br/>
	LOR S2<input type="checkbox" name="sequencers[]" value="LOR_S2" > <br/>
	LOR S3<input type="checkbox" name="sequencers[]" value="LOR_S3" > <br/>
	Vixen 2.1.1<input type="checkbox" name="sequencers[]" value="VIXEN211" > <br/>
	Vixen 2.5<input type="checkbox" name="sequencers[]" value="VIXEN25" > <br/>
	Vixen 3.0<input type="checkbox" name="sequencers[]" value="VIXEN3" > <br/>
	HLS<input type="checkbox" name="sequencers[]" value="HLS" > <br/>
	Other<input type="checkbox" name="sequencers[]" value="OTHER" > <br/>
	</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><input type="submit" name="Submit" value="Register" /></td>
    </tr>
  </table>
</form>
<p><a href="login-form.php">Click here</a> to login to your account.</p>

</body>
</html>
