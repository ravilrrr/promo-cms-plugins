<?php defined('PROMO_ACCESS') or die('No direct script access.');

    Option::delete('map_width');
    Option::delete('map_height');
    Option::delete('map_zoom');
    Option::delete('map_zoomc');
    
    Table::drop('maps');