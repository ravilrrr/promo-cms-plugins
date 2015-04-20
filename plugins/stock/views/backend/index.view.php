<div class="vertical-align margin-bottom-1">
    <div class="text-left row-phone">
        <h2><?php echo __('Stock', 'stock');?></h2>
    </div>
    <div class="text-right row-phone">
        <a class="btn btn-primary" href="index.php?id=stock&action=addalbum"><i class="glyphicon glyphicon-plus"></i> <?php echo __('Add album', 'stock');?></a>
    </div>
</div>

<table class="table table-bordered">
    <thead>
        <tr>
            <th><?php echo __('Albums', 'stock'); ?></th>
            <th width="200"><?php echo __('Shortcode', 'stock'); ?></th>
            <th><?php echo __('Size', 'stock'); ?></th>
            <th nowrap><?php echo __('Actions', 'stock'); ?></th>
        </tr>
    </thead>
    <tbody>
    <?php if (count($records) > 0) foreach ($records as $row) { ?>
    <tr>
        <td><?php echo $row['name']; ?></td>
        <td><input type="text" value='{stock album="<?php echo $row['id']; ?>"}' onclick="this.select();" class="form-control input-sm"></td>
        <td><?php echo $row['w'].'x'.$row['h']; ?></td>        
        <td>
            <div class="btn-group">
                <?php echo Html::anchor('<i class="glyphicon glyphicon-pencil"></i> '.__('Edit', 'stock'), 'index.php?id=stock&album_id='.$row['id'], array('class' => 'btn btn-xs btn-primary')); ?>
            
                <a class="btn dropdown-toggle btn-xs btn-primary" data-toggle="dropdown" href="#" style="font-family:arial;"><span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><?php echo Html::anchor(__('View Embed Code', 'stock'), 'javascript:;', array('title' => __('View Embed Code', 'stock'), 'onclick' => '$.promo.stock.showEmbedCodesAlbum('.$row['id'].');')); ?></li>
                </ul> 
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
                <h5><?php echo __('For pages and blocks', 'stock'); ?></h5>
    
                <dl class="dl-horizontal">
                    <dt><?php echo __('Display all images', 'stock');?></dt>
                    <dd><input class="form-control input-sm" onclick="this.select();" id="shortcode"></dd>
        
                    <dt><?php echo __('Display random image', 'stock');?></dt>
                    <dd><input class="form-control input-sm" onclick="this.select();" id="shortcode-random"></dd>
        
                    <dt><?php echo __('Display last image', 'stock');?></dt>
                    <dd><input class="form-control input-sm" onclick="this.select();" id="shortcode-last"></dd>
        
                    <dt><?php echo __('Display last 3 images', 'stock');?></dt>
                    <dd><input class="form-control input-sm" onclick="this.select();" id="shortcode-last3"></dd>
                </dl>
    
                <h5><?php echo __('The template for a', 'stock'); ?></h5>
    
                <dl class="dl-horizontal">
                    <dt><?php echo __('Display all images', 'stock');?></dt>
                    <dd><input class="form-control input-sm" onclick="this.select();" id="phpcode"></dd>
        
                    <dt><?php echo __('Display random image', 'stock');?></dt>
                    <dd><input class="form-control input-sm" onclick="this.select();" id="phpcode-random"></dd>
        
                    <dt><?php echo __('Display last image', 'stock');?></dt>
                    <dd><input class="form-control input-sm" onclick="this.select();" id="phpcode-last"></dd>
        
                    <dt><?php echo __('Display last 3 images', 'stock');?></dt>
                    <dd><input class="form-control input-sm" onclick="this.select();" id="phpcode-last3"></dd>
                </dl>
            </div>
        </div>
    </div>
</div>