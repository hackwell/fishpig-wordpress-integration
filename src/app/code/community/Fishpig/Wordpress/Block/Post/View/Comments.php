<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Post_View_Comments extends Fishpig_Wordpress_Block_Post_Abstract
{
	/**
	 * Comment collection cache
	 *
	 * @var Fishpig_Wordpress_Model_Resource_Post_Comments_Collection
	 */
	protected $_comments = null;
	
	/**
	 * Returns a collection of comments for the current post
	 *
	 * @return Fishpig_Wordpress_Model_Resource_Post_Comments_Collection
	 */
	public function getComments()
	{
		if (is_null($this->_comments)) {
			$this->_comments = new Varien_Data_Collection();

			if ($this->getCommentCount() > 0) {
				if ($post = $this->getPost()) {
					$userEmail = $this->getWordPressUser() ? $this->getWordPressUser()->getUserEmail() : null;

					if ($comments = $post->getResource()->getPostComments($post, $userEmail)) {
						$this->_comments = $comments;
						
						if ($this->getPagerBlock()) {
							$comments->setPageSize($this->getPagerBlock()->getLimit());
						}
					}
				}
			}
		}
		
		return $this->_comments;
	}
	
	/**
	 * Retrieve the WordPress user
	 * Returns false if user not set
	 * Requires customer synchronisation
	 *
	 * @return false|Fishpig_Wordpress_Model_User
	 */
	public function getWordPressUser()
	{
		$session = Mage::getSingleton('customer/session');
		
		if ($session->isLoggedIn()) {
			if ($user = $session->getCustomer()->getWordpressUser()) {
				return $user;
			}
		}
		
		return false;
	}
	
	/**
	 * Retrieve the amount of comments for the current post
	 *
	 * @return int
	 */
	public function getCommentCount()
	{
		return $this->getPost()->getCommentCount();
	}

	/**
	 * Setup the pager and comments form blocks
	 *
	 */
	protected function _beforeToHtml()
	{
		if (!$this->getTemplate()) {
			$this->setTemplate('wordpress/post/view/comments.phtml');
		}

		if ($this->getCommentCount() > 0)  {
			if ($pagerBlock = $this->getPagerBlock()) {
				$pagerBlock->setCollection($this->getComments());
			}
		}

		if ($commentsFormBlock = $this->getCommentFormBlock()) {
			$commentsFormBlock->setPost($this->getPost());
		}

		parent::_beforeToHtml();
	}
	
	/**
	 * Returns the HTML for the comment form
	 *
	 * @return string
	 */
	public function getCommentFormHtml()
	{
		return $this->getChildHtml('form');
	}
	
	/**
	 * Gets a block for the comment form
	 *
	 * @return Fishpig_Wordpress_Block_Post_View_Comment_Form
	 */
	public function getCommentFormBlock()
	{
		if (!$this->getChild('form')) {
			$this->setChild('form', $this->getLayout()->createBlock('wordpress/post_view_comment_form'));
		}

		return $this->getChild('form');
	}
	
	/**
	 * Get the pager block
	 * If the block isn't set in the layout XML, it will be created and will use the default template
	 *
	 * @return Fishpig_Wordpress_Post_Comment_Pager
	 */
	public function getPagerBlock()
	{
		if (!$this->getChild('pager')) {
			$this->setChild('pager', $this->getLayout()->createBlock('wordpress/post_view_comment_pager'));
		}
		
		return $this->getChild('pager');
	}
	
	/**
	 * Get the HTML for the pager block
	 *
	 * @return null|string
	 */
	public function getPagerHtml()
	{
		if ($this->helper('wordpress')->getWpOption('page_comments', false)) {
			return $this->getChildHtml('pager');
		}
	}
}
