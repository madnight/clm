<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

class modCLM_LogHelper
{
 public static function getData(&$params)
	{
	global $mainframe;
	$db		= JFactory::getDBO();
	$user		=  JFactory::getUser();
	$jid		= $user->get('id');

	$query = " SELECT a.*, u.name as typ, v.name as vname"
		." FROM #__clm_user as a"
		." LEFT JOIN #__clm_usertype as u ON u.usertype = a.usertype"
		." LEFT JOIN #__clm_vereine as v ON v.ZPS = a.zps AND v.sid = a.sid"
		." LEFT JOIN #__clm_saison as s ON s.id = a.sid "
		." WHERE jid = ".$jid
		." AND s.archiv = 0 AND s.published = 1"
		;
	$db->setQuery( $query );
	$data = $db->loadObjectList();

	return $data;
	}

 public static function getLiga(&$params)
	{
	global $mainframe;
	$db		= JFactory::getDBO();
	$user		= JFactory::getUser();
	$jid 		= $user->get('id');

	// Konfigurationsparameter auslesen
	$config = clm_core::$db->config();
	$meldung_verein	= $config->meldung_verein;
	$meldung_heim	= $config->meldung_heim;


	$db->setQuery("SET SQL_BIG_SELECTS=1");
	$db->query();

	$query = "SELECT l.rang,t.meldung,l.name as lname,i.gid,p.sid,p.lid,p.runde,p.paar,p.dg,p.tln_nr,p.gegner,a.zps,  "
		." l.durchgang as durchgang, " 
		." t.deadlineday, t.deadlinetime, t.name as rname, " 
		." m.id,m.sid,m.name,m.liga,m.man_nr,m.published,p.gemeldet "
		." , m.liste "
		." FROM #__clm_user as a"
		//." LEFT JOIN #__clm_mannschaften as m ON (m.zps = a.zps or m.sg_zps = a.zps) AND m.sid = a.sid "
		." LEFT JOIN #__clm_mannschaften as m ON (m.zps = a.zps or FIND_IN_SET(a.zps,m.sg_zps) != 0 ) AND m.sid = a.sid "
		." LEFT JOIN #__clm_saison as s ON s.id = m.sid "
		." LEFT JOIN #__clm_rnd_man as p ON ( m.tln_nr = p.tln_nr AND p.lid = m.liga AND p.sid = a.sid)  "
		." LEFT JOIN #__clm_mannschaften as mg ON mg.tln_nr = p.gegner AND mg.liga = p.lid "
		." LEFT JOIN #__clm_liga as l ON ( l.id = m.liga AND l.sid = m.sid) "
		." LEFT JOIN #__clm_rangliste_id as i ON i.zps = a.zps AND i.gid = l.rang "
		//." LEFT JOIN jos_clm_runden_termine as t ON t.nr = p.runde AND t.liga = m.liga AND t.sid = a.sid "
		." LEFT JOIN #__clm_runden_termine as t ON t.nr = (p.runde + (l.runden * (p.dg - 1))) AND t.liga = m.liga AND t.sid = a.sid " //klkl
		." WHERE jid = ".$jid
		." AND mg.man_nr > 0 "		
		;
	if ($meldung_verein == 0) { $query = $query." AND m.mf = ".$jid;}
	if ($meldung_heim == 0) { $query = $query." AND p.heim = 1";}
		$query = $query
		." AND s.published = 1 AND s.archiv = 0 AND  l.rnd = 1 AND l.published = 1 "
		." ORDER BY l.rang, m.man_nr ASC, p.dg ASC, p.runde ASC "
		;
	$db->setQuery( $query );
	$liga = $db->loadObjectList();

	return $liga;
	}

 public static function getMannschaften($params)
	{
	global $mainframe;
	$db	= JFactory::getDBO();
	$user	= JFactory::getUser();
	$jid 	= $user->get('id');

	// Konfigurationsparameter auslesen
	$config = clm_core::$db->config();
	$meldung_verein	= $config->meldung_verein;

	$query = " SELECT COUNT(m.id) as count "
		." FROM #__clm_user as a"
		//." LEFT JOIN #__clm_mannschaften as m ON (m.zps = a.zps or m.sg_zps = a.zps)"
		." LEFT JOIN #__clm_mannschaften as m ON (m.zps = a.zps or FIND_IN_SET(a.zps,m.sg_zps) != 0 )"
		." LEFT JOIN #__clm_saison as s ON s.id = m.sid "
		." LEFT JOIN #__clm_liga as l ON l.id = m.liga AND l.sid = m.sid  "
		." WHERE jid = ".$jid
		." AND s.published = 1 AND s.archiv = 0 AND m.published = 1 AND l.rnd = 1 "
		;
	if ($meldung_verein == 0) { $query = $query." AND mf = ".$jid;}
	$db->setQuery( $query );
	$count = $db->loadObjectList();

	return $count;
	}

 public static function getMeldeliste($params)
	{
	global $mainframe;
	$db		= JFactory::getDBO();
	$user		= JFactory::getUser();
	$jid 		= $user->get('id');

	//$query = " SELECT m.liste, m.man_nr, m.name, m.sid, m.zps, l.name AS liganame, m.liga as lid"
	$query = " SELECT m.liste, m.man_nr, m.name, m.sid, m.zps, l.name AS liganame, m.liga as lid, m.liste, l.params "
		." FROM #__clm_user as a"
		//." LEFT JOIN #__clm_mannschaften as m ON (m.zps = a.zps or m.sg_zps = a.zps) AND m.sid = a.sid"
		." LEFT JOIN #__clm_mannschaften as m ON (m.zps = a.zps or FIND_IN_SET(a.zps,m.sg_zps) != 0 ) AND m.sid = a.sid"
		." LEFT JOIN #__clm_liga as l ON l.sid = a.sid AND l.id = m.liga"
		." LEFT JOIN #__clm_saison as s ON s.id = m.sid "
		." WHERE jid = ".$jid
		." AND l.rang = 0 "
		." AND s.published = 1 AND s.archiv = 0 AND m.published = 1 "
		//." AND m.liste < 1 
		." ORDER BY m.man_nr ASC"
		;
	$db->setQuery( $query );
	$meldeliste = $db->loadObjectList();

	return $meldeliste;
	}

 public static function getRangliste(&$params)
	{
	global $mainframe;
	$db			= JFactory::getDBO();
	$user			= JFactory::getUser();
	$jid =  $user->get('id');

	$query = "SELECT zps FROM #__clm_user as u"
		." LEFT JOIN #__clm_saison as s ON s.id = u.sid "
		." WHERE u.jid =".$jid
		." AND s.published = 1 AND s.archiv = 0 "
		;
	$db->setQuery( $query );
	$zps_user = $db->loadObjectList();

	if(isset($zps_user[0]->zps)){
	$zps = $zps_user[0]->zps;


	$query = " SELECT a.sid as sid,a.rang as gid,m.zps as zps,i.id,n.Gruppe as gruppe,a.params "
		." FROM #__clm_liga as a "
		." LEFT JOIN #__clm_mannschaften as m ON m.liga = a.id AND m.sid = a.sid "
		." LEFT JOIN #__clm_rangliste_name as n ON n.id = a.rang AND n.sid = a.sid "
		." LEFT JOIN #__clm_rangliste_id as i ON i.gid = n.id AND i.zps = m.zps "
		." LEFT JOIN #__clm_saison as s ON s.id = a.sid "
		." WHERE m.zps = '".$zps."' "
		//." AND a.rang <> 0 AND a.published = 1 AND s.published = 1 AND s.archiv = 0 AND i.id IS NULL "
		." AND a.rang <> 0 AND a.published = 1 AND s.published = 1 AND s.archiv = 0 "
		." GROUP BY n.Gruppe "
		." ORDER BY m.man_nr ASC"
		;
	$db->setQuery( $query );
	$rangliste = $db->loadObjectList();
	}
	else { $rangliste = ""; }

	return $rangliste;
	}
}
 
