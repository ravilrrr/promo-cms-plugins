<?php
$img_download = ($album['resize'] == 'download') ? true : false;
$from_computer = (!Session::get('stock_from') or Session::get('stock_from') == 'computer' or $img_download) ? true : false;
$from_internet = (Session::get('stock_from') == 'internet' and !$from_computer) ? true : false;
?>

<h2><?php echo __('Edit image', 'stock');?></h2><br/>

<?php
echo (
    Form::open(null, array('class' => 'form-horizontal')).
    Form::hidden('csrf', Security::token())
);
?>
<div class="form-group">
    <label for="title" class="col-xs-3 control-label"><?php echo __('The photo title', 'stock'); ?></label>
    <div class="col-xs-9">
      <input type="text" class="form-control" name="title" id="title" value="<?php echo Html::chars($image['title']); ?>">
    </div>
</div>

<div class="form-group" <?php if($album['sortby'] != 'position') { ?>style="display:none"<?php } ?>>
    <label for="position" class="col-xs-3 control-label"><?php echo __('Position', 'stock'); ?></label>
    <div class="col-xs-9">
      <input type="text" class="form-control" name="position" id="position" value="<?php echo $image['pos']; ?>">
    </div>
</div>

<!-- Вывод дополнительных полей -->
<?php if (count($fields)>0) { ?>
    <?php foreach($fields as $field) { ?>
    <div class="form-group">
        <label for="field_<?php echo $field['slug']; ?>" class="col-xs-3 control-label"><?php echo $field['name']; ?></label>
        <div class="col-xs-9">
            <?php if ($field['type'] == 'textarea') { ?>
                <textarea class="form-control" name="fields[<?php echo $field['slug']; ?>]" id="field_<?php echo $field['slug']; ?>"><?php echo (empty($image[$field['slug']])) ? '' : Html::chars($image[$field['slug']]); ?></textarea>
            <?php } elseif ($field['type'] == 'link') { ?>
                <input type="text" class="form-control" value="<?php echo (empty($image[$field['slug']])) ? '' : Html::chars($image[$field['slug']]); ?>" name="fields[<?php echo $field['slug']; ?>]" id="field_<?php echo $field['slug']; ?>" placeholder="http://">
            <?php } else { ?>
                <input type="text" class="form-control" value="<?php echo (empty($image[$field['slug']])) ? '' : Html::chars($image[$field['slug']]); ?>" name="fields[<?php echo $field['slug']; ?>]" id="field_<?php echo $field['slug']; ?>">
            <?php } ?>
        </div>
    </div>
    <?php } ?>
<?php } ?>
<!-- // Закончили вывод дополнительных полей -->

<div class="form-group">
    <div class="col-xs-offset-3 col-xs-9">
        <?php echo Form::submit('submit_img_edit', __('Save', 'stock'), array('class' => 'btn btn-primary')); ?>
        <?php echo Form::submit('submit_img_edit_and_exit', __('Save and exit', 'stock'), array('class' => 'btn btn-default')); ?>
    </div>
</div>

</form>
<hr>

<?php if($photo_exists) { ?>

<a href="<?php echo $path_orig.$image['name'];?>" class="chocolat" rel="<?php echo $path_orig.$image['name'];?>" data-toggle="lightbox"><img src="<?php echo $path_thumb.$image['name'];?>" style="max-width:100px; max-height:50px;" alt=""/></a><br/>
<a href="index.php?id=stock&album_id=<?php echo $album['id'];?>&img_id=<?php echo $image['id'];?>&delete_img=<?php echo $image['name'];?>&onlyimg=true&token=<?php echo Security::token();?>"><?php echo __('Delete', 'stock');?></a>

<?php } else { ?>


<?php
echo (
    Form::open(null, array('enctype' => 'multipart/form-data', 'class' => 'form-horizontal')).
    Form::hidden('csrf', Security::token())
);
?>
<div class="form-group">
    <?php if (!$img_download && !$photo_exists) { ?>
    <div class="col-xs-3" style="text-align:right; padding-top:7px">
        <div class="btn-group" id="stock_upload_variant">
        <button type="button" onClick="return stockWay(this, 'computer');" class="btn btn-default btn-xs<?php if($from_computer) echo ' btn-success active';?>"><?php echo __('From the computer', 'stock');?></button>
        <button type="button" onClick="return stockWay(this, 'internet');" class="btn btn-default btn-xs<?php if($from_internet) echo ' btn-success active';?>"><?php echo __('From the Internet', 'stock');?></button>
        </div>
    </div>
    <?php } else { ?>
    <label for="file" class="col-xs-3 control-label"><?php echo __('Image', 'stock'); ?></label>
    <?php } ?>
    <div class="col-xs-9">
        <div class="stock_from_computer" style="<?php echo (($from_computer) ? '' : 'display:none'); ?>">
            <?php
            echo '<div style="float:left; margin-right:15px">';
                echo Form::label('file', __('Upload photo', 'stock'));
                echo Form::input('file', null, array('type' => 'file', 'size' => '25'));
            echo '</div>';
            
            if ($img_download) {
                echo '<div style="float:left;">';
                    echo Form::label('file_small', __('Thumbnails', 'stock'));
                    echo Form::input('file_small', null, array('type' => 'file', 'size' => '25'));
                echo '</div>';
            }
            ?>
        </div>
        <div class="stock_from_internet" style="<?php echo (($from_internet) ? '' : 'display:none'); ?>">
            <?php echo Form::input('file_link', '', array('class'=>'form-control', "placeholder"=>"http://")); ?>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="col-xs-offset-3 col-xs-9">
        <?php
        echo (
            Form::submit('upload_file', __('Upload', 'stock'), array('class' => 'btn btn-primary')).
            Form::hidden('csrf', Security::token()).
            Form::hidden('photo_edit', $image['id']).
            Form::hidden('album_id', $album['id']).
            
            Form::close()
        );
        ?>
    </div>
</div>
<?php } ?>