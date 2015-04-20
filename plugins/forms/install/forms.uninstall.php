<?php defined('PROMO_ACCESS') or die('No direct script access.');

Table::drop('forms');
Table::drop('forms_elements');

Dir::delete(STORAGE . DS . 'forms' . DS);

Option::delete('forms-demo-msg');