var callback = function () {
    return this.list = [], this.stack = [], this.removing = !1, this.isDisabled = !1, this.fire = function (a) {
        var b = a[0], c = a[1], d = a[2];
        this.removing = !0;
        for (var e = 0, f = this.list.length; f > e; e++) if (!1 === this.list[e].fn.apply(b, d)) {
            c.reject();
            break
        }
        this.removing = !1, c.resolve(), this.stack.length && this.fire(this.stack.shift())
    }, this.inList = function (a, b) {
        b = b || 0;
        for (var c = b, d = this.list.length; d > c; c++) if (this.list[c].fn === a || a.guid && this.list[c].fn.guid && a.guid === this.list[c].fn.guid) return c;
        return -1
    }, this
};
callback.prototype = {
    add: function (a, b) {
        var c = {fn: a, priority: b};
        b = b || 0;
        for (var d = 0, e = this.list.length; e > d; d++) if (b > this.list[d].priority) return this.list.splice(d, 0, c), this;
        return this.list.push(c), this
    }, remove: function (a) {
        for (var b = 0; (b = this.inList(a, b)) > -1;) this.list.splice(b, 1);
        return this
    }, has: function (a) {
        return this.inList(a) > -1
    }, fireWith: function (a, b) {
        var c = jQuery.Deferred();
        return this.isDisabled ? c.reject() : (b = b || [], b = [a, c, b.slice ? b.slice() : b], this.removing ? this.stack.push(b) : this.fire(b), c)
    }, disable: function () {
        this.isDisabled = !0
    }, enable: function () {
        this.isDisabled = !1
    }
}, function (a) {
    "use strict";
    var b = -1, c = function (c, d) {
        return this.itemsContainerSelector = d.wrapperSelector, this.itemSelector = d.itemSelector, this.nextSelector = d.nextSelector, this.paginationSelector = d.paginationSelector, this.$scrollContainer = c, this.$itemsContainer = a(this.itemsContainerSelector), this.$container = window === c.get(0) ? a(document) : c, this.defaultDelay = d.delay, this.negativeMargin = d.negativeMargin, this.nextUrl = null, this.isBound = !1, this.listeners = {
            next: new callback,
            load: new callback,
            loaded: new callback,
            render: new callback,
            rendered: new callback,
            scroll: new callback,
            noneLeft: new callback,
            ready: new callback
        }, this.extensions = [], this.scrollHandler = function () {
            var a = this.getCurrentScrollOffset(this.$scrollContainer), c = this.getScrollThreshold();
            this.isBound && b != c && (this.fire("scroll", [a, c]), a >= c && this.next())
        }, this.getLastItem = function () {
            return a(this.itemSelector, this.$itemsContainer.get(0)).last()
        }, this.getFirstItem = function () {
            return a(this.itemSelector, this.$itemsContainer.get(0)).first()
        }, this.getScrollThreshold = function (a) {
            var c;
            return a = a || this.negativeMargin, a = a >= 0 ? -1 * a : a, c = this.getLastItem(), 0 === c.size() ? b : c.offset().top + c.height() + a
        }, this.getCurrentScrollOffset = function (a) {
            var b = 0, c = a.height();
            return b = window === a.get(0) ? a.scrollTop() : a.offset().top, (-1 != navigator.platform.indexOf("iPhone") || -1 != navigator.platform.indexOf("iPod")) && (c += 80), b + c
        }, this.getNextUrl = function (b) {
            return b || (b = this.$container), a(this.nextSelector, b).last().attr("href")
        }, this.load = function (b, c, d) {
            var e, f, g = this, h = [], i = +new Date;
            d = d || this.defaultDelay;
            var j = {url: b};
            return g.fire("load", [j]), a.get(j.url, null, a.proxy(function (b) {
                e = a(this.itemsContainerSelector, b).eq(0), 0 === e.length && (e = a(b).filter(this.itemsContainerSelector).eq(0)), e && e.find(this.itemSelector).each(function () {
                    h.push(this)
                }), g.fire("loaded", [b, h]), c && (f = +new Date - i, d > f ? setTimeout(function () {
                    c.call(g, b, h)
                }, d - f) : c.call(g, b, h))
            }, g), "html")
        }, this.render = function (b, c) {
            var d = this, e = this.getLastItem(), f = 0, g = this.fire("render", [b]);
            g.done(function () {
                a(b).hide(), e.after(b), a(b).fadeIn(400, function () {
                    ++f < b.length || (d.fire("rendered", [b]), c && c())
                }), a(".actions-primary")
                    .updateQtyAddedToProduct({ajax: true})
            })
        }, this.hidePagination = function () {
            this.paginationSelector && a(this.paginationSelector, this.$container).hide(), a(".toolbar-amount").hide()
        }, this.restorePagination = function () {
            this.paginationSelector && a(this.paginationSelector, this.$container).show()
        }, this.throttle = function (b, c) {
            var d, e, f = 0;
            return d = function () {
                function a() {
                    f = +new Date, b.apply(d, g)
                }

                var d = this, g = arguments, h = +new Date - f;
                e ? clearTimeout(e) : a(), h > c ? a() : e = setTimeout(a, c)
            }, a.guid && (d.guid = b.guid = b.guid || a.guid++), d
        }, this.fire = function (a, b) {
            return this.listeners[a].fireWith(this, b)
        }, this
    };
    c.prototype.initialize = function () {
        var a = this.getCurrentScrollOffset(this.$scrollContainer), b = this.getScrollThreshold();
        this.hidePagination(), this.bind();
        for (var c = 0, d = this.extensions.length; d > c; c++) this.extensions[c].bind(this);
        return this.fire("ready"), this.nextUrl = this.getNextUrl(), a >= b && this.next(), this
    }, c.prototype.bind = function () {
        this.isBound || (this.$scrollContainer.on("scroll", a.proxy(this.throttle(this.scrollHandler, 150), this)), this.isBound = !0)
    }, c.prototype.unbind = function () {
        this.isBound && (this.$scrollContainer.off("scroll", this.scrollHandler), this.isBound = !1)
    }, c.prototype.destroy = function () {
        this.unbind()
    }, c.prototype.on = function (b, c, d) {
        if ("undefined" == typeof this.listeners[b]) throw new Error('There is no event called "' + b + '"');
        return d = d || 0, this.listeners[b].add(a.proxy(c, this), d), this
    }, c.prototype.one = function (a, b) {
        var c = this, d = function () {
            c.off(a, b), c.off(a, d)
        };
        return this.on(a, b), this.on(a, d), this
    }, c.prototype.off = function (a, b) {
        if ("undefined" == typeof this.listeners[a]) throw new Error('There is no event called "' + a + '"');
        return this.listeners[a].remove(b), this
    }, c.prototype.next = function () {
        var a = this.nextUrl, b = this;
        if (this.unbind(), !a) return this.fire("noneLeft", [this.getLastItem()]), this.listeners.noneLeft.disable(), b.bind(), !1;
        var c = this.fire("next", [a]);
        return c.done(function () {
            b.load(a, function (a, c) {
                b.render(c, function () {
                    b.nextUrl = b.getNextUrl(a), b.bind()
                })
            })
        }), c.fail(function () {
            b.bind()
        }), !0
    }, c.prototype.extension = function (a) {
        if ("undefined" == typeof a.bind) throw new Error('Extension doesn\'t have required method "bind"');
        return "undefined" != typeof a.initialize && a.initialize(this), this.extensions.push(a), this
    }, a.scroll = function () {
        var b = a(window);
        return b.scroll.apply(b, arguments)
    }, a.fn.scroll = function (b) {
        var d = Array.prototype.slice.call(arguments), e = this;
        return this.each(function () {
            var f = a(this), g = f.data("scroll"),
                h = a.extend({}, a.fn.scroll.defaults, f.data(), "object" == typeof b && b);
            if (g || (f.data("scroll", g = new c(f, h)), a(document).ready(a.proxy(g.initialize, g))), "string" == typeof b) {
                if ("function" != typeof g[b]) throw new Error('There is no method called "' + b + '"');
                d.shift(), g[b].apply(g, d), "destroy" === b && f.data("scroll", null)
            }
            e = f.data("scroll")
        }), e
    }, a.fn.scroll.defaults = {
        itemSelector: ".product-item",
        wrapperSelector: "#product-wrapper",
        nextSelector: ".pagination .next",
        paginationSelector: ".pagination .item",
        delay: 500,
        negativeMargin: 10
    }
}(jQuery);
var scrollLoading = function (a) {
    return a = jQuery.extend({}, this.defaults, a), this.scroll = null, this.uid = (new Date).getTime(), this.src = a.src, this.html = a.html.replace("{src}", this.src), this.displayLoader = function () {
        var a = this.getLoader() || this.initLoader(), b = this.scroll.getLastItem();
        b.after(a), a.fadeIn()
    }, this.displayLoaderBefore = function () {
        var a = this.getLoader() || this.initLoader(), b = this.scroll.getFirstItem();
        b.before(a), a.fadeIn()
    }, this.hideLoader = function () {
        this.loaderExist() && this.getLoader().remove()
    }, this.getLoader = function () {
        var a = jQuery("#scroll_loading" + this.uid);
        return a.size() > 0 && a
    }, this.loaderExist = function () {
        var a = jQuery("#scroll_loading" + this.uid);
        return a.size() > 0
    }, this.initLoader = function () {
        var a = jQuery(this.html).attr("id", "scroll_loading" + this.uid);
        return a.hide(), a
    }, this
};
scrollLoading.prototype.bind = function (a) {
    this.scroll = a, a.on("next", jQuery.proxy(this.displayLoader, this));
    try {
        a.on("prev", jQuery.proxy(this.displayLoaderBefore, this))
    } catch (a) {
    }
    a.on("render", jQuery.proxy(this.hideLoader, this))
}, scrollLoading.prototype.defaults = {src: "", html: ""};
