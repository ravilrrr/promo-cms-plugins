<?php defined('PROMO_ACCESS') or die('No direct script access.');

$forms = new Table('forms');
$elements = new Table('forms_elements');

// Feedback form
// ----------------------------------------------------
$forms->insert(array(
    'name'=>__('Feedback form', 'forms'),
    'email'=>Option::get('system_email'),
    'button'=>__('Button text default', 'forms'),
    'subject'=>__('Subject default', 'forms'),
    'message'=>__('Message default', 'forms'),
    'template'=>'left',
    'captcha'=>0,
    'align'=>'left',
));

$form_id = $forms->lastId();
$elArr = array();

// name
$elements->insert(array(
    'form_id'=>$form_id,
    'type'=>'name',
    'title'=>__('Element name', 'forms'),
    'comment'=>'',
    'position'=>1,
    'required'=>'yes',
    'values'=>'',
    'width'=>50,
));

$elArr[] = "f{$form_id}_el".$elements->lastId();

// email
$elements->insert(array(
    'form_id'=>$form_id,
    'type'=>'email',
    'title'=>__('Element email', 'forms'),
    'comment'=>'',
    'position'=>2,
    'required'=>'yes',
    'values'=>'',
    'width'=>50,
));

$elArr[] = "f{$form_id}_el".$elements->lastId();

// tel
$elements->insert(array(
    'form_id'=>$form_id,
    'type'=>'tel',
    'title'=>__('Element tel', 'forms'),
    'comment'=>'',
    'position'=>3,
    'required'=>'no',
    'values'=>'',
    'width'=>50,
));

$elArr[] = "f{$form_id}_el".$elements->lastId();

// comment
$elements->insert(array(
    'form_id'=>$form_id,
    'type'=>'textarea',
    'title'=>__('Your message', 'forms'),
    'comment'=>'',
    'position'=>4,
    'required'=>'yes',
    'values'=>'',
    'width'=>75,
));

$elArr[] = "f{$form_id}_el".$elements->lastId();

// new table
//Table::create('forms_form' . $form_id, $elArr);

FormsAdmin::refresh($form_id);

// Call me
// ----------------------------------------------------
$forms->insert(array(
    'name'=>__('Call me', 'forms'),
    'email'=>Option::get('system_email'),
    'button'=>__('Call me', 'forms'),
    'subject'=>__('Call me', 'forms'),
    'message'=>__('Message call me', 'forms'),
    'template'=>'top',
    'captcha'=>0,
    'align'=>'center',
));

$form_id = $forms->lastId();
$elArr = array();

// name
$elements->insert(array(
    'form_id'=>$form_id,
    'type'=>'name',
    'title'=>__('Element name', 'forms'),
    'comment'=>'',
    'position'=>1,
    'required'=>'no',
    'values'=>'',
    'width'=>100,
));

$elArr[] = "f{$form_id}_el".$elements->lastId();

// tel
$elements->insert(array(
    'form_id'=>$form_id,
    'type'=>'tel',
    'title'=>__('Element tel', 'forms'),
    'comment'=>'',
    'position'=>2,
    'required'=>'yes',
    'values'=>'',
    'width'=>100,
));

$elArr[] = "f{$form_id}_el".$elements->lastId();

// new table
//Table::create('forms_form' . $form_id, $elArr);

FormsAdmin::refresh($form_id);

Option::update('forms-demo-msg', 0);