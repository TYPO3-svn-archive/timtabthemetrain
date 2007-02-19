<?php
//
//	$Id$
//

if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

//get EXT path
$PATH_timtab_theme_train = t3lib_extMgm::extPath('timtab_theme_train');

if (TYPO3_MODE == 'FE')	{
	require_once($PATH_timtab_theme_train.'class.tx_timtabthemetrain_fe.php');
}

//registering for several hooks
$TYPO3_CONF_VARS['EXTCONF']['tt_news']['extraItemMarkerHook'][]      = 'tx_timtabthemetrain_fe';
$TYPO3_CONF_VARS['EXTCONF']['ve_guestbook']['extraItemMarkerHook'][] = 'tx_timtabthemetrain_fe';

?>