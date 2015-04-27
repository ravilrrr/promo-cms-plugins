<h2 class="margin-bottom-1"><?php echo __('Maps', 'maps');?></h2>

<ul class="nav nav-tabs">
    <li <?php if (Notification::get('address')) { ?>class="active"<?php } ?>><a href="#address" data-toggle="tab"><?php echo __('Add address', 'maps'); ?></a></li>
    <li <?php if (Notification::get('settings')) { ?>class="active"<?php } ?>><a href="#settings" data-toggle="tab"><?php echo __('Settings', 'maps'); ?></a></li>
</ul>
         
<div class="tab-content tab-page">
    <div class="tab-pane <?php if (Notification::get('address')) { ?>active<?php } ?>" id="address">
        <?php echo Form::open(); ?>
        <?php echo Form::hidden('csrf', Security::token()); ?>
        
        <div class="form-group">
        <?php
            echo (
                Form::label('address', __('Address', 'maps')).
                Form::input('address', '', array('class' => 'form-control'))
            );
        ?>
        </div>  
        
        <div class="form-group">
        <?php
            echo (
                Form::label('phones', __('Phones', 'maps')).
                Form::input('phones', '', array('class' => 'form-control'))
            );
        ?>
        </div> 
        
        <?php echo Form::submit('submit_add_address', __('Add address', 'maps'), array('class' => 'btn btn-primary')); ?>
        <?php echo Form::close(); ?>     
    </div>
    <div class="tab-pane <?php if (Notification::get('settings')) { ?>active<?php } ?>" id="settings">
    
        <form onSubmit="return mapsSettingsSave(this);" method="post">
        <?php 
        $zoom = $zoomc = array_combine(range(3,20), range(3,20));

        echo (            
            __('Width maps (px)', 'maps').Html::Br().
            Form::input('width', Option::get('map_width'), array('class'=>'form-control')).Html::Br().
            
            __('Height maps (px)', 'maps').Html::Br().
            Form::input('height', Option::get('map_height'), array('class'=>'form-control')).Html::Br().

            __('Zoom', 'maps').Html::Br().
            Form::select('zoom', $zoom, Option::get('map_zoom'), array('class'=>'form-control')).Html::Br().
            
            __('Zoom plus', 'maps').Html::Br().
            Form::select('zoomc', $zoomc, Option::get('map_zoomc'), array('class'=>'form-control')).Html::Br().

            Form::hidden('csrf', Security::token()).
            Form::hidden('siteurl', Option::get('siteurl')).
            Form::hidden('maps_submit_settings', true).
            Form::submit('maps_submit_set', __('Save', 'maps'), array('class' => 'btn btn-primary')).
            ' &nbsp; <span id="maps-settings-result"></span>'.
            Form::close()
        );
        ?>  
    </div>
</div>
<br/>
<table class="table table-bordered">
    <thead>
        <tr>
            <th><?php echo __('Address and phones', 'maps');?></th>
            <th width="30%">&nbsp;</th>
        </tr>
    </thead>
    <tbody>
    <?php     
    if(count($records)>0) {
        foreach($records as $row) {
            echo (
                '<tr><td><b>'.$row['address'].'</b>'.
                Html::Br().
                $row['phones'].'</td><td>'.
                Html::anchor('<i class="glyphicon glyphicon-trash"></i> '.__('Delete', 'maps'), 'index.php?id=maps&delete_id='.$row['id'].'&token='.Security::token(), 
                    array('class' => 'btn btn-xs btn-danger', 'onclick' => "return confirmDelete('".__('Delete?', 'maps')."')")).
                '</td></tr>'
            );
        }
    }
    ?>
    </tbody>
</table>