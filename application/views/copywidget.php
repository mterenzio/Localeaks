<?php
$this->load->view('header');
?>
<h2>Copy and paste this widget code on to your home page!</h2>
<table><tr><td style="margin: 10px; padding: 10px;">
<textarea rows="20" cols="50"><script src="http://cdn.localeaks.com/js/widget.js">
  {
  	"org": "<?php echo $org;?>",
  	"orgid": "<?php echo $orgid;?>",
  	"text": "Submit an anonymous tip",
  	"count": "true",
  	"color":"#9f9f9f",
  	"borderColor": "#9f9f9f",
  	"width": "300px",
  	"borderStyle": "solid",
  	"borderWidth": "1px",
  	"padding": "5px",
  	"fontFamily": "arial,sans-serif",
  	"textAlign": "center",
  	"fontWeight": "bold",
  	"textDecoration": "none",
  	"backgroundColor": "#ffffff"
  }
 </script></textarea>
</td>
<td valign="top" style="width: 300px; padding: 10px; background-color: #efefef;"><b>Customizing the widget is easy!<br /><br />Your designer can customize the style of the widget using any of the <a href="http://www.w3schools.com/jsref/dom_obj_style.asp">HTML DOM Style Object</a> properties and adding them to the widget. 
<br /><br />We've just given them a head start! <br /><br />Try changing the number in the width field from 300 to 160 to customize the wiget width. Refresh your web page after you make the changes to see them.<br /><br />Contact <a href="mailto:support@localeaks.com">support@localeaks.com</a> for questions regarding the widget.
</b></td>
</tr>
</table>
<?php
$this->load->view('footer');
?>