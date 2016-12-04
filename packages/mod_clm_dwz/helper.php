<?php

// no direct access
defined('_JEXEC') or die('Restricted access');


class modCLM_DWZHelper {
	public static function getVereine($params) {
		global $mainframe;
		$db = JFactory::getDBO();
		$zps = clm_escape_dwz($params->get('zps',''));
		$all = clm_escape_dwz($params->get('all_clubs',0));
		$view = clm_escape_dwz($params->get('view',0));
		
		if($all) {
			$query = "SELECT zps,name,sid FROM #__clm_vereine WHERE sid=(SELECT id from #__clm_saison where published=1 and archiv=0) ORDER BY name ASC";
      } else {
      	$zps = explode(",",$zps);
			$query = "SELECT zps,name,sid FROM #__clm_vereine WHERE zps in (";
			for($i=0; $i<count($zps);$i++) {
				if($i>0) {
					$query.=", ";				
				}
				$query.="'".$zps[$i]."'";
			}
         $query.= ") AND sid=(SELECT id from #__clm_saison where published=1 and archiv=0) ORDER BY name ASC";
      }
                                            
		$db->setQuery( $query );
		$vereine = $db->loadObjectList();

		if (count($vereine) == 0) {
			return array("Alle ZPS fehlerhaft");
		}

		$vliste = array ();
		foreach ($vereine as $verein) {
			$vliste[] = array($verein->sid,$verein->zps, $verein->name,($view==0 ? "dwz" : "verein"));
		}

		return $vliste;
	}
}