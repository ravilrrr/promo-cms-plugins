<?php defined('PROMO_ACCESS') or die('No direct script access.');

    Table::create('stock', array('name', 'w', 'h', 'wmax', 'hmax', 'resize', 'sortby', 'order', 'template', 'quality'));
    Table::create('stock_img', array('name', 'pos', 'title', 'cat_id')); 
    Table::create('stock_fields', array('album_id', 'name', 'slug', 'pos', 'type'));
    
    $dir = ROOT . DS . 'public' . DS . 'stock' . DS;  
    if(!is_dir($dir)) mkdir($dir, 0755);