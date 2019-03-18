<?php
/**
 * Observer.php
 *
 * @category Cammino
 * @package  Cammino_Productstocksort
 * @author   Cammino Digital <suporte@cammino.com.br>
 * @license  http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link     https://github.com/cammino/magento-productstocksort
 */

class Cammino_Productstocksort_Model_Observer
{
    /**
     * Function responsible for check the quantity in stock,
     * if zero, add product at the bottom of the page
     *
     * @param object $observer Magento observer
     *
     * @return null
     */
    public function catalogProductCollectionLoadBefore(Varien_Event_Observer $observer)
    {        
        // It takes the select query, stores it in a variable and
        // checks if a part of the select "_inventory_table" contains in the string

        $collection = $observer->getCollection();
        $sql = $collection->getSelect()->__toString();

        if (strpos($sql, "_inventory_table") === false) {

            $collection->getSelect()->joinLeft(
                array(
                    "_inventory_table" => $collection->getTable('cataloginventory/stock_item')
                ),
                "_inventory_table.product_id = e.entity_id",
                array('is_in_stock', 'manage_stock')
            );
            
            $collection->addExpressionAttributeToSelect(
                'on_top',
                "(CASE WHEN (((_inventory_table.use_config_manage_stock = 1) AND 
                (_inventory_table.is_in_stock = 1)) OR 
                ((_inventory_table.use_config_manage_stock = 0) AND 
                (1 - _inventory_table.manage_stock + _inventory_table.is_in_stock >= 1))) THEN 1 ELSE 0 END)",
                array()
            );

            // Make sure on_top is the first order directive
            $collection->getSelect()->order('on_top DESC');

            $order = $collection->getSelect()->getPart('order');
            array_unshift($order, array_pop($order));
            $collection->getSelect()->setPart('order', $order);
        }
    }
}