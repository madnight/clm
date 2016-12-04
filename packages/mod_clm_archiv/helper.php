<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

class modCLM_ArchivHelper
{
	public static function getLink(&$params)
	{
	global $mainframe;
	$db			= JFactory::getDBO();
	$par_mt_type = $params->def('mt_type', 0);
	$sid		= JRequest::getInt( 'saison', 0);
	$turnier	= JRequest::getInt( 'turnier', 0);
	$liga		= JRequest::getInt( 'liga', 0);
	$atyp		= JRequest::getInt( 'atyp', 0);

	if ($par_mt_type == 0) {
		if (($atyp == 0 OR $liga > 0) AND $turnier == 0 AND $sid > 0) {
			$query = "SELECT  a.sid, a.id, a.name, a.runden, a.durchgang, a.rang, a.runden_modus, a.liga_mt "
				."\n FROM #__clm_liga as a"
				."\n LEFT JOIN #__clm_saison as s ON s.id = a.sid "
				."\n WHERE a.published = 1"
				."\n AND s.published = 1"
				."\n AND s.archiv  = 1 "
				." AND a.sid  = ".$sid
				."\n ORDER BY a.sid DESC,a.ordering ASC, a.id ASC "
				;
			$db->setQuery( $query );
			$link = $db->loadObjectList();;
		} else {
			$link = array();
		}
	} else {
		if ($sid == 0 AND $turnier > 0) {
			$query = "SELECT  a.sid "
				."\n FROM #__clm_turniere as a"
				."\n WHERE a.id = ".$turnier
				;
			$db->setQuery( $query );
			$sid = $db->loadResult();
			//echo "<br>tsid: $turnier :"; var_dump($sid);
			JRequest::setVar('saison', $sid);
		}
		if (($atyp == 1 OR $turnier > 0) AND $liga == 0 AND $sid > 0) {
			$query = "SELECT  a.sid, a.id, a.name, a.runden, a.dg, a.typ "
				."\n FROM #__clm_turniere as a"
				."\n LEFT JOIN #__clm_saison as s ON s.id = a.sid "
				."\n WHERE a.published = 1"
				."\n AND s.published = 1"
				."\n AND s.archiv  = 1 "
				." AND a.sid  = ".$sid
				."\n ORDER BY a.sid DESC,a.ordering ASC, a.id ASC "
				;
			$db->setQuery( $query );
			$link = $db->loadObjectList();
		} else {
			$link = array();
		}
	} 

	return $link;
	}

	public static function getCount(&$params)
	{
	global $mainframe;
	$db			= JFactory::getDBO();
	$par_mt_type = $params->def('mt_type', 0);

	if ($par_mt_type == 0) {
		$query = "SELECT COUNT(a.id) as id "
			."\n FROM #__clm_liga as a"
			."\n LEFT JOIN #__clm_saison as s ON s.id = a.sid "
			."\n WHERE a.published = 1"
			."\n AND s.archiv  = 0"
			;
		$db->setQuery( $query );
		$count = $db->loadObjectList();;
	} else {
		$query = "SELECT COUNT(a.id) as id "
			."\n FROM #__clm_turniere as a"
			."\n LEFT JOIN #__clm_saison as s ON s.id = a.sid "
			."\n WHERE a.published = 1"
			."\n AND s.archiv  = 0"
			;
		$db->setQuery( $query );
		$count = $db->loadObjectList();;
	} 

	return $count;
	}

	public static function getSaison(&$params)
	{
	global $mainframe;
	$db			= JFactory::getDBO();

	$query = "SELECT  a.id, a.name "
		."\n FROM #__clm_saison as a "
		."\n WHERE a.published = 1"
		."\n AND a.archiv  = 1"
		."\n ORDER BY a.id DESC "
		;
	$db->setQuery( $query );
	$saison = $db->loadObjectList();;

	return $saison;
	}

	public static function getRunde(&$params) {
		$liga	= JRequest::getVar( 'liga', 0);
		$db	= JFactory::getDBO();
	
		$query = " SELECT  a.* "
			." FROM #__clm_runden_termine as a"
			." LEFT JOIN #__clm_saison as s ON s.id = a.sid "
			." WHERE a.liga =".$liga
			." AND s.published = 1"
			." AND s.archiv  = 1"
			." ORDER BY a.nr ASC"
			;
		$db->setQuery( $query );
		$runden = $db->loadObjectList();

		return $runden;
	}

}
 