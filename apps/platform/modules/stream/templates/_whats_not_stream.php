<?php foreach ($pSocialObjects as $lObject) { ?>
  <li class="clearfix stream_item" id="stream_item_<? echo $lObject->getId(); ?>" data-obj='{"action":"StreamItem.getDetailAction", "callback":"ItemDetail.show", "itemid":"<?php echo $lObject->getId(); ?>", "css":"{\"itemid\":\"stream_item_<? echo $lObject->getId(); ?>\"}"}'>
    <?php include_partial('whats_not_stream_item', array('pObject' => $lObject)); ?>
  </li>
<?php } ?>

<?php include_partial('stream/stream_pager'); ?>