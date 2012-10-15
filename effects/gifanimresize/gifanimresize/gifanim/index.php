<?
	/* INCLUDE GIF SPLIT / MERGE CLASSES */
	require_once("gifsplit.php"); 
	
	/* INCLUDE IMAGE RESIZING FUNCTIONS */
	require_once("functions.php"); 	

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>GIF resize test</title>
</head>
<body>
	<h1>Resizing GIF animations with PHP / GD</h1>
	<h2>Sources</h2>
	<p>
		<a href="gifanimresize.zip">.zip file</a><br />
		To install, just extract the zip to an Apache folder, create a directory called "output" and grant write access to it.
	</p>


	<h2>Example</h2>
	<p>
		Original image<br />
		<img src="ark02.gif" />
	</p>

	<p>
		500x500<br />
		<?
			scaleImageFile(
				"ark02.gif",
				500,
				500,
				"output/500x500.gif",
				4
			);
		?>
		<img src="output/500x500.gif" />
	</p>

	<p>
		100x100<br />
		<?
			scaleImageFile(
				"ark02.gif",
				100,
				100,
				"output/100x100.gif",
				4
			);
		?>
		<img src="output/100x100.gif" />
	</p>

	<p>
		1000x50<br />
		<?
			scaleImageFile(
				"ark02.gif",
				1000,
				50,
				"output/1000x50.gif",
				4
			);
		?>
		<img src="output/1000x50.gif" />
	</p>
	
</body>
</html>