<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

// Konfiguration wird benötigt
require_once (JPATH_SITE . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_clm" . DIRECTORY_SEPARATOR . "clm" . DIRECTORY_SEPARATOR . "index.php");

// Include the syndicate functions only once
require_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'helper.php');


// INIT
$db  = JFactory::getDBO();


// Modul-Parameter
$param['categoryid'] = $params->get('categoryid', '');
$param['turnierid'] = $params->get('turnierid', array());

$param['linkplayerslist'] = $params->get('linkplayerslist', 1);
$param['linkroundseach'] = $params->get('linkroundseach', 0);
$param['showunpublishedrounds'] = $params->get('showunpublishedrounds', 0);
$param['linkmatchescomplete'] = $params->get('linkmatchescomplete', 0);
$param['linktable'] = $params->get('linktable', 0);
$param['linkrankingscore'] = $params->get('linkrankingscore', 0);
$param['textrankingscore'] = $params->get('textrankingscore', '');
$param['linkdwz'] = $params->get('linkdwz', 0);

$param['shownotes'] = $params->get('shownotes', 0);
$param['linkinvitation'] = $params->get('linkinvitation', 0);
$param['contentid'] = $params->get('contentid', '');
$param['textbottom'] = $params->get('textbottom', '');
$param['itemid'] = $params->get('itemid', '');

$arrayTurniere_object=array();
// CategoryID vorgegeben?
if ($param['categoryid'] != '' AND $param['categoryid'] > 0) {
	
	list($parentArray, $parentKeys, $parentChilds) = modCLM_TurnierHelper::getTree();
	
	// für jede Kategorie Unterkategorien ermitteln
	$arrayAllCatid = array();
	if (isset($parentChilds[$param['categoryid']])) {
		$arrayAllCatid = $parentChilds[$param['categoryid']];
		$arrayAllCatid[] = $param['categoryid'];
	} else {
		$arrayAllCatid[] = $param['categoryid'];
	}
	$addWhere = '( ( catidAlltime = '.implode( ' OR catidAlltime = ', $arrayAllCatid ).' )
						OR 
						( catidEdition = '.implode( ' OR catidEdition = ', $arrayAllCatid ).' ) )'; 
	
	// zugewiesene Turniere
	$query = 'SELECT id'
				. ' FROM #__clm_turniere'
				. ' WHERE '.$addWhere
				. ' AND published=1 ORDER BY ordering'
				;
	$db->setQuery($query);
	$arrayTurniere_object = $db->loadObjectList(); // loadResultArray funktioniert anscheinend nicht in 3.2
}


// Kategorieturniere und einzelne Turniere zusammenführen
$arrayTurniere = $param['turnierid'];

foreach($arrayTurniere_object as $array)
{
$arrayTurniere[]=$array->id;
}


// sollen Turniere ausgegeben werden?
if (count($arrayTurniere) > 0) {
	
	// doppelte ausscheiden
	$arrayTurniere = array_unique($arrayTurniere);	
	
	$arrayTextRankingScore = explode(',', $param['textrankingscore']);
	
	// INIT
	$counter = 0;
	$turData = array();
	$turRounds = array();
	
	// alle Turniere durchgehen
	foreach ($arrayTurniere as $key => $value) {
	
		// in Int umwandeln
		$value = intval($value);
	
		if ($value > 0) { // gültige ID?
		
			// Turnierdaten holen
			$query = "SELECT *, CHAR_LENGTH(invitationText) AS invitationLength"
					. " FROM #__clm_turniere"
					. " WHERE id = ".$value." AND published=1"
						;
			$db->setQuery($query);
			if ($temp = $db->loadObject()) {
				$turData[$value] = $temp;
			
				$counter++; // Turnier existent!
			
				// Link Scoreboard
				if (isset($arrayTextRankingScore[$key])) { // text gegeben?
					$turData[$value]->stringRankingScore = $arrayTextRankingScore[$key];
				} else {
					$turData[$value]->stringRankingScore = "";
				}
			
				// Runden
				if ($param['linkroundseach'] == 1) {
					// published/unpublished
					if ($param['showunpublishedrounds'] == 0) {
						$sqlPublished = " AND published = '1'";
					} else {
						$sqlPublished = "";
					}
					// Abfrage
					$query = "SELECT id, name, dg, nr, abgeschlossen, tl_ok, published"
							. " FROM #__clm_turniere_rnd_termine"
							. " WHERE turnier = ".$value.$sqlPublished
							. " ORDER BY ordering ASC, dg ASC, nr ASC"
							;
					$db->setQuery($query);
					$turRounds[$value] = $db->loadObjectList();
				}
				
			}
		
		}
	
	}
	
	// content
	if ($param['contentid'] > 0) {
		$query = "SELECT title"
				. " FROM #__content"
				. " WHERE id = ".$param['contentid']
				;
		$db->setQuery($query);
		$contentTitle = $db->loadResult();
	}
	
	if ($counter > 0) {
		// nur dann Anzeige
		require(JModuleHelper::getLayoutPath('mod_clm_turnier'));
	}


}
// ohne turnierID gar keine Anzeige des Moduls


?>

