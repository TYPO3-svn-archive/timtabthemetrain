<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2005 Ingo Renner (typo3@ingo-renner.com)
*  All rights reserved
*
*  This script is part of the Typo3 project. The Typo3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * class.tx_timtabthemetrain_fe.php
 *
 * Class to localize strings and do custom theme stuff
 *
 * $Id$
 *
 * @author Ingo Renner <typo3@ingo-renner.com>
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 */

#$PATH_timtab = t3lib_extMgm::extPath('timtab');
require_once(PATH_tslib.'class.tslib_pibase.php');

class tx_timtabthemetrain_fe extends tslib_pibase {
	var $cObj; // The backReference to the mother cObj object set at call time
	// Default plugin variables:
	var $prefixId 		= 'tx_timtabthemetrain_fe';		// Same as class name
	var $scriptRelPath 	= 'class.tx_timtabthemetrain_fe.php';	// Path to this script relative to the extension dir.
	var $extKey 		= 'timtab_theme_train';	// The extension key.

	var $conf;
	var $markerArray;

	/**
	 * main function which executes all steps
	 *
	 * @param	array		an array of markers coming from tt_news
	 * @param	array		the configuration coming from tt_news
	 * @return	array		modified marker array
	 */
	function main($markerArray, $conf) {		
		$this->init($markerArray, $conf);
		$this->substituteMarkers();

		return $this->markerArray;
	}

	/**
	 * initializes the configuration for the extension
	 *
	 * @param	array		an array of markers coming from tt_news
	 * @param	array		the configuration coming from tt_news
	 * @return	void
	 */
	function init($markerArray, $conf) {
		$this->cObj = t3lib_div::makeInstance('tslib_cObj'); // local cObj.
		$this->pi_loadLL(); // Loading language-labels
		
		$this->conf['allowCaching'] = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tt_news.']['allowCaching'];
		$this->conf['blogPid'] = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_timtab.']['blogPid'];
		
		$this->markerArray = $markerArray;
	}

	/**
	 * substitutes markers like count of comments
	 *
	 * @return	void
	 */
	function substituteMarkers() {
		if($this->calledBy == 'tt_news') {	
			$this->markerArray['###TRAIN_POST_ID###'] = $this->conf['data']['uid'];
			$this->markerArray['###TRAIN_IN###'] = $this->pi_getLL('posted_in');
			$this->markerArray['###TRAIN_BY###'] = sprintf($this->pi_getLL('by'), $this->conf['data']['author']);
			$this->markerArray['###TRAIN_COMMENT_LINK###'] = $this->getCommentLink($this->conf['data']);		
		} elseif($this->calledBy == 've_guestbook') {		
			$this->markerArray['###TRAIN_COMMENTS###'] = $this->pi_getLL('comments');
			$this->markerArray['###TRAIN_COMMENT_BY###'] = $this->pi_getLL('comment_by');
		}
	}
	
	function getLabel($content, $conf) {
		$this->pi_loadLL();

		return $this->pi_getLL(trim($conf['label']));
	}

	/**
	 * builds a link to a given post
	 * 
	 * @param	array		the post data
	 * @return	string		the post link
	 */
	function getCommentLink($tt_news) {
		$urlParams = array(
			'tx_ttnews[year]'    => date('Y', $tt_news['datetime']),
			'tx_ttnews[month]'   => date('m', $tt_news['datetime']),
			'tx_ttnews[day]'     => date('d', $tt_news['datetime']),
			'tx_ttnews[tt_news]' => $tt_news['uid']
		);

		$conf = array(
			'useCacheHash'     => $this->conf['allowCaching'],
			'no_cache'         => !$this->conf['allowCaching'],
			'parameter'        => $this->conf['blogPid'],
			'additionalParams' => $this->conf['parent.']['addParams'].t3lib_div::implodeArrayForUrl('',$urlParams,'',1).$this->pi_moreParams,
			'ATagParams'       => ' class="commentslink"',
			'section'          => 'comments'
		);
		
		if(empty($this->markerArray['###BLOG_COMMENTS_COUNT###'])) {
			$this->markerArray['###BLOG_COMMENTS_COUNT###'] = '0';	
		}
		
		$link = $this->pi_getLL('comments')
				.' ('.$this->markerArray['###BLOG_COMMENTS_COUNT###'].')';

		return $this->cObj->typoLink($link, $conf);
	}
	
	
	
	
	
	
	/***********************************************
	 *
	 * Hook Connector
	 *
	 **********************************************/

	/**
	 * connects into tt_news and ve_guestbook item marker processing hook
	 * and fills our markers
	 *
	 * @param	array		an array of markers coming from tt_news
	 * @param	array		the current tt_news record
	 * @param	array		the configuration coming from tt_news
	 * @param	object		the parent object calling this method
	 * @return	array		processed marker array
	 */
	function extraItemMarkerProcessor($markerArray, $row, $lConf, &$pObj) {
		$this->conf['data'] = $row;
		$this->pObj = &$pObj;
		$this->calledBy = $pObj->extKey; //who is calling? 

		return $this->main($markerArray, $lConf);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/timtab_theme_train/class.tx_timtabthemetrain_fe.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/timtab_theme_train/class.tx_timtabthemetrain_fe.php']);
}

?>