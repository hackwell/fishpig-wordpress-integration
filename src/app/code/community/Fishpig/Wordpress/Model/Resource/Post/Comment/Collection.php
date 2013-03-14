<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Model_Resource_Post_Comment_Collection extends Fishpig_Wordpress_Model_Resource_Collection_Abstract
{
	public function _construct()
	{
		$this->_init('wordpress/post_comment');
	}

	/**
	 * Perform the joins necessary to create a full category record
	 */
	protected function _initSelect()
	{
		$select = $this->getSelect()
			->distinct()
			->from(array('main_table' => $this->getResource()->getMainTable()));	
	}	

	public function addOrderByDate($dir = null)
	{
		if (is_null($dir)) {
			$dir = Mage::helper('wordpress')->getWpOption('comment_order');
			$dir = in_array($dir, array('asc', 'desc')) ? $dir : 'asc';
		}
		
		$this->getSelect()->order('main_table.comment_date ' . $dir);
		
		return $this;
	}
	
	/**
	  * Filters the collection of comments
	  * so only comments for a certain post are returned
	  *
	  */
	public function addPostIdFilter($postId)
	{
		return $this->addFieldToFilter('comment_post_ID', $postId);
	}
	
	/**
	 * Filter the collection by a user's ID
	 *
	 * @param int $userId
	 */
	public function addUserIdFilter($userId)
	{
		return $this->addFieldToFilter('user_id', $userId);
	}
	
	/**
	 * Filter the collection by the comment_author_email column
	 *
	 * @param string $email
	 */
	public function addCommentAuthorEmailFilter($email)
	{
		return $this->addFieldToFilter('comment_author_email', $email);
	}
	
	/**
	 * Filters the collection so only approved comments are returned
	 *
	 */
	public function addCommentApprovedFilter($status = 1, $userEmail = null)
	{
		if (is_null($userEmail) || $status !== 1) {
			return $this->addFieldToFilter('comment_approved', $status);
		}

		$this->getSelect()->where('comment_approved = ' .(int)$status . " OR (comment_approved = '0' AND comment_author_email = ?)", $userEmail);

		return $this;
	}
}
