
<table border=1>
<tr>
<td><a href="../index.html">
<?php if($menu=="index") echo "<font color=blue><b>"; ?>
Home
<?php if($menu=="index") echo "</b></font>";?>
</a></td>

<td><a href="../login/member-index.php">
<?php if($menu=="member-index") echo "<font color=blue><b>"; ?>
Target Generator
<?php if($menu=="member-index") echo "</b></font>";?>
</a></td>

<td><a href="../effects/effect-form.php">
<?php if($menu=="effect-form") echo "<font color=blue><b>"; ?>
Effects Generator
<?php if($menu=="effect-form") echo "</b></font>";?>
</a></td>

<td><a href="../effects/cleanup.php">
<?php if($menu=="cleanup") echo "<font color=blue><b>"; ?>
Cleanup old targets and effects
<?php if($menu=="cleanup") echo "</b></font>";?>
</a></td>

<td><a href="../effects/project-form.php">
<?php if($menu=="project-form") echo "<font color=blue><b>"; ?>
Nutcracker Projects
<?php if($menu=="project-form") echo "</b></font>";?>
</a></td>

<td><a href="../effects/bug.php">
<?php if($menu=="bug") echo "<font color=blue><b>"; ?>
Open a Bug or Enhancement Ticket
<?php if($menu=="bug") echo "</b></font>";?>
</a></td>


<td><a href="../effects/tutorials.html">
<?php if($menu=="tutorials") echo "<font color=blue><b>"; ?>
Nutcracker Tutorials
<?php if($menu=="tutorials") echo "</b></font>";?>
</a></td>


<td><a href="../login/logout.php">Logout</a></td>
<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
<td>
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

</td>
</tr>
</table>

