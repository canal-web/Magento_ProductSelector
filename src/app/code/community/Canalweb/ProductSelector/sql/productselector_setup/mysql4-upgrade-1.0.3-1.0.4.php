<?php
/* @var $this Mage_Eav_Model_Entity_Setup */

// Add another extra column to the catalog_eav_attribute-table:
$this->getConnection()->addColumn(
    $this->getTable('catalog/eav_attribute'),
    'selectorunit',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable'  => true,
        'comment'   => 'Unit of attribute in selector'
    )
);

Mage::log('+selectorunit 1.0.3 > 1.0.4');
