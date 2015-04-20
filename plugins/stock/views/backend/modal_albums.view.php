<?php foreach($records as $row):?>
<button type="button" class="btn btn-info btn-block stock-modal-images" rel="<?php echo $row['id'];?>"><?php echo $row['name'];?> (<?php echo $row['w'];?>x<?php echo $row['h'];?>)</button>
    
<div class="stock-modal-images-result" style="display:none;"></div>
    
<div>
    <a href="#" class="btn btn-link btn-mini stock-modal-all" onclick="stockModal();" rel='{stock album="<?php echo $row['id'];?>"}'><i class="icon-circle-arrow-down"></i> <?php echo __('Display all images', 'stock');?></a>
    <a href="#" class="btn btn-link btn-mini stock-modal-random" onclick="stockModal();" rel='{stock album="<?php echo $row['id'];?>" show="random"}'><?php echo __('Display random image', 'stock');?></a>
    <a href="#" class="btn btn-link btn-mini stock-modal-last" onclick="stockModal();" rel='{stock album="<?php echo $row['id'];?>" show="last"}'><?php echo __('Display last image', 'stock');?></a>
    <a href="#" class="btn btn-link btn-mini stock-modal-last3" onclick="stockModal();" rel='{stock album="<?php echo $row['id'];?>" show="last" count="3"}'><?php echo __('Display last 3 images', 'stock');?></a>
</div>
    
<hr/>
<?php endforeach;?>