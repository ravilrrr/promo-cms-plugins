<?php
$img_download = ($album['resize'] == 'download') ? true : false;
$from_computer = (!Session::get('stock_from') or Session::get('stock_from') == 'computer' or $img_download) ? true : false;
$from_internet = (Session::get('stock_from') == 'internet' and !$from_computer) ? true : false;
?>
    
<h2><?php echo __('Album', 'stock').': "'.$album['name'].'"';?></h2><br/>

<ul class="nav nav-tabs">
    <li <?php if (Notification::get('upload')) { ?>class="active"<?php } ?>><a href="#upload" data-toggle="tab"><?php echo __('Upload photo', 'stock'); ?></a></li>
    <li <?php if (Notification::get('edit')) { ?>class="active"<?php } ?>><a href="#edit" data-toggle="tab"><?php echo __('Edit', 'stock'); ?></a></li>
    <li <?php if (Notification::get('fields')) { ?>class="active"<?php } ?>><a href="#fields" data-toggle="tab"><?php echo __('Fields', 'stock'); ?></a></li>
    <?php if (!$img_download):?>
    <li <?php if (Notification::get('resize')) { ?>class="active"<?php } ?>><a href="#resize" data-toggle="tab"><?php echo __('Resize', 'stock'); ?></a></li>
    <?php endif;?>
    <li <?php if (Notification::get('delete')) { ?>class="active"<?php } ?>><a href="#delete" data-toggle="tab"><?php echo __('Delete', 'stock'); ?></a></li>
</ul>

<div class="tab-content tab-page">
    
    <div class="tab-pane <?php if (Notification::get('upload')) { ?>active<?php } ?>" id="upload">
        
        <?php
        echo (
            Form::open(null, array('enctype' => 'multipart/form-data', 'class' => 'form-horizontal')).
            Form::hidden('csrf', Security::token()).
            Form::hidden('album_id', $album['id'])
        );
        ?>
        <div class="form-group">
            <label for="title" class="col-xs-3 control-label"><?php echo __('The photo title', 'stock'); ?></label>
            <div class="col-xs-9">
              <input type="text" class="form-control" name="title" id="title">
            </div>
        </div>
        
        <div class="form-group" <?php if($album['sortby'] != 'position') { ?>style="display:none"<?php } ?>>
            <label for="position" class="col-xs-3 control-label"><?php echo __('Position', 'stock'); ?></label>
            <div class="col-xs-9">
              <input type="text" class="form-control" name="position" value="<?php echo $max_position; ?>" id="position">
            </div>
        </div>
        
        <div class="form-group">
            <?php if (!$img_download) {?>
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
        
        <!-- Вывод дополнительных полей -->
        <?php if (count($fields)>0) { ?>
            <hr>
            <?php foreach($fields as $field) { ?>
            <div class="form-group">
                <label for="field_<?php echo $field['slug']; ?>" class="col-xs-3 control-label"><?php echo $field['name']; ?></label>
                <div class="col-xs-9">
                    <?php if ($field['type'] == 'textarea') { ?>
                        <textarea class="form-control" name="fields[<?php echo $field['slug']; ?>]" id="field_<?php echo $field['slug']; ?>"></textarea>
                    <?php } elseif ($field['type'] == 'link') { ?>
                        <input type="text" class="form-control" name="fields[<?php echo $field['slug']; ?>]" id="field_<?php echo $field['slug']; ?>" placeholder="http://">
                    <?php } else { ?>
                        <input type="text" class="form-control" name="fields[<?php echo $field['slug']; ?>]" id="field_<?php echo $field['slug']; ?>">
                    <?php } ?>
                </div>
            </div>
            <?php } ?>
        <?php } ?>
        <!-- // Закончили вывод дополнительных полей -->
        
        <div class="form-group">
            <div class="col-xs-offset-3 col-xs-9">
                <?php echo Form::submit('upload_file', __('Upload', 'stock'), array('class' => 'btn btn-primary')); ?>
            </div>
        </div>
        
        </form>
    </div>
    <div class="tab-pane <?php if (Notification::get('edit')) { ?>active<?php } ?>" id="edit">
        
        <!-- edit form -->
        <form onSubmit="return stAlbumEditSave(this);" method="post">
            <div style="width:500px; overflow:hidden">
            
                <!-- name -->
                <div class="row">
                    <div class="col-xs-12">
                        <label for="name"><?php echo __('The album title', 'stock'); ?></label>
                        <input type="text" id="name" name="name" value="<?php echo Html::chars($album['name']); ?>" class="form-control">
                    </div>
                </div>
                
                <!-- width and height thumbnails -->
                <div class="row">
                    <div class="col-xs-6">
                        <label for="width_thumb"><?php echo __('Width thumbnails (px)', 'stock'); ?></label>
                        <input type="text" id="width_thumb" name="width_thumb" value="<?php echo $album['w']; ?>" class="form-control">
                    </div>
                    <div class="col-xs-6">
                        <label for="height_thumb"><?php echo __('Height thumbnails (px)', 'stock'); ?></label>
                        <input type="text" id="height_thumb" name="height_thumb" value="<?php echo $album['h']; ?>" class="form-control">
                    </div>
                </div>
                
                <!-- original width and height -->
                <div class="row">
                    <div class="col-xs-6">
                        <label for="width_orig"><?php echo __('Original width (px, max)', 'stock'); ?></label>
                        <input type="text" id="width_orig" name="width_orig" value="<?php echo $album['wmax']; ?>" class="form-control">
                    </div>
                    <div class="col-xs-6">
                        <label for="height_orig"><?php echo __('Original height (px, max)', 'stock'); ?></label>
                        <input type="text" id="height_orig" name="height_orig" value="<?php echo $album['hmax']; ?>" class="form-control">
                    </div>
                </div>
                
                <!-- quality and resize -->
                <div class="row">
                    <div class="col-xs-6">
                        <label for="quality"><?php echo __('Quality', 'stock'); ?></label>
                        <input type="text" id="quality" name="quality" value="<?php echo $album['quality']; ?>" class="form-control">
                    </div>
                    <div class="col-xs-6">
                        <label for="resize_way"><?php echo __('Resize way', 'stock'); ?></label>
                        <?php echo Form::select('resize_way', $resize_way, $album['resize'], array('class'=>'form-control')); ?>
                    </div>
                </div>
                
                <!-- sort and order -->
                <div class="row">
                    <div class="col-xs-6">
                        <label for="sort_by"><?php echo __('Sort by', 'stock'); ?></label>
                        <?php echo Form::select('sort_by', $sort_by, $album['sortby'], array('class'=>'form-control')); ?>
                    </div>
                    <div class="col-xs-6">
                        <label for="order"><?php echo __('order', 'stock'); ?></label>
                        <?php echo Form::select('order', $order, $album['order'], array('class'=>'form-control')); ?>
                    </div>
                </div>
                
                <!-- template -->
                <div class="row">
                    <div class="col-xs-12">
                        <label for="template"><?php echo __('Template', 'stock'); ?> <a href="#" data-toggle="modal" data-target="#templateHelp"><i class="glyphicon glyphicon-info-sign"></i></a></label>
                        <div class="input-group">
                            <input type="text" id="template" name="template" value="<?php echo $album['template']; ?>" class="form-control">
                            <div class="input-group-addon">.view.php</div>
                        </div>
                    </div>
                </div>
                
                <br clear="both">
                
                <?php 
                echo (
                    Form::hidden('csrf', Security::token()).
                    Form::hidden('album_id', $album['id']).
                    Form::hidden('stock_submit_album_edit', true).
                    Form::hidden('siteurl',Option::get('siteurl')).

                    Form::submit('submit_settings', __('Save', 'stock'), array('class' => 'btn btn-primary')).
                    
                    '&nbsp; <span id="st-edit-result"></span>'.
                    Form::close()
                );
                ?>  

            </div>
        </form>
        
    </div>
    
    <div class="tab-pane <?php if (Notification::get('fields')) { ?>active<?php } ?>" id="fields">
        
        <!-- Вывод дополнительных полей -->
        <?php if (count($fields)>0) { ?>
            <table class="table">
                <tbody>
                    <?php foreach($fields as $field) { ?>
                    <tr>
                        <td><strong><?php echo $field['name']; ?></strong></td>
                        <td><?php echo $field['slug']; ?></td>
                        <td><?php echo (empty($field['type'])) ? '' : $fields_type[$field['type']]; ?></td>
                        <td><?php echo (empty($field['pos'])) ? '' : $field['pos']; ?></td>
                        <td>
                            <?php echo Html::anchor('<i class="glyphicon glyphicon-trash"></i> '.__('Delete', 'stock'), 'index.php?id=stock&album_id='.$album['id'].'&field_id_delete='.$field['id'].'&token='.Security::token(), array('class' => 'btn btn-xs btn-danger', 'onClick'=>'return confirmDelete(\''.__('sure field', 'stock').'\')')); ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } ?>
        <!-- // Закончили вывод дополнительных полей -->
        
        <form method="post" class="form-inline">
            <?php echo Form::hidden('csrf', Security::token()); ?>
            <?php echo Form::hidden('album_id', $album['id']); ?>
            <input type="text" class="form-control" name="fields_name" placeholder="<?php echo __('Enter the name of the field', 'stock'); ?>"> 
            <input type="text" class="form-control" name="fields_slug" placeholder="<?php echo __('Enter the slug', 'stock'); ?>"> 
            <input type="hidden" class="form-control" name="fields_pos" value="0" placeholder="<?php echo __('Position', 'stock'); ?>"> 
            <?php echo Form::select('fields_type', $fields_type, null, array('class'=>'form-control')); ?>
            <?php echo Form::submit('submit_field_add', __('Add Field', 'stock'), array('class' => 'btn btn-primary')); ?>  
        </form>
    </div>
    
    <div class="tab-pane <?php if (Notification::get('resize')) { ?>active<?php } ?>" id="resize">
        <?php 
            echo __('Resize content', 'stock').Html::Br(2);
            echo Html::anchor(__('Resize start', 'stock'), '#',
           array('class' => 'btn btn-primary btn-actions', 'onclick' => "return stResize(".$album['id'].", '".Option::get('siteurl')."');"));
        ?> 
        &nbsp;<span id="st_result"></span>
    </div>
    
    <div class="tab-pane <?php if (Notification::get('delete')) { ?>active<?php } ?>" id="delete">
        <?php echo __('sure album', 'stock');?><br/><br/>
        <a class="btn btn-danger btn-actions" href="index.php?id=stock&delete_album=<?php echo $album['id'];?>&token=<?php echo Security::token();?>"><i class="glyphicon glyphicon-trash"></i> <?php echo __('Delete album', 'stock');?></a>
        <br clear="both"/>
    </div>
</div>
<br/>

<table class="table table-bordered">
    <thead>
        <tr>
            <th><?php echo __('Photos', 'stock'); ?></th>
            <th><?php echo __('Title', 'stock'); ?></th>
            <?php if (count($fields) > 0) { ?><th><?php echo __('Fields', 'stock');?></th><?php } ?>
            <?php if($album['sortby'] == 'position') { ?><th><?php echo __('Position', 'stock'); ?></th><?php } ?>
            <th width="30%"><?php echo __('Actions', 'stock'); ?></th>
        </tr>
    </thead>
    <tbody>
    <?php if (count($files) > 0) foreach ($files as $img) { ?>
    <tr>
        <td><a href="<?php echo $path_orig.$img['name'];?>" class="chocolat" rel="<?php echo $path_orig.$img['name'];?>" data-toggle="lightbox"><img src="<?php echo $path_thumb.$img['name'];?>" style="max-width:100px; max-height:50px;" alt=""/></a></td>
        <td><?php echo $img['title']; ?></td>
        <?php if (count($fields) > 0) { ?>
        <td><dl class="dl-horizontal">
            <?php 
            foreach($fields as $field) {
                echo '<dt>'.$field['name'].'</dt><dd>&nbsp;';
                if (isset($img[$field['slug']])) {
                    if ($field['type'] == 'link') {
                        echo '<a href="'.$img[$field['slug']].'" target="_blank">'.$img[$field['slug']].'</a>';
                    } else {
                        echo $img[$field['slug']];
                    }
                }
                echo '</dd>';
            }
            ?>
            </dl>
        </td>
        <?php } ?>
        <?php if($album['sortby'] == 'position') { ?><td><?php echo $img['pos']; ?></td><?php } ?>
        <td>

        <div class="btn-toolbar">
            <div class="btn-group">
                <?php echo Html::anchor('<i class="glyphicon glyphicon-pencil"></i> '.__('Edit', 'stock'), 'index.php?id=stock&action=img_edit&img_id='.$img['id'].'&album_id='.$album['id'], array('class' => 'btn btn-xs btn-primary')); ?>
                <a class="btn dropdown-toggle btn-xs btn-primary" data-toggle="dropdown" href="#" style="font-family:arial;"><span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><?php echo Html::anchor(__('View Embed Code', 'stock'), 'javascript:;', array('title' => __('View Embed Code', 'stock'), 'onclick' => '$.promo.stock.showEmbedCodes("'.$img['name'].'", "'.$album['id'].'");')); ?></li>
                </ul> 
            </div>
            <?php echo Html::anchor('<i class="glyphicon glyphicon-trash"></i> '.__('Delete', 'stock'), 'index.php?id=stock&album_id='.$album['id'].'&img_id='.$img['id'].'&delete_img='.$img['name'].'&token='.Security::token(), array('class' => 'btn btn-xs btn-danger', 'onClick'=>'return confirmDelete(\''.__('sure', 'stock').'\')')); ?>
        </div>
   
        </td>
    </tr>
    <?php } ?>
    </tbody>
</table>

<div class="modal fade" id="embedCodes"> 
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="close" data-dismiss="modal">&times;</div>
                <h4 class="modal-title"><?php echo __('Embed Code', 'stock'); ?></h4>
            </div>
            <div class="modal-body">
                <b><?php echo __('Shortcode', 'stock'); ?></b><br>
                <input class="form-control input-sm" onclick="this.select();" id="shortcode"><br>
                
                <b><?php echo __('PHP', 'stock'); ?></b><br>
                <input class="form-control input-sm" onclick="this.select();" id="phpcode">
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="templateHelp"> 
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="close" data-dismiss="modal">&times;</div>
                <h4 class="modal-title">Шаблон-отображение для альбома</h4>
            </div>
            <div class="modal-body">
                <p>Если оставить поле "Шаблон" пустым, то по умолчанию будет установлен шаблон <strong>default.view.php</strong></p>
                <p>Шаблоны находятся в папке <strong>/plugins/stock/views/frontend/</strong></p>
                <p>Вы можете выбрать любой из заранее созданных шаблонов: <b>default, fields</b></p>
                <p>Для создания собственного шаблона, необходимо создать шаблон в папке <b>/public/themes/{тема_оформления}/stock/views/frontend/{шаблон}.view.php</b></p>
            </div>
        </div>
    </div>
</div>