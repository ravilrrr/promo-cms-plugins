<?php

    /**
     *  Fancybox plugin
     *
     *  @package Promo CMS
     *  @subpackage Plugins
     *  @author Yudin Evgeniy / JINN
     *  @copyright 2013-2015 Yudin Evgeniy / JINN
     *  @version 1.0.0
     *
     *  Fancybox
     *  http://fancyapps.com/fancybox/
     *  version: 2.1.5 (June 14, 2013)
     */


    // Register plugin
    Plugin::register( __FILE__,
                    __('Fancybox', 'fancybox'),
                    __('Fancybox plugin for Promo CMS', 'fancybox'),  
                    '1.0.0',
                    'JINN',
                    'http://cms.promo360.ru/');
    
    // Frontend
    Action::add('theme_header', 'FancyboxCSS');
    Javascript::add('plugins/fancybox/lib/source/jquery.fancybox.pack.js', 'frontend', 13);
    Javascript::add('plugins/fancybox/lib/settings.js', 'frontend', 13);
    
    // Backend
    Action::add('admin_header', 'FancyboxCSS');
    Javascript::add('plugins/fancybox/lib/source/jquery.fancybox.pack.js', 'backend', 13);
    Javascript::add('plugins/fancybox/lib/settings.js', 'backend', 13);
    
    function FancyboxCSS() {
        echo '<link rel="stylesheet" href="'.Site::url().'/plugins/fancybox/lib/source/jquery.fancybox.css" type="text/css" />';
    }