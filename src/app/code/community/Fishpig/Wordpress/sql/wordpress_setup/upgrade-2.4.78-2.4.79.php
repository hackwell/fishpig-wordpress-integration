<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */
	
	$this->startSetup();

	$tables = array(
		'wordpress_category_category',
		'wordpress_category_post',
		'wordpress_product_category',
		'wordpress_product_post',
	);
	
	foreach($tables as $table) {
		try {
			$this->run("DROP TABLE IF EXISTS {$this->getTable($table)};");
		}
		catch (Exception $e) {
			Mage::helper('wordpress')->log($e);
		}
	}
	
	$this->endSetup();
