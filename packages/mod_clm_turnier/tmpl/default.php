<?php // no direct access
defined('_JEXEC') or die('Restricted access'); 
jimport( 'joomla.html.parameter' );

// Parameter

// aktueller View 
// $view	= JRequest::getVar( 'view' );
$view = JFactory::getApplication()->input->get('view');

// aktuell angezeigtes/ausgewähltes Turnier
// $turnierid = JRequest::getInt('turnier', 0);
$turnierid = JFactory::getApplication()->input->get('turnier', 0);

// item
// $itemid	= JRequest::getVar( 'Itemid' );
// $itemid = JFactory::getApplication()->input->get('Itemid');


$rankingscoreorderby = $params->get('rankingscoreorderby', 1);
if ($rankingscoreorderby == 1) {
	$str_rsorderby = "orderby=snr";
} else {
	$str_rsorderby = "orderby=pos";
}
 

// Anzeige/Menu beginnen
?>
<ul class="menu">

<?php
// alle Turniere mit Turniernamen durchgehen
foreach ($turData as $value) {

	// Turniername
	// Link je nach ParameterEinstellung setzen
	switch($params->get('namelinksto', 0)) {
		case 3: // Paarungstafel
			echo modCLM_TurnierHelper::makeLink('turnier_rangliste', $value->id, array($str_rsorderby), $value->name, $view, $turnierid, $param['itemid']);
			break;
		case 2: // alle Partien
			echo modCLM_TurnierHelper::makeLink('turnier_paarungsliste', $value->id, array(), $value->name, $view, $turnierid, $param['itemid']);
			break;
		case 1: // Runde 1
			// TODO: Rundenname
			echo modCLM_TurnierHelper::makeLink('turnier_runde', $value->id, array("runde=1"), $value->name, $view, $turnierid, $param['itemid']);
			break;
		case 0: // TlnLIste
			echo modCLM_TurnierHelper::makeLink('turnier_teilnehmer', $value->id, array(), $value->name, $view, $turnierid, $param['itemid']);
			break;
		case 5: // Ausschreibung
			echo modCLM_TurnierHelper::makeLink('turnier_invitation', $value->id, array(), $value->name, $view, $turnierid, $param['itemid']);
			break;
		case 6: // Tabelle
			echo modCLM_TurnierHelper::makeLink('turnier_tabelle', $value->id, array(), $value->name, $view, $turnierid, $param['itemid']);
			break;
		case 4: // Turnier-Info
		default:
			echo modCLM_TurnierHelper::makeLink('turnier_info', $value->id, array(), $value->name, $view, $turnierid, $param['itemid']);
			break;
	}
	
	// aktiviertes Turnier aufklappen!
	if ($value->id == $turnierid) {
		
		// veröffentlicht
		if ($value->published == 1) {
		
		
			echo '<ul>';
				
				// Teilnehmerliste
				if ($param['linkplayerslist'] == 1) {
					echo modCLM_TurnierHelper::makeLink('turnier_teilnehmer', $value->id, array(), JText::_('PLAYERSLIST'), $view, $turnierid, $param['itemid']);
				}
				
				// Runden
				if ($param['linkroundseach'] == 1) {
					// alle ausgelesenen Runden durchgehen
					foreach ($turRounds[$value->id] as $round) {
						echo modCLM_TurnierHelper::makeLink('turnier_runde', $value->id, array("runde=".$round->nr,"dg=".$round->dg), $round->name, $view, $turnierid, $param['itemid']);
					}
				}
				
				// alle Matches
				if ($param['linkmatchescomplete'] == 1) {
					echo modCLM_TurnierHelper::makeLink('turnier_paarungsliste', $value->id, array(), JText::_('MATCHESCOMPLETE'), $view, $turnierid, $param['itemid']);
				}
				
				// Tabelle
				if ($param['linktable'] == 1 AND $value->typ != 3) {
					echo modCLM_TurnierHelper::makeLink('turnier_tabelle', $value->id, array(), JText::_('TABLE'), $view, $turnierid, $param['itemid']);
				}
				
				
				// Fortschritt/Paarungstafel
				if ($param['linkrankingscore'] == 1 AND $value->typ != 3) {
					if (strlen($value->stringRankingScore) > 0) {
						$text = $value->stringRankingScore;
					} elseif ($value->typ == 1) {
						$text = JText::_('RANKING');
					} elseif ($value->typ == 2) {
						$text = JText::_('SCOREBOARD');
					}
					echo modCLM_TurnierHelper::makeLink('turnier_rangliste', $value->id, array($str_rsorderby), $text, $view, $turnierid, $param['itemid']);
				}
		
				// Inof. DWZ
				if ($param['linkdwz'] == 1) {

					$table = clm_core::$db->turniere->get($turnierid);
					if(!$table->isNew()) {
						$parameter = new clm_class_params($table->params);
						if($parameter->get("inofDWZ","0") == 1) {
							echo modCLM_TurnierHelper::makeLink('turnier_dwz', $value->id, array(), JText::_('DWZ'), $view, $turnierid, $param['itemid']);
						}
					}
				}

				// Ausschreibung
				if ($param['linkinvitation'] == 1 AND $value->invitationLength > 0) {
					echo modCLM_TurnierHelper::makeLink('turnier_invitation', $value->id, array($str_rsorderby), JText::_('INVITATION'), $view, $turnierid, $param['itemid']);
				}
				
				// Bemerkungen
				if ($param['shownotes'] == 1 AND strlen($value->bemerkungen) > 0) {
					echo '<li><span>'.nl2br(JFilterOutput::cleantext($value->bemerkungen)).'</span></li>';
				}
				
			echo '</ul>';
	
	
		} else { // nicht veröffentlicht
			echo '<ul><li><span>'.JText::_('UNPUBLISHED').'</span></li></ul>';
		}
	
	}
	
	echo '</li>';
	// schließt dieses Turnier ab
	
}
// alle Turniere abgeschlossen


// ContentID gesetzt, Artikel gefunden
if ($param['contentid'] > 0 AND isset($contentTitle)) {
	$contenttext = $params->get('contenttext', '');
	if ($contenttext != '') {
		$textToUse = $contenttext;
	} else {
		$textToUse = $contentTitle;
	}
	echo '<li><span><a href="index.php?option=com_content&view=article&id='.$param['contentid'].'">'.$textToUse.'</a></span></li>';
	
}

// Text unterhalb
if ($param['textbottom'] != '') {
	echo '<li><span>'.nl2br(JFilterOutput::cleantext($param['textbottom'])).'</span></li>';
}


?>


</ul>
