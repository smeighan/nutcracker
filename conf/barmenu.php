<?php
function show_barmenu() {
?>
<h1>NUTCRACKER: RGB Effects Builder</h1>
<ul id="sddm">
	<li><a href="/nutcracker" onmouseover="mopen('m1')" onmouseout="mclosetime()">Home</a></li>
	<li><a href="/nutcracker/login/member-index.php" onmouseover="mopen('m2')" onmouseout="mclosetime()">Models</a></li>
	<li><a href="/nutcracker/effects/effect-form.php" onmouseover="mopen('m3')" onmouseout="mclosetime()">Effects</a></li>
	<li><a href="/nutcracker/effects/project.php" onmouseover="mopen('m4')" onmouseout="mclosetime()">Projects</a></li>
	<li><a href="/nutcracker/effects/xmas_songs.php" onmouseover="mopen('m5')" onmouseout="mclosetime()">Christmas Songs</a></li>
	<li><a href="/nutcracker/effects/gallery-form.php" onmouseover="mopen('m6')" onmouseout="mclosetime()">Gallery</a></li>
	<li><a href="/nutcracker/admin/admin.php" onmouseover="mopen('m7')" onmouseout="mclosetime()">Admin</a>
		<div id="m7" onmouseover="mcancelclosetime()" onmouseout="mclosetime()">
		<a href="/nutcracker/admin/cleanup.php">Cleanup Old Effects and Models</a>
		<a href="/nutcracker/admin/bug.php">Open a Bug or Enhancement ticket</a>

		<a href="/nutcracker/effects/tutorials.html">Nutcracker Tutorials</a>
		</div>
	</li>
	<li><a href="#" onmouseover="mopen('m8')" onmouseout="mclosetime()">Help</a>
		<div id="m8" onmouseover="mcancelclosetime()" onmouseout="mclosetime()">
		<a href="#">Nutcracker Tutorials</a>
		<a href="#">Nutcracker FAQ</a>
		<a href="#">Nutcracker Wiki</a>
		</div>
	</li>
	<li><a href="#" onmouseover="mopen('m9')" onmouseout="mclosetime()">Contact</a>
		<div id="m9" onmouseover="mcancelclosetime()" onmouseout="mclosetime()">
		<a href="mailto:sean.meighan@oracle.com?Subject=Nutcracker%20Question">E-mail</a>
		<a href="#">Submit Request Form</a>
		</div>
	</li>
	<li><a href="/nutcracker/login/logout.php" onmouseover="mopen('m6')" onmouseout="mclosetime()">Logout</a></li>
	<li>
	<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_donations">
<input type="hidden" name="business" value="P2S34RML54T9W">
<input type="hidden" name="lc" value="US">
<input type="hidden" name="item_name" value="Nutcracker: RGB Effects Builder">
<input type="hidden" name="currency_code" value="USD">
<input type="hidden" name="bn" value="PP-DonationsBF:btn_donate_SM.gif:NonHostedGuest">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>
</li>
</ul>
<div style="clear:both"></div>
<?php
}
?>