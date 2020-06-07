<!-- overlayed element -->
<div id="dialog-modal" title="">
  	<!-- the external content is loaded inside this tag -->
	<div class="contentWrap"></div>
</div>
<!-- <div class="apple_overlay" id="overlay">
	<div class="contentWrap"></div>
</div>  -->
<div class="courses form">
	<div class="breadcrumb"><?php echo $this->Html->link('Courses',array('controller'=>'courses','action'=>'index')); ?> > Edit '<?php echo $this->FormEnum->value('Course.name');?>'</div>
<?php echo $this->element('errors', array('errors' => $errors)); ?>
<?php echo $this->FormEnum->create('Course');
?>
	<fieldset>
 		<legend><?php __('Edit Course'); ?></legend>
	<?php
		echo $this->FormEnum->hidden('Course.id');
		echo $this->FormEnum->input('Course.name', array('label'=>'Name','class'=>'course_name'));
		//print_r($errors);	
	?>
	<fieldset>
 		<legend>Administrators, Staff and Attendees</legend>
 		<div id="courses_users">
		<?php 
		//debug($this->data);
		$courses_user_row_count = 0;
		echo "<table id='users_table'>";
			echo "<thead>";
				echo "<tr>";
					echo "<th>Name</th>";
					echo "<th style='text-align:right;'>Role</th>";
					echo "<th>Remove</th>";
				echo "</tr>";
			echo "</thead>";
			echo "<tbody>";
			foreach($this->data['CoursesUser'] as $key => $value)
				{
				//echo "<div id='courses_user_row_".$key."' class='courses_user_row'>";
				echo "<tr id='courses_user_row_".$key."' class='courses_user_row'>";
					echo "<td>";
						if(isset($value['User'])){
							//don't submit id unless already added
							echo $this->FormEnum->hidden('CoursesUser.'.$key.'.id');
							}
						echo $this->FormEnum->hidden('CoursesUser.'.$key.'.course_id');
						if(isset($value['User'])){
							echo $value['User']['first_name']." ".$value['User']['last_name'];
							}
						else{
							echo '<input id="CoursesUser'.$key.'Username" class="courses_user_row user_name" type="text" maxlength="255" name="data[CoursesUser]['.$key.'][username]" value="'.$this->data['CoursesUser'][$key]['username'].'">';
							}
					echo "</td>";
					echo "<td>";
						//echo $this->FormEnum->input('CoursesUser.'.$key.'.user_type',array('class'=>'courses_user','label'=>array('text'=>$value['User']['first_name']." ".$value['User']['last_name']." ",'class'=>'courses_user')));
						if(!isset($value['User'])||$value['User']['username']!=AuthComponent::user('username'))
							{
							echo $this->FormEnum->input('CoursesUser.'.$key.'.user_type',array('class'=>'courses_user','label'=>false,'div'=>false));
							}
						else
							{
							echo "admin";
							}
					echo "</td>";
					echo "<td>";
						if(!isset($value['User'])||$value['User']['username']!=AuthComponent::user('username'))
							{
							//ie. can't delete yourself
							echo "<a id='remove_courses_user_".$key."' href='javascript:void(0)' onclick='removeCoursesUser(".$key.")'><img src='".$this->webroot."img/interface/remove.png' alt='Remove' title='Remove'></a>";			
							}
						else
							{
							echo "Cannot remove yourself";
							}
						//these are included so they will persist ion this->data in case of errors
						if(isset($this->data['CoursesUser'][$key]['User'])){
							echo $this->FormEnum->hidden('CoursesUser.'.$key.'.User.first_name',array('value'=>$this->data['CoursesUser'][$key]['User']['first_name']));
							echo $this->FormEnum->hidden('CoursesUser.'.$key.'.User.last_name',array('value'=>$this->data['CoursesUser'][$key]['User']['last_name']));
							echo $this->FormEnum->hidden('CoursesUser.'.$key.'.User.username',array('value'=>$this->data['CoursesUser'][$key]['User']['username']));
							echo $this->FormEnum->hidden('CoursesUser.'.$key.'.user_id',array('value'=>$this->data['CoursesUser'][$key]['user_id']));
							}
					echo "</td>";
				//echo "</div>";
				echo "</tr>";
				$courses_user_row_count++;
				}
				echo "</tbody>";
			echo "</table>";
		echo $this->Html->scriptBlock("var courses_user_row_count = ".$courses_user_row_count.";");
		?>
 		</div>
 		<div class='add_courses_user'>
			<a id="add_courses_user" href="javascript:void(0)" onclick="addCoursesUser()"><img src="<?php echo $this->webroot?>img/interface/add.png" alt="Add" title="Add"></a>
			<?php $course_id = $this->FormEnum->value('Course.id'); ?>
			<?php echo $this->Html->link($this->Html->image("interface/add_many.png", array("alt" => "Add Multiple Users", "title"=>"Add Multiple Users")), array("controller"=>"courses", "action"=>"add_many_users", "course_id"=>$course_id),array("id"=>"modalInput", "escape" => false));?>
		</div>
 	</fieldset>
	<fieldset>
 		<legend>Events</legend>
		<div id="events">
		<?php 
		echo "<table id='events_table'>";
			echo "<thead>";
				echo "<tr>";
					echo "<th>Event name</th>";
					echo "<th>Start date/time</th>";
					echo "<th>End date/time</th>";
					echo "<th>Remove</th>";
				echo "</tr>";
			echo "</thead>";
		echo "<tbody>";
		if(isset($this->data['Event'])&&count($this->data['Event'])>0)
			{
			foreach($this->data['Event'] as $key => $value)
				{
				//echo "<div id='grid_row_".$key."' class='grid_row'>";
				echo "<tr id='grid_row_".$key."' class='grid_row'>";
						echo "<td class='event_name'>";
							echo $this->FormEnum->hidden('Event.'.$key.'.id');
							//echo $this->FormEnum->input('Event.'.$key.'.name', array('label'=>'Event name','div'=>false,'class'=>'grid_row event_name'));
							echo $this->FormEnum->input('Event.'.$key.'.name', array('label'=>false,'div'=>false,'class'=>'grid_row event_name'));
						echo "</td>";
					//echo "<div class='dates_container'>";
						//echo "<div class='date_start_container'>";
						echo "<td class='date_start_container'>";
							//echo $this->FormEnum->input('Event.'.$key.'.date', array('label'=>'Start date/time: ','div'=>false,'class'=>'grid_row event_date','separator'=>'','dateFormat'=>'DMY','timeFormat'=>'24'));
							//echo $this->FormEnum->input('Event.'.$key.'.date', array('label'=>false,'div'=>false,'class'=>'grid_row event_date','separator'=>'','dateFormat'=>'DMY','timeFormat'=>'24'));
							echo $this->FormEnum->input('Event.'.$key.'.date', array('label'=>false,'div'=>false,'class'=>'grid_row event_date datepicker','type'=>'text'));
						//echo "</div>";
						echo "</td>";
						//echo "<div class='date_end_container'>";
						echo "<td class='date_end_container'>";
							//echo $this->FormEnum->input('Event.'.$key.'.end_date', array('label'=>'End date/time: ','div'=>false,'class'=>'grid_row event_date','separator'=>'','dateFormat'=>'DMY','timeFormat'=>'24'));
							//echo $this->FormEnum->input('Event.'.$key.'.end_date', array('label'=>false,'div'=>false,'class'=>'grid_row event_date','separator'=>'','dateFormat'=>'DMY','timeFormat'=>'24'));
							echo $this->FormEnum->input('Event.'.$key.'.end_date', array('label'=>false,'div'=>false,'class'=>'grid_row event_date datepicker','type'=>'text'));
						echo "</td>";
					//echo "</div>";
					echo $this->FormEnum->hidden('Event.'.$key.'.course_id');
					//only show remove button next to those events without users
					echo "<td>";
					if(isset($this->data['Event'][$key]['id']))
						{
						if(!isset($existing_events_with_users)||(isset($existing_events_with_users)&&!in_array($this->data['Event'][$key]['id'],$existing_events_with_users)))
							{
							echo "<div class='remove_event'><a id='remove_event_".$key."' href='javascript:void(0)' onclick='removeEvent(".$key.")'><img src='".$this->webroot."img/interface/remove.png' alt='Remove event' title='Remove event'></a></div>";			
							}
						}
					echo "</td>";
					//echo $this->Html->scriptBlock("datePickerController.createDatePicker({formElements:{'Event".$key."DateYear':'Y', 'Event".$key."DateDay':'d', 'Event".$key."DateMonth':'m'}});");
					//echo $this->Html->scriptBlock("datePickerController.createDatePicker({formElements:{'Event".$key."EndDateYear':'Y', 'Event".$key."EndDateDay':'d', 'Event".$key."EndDateMonth':'m'}});");
				//echo "</div>";
				echo "</tr>";
				}
			echo $this->Html->scriptBlock("var grid_row_count = ".count($this->data['Event']).";");
			}
		else 
			{
			echo $this->Html->scriptBlock("var grid_row_count = 0;");
			}
			echo "</tbody>";
		echo "</table>";
		?>
		</div>
		<div class='add_event'>
			<a id="add_event" href="javascript:void(0)" onclick="addEvent()"><img src="<?php echo $this->webroot?>img/interface/add.png" alt="Add event" title="Add event"></a>
		</div>
		</fieldset>
	</fieldset>
<div class="controls">
<?php echo $this->FormEnum->button('Save Course', array('type'=>'submit', 'class'=>'submit'));
echo $this->FormEnum->button('Cancel', array('type'=>'button', 'class'=>'cancel', 'onclick'=>'window.location=\''.$this->Html->url(array('controller'=>'courses', 'action'=>'index')).'\';'));?>
</div>
<?php 
echo $this->FormEnum->end();
?>
<div class="breadcrumb"><?php echo $this->Html->link('Courses',array('controller'=>'courses','action'=>'index')); ?> > Edit '<?php echo $this->FormEnum->value('Course.name');?>'</div>
</div>
<div class="actions">
	<h3>Instructions</h3>
	<p>
		Use the <?php echo $this->Html->image('interface/add.png', array('alt'=>'Add event', 'title'=>'Add event'));?> and <?php echo $this->Html->image('interface/remove.png', array('alt'=>'Add event', 'title'=>'Add event'));?> buttons
		to add and remove (respectively) users (by Oxford username) and events for this course. Events can only be removed if they do not already have attendees. 
	</p>
	<p>
		<strong>Administrators</strong> can edit events and courses in addition to the rights of <strong>Staff</strong>, who can view events and courses and 
		view attendance. <strong>Attendees</strong> will be listed as 'expected' for all events for this course.
	</p>
	<p>
		The <?php echo $this->Html->image('interface/add_many.png', array('alt'=>'Add multiple users', 'title'=>'Add multiple users'));?> allows you to 
		upload multiple <strong>attendees</strong> by pasting their Oxford usernames into a text box. 
	</p>
	<!-- <h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php //echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('Course.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('Course.id'))); ?></li> 
		<li><?php //echo $this->Html->link(__('List Courses', true), array('action' => 'index'));?></li>
	</ul> -->
</div>
<!-- <div id="grid_row" class="grid_row"> -->
<table class="hidden_table">
	<tr id="grid_row" class="grid_row">
		<td class="event_name">
			<input id="EventxName" class="grid_row event_name" type="text" maxlength="255" name="data[Event][x][name]" placeholder="Event name">
		</td>
		<td class="date_start_container">
			<input id="EventxDate" class="grid_row event_date" type="text" name="data[Event][x][date]">
			<!-- <select id="EventxDateDay" class="grid_row event_date" name="data[Event][x][date][day]">
					<option value="01">1</option>
					<option value="02">2</option>
					<option value="03">3</option>
					<option value="04">4</option>
					<option value="05">5</option>
					<option value="06">6</option>
					<option value="07">7</option>
					<option value="08">8</option>
					<option value="09">9</option>
					<option value="10">10</option>
					<option value="11">11</option>
					<option value="12">12</option>
					<option value="13">13</option>
					<option value="14">14</option>
					<option value="15">15</option>
					<option value="16">16</option>
					<option value="17">17</option>
					<option value="18">18</option>
					<option value="19">19</option>
					<option value="20">20</option>
					<option value="21">21</option>
					<option value="22">22</option>
					<option value="23">23</option>
					<option value="24">24</option>
					<option value="25">25</option>
					<option value="26">26</option>
					<option value="27">27</option>
					<option value="28">28</option>
					<option value="29">29</option>
					<option value="30">30</option>
					<option value="31">31</option>
				</select><select id="EventxDateMonth" class="grid_row event_date" name="data[Event][x][date][month]">
					<option value="01">January</option>
					<option value="02">February</option>
					<option value="03">March</option>
					<option value="04">April</option>
					<option value="05">May</option>
					<option value="06">June</option>
					<option value="07">July</option>
					<option value="08">August</option>
					<option value="09">September</option>
					<option value="10">October</option>
					<option value="11">November</option>
					<option value="12">December</option>
				</select><select id="EventxDateYear" class="grid_row event_date" name="data[Event][x][date][year]">
					<option value="2031">2031</option>
					<option value="2030">2030</option>
					<option value="2029">2029</option>
					<option value="2028">2028</option>
					<option value="2027">2027</option>
					<option value="2026">2026</option>
					<option value="2025">2025</option>
					<option value="2024">2024</option>
					<option value="2023">2023</option>
					<option value="2022">2022</option>
					<option value="2021">2021</option>
					<option value="2020">2020</option>
					<option value="2019">2019</option>
					<option value="2018">2018</option>
					<option value="2017">2017</option>
					<option value="2016">2016</option>
					<option value="2015">2015</option>
					<option value="2014">2014</option>
					<option value="2013">2013</option>
					<option value="2012">2012</option>
					<option value="2011">2011</option>
					<option value="2010">2010</option>
					<option value="2009">2009</option>
					<option value="2008">2008</option>
					<option value="2007">2007</option>
					<option value="2006">2006</option>
					<option value="2005">2005</option>
					<option value="2004">2004</option>
					<option value="2003">2003</option>
					<option value="2002">2002</option>
					<option value="2001">2001</option>
					<option value="2000">2000</option>
					<option value="1999">1999</option>
					<option value="1998">1998</option>
					<option value="1997">1997</option>
					<option value="1996">1996</option>
					<option value="1995">1995</option>
					<option value="1994">1994</option>
					<option value="1993">1993</option>
					<option value="1992">1992</option>
					<option value="1991">1991</option>
				</select><select id="EventxDateHour" class="grid_row event_date" name="data[Event][x][date][hour]">
					<option value="00">0</option>
					<option value="01">1</option>
					<option value="02">2</option>
					<option value="03">3</option>
					<option value="04">4</option>
					<option value="05">5</option>
					<option value="06">6</option>
					<option value="07">7</option>
					<option value="08">8</option>
					<option value="09">9</option>
					<option value="10">10</option>
					<option value="11">11</option>
					<option value="12">12</option>
					<option value="13">13</option>
					<option value="14">14</option>
					<option value="15">15</option>
					<option value="16">16</option>
					<option value="17">17</option>
					<option value="18">18</option>
					<option value="19">19</option>
					<option value="20">20</option>
					<option value="21">21</option>
					<option value="22">22</option>
					<option value="23">23</option>
				</select>:<select id="EventxDateMin" class="grid_row event_date" name="data[Event][x][date][min]">
					<option value="00">00</option>
					<option value="05">05</option>
					<option value="10">10</option>
					<option value="15">15</option>
					<option value="20">20</option>
					<option value="25">25</option>
					<option value="30">30</option>
					<option value="35">35</option>
					<option value="40">40</option>
					<option value="45">45</option>
					<option value="50">50</option>
					<option value="55">55</option>
				</select> -->
			</td>
			<td class="date_end_container">
				<input id="EventxEndDate" class="grid_row event_date" type="text" name="data[Event][x][end_date]">
				<!-- <select id="EventxEndDateDay" class="grid_row event_date" name="data[Event][x][end_date][day]">
					<option value="01">1</option>
					<option value="02">2</option>
					<option value="03">3</option>
					<option value="04">4</option>
					<option value="05">5</option>
					<option value="06">6</option>
					<option value="07">7</option>
					<option value="08">8</option>
					<option value="09">9</option>
					<option value="10">10</option>
					<option value="11">11</option>
					<option value="12">12</option>
					<option value="13">13</option>
					<option value="14">14</option>
					<option value="15">15</option>
					<option value="16">16</option>
					<option value="17">17</option>
					<option value="18">18</option>
					<option value="19">19</option>
					<option value="20">20</option>
					<option value="21">21</option>
					<option value="22">22</option>
					<option value="23">23</option>
					<option value="24">24</option>
					<option value="25">25</option>
					<option value="26">26</option>
					<option value="27">27</option>
					<option value="28">28</option>
					<option value="29">29</option>
					<option value="30">30</option>
					<option value="31">31</option>
				</select><select id="EventxEndDateMonth" class="grid_row event_date" name="data[Event][x][end_date][month]">
					<option value="01">January</option>
					<option value="02">February</option>
					<option value="03">March</option>
					<option value="04">April</option>
					<option value="05">May</option>
					<option value="06">June</option>
					<option value="07">July</option>
					<option value="08">August</option>
					<option value="09">September</option>
					<option value="10">October</option>
					<option value="11">November</option>
					<option value="12">December</option>
				</select><select id="EventxEndDateYear" class="grid_row event_date" name="data[Event][x][end_date][year]">
					<option value="2031">2031</option>
					<option value="2030">2030</option>
					<option value="2029">2029</option>
					<option value="2028">2028</option>
					<option value="2027">2027</option>
					<option value="2026">2026</option>
					<option value="2025">2025</option>
					<option value="2024">2024</option>
					<option value="2023">2023</option>
					<option value="2022">2022</option>
					<option value="2021">2021</option>
					<option value="2020">2020</option>
					<option value="2019">2019</option>
					<option value="2018">2018</option>
					<option value="2017">2017</option>
					<option value="2016">2016</option>
					<option value="2015">2015</option>
					<option value="2014">2014</option>
					<option value="2013">2013</option>
					<option value="2012">2012</option>
					<option value="2011">2011</option>
					<option value="2010">2010</option>
					<option value="2009">2009</option>
					<option value="2008">2008</option>
					<option value="2007">2007</option>
					<option value="2006">2006</option>
					<option value="2005">2005</option>
					<option value="2004">2004</option>
					<option value="2003">2003</option>
					<option value="2002">2002</option>
					<option value="2001">2001</option>
					<option value="2000">2000</option>
					<option value="1999">1999</option>
					<option value="1998">1998</option>
					<option value="1997">1997</option>
					<option value="1996">1996</option>
					<option value="1995">1995</option>
					<option value="1994">1994</option>
					<option value="1993">1993</option>
					<option value="1992">1992</option>
					<option value="1991">1991</option>
				</select><select id="EventxEndDateHour" class="grid_row event_date" name="data[Event][x][end_date][hour]">
					<option value="00">0</option>
					<option value="01">1</option>
					<option value="02">2</option>
					<option value="03">3</option>
					<option value="04">4</option>
					<option value="05">5</option>
					<option value="06">6</option>
					<option value="07">7</option>
					<option value="08">8</option>
					<option value="09">9</option>
					<option value="10">10</option>
					<option value="11">11</option>
					<option value="12">12</option>
					<option value="13">13</option>
					<option value="14">14</option>
					<option value="15">15</option>
					<option value="16">16</option>
					<option value="17">17</option>
					<option value="18">18</option>
					<option value="19">19</option>
					<option value="20">20</option>
					<option value="21">21</option>
					<option value="22">22</option>
					<option value="23">23</option>
				</select>:<select id="EventxEndDateMin" class="grid_row event_date" name="data[Event][x][end_date][min]">
					<option value="00">00</option>
					<option value="05">05</option>
					<option value="10">10</option>
					<option value="15">15</option>
					<option value="20">20</option>
					<option value="25">25</option>
					<option value="30">30</option>
					<option value="35">35</option>
					<option value="40">40</option>
					<option value="45">45</option>
					<option value="50">50</option>
					<option value="55">55</option>
				</select> -->
			</td>
		<input id="EventxCourseId" type="hidden" value="<?php echo $this->data['Course']['id'];?>" name="data[Event][x][course_id]">
		<td>
		<div class="remove_event"><a id="remove_event_x" href="javascript:void(0)" onclick="removeEvent(grid_row)"><img src="<?php echo $this->webroot?>img/interface/remove.png" alt="Remove event" title="Remove event"></a></div>			
		</td>
		<!--</div> -->
	</tr>
</table>

<table class="hidden_table">
<!-- <div id="courses_user_row" class="courses_user_row"> -->
	<tr id='courses_user_row' class='courses_user_row'>
	<!-- <div id="courses_user_controls" class="courses_user_controls"> -->
		<td>
			<input id="CoursesUserxCourseId" type="hidden" value="<?php echo $this->data['Course']['id'];?>" name="data[CoursesUser][x][course_id]">
			<input id="CoursesUserxUsername" class="courses_user_row user_name" type="text" maxlength="255" name="data[CoursesUser][x][username]" placeholder="Oxford username (e.g. abcd1234)">
		</td>
		<td>
			<select id="CoursesUserxUserType" class="courses_user" name="data[CoursesUser][x][user_type]">
				<option value="admin">admin</option>
				<option value="staff">staff</option>
				<option value="attendee">attendee</option>
			</select>
		</td>
	<!-- </div> -->
		<td>
			<a id="remove_courses_user_x" onclick="removeCoursesUser(x)" href="javascript:void(0)">
			<img title="Remove" alt="Remove" src="<?php echo $this->webroot?>img/interface/remove.png">
			</a>
		</td>
	</tr>
<!-- </div> -->
</table>

<script>
function adjustDatepickerPosition(input) {
	var top = $(input).offset().top;	//Get the top of the input element
	var scrollTop = $(document).scrollTop();	//Get the current scroll position
	var viewHeight = $(window).height();	//Get the height of the viewport
	var diff = scrollTop + viewHeight - top;	//Get the difference between the top of the 
	var spaceNeeded = 330;	//Amount of space needed for datepicker to fit - bit of trial and error to get this right (and may not be perfect)
	if(diff <= spaceNeeded) {	//If there is not enough space for the datepicker, it will be put above the input box, and cover up the box, so move it up a bit
		$('#ui-datepicker-div').css('margin-top', '-35px');	
	}
	else {	//Otherwise, datepicker will go beneath the box, and can be left where it is
		$('#ui-datepicker-div').css('margin-top', '0');
	}
	return false;		
}
function addEvent(){
   	cloned_grid_row = $("#grid_row").clone();
   	//now change names and visibility
   	cloned_grid_row.attr('id','grid_row_'+ grid_row_count);
	//cloned_grid_row.find("#EventxNameLabel").attr({
   		//'id':'Event'+ grid_row_count + 'Name_label',
   		//'for':'Event'+ grid_row_count + 'Name'
   	 	//});
   	cloned_grid_row.find("#EventxName").attr({
   	   	'id':'Event'+ grid_row_count + 'Name',
   	 	'name':'data[Event][' + grid_row_count + '][name]'
   		});
   	cloned_grid_row.find("#EventxCourseId").attr({
   		'id':'Event'+ grid_row_count + 'CourseId',
   		'name':'data[Event]['+ grid_row_count + '][course_id]'
   	 	});
   	//start date
   	//cloned_grid_row.find("#EventxDateMonth_label").attr({
   	   	//'id':'Event_'+ grid_row_count+ 'DateMonth_label',
   	 	//'for':'Event'+ grid_row_count + 'DateMonth'
   		//});
   	cloned_grid_row.find("#EventxDate").attr({
   		'id':'Event'+ grid_row_count + 'Date',
   	 	'name':'data[Event][' + grid_row_count + '][date]'
   		});
	//cloned_grid_row.find("#EventxDateDay").attr({
   		//'id':'Event'+ grid_row_count + 'DateDay',
   	 	//'name':'data[Event][' + grid_row_count + '][date][day]'
   		//});
   	//cloned_grid_row.find("#EventxDateMonth").attr({
   	   	//'id':'Event'+ grid_row_count + 'DateMonth',
   	 	//'name':'data[Event][' + grid_row_count + '][date][month]'
   		//});
   	//cloned_grid_row.find("#EventxDateYear").attr({	
   	   	//'id':'Event'+ grid_row_count + 'DateYear',
   	 	//'name':'data[Event][' + grid_row_count + '][date][year]'
   		//});
   	//cloned_grid_row.find("#EventxDateHour").attr({		
   	   	//'id':'Event'+ grid_row_count + 'DateHour',
   	   	//'name':'data[Event][' + grid_row_count + '][date][hour]'
   	   	//});
   	//cloned_grid_row.find("#EventxDateMin").attr({   	
   	   	//'id':'Event'+ grid_row_count + 'DateMin',
   	   	//'name':'data[Event][' + grid_row_count + '][date][min]'
   	   	//});
	//end date
   	//cloned_grid_row.find("#EventxEndDateMonth_label").attr({
   	   	//'id':'Event_'+ grid_row_count+ 'EndDateMonth_label',
   	 	//'for':'Event'+ grid_row_count + 'EndDateMonth'
   		//});
   	cloned_grid_row.find("#EventxEndDate").attr({
   		'id':'Event'+ grid_row_count + 'EndDate',
   	 	'name':'data[Event][' + grid_row_count + '][end_date]'
   		});
   	//cloned_grid_row.find("#EventxEndDateDay").attr({
   		//'id':'Event'+ grid_row_count + 'EndDateDay',
   	 	//'name':'data[Event][' + grid_row_count + '][end_date][day]'
   		//});
   //	cloned_grid_row.find("#EventxEndDateMonth").attr({
   	   	//'id':'Event'+ grid_row_count + 'EndDateMonth',
   	 	//'name':'data[Event][' + grid_row_count + '][end_date][month]'
   		//});
   	//cloned_grid_row.find("#EventxEndDateYear").attr({	
   	   	//'id':'Event'+ grid_row_count + 'EndDateYear',
   	 	//'name':'data[Event][' + grid_row_count + '][end_date][year]'
   		//});
   	///cloned_grid_row.find("#EventxEndDateHour").attr({		
   	   	//'id':'Event'+ grid_row_count + 'EndDateHour',
   	   //'name':'data[Event][' + grid_row_count + '][end_date][hour]'
   	   	//});
   	//cloned_grid_row.find("#EventxEndDateMin").attr({   	
   	   	//'id':'Event'+ grid_row_count + 'EndDateMin',
   	   	//'name':'data[Event][' + grid_row_count + '][end_date][min]'
   	   	//});
   	//cloned_grid_row.find("#remove_event").attr({   	
   	   	//'id':'remove_event_'+ grid_row_count,
   	   	//'onclick':'removeEvent(' +  grid_row_count + ')'
   	   	//});
	cloned_grid_row.appendTo($("#events_table > tbody"));
	var today = new Date();
	//set date to current date
	$('#Event'+ grid_row_count + 'Date').val(('0' + today.getDate()).slice(-2) + '/' + ('0'+(today.getMonth()+1)).slice(-2) + '/' + today.getFullYear().toString().substr(2,2)+ " " + ('0' + today.getHours()).slice(-2) + ":" + ('0' + today.getMinutes()).slice(-2));
	//$('#Event'+ grid_row_count + 'DateDay').val(('0' + today.getDate()).slice(-2));
	//$('#Event'+ grid_row_count + 'DateMonth').val(('0' + (today.getMonth()+1)).slice(-2)); //slice (-2) always gives last two characters of strinf e.g 09 of 09 or 10 of 010
	//$('#Event'+ grid_row_count + 'DateYear').val(today.getFullYear());
	$('#Event'+ grid_row_count + 'EndDate').val(('0' + today.getDate()).slice(-2) + '/' + ('0'+(today.getMonth()+1)).slice(-2) + '/' + today.getFullYear().toString().substr(2,2)+ " " + ('0' + (today.getHours()+1)).slice(-2) + ":" + ('0' + today.getMinutes()).slice(-2));
	//$('#Event'+ grid_row_count + 'EndDateDay').val(('0' + today.getDate()).slice(-2));
	//$('#Event'+ grid_row_count + 'EndDateMonth').val(('0' + (today.getMonth()+1)).slice(-2)); //slice (-2) always gives last two characters of strinf e.g 09 of 09 or 10 of 010
	//$('#Event'+ grid_row_count + 'EndDateYear').val(today.getFullYear());
	//create script to attach datepicker
	//var str="<script>";
		//str+="datePickerController.createDatePicker({formElements:{'Event" + grid_row_count + "DateYear':'Y', 'Event" + grid_row_count + "DateDay':'d', 'Event" + grid_row_count + "DateMonth':'m'}});";
		//str+="datePickerController.createDatePicker({formElements:{'Event" + grid_row_count + "EndDateYear':'Y', 'Event" + grid_row_count + "EndDateDay':'d', 'Event" + grid_row_count + "EndDateMonth':'m'}});";
        //str+="<";
        //str+="/script>";
	//$(str).appendTo("#grid_row_"+ grid_row_count);
	$('#Event'+ grid_row_count + 'Date').datetimepicker({
		addSliderAccess: true,
		sliderAccessArgs: { touchonly: false },
		timeFormat: 'HH:mm',
		dateFormat: "dd/mm/y",
		beforeShow: function() {
			adjustDatepickerPosition(this);
		}
	});
	$('#Event'+ grid_row_count + 'EndDate').datetimepicker({
		addSliderAccess: true,
		sliderAccessArgs: { touchonly: false },
		timeFormat: 'HH:mm',
		dateFormat: "dd/mm/y",
		beforeShow: function() {
			adjustDatepickerPosition(this);
		}
	});
	grid_row_count++;
}
function addCoursesUser(){
   	cloned_courses_user_row = $("#courses_user_row").clone();
   	//now change names and visibility
   	cloned_courses_user_row.attr('id','courses_user_row_'+ courses_user_row_count);
   	cloned_courses_user_row.find("#courses_user_controls").attr({
   		'id':'courses_user_controls_'+ courses_user_row_count,
   		});
   	cloned_courses_user_row.find("#CoursesUserxCourseId").attr({
   		'id':'CoursesUser'+ courses_user_row_count + 'CourseId',
   		'name':'data[CoursesUser]['+ courses_user_row_count + '][course_id]'
   	 	});
   	cloned_courses_user_row.find("#CoursesUserxUsername").attr({
   		'id':'CoursesUser'+ courses_user_row_count + 'Username',
   		'name':'data[CoursesUser]['+ courses_user_row_count + '][username]'
   	 	});
   	cloned_courses_user_row.find("#CoursesUserxUserType").attr({
   	   	'id':'CoursesUser'+ courses_user_row_count + 'UserType',
   	 	'name':'data[CoursesUser][' + courses_user_row_count + '][user_type]'
   		});
   	cloned_courses_user_row.find("#remove_courses_user_x").attr({
   	   	'id':'remove_courses_user_'+ courses_user_row_count,
   	 	'onclick':'removeCoursesUser(' +  courses_user_row_count + ')'
   		});
   	cloned_courses_user_row.appendTo($("#users_table > tbody"));
	//create script to attach datepicker
	courses_user_row_count++;
}
function removeEvent(row){
   	$("#grid_row_"+row).remove();
}
function removeCoursesUser(row){
   	$("#courses_user_row_"+row).remove();
}
$(document).ready(function() {
	$( "#dialog-modal" ).dialog({
        autoOpen: false,
        show: {
          effect: "blind",
          duration: 500
        },
        hide: {
          effect: "blind",
          duration: 500
        },
        title: "Add multiple attendees",
        modal: true
      });
	$("#modalInput").click(function(event){
    	event.preventDefault();
    	$('.contentWrap').load($(this).attr("href"));
    	$('#dialog-modal').dialog('open');
    });
	$(".datepicker").datetimepicker({
		addSliderAccess: true,
		sliderAccessArgs: { touchonly: false },
		timeFormat: 'HH:mm',
		dateFormat: "dd/mm/y",
		beforeShow: function() {
			adjustDatepickerPosition(this);
		}
	});
	
});

</script>