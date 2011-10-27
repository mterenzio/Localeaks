<?php $this->load->view('header');
echo form_open(base_url() . 'account/password')?>
<fieldset>
	<legend>Reset Password</legend>
			<label>Email:</label>
			<?=form_input('userEmail', set_value('userEmail'), 'class="text" size="30"')?>
			<?=form_error('userEmail')?>
			<?=form_submit('', 'Reset Password','class="submit"')?>
</fieldset>
<?php
echo form_close();
$this->load->view('footer');
?>