<?php defined('PROMO_ACCESS') or die('No direct script access.');

Option::delete('guestbook_template');
Option::delete('guestbook_limit');
Option::delete('guestbook_time');
Option::delete('guestbook_check');
Option::delete('guestbook_double');
Option::delete('guestbook_email');
Option::delete('guestbook_form');

Table::drop('guestbook');