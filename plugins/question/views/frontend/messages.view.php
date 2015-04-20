<?php if (count($records) > 0) foreach($records as $row) { ?>
<div class="question-item">
    <div class="question-title">
        <strong><?php echo Html::toText($row['name']); ?></strong> / <?php echo Question::getdate($row['date']); ?></small>
    </div>
    <div class="question-body">
        <?php echo nl2br(Html::toText($row['message'])); ?>
        <?php if ($answer_show and $row['answer'] != '') { ?>
            <div class="question-answer">
                <?php echo nl2br(Html::toText($row['answer'])); ?>
            </div>
        <?php } ?>
    </div>
</div>
<?php } ?>