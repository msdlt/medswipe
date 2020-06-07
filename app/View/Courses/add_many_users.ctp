<script>
$(document).ready(function() {
    $('#EventsUserAddForm').ajaxForm({
        target: '.contentWrap',
        resetForm: false,
        beforeSubmit: function() {
            $('.contentWrap').html('Loading...');
        },
        success: function(response) {
            if (isInt(response)) 
            	{
            	$('.contentWrap').html('<p class="ok">Attendee saved</p>');
            	overlayElem.overlay().close();
            	location.reload();
           	 	}
		}
    });
	$('#UserUsername').focus();
});
//from http://www.peterbe.com/plog/isint-function
function isInt(x) { 
	   var y=parseInt(x); 
	   if (isNaN(y)) return false; 
	   return x==y && x.toString()==y.toString(); 
	 }
</script>
<div class="coursesUsers form">
<?php 
echo $this->Form->create('Course');
?>
	<fieldset>
 		<legend><?php __('Add attendee');?></legend>
	<?php
		echo $this->Form->hidden('Course.id', array('value'=>$course_id));
		echo $this->Form->input('Course.usernames', array('label'=>'Oxford usernames (separated by return/line break)','type'=>'textarea'));
	?>
	</fieldset>
<div class="controls">
<?php echo $this->Form->button('Add attendees', array('type'=>'submit', 'class'=>'submit'));
echo $this->Form->button('Cancel', array('type'=>'button', 'class'=>'cancel', 'onclick'=>'window.location=\''.$this->Html->url(array('controller'=>'courses', 'action'=>'edit',$course_id)).'\';'));?>
</div>
<?php 
echo $this->Form->end();
?>
</div>