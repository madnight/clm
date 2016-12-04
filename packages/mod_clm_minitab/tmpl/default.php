<?php // no direct access
defined('_JEXEC') or die('Restricted access'); 

$view	= JRequest::getVar( 'view' );

$dg	= JRequest::getInt( 'dg' );
$itemid	= JRequest::getInt( 'Itemid' );

?>

<link href="<?php echo JURI::base().'modules/mod_clm_minitab/tmpl/minitab_css.css'; ?>" rel="stylesheet" type="text/css" />

<style type="text/css">
<!--
tr.bold { <?php echo $bold; ?> }
-->
</style>

<table cellspacing="0" cellpadding="0" class="minitab">
<?php if ( $show_th == 1 ) { ?>
<tr>
	<th class="rang"></th>
	<th class="team"><div><?php echo JText::_('Team') ?></div></th>
	<?php if ( $show_mp == 1 ) { ?><th class="mp"><div><?php echo JText::_('MP') ?></div></th><?php } ?>
	<?php if ( $show_bp == 1 ) { ?><th class="bp"><div><?php echo JText::_('BP') ?></div></th><?php } ?>
	<?php if ( $liga[0]->b_wertung > 0) { ?><th class="bp"><div><?php echo JText::_('WP') ?></div></th><?php } ?>
</tr>
<?php } ?>

<?php
// Anzahl der Teilnehmer durchlaufen
//for ($x=0; $x< ($liga[0]->teil)-$diff; $x++){
for ($x=0; $x< ($liga[0]->teil); $x++){
	if (!isset($punkte[$x]->zps)) break;
?>
<tr <?php if ($show_zps == 1) { if ($punkte[$x]->zps == $zps) { echo 'class="bold"'; } else { } } ?>>

	<td class="rang<?php 
	if ($show_aufab == 1) {
		if($x < $liga[0]->auf) { echo "_auf"; }
		if($x >= $liga[0]->auf AND $x < ($liga[0]->auf + $liga[0]->auf_evtl)) { echo "_auf_evtl"; }
		if($x >= ($liga[0]->teil-$liga[0]->ab)) { echo "_ab"; }
		if($x >= ($liga[0]->teil-($liga[0]->ab_evtl + $liga[0]->ab)) AND $x < ($liga[0]->teil-$liga[0]->ab) ) { echo "_ab_evtl"; }
	}
	?>"><div><?php echo $x+1; ?></div></td>
    
	<td class="team">
	<?php 
	if ($punkte[$x]->published ==1) { 
		if ($altItemid != ""){
			echo '<div><a href="index.php?option=com_clm&view=mannschaft&saison='. $punkte[$x]->sid .'&liga='. $lid .'&tlnr='. $punkte[$x]->tln_nr .'&amp;Itemid='. $altItemid .'">'. $punkte[$x]->name ."</a></div>";
		} else {
			echo '<div><a href="index.php?option=com_clm&view=mannschaft&saison='. $punkte[$x]->sid .'&liga='. $lid .'&tlnr='. $punkte[$x]->tln_nr .'&amp;Itemid='. $itemid .'">'. $punkte[$x]->name ."</a></div>"; 
		}	
	} else { 
		echo "<div>". $punkte[$x]->name ."</div>";  
	} ?>
	</td>

	<?php if ( $show_mp == 1 ) { ?><td class="mp"><div><?php echo $punkte[$x]->mp; ?></div></td> <?php } ?>
	<?php if ( $show_bp == 1 ) { ?><td class="bp"><div><?php echo $punkte[$x]->bp; ?></div></td> <?php } ?>
	<?php if ( $liga[0]->b_wertung > 0) { ?><td class="bp"><div><?php echo $punkte[$x]->wp; ?></div></td><?php } ?>
</tr>
<?php }
// Ende Teilnehmer
?>
</table>