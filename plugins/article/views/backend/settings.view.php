<h2 class="margin-bottom-1"><?php echo __('Settings article', 'article');?></h2>

<?php
    echo (
        Form::open(null).
        Form::hidden('csrf', Security::token())
    );
?>

<div class="form-group">
<?php
    echo (
        Form::label('limit', __('Article per page (website)', 'article')).
        Form::input('limit', Option::get('article_limit'), array('class' => 'form-control'))
    );
?>
</div>
<div class="form-group">
<?php
    echo (
        Form::label('limit_admin', __('Article per page (admin)', 'article')).
        Form::input('limit_admin', Option::get('article_limit_admin'), array('class' => 'form-control'))
    );
?>
</div>
<div class="form-group">
<?php
    echo (
        Form::label('width', __('Width image', 'article')).
        Form::input('width', Option::get('article_w'), array('class' => 'form-control'))
    );
?>
</div>

<?php
    echo (
        Form::submit('article_submit_settings', __('Save', 'article'), array('class' => 'btn btn-primary')).Html::Nbsp(2).
        Form::submit('article_submit_settings_cancel', __('Cancel', 'article'), array('class' => 'btn btn-default')).
        Form::close()
    );
?>

<br/><h2><?php echo __('Image default', 'article');?></h2>

<?php      
echo (
    Form::open(null, array('enctype' => 'multipart/form-data')).
    Form::hidden('csrf', Security::token()).
    Form::input('file', null, array('type' => 'file', 'size' => '25')).Html::br().
    Form::submit('article_submit_image', __('Upload', 'article'), array('class' => 'btn btn-default')).
    Form::close()
);
if(file_exists($imgdefault)) {
    echo '<a href="'.$imgdefault_url.'"><img src="'.$imgdefault_url.'" alt="" style="height:50px;"/></a><br/>';
    echo '<a href="index.php?id=article&action=settings&delete_img=default&token='.Security::token().'" onClick="return confirmDelete(\''.__('Delete image', 'article').'\');">'.__('Delete', 'article').'</a>';
}
?>