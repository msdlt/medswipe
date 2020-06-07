<ul>
	<li>
		<a <?php echo (isset($navigation) && $navigation=='events')? "class=\"current tabnav\"" : "class=\"tabnav\""	?> 
			href="<?php echo $this->Html->url(array('controller'=>'events', 'action'=>'index_by_date'));?>">
			Events
		</a>
	</li>
	<?php if(AuthComponent::user('id')):?>
	<li>
		<a <?php echo (isset($navigation) && $navigation=='courses')? "class=\"current tabnav\"" : "class=\"tabnav\""?> 
			href="<?php echo $this->Html->url(array('controller'=>'courses', 'action'=>'index'));?>">
			Courses
		</a>
	</li>
	<?php endif ?>
</ul>


