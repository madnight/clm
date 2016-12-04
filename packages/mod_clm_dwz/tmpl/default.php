<?php // no direct access
defined('_JEXEC') or die('Restricted access'); 

$vereine = modCLM_DWZHelper::getVereine($params);

?>
<ul class="menu">
<?php 
  foreach ($vereine as $verein) {
  	if(!is_array($verein)) {
  		echo '<li>'.$verein.'</li>';
  	}  else {
  	  	echo '<li><a href="'.JRoute::_('index.php?option=com_clm&amp;view='.$verein[3].'&amp;saison='.$verein[0].'&amp;zps='.$verein[1]).'">'.$verein[2].'</a></li>';
  	}	
  }
?></ul>