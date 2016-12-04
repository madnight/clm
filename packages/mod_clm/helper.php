<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

class modCLMHelper {
	
	public static function getLink(&$params) {
		$db	= JFactory::getDBO();
		$par_mt_type = $params->def('mt_type', 0);
		// einzelne IDs gegeben?
		$par_ids = $params->def('ids', '');
		if ($par_ids != '') {
			// String zerlegen
			$array_ids = explode(",", $par_ids);
			if (count($array_ids) > 0) {
				$sqlIDs = " AND (";
				$counter = 0; // zählt tatsächliche abgefragte IDs
				foreach ($array_ids as $value) {
					settype($value, "int");
					// check auf Zahl
					if (is_int($value) AND $value > 0) {
						if ($counter > 0) {
							$sqlIDs .= " OR ";
						}
						$sqlIDs .= "a.id = ".$value;
						$counter++;
					}
				}
				$sqlIDs .= ")";
			}
			// falls doch ekien IDs eingetragen wurden
			if ($counter == 0) {
				$sqlIDs = "";
			}
		} else {
			$sqlIDs = "";
		}
		
	
		$query = "SELECT  a.sid, a.id, a.name, a.runden, a.durchgang, a.rang, a.runden_modus, a.liga_mt "
			."\n FROM #__clm_liga as a"
			."\n LEFT JOIN #__clm_saison as s ON s.id = a.sid "
			."\n WHERE a.published = 1"
			.($par_mt_type < 2 ? "\n AND a.liga_mt = ".$par_mt_type : "")
			."\n AND s.published = 1"
			."\n AND s.archiv  != 1".$sqlIDs
			."\n ORDER BY a.sid DESC,a.ordering ASC, a.id ASC "
			;
		$db->setQuery( $query );
		$link = $db->loadObjectList();;
	
		return $link;
	}

	public static function getCount(&$params) {
		$par_mt_type = $params->def('mt_type', 0);
		$db	= JFactory::getDBO();
		$query = "SELECT COUNT(a.id) as id "
			."\n FROM #__clm_liga as a"
			."\n LEFT JOIN #__clm_saison as s ON s.id = a.sid "
			."\n WHERE a.published = 1"
			.($par_mt_type < 2 ? "\n AND a.liga_mt = ".$par_mt_type : "")
			."\n AND s.archiv  != 1"
			;
		$db->setQuery( $query );
		$count = $db->loadObjectList();;
	
		return $count;
	}

	public static function getRunde(&$params) {
		$liga	= JRequest::getVar( 'liga', 1);
		$db	= JFactory::getDBO();
	
		$query = " SELECT  a.* "
			." FROM #__clm_runden_termine as a"
			." LEFT JOIN #__clm_saison as s ON s.id = a.sid "
			." WHERE a.liga =".$liga
			." AND s.published = 1"
			." AND s.archiv  != 1"
			." ORDER BY a.nr ASC"
			;
		$db->setQuery( $query );
		$runden = $db->loadObjectList();;
	
		return $runden;
	}

}
