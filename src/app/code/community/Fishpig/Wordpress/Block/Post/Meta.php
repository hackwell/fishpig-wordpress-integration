<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Post_Meta extends Mage_Core_Block_Template
{
	public function getCategoryString(Fishpig_Wordpress_Model_Post_Abstract $object, array $params = array())
	{
		$html = array();
		
		if (count($categories = $object->getParentCategories()) > 0) {
			foreach($categories as $category) {
				$html[] = $this->_generateAnchor($category->getUrl(), $category->getName(), $params);
			}
		}
		
		return implode(', ', $html);
	}
	
	public function getTagString(Fishpig_Wordpress_Model_Post_Abstract $object, array $params = array())
	{
		$html = array();
		
		if (count($tags = $object->getTags()) > 0) {
			foreach($tags as $tag) {
				$html[] = $this->_generateAnchor($tag->getUrl(), $tag->getName(), $params);
			}
		}
		
		return implode(', ', $html);
	}
	
	public function getAuthorString(Fishpig_Wordpress_Model_Post_Abstract $object, array $params = array())
	{
		$author = $object->getAuthor();
		
		return $this->_generateAnchor($author->getUrl(), $author->getDisplayName(), $params);
	}
	
	
	public function hasTags(Fishpig_Wordpress_Model_Post_Abstract $object)
	{
		return count($object->getTags()) > 0;
	}
	
	protected function _generateAnchor($href, $anchor, array $params = array())
	{
		foreach($params as $param => $value) {
			$params[$params] = sprintf('%s="%s"', $param, $value);
		}
		
		$params = ' ' . implode(' ', $params);
		
		return sprintf('<a href="%s"%s>%s</a>', $href, $params, $anchor);
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
}
