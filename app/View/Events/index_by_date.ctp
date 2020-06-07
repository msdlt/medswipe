<div class="events index">
	<h2><?php __("Today's events");?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('name');?></th>
			<th><?php echo $this->Paginator->sort('date');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($events as $event):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $this->Html->link($event['Event']['name'], array('controller' => 'events', 'action' => 'view', $event['Event']['id'])); ?></td>
		<td><?php 
			$date = new DateTime($event['Event']['date']);
			echo $date->format('d/m/Y H:i'); 
			?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter('Page {:page} of {:pages}, showing {:current} records out of {:count} total');
	?>	</p>

	<div class="paging">
		<?php echo $this->Paginator->prev('<< ' . __('previous', true), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 |
		<?php echo $this->Paginator->next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));?>
	</div>
</div>
<!-- <a href="https://msdlt.physiol.ox.ac.uk/oak-test/oak-test.php" target="_blank">Temp link</a> -->