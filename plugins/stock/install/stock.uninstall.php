<?php defined('PROMO_ACCESS') or die('No direct script access.');
       
    Table::drop('stock');
    Table::drop('stock_img');
    Table::drop('stock_fields');
    
    function stockRemoveDir($dir) {
        if ($objs = glob($dir."/*")) {
            foreach($objs as $obj) {
                is_dir($obj) ? stockRemoveDir($obj) : unlink($obj);
            }
        }
        rmdir($dir);
    }
    
    stockRemoveDir(ROOT . DS . 'public' . DS . 'stock');