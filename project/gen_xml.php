<?php
function genXML($username, $project_id) {
	$outfile="workarea/".$username."~".$project_id.".xml";
	$newXMLArray=DBtoXMLArray($project_id);
	$newXML=ArraytoXML($newXMLArray);
	SaveXML($newXML,$outfile);
	return($outfile);
}

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
		$sql = "SELECT `effect_class`, username, effect_name, `effect_desc` FROM `effects_user_hdr` WHERE username='".$username."' AND effect_name='".$effName."'";
		$sql2 = "SELECT `username`, `effect_name`, `param_name`, `param_value`, `segment`, `created`, `last_upd` FROM `effects_user_dtl` WHERE username='".$username."' AND effect_name='".$effName."'";
		$result=nc_query($sql);
		$row=mysql_fetch_array($result,MYSQL_ASSOC);
		$effClass=$row['effect_class'];
		$effDesc=$row['effect_desc'];
		$result2=nc_query($sql2);
		while($row=mysql_fetch_array($result2,MYSQL_ASSOC)) {		
			$key=$row['param_name'];
			$val=$row['param_value'];
			$paramArray[$key]= $val;
		}
		$projEffect[]=array("EffectName"=>$effName,"EffectClass"=>$effClass,"EffectDesc"=>$effDesc,"Parameters"=>$paramArray);
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
	$target['TargetDesc']=$row['object_desc'];
	$target['TargetType']=$row['model_type'];
	$target['StringType']=$row['string_type'];
	$target['PixelCount']=$row['pixel_count'];
	$target['Folds']=$row['folds'];
	$target['BottomStart']=$row['start_bottom'];
	$target['PixelFirst']=$row['pixel_first'];
	$target['PixelLast']=$row['pixel_last'];
	$target['PixelLength']=$row['pixel_length'];
	$target['Units']=$row['unit_of_measure'];
	$target['TotalStrings']=$row['total_strings'];
	$target['TotalPixels']=$row['total_pixels'];
	$target['TargetDisplay']=$row['window_degrees'];
	$target['NumSegments']=$row['number_segments'];
	$target['GifModel']=$row['gif_model'];
	$target['Direction']=$row['direction'];
	$target['Orientation']=$row['orientation'];
	$target['Topography']=$row['topography'];
	$target['h1']=$row['h1'];
	$target['h2']=$row['h2'];
	$target['d1']=$row['d1'];
	$target['d2']=$row['d2'];
	$target['d3']=$row['d3'];
	$target['d4']=$row['d4'];
	//$target['Strands']=$row['folds'];

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
		$class=$effect['EffectClass'];
		$desc=$effect['EffectDesc'];
		$xmlStr.='		<Effect EffectName="'.$name.'" EffectClass="'.$class.'" EffectDesc="'.$desc.'">'."\n";
		$xmlStr.="			<Parameters>\n";
		foreach($effect['Parameters'] as $key=>$val) {
			$xmlStr.="				<".$key.">".$val."</".$key.">\n";
		}
		$xmlStr.="			</Parameters>\n";
		$xmlStr.="		</Effect>\n";
	}
	$xmlStr.="	</Effects>\n";
	
	//Translate Target
	$TargArray=array('TargetName','TargetDesc','TargetType','StringType','PixelCount','Folds','BottomStart','PixelFirst','PixelLast','Units','TotalStrings','TotalPixels','TargetDisplay','NumSegments','GifModel','Direction','Orientation','Topography','h1','h2','d1','d2','d3','d4');
	$xmlStr.='	<Target';
	foreach($TargArray as $field) 
		$xmlStr.=' '.$field.'="'.$inar['Target'][$field].'"';
	$xmlStr.=">\n";
	$xmlStr.="		<TargetDesc>".$inar['Target']['TargetDesc']."</TargetDesc>\n	</Target>\n";

	//Translate Project Details
	$xmlStr.="	<ProjectDetails>\n";
	foreach($inar['projDetail'] as $detail)
		$xmlStr.='		<Phrase id="'.$detail['id'].'" PhraseName="'.$detail['PhraseName'].'" StartTime="'.$detail['StartTime'].'" EndTime="'.$detail['EndTime'].'" EffectName="'.$detail['EffectName'].'">'."</Phrase>\n";
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
		$effect=(string) $phrase['EffectName'];
		$projDetail[]=array("id"=>$id,"PhraseName"=>$pname, "StartTime"=>$st, "EndTime"=>$et, "EffectName"=>$effect);
	}
	//Translate Project Effects
	$projEffect=array();
	foreach($project->Effects->Effect as $effect) {
		$paramArray=array();
		$effName=(string) $effect['EffectName'];
		$effClass=(string) $effect['EffectClass'];
		$effDesc=(string) $effect['EffectDesc'];
		foreach($effect->Parameters->children() as $param) {
			$key=(string) $param->getName();
			$val=(string) $param;
			$paramArray[$key]= $val;
		}
		$projEffect[]=array("EffectName"=>$effName, "EffectClass"=>$effClass,"EffectDesc"=>$effDesc, "Parameters"=>$paramArray);
	}

	//Translate Target Information
	$TargArray=array('TargetName','TargetDesc','TargetType','StringType','PixelCount','Folds','BottomStart','PixelFirst','PixelLast','Units','TotalStrings','TotalPixels','TargetDisplay','NumSegments','GifModel','Direction','Orientation','Topography','h1','h2','d1','d2','d3','d4');
	$TargType=array('string','string','string','string','integer','integer','string','integer','integer','string','integer','integer','integer','integer','string','string','string','string','real','real','real','real','real','real'); 
	$target=array();
	for ($x=0;$x<count($TargArray);$x++) {
		$type=$TargType[$x];
		$field=$TargArray[$x];
		if ($type=='string')
			$target[$field]=(string) $project->Target[$field];
		else
			if ($type=='integer')
				$target[$field]=(integer) $project->Target[$field];
			else
				$target[$field]=(real) $project->Target[$field];
	}
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