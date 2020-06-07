<?php // views/elements/errors.ctp
function flattenArray(array $array){
  $ret_array = array();
  foreach(new RecursiveIteratorIterator(new RecursiveArrayIterator($array)) as $value)
	 {
	 $ret_array[] = $value;
	 }
  return $ret_array;
  }
if (!empty($errors)) {
	$errors = flattenArray($errors);
	?>
<div class="errors">
    <h3>I found <?php echo count($errors); ?> error(s):</h3>
    <ul>
        <?php foreach ($errors as $field => $error) { ?>
        <li><?php echo $error; ?></li>
        <?php } ?>
    </ul>
</div>
<?php } ?>
