<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2015 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined('_JEXEC') or die();
jimport('joomla.application.component.model');

class CLMModelMeldeliste extends JModelLegacy
{
	function _getCLMLiga( &$options )
	{
	$sid	= JRequest::getInt('saison','1');
	$zps	= clm_escape(JRequest::getVar('zps'));
	$man	= JRequest::getInt('man','1');
	$layout	= clm_escape(JRequest::getVar('layout'));
	$gid	= JRequest::getInt('gid');

		// TODO: Cache on the fingerprint of the arguments
		$db	= JFactory::getDBO();
		$id	= @$options['id'];

 	if($layout =="rangliste"){
		$query = "SELECT a.name as vname, r.Gruppe as gruppe "
			." FROM #__clm_vereine as a"
			." LEFT JOIN #__clm_rangliste_name as r ON r.id =".$gid
			." WHERE a.sid = $sid AND a.zps = '$zps' AND r.id = $gid "
			." AND a.published = 1 "
			;
				}
	else {
		$query = "SELECT a.name as man_name, l.name as liga, a.man_nr,l.id as lid, " 
			." a.zps, a.sg_zps, "
			." l.stamm, l.ersatz, l.rang, a.mf, a.lokal, v.lokal as vlokal "
			." FROM #__clm_mannschaften as a"
			." LEFT JOIN #__clm_liga as l ON l.id = a.liga AND l.sid = a.sid  "
			." LEFT JOIN #__clm_vereine as v ON v.zps = a.zps AND v.sid = a.sid  "
			." WHERE a.sid = $sid AND (a.zps = '$zps' OR FIND_IN_SET('$zps',a.sg_zps) != 0 ) AND a.man_nr = $man AND a.published = 1 "
			;
		}
	return $query;
	}
	function getCLMLiga( $options=array() )
	{
		$query	= $this->_getCLMLiga( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function _getCLMSpieler( &$options )
	{
	$sid	= JRequest::getInt('saison','1');
	$zps	= clm_escape(JRequest::getVar('zps'));
	$man	= JRequest::getInt('man','1');
	$layout	= clm_escape(JRequest::getVar('layout'));
	$gid	= JRequest::getInt('gid');
	//CLM parameter auslesen
	$config = clm_core::$db->config();
	$countryversion = $config->countryversion;
		// TODO: Cache on the fingerprint of the arguments
		$db	= JFactory::getDBO();
		$id	= @$options['id'];
	// Konfigurationsparameter auslesen
	$config = clm_core::$db->config();
	$val=$config->meldeliste;

	if (($layout =="rangliste") OR ($layout =="sent_rangliste")) {

	$sql = " SELECT * "
		." FROM #__clm_rangliste_name"
		." WHERE id =".$gid
		." AND sid = ".$sid
		;
	$db->setQuery($sql);
	$gid	= $db->loadObjectList();

	$melde = explode ("-",$gid[0]->Meldeschluss);
	$jahr = $melde[0];
	$gid1 = $gid[0]->id;


	$geb = "";
	$ges = "";
	if ($gid[0]->alter_grenze == "1") {
		$geb = " AND a.Geburtsjahr < ".($jahr - $gid[0]->alter);
		}
	if ($gid[0]->alter_grenze == "2") {
		$geb = " AND a.Geburtsjahr > ".($jahr - ( $gid[0]->alter + 1));
		}
	if ($gid[0]->geschlecht == 1) {
		$ges = " AND a.Geschlecht = 'W' ";
		}
	if ($gid[0]->geschlecht == 2) {
		$ges = " AND a.Geschlecht = 'M' ";
		}

	$query = " SELECT l.man_nr,l.Rang,a.sid,a.ZPS,a.Mgl_Nr,a.PKZ,a.DWZ,a.DWZ_Index,a.Geburtsjahr,a.Spielername"
		." FROM #__clm_dwz_spieler as a"
		." LEFT JOIN #__clm_rangliste_spieler as l ON l.Gruppe = $gid1 AND l.sid = $sid AND l.ZPS = '$zps' AND l.Mgl_Nr = a.Mgl_Nr "
		." WHERE a.ZPS = '$zps'"
		." AND a.sid =".$sid
		.$geb.$ges
		." ORDER BY IFNULL(l.man_nr,999) ASC,IFNULL(l.Rang,999) ASC,a.DWZ DESC, a.DWZ_Index ASC, a.Spielername ASC "
		;
			}
	else {
		if ($val == 1) { $order = "IFNULL(l.snr,999), a.Spielername ASC"; }
		else { $order = "IFNULL(l.snr,999), a.DWZ DESC";}
		$query = "SELECT a.zps, a.sg_zps, a.liga "
			." FROM #__clm_mannschaften as a"
			." WHERE a.sid = $sid AND a.zps = '$zps' AND a.man_nr = $man AND a.published = 1 "
			;
			$db->setQuery($query);
			$team = $db->loadObjectList();
			$liga = $team[0]->liga;
		if ($countryversion =="de") {
			$query = "SELECT a.Spielername as name, CONCAT(a.zps,a.Mgl_Nr) as id, a.zps, a.Mgl_Nr, a.PKZ, a.DWZ as dwz, 0 as checked_out, IFNULL(l.snr,999) as snr "; 
		} else {
			$query = "SELECT a.Spielername as name, CONCAT(a.zps,a.PKZ) as id, a.zps, a.Mgl_Nr, a.PKZ, a.DWZ as dwz, 0 as checked_out, IFNULL(l.snr,999) as snr "; 
		}
		$query .= " FROM #__clm_dwz_spieler as a "
			." LEFT JOIN #__clm_meldeliste_spieler as l ON l.lid = $liga AND l.sid = $sid AND l.mnr = $man "
			." AND (l.zps = '$zps' OR FIND_IN_SET(l.zps,'".$team[0]->sg_zps."') != 0 ) ";
		if ($countryversion =="de") {
			$query .= "AND l.mgl_nr = a.Mgl_Nr  ";
		} else {
			$query .= "AND l.PKZ = a.PKZ  ";
		}
		if ($team[0]->sg_zps > '0') {
			$query .= " WHERE (a.zps = '$zps' OR FIND_IN_SET(a.zps,'".$team[0]->sg_zps."') != 0 )"; }
		else {
			$query .= " WHERE a.zps = '$zps' "; }
		$query  .= " AND a.sid = ".$sid              
			." ORDER BY $order ";
		}
	return $query;
	}
	function getCLMSpieler( $options=array() )
	{
		$query	= $this->_getCLMSpieler( $options );
		$result = $this->_getList( $query );
//echo "<br>result: "; var_dump($result);
//echo "<br>query00: ".$query; 
//echo "<br>error: ".mysql_errno() . ": " . mysql_error(). "\n";
	return @$result;
	}

	function _getCLMCount( &$options )
	{
	$zps = JRequest::getVar('zps');

		// TODO: Cache on the fingerprint of the arguments
		$db	= JFactory::getDBO();
		$id	= @$options['id'];
		$sid	= JRequest::getInt('saison','1');
		$zps	= clm_escape(JRequest::getVar('zps'));
		$man	= JRequest::getInt('man','1');
		$query = "SELECT a.zps, a.sg_zps "
			." FROM #__clm_mannschaften as a"
			." WHERE a.sid = $sid AND a.zps = '$zps' AND a.man_nr = $man AND a.published = 1 "
			;
		$db->setQuery($query);
		$team = $db->loadObjectList();
		if (count($team) > 0) $team_sg_zps = $team[0]->sg_zps;
		else $team_sg_zps = '';
		$query = "SELECT COUNT(ZPS) as zps " 
			." FROM #__clm_dwz_spieler "
			." WHERE (zps = '$zps' OR FIND_IN_SET(zps,'".$team_sg_zps."') != 0 )"
			;
	return $query;
	}
	function getCLMCount( $options=array() )
	{
		$query	= $this->_getCLMCount( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	// Prüfen ob Meldeliste schon abgegeben wurde
	function _getCLMAccess ( &$options )
	{
	$sid	= JRequest::getInt('saison','1');
	$zps	= clm_escape(JRequest::getVar('zps'));
	$man	= JRequest::getInt('man','1');
	$layout	= clm_escape(JRequest::getVar('layout'));
	$gid	= JRequest::getInt('gid');

		$db	= JFactory::getDBO();
		$id	= @$options['id'];
	if($layout =="rangliste"){
	$query	= "SELECT COUNT(snr) as snr "
		." FROM #__clm_meldeliste_spieler as a"
		." WHERE a.sid = $sid AND a.zps = '$zps' AND a.mnr = $man "
		;
				}
	else {
	$query	= "SELECT COUNT(snr) as snr "
		." FROM #__clm_meldeliste_spieler as a"
		." WHERE a.sid = $sid AND a.zps = '$zps' AND a.mnr = $man "
		;
		}
	return $query;
	}

	function getCLMAccess ( $options=array() )
	{
		$query	= $this->_getCLMAccess( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function _getCLMAbgabe ( &$options )
	{
	$sid	= JRequest::getInt('saison','1');
	$zps	= clm_escape(JRequest::getVar('zps'));
	$man	= JRequest::getInt('man','1');
	$layout	= clm_escape(JRequest::getVar('layout'));
	$gid	= JRequest::getInt('gid','1');

		$db	= JFactory::getDBO();
		$id	= @$options['id'];

	if($layout =="rangliste"){
	$query	= "SELECT id "
		." FROM #__clm_rangliste_id "
		." WHERE sid = $sid AND zps = '$zps' AND gid = $gid "
		;
				}
	else {
	$query	= "SELECT m.id, m.liste, l.params as params, m.sg_zps "
		." FROM #__clm_mannschaften as m"
		." LEFT JOIN #__clm_liga as l ON l.id = m.liga AND l.sid = m.sid  "
		." WHERE m.sid = $sid AND m.zps = '$zps' AND m.man_nr = $man "
		;
		}
	return $query;
	}

	function getCLMAbgabe ( $options=array() )
	{
		$query	= $this->_getCLMAbgabe( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	// Prüfen ob User berechtigt ist zu melden
	function _getCLMClmuser ( &$options )
	{
	$user	= JFactory::getUser();
	$jid	= $user->get('id');
	$sid	= JRequest::getInt('saison','1');


		$db	= JFactory::getDBO();
		$id	= @$options['id'];

	$query	= "SELECT zps,published "
		." FROM #__clm_user "
		." WHERE jid = $jid "
		." AND sid = $sid "
		;
	return $query;
	}

	function getCLMClmuser ( $options=array() )
	{
		$query	= $this->_getCLMClmuser( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	public static function Sortierung ( $cids ) {

	$zps 	= clm_escape(JRequest::getVar('zps'));
	$man	= JRequest::getInt('man','1');
	$sid	= JRequest::getInt('saison','1');      
	//CLM parameter auslesen
	$config = clm_core::$db->config();
	$countryversion = $config->countryversion;

	$db	= JFactory::getDBO();
	$query = "SELECT a.zps, a.sg_zps, a.liga "
		." FROM #__clm_mannschaften as a"
		." WHERE a.sid = $sid AND a.zps = '$zps' AND a.man_nr = $man AND a.published = 1 "
		;
	$db->setQuery($query);
	$team = $db->loadObjectList();
	$liga = $team[0]->liga;

	if ($countryversion =="de") {
		$query = "SELECT a.Spielername as name, CONCAT(a.zps,a.Mgl_Nr) as id, a.zps, a.Mgl_Nr, a.PKZ, a.DWZ as dwz, IFNULL(l.snr,999) as snr "; 
	} else {
		$query = "SELECT a.Spielername as name, CONCAT(a.zps,a.PKZ) as id, a.zps, a.Mgl_Nr, a.PKZ, a.DWZ as dwz, IFNULL(l.snr,999) as snr "; 
	}
	$query .= " ,v.Vereinname "
		." FROM #__clm_dwz_spieler as a "
		." LEFT JOIN #__clm_dwz_vereine as v ON v.ZPS = a.zps AND v.sid = a.sid"
		." LEFT JOIN #__clm_meldeliste_spieler as l ON l.lid = $liga AND l.sid = $sid AND l.mnr = $man "
				." AND (l.zps = '$zps' OR FIND_IN_SET(l.zps,'".$team[0]->sg_zps."') != 0 ) ";
		if ($countryversion =="de") {
			$query .= " AND l.mgl_nr = a.Mgl_Nr  ";
		} else {
			$query .= " AND l.PKZ = a.PKZ  ";
		}
		if ($team[0]->sg_zps > '0') {
			$query .= " WHERE (a.zps = '$zps' OR FIND_IN_SET(a.zps,'".$team[0]->sg_zps."') != 0 ) "; }
		else {
			$query .= " WHERE a.zps = '$zps' "; }
		$query  .= " AND a.sid = ".$sid;              
		if ($countryversion =="de") {
			$query .= " AND CONCAT(a.zps,a.Mgl_Nr) IN ($cids) ";
		} else {
			$query .= " AND CONCAT(a.zps,a.PKZ) IN ($cids) ";
		}
		$query .= " ORDER BY IFNULL(l.snr,999), a.DWZ DESC, a.Spielername ASC ";
		;
	$db->setQuery( $query );
	$sort = $db->loadObjectList();

	return $sort;
	}

	// mögliche Mannschaftsleiter
	function _getCLMML ( &$options )
	{
	$sid	= JRequest::getInt('saison','1');
	$lid	= JRequest::getInt('lid','1');
	$zps 	= clm_escape(JRequest::getVar('zps'));
	$man	= JRequest::getInt('man','1');
	$db	= JFactory::getDBO();
	
	$query = "SELECT a.zps, a.sg_zps " 
			." FROM #__clm_mannschaften as a"
			." WHERE a.sid = $sid AND a.zps = '$zps' "
			." AND a.liga = $lid AND a.man_nr = $man AND a.published = 1 "
			;
	$db->setQuery( $query );
	$team = $db->loadObjectList();
	if (count($team) > 0) $team_sg_zps = $team[0]->sg_zps;
	else $team_sg_zps = '';

	$query	= "SELECT a.jid as mf, a.name as mfname "
		." FROM #__clm_user as a"
		." WHERE (zps = '".$zps."' OR FIND_IN_SET(zps,'".$team_sg_zps."' ) != 0 )"
		."   AND sid = ".$sid
		."   AND published = 1"
		;
	return $query;
	}

	function getCLMML ( $options=array() )
	{
		$query	= $this->_getCLMML( $options );
		$result = $this->_getList( $query );

		return @$result;
	}

}
?>
