(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["page_my-txDetail"],{"34e9":function(t,a,e){"use strict";e.r(a);var i=e("7891"),r=e.n(i);for(var n in i)"default"!==n&&function(t){e.d(a,t,(function(){return i[t]}))}(n);a["default"]=r.a},3958:function(t,a,e){"use strict";var i;e.d(a,"b",(function(){return r})),e.d(a,"c",(function(){return n})),e.d(a,"a",(function(){return i}));var r=function(){var t=this,a=t.$createElement,e=t._self._c||a;return e("v-uni-view",{staticStyle:{width:"100%"}},[e("v-uni-view",{staticClass:"n-tabs-wrapper"},[e("v-uni-view",{staticClass:"n-tabs-tab-wrapper",on:{click:function(a){arguments[0]=a=t.$handleEvent(a),t.switchTab(1)}}},[e("span",{class:{"n-tabs-tab--active":1==t.cashType}},[t._v("余额提现明细")])]),e("v-uni-view",{staticClass:"n-tabs-tab-wrapper",on:{click:function(a){arguments[0]=a=t.$handleEvent(a),t.switchTab(2)}}},[e("span",{class:{"n-tabs-tab--active":2==t.cashType}},[t._v("积分提现明细")])])],1),e("v-uni-view",{staticClass:"n-tabs-wrapper-empty"}),t._l(t.list,(function(a,i){return e("v-uni-view",{key:i,staticClass:"detail_ct"},[e("v-uni-view",{staticClass:"detail_ct_st"},[-1==a.status&&a.fail_msg?e("v-uni-view",{staticClass:"detail_ct_st_text",staticStyle:{color:"red"}},[t._v("未通过 "+t._s(a.fail_msg))]):0==a.status?e("v-uni-view",{staticClass:"detail_ct_st_text"},[t._v("审核中")]):1==a.status?e("v-uni-view",{staticClass:"detail_ct_st_text"},[t._v("待财务审核")]):e("v-uni-view",{staticClass:"detail_ct_st_time"},[t._v("审核通过")]),e("v-uni-view",{staticClass:"detail_ct_st_time"},[t._v(t._s(t.moment(1e3*a.create_time).format("YYYY-MM-DD HH:mm:ss")))])],1),e("v-uni-view",{staticClass:"detail_ct_money"},[t._v(t._s(a.withdraw_price))])],1)}))],2)},n=[]},3976:function(t,a,e){var i=e("4e3f");"string"===typeof i&&(i=[[t.i,i,""]]),i.locals&&(t.exports=i.locals);var r=e("4f06").default;r("5edf198f",i,!0,{sourceMap:!1,shadowMode:!1})},"4e3f":function(t,a,e){var i=e("24fb");a=i(!1),a.push([t.i,'@charset "UTF-8";\n/* 页面左右间距 */\n/* 文字尺寸 */\n/*文字颜色*/\n/* 边框颜色 */\n/* 图片加载中颜色 */\n/* 行为相关颜色 */\n/* 文字基本颜色 */\n/* 背景颜色 */\n/* 边框颜色 */\n/* 尺寸变量 */\n/* 文字尺寸 */\n/* 图片尺寸 */\n/* Border Radius */\n/* 水平间距 */\n/* 垂直间距 */\n/* 透明度 */\n/* 文章场景相关 */.flex-center[data-v-43618f30], .page-btn-wrap[data-v-43618f30], .page-btn-wrap .page-btn[data-v-43618f30], .page-tips-wrap[data-v-43618f30]{display:flex;justify-content:center;align-items:center}.flex-center-y[data-v-43618f30], .flex-title[data-v-43618f30], .app-card .card-title[data-v-43618f30], .app-card .card-item[data-v-43618f30]{display:flex;align-items:center}.text-ellipsis[data-v-43618f30]{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.n-tabs-wrapper[data-v-43618f30]{width:%?750?%;max-width:600px}.flex-title[data-v-43618f30]{justify-content:space-between}.flex-title > .main[data-v-43618f30]{flex:1}.flex-title > .ohter[data-v-43618f30]{text-align:right}.has-arrow[data-v-43618f30]::after{position:absolute;right:%?30?%;content:"";width:%?16?%;height:%?16?%;border-right:%?1?% solid #000;border-top:%?1?% solid #000;-webkit-transform:rotate(45deg);transform:rotate(45deg)}.has-active[data-v-43618f30]:active{background-color:#f1f1f1}*[data-v-43618f30]{margin:0;padding:0;box-sizing:border-box}.status-bar[data-v-43618f30]{height:calc(0px + var(--window-top));width:100%}.page-wrap[data-v-43618f30]{max-width:%?750?%;min-height:calc(100vh - var(--window-top) - 0px);background-color:#f3f4f8;position:relative;background-repeat:no-repeat;background-position:50%;background-size:100% 100%;padding-bottom:%?50?%}.padding-all[data-v-43618f30]{padding:%?30?%}.padding-x[data-v-43618f30]{padding:%?30?% 0}.padding-y[data-v-43618f30]{padding:0 %?30?%}.dev-style[data-v-43618f30]{-webkit-filter:grayscale(100%);-moz-filter:grayscale(100%);-ms-filter:grayscale(100%);-o-filter:grayscale(100%);filter:grayscale(100%);-webkit-filter:grey;filter:gray;color:grey}.triangle-box.show-top[data-v-43618f30]::after{content:"";margin-left:%?10?%;display:inline-block;border-bottom:%?14?% solid #6f98bb;border-left:%?8?% solid transparent;border-right:%?8?% solid transparent}.triangle-box.show-bottom[data-v-43618f30]::after{content:"";margin-left:%?10?%;display:inline-block;border-top:%?14?% solid #6f98bb;border-left:%?8?% solid transparent;border-right:%?8?% solid transparent}.app-card[data-v-43618f30]{border-radius:%?16?%;background-color:#fff;margin-bottom:%?20?%}.app-card .card-title[data-v-43618f30]{height:%?100?%;justify-content:space-between;font-size:%?30?%;color:#ff5454}.app-card .card-title .main .card-title-icon[data-v-43618f30]{width:%?36?%;margin-right:%?20?%}.app-card .card-item[data-v-43618f30]{border-bottom:%?1?% solid #eee;min-height:%?80?%}.app-card .card-item.n-b-b[data-v-43618f30]{border-bottom:none}.app-card .card-item[data-v-43618f30]:last-child{border-bottom:none}.app-card .card-item .card-item-label[data-v-43618f30]{width:%?180?%;height:%?80?%;line-height:%?80?%;font-size:%?28?%}.app-card .card-item .card-item-label.require[data-v-43618f30]::after{content:"*";color:red}.app-card .card-item .card-item-value[data-v-43618f30]{flex:1}.app-card .card-item .card-item-value .card-item-input[data-v-43618f30]{font-size:%?28?%}.page-btn-wrap[data-v-43618f30]{width:100%;height:%?120?%}.page-btn-wrap .page-btn[data-v-43618f30]{color:#fff;width:%?500?%;height:%?80?%;background-color:#ff5454;border-radius:%?60?%}.page-btn-wrap .page-btn[data-v-43618f30]:active{background-color:rgba(255,84,84,.4)}.page-title[data-v-43618f30]{height:%?100?%;font-size:%?32?%;padding:0 %?30?%}.page-tips-wrap[data-v-43618f30]{width:100%;height:%?80?%;font-size:%?24?%;color:#ccc}.app-primary-color[data-v-43618f30]{color:#ff5454}uni-page-body[data-v-43618f30]{background-color:#f7f7f7}.n-tabs-wrapper-empty[data-v-43618f30]{height:%?100?%}.n-tabs-wrapper[data-v-43618f30]{overflow:hidden;height:%?100?%;position:fixed;z-index:20;background-color:#fff}.n-tabs-tab-wrapper[data-v-43618f30]{width:50%;float:left;height:%?99?%;line-height:%?99?%;text-align:center;font-size:%?38?%;color:#333;font-size:%?26?%}.n-tabs-tab--active[data-v-43618f30]{color:#ff5454}.n-tabs-bar[data-v-43618f30]{position:absolute;bottom:0;height:1px;border-bottom:1px solid #ff5454;left:0;max-width:33.33%;width:60%;transition-property:all;transition-duration:.5s}.detail_ct[data-v-43618f30]{display:flex;justify-content:space-between;align-items:center;width:100%;padding:%?15?% %?30?%;border-bottom:%?1?% solid #dcdfe6}.detail_ct .detail_ct_st[data-v-43618f30]{display:flex;flex-direction:column;font-size:%?28?%}.detail_ct .detail_ct_st .detail_ct_st_time[data-v-43618f30]{margin-top:%?10?%;color:#999;font-size:%?24?%}.detail_ct .detail_ct_money[data-v-43618f30]{color:red}body.?%PAGE?%[data-v-43618f30]{background-color:#f7f7f7}',""]),t.exports=a},7891:function(t,a,e){"use strict";var i=e("4ea4");Object.defineProperty(a,"__esModule",{value:!0}),a.default=void 0,e("96cf");var r=i(e("1da1")),n=i(e("a6d2")),d=e("64a8"),o={data:function(){return{cashType:1,tabBarSide:"",type:1,list:[],withdraw_price:"",create_time:"",status:""}},onLoad:function(){this.detailList("1")},methods:{moment:n.default,switchTab:function(t){this.cashType=t,1==t?(this.tabBarSide={transform:"translateX(100%)"},this.detailList(2)):0==t?(this.tabBarSide={transform:"translateX(0%)"},this.detailList("")):(this.tabBarSide={transform:"translateX(200%)"},this.detailList(1))},detailList:function(t){var a=this;return(0,r.default)(regeneratorRuntime.mark((function e(){var i,r;return regeneratorRuntime.wrap((function(e){while(1)switch(e.prev=e.next){case 0:return i={type:t},e.next=3,(0,d.withdrawBillApi)(i);case 3:r=e.sent,a.list=r.data.data,console.log(a.list);case 6:case"end":return e.stop()}}),e)})))()}}};a.default=o},f1b1:function(t,a,e){"use strict";e.r(a);var i=e("3958"),r=e("34e9");for(var n in r)"default"!==n&&function(t){e.d(a,t,(function(){return r[t]}))}(n);e("f226");var d,o=e("f0c5"),s=Object(o["a"])(r["default"],i["b"],i["c"],!1,null,"43618f30",null,!1,i["a"],d);a["default"]=s.exports},f226:function(t,a,e){"use strict";var i=e("3976"),r=e.n(i);r.a}}]);