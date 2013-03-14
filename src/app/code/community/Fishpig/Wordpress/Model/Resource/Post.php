<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Model_Resource_Post extends Fishpig_Wordpress_Model_Resource_Post_Abstract
{
	public function _construct()
	{
		$this->_init('wordpress/post', 'ID');
	}

	/**
	 * Retrieve a collection of post tags
	 *
	 * @param Fishpig_Wordpress_Model_Post $post
	 * @return Fishpig_Wordpress_Model_Resource_Post_Tag_Collection
	 */
	public function getPostTags(Fishpig_Wordpress_Model_Post $post)
	{
		return Mage::getResourceModel('wordpress/post_tag_collection')
					->addPostIdFilter($post->getId());
	}
	
	/**
	 * Retrieve a collection of categories
	 *
	 * @param Fishpig_Wordpress_Model_Post $post
	 * @retrun Fishpig_Wordpress_Model_Post_Category_Collection
	 */
	public function getParentCategories(Fishpig_Wordpress_Model_Post $post)
	{
		return Mage::getResourceModel('wordpress/post_category_collection')
			->addPostIdFilter($post->getId());
	}
}
