<h2><?php echo __('Add album', 'stock');?></h2><br/>

<?php
echo (
    Form::open().
        '<div style="overflow:hidden">'.
            Form::label('name', __('The album title', 'stock')).
            Form::input('name', null, array('style'=>'width:500px;', 'class'=>'form-control')).
        '</div>'.
        
        '<div style="float:left; width:240px; margin-right:20px;">'.
            Form::label('width_thumb', __('Width thumbnails (px)', 'stock')).
            Form::input('width_thumb', $settings['w'], array('class'=>'form-control')).
        '</div><div style="float:left; width:240px;">'.
            Form::label('height_thumb', __('Height thumbnails (px)', 'stock')).
            Form::input('height_thumb', $settings['h'], array('class'=>'form-control')).
        '</div><br clear="both"/>'.
        
        '<div style="float:left; width:240px; margin-right:20px;">'.
            Form::label('width_orig', __('Original width (px, max)', 'stock')).
            Form::input('width_orig', $settings['wmax'], array('class'=>'form-control')).
        '</div><div style="float:left; width:240px;">'.
            Form::label('height_orig', __('Original height (px, max)', 'stock')).
            Form::input('height_orig', $settings['hmax'], array('class'=>'form-control')).
        '</div><br clear="both"/>'.
        
        '<div style="float:left; width:240px; margin-right:20px;">'.
            Form::label('quality', __('Quality', 'stock').' <span>%</span>').
            Form::input('quality', $settings['quality'], array('class'=>'form-control')).
        '</div><div style="float:left; width:240px; margin-top:2px;">'.
            Form::label('resize_way', __('Resize way', 'stock')).
            Form::select('resize_way', $resize_way, $settings['resize'], array('class'=>'form-control')).
        '</div><br clear="both"/>'.
        
        '<div style="float:left; width:240px; margin-right:20px;">'.
            Form::label('sort_by', __('Sort by', 'stock')).
            Form::select('sort_by', $sort_by, $settings['sortby'], array('class'=>'form-control')).
        '</div><div style="float:left; width:240px; margin-top:2px;">'.
            Form::label('order', __('order', 'stock')).
            Form::select('order', $order, $settings['order'], array('class'=>'form-control')).
        '</div><br clear="both">'.
        
        '<div style="float:left; width:240px; margin-top:2px;">'.
            Form::label('template', __('Template', 'stock')).
            Form::select('template', $templates, $settings['template'], array('class'=>'form-control')).
        '</div><br clear="both"/><br>'.
        
        Form::hidden('csrf', Security::token()).
        Form::label('submit_album_add', "&nbsp;").
        Form::submit('submit_album_add', __('Save', 'stock'), array('class' => 'btn btn-primary')).
        
    Form::close()
);
?>