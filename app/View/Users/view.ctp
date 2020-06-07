<div>
<h2><?php echo $user['User']['first_name']." ".$user['User']['last_name'];?></h2>
<?php
$imagePath = ROOT.DS.APP_DIR.DS.WEBROOT_DIR.DS."img/attendees/".strtolower(str_replace(" ","_",$user['User']['last_name']))."_".strtolower(str_replace(" ","_",$user['User']['first_name'])).".jpg";
//debug($imagePath);
//echo '<div style="display: hidden">' . $imagePath . '</div>';
if(file_exists($imagePath))
	{
	echo $this->Html->image("attendees/".strtolower(str_replace(" ","_",$user['User']['last_name']))."_".strtolower(str_replace(" ","_",$user['User']['first_name'])).".jpg", array("height"=>"300"));
	}
else 
	{
	echo $this->Html->image("attendees/missing.jpg", array("height"=>"300"));
	}
	?>
</div>

