<?php $this->load->view('header');
echo form_open(base_url() . 'account/login');
?>
<fieldset>
	<legend>Login form</legend>
			<label>Email:</label>
			<?=form_input('userEmail', set_value('userEmail'), 'class="text" size="30"')?>
			<?=form_error('userEmail')?>
			<label>Password:</label>
			<?=form_password('userPassword', '', 'class="text" size="30"')?>
			<?=form_error('userPassword')?>
			<label></label>
			<?=form_submit('', 'Login','class="submit"')?>
			<a href="/account/password/">Forgot password?</a>
</fieldset>
<?php 
 echo form_close();
 $this->load->view('footer');
?>
