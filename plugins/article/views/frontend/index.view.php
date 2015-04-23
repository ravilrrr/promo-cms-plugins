<h1><?php echo __('Article', 'article');?></h1>

<div id="article">

    <?php if(count($records)>0):?>
    
        <div id="article-list">
        
            <?php foreach($records as $row):?>
                <?php 
                    $article_url = $site_url.'/article/'.$row['id'].'/'.$row['slug'];
                    if(!empty($row['img'])) {
                        if(file_exists(ROOT . DS . 'public' . DS . 'article' . DS . 'images' . DS . $row['img'])) {
                            $img = '<img class="article-avatar" src="'.$site_url.'/public/article/images/'.$row['img'].'" width="'.$img_w.'" alt=""/>';
                        } else $img = $img_def;
                    } else $img = $img_def;
                ?>
                <div class="article-item">
                
                    <h2><a href="<?php echo $article_url;?>"><?php echo $row['name'];?></a></h2>
                    <div class="article-content">
                        <?php echo $img;?>
                        <?php echo Article::getContentShort($row['id'], true, $article_url); ?>
                    </div>
                    <div class="article-status">
                        <div class="article-fleft">
                            <?php echo Date::format($row['date'], 'd.m.Y'); ?>
                        </div>
                        <div class="article-fright">&nbsp;<?php Action::run('article_item_status', array('id' => $row['id']));?></div>
                    </div>
                </div><!-- /article-item-->
                
            <?php endforeach;?>
            
        </div><!-- /article-list-->
    
    <?php endif;?>
    
    <div id="article-paginator"><?php Article::paginator($current_page, $pages_count, $site_url.'article/page/');?></div>
</div><!-- /article -->