<?php defined('PROMO_ACCESS') or die('No direct script access.');

    // Delete Options
    Option::delete('article_template');
    Option::delete('article_limit');
    Option::delete('article_limit_admin');
    Option::delete('article_w');
    
    Table::drop('article');
    
    Dir::delete(ROOT . DS . 'storage' . DS . 'article');
    Dir::delete(ROOT . DS . 'public'  . DS . 'article');
    