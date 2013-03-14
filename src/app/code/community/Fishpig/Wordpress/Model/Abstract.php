<?php
/**
 * @category		Fishpig
 * @package		Fishpig_Wordpress
 * @license		http://fishpig.co.uk/license.txt
 * @author		Ben Tideswell <help@fishpig.co.uk>
 * @info			http://fishpig.co.uk/wordpress-integration.html
 */

abstract class Fishpig_Wordpress_Model_Abstract extends Mage_Core_Model_Abstract
{
	/**
	 * Name of entity meta table
	 * false if entity does not have a meta table
	 *
	 * @var string
	 */
	protected $_metaTable = false;
	
	/**
	 * Name of entity meta field
	 *
	 * @var false|string
	 */
	protected $_metaTableObjectField = false;

	/**
	 * Determine whether some meta fields have a prefix
	 * if true, the database table prefix is used
	 *
	 * @var bool
	 */
	protected $_metaHasPrefix = false;
	
	/**
	 * Array of entity's meta values
	 *
	 * @var array
	 */
	protected $_meta = array();
	
	/**
	 * An array of all meta keys that have changed
	 *
	 * @var array
	 */
	protected $_metaKeysChanged = array();

	/**
	 * Retrieve the name of the meta database table
	 *
	 * @return false|string
	 */
	public function getMetaTable()
	{
		if ($this->hasMeta()) {
			return $this->getResource()->getTable($this->_metaTable);
		}
		
		return false;
	}
	
	/**
	 * Retrieve the name of the column used to identify the entity
	 *
	 * @return string
	 */
	public function getMetaObjectField()
	{
		return $this->_metaTableObjectField;
	}
	
	/**
	 * Retrieve the column name of the primary key fields
	 *
	 * @return string
	 */
	public function getMetaPrimaryKeyField()
	{
		return 'meta_id';
	}
	
	/**
	 * Determine whether the entity type has a meta table
	 *
	 * @return bool
	 */
	public function hasMeta()
	{
		return $this->_metaTable !== false && $this->_metaTableObjectField !== false;
	}
	
	/**
	 * Retrieve a meta value
	 *
	 * @param string $key
	 * @return false|string
	 */
	public function getMetaValue($key)
	{
		if ($this->hasMeta()) {
			if (!isset($this->_meta[$key])) {
				$value = $this->getResource()->getMetaValue($this, $this->_getRealMetaKey($key));
				
				$meta = new Varien_Object(array(
					'key' => $key,
					'value' => $value,
				));
				
				Mage::dispatchEvent($this->_eventPrefix . '_get_meta_value', array('object' => $this, $this->_eventObject => $this, 'meta' => $meta));
				
				$this->_meta[$key] = $meta->getValue();
			}
			
			return $this->_meta[$key];
		}
		
		return false;
	}
	
	/**
	 * Retrieve all of the meta data as an array
	 *
	 * @return false|array
	 */
	public function getMetaData()
	{
		if ($this->hasMeta()) {
			return $this->_meta;
		}
		
		return false;
	}
	
	/**
	 * Set a custom field
	 * value isn't saved until entity is saved
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return $this
	 */
	public function setMetaValue($key, $value)
	{
		if ($this->hasMeta()) {
			$this->_meta[$key] = $value;
			$this->_metaKeysChanged[$key] = $key;
			$this->_hasDataChanges = true;
		}
		
		return $this;
	}
	
	/**
	 * Save any meta key values that have changed
	 *
	 * @return $this
	 */
	public function afterCommitCallback()
	{
		parent::afterCommitCallback();
	
		if ($this->hasMeta()) {
			foreach($this->_metaKeysChanged as $index => $key) {
				if (isset($this->_meta[$key])) {
					$this->getResource()->setMetaValue($this, $this->_getRealMetaKey($key), $this->_meta[$key]);
				}

				unset($this->_metaKeysChanged[$index]);
			}
		}
		
		return $this;
	}
	
	/**
	 * Changes the wp_ to the correct table prefix
	 *
	 * @param string $key
	 * @return string
	 */
	protected function _getRealMetaKey($key)
	{
		if ($this->_metaHasPrefix) {
			$tablePrefix = Mage::helper('wordpress/database')->getTablePrefix();

			if ($tablePrefix !== 'wp_') {
				if (preg_match('/^(wp_)(.*)$/', $key, $matches)) {
					return $tablePrefix . $matches[2];
				}
			}
		}
		
		return $key;	
	}
	
	/**
	 * Deprecreated from version 2.4.0
	 * use self::getMetaValue
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function getCustomField($key)
	{
		return $this->getMetaValue($key);
	}
	
	/**
	 * Deprecreated from version 2.4.0
	 * use self::getMetaValue
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function setCustomField($key, $value)
	{
		return $this->setMetaValue($key, $value);
	}
	
	/**
	 * Retrieve the event prefix
	 *
	 * @return string
	 */
	public function getEventPrefix()
	{
		return $this->_eventPrefix;		
	}
	
	/**
	 * Retrieve the event object name
	 *
	 * @return string
	 */
	public function getEventObject()
	{
		return $this->_eventObject;
	}
}
