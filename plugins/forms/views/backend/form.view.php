<h2><?php echo __('Settings form', 'forms'); ?></h2><br/>

<form method="post">
<?php
echo (
    '<div class="form-group '.((isset($errors['name_empty'])) ? 'has-error' : '').'">'.
    Form::label('name', __('Name', 'forms')).
    Form::input('name', $form['name'], array('class' => 'form-control')).
    ((isset($errors['name_empty'])) ? '<span class="help-block">'.$errors['name_empty'].'</span>' : '').
    '</div>'.
    
    '<div class="form-group '.((isset($errors['email_empty'])) ? 'has-error' : '').'">'.
    Form::label('email', __('E-mail recipients, separated by commas', 'forms')).
    Form::input('email', $form['email'], array('class' => 'form-control')).
    ((isset($errors['email_empty'])) ? '<span class="help-block">'.$errors['email_empty'].'</span>' : '').
    '</div>'.
    
    '<div class="form-group '.((isset($errors['subject_empty'])) ? 'has-error' : '').'">'.
    Form::label('subject', __('Subject', 'forms')).
    Form::input('subject', $form['subject'], array('class' => 'form-control')).
    ((isset($errors['subject_empty'])) ? '<span class="help-block">'.$errors['subject_empty'].'</span>' : '').
    '</div>'.
    
    '<div class="form-group '.((isset($errors['button_empty'])) ? 'has-error' : '').'">'.
    Form::label('button', __('Button text', 'forms')).
    Form::input('button', $form['button'], array('class' => 'form-control')).
    ((isset($errors['button_empty'])) ? '<span class="help-block">'.$errors['button_empty'].'</span>' : '').
    '</div>'.
    
    '<div class="form-group '.((isset($errors['message_empty'])) ? 'has-error' : '').'">'.
    Form::label('message', __('Message', 'forms')).
    Form::textarea('message', $form['message'], array('class' => 'form-control')).
    ((isset($errors['message_empty'])) ? '<span class="help-block">'.$errors['message_empty'].'</span>' : '').
    '</div>'
);
?>

<br>

<div class="row">
    <div class="col-sm-2">
        <?php echo __('Output header', 'forms'); ?>:
        <label class="radio"><input type="radio" name="template" value="top" <?php if ($form['template']=='top') echo 'checked';?>> <?php echo __('Output header top', 'forms'); ?></label>
        <label class="radio"><input type="radio" name="template" value="left" <?php if ($form['template']=='left') echo 'checked';?>> <?php echo __('Output header left', 'forms'); ?></label>
        <label class="radio"><input type="radio" name="template" value="inside" <?php if ($form['template']=='inside') echo 'checked';?>> <?php echo __('Output header inside', 'forms'); ?></label>
    </div>
    <div class="col-sm-2">
        <?php echo __('Submit button', 'forms'); ?><br>
        <label class="radio"><input type="radio" name="align" value="left" <?php if ($form['align']=='left') echo 'checked';?>> <?php echo __('Left', 'forms'); ?></label>
        <label class="radio"><input type="radio" name="align" value="center" <?php if ($form['align']=='center') echo 'checked';?>> <?php echo __('Center', 'forms'); ?></label>
        <label class="radio"><input type="radio" name="align" value="right" <?php if ($form['align']=='right') echo 'checked';?>> <?php echo __('Right', 'forms'); ?></label>
    </div>
</div>

<br>

<input type="hidden" name="csrf" value="<?php echo Security::token(); ?>">

<?php if (isset($form['id'])) {?>
<input type="submit" class="btn btn-primary" name="submit_save_and_exit" value="<?php echo __('Save and Exit', 'forms'); ?>"> 
<?php } ?>

<input type="submit" class="btn btn-default" name="submit_save" value="<?php echo __('Save', 'forms'); ?>"> 

<label class="checkbox-inline <?php if (Option::get('captcha_installed') != 'true') echo 'hide'; ?>"><input type="checkbox" name="captcha" value="1" <?php if ($form['captcha']==1) echo 'checked';?>> <?php echo __('Use captcha', 'forms'); ?></label>

</form>