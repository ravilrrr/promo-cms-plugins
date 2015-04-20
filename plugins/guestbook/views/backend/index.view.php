<div class="vertical-align margin-bottom-1">
    <div class="text-left row-phone">
        <h2><?php echo __('Guestbook', 'guestbook').' <small><sup>'.Html::anchor('<i class="glyphicon glyphicon-new-window"></i>', Site::url().'/guestbook', array('target' => '_blank')).'</sup></small>';?></h2>
    </div>
    <div class="text-right row-phone">
        <div class="btn-group">
            <a class="btn btn-default" href="index.php?id=guestbook&action=settings"><i class="glyphicon glyphicon-cog"></i> <?php echo __('Settings', 'guestbook');?></a> 
            <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li><a href="#guestbookCodeModal" role="button" data-toggle="modal"><?php echo __('View Embed Code', 'guestbook');?></a></li>
            </ul>
        </div>
    </div>
</div>

<form method="post" onSubmit="">
<table class="table table-bordered">
    <thead>
        <tr>
            <th width="20">&nbsp;</th>
            <th width="80"><?php echo __('Date', 'guestbook');?></th>
            <th width="130"><?php echo __('Name', 'guestbook');?></th>
            <th><?php echo __('Message', 'guestbook'); ?></th>
            <?php if (Option::get('guestbook_check') == 'yes') { ?>
                <th>&nbsp;</th>
            <?php } ?>
            <th width="50"><?php echo __('Important', 'guestbook');?></th>
            <th width="210"><?php echo __('Actions', 'guestbook'); ?></th>
        </tr>
    </thead>
    <?php if (count($records) > 0): ?>
    <tbody>
        <?php foreach ($records as $row):?>
        <tr <?php if (Option::get('guestbook_check') == 'yes' and $row['check'] == 0) echo 'class="warning"'; ?>>
            <td><input type="checkbox" name="guestbook_delete[]" class="guestbook-delete" value="<?php echo $row['id'];?>"/></td>
            <td><?php echo Guestbook::getdate($row['date']); ?></td>
            <td><?php echo $row['name']; ?></td>
            <td>
                <?php echo $row['message'];?>
                <?php if ($row['answer'] != '') echo '<blockquote style="margin-bottom:0;"><small>'.$row['answer'].'</small></blockquote>';?>
            </td>
            <?php if (Option::get('guestbook_check') == 'yes') { ?>
                <td>
                <?php if ($row['check'] == 0) { ?>
                    <a class="btn btn-xs btn-danger guestbook-check" href="#" data-id="<?php echo $row['id'];?>" title="<?php echo __('Confirmed', 'guestbook');?>"><i class="glyphicon glyphicon-thumbs-up"></i></a>
                <?php } ?>
                </td>
            <?php } ?>
            <td><a class="btn btn-xs <?php if ($row['important']) echo 'btn-success'; else echo 'btn-default'; ?> guestbook-impotant-check" href="#" data-id="<?php echo $row['id'];?>"><i class="glyphicon glyphicon-ok"></i></a></td>
            <td>
                <?php echo Html::anchor('<i class="glyphicon glyphicon-pencil"></i> '.__('Edit', 'guestbook'), 'index.php?id=guestbook&row_id='.$row['id'].'&action=edit', array('class' => 'btn btn-xs btn-primary')); ?>
                <?php echo Html::anchor('<i class="glyphicon glyphicon-trash"></i> '.__('Delete', 'guestbook'), 'index.php?id=guestbook&row_id='.$row['id'].'&action=delete&page='.Request::get('page').'&token='.Security::token(), array('class' => 'btn btn-xs btn-danger', 'onClick'=>'return confirmDelete(\''.__('Sure you want to remove the :item', 'guestbook', array(':item'=>$row['name'])).'\')')); ?>
            </td>
        </tr>
        <?php endforeach;?>
    </tbody> 
    <?php endif; ?>
</table>
<?php echo Form::hidden('csrf', Security::token());?>
<input type="checkbox" class="check-all" style="margin-left:16px; margin-right:13px;"/>
<input type="submit" name="submit_delete_guestbook" class="btn btn-default delete-guestbook-button disabled" disabled="disabled" value="<?php echo __('Delete checked', 'guestbook');?>" onClick="return confirmDelete('<?php echo __('Sure you want to delete all the selected records', 'guestbook');?>');"/>
</form>

<div class="guestbook-paginator"><?php Guestbook::paginator($current_page, $pages_count, 'index.php?id=guestbook&page=');?></div>

<div class="modal fade" id="guestbookCodeModal"> 
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="close" data-dismiss="modal">&times;</div>
                <h4 class="modal-title"><?php echo __('Embed Code', 'guestbook'); ?></h4>
            </div>
            <div class="modal-body">
                <h5><?php echo __('PHP Code', 'guestbook');?></h5>
        
                <dl class="dl-horizontal">
                    <dt><?php echo __('Count guestbook', 'guestbook');?></dt>
                    <dd><input type="text" class="form-control input-sm" onclick="this.select()" value="&lt;?php echo Guestbook::count(); ?&gt;"></dd>

                    <dt><?php echo __('Random 3', 'guestbook');?></dt>
                    <dd><input type="text" class="form-control input-sm" onclick="this.select()" value="&lt;?php echo Guestbook::show('random', 3); ?&gt;"></dd>
            
                    <dt><?php echo __('Last 3 important', 'guestbook');?></dt>
                    <dd><input type="text" class="form-control input-sm" onclick="this.select()" value="&lt;?php echo Guestbook::show('last', 3, 'important'); ?&gt;"></dd>
                </dl>
    
                <h5><?php echo __('Shortcode', 'guestbook');?></h5>
        
                <dl class="dl-horizontal">
                    <dt><?php echo __('Count guestbook', 'guestbook');?></dt>
                    <dd><input type="text" class="form-control input-sm" onclick="this.select()" value='{guestbook show="count"}'></dd>

                    <dt><?php echo __('Random 3', 'guestbook');?></dt>
                    <dd><input type="text" class="form-control input-sm" onclick="this.select()" value='{guestbook show="random" count="3"}'></dd>
            
                    <dt><?php echo __('Last 3 important', 'guestbook');?></dt>
                    <dd><input type="text" class="form-control input-sm" onclick="this.select()" value='{guestbook show="last" count="3" label="important"}'></dd>
                </dl>
            </div>
        </div>
    </div>
</div>