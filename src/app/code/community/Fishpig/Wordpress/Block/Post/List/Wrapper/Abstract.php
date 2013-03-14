<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

abstract class Fishpig_Wordpress_Block_Post_List_Wrapper_Abstract extends Mage_Core_Block_Template
{
	/**
	 * Cache for post collection
	 *
	 * @var Fishpig_Wordpress_Model_Mysql4_Post_Collection
	 */
	protected $_postCollection = null;

	/**
	 * Cache for the post list block
	 *
	 * @var Fishpig_Wordpress_Block_Post_List
	 */
	protected $_postListBlock = null;
	
	/**
	 * Block name for the post list block
	 *
	 * @var string
	 */
	protected $_postListBlockName = 'wordpress_post_list';	
	
	/**
	  * Constructor
	  * This sets the default template for listing the posts
	  * This is not the template for this (wrapper)
	  *
	  */
	public function __construct()
	{
		parent::__construct();
		// Set the default template to list the posts
		$this->setPostListTemplate('wordpress/post/list.phtml');
	}
	
	/**
	 * Returns the collection of posts
	 *
	 * @return Fishpig_Wordpress_Model_Mysql4_Post_Collection
	 */
	public function getPostCollection()
	{
		if (is_null($this->_postCollection)) {
			if (($collection = $this->_getPostCollection()) !== false) {
				$collection->addStatusFilter('publish')->addOrder('post_date', 'desc');
				
				$this->_postCollection = $collection;
				
				Mage::dispatchEvent('wordpress_post_collection_before_load', array('block' => $this, 'collection' => $this->_postCollection));
				Mage::dispatchEvent($this->_getPostCollectionEventName(), array('block' => $this, 'collection' => $this->_postCollection));
			}
		}

		return $this->_postCollection;
	}
	
	/**
	 * Retrieve the event name for before the post collection is loaded
	 *
	 * @return string
	 */
	protected function _getPostCollectionEventName()
	{
		$class = get_class($this);
		
		return 'wordpress_block_' . strtolower(substr($class, strpos($class, 'Block')+6)) . '_post_collection_before_load';
	}
	
	/**
	 * Generates and returns the collection of posts
	 *
	 * @return Fishpig_Wordpress_Model_Mysql4_Post_Collection
	 */
	protected function _getPostCollection()
	{
		return Mage::getResourceModel('wordpress/post_collection');
	}

	/**
	 * Returns the HTML for the post collection
	 *
	 * @return string
	 */
	public function getPostListHtml()
	{
		return $this->getPostListBlock()->toHtml();
	}
	
	/**
	 * Gets the post list block
	 *
	 * @return Fishpig_Wordpress_Block_Post_List
	 */
	public function getPostListBlock()
	{
		if (is_null($this->_postListBlock)) {
			if (($block = $this->getChild($this->_postListBlockName)) === false) {
				$block = $this->getLayout()
					->createBlock('wordpress/post_list', $this->_postListBlockName . rand(1111, 9999))
					->setTemplate($this->getPostListTemplate());
			}

			$block->setWrapperBlock($this);

			$this->_postListBlock = $block;
		}
		
		return $this->_postListBlock;
	}
	
	/**
	 * Sets the name of the child block that contains the post list
	 *
	 * @param string $blockName
	 */
	public function setPostListBlockName($blockName)
	{
		$this->_postListBlockName = $blockName;
		return $this;
	}
}
