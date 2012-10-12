<?php
/*
 *
 * Function getXML($infile)				// read in the file into an XML element object
 * Function XMLtoArray($project)		// translates the XML element object to an array
 * Function ArraytoXMLStr($newXMLArray)	// translates the XML array to a XML string 
 * Function ArraytoXML($newXMLArray) 	// tranlates the XML array to an XML element object
 * Function SaveXML($newXML,$outfile)	//write the XML element object out to a file
*/
require_once ("dbcontrol.php");
require_once('../conf/auth.php');

$newXMLArray=DBtoXMLArray('11');
$newXML=ArraytoXML($newXMLArray);
$outfile="mytest.xml";
SaveXML($newXML,$outfile); //write the XML element object out to a file

function DBtoXMLArray($project_id) {
	//Translate Project Information
	$projArray=array();
	$sql = "SELECT `song_id`, `username`, `frame_delay`, `model_name` FROM `project` WHERE project_id=".$project_id;
	$result=nc_query($sql);
	$row=mysql_fetch_array($result,MYSQL_ASSOC);
	$projArray['project_id']=$project_id;
	$projArray['ProjectName']=$row['username']."~".$row['model_name'];
	$projArray['Timing']=$row['frame_delay'];
	$username=$row['username'];
	$projArray['username']=$username;
	$targetName=$row['model_name'];
	$projArray['target']=$row['model_name'];
	$song_id=$row['song_id'];
	
	//Tranlate Song Information
	$song=array();
	$sql = "SELECT `song_id`, `active_set`, `song_name`, `artist`, `song_url`, `last_updated`, `audacity_aup`, `music_mo_file`, `username` FROM `song` WHERE song_id=".$song_id;
	$result=nc_query($sql);
	$row=mysql_fetch_array($result,MYSQL_ASSOC);
	$song['SongTitle']=$row['song_name'];
	$song['SongArtist']=$row['artist'];
	$song['audacity_aup']=$row['audacity_aup'];
	$song['music_mo_file']=$row['music_mo_file'];
	$song['active_set']=$row['active_set'];
	$song['SongURL']=$row['song_url'];
	$defaultPhrases=array();
	$songmin=99999.0;
	$songmax=00000.0;
	$sql = "SELECT song_dtl_id, `phrase_name`, `start_secs`, `end_secs`, `sequence` FROM `song_dtl` WHERE song_id=".$song_id;
	$result=nc_query($sql);
	while($row=mysql_fetch_array($result,MYSQL_ASSOC)) {
		$id=$row['song_dtl_id'];
		$pname=$row['phrase_name'];
		$st=$row['start_secs'];
		$et=$row['end_secs'];
		$seq=$row['sequence'];
		if ($st<$songmin)
			$songmin=$st;
		if ($et>$songmax)
			$songmax=$et;
		$defaultPhrases[]=array("id"=>$id,"PhraseName"=>$pname, "StartTime"=>$st, "EndTime"=>$et, "Sequence"=>$seq);
	}
	$song['defaultPhrases']=$defaultPhrases;
	$songlength=$songmax-$songmin;
	$song['SongLength']=$songlength;
	$projArray['Song']=$song;

	// Translate Project Detail
	$sql = "SELECT `project_dtl_id`, `phrase_name`, `start_secs`, `end_secs`, `effect_name`, `project_id` FROM project_dtl WHERE project_id=".$project_id;
	$result=nc_query($sql);
	$effectArray=array();
	while($row=mysql_fetch_array($result,MYSQL_ASSOC)) {
		$pname=$row['phrase_name'];
		$st=(real) $row['start_secs'];
		$et=(real) $row['end_secs'];
		$id=(integer) $row['project_dtl_id'];
		$effect=(string) $row['effect_name'];
		$effectArray[]=$effect;
		$projDetail[]=array("id"=>$id,"PhraseName"=>$pname, "StartTime"=>$st, "EndTime"=>$et, "EffectName"=>$effect);
	}
	$projArray['projDetail']=$projDetail;
	

	//Translate Project Effects
	$projEffect=array();
	foreach($effectArray as $effect) {
		$paramArray=array();
		$effName=$effect;
		$sql = "SELECT `username`, `effect_name`, `param_name`, `param_value`, `segment`, `created`, `last_upd` FROM `effects_user_dtl` WHERE username='".$username."' AND effect_name='".$effName."'";
		$result=nc_query($sql);
		while($row=mysql_fetch_array($result,MYSQL_ASSOC)) {		
			$key=$row['param_name'];
			$val=$row['param_value'];
			$paramArray[$key]= $val;
		}
		$projEffect[]=array("EffectName"=>$effName,"Parameters"=>$paramArray);
	}
	$projArray['Effects']=$projEffect;

	//Translate Target Information
	$target=array();
	$sql = "SELECT `username`, `object_name`, `object_desc`, `model_type`, `string_type`, `pixel_count`, `folds`, `start_bottom`, `pixel_first`, 
	`pixel_last`, `pixel_length`, `pixel_spacing`, `unit_of_measure`, `total_strings`, `total_pixels`, `window_degrees`, `number_segments`, `gif_model`, 
	`direction`, `orientation`, `topography`, `h1`, `h2`, `d1`, `d2`, `d3`, `d4`, `date_created`, `last_updated` FROM `models`
	 WHERE object_name='".$targetName."' AND username='".$username."'";
	$result=nc_query($sql);
	$row=mysql_fetch_array($result,MYSQL_ASSOC);	
	$target['TargetName']=$targetName;
	$target['TargetType']=$row['model_type'];
	$target['Strings']=$row['total_strings'];
	$target['Pixels']=$row['total_pixels'];
	$target['Strands']=$row['folds'];
	$target['bottomStart']=$row['start_bottom'];
	$target['TargetDisplay']=$row['window_degrees'];
	$target['TargetDesc']=$row['object_desc'];
	$projArray['Target']=$target;
	return ($projArray);
}

function getXML($infile) {
	//Get the XML document loaded into a variable
	$xml = file_get_contents($infile);
	//Set up the parser object
	$returnXML = simplexml_load_string($xml);
	return($returnXML);
}
function SaveXML($xml,$outfile) {
  $xml->asXML($outfile); 
}

function ArraytoXML($inar) {
	$retVal= new SimpleXMLElement(ArraytoXMLStr($inar));
	return($retVal);
}

function ArraytoXMLStr($inar) {
	$xmlStr='<Project id="'.$inar['project_id'].'" Name="'.$inar['ProjectName'].'" Timing="'.$inar['Timing'].'" username="'.$inar['username'].'" target="'.$inar['target'].'">'."\n";
	
	// Translate Song 
	$xmlStr.='	<Song SongTitle="'.$inar['Song']['SongTitle'].'" SongLength="'.$inar['Song']['SongLength'].'" SongArtist="'.$inar['Song']['SongTitle'].'" AudacityAup="'.$inar['Song']['audacity_aup'].'" MusicMoFile="'.$inar['Song']['music_mo_file'].'" ActiveSet="'.$inar['Song']['active_set'].'"'.">\n";
	$xmlStr.='		<SongURL>'.$inar['Song']['SongURL']."</SongURL>\n		<DefaultPhrases>\n";
	foreach ($inar['Song']['defaultPhrases'] as $phrase) {
		$id=$phrase['id'];
		$pname=$phrase['PhraseName'];
		$st=$phrase['StartTime'];
		$et=$phrase['EndTime'];
		$seq=$phrase['Sequence'];
		$xmlStr.='			<Phrase id="'.$id.'" PhraseName="'.$pname.'" StartTime="'.$st.'" EndTime="'.$et.'" Sequence="'.$seq.'"></Phrase>'."\n";
	}
	$xmlStr.="		</DefaultPhrases>\n";
	$xmlStr.="	</Song>\n";
	
	//Translate Effects
	$xmlStr.="	<Effects>\n";
	foreach($inar['Effects'] as $effect) {
		$name=$effect['EffectName'];
		$xmlStr.='		<Effect EffectName="'.$name.'">'."\n";
		$xmlStr.="			<Parameters>\n";
		foreach($effect['Parameters'] as $key=>$val) {
			$xmlStr.="				<".$key.">".$val."</".$key.">\n";
		}
		$xmlStr.="			</Parameters>\n";
		$xmlStr.="		</Effect>\n";
	}
	$xmlStr.="	</Effects>\n";
	
	//Translate Target
	$xmlStr.='	<Target Name="'.$inar['Target']['TargetName'].'" Type="'.$inar['Target']['TargetType'].'" Strings="'.$inar['Target']['Strings'].'" Pixels="'.$inar['Target']['Pixels'].'"';
	$xmlStr.=' Strands="'.$inar['Target']['Strands'].'" BotStart="'.$inar['Target']['bottomStart'].'" Display="'.$inar['Target']['TargetDisplay'].'">'."\n";
	$xmlStr.="		<TargetDesc>".$inar['Target']['TargetDesc']."</TargetDesc>\n	</Target>\n";

	//Translate Project Details
	$xmlStr.="	<ProjectDetails>\n";
	foreach($inar['projDetail'] as $detail) {
		$xmlStr.='		<Phrase id="'.$detail['id'].'" PhraseName="'.$detail['PhraseName'].'" StartTime="'.$detail['StartTime'].'" EndTime="'.$detail['EndTime'].'">'."\n";
		$xmlStr.='			<Effect>'.$detail['EffectName']."</Effect>\n";
		$xmlStr.="		</Phrase>\n";
	}

	$xmlStr.="	</ProjectDetails>\n";
	$xmlStr.="</Project>\n";
	return($xmlStr);
}

function XMLtoArray($project) {
	// Translate Project Detail
	$projDetail=array();
	foreach($project->ProjectDetails->Phrase as $phrase) {
		$pname=(string) $phrase['PhraseName'];
		$st=(real) $phrase['StartTime'];
		$et=(real) $phrase['EndTime'];
		$id=(integer) $phrase['id'];
		$effect=(string) $phrase->Effect;
		$projDetail[]=array("id"=>$id,"PhraseName"=>$pname, "StartTime"=>$st, "EndTime"=>$et, "EffectName"=>$effect);
	}
	//Translate Project Effects
	$projEffect=array();
	foreach($project->Effects->Effect as $effect) {
		$paramArray=array();
		$effName=(string) $effect['EffectName'];
		foreach($effect->Parameters->children() as $param) {
			$key=(string) $param->getName();
			$val=(string) $param;
			$paramArray[$key]= $val;
		}
		$projEffect[]=array("EffectName"=>$effName, "Parameters"=>$paramArray);
	}

	//Translate Target Information
	$target=array();
	$target['TargetName']=(string) $project->Target['Name'];
	$target['TargetType']=(string) $project->Target['Type'];
	$target['Strings']=(integer) $project->Target['Strings'];
	$target['Pixels']=(integer) $project->Target['Pixels'];
	$target['Strands']=(integer) $project->Target['Strands'];
	$target['bottomStart']=(string) $project->Target['BotStart'];
	$target['TargetDisplay']=(integer) $project->Target['Display'];
	$target['TargetDesc']=(string) $project->Target->TargetDesc;

	//Tranlate Song Information
	$song=array();
	$song['SongTitle']=(string) $project->Song['SongTitle'];
	$song['SongLength']=(integer) $project->Song['SongLength'];	
	$song['SongArtist']=(string) $project->Song['SongArtist'];
	$song['audacity_aup']=(string) $project->Song['audacity_aup'];
	$song['music_mo_file']=(string) $project->Song['music_mo_file'];
	$song['active_set']=(string) $project->Song['active_set'];	
	$song['SongURL']=(string) $project->Song->SongURL;
	$defaultPhrases=array();
	foreach($project->Song->DefaultPhrases->Phrase as $phrase) {
		$id=(integer) $phrase['id'];
		$pname=(string) $phrase['PhraseName'];
		$st=(real) $phrase['StartTime'];
		$et=(real) $phrase['EndTime'];
		$seq=(integer) $phrase['Sequence'];
		$defaultPhrases[]=array("id"=>$id,"PhraseName"=>$pname, "StartTime"=>$st, "EndTime"=>$et, "Sequence"=>$seq);
	}
	$song['defaultPhrases']=$defaultPhrases;

	//Translate Project Information
	$projArray=array();
	$projArray['project_id']=(integer) $project['id'];
	$projArray['ProjectName']=(string) $project['Name'];
	$projArray['Timing']=(integer) $project['Timing'];
	$projArray['username']=(string) $project['username'];
	$projArray['target']=(string) $project['target'];

	//Add earlier translated arrays
	$projArray['projDetail']=$projDetail;
	$projArray['Effects']=$projEffect;
	$projArray['Target']=$target;
	$projArray['Song']=$song;
	return ($projArray);
}