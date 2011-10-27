<?php $this->load->view('header');
echo form_open(base_url() . 'account/updatepassword/')?>
<fieldset>
	<legend>Reset Password</legend>
			<label>New Password:</label>
			<?=form_password('userPassword', set_value('userPassword'), 'class="text" size="30"')?>
			<?=form_error('userPassword')?>
		<?php
$data = array(
              'userEmailToken'  => $emailtoken,
              'userPasswordToken' => $passwordtoken
            );
echo form_hidden($data);
?>
			<?=form_submit('', 'Reset Password','class="submit"')?>

</fieldset>
<?php
echo form_close();
$this->load->view('footer');
?>