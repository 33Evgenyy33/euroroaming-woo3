!function (a) {
    "use strict";
    "function" == typeof define && define.amd ? define(["jquery"], a) : "undefined" != typeof module && module.exports ? module.exports = a(require("jquery")) : a(jQuery)
}(function (a) {
    var b = -1, c = -1, d = function (a) {
        return parseFloat(a) || 0
    }, e = function (b) {
        var c = 1, e = a(b), f = null, g = [];
        return e.each(function () {
            var b = a(this), e = b.offset().top - d(b.css("margin-top")), h = g.length > 0 ? g[g.length - 1] : null;
            null === h ? g.push(b) : Math.floor(Math.abs(f - e)) <= c ? g[g.length - 1] = h.add(b) : g.push(b), f = e
        }), g
    }, f = function (b) {
        var c = {byRow: !0, property: "height", target: null, remove: !1};
        return "object" == typeof b ? a.extend(c, b) : ("boolean" == typeof b ? c.byRow = b : "remove" === b && (c.remove = !0), c)
    }, g = a.fn.matchHeight = function (b) {
        var c = f(b);
        if (c.remove) {
            var d = this;
            return this.css(c.property, ""), a.each(g._groups, function (a, b) {
                b.elements = b.elements.not(d)
            }), this
        }
        return this.length <= 1 && !c.target ? this : (g._groups.push({
            elements: this,
            options: c
        }), g._apply(this, c), this)
    };
    g.version = "0.7.0", g._groups = [], g._throttle = 80, g._maintainScroll = !1, g._beforeUpdate = null, g._afterUpdate = null, g._rows = e, g._parse = d, g._parseOptions = f, g._apply = function (b, c) {
        var h = f(c), i = a(b), j = [i], k = a(window).scrollTop(), l = a("html").outerHeight(!0),
            m = i.parents().filter(":hidden");
        return m.each(function () {
            var b = a(this);
            b.data("style-cache", b.attr("style"))
        }), m.css("display", "block"), h.byRow && !h.target && (i.each(function () {
            var b = a(this), c = b.css("display");
            "inline-block" !== c && "flex" !== c && "inline-flex" !== c && (c = "block"), b.data("style-cache", b.attr("style")), b.css({
                display: c,
                "padding-top": "0",
                "padding-bottom": "0",
                "margin-top": "0",
                "margin-bottom": "0",
                "border-top-width": "0",
                "border-bottom-width": "0",
                height: "100px",
                overflow: "hidden"
            })
        }), j = e(i), i.each(function () {
            var b = a(this);
            b.attr("style", b.data("style-cache") || "")
        })), a.each(j, function (b, c) {
            var e = a(c), f = 0;
            if (h.target) f = h.target.outerHeight(!1); else {
                if (h.byRow && e.length <= 1)return void e.css(h.property, "");
                e.each(function () {
                    var b = a(this), c = b.attr("style"), d = b.css("display");
                    "inline-block" !== d && "flex" !== d && "inline-flex" !== d && (d = "block");
                    var e = {display: d};
                    e[h.property] = "", b.css(e), b.outerHeight(!1) > f && (f = b.outerHeight(!1)), c ? b.attr("style", c) : b.css("display", "")
                })
            }
            e.each(function () {
                var b = a(this), c = 0;
                h.target && b.is(h.target) || ("border-box" !== b.css("box-sizing") && (c += d(b.css("border-top-width")) + d(b.css("border-bottom-width")), c += d(b.css("padding-top")) + d(b.css("padding-bottom"))), b.css(h.property, f - c + "px"))
            })
        }), m.each(function () {
            var b = a(this);
            b.attr("style", b.data("style-cache") || null)
        }), g._maintainScroll && a(window).scrollTop(k / l * a("html").outerHeight(!0)), this
    }, g._applyDataApi = function () {
        var b = {};
        a("[data-match-height], [data-mh]").each(function () {
            var c = a(this), d = c.attr("data-mh") || c.attr("data-match-height");
            d in b ? b[d] = b[d].add(c) : b[d] = c
        }), a.each(b, function () {
            this.matchHeight(!0)
        })
    };
    var h = function (b) {
        g._beforeUpdate && g._beforeUpdate(b, g._groups), a.each(g._groups, function () {
            g._apply(this.elements, this.options)
        }), g._afterUpdate && g._afterUpdate(b, g._groups)
    };
    g._update = function (d, e) {
        if (e && "resize" === e.type) {
            var f = a(window).width();
            if (f === b)return;
            b = f
        }
        d ? -1 === c && (c = setTimeout(function () {
                h(e), c = -1
            }, g._throttle)) : h(e)
    }, a(g._applyDataApi), a(window).on("load", function (a) {
        g._update(!1, a)
    }), a(window).on("resize orientationchange", function (a) {
        g._update(!0, a)
    })
}), !function (a) {
    jQuery.fn.equalHeightResponsive = function (b) {
        a(window).width() < b ? "true" === this.data("matchHeight") && (this.data("matchHeight", "false"), this.matchHeight({remove: !0})) : "false" !== this.data("matchHeight") && "undefined" != typeof this.data("matchHeight") || (this.data("matchHeight", "true"), this.matchHeight())
    }, a(document).ready(function () {
        a("div.w-pricing-item:first-child ul.w-pricing-item-features .w-pricing-item-feature .pricing-decrip").each(function () {
            a("." + a(this)[0].classList[1]).equalHeightResponsive("700");
            //console.log(a(this));
        })
    }),
        a(window).resize(function () {
        a("div.w-pricing-item:first-child ul.w-pricing-item-features .w-pricing-item-feature .pricing-decrip").each(function () {
            a("." + a(this)[0].classList[1]).equalHeightResponsive("700")
        })
    })
}(jQuery);