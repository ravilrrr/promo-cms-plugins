<?php defined('PROMO_ACCESS') or die('No direct script access.');

Option::add('guestbook_template', 'index');
Option::add('guestbook_limit', 10);
Option::add('guestbook_time', 60);
Option::add('guestbook_check', 'no'); // yes/no
Option::add('guestbook_double', 'no'); // yes/no
Option::add('guestbook_email', Option::get('system_email'));
Option::add('guestbook_form', 'show'); // show/hide

Table::create('guestbook', array('name', 'date', 'message', 'answer', 'check', 'important'));