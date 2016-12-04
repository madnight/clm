<?php
function clm_function_zps_to_district($zps) {
  //CLM parameter auslesen
  $config = clm_core::$db->config();
  $countryversion = $config->countryversion;
	if ($countryversion =="de") {
	  if (strlen($zps) == 5) {
		$query = 'SELECT name as district FROM #__clm_vereine WHERE ZPS = "' . clm_core::$db->escape($zps) . '" AND sid ='.clm_core::$access->getSeason();
		$result = clm_core::$db->loadAssocList($query);
		if(count($result)==1) {
			return $result[0]["district"];
		} else {
			return clm_core::$db->dwz_vereine->get($zps)->Vereinname;
		}
	  }
	  else if (strlen($zps) == 3) {
		return clm_core::$db->dwz_verbaende->get($zps)->Verbandname;
	  } else {
		return "";	
	  }
	}  
	if ($countryversion =="en") {
	  if (strlen($zps) == 4) {
		$query = 'SELECT name as district FROM #__clm_vereine WHERE ZPS = "' . clm_core::$db->escape($zps) . '" AND sid ='.clm_core::$access->getSeason();
		$result = clm_core::$db->loadAssocList($query);
		if(count($result)==1) {
			return $result[0]["district"];
		} else {
			return clm_core::$db->dwz_vereine->get($zps)->Vereinname;
		}
	  }
	  else if (strlen($zps) == 1) {
		return clm_core::$db->dwz_verbaende->get($zps)->Verbandname;
	  } else {
		return "";	
	  }
	} 
}
?>