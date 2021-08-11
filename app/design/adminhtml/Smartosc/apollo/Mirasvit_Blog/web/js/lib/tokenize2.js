!function (t) {
    "function" == typeof define && define.amd ? define(["jquery"], t) : "object" == typeof module && module.exports ? module.exports = function (e, o) {
        return void 0 === o && (o = "undefined" != typeof window ? require("jquery") : require("jquery")(e)), t(o), o
    } : t(jQuery)
}(function (t) {
    var e = function (o, i) {
        this.control = !1, this.element = t(o), this.options = t.extend({}, e.DEFAULTS, i), this.options.tabIndex = -1 === this.options.tabIndex ? 0 : this.options.tabIndex, this.options.sortable = 1 !== this.options.tokensMaxItems && this.options.sortable, this.bind(), this.trigger("tokenize:load")
    }, o = 8, i = 9, n = 13, s = 27, r = 38, a = 40, h = 17, d = 16;
    e.VERSION = "1.0", e.DEBOUNCE = null, e.DEFAULTS = {
        tokensMaxItems: 0,
        tokensAllowCustom: !1,
        dropdownMaxItems: 10,
        searchMinLength: 0,
        searchFromStart: !0,
        searchHighlight: !0,
        displayNoResultsMessage: !1,
        noResultsMessageText: 'No results mached "%s"',
        delimiter: ",",
        dataSource: "select",
        debounce: 0,
        placeholder: !1,
        sortable: !1,
        allowEmptyValues: !1,
        zIndexMargin: 500,
        tabIndex: 0
    }, e.prototype.trigger = function (t, e, o, i) {
        this.element.trigger(t, e, o, i)
    }, e.prototype.bind = function () {
        this.element.on("tokenize:load", {}, t.proxy(function () {
            this.init()
        }, this)).on("tokenize:clear", {}, t.proxy(function () {
            this.clear()
        }, this)).on("tokenize:remap", {}, t.proxy(function () {
            this.remap()
        }, this)).on("tokenize:select", {}, t.proxy(function (t, e) {
            this.focus(e)
        }, this)).on("tokenize:deselect", {}, t.proxy(function () {
            this.blur()
        }, this)).on("tokenize:search", {}, t.proxy(function (t, e) {
            this.find(e)
        }, this)).on("tokenize:paste", {}, t.proxy(function () {
            this.paste()
        }, this)).on("tokenize:dropdown:up", {}, t.proxy(function () {
            this.dropdownSelectionMove(-1)
        }, this)).on("tokenize:dropdown:down", {}, t.proxy(function () {
            this.dropdownSelectionMove(1)
        }, this)).on("tokenize:dropdown:clear", {}, t.proxy(function () {
            this.dropdownClear()
        }, this)).on("tokenize:dropdown:hide", {}, t.proxy(function () {
            this.dropdownHide()
        }, this)).on("tokenize:dropdown:fill", {}, t.proxy(function (t, e) {
            this.dropdownFill(e)
        }, this)).on("tokenize:dropdown:itemAdd", {}, t.proxy(function (t, e) {
            this.dropdownAddItem(e)
        }, this)).on("tokenize:keypress", {}, t.proxy(function (t, e) {
            this.keypress(e)
        }, this)).on("tokenize:keydown", {}, t.proxy(function (t, e) {
            this.keydown(e)
        }, this)).on("tokenize:keyup", {}, t.proxy(function (t, e) {
            this.keyup(e)
        }, this)).on("tokenize:tokens:reorder", {}, t.proxy(function () {
            this.reorder()
        }, this)).on("tokenize:tokens:add", {}, t.proxy(function (t, e, o, i) {
            this.tokenAdd(e, o, i)
        }, this)).on("tokenize:tokens:remove", {}, t.proxy(function (t, e) {
            this.tokenRemove(e)
        }, this))
    }, e.prototype.init = function () {
        this.id = this.guid(), this.element.hide(), this.element.attr("multiple") || console.error("Attribute multiple is missing, tokenize2 can be buggy !"), this.dropdown = void 0, this.searchContainer = t('<li class="token-search" />'), this.input = t('<input autocomplete="off" />').on("keydown", {}, t.proxy(function (t) {
            this.trigger("tokenize:keydown", [t])
        }, this)).on("keypress", {}, t.proxy(function (t) {
            this.trigger("tokenize:keypress", [t])
        }, this)).on("keyup", {}, t.proxy(function (t) {
            this.trigger("tokenize:keyup", [t])
        }, this)).on("focus", {}, t.proxy(function () {
            this.input.val().length >= this.options.searchMinLength && this.input.val().length > 0 && this.trigger("tokenize:search", [this.input.val()])
        }, this)).on("paste", {}, t.proxy(function () {
            this.options.tokensAllowCustom && setTimeout(t.proxy(function () {
                this.trigger("tokenize:paste")
            }, this), 10)
        }, this)), this.tokensContainer = t('<ul class="tokens-container form-control" />').addClass(this.element.attr("data-class")).attr("tabindex", this.options.tabIndex).append(this.searchContainer.append(this.input)), !1 !== this.options.placeholder && (this.placeholder = t('<li class="placeholder" />').html(this.options.placeholder), this.tokensContainer.prepend(this.placeholder), this.element.on("tokenize:tokens:add tokenize:remap tokenize:select tokenize:deselect tokenize:tokens:remove", t.proxy(function () {
            this.container.hasClass("focus") || t("li.token", this.tokensContainer).length > 0 || this.input.val().length > 0 ? this.placeholder.hide() : this.placeholder.show()
        }, this))), this.container = t('<div class="tokenize" />').attr("id", this.id), this.container.append(this.tokensContainer).insertAfter(this.element), this.container.focusin(t.proxy(function (e) {
            this.trigger("tokenize:select", [t(e.target)[0] === this.tokensContainer[0]])
        }, this)).focusout(t.proxy(function () {
            this.trigger("tokenize:deselect")
        }, this)), 1 === this.options.tokensMaxItems && this.container.addClass("single"), this.options.sortable && (void 0 !== t.ui ? (this.container.addClass("sortable"), this.tokensContainer.sortable({
            items: "li.token",
            cursor: "move",
            placeholder: "token shadow",
            forcePlaceholderSize: !0,
            update: t.proxy(function () {
                this.trigger("tokenize:tokens:reorder")
            }, this),
            start: t.proxy(function () {
                this.searchContainer.hide()
            }, this),
            stop: t.proxy(function () {
                this.searchContainer.show()
            }, this)
        })) : (this.options.sortable = !1, console.error("jQuery UI is not loaded, sortable option has been disabled"))), this.element.on("tokenize:tokens:add tokenize:tokens:remove", t.proxy(function () {
            this.options.tokensMaxItems > 0 && t("li.token", this.tokensContainer).length >= this.options.tokensMaxItems ? this.searchContainer.hide() : this.searchContainer.show()
        }, this)).on("tokenize:keydown tokenize:keyup tokenize:loaded", t.proxy(function () {
            this.scaleInput()
        }, this)), this.trigger("tokenize:remap"), this.trigger("tokenize:tokens:reorder"), this.trigger("tokenize:loaded"), this.element.is(":disabled") && this.disable()
    }, e.prototype.reorder = function () {
        var e, o;
        this.options.sortable && t.each(this.tokensContainer.sortable("toArray", {attribute: "data-value"}), t.proxy(function (i, n) {
            o = t('option[value="' + n + '"]', this.element), void 0 === e ? o.prependTo(this.element) : e.after(o), e = o
        }, this))
    }, e.prototype.paste = function () {
        var e = new RegExp(this.escapeRegex(Array.isArray(this.options.delimiter) ? this.options.delimiter.join("|") : this.options.delimiter), "ig");
        e.test(this.input.val()) && t.each(this.input.val().split(e), t.proxy(function (t, e) {
            this.trigger("tokenize:tokens:add", [e, null, !0])
        }, this))
    }, e.prototype.tokenAdd = function (e, o, i) {
        if (e = this.escape(e), o = o || e, i = i || !1, this.resetInput(), void 0 === e || !this.options.allowEmptyValues && "" === e) return this.trigger("tokenize:tokens:error:empty"), this;
        if (this.options.tokensMaxItems > 0 && t("li.token", this.tokensContainer).length >= this.options.tokensMaxItems) return this.trigger("tokenize:tokens:error:max"), this;
        if (t('li.token[data-value="' + e + '"]', this.tokensContainer).length > 0) return this.trigger("tokenize:tokens:error:duplicate", [e, o]), this;
        if (t('option[value="' + e + '"]', this.element).length) t('option[value="' + e + '"]', this.element).attr("selected", "selected").prop("selected", !0); else if (i) this.element.append(t("<option selected />").val(e).html(o)); else {
            if (!this.options.tokensAllowCustom) return this.trigger("tokenize:tokens:error:notokensAllowCustom"), this;
            this.element.append(t('<option selected data-type="custom" />').val(e).html(o))
        }
        return t('<li class="token" />').attr("data-value", e).append("<span>" + o + "</span>").prepend(t('<a class="dismiss" />').on("mousedown touchstart", {}, t.proxy(function (t) {
            t.preventDefault(), this.trigger("tokenize:tokens:remove", [e])
        }, this))).insertBefore(this.searchContainer), this.trigger("tokenize:dropdown:hide"), this
    }, e.prototype.tokenRemove = function (e) {
        var o = t('option[value="' + e + '"]', this.element);
        return "custom" === o.attr("data-type") ? o.remove() : o.removeAttr("selected").prop("selected", !1), t('li.token[data-value="' + e + '"]', this.tokensContainer).remove(), this.trigger("tokenize:tokens:reorder"), this
    }, e.prototype.remap = function () {
        return t("option:selected", this.element).each(t.proxy(function (e, o) {
            this.trigger("tokenize:tokens:add", [t(o).val(), t(o).html(), !1])
        }, this)), this
    }, e.prototype.disable = function () {
        return this.tokensContainer.addClass("disabled"), this.searchContainer.hide(), this
    }, e.prototype.enable = function () {
        return this.tokensContainer.removeClass("disabled"), this.searchContainer.show(), this
    }, e.prototype.focus = function (t) {
        this.element.is(":disabled") ? this.tokensContainer.blur() : (t && this.input.focus(), this.container.addClass("focus"))
    }, e.prototype.blur = function () {
        this.isDropdownOpen() && this.trigger("tokenize:dropdown:hide"), this.container.removeClass("focus"), this.resetPending(), this.tokensContainer.attr("tabindex") || this.tokensContainer.attr("tabindex", this.options.tabIndex)
    }, e.prototype.keydown = function (e) {
        if ("keydown" === e.type) switch (e.keyCode) {
            case o:
                if (this.input.val().length < 1) {
                    if (e.preventDefault(), t("li.token.pending-delete", this.tokensContainer).length > 0) this.trigger("tokenize:tokens:remove", [t("li.token.pending-delete", this.tokensContainer).first().attr("data-value")]); else {
                        var d = t("li.token:last", this.tokensContainer);
                        d.length > 0 && (this.trigger("tokenize:tokens:markForDelete", [d.attr("data-value")]), d.addClass("pending-delete"))
                    }
                    this.trigger("tokenize:dropdown:hide")
                }
                break;
            case i:
                e.shiftKey ? this.tokensContainer.removeAttr("tabindex") : this.pressedDelimiter(e);
                break;
            case n:
                this.pressedDelimiter(e);
                break;
            case s:
                this.resetPending();
                break;
            case r:
                e.preventDefault(), this.trigger("tokenize:dropdown:up");
                break;
            case a:
                e.preventDefault(), this.trigger("tokenize:dropdown:down");
                break;
            case h:
                this.control = !0;
                break;
            default:
                this.resetPending()
        } else e.preventDefault()
    }, e.prototype.keyup = function (t) {
        if ("keyup" === t.type) switch (t.keyCode) {
            case i:
            case n:
            case s:
            case r:
            case a:
            case d:
                break;
            case h:
                this.control = !1;
                break;
            case o:
            default:
                this.input.val().length >= this.options.searchMinLength && this.input.val().length > 0 ? this.trigger("tokenize:search", [this.input.val()]) : this.trigger("tokenize:dropdown:hide")
        } else t.preventDefault()
    }, e.prototype.keypress = function (t) {
        if ("keypress" !== t.type || this.element.is(":disabled")) t.preventDefault(); else {
            var e = !1;
            Array.isArray(this.options.delimiter) ? this.options.delimiter.indexOf(String.fromCharCode(t.which)) >= 0 && (e = !0) : String.fromCharCode(t.which) === this.options.delimiter && (e = !0), e && this.pressedDelimiter(t)
        }
    }, e.prototype.pressedDelimiter = function (e) {
        this.resetPending(), this.isDropdownOpen() && t("li.active", this.dropdown).length > 0 && !1 === this.control ? (e.preventDefault(), t("li.active a", this.dropdown).trigger("mousedown")) : this.input.val().length > 0 && (e.preventDefault(), this.trigger("tokenize:tokens:add", [this.input.val()]))
    }, e.prototype.find = function (t) {
        if (t.length < this.options.searchMinLength) return this.trigger("tokenize:dropdown:hide"), !1;
        this.lastSearchTerms = t, "select" === this.options.dataSource ? this.dataSourceLocal(t) : "function" == typeof this.options.dataSource ? this.options.dataSource(t, this) : this.dataSourceRemote(t)
    }, e.prototype.dataSourceRemote = function (e) {
        this.debounce(t.proxy(function () {
            void 0 !== this.xhr && this.xhr.abort(), this.xhr = t.ajax(this.options.dataSource, {
                data: {search: e},
                dataType: "json",
                success: t.proxy(function (e) {
                    var o = [];
                    t.each(e, function (t, e) {
                        o.push(e)
                    }), this.trigger("tokenize:dropdown:fill", [o])
                }, this)
            })
        }, this), this.options.debounce)
    }, e.prototype.dataSourceLocal = function (e) {
        var o = this.transliteration(e), i = [], n = (this.options.searchFromStart ? "^" : "") + this.escapeRegex(o),
            s = new RegExp(n, "i"), r = this;
        t("option", this.element).not(":selected, :disabled").each(function () {
            s.test(r.transliteration(t(this).html())) && i.push({value: t(this).attr("value"), text: t(this).html()})
        }), this.trigger("tokenize:dropdown:fill", [i])
    }, e.prototype.debounce = function (e, o) {
        var i = arguments, n = t.proxy(function () {
            e.apply(this, i), this.debounceTimeout = void 0
        }, this);
        void 0 !== this.debounceTimeout && clearTimeout(this.debounceTimeout), this.debounceTimeout = setTimeout(n, o || 0)
    }, e.prototype.calculatezindex = function () {
        var t = this.container, e = 0;
        if (!isNaN(parseInt(t.css("z-index"))) && parseInt(t.css("z-index")) > 0 && (e = parseInt(t.css("z-index"))), e < 1) for (; t.length;) if ((t = t.parent()).length > 0) {
            if (!isNaN(parseInt(t.css("z-index"))) && parseInt(t.css("z-index")) > 0) return parseInt(t.css("z-index"));
            if (t.is("html")) break
        }
        return e
    }, e.prototype.dropdownHide = function () {
        this.isDropdownOpen() && (t(window).off("resize scroll"), this.dropdown.remove(), this.dropdown = void 0, this.trigger("tokenize:dropdown:hidden"))
    }, e.prototype.dropdownClear = function () {
        this.dropdown && this.dropdown.find(".dropdown-menu li").remove()
    }, e.prototype.dropdownFill = function (e) {
        e && e.length > 0 ? (this.trigger("tokenize:dropdown:clear"), t.each(e, t.proxy(function (e, o) {
            t("li.dropdown-item", this.dropdown).length <= this.options.dropdownMaxItems && this.trigger("tokenize:dropdown:itemAdd", [o])
        }, this)), t("li.active", this.dropdown).length < 1 && t("li:first", this.dropdown).addClass("active"), t("li.dropdown-item", this.dropdown).length < 1 ? this.trigger("tokenize:dropdown:hide") : this.trigger("tokenize:dropdown:filled")) : this.options.displayNoResultsMessage ? (this.trigger("tokenize:dropdown:clear"), this.dropdown.find(".dropdown-menu").append(t('<li class="dropdown-item locked" />').html(this.options.noResultsMessageText.replace("%s", this.input.val())))) : this.trigger("tokenize:dropdown:hide"), t(window).trigger("resize")
    }, e.prototype.dropdownSelectionMove = function (e) {
        if (t("li.active", this.dropdown).length > 0) if (t("li.active", this.dropdown).is("li:" + (e > 0 ? "last-child" : "first-child"))) t("li.active", this.dropdown).removeClass("active"), t("li:" + (e > 0 ? "first-child" : "last-child"), this.dropdown).addClass("active"); else {
            var o = t("li.active", this.dropdown);
            o.removeClass("active"), e > 0 ? o.next().addClass("active") : o.prev().addClass("active")
        } else t("li:first", this.dropdown).addClass("active")
    }, e.prototype.dropdownAddItem = function (e) {
        if (this.isDropdownOpen()) {
            var o = t('<li class="dropdown-item" />').html(this.dropdownItemFormat(e)).on("mouseover", t.proxy(function (e) {
                e.preventDefault(), e.target = this.fixTarget(e.target), t("li", this.dropdown).removeClass("active"), t(e.target).parent().addClass("active")
            }, this)).on("mouseout", t.proxy(function () {
                t("li", this.dropdown).removeClass("active")
            }, this)).on("mousedown touchstart", t.proxy(function (e) {
                e.preventDefault(), e.target = this.fixTarget(e.target), this.trigger("tokenize:tokens:add", [t(e.target).attr("data-value"), t(e.target).attr("data-text"), !0])
            }, this));
            t('li.token[data-value="' + o.find("a").attr("data-value") + '"]', this.tokensContainer).length < 1 && (this.dropdown.find(".dropdown-menu").append(o), this.trigger("tokenize:dropdown:itemAdded", [e]))
        }
    }, e.prototype.fixTarget = function (e) {
        var o = t(e);
        if (!o.data("value")) {
            var i = o.find("a");
            if (i.length) return i.get(0);
            var n = o.parents("[data-value]");
            if (n.length) return n.get(0)
        }
        return o.get(0)
    }, e.prototype.dropdownItemFormat = function (e) {
        if (e.hasOwnProperty("text")) {
            var o = "";
            if (this.options.searchHighlight) {
                var i = new RegExp((this.options.searchFromStart ? "^" : "") + "(" + this.escapeRegex(this.transliteration(this.lastSearchTerms)) + ")", "gi");
                o = e.text.replace(i, '<span class="tokenize-highlight">$1</span>')
            } else o = e.text;
            return t("<a />").html(o).attr({"data-value": e.value, "data-text": e.text})
        }
    }, e.prototype.dropdownMove = function () {
        var t = this.tokensContainer.offset(), e = this.tokensContainer.outerHeight(),
            o = this.tokensContainer.outerWidth();
        t.top += e, this.dropdown.css({width: o}).offset(t)
    }, e.prototype.isDropdownOpen = function () {
        return void 0 !== this.dropdown
    }, e.prototype.clear = function () {
        return t.each(t("li.token", this.tokensContainer), t.proxy(function (e, o) {
            this.trigger("tokenize:tokens:remove", [t(o).attr("data-value")])
        }, this)), this.trigger("tokenize:dropdown:hide"), this
    }, e.prototype.resetPending = function () {
        var e = t("li.pending-delete:last", this.tokensContainer);
        e.length > 0 && (this.trigger("tokenize:tokens:cancelDelete", [e.attr("data-value")]), e.removeClass("pending-delete"))
    }, e.prototype.scaleInput = function () {
        var t, e;
        this.ctx || (this.ctx = document.createElement("canvas").getContext("2d")), this.ctx.font = this.input.css("font-style") + " " + this.input.css("font-variant") + " " + this.input.css("font-weight") + " " + Math.ceil(parseFloat(this.input.css("font-size"))) + "px " + this.input.css("font-family"), (t = Math.round(this.ctx.measureText(this.input.val() + "M").width) + Math.ceil(parseFloat(this.searchContainer.css("margin-left"))) + Math.ceil(parseFloat(this.searchContainer.css("margin-right")))) >= (e = this.tokensContainer.width() - (Math.ceil(parseFloat(this.tokensContainer.css("border-left-width"))) + Math.ceil(parseFloat(this.tokensContainer.css("border-right-width")) + Math.ceil(parseFloat(this.tokensContainer.css("padding-left"))) + Math.ceil(parseFloat(this.tokensContainer.css("padding-right")))))) && (t = e), this.searchContainer.width(t), this.ctx.restore()
    }, e.prototype.resetInput = function () {
        this.input.val(""), this.scaleInput()
    }, e.prototype.escape = function (t) {
        var e = document.createElement("div");
        return e.innerHTML = t, t = e.textContent || e.innerText || "", String(t).replace(/["]/g, function () {
            return '"'
        })
    }, e.prototype.escapeRegex = function (t) {
        return t.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, "\\$&")
    }, e.prototype.guid = function () {
        function t() {
            return Math.floor(65536 * (1 + Math.random())).toString(16).substring(1)
        }

        return t() + t() + "-" + t() + "-" + t() + "-" + t() + "-" + t() + t() + t()
    }, e.prototype.toArray = function () {
        var e = [];
        return t("option:selected", this.element).each(function () {
            e.push(t(this).val())
        }), e
    }, e.prototype.transliteration = function (t) {
        var e = {
            "Ⓐ": "A",
            "Ａ": "A",
            "À": "A",
            "Á": "A",
            "Â": "A",
            "Ầ": "A",
            "Ấ": "A",
            "Ẫ": "A",
            "Ẩ": "A",
            "Ã": "A",
            "Ā": "A",
            "Ă": "A",
            "Ằ": "A",
            "Ắ": "A",
            "Ẵ": "A",
            "Ẳ": "A",
            "Ȧ": "A",
            "Ǡ": "A",
            "Ä": "A",
            "Ǟ": "A",
            "Ả": "A",
            "Å": "A",
            "Ǻ": "A",
            "Ǎ": "A",
            "Ȁ": "A",
            "Ȃ": "A",
            "Ạ": "A",
            "Ậ": "A",
            "Ặ": "A",
            "Ḁ": "A",
            "Ą": "A",
            "Ⱥ": "A",
            "Ɐ": "A",
            "Ꜳ": "AA",
            "Æ": "AE",
            "Ǽ": "AE",
            "Ǣ": "AE",
            "Ꜵ": "AO",
            "Ꜷ": "AU",
            "Ꜹ": "AV",
            "Ꜻ": "AV",
            "Ꜽ": "AY",
            "Ⓑ": "B",
            "Ｂ": "B",
            "Ḃ": "B",
            "Ḅ": "B",
            "Ḇ": "B",
            "Ƀ": "B",
            "Ƃ": "B",
            "Ɓ": "B",
            "Ⓒ": "C",
            "Ｃ": "C",
            "Ć": "C",
            "Ĉ": "C",
            "Ċ": "C",
            "Č": "C",
            "Ç": "C",
            "Ḉ": "C",
            "Ƈ": "C",
            "Ȼ": "C",
            "Ꜿ": "C",
            "Ⓓ": "D",
            "Ｄ": "D",
            "Ḋ": "D",
            "Ď": "D",
            "Ḍ": "D",
            "Ḑ": "D",
            "Ḓ": "D",
            "Ḏ": "D",
            "Đ": "D",
            "Ƌ": "D",
            "Ɗ": "D",
            "Ɖ": "D",
            "Ꝺ": "D",
            "Ǳ": "DZ",
            "Ǆ": "DZ",
            "ǲ": "Dz",
            "ǅ": "Dz",
            "Ⓔ": "E",
            "Ｅ": "E",
            "È": "E",
            "É": "E",
            "Ê": "E",
            "Ề": "E",
            "Ế": "E",
            "Ễ": "E",
            "Ể": "E",
            "Ẽ": "E",
            "Ē": "E",
            "Ḕ": "E",
            "Ḗ": "E",
            "Ĕ": "E",
            "Ė": "E",
            "Ë": "E",
            "Ẻ": "E",
            "Ě": "E",
            "Ȅ": "E",
            "Ȇ": "E",
            "Ẹ": "E",
            "Ệ": "E",
            "Ȩ": "E",
            "Ḝ": "E",
            "Ę": "E",
            "Ḙ": "E",
            "Ḛ": "E",
            "Ɛ": "E",
            "Ǝ": "E",
            "Ⓕ": "F",
            "Ｆ": "F",
            "Ḟ": "F",
            "Ƒ": "F",
            "Ꝼ": "F",
            "Ⓖ": "G",
            "Ｇ": "G",
            "Ǵ": "G",
            "Ĝ": "G",
            "Ḡ": "G",
            "Ğ": "G",
            "Ġ": "G",
            "Ǧ": "G",
            "Ģ": "G",
            "Ǥ": "G",
            "Ɠ": "G",
            "Ꞡ": "G",
            "Ᵹ": "G",
            "Ꝿ": "G",
            "Ⓗ": "H",
            "Ｈ": "H",
            "Ĥ": "H",
            "Ḣ": "H",
            "Ḧ": "H",
            "Ȟ": "H",
            "Ḥ": "H",
            "Ḩ": "H",
            "Ḫ": "H",
            "Ħ": "H",
            "Ⱨ": "H",
            "Ⱶ": "H",
            "Ɥ": "H",
            "Ⓘ": "I",
            "Ｉ": "I",
            "Ì": "I",
            "Í": "I",
            "Î": "I",
            "Ĩ": "I",
            "Ī": "I",
            "Ĭ": "I",
            "İ": "I",
            "Ï": "I",
            "Ḯ": "I",
            "Ỉ": "I",
            "Ǐ": "I",
            "Ȉ": "I",
            "Ȋ": "I",
            "Ị": "I",
            "Į": "I",
            "Ḭ": "I",
            "Ɨ": "I",
            "Ⓙ": "J",
            "Ｊ": "J",
            "Ĵ": "J",
            "Ɉ": "J",
            "Ⓚ": "K",
            "Ｋ": "K",
            "Ḱ": "K",
            "Ǩ": "K",
            "Ḳ": "K",
            "Ķ": "K",
            "Ḵ": "K",
            "Ƙ": "K",
            "Ⱪ": "K",
            "Ꝁ": "K",
            "Ꝃ": "K",
            "Ꝅ": "K",
            "Ꞣ": "K",
            "Ⓛ": "L",
            "Ｌ": "L",
            "Ŀ": "L",
            "Ĺ": "L",
            "Ľ": "L",
            "Ḷ": "L",
            "Ḹ": "L",
            "Ļ": "L",
            "Ḽ": "L",
            "Ḻ": "L",
            "Ł": "L",
            "Ƚ": "L",
            "Ɫ": "L",
            "Ⱡ": "L",
            "Ꝉ": "L",
            "Ꝇ": "L",
            "Ꞁ": "L",
            "Ǉ": "LJ",
            "ǈ": "Lj",
            "Ⓜ": "M",
            "Ｍ": "M",
            "Ḿ": "M",
            "Ṁ": "M",
            "Ṃ": "M",
            "Ɱ": "M",
            "Ɯ": "M",
            "Ⓝ": "N",
            "Ｎ": "N",
            "Ǹ": "N",
            "Ń": "N",
            "Ñ": "N",
            "Ṅ": "N",
            "Ň": "N",
            "Ṇ": "N",
            "Ņ": "N",
            "Ṋ": "N",
            "Ṉ": "N",
            "Ƞ": "N",
            "Ɲ": "N",
            "Ꞑ": "N",
            "Ꞥ": "N",
            "Ǌ": "NJ",
            "ǋ": "Nj",
            "Ⓞ": "O",
            "Ｏ": "O",
            "Ò": "O",
            "Ó": "O",
            "Ô": "O",
            "Ồ": "O",
            "Ố": "O",
            "Ỗ": "O",
            "Ổ": "O",
            "Õ": "O",
            "Ṍ": "O",
            "Ȭ": "O",
            "Ṏ": "O",
            "Ō": "O",
            "Ṑ": "O",
            "Ṓ": "O",
            "Ŏ": "O",
            "Ȯ": "O",
            "Ȱ": "O",
            "Ö": "O",
            "Ȫ": "O",
            "Ỏ": "O",
            "Ő": "O",
            "Ǒ": "O",
            "Ȍ": "O",
            "Ȏ": "O",
            "Ơ": "O",
            "Ờ": "O",
            "Ớ": "O",
            "Ỡ": "O",
            "Ở": "O",
            "Ợ": "O",
            "Ọ": "O",
            "Ộ": "O",
            "Ǫ": "O",
            "Ǭ": "O",
            "Ø": "O",
            "Ǿ": "O",
            "Ɔ": "O",
            "Ɵ": "O",
            "Ꝋ": "O",
            "Ꝍ": "O",
            "Ƣ": "OI",
            "Ꝏ": "OO",
            "Ȣ": "OU",
            "Ⓟ": "P",
            "Ｐ": "P",
            "Ṕ": "P",
            "Ṗ": "P",
            "Ƥ": "P",
            "Ᵽ": "P",
            "Ꝑ": "P",
            "Ꝓ": "P",
            "Ꝕ": "P",
            "Ⓠ": "Q",
            "Ｑ": "Q",
            "Ꝗ": "Q",
            "Ꝙ": "Q",
            "Ɋ": "Q",
            "Ⓡ": "R",
            "Ｒ": "R",
            "Ŕ": "R",
            "Ṙ": "R",
            "Ř": "R",
            "Ȑ": "R",
            "Ȓ": "R",
            "Ṛ": "R",
            "Ṝ": "R",
            "Ŗ": "R",
            "Ṟ": "R",
            "Ɍ": "R",
            "Ɽ": "R",
            "Ꝛ": "R",
            "Ꞧ": "R",
            "Ꞃ": "R",
            "Ⓢ": "S",
            "Ｓ": "S",
            "ẞ": "S",
            "Ś": "S",
            "Ṥ": "S",
            "Ŝ": "S",
            "Ṡ": "S",
            "Š": "S",
            "Ṧ": "S",
            "Ṣ": "S",
            "Ṩ": "S",
            "Ș": "S",
            "Ş": "S",
            "Ȿ": "S",
            "Ꞩ": "S",
            "Ꞅ": "S",
            "Ⓣ": "T",
            "Ｔ": "T",
            "Ṫ": "T",
            "Ť": "T",
            "Ṭ": "T",
            "Ț": "T",
            "Ţ": "T",
            "Ṱ": "T",
            "Ṯ": "T",
            "Ŧ": "T",
            "Ƭ": "T",
            "Ʈ": "T",
            "Ⱦ": "T",
            "Ꞇ": "T",
            "Ꜩ": "TZ",
            "Ⓤ": "U",
            "Ｕ": "U",
            "Ù": "U",
            "Ú": "U",
            "Û": "U",
            "Ũ": "U",
            "Ṹ": "U",
            "Ū": "U",
            "Ṻ": "U",
            "Ŭ": "U",
            "Ü": "U",
            "Ǜ": "U",
            "Ǘ": "U",
            "Ǖ": "U",
            "Ǚ": "U",
            "Ủ": "U",
            "Ů": "U",
            "Ű": "U",
            "Ǔ": "U",
            "Ȕ": "U",
            "Ȗ": "U",
            "Ư": "U",
            "Ừ": "U",
            "Ứ": "U",
            "Ữ": "U",
            "Ử": "U",
            "Ự": "U",
            "Ụ": "U",
            "Ṳ": "U",
            "Ų": "U",
            "Ṷ": "U",
            "Ṵ": "U",
            "Ʉ": "U",
            "Ⓥ": "V",
            "Ｖ": "V",
            "Ṽ": "V",
            "Ṿ": "V",
            "Ʋ": "V",
            "Ꝟ": "V",
            "Ʌ": "V",
            "Ꝡ": "VY",
            "Ⓦ": "W",
            "Ｗ": "W",
            "Ẁ": "W",
            "Ẃ": "W",
            "Ŵ": "W",
            "Ẇ": "W",
            "Ẅ": "W",
            "Ẉ": "W",
            "Ⱳ": "W",
            "Ⓧ": "X",
            "Ｘ": "X",
            "Ẋ": "X",
            "Ẍ": "X",
            "Ⓨ": "Y",
            "Ｙ": "Y",
            "Ỳ": "Y",
            "Ý": "Y",
            "Ŷ": "Y",
            "Ỹ": "Y",
            "Ȳ": "Y",
            "Ẏ": "Y",
            "Ÿ": "Y",
            "Ỷ": "Y",
            "Ỵ": "Y",
            "Ƴ": "Y",
            "Ɏ": "Y",
            "Ỿ": "Y",
            "Ⓩ": "Z",
            "Ｚ": "Z",
            "Ź": "Z",
            "Ẑ": "Z",
            "Ż": "Z",
            "Ž": "Z",
            "Ẓ": "Z",
            "Ẕ": "Z",
            "Ƶ": "Z",
            "Ȥ": "Z",
            "Ɀ": "Z",
            "Ⱬ": "Z",
            "Ꝣ": "Z",
            "ⓐ": "a",
            "ａ": "a",
            "ẚ": "a",
            "à": "a",
            "á": "a",
            "â": "a",
            "ầ": "a",
            "ấ": "a",
            "ẫ": "a",
            "ẩ": "a",
            "ã": "a",
            "ā": "a",
            "ă": "a",
            "ằ": "a",
            "ắ": "a",
            "ẵ": "a",
            "ẳ": "a",
            "ȧ": "a",
            "ǡ": "a",
            "ä": "a",
            "ǟ": "a",
            "ả": "a",
            "å": "a",
            "ǻ": "a",
            "ǎ": "a",
            "ȁ": "a",
            "ȃ": "a",
            "ạ": "a",
            "ậ": "a",
            "ặ": "a",
            "ḁ": "a",
            "ą": "a",
            "ⱥ": "a",
            "ɐ": "a",
            "ꜳ": "aa",
            "æ": "ae",
            "ǽ": "ae",
            "ǣ": "ae",
            "ꜵ": "ao",
            "ꜷ": "au",
            "ꜹ": "av",
            "ꜻ": "av",
            "ꜽ": "ay",
            "ⓑ": "b",
            "ｂ": "b",
            "ḃ": "b",
            "ḅ": "b",
            "ḇ": "b",
            "ƀ": "b",
            "ƃ": "b",
            "ɓ": "b",
            "ⓒ": "c",
            "ｃ": "c",
            "ć": "c",
            "ĉ": "c",
            "ċ": "c",
            "č": "c",
            "ç": "c",
            "ḉ": "c",
            "ƈ": "c",
            "ȼ": "c",
            "ꜿ": "c",
            "ↄ": "c",
            "ⓓ": "d",
            "ｄ": "d",
            "ḋ": "d",
            "ď": "d",
            "ḍ": "d",
            "ḑ": "d",
            "ḓ": "d",
            "ḏ": "d",
            "đ": "d",
            "ƌ": "d",
            "ɖ": "d",
            "ɗ": "d",
            "ꝺ": "d",
            "ǳ": "dz",
            "ǆ": "dz",
            "ⓔ": "e",
            "ｅ": "e",
            "è": "e",
            "é": "e",
            "ê": "e",
            "ề": "e",
            "ế": "e",
            "ễ": "e",
            "ể": "e",
            "ẽ": "e",
            "ē": "e",
            "ḕ": "e",
            "ḗ": "e",
            "ĕ": "e",
            "ė": "e",
            "ë": "e",
            "ẻ": "e",
            "ě": "e",
            "ȅ": "e",
            "ȇ": "e",
            "ẹ": "e",
            "ệ": "e",
            "ȩ": "e",
            "ḝ": "e",
            "ę": "e",
            "ḙ": "e",
            "ḛ": "e",
            "ɇ": "e",
            "ɛ": "e",
            "ǝ": "e",
            "ⓕ": "f",
            "ｆ": "f",
            "ḟ": "f",
            "ƒ": "f",
            "ꝼ": "f",
            "ⓖ": "g",
            "ｇ": "g",
            "ǵ": "g",
            "ĝ": "g",
            "ḡ": "g",
            "ğ": "g",
            "ġ": "g",
            "ǧ": "g",
            "ģ": "g",
            "ǥ": "g",
            "ɠ": "g",
            "ꞡ": "g",
            "ᵹ": "g",
            "ꝿ": "g",
            "ⓗ": "h",
            "ｈ": "h",
            "ĥ": "h",
            "ḣ": "h",
            "ḧ": "h",
            "ȟ": "h",
            "ḥ": "h",
            "ḩ": "h",
            "ḫ": "h",
            "ẖ": "h",
            "ħ": "h",
            "ⱨ": "h",
            "ⱶ": "h",
            "ɥ": "h",
            "ƕ": "hv",
            "ⓘ": "i",
            "ｉ": "i",
            "ì": "i",
            "í": "i",
            "î": "i",
            "ĩ": "i",
            "ī": "i",
            "ĭ": "i",
            "ï": "i",
            "ḯ": "i",
            "ỉ": "i",
            "ǐ": "i",
            "ȉ": "i",
            "ȋ": "i",
            "ị": "i",
            "į": "i",
            "ḭ": "i",
            "ɨ": "i",
            "ı": "i",
            "ⓙ": "j",
            "ｊ": "j",
            "ĵ": "j",
            "ǰ": "j",
            "ɉ": "j",
            "ⓚ": "k",
            "ｋ": "k",
            "ḱ": "k",
            "ǩ": "k",
            "ḳ": "k",
            "ķ": "k",
            "ḵ": "k",
            "ƙ": "k",
            "ⱪ": "k",
            "ꝁ": "k",
            "ꝃ": "k",
            "ꝅ": "k",
            "ꞣ": "k",
            "ⓛ": "l",
            "ｌ": "l",
            "ŀ": "l",
            "ĺ": "l",
            "ľ": "l",
            "ḷ": "l",
            "ḹ": "l",
            "ļ": "l",
            "ḽ": "l",
            "ḻ": "l",
            "ſ": "l",
            "ł": "l",
            "ƚ": "l",
            "ɫ": "l",
            "ⱡ": "l",
            "ꝉ": "l",
            "ꞁ": "l",
            "ꝇ": "l",
            "ǉ": "lj",
            "ⓜ": "m",
            "ｍ": "m",
            "ḿ": "m",
            "ṁ": "m",
            "ṃ": "m",
            "ɱ": "m",
            "ɯ": "m",
            "ⓝ": "n",
            "ｎ": "n",
            "ǹ": "n",
            "ń": "n",
            "ñ": "n",
            "ṅ": "n",
            "ň": "n",
            "ṇ": "n",
            "ņ": "n",
            "ṋ": "n",
            "ṉ": "n",
            "ƞ": "n",
            "ɲ": "n",
            "ŉ": "n",
            "ꞑ": "n",
            "ꞥ": "n",
            "ǌ": "nj",
            "ⓞ": "o",
            "ｏ": "o",
            "ò": "o",
            "ó": "o",
            "ô": "o",
            "ồ": "o",
            "ố": "o",
            "ỗ": "o",
            "ổ": "o",
            "õ": "o",
            "ṍ": "o",
            "ȭ": "o",
            "ṏ": "o",
            "ō": "o",
            "ṑ": "o",
            "ṓ": "o",
            "ŏ": "o",
            "ȯ": "o",
            "ȱ": "o",
            "ö": "o",
            "ȫ": "o",
            "ỏ": "o",
            "ő": "o",
            "ǒ": "o",
            "ȍ": "o",
            "ȏ": "o",
            "ơ": "o",
            "ờ": "o",
            "ớ": "o",
            "ỡ": "o",
            "ở": "o",
            "ợ": "o",
            "ọ": "o",
            "ộ": "o",
            "ǫ": "o",
            "ǭ": "o",
            "ø": "o",
            "ǿ": "o",
            "ɔ": "o",
            "ꝋ": "o",
            "ꝍ": "o",
            "ɵ": "o",
            "ƣ": "oi",
            "ȣ": "ou",
            "ꝏ": "oo",
            "ⓟ": "p",
            "ｐ": "p",
            "ṕ": "p",
            "ṗ": "p",
            "ƥ": "p",
            "ᵽ": "p",
            "ꝑ": "p",
            "ꝓ": "p",
            "ꝕ": "p",
            "ⓠ": "q",
            "ｑ": "q",
            "ɋ": "q",
            "ꝗ": "q",
            "ꝙ": "q",
            "ⓡ": "r",
            "ｒ": "r",
            "ŕ": "r",
            "ṙ": "r",
            "ř": "r",
            "ȑ": "r",
            "ȓ": "r",
            "ṛ": "r",
            "ṝ": "r",
            "ŗ": "r",
            "ṟ": "r",
            "ɍ": "r",
            "ɽ": "r",
            "ꝛ": "r",
            "ꞧ": "r",
            "ꞃ": "r",
            "ⓢ": "s",
            "ｓ": "s",
            "ß": "s",
            "ś": "s",
            "ṥ": "s",
            "ŝ": "s",
            "ṡ": "s",
            "š": "s",
            "ṧ": "s",
            "ṣ": "s",
            "ṩ": "s",
            "ș": "s",
            "ş": "s",
            "ȿ": "s",
            "ꞩ": "s",
            "ꞅ": "s",
            "ẛ": "s",
            "ⓣ": "t",
            "ｔ": "t",
            "ṫ": "t",
            "ẗ": "t",
            "ť": "t",
            "ṭ": "t",
            "ț": "t",
            "ţ": "t",
            "ṱ": "t",
            "ṯ": "t",
            "ŧ": "t",
            "ƭ": "t",
            "ʈ": "t",
            "ⱦ": "t",
            "ꞇ": "t",
            "ꜩ": "tz",
            "ⓤ": "u",
            "ｕ": "u",
            "ù": "u",
            "ú": "u",
            "û": "u",
            "ũ": "u",
            "ṹ": "u",
            "ū": "u",
            "ṻ": "u",
            "ŭ": "u",
            "ü": "u",
            "ǜ": "u",
            "ǘ": "u",
            "ǖ": "u",
            "ǚ": "u",
            "ủ": "u",
            "ů": "u",
            "ű": "u",
            "ǔ": "u",
            "ȕ": "u",
            "ȗ": "u",
            "ư": "u",
            "ừ": "u",
            "ứ": "u",
            "ữ": "u",
            "ử": "u",
            "ự": "u",
            "ụ": "u",
            "ṳ": "u",
            "ų": "u",
            "ṷ": "u",
            "ṵ": "u",
            "ʉ": "u",
            "ⓥ": "v",
            "ｖ": "v",
            "ṽ": "v",
            "ṿ": "v",
            "ʋ": "v",
            "ꝟ": "v",
            "ʌ": "v",
            "ꝡ": "vy",
            "ⓦ": "w",
            "ｗ": "w",
            "ẁ": "w",
            "ẃ": "w",
            "ŵ": "w",
            "ẇ": "w",
            "ẅ": "w",
            "ẘ": "w",
            "ẉ": "w",
            "ⱳ": "w",
            "ⓧ": "x",
            "ｘ": "x",
            "ẋ": "x",
            "ẍ": "x",
            "ⓨ": "y",
            "ｙ": "y",
            "ỳ": "y",
            "ý": "y",
            "ŷ": "y",
            "ỹ": "y",
            "ȳ": "y",
            "ẏ": "y",
            "ÿ": "y",
            "ỷ": "y",
            "ẙ": "y",
            "ỵ": "y",
            "ƴ": "y",
            "ɏ": "y",
            "ỿ": "y",
            "ⓩ": "z",
            "ｚ": "z",
            "ź": "z",
            "ẑ": "z",
            "ż": "z",
            "ž": "z",
            "ẓ": "z",
            "ẕ": "z",
            "ƶ": "z",
            "ȥ": "z",
            "ɀ": "z",
            "ⱬ": "z",
            "ꝣ": "z",
            "Ά": "Α",
            "Έ": "Ε",
            "Ή": "Η",
            "Ί": "Ι",
            "Ϊ": "Ι",
            "Ό": "Ο",
            "Ύ": "Υ",
            "Ϋ": "Υ",
            "Ώ": "Ω",
            "ά": "α",
            "έ": "ε",
            "ή": "η",
            "ί": "ι",
            "ϊ": "ι",
            "ΐ": "ι",
            "ό": "ο",
            "ύ": "υ",
            "ϋ": "υ",
            "ΰ": "υ",
            "ω": "ω",
            "ς": "σ"
        };
        return t.replace(/[^\u0000-\u007E]/g, function (t) {
            return e[t] || t
        })
    };
    var p = t.fn.tokenize2;
    t.fn.tokenize2 = function (o) {
        var i = [];
        return this.filter("select").each(function () {
            var n = t(this), s = n.data("tokenize2"), r = "object" == typeof o && o;
            s || n.data("tokenize2", new e(this, r)), i.push(n.data("tokenize2"))
        }), i.length > 1 ? i : i[0]
    }, t.fn.tokenize2.Constructor = e, t.fn.tokenize2.noConflict = function () {
        return t.fn.tokenize2 = p, this
    }
});