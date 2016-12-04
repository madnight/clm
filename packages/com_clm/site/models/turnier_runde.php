<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined('_JEXEC') or die();
jimport('joomla.application.component.model');
jimport( 'joomla.html.parameter' );

class CLMModelTurnier_Runde extends JModelLegacy {


	function __construct() {
		
		parent::__construct();

		$this->turnierid = JRequest::getInt('turnier', 0);
		$this->runde = JRequest::getInt('runde', 1); // Nr der Runde, nicht id
		$this->dg = JRequest::getInt('dg', 1); 

		$this->_getTurnierData();

		$this->_getTurnierRound();
		
		$this->_getRoundMatches();
		$this->_getTurnierPoints();
	
	}


	function _getTurnierData() {
	
		$query = "SELECT *"
			." FROM #__clm_turniere"
			." WHERE id = ".$this->turnierid
			;
		$this->_db->setQuery( $query );
		$this->turnier = $this->_db->loadObject();

		// TO-DO: auslagern
		// zudem PGN-Parameter auswerten
		$turParams = new clm_class_params($this->turnier->params);
		$pgnInput = $turParams->get('pgnInput', 1);
		$pgnPublic = $turParams->get('pgnPublic', 1);
		
		// User ermitteln
		$user =JFactory::getUser();
		
		// Flag für View und Template setzen: pgnShow
		// FALSE - PGN nicht verlinken/anzeigen
		// TRUE - PGN-Links setzen und anzeigen 
		// 'pgnInput möglich' UND ('pgn öffentlich' ORDER 'User eingeloggt')
		if ($pgnInput == 1 AND ($pgnPublic == 1 OR $user->id > 0) ) {
			$this->pgnShow = TRUE;
		} else {
			$this->pgnShow = FALSE;
		}

		$this->displayTlOK = $turParams->get('displayTlOK', 0);

		// turniernamen anpassen?
		$addCatToName = $turParams->get('addCatToName', 0);
		if ($addCatToName != 0 AND ($this->turnier->catidAlltime > 0 OR $this->turnier->catidEdition > 0)) {
			$this->turnier->name = CLMText::addCatToName($addCatToName, $this->turnier->name, $this->turnier->catidAlltime, $this->turnier->catidEdition);
		}

	}

	function _getTurnierRound() {
	
		$query = "SELECT *"
			." FROM #__clm_turniere_rnd_termine"
			." WHERE turnier = ".$this->turnierid." AND dg = ".$this->dg." AND nr = ".$this->runde
			;
		$this->_db->setQuery( $query );
		$this->round = $this->_db->loadObject();
	
	}
	
	function _getRoundMatches() {
	
		$query = " SELECT a.*, "
			." t.name as wname, t.twz as wtwz, t.verein as wverein, t.start_dwz as wdwz, t.FIDEelo as welo, "
			." u.name as sname, u.twz as stwz, u.verein as sverein, u.start_dwz as sdwz, u.FIDEelo as selo "
			." FROM #__clm_turniere_rnd_spl as a"
			." LEFT JOIN #__clm_turniere_tlnr as t ON t.snr = a.spieler AND t.turnier = a.turnier "
			." LEFT JOIN #__clm_turniere_tlnr as u ON u.snr = a.gegner AND u.turnier = a.turnier "
			." WHERE a.turnier = ".$this->turnierid
			." AND a.runde = ".$this->runde." AND a.dg = ".$this->dg
			." AND a.heim = 1 "
			." ORDER BY a.brett ASC "
			;

		$this->_db->setQuery( $query );
		$this->matches = $this->_db->loadObjectList();
	
	}

	function _getTurnierPoints() {
	
		$this->points = array();
		// alle ermittelten Runden duirchgehen
		$query = "SELECT spieler, ergebnis "
				." FROM #__clm_turniere_rnd_spl"
				." WHERE turnier = ".$this->turnierid
				." AND ( dg < ".$this->dg." OR ( dg = ".$this->dg." AND runde < ".$this->runde." ) )"
				." ORDER BY dg ASC, runde ASC, brett ASC"
				;
		$this->_db->setQuery( $query );
		$this->round_points = $this->_db->loadObjectList();
		foreach ($this->round_points as $pvalue) {
			if ($pvalue->ergebnis == 1 OR $pvalue->ergebnis == 5) $point = 1;
			elseif ($pvalue->ergebnis == 2) $point = .5;
			else $point = 0;
			if (isset($this->points[$pvalue->spieler]))  $this->points[$pvalue->spieler] += $point;
			else $this->points[$pvalue->spieler] = $point;
		}

	}

}
?>
