<?php $this->load->view('header');
echo "<h2>".$numleaks." total leaks, ". $viewedleaks ." of which have been viewed.</h2>";
?>
<p class="message"><?php
if (isset($message)) {
echo "<i>".$message."</i>";
}
?></p>
<div class="inbox"><?php
if (isset($leaks)) {
	echo "<b>Download these files promptly. They will self destruct in minutes.</b><br /><br />";
	foreach ($leaks as $leak) {
		if (count(explode('.pdf', $leak->leak_file, -1)) == 0) {
			$icon = "/img/text.png";
		} else {
			$icon = "/img/pdf.png";		
		}
		echo "<a href=\"/account/download/".$leak->leak_file."\"><img src=\"$icon\" class=\"icon\"/> - ".$leak->leak_file."</a><small></small><br />";
		$time = time();
		$this->leak_model->UpdateLeak(array('leak_id' => $leak->leak_id, 'leak_status' => 'viewed', 'leak_view_time' => $time));
	}
}
?></div>
<div class="message">
<a href="/widget">Get My Widget</a>
</div>
<?php $this->load->view('footer');?>