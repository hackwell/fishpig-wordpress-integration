<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Post_List_Pager extends Mage_Page_Block_Html_Pager 
{
	/**
	 * Construct the pager and set the limits
	 *
	 */
	protected function _construct()
	{
		parent::_construct();	
		
		$this->setPageVarName($this->helper('wordpress/router')->getPostPagerVar());

		$baseLimit = $this->helper('wordpress')->getWpOption('posts_per_page', 10);
		$currentLimit = $this->getRequest()->getParam('limit', $baseLimit);

		if ($currentLimit%$baseLimit !== 0) {
			$currentLimit = $baseLimit;
		}

		$this->setDefaultLimit($baseLimit);
		$this->setLimit($currentLimit);
		
		$this->setAvailableLimit(array(
			$baseLimit => $baseLimit,
			($baseLimit*2) => ($baseLimit*2),
			($baseLimit*3) => ($baseLimit*3),
		));
	}
	
	/**
	 * Return the URL for a certain page of the collection
	 *
	 * @return string
	 */
	public function getPagerUrl($params=array())
	{
		$limitVar = $this->getLimitVarName();
		$pageVar = $this->getPageVarName();

		if (isset($params[$limitVar]) && $params[$limitVar] == $this->getDefaultLimit()) {
			$params[$limitVar] = null;
		}
		
		if (isset($params[$pageVar]) && $params[$pageVar] == '1') {
			$params[$pageVar] = null;
		}

		return parent::getPagerUrl($params);
	}

	/**
	 * Gets the path info from the request object
	 *
	 * @return string
	 */
	protected function _getPathInfo()
	{
		return trim(Mage::app()->getRequest()->getPathInfo(), '/');;
	}
}
