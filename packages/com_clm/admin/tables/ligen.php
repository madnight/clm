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

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class TableCLMLigen extends JTable
{
	var $id			= null;
	var $name		= '';
	var $sid		= '';
	var $teil		= '';
	var $stamm		= '';
	var $ersatz		= '';
	var $rang		= '';
	var $sl			= '';
	var $runden		= '';
	var $durchgang		= '';
	var $heim		= '';
	var $sieg_bed		= '';
	var $runden_modus	= '';
	var $man_sieg		= '';
	var $man_remis		= '';
	var $man_nieder		= '';
	var $man_antritt	= '';
	var $sieg		= '';
	var $remis		= '';
	var $nieder		= '';
	var $antritt		= '';
	var $mail		= '';
	var $sl_mail		= '';
	var $order		= '';
	var $rnd		= '';
	var $ab			= '';
	var $ab_evtl		= '';
	var $auf		= '';
	var $auf_evtl		= '';
	var $published		= '';
	var $bemerkungen	= '';
	var $bem_int		= '';
	var $checked_out	= 0;
	var $checked_out_time	= 0;
	var $ordering		= null;
	var $b_wertung		= '';
	var $liga_mt		= '';
	var $tiebr1		= 0;
	var $tiebr2		= 0;
	var $tiebr3		= 0;
	var $ersatz_regel	= '';
	var $anzeige_ma	= '';
	var $params 		= null;
	
	function __construct( &$_db ) {
		parent::__construct( '#__clm_liga', 'id', $_db );
	}

	/**
	 * Overloaded check function
	 */
}
