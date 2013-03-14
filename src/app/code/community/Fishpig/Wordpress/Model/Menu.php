<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */
 
class Fishpig_Wordpress_Model_Menu extends Fishpig_Wordpress_Model_Term
{
	/**
	 * Event data
	 *
	 * @var string
	 */
	protected $_eventPrefix      = 'wordpress_menu';
	protected $_eventObject      = 'menu';
	
	public function _construct()
	{
		$this->_init('wordpress/menu');
	}
	
	/**
	 * Retrieve the taxonomy type
	 *
	 * @return string
	 */
	public function getTaxonomy()
	{
		return 'nav_menu';
	}
	
	/**
	 * Retrieve the root menu items
	 *
	 * @return Fishpig_Wordpress_Model_Resource_Menu_Item_Collection
	 */
	public function getMenuItems()
	{
		return $this->getPostCollection();
	}

	/**
	 * Retrieve the object resource model
	 *
	 * @return Fishpig_Wordpress_Model_Resource_Post_Collection_Abstract
	 */    
    protected function _getObjectResourceModel()
    {
	    return Mage::getResourceModel('wordpress/menu_item_collection')
	    	->addPostTypeFilter('nav_menu_item')
	    	->addParentItemIdFilter(0);
    }
}
