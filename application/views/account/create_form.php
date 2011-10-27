<?php $this->load->view('header');
echo form_open(base_url() . 'account/create');
?>
<fieldset>
	<legend>Create your organization access account</legend>
			<label>Email:</label>
			<?=form_input('userEmail', set_value('userEmail'), 'class="text" size="30"')?>
			<?=form_error('userEmail')?>
			<label>Password:</label>
			<?=form_password('userPassword', '', 'class="text" size="30"')?>
			<?=form_error('userPassword')?>
			<label>Username:</label>
			<?=form_input('userName', set_value('userName'),'class="text" size="30"')?>
			<?=form_error('userName')?>
			<label></label>
			<?=form_submit('', 'Create','class="submit"')?>
</fieldset>
<?php 
 echo form_close();
 $this->load->view('footer');
?>