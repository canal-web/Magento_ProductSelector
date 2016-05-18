<?php

class Canalweb_ProductSelector_Model_Observer
{
    /**
     * Hook that allows us to edit the form that is used to create and/or edit attributes.
     * @param Varien_Event_Observer $observer
     */


    public function addFieldToAttributeEditForm($observer)
    {
        $yesnoSource = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();
        $typesArray[] = array(
            'value' => 'default',
            'label' => Mage::helper('core')->__('Default'),
        );
        $typesArray[] = array(
            'value' => 'typeYear',
            'label' => Mage::helper('core')->__('Year: higher is better, max = %s', date('Y')),
        );
        $typesArray[] = array(
            'value' => 'typePrice',
            'label' => Mage::helper('core')->__('Price: lower is better, max = 99999999'),
        );


        // Add an extra field to the base fieldset:
        $fieldset = $observer->getForm()->getElement('front_fieldset');
        $fieldset->addField('selectorize', 'select', array(
            'name' => 'selectorize',
            'values' => $yesnoSource,
            'label' => Mage::helper('core')->__('Use in product selector')
        ), 'apply_to');
        $fieldset->addField('selectortype', 'select', array(
            'name' => 'selectortype',
            'values' => $typesArray,
            'label' => Mage::helper('core')->__('Type of attribute in selector')
        ), 'apply_to');
    }
}
