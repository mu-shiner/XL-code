(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-store-store"],{"1de5":function(t,e,n){"use strict";t.exports=function(t,e){return e||(e={}),t=t&&t.__esModule?t.default:t,"string"!==typeof t?t:(/^['"].*['"]$/.test(t)&&(t=t.slice(1,-1)),e.hash&&(t+=e.hash),/["'() \t\n]/.test(t)||e.needQuotes?'"'.concat(t.replace(/"/g,'\\"').replace(/\n/g,"\\n"),'"'):t)}},"2b46":function(t,e,n){var i=n("bd5b");"string"===typeof i&&(i=[[t.i,i,""]]),i.locals&&(t.exports=i.locals);var o=n("4f06").default;o("294545c8",i,!0,{sourceMap:!1,shadowMode:!1})},3242:function(t,e,n){"use strict";var i;n.d(e,"b",(function(){return o})),n.d(e,"c",(function(){return a})),n.d(e,"a",(function(){return i}));var o=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("v-uni-view",{staticClass:"myBox",style:{height:t.screenHeight+"px"}},[n("v-uni-view",{staticClass:"navigation-filtrate"},[n("v-uni-view",{staticClass:"filtrate-all",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.chengaStore(1)}}},[t._v("全部")]),n("v-uni-view",{staticClass:"filtrate-money",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.chengaStore(2)}}},[n("v-uni-view",{staticClass:"money-title"},[t._v("价格")]),n("v-uni-view",{staticClass:"money-mode"},[n("v-uni-view",{staticClass:"money-up",style:1==t.moneyMode?"border: 15rpx solid #000;border-top: 0rpx;border-left: 12rpx solid transparent;border-right: 12rpx solid transparent;":"",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.chengaMode("价格",1)}}}),n("v-uni-view",{staticClass:"money-down",style:-1==t.moneyMode?"border: 15rpx solid #000;border-bottom: 0rpx;border-left: 12rpx solid transparent;border-right: 12rpx solid transparent;":"",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.chengaMode("价格",-1)}}})],1)],1),n("v-uni-view",{staticClass:"filtrate-sales",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.chengaStore(3)}}},[n("v-uni-view",{staticClass:"sales-title"},[t._v("销量")]),n("v-uni-view",{staticClass:"sales-mode"},[n("v-uni-view",{staticClass:"sales-up",style:1==t.salesMode?"border: 15rpx solid #000;border-top: 0rpx;border-left: 12rpx solid transparent;border-right: 12rpx solid transparent;":"",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.chengaMode("销量",1)}}}),n("v-uni-view",{staticClass:"sales-down",style:-1==t.salesMode?"border: 15rpx solid #000;border-bottom: 0rpx;border-left: 12rpx solid transparent;border-right: 12rpx solid transparent;":"",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.chengaMode("销量",-1)}}})],1)],1)],1),n("v-uni-image",{staticClass:"top-state",style:1==t.topStore?"left: 67rpx":2==t.topStore?"left: 325rpx":3==t.topStore?"left: 600rpx":"",attrs:{src:"/static/image/top-state.png"}}),n("v-uni-view",{staticClass:"goods-box"},t._l(15,(function(e,i){return n("v-uni-view",{staticClass:"item-box"},[n("v-uni-view",{staticClass:"image-box"},[n("v-uni-image",{staticClass:"image-info",attrs:{src:"/static/image/goods-itemTitle.png"}})],1),n("v-uni-view",{staticClass:"title-box"},[t._v("完美日记双色腮红膏盘正品")]),n("v-uni-view",{staticClass:"integral-button"},[n("v-uni-view",{staticClass:"integral-num"},[t._v("299"),n("v-uni-text",{staticStyle:{"font-size":"24rpx","margin-left":"5rpx"}},[t._v("积分")])],1),n("v-uni-view",{staticClass:"button-box",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.toinfo()}}},[t._v("去兑换")])],1)],1)})),1)],1)},a=[]},4430:function(t,e,n){"use strict";var i=n("2b46"),o=n.n(i);o.a},4543:function(t,e,n){t.exports=n.p+"static/img/goods-imageBox.91ee3209.png"},"4d38":function(t,e,n){"use strict";n.r(e);var i=n("3242"),o=n("9c1a");for(var a in o)"default"!==a&&function(t){n.d(e,t,(function(){return o[t]}))}(a);n("4430");var r,s=n("f0c5"),d=Object(s["a"])(o["default"],i["b"],i["c"],!1,null,"6f62d722",null,!1,i["a"],r);e["default"]=d.exports},"93e8":function(t,e,n){"use strict";Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var i={data:function(){return{screenHeight:0}},onLoad:function(){this.screenHeight=uni.getSystemInfoSync().windowHeight}};e.default=i},"9c1a":function(t,e,n){"use strict";n.r(e);var i=n("f946"),o=n.n(i);for(var a in i)"default"!==a&&function(t){n.d(e,t,(function(){return i[t]}))}(a);e["default"]=o.a},bcc3:function(t,e,n){t.exports=n.p+"static/img/integral-button.368f1ef5.png"},bd5b:function(t,e,n){var i=n("24fb"),o=n("1de5"),a=n("4543"),r=n("bcc3");e=i(!1);var s=o(a),d=o(r);e.push([t.i,'@charset "UTF-8";\r\n/**\r\n * 这里是uni-app内置的常用样式变量\r\n *\r\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\r\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\r\n *\r\n */\r\n/**\r\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\r\n *\r\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\r\n */\r\n/* 颜色变量 */\r\n/* 行为相关颜色 */\r\n/* 文字基本颜色 */\r\n/* 背景颜色 */\r\n/* 边框颜色 */\r\n/* 尺寸变量 */\r\n/* 文字尺寸 */\r\n/* 图片尺寸 */\r\n/* Border Radius */\r\n/* 水平间距 */\r\n/* 垂直间距 */\r\n/* 透明度 */\r\n/* 文章场景相关 */.myBox[data-v-6f62d722]{background-color:#f3f6fb;overflow-x:hidden}.navigation-filtrate[data-v-6f62d722]{width:100%;height:%?96?%;background-color:#fff;font-size:%?30?%;position:-webkit-sticky;position:sticky;top:0;z-index:98;display:flex;align-items:center;justify-content:space-between}.navigation-filtrate .filtrate-all[data-v-6f62d722]{margin-left:%?72?%}.navigation-filtrate .filtrate-money[data-v-6f62d722]{display:flex}.navigation-filtrate .filtrate-money .money-title[data-v-6f62d722]{margin-right:%?8?%}.navigation-filtrate .filtrate-money .money-mode[data-v-6f62d722]{display:flex;flex-direction:column;justify-content:center}.navigation-filtrate .filtrate-money .money-mode .money-up[data-v-6f62d722]{margin-bottom:%?5?%;height:%?0?%;width:%?0?%;border:%?15?% solid #999;border-top:%?0?%;border-left:%?12?% solid transparent;border-right:%?12?% solid transparent}.navigation-filtrate .filtrate-money .money-mode .money-down[data-v-6f62d722]{height:%?0?%;width:%?0?%;border:%?15?% solid #999;border-bottom:%?0?%;border-left:%?12?% solid transparent;border-right:%?12?% solid transparent}.navigation-filtrate .filtrate-sales[data-v-6f62d722]{margin-right:%?72?%;display:flex}.navigation-filtrate .filtrate-sales .sales-title[data-v-6f62d722]{margin-right:%?8?%}.navigation-filtrate .filtrate-sales .sales-mode[data-v-6f62d722]{display:flex;flex-direction:column;justify-content:center}.navigation-filtrate .filtrate-sales .sales-mode .sales-up[data-v-6f62d722]{margin-bottom:%?5?%;height:%?0?%;width:%?0?%;border:%?15?% solid #999;border-top:%?0?%;border-left:%?12?% solid transparent;border-right:%?12?% solid transparent}.navigation-filtrate .filtrate-sales .sales-mode .sales-down[data-v-6f62d722]{height:%?0?%;width:%?0?%;border:%?15?% solid #999;border-bottom:%?0?%;border-left:%?12?% solid transparent;border-right:%?12?% solid transparent}.top-state[data-v-6f62d722]{width:%?60?%;height:%?4?%;position:absolute;top:%?81?%;z-index:98;left:%?67?%}.goods-box[data-v-6f62d722]{margin-left:%?35?%;margin-right:%?35?%;display:flex;flex-wrap:wrap;justify-content:space-between}.goods-box .item-box[data-v-6f62d722]{margin-top:%?26?%;display:flex;flex-direction:column;justify-content:center}.goods-box .item-box .image-box[data-v-6f62d722]{width:%?316?%;height:%?176?%;background:url('+s+");background-size:100%;display:flex;align-items:center;justify-content:center}.goods-box .item-box .image-box .image-info[data-v-6f62d722]{width:%?287?%;height:%?138?%;background-size:100%;border-radius:%?17?%}.goods-box .item-box .title-box[data-v-6f62d722]{font-size:%?26?%;margin-top:%?10?%}.goods-box .item-box .integral-button[data-v-6f62d722]{display:flex;align-items:center;justify-content:space-between}.goods-box .item-box .integral-button .integral-num[data-v-6f62d722]{color:#de3a21;font-size:%?28?%}.goods-box .item-box .integral-button .button-box[data-v-6f62d722]{width:%?110?%;height:%?44?%;background:url("+d+");background-size:100%;display:flex;font-size:%?26?%;color:#fff;justify-content:center;align-items:center}",""]),t.exports=e},f946:function(t,e,n){"use strict";var i=n("4ea4");Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var o=i(n("93e8")),a={mixins:[o.default],data:function(){return{topStore:1,moneyMode:0,salesMode:0}},methods:{chengaStore:function(t){this.topStore=t},toinfo:function(){uni.navigateTo({url:"/pages/store/goods-info/goods-info"})},chengaMode:function(t,e){"价格"==t?(console.log(t,e),this.moneyMode=e):"销量"==t&&(console.log(t,e),this.salesMode=e)}}};e.default=a}}]);