
<h1>Releases for Nutcracker</h1>
<pre>
Version 1.0 of Nutcracker was released Feb 2012. This was only a web based tool to create animated effects for RGB devices.
Version 2.0 was released summer of 2012. Version 2.0 supported a local install of Nutcracker using either WAMP or XAMPP.
This version also introduced Projects.
Version 3.0 of Nutcracker was released Feb 2013. This complete rewrite has the following changes
</pre>
<ol>
<li>Code rewrite into C++ instead of PHP.
<li>Will not need XAMPP/WAMP
<li>Gives feedback immediately.
</ol>

<?php
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
Tun update gallery database
create_gallery.php?INSERT_NEW_GIFS=1
*/
set_time_limit(0);
//ini_set("memory_limit","2024M");

$n=0;

$dir="../releases";
echo "<table border=1>";
echo "<tr><th>Version</th><th>File to download</th><th>File Size</th></tr>\n";
$n=getFilesFromDir($dir,$n);
echo "</table>";
echo "<a href=README.txt>README of release notes</a>";
echo "<br/><h2>Tutorials to describe Nutcracker Version 3.0</h2>\n";
?>

I have written an beginners tutorial on setting up xLights/Nutcracker. This process will get you to an
  exported file for whatever sequencer you are using
<br/>PDF Document
<a href=http://nutcracker123.com/nutcracker/tutorials/intro.pdf>Nutcracker 3.0 Introduction</a>
<br/>
Vimeo Movie tutorials:
<ul>
	<li><a href=https://vimeo.com/57960516>xLights and Nutcracker introduction</a></li>
	<li><a href=https://vimeo.com/57960775>Creating a text effect</a></li>
	<li><a href=https://vimeo.com/57961300>Creating two Nuctracker effects and layering between them</a></li>
	<li><a href=https://vimeo.com/57960884>Exporting a xLights/Nutcracker sequence into LOR lms</a></li>
	<li><a href=https://vimeo.com/60850925>Feb 28th Tutorial. Intro to xLights and Nutcracker 3.0</a></li>

</ul>
<br/>
<?php
echo "<br/><h2>What are differences between old Nutcracker and the new Version 3.0?</h2>\n";
echo "<table border=1>";
/*BARS
BUTTERFLY
COLORWASH
COUNTDOWN
FIRE
GARLANDS
GIF
LIFE
METEORS
PICTURES
PINWHEEL
SNOWFLAKES
SNOWSTORM
SPIRALS
TEXT
TREE
TWINKLE*/
echo "<tr><td>TARGET MODELS</td><td><table border=1>";
echo "<tr><th>Model</th><th>Version 2.x</th><th>Version 3.x</th></tr>";
echo "<tr><td > MEGATREE 360 degree</td><td bgcolor=\"#88FF88\">Yes</td><td bgcolor=\"#88FF88\">Yes</td></tr>";
echo "<tr><td > MEGATREE 270 degree</td><td bgcolor=\"#88FF88\">Yes</td><td bgcolor=\"#88FF88\">Yes</td></tr>";
echo "<tr><td > MEGATREE 180 degree</td><td bgcolor=\"#88FF88\">Yes</td><td bgcolor=\"#88FF88\">Yes</td></tr>";
echo "<tr><td > MEGATREE 90 degree</td><td bgcolor=\"#88FF88\">Yes</td><td bgcolor=\"#88FF88\">Yes</td></tr>";
echo "<tr><td > Vertical Matrix</td><td bgcolor=\"#88FF88\">Yes</td><td bgcolor=\"#88FF88\">Yes</td></tr>";
echo "<tr><td > Horizontal Matrix</td><td bgcolor=\"#FF8888\">No</td><td bgcolor=\"#88FF88\">Yes</td></tr>";
echo "<tr><td > Single Strand, Eaves</td><td bgcolor=\"#88FF88\">Yes</td><td bgcolor=\"#88FF88\">Limited</td></tr>";
echo "<tr><td > Single Strand, Arches</td><td bgcolor=\"#88FF88\">Yes</td><td bgcolor=\"#88FF88\">Limited</td></tr>";
echo "<tr><td > Single Strand, Windows</td><td bgcolor=\"#88FF88\">Yes</td><td bgcolor=\"#88FF88\">Limited</td></tr>";
echo "</table>";
echo "</td></tr>";
echo "<tr><td>EFFECTS</td><td><table border=1>";
echo "<tr><th>Effect Name</th><th>Version 2.x</th><th>Version 3.x</th></tr>";
echo "<tr><td > BARS</td><td bgcolor=\"#88FF88\">Yes</td><td bgcolor=\"#88FF88\">Yes</td></tr>";
echo "<tr><td> BUTTERFLY</td><td bgcolor=\"#88FF88\">Yes</td><td bgcolor=\"#88FF88\">Yes</td></tr>";
echo "<tr><td> COLORWASH</td><td bgcolor=\"#88FF88\">Yes</td><td bgcolor=\"#88FF88\">Yes</td></tr>";
echo "<tr><td> COUNTDOWN</td><td bgcolor=\"#88FF88\">Yes</td><td bgcolor=\"#FF8888\">No</td></tr>";
echo "<tr><td> FIRE</td><td bgcolor=\"#88FF88\">Yes</td><td bgcolor=\"#88FF88\">Yes</td></tr>";
echo "<tr><td> GARLANDS</td><td bgcolor=\"#88FF88\">Yes</td><td bgcolor=\"#88FF88\">Yes</td></tr>";
echo "<tr><td> GIF</td><td bgcolor=\"#88FF88\">Yes</td><td bgcolor=\"#FF8888\">No</td></tr>";
echo "<tr><td> LIFE</td><td bgcolor=\"#88FF88\">Yes</td><td bgcolor=\"#88FF88\">Yes</td></tr>";
echo "<tr><td> METEORS</td><td bgcolor=\"#88FF88\">Yes</td><td bgcolor=\"#88FF88\">Yes</td></tr>";
echo "<tr><td> PICTURES</td><td bgcolor=\"#88FF88\">Yes</td><td bgcolor=\"#88FF88\">Yes</td></tr>";
echo "<tr><td> PINWHEEL</td><td bgcolor=\"#88FF88\">Yes</td><td bgcolor=\"#FF8888\">No</td></tr>";
echo "<tr><td> SNOWFLAKES</td><td bgcolor=\"#88FF88\">Yes</td><td bgcolor=\"#88FF88\">Yes</td></tr>";
echo "<tr><td> SNOWSTORM</td><td bgcolor=\"#88FF88\">Yes</td><td bgcolor=\"#88FF88\">Yes</td></tr>";
echo "<tr><td> SPIRALS</td><td bgcolor=\"#88FF88\">Yes</td><td bgcolor=\"#88FF88\">Yes</td></tr>";
echo "<tr><td> TREE</td><td bgcolor=\"#88FF88\">Yes</td><td bgcolor=\"#88FF88\">Yes</td></tr>";
echo "<tr><td> TWINKLE</td><td bgcolor=\"#88FF88\">Yes</td><td bgcolor=\"#88FF88\">Yes</td></tr>";
echo "<tr><td> SPIROGRAPH</td><td bgcolor=\"#FF8888\">No</td><td bgcolor=\"#88FF88\">Yes</td></tr>";
echo "</table>";
echo "</td></tr>";
echo "<tr><td>SEQUENER OUTPUTS</td><td><table border=1>";
echo "<tr><th>Output</th><th>Version 2.x</th><th>Version 3.x</th></tr>";
echo "<tr><td> Vixen: *.vix</td><td bgcolor=\"#88FF88\">Yes</td><td bgcolor=\"#88FF88\">Yes</td></tr>";
echo "<tr><td> Vixen: *.vir</td><td bgcolor=\"#88FF88\">Yes</td><td bgcolor=\"#88FF88\">Yes</td></tr>";
echo "<tr><td> LOR: *.lms</td><td bgcolor=\"#88FF88\">Yes</td><td bgcolor=\"#88FF88\">Yes</td></tr>";
echo "<tr><td> LOR: *.las</td><td bgcolor=\"#FF8888\">No</td><td bgcolor=\"#88FF88\">Yes</td></tr>";
echo "<tr><td> LOR: *.lcb</td><td bgcolor=\"#88FF88\">Yes</td><td bgcolor=\"#88FF88\">Yes</td></tr>";
echo "<tr><td> HLS: *.hlsnc</td><td bgcolor=\"#88FF88\">Yes</td><td bgcolor=\"#88FF88\">Yes</td></tr>";
echo "<tr><td> LSP: *.msq</td><td bgcolor=\"#FF8888\">No</td><td bgcolor=\"#FF8888\">No</td></tr>";
echo "<tr><td> LSP: *.asq</td><td bgcolor=\"#FF8888\">No</td><td bgcolor=\"#FF8888\">No</td></tr>";
echo "<tr><td> LSP: UserPatterns.xml</td><td bgcolor=\"#88FF88\">Yes</td><td bgcolor=\"#FF8888\">No</td></tr>";
echo "<tr><td> xLights: *.xseq</td><td bgcolor=\"#FF8888\">No</td><td bgcolor=\"#88FF88\">Yes</td></tr>";
echo "<tr><td> Conductor: *.seq</td><td bgcolor=\"#FF8888\">No</td><td bgcolor=\"#88FF88\">Yes</td></tr>";
echo "</table>";
echo "</td></tr>";
echo "<tr><td>OTHER DIFFERENCES</td><td><table border=1>";
echo "<tr><th>Description</th><th>Version 2.x</th><th>Version 3.x</th></tr>";
echo "<tr><td>Effect Gallery</td><td bgcolor=\"#88FF88\">Yes</td><td bgcolor=\"#FF88\">Not Yet</td></tr>";
echo "<tr><td>Fade in/Out on effects</td><td bgcolor=\"#88FF88\">Yes</td><td bgcolor=\"#FF88\">Not Yet</td></tr>";
echo "<tr><td>Projects</td><td bgcolor=\"#88FF88\">Yes</td><td bgcolor=\"#FF88\">Not Yet</td></tr>";
echo "<tr><td>Data saved and retrieved from database</td><td bgcolor=\"#88FF88\">Yes</td><td bgcolor=\"#FF88\">Not Yet</td></tr>";
echo "<tr><td>Ability to play mp3 and watch Nutcracker animations</td><td bgcolor=\"#FF8888\">No</td><td bgcolor=\"#88FF88\">Yes</td></tr>";
echo "<tr><td>Convert between different sequencers</td><td bgcolor=\"#FF8888\">No</td><td bgcolor=\"#88FF88\">Yes</td></tr>";
echo "</table>";
echo "</td></tr>";
echo "</table>";

function getFilesFromDir($dir,$n){
	require_once('../conf/config.php');
	//Connect to mysql server
	$files=array();
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link){
		die('Failed to connect to server: ' . mysql_error());
	}
	//Select database
	$db = mysql_select_db(DB_DATABASE);
	if(!$db){
		die("Unable to select database");
	}
	$line=$line0=0;
	if($handle = opendir($dir)){
		while(false !== ($file = readdir($handle))){
			if($file != "." && $file != ".." ){
				if(is_dir($dir.'/'.$file)){
					$dir2 = $dir.'/'.$file; 
					$n=getFilesFromDir($dir2,$n);
				}
				else{ 
					$path_parts = pathinfo($file);  // workspaces/nuelemma/MEGA_001+SEAN_d_22.dat
					$dirname   = $path_parts['dirname']; // workspaces/nuelemma
					$basename  = $path_parts['basename']; // MEGA_001+SEAN_d_22.dat
					$extension =$path_parts['extension']; // .dat
					$filename  = $path_parts['filename']; // MEGA_001+SEAN_d_22
					$tokens=explode("/",$dirname);
					echo "<pre>";
				//	print_r($path_parts);
				
					echo "</pre>";
					$tokens=explode("_",$filename);
					$c=count($tokens);
					if($c==5)
					{
					$version = $tokens[2] . "." . $tokens[3] . "." . $tokens[4];
					if($c==5 and $tokens[2]>=3)
					{
						
					$sizebytes=filesize($basename);
					$sizestring=human_filesize($sizebytes);
					$files[]=$basename;
					}
					}
					//	0 = workspaces
					//	1 = nuelemma or id
					//
					
				} // processing a file, not a directory
			} // if ($file != "." && $file != ".." )
		} // while (false !== ($file = readdir($handle)))
	}
	closedir($handle);
	rsort($files);
	foreach ($files as $basename)
	{
	$tok_file=explode(".exe",$basename);
	$filename=$tok_file[0];
	$tokens=explode("_",$filename);
					$c=count($tokens);
					
					$version = $tokens[2] . "." . $tokens[3] . "." . $tokens[4];
		$sizebytes=filesize($basename);
		$sizestring=human_filesize($sizebytes);
		echo "<tr><td>$version</td><td><a href=$basename>$basename</a></td><td>$sizestring</td></tr>\n";
	}
	return $n;
}
function human_filesize($bytes, $decimals = 2) {
  $sz = 'BKMGTP';
  $factor = floor((strlen($bytes) - 1) / 3);
  return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
}