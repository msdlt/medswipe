<div class="courses index">
	<div class="breadcrumb">Courses</div>
	<h2><?php __('Courses');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('name');?></th>
			<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
	//debug($courses);
	
	$i = 0;
	//debug($courses);
	foreach ($courses as $course):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td>
			<?php 
				echo $course['Course']['name'];
			?>
		</td>
		<td class="actions">
			<?php 
			if($course['CoursesUser'][0]['user_type']=='admin')
				echo $this->Html->link(__('Edit course', true), array('action' => 'edit', $course['Course']['id'])); 
				echo $this->Html->link(__('View attendance', true), array('controller'=>'events_users','action'=>'view_course_attendees', $course['Course']['id'])); 
				echo "<a href='javascript:void(0);' class='modalInput' data-copy='".$course['Course']['id']."'>Copy members</a>";
				//only allow delete if no events_users for ant event in a course
				//echo $this->Html->link(__('Delete', true), array('action' => 'delete', $course['Course']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $course['Course']['id']));
			?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter('Page {:page} of {:pages}, showing {:current} records out of {:count} total');
	?>	
	</p>

	<div class="paging">
		<?php echo $this->Paginator->prev('<< ' . __('previous', true), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 |
		<?php echo $this->Paginator->next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));?>
	</div>
	<!--<p>Click <?php echo $this->Html->image('accordion/down_arrow.png');?>to display events for each course and access attendance lists</p>-->
	<div class="breadcrumb">Courses</div>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('New Course', true), array('action' => 'add')); ?></li>
	</ul>
</div>
<div id="copy_course_dialog">
	<div class="courses form">
		<?php echo $this->Form->create('Course', array('id'=>'CourseCopyForm'));?>
			<fieldset>
		 		<legend><?php __('Copy course_members'); ?></legend>
		 		<div id="copyCourseError" class="error-message"></div>
			<?php
				echo $this->Form->input('Course.name', array('label'=>'Name','class'=>'course_name'));
				echo $this->Form->hidden('CoursesUser.0.user_id',array('value'=>$this->Session->read('Auth.User.id')));
				echo $this->Form->hidden('CoursesUser.0.user_type',array('value'=>'admin'));
				echo $this->Form->hidden('Course.copy_id');
			?>
			</fieldset>
			<div class="controls">
				<?php echo $this->Form->button('Add Course', array('type'=>'submit', 'class'=>'submit'));
				echo $this->Form->button('Cancel', array('type'=>'button', 'class'=>'cancel', 'onclick'=>'window.location=\''.$this->Html->url(array('controller'=>'courses', 'action'=>'index')).'\';'));?>
			</div>
		<?php 
		echo $this->Form->end();
		?>
	</div>
</div>
<script>
$(document).ready(function(){
	//setup modal box for register by sso
	$( "#copy_course_dialog" ).dialog({
        autoOpen: false,
        show: {
          effect: "blind",
          duration: 500
        },
        hide: {
          effect: "blind",
          duration: 500
        },
        title: "Copy course members",
        open: function() {
	    	$("#CourseName").val("");
	    	$("#CourseName").focus();
        	},
        modal: true
      });
	//launch #prompt on click
	$(".modalInput").click(function(event){
 	    event.preventDefault();
 	    $('#CourseCopyId').val($(this).attr('data-copy'));
 	    $('#copy_course_dialog').dialog('open');
 	    });
	});
$('#CourseCopyForm').submit(function(event) {
    $.ajax({
		beforeSend:function (XMLHttpRequest) {
			$("#sending").fadeIn();
			}, 
		data:$('#CourseCopyForm').serialize(), 
		dataType:"json", 
		success:function (data, textStatus) {
			if(data['error_text']=="")
				{
				$('#copy_course_dialog').dialog('close');
				window.location='<?php $this->Html->url(array('controller'=>'courses', 'action'=>'index')); ?>';			    	
				}
			else
				{
				$('#copyCourseError').html(data['error_text']);
				$("#CourseName").val("");
		    	$("#CourseName").focus();
				}
			}, 
		type:"post", 
		url:"<?php echo $this->Html->url(array('controller'=>'courses', 'action'=>'copy'));?>"
		});
    event.preventDefault(); // interrupt form submission
	});
</script>