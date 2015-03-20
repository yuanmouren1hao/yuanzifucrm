<?php
$str = $_GET['k'];
$seg = new SaeSegment();
$ret = $seg->segment($str, 0);
echo '<pre>';
print_r($ret);
echo '</pre>';