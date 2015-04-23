<div id="article">
    <div class="article-item">
        <h1><?php echo $row['h1'];?></h1>
            

        <div class="article-content">
            <?php echo $img;?>
            <?php echo Article::getContentShort($row['id'], false);?>
        </div>
        
        <div class="article-status">
            <div class="article-fleft">
                <?php echo Date::format($row['date'], 'd.m.Y'); ?>
            </div>
            <div class="article-fright">&nbsp;<?php Action::run('article_item_status', array('id' => $row['id']));?></div>
        </div>

    </div><!-- /article-item-->
</div>
<?php Action::run('article_current_footer', array('id' => $row['id']));?>