<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_PageController extends Fishpig_Wordpress_Controller_Abstract
{
	/**
	 * Used to do things en-masse
	 * eg. include canonical URL
	 *
	 * @return false|Fishpig_Wordpress_Model_Page
	 */
	public function getEntityObject()
	{
		return $this->_initPage();
	}
	
	/**
	 * If the feed parameter is set, forward to the comments action
	 *
	 * @return $this
	 */
	public function preDispatch()
	{
		parent::preDispatch();
		
		$page = $this->_initPage();
		
		if (!Mage::getStoreConfigFlag('wordpress_blog/layout/ignore_custom_homepage')) {
			if ($page->isBlogPage()) {
				$this->_forceForwardViaException('index', 'index');
				return false;
			}
		}
	
		return $this;	
	}

	/**
	 * Display the post view page
	 *
	 */
	public function viewAction()
	{
		$page = Mage::registry('wordpress_page');

		$this->_addCustomLayoutHandles(array(
			'wordpress_page_view_index',
			'wordpress_page_view_' . $page->getId(),
		));
		
		$this->_setPageViewTemplate();
		
		$this->_initLayout();		

		if (($headBlock = $this->getLayout()->getBlock('head')) !== false) {
			$headBlock->setDescription($page->getMetaDescription());
		}
		
		$pages = array();
		$buffer = $page;

		while ($buffer) {
			$this->_title(strip_tags($buffer->getPostTitle()));
			$pages[] = $buffer;
			$buffer = $buffer->getParentPage();
		}
		
		$pages = array_reverse($pages);
		$lastPage = array_pop($pages);
		
		foreach($pages as $buffer) {
			$this->addCrumb('page_' . $buffer->getId(), array('label' => $buffer->getPostTitle(), 'link' => $buffer->getPermalink()));
		}
		
		if ($lastPage) {
			$this->addCrumb('page_' . $lastPage->getId(), array('label' => $lastPage->getPostTitle()));
		}
		
		$this->renderLayout();
	}

	/**
	 * Override Magento config by setting a single column template
	 * if specified in Page edit of WP Admin
	 *
	 * @return $this
	 */
	protected function _setPageViewTemplate()
	{
		$page = $this->_initPage();
		
		$this->_rootTemplates[] = 'template_page';
				
		$keys = array('onecolumn', '1column');
		$template = $page->getMetaValue('_wp_page_template');
		
		foreach($keys as $key) {
			if (strpos($template, $key) !== false) {
				$this->getLayout()->helper('page/layout')->applyTemplate('one_column');
				break;
			}
		}
		
		return $this;
	}

	/**
	 * Initialise the post model
	 * Provides redirects for Guid links when using permalinks
	 *
	 * @return false|Fishpig_Wordpress_Model_Page
	 */
	protected function _initPage()
	{
		if ($page = Mage::registry('wordpress_page')) {
			if ($page->getId() && $page->getPostStatus() == 'publish') {
				return $page;
			}
		}
		
		return false;
	}	
}
