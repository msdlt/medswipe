<!-- overlayed element -->
<div id="dialog-modal" title="">
  	<!-- the external content is loaded inside this tag -->
	<div class="contentWrap"></div>
</div>
<!-- <div class="apple_overlay" id="overlay">
	<div class="contentWrap"></div>
</div> -->
<div class="eventsUsers index">
<div class="breadcrumb"><?php echo $this->Html->link('Courses',array('controller'=>'courses','action'=>'index')); ?> > <?php echo $this->Html->link($event['Course']['name'],array('controller'=>'events_users','action'=>'view_course_attendees',$event['Course']['id']));?> > Event attendance: <?php echo $event['Event']['name']?></div>
<h2>
<?php 
$start_date = new DateTime($event['Event']['date']);
$end_date = new DateTime($event['Event']['end_date']);
?>
</h2>
<h3>Event attendance: <?php echo $event['Event']['name'];?></h3>
<h4><?php echo $start_date->format('d/m/Y H:i')." - ".$end_date->format('d/m/Y H:i');?></h4>
<p><span id="no_of_records"></span> <span id="no_of_participants_who_are_expected"></span> <span id="no_of_participants"></span><p>
<div id="success">
<div id="no_of_records"></div>
<table id="list"><tr><td/></tr></table>
</div>
<div class="breadcrumb"><?php echo $this->Html->link('Courses',array('controller'=>'courses','action'=>'index')); ?> > <?php echo $this->Html->link($event['Course']['name'],array('controller'=>'events_users','action'=>'view_course_attendees',$event['Course']['id']));?> > Event attendance: <?php echo $event['Event']['name']?></div>
</div>
<!-- This div and from displayed when Register with Oxford username button clicked -->
<div id="prompt">
	<div class="eventsUsers form">
		<?php
	    echo $this->Form->create('EventsUser', array('id'=>'EventsUserViewFormAdd'));//, array('default'=>'false'));
	    ?>
	    <fieldset>
	 		<legend><?php __('Register');?></legend>
			<div id="addBySSOerror" class="error-message"></div>
	    <?php 
	    echo $this->Form->input('User.username', array('label'=>'Single sign-on username'));
		echo $this->Form->hidden('EventsUser.event_id',array('value'=>$event['Event']['id'],'id'=>'event_id'));
	    echo $this->Form->hidden('Course.id',array('value'=>$event['Course']['id'],'id'=>'course_id'));
	    ?>
	    <div class="controls">
		    <?php 
		    echo $this->Form->button('Register', array('type'=>'submit', 'class'=>'submit'));
		    echo $this->Form->button('Cancel', array('type'=>'button', 'class'=>'cancel', 'onclick'=>'window.location=\''.$this->Html->url(array('controller'=>'events_users', 'action'=>'view_attendees',$event['Event']['id'])).'\';'));
		    ?>
		</div>
	    </fieldset>
	    <?php 
	    echo $this->Form->end();
	    ?>
	</div>
</div>
<div class='actions'>
	<!-- <button class="add_button" id="modalInput" rel="#prompt">Register with Oxford username</button> -->
	<ul>
		<li><a href='javascript:void();' id='modalInput'>Forgotten card?</a></li>
	</ul>
</div>
<!-- <div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php //echo $this->Html->link(__('Add attendee', true), array('action' => 'add','event_id'=>$event['Event']['id'])); ?> </li>
	</ul>
</div> -->
<script>
var modalBoxTrigger;
$(document).ready(function(){
	$( "#prompt" ).dialog({
        autoOpen: false,
        show: {
          effect: "blind",
          duration: 500
        },
        hide: {
          effect: "blind",
          duration: 500
        },
        title: "Register with SSO",
        open: function() {
	    	$("#UserUsername").val("");
	    	$("#UserUsername").focus();
        	},
        modal: true
      });
	//launch #prompt on click
	$("#modalInput").click(function(event){
 	    event.preventDefault();
 	    $('#prompt').dialog('open');
 	    });
	//setup box for showing images
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
        });
});
$('#EventsUserViewFormAdd').submit(function(event) {
    $.ajax({
		beforeSend:function (XMLHttpRequest) {
			$("#sending").fadeIn();
			}, 
		complete:function (XMLHttpRequest, textStatus) {
			
			}, 
		data:$('#EventsUserViewFormAdd').serialize(), 
		dataType:"json", 
		success:function (data, textStatus) {
			if(data['error_text']=="")
				{
				//everything's OK
				$('#prompt').dialog('close');
				refreshGrids();		    	
				}
			else
				{
				$('#addBySSOerror').html(data['error_text']);
				$("#UserUsername").val("");
		    	$("#UserUsername").focus();
				}
			}, 
		type:"post", 
		url:"<?php echo $this->Html->url(array('controller'=>'events_users', 'action'=>'add'));?>"
		});
    event.preventDefault(); // interrupt form submission
	});
function refreshGrids(){
	$('#list').trigger('reloadGrid');
}

$("#list").jqGrid({
    url:'<?php echo $this->Html->url(array("controller" => "events_users","action" => "ajax_get_events_attendance","event_id" => $event['Event']['id']));?>',
    datatype: 'json',
    mtype: 'GET',
    colNames:['Last name','First name', 'Time','IP address','Type'],
    colModel :[ 
      {name:'last_name', index:'last_name', align:'right', formatter:returnMyLink}, 
      {name:'first_name', index:'first_name', align:'right'}, 
      {name:'attended_at', index:'attended_at', align:'right'},
      {name:'ip_address', index:'ip_address', align:'right'}, 
      {name:'registration_type', index:'registration_type', align:'right'} 
    ],
    sortname: 'last_name',
    autowidth: true,
    height: 'auto',
    sortorder: 'asc',
    loadonce: false,
    viewrecords: true,
    caption: 'Event attendance',
    rowNum: 99999,
    jsonReader : {
        root:"rows",
        repeatitems: false,
        id: "0"
     },
     gridComplete: function() {
    	 $.get('<?php echo $this->Html->url(array("controller" => "events_users","action" => "ajax_count_distinct_attendees","event_id" => $event['Event']['id']));?>',
         		  function(data)
         		  	{
         		 	$("#no_of_records").html(data);
         	 		}
         		);
    	 $.get('<?php echo $this->Html->url(array("controller" => "events_users","action" => "ajax_count_distinct_participants","event_id" => $event['Event']['id'],"course_id" => $event['Course']['id']));?>',
        		  function(data)
        		  	{
        		 	$("#no_of_participants").html(data);
        	 		}
        		);
  	 	$.get('<?php echo $this->Html->url(array("controller" => "events_users","action" => "ajax_count_distinct_attendees_who_are_expected","event_id" => $event['Event']['id'],"course_id" => $event['Course']['id']));?>',
       		  function(data)
       		  	{
       		 	$("#no_of_participants_who_are_expected").html(data);
       	 		}
       		);
    	 var recs = parseInt($("#list").getGridParam("records"),10);
 			if (isNaN(recs) || recs == 0) {
             $("#grid_wrapper_attendees").hide();
 			}
         else {
             $('#grid_wrapper_attendees').show();
             }
 		$(".image_popup").click(function(event){
 	    	event.preventDefault();
 	    	$('.contentWrap').load($(this).attr("href"));
 	    	$('#dialog-modal').dialog('open');
 	    	});
		//});
    }
      
  }); 
  function returnMyLink(cellValue, options, rowdata, action){
	    return "<a class='image_popup' href='<?php echo $this->Html->url(array('controller'=>'users', 'action'=>'view'));?>/" + rowdata['user_id'] + "'>"+options.rowId+"</a>";
		}
  //function returnMyLink(cellValue, options, rowdata, action) 
//{
//    return "<a href='<?php echo $this->Html->url(array('controller'=>'users', 'action'=>'view'));?>/" + rowdata['user_id'] + "' rel='#overlay'>"+options.rowId+"</a>";
//}
</script>
