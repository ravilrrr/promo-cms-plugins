<div class="vertical-align margin-bottom-1">
    <div class="text-left row-phone">
        <h2><?php echo $form['name']; ?></h2>
    </div>
    <div class="text-right row-phone">
        <div class="btn-group">
            <a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#">
                <i class="glyphicon glyphicon-plus"></i> <?php echo __('Add item', 'forms'); ?>
                <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
                <li><a href="index.php?id=forms&form_id=<?php echo $form['id']; ?>&action=element_add&type=text"><?php echo __('Element text', 'forms'); ?></a></li>
                <li><a href="index.php?id=forms&form_id=<?php echo $form['id']; ?>&action=element_add&type=textarea"><?php echo __('Element textarea', 'forms'); ?></a></li>
                <li><a href="index.php?id=forms&form_id=<?php echo $form['id']; ?>&action=element_add&type=radio"><?php echo __('Element radio', 'forms'); ?></a></li>
                <li><a href="index.php?id=forms&form_id=<?php echo $form['id']; ?>&action=element_add&type=checkbox"><?php echo __('Element checkbox', 'forms'); ?></a></li>
                <li><a href="index.php?id=forms&form_id=<?php echo $form['id']; ?>&action=element_add&type=select"><?php echo __('Element select', 'forms'); ?></a></li>
                <li class="divider"></li>
                <li><a href="index.php?id=forms&form_id=<?php echo $form['id']; ?>&action=element_add&type=subtitle"><?php echo __('Element subtitle', 'forms'); ?></a></li>
                <li><a href="index.php?id=forms&form_id=<?php echo $form['id']; ?>&action=element_add&type=name"><?php echo __('Element name', 'forms'); ?></a></li>
                <li><a href="index.php?id=forms&form_id=<?php echo $form['id']; ?>&action=element_add&type=email"><?php echo __('Element email', 'forms'); ?></a></li>
                <li><a href="index.php?id=forms&form_id=<?php echo $form['id']; ?>&action=element_add&type=tel"><?php echo __('Element tel', 'forms'); ?></a></li>
            </ul>
        </div>
        <a class="btn btn-primary" href="index.php?id=forms&form_id=<?php echo $form['id']; ?>&action=form"><i class="glyphicon glyphicon-cog"></i> <?php echo __('Settings form', 'forms'); ?></a>
    </div>
</div>

<table class="table table-bordered" id="forms_elements" data-form-id="<?php echo $form['id']; ?>">
    <thead>
        <tr>
            <th width="10">&nbsp;</th>
            <th><?php echo __('Title', 'forms'); ?></th>
            <th><?php echo __('Required field', 'forms'); ?></th>
            <th><?php echo __('Width', 'forms'); ?></th>
            <th><?php echo __('Type', 'forms'); ?></th>
            <th width="220"><?php echo __('Actions', 'forms'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php if(count($elements)>0) foreach($elements as $e) { ?>
        <tr id="<?php echo $e['id']; ?>">
            <td class="dragHandle"></td>
            <td><?php echo $e['title']; ?></td>
            <td>
                <?php if($e['type']!='subtitle') { ?>
                <a href="#" class="forms-element-required-check btn btn-xs <?if($e['required']=='yes') echo 'btn-primary'; else echo 'btn-default'; ?>" data-form-id="<?php echo $form['id']; ?>" data-element-id="<?php echo $e['id']; ?>"><i class="glyphicon glyphicon-ok"></i></a>
                <?php } ?>
            </td>
            <td>
                <?php if(!in_array($e['type'], array('subtitle', 'radio', 'checkbox'))) { ?>
                <div class="btn-group" data-form-id="<?php echo $form['id']; ?>" data-element-id="<?php echo $e['id']; ?>">
                    <button type="button" class="forms-element-width-change btn btn-default btn-xs <?if($e['width']==25) echo 'active';?>">25</button>
                    <button type="button" class="forms-element-width-change btn btn-default btn-xs <?if($e['width']==50) echo 'active';?>">50</button>
                    <button type="button" class="forms-element-width-change btn btn-default btn-xs <?if($e['width']==75) echo 'active';?>">75</button>
                    <button type="button" class="forms-element-width-change btn btn-default btn-xs <?if($e['width']==100) echo 'active';?>">100</button>
                </div>
                <?php } ?>
            </td>
            <td><?php echo __('Element '.$e['type'], 'forms'); ?></td>
            <td>
                <?php echo Html::anchor('<i class="glyphicon glyphicon-pencil"></i> '.__('Edit', 'forms'), 'index.php?id=forms&element_id='.$e['id'].'&action=element', array('class' => 'btn btn-primary btn-xs')); ?>
                <?php echo Html::anchor('<i class="glyphicon glyphicon-trash"></i> '.__('Delete', 'forms'), 'index.php?id=forms&form_id='.$form['id'].'&element_id='.$e['id'].'&action=element_delete&token='.Security::token(), array('class' => 'btn btn-danger btn-xs', 'onClick'=>'return confirmDelete(\''.__('Sure you want to remove the :item', 'forms', array(':item'=>$e['title'])).'\')')); ?>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>