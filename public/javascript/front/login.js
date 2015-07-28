
/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package		Front-Office
 * @copyright	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

var hexcase = 0;
var b64pad  = "";
var chrsz   = 8;

function hex_sha1(s){return binb2hex(core_sha1(str2binb(s),s.length * chrsz));}
function b64_sha1(s){return binb2b64(core_sha1(str2binb(s),s.length * chrsz));}
function str_sha1(s){return binb2str(core_sha1(str2binb(s),s.length * chrsz));}
function hex_hmac_sha1(key, data){ return binb2hex(core_hmac_sha1(key, data));}
function b64_hmac_sha1(key, data){ return binb2b64(core_hmac_sha1(key, data));}
function str_hmac_sha1(key, data){ return binb2str(core_hmac_sha1(key, data));}

function sha1_vm_test(){
  return hex_sha1("abc") == "a9993e364706816aba3e25717850c26c9cd0d89d";
}

function core_sha1(x, len){
  x[len >> 5] |= 0x80 << (24 - len % 32);
  x[((len + 64 >> 9) << 4) + 15] = len;
  var w = Array(80);
  var a =  1732584193;
  var b = -271733879;
  var c = -1732584194;
  var d =  271733878;
  var e = -1009589776;
  for(var i = 0; i < x.length; i += 16){
    var olda = a;
    var oldb = b;
    var oldc = c;
    var oldd = d;
    var olde = e;

    for(var j = 0; j < 80; j++){
      if(j < 16) w[j] = x[i + j];
      else w[j] = rol(w[j-3] ^ w[j-8] ^ w[j-14] ^ w[j-16], 1);
      var t = safe_add(safe_add(rol(a, 5), sha1_ft(j, b, c, d)),
                       safe_add(safe_add(e, w[j]), sha1_kt(j)));
      e = d;
      d = c;
      c = rol(b, 30);
      b = a;
      a = t;
    }

    a = safe_add(a, olda);
    b = safe_add(b, oldb);
    c = safe_add(c, oldc);
    d = safe_add(d, oldd);
    e = safe_add(e, olde);
  }
  return Array(a, b, c, d, e);

}

function sha1_ft(t, b, c, d){
  if(t < 20) return (b & c) | ((~b) & d);
  if(t < 40) return b ^ c ^ d;
  if(t < 60) return (b & c) | (b & d) | (c & d);
  return b ^ c ^ d;
}

function sha1_kt(t){
  return (t < 20) ?  1518500249 : (t < 40) ?  1859775393 :
         (t < 60) ? -1894007588 : -899497514;
}

function core_hmac_sha1(key, data){
  var bkey = str2binb(key);
  if(bkey.length > 16) bkey = core_sha1(bkey, key.length * chrsz);

  var ipad = Array(16), opad = Array(16);
  for(var i = 0; i < 16; i++){
    ipad[i] = bkey[i] ^ 0x36363636;
    opad[i] = bkey[i] ^ 0x5C5C5C5C;
  }

  var hash = core_sha1(ipad.concat(str2binb(data)), 512 + data.length * chrsz);
  return core_sha1(opad.concat(hash), 512 + 160);
}

function safe_add(x, y){
  var lsw = (x & 0xFFFF) + (y & 0xFFFF);
  var msw = (x >> 16) + (y >> 16) + (lsw >> 16);
  return (msw << 16) | (lsw & 0xFFFF);
}

function rol(num, cnt){
  return (num << cnt) | (num >>> (32 - cnt));
}

function str2binb(str){
  var bin = Array();
  var mask = (1 << chrsz) - 1;
  for(var i = 0; i < str.length * chrsz; i += chrsz)
    bin[i>>5] |= (str.charCodeAt(i / chrsz) & mask) << (32 - chrsz - i%32);
  return bin;
}

function binb2str(bin){
  var str = "";
  var mask = (1 << chrsz) - 1;
  for(var i = 0; i < bin.length * 32; i += chrsz)
    str += String.fromCharCode((bin[i>>5] >>> (32 - chrsz - i%32)) & mask);
  return str;
}

function binb2hex(binarray){
  var hex_tab = hexcase ? "0123456789ABCDEF" : "0123456789abcdef";
  var str = "";
  for(var i = 0; i < binarray.length * 4; i++)
  {
    str += hex_tab.charAt((binarray[i>>2] >> ((3 - i%4)*8+4)) & 0xF) +
           hex_tab.charAt((binarray[i>>2] >> ((3 - i%4)*8  )) & 0xF);
  }
  return str;
}

function binb2b64(binarray){
  var tab = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
  var str = "";
  for(var i = 0; i < binarray.length * 4; i += 3){
    var triplet = (((binarray[i   >> 2] >> 8 * (3 -  i   %4)) & 0xFF) << 16)
                | (((binarray[i+1 >> 2] >> 8 * (3 - (i+1)%4)) & 0xFF) << 8 )
                |  ((binarray[i+2 >> 2] >> 8 * (3 - (i+2)%4)) & 0xFF);
    for(var j = 0; j < 4; j++)
    {
      if(i * 8 + j * 6 > binarray.length * 32) str += b64pad;
      else str += tab.charAt((triplet >> 6*(3-j)) & 0x3F);
    }
  }
  return str;
};

var Auth = {

	highPerformance: 1,
	authTimeout: null,
	failedLogin: 0,

	startSession: function(){
		if($('login').tagName=='INPUT'){
			if(!$("login").value){
				$("pass").activate();
				if(typeof Effect != "undefined"){
					new Effect.Highlight("login", {
						startcolor:"#BFB2C4",
						afterFinish: function(){
							$('login').activate();
						}
					});
				};
				return;
			};
		} else {
			if($("login").selectedIndex==0){
				$("pass").activate();
				if(typeof Effect != "undefined"){
					new Effect.Highlight("login", {
						startcolor:"#BFB2C4",
						afterFinish: function(){
							$('login').activate();
						}
					});
				};
				return;
			};
		};
		if($("pass").value==""){
			$("pass").activate();
			if(typeof Effect != "undefined"){
				new Effect.Highlight("pass", {startcolor:"#BFB2C4"});
			};
			return;
		};
		$('loginButton').disable();
		$('pass').disable();
		$('login').disable();
		Auth.authTimeout = window.setTimeout(function(){
			$('logForm').hide();
			if($Jasmin.high==1){
				new Effect.Appear('spinner', {duration:0.5});
			} else {
				$('spinner').show();
			}
			Auth.authTimeout = null;
		}, 1500);
		var value = hex_sha1($F("pass"));
		var urlRequest = "webServices/Auth.php";
		new Ajax.Request(urlRequest, {
			parameters: {
				'login': $F("login"),
				'pass': value,
				'high': Auth.highPerformance,
				'captcha': $F("captcha")
			},
			onComplete: function(transport){
				if(Auth.authTimeout!=null){
					window.clearTimeout(Auth.authTimeout);
					Auth.authTimeout = null;
				};
				if($('logForm').visible()==false){
					if($Jasmin.high==1){
						new Effect.Fade('spinner', {
							duration: 0.3,
							afterFinish: function(){
								new Effect.Appear('logForm', {
									duration: 0.3,
									afterFinish: function(){
										Auth.logIn(transport);
									}
								});
							}
						});
					} else {
						$('spinner').hide();
						$('logForm').show();
						Auth.logIn(transport);
					}
				} else {
					Auth.logIn(transport);
				};
				$('loginButton').enable();
			}
		});
	},

	redirectToIndex: function(){
		if(Prototype.Browser.Gecko){
			var supportedNavigator = Auth.isSupportedNavigator();
			if(supportedNavigator){
				window.location = "index.php?option=9&action=inicio";
			} else {
				window.location = "index.php?option=9&action=firefox";
			}
		} else {
			if(Prototype.Browser.IE){
				window.location = "index.php?option=9&action=ie";
			} else {
				window.location = "index.php?option=9&action=inicio";
			}
		}
	},

	logIn:  function(transport){
		var oDiv;
		try {
			var items = transport.responseXML.getElementsByTagName("row");
			var success = 0;
			success = items[0].getAttribute("value");
			if(success==1){
				if($Jasmin.high==1){
					new Effect.Fade('appLogo', {
						duration: 0.4
					});
					new Effect.Fade('logonTable', {
						duration: 0.4,
						afterFinish: function(){
							new Auth.redirectToIndex();
						}
					});
				} else {
					new Auth.redirectToIndex();
				}
			} else {
				$('pass').enable();
				$('login').enable();
				if($Jasmin.high==1){
					window.setTimeout(function(){
						$('pass').activate();
					}, 550);
					new Effect.Shake('logonTable', {
						duration: 0.4
					});
				} else {
					alert('Usuario/Clave incorrectos');
					$('pass').activate();
				};
				Auth.failedLogin++;
				if(Auth.failedLogin>3){
					Auth.showCaptcha();
				}
			};
		}
		catch(e){
			if($('AuthErr').style.display=='none'){
				new Effect.SlideDown('AuthErr', {
					duration: 0.5
				});
			} else {
				new Effect.Shake('AuthErr', {
					duration: 0.5
				});
			};
			$('loginButton').show();
		}
	},

	showCaptcha: function(){
		if(!$('recaptcha')){
			$('captcha_lab').show();
			$('captcha_con').show();
			$('captcha_inp').show();
			['login', 'pass', 'pass_dummy', 'captcha'].each(function(element){
				$(element).setStyle({
					'fontSize': '11px',
					'padding': '2px',
					'margin': '0px'
				});
			});
			Auth.failedLogin = 4;
		};
		var nocache = parseInt(Math.random()*10000);
		$('recapcha_con').update('<img src="scripts/captcha.php?nocache='+nocache+'" id="recaptcha"/>');
	},

	startPreload: function(){
		var images = ['logo2.png', 'planes.png', 'keys.png', 'recep.png', 'llaves.png', 'factu.png', 'cartera.png',
		 'audit.png', 'audit.png', 'estadisticas.png', 'eventos.png', 'basicas.png', 'mail.png',
		 'address.png', 'modem.png', 'reminders.png', 'inactive_top.png', 'leftp.png'];
		for(var i=0;i<images.length;i++){
			var image = $(document.createElement('IMG'));
			image.src = 'img/'+images[i];
			image.setStyle({
				'position': 'fixed',
				'top': '0px',
				'left': '0px',
				'visibility': 'hidden',
				'opacity': -100
			});
			document.body.appendChild(image);
		}
	},

	isSupportedNavigator: function(){
		return /Firefox\/3\.[6-9]/.test(navigator.userAgent) || /Firefox\/4\.[0-1]/.test(navigator.userAgent);
	},

	showTiptip: function(){
		var pass = $('pass');
		var tiptip = $('tiptip');
		var position = pass.cumulativeOffset();
		tiptip.style.top = (position[1]-1)+'px';
		tiptip.style.left = (position[0]+pass.getWidth()+17)+'px';
		tiptip.update('Mayúsculas Activado');
		tiptip.setOpacity(1.0);
		tiptip.show();
		var tiptipArrow = $('tiptip_arrow');
		tiptipArrow.setOpacity(1.0);
		tiptipArrow.show();
		tiptipArrow.style.top = (position[1]+9)+'px';
		tiptipArrow.style.left = (position[0]+pass.getWidth()+12)+'px';
	},

	hideTiptip: function(){
		$('tiptip').hide();
		$('tiptip_arrow').hide();
	}

};

new Event.observe(document, 'dom:loaded', function(){
	Auth.highPerformance = $Jasmin.high;
	if(Prototype.Browser.Gecko){
		var supportedNavigator = Auth.isSupportedNavigator();
		if(supportedNavigator){
			if(localStorage.high==null){
				localStorage.high = $Jasmin.high;
			};
			Auth.highPerformance = localStorage.high;
		}
	};
	$('login').activate();
	new Event.observe('pass', 'keypress', function(e){
		var charcode = e.charCode;
		if(typeof charcode == 'undefined'){
			charcode = e.keyCode;
		}
		var character = String.fromCharCode(charcode);
		if(/^[A-Z]$/.test(character) && !e.shiftKey){
			Auth.showTiptip();
		} else {
			Auth.hideTiptip();
		}
	});
	new Event.observe('pass', 'keyup', function(e){
		if(e.keyCode==Event.KEY_RETURN){
			Auth.startSession();
			new Event.stop(e);
		}
	});
	new Event.observe('pass', 'keydown', function(e){
		if(e.keyCode==20){
			if($('tiptip').visible()){
				Auth.hideTiptip();
			} else {
				Auth.showTiptip();
			}
		}
	});
	new Event.observe('captcha', 'keyup', function(e){
		if(e.keyCode==Event.KEY_RETURN){
			Auth.startSession();
			new Event.stop(e);
		}
	});
	$('loginButton').observe('click', Auth.startSession);
	$('pass').observe('blur', function(){
		if(this.value==""){
			this.hide();
			$('pass_dummy').show();
		}
	});
	$('pass_dummy').observe('focus', function(){
		this.hide();
		$('pass').show();
		$('pass').activate();
	});
	if($('login').tagName=='SELECT'){
		$('login').observe('change', function(){
			this.blur();
			$("pass").activate();
		});
	};
	if($('captcha_lab').visible()){
		Auth.showCaptcha();
	};

});

new Event.observe(window, 'load', Auth.startPreload);
