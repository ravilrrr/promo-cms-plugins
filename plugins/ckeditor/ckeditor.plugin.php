<?php

/**
 *	CKEditor plugin
 *
 *	@package Promo CMS
 *  @subpackage Plugins
 *	@author Yudin Evgeniy / JINN
 *	@copyright 2015 Yudin Evgeniy / JINN
 *	@version 1.0.0
 *
 */

// Register plugin
Plugin::register( __FILE__,
                __('CKEditor', 'ckeditor'),
                __('The best web text editor for everyone', 'ckeditor'),
                '1.0.0',
                'JINN',
                'http://cms.promo360.ru');

// Add hooks
Action::add('admin_header', 'CKEditor::headers');

/**
 * CKEditor Class
 */
class CKEditor
{
    /**
     * Set editor headers
     */
    public static function headers()
    {
        echo ('
            <script type="text/javascript" src="'.Option::get('siteurl').'/plugins/ckeditor/ckeditor/ckeditor.js"></script>
            <script>$(document).ready(function () { CKEDITOR.replace("editor_area"); });</script>
        ');
    }

}
