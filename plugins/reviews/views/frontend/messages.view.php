<?php if (count($records) > 0) foreach($records as $row) { ?>
<div class="reviews-item">
    <div class="reviews-title">
        <strong><?php echo Html::toText($row['name']); ?></strong> / <?php echo Reviews::getdate($row['date']); ?></small>
    </div>
    <div class="reviews-body">
        <?php echo nl2br(Html::toText($row['message'])); ?>
        <?php if ($answer_show and $row['answer'] != '') { ?>
            <div class="reviews-answer">
                <?php echo nl2br(Html::toText($row['answer'])); ?>
            </div>
        <?php } ?>
    </div>
</div>
<?php } ?>