<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */
 
class Fishpig_Wordpress_Model_Observer extends Varien_Object
{
	/**
	 * Flag used to ensure observers only run once per cycle
	 *
	 * @var static array
	 */
	static protected $_singleton = array();

	/**
	 * Save the associations
	 *
	 * @param Mage_Sitemap_Model_Sitemap $sitemap
	 * @return bool
	 */	
	public function saveAssociationsObserver(Varien_Event_Observer $observer)
	{
		if (!$this->_observerCanRun(__METHOD__)) {
			return false;
		}

		try {
			Mage::helper('wordpress/associations')->processObserver($observer);
		}
		catch (Exception $e) {
			Mage::helper('wordpress')->log($e);
		}
	}
	
	/**
	 * Inject links into the top navigation
	 *
	 * @param Varien_Event_Observer $observer
	 * @return bool
	 */
	public function injectTopmenuLinksObserver(Varien_Event_Observer $observer)
	{
		if (!$this->_observerCanRun(__METHOD__)) {
			return false;
		}
		
		if (Mage::getStoreConfigFlag('wordpress_blog/menu/enabled')) {
			return $this->injectTopmenuLinks($observer->getEvent()->getMenu());
		}
	}

	/**
	 * Inject links into the Magento topmenu
	 *
	 * @param Varien_Data_Tree_Node $topmenu
	 * @return bool
	 */
	public function injectTopmenuLinks($topmenu, $menuId = null)
	{
		if (is_null($menuId)) {
			$menuId = Mage::getStoreConfig('wordpress_blog/menu/id');
		}
		
		if (!$menuId) {
			return false;
		}

		$menu = Mage::getModel('wordpress/menu')->load($menuId);		
		
		if (!$menu->getId()) {
			return false;
		}
		
		if (count($items = $menu->getMenuItems()) > 0) {
			return $this->_injectTopmenuLinks($items, $topmenu);
		}
		
		return false;
	}
	
	/**
	 * Inject links into the top navigation
	 *
	 * @param Fishpig_Wordpress_Model_Resource_Menu_Item_Collection $items
	 * @param Varien_Data_Tree_Node $parentNode
	 * @return bool
	 */
	protected function _injectTopmenuLinks($items, $parentNode)
	{
		foreach($items as $item) {
			$nodeId = 'wp-node-' . $item->getId();
				
			$data = array(
				'name' => $item->getLabel(),
				'id' => $nodeId,
				'url' => $item->getUrl(),
				'is_active' => $item->isItemActive(),
			);
			
			$itemNode = new Varien_Data_Tree_Node($data, 'id', $parentNode->getTree(), $parentNode);
			$parentNode->addChild($itemNode);

			if (count($children = $item->getChildrenItems()) > 0) {
				$this->_injectTopmenuLinks($children, $itemNode);
			}
		}
		
		return true;
	}
	
	/**
	 * Determine whether the observer method can run
	 * This stops methods being called twice in a single cycle
	 *
	 * @param string $method
	 * @return bool
	 */
	protected function _observerCanRun($method)
	{
		if (!isset(self::$_singleton[$method])) {
			self::$_singleton[$method] = true;
			
			return true;
		}
		
		return false;
	}
}
