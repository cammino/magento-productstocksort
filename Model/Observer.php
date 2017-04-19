<?php
class Cammino_Productstocksort_Model_Observer
{
    public function catalogProductCollectionLoadBefore(Varien_Event_Observer $observer)
    {        
        //Pega a query do select, armazena em uma variável e
        //verifica se uma parte do select "_inventory_table" contém na string
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
                "(CASE WHEN (((_inventory_table.use_config_manage_stock = 1) AND (_inventory_table.is_in_stock = 1)) OR  ((_inventory_table.use_config_manage_stock = 0) AND (1 - _inventory_table.manage_stock + _inventory_table.is_in_stock >= 1))) THEN 1 ELSE 0 END)",
                array()
            );
            $collection->getSelect()->order('on_top DESC');
            // Make sure on_top is the first order directive
            $order = $collection->getSelect()->getPart('order');
            array_unshift($order, array_pop($order));
            $collection->getSelect()->setPart('order', $order);
        }
    }
}