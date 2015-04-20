<h2><?php echo $form['name']; ?></h2><br>

<form method="post">
    
    <div class="form-group <?php if (isset($errors['title_empty'])) echo 'has-error'; ?>">
        <label><?php echo __('Title', 'forms'); ?></label>
        <input type="text" name="title" value="<?php echo $element['title']; ?>" class="form-control">
        <?php if (isset($errors['title_empty'])) echo '<span class="help-block">'.$errors['title_empty'].'</span>'; ?>
    </div>
    
    <div class="form-group">
        <label><?php echo __('Comment', 'forms'); ?></label>
        <input type="text" name="comment" value="<?php echo $element['comment']; ?>" class="form-control">
    </div>

    <?php
    if (in_array($element['type'], array('text', 'textarea', 'select', 'email', 'name', 'tel'))) {
        echo (
            '<div class="form-group">'.
            Form::label('width', __('Width', 'forms')).
            Form::select('width', array(25=>'25%',50=>'50%',75=>'75%',100=>'100%'), $element['width'], array('class'=>'form-control')).
            '</div>'
        );
    }
    ?>
    
    
    <?php 
    if (in_array($element['type'], array('checkbox', 'radio', 'select'))) {
        echo (
            '<div class="form-group '.((isset($errors['values_empty'])) ? 'has-error' : '').'">'.
                Form::label('values', __('Values', 'forms')).
                Form::textarea('values', $element['values'], array('class' => 'form-control', 'rows' => 4)).
                ((isset($errors['values_empty'])) ? '<span class="help-block">'.$errors['values_empty'].'</span>' : '').
            '</div>'
        );
    }
    ?>
    

    <?php
    echo (
        Form::hidden('csrf', Security::token()).
        Form::submit('submit_save', __('Save and Exit', 'forms'), array('class' => 'btn btn-primary'))
    );
    ?>
    
    
    <?php if ($element['type'] != 'subtitle') { ?>
    <label class="checkbox-inline">
        <input type="checkbox" name="required" value="1" <?php if ($element['required'] == 'yes') echo 'checked'; ?>> 
        <?php echo __('Required', 'forms'); ?>
    </label>
    <?php } ?>
    
</form>