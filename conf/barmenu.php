<?php
function show_barmenu() {
?>
<h1>NUTCRACKER: RGB Effects Builder</h1>
<ul id="sddm">
	<li><a href="/nutcracker" onmouseover="mopen('m1')" onmouseout="mclosetime()">Home</a></li>
	<li><a href="/nutcracker/login/member-index.php" onmouseover="mopen('m2')" onmouseout="mclosetime()">Models</a></li>
	<li><a href="/nutcracker/effects/effect-form.php" onmouseover="mopen('m3')" onmouseout="mclosetime()">Effects</a></li>
	<li><a href="/nutcracker/effects/project.php" onmouseover="mopen('m4')" onmouseout="mclosetime()">Projects</a></li>
	<li><a href="/nutcracker/admin/admin.php" onmouseover="mopen('m5')" onmouseout="mclosetime()">Admin</a>
		<div id="m5" onmouseover="mcancelclosetime()" onmouseout="mclosetime()">
		<a href="/nutcracker/effects/cleanup.php">Cleanup Old Effects and Models</a>
		<a href="/nutcracker/effects/bug.php">Open a Bug or Enhancement ticket</a>
		<a href="/nutcracker/effects/tutorials.html">Nutcracker Tutorials</a>
		</div>
	</li>
	<li><a href="#" onmouseover="mopen('m7')" onmouseout="mclosetime()">Help</a>
		<div id="m7" onmouseover="mcancelclosetime()" onmouseout="mclosetime()">
		<a href="#">Nutcracker Tutorials</a>
		<a href="#">Nutcracker FAQ</a>
		<a href="#">Nutcracker Wiki</a>
		</div>
	</li>
	<li><a href="#" onmouseover="mopen('m8')" onmouseout="mclosetime()">Contact</a>
		<div id="m8" onmouseover="mcancelclosetime()" onmouseout="mclosetime()">
		<a href="#">E-mail</a>
		<a href="#">Submit Request Form</a>
		</div>
	</li>
	<li><a href="/nutcracker/login/logout.php" onmouseover="mopen('m6')" onmouseout="mclosetime()">Logout</a></li>
</ul>
<div style="clear:both"></div>
<?php
}
?>