<?php defined('PROMO_ACCESS') or die('No direct script access.');

Option::add('reviews_template', 'index');
Option::add('reviews_limit', 10);
Option::add('reviews_time', 60);
Option::add('reviews_check', 'no'); // yes/no
Option::add('reviews_double', 'no'); // yes/no
Option::add('reviews_email', Option::get('system_email'));
Option::add('reviews_form', 'show'); // show/hide

Table::create('reviews', array('name', 'date', 'message', 'answer', 'check', 'important'));