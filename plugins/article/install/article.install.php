<?php defined('PROMO_ACCESS') or die('No direct script access.');

    // Add New Options
    Option::add('article_template', 'index');
    Option::add('article_limit', 7);
    Option::add('article_limit_admin', 10);
    Option::add('article_w', 150);
    
    // Add table
    $fields = array('name', 'title', 'h1', 'description', 'keywords', 'slug', 'date', 'author', 'status', 'img');
    Table::create('article', $fields);
    
    // Add directory for content
    $dir = ROOT . DS . 'storage' . DS . 'article' . DS;
    if(!is_dir($dir)) mkdir($dir, 0755);
    
    // Add directory for default image
    $dir = ROOT . DS . 'public' . DS . 'article' . DS;  
    if(!is_dir($dir)) mkdir($dir, 0755);
    
    // Add directory for images
    $dir = ROOT . DS . 'public' . DS . 'article' . DS . 'images' . DS;  
    if(!is_dir($dir)) mkdir($dir, 0755);