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

}
