<?php

function show_barmenu()
{
	?>
	<h1>NUTCRACKER: RGB Effects Builder</h1>
	<ul id="sddm">
	<li><a href="../" onmouseover="mopen('m1')" onmouseout="mclosetime()">Home</a></li>
	<li><a href="../login/member-index.php" onmouseover="mopen('m2')" onmouseout="mclosetime()">Models</a></li>
	<li><a href="../effects/effect-form.php" onmouseover="mopen('m3')" onmouseout="mclosetime()">Effects</a></li>
	<li><a href="../project/project.php" onmouseover="mopen('m4')" onmouseout="mclosetime()">Projects</a>
	<div id="m4" onmouseover="mcancelclosetime()" onmouseout="mclosetime()">
	<a href="../project/project.php">Manage Projects</a>
	<a href="../project/songs.php">Manage Songs</a>
	</div>
	</li>
	<li><a href="../effects/xmas_songs.php" onmouseover="mopen('m5')" onmouseout="mclosetime()">Xmas&nbsp;Songs</a></li>
	<li><a href="../project/gallery_page.php" onmouseover="mopen('m6')" onmouseout="mclosetime()">Gallery</a></li>
	<li><a href="../admin/admin.php" onmouseover="mopen('m7')" onmouseout="mclosetime()">Admin</a>
	<div id="m7" onmouseover="mcancelclosetime()" onmouseout="mclosetime()">
	<a href="../admin/cleanup.php">Cleanup Old Effects and Models</a>
	<a href="../admin/bug.php">Open a Bug or Enhancement ticket</a>
	<a href="../effects/tutorials.html">Nutcracker Tutorials</a>
	<a href="../admin/smeighan-nutcracker-1.0-105-gbdbbaf9.zip">Nutcracker 2.0 zip file (Sep 20,2012)</a>
	<a href="../effects/image_library.php?member_id=2">Image Library of a user</a>
	</div>
	</li>
	<li><a href="#" onmouseover="mopen('m8')" onmouseout="mclosetime()">Help</a>
	<div id="m8" onmouseover="mcancelclosetime()" onmouseout="mclosetime()">
	<a href="#">Nutcracker Tutorials</a>
	<a href="#">Nutcracker FAQ</a>
	<a href="#">Nutcracker Wiki</a>
	<a href="../effects/nc_status.php">Nutcracker Current Status</a>
	</div>
	</li>
	<li><a href="#" onmouseover="mopen('m9')" onmouseout="mclosetime()">Contact</a>
	<div id="m9" onmouseover="mcancelclosetime()" onmouseout="mclosetime()">
	<a href="mailto:sean.meighan@oracle.com?Subject=Nutcracker%20Question">E-mail</a>
	<a href="#">Submit Request Form</a>
	</div>
	</li>
	<li><a href="../login/logout.php" onmouseover="mopen('m6')" onmouseout="mclosetime()">Logout</a></li>
	<li>  <!-- paypal -->
	<form action="https://www.paypal.com/cgi-bin/webscr" method="get">
	<input type="hidden" name="cmd" value="_donations"/>
	<input type="hidden" name="business" value="P2S34RML54T9W"/>
	<input type="hidden" name="lc" value="US"/>
	<input type="hidden" name="item_name" value="Nutcracker: RGB Effects Builder"/>
	<input type="hidden" name="currency_code" value="USD"/>
	<input type="hidden" name="bn" value="PP-DonationsBF:btn_donate_SM.gif:NonHostedGuest"/>
	<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif"
	name="submit" alt="PayPal - The safer, easier way to pay online!"/>
	<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1"/>
	</form>
	</li>
	</ul>
	<div style="clear:both"></div>
	<?php
}
?>
