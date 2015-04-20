<h2><?php echo __('Settings', 'question');?></h2><br/>

<?php
echo (
    Form::open().
    
    Form::label('template', __('Template', 'question')).
    Form::select('template', QuestionAdmin::getTemplates(), Option::get('question_template'), array('class'=>'form-control')). 
    
    Form::label('limit', __('The number of records per page', 'question')).
    Form::input('limit', Option::get('question_limit'), array('class'=>'form-control')). 
    
    Form::label('time', __('Between messages (seconds)', 'question')).
    Form::input('time', Option::get('question_time'), array('class'=>'form-control')). 
    
    Html::br(1).Form::checkbox('form', 1, ((Option::get('question_form')=='hide') ? true : false)) . ' ' . __('Hide form', 'question').
    
    Html::br(2).Form::checkbox('check', 1, ((Option::get('question_check')=='yes') ? true : false)) . ' ' . __('Premoderation', 'question').
    
    Html::br(2).Form::checkbox('double', 1, ((Option::get('question_double')=='yes') ? true : false)) . ' ' . __('Duplicate messages to e-mail', 'question').Html::br(2).
    
    Form::label('email', __('Your e-mail', 'question')).
    Form::input('email', Option::get('question_email'), array('class'=>'form-control')). 
    
    Html::br().
    Form::hidden('csrf', Security::token()).
    Form::submit('submit_save_and_exit', __('Save and Exit', 'question'), array('class' => 'btn btn-primary')).Html::nbsp(2).
    Form::submit('submit_save', __('Save', 'question'), array('class' => 'btn btn-default')).
    Form::close()
);
?>