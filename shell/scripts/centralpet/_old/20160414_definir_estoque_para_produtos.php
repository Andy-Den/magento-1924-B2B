<?php

require_once './configScript.php';

$stockId = 14;

$query = 'delete
		  from cataloginventory_stock_item
		  where stock_id = ' . $stockId;

$writeConnection->query($query);

$query = 'INSERT
    IGNORE INTO
        cataloginventory_stock_item(
            stock_id,
            qty,
            min_qty,
            use_config_min_qty,
            is_qty_decimal,
            backorders,
            use_config_backorders,
            min_sale_qty,
            use_config_min_sale_qty,
            max_sale_qty,
            use_config_max_sale_qty,
            is_in_stock,
            low_stock_date,
            notify_stock_qty,
            use_config_notify_stock_qty,
            manage_stock,
            use_config_manage_stock,
            stock_status_changed_auto,
            use_config_qty_increments,
            qty_increments,
            use_config_enable_qty_inc,
            enable_qty_increments,
            is_decimal_divided,
            product_id
        ) SELECT '. $stockId . ',
            10.0000,
            0.0000,
            1,
            0,
            0,
            1,
            1.0000,
            1,
            0.0000,
            1,
            1,
            NULL,
            NULL,
            1,
            1,
            0,
            0,
            1,
            0.0000,
            1,
            0,
            0,
            product_id
        FROM
            catalog_product_website
        WHERE
            website_id = ' . $websiteId;

$writeConnection->query($query);