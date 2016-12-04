<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2011-2016 CLM Team  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Fjodor Schäfer
 * @email ich@vonfio.de
*/

defined('_JEXEC') or die('Restricted access'); 
jimport( 'joomla.html.parameter' );

$liga	= JRequest::getVar( 'liga');
$runde	= JRequest::getVar( 'runde');
$view	= JRequest::getVar( 'view' );
$dg		= JRequest::getVar( 'dg' );
$itemid	= JRequest::getVar( 'Itemid','' );
$start	= JRequest::getVar( 'start','1');
$categoryid	= JRequest::getInt( 'categoryid',0);
 
?>
<style type="text/css">
<?php 

	$document = JFactory::getDocument();

	$cssDir = JURI::base().'modules/mod_clm_termine';
	//	$cssDir = JURI::base().'components'.DS.'com_clm'.DS.'includes';

	$document->addStyleSheet( $cssDir.'/mod_clm_termine.css', 'text/css', null, array() );
 
?>
</style>

<?php 

if ($par_liste == 0) { 
?>
 
<ul class="menu">
 
<?php 	

$arrWochentag = array( 
		"Monday" => JText::_('MOD_CLM_TERMINE_T01'), 
		"Tuesday" => JText::_('MOD_CLM_TERMINE_T02'), 
		"Wednesday" => JText::_('MOD_CLM_TERMINE_T03'), 
		"Thursday" => JText::_('MOD_CLM_TERMINE_T04'), 
		"Friday" => JText::_('MOD_CLM_TERMINE_T05'), 
		"Saturday" => JText::_('MOD_CLM_TERMINE_T06'), 
		"Sunday" => JText::_('MOD_CLM_TERMINE_T07') );
$count = 0; 
if ($start == '1') $start = date("Y-m-d");
for ($t = 0; $t < $par_anzahl; $t++) {
	if (!isset($runden[$t])) break;
	if ($runden[$t]->datum >= $start) { 
 
		// Veranstaltung verlinken
		if ($runden[$t]->source == 'termin') { 
			$linkname = "index.php?option=com_clm&amp;view=termine&amp;nr=". $runden[$t]->id ."&amp;layout=termine_detail&amp;categoryid=".$categoryid; 
		} elseif ($runden[$t]->ligarunde != 0) { 
			//$linkname = "index.php?option=com_clm&amp;view=runde&amp;saison=" . $runden[$t]->sid . "&amp;liga=" .  $runden[$t]->typ_id ."&amp;runde=" . $runden[$t]->nr ."&amp;dg=" . $runden[$t]->durchgang;
			if (($runden[$t]->durchgang > 1) AND ($runden[$t]->nr > $runden[$t]->runden))
				$linkname = "index.php?option=com_clm&amp;view=runde&amp;saison=" . $runden[$t]->sid . "&amp;liga=" .  $runden[$t]->typ_id ."&amp;runde=" . ($runden[$t]->nr - $runden[$t]->runden) ."&amp;dg=2";
			else 
				$linkname = "index.php?option=com_clm&amp;view=runde&amp;saison=" . $runden[$t]->sid . "&amp;liga=" .  $runden[$t]->typ_id ."&amp;runde=" . $runden[$t]->nr ."&amp;dg=1";
			
		} else {
			$linkname = "index.php?option=com_clm&amp;view=turnier_runde&amp;runde=" . $runden[$t]->nr . "&amp;turnier=" . $runden[$t]->typ_id; }
		$linkname .= "&amp;start=". $runden[$t]->datum;             
		// Datumsberechnungen
		$datum[$t] = strtotime($runden[$t]->datum);
		$datum_arr[$t] = explode("-",$runden[$t]->datum);
		
		$datum_link = '<a href="'. $linkname;
		if ($itemid <>'') { $datum_link .= '&Itemid='.$itemid; }
		$datum_link .= '">'. $arrWochentag[date("l",$datum[$t])]. ',&nbsp;' . $datum_arr[$t][2].'.'.$datum_arr[$t][1].'.'.$datum_arr[$t][0];
		if ($runden[$t]->enddatum > $runden[$t]->datum) { 
			$enddatum[$t] = strtotime($runden[$t]->enddatum);
			$enddatum_arr[$t] = explode("-",$runden[$t]->enddatum); 
			$datum_link .= ' - '. $arrWochentag[date("l",$enddatum[$t])]. ',&nbsp;' . $enddatum_arr[$t][2].'.'.$enddatum_arr[$t][1].'.'.$enddatum_arr[$t][0]; 
		} else {
			$enddatum[$t] = '';
		}
		$datum_link .= "</a>\n";
						
    echo '<li>'; 
	
		if ($par_datum == 1) { // Parameter prüfen: Datum
			if ((isset($datum[$t-1])) AND ($datum[$t] == $datum[$t-1]) AND (isset($enddatum[$t-1])) AND ($enddatum[$t] == $enddatum[$t-1])) { echo ''; }      //klkl
				else { 
					if ($par_datum_link == 1) { // Parameter prüfen: Datum verlinken
						echo $datum_link;
					} else {  
						echo $arrWochentag[date("l",$datum[$t])]. ",&nbsp;" . $datum_arr[$t][2].".".$datum_arr[$t][1].".".$datum_arr[$t][0]; 
							if ($runden[$t]->enddatum > $runden[$t]->datum) { //klkl
							echo ' - '.$arrWochentag[date("l",$enddatum[$t])]. ',&nbsp;' . $enddatum_arr[$t][2].'.'.$enddatum_arr[$t][1].'.'.$enddatum_arr[$t][0]; }
					} 	
				 } 
		} else { } 
		if (($par_name == 1) OR ($par_typ == 1) AND (($runden[$t]->name <>'') AND ($runden[$t]->typ <>'')) ) {
			
			if ($par_termin_link == 1 ) {
				echo '<a href="'. $linkname;
				if ($itemid <>'') { echo "&Itemid=".$itemid; }
				echo '">';
			}
				if ($runden[$t]->starttime != '00:00:00') { echo "&nbsp;&nbsp;".substr($runden[$t]->starttime,0,5); } // Starttime 								
				if (($par_name == 1) OR ($par_typ == 1) AND ($runden[$t]->typ <>'') ) { echo "&nbsp;&nbsp;"; }
				if ($par_name == 1) { echo $runden[$t]->name ."\n"; } // Parameter prüfen: Terminname 				
				if (($par_name == 1) AND ($par_typ == 1) AND ($runden[$t]->typ <>'') ) { echo "&nbsp;-&nbsp;"; }
				if ($par_typ == 1) { echo $runden[$t]->typ ."\n"; }  // Parameter prüfen: Ort / Liga / Turnier
			if ($par_termin_link == 1 ) {
				echo "</a>\n";
			}
			
			echo '<br />';
	 	} else { 
			echo '<br />'; 
		}
		
    echo "</li>\n";

} 
} ?>

</ul>


<?php
} else { 

// Termine als Timestamp zu einem Array machen
$datum_stamp	= array ();
$datumend_stamp	= array ();
// Termin Details
$event_desc		= array ();
for ( $a = 0; $a < count ($runden); $a++ ) {

	// Veranstaltung verlinken
	if ($runden[$a]->source == 'termin') { 
 		$linkname = "index.php?option=com_clm&amp;view=termine&amp;nr=". $runden[$a]->id ."&amp;layout=termine_detail"; 
 	} elseif ($runden[$a]->ligarunde != 0) { 
 		//$linkname = "index.php?option=com_clm&amp;view=runde&amp;saison=". $runden[$a]->sid ."&amp;liga=".  $runden[$a]->typ_id ."&amp;runde=". $runden[$a]->nr ."&amp;dg=". $runden[$a]->durchgang; 
//		if (($runden[$a]->durchgang > 1) AND ($runden[$a]->nr > $runden[$a]->runden))
//			$linkname = "index.php?option=com_clm&amp;view=runde&amp;saison=" . $runden[$a]->sid . "&amp;liga=" .  $runden[$a]->typ_id ."&amp;runde=" . ($runden[$a]->nr - $runden[$a]->runden) ."&amp;dg=2";
		if (($runden[$a]->durchgang > 1) AND ($runden[$a]->nr > $runden[$a]->ligarunde))
			$linkname = "index.php?option=com_clm&amp;view=runde&amp;saison=" . $runden[$a]->sid . "&amp;liga=" .  $runden[$a]->typ_id ."&amp;runde=" . ($runden[$a]->nr - $runden[$a]->ligarunde) ."&amp;dg=2";
		else 
			$linkname = "index.php?option=com_clm&amp;view=runde&amp;saison=" . $runden[$a]->sid . "&amp;liga=" .  $runden[$a]->typ_id ."&amp;runde=" . $runden[$a]->nr ."&amp;dg=1";
 	} else {
 		$linkname = "index.php?option=com_clm&amp;view=turnier_runde&amp;runde=". $runden[$a]->nr ."&amp;turnier=". $runden[$a]->typ_id; 
	}
	$linkname .= "&amp;start=". $runden[$a]->datum; 
	$title			= $runden[$a]->name;
	$ende			= strtotime($runden[$a]->enddatum); 
	$anfang 		= strtotime($runden[$a]->datum);
		
	
	$datum_stamp[] 		= 	strtotime($runden[$a]->datum); 
	$event_desc[]		= 	array ($linkname , $title, $anfang, $ende  );  
	while ($ende > $anfang) {
		$anfang = mktime(0, 0, 0, date("m",$anfang)  , date("d",$anfang)+1, date("Y",$anfang));
		$datum_stamp[] 		= 	$anfang; 
		$event_desc[]		= 	array ($linkname , $title, $anfang, $ende  );  
	}
}
array_multisort ($datum_stamp, $event_desc);

// Mehrdimensionaler Array mit allen Information. Das Timestamp ist der Key
$event	= array_combine ($datum_stamp, $event_desc);

if( isset($_REQUEST['timestamp'])) { $date = $_REQUEST['timestamp']; }
else { $date = time(); }
if ($start != '1') {
	$start_arr = explode("-",$start);
    $date = mktime(0,0,0,$start_arr[1],$start_arr[2],$start_arr[0]);
}

$arrMonth = array(
    "January" => JText::_('MOD_CLM_TERMINE_M01'),
    "February" => JText::_('MOD_CLM_TERMINE_M02'),
    "March" => JText::_('MOD_CLM_TERMINE_M03'),
    "April" => JText::_('MOD_CLM_TERMINE_M04'),
    "May" => JText::_('MOD_CLM_TERMINE_M05'),
    "June" => JText::_('MOD_CLM_TERMINE_M06'),
    "July" => JText::_('MOD_CLM_TERMINE_M07'),
    "August" => JText::_('MOD_CLM_TERMINE_M08'),
    "September" => JText::_('MOD_CLM_TERMINE_M09'),
    "October" => JText::_('MOD_CLM_TERMINE_M10'),
    "November" => JText::_('MOD_CLM_TERMINE_M11'),
    "December" => JText::_('MOD_CLM_TERMINE_M12')
);
    
//$headline = array('Mo','Di','Mi','Do','Fr','Sa','So');
$headline = array( 
		JText::_('MOD_CLM_TERMINE_K01'), 
		JText::_('MOD_CLM_TERMINE_K02'), 
		JText::_('MOD_CLM_TERMINE_K03'), 
		JText::_('MOD_CLM_TERMINE_K04'), 
		JText::_('MOD_CLM_TERMINE_K05'), 
		JText::_('MOD_CLM_TERMINE_K06'), 
		JText::_('MOD_CLM_TERMINE_K07') );

$linkname_tl = "index.php?option=com_clm&amp;view=termine&amp;Itemid=1"; 
$htext = $arrMonth[date('F',$date)].' '.date('Y',$date);

?>
<?php // URI holen  $uri     = &JFactory::getUri();  
// URL :  $uri->toString(); ?>
<center>
<div class="kalender">
    <div class="kal_pagination">
        <a href="?timestamp=<?php echo modCLMTermineHelper::yearBack($date); ?>" class="last">&laquo;</a> 
        <a href="?timestamp=<?php echo modCLMTermineHelper::monthBack($date); ?>" class="last">&lsaquo;</a> 
        <span><a title="<?php echo 'Termine '.$htext; ?>" href="<?php echo $linkname_tl.'&amp;start='.date('Y-m',$date).'-01'; ?>"><?php echo $htext ?></a></span>
        <a href="?timestamp=<?php echo modCLMTermineHelper::monthForward($date); ?>" class="next">&rsaquo;</a>
        <a href="?timestamp=<?php echo modCLMTermineHelper::yearForward($date); ?>" class="next">&raquo;</a>
        <div class="clear"></div>  
    </div>
    <?php modCLMTermineHelper::getCalender($date,$headline,$event,$datum_stamp); ?>
    <div class="clear"></div>
</div>
</center>
<?php } ?>
 
