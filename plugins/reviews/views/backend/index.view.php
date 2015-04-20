<div class="vertical-align margin-bottom-1">
    <div class="text-left row-phone">
        <h2><?php echo __('Reviews', 'reviews').' <small><sup>'.Html::anchor('<i class="glyphicon glyphicon-new-window"></i>', Site::url().'/reviews', array('target' => '_blank')).'</sup></small>';?></h2>
    </div>
    <div class="text-right row-phone">
        <div class="btn-group">
            <a class="btn btn-default" href="index.php?id=reviews&action=settings"><i class="glyphicon glyphicon-cog"></i> <?php echo __('Settings', 'reviews');?></a> 
            <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li><a href="#reviewsCodeModal" role="button" data-toggle="modal"><?php echo __('View Embed Code', 'reviews');?></a></li>
            </ul>
        </div>
    </div>
</div>

<form method="post" onSubmit="">
<table class="table table-bordered">
    <thead>
        <tr>
            <th width="20">&nbsp;</th>
            <th width="80"><?php echo __('Date', 'reviews');?></th>
            <th width="130"><?php echo __('Name', 'reviews');?></th>
            <th><?php echo __('Message', 'reviews'); ?></th>
            <?php if (Option::get('reviews_check') == 'yes') { ?>
                <th>&nbsp;</th>
            <?php } ?>
            <th width="50"><?php echo __('Important', 'reviews');?></th>
            <th width="210"><?php echo __('Actions', 'reviews'); ?></th>
        </tr>
    </thead>
    <?php if (count($records) > 0): ?>
    <tbody>
        <?php foreach ($records as $row):?>
        <tr <?php if (Option::get('reviews_check') == 'yes' and $row['check'] == 0) echo 'class="warning"'; ?>>
            <td><input type="checkbox" name="reviews_delete[]" class="reviews-delete" value="<?php echo $row['id'];?>"/></td>
            <td><?php echo Reviews::getdate($row['date']); ?></td>
            <td><?php echo $row['name']; ?></td>
            <td>
                <?php echo $row['message'];?>
                <?php if ($row['answer'] != '') echo '<blockquote style="margin-bottom:0;"><small>'.$row['answer'].'</small></blockquote>';?>
            </td>
            <?php if (Option::get('reviews_check') == 'yes') { ?>
                <td>
                <?php if ($row['check'] == 0) { ?>
                    <a class="btn btn-xs btn-danger reviews-check" href="#" data-id="<?php echo $row['id'];?>" title="<?php echo __('Confirmed', 'reviews');?>"><i class="glyphicon glyphicon-thumbs-up"></i></a>
                <?php } ?>
                </td>
            <?php } ?>
            <td><a class="btn btn-xs <?php if ($row['important']) echo 'btn-success'; else echo 'btn-default'; ?> reviews-impotant-check" href="#" data-id="<?php echo $row['id'];?>"><i class="glyphicon glyphicon-ok"></i></a></td>
            <td>
                <?php echo Html::anchor('<i class="glyphicon glyphicon-pencil"></i> '.__('Edit', 'reviews'), 'index.php?id=reviews&row_id='.$row['id'].'&action=edit', array('class' => 'btn btn-xs btn-primary')); ?>
                <?php echo Html::anchor('<i class="glyphicon glyphicon-trash"></i> '.__('Delete', 'reviews'), 'index.php?id=reviews&row_id='.$row['id'].'&action=delete&page='.Request::get('page').'&token='.Security::token(), array('class' => 'btn btn-xs btn-danger', 'onClick'=>'return confirmDelete(\''.__('Sure you want to remove the :item', 'reviews', array(':item'=>$row['name'])).'\')')); ?>
            </td>
        </tr>
        <?php endforeach;?>
    </tbody> 
    <?php endif; ?>
</table>
<?php echo Form::hidden('csrf', Security::token());?>
<input type="checkbox" class="check-all" style="margin-left:16px; margin-right:13px;"/>
<input type="submit" name="submit_delete_reviews" class="btn btn-default delete-reviews-button disabled" disabled="disabled" value="<?php echo __('Delete checked', 'reviews');?>" onClick="return confirmDelete('<?php echo __('Sure you want to delete all the selected records', 'reviews');?>');"/>
</form>

<div class="reviews-paginator"><?php Reviews::paginator($current_page, $pages_count, 'index.php?id=reviews&page=');?></div>

<div class="modal fade" id="reviewsCodeModal"> 
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="close" data-dismiss="modal">&times;</div>
                <h4 class="modal-title"><?php echo __('Embed Code', 'reviews'); ?></h4>
            </div>
            <div class="modal-body">
                <h5><?php echo __('PHP Code', 'reviews');?></h5>
        
                <dl class="dl-horizontal">
                    <dt><?php echo __('Count reviews', 'reviews');?></dt>
                    <dd><input type="text" class="form-control input-sm" onclick="this.select()" value="&lt;?php echo Reviews::count(); ?&gt;"></dd>

                    <dt><?php echo __('Random 3', 'reviews');?></dt>
                    <dd><input type="text" class="form-control input-sm" onclick="this.select()" value="&lt;?php echo Reviews::show('random', 3); ?&gt;"></dd>
            
                    <dt><?php echo __('Last 3 important', 'reviews');?></dt>
                    <dd><input type="text" class="form-control input-sm" onclick="this.select()" value="&lt;?php echo Reviews::show('last', 3, 'important'); ?&gt;"></dd>
                </dl>
    
                <h5><?php echo __('Shortcode', 'reviews');?></h5>
        
                <dl class="dl-horizontal">
                    <dt><?php echo __('Count reviews', 'reviews');?></dt>
                    <dd><input type="text" class="form-control input-sm" onclick="this.select()" value='{reviews show="count"}'></dd>

                    <dt><?php echo __('Random 3', 'reviews');?></dt>
                    <dd><input type="text" class="form-control input-sm" onclick="this.select()" value='{reviews show="random" count="3"}'></dd>
            
                    <dt><?php echo __('Last 3 important', 'reviews');?></dt>
                    <dd><input type="text" class="form-control input-sm" onclick="this.select()" value='{reviews show="last" count="3" label="important"}'></dd>
                </dl>
            </div>
        </div>
    </div>
</div>