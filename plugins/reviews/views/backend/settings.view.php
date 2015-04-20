<h2><?php echo __('Settings', 'reviews');?></h2><br/>

<?php
echo (
    Form::open().
    
    Form::label('template', __('Template', 'reviews')).
    Form::select('template', ReviewsAdmin::getTemplates(), Option::get('reviews_template'), array('class'=>'form-control')). 
    
    Form::label('limit', __('The number of records per page', 'reviews')).
    Form::input('limit', Option::get('reviews_limit'), array('class'=>'form-control')). 
    
    Form::label('time', __('Between messages (seconds)', 'reviews')).
    Form::input('time', Option::get('reviews_time'), array('class'=>'form-control')). 
    
    Html::br(1).Form::checkbox('form', 1, ((Option::get('reviews_form')=='hide') ? true : false)) . ' ' . __('Hide form', 'reviews').
    
    Html::br(2).Form::checkbox('check', 1, ((Option::get('reviews_check')=='yes') ? true : false)) . ' ' . __('Premoderation', 'reviews').
    
    Html::br(2).Form::checkbox('double', 1, ((Option::get('reviews_double')=='yes') ? true : false)) . ' ' . __('Duplicate messages to e-mail', 'reviews').Html::br(2).
    
    Form::label('email', __('Your e-mail', 'reviews')).
    Form::input('email', Option::get('reviews_email'), array('class'=>'form-control')). 
    
    Html::br().
    Form::hidden('csrf', Security::token()).
    Form::submit('submit_save_and_exit', __('Save and Exit', 'reviews'), array('class' => 'btn btn-primary')).Html::nbsp(2).
    Form::submit('submit_save', __('Save', 'reviews'), array('class' => 'btn btn-default')).
    Form::close()
);
?>