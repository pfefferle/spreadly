<?php use_helper('I18N') ?>
<?php use_stylesheets_for_form( $form ) ?>
<?php slot('sf_apply_login') ?>
<?php end_slot() ?>
<?php slot('content') ?>
<div class="sf_apply sf_apply_reset_request">

<h2 class="green_style"><?php echo __("&raquo; Reset Password", array(), 'sfForkedApply') ?></h2>
<form method="POST" action="<?php echo url_for('sfApply/resetRequest') ?>" name="sf_apply_reset_request" id="sf_apply_reset_request">

<p>
	<?php echo __('Forgot your username or password? No problem! Just enter your username <strong>or</strong>your email address and click "Reset My Password." You will receive an email message containing both your username and a link permitting you to change your password if you wish.', array(), 'sfForkedApply') ?>
</p>

<table>
  <tbody>
    <?php echo $form ?>
  </tbody>
  <tfoot>
    <tr>
      <td colspan="2">
        <button class="button" type="submit"><span><?php echo __("Reset My Password", array(), 'sfForkedApply') ?></span></button>
        <?php echo link_to('<span>'.__("Cancel", array(), 'sfForkedApply').'</span>', sfConfig::get('app_sfApplyPlugin_after', '@homepage'), array('class' => 'button')) ?>
      </td>
    </tr>
  </tfoot>
</table>
</form>
</div>
<?php end_slot(); ?>
<?php include_partial('global/graybox', array('pClass' => 'register-box')); ?>
