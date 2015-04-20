<h1><?php echo  __('Guestbook', 'guestbook'); ?></h1>

<div class="guestbook-form">
    <?php if (Option::get('guestbook_form') == 'hide') { $class = 'guestbook-hide'; ?>
        <a href="#" class="guestbook-show-form"><?php echo __('Add guestbook', 'guestbook');?></a>
    <?php } else { $class = ''; } ?>
    
    <form method="post" onSubmit="return guestbookAdd(this);" class="<?php echo $class;?>">
        <?php echo Form::hidden('csrf', Security::token()); ?>
        
        <?php echo __('Your name', 'guestbook');?><br/>
        <input type="text" name="name" value=""/><br/>
        
        <?php echo __('Message', 'guestbook');?><br/>
        <textarea name="message_guestbook" required=""></textarea><br/>
        
        
        <?php if (Option::get('captcha_installed') == 'true') { ?>
            <?php echo __('Captcha', 'guestbook'); ?><br/>
            <input type="text" name="captcha" required="" class="guestbook-captcha"><?php CryptCaptcha::draw(); ?>
        <?php } ?>
        
        <div class="guestbook-hide"><input type="text" name="spam_hello" value=""/></div>
        <input type="submit" data-loading-text="<?php echo __('Loading', 'guestbook');?>" value="<?php echo __('Add guestbook', 'guestbook');?>"/>
    </form>
</div>