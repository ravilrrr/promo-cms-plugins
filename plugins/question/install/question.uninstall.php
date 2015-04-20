<?php defined('PROMO_ACCESS') or die('No direct script access.');

Option::delete('question_template');
Option::delete('question_limit');
Option::delete('question_time');
Option::delete('question_check');
Option::delete('question_double');
Option::delete('question_email');
Option::delete('question_form');

Table::drop('question');