<?php

    /**
     *  Stock plugin
     *
     *  @package Promo CMS
     *  @subpackage Plugins
     *  @author Yudin Evgeniy / JINN
     *  @copyright 2013-2015 Yudin Evgeniy / JINN
     *  @version 1.0.0
     */


    // Register plugin
    Plugin::register( __FILE__,
                    __('Stock', 'stock'),
                    __('Stock plugin for Promo CMS', 'stock'),  
                    '1.0.0',
                    'JINN',
                    'http://cms.promo360.ru/');


    // Load Stock Admin for Editor and Admin
    if (Session::exists('user_role') && in_array(Session::get('user_role'), array('admin', 'editor'))) {
        Plugin::admin('stock');
    }
    
    Stylesheet::add('plugins/stock/lib/style.css', 'frontend', 11);
    Javascript::add('plugins/stock/lib/admin.js', 'backend', 11);
    
    Shortcode::add('stock', 'Stock::_shortcode');
    
    class Stock {
    
        /**
         *  Shortcode
         *
         *  One image: {stock album="1" img="Tf7x4ySG2h.jpg"}
         *  One image random: {stock album="1" show="random"}
         *  One image last: {stock album="1" show="last"}
         *  Three image last: {stock album="1" show="last" count="3"}
         *  Album: {stock album="1"}
         */
        public static function _shortcode($attributes) {
            extract($attributes);
            
            if (isset($img))
                return Stock::img($album, $img);
            elseif (isset($show) and isset($count))
                return Stock::album($album, $count);
            elseif (isset($show))
                return Stock::_show($album, $show);
            else
                return Stock::album($album);
        }
        
        /**
         *  Show one image: last image, random image, ...
         *  for Stock::last() and Stock::random() function
         *
         *  $type: last or random
         */
        public static function _show($album_id, $type='last'){
            $album_id = intval($album_id);
            
            $original = Site::url().'/public/stock/'.$album_id.'/original/';
            $thumbs = Site::url().'/public/stock/'.$album_id.'/thumbs/';
            
            $stock_img = new Table('stock_img');
            $images = $stock_img->select('[cat_id='.$album_id.']');
            
            if ($type == 'random')
                $key = array_rand($images);
            else 
                $key = count($images)-1;
            
            return Stock::img($album_id, $images[$key]['name']);
        }
        
        /**
         *  Display last image from album
         *
         *  Example: 
         *  <?php echo Stock::last(1);?> - one last image
         *  <?php echo Stock::last(1, 3);?> - three last images
         */
        public static function last($album_id, $count = null){
            if ($count == null)
                return Stock::_show($album_id, 'last');
            else 
                return Stock::album($album_id, $count);
        }
        
        /**
         *  Display random image from album
         *
         *  Example: <?php echo Stock::random(1); ?>
         */
        public static function random($album_id){
            return Stock::_show($album_id, 'random');
        }
        
        /**
         *  Display one image
         *  
         *  Example: <?php echo Stock::img(1, 'Tf7x4ySG2h.jpg'); ?>
         */
        public static function img($album_id, $img) {
            $album_id = intval($album_id);
            
            // path
            $original = Site::url().'/public/stock/'.$album_id.'/original/';
            $thumbs = Site::url().'/public/stock/'.$album_id.'/thumbs/';
            
            $html = '<a href="'.$original.$img.'" class="stock_one"><img src="'.$thumbs.$img.'" alt=""/></a>';
            
            return $html;
        }
        
        /**
         *  Display album
         *
         *  Example: <?php echo Stock::album(1); ?>
         */
        public static function album($album_id, $count_image='all'){
            $album_id = intval($album_id);
            $count_image = ((int)$count_image > 0) ? (int)$count_image : 'all';
            
            // path
            $original = Site::url().'/public/stock/'.$album_id.'/original/';
            $thumbs = Site::url().'/public/stock/'.$album_id.'/thumbs/';
            
            // table
            $stock = new Table('stock');
            $stock_img = new Table('stock_img');
            $stock_fields = new Table('stock_fields');
            
            // select
            $album = $stock->select('[id='.$album_id.']', null);
            $files = $stock_img->select('[cat_id='.$album_id.']', $count_image);
            $fields = $stock_fields->select('[album_id='.$album_id.']', 'all', null, null, 'pos');
            
            // the sort order
            if ($album['order']!= 'DESC')
                $album['order'] = 'ASC';
            
            // sort by
            if ($album['sortby'] == 'position')
                $files = Arr::subvalSort($files, 'pos', $album['order']);
            else 
                $files = Arr::subvalSort($files, 'id', $album['order']);
            
            // template
            $template = (empty($album['template'])) ? 'default' : $album['template'];
            
            $view = new View('stock/views/frontend/' . $template);
            
            $view->assign('original', $original);
            $view->assign('thumbs', $thumbs);
            $view->assign('album', $album);
            $view->assign('files', $files);
            $view->assign('fields', $fields);
            
            if (count($files) > 0) {
                return $view->render();
            }
        }
    }