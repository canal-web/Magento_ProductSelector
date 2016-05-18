<?php
class Canalweb_ProductSelector_IndexController extends Mage_Core_Controller_Front_Action
{
    /*
     * The proper "selector" function:
     * Generates a SQL query with the client's input
     * And returns a JSON formatted array containing:
     * - 1/ count: amount of products the query returns
     * - 2/ each param of the query in order to know the available options to be displayed in the inputs
     * - 3/ url: the url corresponding to the category page, pre-filtered
     */

    public function productSelectorAction()
    {
        $response = array();

        /*
         *  1/ "COUNT" : build the query
         */
        $attributeSetId = Mage::getStoreConfig('productselector/main/attribute_set_id');

        // fetch all selectorized params
        $params = array();
        $paramsTypeYear = array();
        $paramsTypePrices = array();
        $selectorAttributes = Mage::helper('productselector/data')->getSelectorAttributes($attributeSetId);
        foreach ($selectorAttributes as $selectorAttribute) {
            $code = $selectorAttribute->getAttributeCode();
            $params[] = $code;
            // add special treatment for special fields
            // right now, only year-type, but @TODO km/prices
            // see $paramsShort [= $paramsTypePrices] & $paramsShortReverse in old code for that
            if (Mage::helper('productselector/data')->isTypeYear($code)) {
                $paramsTypeYear[] = $code;
            } elseif (Mage::helper('productselector/data')->isTypePrice($code)) {
                $paramsTypePrices[] = $code;
            }
        }

        $inputParams = array();
        $getInputParams = array();
        $doNotReturn = array();

        // BUILDING THE QUERY
        // A: Construct the select / where query
        $select = "SELECT ";
        $where = " WHERE attribute_set_id=" . $attributeSetId . " AND s.qty > 0";
        $filter = "";
        $i = 0;
        foreach ($params as $param) {
            // typePrice is for special attributes: typeYears and TypePrices
            $typePrice = false;
            if (in_array($param, $paramsTypeYear)) {
                $comparatorOperator = '>=';
                $typePrice = true;
            } elseif (in_array($param, $paramsTypePrices)) {
                $comparatorOperator = '<=';
                $typePrice = true;
            }

            if ($typePrice) {
                if ($i == 0) $select .= "FLOOR(f." . $param . ") AS $param, FLOOR(f." . $param . ") AS " . $param . '_value';
                else $select .= ",FLOOR(f." . $param . ") AS $param, FLOOR(f." . $param . ") AS " . $param . '_value';
            } else {
                if ($i == 0) $select .= 'f.' . $param . ",f." . $param . "_value";
                else $select .= ",f." . $param . ",f." . $param . "_value";
            }

            if ((int) $this->getRequest()->getParam($param, false)) {
                $value = (int) $this->getRequest()->getParam($param, false);
                if ($typePrice) {
                    $where .= " AND " . $param . $comparatorOperator . $value;
                    $doNotReturn[$param] = $param; // we do not need an array of options in response
                    if (Mage::helper('productselector/data')->isTypeYear($param)) {
                        // we make an exception for "year"-type attributes
                        $maxValue = date("Y");
                        $filter .= "/$param/$value-" . $maxValue;
                    } else {
                        $maxValue = '99999999';
                        $filter .= "/$param/0-" . $value;
                    }
                } else {
                    $where .= " AND f." . $param . "=" . $value;
                    $inputParams[$param] = array($value, "");
                }
            }
            // Some parameters must always be returned
            if ((int) $this->getRequest()->getParam("get_" . $param, false)) {
                $value = (int) $this->getRequest()->getParam("get_" . $param, false);
                $getInputParams[$param] = array($value, "");
            }
            $i++;
        } // end foreach params

        /* B: @TODO add special treatment for prices & km
        foreach ($paramsTypePrices as $param) {
        } */

        // C: Merge all parts
        $storeId = Mage::app()->getStore()->getId();
        $dbTable = "catalog_product_flat_" . $storeId . ' f join cataloginventory_stock_status s on f.entity_id = s.product_id';
        $query = $select . ' FROM ' . $dbTable . $where;
        $readConnection = Mage::getSingleton('core/resource')->getConnection('core_read');
        $results = $readConnection->fetchAll($query);
        $response["count"] = count($results);


        /*
         * 2/ PARAMS
         * add to the response each attribute and its options
         * that are available for the filters currently selected
         * exceptions for price-typed attributes (doNotReturn)
         */
        foreach($params as $param) {
            if (array_key_exists($param, $doNotReturn)) {
                continue;
            }

            if ( (!array_key_exists($param, $inputParams)) || (array_key_exists($param, $getInputParams)) ) {
                $response[$param] = array();
            }

            $dummyArray = array();
            // Important: we build the arrays from the SQL results we got in step 1
            foreach($results as $result) {
                if (((array_key_exists($param, $inputParams)) || (array_key_exists($param, $getInputParams))) && $result[$param] == $inputParams[$param][0]) {
                    $inputParams[$param][1] = $result[$param."_value"];
                }

                $item = array();
                $item["value"] = $result[$param];
                $item["label"] = $result[$param."_value"];

                if ($item["label"] != null && !in_array($result[$param], $dummyArray)) {
                    if ((!array_key_exists($param, $inputParams)) || (array_key_exists($param, $getInputParams)) ) {
                        $response[$param][] = $item;
                    }
                    $dummyArray[] = $result[$param];
                }
            }
            if ((!array_key_exists($param, $inputParams)) || (array_key_exists($param, $getInputParams)) ) {
                usort($response[$param],
                    function($a, $b)
                    {
                        return $a["label"] > $b["label"];
                    }
                );
            }
        } // end foreach params


        /*
         *  3: URL CONSTRUCTION
         */
        $url = Mage::getStoreConfig('productselector/main/list_page');;

        // Arguments translation (order is important)
        $search = array(
            "'",
            "/",
            "_",
            " "
        );
        $replace = array(
            "",
            "--per-",
            "--uscore-",
            "-"
        );

        // Add each param to url (in a url-compatible way)
        foreach($inputParams as $paramCode => $paramValue) {
            //translate
            $paramValue = strtolower($paramValue[1]);
            $paramValuePropre = str_replace($search, $replace, $paramValue);
            $url .= "/" . $paramCode . "/" . $paramValuePropre;
        }
        $response["url"] = $url . $filter . ".html";


        /*
         * 4: RETURN
         * Finally, return the whole $response
         */
        $this->getResponse()->setBody(Zend_Json::encode($response));
    }

}
