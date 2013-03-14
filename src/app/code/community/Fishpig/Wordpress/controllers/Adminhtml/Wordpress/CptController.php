<?php
/**
 * @category		Fishpig
 * @package		Fishpig_Wordpress
 * @license		http://fishpig.co.uk/license.txt
 * @author		Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Adminhtml_Wordpress_CptController extends Mage_Adminhtml_Controller_Action
{
	/**
	 * Ensure required addon is installed
	 *
	 */
	public function preDispatch()
	{
		if ($this->getRequest()->getActionName() !== 'upgrade') {
			if (!$this->isAddonInstalled()) {
				$this->setFlag('', self::FLAG_NO_DISPATCH, true);
				$e = new Mage_Core_Controller_Varien_Exception();
				throw $e->prepareForward('upgrade');
			}
		}
		
		return parent::preDispatch();
	}
	
	/**
	 * Display the upgrade page
	 *
	 */
	public function upgradeAction()
	{
		$this->loadLayout();
		$this->_setBasePageTitle();
		$this->_title(Mage::helper('wordpress')->__('Upgrade Required'));
		$this->renderLayout();
	}

	/**
	 * Display a list of existing custom post types
	 *
	 */
	public function indexAction()
	{
		$this->loadLayout();
		$this->_setBasePageTitle();
		$this->renderLayout();
	}
	
	/**
	 * Allow the user to enter a new custom post type
	 *
	 */
	public function newAction()
	{
		$this->_forward('edit');
	}
	
	/**
	 * Edit an existing post type
	 *
	 */
	public function editAction()
	{
		$this->_getPostType();
		
		$this->loadLayout();
		
		if (($type = $this->_getPostType()) !== false) {
			$this->_title($type->getName());
		}

		$this->renderLayout();
	}
	
	public function saveAction()
	{
		if ($data = $this->getRequest()->getPost('cpt')) {
			$object = Mage::getModel('wp_addon_cpt/type')
				->setData($data)
				->setId($this->getRequest()->getParam('id', null));
				
			try {
				$object->save();

				$this->_getSession()->addSuccess($this->__('The custom post type has been saved.'));
			}
			catch (Exception $e) {
				$this->_getSession()->addError($this->__($e->getMessage()));
			}
				
			if ($object->getId() && $this->getRequest()->getParam('back', false)) {
				$this->_redirect('*/*/edit', array('id' => $object->getId()));
				return;
			}
		}
		else {
			$this->_getSession()->addError($this->__('There was no data to save.'));
		}

		$this->_redirect('*/*');
	}
	
	/**
	 * Set the base page titles
	 *
	 * @return $this
	 */
	protected function _setBasePageTitle()
	{
		$this->_title(Mage::helper('wordpress')->__('WordPress'));
		$this->_title(Mage::helper('wordpress')->__('Custom Post Types'));
		
		return $this;
	}
	
	/**
	 * Retrieve the current post type
	 *
	 * @return Fishpig_Wordpress_Model_Post_Type
	 */
	protected function _getPostType()
	{
		if (($type = Mage::registry('wordpress_post_type')) !== null) {
			return $type;
		}
		
		$typeId = $this->getRequest()->getParam('id');
		
		$type = Mage::getModel('wp_addon_cpt/type')->load($this->getRequest()->getParam('id', 0));
		
		if ($type->getId()) {
			Mage::register('wordpress_post_type', $type);
			
			return $type;
		}
		
		return false;
	}
	
	public function isAddonInstalled()
	{
		$modules = (array)Mage::getConfig()->getNode('modules')->children();
			
		return isset($modules['Fishpig_Wordpress_Addon_CPT']);
	}
}
