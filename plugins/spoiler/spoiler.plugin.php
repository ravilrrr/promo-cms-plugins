<?php

    /**
     *  Spoiler plugin 
     *
     *  @package Promo CMS
     *  @subpackage Plugins
     *  @author Yudin Evgeniy / JINN
     *  @copyright 2012-2015 Yudin Evgeniy / JINN
     *  @version 1.0.0
     *
     */

    // Register plugin
    Plugin::register( __FILE__,
                    __('Spoiler', 'spoiler'),
                    __('Spoiler plugin for Promo CMS', 'spoiler'),  
                    '1.0.0',
                    'JINN',
                    'http://cms.promo360.ru');

    Shortcode::add('spoiler', 'Spoiler::_shortcode');

    Javascript::add('plugins/spoiler/lib/script.js', 'frontend', 11);
    Stylesheet::add('plugins/spoiler/lib/style.css', 'frontend', 11);
    
    Action::add('admin_header','Spoiler::_header', 9);
    
    class Spoiler {
        public static function _shortcode($attributes, $content) {
            extract($attributes);

            if (isset($title)) {
                $class = (isset($class)) ? $class : 'sp-default';
                $current = (isset($show) and $show == true) ? ' current' : '';
                $html = '<div class="spoiler-head '.$class.$current.'">'.$title.'</div>';
                $html.= '<div class="spoiler-body '.$class.$current.'">'.Filter::apply('content', $content).'</div>';
                return $html;
            }
        }
        
        public static function _header() {
            if (isset(Plugin::$plugins['markitup'])) {
                echo "<script>$(document).ready(function(){mySettings.markupSet.push({name:'Spoiler', replaceWith:'{spoiler title=\"".__('Title', 'spoiler')."\"}".__('Hidden text', 'spoiler')."{/spoiler}', className:'spoiler-button' });});</script>";
                echo "<style>.spoiler-button a {background-image:url(".Site::url()."/plugins/spoiler/lib/button.png);}</style>";
            }
        }
    }