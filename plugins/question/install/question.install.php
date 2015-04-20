<?php defined('PROMO_ACCESS') or die('No direct script access.');

Option::add('question_template', 'index');
Option::add('question_limit', 10);
Option::add('question_time', 60);
Option::add('question_check', 'no'); // yes/no
Option::add('question_double', 'no'); // yes/no
Option::add('question_email', Option::get('system_email'));
Option::add('question_form', 'show'); // show/hide

Table::create('question', array('name', 'date', 'message', 'answer', 'check', 'important'));