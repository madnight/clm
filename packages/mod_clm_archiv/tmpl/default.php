<?php // no direct access
defined('_JEXEC') or die('Restricted access'); 

$sid	= JRequest::getInt( 'saison');
$liga	= JRequest::getInt( 'liga');
$turnier	= JRequest::getInt( 'turnier', 0);
$runde	= JRequest::getInt( 'runde');
$view	= JRequest::getVar( 'view' );
$dg	= JRequest::getInt( 'dg' );
// itemid
if($par_itemid == '' || !is_numeric($par_itemid)) {
	$itemid	= JRequest::getVar( 'Itemid' );
} else {
	$itemid = $par_itemid;
}
$par_mt_type = $params->def('mt_type', 0);
$typeid	= JRequest::getVar( 'typeid' );
if (!isset($typeid)) $typeid = 21; 

//URL-Test: falls nicht belegt --> mod_clm oder mod_clm_archiv
//			falls belegt --> mod_clm_ext
$url		= JRequest::getVar('url');

// Konfigurationsparameter auslesen
$config = clm_core::$db->config();
$countryversion	= $config->countryversion;
	
?>
<ul class="menu">
<?php 
    foreach($saison as $saison){ ?>
	<li id="current" class="first_link">
	    <a href="index.php?option=com_clm&amp;view=info&amp;saison=<?php echo $saison->id;?>&amp;atyp=<?php echo $par_mt_type;?>&amp;Itemid=<?php echo $itemid;?>"
	    <?php if (isset($link->id) AND $liga == $link->id AND $view == 'rangliste') {echo ' class="active_link"';} ?>>
	    <span><?php echo "<b>".$saison->name."</b>"; ?></span>
	    </a>
	</li>
<?php    
//} else {
if($sid == $saison->id AND !isset($url)) { ?>
<ul><?php
foreach ($link as $link) {
  if ($par_mt_type == 0) {
// Hauptlinks des Menüs für Ligen und Mannschaftsturniere
?>
	<li <?php if ($liga == $link->id AND $typeid == 21) { ?> id="current" class="first_link" <?php } ?>>
	<?php $typeid = 21; 
		$view21 = 'rangliste';
		if ($link->runden_modus == 1 OR $link->runden_modus == 2 OR $link->runden_modus == 3) $view21 = 'rangliste';
	    if ($link->runden_modus == 4 OR $link->runden_modus == 5) $view21 = 'paarungsliste'; ?>
	<a href="index.php?option=com_clm&amp;view=<?php echo $view21;?>&amp;saison=<?php echo $link->sid;?>&amp;liga=<?php echo $link->id;?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?><?php echo "&typeid=".$typeid; ?>"
	<?php if ($liga == $link->id AND $view == $view21 ) {echo ' class="active_link"';} ?>>
	<span><?php echo $link->name; ?></span>
	</a>
<?php 
// Unterlinks falls Link angeklickt
if ($liga == $link->id AND $view == $view21 AND !isset($url) ) { ?>
	<ul>
		<?php $typeid = 22; 
		if ($link->runden_modus == 1 OR $link->runden_modus == 2 OR $link->runden_modus == 3) { ?>
		<li class="first_link liga<?php echo $liga; ?>">
		<a href="index.php?option=com_clm&amp;view=paarungsliste&amp;saison=<?php echo $link->sid; ?>&amp;liga=<?php echo $liga; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?><?php if ($typeid <>'') { echo "&typeid=".$typeid; } ?>">
		<span><?php echo JText::_('MOD_CLM_ARCHIV_PAIRINGLIST_LABEL'); ?></span></a>
		</li>
		<?php } ?>
	<?php for ($y=0; $y < $link->runden; $y++) { ?>
		<li>
		<a href="index.php?option=com_clm&amp;view=runde&amp;saison=<?php echo $link->sid; ?>&amp;liga=<?php echo $liga; ?>&amp;runde=<?php echo $y+1; ?>&amp;dg=1<?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?><?php if ($typeid <>'') { echo "&typeid=".$typeid; } ?>">
		<span><?php if ($runden[$y]->published =="0") { ?><s><?php } echo $runden[$y]->name; ?><?php if ($runden[$y]->published =="0") { ?></s><?php } ?></span></a>
		</li>
	<?php } $cnt = $y;
	if ($link->durchgang > 1) {
	for ($y=0; $y < $link->runden; $y++) { ?>
		<li <?php if ($view == 'runde') { ?> id="current" class="active" <?php } ?>>
		<a href="index.php?option=com_clm&amp;view=runde&amp;saison=<?php echo $link->sid; ?>&amp;liga=<?php echo $liga; ?>&amp;runde=<?php echo $y+1; ?>&amp;dg=2<?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?><?php if ($typeid <>'') { echo "&typeid=".$typeid; } ?>" <?php if ($view == 'runde' AND $runde == ($y+1)) { ?> class="active_link" <?php } ?>>
		<span><?php if ($runden[$y+$cnt]->published =="0") { ?><s><?php } echo $runden[$y+$cnt]->name; ?><?php if ($runden[$y+$cnt]->published =="0") { ?></s><?php } ?></span></a>
		</li>
	<?php }} 
	if ($link->durchgang > 2) {
	for ($y=0; $y < $link->runden; $y++) { ?>
		<li <?php if ($view == 'runde') { ?> class="active" <?php } ?>>
		<a href="index.php?option=com_clm&amp;view=runde&amp;saison=<?php echo $link->sid; ?>&amp;liga=<?php echo $liga; ?>&amp;runde=<?php echo $y+1; ?>&amp;dg=3<?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?><?php if ($typeid <>'') { echo "&typeid=".$typeid; } ?>" <?php if ($view == 'runde' AND $runde == ($y+1)) { ?> class="active_link" <?php } ?>>
		<span><?php if ($runden[$y+(2 * $cnt)]->published =="0") { ?><s><?php } echo $runden[$y+(2 * $cnt)]->name; ?><?php if ($runden[$y+(2 * $cnt)]->published =="0") { ?></s><?php } ?></span></a>
		</li>
	<?php }} 
	if ($link->durchgang > 3) {
	for ($y=0; $y < $link->runden; $y++) { ?>
		<li <?php if ($view == 'runde') { ?> class="active" <?php } ?>>
		<a href="index.php?option=com_clm&amp;view=runde&amp;saison=<?php echo $link->sid; ?>&amp;liga=<?php echo $liga; ?>&amp;runde=<?php echo $y+1; ?>&amp;dg=4<?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?><?php if ($typeid <>'') { echo "&typeid=".$typeid; } ?>" <?php if ($view == 'runde' AND $runde == ($y+1)) { ?> class="active_link" <?php } ?>>
		<span><?php if ($runden[$y+(3 * $cnt)]->published =="0") { ?><s><?php } echo $runden[$y+(3 * $cnt)]->name; ?><?php if ($runden[$y+(3 * $cnt)]->published =="0") { ?></s><?php } ?></span></a>
		</li>
	<?php }} 
	
	if ($countryversion == "de") { ?>
		<li <?php if ($view == 'dwz_liga') { ?> id="current" class="active" <?php } ?>>
		<a href="index.php?option=com_clm&amp;view=dwz_liga&amp;saison=<?php echo $link->sid; ?>&amp;liga=<?php echo $liga; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?><?php if ($typeid <>'') { echo "&typeid=".$typeid; } ?>" <?php if ($view == 'dwz_liga') { ?> class="active_link" <?php } ?>>
		<span><?php if ($countryversion == "de") echo JText::_('MOD_CLM_ARCHIV_PARAM_DWZ_LABEL'); else echo JText::_('MOD_CLM_ARCHIV_PARAM_GRADES_LABEL'); ?></span></a>
		</li>
	<?php } ?> 

		<li <?php if ($view == 'statistik') { ?> id="current" class="active" <?php } ?>>
		<a href="index.php?option=com_clm&amp;view=statistik&amp;saison=<?php echo $link->sid; ?>&amp;liga=<?php echo $liga; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?><?php if ($typeid <>'') { echo "&typeid=".$typeid; } ?>" <?php if ($view == 'statistik') { ?> class="active_link" <?php } ?>>
		<span><?php echo JText::_('MOD_CLM_ARCHIV_PARAM_STATS_LABEL'); ?></span></a>
		</li>
	</ul>
	<?php } ?>
	</li>
<!-- Unterlink angeklickt -->
<?php 
	if ($liga == $link->id AND $view != $view21 AND !isset($url) ) { ?>
	<li class="parent active">
	<ul>
		<li <?php if ($view == 'paarungsliste') { ?> id="current" class="active" <?php } ?>>
		<a href="index.php?option=com_clm&amp;view=paarungsliste&amp;saison=<?php echo $link->sid; ?>&amp;liga=<?php echo $liga; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?><?php if ($typeid <>'') { echo "&typeid=".$typeid; } ?>" <?php if ($view == 'paarungsliste') { ?> class="active_link" <?php } ?>>
		<span><?php echo JText::_('MOD_CLM_ARCHIV_PAIRINGLIST_LABEL'); ?></span></a>
		</li>
	<?php for ($y=0; $y < $link->runden; $y++) { ?>
		<li <?php if ($view == 'runde' AND $dg == 1 AND ($runde == $y+1)) { ?> id="current" class="active" <?php } ?>>
		<a href="index.php?option=com_clm&amp;view=runde&amp;saison=<?php echo $link->sid; ?>&amp;liga=<?php echo $liga; ?>&amp;runde=<?php echo $y+1; ?>&amp;dg=1<?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?><?php if ($typeid <>'') { echo "&typeid=".$typeid; } ?>" <?php if ($view == 'runde' AND $runde == ($y+1)) { ?> class="active_link" <?php } ?>>
<!--		<span>Runde <?php echo $y+1; if ($link->durchgang >1) {echo " (Hinrunde)";}?></span></a> -->
		<span><?php if ($runden[$y]->published =="0") { ?><s><?php } echo $runden[$y]->name; ?><?php if ($runden[$y]->published =="0") { ?></s><?php } ?></span></a>
		</li>
	<?php } $cnt = $y;
	if ($link->durchgang > 1) {
	for ($y=0; $y < $link->runden; $y++) { ?>
		<li <?php if ($view == 'runde' AND $dg == 2 AND ($runde == $y+1)) { ?> id="current" class="active" <?php } ?>>
		<a href="index.php?option=com_clm&amp;view=runde&amp;saison=<?php echo $link->sid; ?>&amp;liga=<?php echo $liga; ?>&amp;runde=<?php echo $y+1; ?>&amp;dg=2<?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?><?php if ($typeid <>'') { echo "&typeid=".$typeid; } ?>" <?php if ($view == 'runde' AND $runde == ($y+1)) { ?> class="active_link" <?php } ?>>
<!--		<span>Runde <?php echo $y+1; ?> (Rückrunde)</span></a> -->
		<span><?php if ($runden[$y+$cnt]->published =="0") { ?><s><?php } echo $runden[$y+$cnt]->name; ?><?php if ($runden[$y+$cnt]->published =="0") { ?></s><?php } ?></span></a>
		</li>
	<?php }} 
	if ($link->durchgang > 2) {
	for ($y=0; $y < $link->runden; $y++) { ?>
		<li <?php if ($view == 'runde') { ?> class="active" <?php } ?>>
		<a href="index.php?option=com_clm&amp;view=runde&amp;saison=<?php echo $link->sid; ?>&amp;liga=<?php echo $liga; ?>&amp;runde=<?php echo $y+1; ?>&amp;dg=3<?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?><?php if ($typeid <>'') { echo "&typeid=".$typeid; } ?>" <?php if ($view == 'runde' AND $runde == ($y+1)) { ?> class="active_link" <?php } ?>>
		<span><?php if ($runden[$y+(2 * $cnt)]->published =="0") { ?><s><?php } echo $runden[$y+(2 * $cnt)]->name; ?><?php if ($runden[$y+(2 * $cnt)]->published =="0") { ?></s><?php } ?></span></a>
		</li>
	<?php }} 
	if ($link->durchgang > 3) {
	for ($y=0; $y < $link->runden; $y++) { ?>
		<li <?php if ($view == 'runde') { ?> class="active" <?php } ?>>
		<a href="index.php?option=com_clm&amp;view=runde&amp;saison=<?php echo $link->sid; ?>&amp;liga=<?php echo $liga; ?>&amp;runde=<?php echo $y+1; ?>&amp;dg=4<?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?><?php if ($typeid <>'') { echo "&typeid=".$typeid; } ?>" <?php if ($view == 'runde' AND $runde == ($y+1)) { ?> class="active_link" <?php } ?>>
		<span><?php if ($runden[$y+(3 * $cnt)]->published =="0") { ?><s><?php } echo $runden[$y+(3 * $cnt)]->name; ?><?php if ($runden[$y+(3 * $cnt)]->published =="0") { ?></s><?php } ?></span></a>
		</li>
	<?php }} 
	
	if ($countryversion == 'de') { ?>
		<li <?php if ($view == 'dwz_liga') { ?> id="current" class="active" <?php } ?>>
		<a href="index.php?option=com_clm&amp;view=dwz_liga&amp;saison=<?php echo $link->sid; ?>&amp;liga=<?php echo $liga; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?><?php if ($typeid <>'') { echo "&typeid=".$typeid; } ?>" <?php if ($view == 'dwz_liga') { ?> class="active_link" <?php } ?>>
		<span><?php if ($countryversion == 'de') echo JText::_('MOD_CLM_ARCHIV_PARAM_DWZ_LABEL'); else echo JText::_('MOD_CLM_ARCHIV_PARAM_GRADES_LABEL'); ?></span></a>
		</li>
	<?php } ?> 

		<li <?php if ($view == 'statistik') { ?> id="current" class="active" <?php } ?>>
		<a href="index.php?option=com_clm&amp;view=statistik&amp;saison=<?php echo $link->sid; ?>&amp;liga=<?php echo $liga; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?><?php if ($typeid <>'') { echo "&typeid=".$typeid; } ?>" <?php if ($view == 'statistik') { ?> class="active_link" <?php } ?>>
		<span><?php echo JText::_('MOD_CLM_ARCHIV_PARAM_STATS_LABEL'); ?></span></a>
		</li>
	</ul>
	</li>
<?php							}
		
  } else {
// Hauptlinks des Menüs für Einzelturniere
?>
	<li id="current" class="first_link">
	<a href="index.php?option=com_clm&amp;view=turnier_info&amp;saison=<?php echo $link->sid; ?>&amp;turnier=<?php echo $link->id;?>&amp;Itemid=<?php echo $itemid;?>"
	<?php if ($turnier == $link->id) {echo ' class="active_link"';} ?>>
	<span><?php echo "&nbsp;&nbsp;".$link->name; ?></span>
	</a>
<?php  }
 }
	?></ul><?php
			} 
			      } ?>
</ul>