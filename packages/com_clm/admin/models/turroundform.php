<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

class CLMModelTurRoundForm extends JModelLegacy {

	var $_pagination = null;
	var $_total = null;


	// benötigt für Pagination
	function __construct() {
		
		parent::__construct();


		// user
		$this->user =JFactory::getUser();
		
		// get parameters
		$this->_getParameters();

		// get turnier
		$this->_getTurnierData();

		// get Round
		$this->_getRoundData();
		

	}


	// alle vorhandenen Parameter auslesen
	function _getParameters() {
	
		// turnierid
		$this->param['turnierid'] = JRequest::getInt('turnierid');
		
		// roundid
		$this->param['roundid'] = JRequest::getInt('roundid');
	
	}


	function _getTurnierData() {
	
		$query = 'SELECT name'
			. ' FROM #__clm_turniere'
			. ' WHERE id = '.$this->param['turnierid']
			;
		$this->_db->setQuery($query);
		$this->turnierData = $this->_db->loadObject();
	
	}


	function _getRoundData() {
	
		$query = 'SELECT *'
			. ' FROM #__clm_turniere_rnd_termine'
			. ' WHERE turnier = '.$this->param['turnierid'].' AND id = '.$this->param['roundid']
			;
		$this->_db->setQuery($query);
		$this->roundData = $this->_db->loadObject();
	
	}



}

?>