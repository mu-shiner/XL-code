(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-my-popularize-card-card"],{"0e30":function(t,n,e){"use strict";e.r(n);var a=e("cb4b"),r=e.n(a);for(var i in a)"default"!==i&&function(t){e.d(n,t,(function(){return a[t]}))}(i);n["default"]=r.a},"1de5":function(t,n,e){"use strict";t.exports=function(t,n){return n||(n={}),t=t&&t.__esModule?t.default:t,"string"!==typeof t?t:(/^['"].*['"]$/.test(t)&&(t=t.slice(1,-1)),n.hash&&(t+=n.hash),/["'() \t\n]/.test(t)||n.needQuotes?'"'.concat(t.replace(/"/g,'\\"').replace(/\n/g,"\\n"),'"'):t)}},"1f90":function(t,n,e){"use strict";var a;e.d(n,"b",(function(){return r})),e.d(n,"c",(function(){return i})),e.d(n,"a",(function(){return a}));var r=function(){var t=this,n=t.$createElement,e=t._self._c||n;return e("v-uni-view",[e("v-uni-view",{staticClass:"MyNav"},[e("v-uni-image",{staticClass:"MyNav-icon",attrs:{src:"/static/image/黑返回.png"},on:{click:function(n){arguments[0]=n=t.$handleEvent(n),t.toReturn.apply(void 0,arguments)}}}),e("v-uni-view",{staticClass:"MyNav-title"},[e("h4",{staticClass:"MyNav-text"},[t._v(t._s(t.title))])])],1)],1)},i=[]},2993:function(t,n,e){"use strict";e.d(n,"b",(function(){return r})),e.d(n,"c",(function(){return i})),e.d(n,"a",(function(){return a}));var a={MyNav:e("559e").default},r=function(){var t=this,n=t.$createElement,e=t._self._c||n;return e("v-uni-view",{staticClass:"myBox",style:{height:t.screenHeight+"px"}},[e("MyNav",{attrs:{title:"推广二维码"}}),e("v-uni-view",{staticClass:"box"},[e("v-uni-view",{staticClass:"box-title"},[t._v("邀请码")]),e("v-uni-view",{staticClass:"box-name"},[t._v("小猪佩奇")]),e("v-uni-view",{staticClass:"box-info"},[t._v("您的好友在注册时也可以填写邀请码")]),e("v-uni-image",{staticClass:"box-img",attrs:{src:t.url},on:{longpress:function(n){arguments[0]=n=t.$handleEvent(n),t.saveImg()}}}),e("v-uni-view",{staticClass:"box-download"},[t._v("长按保存二维码")])],1),e("v-uni-view",{staticClass:"bottom",on:{click:function(n){arguments[0]=n=t.$handleEvent(n),t.saveImg.apply(void 0,arguments)}}},[t._v("保存图片")])],1)},i=[]},"2e32":function(t,n,e){"use strict";var a=e("db7b"),r=e.n(a);r.a},"37d7":function(t,n,e){"use strict";var a=e("4ea4");Object.defineProperty(n,"__esModule",{value:!0}),n.default=void 0;var r=a(e("93e8")),i={mixins:[r.default],data:function(){return{url:"https://lanhu.oss-cn-beijing.aliyuncs.com/pscmf0wn1wtfs8kag8oohbbtqbfm0cpek5d3d3e38-ad47-41f1-b1c9-61d29b5cee32"}},methods:{saveImg:function(){window.location.href=this.url}}};n.default=i},"559e":function(t,n,e){"use strict";e.r(n);var a=e("1f90"),r=e("0e30");for(var i in r)"default"!==i&&function(t){e.d(n,t,(function(){return r[t]}))}(i);e("e595");var o,s=e("f0c5"),c=Object(s["a"])(r["default"],a["b"],a["c"],!1,null,"fae55b40",null,!1,a["a"],o);n["default"]=c.exports},7148:function(t,n,e){var a=e("72bb");"string"===typeof a&&(a=[[t.i,a,""]]),a.locals&&(t.exports=a.locals);var r=e("4f06").default;r("3769f423",a,!0,{sourceMap:!1,shadowMode:!1})},"72bb":function(t,n,e){var a=e("24fb");n=a(!1),n.push([t.i,'@charset "UTF-8";\r\n/**\r\n * 这里是uni-app内置的常用样式变量\r\n *\r\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\r\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\r\n *\r\n */\r\n/**\r\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\r\n *\r\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\r\n */\r\n/* 颜色变量 */\r\n/* 行为相关颜色 */\r\n/* 文字基本颜色 */\r\n/* 背景颜色 */\r\n/* 边框颜色 */\r\n/* 尺寸变量 */\r\n/* 文字尺寸 */\r\n/* 图片尺寸 */\r\n/* Border Radius */\r\n/* 水平间距 */\r\n/* 垂直间距 */\r\n/* 透明度 */\r\n/* 文章场景相关 */.MyNav[data-v-fae55b40]{width:100%;height:%?88?%;display:flex;align-items:center;-webkit-transform:translateY(%?-2?%);transform:translateY(%?-2?%)}.MyNav .MyNav-icon[data-v-fae55b40]{width:%?22?%;height:%?32?%;background-size:100%;margin-left:%?28?%}.MyNav .MyNav-title[data-v-fae55b40]{width:100%;display:flex;justify-content:center;margin-right:%?50?%}',""]),t.exports=n},"93e8":function(t,n,e){"use strict";Object.defineProperty(n,"__esModule",{value:!0}),n.default=void 0;var a={data:function(){return{screenHeight:0}},onLoad:function(){this.screenHeight=uni.getSystemInfoSync().windowHeight}};n.default=a},c245:function(t,n,e){var a=e("24fb"),r=e("1de5"),i=e("fb51");n=a(!1);var o=r(i);n.push([t.i,'@charset "UTF-8";\r\n/**\r\n * 这里是uni-app内置的常用样式变量\r\n *\r\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\r\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\r\n *\r\n */\r\n/**\r\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\r\n *\r\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\r\n */\r\n/* 颜色变量 */\r\n/* 行为相关颜色 */\r\n/* 文字基本颜色 */\r\n/* 背景颜色 */\r\n/* 边框颜色 */\r\n/* 尺寸变量 */\r\n/* 文字尺寸 */\r\n/* 图片尺寸 */\r\n/* Border Radius */\r\n/* 水平间距 */\r\n/* 垂直间距 */\r\n/* 透明度 */\r\n/* 文章场景相关 */.myBox[data-v-ca23b2f0]{background-color:#f3f6fb}.box[data-v-ca23b2f0]{width:%?540?%;height:%?700?%;background-color:#fff;border-radius:%?24?%;position:absolute;top:50%;left:50%;-webkit-transform:translate(-50%,-50%);transform:translate(-50%,-50%);text-align:center}.box .box-title[data-v-ca23b2f0]{margin-top:%?36?%;margin-bottom:%?35?%;color:#ec768d;font-size:%?32?%}.box .box-name[data-v-ca23b2f0]{color:#7b6afe;font-size:%?32?%;margin-bottom:%?39?%}.box .box-info[data-v-ca23b2f0]{font-size:%?26?%;color:#999;margin-bottom:%?50?%}.box .box-img[data-v-ca23b2f0]{width:%?331?%;height:%?331?%;background-size:100%;margin-bottom:%?21?%}.box .box-download[data-v-ca23b2f0]{font-size:%?26?%;color:#999}.bottom[data-v-ca23b2f0]{width:%?250?%;height:%?66?%;background:url('+o+") 100% no-repeat;background-size:100% 100%;color:#fff;display:flex;justify-content:center;align-items:center;position:fixed;bottom:%?83?%;left:%?250?%}",""]),t.exports=n},cb4b:function(t,n,e){"use strict";Object.defineProperty(n,"__esModule",{value:!0}),n.default=void 0;var a={name:"MyNav",props:["title"],data:function(){return{}},methods:{toReturn:function(){uni.$return()}}};n.default=a},db7b:function(t,n,e){var a=e("c245");"string"===typeof a&&(a=[[t.i,a,""]]),a.locals&&(t.exports=a.locals);var r=e("4f06").default;r("42f576a8",a,!0,{sourceMap:!1,shadowMode:!1})},e595:function(t,n,e){"use strict";var a=e("7148"),r=e.n(a);r.a},f0a9:function(t,n,e){"use strict";e.r(n);var a=e("37d7"),r=e.n(a);for(var i in a)"default"!==i&&function(t){e.d(n,t,(function(){return a[t]}))}(i);n["default"]=r.a},f75a:function(t,n,e){"use strict";e.r(n);var a=e("2993"),r=e("f0a9");for(var i in r)"default"!==i&&function(t){e.d(n,t,(function(){return r[t]}))}(i);e("2e32");var o,s=e("f0c5"),c=Object(s["a"])(r["default"],a["b"],a["c"],!1,null,"ca23b2f0",null,!1,a["a"],o);n["default"]=c.exports},fb51:function(t,n,e){t.exports=e.p+"static/img/QR-save-button.997c2dab.png"}}]);