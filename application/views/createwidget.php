<?php
$this->load->view('header');
?>
<h2>Create your branded affiliate page and site widget!</h2>
<table><tr><td valign="top">
<?php
$attributes = array('class' => 'leak', 'id' => 'widgetform');
if (validation_errors() != '') {
	echo "<div class=\"formerror\">There were errors. NOTE: If you were uploading a file you will need to reselect it now as well!</div>";
}
echo form_open_multipart('/widget', $attributes);
echo "<p class=\"warning\">You must javascript enabled to use this form!</p>";
$subdomain = set_value('subdomain');
$data = array(
              'name'        => 'subdomain',
              'id'          => 'subdomain',
              'value'       => $subdomain,
            );
echo "<div class=\"formelement\">".form_error('subdomain').form_label('Enter your desired subdomain: <br /> <small>e.g. dailypost.localeaks.com</small>', 'subdomain').'https://'.form_input($data).".localeaks.com</div>";
$file = set_value('file');
$data = array(
              'name'        => 'file',
              'id'          => 'file',
              'value'       => $file,
              'size'   => '25',
              'style'       => 'width:35%;border: 1px solid #879A45; padding: 5px; font-size: 100%; font-weight: bold; -moz-border-radius: 10px; -webkit-border-radius: 10px;',
            );
echo "<div class=\"formelement\">".form_error('file').form_label('Upload your site logo:<br /><small>.png files only | max-height = 90px | max-width = 400px | max-size = 300k </small>', 'file').form_upload($data)."</div>";

$website = set_value('website');
$data = array(
              'name'        => 'website',
              'id'          => 'website',
              'value'       => $website,
            );
echo "<div class=\"formelement\">".form_error('website').form_label('Enter your website address: <br /> <small>(include http://)</small>', 'website').form_input($data)."</div>";

$options = array();
$options[''] = '';
foreach ($orgs as $org) {
		$options[$org->org_id] = $org->org_name;
}

$org = set_value('org');
echo "<div class=\"formelement\">".form_error('org').form_label('Choose the news organization: ', 'org');
echo form_dropdown('org', $options, $org, 'id="org" class="dropdown"')."</div>";


$data = array(
    'name'        => 'accept',
    'id'          => 'accept',
    'value'       => 'accept',
    'checked'     => FALSE,
    'style'       => 'margin:10px',
    );
echo "<br /><div class=\"formelement\">".form_error('accept').form_label('I agree to <a href="/tos">terms of service</a>:', 'accept');
echo form_checkbox($data)."</div>";

//submit button and close form
echo "<div class=\"formelement\">".form_submit('mysubmit', 'Get My Widget!', 'class="submit"')."</div>";
echo form_close();

?>
<script type="text/javascript">
$(document).ready(function(){
$('.warning').remove();
$.get("token.php",function(txt){
  $(".leak").append('<input type="hidden" name="ts" value="'+txt+'" />');
});
});
</script>
</td>
<td valign="top" style="width: 300px; padding: 10px; background-color: #efefef;"><b>Instructions:<br /><br />It's easy to create your branded affiliate page with this form.
<br /><br />1. Enter your desired subdomain. This will be where your secure affiliate page will be. For example, https://dailypost.localeaks.com.
<br /><br />2. Upload a .png version of your site logo. This will be used on your affiliate page. Maximum height is 90 pixels, maximum width is 400 pixels and the maximum file size is 300k.
<br /><br />3. Enter the website address you will be using the widget on. Include the http:// portion of the address.
<br /><br />4. Choose the news organization to associate with this affiliate page and widget.
<br /><br />5. Agree to the terms and submit the form. You will be presented with a javascript widget that you can put anywhere on your site.
<br /><br />Contact <a href="mailto:support@localeaks.com">support@localeaks.com</a> for questions regarding the affiliate pages or widgets.
</b></td>
</tr>
</table>
<?php
$this->load->view('footer');
?>