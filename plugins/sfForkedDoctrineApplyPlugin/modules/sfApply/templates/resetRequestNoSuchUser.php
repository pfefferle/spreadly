<?php use_helper('I18N') ?>
<?php slot('content') ?>
<div class="sf_apply_notice">
<p>
<?php echo __('Sorry, there is no user with that username or email address.', array(), 'sfGuardApply') ?>
</p>
<?php include_partial('sfApply/continue') ?>
</div>
<?php end_slot(); ?>
<?php include_partial('global/graybox'); ?>
