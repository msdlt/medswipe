<!-- overlayed element -->
<div id="dialog-modal" title="">
  	<!-- the external content is loaded inside this tag -->
	<div class="contentWrap"></div>
</div>
<!-- <div id="overlay">
	<div class="contentWrap"></div>
</div> -->
<div class="events view">
	<div class="breadcrumb"><?php echo $this->Html->link('Events',array('controller'=>'events','action'=>'index_by_date')); ?> > <?php echo $event['Course']['name'] . " ". $event['Event']['name'];?></div>
	<div class="clear_both">
		<div class="headings">
			<div class="barcode_form">
				<div id="addByBarcodeerror" class="error-message"></div>
				<?php
			    echo $this->Form->create('EventsUser');//, array('default'=>'false'));
			    echo $this->Form->input('User.barcode',array('id'=>'barcode','label'=>"Box must be green - click if red: ",'class'=>'barcode'));
			    echo $this->Form->hidden('EventsUser.event_id',array('value'=>$event['Event']['id'],'id'=>'event_id'));
			    echo $this->Form->hidden('Course.id',array('value'=>$event['Course']['id'],'id'=>'course_id'));
			    echo $this->Form->end();
			    ?>
			</div>
			<h2>
			<?php 
			$start_date = new DateTime($event['Event']['date']);
			$end_date = new DateTime($event['Event']['end_date']);
			//$end_date->add(new DateInterval('PT'.$event['Event']['duration'].'M'));
			//$end_date->modify('+'.$event['Event']['duration'].' minutes');
			echo $event['Course']['name'];//$this->Html->link($event['Course']['name'], array('controller' => 'courses', 'action' => 'view', $event['Course']['id']));
			?>
			</h2>
			<h4><?php echo $event['Event']['name'];?></h4>
			<h4><?php echo $start_date->format('d/m/Y H:i')." - ".$end_date->format('d/m/Y H:i');?></h4>
			<p><span id="no_of_records"></span> <span id="no_of_participants_who_are_expected"></span> <span id="no_of_participants"></span></p>
			<!-- <div class='actions add_button'><ul><li><?php //echo $this->Html->link(__('Register with Oxford username', true), array('controller' => 'events_users','action' => 'add','event_id'=>$event['Event']['id']),array('rel'=>'#overlay')); ?></li></ul></div> -->
			<div class='actions'>
				<!-- <button class="add_button" id="modalInput" rel="#prompt">Register with Oxford username</button> -->
				<?php 
				//debug($role['CoursesUser']['user_type']);
				if(isset($role['CoursesUser'])&&($role['CoursesUser']['user_type']=="staff"||$role['CoursesUser']['user_type']=="admin")){
					echo"<ul>";
					echo"<li><a href='javascript:void();' id='modalInput'>Forgotten Card?</a></li>";
					echo"</ul>";
					}
				?>
			</div>
		</div>
	</div>
	<div id="wrapper" class="clear_both">
		<div id="grid_wrapper_participants">
			<table id="list_participants"><tr><td/></tr></table>
		</div>
		<div id="grid_wrapper_attendees">
			<table id="list"><tr><td/></tr></table>
		</div>
		<div id="success">
		
		</div>
	</div>
	<div class="breadcrumb"><?php echo $this->Html->link('Events',array('controller'=>'events','action'=>'index_by_date')); ?> > <?php echo $event['Course']['name'] . " ". $event['Event']['name'];?></div>
</div>
<div class="actions">
	<div id="attendee_image">
	</div>
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
		    //create submit button which submits form and receives back either add_success or add_failure
		    //echo $this->Js->submit('Save', array(
		    	//'url'=> array('controller'=>'events_users', 'action'=>'add'),
		    	//'complete'=>'refreshGrids();$("#barcode").val("");$("#barcode").focus();modalBoxTrigger.eq(0).overlay().close();',
				//'update' => '#success',
		    	//'class' => 'submit',
		    	//'div'=>false
		    //));
		    echo $this->Form->button('Register', array('type'=>'submit', 'class'=>'submit'));
		    echo $this->Form->button('Cancel', array('type'=>'button', 'class'=>'cancel', 'onclick'=>'window.location=\''.$this->Html->url(array('controller'=>'events', 'action'=>'view',$event['Event']['id'])).'\';'));
		    ?>
		</div>
	    </fieldset>
	    <?php 
	    echo $this->Form->end();
	    ?>
	</div>
</div>
<script>
var modalBoxTrigger;
$(document).ready(function(){
	//focus on barcode input on load
	$('#barcode').focus();
	//setup modal box for register by sso
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
        close: function() {
        	$("#barcode").val("");
			$("#barcode").focus();
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
        close: function() {
        	$("#barcode").val("");
			$("#barcode").focus();
           	}
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
				displayImage(data['image_name'], data['first_name'], data['last_name']);
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
$('#barcode').bind('keypress', function(e) {
//from http://stackoverflow.com/questions/302122/jquery-event-keypress-which-key-was-pressed
var code = (e.keyCode ? e.keyCode : e.which);
	//alert(code);
	if(code == 13) { //Enter keycode
		$.ajax({
			beforeSend:function (XMLHttpRequest) {
				return checkBarcode();
				$("#sending").fadeIn();
				}, 
			complete:function (XMLHttpRequest, textStatus) {
				//$("#barcode").val("");
				//$("#barcode").focus();
				//displayImage(data['image_name'], data['first_name'], data['last_name'])
				//refreshGrids();		    	
				}, 
			data:$("#barcode").closest("form").serialize(), 
			dataType:"json", 
			success:function (data, textStatus) {
				if(data['error_text']=="")
					{
					//everything's OK
					displayImage(data['image_name'], data['first_name'], data['last_name']);
					refreshGrids();		    	
					}
				else
					{
					$('#addByBarcodeerror').html(data['error_text']);
					}
				$("#barcode").val("");
				$("#barcode").focus();
				//$("#success").html(data);
				}, 
			type:"post", 
			url:"<?php echo $this->Html->url(array('controller' => 'events_users','action' => 'ajax_add'));?>"
			});
		e.preventDefault();
	}
});
function checkBarcode(){
	var regExpAtLeast7Digits = /^\d{7,}.$/;  //. at end allows any character e.g X, %, .
    if(!regExpAtLeast7Digits.test($('#barcode').val())) 
		{
		if($('#barcode_error').length==0)
			{
			$('#barcode').after('<div id="barcode_error" class="error-message">Error: Please scan your barcode again.</div>');
			$('#attendee_image').html('<h1>Error</h1><p>Please try scanning your card again</p>');
			}
		//empty
		$('#barcode').val("");
		$('#barcode').focus();
		return false;
		}
	else
		{
		$('#barcode_error').remove();
		return true;
		}
}
function refreshGrids(){
	$('#list').trigger('reloadGrid');
	$('#list_participants').trigger('reloadGrid');
}
$("#list").jqGrid({
    url:'<?php echo $this->Html->url(array("controller" => "events_users","action" => "ajax_get_events_attendance","event_id" => $event['Event']['id']));?>',
    datatype: 'json',
    mtype: 'GET',
    colNames:['Last name','First name', 'Time','IP address','Type'],
    colModel :[ 
      {name:'last_name', index:'last_name', align:'right'},//, formatter:returnMyLink}, 
      {name:'first_name', index:'first_name', align:'right'}, 
      {name:'attended_at', index:'attended_at', align:'right'},
      {name:'ip_address', index:'ip_address', align:'right'}, 
      {name:'registration_type', index:'registration_type', align:'right'} 
    ],
    sortname: 'attended_at',
    autowidth: true,
	shrinktofit: true,
    height: 'auto',
    sortorder: 'desc',
    loadonce: false,
    viewrecords: true,
    caption: 'In attendance',
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
    	//hide grid if empty
		var recs = parseInt($("#list").getGridParam("records"),10);
		if (isNaN(recs) || recs == 0) {
            $("#grid_wrapper_attendees").hide();
			}
        else {
            $('#grid_wrapper_attendees').show();
            }
    	//note overlayElem holds a reference to last opened a[rel] cf. http://stackoverflow.com/questions/3266780/jquery-tools-how-to-close-an-overlay
    	//$('a[rel]').click(function() { overlayElem = $(this); });
     }     
  });
$("#list_participants").jqGrid({
	url:'<?php echo $this->Html->url(array("controller" => "events_users","action" => "ajax_get_events_participants","event_id" => $event['Event']['id'],"course_id"=>$event['Course']['id']));?>',
    //url:'/medswipe-admin/events_users/ajax_get_events_participants/event_id:92/course_id:34',
    datatype: 'json',
    mtype: 'GET',
    colNames:['Last name','First name'],
    colModel :[ 
      {name:'last_name', index:'last_name', align:'right', formatter:returnMyParticipantLink}, 
      {name:'first_name', index:'first_name', align:'right'}, 
    ],
    sortname: 'last_name',
    autowidth: true,
	shrinktofit: true,
    height: 'auto',
    sortorder: 'asc',
    loadonce: false,
    viewrecords: true,
    caption: 'Not in attendance',
    rowNum: 99999,
    jsonReader : {
        root:"rows",
        repeatitems: false,
        id: "0"
     },
     gridComplete: function() {
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
    	$(".image_popup").click(function(event){
 	    	event.preventDefault();
 	    	$('.contentWrap').load($(this).attr("href"));
 	    	$('#dialog-modal').dialog('open');
 	    });
    	//hide grid if empty
		var recs = parseInt($("#list_participants").getGridParam("records"),10);
		if (isNaN(recs) || recs == 0) {
            $("#grid_wrapper_participants").hide();
			}
        else {
            $('#grid_wrapper_participants').show();
            }
    	//note overlayElem holds a reference to last opened a[rel] cf. http://stackoverflow.com/questions/3266780/jquery-tools-how-to-close-an-overlay
    		//$('a[rel]').click(function() { overlayElem = $(this); });
     }     
  }); 
//function returnMyParticipantLink(cellValue, options, rowdata, action){
    //return "<a href='<?php //echo $this->Html->url(array('controller'=>'users', 'action'=>'view'));?>/" + rowdata['id'] + "' rel='#overlay'>"+options.rowId+"</a>";
	//}
function returnMyParticipantLink(cellValue, options, rowdata, action){
    return "<a class='image_popup' href='<?php echo $this->Html->url(array('controller'=>'users', 'action'=>'view'));?>/" + rowdata['id'] + "'>"+options.rowId+"</a>";
	}
function displayImage(imgName, firstName, lastName){
	$("#attendee_image").html('<img src="<?php echo $this->webroot; ?>img/attendees/'+imgName+'.jpg" height=182><h3 id="thank_you">'+firstName+' '+lastName+'</h3>');
	$("#attendee_image").css('display','block');
	}

</script>

