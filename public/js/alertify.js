!function(e){"use strict";var t=13,n=27,i=112,s=123,o=37,a=39,l={modal:!0,basic:!1,frameless:!1,movable:!0,moveBounded:!1,resizable:!0,closable:!0,closableByDimmer:!0,maximizable:!0,startMaximized:!1,pinnable:!0,pinned:!0,padding:!0,overflow:!0,maintainFocus:!0,transition:"pulse",autoReset:!0,notifier:{delay:5,position:"bottom-right"},glossary:{title:"AlertifyJS",ok:"OK",cancel:"Cancel",acccpt:"Accept",deny:"Deny",confirm:"Confirm",decline:"Decline",close:"Close",maximize:"Maximize",restore:"Restore"},theme:{input:"ajs-input",ok:"ajs-ok",cancel:"ajs-cancel"}},r=[];function c(e,t){e.className+=" "+t}function d(e,t){for(var n=t.split(" "),i=0;i<n.length;i+=1)e.className=e.className.replace(" "+n[i],"")}function u(){return"rtl"===e.getComputedStyle(document.body).direction}function m(){return document.documentElement&&document.documentElement.scrollTop||document.body.scrollTop}function f(){return document.documentElement&&document.documentElement.scrollLeft||document.body.scrollLeft}function h(e){for(;e.lastChild;)e.removeChild(e.lastChild)}function p(e){if(null===e)return e;var t;if(Array.isArray(e)){t=[];for(var n=0;n<e.length;n+=1)t.push(p(e[n]));return t}if(e instanceof Date)return new Date(e.getTime());if(e instanceof RegExp)return(t=new RegExp(e.source)).global=e.global,t.ignoreCase=e.ignoreCase,t.multiline=e.multiline,t.lastIndex=e.lastIndex,t;if("object"==typeof e){for(var i in t={},e)e.hasOwnProperty(i)&&(t[i]=p(e[i]));return t}return e}function v(e,t){var n=e.elements.root;n.parentNode.removeChild(n),delete e.elements,e.settings=p(e.__settings),e.__init=t,delete e.__internal}var b=document.addEventListener?function(e,t,n,i){e.addEventListener(t,n,!0===i)}:document.attachEvent?function(e,t,n){e.attachEvent("on"+t,n)}:void 0,g=document.removeEventListener?function(e,t,n,i){e.removeEventListener(t,n,!0===i)}:document.detachEvent?function(e,t,n){e.detachEvent("on"+t,n)}:void 0,y=function(){var e,t,n=!1,i={animation:"animationend",OAnimation:"oAnimationEnd oanimationend",msAnimation:"MSAnimationEnd",MozAnimation:"animationend",WebkitAnimation:"webkitAnimationEnd"};for(e in i)if(void 0!==document.documentElement.style[e]){t=i[e],n=!0;break}return{type:t,supported:n}}();function _(e,t){return function(){if(arguments.length>0){for(var n=[],i=0;i<arguments.length;i+=1)n.push(arguments[i]);return n.push(e),t.apply(e,n)}return t.apply(e,[null,e])}}function k(e,t){return{index:e,button:t,cancel:!1}}var x=function(){var t,l,x=[],H=null,C=e.navigator.userAgent.indexOf("Safari")>-1&&e.navigator.userAgent.indexOf("Chrome")<0,T='<div class="ajs-dimmer"></div>',z='<div class="ajs-modal" tabindex="0"></div>',M='<div class="ajs-dialog" tabindex="0"></div>',O='<button class="ajs-reset"></button>',E='<div class="ajs-commands"><button class="ajs-pin"></button><button class="ajs-maximize"></button><button class="ajs-close"></button></div>',j='<div class="ajs-header"></div>',N='<div class="ajs-body"></div>',L='<div class="ajs-content"></div>',A='<div class="ajs-footer"></div>',I={primary:'<div class="ajs-primary ajs-buttons"></div>',auxiliary:'<div class="ajs-auxiliary ajs-buttons"></div>'},W='<button class="ajs-button"></button>',P='<div class="ajs-handle"></div>',B="alertify",R="ajs-",D="ajs-hidden",S="ajs-no-selection",F="ajs-no-overflow",U="ajs-no-padding",X="ajs-modeless",Y="ajs-movable",q="ajs-resizable",J="ajs-capture",K="ajs-closable",V="ajs-maximizable",G="ajs-pinnable",Q="ajs-unpinned",Z="ajs-maximized",$="ajs-in",ee="ajs-out",te="ajs-shake",ne="ajs-basic",ie="ajs-frameless";function se(e){if(!e.__internal){var t;delete e.__init,e.__settings||(e.__settings=p(e.settings)),null===H&&document.body.setAttribute("tabindex","0"),"function"==typeof e.setup?((t=e.setup()).options=t.options||{},t.focus=t.focus||{}):t={buttons:[],focus:{element:null,select:!1},options:{}},"object"!=typeof e.hooks&&(e.hooks={});var n=[];if(Array.isArray(t.buttons))for(var i=0;i<t.buttons.length;i+=1){var s=t.buttons[i],o={};for(var a in s)s.hasOwnProperty(a)&&(o[a]=s[a]);n.push(o)}var l=e.__internal={isOpen:!1,activeElement:document.body,timerIn:void 0,timerOut:void 0,buttons:n,focus:t.focus,options:{title:void 0,modal:void 0,basic:void 0,frameless:void 0,pinned:void 0,movable:void 0,moveBounded:void 0,resizable:void 0,autoReset:void 0,closable:void 0,closableByDimmer:void 0,maximizable:void 0,startMaximized:void 0,pinnable:void 0,transition:void 0,padding:void 0,overflow:void 0,onshow:void 0,onclose:void 0,onfocus:void 0},resetHandler:void 0,beginMoveHandler:void 0,beginResizeHandler:void 0,bringToFrontHandler:void 0,modalClickHandler:void 0,buttonsClickHandler:void 0,commandsClickHandler:void 0,transitionInHandler:void 0,transitionOutHandler:void 0,destroy:void 0},r={};r.root=document.createElement("div"),r.root.className=B+" "+D+" ",r.root.innerHTML=T+z,r.dimmer=r.root.firstChild,r.modal=r.root.lastChild,r.modal.innerHTML=M,r.dialog=r.modal.firstChild,r.dialog.innerHTML=O+E+j+N+A+P+O,r.reset=[],r.reset.push(r.dialog.firstChild),r.reset.push(r.dialog.lastChild),r.commands={},r.commands.container=r.reset[0].nextSibling,r.commands.pin=r.commands.container.firstChild,r.commands.maximize=r.commands.pin.nextSibling,r.commands.close=r.commands.maximize.nextSibling,r.header=r.commands.container.nextSibling,r.body=r.header.nextSibling,r.body.innerHTML=L,r.content=r.body.firstChild,r.footer=r.body.nextSibling,r.footer.innerHTML=I.auxiliary+I.primary,r.resizeHandle=r.footer.nextSibling,r.buttons={},r.buttons.auxiliary=r.footer.firstChild,r.buttons.primary=r.buttons.auxiliary.nextSibling,r.buttons.primary.innerHTML=W,r.buttonTemplate=r.buttons.primary.firstChild,r.buttons.primary.removeChild(r.buttonTemplate);for(var d=0;d<e.__internal.buttons.length;d+=1){var u=e.__internal.buttons[d];for(var m in x.indexOf(u.key)<0&&x.push(u.key),u.element=r.buttonTemplate.cloneNode(),u.element.innerHTML=u.text,"string"==typeof u.className&&""!==u.className&&c(u.element,u.className),u.attrs)"className"!==m&&u.attrs.hasOwnProperty(m)&&u.element.setAttribute(m,u.attrs[m]);"auxiliary"===u.scope?r.buttons.auxiliary.appendChild(u.element):r.buttons.primary.appendChild(u.element)}e.elements=r,l.resetHandler=_(e,ze),l.beginMoveHandler=_(e,De),l.beginResizeHandler=_(e,Ve),l.bringToFrontHandler=_(e,le),l.modalClickHandler=_(e,_e),l.buttonsClickHandler=_(e,He),l.commandsClickHandler=_(e,ue),l.transitionInHandler=_(e,Me),l.transitionOutHandler=_(e,Oe),e.set("title",void 0===t.options.title?w.defaults.glossary.title:t.options.title),e.set("modal",void 0===t.options.modal?w.defaults.modal:t.options.modal),e.set("basic",void 0===t.options.basic?w.defaults.basic:t.options.basic),e.set("frameless",void 0===t.options.frameless?w.defaults.frameless:t.options.frameless),e.set("movable",void 0===t.options.movable?w.defaults.movable:t.options.movable),e.set("moveBounded",void 0===t.options.moveBounded?w.defaults.moveBounded:t.options.moveBounded),e.set("resizable",void 0===t.options.resizable?w.defaults.resizable:t.options.resizable),e.set("autoReset",void 0===t.options.autoReset?w.defaults.autoReset:t.options.autoReset),e.set("closable",void 0===t.options.closable?w.defaults.closable:t.options.closable),e.set("closableByDimmer",void 0===t.options.closableByDimmer?w.defaults.closableByDimmer:t.options.closableByDimmer),e.set("maximizable",void 0===t.options.maximizable?w.defaults.maximizable:t.options.maximizable),e.set("startMaximized",void 0===t.options.startMaximized?w.defaults.startMaximized:t.options.startMaximized),e.set("pinnable",void 0===t.options.pinnable?w.defaults.pinnable:t.options.pinnable),e.set("pinned",void 0===t.options.pinned?w.defaults.pinned:t.options.pinned),e.set("transition",void 0===t.options.transition?w.defaults.transition:t.options.transition),e.set("padding",void 0===t.options.padding?w.defaults.padding:t.options.padding),e.set("overflow",void 0===t.options.overflow?w.defaults.overflow:t.options.overflow),"function"==typeof e.build&&e.build()}document.body.appendChild(e.elements.root)}function oe(){for(var e=0,t=0;t<r.length;t+=1){var n=r[t];(n.isModal()||n.isMaximized())&&(e+=1)}0===e?d(document.body,F):e>0&&document.body.className.indexOf(F)<0&&c(document.body,F)}function ae(e,t,n){"string"==typeof n&&d(e.elements.root,R+n),c(e.elements.root,R+t),H=e.elements.root.offsetWidth}function le(e,t){for(var n=r.indexOf(t)+1;n<r.length;n+=1)if(r[n].isModal())return;return document.body.lastChild!==t.elements.root&&(document.body.appendChild(t.elements.root),r.splice(r.indexOf(t),1),r.push(t),Te(t)),!1}function re(e,t,n,i){switch(t){case"title":e.setHeader(i);break;case"modal":!function(e){e.get("modal")?(d(e.elements.root,X),e.isOpen()&&(tt(e),ge(e),oe())):(c(e.elements.root,X),e.isOpen()&&(et(e),ge(e),oe()))}(e);break;case"basic":!function(e){e.get("basic")?c(e.elements.root,ne):d(e.elements.root,ne)}(e);break;case"frameless":!function(e){e.get("frameless")?c(e.elements.root,ie):d(e.elements.root,ie)}(e);break;case"pinned":!function(e){e.get("pinned")?(d(e.elements.root,Q),e.isOpen()&&be(e)):(c(e.elements.root,Q),e.isOpen()&&!e.isModal()&&ve(e))}(e);break;case"closable":!function(e){e.get("closable")?(c(e.elements.root,K),function(e){b(e.elements.modal,"click",e.__internal.modalClickHandler)}(e)):(d(e.elements.root,K),function(e){g(e.elements.modal,"click",e.__internal.modalClickHandler)}(e))}(e);break;case"maximizable":!function(e){e.get("maximizable")?c(e.elements.root,V):d(e.elements.root,V)}(e);break;case"pinnable":!function(e){e.get("pinnable")?c(e.elements.root,G):d(e.elements.root,G)}(e);break;case"movable":!function(e){e.get("movable")?(c(e.elements.root,Y),e.isOpen()&&nt(e)):(Ue(e),d(e.elements.root,Y),e.isOpen()&&it(e))}(e);break;case"resizable":!function(e){e.get("resizable")?(c(e.elements.root,q),e.isOpen()&&st(e)):(Ze(e),d(e.elements.root,q),e.isOpen()&&ot(e))}(e);break;case"transition":case"transition":ae(e,i,n);break;case"padding":i?d(e.elements.root,U):e.elements.root.className.indexOf(U)<0&&c(e.elements.root,U);break;case"overflow":i?d(e.elements.root,F):e.elements.root.className.indexOf(F)<0&&c(e.elements.root,F)}"function"==typeof e.hooks.onupdate&&e.hooks.onupdate.call(e,t,n,i)}function ce(e,t,n,i,s){var o,a={op:void 0,items:[]};if(void 0===s&&"string"==typeof i)a.op="get",t.hasOwnProperty(i)?(a.found=!0,a.value=t[i]):(a.found=!1,a.value=void 0);else if(a.op="set","object"==typeof i){var l=i;for(var r in l)t.hasOwnProperty(r)?(t[r]!==l[r]&&(o=t[r],t[r]=l[r],n.call(e,r,o,l[r])),a.items.push({key:r,value:l[r],found:!0})):a.items.push({key:r,value:l[r],found:!1})}else{if("string"!=typeof i)throw new Error("args must be a string or object");t.hasOwnProperty(i)?(t[i]!==s&&(o=t[i],t[i]=s,n.call(e,i,o,s)),a.items.push({key:i,value:s,found:!0})):a.items.push({key:i,value:s,found:!1})}return a}function de(e){var t;xe(e,(function(e){return t=!0===e.invokeOnClose})),!t&&e.isOpen()&&e.close()}function ue(e,t){switch(e.srcElement||e.target){case t.elements.commands.pin:t.isPinned()?fe(t):me(t);break;case t.elements.commands.maximize:t.isMaximized()?pe(t):he(t);break;case t.elements.commands.close:de(t)}return!1}function me(e){e.set("pinned",!0)}function fe(e){e.set("pinned",!1)}function he(e){c(e.elements.root,Z),e.isOpen()&&oe()}function pe(e){d(e.elements.root,Z),e.isOpen()&&oe()}function ve(e){var t=f();e.elements.modal.style.marginTop=m()+"px",e.elements.modal.style.marginLeft=t+"px",e.elements.modal.style.marginRight=-t+"px"}function be(e){var t=parseInt(e.elements.modal.style.marginTop,10),n=parseInt(e.elements.modal.style.marginLeft,10);if(e.elements.modal.style.marginTop="",e.elements.modal.style.marginLeft="",e.elements.modal.style.marginRight="",e.isOpen()){var i=0,s=0;""!==e.elements.dialog.style.top&&(i=parseInt(e.elements.dialog.style.top,10)),e.elements.dialog.style.top=i+(t-m())+"px",""!==e.elements.dialog.style.left&&(s=parseInt(e.elements.dialog.style.left,10)),e.elements.dialog.style.left=s+(n-f())+"px"}}function ge(e){e.get("modal")||e.get("pinned")?be(e):ve(e)}var ye=!1;function _e(e,t){var n=e.srcElement||e.target;return ye||n!==t.elements.modal||!0!==t.get("closableByDimmer")||de(t),ye=!1,!1}var ke=!1;function xe(e,t){for(var n=0;n<e.__internal.buttons.length;n+=1){var i=e.__internal.buttons[n];if(!i.element.disabled&&t(i)){var s=k(n,i);"function"==typeof e.callback&&e.callback.apply(e,[s]),!1===s.cancel&&e.close();break}}}function He(e,t){var n=e.srcElement||e.target;xe(t,(function(e){return e.element===n&&(ke=!0)}))}function we(e){if(!ke){var t=r[r.length-1],i=e.keyCode;return 0===t.__internal.buttons.length&&i===n&&!0===t.get("closable")?(de(t),!1):x.indexOf(i)>-1?(xe(t,(function(e){return e.key===i})),!1):void 0}ke=!1}function Ce(e){var t=r[r.length-1],n=e.keyCode;if(n===o||n===a){for(var l=t.__internal.buttons,c=0;c<l.length;c+=1)if(document.activeElement===l[c].element)switch(n){case o:return void l[(c||l.length)-1].element.focus();case a:return void l[(c+1)%l.length].element.focus()}}else if(n<s+1&&n>i-1&&x.indexOf(n)>-1)return e.preventDefault(),e.stopPropagation(),xe(t,(function(e){return e.key===n})),!1}function Te(e,t){if(t)t.focus();else{var n=e.__internal.focus,i=n.element;switch(typeof n.element){case"number":e.__internal.buttons.length>n.element&&(i=!0===e.get("basic")?e.elements.reset[0]:e.__internal.buttons[n.element].element);break;case"string":i=e.elements.body.querySelector(n.element);break;case"function":i=n.element.call(e)}null==i&&0===e.__internal.buttons.length&&(i=e.elements.reset[0]),i&&i.focus&&(i.focus(),n.select&&i.select&&i.select())}}function ze(e,t){if(!t)for(var n=r.length-1;n>-1;n-=1)if(r[n].isModal()){t=r[n];break}if(t&&t.isModal()){var i,s=e.srcElement||e.target,o=s===t.elements.reset[1]||0===t.__internal.buttons.length&&s===document.body;o&&(t.get("maximizable")?i=t.elements.commands.maximize:t.get("closable")&&(i=t.elements.commands.close)),void 0===i&&("number"==typeof t.__internal.focus.element?s===t.elements.reset[0]?i=t.elements.buttons.auxiliary.firstChild||t.elements.buttons.primary.firstChild:o&&(i=t.elements.reset[0]):s===t.elements.reset[0]&&(i=t.elements.buttons.primary.lastChild||t.elements.buttons.auxiliary.lastChild)),Te(t,i)}}function Me(n,i){clearTimeout(i.__internal.timerIn),Te(i),e.scrollTo(t,l),ke=!1,"function"==typeof i.get("onfocus")&&i.get("onfocus").call(i),g(i.elements.dialog,y.type,i.__internal.transitionInHandler),d(i.elements.root,$)}function Oe(e,t){clearTimeout(t.__internal.timerOut),g(t.elements.dialog,y.type,t.__internal.transitionOutHandler),Ue(t),Ze(t),t.isMaximized()&&!t.get("startMaximized")&&pe(t),w.defaults.maintainFocus&&t.__internal.activeElement&&(t.__internal.activeElement.focus(),t.__internal.activeElement=null),"function"==typeof t.__internal.destroy&&t.__internal.destroy.apply(t)}var Ee=null,je=0,Ne=0,Le="pageX",Ae="pageY",Ie=null,We=!1,Pe=null;function Be(e,t){var n=e[Le]-je,i=e[Ae]-Ne;We&&(i-=document.body.scrollTop),t.style.left=n+"px",t.style.top=i+"px"}function Re(e,t){var n=e[Le]-je,i=e[Ae]-Ne;We&&(i-=document.body.scrollTop),t.style.left=Math.min(Ie.maxLeft,Math.max(Ie.minLeft,n))+"px",t.style.top=We?Math.min(Ie.maxTop,Math.max(Ie.minTop,i))+"px":Math.max(Ie.minTop,i)+"px"}function De(e,t){if(null===Xe&&!t.isMaximized()&&t.get("movable")){var n,i=0,s=0;if("touchstart"===e.type?(e.preventDefault(),n=e.targetTouches[0],Le="clientX",Ae="clientY"):0===e.button&&(n=e),n){var o=t.elements.dialog;if(c(o,J),o.style.left&&(i=parseInt(o.style.left,10)),o.style.top&&(s=parseInt(o.style.top,10)),je=n[Le]-i,Ne=n[Ae]-s,t.isModal()?Ne+=t.elements.modal.scrollTop:t.isPinned()&&(Ne-=document.body.scrollTop),t.get("moveBounded")){var a=o,l=-i,r=-s;do{l+=a.offsetLeft,r+=a.offsetTop}while(a=a.offsetParent);Ie={maxLeft:l,minLeft:-l,maxTop:document.documentElement.clientHeight-o.clientHeight-r,minTop:-r},Pe=Re}else Ie=null,Pe=Be;return We=!t.isModal()&&t.isPinned(),Ee=t,Pe(n,o),c(document.body,S),!1}}}function Se(e){var t;Ee&&("touchmove"===e.type?(e.preventDefault(),t=e.targetTouches[0]):0===e.button&&(t=e),t&&Pe(t,Ee.elements.dialog))}function Fe(){if(Ee){var e=Ee.elements.dialog;Ee=Ie=null,d(document.body,S),d(e,J)}}function Ue(e){Ee=null;var t=e.elements.dialog;t.style.left=t.style.top=""}var Xe=null,Ye=Number.Nan,qe=0,Je=0,Ke=0;function Ve(e,t){var n;if(!t.isMaximized()&&("touchstart"===e.type?(e.preventDefault(),n=e.targetTouches[0]):0===e.button&&(n=e),n)){Xe=t,Ke=t.elements.resizeHandle.offsetHeight/2;var i=t.elements.dialog;return c(i,J),Ye=parseInt(i.style.left,10),i.style.height=i.offsetHeight+"px",i.style.minHeight=t.elements.header.offsetHeight+t.elements.footer.offsetHeight+"px",i.style.width=(qe=i.offsetWidth)+"px","none"!==i.style.maxWidth&&(i.style.minWidth=(Je=i.offsetWidth)+"px"),i.style.maxWidth="none",c(document.body,S),!1}}function Ge(e){var t;Xe&&("touchmove"===e.type?(e.preventDefault(),t=e.targetTouches[0]):0===e.button&&(t=e),t&&function(e,t,n){var i,s,o=t,a=0,l=0;do{a+=o.offsetLeft,l+=o.offsetTop}while(o=o.offsetParent);!0===n?(i=e.pageX,s=e.pageY):(i=e.clientX,s=e.clientY);var r=u();if(r&&(i=document.body.offsetWidth-i,isNaN(Ye)||(a=document.body.offsetWidth-a-t.offsetWidth)),t.style.height=s-l+Ke+"px",t.style.width=i-a+Ke+"px",!isNaN(Ye)){var c=.5*Math.abs(t.offsetWidth-qe);r&&(c*=-1),t.offsetWidth>qe?t.style.left=Ye+c+"px":t.offsetWidth>=Je&&(t.style.left=Ye-c+"px")}}(t,Xe.elements.dialog,!Xe.get("modal")&&!Xe.get("pinned")))}function Qe(){if(Xe){var e=Xe.elements.dialog;Xe=null,d(document.body,S),d(e,J),ye=!0}}function Ze(e){Xe=null;var t=e.elements.dialog;"none"===t.style.maxWidth&&(t.style.maxWidth=t.style.minWidth=t.style.width=t.style.height=t.style.minHeight=t.style.left="",Ye=Number.Nan,qe=Je=Ke=0)}function $e(){for(var e=0;e<r.length;e+=1){var t=r[e];t.get("autoReset")&&(Ue(t),Ze(t))}}function et(e){b(e.elements.dialog,"focus",e.__internal.bringToFrontHandler,!0)}function tt(e){g(e.elements.dialog,"focus",e.__internal.bringToFrontHandler,!0)}function nt(e){b(e.elements.header,"mousedown",e.__internal.beginMoveHandler),b(e.elements.header,"touchstart",e.__internal.beginMoveHandler)}function it(e){g(e.elements.header,"mousedown",e.__internal.beginMoveHandler),g(e.elements.header,"touchstart",e.__internal.beginMoveHandler)}function st(e){b(e.elements.resizeHandle,"mousedown",e.__internal.beginResizeHandler),b(e.elements.resizeHandle,"touchstart",e.__internal.beginResizeHandler)}function ot(e){g(e.elements.resizeHandle,"mousedown",e.__internal.beginResizeHandler),g(e.elements.resizeHandle,"touchstart",e.__internal.beginResizeHandler)}return{__init:se,isOpen:function(){return this.__internal.isOpen},isModal:function(){return this.elements.root.className.indexOf(X)<0},isMaximized:function(){return this.elements.root.className.indexOf(Z)>-1},isPinned:function(){return this.elements.root.className.indexOf(Q)<0},maximize:function(){return this.isMaximized()||he(this),this},restore:function(){return this.isMaximized()&&pe(this),this},pin:function(){return this.isPinned()||me(this),this},unpin:function(){return this.isPinned()&&fe(this),this},bringToFront:function(){return le(0,this),this},moveTo:function(e,t){if(!isNaN(e)&&!isNaN(t)){var n=this.elements.dialog,i=n,s=0,o=0;n.style.left&&(s-=parseInt(n.style.left,10)),n.style.top&&(o-=parseInt(n.style.top,10));do{s+=i.offsetLeft,o+=i.offsetTop}while(i=i.offsetParent);var a=e-s,l=t-o;u()&&(a*=-1),n.style.left=a+"px",n.style.top=l+"px"}return this},resizeTo:function(e,t){var n=parseFloat(e),i=parseFloat(t),s=/(\d*\.\d+|\d+)%/;if(!isNaN(n)&&!isNaN(i)&&!0===this.get("resizable")){(""+e).match(s)&&(n=n/100*document.documentElement.clientWidth),(""+t).match(s)&&(i=i/100*document.documentElement.clientHeight);var o=this.elements.dialog;"none"!==o.style.maxWidth&&(o.style.minWidth=(Je=o.offsetWidth)+"px"),o.style.maxWidth="none",o.style.minHeight=this.elements.header.offsetHeight+this.elements.footer.offsetHeight+"px",o.style.width=n+"px",o.style.height=i+"px"}return this},setting:function(e,t){var n=this,i=ce(this,this.__internal.options,(function(e,t,i){re(n,e,t,i)}),e,t);if("get"===i.op)return i.found?i.value:void 0!==this.settings?ce(this,this.settings,this.settingUpdated||function(){},e,t).value:void 0;if("set"===i.op){if(i.items.length>0)for(var s=this.settingUpdated||function(){},o=0;o<i.items.length;o+=1){var a=i.items[o];a.found||void 0===this.settings||ce(this,this.settings,s,a.key,a.value)}return this}},set:function(e,t){return this.setting(e,t),this},get:function(e){return this.setting(e)},setHeader:function(t){return"string"==typeof t?(h(this.elements.header),this.elements.header.innerHTML=t):t instanceof e.HTMLElement&&this.elements.header.firstChild!==t&&(h(this.elements.header),this.elements.header.appendChild(t)),this},setContent:function(t){return"string"==typeof t?(h(this.elements.content),this.elements.content.innerHTML=t):t instanceof e.HTMLElement&&this.elements.content.firstChild!==t&&(h(this.elements.content),this.elements.content.appendChild(t)),this},showModal:function(e){return this.show(!0,e)},show:function(n,i){if(se(this),this.__internal.isOpen){Ue(this),Ze(this),c(this.elements.dialog,te);var s=this;setTimeout((function(){d(s.elements.dialog,te)}),200)}else{if(this.__internal.isOpen=!0,r.push(this),w.defaults.maintainFocus&&(this.__internal.activeElement=document.activeElement),"function"==typeof this.prepare&&this.prepare(),a=this,1===r.length&&(b(e,"resize",$e),b(document.body,"keyup",we),b(document.body,"keydown",Ce),b(document.body,"focus",ze),b(document.documentElement,"mousemove",Se),b(document.documentElement,"touchmove",Se),b(document.documentElement,"mouseup",Fe),b(document.documentElement,"touchend",Fe),b(document.documentElement,"mousemove",Ge),b(document.documentElement,"touchmove",Ge),b(document.documentElement,"mouseup",Qe),b(document.documentElement,"touchend",Qe)),b(a.elements.commands.container,"click",a.__internal.commandsClickHandler),b(a.elements.footer,"click",a.__internal.buttonsClickHandler),b(a.elements.reset[0],"focus",a.__internal.resetHandler),b(a.elements.reset[1],"focus",a.__internal.resetHandler),ke=!0,b(a.elements.dialog,y.type,a.__internal.transitionInHandler),a.get("modal")||et(a),a.get("resizable")&&st(a),a.get("movable")&&nt(a),void 0!==n&&this.set("modal",n),t=f(),l=m(),oe(),"string"==typeof i&&""!==i&&(this.__internal.className=i,c(this.elements.root,i)),this.get("startMaximized")?this.maximize():this.isMaximized()&&pe(this),ge(this),d(this.elements.root,ee),c(this.elements.root,$),clearTimeout(this.__internal.timerIn),this.__internal.timerIn=setTimeout(this.__internal.transitionInHandler,y.supported?1e3:100),C){var o=this.elements.root;o.style.display="none",setTimeout((function(){o.style.display="block"}),0)}H=this.elements.root.offsetWidth,d(this.elements.root,D),"function"==typeof this.hooks.onshow&&this.hooks.onshow.call(this),"function"==typeof this.get("onshow")&&this.get("onshow").call(this)}var a;return this},close:function(){var t;return this.__internal.isOpen&&(t=this,1===r.length&&(g(e,"resize",$e),g(document.body,"keyup",we),g(document.body,"keydown",Ce),g(document.body,"focus",ze),g(document.documentElement,"mousemove",Se),g(document.documentElement,"mouseup",Fe),g(document.documentElement,"mousemove",Ge),g(document.documentElement,"mouseup",Qe)),g(t.elements.commands.container,"click",t.__internal.commandsClickHandler),g(t.elements.footer,"click",t.__internal.buttonsClickHandler),g(t.elements.reset[0],"focus",t.__internal.resetHandler),g(t.elements.reset[1],"focus",t.__internal.resetHandler),b(t.elements.dialog,y.type,t.__internal.transitionOutHandler),t.get("modal")||tt(t),t.get("movable")&&it(t),t.get("resizable")&&ot(t),d(this.elements.root,$),c(this.elements.root,ee),clearTimeout(this.__internal.timerOut),this.__internal.timerOut=setTimeout(this.__internal.transitionOutHandler,y.supported?1e3:100),c(this.elements.root,D),H=this.elements.modal.offsetWidth,void 0!==this.__internal.className&&""!==this.__internal.className&&d(this.elements.root,this.__internal.className),"function"==typeof this.hooks.onclose&&this.hooks.onclose.call(this),"function"==typeof this.get("onclose")&&this.get("onclose").call(this),r.splice(r.indexOf(this),1),this.__internal.isOpen=!1,oe()),this},closeOthers:function(){return w.closeAll(this),this},destroy:function(){return this.__internal.isOpen?(this.__internal.destroy=function(){v(this,se)},this.close()):v(this,se),this}}}(),H=function(){var t,n=[],i="alertify-notifier",s="ajs-message",o="ajs-top",a="ajs-right",l="ajs-bottom",r="ajs-left",u="ajs-visible";function m(e){e.__internal||(e.__internal={position:w.defaults.notifier.position,delay:w.defaults.notifier.delay},t=document.createElement("DIV"),p(e)),t.parentNode!==document.body&&document.body.appendChild(t)}function f(e){e.__internal.pushed=!0,n.push(e)}function p(e){switch(t.className=i,e.__internal.position){case"top-right":c(t,o+" "+a);break;case"top-left":c(t,o+" "+r);break;case"bottom-left":c(t,l+" "+r);break;default:c(t,l+" "+a)}}function v(i,s){function o(e,t){t.dismiss(!0)}function a(e,n){g(n.element,y.type,a),t.removeChild(n.element)}function l(e){clearTimeout(e.__internal.timer),clearTimeout(e.__internal.transitionTimeout)}return r={element:i,push:function(e,n){if(!this.__internal.pushed){var i,s;switch(f(this),l(this),arguments.length){case 0:s=this.__internal.delay;break;case 1:"number"==typeof e?s=e:(i=e,s=this.__internal.delay);break;case 2:i=e,s=n}return void 0!==i&&this.setContent(i),H.__internal.position.indexOf("top")<0?t.appendChild(this.element):t.insertBefore(this.element,t.firstChild),this.element.offsetWidth,c(this.element,u),b(this.element,"click",this.__internal.clickHandler),this.delay(s)}return this},ondismiss:function(){},callback:s,dismiss:function(e){var i;return this.__internal.pushed&&(l(this),"function"==typeof this.ondismiss&&!1===this.ondismiss.call(this)||(g(this.element,"click",this.__internal.clickHandler),void 0!==this.element&&this.element.parentNode===t&&(this.__internal.transitionTimeout=setTimeout(this.__internal.transitionEndHandler,y.supported?1e3:100),d(this.element,u),"function"==typeof this.callback&&this.callback.call(this,e)),i=this,n.splice(n.indexOf(i),1),i.__internal.pushed=!1)),this},delay:function(e){if(l(this),this.__internal.delay=void 0===e||isNaN(+e)?H.__internal.delay:+e,this.__internal.delay>0){var t=this;this.__internal.timer=setTimeout((function(){t.dismiss()}),1e3*this.__internal.delay)}return this},setContent:function(t){return"string"==typeof t?(h(this.element),this.element.innerHTML=t):t instanceof e.HTMLElement&&this.element.firstChild!==t&&(h(this.element),this.element.appendChild(t)),this},dismissOthers:function(){return H.dismissAll(this),this}},r.__internal||(r.__internal={pushed:!1,delay:void 0,timer:void 0,clickHandler:void 0,transitionEndHandler:void 0,transitionTimeout:void 0},r.__internal.clickHandler=_(r,o),r.__internal.transitionEndHandler=_(r,a)),r;var r}return{setting:function(e,t){if(m(this),void 0===t)return this.__internal[e];switch(e){case"position":this.__internal.position=t,p(this);break;case"delay":this.__internal.delay=t}return this},set:function(e,t){return this.setting(e,t),this},get:function(e){return this.setting(e)},create:function(e,t){m(this);var n=document.createElement("div");return n.className=s+("string"==typeof e&&""!==e?" ajs-"+e:""),v(n,t)},dismissAll:function(e){for(var t=n.slice(0),i=0;i<t.length;i+=1){var s=t[i];void 0!==e&&e===s||s.dismiss()}}}}();var w=new function(){var e={};function t(e,t){for(var n in t)t.hasOwnProperty(n)&&(e[n]=t[n]);return e}function n(t){var n=e[t].dialog;return n&&"function"==typeof n.__init&&n.__init(n),n}return{defaults:l,dialog:function(i,s,o,a){if("function"!=typeof s)return n(i);if(this.hasOwnProperty(i))throw new Error("alertify.dialog: name already exists");var l=function(n,i,s,o){var a={dialog:null,factory:i};return void 0!==o&&(a.factory=function(){return t(new e[o].factory,new i)}),s||(a.dialog=t(new a.factory,x)),e[n]=a}(i,s,o,a);this[i]=o?function(){if(0===arguments.length)return l.dialog;var e=t(new l.factory,x);return e&&"function"==typeof e.__init&&e.__init(e),e.main.apply(e,arguments),e.show.apply(e)}:function(){if(l.dialog&&"function"==typeof l.dialog.__init&&l.dialog.__init(l.dialog),0===arguments.length)return l.dialog;var e=l.dialog;return e.main.apply(l.dialog,arguments),e.show.apply(l.dialog)}},closeAll:function(e){for(var t=r.slice(0),n=0;n<t.length;n+=1){var i=t[n];void 0!==e&&e===i||i.close()}},setting:function(e,t,i){if("notifier"===e)return H.setting(t,i);var s=n(e);return s?s.setting(t,i):void 0},set:function(e,t,n){return this.setting(e,t,n)},get:function(e,t){return this.setting(e,t)},notify:function(e,t,n,i){return H.create(t,i).push(e,n)},message:function(e,t,n){return H.create(null,n).push(e,t)},success:function(e,t,n){return H.create("success",n).push(e,t)},error:function(e,t,n){return H.create("error",n).push(e,t)},warning:function(e,t,n){return H.create("warning",n).push(e,t)},dismissAll:function(){H.dismissAll()}}};w.dialog("alert",(function(){return{main:function(e,t,n){var i,s,o;switch(arguments.length){case 1:s=e;break;case 2:"function"==typeof t?(s=e,o=t):(i=e,s=t);break;case 3:i=e,s=t,o=n}return this.set("title",i),this.set("message",s),this.set("onok",o),this},setup:function(){return{buttons:[{text:w.defaults.glossary.ok,key:n,invokeOnClose:!0,className:w.defaults.theme.ok}],focus:{element:0,select:!1},options:{maximizable:!1,resizable:!1}}},build:function(){},prepare:function(){},setMessage:function(e){this.setContent(e)},settings:{message:void 0,onok:void 0,label:void 0},settingUpdated:function(e,t,n){switch(e){case"message":this.setMessage(n);break;case"label":this.__internal.buttons[0].element&&(this.__internal.buttons[0].element.innerHTML=n)}},callback:function(e){if("function"==typeof this.get("onok")){var t=this.get("onok").call(this,e);void 0!==t&&(e.cancel=!t)}}}})),w.dialog("confirm",(function(){var e={timer:null,index:null,text:null,duration:null,task:function(t,n){if(n.isOpen()){if(n.__internal.buttons[e.index].element.innerHTML=e.text+" (&#8207;"+e.duration+"&#8207;) ",e.duration-=1,-1===e.duration){i(n);var s=n.__internal.buttons[e.index],o=k(e.index,s);"function"==typeof n.callback&&n.callback.apply(n,[o]),!1!==o.close&&n.close()}}else i(n)}};function i(t){null!==e.timer&&(clearInterval(e.timer),e.timer=null,t.__internal.buttons[e.index].element.innerHTML=e.text)}function s(t,n,s){i(t),e.duration=s,e.index=n,e.text=t.__internal.buttons[n].element.innerHTML,e.timer=setInterval(_(t,e.task),1e3),e.task(null,t)}return{main:function(e,t,n,i){var s,o,a,l;switch(arguments.length){case 1:o=e;break;case 2:o=e,a=t;break;case 3:o=e,a=t,l=n;break;case 4:s=e,o=t,a=n,l=i}return this.set("title",s),this.set("message",o),this.set("onok",a),this.set("oncancel",l),this},setup:function(){return{buttons:[{text:w.defaults.glossary.ok,key:t,className:w.defaults.theme.ok},{text:w.defaults.glossary.cancel,key:n,invokeOnClose:!0,className:w.defaults.theme.cancel}],focus:{element:0,select:!1},options:{maximizable:!1,resizable:!1}}},build:function(){},prepare:function(){},setMessage:function(e){this.setContent(e)},settings:{message:null,labels:null,onok:null,oncancel:null,defaultFocus:null,reverseButtons:null},settingUpdated:function(e,t,n){switch(e){case"message":this.setMessage(n);break;case"labels":"ok"in n&&this.__internal.buttons[0].element&&(this.__internal.buttons[0].text=n.ok,this.__internal.buttons[0].element.innerHTML=n.ok),"cancel"in n&&this.__internal.buttons[1].element&&(this.__internal.buttons[1].text=n.cancel,this.__internal.buttons[1].element.innerHTML=n.cancel);break;case"reverseButtons":!0===n?this.elements.buttons.primary.appendChild(this.__internal.buttons[0].element):this.elements.buttons.primary.appendChild(this.__internal.buttons[1].element);break;case"defaultFocus":this.__internal.focus.element="ok"===n?0:1}},callback:function(e){var t;switch(i(this),e.index){case 0:"function"==typeof this.get("onok")&&void 0!==(t=this.get("onok").call(this,e))&&(e.cancel=!t);break;case 1:"function"==typeof this.get("oncancel")&&void 0!==(t=this.get("oncancel").call(this,e))&&(e.cancel=!t)}},autoOk:function(e){return s(this,0,e),this},autoCancel:function(e){return s(this,1,e),this}}})),w.dialog("prompt",(function(){var i=document.createElement("INPUT"),s=document.createElement("P");return{main:function(e,t,n,i,s){var o,a,l,r,c;switch(arguments.length){case 1:a=e;break;case 2:a=e,l=t;break;case 3:a=e,l=t,r=n;break;case 4:a=e,l=t,r=n,c=i;break;case 5:o=e,a=t,l=n,r=i,c=s}return this.set("title",o),this.set("message",a),this.set("value",l),this.set("onok",r),this.set("oncancel",c),this},setup:function(){return{buttons:[{text:w.defaults.glossary.ok,key:t,className:w.defaults.theme.ok},{text:w.defaults.glossary.cancel,key:n,invokeOnClose:!0,className:w.defaults.theme.cancel}],focus:{element:i,select:!0},options:{maximizable:!1,resizable:!1}}},build:function(){i.className=w.defaults.theme.input,i.setAttribute("type","text"),i.value=this.get("value"),this.elements.content.appendChild(s),this.elements.content.appendChild(i)},prepare:function(){},setMessage:function(t){"string"==typeof t?(h(s),s.innerHTML=t):t instanceof e.HTMLElement&&s.firstChild!==t&&(h(s),s.appendChild(t))},settings:{message:void 0,labels:void 0,onok:void 0,oncancel:void 0,value:"",type:"text",reverseButtons:void 0},settingUpdated:function(e,t,n){switch(e){case"message":this.setMessage(n);break;case"value":i.value=n;break;case"type":switch(n){case"text":case"color":case"date":case"datetime-local":case"email":case"month":case"number":case"password":case"search":case"tel":case"time":case"week":i.type=n;break;default:i.type="text"}break;case"labels":n.ok&&this.__internal.buttons[0].element&&(this.__internal.buttons[0].element.innerHTML=n.ok),n.cancel&&this.__internal.buttons[1].element&&(this.__internal.buttons[1].element.innerHTML=n.cancel);break;case"reverseButtons":!0===n?this.elements.buttons.primary.appendChild(this.__internal.buttons[0].element):this.elements.buttons.primary.appendChild(this.__internal.buttons[1].element)}},callback:function(e){var t;switch(e.index){case 0:this.settings.value=i.value,"function"==typeof this.get("onok")&&void 0!==(t=this.get("onok").call(this,e,this.settings.value))&&(e.cancel=!t);break;case 1:"function"==typeof this.get("oncancel")&&void 0!==(t=this.get("oncancel").call(this,e))&&(e.cancel=!t)}}}})),"object"==typeof module&&"object"==typeof module.exports?module.exports=w:"function"==typeof define&&define.amd?define([],(function(){return w})):e.alertify||(e.alertify=w)}("undefined"!=typeof window?window:this);