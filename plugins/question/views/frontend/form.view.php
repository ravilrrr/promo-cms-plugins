<h1><?php echo  __('Question', 'question'); ?></h1>

<div class="question-form">
    <?php if (Option::get('question_form') == 'hide') { $class = 'question-hide'; ?>
        <a href="#" class="question-show-form"><?php echo __('Add question', 'question');?></a>
    <?php } else { $class = ''; } ?>
    
    <form method="post" onSubmit="return questionAdd(this);" class="<?php echo $class;?>">
        <?php echo Form::hidden('csrf', Security::token()); ?>
        
        <?php echo __('Your name', 'question');?><br/>
        <input type="text" name="name" value=""/><br/>
        
        <?php echo __('Message', 'question');?><br/>
        <textarea name="message_question" required=""></textarea><br/>
        
        
        <?php if (Option::get('captcha_installed') == 'true') { ?>
            <?php echo __('Captcha', 'question'); ?><br/>
            <input type="text" name="captcha" required="" class="question-captcha"><?php CryptCaptcha::draw(); ?>
        <?php } ?>
        
        <div class="question-hide"><input type="text" name="spam_hello" value=""/></div>
        <input type="submit" data-loading-text="<?php echo __('Loading', 'question');?>" value="<?php echo __('Add question', 'question');?>"/>
    </form>
</div>