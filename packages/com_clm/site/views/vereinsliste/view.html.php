<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2015 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

jimport( 'joomla.application.component.view');

class CLMViewVereinsliste extends JViewLegacy
{
	function display($tpl = null) {
	
		$model	  = $this->getModel();
		$vereinsliste     = $model->getCLMVereinsliste();
		$this->assignRef('vereinsliste'  , $vereinsliste);
		
		$model	  = $this->getModel();
		$vereine     = $model->getCLMVereine();
		$this->assignRef('vereine'  , $vereine);
		
		$model	  = $this->getModel();
		$verband     = $model->getCLMVerband();
		$this->assignRef('verband'  , $verband);
		
		$model	  = $this->getModel();
		$saisons     = $model->getCLMSaisons();
		$this->assignRef('saisons'  , $saisons);
		
		$model		= $this->getModel();
		
		$mainframe = JFactory::getApplication();
		$document =JFactory::getDocument();
		$document->setTitle($mainframe->getCfg('sitename')." - ".JText::_('CLUBS_LIST'));
		
		$document->addScript('http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js');
		$document->addScript(JURI::base().'components/com_clm/javascript/updateTableHeaders.js');
		
		/* Call the state object */
//		$state = $this->get( 'state' );
		$mainframe =JFactory::getApplication();
		global $option;
		$lists['state'] = $mainframe->getUserStateFromRequest( "$option.filter_state",'filter_state','','word' );
 
		/* Get the values from the state object that were inserted in the model's construct function */
		$lists['order'] = $mainframe->getUserStateFromRequest( "$option.filter_order", 'filter_order', 'name', 'cmd' ); // JRequest::getString('filter_order', 'a.id');
		$lists['order_Dir'] = $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",'filter_order_Dir','desc','word' );
		//$lists['order']     = $state->get( 'filter_order_vl' );
		//$lists['order_Dir'] = $state->get( 'filter_order_Dir_vl' );
 
		$this->assignRef( 'lists', $lists );
		
		parent::display($tpl);
	}	
}
?>
