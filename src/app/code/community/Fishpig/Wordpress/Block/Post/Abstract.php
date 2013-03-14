<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

abstract class Fishpig_Wordpress_Block_Post_Abstract extends Mage_Core_Block_Template
{
	/**
	 * Retrieve the current post object
	 *
	 * @return null|Fishpig_Wordpress_Model_Post
	 */
	public function getPost()
	{
		if (!$this->hasPost()) {
			return Mage::registry('wordpress_post');
		}
		
		return $this->_getData('post');
	}

	/**
	 * Returns the ID of the currently loaded post
	 *
	 * @return int|false
	 */
	public function getPostId()
	{
		if (is_object($this->getPost())) {
			return $this->getPost()->getId();
		}
		
		return false;
	}
	
	/**
	 * Returns true if comments are enabled for this post
	 *
	 * @return bool
	 */
	protected function canComment()
	{
		if ($post = $this->getPost()) {
			return $post->getCommentStatus() === 'open';
		}
		
		return false;
	}
	
	/**
	 * Determine whether previous/next links are enabled in the config
	 *
	 * @return bool
	 */
	public function canDisplayPreviousNextLinks()
	{
		if (!$this->hasDisplayPreviousNextLinks()) {
			$this->setDisplayPreviousNextLinks(Mage::getStoreConfigFlag('wordpress_blog/posts/display_previous_next'));
		}
		
		return $this->_getData('display_previous_next_links');
	}
	
	/**
	 * Gets the post meta block
	 *
	 * @return Mage_Core_Block_Template
	 */
	public function getMetaBlock()
	{
		if (!$this->getChild('post_meta')) {
			$renderBlock = $this->getLayout()->createBlock('wordpress/post_meta');

			$renderBlock->setTemplate('wordpress/post/meta.phtml');
				
			$this->setChild('post_meta', $renderBlock);
		}
		
		return $this->getChild('post_meta');
	}
	
	
	/**
	 * Retrieve the HTML for the password protect form
	 *
	 * @param Fishpig_Wordpress_Model_Post $post
	 * @return string
	 */
	public function getPasswordProtectHtml($post)
	{
		$block = $this->getLayout()
			->createBlock('core/template')
			->setTemplate('wordpress/post/protected.phtml')
			->setPost($post);
					
		return $block->toHtml();
	}
	
	/**
	 * Determine whether to display the full post content or the excerpt
	 *
	 * @return bool
	 */
	public function displayExcerptInFeed()
	{
		return Mage::helper('wordpress')->getWpOption('rss_use_excerpt') == '1';
	}
}
