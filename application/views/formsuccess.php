<?php $this->load->view('header');?>

<p class="message"><?php
if (isset($message)) {
echo $message;
}
?></p>

<?php $this->load->view('footer');?>