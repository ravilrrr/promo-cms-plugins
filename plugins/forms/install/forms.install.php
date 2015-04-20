<?php defined('PROMO_ACCESS') or die('No direct script access.');

Table::create('forms', array('name', 'email', 'button', 'subject', 'message', 'template', 'captcha', 'align'));
Table::create('forms_elements', array('form_id', 'type', 'title', 'comment', 'position', 'required', 'values', 'width'));

$dir = STORAGE . DS . 'forms' . DS;
if(!is_dir($dir)) mkdir($dir, 0755);

Option::add('forms-demo-msg', 1);