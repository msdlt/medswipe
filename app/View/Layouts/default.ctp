<?php
/**
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.cake.libs.view.templates.layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo "MedSwipe".($title_for_layout?": ".$title_for_layout:""); ?>
	</title>
	<?php
		echo $this->Html->meta('icon');

		echo $this->Html->css('cake.generic');
		echo $this->Html->css('medswipe');
		echo $this->Html->css('msdlt');
		//echo $this->Html->css('datepicker');
		echo $this->Html->css('ui.jqgrid');
		echo $this->Html->css('ui-lightness/jquery-ui-1.10.3.custom');
		echo $this->Html->css('jquery-ui-timepicker-addon');
		//echo $this->Html->script('datepicker/datepicker.packed');
		echo $this->Html->script('jquery-1.10.2.min');
		echo $this->Html->script('jquery-ui-1.10.3.custom.min');
		echo $this->Html->script('jquery-ui-timepicker-addon');
		echo $this->Html->script('jquery-ui-sliderAccess');
		echo $this->Html->script('jqgrid/i18n/grid.locale-en');
		echo $this->Html->script('jqgrid/jquery.jqGrid.min');
		//echo $this->Html->script('jquery.tools.min.js');
		echo $this->Html->script('jquery.form');
		echo $scripts_for_layout;
	?>
	<script src="https://code.jquery.com/jquery-migrate-1.0.0.js"></script>
</head>
<body>
	<div id="container">
<!-- START OF HEADER -->
		<div id="top">
			<div id="top_bar_top">
				<div id="top_bar_top_flash_msg"><?php echo $this->element('topnav');?></div>
			</div>
			<div id="top_bar_middle">
				<div id="top_bar_middle_titlebg"><span style="color: #AB8E42;">Med</span>Swipe</div>
			</div>
			<div id="top_bar_bottom">
				<div id="navcontainer"><?php echo $this->element('tabnav');?></div>
			</div>
		</div>
<!-- END OF HEADER -->
<!-- START OF CONTENT -->
		<div id="leftnav">
		</div>
		<div id="rightnav">
		</div>
		<div id="content">
			<?php echo $this->Session->flash(); ?>
			<?php echo $content_for_layout; ?>
		</div>
<!-- END OF CONTENT -->
<div class="clear"></div>
<!-- FOOTER -->
	    <div id="footer">
	    	<div id="footer_container">
		       	<div id="msd_logo">
		       		<?php 
		       		echo $this->Html->link(
					$this->Html->image("logos/MSD-logo.gif", array("alt" => "Medical Sciences Division Learning Technologies", "title"=>"Medical Sciences Division Learning Technologies", "height"=>"73", "width"=>"73")),
					"http://emsd.medsci.ox.ac.uk/",
					array('escape' => false, 'target'=>'_blank')
					);
					?>
		       	</div> 
		        <div id="oxlogo">
		        	<?php 
		       		echo $this->Html->link(
					$this->Html->image("logos/uni-ox-logo.gif", array("alt" => "University of Oxford logo", "title"=>"University of Oxford logo")),
					"http://www.ox.ac.uk/",
					array('escape' => false, 'target'=>'_blank')
					);
					?>
		        </div>
				<div id="footercontent">
					<p id="contactlinks"><a href="mailto:msdlt@medsci.ox.ac.uk">Contact Medical Sciences Division Learning Technologies</a></p>
					<p id="legallinks"><a href="http://www.admin.ox.ac.uk/dataprotection/privacypolicy/" target="_blank">University Privacy Policy</a> | <a href="http://www.ox.ac.uk/copyright/" target="_blank">University Copyright Statement</a> | <a href="http://www.ox.ac.uk/web/accessibility.html" target="_blank">University Accessibility Statement</a></p>
					<p id="copyright">&copy; University of Oxford</p>
				</div>
			</div>
		</div>
<!-- END OF FOOTER -->
	</div>
	<?php //echo $this->element('sql_dump'); ?>
	<?php echo $this->Js->writeBuffer(); // Write cached scripts ?>
</body>
</html>