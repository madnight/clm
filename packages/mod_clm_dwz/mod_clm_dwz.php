<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

// angemeldet
require_once (dirname(__FILE__).DS.'helper.php');
require_once (dirname(__FILE__).DS.'escape_dwz.php');

$vereine = modCLM_DWZHelper::getVereine($params);

require(JModuleHelper::getLayoutPath('mod_clm_dwz'));