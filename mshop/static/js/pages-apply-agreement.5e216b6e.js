(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-apply-agreement"],{"10c1":function(t,a,n){"use strict";(function(t){var e=n("4ea4");n("c975"),n("13d5"),n("4d63"),n("ac1f"),n("25f0"),n("466d"),n("5319"),n("1276"),Object.defineProperty(a,"__esModule",{value:!0}),a.default=void 0;var i=e(n("e8fc")),o=/^<([-A-Za-z0-9_]+)((?:\s+[a-zA-Z_:][-a-zA-Z0-9_:.]*(?:\s*=\s*(?:(?:"[^"]*")|(?:'[^']*')|[^>\s]+))?)*)\s*(\/?)>/,l=/^<\/([-A-Za-z0-9_]+)[^>]*>/,s=/([a-zA-Z_:][-a-zA-Z0-9_:.]*)(?:\s*=\s*(?:(?:"((?:\\.|[^"])*)")|(?:'((?:\\.|[^'])*)')|([^>\s]+)))?/g,f=m("area,base,basefont,br,col,frame,hr,img,input,link,meta,param,embed,command,keygen,source,track,wbr"),r=m("a,address,article,applet,aside,audio,blockquote,button,canvas,center,dd,del,dir,div,dl,dt,fieldset,figcaption,figure,footer,form,frameset,h1,h2,h3,h4,h5,h6,header,hgroup,hr,iframe,isindex,li,map,menu,noframes,noscript,object,ol,output,p,pre,section,script,table,tbody,td,tfoot,th,thead,tr,ul,video"),p=m("abbr,acronym,applet,b,basefont,bdo,big,br,button,cite,code,del,dfn,em,font,i,iframe,img,input,ins,kbd,label,map,object,q,s,samp,script,select,small,span,strike,strong,sub,sup,textarea,tt,u,var"),d=m("colgroup,dd,dt,li,options,p,td,tfoot,th,thead,tr"),c=m("checked,compact,declare,defer,disabled,ismap,multiple,nohref,noresize,noshade,nowrap,readonly,selected"),v=m("script,style");function y(t,a){var n,e,i,y=[],m=t;y.last=function(){return this[this.length-1]};while(t){if(e=!0,y.last()&&v[y.last()])t=t.replace(new RegExp("([\\s\\S]*?)</"+y.last()+"[^>]*>"),(function(t,n){return n=n.replace(/<!--([\s\S]*?)-->|<!\[CDATA\[([\s\S]*?)]]>/g,"$1$2"),a.chars&&a.chars(n),""})),g("",y.last());else if(0==t.indexOf("\x3c!--")?(n=t.indexOf("--\x3e"),n>=0&&(a.comment&&a.comment(t.substring(4,n)),t=t.substring(n+3),e=!1)):0==t.indexOf("</")?(i=t.match(l),i&&(t=t.substring(i[0].length),i[0].replace(l,g),e=!1)):0==t.indexOf("<")&&(i=t.match(o),i&&(t=t.substring(i[0].length),i[0].replace(o,h),e=!1)),e){n=t.indexOf("<");var u=n<0?t:t.substring(0,n);t=n<0?"":t.substring(n),a.chars&&a.chars(u)}if(t==m)throw"Parse Error: "+t;m=t}function h(t,n,e,i){if(n=n.toLowerCase(),r[n])while(y.last()&&p[y.last()])g("",y.last());if(d[n]&&y.last()==n&&g("",n),i=f[n]||!!i,i||y.push(n),a.start){var o=[];e.replace(s,(function(t,a){var n=arguments[2]?arguments[2]:arguments[3]?arguments[3]:arguments[4]?arguments[4]:c[a]?a:"";o.push({name:a,value:n,escaped:n.replace(/(^|[^\\])"/g,'$1\\"')})})),a.start&&a.start(n,o,i)}}function g(t,n){if(n){for(e=y.length-1;e>=0;e--)if(y[e]==n)break}else var e=0;if(e>=0){for(var i=y.length-1;i>=e;i--)a.end&&a.end(y[i]);y.length=e}}g()}function m(t){for(var a={},n=t.split(","),e=0;e<n.length;e++)a[n[e]]=!0;return a}function u(t){return t.replace(/<\?xml.*\?>\n/,"").replace(/<!doctype.*>\n/,"").replace(/<!DOCTYPE.*>\n/,"")}function h(t){t=t.replace(/<!--[\s\S]*-->/gi,"");return t}function g(t){t=t.replace(/\\/g,"").replace(/<img/g,'<img style="width:100% !important;display:block;"');return t=t.replace(/<img [^>]*src=['"]([^'"]+)[^>]*>/gi,(function(t,a){return'<img style="width:100% !important;display:block;" src="'+i.default.img(a)+'"/>'})),t}function b(t){t=t.replace(/style\s*=\s*["][^>]*;[^"]?/gi,(function(t,a){return t=t.replace(/[:](\s?)[\s\S]*/gi,(function(t,a){return t.replace(/"/g,"'")})),t}));return t}function x(t){return t.reduce((function(t,a){var n=a.value,e=a.name;return t[e]?t[e]=t[e]+" "+n:t[e]=n,t}),{})}function q(a){a=u(a),a=h(a),a=g(a),a=b(a);var n=[],e={node:"root",children:[]};return y(a,{start:function(t,a,i){var o={name:t};if(0!==a.length&&(o.attrs=x(a)),i){var l=n[0]||e;l.children||(l.children=[]),l.children.push(o)}else n.unshift(o)},end:function(a){var i=n.shift();if(i.name!==a&&t.error("invalid state: mismatch end tag"),0===n.length)e.children.push(i);else{var o=n[0];o.children||(o.children=[]),o.children.push(i)}},chars:function(t){var a={type:"text",text:t};if(0===n.length)e.children.push(a);else{var i=n[0];i.children||(i.children=[]),i.children.push(a)}},comment:function(t){var a={node:"comment",text:t},e=n[0];e.children||(e.children=[]),e.children.push(a)}}),e.children}var z=q;a.default=z}).call(this,n("5a52")["default"])},"25e2":function(t,a,n){"use strict";var e;n.d(a,"b",(function(){return i})),n.d(a,"c",(function(){return o})),n.d(a,"a",(function(){return e}));var i=function(){var t=this,a=t.$createElement,n=t._self._c||a;return n("v-uni-view",{staticClass:"agreement"},[n("v-uni-view",{staticClass:"agreement-title"},[t._v("签订入驻协议")]),n("v-uni-view",{staticClass:"agreement-content"},[n("v-uni-view",{staticClass:"agreement-item"},[t._v(t._s(t.title))]),n("v-uni-rich-text",{attrs:{nodes:t.content}})],1),n("v-uni-view",{staticClass:"agreement-btn"},[n("v-uni-view",{staticClass:"checkbox-wrap",on:{click:function(a){arguments[0]=a=t.$handleEvent(a),t.isChecked()}}},[n("v-uni-text",{staticClass:"checkbox iconfont",class:t.checked?"iconfuxuankuang1 color-base-text":"iconfuxuankuang2"}),n("v-uni-text",{staticClass:"color-base-text"},[t._v("我已阅读并同意以上协议")])],1),n("v-uni-button",{attrs:{type:"primary"},on:{click:function(a){arguments[0]=a=t.$handleEvent(a),t.toApplyInfo()}}},[t._v("下一步，填写申请信息")])],1)],1)},o=[]},"29f0":function(t,a,n){"use strict";n.r(a);var e=n("c1bc"),i=n.n(e);for(var o in e)"default"!==o&&function(t){n.d(a,t,(function(){return e[t]}))}(o);a["default"]=i.a},"7efb":function(t,a,n){"use strict";var e=n("c498"),i=n.n(e);i.a},b545:function(t,a,n){var e=n("24fb");a=e(!1),a.push([t.i,'@charset "UTF-8";\r\n/**\r\n * 你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\r\n * 建议使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\r\n */.uni-line-hide[data-v-68f184dc]{overflow:hidden;text-overflow:ellipsis;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical}.uni-using-hide[data-v-68f184dc]{overflow:hidden;width:100%;text-overflow:ellipsis;white-space:nowrap}.prevent-head-roll[data-v-68f184dc]{position:fixed;left:0;right:0;z-index:998}.flex-center[data-v-68f184dc], .page-btn-wrap[data-v-68f184dc], .page-btn-wrap .page-btn[data-v-68f184dc], .page-tips-wrap[data-v-68f184dc]{display:flex;justify-content:center;align-items:center}.flex-center-y[data-v-68f184dc], .flex-title[data-v-68f184dc], .app-card .card-title[data-v-68f184dc], .app-card .card-item[data-v-68f184dc]{display:flex;align-items:center}.text-ellipsis[data-v-68f184dc]{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.flex-title[data-v-68f184dc]{justify-content:space-between}.flex-title > .main[data-v-68f184dc]{flex:1}.flex-title > .ohter[data-v-68f184dc]{text-align:right}.has-arrow[data-v-68f184dc]::after{position:absolute;right:%?30?%;content:"";width:%?16?%;height:%?16?%;border-right:%?1?% solid #000;border-top:%?1?% solid #000;-webkit-transform:rotate(45deg);transform:rotate(45deg)}.has-active[data-v-68f184dc]:active{background-color:#f1f1f1}*[data-v-68f184dc]{margin:0;padding:0;box-sizing:border-box}.status-bar[data-v-68f184dc]{height:calc(0px + var(--window-top));width:100%}.page-wrap[data-v-68f184dc]{max-width:%?750?%;min-height:calc(100vh - var(--window-bottom) - var(--window-top) - 0px);background-color:#f3f4f8;position:relative;background-repeat:no-repeat;background-position:50%;background-size:100% 100%;padding-bottom:%?50?%}.padding-all[data-v-68f184dc]{padding:%?30?%}.padding-x[data-v-68f184dc]{padding:%?30?% 0}.padding-y[data-v-68f184dc]{padding:0 %?30?%}.dev-style[data-v-68f184dc]{-webkit-filter:grayscale(100%);-moz-filter:grayscale(100%);-ms-filter:grayscale(100%);-o-filter:grayscale(100%);filter:grayscale(100%);-webkit-filter:grey;filter:gray;color:grey}.triangle-box.show-top[data-v-68f184dc]::after{content:"";margin-left:%?10?%;display:inline-block;border-bottom:%?14?% solid #6f98bb;border-left:%?8?% solid transparent;border-right:%?8?% solid transparent}.triangle-box.show-bottom[data-v-68f184dc]::after{content:"";margin-left:%?10?%;display:inline-block;border-top:%?14?% solid #6f98bb;border-left:%?8?% solid transparent;border-right:%?8?% solid transparent}.app-card[data-v-68f184dc]{border-radius:%?16?%;background-color:#fff;margin-bottom:%?20?%}.app-card .card-title[data-v-68f184dc]{height:%?100?%;justify-content:space-between;font-size:%?30?%;color:#ff6a00}.app-card .card-title .main .card-title-icon[data-v-68f184dc]{width:%?36?%;margin-right:%?20?%}.app-card .card-item[data-v-68f184dc]{border-bottom:%?1?% solid #eee;min-height:%?80?%}.app-card .card-item.n-b-b[data-v-68f184dc]{border-bottom:none}.app-card .card-item[data-v-68f184dc]:last-child{border-bottom:none}.app-card .card-item .card-item-label[data-v-68f184dc]{width:%?180?%;height:%?80?%;line-height:%?80?%;font-size:%?28?%}.app-card .card-item .card-item-label.require[data-v-68f184dc]::after{content:"*";color:red}.app-card .card-item .card-item-value[data-v-68f184dc]{flex:1}.app-card .card-item .card-item-value .card-item-input[data-v-68f184dc]{font-size:%?28?%}.page-btn-wrap[data-v-68f184dc]{width:100%;height:%?120?%}.page-btn-wrap .page-btn[data-v-68f184dc]{color:#fff;width:%?500?%;height:%?80?%;background-color:#ff6a00;border-radius:%?60?%}.page-btn-wrap .page-btn[data-v-68f184dc]:active{background-color:rgba(255,106,0,.4)}.page-title[data-v-68f184dc]{height:%?100?%;font-size:%?32?%;padding:0 %?30?%}.page-tips-wrap[data-v-68f184dc]{width:100%;height:%?80?%;font-size:%?24?%;color:#ccc}.app-primary-color[data-v-68f184dc]{color:#ff6a00}uni-page-body[data-v-68f184dc]{background-color:#fff}.agreement[data-v-68f184dc]{width:100%;padding:%?50?% %?30?%;overflow:hidden;box-sizing:border-box}.agreement .agreement-title[data-v-68f184dc]{font-size:%?40?%;font-weight:700;text-align:center;margin-bottom:%?30?%}.agreement .agreement-content[data-v-68f184dc]{width:100%}.agreement .agreement-content .agreement-item[data-v-68f184dc]{font-size:%?30?%;text-align:justify}.agreement .agreement-btn[data-v-68f184dc]{text-align:center}.agreement .agreement-btn .checkbox-wrap .checkbox[data-v-68f184dc]{color:#909399;margin-right:%?10?%}.agreement .agreement-btn .checkbox-wrap uni-text[data-v-68f184dc]{vertical-align:middle;display:inline-block}.agreement .agreement-btn uni-button[data-v-68f184dc]{margin-top:%?40?%}body.?%PAGE?%[data-v-68f184dc]{background-color:#fff}',""]),t.exports=a},c1bc:function(t,a,n){"use strict";var e=n("4ea4");Object.defineProperty(a,"__esModule",{value:!0}),a.default=void 0;var i=e(n("10c1")),o={data:function(){return{type:1,checked:!1,title:"商家入驻协议",content:'<div style="position: relative;"><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">用户使用“NIUSHOP多商户入驻系统”前请认真阅读并理解本协议内容，本协议内容中以加粗方式显着标识的条款，请用户着重阅读、慎重考虑。</font></font></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""> </span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">1.</font></font><span style="font-family: 宋体;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">本协议的订立</font></font></span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""><span style="font-family: 宋体;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">符合本网站商家入驻标准的用户（以下简称</font></font></span><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">“商家”），在同意本协议全部条款后，方有资格使用“</font></font><span style="font-family: Calibri;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">NIUSHOP</font></font></span><span style="font-family: 宋体;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">多商户入驻系统”（以下简称“入驻系统”） 申请入驻。一经商家点击“我已阅读并且同意以上服务协议”按键，即意味着商家同意与本网站签订本协议并同意受本协议约束。</font></font></span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""> </span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">2.</font></font><span style="font-family: 宋体;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">入驻系统使用说明</font></font></span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">商家通过入驻系统提出入驻申请，并按照要求填写商家信息、提供商家资质资料后，由本网站审核并与有合作意向的商家联系协商合作相关事宜。</font></font></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""> </span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""> </span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">3.</font></font><span style="font-family: 宋体;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">商家权利义务</font></font></span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""> </span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">3.1 </font></font><span style="font-family: 宋体;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">商家应查看本网站公示的入驻商家标准，并确保资质符合本网站公示的基本要求，商家知悉并理解本网站将结合自身业务发展情况对商家进行选择。</font></font></span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""> </span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">3.2 </font></font><span style="font-family: 宋体;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">商家应按照本网站要求诚信提供入驻申请所需资料并如实填写相关信息，商家应确保提供的申请资料及信息真实、准确、完整、合法有效，经本网站审核通过后，商家不得擅自修改替换相应资料及主要信息。如商家提供的申请资料及信息不合法、不真实、不准确的，商家需承担因此引起的相应责任及后果，并且本网站有权立即终止商家使用入驻系统的权利。</font></font></span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""> </span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">3.3 </font></font><span style="font-family: 宋体;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">商家使用入驻系统提交的所有内容应不含有木马等软件病毒、政治宣传、或其他任何形式的“垃圾信息”、违法信息，且商家应按本网站规则使用入驻系统，不得从事影响或可能影响本网站或入驻系统正常运营的行为，否则，本网站有权清除前述内容，并有权立即终止商家使用入驻系统的权利。</font></font></span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""> </span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">3.4 </font></font><span style="font-family: 宋体;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">商家应注意查看入驻系统公示的入驻申请结果，如审核通过的商家，则按照本网站工作人员的通知按要求办理入驻的相关手续；如审批未通过的商家，则可自本网站通过入驻系统将审批结果告知商家（需商家登陆入驻系统查看）之日起 </font></font></span><span style="font-family: Calibri;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">15 </font></font></span><span style="font-family: 宋体;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">日内提出异议并提供相应资料，如审批仍未通过的，则商家同意提交的申请资料及信息本网站无需返还，由本网站自行处理。</font></font></span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""> </span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">3.5 </font></font><span style="font-family: 宋体;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">商家不得以任何形式擅自转让或授权他人使用自己在本网站的用户帐号使用入驻系统，否则由此产生的不利后果均由商家自行承担。</font></font></span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""> </span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">4.</font></font><span style="font-family: 宋体;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">本网站权利义务</font></font></span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""> </span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">4.1 </font></font><span style="font-family: 宋体;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">本网站开发的入驻系统仅为商家申请入驻的平台，商家正式入驻后，将在商家后台系统中进行相关操作。</font></font></span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""> </span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">4.2 </font></font><span style="font-family: 宋体;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">本网站有权对商家提供的资料及信息进行审核，并有权结合自身业务情况对合作商家进行选择；本网站对商家提交资料及信息的审核均不代表本网站对审核内容的真实、合法、准确、完整性作出的确认，商家应对提供资料及信息承担相应的法律责任。</font></font></span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""> </span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">4.3 </font></font><span style="font-family: 宋体;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">无论商家是否通过本网站的审核，本网站有权对商家提供的资料及信息予以留存并随时查阅，同时，本网站有义务对商家提供的资料予以保密，但国家行政机关、司法机关等国家机构调取资料的除外。</font></font></span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""> </span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">4.4 </font></font><span style="font-family: 宋体;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">本网站会尽力维护本系统信息的安全，但法律规定的不可抗力，以及因为黑客入侵、计算机病毒侵入发作等原因造成商家资料泄露、丢失、被盗用、被篡改的，本网站不承担任何责任。</font></font></span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""> </span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">4.5 </font></font><span style="font-family: 宋体;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">本网站应在现有技术支持的基础上确保入驻系统的正常运营，尽量避免入驻系统服务的中断给商家带来的不便。</font></font></span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""> </span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">5.</font></font><span style="font-family: 宋体;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">知识产权</font></font></span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""> </span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">5.1 </font></font><span style="font-family: 宋体;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">本网站开发的入驻系统及其包含的各类信息的知识产权归本网站所有者所有，受国家法律保护</font></font></span><span style="font-family: Calibri;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">,</font></font></span><span style="font-family: 宋体;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">本网站有权不时地对入驻系统的内容进行修改，并在入驻系统中公示，无须另行通知商家。</font></font></span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""> </span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">5.2 </font></font><span style="font-family: 宋体;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">在法律允许的最大限度范围内，本网站所有者对本协议及入驻系统涉及的内容拥有解释权。</font></font></span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""> </span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">5.3 </font></font><span style="font-family: 宋体;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">商家未经本网站所有者书面许可，不得擅自使用、非法全部或部分的复制、转载、抓取入驻系统中的信息，否则由此给本网站造成的损失，商家应予以全部赔偿。</font></font></span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""> </span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">6.</font></font><span style="font-family: 宋体;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">入驻系统服务的终止</font></font></span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""> </span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">6.1 </font></font><span style="font-family: 宋体;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">商家自行终止入驻申请，或商家经本网站审批未通过的，则入驻系统服务自行终止。</font></font></span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""> </span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">6.2 </font></font><span style="font-family: 宋体;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">商家使用本网站或入驻系统时，如违反相关法律法规或者违反本协议规定的，本网站有权随时终止商家使用入驻系统。</font></font></span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""> </span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">7.</font></font><span style="font-family: 宋体;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">本协议的修改</font></font></span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""> </span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">本协议可由本网站随时修订，并将修订后的协议公告于本网站及入驻系统，修订后的条款内容自公告时起生效，并成为本协议的一部分。</font></font></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""> </span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">8.</font></font><span style="font-family: 宋体;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">法律适用与争议解决</font></font></span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""> </span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">8.1 </font></font><span style="font-family: 宋体;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">本协议适用中华人民共和国法律。</font></font></span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""> </span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &amp;quot;Microsoft YaHei&amp;quot;;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">8.2 </font></font><span style="font-family: 宋体;" data-v-d9275680=""><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">因本协议产生的任何争议，由双方协商解决，协商不成的，任何一方有权向有管辖权的中华人民共和国大陆地区法院提起诉讼。</font></font></span></span></p><p><br></p><p><br></p><uni-resize-sensor><div><div></div></div><div><div></div></div></uni-resize-sensor></div>'}},onLoad:function(t){this.type=t.type||1},onShow:function(){},methods:{isChecked:function(){this.checked=!this.checked},toApplyInfo:function(){0==this.checked?this.$util.showToast({title:"请先同意协议"}):this.$util.redirectTo("/pages/apply/openinfo")},initData:function(){var t=this;this.$api.sendRequest({url:"/shopapi/apply/index",success:function(a){var n=a.data;0==a.code&&n.shop_apply_agreement.content?(t.title=n.shop_apply_agreement.title,t.content=(0,i.default)(n.shop_apply_agreement.content)):t.$util.showToast({title:a.message}),t.$refs.loadingCover&&t.$refs.loadingCover.hide()}})}}};a.default=o},c498:function(t,a,n){var e=n("b545");"string"===typeof e&&(e=[[t.i,e,""]]),e.locals&&(t.exports=e.locals);var i=n("4f06").default;i("84478cb8",e,!0,{sourceMap:!1,shadowMode:!1})},c8d7:function(t,a,n){"use strict";n.r(a);var e=n("25e2"),i=n("29f0");for(var o in i)"default"!==o&&function(t){n.d(a,t,(function(){return i[t]}))}(o);n("7efb");var l,s=n("f0c5"),f=Object(s["a"])(i["default"],e["b"],e["c"],!1,null,"68f184dc",null,!1,e["a"],l);a["default"]=f.exports}}]);