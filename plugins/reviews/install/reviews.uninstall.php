<?php defined('PROMO_ACCESS') or die('No direct script access.');

Option::delete('reviews_template');
Option::delete('reviews_limit');
Option::delete('reviews_time');
Option::delete('reviews_check');
Option::delete('reviews_double');
Option::delete('reviews_email');
Option::delete('reviews_form');

Table::drop('reviews');