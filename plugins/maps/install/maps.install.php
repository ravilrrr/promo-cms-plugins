<?php defined('PROMO_ACCESS') or die('No direct script access.');
    
    Option::add('map_width', 0);
    Option::add('map_height', 300);
    Option::add('map_zoom', 14);
    Option::add('map_zoomc', 19);   
        
    Table::create('maps', array('address', 'phones', 'lat', 'lon'));