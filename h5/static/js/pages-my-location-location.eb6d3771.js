(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-my-location-location"],{"0140":function(t,e,a){"use strict";var n=a("4ea4");a("4160"),a("159b"),Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var i=n(a("5530"));a("96cf");var r=n(a("1da1")),s=a("26cb"),o=n(a("93e8")),d={mixins:[o.default],data:function(){return{}},mounted:function(){this.getAddress()},methods:{getDelAddress:function(t){var e=this;return(0,r.default)(regeneratorRuntime.mark((function a(){var n,i;return regeneratorRuntime.wrap((function(a){while(1)switch(a.prev=a.next){case 0:return console.log(t),n={address_id:t.address_id,token:uni.$token},a.prev=2,a.next=5,e.$store.dispatch("my/getDelAddress",n);case 5:e.getAddress(),a.next=12;break;case 8:a.prev=8,a.t0=a["catch"](2),i=a.t0.message,uni.$showMsg(i);case 12:case"end":return a.stop()}}),a,null,[[2,8]])})))()},isDefault:function(t){var e=this;return(0,r.default)(regeneratorRuntime.mark((function a(){var n;return regeneratorRuntime.wrap((function(a){while(1)switch(a.prev=a.next){case 0:return e.address.forEach((function(t){t.is_default=0})),t.is_default=1,e.$set(t,"token",uni.$token),a.prev=3,a.next=6,e.$store.dispatch("my/getSaveAddress",t);case 6:e.getAddress(),a.next=13;break;case 9:a.prev=9,a.t0=a["catch"](3),n=a.t0.message,uni.$showMsg(n);case 13:case"end":return a.stop()}}),a,null,[[3,9]])})))()},getAddress:function(){var t={token:uni.$token};this.$store.dispatch("my/getAddress",t)},toAddOrRevamp:function(t,e){e&&uni.setStorageSync("amendLocation",e),uni.navigateTo({url:"./addOrRevamp/addOrRevamp?is=".concat(t)})}},computed:(0,i.default)({},(0,s.mapState)({address:function(t){return t.my.address}}))};e.default=d},"0c01":function(t,e,a){var n=a("24fb"),i=a("1de5"),r=a("105f");e=n(!1);var s=i(r);e.push([t.i,'@charset "UTF-8";\r\n/**\r\n * 这里是uni-app内置的常用样式变量\r\n *\r\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\r\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\r\n *\r\n */\r\n/**\r\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\r\n *\r\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\r\n */\r\n/* 颜色变量 */\r\n/* 行为相关颜色 */\r\n/* 文字基本颜色 */\r\n/* 背景颜色 */\r\n/* 边框颜色 */\r\n/* 尺寸变量 */\r\n/* 文字尺寸 */\r\n/* 图片尺寸 */\r\n/* Border Radius */\r\n/* 水平间距 */\r\n/* 垂直间距 */\r\n/* 透明度 */\r\n/* 文章场景相关 */.myBox[data-v-dd47b20a]{background-color:#f3f6fb;overflow-x:hidden}.box-top[data-v-dd47b20a]{width:%?750?%;height:%?164?%;background-color:#fff;display:flex;justify-content:space-between}.box-top .left-text[data-v-dd47b20a]{margin-left:%?30?%;margin-top:%?20?%}.box-top .left-text .text-name[data-v-dd47b20a]{font-size:%?30?%}.box-top .left-text .text-phone[data-v-dd47b20a]{font-size:%?30?%}.box-top .left-text .text-location[data-v-dd47b20a]{font-size:%?24?%}.box-top .right-select[data-v-dd47b20a]{display:flex;margin-right:%?30?%;margin-top:%?18?%}.box-top .right-select .image-wrapper_1[data-v-dd47b20a]{background-color:#745aef;border-radius:50%;height:%?32?%;width:%?32?%;display:flex}.box-top .right-select .thumbnail_1[data-v-dd47b20a]{width:%?16?%;height:%?10?%;margin:auto}.box-top .right-select .text_4[data-v-dd47b20a]{font-size:%?24?%;margin-left:%?20?%}.box_4[data-v-dd47b20a]{box-shadow:0 1px 0 0 #ebebeb;background-color:#fff;width:%?750?%;height:%?60?%;margin-top:%?1?%;flex-direction:row;display:flex;margin-bottom:%?22?%}.image-text_1[data-v-dd47b20a]{width:%?98?%;height:%?28?%;flex-direction:row;display:flex;justify-content:space-between;margin:%?15?% 0 0 %?492?%}.label_1[data-v-dd47b20a]{width:%?28?%;height:%?28?%}.text-group_2[data-v-dd47b20a]{width:%?51?%;height:%?25?%;overflow-wrap:break-word;color:#999;font-size:%?26?%;font-family:Adobe Heiti Std R;text-align:left;white-space:nowrap;line-height:%?26?%;margin-top:%?1?%}.image-text_2[data-v-dd47b20a]{width:%?90?%;height:%?26?%;flex-direction:row;display:flex;justify-content:space-between;margin:%?16?% %?29?% 0 %?41?%}.label_2[data-v-dd47b20a]{width:%?23?%;height:%?26?%}.text-group_3[data-v-dd47b20a]{width:%?47?%;height:%?22?%;overflow-wrap:break-word;color:#999;font-size:%?24?%;font-family:Adobe Heiti Std R;text-align:left;white-space:nowrap;line-height:%?24?%;margin-top:%?2?%}.text-wrapper_3[data-v-dd47b20a]{height:%?80?%;background:url('+s+") %?0?% %?0?% no-repeat;background-size:%?750?% %?88?%;display:flex;flex-direction:column;position:fixed;bottom:0;left:0;width:100%;width:%?750?%}.text_10[data-v-dd47b20a]{width:%?126?%;height:%?30?%;overflow-wrap:break-word;color:#fff;font-size:%?32?%;font-family:PingFang-SC-Medium;text-align:left;white-space:nowrap;line-height:%?32?%;margin:%?24?% 0 0 %?312?%}",""]),t.exports=e},"105f":function(t,e,a){t.exports=a.p+"static/img/location-addBottom.183b7f82.png"},"1de5":function(t,e,a){"use strict";t.exports=function(t,e){return e||(e={}),t=t&&t.__esModule?t.default:t,"string"!==typeof t?t:(/^['"].*['"]$/.test(t)&&(t=t.slice(1,-1)),e.hash&&(t+=e.hash),/["'() \t\n]/.test(t)||e.needQuotes?'"'.concat(t.replace(/"/g,'\\"').replace(/\n/g,"\\n"),'"'):t)}},2384:function(t,e,a){"use strict";var n;a.d(e,"b",(function(){return i})),a.d(e,"c",(function(){return r})),a.d(e,"a",(function(){return n}));var i=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("v-uni-view",{staticClass:"myBox",style:{height:2*t.screenHeight-80+"rpx"}},[t._l(t.address,(function(e,n){return a("v-uni-view",{key:e.address_id},[a("v-uni-view",{staticClass:"box-top"},[a("v-uni-view",{staticClass:"left-text"},[a("span",{staticClass:"text-name"},[t._v(t._s(e.name))]),a("br"),a("span",{staticClass:"text-phone"},[t._v(t._s(e.telephone))]),a("br"),a("span",{staticClass:"text-location"},[t._v(t._s(e.province_name+e.city_name+e.area_name+e.address))])]),a("v-uni-view",{staticClass:"right-select"},[1==e.is_default?a("v-uni-view",{staticStyle:{display:"flex"}},[a("v-uni-view",{staticClass:"image-wrapper_1"},[a("v-uni-image",{staticClass:"thumbnail_1",attrs:{src:"/static/image/pitchOn.png"}})],1),a("v-uni-text",{staticClass:"text_4",staticStyle:{color:"#6A43E9"},attrs:{lines:"1"}},[t._v("默认地址")])],1):t._e(),0==e.is_default?a("v-uni-view",{staticStyle:{display:"flex"},on:{click:function(a){arguments[0]=a=t.$handleEvent(a),t.isDefault(e)}}},[a("v-uni-view",{staticClass:"image-wrapper_1",staticStyle:{"background-color":"#fff","border-radius":"50%",border:"2rpx #999999 solid"}}),a("v-uni-text",{staticClass:"text_4",attrs:{lines:"1"}},[t._v("默认地址")])],1):t._e()],1)],1),a("v-uni-view",{staticClass:"box-bottom"},[a("v-uni-view",{staticClass:"box_4"},[a("v-uni-view",{staticClass:"image-text_1"},[a("v-uni-image",{staticClass:"label_1",attrs:{src:"/static/image/compile.png"}}),a("v-uni-text",{staticClass:"text-group_2",attrs:{lines:"1"},on:{click:function(a){arguments[0]=a=t.$handleEvent(a),t.toAddOrRevamp("编辑地址",e)}}},[t._v("编辑")])],1),a("v-uni-view",{staticClass:"image-text_2",on:{click:function(a){arguments[0]=a=t.$handleEvent(a),t.getDelAddress(e)}}},[a("v-uni-image",{staticClass:"label_2",attrs:{src:"/static/image/delete.png"}}),a("v-uni-text",{staticClass:"text-group_3",attrs:{lines:"1"}},[t._v("删除")])],1)],1)],1)],1)})),a("v-uni-view",{staticClass:"text-wrapper_3"},[a("v-uni-text",{staticClass:"text_10",attrs:{lines:"1"},on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.toAddOrRevamp("新增地址")}}},[t._v("新增地址")])],1)],2)},r=[]},3485:function(t,e,a){"use strict";a.r(e);var n=a("2384"),i=a("9f6f");for(var r in i)"default"!==r&&function(t){a.d(e,t,(function(){return i[t]}))}(r);a("9c61");var s,o=a("f0c5"),d=Object(o["a"])(i["default"],n["b"],n["c"],!1,null,"dd47b20a",null,!1,n["a"],s);e["default"]=d.exports},"93e8":function(t,e,a){"use strict";Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var n={data:function(){return{screenHeight:0}},onLoad:function(){this.screenHeight=uni.getSystemInfoSync().windowHeight}};e.default=n},"9c61":function(t,e,a){"use strict";var n=a("d8d1"),i=a.n(n);i.a},"9f6f":function(t,e,a){"use strict";a.r(e);var n=a("0140"),i=a.n(n);for(var r in n)"default"!==r&&function(t){a.d(e,t,(function(){return n[t]}))}(r);e["default"]=i.a},d8d1:function(t,e,a){var n=a("0c01");"string"===typeof n&&(n=[[t.i,n,""]]),n.locals&&(t.exports=n.locals);var i=a("4f06").default;i("87e2c7c2",n,!0,{sourceMap:!1,shadowMode:!1})}}]);