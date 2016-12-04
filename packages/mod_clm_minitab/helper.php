<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

class modCLM_MinitabHelper
{
	
	public static function getCLMLiga(  &$params )
	{
	
	global $mainframe;
	
	$liga	= $params->def('liga');
	$db	= JFactory::getDBO();
 
		$query = " SELECT a.* FROM #__clm_liga as a"
			." WHERE a.id = ".$liga
			;
			
		$db->setQuery( $query );
		$liga = $db->loadObjectList();
	
		return $liga;
	}
	
	
	public static function getCLMPunkte(  &$params )
	{
	
	global $mainframe;
	
	$liga	= $params->def('liga');
	$runde	= JRequest::getInt('runde');
	
	$db		= JFactory::getDBO();
	
	// ordering für Rangliste -> Ersatz für direkten Vergleich
		$query = "SELECT a.sid, a.order, a.b_wertung FROM #__clm_liga as a"
			." WHERE id = ".$liga
			;
		$db->setQuery($query);
		$order = $db->loadObjectList();
 			if ($order[0]->order == 1) { $ordering = " , m.ordering ASC";}
			else { $ordering =', a.tln_nr ASC ';} 
		$query = " SELECT a.sid,  a.tln_nr as tln_nr,m.name as name, SUM(a.manpunkte) as mp, m.zps, "
			." SUM(a.brettpunkte) as bp, SUM(a.wertpunkte) as wp, m.published, m.man_nr "
			." FROM #__clm_rnd_man as a "
			." LEFT JOIN #__clm_mannschaften as m ON m.liga = $liga AND m.tln_nr = a.tln_nr "
			." WHERE a.lid = ".$liga
			." AND m.man_nr <> 0 ";
			if ($runde != "") { $query = $query." AND runde < ".($runde +1);}
		$query = $query	
			." GROUP BY a.tln_nr ";
		if ($order[0]->b_wertung == 0) {   
			$query = $query
			." ORDER BY mp DESC, bp DESC".$ordering; }
		if ($order[0]->b_wertung == 3) { 
			$query = $query
			." ORDER BY mp DESC, bp DESC, wp DESC".$ordering; }
		if ($order[0]->b_wertung == 4) { 
			$query = $query
			." ORDER BY mp DESC, bp DESC, ".$ordering.", wp DESC"; }
			
		$db->setQuery( $query );
		$punkte = $db->loadObjectList();
	
		return $punkte;
	}

}