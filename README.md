# Magento_ProductSelector
Create a block with dynamic filters redirecting to a pre-filtered category page (using **Manadev** filters).

## Howto
After installing the module, you should have two new fields on attributes edit page: "use in product selector" (set yes for each attribute you want to see as an input) and "type of attribute in selector" (let default value unless specific cases like year). The attribute must be used in product listing.

To call the block in your template :

```php
echo $this->getLayout()->createBlock('core/template')->setTemplate('productselector/selector-block.phtml')->toHtml();
```

or from an xml layout :
```xml
<reference name="home">
  <block type="page/template_links" name="product_selector" template="productselector/selector-block.phtml"/>
</reference>
```

There is some basic configuration in System > Configuration > ProductSelector (important : you will need to specify there the attribute set you want to use).

Important: for accentuated locales, be sure to check Manadev > SEO url keys (attributes and options should not be accentuated)

Important 2: if you install this module via composer, remember to allow simlinks in system > config > developer > template, or copy the base template in your local theme.

French translation inc.

## @TODO:
- [x] fresh test on a new website
- [ ] handle prices, km, and other digit-based parameters
