<div class="eventsUsers index">
<div class="breadcrumb"><?php echo $this->Html->link('Courses',array('controller'=>'courses','action'=>'index')); ?> > Course attendance: <?php echo $course['Course']['name'];?></div>
<h2>Course attendance: <?php echo $course['Course']['name'];?></h2>
<select id="list_type" name="list_type">
	<option value="all" selected>Show all attendees</option>
	<option value="expected">Show expected attendees only</option>
</select>
<div id="success">
<div id="no_of_records"></div>
<table id="list"><tr><td/></tr></table>
<?php if($course_has_members==true): ?>

<?php endif ?>
</div>
<div class="breadcrumb"><?php echo $this->Html->link('Courses',array('controller'=>'courses','action'=>'index')); ?> > Course attendance: <?php echo $course['Course']['name'];?></div>
</div>
<div class="actions ">
	<ul>
		<li><a id="download_link" href="<?php echo $this->Html->url(array("controller" => "events_users","action" => "ajax_get_course_attendance","course_id" => $course['Course']['id'],"download"=>"true","type"=>"all"));?>"><span style="display: inline-block" class="ui-icon ui-icon-arrowthickstop-1-s"></span>Download as Excel</a></li>
	</ul>
</div>
<!-- overlayed element -->
<div id="dialog-modal" title="">
  	<!-- the external content is loaded inside this tag -->
	<div class="contentWrap"></div>
</div>
<!-- <div class="apple_overlay" id="overlay">
	<div class="contentWrap"></div>
</div> -->
<script>
$("#list").jqGrid({
	url:'<?php echo $this->Html->url(array("controller" => "events_users","action" => "ajax_get_course_attendance","course_id" => $course['Course']['id']));?>'+'/type:'+$('#list_type option:selected').val(),
    //url:'<?php //echo $this->Html->url(array("controller" => "events_users","action" => "ajax_get_course_attendance","course_id" => $course['Course']['id'], "type"=>$('#list_type'));?>',
    datatype: 'json',
    mtype: 'GET',
    colNames:['Last name','First name','No of events'<?php 
    	foreach ($course['Event'] as $event){
    		echo ",'".$this->Html->link($event['name'], array('controller'=>'events_users','action'=>'view_attendees',$event['id']),array('class'=>'jqgrid_header_link'))."'";
    		}
    		?>],
    colModel :[ 
      {name:'last_name', index:'last_name', align:'right', formatter:returnMyLink, frozen : true}, 
      {name:'first_name', index:'first_name', align:'right', frozen : true},
      {name:'no_of_events', index:'no_of_events', align:'right', sorttype:'int'} 
      <?php foreach ($course['Event'] as $event){
  		echo ",{name:'event_".$event['id']."', index:'event_".$event['id']."', align:'right', sortable:false, width:'100'}";
  		}
  	?>],
    
    sortname: 'last_name',
    autowidth: true,
	shrinkToFit: false,
	//width: 700,
	height: 100,
    sortorder: 'asc',
    //loadonce: true,
    viewrecords: true,
    caption: 'All attendees',
    rowNum: 99999,
    jsonReader : {
        root:"rows",
        repeatitems: false,
        id: "0"
     	},
     gridComplete: function() {
    	$(".image_popup").click(function(event){
    	    	event.preventDefault();
    	    	$('.contentWrap').load($(this).attr("href"));
    	    	$('#dialog-modal').dialog('open');
    	    });
    	$('#list').closest(".ui-jqgrid-bdiv").css({"overflow-y" : "scroll"});
		$('#list').setGridHeight($('#container').height()-$('#top').height()-$('#footer').height());
     	}
  });
$("#list").jqGrid('setFrozenColumns');

function returnMyLink(cellValue, options, rowdata, action) 
{
    return "<a class='image_popup' href='<?php echo $this->Html->url(array('controller'=>'users', 'action'=>'view'));?>/" + rowdata['user_id'] + "'>"+options.rowId+"</a>";
}
//From: http://stackoverflow.com/questions/7246506/how-to-wrap-single-column-header-text-into-multiple-lines-in-jqgrid
// get the header row which contains
headerRow = $("#list").closest("div.ui-jqgrid-view")
    .find("table.ui-jqgrid-htable>thead>tr.ui-jqgrid-labels");

// increase the height of the resizing span
resizeSpanHeight = 'height: ' + headerRow.height() +
    'px !important; cursor: col-resize;';
headerRow.find("span.ui-jqgrid-resize").each(function () {
    this.style.cssText = resizeSpanHeight;
});

// set position of the dive with the column header text to the middle
rowHight = headerRow.height();
headerRow.find("div.ui-jqgrid-sortable").each(function () {
    var ts = $(this);
    ts.css('top', (rowHight - ts.outerHeight()) / 2 + 'px');
});

jQuery(document).ready(function() {
    $(".jqgrid_header_link").click(function() {
        window.location=$(this).attr('href');
    });
    $( "#dialog-modal" ).dialog({
        autoOpen: false,
        show: {
          effect: "blind",
          duration: 500
        },
        hide: {
          effect: "blind",
          duration: 500
        }
      });
    $("#list_type").change(function() {
        //alert('Im here');
    	var caption;
    	var list_type;
    	if($('#list_type option:selected').val()=="expected"){
        	caption = "Expected attendees only";
    		list_type="expected";
        	}
		else{
			caption = "All attendees";
    		list_type="all";
			}
    	$("#list").jqGrid('setCaption', caption);	
    	$("#list").jqGrid().setGridParam({url : '<?php echo $this->Html->url(array("controller" => "events_users","action" => "ajax_get_course_attendance","course_id" => $course['Course']['id']));?>'+'/type:'+list_type}).trigger('reloadGrid');
    	$("#download_link").attr('href','<?php echo $this->Html->url(array("controller" => "events_users","action" => "ajax_get_course_attendance","course_id" => $course['Course']['id'],"download"=>"true"));?>/type:'+list_type);
    	//$('#list').trigger('reloadGrid');
    });
});
</script>
