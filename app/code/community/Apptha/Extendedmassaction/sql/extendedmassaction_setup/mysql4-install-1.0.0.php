<?php 

/**
 * Apptha
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.apptha.com/LICENSE.txt
 *
 * ==============================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * ==============================================================
 * This package designed for Magento COMMUNITY edition
 * Apptha does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Apptha does not provide extension support in case of
 * incorrect edition usage.
 * ==============================================================
 *
 * @category    Apptha
 * @package     Apptha_Extendedmassaction
 * @version     1.0.0
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2014 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 *
 */
$installer = $this;
	
	$installer->startSetup();

    $installer->run("
            DROP TABLE IF EXISTS {$this->getTable('extendedmassaction_column_details')};
            CREATE TABLE {$this->getTable('extendedmassaction_column_details')} (
            `id` INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `key` VARCHAR(255) COLLATE utf8_general_ci NOT NULL DEFAULT '',
            `value` TEXT COLLATE utf8_general_ci NOT NULL DEFAULT '',
            PRIMARY KEY (`id`)
           )ENGINE=InnoDB CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';"
    ); 
    
    $installer->endSetup();
?>