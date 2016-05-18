<?php
/* @var $this Mage_Eav_Model_Entity_Setup */

// Add another extra column to the catalog_eav_attribute-table:
$this->getConnection()->addColumn(
    $this->getTable('catalog/eav_attribute'),
    'selectortype',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable'  => true,
        'comment'   => 'Type of attribute in selector'
    )
);

Mage::log('selectortype 1.0.2 > 1.0.3');
