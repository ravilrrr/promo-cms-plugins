<div class="vertical-align margin-bottom-1">
    <div class="text-left row-phone">
        <h2><?php echo __('Question', 'question').' <small><sup>'.Html::anchor('<i class="glyphicon glyphicon-new-window"></i>', Site::url().'/question', array('target' => '_blank')).'</sup></small>';?></h2>
    </div>
    <div class="text-right row-phone">
        <div class="btn-group">
            <a class="btn btn-default" href="index.php?id=question&action=settings"><i class="glyphicon glyphicon-cog"></i> <?php echo __('Settings', 'question');?></a> 
            <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li><a href="#questionCodeModal" role="button" data-toggle="modal"><?php echo __('View Embed Code', 'question');?></a></li>
            </ul>
        </div>
    </div>
</div>

<form method="post" onSubmit="">
<table class="table table-bordered">
    <thead>
        <tr>
            <th width="20">&nbsp;</th>
            <th width="80"><?php echo __('Date', 'question');?></th>
            <th width="130"><?php echo __('Name', 'question');?></th>
            <th><?php echo __('Message', 'question'); ?></th>
            <?php if (Option::get('question_check') == 'yes') { ?>
                <th>&nbsp;</th>
            <?php } ?>
            <th width="50"><?php echo __('Important', 'question');?></th>
            <th width="210"><?php echo __('Actions', 'question'); ?></th>
        </tr>
    </thead>
    <?php if (count($records) > 0): ?>
    <tbody>
        <?php foreach ($records as $row):?>
        <tr <?php if (Option::get('question_check') == 'yes' and $row['check'] == 0) echo 'class="warning"'; ?>>
            <td><input type="checkbox" name="question_delete[]" class="question-delete" value="<?php echo $row['id'];?>"/></td>
            <td><?php echo Question::getdate($row['date']); ?></td>
            <td><?php echo $row['name']; ?></td>
            <td>
                <?php echo $row['message'];?>
                <?php if ($row['answer'] != '') echo '<blockquote style="margin-bottom:0;"><small>'.$row['answer'].'</small></blockquote>';?>
            </td>
            <?php if (Option::get('question_check') == 'yes') { ?>
                <td>
                <?php if ($row['check'] == 0) { ?>
                    <a class="btn btn-xs btn-danger question-check" href="#" data-id="<?php echo $row['id'];?>" title="<?php echo __('Confirmed', 'question');?>"><i class="glyphicon glyphicon-thumbs-up"></i></a>
                <?php } ?>
                </td>
            <?php } ?>
            <td><a class="btn btn-xs <?php if ($row['important']) echo 'btn-success'; else echo 'btn-default'; ?> question-impotant-check" href="#" data-id="<?php echo $row['id'];?>"><i class="glyphicon glyphicon-ok"></i></a></td>
            <td>
                <?php echo Html::anchor('<i class="glyphicon glyphicon-pencil"></i> '.__('Edit', 'question'), 'index.php?id=question&row_id='.$row['id'].'&action=edit', array('class' => 'btn btn-xs btn-primary')); ?>
                <?php echo Html::anchor('<i class="glyphicon glyphicon-trash"></i> '.__('Delete', 'question'), 'index.php?id=question&row_id='.$row['id'].'&action=delete&page='.Request::get('page').'&token='.Security::token(), array('class' => 'btn btn-xs btn-danger', 'onClick'=>'return confirmDelete(\''.__('Sure you want to remove the :item', 'question', array(':item'=>$row['name'])).'\')')); ?>
            </td>
        </tr>
        <?php endforeach;?>
    </tbody> 
    <?php endif; ?>
</table>
<?php echo Form::hidden('csrf', Security::token());?>
<input type="checkbox" class="check-all" style="margin-left:16px; margin-right:13px;"/>
<input type="submit" name="submit_delete_question" class="btn btn-default delete-question-button disabled" disabled="disabled" value="<?php echo __('Delete checked', 'question');?>" onClick="return confirmDelete('<?php echo __('Sure you want to delete all the selected records', 'question');?>');"/>
</form>

<div class="question-paginator"><?php Question::paginator($current_page, $pages_count, 'index.php?id=question&page=');?></div>

<div class="modal fade" id="questionCodeModal"> 
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="close" data-dismiss="modal">&times;</div>
                <h4 class="modal-title"><?php echo __('Embed Code', 'question'); ?></h4>
            </div>
            <div class="modal-body">
                <h5><?php echo __('PHP Code', 'question');?></h5>
        
                <dl class="dl-horizontal">
                    <dt><?php echo __('Count question', 'question');?></dt>
                    <dd><input type="text" class="form-control input-sm" onclick="this.select()" value="&lt;?php echo Question::count(); ?&gt;"></dd>

                    <dt><?php echo __('Random 3', 'question');?></dt>
                    <dd><input type="text" class="form-control input-sm" onclick="this.select()" value="&lt;?php echo Question::show('random', 3); ?&gt;"></dd>
            
                    <dt><?php echo __('Last 3 important', 'question');?></dt>
                    <dd><input type="text" class="form-control input-sm" onclick="this.select()" value="&lt;?php echo Question::show('last', 3, 'important'); ?&gt;"></dd>
                </dl>
    
                <h5><?php echo __('Shortcode', 'question');?></h5>
        
                <dl class="dl-horizontal">
                    <dt><?php echo __('Count question', 'question');?></dt>
                    <dd><input type="text" class="form-control input-sm" onclick="this.select()" value='{question show="count"}'></dd>

                    <dt><?php echo __('Random 3', 'question');?></dt>
                    <dd><input type="text" class="form-control input-sm" onclick="this.select()" value='{question show="random" count="3"}'></dd>
            
                    <dt><?php echo __('Last 3 important', 'question');?></dt>
                    <dd><input type="text" class="form-control input-sm" onclick="this.select()" value='{question show="last" count="3" label="important"}'></dd>
                </dl>
            </div>
        </div>
    </div>
</div>