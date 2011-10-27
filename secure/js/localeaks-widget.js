( function() {
 var randomName = '';
 for (var i = 0; i < 16; i++) {
  randomName += String.fromCharCode(Math.floor(Math.random() * 26) + 97);
 }
 window[randomName] = {};
 var $ = window[randomName];
 $.initialize = function() {
  return {
   init : function(target) {
    var theScripts = document.getElementsByTagName('SCRIPT');
    for (var i = 0; i < theScripts.length; i++) {
     if (theScripts[i].src.match(target)) {
      $.widget = document.createElement('DIV');
      $.a = {};
      if (theScripts[i].innerHTML) {
       $.a = $.initialize.parseJson(theScripts[i].innerHTML);
      }
      if ($.a.err) {
       alert('you have an error in your json!');
      }

p =  $.initialize.parseJson(theScripts[i].innerHTML);    
for (var key in p) {
  if (p.hasOwnProperty(key)) {
    //alert(key + " -> " + p[key]);
    $.widget.style[key] = p[key];
   	//alert($.widget.style[key] + " -> " + p[key]);
  }
}

           
      if ($.a.org) {
        link = "https://" + $.a.org + ".localeaks.com";      
      } else {
      	link = "https://localeaks.com";
      }
      $.widget.innerHTML = '<a href="'+link+'">Anonymous Tips</a><br /><a href="https://localeaks.com"><img src="https://localeaks.com/img/poweredby.png" border="0" alt="Powered by Localeaks"/></a>';
      theScripts[i].parentNode.insertBefore($.widget, theScripts[i]);
      theScripts[i].parentNode.removeChild(theScripts[i]);
      break;
     }
    }
   },
   parseJson : function(json) {
    this.parseJson.data = json;
    if ( typeof json !== 'string') {
     return {"err":"trying to parse a non-string JSON object"};
    }
    try {
     var f = Function(['var document,top,self,window,parent,Number,Date,Object,Function,',
      'Array,String,Math,RegExp,Image,ActiveXObject;',
      'return (' , json.replace(/<\!--.+-->/gim,'').replace(/\bfunction\b/g,'function­') , ');'].join(''));
       return f();
    } catch (e) {
     return {"err":"trouble parsing JSON object"};
    }
   }
  };
 }();
 var remoteScript = 'https://localeaks.com/js/localeaks-widget.js';
 if (typeof window.addEventListener !== 'undefined') {
  window.addEventListener('load', function() { $.initialize.init(remoteScript); }, false);
 } else if (typeof window.attachEvent !== 'undefined') {
  window.attachEvent('onload', function() { $.initialize.init(remoteScript); });
 }
})();