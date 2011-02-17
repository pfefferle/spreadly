<dd>
  <div class="commentbox clearfix">
    <strong>Marco Ripanti</strong> <span class="timeago">41 minutes ago</span>
    <p>
      <strong title="<?php echo $pActivity->getTitle(); ?>"><?php echo truncate_text($pActivity->getTitle(), 45); ?></strong>
      <?php
        if (strlen($pActivity->getTitle()) < 20) {
        $lDescrCount = 45 - strlen($pActivity->getTitle());
      ?>
         - <span title="<?php echo $pActivity->getDescr() ?>"><?php echo truncate_text($pActivity->getDescr(), $lDescrCount) ?></span>
      <?php } ?>
      <br />
      <a href="<?php echo $pActivity->getUrl(); ?>" target="_blank"><?php echo $pActivity->getUrl(); ?></a>
    </p>
  </div>
</dd>