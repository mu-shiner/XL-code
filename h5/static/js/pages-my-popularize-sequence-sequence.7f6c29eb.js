(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-my-popularize-sequence-sequence"],{1131:function(t,e,i){"use strict";i.r(e);var a=i("6c6f"),n=i.n(a);for(var s in a)"default"!==s&&function(t){i.d(e,t,(function(){return a[t]}))}(s);e["default"]=n.a},1164:function(t,e,i){t.exports=i.p+"static/img/shade-box.030764b7.png"},"1de5":function(t,e,i){"use strict";t.exports=function(t,e){return e||(e={}),t=t&&t.__esModule?t.default:t,"string"!==typeof t?t:(/^['"].*['"]$/.test(t)&&(t=t.slice(1,-1)),e.hash&&(t+=e.hash),/["'() \t\n]/.test(t)||e.needQuotes?'"'.concat(t.replace(/"/g,'\\"').replace(/\n/g,"\\n"),'"'):t)}},"20a5":function(t,e,i){var a=i("a079");"string"===typeof a&&(a=[[t.i,a,""]]),a.locals&&(t.exports=a.locals);var n=i("4f06").default;n("885d63be",a,!0,{sourceMap:!1,shadowMode:!1})},"4b6b":function(t,e,i){"use strict";var a=i("20a5"),n=i.n(a);n.a},"5a2a":function(t,e,i){"use strict";i.r(e);var a=i("ec7d"),n=i("1131");for(var s in n)"default"!==s&&function(t){i.d(e,t,(function(){return n[t]}))}(s);i("4b6b");var r,c=i("f0c5"),o=Object(c["a"])(n["default"],a["b"],a["c"],!1,null,"1c90290c",null,!1,a["a"],r);e["default"]=o.exports},"6c6f":function(t,e,i){"use strict";var a=i("4ea4");Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var n=a(i("5530")),s=i("26cb"),r=a(i("93e8")),c={mixins:[r.default],data:function(){return{baseUrl:""}},mounted:function(){this.baseUrl=uni.$baseUrl,this.getLeaderboard()},methods:{getLeaderboard:function(){var t={token:uni.$token};this.$store.dispatch("my/getLeaderboard",t)}},computed:(0,n.default)({},(0,s.mapState)({leaderboard:function(t){return t.my.leaderboard}}))};e.default=c},"93e8":function(t,e,i){"use strict";Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var a={data:function(){return{screenHeight:0}},onLoad:function(){this.screenHeight=uni.getSystemInfoSync().windowHeight}};e.default=a},a079:function(t,e,i){var a=i("24fb"),n=i("1de5"),s=i("1164");e=a(!1);var r=n(s);e.push([t.i,'@charset "UTF-8";\r\n/**\r\n * 这里是uni-app内置的常用样式变量\r\n *\r\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\r\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\r\n *\r\n */\r\n/**\r\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\r\n *\r\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\r\n */\r\n/* 颜色变量 */\r\n/* 行为相关颜色 */\r\n/* 文字基本颜色 */\r\n/* 背景颜色 */\r\n/* 边框颜色 */\r\n/* 尺寸变量 */\r\n/* 文字尺寸 */\r\n/* 图片尺寸 */\r\n/* Border Radius */\r\n/* 水平间距 */\r\n/* 垂直间距 */\r\n/* 透明度 */\r\n/* 文章场景相关 */.myBox[data-v-1c90290c]{background-color:#f3f6fb}.box-top[data-v-1c90290c]{width:%?750?%;height:%?663?%;background-image:url('+r+");background-size:100% 100%}.box-top .watermark[data-v-1c90290c]{background-size:100% 100%;position:absolute}.box-top .text-title[data-v-1c90290c]{margin-left:%?46?%;padding-top:%?72?%;color:#fff}.box-top .text-title .text-1[data-v-1c90290c]{font-size:%?60?%}.box-top .text-title .text-2[data-v-1c90290c]{font-size:%?10?%}.box-top .list[data-v-1c90290c]{width:%?690?%;height:%?770?%;background-color:#fff;border-radius:%?30?%;margin:%?65?% auto 0 auto}.box-top .list .list-title[data-v-1c90290c]{padding-top:%?30?%}.box-top .list .list-title .title-text[data-v-1c90290c]{color:#999;font-size:%?22?%;display:flex;justify-content:space-between;margin-left:%?67?%;margin-right:%?72?%;margin-bottom:%?19?%}.box-top .list .list-title .title-line[data-v-1c90290c]{width:%?610?%;height:%?1?%;background-color:#dcdeff;margin:0 auto}.box-top .list .list-big[data-v-1c90290c]{width:%?610?%;height:%?699?%;margin:0 auto;overflow-x:hidden}.box-top .list .list-big .item[data-v-1c90290c]{height:%?70?%;display:flex;justify-content:space-between;margin:%?20?% %?26?% %?40?% %?20?%}.box-top .list .list-big .item .left[data-v-1c90290c]{display:flex}.box-top .list .list-big .item .left .left-num[data-v-1c90290c]{display:flex;align-items:center;margin-right:%?36?%}.box-top .list .list-big .item .left .left-num .num[data-v-1c90290c]{width:%?51?%;height:%?58?%;display:flex;align-items:center;justify-content:center}.box-top .list .list-big .item .left .left-img[data-v-1c90290c]{width:%?70?%;height:%?70?%;border-radius:50%;margin-right:%?18?%}.box-top .list .list-big .item .left .left-name[data-v-1c90290c]{display:flex;align-items:center}.box-top .list .list-big .item .right[data-v-1c90290c]{display:flex;align-items:center}",""]),t.exports=e},ec7d:function(t,e,i){"use strict";var a;i.d(e,"b",(function(){return n})),i.d(e,"c",(function(){return s})),i.d(e,"a",(function(){return a}));var n=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("v-uni-view",{staticClass:"myBox",style:{height:t.screenHeight+"px"}},[i("v-uni-view",{staticClass:"box-top"},[i("v-uni-view",{staticClass:"text-title"},[i("v-uni-text",{staticClass:"text-1"},[t._v("推广排行榜")]),i("br"),i("v-uni-text",{staticClass:"text-2"},[t._v("每天晚上0点更新")])],1),i("v-uni-view",{staticClass:"list"},[i("v-uni-view",{staticClass:"list-title"},[i("v-uni-view",{staticClass:"title-text"},[i("span",[t._v("排行")]),i("span",[t._v("累计推广奖励")])]),i("v-uni-view",{staticClass:"title-line"})],1),i("v-uni-view",{staticClass:"list-big"},t._l(t.leaderboard.list,(function(e,a){return i("v-uni-view",{key:e.users_id,staticClass:"item"},[i("v-uni-view",{staticClass:"left"},[i("v-uni-view",{staticClass:"left-num"},[0==a?i("v-uni-image",{staticClass:"num",attrs:{src:"/static/image/one.png"}}):t._e(),1==a?i("v-uni-image",{staticClass:"num",attrs:{src:"/static/image/two.png"}}):t._e(),2==a?i("v-uni-image",{staticClass:"num",attrs:{src:"/static/image/three.png"}}):t._e(),a+1>3?i("v-uni-view",{staticClass:"num"},[t._v(t._s(a+1))]):t._e()],1),i("v-uni-image",{staticClass:"left-img",attrs:{src:t.baseUrl+e.avatar}}),i("v-uni-view",{staticClass:"left-name"},[t._v(t._s(e.users_name))])],1),i("v-uni-view",{staticClass:"right"},[t._v(t._s(e.c))])],1)})),1)],1)],1)],1)},s=[]}}]);