<?php
class Canalweb_ProductSelector_Helper_Data extends Mage_Core_Helper_Abstract
{

    /* List attributes that are supposed to appear in selector */
    function getSelectorAttributes($attributeSetId)
    {
        $attributes = Mage::getModel('catalog/product_attribute_api')->items($attributeSetId);
        $selectorAttributes = array();

        foreach ($attributes as $attribute)
        {
            $attributeModel = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', $attribute['code']);
            if ($attributeModel->getSelectorize())
                $selectorAttributes[] = $attributeModel;
        }

        return $selectorAttributes;
    }

    function isTypeYear($attributeCode)
    {
        $attributeModel = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', $attributeCode);
        if ($attributeModel->getSelectortype() == 'typeYear') {
            return true;
        }
    }

    function isTypePrice($attributeCode)
    {
        $attributeModel = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', $attributeCode);
        if ($attributeModel->getSelectortype() == 'typePrice') {
            return true;
        }
    }

    /* Get unit to use  */
    function getUnit($attributeCode)
    {
        $attributeModel = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', $attributeCode);
        return $attributeModel->getSelectorunit() ? $attributeModel->getSelectorunit() : false;
    }

    /* Get max value available for typePrices parameters */
    function getMaxValue($attributeCode)
    {
        // Params needed to build the query
        $attributeSetId = Mage::getStoreConfig('productselector/main/attribute_set_id');
        $storeId = Mage::app()->getStore()->getId();

        // query building
        $query = "SELECT FLOOR(f.".$attributeCode.") AS max FROM catalog_product_flat_". $storeId ." f join cataloginventory_stock_status s on f.entity_id = s.product_id WHERE attribute_set_id=".$attributeSetId." AND s.qty>0 ORDER BY max DESC limit 1";
        $readConnection = Mage::getSingleton('core/resource')->getConnection('core_read');
        $result = $readConnection->fetchAll($query);
        return $result[0]["max"];
    }

}
