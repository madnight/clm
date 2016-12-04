<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

class modCLM_TurnierHelper {
	
	public static function makeLink($gotoView, $gotoID, $param = array(), $name, $currentView, $currentID, $itemid) {
	
		// current?
		$flagActive = false;
		if ($gotoView == $currentView AND $gotoID == $currentID) {
			// bei turnier_runde muß die gewählte Runde ebenfalls geprüft werden
			if ($gotoView == 'turnier_runde') {
				// aktuell angezeigte/ausgewählte Runde
				$runde = JFactory::getApplication()->input->get('runde', 0);
				$dg = JFactory::getApplication()->input->get('dg', 0);
				if ($param[0] == 'runde='.$runde AND $param[1] == 'dg='.$dg) {
					$liTag = '<li id="current" class="active" >';
					$class = ' class="active_link"';
				} else {
					$liTag = '<li>';
					$class = '';
				}
			} else {
			// Standard für alle anderen Views
				$liTag = '<li id="current" class="active" >';
				$class = ' class="active_link"';
			}
		} else {
			$liTag = '<li>';
			$class = '';
		}
	
		// Komponente
		$url = 'index.php?option=com_clm';
		
		// View
		$url .= '&amp;view='.$gotoView;
	
		// Turnier
		$url .= '&amp;turnier='.$gotoID;
		
		// weitere Parameter
		if (count($param) > 0) {
			$url .= '&amp;'.implode("&amp;", $param);
		}
	
		// itemid
		if($itemid == '' || !is_numeric($itemid)) {
			$itemid = JFactory::getApplication()->input->get('Itemid'); // JRequest::getVar('Itemid');
		}
		$url .= '&amp;Itemid='.$itemid;
	
		// zusammensetzen
		$tag = $liTag.'<span><a href="'.JRoute::_($url).'"'.$class.'>'.$name.'</a></span>';
		
		return $tag;
	
	}


	public static function getTree() {
	
		// DB
		$_db				=  JFactory::getDBO();
	
		// alle Cats holen
		$query = "SELECT id, name, parentid FROM #__clm_categories";
		$_db->setQuery($query);
		$parentList = $_db->loadObjectList('id');
	
		// Array speichert alle Kategorien in der Tiefe ihrer Verschachtelung
		$parentArray = array();
	
		// Array speichert für alle Kategorien die spezielle einzelne parentID ab
		$parentID = array();
		
		// Array speichert für alle Kategorien die Keys aller vorhandenen Parents ab
		$parentKeys = array();
		
		// Array speichert für alle Kategorien die Childs ab
		$parentChilds = array();
		
		// aufheben für Bearbeitung in parentChilds
		$saved_parentList = $parentList;
		
		// erste Ebene der Parents
		$parentsExisting = array(); // enthält alle IDs von Parents, die bereits ermittelt wurden
		foreach ($parentList as $key => $value) {
			if (!$value->parentid OR $value->parentid == 0) {
				$parentArray[$key] = $value->name; // Name an ID binden
				$parentsExisting[] = $value->id; // ID als existierender Parent eintragen
				// Eintrag kann nun aus Liste gelöscht werden!
				unset($parentList[$key]);
				
			}
		}
	
		$continueLoop = 1; // Flag, ob Schleife weiterlaufen soll
	
		// noch Einträge vorhanden?
		WHILE (count($parentList) > 0 AND $continueLoop == 1) { 
			
			$continueLoop = 0; // abschalten - erst wieder anschalten, wenn Eintrag gefunden
			
			
			// weitere Ebenen
			foreach ($parentList as $key => $value) {
				
				// checken, ob ParentID in Array der bereits ermittelten Parents vorhanden
				if (in_array($value->parentid, $parentsExisting)) {
					
					$parentArray[$key] = $parentArray[$value->parentid].' > '.$value->name;
					
					// Parent
					$parentID[$key] = $value->parentid;
					
					// Key
					$parentKeys[$key] = array($value->parentid);
					// hatte Parent schon keys?
					if (isset($parentKeys[$value->parentid])) {
						$parentKeys[$key] = array_merge($parentKeys[$key], $parentKeys[$value->parentid]);
					}
					$parentsExisting[] = $value->id;
					
					// Eintrag kann nun aus Liste gelöscht werden!
					unset($parentList[$key]);
					
					$continueLoop = 1; // Flag, ob Schleife weiterlaufen soll
					
				}
			}
		
		}
	
	
		// alle Childs
		foreach ($saved_parentList as $key => $value) {
			// nur welche, die auch Kind sind, können Kindschaft den Parents anhängen
			if ($value->parentid > 0) {
				// allen Parents dieses Childs diesen Eintrag anhängen
				foreach ($parentKeys[$key] AS $pvalue) {
					$parentChilds[$pvalue][] = $key;
				}
			}
		}
	
		return array($parentArray, $parentKeys, $parentChilds);
	
	}


}
 
