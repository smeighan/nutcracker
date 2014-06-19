
<h1>Releases for Nutcracker</h1>


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
$files=array();

#$n=getFilesFromDir($dir,$n);
$files = directoryToArray("./", false);

foreach($files as $n=>$filename)
{
	$filename2 = str_replace('./','',$filename);
	$noext = explode('.',$filename2);	

	if($noext[1]=="exe")
	{
		$tokens=explode("_",$noext[0]);
		#$version=$v1 . "_" . $v2 . "_" . $v3;
		#echo "c=$c, v1,v2,v3=$v1,$v2,$v3,version=$version\n";

		$version  = substr($tokens[2]+100,1,2) . "_" . substr($tokens[3]+100,1,2) . "_" . substr($tokens[4]+100,1,2) ;
		$newname = $tokens[0] . '_' . $tokens[1] . '_' . $version . '.exe';
		$newfiles[] = $newname;
		$version_array[$newname] =  substr($tokens[2]+100,1,2) . "." . substr($tokens[3]+100,1,2) . "." . substr($tokens[4]+100,1,2) ;
		$orig_filename[$newname]=$filename2;
	}

}

rsort($newfiles);
echo "<ol>";
foreach($newfiles as $n=>$filename)
{
	$name=$orig_filename[$filename];

	$sizebytes=filesize($name);
	$sizestring=human_filesize($sizebytes);
	$version=$version_array[$filename];
	echo "<tr><td>$version</td><td><a href=" . $name . ">" .  $filename . "</a></td><td>$sizestring</td></tr>\n" ;
}

echo "</table>";
echo "<a href=README.txt>README of release notes</a>";
echo "<br/><h2>Tutorials to describe Nutcracker Version 3.0</h2>\n";
?>

I have written an beginners tutorial on setting up xLights/Nutcracker. This process will get you to an
  exported file for whatever sequencer you are using
<br/>PDF Document
<a href=http://nutcracker123.com/nutcracker/tutorials/intro.pdf>Nutcracker 3.0 Introduction</a>
<br/>
<h2>Vimeo Movie tutorials:</h2>
<ul>
	<li><a href=https://vimeo.com/57960516>xLights and Nutcracker introduction</a></li>
	<li><a href=https://vimeo.com/57960775>Creating a text effect</a></li>
	<li><a href=https://vimeo.com/57961300>Creating two Nuctracker effects and layering between them</a></li>
	<li><a href=https://vimeo.com/57960884>Exporting a xLights/Nutcracker sequence into LOR lms</a></li>
	<li><a href=https://vimeo.com/61862629>My megatree suspended from the ceiling in my basement.</a></li>
	<li><a href=https://vimeo.com/60850925>Feb 28th Tutorial. Intro to xLights and Nutcracker 3.0</a></li>
<li><a href=https://vimeo.com/61917292>Mar 14th Tutorial. Nutcracker 3, Spirograph effect</a></li>
<li><a href=https://vimeo.com/62432097>Mar 21st Tutorial. Configurator Spreadsheet. Nutcracker and Vixen</a></li>
<li><a href=https://vimeo.com/61690739>3.0.6 Release of Nutcracker with Spirograph effect</a></li>
<li><a href=https://vimeo.com/63256211>Mar 28th Tutorial. Using Pictures in Nutcracker</a></li>
<li><a href=https://vimeo.com/64391490>Apr 18th Tutorial. Intro, Vixen, LOR</a></li>
<li><a href=https://vimeo.com/64869234>Apr 25th LSP UserPatterns.cml,  Audacity marking up music phrases</a></li>
<li><a href=https://vimeo.com/66620695>May 9th Fireworks Effect. Changes coming to targets</a></li>
<li><a href=https://vimeo.com/66620693>May 16th animated gifs, text rotation</a></li>
<li><a href=https://vimeo.com/67364522>May 30th text,strobe,exchanging sequences with one another</a></li>
</ul>


<h2>For Developers only (C++)</h2>
<ul>
	<li><a href=https://vimeo.com/61068045>DEVELOPERS: Tutorial1 setting up windows development</a></li>
	<li><a href=https://vimeo.com/61068046>DEVELOPERS: Tutorial2 how to add effect to Nutcracker 3.</a></li>
	<li><a href=https://vimeo.com/61153242>DEVELOPERS Tutorial3 Create An Effect</a></li>
	</ul>
<br/>
<?php
echo "<h1>RGB Configurator Spreadsheet</h1>\n";
echo "This spread sheet will allow the comparison of the different controllers. Enter how many strings you ";
echo "plan on using, it will calculate the total cost for 6 different RGB controller systems. ";
echo "These are the five systems compared:<ol>\n";
echo "<li>Pixelnet using active hub</li>\n";
echo "<li>Pixelnet using Zeus hub (does not need smart string controllers)</li>\n";
echo "<li>J1sys ecg-p12r</li>\n";
echo "<li>SanDevices E682</li>\n";
echo "<li>Pixelnet board by dpitts/mykroft</li>\n";
echo "<li>LOR Cosmic Color Ribbon</li>\n";
echo "<li>Seasonal Equipment Rainbow Pixel Controller</li>\n";

echo "</ol>";

echo "<ul>";
echo "<li><a href=rgb_configurator.ods>rgb_configurator.ods for OpenOffice spreadsheet</a></li>\n";
echo "<li><a href=rgb_configurator.xls>rgb_configurator.xls for Excel</a></li>\n";
echo "</ul>\n";
echo "<br/><br/>";
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
echo "<tr><td >BARS</td><td bgcolor=\"#88FF88\">Yes</td><td bgcolor=\"#88FF88\">Yes</td></tr>";
echo "<tr><td> BUTTERFLY</td><td bgcolor=\"#88FF88\">Yes</td><td bgcolor=\"#88FF88\">Yes</td></tr>";
echo "<tr><td> COLORWASH</td><td bgcolor=\"#88FF88\">Yes</td><td bgcolor=\"#88FF88\">Yes</td></tr>";
echo "<tr><td> COUNTDOWN</td><td bgcolor=\"#88FF88\">Yes</td><td bgcolor=\"#FF8888\">No</td></tr>";
echo "<tr><td> FIRE</td><td bgcolor=\"#88FF88\">Yes</td><td bgcolor=\"#88FF88\">Yes</td></tr>";
echo "<tr><td> GARLANDS</td><td bgcolor=\"#88FF88\">Yes</td><td bgcolor=\"#88FF88\">Yes</td></tr>";
echo "<tr><td> GIF</td><td bgcolor=\"#88FF88\">Yes</td><td bgcolor=\"#88FF88\">Yes</td></tr>";
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
echo "<tr><td> LSP: UserPatterns.xml</td><td bgcolor=\"#88FF88\">Yes</td><td bgcolor=\"#88FF88\">Yes</td></tr>";
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

function getFilesFromDir($files)
{


/*	
[0] => ./xLights_Nutcracker_3_0_15.exe
    [1] => ./xLights_Nutcracker_3_0_1.exe
    [2] => ./xLights_Nutcracker_3_0_0.exe
    [3] => ./xLights_Nutcracker_3_0_5.exe
    [4] => ./xLights_Nutcracker_3_0_14.exe
    [5] => ./rgb_configurator.xls
}*/
	foreach($files as $filename)
	{
		$noext = explode(".",$filename);	

		$tokens=explode("_",$noext[0]);
		print_r($tokens);
		$c=count($tokens);
		#	Give 3.0.14 means v1=3, v2=0, v3=14
		$v1=string ((int)$tokens[2]+100);
		$v2=string ((int)$tokens[3]+100);
		$v3=string ((int)$tokens[4]+100);
		echo "c=$c, v1,v2,v3=$v1,$v2,$v3";
		echo "</pre>";

		if($tokens[2]>=3 and $tokens[2]<=9)
		{
			$version = substr($v1,1,2) . "." . substr($v2,1,2) . "." . substr($v3,1,2);

			$sizebytes=filesize($basename);
			$sizestring=human_filesize($sizebytes);
			$files_array[]=$version;  # was basename

		}


		rsort($files_array);
		foreach ($files_array as $basename)
		{
			$basename = $basename . ".exe";
			$sizebytes=filesize($basename);
			$sizestring=human_filesize($sizebytes);
			echo "<tr><td>$version</td><td><a href=$basename>$basename</a></td><td>$sizestring</td></tr>\n";
		}
	}
}

function directoryToArray($directory, $recursive) {
	$array_items = array();
	if ($handle = opendir($directory)) {
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != "..") {
				if (is_dir($directory. "/" . $file)) {
					if($recursive) {
						$array_items = array_merge($array_items, directoryToArray($directory. "/" . $file, $recursive));
					}
					$file = $directory . "/" . $file;
					$array_items[] = preg_replace("/\/\//si", "/", $file);
				} else {
					$file = $directory . "/" . $file;
					$array_items[] = preg_replace("/\/\//si", "/", $file);
				}
			}
		}
		closedir($handle);
	}
	return $array_items;
}


function human_filesize($bytes, $decimals = 2) {
	$sz = 'BKMGTP';
	$factor = floor((strlen($bytes) - 1) / 3);
	return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
}

