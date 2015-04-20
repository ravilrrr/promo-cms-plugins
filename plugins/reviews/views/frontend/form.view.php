<h1><?php echo  __('Reviews', 'reviews'); ?></h1>

<div class="reviews-form">
    <?php if (Option::get('reviews_form') == 'hide') { $class = 'reviews-hide'; ?>
        <a href="#" class="reviews-show-form"><?php echo __('Add reviews', 'reviews');?></a>
    <?php } else { $class = ''; } ?>
    
    <form method="post" onSubmit="return reviewsAdd(this);" class="<?php echo $class;?>">
        <?php echo Form::hidden('csrf', Security::token()); ?>
        
        <?php echo __('Your name', 'reviews');?><br/>
        <input type="text" name="name" value=""/><br/>
        
        <?php echo __('Message', 'reviews');?><br/>
        <textarea name="message_reviews" required=""></textarea><br/>
        
        
        <?php if (Option::get('captcha_installed') == 'true') { ?>
            <?php echo __('Captcha', 'reviews'); ?><br/>
            <input type="text" name="captcha" required="" class="reviews-captcha"><?php CryptCaptcha::draw(); ?>
        <?php } ?>
        
        <div class="reviews-hide"><input type="text" name="spam_hello" value=""/></div>
        <input type="submit" data-loading-text="<?php echo __('Loading', 'reviews');?>" value="<?php echo __('Add reviews', 'reviews');?>"/>
    </form>
</div>