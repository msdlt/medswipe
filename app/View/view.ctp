<div>
<h2><?php echo $user['User']['first_name']." ".$user['User']['last_name'];?></h2>
<?php
debug("ROOT.DS.APP_DIR.DS.WEBROOT_DIR.DS.img/attendees/".strtolower(str_replace(" ","_",$user['User']['last_name']))."_".strtolower(str_replace(" ","_",$user['User']['first_name'])).".jpg");
if(file_exists($this->webroot."app/webroot/img/attendees/".strtolower(str_replace(" ","_",$user['User']['last_name']))."_".strtolower(str_replace(" ","_",$user['User']['first_name'])).".jpg"))
	{
	echo $this->Html->image("attendees/".strtolower(str_replace(" ","_",$user['User']['last_name']))."_".strtolower(str_replace(" ","_",$user['User']['first_name'])).".jpg", array("height"=>"300"));
	}
else 
	{
	echo "Can't find an image for this attendee yet";
	}
	?>
</div>

