import Config from '@/common/config.js';

// 工具类
export default {
	config: Config,
	/**
	 * 页面跳转
	 * @param {string} to 跳转链接 /pages/index/index
	 * @param {Object} param 参数 {key : value, ...}
	 * @param {string} mode 模式 
	 */
	redirectTo(to, param, mode) {
		let url = to;
		// console.log({"$store":store.state.$store.tabbarList.list}) 
		let tabbarList = ['/pages/index/index', '/pages/my/my'];
		for (let i = 0; i < tabbarList.length; i++) {
			if (to.indexOf(tabbarList[i]) != -1) {
				uni.switchTab({
					url: url
				})
				return;
			}
		}
		// // #ifdef H5
		// window.history.pushState(null, null, url);
		// // #endif
		if (param != undefined) {
			Object.keys(param).forEach(function(key) {
				if (url.indexOf('?') != -1) {
					url += "&" + key + "=" + param[key];
				} else {
					url += "?" + key + "=" + param[key];
				}
			});
		}
		switch (mode) {
			case 'tabbar':
				// 跳转到 tabBar 页面，并关闭其他所有非 tabBar 页面。
				uni.switchTab({
					url: url
				})
				break;
			case 'redirectTo':
				// 关闭当前页面，跳转到应用内的某个页面。
				uni.redirectTo({
					url: url
				});
				break;
			case 'reLaunch':
				// 关闭所有页面，打开到应用内的某个页面。
				uni.reLaunch({
					url: url
				});
				break;
			default:
				// 保留当前页面，跳转到应用内的某个页面
				uni.navigateTo({
					url: url
				});
		}
	},

	/**
	 * 图片路径转换
	 * @param {String} img_path 图片地址
	 * @param {Object} params 参数，针对商品、相册里面的图片区分大中小，size: big、mid、small
	 */
	img(img_path, params) {
		var path = "";
		if (img_path && img_path != undefined && img_path != "") {
			if (params && img_path != this.getDefaultImage().default_goods_img) {
				// 过滤默认图
				let arr = img_path.split(".");
				let suffix = arr[arr.length - 1];
				arr.pop();
				arr[arr.length - 1] = arr[arr.length - 1] + "_" + params.size;
				arr.push(suffix);
				img_path = arr.join(".");
			}
			if (img_path.indexOf("http://") == -1 && img_path.indexOf("https://") == -1) {
				path = Config.imgUrl + "/" + img_path;
			} else {
				path = img_path;
			}
		}
		return path;
	},

	/**
	 * 显示消息提示框
	 *  @param {Object} params 参数
	 */
	showToast(params = {}) {
		params.title = params.title || "";
		params.icon = params.icon || "none";
		params.position = params.position || 'bottom';
		params.duration = 1500;
		uni.showToast(params);
	},

	/**
	 * TODO（不能只根据token验证）
	 * 判断是否登录
	 */
	checkLogin() {
		if (uni.getStorageSync('token')) {
			return true;
		} else {
			return false;
		}
	},

	/**
	 * 判断是否会员
	 */
	checkMember() {
		let _userInfo = uni.getStorageSync('userInfo');
		if (_userInfo.member_level) {
			return true;
		} else {
			return false;
		}
	},
	
	/**
	 * 检测苹果X以上的手机
	 */
	isIPhoneX() {
		let res = uni.getSystemInfoSync();
		if (res.model.search('iPhone X') != -1) {
			return true;
		}
		return false;
	},
	//判断安卓还是iOS
	isAndroid() {
		let platform = uni.getSystemInfoSync().platform
		if (platform == 'ios') {
			return false;
		} else if (platform == 'android') {
			return true;
		}
	},

	/**
	 * 复制
	 * @param {Object} message
	 * @param {Object} callback
	 */
	copy(value, callback) {
		// #ifdef H5
		var oInput = document.createElement('input'); //创建一个隐藏input（重要！）
		oInput.value = value; //赋值
		document.body.appendChild(oInput);
		oInput.select(); // 选择对象
		document.execCommand("Copy"); // 执行浏览器复制命令
		oInput.className = 'oInput';
		oInput.style.display = 'none';
		uni.hideKeyboard();
		this.showToast({
			title: '复制成功'
		});
		typeof callback == 'function' && callback();
		// #endif
		// #ifdef MP || APP-PLUS
		uni.setClipboardData({
			data: value,
			success: () => {
				typeof callback == 'function' && callback();
			}
		});
		// #endif
	},

	/**
	 * 是否是微信浏览器
	 */
	isWeiXin() {
		// #ifndef H5
		return false;
		// #endif
		var ua = navigator.userAgent.toLowerCase();
		if (ua.match(/MicroMessenger/i) == "micromessenger") {
			return true;
		} else {
			return false;
		}
	},

	/**
	 * 自定义模板的跳转链接
	 * @param {Object} link
	 */
	diyRedirectTo(link, method) {
		if (link == null || link == '' || !link.wap_url) return;
		uni.setStorageSync('webviewUrl', link.wap_url);
		if (link.title) {
			uni.setStorageSync('webviewTitle', link.title);
		}
		this.redirectTo('/otherpages/webview/webview');
		// console.log(link)
		// if (link == null || link == '' || !link.wap_url) return;
		// if (link.wap_url.indexOf('http') != -1) {
		// 	this.redirectTo('/otherpages/webview/webview', {
		// 		link: encodeURIComponent(link.wap_url)
		// 	});
		// } else {
		// 	var params = link.site_id ? {
		// 		site_id: link.site_id
		// 	} : {};
		// 	this.redirectTo(link.wap_url);
		// }
	},


	/**
	 * 计算两个经纬度之间的距离
	 * @param {Object} lat1
	 * @param {Object} lng1
	 * @param {Object} lat2
	 * @param {Object} lng2
	 */
	getDistance(lat1, lng1, lat2, lng2) {
		var radLat1 = lat1 * Math.PI / 180.0;
		var radLat2 = lat2 * Math.PI / 180.0;
		var a = radLat1 - radLat2;
		var b = lng1 * Math.PI / 180.0 - lng2 * Math.PI / 180.0;
		var s = 2 * Math.asin(Math.sqrt(Math.pow(Math.sin(a / 2), 2) +
			Math.cos(radLat1) * Math.cos(radLat2) * Math.pow(Math.sin(b / 2), 2)));
		s = s * 6378.137; // EARTH_RADIUS;
		s = Math.round(s * 10000) / 10000;
		return s;
	},
	// #ifdef H5
	/**
	 * 判断该浏览器是否为safaria浏览器
	 */
	isSafari() {
		let res = uni.getSystemInfoSync();
		var ua = navigator.userAgent.toLowerCase();
		if (ua.indexOf('applewebkit') > -1 && ua.indexOf('mobile') > -1 && ua.indexOf('safari') > -1 &&
			ua.indexOf('linux') === -1 && ua.indexOf('android') === -1 && ua.indexOf('chrome') === -1 &&
			ua.indexOf('ios') === -1 && ua.indexOf('browser') === -1) {
			return true;
		} else {
			return false;
		}
	},
	// #endif
	goBack(backUrl = '/pages/index/index/index') {
		if (getCurrentPages().length == 1) {
			this.redirectTo(backUrl);
		} else {
			uni.navigateBack();
		}
	},
	/**
	 * 转换数字，保留f位
	 * @param {Object} e
	 * @param {Object} f
	 */
	numberFixed(e, f) {
		if (!f) {
			f = 0;
		}
		return Number(e).toFixed(f);
	},

	/**
	 * 获取url参数
	 * @param {Object} callback
	 */
	getUrlCode(callback) {
		var url = location.search;
		var theRequest = new Object();
		if (url.indexOf('?') != -1) {
			var str = url.substr(1);
			var strs = str.split('&');
			for (var i = 0; i < strs.length; i++) {
				theRequest[strs[i].split('=')[0]] = strs[i].split('=')[1];
			}
		}
		typeof callback == 'function' && callback(theRequest);
	},


	/**
	 * 节流原理：在一定时间内，只能触发一次
	 *
	 * @param {Function} func 要执行的回调函数
	 * @param {Number} wait 延时的时间
	 * @param {Boolean} immediate 是否立即执行
	 * @return null
	 */
	throttle(func, wait = 500, immediate = true) {
		let timer
		let flag
		if (immediate) {
			if (!flag) {
				flag = true
				// 如果是立即执行，则在wait毫秒内开始时执行
				typeof func === 'function' && func()
				timer = setTimeout(() => {
					flag = false
				}, wait)
			}
		} else if (!flag) {
			flag = true
			// 如果是非立即执行，则在wait毫秒内的结束处执行
			timer = setTimeout(() => {
				flag = false
				typeof func === 'function' && func()
			}, wait)
		}
	},
	/**
	 * 防抖原理：一定时间内，只有最后一次操作，再过wait毫秒后才执行函数
	 *
	 * @param {Function} func 要执行的回调函数
	 * @param {Number} wait 延时的时间
	 * @param {Boolean} immediate 是否立即执行
	 * @return null
	 */
	debounce(func, wait = 500, immediate = false) {
		let timeout = null
		// 清除定时器
		if (timeout !== null) clearTimeout(timeout)
		// 立即执行，此类情况一般用不到
		if (immediate) {
			const callNow = !timeout
			timeout = setTimeout(() => {
				timeout = null
			}, wait)
			if (callNow) typeof func === 'function' && func()
		} else {
			// 设置定时器，当最后一次操作后，timeout不会再被清除，所以在延时wait毫秒后执行func回调方法
			timeout = setTimeout(() => {
				typeof func === 'function' && func()
			}, wait)
		}
	}

}
