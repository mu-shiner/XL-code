(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-my-orderForm-orderForm"],{"5e5b":function(t,e,i){"use strict";i.r(e);var a=i("85f9"),n=i.n(a);for(var r in a)"default"!==r&&function(t){i.d(e,t,(function(){return a[t]}))}(r);e["default"]=n.a},8017:function(t,e,i){i("e25e"),i("ac1f"),i("5319"),i("1276");var a=function(t){for(var e=(t+"").split(""),i=0;i<13;i++)e[i]||(e[i]="0");t=1*e.join("");var a=6e4,n=60*a,r=24*n,s=30*r,o=(new Date).getTime(),l=o-t;if(l<0)return"不久前";var f=l/s,c=l/(7*r),d=l/r,u=l/n,g=l/a,h=function(t){return t<10?"0"+t:t};return f>12?function(){var e=new Date(t);return e.getFullYear()+"年"+h(e.getMonth()+1)+"月"+h(e.getDate())+"日"}():f>=1?parseInt(f)+"月前":c>=1?parseInt(c)+"周前":d>=1?parseInt(d)+"天前":u>=1?parseInt(u)+"小时前":g>=1?parseInt(g)+"分钟前":"刚刚"},n=function(t){for(var e=(t+"").split(""),i=0;i<13;i++)e[i]||(e[i]="0");var a=1*e.join(""),n=new Date(a),r=((new Date).getTime(),new Date,n.getFullYear()),s=n.getMonth()+1<10?"0"+(n.getMonth()+1):n.getMonth()+1,o=n.getDate()<10?"0"+n.getDate():n.getDate(),l=n.getDay(),f=n.getHours()<10?"0"+n.getHours():n.getHours(),c=n.getMinutes()<10?"0"+n.getMinutes():n.getMinutes(),d=new Date((new Date).setHours(0,0,0,0))/1e3*1e3,u=d-864e5,g=d-6048e5;if(d<a)return f<12?"上午"+f+":"+c:"下午"+f+":"+c;if(u<a&&a<d)return"昨天";if(g<a){if(0==l)return"星期日";if(1==l)return"星期一";if(2==l)return"星期二";if(3==l)return"星期三";if(4==l)return"星期四";if(5==l)return"星期五";if(6==l)return"星期六"}return r+"-"+s+"-"+o},r=function(t){for(var e=(t+"").split(""),i=0;i<13;i++)e[i]||(e[i]="0");var a=1*e.join(""),n=new Date(a),r=n.getFullYear(),s=n.getMonth()+1<10?"0"+(n.getMonth()+1):n.getMonth()+1,o=n.getDate()<10?"0"+n.getDate():n.getDate(),l=n.getHours()<10?"0"+n.getHours():n.getHours(),f=n.getMinutes()<10?"0"+n.getMinutes():n.getMinutes();return r+"-"+s+"-"+o+" "+l+":"+f},s=function(t){var e=t.replace(/\//g,"-");e=t.replace(/：/g,":");var i=new Date(e),a=i.getTime();return a},o=function(t,e){13==t.length&&(t=(t-0)/1e3),13==e.length&&(e=(e-0)/1e3);var i=0,a=Math.ceil((e-t)/1e3);if(a<60)return i=a<10?"0"+a:a,"00:"+i;if(60<a&&a<3600){var n=Math.floor(a/60),r=Math.floor(a-60*n);return n<10&&(n="0"+n),r<10&&(r="0"+r),n+":"+r}if(3600<a){var s=Math.floor(a/3600);n=Math.floor(a/60-60*s),r=Math.floor(a-3600*s-60*n);return s<10&&(s="0"+s),n<10&&(n="0"+n),r<10&&(r="0"+r),s+":"+n+":"+r}};t.exports={time1:a,time2:n,time3:r,time4:s,time5:o}},"85f9":function(t,e,i){"use strict";var a=i("4ea4");Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var n=a(i("5530")),r=i("26cb"),s=a(i("8017")),o=a(i("93e8")),l={mixins:[o.default],data:function(){return{state:1,baseUrl:""}},onLoad:function(){},mounted:function(){this.baseUrl=uni.$baseUrl,this.getMyOrderList()},methods:{time:function(t){return s.default.time3(t)},getMyOrderList:function(){var t={token:uni.$token};this.$store.dispatch("my/getMyOrderList",t)},toGoodsInfo:function(t){uni.setStorageSync("goodsInfo",t),uni.navigateTo({url:"/subpkg/goods-info/goods-info"})},change:function(t){this.state=t;var e={status:t,token:uni.$token};this.$store.dispatch("my/getMyOrderList",e)}},computed:(0,n.default)({},(0,r.mapState)({myOrderList:function(t){return t.my.myOrderList}}))};e.default=l},"8eaf":function(t,e,i){"use strict";i.r(e);var a=i("b96a"),n=i("5e5b");for(var r in n)"default"!==r&&function(t){i.d(e,t,(function(){return n[t]}))}(r);i("f588");var s,o=i("f0c5"),l=Object(o["a"])(n["default"],a["b"],a["c"],!1,null,"3c2dee1f",null,!1,a["a"],s);e["default"]=l.exports},"93e8":function(t,e,i){"use strict";Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var a={data:function(){return{screenHeight:0}},onLoad:function(){this.screenHeight=uni.getSystemInfoSync().windowHeight}};e.default=a},aaba:function(t,e,i){var a=i("24fb");e=a(!1),e.push([t.i,'@charset "UTF-8";\r\n/**\r\n * 这里是uni-app内置的常用样式变量\r\n *\r\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\r\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\r\n *\r\n */\r\n/**\r\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\r\n *\r\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\r\n */\r\n/* 颜色变量 */\r\n/* 行为相关颜色 */\r\n/* 文字基本颜色 */\r\n/* 背景颜色 */\r\n/* 边框颜色 */\r\n/* 尺寸变量 */\r\n/* 文字尺寸 */\r\n/* 图片尺寸 */\r\n/* Border Radius */\r\n/* 水平间距 */\r\n/* 垂直间距 */\r\n/* 透明度 */\r\n/* 文章场景相关 */.myBox[data-v-3c2dee1f]{background-color:#f3f6fb}.tabs_1[data-v-3c2dee1f]{background-color:#fff;height:%?96?%;width:%?750?%;display:flex;flex-direction:column}.text-wrapper_1[data-v-3c2dee1f]{width:%?617?%;height:%?29?%;flex-direction:row;display:flex;justify-content:space-between;margin:%?30?% 0 0 %?66?%}.text_2[data-v-3c2dee1f]{width:%?57?%;height:%?29?%;overflow-wrap:break-word;color:#000;font-size:%?30?%;font-family:PingFang-SC-Medium;text-align:left;white-space:nowrap;line-height:%?30?%}.text_3[data-v-3c2dee1f]{width:%?87?%;height:%?29?%;overflow-wrap:break-word;color:#000;font-size:%?30?%;font-family:PingFang-SC-Medium;text-align:left;white-space:nowrap;line-height:%?30?%;margin-left:%?100?%}.text_4[data-v-3c2dee1f]{width:%?87?%;height:%?29?%;overflow-wrap:break-word;color:#000;font-size:%?30?%;font-family:PingFang-SC-Medium;text-align:left;white-space:nowrap;line-height:%?30?%;margin-left:%?100?%}.text_5[data-v-3c2dee1f]{width:%?87?%;height:%?28?%;overflow-wrap:break-word;color:#000;font-size:%?30?%;font-family:PingFang-SC-Medium;text-align:right;white-space:nowrap;line-height:%?30?%;margin:%?1?% 0 0 %?99?%}.image-wrapper_1[data-v-3c2dee1f]{width:%?70?%;height:%?6?%;display:flex;flex-direction:row;margin:%?15?% 0 %?21?% 0}.image_2[data-v-3c2dee1f]{width:%?70?%;height:%?6?%}.box_2[data-v-3c2dee1f]{background-color:#fff;border-radius:%?24?%;height:%?300?%;width:%?690?%;display:flex;flex-direction:column;margin:%?29?% 0 0 %?31?%}.text-wrapper_2[data-v-3c2dee1f]{width:%?268?%;height:%?21?%;display:flex;flex-direction:row;margin:%?22?% 0 0 %?30?%}.text_6[data-v-3c2dee1f]{width:%?268?%;height:%?21?%;overflow-wrap:break-word;color:#999;font-size:%?22?%;font-family:PingFang-SC-Medium;text-align:left;white-space:nowrap;line-height:%?22?%}.block_2[data-v-3c2dee1f]{width:%?472?%;height:%?121?%;flex-direction:row;display:flex;justify-content:space-between;margin:%?34?% 0 0 %?31?%}.section_1[data-v-3c2dee1f]{background-color:#f3f6fb;background-size:100%;border-radius:%?10?%;width:%?120?%;height:%?120?%;display:flex;flex-direction:column}.section_2[data-v-3c2dee1f]{width:%?319?%;height:%?121?%;display:flex;flex-direction:column}.text_7[data-v-3c2dee1f]{width:%?318?%;height:%?25?%;overflow-wrap:break-word;color:#000;font-size:%?26?%;font-family:PingFang-SC-Medium;text-align:left;white-space:nowrap;line-height:%?40?%;margin-left:%?1?%}.text_8[data-v-3c2dee1f]{width:%?126?%;height:%?21?%;overflow-wrap:break-word;color:#999;font-size:%?22?%;font-family:PingFang-SC-Medium;text-align:left;white-space:nowrap;line-height:%?22?%;margin:%?28?% 0 0 %?1?%}.text-wrapper_3[data-v-3c2dee1f]{width:%?83?%;height:%?28?%;overflow-wrap:break-word;font-size:%?0?%;font-family:OPPOSans-B;text-align:left;white-space:nowrap;line-height:%?26?%;margin-top:%?19?%}.text_9[data-v-3c2dee1f]{width:%?83?%;height:%?28?%;overflow-wrap:break-word;color:#de3a21;font-size:%?26?%;font-family:OPPOSans-B;text-align:left;white-space:nowrap;line-height:%?26?%}.text_10[data-v-3c2dee1f]{width:%?83?%;height:%?28?%;overflow-wrap:break-word;color:#de3a21;font-size:%?32?%;font-family:OPPOSans-B;text-align:left;white-space:nowrap;line-height:%?32?%}.text-wrapper_4[data-v-3c2dee1f]{width:%?611?%;height:%?28?%;flex-direction:row;display:flex;margin:%?51?% 0 %?23?% %?29?%}.text_11[data-v-3c2dee1f]{width:%?75?%;height:%?25?%;overflow-wrap:break-word;color:#333;font-size:%?26?%;font-family:PingFang-SC-Bold;text-align:left;white-space:nowrap;line-height:%?26?%;margin-top:%?3?%}.text_12[data-v-3c2dee1f]{width:%?85?%;height:%?25?%;overflow-wrap:break-word;color:#de3a21;font-size:%?26?%;font-family:PingFang-SC-Medium;text-align:left;white-space:nowrap;line-height:%?26?%;margin-left:%?344?%}.text_13[data-v-3c2dee1f]{width:%?88?%;height:%?22?%;overflow-wrap:break-word;color:#de3a21;font-size:%?26?%;font-family:OPPOSans-M;text-align:left;white-space:nowrap;line-height:%?26?%;margin:%?2?% 0 0 %?19?%}',""]),t.exports=e},b96a:function(t,e,i){"use strict";var a;i.d(e,"b",(function(){return n})),i.d(e,"c",(function(){return r})),i.d(e,"a",(function(){return a}));var n=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("v-uni-view",{staticClass:"myBox",style:{height:t.screenHeight+"px"}},[i("v-uni-view",{staticClass:"tabs_1"},[i("v-uni-view",{staticClass:"text-wrapper_1"},[i("v-uni-text",{staticClass:"text_2",attrs:{lines:"1"},on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.change(1)}}},[t._v("全部")]),i("v-uni-text",{staticClass:"text_3",attrs:{lines:"1"},on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.change(2)}}},[t._v("待发货")]),i("v-uni-text",{staticClass:"text_4",attrs:{lines:"1"},on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.change(3)}}},[t._v("待收货")]),i("v-uni-text",{staticClass:"text_5",attrs:{lines:"1"},on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.change(4)}}},[t._v("已完成")])],1),1==t.state?i("v-uni-view",{staticClass:"image-wrapper_1",staticStyle:{"margin-left":"60rpx"}},[i("v-uni-image",{staticClass:"image_2",attrs:{src:"/static/image/top-state.png"}})],1):t._e(),2==t.state?i("v-uni-view",{staticClass:"image-wrapper_1",staticStyle:{"margin-left":"230rpx"}},[i("v-uni-image",{staticClass:"image_2",attrs:{src:"/static/image/top-state.png"}})],1):t._e(),3==t.state?i("v-uni-view",{staticClass:"image-wrapper_1",staticStyle:{"margin-left":"420rpx"}},[i("v-uni-image",{staticClass:"image_2",attrs:{src:"/static/image/top-state.png"}})],1):t._e(),4==t.state?i("v-uni-view",{staticClass:"image-wrapper_1",staticStyle:{"margin-left":"605rpx"}},[i("v-uni-image",{staticClass:"image_2",attrs:{src:"/static/image/top-state.png"}})],1):t._e()],1),i("v-uni-view",{staticStyle:{height:"1400rpx","overflow-x":"hidden"}},t._l(t.myOrderList,(function(e,a){return i("v-uni-view",{key:a,staticClass:"box_2",on:{click:function(i){arguments[0]=i=t.$handleEvent(i),t.toGoodsInfo(e)}}},[i("v-uni-view",{staticClass:"text-wrapper_2"},[i("v-uni-text",{staticClass:"text_6",attrs:{lines:"1",decode:"true"}},[t._v(t._s(t.time(e.create_time)))])],1),i("v-uni-view",{staticClass:"block_2"},[i("v-uni-view",{staticClass:"section_1"},[i("v-uni-image",{staticClass:"section_1",attrs:{src:t.baseUrl+e.image_url,mode:""}})],1),i("v-uni-view",{staticClass:"section_2"},[i("v-uni-text",{staticClass:"text_7",attrs:{lines:"1"}},[t._v(t._s(e.goods_name))]),i("v-uni-text",{staticClass:"text_8",attrs:{lines:"1"}},[t._v("单购价￥"+t._s(e.order_price))]),i("v-uni-view",{staticClass:"text-wrapper_3"},[i("v-uni-text",{staticClass:"text_9",attrs:{lines:"1"}},[t._v("￥")]),i("v-uni-text",{staticClass:"text_10",attrs:{lines:"1"}},[t._v(t._s(e.order_price))])],1)],1)],1),i("v-uni-view",{staticClass:"text-wrapper_4"},[i("v-uni-text",{staticClass:"text_11",attrs:{lines:"1"}},[t._v(t._s("1"==e.order_status?"待发货":"2"==e.order_status?"待收货":"3"==e.order_status?"已完成":""))]),i("v-uni-text",{staticClass:"text_12",attrs:{lines:"1"}},[t._v("实付款：")]),i("v-uni-text",{staticClass:"text_13",attrs:{lines:"1"}},[t._v(t._s(e.order_price))])],1)],1)})),1)],1)},r=[]},f279:function(t,e,i){var a=i("aaba");"string"===typeof a&&(a=[[t.i,a,""]]),a.locals&&(t.exports=a.locals);var n=i("4f06").default;n("6806cb02",a,!0,{sourceMap:!1,shadowMode:!1})},f588:function(t,e,i){"use strict";var a=i("f279"),n=i.n(a);n.a}}]);