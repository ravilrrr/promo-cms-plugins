<h2 class="margin-bottom-1"><?php echo __('New article', 'article'); ?></h2>

<?php
    echo (
        Form::open(null, array('enctype' => 'multipart/form-data')).
        Form::hidden('csrf', Security::token())
    );
?>

<ul class="nav nav-tabs">
    <li <?php if (Notification::get('article')) { ?>class="active"<?php } ?>><a href="#article" data-toggle="tab"><?php echo __('Caption', 'article'); ?></a></li>
    <li <?php if (Notification::get('seo')) { ?>class="active"<?php } ?>><a href="#seo" data-toggle="tab"><?php echo __('SEO', 'article'); ?></a></li>
    <li <?php if (Notification::get('settings')) { ?>class="active"<?php } ?>><a href="#settings" data-toggle="tab"><?php echo __('Settings', 'article'); ?></a></li>
</ul>

<div class="tab-content tab-page margin-bottom-1">
    <div class="tab-pane <?php if (Notification::get('article')) { ?>active<?php } ?>" id="article">
        <div class="form-group">
        <?php
            echo (
                Form::label('article_name', __('Name', 'pages')).
                Form::input('article_name', $post_name, array('class' => (isset($errors['article_empty_name'])) ? 'form-control error-field' : 'form-control'))
            );
            if (isset($errors['article_empty_name'])) echo Html::nbsp(3).'<span class="error-message">'.$errors['article_empty_name'].'</span>';
        ?>
        </div>
    </div>
    <div class="tab-pane <?php if (Notification::get('seo')) { ?>active<?php } ?>" id="seo">
        <div class="form-group">
        <?php
            echo (
                Form::label('article_title', __('Title', 'article')).
                Form::input('article_title', $post_title, array('class' => 'form-control'))
            );
        ?>
        </div>
        <div class="form-group">
        <?php
            echo (
                Form::label('article_h1', __('H1', 'article')).
                Form::input('article_h1', $post_h1, array('class' => 'form-control'))
            );
        ?>
        </div>
        <div class="form-group">
        <?php
            echo (
                Form::label('article_slug', __('Alias (slug)', 'article')).
                Form::input('article_slug', $post_slug, array('class' => 'form-control'))
            );
        ?>
        </div>
        <div class="form-group">
        <?php
            echo (
                Form::label('article_keywords', __('Keywords', 'article')).
                Form::input('article_keywords', $post_keywords, array('class' => 'form-control'))
            );
        ?>
        </div>
        <div class="form-group">
        <?php
            echo (
                Form::label('article_description', __('Description', 'article')).
                Form::input('article_description', $post_description, array('class' => 'form-control'))
            );
        ?>
        </div>
    </div>
    <div class="tab-pane <?php if (Notification::get('settings')) { ?>active<?php } ?>" id="settings">
        <div class="form-group">
            <?php
                echo (
                    Form::label('status', __('Status', 'article')).
                    Form::select('status', $status_array, 'published', array('class' => 'form-control'))
                );
            ?>
        </div>
        <div class="form-group">
            <?php 
                echo (
                    Form::label('file', __('Image', 'article')).
                    Form::input('file', null, array('type' => 'file', 'size' => '25'))
                );
            ?>
        </div>
    </div>
</div>

<div class="row margin-bottom-1">
    <div class="col-xs-12">
        <?php Action::run('admin_editor', array(Html::toText($post_content))); ?>
    </div>
</div>
        
<div class="row margin-top-1">
    <div class="col-sm-6">
        <?php
            echo (
                Form::submit('add_article_and_exit', __('Save and exit', 'article'), array('class' => 'btn btn-primary')).Html::nbsp(2).
                Form::submit('add_article', __('Save', 'article'), array('class' => 'btn btn-primary')).Html::nbsp(2).
                Html::anchor(__('Cancel', 'article'), 'index.php?id=article', array('title' => __('Cancel', 'article'), 'class' => 'btn btn-phone btn-default'))
            );
        ?>
    </div>
    <div class="col-sm-6 visible-sm visible-md visible-lg">
        <div class="pull-right">
            <div class="input-group datapicker">
                <?php echo Form::input('article_date', $date, array('class' => 'form-control')); ?>
                <span class="input-group-addon add-on">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>            
            </div>           
        </div>
        <?php echo Form::close(); ?>
    </div>
</div>