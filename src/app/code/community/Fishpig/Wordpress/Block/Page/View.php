<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Page_View extends Mage_Core_Block_Template
{
	/**
	 * Returns the currently loaded page model
	 *
	 * @return Fishpig_Wordpress_Model_Page
	 */
	public function getPage()
	{
		return Mage::registry('wordpress_page');
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
			$commentsBlock->setPost($this->getPage());
		}
		
		return parent::_beforeToHtml();
	}
	
	/**
	 * Retrieve the HTML for the password protect form
	 *
	 * @return string
	 */
	public function getPasswordProtectHtml()
	{
		if (!$this->hasPasswordProtectHtml()) {
			$block = $this->getLayout()
				->createBlock('core/template')
				->setTemplate('wordpress/page/protected.phtml')
				->setPage($this->getPage());
					
			$this->setPasswordProtectHtml($block->toHtml());
		}
		
		return $this->_getData('password_protect_html');
	}
}
