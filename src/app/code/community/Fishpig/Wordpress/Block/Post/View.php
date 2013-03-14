<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Post_View extends Fishpig_Wordpress_Block_Post_Abstract
{
	/**
	 * Retrieve the current post model
	 *
	 * @return Fishpig_Wordpress_Model_Post
	 */
	public function getPost()
	{
		if (!$this->_getData('post')) {
			$this->setData('post', Mage::registry('wordpress_post'));
		}
		
		return $this->_getData('post');
	}

	/**
	  * Returns the HTML for the comments block
	  *
	  * @return string
	  */
	public function getCommentsHtml()
	{
		return $this->getChildHtml('comments');
	}
	
	/**
	 * Gets the comments block
	 *
	 * @return Fishpig_Wordpress_Block_Post_View_Comments
	 */
	public function getCommentsBlock()
	{
		if (!$this->getChild('comments')) {
			$this->setChild('comments', $this->getLayout()->createBlock('wordpress/post_view_comments'));
		}
		
		return $this->getChild('comments');
	}

	/**
	 * Setup the comments block
	 *
	 */
	protected function _beforeToHtml()
	{
		if ($commentsBlock = $this->getCommentsBlock()) {
			$commentsBlock->setPost($this->getPost());
		}
		
		if ($this->getPost()->getPostViewTemplate()) {
			$this->setTemplate($this->getPost()->getPostViewTemplate());
		}

		return parent::_beforeToHtml();
	}
	
	/**
	 * Gets the post meta block
	 *
	 * @return Mage_Core_Block_Template
	 */
	public function getMetaBlock()
	{
		return parent::getMetaBlock()->setIncludeNextPreviousLinks(true);
	}
}
