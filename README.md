# Magento_ProductSelector
Create a block with dynamic filters redirecting to a pre-filtered category page (using **Manadev** filters).

## Howto
After installing the module, you should have two new fields on attributes edit page: "use in product selector" (set yes for each attribute you want to see as an input) and "type of attribute in selector" (let default value unless specific cases like year).

To call the block in your template :

```php
echo $this->getLayout()->createBlock('core/template')->setTemplate('productselector/selector-block.phtml')->toHtml();
```

There is some basic configuration in System > Configuration > ProductSelector

Important: for accentuated locales, be sure to check Manadev > SEO url keys (attributes and options should not be accentuated)

French translation inc.

## @TODO:
- [ ] fresh test on a new website
- [ ] handle prices, km, and other digit-based parameters
