<?php
function clm_escape_dwz($in)
{
$db = JFactory::getDbo(); 
return $db->escape($in);
}
?>
