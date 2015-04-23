<div class="vertical-align margin-bottom-1">
    <div class="text-left row-phone">
        <h2><?php echo __('Article', 'article'); ?></h2>
    </div>
    <div class="text-right row-phone">
        <?php
            echo (Html::anchor('<i class="glyphicon glyphicon-plus"></i> '.__('Add article', 'article'), 'index.php?id=article&action=add', array('class' => 'btn btn-primary btn-small'))).Html::Nbsp(2);
            echo (Html::anchor('<i class="glyphicon glyphicon-cog"></i> '.__('Settings', 'article'), 'index.php?id=article&action=settings', array('class' => 'btn btn-default btn-small')));
        ?>
    </div>
</div>

<table class="table table-bordered">
    <thead>
        <tr>
            <th><?php echo __('Name', 'article'); ?></th>
            <!--<th><?php echo __('Author', 'article'); ?></th>-->
            <th class="visible-lg hidden-xs"><?php echo __('Status', 'article'); ?></th>
            <th class="visible-lg hidden-xs"><?php echo __('Date', 'article'); ?></th>
            <th><?php echo __('Actions', 'article'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($article_list) != 0): ?> 
        <?php foreach ($article_list as $row): ?>
        <tr>        
            <td><?php echo Html::anchor(Html::toText($row['name']), $site_url.'/article/'.$row['id'].'/'.$row['slug'], array('target' => '_blank')); ?></td>
            <!--<td><?php echo $row['author']; ?></td>-->
            <td><?php echo $status_array[$row['status']]; ?></td>
            <td><?php echo Date::format($row['date'], "d.m.Y"); ?></td>
            <td>
                <?php 
                    echo (
                        Html::anchor('<i class="glyphicon glyphicon-pencil"></i> '.__('Edit', 'article'), 'index.php?id=article&action=edit&article_id='.$row['id'], 
                            array('class' => 'btn btn-primary btn-xs')).Html::Nbsp(2).
                            
                        Html::anchor('<i class="glyphicon glyphicon-trash"></i> '.__('Delete', 'article'), 'index.php?id=article&action=delete&article_id='.$row['id'].'&token='.Security::token(),
                            array('class' => 'btn btn-danger btn-xs', 'onclick' => "return confirmDelete('".__("Delete article: :article", 'article', 
                            array(':article' => Html::toText($row['name'])))."')"))
                    );
                ?>
            </td>
        </tr> 
        <?php
            endforeach;
            endif;
        ?>
    </tbody>
</table>

<div id="article-paginator-admin"><?php Article::paginator($current_page, $pages_count, 'index.php?id=article&sort='.$sort.'&order='.$order.'&page=');?></div>