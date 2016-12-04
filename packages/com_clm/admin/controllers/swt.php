<?php

/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2016 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMControllerSWT extends JControllerLegacy
{
	function __construct() {		
		parent::__construct();		
	}
	
	function display($cachable = false, $urlparams = array()) { 
		JRequest::setVar('view','swt');
		parent::display(); 
	} 
	
	function upload() {
		$model = $this->getModel('swt');
		$msg = $model->upload();
		$filename = JRequest::getVar('filename', '');
		
		$adminLink = new AdminLink();
		$adminLink->more = array('filename' => $filename);
		$adminLink->view = "swt";
		$adminLink->makeURL();
			
		$this->setRedirect($adminLink->url,$msg); 		
	}
	
	function delete(){
		$model = $this->getModel('swt');
		$msg = $model->delete();
		
		$adminLink = new AdminLink();
		$adminLink->view = "swt";
		$adminLink->makeURL();
			
		$this->setRedirect($adminLink->url,$msg);
	}
	
	function import() {
		$model = $this->getModel('swt');
		$type = $model->import();
		
		if($type == 0) {
			JRequest::setVar('view', 'swtturnier');
			parent::display();
		} elseif($type == 255){
			JRequest::setVar('view', 'swtliga');
			parent::display();
		} else {
			$adminLink = new AdminLink();
			$adminLink->view = "swt";
			$adminLink->makeURL();
			
			$msg = JText::_( 'SWT_FILE_ERROR' ); 
			
			$this->setRedirect($adminLink->url,$msg);
		}				
	}
}
?>