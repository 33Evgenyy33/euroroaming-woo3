/*
* ===========================================================
* SUPPORT BOARD - MAIN JAVASCRIPT
* ===========================================================
* 
* Main Javascript file that manage all the plugin functions
* Schiocco - Copyright (c)
*/

'use strict';

/* Autosize 3.0.14 - license: MIT - jacklmoore.com/autosize */
!function (e, t) { if ("function" == typeof define && define.amd) define(["exports", "module"], t); else if ("undefined" != typeof exports && "undefined" != typeof module) t(exports, module); else { var n = { exports: {} }; t(n.exports, n), e.autosize = n.exports } }(this, function (e, t) { "use strict"; function n(e) { function t() { var t = window.getComputedStyle(e, null); c = t.overflowY, "vertical" === t.resize ? e.style.resize = "none" : "both" === t.resize && (e.style.resize = "horizontal"), f = "content-box" === t.boxSizing ? -(parseFloat(t.paddingTop) + parseFloat(t.paddingBottom)) : parseFloat(t.borderTopWidth) + parseFloat(t.borderBottomWidth), isNaN(f) && (f = 0), i() } function n(t) { var n = e.style.width; e.style.width = "0px", e.offsetWidth, e.style.width = n, c = t, u && (e.style.overflowY = t), o() } function o() { var t = window.pageYOffset, n = document.body.scrollTop, o = e.style.height; e.style.height = "auto"; var i = e.scrollHeight + f; return 0 === e.scrollHeight ? void (e.style.height = o) : (e.style.height = i + "px", v = e.clientWidth, document.documentElement.scrollTop = t, void (document.body.scrollTop = n)) } function i() { var t = e.style.height; o(); var i = window.getComputedStyle(e, null); if (i.height !== e.style.height ? "visible" !== c && n("visible") : "hidden" !== c && n("hidden"), t !== e.style.height) { var r = document.createEvent("Event"); r.initEvent("autosize:resized", !0, !1), e.dispatchEvent(r) } } var d = void 0 === arguments[1] ? {} : arguments[1], s = d.setOverflowX, l = void 0 === s ? !0 : s, a = d.setOverflowY, u = void 0 === a ? !0 : a; if (e && e.nodeName && "TEXTAREA" === e.nodeName && !r.has(e)) { var f = null, c = null, v = e.clientWidth, p = function () { e.clientWidth !== v && i() }, h = function (t) { window.removeEventListener("resize", p, !1), e.removeEventListener("input", i, !1), e.removeEventListener("keyup", i, !1), e.removeEventListener("autosize:destroy", h, !1), e.removeEventListener("autosize:update", i, !1), r["delete"](e), Object.keys(t).forEach(function (n) { e.style[n] = t[n] }) }.bind(e, { height: e.style.height, resize: e.style.resize, overflowY: e.style.overflowY, overflowX: e.style.overflowX, wordWrap: e.style.wordWrap }); e.addEventListener("autosize:destroy", h, !1), "onpropertychange" in e && "oninput" in e && e.addEventListener("keyup", i, !1), window.addEventListener("resize", p, !1), e.addEventListener("input", i, !1), e.addEventListener("autosize:update", i, !1), r.add(e), l && (e.style.overflowX = "hidden", e.style.wordWrap = "break-word"), t() } } function o(e) { if (e && e.nodeName && "TEXTAREA" === e.nodeName) { var t = document.createEvent("Event"); t.initEvent("autosize:destroy", !0, !1), e.dispatchEvent(t) } } function i(e) { if (e && e.nodeName && "TEXTAREA" === e.nodeName) { var t = document.createEvent("Event"); t.initEvent("autosize:update", !0, !1), e.dispatchEvent(t) } } var r = "function" == typeof Set ? new Set : function () { var e = []; return { has: function (t) { return Boolean(e.indexOf(t) > -1) }, add: function (t) { e.push(t) }, "delete": function (t) { e.splice(e.indexOf(t), 1) } } }(), d = null; "undefined" == typeof window || "function" != typeof window.getComputedStyle ? (d = function (e) { return e }, d.destroy = function (e) { return e }, d.update = function (e) { return e }) : (d = function (e, t) { return e && Array.prototype.forEach.call(e.length ? e : [e], function (e) { return n(e, t) }), e }, d.destroy = function (e) { return e && Array.prototype.forEach.call(e.length ? e : [e], o), e }, d.update = function (e) { return e && Array.prototype.forEach.call(e.length ? e : [e], i), e }), t.exports = d });

/* Copyright (c) 2011 Piotr Rochala (http://rocha.la)
 * Dual licensed under the MIT (http://www.opensource.org/licenses/mit-license.php)
 * and GPL (http://www.opensource.org/licenses/gpl-license.php) licenses.
 * Version: 1.3.8
 */
!function (e) { e.fn.extend({ slimScroll: function (i) { var s = { width: "auto", height: "300px", size: "7px", color: "#000", position: "right", distance: "1px", start: "top", opacity: 1, alwaysVisible: !1, disableFadeOut: !1, railVisible: !1, railColor: "#333", railOpacity: .2, railDraggable: !0, railClass: "slimScrollRail", barClass: "slimScrollBar", wrapperClass: "slimScrollDiv", allowPageScroll: !1, wheelStep: 20, touchScrollStep: 200, borderRadius: "7px", railBorderRadius: "7px" }, o = e.extend(s, i); return this.each(function () { function s(t) { if (h) { var t = t || window.event, i = 0; t.wheelDelta && (i = -t.wheelDelta / 120), t.detail && (i = t.detail / 3); var s = t.target || t.srcTarget || t.srcElement; e(s).closest("." + o.wrapperClass).is(x.parent()) && r(i, !0), t.preventDefault && !y && t.preventDefault(), y || (t.returnValue = !1) } } function r(e, t, i) { y = !1; var s = e, r = x.outerHeight() - D.outerHeight(); if (t && (s = parseInt(D.css("top")) + e * parseInt(o.wheelStep) / 100 * D.outerHeight(), s = Math.min(Math.max(s, 0), r), s = e > 0 ? Math.ceil(s) : Math.floor(s), D.css({ top: s + "px" })), v = parseInt(D.css("top")) / (x.outerHeight() - D.outerHeight()), s = v * (x[0].scrollHeight - x.outerHeight()), i) { s = e; var a = s / x[0].scrollHeight * x.outerHeight(); a = Math.min(Math.max(a, 0), r), D.css({ top: a + "px" }) } x.scrollTop(s), x.trigger("slimscrolling", ~~s), n(), c() } function a(e) { window.addEventListener ? (e.addEventListener("DOMMouseScroll", s, !1), e.addEventListener("mousewheel", s, !1)) : document.attachEvent("onmousewheel", s) } function l() { f = Math.max(x.outerHeight() / x[0].scrollHeight * x.outerHeight(), m), D.css({ height: f + "px" }); var e = f == x.outerHeight() ? "none" : "block"; D.css({ display: e }) } function n() { if (l(), clearTimeout(p), v == ~~v) { if (y = o.allowPageScroll, b != v) { var e = 0 == ~~v ? "top" : "bottom"; x.trigger("slimscroll", e) } } else y = !1; return b = v, f >= x.outerHeight() ? void (y = !0) : (D.stop(!0, !0).fadeIn("fast"), void (o.railVisible && R.stop(!0, !0).fadeIn("fast"))) } function c() { o.alwaysVisible || (p = setTimeout(function () { o.disableFadeOut && h || u || d || (D.fadeOut("slow"), R.fadeOut("slow")) }, 1e3)) } var h, u, d, p, g, f, v, b, w = "<div></div>", m = 30, y = !1, x = e(this); if (x.parent().hasClass(o.wrapperClass)) { var C = x.scrollTop(); if (D = x.siblings("." + o.barClass), R = x.siblings("." + o.railClass), l(), e.isPlainObject(i)) { if ("height" in i && "auto" == i.height) { x.parent().css("height", "auto"), x.css("height", "auto"); var H = x.parent().parent().height(); x.parent().css("height", H), x.css("height", H) } else if ("height" in i) { var S = i.height; x.parent().css("height", S), x.css("height", S) } if ("scrollTo" in i) C = parseInt(o.scrollTo); else if ("scrollBy" in i) C += parseInt(o.scrollBy); else if ("destroy" in i) return D.remove(), R.remove(), void x.unwrap(); r(C, !1, !0) } } else if (!(e.isPlainObject(i) && "destroy" in i)) { o.height = "auto" == o.height ? x.parent().height() : o.height; var E = e(w).addClass(o.wrapperClass).css({ position: "relative", overflow: "hidden", width: o.width, height: o.height }); x.css({ overflow: "hidden", width: o.width, height: o.height }); var R = e(w).addClass(o.railClass).css({ width: o.size, height: "100%", position: "absolute", top: 0, display: o.alwaysVisible && o.railVisible ? "block" : "none", "border-radius": o.railBorderRadius, background: o.railColor, opacity: o.railOpacity, zIndex: 90 }), D = e(w).addClass(o.barClass).css({ background: o.color, width: o.size, position: "absolute", top: 0, opacity: o.opacity, display: o.alwaysVisible ? "block" : "none", "border-radius": o.borderRadius, BorderRadius: o.borderRadius, MozBorderRadius: o.borderRadius, WebkitBorderRadius: o.borderRadius, zIndex: 99 }), M = "right" == o.position ? { right: o.distance } : { left: o.distance }; R.css(M), D.css(M), x.wrap(E), x.parent().append(D), x.parent().append(R), o.railDraggable && D.bind("mousedown", function (i) { var s = e(document); return d = !0, t = parseFloat(D.css("top")), pageY = i.pageY, s.bind("mousemove.slimscroll", function (e) { currTop = t + e.pageY - pageY, D.css("top", currTop), r(0, D.position().top, !1) }), s.bind("mouseup.slimscroll", function (e) { d = !1, c(), s.unbind(".slimscroll") }), !1 }).bind("selectstart.slimscroll", function (e) { return e.stopPropagation(), e.preventDefault(), !1 }), R.hover(function () { n() }, function () { c() }), D.hover(function () { u = !0 }, function () { u = !1 }), x.hover(function () { h = !0, n(), c() }, function () { h = !1, c() }), x.bind("touchstart", function (e, t) { e.originalEvent.touches.length && (g = e.originalEvent.touches[0].pageY) }), x.bind("touchmove", function (e) { if (y || e.originalEvent.preventDefault(), e.originalEvent.touches.length) { var t = (g - e.originalEvent.touches[0].pageY) / o.touchScrollStep; r(t, !0), g = e.originalEvent.touches[0].pageY } }), l(), "bottom" === o.start ? (D.css({ top: x.outerHeight() - D.outerHeight() }), r(0, !0)) : "top" !== o.start && (r(e(o.start).position().top, null, !0), o.alwaysVisible || D.hide()), a(this) } }), this } }), e.fn.extend({ slimscroll: e.fn.slimScroll }) }(jQuery);

/* Push v1.0-beta
*  The MIT License (MIT)
*/
!function (e) { if ("object" == typeof exports && "undefined" != typeof module) module.exports = e(); else if ("function" == typeof define && define.amd) define([], e); else { ("undefined" != typeof window ? window : "undefined" != typeof global ? global : "undefined" != typeof self ? self : this).Push = e() } }(function () { return function e(t, n, i) { function o(s, a) { if (!n[s]) { if (!t[s]) { var u = "function" == typeof require && require; if (!a && u) return u(s, !0); if (r) return r(s, !0); var c = new Error("Cannot find module '" + s + "'"); throw c.code = "MODULE_NOT_FOUND", c } var f = n[s] = { exports: {} }; t[s][0].call(f.exports, function (e) { var n = t[s][1][e]; return o(n || e) }, f, f.exports, e, t, n, i) } return n[s].exports } for (var r = "function" == typeof require && require, s = 0; s < i.length; s++) o(i[s]); return o }({ 1: [function (e, t, n) { "use strict"; Object.defineProperty(n, "__esModule", { value: !0 }); n.default = { errors: { incompatible: "PushError: Push.js is incompatible with browser.", invalid_plugin: "PushError: plugin class missing from plugin manifest (invalid plugin). Please check the documentation.", invalid_title: "PushError: title of notification must be a string", permission_denied: "PushError: permission request declined", sw_notification_error: "PushError: could not show a ServiceWorker notification due to the following reason: ", sw_registration_error: "PushError: could not register the ServiceWorker due to the following reason: ", unknown_interface: "PushError: unable to create notification: unknown interface" } } }, {}], 2: [function (e, t, n) { "use strict"; function i(e, t) { if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function") } Object.defineProperty(n, "__esModule", { value: !0 }); var o = function () { function e(e, t) { for (var n = 0; n < t.length; n++) { var i = t[n]; i.enumerable = i.enumerable || !1, i.configurable = !0, "value" in i && (i.writable = !0), Object.defineProperty(e, i.key, i) } } return function (t, n, i) { return n && e(t.prototype, n), i && e(t, i), t } }(), r = function () { function e(t) { i(this, e), this._win = t, this.DEFAULT = "default", this.GRANTED = "granted", this.DENIED = "denied", this._permissions = [this.GRANTED, this.DEFAULT, this.DENIED] } return o(e, [{ key: "request", value: function (e, t) { var n = this, i = this.get(), o = function (i) { i === n.GRANTED || 0 === i ? e && e() : t && t() }; i !== this.DEFAULT ? o(i) : this._win.Notification && this._win.Notification.requestPermission ? this._win.Notification.requestPermission().then(o).catch(function () { t && t() }) : this._win.webkitNotifications && this._win.webkitNotifications.checkPermission ? this._win.webkitNotifications.requestPermission(o) : e && e() } }, { key: "has", value: function () { return this.get() === this.GRANTED } }, { key: "get", value: function () { return this._win.Notification && this._win.Notification.permission ? this._win.Notification.permission : this._win.webkitNotifications && this._win.webkitNotifications.checkPermission ? this._permissions[this._win.webkitNotifications.checkPermission()] : navigator.mozNotification ? this.GRANTED : this._win.external && this._win.external.msIsSiteMode ? this._win.external.msIsSiteMode() ? this.GRANTED : this.DEFAULT : this.GRANTED } }]), e }(); n.default = r }, {}], 3: [function (e, t, n) { "use strict"; function i(e) { return e && e.__esModule ? e : { default: e } } function o(e, t) { if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function") } Object.defineProperty(n, "__esModule", { value: !0 }); var r = function () { function e(e, t) { for (var n = 0; n < t.length; n++) { var i = t[n]; i.enumerable = i.enumerable || !1, i.configurable = !0, "value" in i && (i.writable = !0), Object.defineProperty(e, i.key, i) } } return function (t, n, i) { return n && e(t.prototype, n), i && e(t, i), t } }(), s = i(e("./Messages")), a = i(e("./Permission")), u = i(e("./Util")), c = i(e("./agents/DesktopAgent")), f = i(e("./agents/MobileChromeAgent")), l = i(e("./agents/MobileFirefoxAgent")), h = i(e("./agents/MSAgent")), d = i(e("./agents/WebKitAgent")), p = function () { function e(t) { o(this, e), this._currentId = 0, this._notifications = {}, this._win = t, this.Permission = new a.default(t), this._agents = { desktop: new c.default(t), chrome: new f.default(t), firefox: new l.default(t), ms: new h.default(t), webkit: new d.default(t) }, this._configuration = { serviceWorker: "/serviceWorker.min.js", fallback: function (e) { } } } return r(e, [{ key: "_closeNotification", value: function (e) { var t = !0, n = this._notifications[e]; if (void 0 !== n) { if (t = this._removeNotification(e), this._agents.desktop.isSupported()) this._agents.desktop.close(n); else if (this._agents.webkit.isSupported()) this._agents.webkit.close(n); else { if (!this._agents.ms.isSupported()) throw t = !1, new Error(s.default.errors.unknown_interface); this._agents.ms.close() } return t } return !1 } }, { key: "_addNotification", value: function (e) { var t = this._currentId; return this._notifications[t] = e, this._currentId++, t } }, { key: "_removeNotification", value: function (e) { var t = !1; return this._notifications.hasOwnProperty(e) && (delete this._notifications[e], t = !0), t } }, { key: "_prepareNotification", value: function (e, t) { var n = this, i = void 0; return i = { get: function () { return n._notifications[e] }, close: function () { n._closeNotification(e) } }, t.timeout && setTimeout(function () { i.close() }, t.timeout), i } }, { key: "_serviceWorkerCallback", value: function (e, t, n) { var i = this, o = this._addNotification(e[e.length - 1]); navigator.serviceWorker.addEventListener("message", function (e) { var t = JSON.parse(e.data); "close" === t.action && Number.isInteger(t.id) && i._removeNotification(t.id) }), n(this._prepareNotification(o, t)) } }, { key: "_createCallback", value: function (e, t, n) { var i = this, o = void 0, r = null; if (t = t || {}, o = function (e) { i._removeNotification(e), u.default.isFunction(t.onClose) && t.onClose.call(i, r) }, this._agents.desktop.isSupported()) try { r = this._agents.desktop.create(e, t) } catch (o) { var s = this._currentId, a = this.config().serviceWorker, c = function (e) { return i._serviceWorkerCallback(e, t, n) }; this._agents.chrome.isSupported() && this._agents.chrome.create(s, e, t, a, c) } else this._agents.webkit.isSupported() ? r = this._agents.webkit.create(e, t) : this._agents.firefox.isSupported() ? this._agents.firefox.create(e, t) : this._agents.ms.isSupported() ? r = this._agents.ms.create(e, t) : (t.title = e, this.config().fallback(t)); if (null !== r) { var f = this._addNotification(r), l = this._prepareNotification(f, t); u.default.isFunction(t.onShow) && r.addEventListener("show", t.onShow), u.default.isFunction(t.onError) && r.addEventListener("error", t.onError), u.default.isFunction(t.onClick) && r.addEventListener("click", t.onClick), r.addEventListener("close", function () { o(f) }), r.addEventListener("cancel", function () { o(f) }), n(l) } n(null) } }, { key: "create", value: function (e, t) { var n = this, i = void 0; if (!u.default.isString(e)) throw new Error(s.default.errors.invalid_title); return i = this.Permission.has() ? function (i, o) { try { n._createCallback(e, t, i) } catch (e) { o(e) } } : function (i, o) { n.Permission.request(function () { try { n._createCallback(e, t, i) } catch (e) { o(e) } }, function () { o(s.default.errors.permission_denied) }) }, new Promise(i) } }, { key: "count", value: function () { var e = void 0, t = 0; for (e in this._notifications) this._notifications.hasOwnProperty(e) && t++; return t } }, { key: "close", value: function (e) { var t = void 0; for (t in this._notifications) if (this._notifications.hasOwnProperty(t) && this._notifications[t].tag === e) return this._closeNotification(t) } }, { key: "clear", value: function () { var e = void 0, t = !0; for (e in this._notifications) this._notifications.hasOwnProperty(e) && (t = t && this._closeNotification(e)); return t } }, { key: "supported", value: function () { var e = !1; for (var t in this._agents) this._agents.hasOwnProperty(t) && (e = e || this._agents[t].isSupported()); return e } }, { key: "config", value: function (e) { return (void 0 !== e || null !== e && u.default.isObject(e)) && u.default.objectMerge(this._configuration, e), this._configuration } }, { key: "extend", value: function (e) { var t, n = {}.hasOwnProperty; if (!n.call(e, "plugin")) throw new Error(s.default.errors.invalid_plugin); n.call(e, "config") && u.default.isObject(e.config) && null !== e.config && this.config(e.config), t = new (0, e.plugin)(this.config()); for (var i in t) n.call(t, i) && u.default.isFunction(t[i]) && (this[i] = t[i]) } }]), e }(); n.default = p }, { "./Messages": 1, "./Permission": 2, "./Util": 4, "./agents/DesktopAgent": 6, "./agents/MSAgent": 7, "./agents/MobileChromeAgent": 8, "./agents/MobileFirefoxAgent": 9, "./agents/WebKitAgent": 10 }], 4: [function (e, t, n) { "use strict"; function i(e, t) { if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function") } Object.defineProperty(n, "__esModule", { value: !0 }); var o = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (e) { return typeof e } : function (e) { return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : typeof e }, r = function () { function e(e, t) { for (var n = 0; n < t.length; n++) { var i = t[n]; i.enumerable = i.enumerable || !1, i.configurable = !0, "value" in i && (i.writable = !0), Object.defineProperty(e, i.key, i) } } return function (t, n, i) { return n && e(t.prototype, n), i && e(t, i), t } }(), s = function () { function e() { i(this, e) } return r(e, null, [{ key: "isUndefined", value: function (e) { return void 0 === e } }, { key: "isString", value: function (e) { return "string" == typeof e } }, { key: "isFunction", value: function (e) { return e && "[object Function]" === {}.toString.call(e) } }, { key: "isObject", value: function (e) { return "object" == (void 0 === e ? "undefined" : o(e)) } }, { key: "objectMerge", value: function (e, t) { for (var n in t) e.hasOwnProperty(n) && this.isObject(e[n]) && this.isObject(t[n]) ? this.objectMerge(e[n], t[n]) : e[n] = t[n] } }]), e }(); n.default = s }, {}], 5: [function (e, t, n) { "use strict"; function i(e, t) { if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function") } Object.defineProperty(n, "__esModule", { value: !0 }); n.default = function e(t) { i(this, e), this._win = t } }, {}], 6: [function (e, t, n) { "use strict"; function i(e) { return e && e.__esModule ? e : { default: e } } function o(e, t) { if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function") } function r(e, t) { if (!e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return !t || "object" != typeof t && "function" != typeof t ? e : t } function s(e, t) { if ("function" != typeof t && null !== t) throw new TypeError("Super expression must either be null or a function, not " + typeof t); e.prototype = Object.create(t && t.prototype, { constructor: { value: e, enumerable: !1, writable: !0, configurable: !0 } }), t && (Object.setPrototypeOf ? Object.setPrototypeOf(e, t) : e.__proto__ = t) } Object.defineProperty(n, "__esModule", { value: !0 }); var a = function () { function e(e, t) { for (var n = 0; n < t.length; n++) { var i = t[n]; i.enumerable = i.enumerable || !1, i.configurable = !0, "value" in i && (i.writable = !0), Object.defineProperty(e, i.key, i) } } return function (t, n, i) { return n && e(t.prototype, n), i && e(t, i), t } }(), u = i(e("./AbstractAgent")), c = i(e("../Util")), f = function (e) { function t() { return o(this, t), r(this, (t.__proto__ || Object.getPrototypeOf(t)).apply(this, arguments)) } return s(t, u.default), a(t, [{ key: "isSupported", value: function () { return void 0 !== this._win.Notification } }, { key: "create", value: function (e, t) { return new this._win.Notification(e, { icon: c.default.isString(t.icon) || c.default.isUndefined(t.icon) ? t.icon : t.icon.x32, body: t.body, tag: t.tag, requireInteraction: t.requireInteraction }) } }, { key: "close", value: function (e) { e.close() } }]), t }(); n.default = f }, { "../Util": 4, "./AbstractAgent": 5 }], 7: [function (e, t, n) { "use strict"; function i(e) { return e && e.__esModule ? e : { default: e } } function o(e, t) { if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function") } function r(e, t) { if (!e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return !t || "object" != typeof t && "function" != typeof t ? e : t } function s(e, t) { if ("function" != typeof t && null !== t) throw new TypeError("Super expression must either be null or a function, not " + typeof t); e.prototype = Object.create(t && t.prototype, { constructor: { value: e, enumerable: !1, writable: !0, configurable: !0 } }), t && (Object.setPrototypeOf ? Object.setPrototypeOf(e, t) : e.__proto__ = t) } Object.defineProperty(n, "__esModule", { value: !0 }); var a = function () { function e(e, t) { for (var n = 0; n < t.length; n++) { var i = t[n]; i.enumerable = i.enumerable || !1, i.configurable = !0, "value" in i && (i.writable = !0), Object.defineProperty(e, i.key, i) } } return function (t, n, i) { return n && e(t.prototype, n), i && e(t, i), t } }(), u = i(e("./AbstractAgent")), c = i(e("../Util")), f = function (e) { function t() { return o(this, t), r(this, (t.__proto__ || Object.getPrototypeOf(t)).apply(this, arguments)) } return s(t, u.default), a(t, [{ key: "isSupported", value: function () { return void 0 !== this._win.external && void 0 !== this._win.external.msIsSiteMode } }, { key: "create", value: function (e, t) { return this._win.external.msSiteModeClearIconOverlay(), this._win.external.msSiteModeSetIconOverlay(c.default.isString(t.icon) || c.default.isUndefined(t.icon) ? t.icon : t.icon.x16, e), this._win.external.msSiteModeActivate(), null } }, { key: "close", value: function () { this._win.external.msSiteModeClearIconOverlay() } }]), t }(); n.default = f }, { "../Util": 4, "./AbstractAgent": 5 }], 8: [function (e, t, n) { "use strict"; function i(e) { return e && e.__esModule ? e : { default: e } } function o(e, t) { if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function") } function r(e, t) { if (!e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return !t || "object" != typeof t && "function" != typeof t ? e : t } function s(e, t) { if ("function" != typeof t && null !== t) throw new TypeError("Super expression must either be null or a function, not " + typeof t); e.prototype = Object.create(t && t.prototype, { constructor: { value: e, enumerable: !1, writable: !0, configurable: !0 } }), t && (Object.setPrototypeOf ? Object.setPrototypeOf(e, t) : e.__proto__ = t) } Object.defineProperty(n, "__esModule", { value: !0 }); var a = function () { function e(e, t) { for (var n = 0; n < t.length; n++) { var i = t[n]; i.enumerable = i.enumerable || !1, i.configurable = !0, "value" in i && (i.writable = !0), Object.defineProperty(e, i.key, i) } } return function (t, n, i) { return n && e(t.prototype, n), i && e(t, i), t } }(), u = i(e("./AbstractAgent")), c = i(e("../Util")), f = i(e("../Messages")), l = function (e) { function t() { return o(this, t), r(this, (t.__proto__ || Object.getPrototypeOf(t)).apply(this, arguments)) } return s(t, u.default), a(t, [{ key: "isSupported", value: function () { return void 0 !== this._win.navigator && void 0 !== this._win.navigator.serviceWorker } }, { key: "getFunctionBody", value: function (e) { return e.toString().match(/function[^{]+{([\s\S]*)}$/)[1] } }, { key: "create", value: function (e, t, n, i, o) { var r = this; this._win.navigator.serviceWorker.register(i), this._win.navigator.serviceWorker.ready.then(function (i) { var s = { id: e, link: n.link, origin: document.location.href, onClick: c.default.isFunction(n.onClick) ? r.getFunctionBody(n.onClick) : "", onClose: c.default.isFunction(n.onClose) ? r.getFunctionBody(n.onClose) : "" }; void 0 !== n.data && null !== n.data && (s = Object.assign(s, n.data)), i.showNotification(t, { icon: n.icon, body: n.body, vibrate: n.vibrate, tag: n.tag, data: s, requireInteraction: n.requireInteraction, silent: n.silent }).then(function () { i.getNotifications().then(function (e) { i.active.postMessage(""), o(e) }) }).catch(function (e) { throw new Error(f.default.errors.sw_notification_error + e.message) }) }).catch(function (e) { throw new Error(f.default.errors.sw_registration_error + e.message) }) } }, { key: "close", value: function () { } }]), t }(); n.default = l }, { "../Messages": 1, "../Util": 4, "./AbstractAgent": 5 }], 9: [function (e, t, n) { "use strict"; function i(e, t) { if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function") } function o(e, t) { if (!e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return !t || "object" != typeof t && "function" != typeof t ? e : t } function r(e, t) { if ("function" != typeof t && null !== t) throw new TypeError("Super expression must either be null or a function, not " + typeof t); e.prototype = Object.create(t && t.prototype, { constructor: { value: e, enumerable: !1, writable: !0, configurable: !0 } }), t && (Object.setPrototypeOf ? Object.setPrototypeOf(e, t) : e.__proto__ = t) } Object.defineProperty(n, "__esModule", { value: !0 }); var s = function () { function e(e, t) { for (var n = 0; n < t.length; n++) { var i = t[n]; i.enumerable = i.enumerable || !1, i.configurable = !0, "value" in i && (i.writable = !0), Object.defineProperty(e, i.key, i) } } return function (t, n, i) { return n && e(t.prototype, n), i && e(t, i), t } }(), a = function (e) { return e && e.__esModule ? e : { default: e } }(e("./AbstractAgent")), u = function (e) { function t() { return i(this, t), o(this, (t.__proto__ || Object.getPrototypeOf(t)).apply(this, arguments)) } return r(t, a.default), s(t, [{ key: "isSupported", value: function () { return void 0 !== this._win.navigator.mozNotification } }, { key: "create", value: function (e, t) { var n = this._win.navigator.mozNotification.createNotification(e, t.body, t.icon); return n.show(), n } }]), t }(); n.default = u }, { "./AbstractAgent": 5 }], 10: [function (e, t, n) { "use strict"; function i(e, t) { if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function") } function o(e, t) { if (!e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return !t || "object" != typeof t && "function" != typeof t ? e : t } function r(e, t) { if ("function" != typeof t && null !== t) throw new TypeError("Super expression must either be null or a function, not " + typeof t); e.prototype = Object.create(t && t.prototype, { constructor: { value: e, enumerable: !1, writable: !0, configurable: !0 } }), t && (Object.setPrototypeOf ? Object.setPrototypeOf(e, t) : e.__proto__ = t) } Object.defineProperty(n, "__esModule", { value: !0 }); var s = function () { function e(e, t) { for (var n = 0; n < t.length; n++) { var i = t[n]; i.enumerable = i.enumerable || !1, i.configurable = !0, "value" in i && (i.writable = !0), Object.defineProperty(e, i.key, i) } } return function (t, n, i) { return n && e(t.prototype, n), i && e(t, i), t } }(), a = function (e) { return e && e.__esModule ? e : { default: e } }(e("./AbstractAgent")), u = function (e) { function t() { return i(this, t), o(this, (t.__proto__ || Object.getPrototypeOf(t)).apply(this, arguments)) } return r(t, a.default), s(t, [{ key: "isSupported", value: function () { return void 0 !== this._win.webkitNotifications } }, { key: "create", value: function (e, t) { var n = this._win.webkitNotifications.createNotification(t.icon, e, t.body); return n.show(), n } }, { key: "close", value: function (e) { e.cancel() } }]), t }(); n.default = u }, { "./AbstractAgent": 5 }], 11: [function (e, t, n) { "use strict"; var i = function (e) { return e && e.__esModule ? e : { default: e } }(e("./classes/Push")); t.exports = new i.default("undefined" != typeof window ? window : void 0) }, { "./classes/Push": 3 }] }, {}, [11])(11) });

(function ($) {
    var allowed_extensions = ["psd", "ai", "eps", "pptx", "rtf", "wma", "odp", "ods", "sxw", "sxi", "sxc", "dwg", "xps", "jpg", "jpeg", "png", "gif", "svg", "pdf", "doc", "docx", "key", "ppt", "odt", "xls", "xlsx", "zip", "rar", "mp3", "m4a", "ogg", "wav", "mp4", "mov", "wmv", "avi", "mpg", "ogv", "3gp", "3g2", "mkv", "txt", "ico", "exe", "csv", "java", "js", "xml", "unx", "ttf", "font", "css"];
    var sb_user_arr = { id: "", img: "", username: "", email: "" };
    var new_user_id = getRandomInt(9999999, 99999999);
    var current_cnt;
    var isChat = false;
    var real_time = false;
    var sounds = false;
    var audio;
    var isScrollBox = false;
    var msgCount = 999999;
    var sbInit = true;
    var isGuest = false;
    var isNewGuest = false;
    var isWelcome = false;
    var welcomeShowed = false;
    var welcomeAlways = false;
    var isFollow = false;
    var isWpAdmin = false;
    var isSlack = false;
    var isPush = false;
    var isFlash = false;
    var submitEnabled = true;
    var tabActive = true;
    var flashInterval;
    var flashMessage;
    var sb_main;
    var sb_plugin_url;
    var sb_plugin_ajax_url;
    var is_php = false;
    var lang_php = "";

    $(document).ready(function () {

        //VARIOUS
        sb_main = $("#sb-main");
        current_cnt = $("body");
        autosize($(".sb-editor textarea"));
        if ($(sb_main).hasClass("sb-php")) {
            is_php = true;
        }
        if (is_php) {
            sb_plugin_url = $("#sb-php-init").attr("src");
            lang_php = sb_plugin_url;
            sb_plugin_url = sb_plugin_url.substr(0, sb_plugin_url.lastIndexOf("/php"));
            sb_plugin_ajax_url = sb_plugin_url + "/php/core.php";
            if (lang_php.indexOf("lang=") > 0) {
                lang_php = lang_php.substr(lang_php.indexOf("lang=") + 5)
            } else {
                lang_php = "";
            }
        } else {
            sb_plugin_url = sb_plugin_url_wp;
            sb_plugin_ajax_url = sb_ajax_url;
        }
        if ($(".sb-chat-cnt").length) {
            isChat = true;
        }
        if ($("#sb-audio").length) {
            audio = $("#sb-audio")[0];
            sounds = true;
        }
        if (!isEmpty($(sb_main).attr("data-welcome"))) {
            isWelcome = true;
        }
        if (!isEmpty($(sb_main).attr("data-follow"))) {
            isFollow = true;
        }
        if (!isEmpty($(sb_main).attr("data-slack"))) {
            isSlack = true;
        }
        if (!isEmpty($(sb_main).attr("data-flash"))) {
            flashMessage = $(sb_main).attr("data-flash");
        } else {
            flashMessage = "New message received";
        }
        if ($(sb_main).hasClass("sb-push")) {
            if (Push.Permission.get() == "granted") {
                isPush = true;
            } else {
                Push.Permission.request(function () { isPush = true; }, function () { });
            }
        }
        if ($(sb_main).hasClass("sb-flash")) {
            isFlash = true;
        }
        if ($(sb_main).hasClass("welcome-always")) {
            welcomeAlways = true;
        } 
        if ($("body.admin-bar").length) {
            isWpAdmin = true;
        }
        $(sb_main).css("display", "");
        $("body").on("hover", sb_main, function () {
            clearInterval(flashInterval);
        });

        //LOGIN
        $("body").on("click", ".sb-register-link div", function () {
            $(".sb-login").removeClass("active");
            $(".sb-register").addClass("active");
        });
        $("body").on("click", ".sb-login-link div", function () {
            $(".sb-login").addClass("active");
            $(".sb-register").removeClass("active");
        });
        $("body").on("click", ".sb-submit-login", function () {
            var _user = $('.sb-input-user input').val();
            var _password = $('.sb-input-psw input').val();

            if (_user != "" && _password != "") {
                jQuery.ajax({
                    method: "POST",
                    url: sb_plugin_ajax_url,
                    data: {
                        action: 'sb_ajax_login',
                        user: _user,
                        password: _password,
                    },
                    async: false
                }).done(function (response) {
                    if (response == "success") {
                        $(".sb-error-msg").hide();
                        var link = window.location.href;
                        link = link.replace("&logout=true", "").replace("?logout=true", "").replace("&login=true", "").replace("?login=true", "");
                        if (link.indexOf("?") > 0) {
                            window.location.href = link + "&login=true";
                        } else {
                            window.location.href = link + "?login=true";
                        }
                    } else {
                        $(".sb-error-msg").show();
                    }
                });
            }
        });
        $("body").on("click", ".sb-logout", function () {
            jQuery.ajax({
                method: "POST",
                url: sb_plugin_ajax_url,
                data: {
                    action: 'sb_ajax_logout'
                }
            }).done(function (response) {
                if (response == "success") {
                    var link = window.location.href;
                    link = link.replace("&logout=true", "").replace("?logout=true", "").replace("&login=true", "").replace("?login=true", "");
                    if (link.indexOf("?") > 0) {
                        window.location.href = link + "&logout=true";
                    } else {
                        window.location.href = link + "?logout=true";
                    }
                }
            });
        });

        //REGISTER
        $("body").on("click", ".sb-submit-register", function () {
            var _user = $(".sb-input-reg-user input").val();
            var _psw = $(".sb-input-reg-psw input").val();
            var _email;
            var err = $(".sb-error-msg-reg");
            var msg = $(err).attr("data-messages").split("|");

            if ($(".sb-input-reg-user input").attr("type") == "email") {
                _email = _user;
            } else {
                var t = $(".sb-email  input");
                if (t.length) _email = $(t).val();
                else _email = "--";
            }
            if (_user != "" && _psw != "" && (_email != "--" && (_email.indexOf("@") < 1 || _email.indexOf(".") < 1))) {
                $(err).html(msg[3]).show();
                _email = "";
            } else {
                $(err).hide();
            }
            if (_user != "" && _psw != "" && (_email != "" || _email == "--")) {
                if (_psw.length > 3) {
                    if (_psw == $(".sb-input-reg-psw-2  input").val()) {
                        var _extra1 = $(".sb-extra-1  input").val();
                        var _extra2 = $(".sb-extra-2  input").val();
                        var _extra3 = $(".sb-extra-3  input").val();
                        var _extra4 = $(".sb-extra-4  input").val();
                        var _img = $(".sb-input-reg-img img").attr("src");

                        if (isEmpty(_email)) _email = "";
                        if (isEmpty(_extra1)) _extra1 = "";
                        if (isEmpty(_extra2)) _extra2 = "";
                        if (isEmpty(_extra3)) _extra3 = "";
                        if (isEmpty(_extra4)) _extra4 = "";
                        if (isEmpty(_img)) _img = sb_plugin_url + '/media/user-2.jpg';

                        jQuery.ajax({
                            method: "POST",
                            url: sb_plugin_ajax_url,
                            data: {
                                action: 'sb_ajax_register',
                                id: new_user_id,
                                img: _img,
                                username: _user,
                                psw: _psw,
                                email: _email,
                                extra1: _extra1,
                                extra2: _extra2,
                                extra3: _extra3,
                                extra4: _extra4,
                            }
                        }).done(function (response) {
                            if (response == "success") {
                                $(".sb-login .sb-success-registration").show();
                                $(".sb-login-link div").click();
                            }
                            if (response == "error-user-double") {
                                $(err).html(msg[2]).show();
                            }
                        });
                    } else {
                        $(err).html(msg[0]).show();
                    }
                } else {
                    $(err).html(msg[1]).show();
                }
            }
        });
        $("body").on("click", ".sb-input-reg-img", function () {
            $(".sb-upload-profile-img").click();
        });
        $('.sb-upload-profile-img').change(function (data) {
            var files = $(this).prop('files');
            if (files[0].name.indexOf(".jpg") > 0 || files[0].name.indexOf(".png") > 0) {
                sb_upload_profile(files);
            }
        });

        //CONVERSATION AND INIT
        sb_user_arr = localStorage.getItem("sb_guest_user");
        if (!isEmpty(sb_user_arr)) {
            sb_user_arr = JSON.parse(sb_user_arr);
        } else {
            var id = getRandomInt(9999, 999999);
            sb_user_arr = { id: "guest-" + id, img: sb_plugin_url + "/media/user-2.jpg", username: "Guest " + id, email: "" };
            isGuest = true;
            isNewGuest = true;
        }
        jQuery.ajax({
            method: "POST",
            url: sb_plugin_ajax_url,
            data: {
                action: 'sb_ajax_init_user',
                user_id: sb_user_arr["id"],
                user_img: sb_user_arr["img"],
                user_name: sb_user_arr["username"],
                user_email: sb_user_arr["email"]
            }
        }).done(function (response) {
            if (response != "success") {
                sb_user_arr = JSON.parse(response);
                if (isNewGuest) {
                    localStorage.setItem("sb_guest_user", JSON.stringify(sb_user_arr));
                }
                isGuest = true;
            }
            $(".sb-list,.sb-chat-list").each(function () {
                var t = this;
                if (!isEmpty($(t).attr("data-scroll")) || isChat) {
                    var _height = $(t).attr("data-height");
                    var offset = parseInt($(t).attr("data-offset"));
                    if (isChat) _height = 300;
                    if (isEmpty(offset)) offset = 0;
                    if (isWpAdmin) offset += 32;
                    if (_height == "fullscreen") _height = window.innerHeight - offset;
                    else parseInt(_height);
                    if (_height > 200) {
                        isScrollBox = true;
                        var optionsString = $(t).attr("data-options");
                        var optionsArr;
                        var options = {
                            height: _height,
                            size: '4px',
                            color: '#AAB2BA',
                            start: 'bottom',
                            allowPageScroll: ((isChat) ? false : true)
                        }
                        if (!isEmpty(optionsString)) {
                            optionsArr = optionsString.split(",");
                            options = getOptionsString(optionsString, options);
                        }
                        $(t).slimScroll(options);
                    }
                } else {
                    if ($(".sb-card").length) {
                        $('body').animate({
                            scrollTop: $(".sb-card").last().offset().top
                        }, 1);
                    }
                }
                sb_read_messages(t, isChat);
                if (!isChat && !real_time) {
                    setInterval(function () {
                        if (submitEnabled) {
                            sb_read_messages($(".sb-list"));
                        }
                    }, 5000);
                    real_time = true;
                }
            });
        });
        $("body").on("click", ".sb-editor .sb-submit", function () {
            var files = $(this).closest(".sb-editor").find('.sb-upload-files').prop('files');
            current_cnt = $(this).closest(".sb-cnt-global");
            if (isNewGuest) {
                jQuery.ajax({
                    method: "POST",
                    url: sb_plugin_ajax_url,
                    data: {
                        action: 'sb_ajax_register',
                        id: sb_user_arr["id"],
                        img: sb_user_arr["img"],
                        username: sb_user_arr["username"],
                        psw: "123456",
                        email: ""
                    }
                }).done(function (response) {
                    if (files.length > 0) {
                        sb_upload_files(files);
                        $(".sb-clear-msg").hide();
                    } else {
                        sb_add_message();
                    }
                });
                isNewGuest = false;
            } else {
                if (files.length > 0) {
                    sb_upload_files(files);
                    $(".sb-clear-msg").hide();
                } else {
                    sb_add_message();
                }
            }
            submitEnabled = false;
            if (!real_time) {
                setInterval(function () {
                    if (submitEnabled) {
                        sb_read_messages($(".sb-chat-list,.sb-list"));
                    }
                }, 5000);
                real_time = true;
            }
            $(".sb-list-msg").hide();
        });
        $("body").on("click", ".sb-clear-msg", function () {
            $(".sb-editor .sb-upload-files").replaceWith($(".sb-editor .sb-upload-files").val('').clone(true));
            $(".sb-attachment-cnt .sb-attachments-list").html("");
            $(".sb-clear-msg").hide();
        });

        //CHAT
        if (isChat) {
            $("body").on("click", ".sb-chat-btn", function () {
                var t = $(".sb-chat-cnt");
                if ($(t).hasClass("sb-active")) {
                    $(t).removeClass("sb-active");
                } else {
                    $(t).addClass("sb-active");
                    if (!real_time) {
                        setInterval(function () {
                            if (submitEnabled) {
                                sb_read_messages($(".sb-chat-list"));
                            }
                        }, 5000);
                        real_time = true;
                    }
                }
            });
            $(".sb-chat .sb-editor textarea").on("keydown", function (e) {
                if (e.which == 13) {
                    $(".sb-chat .sb-submit").click();
                    e.preventDefault;
                }
            });
        }
        $("body").on("click", ".sb-btn-email", function () {
            var t = $(this).closest(".sb-card-cnt");
            var _email = $(t).find("input").val();
            if (_email != "") {
                $(t).find(".sb-error-msg").hide();
                if (_email.indexOf("@") > 0 && _email.indexOf(".") > 0) {
                    jQuery.ajax({
                        method: "POST",
                        url: sb_plugin_ajax_url,
                        data: {
                            action: 'sb_ajax_update_user',
                            id: sb_user_arr["id"],
                            email: _email
                        }
                    }).done(function (response) {
                        if (response == "success") {
                            $(t).html($(t).find(".sb-success-msg").html()).addClass("sb-success-msg");
                        }
                    });
                } else {
                    $(t).find(".sb-error-msg").show();
                }
            }
        });

        //UPLOADER
        $("body").on("click", ".sb-attachment", function () {
            $(this).closest(".sb-editor").find(".sb-upload-files").click();
        });
        $('.sb-upload-files').change(function (data) {
            var html = "";
            var extension_error = false;
            var len = data.currentTarget.files.length;
            for (var i = 0; i < len; i++) {
                html += '<div class="sb-attachment-item">' + data.currentTarget.files[i].name + '</div>';
            }
            $(this).closest(".sb-editor").find(".sb-attachment-cnt .sb-attachments-list").html(html);
            $(".sb-clear-msg").show();
        });
    });

    //UPLOADER
    function sb_upload_files(files) {
        $(".sb-progress-bar").css("width", "0%");
        $(".sb-progress").show();

        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            var form_data = new FormData();
            form_data.append('file', file);
            form_data.append('user_id', sb_user_arr["id"]);
            form_data.append('type', 'attachments');
            jQuery.ajax({
                url: sb_plugin_url + ((is_php) ? '/php/upload.php' : '/include/upload.php'),
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                type: 'post',
                success: function (response) { },
                xhr: function () {
                    var myXhr = jQuery.ajaxSettings.xhr();
                    if (myXhr.upload) {
                        myXhr.upload.addEventListener('progress', sb_progress, false);
                    }
                    return myXhr;
                },
            });
        }
    }
    function sb_progress(e) {
        if (e.lengthComputable) {
            var max = e.total;
            var current = e.loaded;

            var percentage = (current * 100) / max;
            $(".sb-progress-bar").css("width", percentage + "%");

            if (percentage >= 100) {
                sb_add_message();
                $("#status-completed").show();
                $(".sb-progress").hide();
                $(".sb-clear-msg").hide();
            }
        }
    }
    function sb_upload_profile(files) {
        $(".sb-progress-bar").css("width", "0%");
        $(".sb-progress").show();
        var form_data = new FormData();
        form_data.append('file', files[0]);
        form_data.append('user_id', new_user_id);
        form_data.append('type', 'profile');
        jQuery.ajax({
            url: sb_plugin_url + ((is_php) ? '/php/upload.php' : '/include/upload-profile.php'),
            dataType: 'image',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'post',
            success: function (response) { },
            xhr: function () {
                var myXhr = jQuery.ajaxSettings.xhr();
                if (myXhr.upload) {
                    myXhr.upload.addEventListener('progress', sb_profile_progress, false);
                }
                return myXhr;
            }
        });
    }
    function sb_profile_progress(e) {
        if (e.lengthComputable) {
            var max = e.total;
            var current = e.loaded;
            var percentage = (current * 100) / max;
            $(".sb-progress-bar").css("width", percentage + "%");
            if (percentage >= 100) {
                var url = ((is_php) ? sb_plugin_url + '/php/uploads/' : sb_wp_url + '/wp-content/uploads/supportboard/') + new_user_id + '/' + new_user_id + '.jpg';
                setTimeout(function () {
                    $(".sb-input-reg-img img").attr("src", url);
                }, 300);
                $(".sb-progress").hide();
            }
        }
    }

    //CONVERSATION
    function sb_add_message() {
        var cnt = current_cnt;
        var ta = $(cnt).find(".sb-editor textarea");
        var _msg = $(ta).val();
        var files = $(cnt).find('.sb-editor .sb-upload-files').prop('files');
        var d = new Date();
        var files_html = '';
        var files_string = '';
        var extension_error = false;
        if (files.length > 0) {
            files_html = '<div class="sb-files">';
            for (var i = 0; i < files.length; i++) {
                var name = files[i].name;
                var extension = name.substring(name.lastIndexOf('.') + 1);
                if ($.inArray(extension, allowed_extensions) > -1) {
                    var url = ((is_php) ? sb_plugin_url + '/php/uploads/' : sb_wp_url + '/wp-content/uploads/supportboard/') + sb_user_arr["id"] + '/' + files[i].name;
                    files_html += getAttachmentCode(url, files[i].name);
                    files_string += url + "|" + files[i].name + "?";
                } else {
                    extension_error = true;
                }
            }
            files_html += '</div>';
            files_string = files_string.substring(0, files_string.length - 1);
        }
        if (_msg != "") {
            _msg = $.trim(_msg);
        }
        if (_msg.length > 0 || files_string != "") {
            $(cnt).find(".sb-editor .sb-loader").show();
            _msg = parseMessage(_msg);
            jQuery.ajax({
                method: "POST",
                url: sb_plugin_ajax_url,
                data: {
                    action: 'sb_ajax_add_message',
                    msg: _msg,
                    files: files_string,
                    time: d.toLocaleString(),
                    user_id: sb_user_arr["id"],
                    user_img: sb_user_arr["img"],
                    user_name: sb_user_arr["username"],
                    user_type: "user",
                    environment: ((is_php) ? "php" : "wp"),
                    sb_lang: lang_php
                }
            }).done(function (response) {
                submitEnabled = true;
                if (response.length > 5) {
                    response = JSON.parse(response);
                } else {
                    response = ["error", ""];
                }
                if (response[0] == "success" || response[0] == "success-bot") {
                    jQuery.ajax({
                        method: "POST",
                        url: sb_plugin_ajax_url,
                        data: {
                            action: 'sb_send_async_email',
                            environment: ((is_php) ? "php" : "wp")
                        }
                    }).done(function (response) { });

                    var list = $(cnt).find(".sb-list,.sb-chat-list");
                    $(list).append('<div class="sb-card sb-card-right' + ((_msg == "") ? ' sb-card-no-msg' : '') + '"><div class="sb-thumb"><img src="' + sb_user_arr["img"] + '" /><div>' + sb_user_arr["username"] + '</div></div>' +
                    '<div class="sb-card-cnt"><div class="sb-message">' + formatMessage(_msg) + '</div>' + files_html + '<div class="sb-time">' + d.toLocaleString() + '</div></div></div>');

                    sb_send_slack_message(_msg, files_string);

                    if (response[0] == "success-bot") {
                        files_html = "";
                        files_string = "";
                        if (!isEmpty(response[1]["files"]) && response[1]["files"].length > 0) {
                            files_html += '<div class="sb-files">';
                            for (var j = 0; j < response[1]["files"].length; j++) {
                                if (response[1]["files"][j].indexOf("|") > 0) {
                                    var arr = response[1]["files"][j].split("|");
                                    files_html += getAttachmentCode(arr[0], arr[1]);
                                    files_string += arr[0] + "|" + arr[1] + "?";
                                }
                            }
                            files_html += '</div>';
                            files_string = files_string.substring(0, files_string.length - 1);
                        }

                        setTimeout(function () {
                            $(list).append('<div class="sb-card"><div class="sb-thumb"><img src="' + response[1]["user_img"] + '" /><div>' + response[1]["user_name"] + '</div></div>' +
                            '<div class="sb-card-cnt"><div class="sb-message">' + formatMessage(response[1]["msg"]) + '</div>' + files_html + '<div class="sb-time">' + response[1]["time"] + '</div></div></div>');
                            msgCount++;
                            scrollToMessagePos(list, isChat);
                            if (sounds && isChat) {
                                audio.play();
                            }
                            sb_send_slack_message(formatMessage(response[1]["msg"]), files_string, { "id": sb_user_arr["id"], "name": response[1]["user_name"], "img": response[1]["user_img"] }, true);
                        }, 1500);
                    } else {
                        if (isFollow && !welcomeShowed && isChat && isGuest && sb_user_arr["email"] == "") {
                            var currentCount = msgCount + 1;
                            if (msgCount == 999999) currentCount = 1;
                            setTimeout(function () {
                                if (msgCount == currentCount) {
                                    $(list).append($("#sb-card-contacts-cnt").html());
                                    scrollToMessagePos(list, isChat);
                                    welcomeShowed = true;
                                }
                            }, 15000);
                        }
                    }
                    scrollToMessagePos(list, isChat);
                    msgCount++;
                }
                $(cnt).find(".sb-editor .sb-loader").hide();
                $(cnt).find(".sb-progress-bar").css("width", "0%");
                $(ta).val("").focus();
                $(sb_main).removeClass("sb-new");
            });
        }
        $(ta).val("").focus();
        $(cnt).find(".sb-editor .sb-upload-files").replaceWith($(cnt).find(".sb-editor .sb-upload-files").val('').clone(true));
        $(cnt).find(".sb-attachment-cnt .sb-attachments-list").html("");
        if (extension_error) {
            alert("Sorry, some file type is not permitted for security reasons. Insert the files into zip archive.");
        }
    }
    function sb_read_messages(t, isChat) {
        if (isEmpty(isChat)) {
            if ($(t).hasClass("sb-chat-list")) isChat = true;
            else isChat = false;
        }
        jQuery.ajax({
            method: "POST",
            url: sb_plugin_ajax_url,
            data: {
                action: 'sb_ajax_read_messages'
            }
        }).done(function (response) {
            var empty = true;
            if (response != "error" && response.length > 10) {
                var arr_conversations = false;
                var last_msg_user_type = "";
                var len = 0;
                try {
                    arr_conversations = JSON.parse(response);
                } catch (e) {
                    $(t).html("The conversation can not be processed to a JSON parser error. Please contact the support by email or phone. We are sorry for the problem.");
                    console.log(e.message);
                }
                if (arr_conversations != false) {
                    empty = false;
                    var html = '';
                    var sb_agent_arr = { id: "", img: "", name: "" };
                    len = arr_conversations.length;
                    if ($(t).find(".sb-card:not(.sb-card-exclude)").length != arr_conversations.length) {
                        for (var i = 0; i < len; i++) {
                            var msg = arr_conversations[i]["msg"];
                            if (msg != "" || (!isEmpty(arr_conversations[i]["files"]) && arr_conversations[i]["files"].length > 0)) {
                                var user_type = "agent";
                                if (arr_conversations[i]["user_id"] == sb_user_arr["id"]) user_type = "user";
                                var msg_tmp = commandsMessage(msg);
                                if (msg_tmp == false) {
                                    var files_html = '';
                                    if (!isEmpty(arr_conversations[i]["files"]) && arr_conversations[i]["files"].length > 0) {
                                        files_html += '<div class="sb-files">';
                                        for (var j = 0; j < arr_conversations[i]["files"].length; j++) {
                                            if (arr_conversations[i]["files"][j].indexOf("|") > 0) {
                                                var arr = arr_conversations[i]["files"][j].split("|");
                                                files_html += getAttachmentCode(arr[0], arr[1]);
                                            }
                                        }
                                        files_html += '</div>';
                                    }
                                    var time = arr_conversations[i]["time"];
                                    if (time.indexOf("unix") > -1) {
                                        time = new Date(parseInt(time.replace("unix", "") + "000")).toLocaleString();
                                    }
                                    html += '<div class="sb-card' + ((user_type == "user") ? " sb-card-right" : "") + ((msg == "") ? ' sb-card-no-msg' : '') + '" data-user-id="' + arr_conversations[i]["user_id"] + '"><div class="sb-thumb"><img src="' + arr_conversations[i]["user_img"] + '" /><div>' + arr_conversations[i]["user_name"] + '</div></div>' +
                                     '<div class="sb-card-cnt"><div class="sb-message">' + formatMessage(msg) + '</div>' + files_html + '<div class="sb-time">' + time + '</div></div></div>';
                                } else {
                                    html += msg_tmp;
                                }
                                if (user_type == "agent") {
                                    sb_agent_arr = { id: arr_conversations[i]["user_id"], img: arr_conversations[i]["user_img"], name: arr_conversations[i]["user_name"] };
                                }
                                last_msg_user_type = user_type;
                            }
                        }
                        $(t).html(html);
                        if (sbInit && sb_agent_arr["id"] != "") {
                            var subtitle = $(sb_main).attr("data-agent");
                            var img_c = "";
                            if (!isEmpty(subtitle)) {
                                subtitle = '<div class="sb-chat-header-sub">' + subtitle + '</div>';
                            } else {
                                subtitle = '';
                                img_c = " sb-only-name";
                            }
                            $(".sb-chat-header").html('<img class="sb-chat-header-img' + img_c + '" src="' + sb_agent_arr["img"] + '" /><div class="sb-chat-header-text">' + sb_agent_arr["name"] + subtitle + '</div>').addClass("sb-chat-header-agent");
                        }
                        if (sbInit && html == "") {
                            $(sb_main).addClass("sb-new");
                        }
                        if (sounds) {
                            if (msgCount < len) {
                                audio.play();
                            }
                        }
                        if (!tabActive && !sbInit && isPush) {
                            Push.create(sb_agent_arr["name"], {
                                body: arr_conversations[len - 1]["msg"],
                                icon: sb_agent_arr["img"],
                                timeout: 4000,
                                onClick: function () {
                                    openChat(false);
                                }
                            });
                        }
                        if (isFlash && !sbInit) {
                            var title = document.title;
                            clearInterval(flashInterval);
                            flashInterval = setInterval(function () {
                                document.title = flashMessage;
                                setTimeout(function () {
                                    document.title = title;
                                }, 1500);
                            }, 3000);
                        }
                    }
                }
                if (empty) {
                    $(t).find(".sb-list-msg").show();
                }
                if (isGuest && isChat && sbInit && msgCount > 1 && last_msg_user_type == "agent") {
                    openChat(sounds);
                }
                if ((msgCount < len) || sbInit) {
                    scrollToMessagePos(t, isChat);
                    sbInit = false;
                } else {
                    if ((msgCount < len || msgCount == 999999) && $(".sb-card").length) {
                        $('body').animate({
                            scrollTop: $(".sb-card").last().offset().top
                        }, 1);
                    }
                }
                msgCount = len;
            } else {
                $(sb_main).addClass("sb-new");
                if (isChat) {
                    var wm = $(sb_main).attr("data-welcome");
                    if (!isEmpty(wm) && isWelcome && (welcomeAlways || isEmpty(localStorage.getItem("sb_first_visit")))) {
                        setTimeout(function () {
                            $(t).html('<div class="sb-card" data-user-id="welcome"><div class="sb-thumb"><img src="' + $(sb_main).attr("data-welcome-img") + '"><div></div></div><div class="sb-card-cnt"><div class="sb-message">' + wm + '</div><div class="sb-time"></div></div></div>');
                            openChat(sounds);
                            var d = new Date();
                            localStorage.setItem("sb_first_visit", d.toLocaleString());
                        }, 1000);
                    }
                } else {
                    $(t).find(".sb-list-msg").show();
                }
            }
            $(".sb-chat-list-loader,.sb-list-loader").remove();
        });
    }
    function sb_send_slack_message(message, files, user_info, isBot) {
        if (isSlack) {
            var id;
            var img;
            var name;
            if (isEmpty(isBot)) isBot = false;
            if (isEmpty(files)) files = "";
            if (!isEmpty(user_info)) {
                id = user_info["id"];
                img = user_info["img"];
                name = user_info["name"];
            } else {
                id = sb_user_arr["id"];
                img = sb_user_arr["img"];
                name = sb_user_arr["username"];
            }
            jQuery.ajax({
                method: "POST",
                url: sb_plugin_ajax_url,
                data: {
                    action: 'sb_ajax_slack_send_message',
                    msg: message,
                    files: files,
                    user_id: id,
                    user_img: img,
                    user_name: name,
                    is_bot: isBot
                }
            }).done(function (response) { });
        }
    }

    //VARIOUS
    function openChat(sounds) {
        if (!$(".sb-chat-cnt").hasClass("sb-active")) {
            $(".sb-chat-btn").click();
            if (sounds) {
                audio.play();
            }
        }
    }
    function scrollToMessagePos(list, isChat) {
        if ((!isEmpty($(list).attr("data-scroll")) || isChat) && isScrollBox) {
            $(list).slimScroll({
                scrollTo: '99999px'
            });
        } else {
            window.scrollTo(0, document.body.scrollHeight);
        }
    }
    function parseMessage(msg) {
        if (!isEmpty(msg)) {
            //Breakline
            msg = msg.replace(/(?:\r\n|\r|\n)/g, '<br />');

            //Json
            msg = msg.replace(/'/g, "&#39;");
            msg = msg.replace(/\/"/g, "/&#8220;");
            msg = msg.replace(/\"/g, "&#8220;");
          
            return msg
        }
        return "";
    }
    function formatMessage(msg) {
        if (!isEmpty(msg)) {
            //URLs starting with http://, https://, or ftp://
            var replacePattern1 = /(\b(https?|ftp):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/gim;
            var replacedText = msg.replace(replacePattern1, '<a href="$1" target="_blank">$1</a>');

            //URLs starting with www. (without // before it, or it'd re-link the ones done above)
            var replacePattern2 = /(^|[^\/f])(www\.[\S]+(\b|$))/gim;
            var replacedText = replacedText.replace(replacePattern2, '$1<a href="http://$2" target="_blank">$2</a>');

            //Change email addresses to mailto:: links
            var replacePattern3 = /(\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,6})/gim;
            var replacedText = replacedText.replace(replacePattern3, '<a href="mailto:$1">$1</a>');

            //Restore HTML tags
            replacedText = replacedText.replace(/&lt;br \/&gt;/g, '<br />');
            replacedText = replacedText.replace(/&amp;#8220;/g, '"');
            replacedText = replacedText.replace(/&amp;#39;/g, '"');

            //Return
            return replacedText
        }
        return "";
    }
    function commandsMessage(msg) {
        var isCommand = false;
        //Show #follow command
        if (msg == "#follow") {
            msg = $("#sb-card-contacts-cnt").html().replace("sb-card-exclude", "sb-card-command");
            isCommand = true;
        }
        if (isCommand) return msg;
        else return false;
    }
    function isEmpty(obj) { var type = typeof (obj); if (type !== "undefined" && obj !== null && (obj.length > 0 || (type == "object" && Object.keys(obj).length > 0) || type === "boolean" || type == 'number') && obj !== "undefined") return false; else return true; }
    function getRandomInt(min, max) {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }
    function getOptionsString(txt, mainArray) {
        var optionsArr = txt.split(",");
        for (var i = 0; i < optionsArr.length; i++) {
            mainArray[optionsArr[i].split(":")[0]] = correctValue(optionsArr[i].split(":")[1]);
        }
        return mainArray;
    }
    function correctValue(n) { return typeof n == "number" ? parseFloat(n) : n == "true" ? !0 : n == "false" ? !1 : n }
    var vis = (function () {
        var stateKey, eventKey, keys = {
            hidden: "visibilitychange",
            webkitHidden: "webkitvisibilitychange",
            mozHidden: "mozvisibilitychange",
            msHidden: "msvisibilitychange"
        };
        for (stateKey in keys) {
            if (stateKey in document) {
                eventKey = keys[stateKey];
                break;
            }
        }
        return function (c) {
            if (c) document.addEventListener(eventKey, c);
            return !document[stateKey];
        }
    })();
    vis(function () {
        if (vis()) {
            tabActive = true;
            clearInterval(flashInterval);
        } else {
            tabActive = false;
        }
    });
    function getAttachmentCode(link, name) {
        var html = "";
        if (link.indexOf(".jpg") > 0 || link.indexOf(".png") > 0 || link.indexOf(".gif") > 0) {
            html += '<a class="sb-link-img" target="_blank" href="' + link + '"><img src="' + link + '" /></a>';
        } else {
            html += '<a target="_blank" href="' + link + '">' + name + '</a>';
        }
        return html;
    }

}(jQuery));


