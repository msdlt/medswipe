<div class="courses form">
<?php echo $this->Form->create('Course');?>
	<fieldset>
 		<legend><?php __('Add Course'); ?></legend>
	<?php
		echo $this->Form->input('Course.name', array('label'=>'Name','class'=>'course_name'));
		echo $this->Form->hidden('CoursesUser.0.user_id',array('value'=>$this->Session->read('Auth.User.id')));
		echo $this->Form->hidden('CoursesUser.0.user_type',array('value'=>'admin'));
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
<div class="actions">
	<h3>Instructions</h3>
	<p>
		Please give your new course a name. You will automatically be an administrator for this course. You can manage who else can see and edit your course, and add events, after you have created it.
	</p>
</div>




