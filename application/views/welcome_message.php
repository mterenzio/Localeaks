<?php
$this->load->view('header');

$attributes = array('class' => 'leak', 'id' => 'tipform');
if (validation_errors() != '') {
	echo "<div class=\"formerror\">There were errors. NOTE: If you were uploading a file you will need to reselect it now as well!</div>";
}
echo form_open_multipart('', $attributes);
echo "<p class=\"warning\">You must javascript enabled to use this form!</p>";
$tip = set_value('tip');
$data = array(
              'name'        => 'tip',
              'id'          => 'tip',
              'value'       => $tip,
              'rows'   => '8',
              'style'       => 'width:99%;',
            );
echo "<div class=\"formelement\">".form_error('tip').form_label('Enter your news tip: ', 'tip').form_textarea($data)."</div>";
$file = set_value('file');
$data = array(
              'name'        => 'file',
              'id'          => 'file',
              'value'       => $file,
              'size'   => '25',
              'style'       => 'width:35%;border: 1px solid #879A45; padding: 5px; font-size: 100%; font-weight: bold; -moz-border-radius: 10px; -webkit-border-radius: 10px;',
            );
echo "<div class=\"formelement\">".form_error('file').form_label('Choose a file to upload (optional):<br /><small>PDF files only</small>', 'file').form_upload($data)."</div>";

//hide states/orgs if affiliate page

if ($this->affiliate_model->prefs['affiliate'] == FALSE) {
$options = array();
$options[''] = '';
foreach ($states as $state) {
	$options[$state->state_abbrev] = $state->state_name;
}

$state = set_value('states');
echo "<div class=\"formelement\">".form_error('states').form_label('Choose your State: ', 'states');
echo form_dropdown('states', $options, $state, 'id="states" class="dropdown"')."</div>";

$defaults = array();
//echo print_r($orgs, true);
if ($state != '') {
 $options = $orgs;
}
echo "<div class=\"formelement\">".form_error('orgs').form_label('Choose your news organizations:<br /><small>(select a state first)</small> ', 'orgs');
echo form_multiselect('orgs[]', $options, $defaults, 'id="orgs" class="dropdown"')."</div>";

}  else {
$data = array(
              'states'  => $this->affiliate_model->prefs['state'],
              'orgs' => $this->affiliate_model->prefs['id'],
            );

echo form_hidden($data);

}
$data = array(
    'name'        => 'accept',
    'id'          => 'accept',
    'value'       => 'accept',
    'checked'     => FALSE,
    'style'       => 'margin:10px',
    );
echo "<br /><div class=\"formelement\">".form_error('accept').form_label('I agree to <a href="/tos">terms of service</a>:', 'accept');
echo form_checkbox($data)."</div>";

//echo "<div class=\"formelement\">".form_error('captcha').form_label('Enter the sum of 3 and seven: ', 'captcha');
//echo form_input('captcha', '','class="text" size="20"')."</div>";

//submit button and close form
echo "<div class=\"formelement\">".form_submit('mysubmit', 'Submit News Tip!', 'class="submit"')."</div>";
echo form_close();

//if no affiliate no need for states/orgs ajax
if ($this->affiliate_model->prefs['affiliate'] == FALSE) {
?>
<script type="text/javascript">
$(document).ready(function(){ 
    	if ($("#states").val() == '') {
    	$("#orgs").hide(); 
    	}// else {
        //var state_url = '/orgs/state/' + $("#states").val();
        //$("#orgs").empty(); 
       //         $("#orgs").show(); 
       //     var cct = $("input[name=ci_csrf_token]").val();
       //     $('#orgs').load(state_url, {'ci_csrf_token': cct}, function (data) {this.value = data;});
       // }
    $("#states").change(function() 
    {         
    	if ($("#states").val() == '') {
    	$("#orgs").hide(); 
    	} else {
        var state_url = '/orgs/state/' + $("#states").val();
        $("#orgs").empty(); 
                $("#orgs").show(); 
            var cct = $("input[name=ci_csrf_token]").val();
            $('#orgs').load(state_url, {'ci_csrf_token': cct}, function (data) {this.value = data;});
        }
    }); 
});
</script>
<?php 
} //end hide org/state ajax for affiliates
?>
<script type="text/javascript">
$(document).ready(function(){
$('.warning').remove();
$.get("token.php",function(txt){
  $(".leak").append('<input type="hidden" name="ts" value="'+txt+'" />');
});
});
</script>
<?php
$this->load->view('footer');
?>