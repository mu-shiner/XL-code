(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-home-goods-info-goods-info"],{"069a":function(t,e,i){"use strict";var n=i("4ea4");i("a9e3"),i("e25e"),Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var o=i("37dc"),s=n(i("e284")),a=(0,o.initVueI18n)(s.default),r=a.t,l={name:"UniCountdown",emits:["timeup"],props:{showDay:{type:Boolean,default:!0},showColon:{type:Boolean,default:!0},start:{type:Boolean,default:!0},backgroundColor:{type:String,default:""},color:{type:String,default:"#333"},fontSize:{type:Number,default:14},splitorColor:{type:String,default:"#333"},day:{type:Number,default:0},hour:{type:Number,default:0},minute:{type:Number,default:0},second:{type:Number,default:0},timestamp:{type:Number,default:0}},data:function(){return{timer:null,syncFlag:!1,d:"00",h:"00",i:"00",s:"00",leftTime:0,seconds:0}},computed:{dayText:function(){return r("uni-countdown.day")},hourText:function(t){return r("uni-countdown.h")},minuteText:function(t){return r("uni-countdown.m")},secondText:function(t){return r("uni-countdown.s")},timeStyle:function(){var t=this.color,e=this.backgroundColor,i=this.fontSize;return{color:t,backgroundColor:e,fontSize:"".concat(i,"px"),width:"".concat(22*i/14,"px"),lineHeight:"".concat(20*i/14,"px"),borderRadius:"".concat(3*i/14,"px")}},splitorStyle:function(){var t=this.splitorColor,e=this.fontSize,i=this.backgroundColor;return{color:t,fontSize:"".concat(12*e/14,"px"),margin:i?"".concat(4*e/14,"px"):""}}},watch:{day:function(t){this.changeFlag()},hour:function(t){this.changeFlag()},minute:function(t){this.changeFlag()},second:function(t){this.changeFlag()},start:{immediate:!0,handler:function(t,e){if(t)this.startData();else{if(!e)return;clearInterval(this.timer)}}}},created:function(t){this.seconds=this.toSeconds(this.timestamp,this.day,this.hour,this.minute,this.second),this.countDown()},destroyed:function(){clearInterval(this.timer)},methods:{toSeconds:function(t,e,i,n,o){return t?t-parseInt((new Date).getTime()/1e3,10):60*e*60*24+60*i*60+60*n+o},timeUp:function(){clearInterval(this.timer),this.$emit("timeup")},countDown:function(){var t=this.seconds,e=0,i=0,n=0,o=0;t>0?(e=Math.floor(t/86400),i=Math.floor(t/3600)-24*e,n=Math.floor(t/60)-24*e*60-60*i,o=Math.floor(t)-24*e*60*60-60*i*60-60*n):this.timeUp(),e<10&&(e="0"+e),i<10&&(i="0"+i),n<10&&(n="0"+n),o<10&&(o="0"+o),this.d=e,this.h=i,this.i=n,this.s=o},startData:function(){var t=this;if(this.seconds=this.toSeconds(this.timestamp,this.day,this.hour,this.minute,this.second),this.seconds<=0)return this.seconds=this.toSeconds(0,0,0,0,0),void this.countDown();clearInterval(this.timer),this.countDown(),this.timer=setInterval((function(){t.seconds--,t.seconds<0?t.timeUp():t.countDown()}),1e3)},update:function(){this.startData()},changeFlag:function(){this.syncFlag||(this.seconds=this.toSeconds(this.timestamp,this.day,this.hour,this.minute,this.second),this.startData(),this.syncFlag=!0)}}};e.default=l},"07ba":function(t,e,i){"use strict";i.r(e);var n=i("1aa8"),o=i("641d");for(var s in o)"default"!==s&&function(t){i.d(e,t,(function(){return o[t]}))}(s);i("db87");var a,r=i("f0c5"),l=Object(r["a"])(o["default"],n["b"],n["c"],!1,null,"21f605bc",null,!1,n["a"],a);e["default"]=l.exports},"1aa8":function(t,e,i){"use strict";i.d(e,"b",(function(){return o})),i.d(e,"c",(function(){return s})),i.d(e,"a",(function(){return n}));var n={uniCountdown:i("4dd1").default},o=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("v-uni-view",{staticClass:"myBox",style:{height:t.screenHeight+"px"}},[i("v-uni-view",{staticClass:"slid-wrap"},[i("v-uni-swiper",{staticClass:"slid-swiper",attrs:{"indicator-dots":!1,"indicator-active-color":"rgba(220, 32, 31, 1)","indicator-color":"rgba(255, 255, 255, 0.3)",circular:!0,autoplay:!0,interval:8e3,duration:1e3},on:{change:function(e){arguments[0]=e=t.$handleEvent(e),t.change.apply(void 0,arguments)}}},t._l(t.group.img_list,(function(e,n){return i("v-uni-swiper-item",{key:n,staticClass:"slid-swiper-item"},[i("v-uni-view",{staticClass:"swiper-item"},[i("v-uni-image",{staticClass:"slid-img",attrs:{src:t.baseUrl+e}})],1)],1)})),1),i("v-uni-cover-view",{staticClass:"slid-num-wrap"},[t._v(t._s(t.page)+"/"+t._s(t.banners||0))])],1),i("v-uni-view",{staticClass:"sales-box"},[i("v-uni-view",{staticClass:"sales"},[i("v-uni-view",{staticClass:"placeholder"}),i("v-uni-view",{staticClass:"sales-title"},[t._v("盲盒券")]),i("v-uni-view",{staticClass:"sales-info"},[i("v-uni-text",[t._v("已激活：32332")]),i("v-uni-text",[t._v("已核销:684")]),i("v-uni-text",[t._v("剩余：999667")])],1)],1)],1),i("v-uni-view",{staticClass:"money-info"},[i("v-uni-view",{staticClass:"money-info-num"},[t._v("￥"),i("v-uni-text",{staticStyle:{"font-size":"60rpx"}},[t._v("20")])],1),i("v-uni-view",{staticClass:"money-info-nameNum"},[i("del",{staticClass:"money-nameNum-del"},[t._v("￥30")]),i("v-uni-view",{staticClass:"info-nameNum"},[t._v("10人团")])],1),i("v-uni-view",{staticClass:"money-info-time"},[i("v-uni-view",{staticStyle:{"margin-bottom":"2rpx"}},[t._v("距离结束仅剩")]),i("uni-countdown",{attrs:{day:t.day,hour:t.time,minute:t.minute,second:0,color:"#866CFE","background-color":"rgba(255,255,255,0.800000)"}})],1)],1),i("v-uni-view",{staticClass:"list-boxBig"},[i("v-uni-view",{staticClass:"listBig"},[i("v-uni-view",{staticClass:"title-placeholder"}),i("v-uni-view",{staticClass:"list-title"},[t._v("展开全部"),t.isAll?i("v-uni-image",{staticClass:"list-titleIcon",attrs:{src:"/static/image/up.png"},on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.isAllChange(10)}}}):i("v-uni-image",{staticClass:"list-titleIcon2",attrs:{src:"/static/image/up.png"},on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.isAllChange("all")}}})],1),i("v-uni-view",{staticClass:"item-box"},t._l(t.list,(function(e,n){return n<t.num?i("v-uni-view",{key:n,staticClass:"item"},[i("v-uni-image",{staticClass:"item-leftIcon",attrs:{src:"/static/logo.png"}}),i("v-uni-view",{staticClass:"item-left"},[i("v-uni-view",{staticClass:"item-leftTop"},[i("v-uni-view",{staticClass:"leftTop-left"},[i("v-uni-view",{staticClass:"leftTop-title"},[t._v("食东道餐厅"+t._s(n))]),i("v-uni-view",{staticClass:"leftTop-title-isTop"},[t._v("已置顶")])],1),i("v-uni-view",{staticClass:"item-rightNum"},[t._v("0.00km")])],1),i("v-uni-view",{staticClass:"item-leftBottom"},[i("v-uni-view",{staticClass:"leftBottom-info"},[t._v("绵江路20号中央公园世纪...")]),i("v-uni-view",{staticClass:"leftBottom-button"},[t._v("去这里")])],1)],1)],1):t._e()})),1)],1)],1),i("v-uni-view",{staticClass:"bottom-box"},[i("v-uni-view",{staticClass:"bottom-button",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.tuxedoInfo()}}},[t._v("去参团")])],1)],1)},s=[]},"1de5":function(t,e,i){"use strict";t.exports=function(t,e){return e||(e={}),t=t&&t.__esModule?t.default:t,"string"!==typeof t?t:(/^['"].*['"]$/.test(t)&&(t=t.slice(1,-1)),e.hash&&(t+=e.hash),/["'() \t\n]/.test(t)||e.needQuotes?'"'.concat(t.replace(/"/g,'\\"').replace(/\n/g,"\\n"),'"'):t)}},"1e62":function(t,e,i){t.exports=i.p+"static/img/goods-infoButton.d00233e9.png"},"1f56":function(t){t.exports=JSON.parse('{"uni-countdown.day":"天","uni-countdown.h":"時","uni-countdown.m":"分","uni-countdown.s":"秒"}')},"34d1":function(t,e,i){t.exports=i.p+"static/img/TuxedoPayButton.e038c762.png"},"4dd1":function(t,e,i){"use strict";i.r(e);var n=i("ade7"),o=i("ce66");for(var s in o)"default"!==s&&function(t){i.d(e,t,(function(){return o[t]}))}(s);i("d30f");var a,r=i("f0c5"),l=Object(r["a"])(o["default"],n["b"],n["c"],!1,null,"0ee6ec24",null,!1,n["a"],a);e["default"]=l.exports},"5b13":function(t,e,i){var n=i("24fb");e=n(!1),e.push([t.i,'@charset "UTF-8";\r\n/**\r\n * 这里是uni-app内置的常用样式变量\r\n *\r\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\r\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\r\n *\r\n */\r\n/**\r\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\r\n *\r\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\r\n */\r\n/* 颜色变量 */\r\n/* 行为相关颜色 */\r\n/* 文字基本颜色 */\r\n/* 背景颜色 */\r\n/* 边框颜色 */\r\n/* 尺寸变量 */\r\n/* 文字尺寸 */\r\n/* 图片尺寸 */\r\n/* Border Radius */\r\n/* 水平间距 */\r\n/* 垂直间距 */\r\n/* 透明度 */\r\n/* 文章场景相关 */.uni-countdown[data-v-0ee6ec24]{display:flex;flex-direction:row;justify-content:flex-start;align-items:center}.uni-countdown__splitor[data-v-0ee6ec24]{margin:0 2px;font-size:14px;color:#333}.uni-countdown__number[data-v-0ee6ec24]{border-radius:3px;text-align:center;font-size:14px}',""]),t.exports=e},6326:function(t,e,i){var n=i("efb4");"string"===typeof n&&(n=[[t.i,n,""]]),n.locals&&(t.exports=n.locals);var o=i("4f06").default;o("0d70129e",n,!0,{sourceMap:!1,shadowMode:!1})},"641d":function(t,e,i){"use strict";i.r(e);var n=i("d9ec"),o=i.n(n);for(var s in n)"default"!==s&&function(t){i.d(e,t,(function(){return n[t]}))}(s);e["default"]=o.a},"7cfd":function(t,e,i){function n(t,e){var i=t.split("-"),n=e.split("-"),o=new Date(i[0],i[1],i[2]),s=new Date(n[0],n[1],n[2]),a=(s-o)/864e5;return a}i("ac1f"),i("1276"),t.exports={getDateDiff:n}},8017:function(t,e,i){i("e25e"),i("ac1f"),i("5319"),i("1276");var n=function(t){for(var e=(t+"").split(""),i=0;i<13;i++)e[i]||(e[i]="0");t=1*e.join("");var n=6e4,o=60*n,s=24*o,a=30*s,r=(new Date).getTime(),l=r-t;if(l<0)return"不久前";var u=l/a,c=l/(7*s),f=l/s,d=l/o,m=l/n,g=function(t){return t<10?"0"+t:t};return u>12?function(){var e=new Date(t);return e.getFullYear()+"年"+g(e.getMonth()+1)+"月"+g(e.getDate())+"日"}():u>=1?parseInt(u)+"月前":c>=1?parseInt(c)+"周前":f>=1?parseInt(f)+"天前":d>=1?parseInt(d)+"小时前":m>=1?parseInt(m)+"分钟前":"刚刚"},o=function(t){for(var e=(t+"").split(""),i=0;i<13;i++)e[i]||(e[i]="0");var n=1*e.join(""),o=new Date(n),s=((new Date).getTime(),new Date,o.getFullYear()),a=o.getMonth()+1<10?"0"+(o.getMonth()+1):o.getMonth()+1,r=o.getDate()<10?"0"+o.getDate():o.getDate(),l=o.getDay(),u=o.getHours()<10?"0"+o.getHours():o.getHours(),c=o.getMinutes()<10?"0"+o.getMinutes():o.getMinutes(),f=new Date((new Date).setHours(0,0,0,0))/1e3*1e3,d=f-864e5,m=f-6048e5;if(f<n)return u<12?"上午"+u+":"+c:"下午"+u+":"+c;if(d<n&&n<f)return"昨天";if(m<n){if(0==l)return"星期日";if(1==l)return"星期一";if(2==l)return"星期二";if(3==l)return"星期三";if(4==l)return"星期四";if(5==l)return"星期五";if(6==l)return"星期六"}return s+"-"+a+"-"+r},s=function(t){for(var e=(t+"").split(""),i=0;i<13;i++)e[i]||(e[i]="0");var n=1*e.join(""),o=new Date(n),s=o.getFullYear(),a=o.getMonth()+1<10?"0"+(o.getMonth()+1):o.getMonth()+1,r=o.getDate()<10?"0"+o.getDate():o.getDate(),l=o.getHours()<10?"0"+o.getHours():o.getHours(),u=o.getMinutes()<10?"0"+o.getMinutes():o.getMinutes();return s+"-"+a+"-"+r+" "+l+":"+u},a=function(t){var e=t.replace(/\//g,"-");e=t.replace(/：/g,":");var i=new Date(e),n=i.getTime();return n},r=function(t,e){13==t.length&&(t=(t-0)/1e3),13==e.length&&(e=(e-0)/1e3);var i=0,n=Math.ceil((e-t)/1e3);if(n<60)return i=n<10?"0"+n:n,"00:"+i;if(60<n&&n<3600){var o=Math.floor(n/60),s=Math.floor(n-60*o);return o<10&&(o="0"+o),s<10&&(s="0"+s),o+":"+s}if(3600<n){var a=Math.floor(n/3600);o=Math.floor(n/60-60*a),s=Math.floor(n-3600*a-60*o);return a<10&&(a="0"+a),o<10&&(o="0"+o),s<10&&(s="0"+s),a+":"+o+":"+s}};t.exports={time1:n,time2:o,time3:s,time4:a,time5:r}},"81ae":function(t){t.exports=JSON.parse('{"uni-countdown.day":"天","uni-countdown.h":"时","uni-countdown.m":"分","uni-countdown.s":"秒"}')},"93e8":function(t,e,i){"use strict";Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var n={data:function(){return{screenHeight:0}},onLoad:function(){this.screenHeight=uni.getSystemInfoSync().windowHeight}};e.default=n},ad26:function(t){t.exports=JSON.parse('{"uni-countdown.day":"day","uni-countdown.h":"h","uni-countdown.m":"m","uni-countdown.s":"s"}')},ade7:function(t,e,i){"use strict";var n;i.d(e,"b",(function(){return o})),i.d(e,"c",(function(){return s})),i.d(e,"a",(function(){return n}));var o=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("v-uni-view",{staticClass:"uni-countdown"},[t.showDay?i("v-uni-text",{staticClass:"uni-countdown__number",style:[t.timeStyle]},[t._v(t._s(t.d))]):t._e(),t.showDay?i("v-uni-text",{staticClass:"uni-countdown__splitor",style:[t.splitorStyle]},[t._v(t._s(t.dayText))]):t._e(),i("v-uni-text",{staticClass:"uni-countdown__number",style:[t.timeStyle]},[t._v(t._s(t.h))]),i("v-uni-text",{staticClass:"uni-countdown__splitor",style:[t.splitorStyle]},[t._v(t._s(t.showColon?":":t.hourText))]),i("v-uni-text",{staticClass:"uni-countdown__number",style:[t.timeStyle]},[t._v(t._s(t.i))]),i("v-uni-text",{staticClass:"uni-countdown__splitor",style:[t.splitorStyle]},[t._v(t._s(t.showColon?":":t.minuteText))]),i("v-uni-text",{staticClass:"uni-countdown__number",style:[t.timeStyle]},[t._v(t._s(t.s))]),t.showColon?t._e():i("v-uni-text",{staticClass:"uni-countdown__splitor",style:[t.splitorStyle]},[t._v(t._s(t.secondText))])],1)},s=[]},cb8f:function(t,e,i){var n=i("5b13");"string"===typeof n&&(n=[[t.i,n,""]]),n.locals&&(t.exports=n.locals);var o=i("4f06").default;o("6451c9da",n,!0,{sourceMap:!1,shadowMode:!1})},ce66:function(t,e,i){"use strict";i.r(e);var n=i("069a"),o=i.n(n);for(var s in n)"default"!==s&&function(t){i.d(e,t,(function(){return n[t]}))}(s);e["default"]=o.a},d30f:function(t,e,i){"use strict";var n=i("cb8f"),o=i.n(n);o.a},d9ec:function(t,e,i){"use strict";var n=i("4ea4");i("ac1f"),i("1276"),Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var o=i("7cfd"),s=n(i("8017")),a=n(i("93e8")),r={mixins:[a.default],data:function(){return{page:1,info:[{colorClass:"uni-bg-red",url:"https://vkceyugu.cdn.bspapp.com/VKCEYUGU-dc-site/094a9dc0-50c0-11eb-b680-7980c8a877b8.jpg",content:"内容 A"},{colorClass:"uni-bg-green",url:"https://vkceyugu.cdn.bspapp.com/VKCEYUGU-dc-site/094a9dc0-50c0-11eb-b680-7980c8a877b8.jpg",content:"内容 B"},{colorClass:"uni-bg-blue",url:"https://vkceyugu.cdn.bspapp.com/VKCEYUGU-dc-site/094a9dc0-50c0-11eb-b680-7980c8a877b8.jpg",content:"内容 C"}],isAll:!1,num:10,list:11,baseUrl:"",group:{},day:0,time:0,minute:0}},mounted:function(){this.baseUrl=uni.$baseUrl,this.group=uni.getStorageSync("groupInfo"),this.countDown()},methods:{countDown:function(){var t=Date.now(),e=this.group.end_time,i=s.default.time3(t),n=s.default.time3(e),a=(0,o.getDateDiff)(i.split(" ")[0],n.split(" ")[0]);this.day=a;var r=i.split(" ")[1],l=n.split(" ")[1],u=+r.split(":")[0],c=+r.split(":")[1],f=+l.split(":")[0],d=+l.split(":")[1];this.time=23-f-u,this.minute=60-d-c},tuxedoInfo:function(){uni.navigateTo({url:"../tuxedo-info/tuxedo-info"})},isAllChange:function(t){this.isAll=!this.isAll,this.num=10==t?t:this.list},change:function(t){this.page=t.detail.current+1}},computed:{banners:function(){var t=1;if(this.group)return t=this.group.img_list.length,t}}};e.default=r},db87:function(t,e,i){"use strict";var n=i("6326"),o=i.n(n);o.a},e284:function(t,e,i){"use strict";var n=i("4ea4");Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var o=n(i("ad26")),s=n(i("81ae")),a=n(i("1f56")),r={en:o.default,"zh-Hans":s.default,"zh-Hant":a.default};e.default=r},efb4:function(t,e,i){var n=i("24fb"),o=i("1de5"),s=i("34d1"),a=i("1e62");e=n(!1);var r=o(s),l=o(a);e.push([t.i,'@charset "UTF-8";\r\n/**\r\n * 这里是uni-app内置的常用样式变量\r\n *\r\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\r\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\r\n *\r\n */\r\n/**\r\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\r\n *\r\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\r\n */\r\n/* 颜色变量 */\r\n/* 行为相关颜色 */\r\n/* 文字基本颜色 */\r\n/* 背景颜色 */\r\n/* 边框颜色 */\r\n/* 尺寸变量 */\r\n/* 文字尺寸 */\r\n/* 图片尺寸 */\r\n/* Border Radius */\r\n/* 水平间距 */\r\n/* 垂直间距 */\r\n/* 透明度 */\r\n/* 文章场景相关 */.myBox[data-v-21f605bc]{background-color:#f3f6fb;overflow-x:hidden}.slid-wrap[data-v-21f605bc]{width:%?750?%;height:%?750?%;position:relative}.slid-wrap .slid-num-wrap[data-v-21f605bc]{width:%?90?%;height:%?40?%;background-color:#625f66;color:#fff;position:absolute;right:%?30?%;bottom:%?30?%;border-radius:%?30?%;display:flex;justify-content:center;line-height:%?40?%}.slid-wrap .slid-swiper[data-v-21f605bc]{width:%?750?%;height:%?750?%}.slid-wrap .slid-swiper .swiper-item[data-v-21f605bc]{width:%?750?%;height:%?750?%;height:100%;position:relative}.slid-wrap .slid-swiper .swiper-item .slid-img[data-v-21f605bc]{width:%?750?%;height:%?750?%;background-size:100%}.money-info[data-v-21f605bc]{-webkit-transform:translateY(%?-326?%);transform:translateY(%?-326?%);width:%?690?%;height:%?120?%;background:url(https://lanhu.oss-cn-beijing.aliyuncs.com/ps7di8obd2fw8f93sizy4nc56n80dia4q5c7274971-fc76-4f73-b14a-7f5bf5d8fa9a) 100% no-repeat;background-size:100% 100%;border-radius:%?24?%;margin:%?30?% auto 0 auto;display:flex;justify-content:space-between}.money-info .money-info-num[data-v-21f605bc]{margin-top:%?18?%;color:#fff;font-size:%?36?%;margin-left:%?36?%}.money-info .money-info-nameNum[data-v-21f605bc]{margin-top:%?15?%}.money-info .money-info-nameNum .money-nameNum-del[data-v-21f605bc]{color:#fff;font-size:%?26?%}.money-info .money-info-nameNum .info-nameNum[data-v-21f605bc]{margin-top:%?2?%;width:%?78?%;height:%?32?%;color:#000;background-color:#ffe07c;font-size:%?22?%;border-radius:%?4?%}.money-info .money-info-time[data-v-21f605bc]{margin-top:%?10?%;text-align:center;color:#fff;margin-right:%?36?%}.sales-box[data-v-21f605bc]{margin-top:%?106?%;display:flex;justify-content:center}.sales-box .sales[data-v-21f605bc]{width:%?690?%;height:%?220?%;background-color:#fff;border-radius:%?24?%}.sales-box .sales .placeholder[data-v-21f605bc]{width:100%;height:%?81?%}.sales-box .sales .sales-title[data-v-21f605bc]{margin-left:%?30?%;font-size:%?36?%}.sales-box .sales .sales-info[data-v-21f605bc]{margin-top:%?30?%;display:flex;color:#999;font-size:%?24?%;margin-left:%?30?%;margin-right:%?30?%;justify-content:space-between}.list-boxBig[data-v-21f605bc]{width:%?690?%;height:auto;background-color:#fff;border-radius:%?24?%;-webkit-transform:translateY(%?-130?%);transform:translateY(%?-130?%);margin:0 auto;padding-bottom:%?10?%}.list-boxBig .listBig .title-placeholder[data-v-21f605bc]{width:100%;height:%?30?%}.list-boxBig .listBig .list-title[data-v-21f605bc]{display:flex;align-items:center;justify-content:flex-end;font-size:%?32?%;margin-right:%?52?%}.list-boxBig .listBig .list-title .list-titleIcon[data-v-21f605bc]{margin-left:%?11?%;width:%?17?%;height:%?10?%;background-size:100%}.list-boxBig .listBig .list-title .list-titleIcon2[data-v-21f605bc]{-webkit-transform:rotate(180deg);transform:rotate(180deg);margin-left:%?11?%;width:%?17?%;height:%?10?%;background-size:100%}.list-boxBig .listBig .item-box[data-v-21f605bc]{margin:%?40?% %?30?%}.list-boxBig .listBig .item-box .item[data-v-21f605bc]{display:flex;justify-content:space-between;margin:%?40?% 0}.list-boxBig .listBig .item-box .item .item-leftIcon[data-v-21f605bc]{width:%?120?%;height:%?120?%;background-size:100%;margin-right:%?19?%}.list-boxBig .listBig .item-box .item .item-left[data-v-21f605bc]{display:flex;flex-direction:column;width:%?490?%}.list-boxBig .listBig .item-box .item .item-left .item-leftTop[data-v-21f605bc]{margin-bottom:%?30?%;display:flex;align-items:center;justify-content:space-between;margin-top:%?6?%}.list-boxBig .listBig .item-box .item .item-left .item-leftTop .leftTop-left[data-v-21f605bc]{display:flex;align-items:center}.list-boxBig .listBig .item-box .item .item-left .item-leftTop .leftTop-left .leftTop-title[data-v-21f605bc]{font-size:%?32?%;margin-right:%?19?%}.list-boxBig .listBig .item-box .item .item-left .item-leftTop .leftTop-left .leftTop-title-isTop[data-v-21f605bc]{width:%?78?%;height:%?28?%;border:%?1?% #796aff solid;border-radius:%?15?%;color:#796aff;font-size:%?22?%;line-height:%?28?%;text-align:center}.list-boxBig .listBig .item-box .item .item-left .item-leftTop .item-rightNum[data-v-21f605bc]{font-size:%?24?%;color:#999}.list-boxBig .listBig .item-box .item .item-left .item-leftBottom[data-v-21f605bc]{display:flex;align-items:center;justify-content:space-between}.list-boxBig .listBig .item-box .item .item-left .item-leftBottom .leftBottom-info[data-v-21f605bc]{font-size:%?24?%;color:#999}.list-boxBig .listBig .item-box .item .item-left .item-leftBottom .leftBottom-button[data-v-21f605bc]{width:%?126?%;height:%?46?%;line-height:%?46?%;text-align:center;border-radius:%?23?%;background:url('+r+") 100% no-repeat;background-size:100% 100%;font-size:%?26?%;color:#fff}.bottom-box[data-v-21f605bc]{position:fixed;bottom:0;left:0;width:100%;width:%?750?%;height:%?100?%;background-color:#fff;display:flex}.bottom-box .bottom-button[data-v-21f605bc]{margin:auto auto;width:%?690?%;height:%?80?%;background:url("+l+") 100% no-repeat;background-size:100% 100%;color:#fff;display:flex;justify-content:center;line-height:%?80?%}",""]),t.exports=e}}]);