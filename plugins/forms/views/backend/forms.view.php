<div class="vertical-align margin-bottom-1">
    <div class="text-left row-phone">
        <h2><?php echo __('Forms', 'forms'); ?></h2>
    </div>
    <div class="text-right row-phone">
        <?php
            echo (
                Html::anchor('<i class="glyphicon glyphicon-plus"></i> '.__('Add form', 'forms'), 'index.php?id=forms&action=form', array('title' => __('Add form', 'forms'), 'class' => 'btn btn-phone btn-primary'))
            );
        ?>
    </div>
</div>

<table class="table table-bordered">
    <thead>
        <tr>
            <th><?php echo __('Name', 'forms'); ?></th>
            <th><?php echo __('E-mail recipient', 'forms'); ?></th>
            <th><?php echo __('Shortcode', 'forms'); ?></th>
            <th><?php echo __('PHP Code', 'forms'); ?></th>
            <th width="270"><?php echo __('Actions', 'forms'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($records)>0) foreach($records as $row) { ?>
        <tr>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['email']; ?></td>
            <td width="130"><input class="form-control input-sm" onclick="this.select();" value='{forms id="<?php echo $row['id']; ?>"}'></td>
            <td width="220"><input class="form-control input-sm" onclick="this.select();" value='&lt;?php echo Forms::get(<?php echo $row['id']; ?>); ?&gt;'></td>
            <td nowrap>
                <a class="btn btn-primary btn-xs" href="index.php?id=forms&form_id=<?php echo $row['id']; ?>&action=elements"><i class="glyphicon glyphicon-align-justify"></i> <?php echo __('Elements', 'forms'); ?></a>
                <a class="btn btn-primary btn-xs" href="index.php?id=forms&form_id=<?php echo $row['id']; ?>&action=form"><i class="glyphicon glyphicon-cog"></i> <?php echo __('Settings', 'forms'); ?></a>
                <?php echo Html::anchor('<i class="glyphicon glyphicon-trash"></i> '.__('Delete', 'forms'), 'index.php?id=forms&form_id='.$row['id'].'&action=delete&token='.Security::token(), array('class' => 'btn btn-danger btn-xs', 'onClick'=>'return confirmDelete(\''.__('Sure you want to remove the :item', 'forms', array(':item'=>$row['name'])).'\')'))?>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<?php if (Option::get('forms-demo-msg') == 1) { ?>
<div class="alert alert-block">
    <a href="index.php?id=forms&action=demo" class="btn btn-success"><?php echo __('Install the demo form?', 'forms'); ?></a> 
    <a href="#" class="btn btn-danger" data-dismiss="alert" onClick="return formsDemoMsg();"><i class="glyphicon glyphicon-remove"></i></a> 
</div>
<?php } ?>