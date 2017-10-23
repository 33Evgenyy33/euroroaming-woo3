/*
* ======================================
* SUPPORT BOARD - ADMIN JS - PHP AND WP
* ======================================
* 
* Support Board plugin admin side Javascript - Global PHP and WP versions 
* Schiocco - Copyright (c)
*/

'use strict';

/* Autosize 3.0.14 - license: MIT - jacklmoore.com/autosize */
!function (e, t) { if ("function" == typeof define && define.amd) define(["exports", "module"], t); else if ("undefined" != typeof exports && "undefined" != typeof module) t(exports, module); else { var n = { exports: {} }; t(n.exports, n), e.autosize = n.exports } }(this, function (e, t) { "use strict"; function n(e) { function t() { var t = window.getComputedStyle(e, null); c = t.overflowY, "vertical" === t.resize ? e.style.resize = "none" : "both" === t.resize && (e.style.resize = "horizontal"), f = "content-box" === t.boxSizing ? -(parseFloat(t.paddingTop) + parseFloat(t.paddingBottom)) : parseFloat(t.borderTopWidth) + parseFloat(t.borderBottomWidth), isNaN(f) && (f = 0), i() } function n(t) { var n = e.style.width; e.style.width = "0px", e.offsetWidth, e.style.width = n, c = t, u && (e.style.overflowY = t), o() } function o() { var t = window.pageYOffset, n = document.body.scrollTop, o = e.style.height; e.style.height = "auto"; var i = e.scrollHeight + f; return 0 === e.scrollHeight ? void (e.style.height = o) : (e.style.height = i + "px", v = e.clientWidth, document.documentElement.scrollTop = t, void (document.body.scrollTop = n)) } function i() { var t = e.style.height; o(); var i = window.getComputedStyle(e, null); if (i.height !== e.style.height ? "visible" !== c && n("visible") : "hidden" !== c && n("hidden"), t !== e.style.height) { var r = document.createEvent("Event"); r.initEvent("autosize:resized", !0, !1), e.dispatchEvent(r) } } var d = void 0 === arguments[1] ? {} : arguments[1], s = d.setOverflowX, l = void 0 === s ? !0 : s, a = d.setOverflowY, u = void 0 === a ? !0 : a; if (e && e.nodeName && "TEXTAREA" === e.nodeName && !r.has(e)) { var f = null, c = null, v = e.clientWidth, p = function () { e.clientWidth !== v && i() }, h = function (t) { window.removeEventListener("resize", p, !1), e.removeEventListener("input", i, !1), e.removeEventListener("keyup", i, !1), e.removeEventListener("autosize:destroy", h, !1), e.removeEventListener("autosize:update", i, !1), r["delete"](e), Object.keys(t).forEach(function (n) { e.style[n] = t[n] }) }.bind(e, { height: e.style.height, resize: e.style.resize, overflowY: e.style.overflowY, overflowX: e.style.overflowX, wordWrap: e.style.wordWrap }); e.addEventListener("autosize:destroy", h, !1), "onpropertychange" in e && "oninput" in e && e.addEventListener("keyup", i, !1), window.addEventListener("resize", p, !1), e.addEventListener("input", i, !1), e.addEventListener("autosize:update", i, !1), r.add(e), l && (e.style.overflowX = "hidden", e.style.wordWrap = "break-word"), t() } } function o(e) { if (e && e.nodeName && "TEXTAREA" === e.nodeName) { var t = document.createEvent("Event"); t.initEvent("autosize:destroy", !0, !1), e.dispatchEvent(t) } } function i(e) { if (e && e.nodeName && "TEXTAREA" === e.nodeName) { var t = document.createEvent("Event"); t.initEvent("autosize:update", !0, !1), e.dispatchEvent(t) } } var r = "function" == typeof Set ? new Set : function () { var e = []; return { has: function (t) { return Boolean(e.indexOf(t) > -1) }, add: function (t) { e.push(t) }, "delete": function (t) { e.splice(e.indexOf(t), 1) } } }(), d = null; "undefined" == typeof window || "function" != typeof window.getComputedStyle ? (d = function (e) { return e }, d.destroy = function (e) { return e }, d.update = function (e) { return e }) : (d = function (e, t) { return e && Array.prototype.forEach.call(e.length ? e : [e], function (e) { return n(e, t) }), e }, d.destroy = function (e) { return e && Array.prototype.forEach.call(e.length ? e : [e], o), e }, d.update = function (e) { return e && Array.prototype.forEach.call(e.length ? e : [e], i), e }), t.exports = d });

/* tinyColorPicker - v1.0.0 2015-12-15 - license: MIT */
!function (a, b) { "object" == typeof exports ? module.exports = b(a) : "function" == typeof define && define.amd ? define("colors", [], function () { return b(a) }) : a.Colors = b(a) }(this, function (a, b) { "use strict"; function c(a, c, d, f, g) { if ("string" == typeof c) { var c = v.txt2color(c); d = c.type, p[d] = c[d], g = g !== b ? g : c.alpha } else if (c) for (var h in c) a[d][h] = k(c[h] / l[d][h][1], 0, 1); return g !== b && (a.alpha = k(+g, 0, 1)), e(d, f ? a : b) } function d(a, b, c) { var d = o.options.grey, e = {}; return e.RGB = { r: a.r, g: a.g, b: a.b }, e.rgb = { r: b.r, g: b.g, b: b.b }, e.alpha = c, e.equivalentGrey = n(d.r * a.r + d.g * a.g + d.b * a.b), e.rgbaMixBlack = i(b, { r: 0, g: 0, b: 0 }, c, 1), e.rgbaMixWhite = i(b, { r: 1, g: 1, b: 1 }, c, 1), e.rgbaMixBlack.luminance = h(e.rgbaMixBlack, !0), e.rgbaMixWhite.luminance = h(e.rgbaMixWhite, !0), o.options.customBG && (e.rgbaMixCustom = i(b, o.options.customBG, c, 1), e.rgbaMixCustom.luminance = h(e.rgbaMixCustom, !0), o.options.customBG.luminance = h(o.options.customBG, !0)), e } function e(a, b) { var c, e, k, q = b || p, r = v, s = o.options, t = l, u = q.RND, w = "", x = "", y = { hsl: "hsv", rgb: a }, z = u.rgb; if ("alpha" !== a) { for (var A in t) if (!t[A][A]) { a !== A && (x = y[A] || "rgb", q[A] = r[x + "2" + A](q[x])), u[A] || (u[A] = {}), c = q[A]; for (w in c) u[A][w] = n(c[w] * t[A][w][1]) } z = u.rgb, q.HEX = r.RGB2HEX(z), q.equivalentGrey = s.grey.r * q.rgb.r + s.grey.g * q.rgb.g + s.grey.b * q.rgb.b, q.webSave = e = f(z, 51), q.webSmart = k = f(z, 17), q.saveColor = z.r === e.r && z.g === e.g && z.b === e.b ? "web save" : z.r === k.r && z.g === k.g && z.b === k.b ? "web smart" : "", q.hueRGB = v.hue2RGB(q.hsv.h), b && (q.background = d(z, q.rgb, q.alpha)) } var B, C, D, E = q.rgb, F = q.alpha, G = "luminance", H = q.background; return B = i(E, { r: 0, g: 0, b: 0 }, F, 1), B[G] = h(B, !0), q.rgbaMixBlack = B, C = i(E, { r: 1, g: 1, b: 1 }, F, 1), C[G] = h(C, !0), q.rgbaMixWhite = C, s.customBG && (D = i(E, H.rgbaMixCustom, F, 1), D[G] = h(D, !0), D.WCAG2Ratio = j(D[G], H.rgbaMixCustom[G]), q.rgbaMixBGMixCustom = D, D.luminanceDelta = m.abs(D[G] - H.rgbaMixCustom[G]), D.hueDelta = g(H.rgbaMixCustom, D, !0)), q.RGBLuminance = h(z), q.HUELuminance = h(q.hueRGB), s.convertCallback && s.convertCallback(q, a), q } function f(a, b) { var c = {}, d = 0, e = b / 2; for (var f in a) d = a[f] % b, c[f] = a[f] + (d > e ? b - d : -d); return c } function g(a, b, c) { return (m.max(a.r - b.r, b.r - a.r) + m.max(a.g - b.g, b.g - a.g) + m.max(a.b - b.b, b.b - a.b)) * (c ? 255 : 1) / 765 } function h(a, b) { for (var c = b ? 1 : 255, d = [a.r / c, a.g / c, a.b / c], e = o.options.luminance, f = d.length; f--;) d[f] = d[f] <= .03928 ? d[f] / 12.92 : m.pow((d[f] + .055) / 1.055, 2.4); return e.r * d[0] + e.g * d[1] + e.b * d[2] } function i(a, c, d, e) { var f = {}, g = d !== b ? d : 1, h = e !== b ? e : 1, i = g + h * (1 - g); for (var j in a) f[j] = (a[j] * g + c[j] * h * (1 - g)) / i; return f.a = i, f } function j(a, b) { var c = 1; return c = a >= b ? (a + .05) / (b + .05) : (b + .05) / (a + .05), n(100 * c) / 100 } function k(a, b, c) { return a > c ? c : b > a ? b : a } var l = { rgb: { r: [0, 255], g: [0, 255], b: [0, 255] }, hsv: { h: [0, 360], s: [0, 100], v: [0, 100] }, hsl: { h: [0, 360], s: [0, 100], l: [0, 100] }, alpha: { alpha: [0, 1] }, HEX: { HEX: [0, 16777215] } }, m = a.Math, n = m.round, o = {}, p = {}, q = { r: .298954, g: .586434, b: .114612 }, r = { r: .2126, g: .7152, b: .0722 }, s = function (a) { this.colors = { RND: {} }, this.options = { color: "rgba(0,0,0,0)", grey: q, luminance: r, valueRanges: l }, t(this, a || {}) }, t = function (a, d) { var e, f = a.options; u(a); for (var g in d) d[g] !== b && (f[g] = d[g]); e = f.customBG, f.customBG = "string" == typeof e ? v.txt2color(e).rgb : e, p = c(a.colors, f.color, b, !0) }, u = function (a) { o !== a && (o = a, p = a.colors) }; s.prototype.setColor = function (a, d, f) { return u(this), a ? c(this.colors, a, d, b, f) : (f !== b && (this.colors.alpha = k(f, 0, 1)), e(d)) }, s.prototype.setCustomBackground = function (a) { return u(this), this.options.customBG = "string" == typeof a ? v.txt2color(a).rgb : a, c(this.colors, b, "rgb") }, s.prototype.saveAsBackground = function () { return u(this), c(this.colors, b, "rgb", !0) }, s.prototype.toString = function (a, b) { return v.color2text((a || "rgb").toLowerCase(), this.colors, b) }; var v = { txt2color: function (a) { var b = {}, c = a.replace(/(?:#|\)|%)/g, "").split("("), d = (c[1] || "").split(/,\s*/), e = c[1] ? c[0].substr(0, 3) : "rgb", f = ""; if (b.type = e, b[e] = {}, c[1]) for (var g = 3; g--;) f = e[g] || e.charAt(g), b[e][f] = +d[g] / l[e][f][1]; else b.rgb = v.HEX2rgb(c[0]); return b.alpha = d[3] ? +d[3] : 1, b }, color2text: function (a, b, c) { var d = c !== !1 && n(100 * b.alpha) / 100, e = "number" == typeof d && c !== !1 && (c || 1 !== d), f = b.RND.rgb, g = b.RND.hsl, h = "hex" === a && e, i = "hex" === a && !h, j = "rgb" === a || h, k = j ? f.r + ", " + f.g + ", " + f.b : i ? "#" + b.HEX : g.h + ", " + g.s + "%, " + g.l + "%"; return i ? k : (h ? "rgb" : a) + (e ? "a" : "") + "(" + k + (e ? ", " + d : "") + ")" }, RGB2HEX: function (a) { return ((a.r < 16 ? "0" : "") + a.r.toString(16) + (a.g < 16 ? "0" : "") + a.g.toString(16) + (a.b < 16 ? "0" : "") + a.b.toString(16)).toUpperCase() }, HEX2rgb: function (a) { return a = a.split(""), { r: +("0x" + a[0] + a[a[3] ? 1 : 0]) / 255, g: +("0x" + a[a[3] ? 2 : 1] + (a[3] || a[1])) / 255, b: +("0x" + (a[4] || a[2]) + (a[5] || a[2])) / 255 } }, hue2RGB: function (a) { var b = 6 * a, c = ~~b % 6, d = 6 === b ? 0 : b - c; return { r: n(255 * [1, 1 - d, 0, 0, d, 1][c]), g: n(255 * [d, 1, 1, 1 - d, 0, 0][c]), b: n(255 * [0, 0, d, 1, 1, 1 - d][c]) } }, rgb2hsv: function (a) { var b, c, d, e = a.r, f = a.g, g = a.b, h = 0; return g > f && (f = g + (g = f, 0), h = -1), c = g, f > e && (e = f + (f = e, 0), h = -2 / 6 - h, c = m.min(f, g)), b = e - c, d = e ? b / e : 0, { h: 1e-15 > d ? p && p.hsl && p.hsl.h || 0 : b ? m.abs(h + (f - g) / (6 * b)) : 0, s: e ? b / e : p && p.hsv && p.hsv.s || 0, v: e } }, hsv2rgb: function (a) { var b = 6 * a.h, c = a.s, d = a.v, e = ~~b, f = b - e, g = d * (1 - c), h = d * (1 - f * c), i = d * (1 - (1 - f) * c), j = e % 6; return { r: [d, h, g, g, i, d][j], g: [i, d, d, h, g, g][j], b: [g, g, i, d, d, h][j] } }, hsv2hsl: function (a) { var b = (2 - a.s) * a.v, c = a.s * a.v; return c = a.s ? 1 > b ? b ? c / b : 0 : c / (2 - b) : 0, { h: a.h, s: a.v || c ? c : p && p.hsl && p.hsl.s || 0, l: b / 2 } }, rgb2hsl: function (a, b) { var c = v.rgb2hsv(a); return v.hsv2hsl(b ? c : p.hsv = c) }, hsl2rgb: function (a) { var b = 6 * a.h, c = a.s, d = a.l, e = .5 > d ? d * (1 + c) : d + c - c * d, f = d + d - e, g = e ? (e - f) / e : 0, h = ~~b, i = b - h, j = e * g * i, k = f + j, l = e - j, m = h % 6; return { r: [e, l, f, f, k, e][m], g: [k, e, e, l, f, f][m], b: [f, f, k, e, e, l][m] } } }; return s }), function (a, b) { "object" == typeof exports ? module.exports = b(a, require("jquery"), require("colors")) : "function" == typeof define && define.amd ? define(["jquery", "colors"], function (c, d) { return b(a, c, d) }) : b(a, a.jQuery, a.Colors) }(this, function (a, b, c, d) { "use strict"; function e(a) { return a.value || a.getAttribute("value") || b(a).css("background-color") || "#FFF" } function f(a) { return a = a.originalEvent && a.originalEvent.touches ? a.originalEvent.touches[0] : a, a.originalEvent ? a.originalEvent : a } function g(a) { return b(a.find(r.doRender)[0] || a[0]) } function h(c) { var d = b(this), f = d.offset(), h = b(a), k = r.gap; c ? (s = g(d), s._colorMode = s.data("colorMode"), p.$trigger = d, (t || i()).css(r.positionCallback.call(p, d) || { left: (t._left = f.left) - ((t._left += t._width - (h.scrollLeft() + h.width())) + k > 0 ? t._left + k : 0), top: (t._top = f.top + d.outerHeight()) - ((t._top += t._height - (h.scrollTop() + h.height())) + k > 0 ? t._top + k : 0) }).show(r.animationSpeed, function () { c !== !0 && (y.toggle(!!r.opacity)._width = y.width(), v._width = v.width(), v._height = v.height(), u._height = u.height(), q.setColor(e(s[0])), n(!0)) }).off(".tcp").on(D, ".cp-xy-slider,.cp-z-slider,.cp-alpha", j)) : p.$trigger && b(t).hide(r.animationSpeed, function () { n(!1), p.$trigger = null }).off(".tcp") } function i() { return b("head")[r.cssPrepend ? "prepend" : "append"]('<style type="text/css" id="tinyColorPickerStyles">' + (r.css || I) + (r.cssAddon || "") + "</style>"), b(H).css({ margin: r.margin }).appendTo("body").show(0, function () { p.$UI = t = b(this), F = r.GPU && t.css("perspective") !== d, u = b(".cp-z-slider", this), v = b(".cp-xy-slider", this), w = b(".cp-xy-cursor", this), x = b(".cp-z-cursor", this), y = b(".cp-alpha", this), z = b(".cp-alpha-cursor", this), r.buildCallback.call(p, t), t.prepend("<div>").children().eq(0).css("width", t.children().eq(0).width()), t._width = this.offsetWidth, t._height = this.offsetHeight }).hide() } function j(a) { var c = this.className.replace(/cp-(.*?)(?:\s*|$)/, "$1").replace("-", "_"); (a.button || a.which) > 1 || (a.preventDefault && a.preventDefault(), a.returnValue = !1, s._offset = b(this).offset(), (c = "xy_slider" === c ? k : "z_slider" === c ? l : m)(a), n(), A.on(E, function () { A.off(".tcp") }).on(C, function (a) { c(a), n() })) } function k(a) { var b = f(a), c = b.pageX - s._offset.left, d = b.pageY - s._offset.top; q.setColor({ s: c / v._width * 100, v: 100 - d / v._height * 100 }, "hsv") } function l(a) { var b = f(a).pageY - s._offset.top; q.setColor({ h: 360 - b / u._height * 360 }, "hsv") } function m(a) { var b = f(a).pageX - s._offset.left, c = b / y._width; q.setColor({}, "rgb", c) } function n(a) { var b = q.colors, c = b.hueRGB, e = (b.RND.rgb, b.RND.hsl, r.dark), f = r.light, g = q.toString(s._colorMode, r.forceAlpha), h = b.HUELuminance > .22 ? e : f, i = b.rgbaMixBlack.luminance > .22 ? e : f, j = (1 - b.hsv.h) * u._height, k = b.hsv.s * v._width, l = (1 - b.hsv.v) * v._height, m = b.alpha * y._width, n = F ? "translate3d" : "", p = s[0].value, t = s[0].hasAttribute("value") && "" === p && a !== d; v._css = { backgroundColor: "rgb(" + c.r + "," + c.g + "," + c.b + ")" }, w._css = { transform: n + "(" + k + "px, " + l + "px, 0)", left: F ? "" : k, top: F ? "" : l, borderColor: b.RGBLuminance > .22 ? e : f }, x._css = { transform: n + "(0, " + j + "px, 0)", top: F ? "" : j, borderColor: "transparent " + h }, y._css = { backgroundColor: "#" + b.HEX }, z._css = { transform: n + "(" + m + "px, 0, 0)", left: F ? "" : m, borderColor: i + " transparent" }, s._css = { backgroundColor: t ? "" : g, color: t ? "" : b.rgbaMixBGMixCustom.luminance > .22 ? e : f }, s.text = t ? "" : p !== g ? g : "", a !== d ? o(a) : G(o) } function o(a) { v.css(v._css), w.css(w._css), x.css(x._css), y.css(y._css), z.css(z._css), r.doRender && s.css(s._css), s.text && s.val(s.text), r.renderCallback.call(p, s, "boolean" == typeof a ? a : d) } var p, q, r, s, t, u, v, w, x, y, z, A = b(document), B = b(), C = "touchmove.tcp mousemove.tcp pointermove.tcp", D = "touchstart.tcp mousedown.tcp pointerdown.tcp", E = "touchend.tcp mouseup.tcp pointerup.tcp", F = !1, G = a.requestAnimationFrame || a.webkitRequestAnimationFrame || function (a) { a() }, H = '<div class="cp-color-picker"><div class="cp-z-slider"><div class="cp-z-cursor"></div></div><div class="cp-xy-slider"><div class="cp-white"></div><div class="cp-xy-cursor"></div></div><div class="cp-alpha"><div class="cp-alpha-cursor"></div></div></div>', I = ".cp-color-picker{position:absolute;overflow:hidden;padding:6px 6px 0;background-color:#444;color:#bbb;font-family:Arial,Helvetica,sans-serif;font-size:12px;font-weight:400;cursor:default;border-radius:5px}.cp-color-picker>div{position:relative;overflow:hidden}.cp-xy-slider{float:left;height:128px;width:128px;margin-bottom:6px;background:linear-gradient(to right,#FFF,rgba(255,255,255,0))}.cp-white{height:100%;width:100%;background:linear-gradient(rgba(0,0,0,0),#000)}.cp-xy-cursor{position:absolute;top:0;width:10px;height:10px;margin:-5px;border:1px solid #fff;border-radius:100%;box-sizing:border-box}.cp-z-slider{float:right;margin-left:6px;height:128px;width:20px;background:linear-gradient(red 0,#f0f 17%,#00f 33%,#0ff 50%,#0f0 67%,#ff0 83%,red 100%)}.cp-z-cursor{position:absolute;margin-top:-4px;width:100%;border:4px solid #fff;border-color:transparent #fff;box-sizing:border-box}.cp-alpha{clear:both;width:100%;height:16px;margin:6px 0;background:linear-gradient(to right,#444,rgba(0,0,0,0))}.cp-alpha-cursor{position:absolute;margin-left:-4px;height:100%;border:4px solid #fff;border-color:#fff transparent;box-sizing:border-box}", J = function (a) { q = this.color = new c(a), r = q.options, p = this }; J.prototype = { render: n, toggle: h }, b.fn.colorPicker = function (c) { var d = this, f = function () { }; return c = b.extend({ animationSpeed: 150, GPU: !0, doRender: !0, customBG: "#FFF", opacity: !0, renderCallback: f, buildCallback: f, positionCallback: f, body: document.body, scrollResize: !0, gap: 4, dark: "#222", light: "#DDD" }, c), !p && c.scrollResize && b(a).on("resize.tcp scroll.tcp", function () { p.$trigger && p.toggle.call(p.$trigger[0], !0) }), B = B.add(this), this.colorPicker = p || new J(c), this.options = c, b(c.body).off(".tcp").on(D, function (a) { -1 === B.add(t).add(b(t).find(a.target)).index(a.target) && h() }), this.on("focusin.tcp click.tcp", function (a) { p.color.options = b.extend(p.color.options, r = d.options), h.call(this, a) }).on("change.tcp", function () { q.setColor(this.value || "#FFF"), d.colorPicker.render(!0) }).each(function () { var a = e(this), d = a.split("("), f = g(b(this)); f.data("colorMode", d[1] ? d[0].substr(0, 3) : "HEX").attr("readonly", r.preventFocus), c.doRender && f.css({ "background-color": a, color: function () { return q.setColor(a).rgbaMixBGMixCustom.luminance > .22 ? c.dark : c.light } }) }) }, b.fn.colorPicker.destroy = function () { b("*").off(".tcp"), p.toggle(!1), B = b() } });

/* Push v1.0-beta
*  The MIT License (MIT)
*/
!function (e) { if ("object" == typeof exports && "undefined" != typeof module) module.exports = e(); else if ("function" == typeof define && define.amd) define([], e); else { ("undefined" != typeof window ? window : "undefined" != typeof global ? global : "undefined" != typeof self ? self : this).Push = e() } }(function () { return function e(t, n, i) { function o(s, a) { if (!n[s]) { if (!t[s]) { var u = "function" == typeof require && require; if (!a && u) return u(s, !0); if (r) return r(s, !0); var c = new Error("Cannot find module '" + s + "'"); throw c.code = "MODULE_NOT_FOUND", c } var f = n[s] = { exports: {} }; t[s][0].call(f.exports, function (e) { var n = t[s][1][e]; return o(n || e) }, f, f.exports, e, t, n, i) } return n[s].exports } for (var r = "function" == typeof require && require, s = 0; s < i.length; s++) o(i[s]); return o }({ 1: [function (e, t, n) { "use strict"; Object.defineProperty(n, "__esModule", { value: !0 }); n.default = { errors: { incompatible: "PushError: Push.js is incompatible with browser.", invalid_plugin: "PushError: plugin class missing from plugin manifest (invalid plugin). Please check the documentation.", invalid_title: "PushError: title of notification must be a string", permission_denied: "PushError: permission request declined", sw_notification_error: "PushError: could not show a ServiceWorker notification due to the following reason: ", sw_registration_error: "PushError: could not register the ServiceWorker due to the following reason: ", unknown_interface: "PushError: unable to create notification: unknown interface" } } }, {}], 2: [function (e, t, n) { "use strict"; function i(e, t) { if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function") } Object.defineProperty(n, "__esModule", { value: !0 }); var o = function () { function e(e, t) { for (var n = 0; n < t.length; n++) { var i = t[n]; i.enumerable = i.enumerable || !1, i.configurable = !0, "value" in i && (i.writable = !0), Object.defineProperty(e, i.key, i) } } return function (t, n, i) { return n && e(t.prototype, n), i && e(t, i), t } }(), r = function () { function e(t) { i(this, e), this._win = t, this.DEFAULT = "default", this.GRANTED = "granted", this.DENIED = "denied", this._permissions = [this.GRANTED, this.DEFAULT, this.DENIED] } return o(e, [{ key: "request", value: function (e, t) { var n = this, i = this.get(), o = function (i) { i === n.GRANTED || 0 === i ? e && e() : t && t() }; i !== this.DEFAULT ? o(i) : this._win.Notification && this._win.Notification.requestPermission ? this._win.Notification.requestPermission().then(o).catch(function () { t && t() }) : this._win.webkitNotifications && this._win.webkitNotifications.checkPermission ? this._win.webkitNotifications.requestPermission(o) : e && e() } }, { key: "has", value: function () { return this.get() === this.GRANTED } }, { key: "get", value: function () { return this._win.Notification && this._win.Notification.permission ? this._win.Notification.permission : this._win.webkitNotifications && this._win.webkitNotifications.checkPermission ? this._permissions[this._win.webkitNotifications.checkPermission()] : navigator.mozNotification ? this.GRANTED : this._win.external && this._win.external.msIsSiteMode ? this._win.external.msIsSiteMode() ? this.GRANTED : this.DEFAULT : this.GRANTED } }]), e }(); n.default = r }, {}], 3: [function (e, t, n) { "use strict"; function i(e) { return e && e.__esModule ? e : { default: e } } function o(e, t) { if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function") } Object.defineProperty(n, "__esModule", { value: !0 }); var r = function () { function e(e, t) { for (var n = 0; n < t.length; n++) { var i = t[n]; i.enumerable = i.enumerable || !1, i.configurable = !0, "value" in i && (i.writable = !0), Object.defineProperty(e, i.key, i) } } return function (t, n, i) { return n && e(t.prototype, n), i && e(t, i), t } }(), s = i(e("./Messages")), a = i(e("./Permission")), u = i(e("./Util")), c = i(e("./agents/DesktopAgent")), f = i(e("./agents/MobileChromeAgent")), l = i(e("./agents/MobileFirefoxAgent")), h = i(e("./agents/MSAgent")), d = i(e("./agents/WebKitAgent")), p = function () { function e(t) { o(this, e), this._currentId = 0, this._notifications = {}, this._win = t, this.Permission = new a.default(t), this._agents = { desktop: new c.default(t), chrome: new f.default(t), firefox: new l.default(t), ms: new h.default(t), webkit: new d.default(t) }, this._configuration = { serviceWorker: "/serviceWorker.min.js", fallback: function (e) { } } } return r(e, [{ key: "_closeNotification", value: function (e) { var t = !0, n = this._notifications[e]; if (void 0 !== n) { if (t = this._removeNotification(e), this._agents.desktop.isSupported()) this._agents.desktop.close(n); else if (this._agents.webkit.isSupported()) this._agents.webkit.close(n); else { if (!this._agents.ms.isSupported()) throw t = !1, new Error(s.default.errors.unknown_interface); this._agents.ms.close() } return t } return !1 } }, { key: "_addNotification", value: function (e) { var t = this._currentId; return this._notifications[t] = e, this._currentId++, t } }, { key: "_removeNotification", value: function (e) { var t = !1; return this._notifications.hasOwnProperty(e) && (delete this._notifications[e], t = !0), t } }, { key: "_prepareNotification", value: function (e, t) { var n = this, i = void 0; return i = { get: function () { return n._notifications[e] }, close: function () { n._closeNotification(e) } }, t.timeout && setTimeout(function () { i.close() }, t.timeout), i } }, { key: "_serviceWorkerCallback", value: function (e, t, n) { var i = this, o = this._addNotification(e[e.length - 1]); navigator.serviceWorker.addEventListener("message", function (e) { var t = JSON.parse(e.data); "close" === t.action && Number.isInteger(t.id) && i._removeNotification(t.id) }), n(this._prepareNotification(o, t)) } }, { key: "_createCallback", value: function (e, t, n) { var i = this, o = void 0, r = null; if (t = t || {}, o = function (e) { i._removeNotification(e), u.default.isFunction(t.onClose) && t.onClose.call(i, r) }, this._agents.desktop.isSupported()) try { r = this._agents.desktop.create(e, t) } catch (o) { var s = this._currentId, a = this.config().serviceWorker, c = function (e) { return i._serviceWorkerCallback(e, t, n) }; this._agents.chrome.isSupported() && this._agents.chrome.create(s, e, t, a, c) } else this._agents.webkit.isSupported() ? r = this._agents.webkit.create(e, t) : this._agents.firefox.isSupported() ? this._agents.firefox.create(e, t) : this._agents.ms.isSupported() ? r = this._agents.ms.create(e, t) : (t.title = e, this.config().fallback(t)); if (null !== r) { var f = this._addNotification(r), l = this._prepareNotification(f, t); u.default.isFunction(t.onShow) && r.addEventListener("show", t.onShow), u.default.isFunction(t.onError) && r.addEventListener("error", t.onError), u.default.isFunction(t.onClick) && r.addEventListener("click", t.onClick), r.addEventListener("close", function () { o(f) }), r.addEventListener("cancel", function () { o(f) }), n(l) } n(null) } }, { key: "create", value: function (e, t) { var n = this, i = void 0; if (!u.default.isString(e)) throw new Error(s.default.errors.invalid_title); return i = this.Permission.has() ? function (i, o) { try { n._createCallback(e, t, i) } catch (e) { o(e) } } : function (i, o) { n.Permission.request(function () { try { n._createCallback(e, t, i) } catch (e) { o(e) } }, function () { o(s.default.errors.permission_denied) }) }, new Promise(i) } }, { key: "count", value: function () { var e = void 0, t = 0; for (e in this._notifications) this._notifications.hasOwnProperty(e) && t++; return t } }, { key: "close", value: function (e) { var t = void 0; for (t in this._notifications) if (this._notifications.hasOwnProperty(t) && this._notifications[t].tag === e) return this._closeNotification(t) } }, { key: "clear", value: function () { var e = void 0, t = !0; for (e in this._notifications) this._notifications.hasOwnProperty(e) && (t = t && this._closeNotification(e)); return t } }, { key: "supported", value: function () { var e = !1; for (var t in this._agents) this._agents.hasOwnProperty(t) && (e = e || this._agents[t].isSupported()); return e } }, { key: "config", value: function (e) { return (void 0 !== e || null !== e && u.default.isObject(e)) && u.default.objectMerge(this._configuration, e), this._configuration } }, { key: "extend", value: function (e) { var t, n = {}.hasOwnProperty; if (!n.call(e, "plugin")) throw new Error(s.default.errors.invalid_plugin); n.call(e, "config") && u.default.isObject(e.config) && null !== e.config && this.config(e.config), t = new (0, e.plugin)(this.config()); for (var i in t) n.call(t, i) && u.default.isFunction(t[i]) && (this[i] = t[i]) } }]), e }(); n.default = p }, { "./Messages": 1, "./Permission": 2, "./Util": 4, "./agents/DesktopAgent": 6, "./agents/MSAgent": 7, "./agents/MobileChromeAgent": 8, "./agents/MobileFirefoxAgent": 9, "./agents/WebKitAgent": 10 }], 4: [function (e, t, n) { "use strict"; function i(e, t) { if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function") } Object.defineProperty(n, "__esModule", { value: !0 }); var o = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (e) { return typeof e } : function (e) { return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : typeof e }, r = function () { function e(e, t) { for (var n = 0; n < t.length; n++) { var i = t[n]; i.enumerable = i.enumerable || !1, i.configurable = !0, "value" in i && (i.writable = !0), Object.defineProperty(e, i.key, i) } } return function (t, n, i) { return n && e(t.prototype, n), i && e(t, i), t } }(), s = function () { function e() { i(this, e) } return r(e, null, [{ key: "isUndefined", value: function (e) { return void 0 === e } }, { key: "isString", value: function (e) { return "string" == typeof e } }, { key: "isFunction", value: function (e) { return e && "[object Function]" === {}.toString.call(e) } }, { key: "isObject", value: function (e) { return "object" == (void 0 === e ? "undefined" : o(e)) } }, { key: "objectMerge", value: function (e, t) { for (var n in t) e.hasOwnProperty(n) && this.isObject(e[n]) && this.isObject(t[n]) ? this.objectMerge(e[n], t[n]) : e[n] = t[n] } }]), e }(); n.default = s }, {}], 5: [function (e, t, n) { "use strict"; function i(e, t) { if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function") } Object.defineProperty(n, "__esModule", { value: !0 }); n.default = function e(t) { i(this, e), this._win = t } }, {}], 6: [function (e, t, n) { "use strict"; function i(e) { return e && e.__esModule ? e : { default: e } } function o(e, t) { if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function") } function r(e, t) { if (!e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return !t || "object" != typeof t && "function" != typeof t ? e : t } function s(e, t) { if ("function" != typeof t && null !== t) throw new TypeError("Super expression must either be null or a function, not " + typeof t); e.prototype = Object.create(t && t.prototype, { constructor: { value: e, enumerable: !1, writable: !0, configurable: !0 } }), t && (Object.setPrototypeOf ? Object.setPrototypeOf(e, t) : e.__proto__ = t) } Object.defineProperty(n, "__esModule", { value: !0 }); var a = function () { function e(e, t) { for (var n = 0; n < t.length; n++) { var i = t[n]; i.enumerable = i.enumerable || !1, i.configurable = !0, "value" in i && (i.writable = !0), Object.defineProperty(e, i.key, i) } } return function (t, n, i) { return n && e(t.prototype, n), i && e(t, i), t } }(), u = i(e("./AbstractAgent")), c = i(e("../Util")), f = function (e) { function t() { return o(this, t), r(this, (t.__proto__ || Object.getPrototypeOf(t)).apply(this, arguments)) } return s(t, u.default), a(t, [{ key: "isSupported", value: function () { return void 0 !== this._win.Notification } }, { key: "create", value: function (e, t) { return new this._win.Notification(e, { icon: c.default.isString(t.icon) || c.default.isUndefined(t.icon) ? t.icon : t.icon.x32, body: t.body, tag: t.tag, requireInteraction: t.requireInteraction }) } }, { key: "close", value: function (e) { e.close() } }]), t }(); n.default = f }, { "../Util": 4, "./AbstractAgent": 5 }], 7: [function (e, t, n) { "use strict"; function i(e) { return e && e.__esModule ? e : { default: e } } function o(e, t) { if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function") } function r(e, t) { if (!e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return !t || "object" != typeof t && "function" != typeof t ? e : t } function s(e, t) { if ("function" != typeof t && null !== t) throw new TypeError("Super expression must either be null or a function, not " + typeof t); e.prototype = Object.create(t && t.prototype, { constructor: { value: e, enumerable: !1, writable: !0, configurable: !0 } }), t && (Object.setPrototypeOf ? Object.setPrototypeOf(e, t) : e.__proto__ = t) } Object.defineProperty(n, "__esModule", { value: !0 }); var a = function () { function e(e, t) { for (var n = 0; n < t.length; n++) { var i = t[n]; i.enumerable = i.enumerable || !1, i.configurable = !0, "value" in i && (i.writable = !0), Object.defineProperty(e, i.key, i) } } return function (t, n, i) { return n && e(t.prototype, n), i && e(t, i), t } }(), u = i(e("./AbstractAgent")), c = i(e("../Util")), f = function (e) { function t() { return o(this, t), r(this, (t.__proto__ || Object.getPrototypeOf(t)).apply(this, arguments)) } return s(t, u.default), a(t, [{ key: "isSupported", value: function () { return void 0 !== this._win.external && void 0 !== this._win.external.msIsSiteMode } }, { key: "create", value: function (e, t) { return this._win.external.msSiteModeClearIconOverlay(), this._win.external.msSiteModeSetIconOverlay(c.default.isString(t.icon) || c.default.isUndefined(t.icon) ? t.icon : t.icon.x16, e), this._win.external.msSiteModeActivate(), null } }, { key: "close", value: function () { this._win.external.msSiteModeClearIconOverlay() } }]), t }(); n.default = f }, { "../Util": 4, "./AbstractAgent": 5 }], 8: [function (e, t, n) { "use strict"; function i(e) { return e && e.__esModule ? e : { default: e } } function o(e, t) { if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function") } function r(e, t) { if (!e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return !t || "object" != typeof t && "function" != typeof t ? e : t } function s(e, t) { if ("function" != typeof t && null !== t) throw new TypeError("Super expression must either be null or a function, not " + typeof t); e.prototype = Object.create(t && t.prototype, { constructor: { value: e, enumerable: !1, writable: !0, configurable: !0 } }), t && (Object.setPrototypeOf ? Object.setPrototypeOf(e, t) : e.__proto__ = t) } Object.defineProperty(n, "__esModule", { value: !0 }); var a = function () { function e(e, t) { for (var n = 0; n < t.length; n++) { var i = t[n]; i.enumerable = i.enumerable || !1, i.configurable = !0, "value" in i && (i.writable = !0), Object.defineProperty(e, i.key, i) } } return function (t, n, i) { return n && e(t.prototype, n), i && e(t, i), t } }(), u = i(e("./AbstractAgent")), c = i(e("../Util")), f = i(e("../Messages")), l = function (e) { function t() { return o(this, t), r(this, (t.__proto__ || Object.getPrototypeOf(t)).apply(this, arguments)) } return s(t, u.default), a(t, [{ key: "isSupported", value: function () { return void 0 !== this._win.navigator && void 0 !== this._win.navigator.serviceWorker } }, { key: "getFunctionBody", value: function (e) { return e.toString().match(/function[^{]+{([\s\S]*)}$/)[1] } }, { key: "create", value: function (e, t, n, i, o) { var r = this; this._win.navigator.serviceWorker.register(i), this._win.navigator.serviceWorker.ready.then(function (i) { var s = { id: e, link: n.link, origin: document.location.href, onClick: c.default.isFunction(n.onClick) ? r.getFunctionBody(n.onClick) : "", onClose: c.default.isFunction(n.onClose) ? r.getFunctionBody(n.onClose) : "" }; void 0 !== n.data && null !== n.data && (s = Object.assign(s, n.data)), i.showNotification(t, { icon: n.icon, body: n.body, vibrate: n.vibrate, tag: n.tag, data: s, requireInteraction: n.requireInteraction, silent: n.silent }).then(function () { i.getNotifications().then(function (e) { i.active.postMessage(""), o(e) }) }).catch(function (e) { throw new Error(f.default.errors.sw_notification_error + e.message) }) }).catch(function (e) { throw new Error(f.default.errors.sw_registration_error + e.message) }) } }, { key: "close", value: function () { } }]), t }(); n.default = l }, { "../Messages": 1, "../Util": 4, "./AbstractAgent": 5 }], 9: [function (e, t, n) { "use strict"; function i(e, t) { if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function") } function o(e, t) { if (!e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return !t || "object" != typeof t && "function" != typeof t ? e : t } function r(e, t) { if ("function" != typeof t && null !== t) throw new TypeError("Super expression must either be null or a function, not " + typeof t); e.prototype = Object.create(t && t.prototype, { constructor: { value: e, enumerable: !1, writable: !0, configurable: !0 } }), t && (Object.setPrototypeOf ? Object.setPrototypeOf(e, t) : e.__proto__ = t) } Object.defineProperty(n, "__esModule", { value: !0 }); var s = function () { function e(e, t) { for (var n = 0; n < t.length; n++) { var i = t[n]; i.enumerable = i.enumerable || !1, i.configurable = !0, "value" in i && (i.writable = !0), Object.defineProperty(e, i.key, i) } } return function (t, n, i) { return n && e(t.prototype, n), i && e(t, i), t } }(), a = function (e) { return e && e.__esModule ? e : { default: e } }(e("./AbstractAgent")), u = function (e) { function t() { return i(this, t), o(this, (t.__proto__ || Object.getPrototypeOf(t)).apply(this, arguments)) } return r(t, a.default), s(t, [{ key: "isSupported", value: function () { return void 0 !== this._win.navigator.mozNotification } }, { key: "create", value: function (e, t) { var n = this._win.navigator.mozNotification.createNotification(e, t.body, t.icon); return n.show(), n } }]), t }(); n.default = u }, { "./AbstractAgent": 5 }], 10: [function (e, t, n) { "use strict"; function i(e, t) { if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function") } function o(e, t) { if (!e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return !t || "object" != typeof t && "function" != typeof t ? e : t } function r(e, t) { if ("function" != typeof t && null !== t) throw new TypeError("Super expression must either be null or a function, not " + typeof t); e.prototype = Object.create(t && t.prototype, { constructor: { value: e, enumerable: !1, writable: !0, configurable: !0 } }), t && (Object.setPrototypeOf ? Object.setPrototypeOf(e, t) : e.__proto__ = t) } Object.defineProperty(n, "__esModule", { value: !0 }); var s = function () { function e(e, t) { for (var n = 0; n < t.length; n++) { var i = t[n]; i.enumerable = i.enumerable || !1, i.configurable = !0, "value" in i && (i.writable = !0), Object.defineProperty(e, i.key, i) } } return function (t, n, i) { return n && e(t.prototype, n), i && e(t, i), t } }(), a = function (e) { return e && e.__esModule ? e : { default: e } }(e("./AbstractAgent")), u = function (e) { function t() { return i(this, t), o(this, (t.__proto__ || Object.getPrototypeOf(t)).apply(this, arguments)) } return r(t, a.default), s(t, [{ key: "isSupported", value: function () { return void 0 !== this._win.webkitNotifications } }, { key: "create", value: function (e, t) { var n = this._win.webkitNotifications.createNotification(t.icon, e, t.body); return n.show(), n } }, { key: "close", value: function (e) { e.cancel() } }]), t }(); n.default = u }, { "./AbstractAgent": 5 }], 11: [function (e, t, n) { "use strict"; var i = function (e) { return e && e.__esModule ? e : { default: e } }(e("./classes/Push")); t.exports = new i.default("undefined" != typeof window ? window : void 0) }, { "./classes/Push": 3 }] }, {}, [11])(11) });

(function ($) {
    $(document).ready(function () {

        //VARIABLES
        var html = "";
        var real_time_interval;
        var msgCount = 999999;
        var last_user_id = "";
        var isPush = false;
        var isIntoConversation = false;
        var sb_settings_arr = $("#save_array_json").val();
        var agents_arr = [];
        var agents_arr_ids = [];
        var is_slack;
        var all_tickets;
        var active_ticket_tab = ".sb-all-users-guests";
        var json_error = 'The conversation can not be processed JSON parser error. The common cause is due to a special char into the conversation. Delete all the tickets for fix the problem, all your tickets will be deleted. <br><br><a class="sb-btn-delete-tickets button action">Delete all tickets</a><br><br><b>CONVERSATION RAW TEXT</b><br><br>';
        var is_php = false;

        if ($("#sb-admin").hasClass("sb-php")) {
            is_php = true;
        }
        if (is_php) {
            sb_ajax_url = "../php/core.php";
        }
        if (!isEmpty(sb_settings_arr)) {
            try {
                sb_settings_arr = JSON.parse(sb_settings_arr);
            } catch (e) {
                sb_settings_arr = [];
                console.log(e.message);
            }
        } else {
            sb_settings_arr = [];
        }

        //VARIOUS
        autosize($(".sb-editor textarea"));
        indipendentSaveSystem(".settings-cnt", "populate");
        $('.sb-color-picker').colorPicker();
        if (sb_settings_arr["push-notifications"] == "all" || sb_settings_arr["push-notifications"] == "agents") isPush = true;

        //TABS
        $("body").on("click", ".nav-plugin li", function () {
            var t = $(this).closest(".tab-plugin");
            $(t).find(".panel-plugin,.nav-plugin li").removeClass("active");
            $(t).find(".panel-plugin").eq($(this).index()).addClass("active");
            $(this).addClass("active");
        });

        //USERS
        var user_arr = [];
        if (!isEmpty(sb_users_arr)) {
            try {
                user_arr = JSON.parse(sb_users_arr);
            } catch (e) {
                user_arr = [];
                console.log(e.message);
            }
        }
        html = "";
        for (var i = 0; i < user_arr.length; i++) {
            var email = user_arr[i]["email"];
            if (isEmpty(email)) email = "--";
            html += '<tr><td><input class="sb-user-id" disabled type="text" value="' + user_arr[i]["id"] + '" /></td>' +
                    '<td><img class="sb-user-img" src="' + user_arr[i]["img"] + '" alt="" /><input class="sb-user-username" readonly type="text" value="' + user_arr[i]["username"] + '" /></td>' +
                    '<td><input class="sb-user-email" readonly type="text" value="' + email + '" /></td><td><input class="sb-user-psw" readonly type="password" value="' + user_arr[i]["psw"] + '" /></td>';
            if (sb_settings_arr["user-extra-1"] != "") html += '<td><input class="sb-user-extra-1" readonly type="text" value="' + (isEmpty(user_arr[i]["extra1"]) ? "--" : user_arr[i]["extra1"]) + '" /></td>';
            if (sb_settings_arr["user-extra-2"] != "") html += '<td><input class="sb-user-extra-2" readonly type="text" value="' + (isEmpty(user_arr[i]["extra2"]) ? "--" : user_arr[i]["extra2"]) + '" /></td>';
            if (sb_settings_arr["user-extra-3"] != "") html += '<td><input class="sb-user-extra-3" readonly type="text" value="' + (isEmpty(user_arr[i]["extra3"]) ? "--" : user_arr[i]["extra3"]) + '" /></td>';
            if (sb_settings_arr["user-extra-4"] != "") html += '<td><input class="sb-user-extra-4" readonly type="text" value="' + (isEmpty(user_arr[i]["extra4"]) ? "--" : user_arr[i]["extra4"]) + '" /></td>';
            html += '<td><i class="sb-remove">X</i></td></tr>';
        }
        $(".table-users tbody").html(html);
        $("body").on("click", "#sb-btn-save", function () {
            if (!$(this).hasClass("disabled")) {
                var arr = [];
                var error = false;
                $(".table-users tbody tr").each(function (index) {
                    var _username = $(this).find(".sb-user-username").val();
                    var _psw = $(this).find(".sb-user-psw").val();
                    arr.push({
                        id: $(this).find(".sb-user-id").val(),
                        img: $(this).find(".sb-user-img").attr("src"),
                        username: _username,
                        psw: _psw,
                        email: $(this).find(".sb-user-email").val(),
                        extra1: $(this).find("sb-user-extra-1").val(),
                        extra2: $(this).find("sb-user-extra-2").val(),
                        extra3: $(this).find("sb-user-extra-3").val(),
                        extra4: $(this).find("sb-user-extra-4").val()
                    });
                    if (isEmpty(_username) || isEmpty(_psw)) error = true;
                });
                if (error) {
                    $(".sb-msg-error").show();
                    setTimeout(function () {
                        $(".sb-msg-error").hide();
                    }, 4000);
                } else {
                    var json = JSON.stringify(arr);
                    jQuery.ajax({
                        method: "POST",
                        url: sb_ajax_url,
                        data: {
                            action: 'sb_ajax_save_option',
                            option_name: 'sb-users-arr',
                            content: json,
                        },
                        async: false
                    }).done(function (response) {
                        $(".sb-msg-success").show();
                        setTimeout(function () {
                            $(".sb-msg-success").hide();
                        }, 2000);
                    });
                }
            }
        });

        //AGENTS
        if (!isEmpty(sb_agents_arr)) agents_arr = JSON.parse(sb_agents_arr);
        var html_wp_users = '<select class="sb-wp-users">';
        is_slack = ((sb_settings_arr["slack-token"] != "") ? true : false);
        var slack_complete = false;
        for (var i = 0; i < sb_wp_users_arr.length; i++) {
            html_wp_users += '<option value="' + sb_wp_users_arr[i][0] + '">' + sb_wp_users_arr[i][1] + '</option>';
        }
        html_wp_users += '</select>';
        html = "";
        for (var i = 0; i < agents_arr.length; i++) {
            html += '<tr id="' + agents_arr[i]["id"] + '"><td><input class="sb-user-id" disabled type="text" value="' + agents_arr[i]["id"] + '" /></td>' +
                    '<td><img class="sb-user-img" src="' + agents_arr[i]["img"] + '" alt="" /><input class="sb-user-username" readonly type="text" value="' + agents_arr[i]["username"] + '" /></td>' +
                    '<td><input class="sb-user-email" readonly type="text" value="' + agents_arr[i]["email"] + '" /></td>' +
                    ((is_php) ? '<td><input class="sb-agent-psw" readonly type="password" value="' + agents_arr[i]["psw"] + '" /></td>' : '<td>' + html_wp_users.replace('value="' + agents_arr[i]["wp_user_id"] + '"', 'selected value="' + agents_arr[i]["wp_user_id"] + '"') + '</td>') +
                    ((is_slack) ? '<td class="slack-cell">' : '</td>') + '<td><i class="sb-remove">X</i></td></tr>';
            agents_arr_ids.push(agents_arr[i]["id"]);
        }
        $(".table-agents tbody").html(html);
        $("body").on("click", "#tab-agents", function () {
            if (is_slack && !slack_complete) {
                jQuery.ajax({
                    method: "POST",
                    url: sb_ajax_url,
                    data: {
                        action: 'sb_ajax_slack_get_users',
                    }
                }).done(function (response) {
                    var arr = JSON.parse(response);
                    if (!isEmpty(arr) || (response.length > 15 && !isEmpty(arr["members"]))) {
                        arr = arr["members"];
                        var html = '<select class="sb-slack-users">';
                        for (var i = 0; i < arr.length; i++) {
                            html += '<option value="' + arr[i]["id"] + '">' + arr[i]["real_name"] + '</option>';
                        }
                        html += '</select>';
                        $('.table-agents .slack-cell').html(html);
                        for (var i = 0; i < agents_arr.length; i++) {
                            if (!isEmpty(agents_arr[i]["slack_user_id"])) {
                                $('.table-agents #' + agents_arr[i]["id"] + ' .sb-slack-users').val(agents_arr[i]["slack_user_id"]);
                            }
                        }
                    }
                    slack_complete = true;
                });
            }
        });
        $("body").on("click", "#sb-btn-save-agent", function () {
            if (!$(this).hasClass("disabled")) {
                var arr = [];
                var error = false;
                $(".table-agents tbody tr").each(function (index) {
                    var _username = $(this).find(".sb-user-username").val();
                    var _email = $(this).find(".sb-user-email").val();
                    var _slack = $(this).find(".sb-slack-users").val();
                    if (isEmpty(_slack)) _slack = "";
                    var item = {
                        id: $(this).find(".sb-user-id").val(),
                        img: $(this).find(".sb-user-img").attr("src"),
                        username: _username,
                        email: _email,
                        slack_user_id: _slack,
                        last_email: "-1"
                    };
                    if (is_php) {
                        item["psw"] = $(this).find(".sb-agent-psw").val();
                    } else {
                        item["wp_user_id"] = $(this).find(".sb-wp-users").val();
                    }
                    arr.push(item);
                    if (isEmpty(_username)) error = true;
                });
                if (error) {
                    $(".sb-msg-error-agent").show();
                    setTimeout(function () {
                        $(".sb-msg-error-agent").hide();
                    }, 4000);
                } else {
                    var json = JSON.stringify(arr);
                    jQuery.ajax({
                        method: "POST",
                        url: sb_ajax_url,
                        data: {
                            action: 'sb_ajax_save_option',
                            option_name: 'sb-agents-arr',
                            content: json,
                        }
                    }).done(function (response) {
                        $(".sb-msg-success-agent").show();
                        setTimeout(function () {
                            $(".sb-msg-success-agent").hide();
                        }, 2000);
                    });
                }
            }
        });

        //USERS AND AGENTS
        $("body").on("click", "#sb-btn-add-new-user,#sb-btn-add-new-agent", function () {
            var user = true;
            var img_thumb = "/media/user-2.jpg";
            if ($(this).attr("id") == "sb-btn-add-new-agent") {
                img_thumb = "/media/user-1.jpg";
                user = false;
            }
            var html = '<tr><td><input class="sb-user-id" disabled type="text" value="' + getRandomInt(9999999, 99999999) + '" /></td>' +
                       '<td><img class="sb-user-img" src="' + sb_plugin_url + img_thumb + '" alt="" /><input class="sb-user-username" type="text" value="" /></td>' +
                       '<td><input class="sb-user-email" type="text" value="" /></td>';
            if (user) {
                html += '<td><input class="sb-user-psw" type="password" value="" /></td>';
                if (sb_settings_arr["user-extra-1"] != "") html += '<td><input class="sb-user-extra-1" type="text" value="" /></td>';
                if (sb_settings_arr["user-extra-2"] != "") html += '<td><input class="sb-user-extra-2" type="text" value="" /></td>';
                if (sb_settings_arr["user-extra-3"] != "") html += '<td><input class="sb-user-extra-3" type="text" value="" /></td>';
                if (sb_settings_arr["user-extra-4"] != "") html += '<td><input class="sb-user-extra-4" type="text" value="" /></td>';
            } else {
                if (is_php) {
                    html += '<td><input class="sb-agent-psw" type="password" value="" /></td>';
                } else {
                    html += '<td>' + html_wp_users + '</td>';
                }
                if ($(".table-agents > thead .slack-td").length) html += '<td></td>';
            }
            html += '<td><i class="sb-remove">X</i></td></tr>';
            if (user) {
                $(".table-users tbody").append(html);
            } else {
                $(".table-agents tbody").append(html);
            }
        });
        $(".sb-table tbody tr td input").dblclick(function () {
            $(this).attr("readonly", false);
        });
        $("body").on("focusout", ".sb-table tbody tr td input", function () {
            var val = $(this).val();
            if (val != "") {
                $(this).attr("val", $(this).val());
                $(this).attr("readonly", true);
            }
        });
        $("body").on("click", ".sb-remove", function () {
            $(this).closest("tr").remove();
        });
        $("body").on("click", ".sb-user-img,.sb-agent-img,.sb-upload-img", function () {
            var t = this;
            if (is_php) {
                $(".sb-upload-php").removeAttr("multiple").attr("data-user-id", $(this).closest("tr").find(".sb-user-id").val()).click();
            } else {
                $(this).open_upload_box(function (target, attachment) {
                    $(t).attr("src", attachment.first().toJSON().url);
                });
            }
        });
        $("body").on("click", ".sb-upload-img-remove", function () {
            $(this).closest(".item-input-img").find("img").attr("src", $(this).attr("data-src"));
        });

        //TICKETS
        var sb_ajax_get_tickets_len = 0;
        var isFalsePush = false;
        all_tickets = $(".sb-all-tickets .sb-all-tickets-list");
        html = '<div class="sb-agents-cnt"><span>Agent</span><select class="sb-wp-agents">';
        for (var i = 0; i < agents_arr.length; i++) {
            html += '<option value="' + agents_arr[i]["wp_user_id"] + '" data-agent="' + agents_arr[i]["id"] + '">' + agents_arr[i]["username"] + '</option>';
        }
        if (!isEmpty(sb_current_wp_user) || is_php) {
            $(".sb-editor").append(html.replace('value="' + sb_current_wp_user + '"', 'selected value="' + sb_current_wp_user + '"') + '</select></div>');
        }
        sb_get_tickets();
        setInterval(function () {
            if (!isIntoConversation) {
                sb_get_tickets();
            }
        }, 5000);
        $("body").on("click", ".sb-all-tickets .sb-all-users-guests", function () {
            $(".sb-all-tickets-list .sb-ticket-user").show();
            $(".sb-user-all-parent > div").removeClass("active");
            $(this).addClass("active");
            active_ticket_tab = ".sb-all-users-guests";
        });
        $("body").on("click", ".sb-all-tickets .sb-all-users", function () {
            $(".sb-all-tickets-list .sb-ticket-user").show();
            $(".sb-all-tickets-list .sb-ticket-user.sb-guest").hide();
            $(".sb-user-all-parent > div").removeClass("active");
            $(this).addClass("active");
            active_ticket_tab = ".sb-all-users";
        });
        $("body").on("click", ".sb-all-tickets .sb-all-guests", function () {
            $(".sb-all-tickets-list .sb-ticket-user").hide();
            $(".sb-all-tickets-list .sb-ticket-user.sb-guest").show();
            $(".sb-user-all-parent > div").removeClass("active");
            $(this).addClass("active");
            active_ticket_tab = ".sb-all-guests";
        });
        $("body").on("click", ".sb-btn-reply", function () {
            var tickets_panel = $(".sb-user-tickets-cnt");
            $(".sb-all-tickets").hide();
            $(".sb-user-tickets").show();
            var _user_id = $(this).closest(".sb-ticket-user").attr("data-user-id");
            $(tickets_panel).attr("data-costumer-id", _user_id);
            sb_read_messages(_user_id, tickets_panel, true, true);
            clearTimeout(real_time_interval);
            real_time_interval = setInterval(function () {
                sb_read_messages(_user_id, tickets_panel);
            }, 5000);
            isIntoConversation = true;
        });
        $("body").on("click", ".sb-user-tickets .sb-attachment", function () {
            var t = this;
            if (is_php) {
                $(".sb-upload-php").attr("multiple", true).attr("data-user-id", $(".sb-user-tickets-cnt").attr("data-costumer-id")).click();
            } else {
                $(this).open_upload_box(function (target, attachment) {
                    var html = '';
                    for (var i = 0; i < attachment.length; i++) {
                        var file = attachment.models[i].toJSON();
                        html += '<div class="sb-attachment-item" data-url="' + file.url + '">' + file.filename + '</div>';
                    }
                    $(".sb-user-tickets .sb-attachments-list").html(html);
                });
            } 
        });
        $("body").on("click", ".sb-user-tickets .sb-submit", function () {
            if (!$(this).hasClass("disabled")) {
                var ta = $(".sb-user-tickets .sb-editor textarea");
                var msg = $(ta).val();
                var d = new Date();
                var files_html = '';
                var files_string = '';
                if ($(".sb-attachments-list").html() != "") {
                    files_html = '<div class="sb-files">';
                    $(".sb-attachments-list .sb-attachment-item").each(function (index) {
                        var url = $(this).attr("data-url");
                        var name = $(this).html();
                        files_string += url + "|" + name + "?";
                        files_html += '<a target="_blank" href="' + url + '">' + name + '</a>';
                    });
                    files_html += '</div>';
                    files_string = files_string.substring(0, files_string.length - 1);
                }
                if (msg != "" || files_html != "") {
                    msg = parseMessage(msg);
                    var agent = $(".sb-wp-agents").find("[value='" + $(".sb-wp-agents").val() + "']").attr("data-agent");
                    for (var i = 0; i < agents_arr.length; i++) {
                        if (agents_arr[i]["id"] == agent) agent = agents_arr[i];
                    }
                    $(".sb-editor .sb-loader").show();
                    jQuery.ajax({
                        method: "POST",
                        url: sb_ajax_url,
                        data: {
                            action: 'sb_ajax_add_message',
                            msg: msg,
                            files: files_string,
                            time: d.toLocaleString(),
                            user_id: agent["id"],
                            user_img: agent["img"],
                            user_name: agent["username"],
                            costumer_id: $(".sb-user-tickets-cnt").attr("data-costumer-id"),
                            user_type: "agent"
                        }
                    }).done(function (response) {
                        if (response.length > 5) {
                            try {
                                response = JSON.parse(response);
                            } catch (e) {
                                response = ["error", ""];
                                console.log(e.message);
                            }
                        } else {
                            response = ["error", ""];
                        }
                        if (response[0] == "success") {
                            $(".sb-user-tickets-cnt").append('<div class="sb-ticket-user" data-user-id="' + agent["id"] + '"><img src="' + agent["img"] + '" class="sb-img" /><div class="sb-name">' + agent["username"] + '</div>' +
                                            '<div class="sb-text">' + msg + '</div>' + files_html + '<div class="sb-time">' + d.toLocaleString() + '</div></div>');
                            isFalsePush = true;
                        }
                        $(".sb-editor .sb-loader").hide();
                    });
                }
                $(ta).val("");
                $(".sb-attachment-cnt .sb-attachments-list").html("");
            }
        });
        $("body").on("click", ".sb-user-tickets-back", function () {
            $(".sb-user-tickets-cnt").html("");
            $(".sb-user-tickets,.sb-user-tickets-parent").hide();
            $(".sb-all-tickets,.sb-user-tickets .sb-loader").show();
            $(active_ticket_tab).click();
            clearTimeout(real_time_interval);
            isIntoConversation = false;
        });
        $("body").on("click", ".sb-user-tickets-delete", function () {
            jQuery.ajax({
                method: "POST",
                url: sb_ajax_url,
                data: {
                    action: 'sb_ajax_delete_conversation',
                    costumer_id: $(".sb-user-tickets-cnt").attr("data-costumer-id")
                }
            }).done(function (response) {
                if (response == "success") location.reload();
            });
        });
        $("body").on("click", ".sb-btn-delete-tickets", function () {
            jQuery.ajax({
                method: "POST",
                url: sb_ajax_url,
                data: {
                    action: 'sb_ajax_delete_all_tickets'
                }
            }).done(function (response) {
                if (response == "success") location.reload();
            });
        });
        function sb_get_tickets() {
            jQuery.ajax({
                method: "POST",
                url: sb_ajax_url,
                data: {
                    action: 'sb_ajax_get_tickets'
                }
            }).done(function (response) {
                var empty = true;
                var len = response.length;
                if (!isEmpty(response) && response != "error" && len > 10 && sb_ajax_get_tickets_len != len) {
                    var arr_tickets = false;
                    var error = false;
                    try {
                        response = response
                        arr_tickets = JSON.parse(response);
                    } catch (e) {
                        arr_tickets = false;
                        error = true;
                        $(all_tickets).html(json_error + response);
                        console.log(e.message);
                    }
                    if (arr_tickets != false) {
                        empty = false;
                        var html = '';
                        var html_disabled = '';
                        var tickets_disabled = 0;
                        for (var k = 0; k < arr_tickets.length; k++) {
                            var tickets_user = arr_tickets[k]["tickets"];
                            if (tickets_user != false && tickets_user != null) {
                                var files_html = sb_get_msg_files(tickets_user["files"]);
                                var guest = ((arr_tickets[k]["id"].toString().indexOf("guest-") == 0) ? "sb-guest" : "");
                                var time = tickets_user["time"];
                                if (time.indexOf("unix") > -1) {
                                    time = new Date(parseInt(time.replace("unix", "") + "000")).toLocaleString();
                                }
                                var tmp_html = '<div class="sb-ticket-user sb-ticket-user-disabled ' + guest + '" data-user-id="' + arr_tickets[k]["id"] + '"><img src="' + tickets_user["user_img"] + '" class="sb-img" /><div class="sb-name">' + tickets_user["user_name"] + '</div>' +
                                               '<div class="sb-text">' + tickets_user["msg"] + '</div>' + files_html + '<div class="sb-time">' + time + '</div><div class="sb-btn-reply button action">Reply</div></div>';

                                if (arr_tickets[k]["id"] == tickets_user["user_id"]) {
                                    html += tmp_html.replace(" sb-ticket-user-disabled", "");
                                } else {
                                    html_disabled += tmp_html;
                                }
                            } else {
                                if (isEmpty(tickets_user)) {
                                    $(all_tickets).html(json_error + response);
                                }
                            }
                        }
                        if (html != "" || html_disabled != "") {
                            $(all_tickets).html(html + html_disabled);
                            $(active_ticket_tab).click();
                            if (isPush && sb_ajax_get_tickets_len != 0 && !isFalsePush) {
                                Push.create("Support Board", {
                                    body: "You received a new message from Support Board",
                                    icon: sb_plugin_url + "/media/icon-sb-push.png",
                                    timeout: 4000
                                });
                                isFalsePush = false;
                            }
                            sb_ajax_get_tickets_len = len;
                        } else {
                            $(all_tickets).html(json_error + response);
                        }
                    }
                }
                if (empty && !error) {
                    $(".sb-list-msg").show();
                }
                if (error) {
                    $(all_tickets).html(json_error + response);
                }
            });
        }
        function sb_read_messages(_user_id, tickets_panel, forceReload, stopPush) {
            if (isEmpty(forceReload)) forceReload = false;
            if (isEmpty(stopPush)) stopPush = false;
            jQuery.ajax({
                method: "POST",
                url: sb_ajax_url,
                data: {
                    action: 'sb_ajax_read_messages',
                    user_id: _user_id
                }
            }).done(function (response) {
                if (!isEmpty(response) && response != "error" && response.length > 10) {
                    var arr_conversations = [];
                    try {
                        arr_conversations = JSON.parse(response);
                    } catch (e) {
                        arr_conversations = [];
                        $(".sb-user-tickets-cnt").html(json_error + response);
                        $(".sb-user-tickets .sb-loader").hide();
                        $(".sb-user-tickets-parent").show();
                        console.log(e.message);
                    }
                    if (arr_conversations != false) {
                        var len = arr_conversations.length;
                        if (msgCount == 999999 || len != msgCount || forceReload) {
                            var html = '';
                            for (var i = 0; i < len; i++) {
                                if (arr_conversations[i]["msg"] != "" || (!isEmpty(arr_conversations[i]["files"]) && arr_conversations[i]["files"].length > 0)) {
                                    var time = arr_conversations[i]["time"];
                                    if (time.indexOf("unix") > -1) {
                                        time = new Date(parseInt(time.replace("unix", "") + "000")).toLocaleString();
                                    }
                                    var files_html = sb_get_msg_files(arr_conversations[i]["files"]);
                                    html += '<div class="sb-ticket-user" data-user-id="' + arr_conversations[i]["user_id"] + '"><img src="' + arr_conversations[i]["user_img"] + '" class="sb-img" /><div class="sb-name">' + arr_conversations[i]["user_name"] + '</div>' +
                                            '<div class="sb-text">' + formatMessage(arr_conversations[i]["msg"]) + '</div>' + files_html + '<div class="sb-time">' + time + '</div></div>';
                                }
                            }
                            $(tickets_panel).html(html);
                            $(".sb-user-tickets .sb-loader").hide();
                            $(".sb-user-tickets-parent").show();
                            window.scrollTo(0, 9999999);
                            if (!stopPush && isPush && msgCount != 999999) {
                                if ($.inArray(arr_conversations[len - 1]["user_id"], agents_arr_ids)) {
                                    var msg = arr_conversations[len - 1]["msg"];
                                    if (msg == "") msg = "Attachment received."
                                    Push.create(arr_conversations[len - 1]["user_name"], {
                                        body: msg,
                                        icon: arr_conversations[len - 1]["user_img"],
                                        timeout: 4000,
                                        onClick: function () {
                                            openChat(false);
                                        }
                                    });
                                }
                            }
                            $(active_ticket_tab).click();
                        }
                        msgCount = arr_conversations.length;
                    }
                    last_user_id = _user_id;
                }
            });
        }
        function sb_get_msg_files(filsb_arr) {
            var files_html = "";
            if (!isEmpty(filsb_arr)) {
                if (filsb_arr.length > 0) {
                    files_html = '<div class="sb-files">';
                    for (var j = 0; j < filsb_arr.length; j++) {
                        if (filsb_arr[j].indexOf("|") > 0) {
                            files_html += '<a target="_blank" href="' + filsb_arr[j].split("|")[0] + '">' + filsb_arr[j].split("|")[1] + '</a>';
                        }
                    }
                    files_html += '</div>';
                }
            }
            return files_html;
        }

        //SETTINGS
        if ($("[data-setting='users-engine']").val() == "wp") {
            $(".sb-only").hide();
            $(".sb-wp-only").show();
        } else {
            $(".sb-only").show();
            $(".sb-wp-only").hide();
        }
        $("body").on("change", "[data-setting='users-engine']", function () {
            if ($(this).val() == "wp") {
                $(".sb-only").hide();
                $(".sb-wp-only").show();
            } else {
                $(".sb-only").show();
                $(".sb-wp-only").hide();
            }
        });
        $("body").on("click", "#sb-btn-test-email", function () {
            jQuery.ajax({
                method: "POST",
                url: sb_ajax_url,
                data: {
                    action: 'sb_send_test_email',
                    email: $("#test-email").val()
                }
            }).done(function (response) { if (response == "success") alert("Email sent."); });
        });

        //EMAILS
        $("body").on("click", "#btn-save-emails", function () {
            var t = this;
            jQuery.ajax({
                method: "POST",
                url: sb_ajax_url,
                data: {
                    action: 'sb_ajax_save_option',
                    option_name: 'sb-emails',
                    content: JSON.stringify([$("#email-user-subject").val().replace(/'/g, "&#39;"), $("#email-user").val().replace(/'/g, "&#39;"), $("#email-agent-subject").val().replace(/'/g, "&#39;"), $("#email-agent").val().replace(/'/g, "&#39;")])
                }
            }).done(function (response) {
                if (response) {
                    $(".sb-msg-success-email").show();
                } else {
                    $(".sb-msg-error-email").show();
                }
                setTimeout(function () {
                    $(".sb-msg-success").hide();
                }, 2000);
            });
        });

        //INDIPENDENT JS SAVE SETTINGS SYSTEM
        $("body").on("click", "#btn-save-settings", function () {
            var t = this;
            jQuery.ajax({
                method: "POST",
                url: sb_ajax_url,
                data: {
                    action: 'sb_ajax_save_option',
                    option_name: 'sb-settings',
                    content: indipendentSaveSystem(t, "read"),
                }
            }).done(function (response) {
                var msg = $(".settings-cnt .sb-msg-success");
                $(msg).show();
                setTimeout(function () {
                    $(msg).hide();
                }, 2000);
            });
        });
        function indipendentSaveSystem(source, action) {
            if (isEmpty(action) || action == "read") {
                var t = $(source).closest(".settings-cnt");
                var arr = $(t).find("[data-setting]");
                var arr_result = {};

                for (var i = 0; i < arr.length; i++) {
                    var value;
                    if ($(arr[i]).is("input:text") || $(arr[i]).attr('type') == "hidden" || $(arr[i]).is("select") || $(arr[i]).is("textarea")) value = $(arr[i]).val();
                    if ($(arr[i]).is("input:checkbox")) {
                        value = $(arr[i]).is(':checked');
                    }
                    if ($(arr[i]).is("img")) {
                        value = $(arr[i]).attr("src");
                    }
                    if (isEmpty(value)) {
                        value = "";
                    } else {
                        if (typeof value == "String" || typeof value == "string") {
                            value = value.replace(/'/g, "&#39;");
                        }
                    }
                    arr_result[$(arr[i]).attr("data-setting")] = value;
                }
                return JSON.stringify(arr_result);
            } else {
                if (action == "populate") {
                    var arr_json = $(source).find("#save_array_json").val();
                    if (!isEmpty(arr_json)) {
                        try {
                            arr_json = JSON.parse(arr_json);
                            for (var key in arr_json) {
                                var val = arr_json[key];
                                var obj = $(source).find("[data-setting='" + key + "']");
                                var bool = true;
                                if (val == "" || val == "false") bool = false;
                                if ($(obj).is("input:text") || $(obj).attr('type') == "hidden") $(obj).val(val).attr("value", val);
                                else if ($(obj).is("input:checkbox")) {
                                    $(obj).prop('checked', bool)
                                    if (bool) $(obj).attr("checked", "checked");
                                    else $(obj).removeAttr("checked");
                                } else if ($(obj).is("textarea")) {
                                    $(obj).val(val).html(val);
                                } else if ($(obj).is("select")) {
                                    $(obj).val(val);
                                    $(obj).find("option").removeAttr("data-select");
                                    $(obj).find("option[value='" + val + "']").attr("data-select", "selected");
                                } else if ($(obj).is("img")) {
                                    $(obj).attr("src", val);
                                }
                            }
                        } catch (e) {
                            console.log(e.message);
                        }
                    }
                }
            }
        }

        //SLACK
        $("body").on("click", "#sb-slack-reset", function () {
            jQuery.ajax({
                method: "POST",
                url: sb_ajax_url,
                data: {
                    action: 'sb_ajax_save_option',
                    option_name: 'sb-slack-channels',
                    content: "",
                },
                async: false
            }).done(function (response) {
                $("[data-setting='slack-token']").val("");
                $("[data-setting='slack-channel']").val("");
            });
        });
        $("body").on("click", "#sb-slack-test", function () {
            jQuery.ajax({
                method: "POST",
                url: sb_ajax_url,
                data: {
                    action: 'sb_ajax_slack_send_message',
                    msg: 'Hey! This is a test message.',
                    files: 'https://api.slack.com/|Test link?https://www.google.com|Test link 2'
                }
            }).done(function (response) {
                if (response == "slack-not-active") {
                    if (is_php) {
                        response = "You must activate Slack and set Access Token and Channel ID on config.php file. Message not sent."
                    } else {
                        response = "You must activate Slack and set Access Token and Channel ID. Message not sent."
                    }
                } else {
                    console.log(response);
                    response = "Message sent! Details on console."
                }
                alert(response);
            });
        });
    });

    //WP UPLOAD BOX
    $.fn.open_upload_box = function (myfunction) {
        var target = this;
        var frame = wp.media({
            title: 'Select or upload the media',
            button: {
                text: 'Use this media'
            },
            multiple: true
        });

        frame.on('select', function () {
            var attachment = frame.state().get('selection');
            myfunction(target, attachment);
        });

        frame.open();
        event.preventDefault();
    }

    //VARIOUS
    function isEmpty(obj) { if (typeof (obj) !== "undefined" && obj !== null && obj != "null" && (obj.length > 0 || typeof (obj) === "boolean" || typeof (obj) == 'number') && obj !== "undefined") return false; else return true; }
    function getRandomInt(min, max) {
        return Math.floor(Math.random() * (max - min + 1)) + min;
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
            return replacedText
        }
        return "";
    }
}(jQuery));



