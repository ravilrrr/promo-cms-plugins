<h2><?php echo __('Edit', 'reviews');?></h2><br/>

<?php       
echo (
    Form::open().
    
    '<div class="form-group'.(isset($errors['name_empty']) ? ' has-error' : '').'">'.
    Form::label('name', __('Name', 'reviews')).
    Form::input('name', $row['name'], array('class' => 'form-control')).
    '</div>'.
    
    '<div class="form-group'.(isset($errors['date_empty']) ? ' has-error' : '').'">'.
    Form::label('date', __('Date', 'reviews')).
    Form::input('date', Date::format($row['date'], 'Y-m-d H:i:s'), array('class' => 'form-control')).
    '</div>'.
    
    '<div class="form-group'.(isset($errors['message_empty']) ? ' has-error' : '').'">'.
    Form::label('message', __('Message', 'reviews')).    
    Form::textarea('message', $row['message'], array('style' => 'height:100px', 'class' => 'form-control')).
    '</div>'.
    
    Form::label('answer', __('Answer admin', 'reviews')).    
    Form::textarea('answer', $row['answer'], array('style' => 'height:100px', 'class' => 'form-control')).
    
    Html::br().
    Form::hidden('csrf', Security::token()).
    Form::submit('submit_save_and_exit', __('Save and Exit', 'reviews'), array('class' => 'btn btn-primary')).Html::nbsp(2).
    Form::submit('submit_save', __('Save', 'reviews'), array('class' => 'btn btn-default')).
    Form::close()
);
?>