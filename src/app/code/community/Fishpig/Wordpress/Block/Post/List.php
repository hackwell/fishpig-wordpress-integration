<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Post_List extends Fishpig_Wordpress_Block_Post_Abstract
{
	/**
	 * Renderer block for posts
	 *
	 * @var Fishpig_Wordpress_Block_Post_List_Renderer
	 */
	protected $_renderBlock = null;
	
	/**
	 * Renderer and template information for post types
	 *
	 * @var array
	 */
	protected $_postTypeTemplates = array(
		'post' => 'wordpress/post/list/renderer/default.phtml',
	);

	/**
	 * Cache for post collection
	 *
	 * @var Fishpig_Wordpress_Model_Resource_Post_Collection
	 */
	protected $_postCollection = null;

	/**
	 * Block wrapper (category, tag, author etc)
	 *
	 * @var Fishpig_Wordpress_Block_Post_List_Abstract
	 */
	protected $_wrapperBlock = null;
	
	/**
	 * Cache for the pager block
	 *
	 * @var Fishpig_Wordpress_Block_Post_List_Pager
	 */
	protected $_pagerBlock = null;
	
	/**
	 * Returns the collection of posts
	 *
	 * @return Fishpig_Wordpress_Model_Mysql4_Post_Collection
	 */
	public function getPosts()
	{
		return $this->_getPostCollection();
	}
	
	/**
	 * Generates and returns the collection of posts
	 *
	 * @return Fishpig_Wordpress_Model_Mysql4_Post_Collection
	 */
	protected function _getPostCollection()
	{
		if (is_null($this->_postCollection)) {
			if (is_null($this->getWrapperBlock()) === false) {
				$this->_postCollection = $this->getWrapperBlock()->getPostCollection();
				
				if ($this->getPagerBlock() && $this->_postCollection) {
					$this->getPagerBlock()->setCollection($this->_postCollection);
				}
			}
		}
		
		return $this->_postCollection;
	}
	
	/**
	 * Sets the parent block of this block
	 * This block can be used to auto generate the post list
	 *
	 * @param Fishpig_Wordpress_Block_Post_List_Wrapper_Abstract $wrapper
	 * @return $this
	 */
	public function setWrapperBlock(Fishpig_Wordpress_Block_Post_List_Wrapper_Abstract $wrapper)
	{
		$this->_wrapperBlock = $wrapper;

		return $this;
	}
	
	/**
	 * Returns the block wrapper object
	 *
	 * @return Fishpig_Wordpress_Block_Post_List_Wrapper_Abstract
	 */
	public function getWrapperBlock()
	{
		return $this->_wrapperBlock;
	}
	
	/**
	 * Get the pager block
	 * If the block isn't set in the layout XML, it will be created and will use the default template
	 *
	 * @return Fishpig_Wordpress_Post_List_Pager
	 */
	public function getPagerBlock()
	{
		if (is_null($this->_pagerBlock)) {
			$pagerBlock = $this->getChild('pager');
			
			if (!$pagerBlock) {
				$pagerBlock = $this->getLayout()->createBlock('wordpress/post_list_pager', 'pager' . rand(1111, 9999));
				
				$this->setChild('pager', $pagerBlock);
			}

			$pagerBlock->setPostListBlock($this);

			$this->_pagerBlock = $pagerBlock;
		}
		
		return $this->_pagerBlock;
	}
	
	/**
	 * Get the HTML for the pager block
	 *
	 * @return string
	 */
	public function getPagerHtml()
	{
		return $this->getChildHtml('pager');
	}
	
	/**
	 * Retrieve the correct renderer and template for $post
	 *
	 * @param Fishpig_Wordpress_Model_Post_Abstract $post
	 * @return Fishpig_Wordpress_Block_Post_List_Renderer
	 */
	public function getPostRenderer(Fishpig_Wordpress_Model_Post_Abstract $post)
	{
		$type = $post->getPostType();
		
		if (is_null($this->_renderBlock)) {
			$this->_renderBlock = $this->getLayout()->createBlock('wordpress/post_list_renderer');
		}
		
		$this->_renderBlock->setPost($post);
		
		if ($post->getPostListTemplate()) {
			return $this->_renderBlock->setTemplate($post->getPostListTemplate());
		}

		if (isset($this->_postTypeTemplates[$type])) {
//			return $this->_renderBlock->setTemplate($this->_postTypeTemplates[$type]);
		}
		
		return $this->_renderBlock->setTemplate('wordpress/post/list/renderer/default.phtml');
	}
}
