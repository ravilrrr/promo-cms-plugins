<h2><?php echo __('Settings', 'guestbook');?></h2><br/>

<?php
echo (
    Form::open().
    
    Form::label('template', __('Template', 'guestbook')).
    Form::select('template', GuestbookAdmin::getTemplates(), Option::get('guestbook_template'), array('class'=>'form-control')). 
    
    Form::label('limit', __('The number of records per page', 'guestbook')).
    Form::input('limit', Option::get('guestbook_limit'), array('class'=>'form-control')). 
    
    Form::label('time', __('Between messages (seconds)', 'guestbook')).
    Form::input('time', Option::get('guestbook_time'), array('class'=>'form-control')). 
    
    Html::br(1).Form::checkbox('form', 1, ((Option::get('guestbook_form')=='hide') ? true : false)) . ' ' . __('Hide form', 'guestbook').
    
    Html::br(2).Form::checkbox('check', 1, ((Option::get('guestbook_check')=='yes') ? true : false)) . ' ' . __('Premoderation', 'guestbook').
    
    Html::br(2).Form::checkbox('double', 1, ((Option::get('guestbook_double')=='yes') ? true : false)) . ' ' . __('Duplicate messages to e-mail', 'guestbook').Html::br(2).
    
    Form::label('email', __('Your e-mail', 'guestbook')).
    Form::input('email', Option::get('guestbook_email'), array('class'=>'form-control')). 
    
    Html::br().
    Form::hidden('csrf', Security::token()).
    Form::submit('submit_save_and_exit', __('Save and Exit', 'guestbook'), array('class' => 'btn btn-primary')).Html::nbsp(2).
    Form::submit('submit_save', __('Save', 'guestbook'), array('class' => 'btn btn-default')).
    Form::close()
);
?>