window.spokenWord = function(t) {
  function e(r) {
    if (n[r]) return n[r].exports;
    var o = n[r] = {
      i: r,
      l: !1,
      exports: {}
    };
    return t[r].call(o.exports, o, o.exports, e), o.l = !0, o.exports
  }
  var n = {};
  return e.m = t, e.c = n, e.d = function(t, n, r) {
    e.o(t, n) || Object.defineProperty(t, n, {
      configurable: !1,
      enumerable: !0,
      get: r
    })
  }, e.n = function(t) {
    var n = t && t.__esModule ? function() {
      return t.default
    } : function() {
      return t
    };
    return e.d(n, "a", n), n
  }, e.o = function(t, e) {
    return Object.prototype.hasOwnProperty.call(t, e)
  }, e.p = "", e(e.s = 27)
}([function(t, e, n) {
  var r = n(18),
    o = "object" == typeof self && self && self.Object === Object && self,
    i = r || o || Function("return this")();
  t.exports = i
}, function(t, e, n) {
  var r = n(70),
    o = n(76);
  t.exports = function(t, e) {
    var n = o(t, e);
    return r(n) ? n : void 0
  }
}, function(t, e, n) {
  function r(t) {
    var e = -1,
      n = null == t ? 0 : t.length;
    for (this.clear(); ++e < n;) {
      var r = t[e];
      this.set(r[0], r[1])
    }
  }
  var o = n(60),
    i = n(61),
    a = n(62),
    s = n(63),
    u = n(64);
  r.prototype.clear = o, r.prototype.delete = i, r.prototype.get = a, r.prototype.has = s, r.prototype.set = u, t.exports = r
}, function(t, e, n) {
  var r = n(16);
  t.exports = function(t, e) {
    for (var n = t.length; n--;)
      if (r(t[n][0], e)) return n;
    return -1
  }
}, function(t, e, n) {
  var r = n(12),
    o = n(72),
    i = n(73),
    a = "[object Null]",
    s = "[object Undefined]",
    u = r ? r.toStringTag : void 0;
  t.exports = function(t) {
    return null == t ? void 0 === t ? s : a : u && u in Object(t) ? o(t) : i(t)
  }
}, function(t, e, n) {
  var r = n(1)(Object, "create");
  t.exports = r
}, function(t, e, n) {
  var r = n(85);
  t.exports = function(t, e) {
    var n = t.__data__;
    return r(e) ? n["string" == typeof e ? "string" : "hash"] : n.map
  }
}, function(t, e) {
  t.exports = function(t) {
    return null != t && "object" == typeof t
  }
}, function(t, e, n) {
  "use strict";
  Object.defineProperty(e, "__esModule", {
      value: !0
    }),
    function(t) {
      function r() {
        return null
      }

      function o(t, e, n) {
        var r = e && e._preactCompatRendered && e._preactCompatRendered.base;
        r && r.parentNode !== e && (r = null), !r && e && (r = e.firstElementChild);
        for (var o = e.childNodes.length; o--;) e.childNodes[o] !== r && e.removeChild(e.childNodes[o]);
        var i = Object(C.e)(t, e, r);
        return e && (e._preactCompatRendered = i && (i._component || {
          base: i
        })), "function" == typeof n && n(), i && i._component || i
      }

      function i(t, e, n, r) {
        var i = o(Object(C.c)(z, {
            context: t.context
          }, e), n),
          a = i._component || i.base;
        return r && r.call(a, i), a
      }

      function a(t) {
        var e = t._preactCompatRendered && t._preactCompatRendered.base;
        return !(!e || e.parentNode !== t) && (Object(C.e)(Object(C.c)(r), t, e), !0)
      }

      function s(t) {
        return l.bind(null, t)
      }

      function u(t, e) {
        for (var n = e || 0; n < t.length; n++) {
          var r = t[n];
          Array.isArray(r) ? u(r) : r && "object" == typeof r && !h(r) && (r.props && r.type || r.attributes && r.nodeName || r.children) && (t[n] = l(r.type || r.nodeName, r.props || r.attributes, r.children))
        }
      }

      function c(t) {
        var e = t[R];
        return e ? !0 === e ? t : e : (e = function(t) {
          return m({
            displayName: t.displayName || t.name,
            render: function() {
              return t(this.props, this.context)
            }
          })
        }(t), Object.defineProperty(e, R, {
          configurable: !0,
          value: !0
        }), e.displayName = t.displayName, e.propTypes = t.propTypes, e.defaultProps = t.defaultProps, Object.defineProperty(t, R, {
          configurable: !0,
          value: e
        }), e)
      }

      function l() {
        for (var t = [], e = arguments.length; e--;) t[e] = arguments[e];
        return u(t, 2), f(C.c.apply(void 0, t))
      }

      function f(t) {
        t.preactCompatNormalized = !0,
          function(t) {
            var e = t.attributes || (t.attributes = {});
            H.enumerable = "className" in e, e.className && (e.class = e.className);
            Object.defineProperty(e, "className", H)
          }(t),
          function(t) {
            return "function" == typeof t && !(t.prototype && t.prototype.render)
          }(t.nodeName) && (t.nodeName = c(t.nodeName));
        var e = t.attributes.ref,
          n = e && typeof e;
        return !$ || "string" !== n && "number" !== n || (t.attributes.ref = function(t, e) {
            return e._refProxies[t] || (e._refProxies[t] = function(n) {
              e && e.refs && (e.refs[t] = n, null === n && (delete e._refProxies[t], e = null))
            })
          }(e, $)),
          function(t) {
            var e = t.nodeName,
              n = t.attributes;
            if (!n || "string" != typeof e) return;
            var r = {};
            for (var o in n) r[o.toLowerCase()] = o;
            r.ondoubleclick && (n.ondblclick = n[r.ondoubleclick], delete n[r.ondoubleclick]);
            if (r.onchange && ("textarea" === e || "input" === e.toLowerCase() && !/^fil|che|rad/i.test(n.type))) {
              var i = r.oninput || "oninput";
              n[i] || (n[i] = _([n[i], n[r.onchange]]), delete n[r.onchange])
            }
          }(t), t
      }

      function p(t, e) {
        for (var n = [], r = arguments.length - 2; r-- > 0;) n[r] = arguments[r + 2];
        if (!h(t)) return t;
        var o = t.attributes || t.props,
          i = [Object(C.c)(t.nodeName || t.type, o, t.children || o && o.children), e];
        return n && n.length ? i.push(n) : e && e.children && i.push(e.children), f(C.b.apply(void 0, i))
      }

      function h(t) {
        return t && (t instanceof M || t.$$typeof === A)
      }

      function d(t, e) {
        for (var n = arguments, r = 1, o = void 0; r < arguments.length; r++)
          if (o = n[r])
            for (var i in o) o.hasOwnProperty(i) && (t[i] = o[i]);
        return t
      }

      function y(t, e) {
        for (var n in t)
          if (!(n in e)) return !0;
        for (var r in e)
          if (t[r] !== e[r]) return !0;
        return !1
      }

      function v(t) {
        return t && t.base || t
      }

      function g() {}

      function m(t) {
        function e(t, e) {
          ! function(t) {
            for (var e in t) {
              var n = t[e];
              "function" != typeof n || n.__bound || I.hasOwnProperty(e) || ((t[e] = n.bind(t)).__bound = !0)
            }
          }(this), O.call(this, t, e, L), x.call(this, t, e)
        }
        return (t = d({
          constructor: e
        }, t)).mixins && function(t, e) {
          for (var n in e) e.hasOwnProperty(n) && (t[n] = _(e[n].concat(t[n] || W), "getDefaultProps" === n || "getInitialState" === n || "getChildContext" === n))
        }(t, function(t) {
          for (var e = {}, n = 0; n < t.length; n++) {
            var r = t[n];
            for (var o in r) r.hasOwnProperty(o) && "function" == typeof r[o] && (e[o] || (e[o] = [])).push(r[o])
          }
          return e
        }(t.mixins)), t.statics && d(e, t.statics), t.propTypes && (e.propTypes = t.propTypes), t.defaultProps && (e.defaultProps = t.defaultProps), t.getDefaultProps && (e.defaultProps = t.getDefaultProps()), g.prototype = O.prototype, e.prototype = d(new g, t), e.displayName = t.displayName || "Component", e
      }

      function b(t, e, n) {
        if ("string" == typeof e && (e = t.constructor.prototype[e]), "function" == typeof e) return e.apply(t, n)
      }

      function _(t, e) {
        return function() {
          for (var n, r = arguments, o = 0; o < t.length; o++) {
            var i = b(this, t[o], r);
            if (e && null != i) {
              n || (n = {});
              for (var a in i) i.hasOwnProperty(a) && (n[a] = i[a])
            } else void 0 !== i && (n = i)
          }
          return n
        }
      }

      function x(t, e) {
        k.call(this, t, e), this.componentWillReceiveProps = _([k, this.componentWillReceiveProps || "componentWillReceiveProps"]), this.render = _([k, w, this.render || "render", S])
      }

      function k(t, e) {
        if (t) {
          var n = t.children;
          if (n && Array.isArray(n) && 1 === n.length && ("string" == typeof n[0] || "function" == typeof n[0] || n[0] instanceof M) && (t.children = n[0], t.children && "object" == typeof t.children && (t.children.length = 1, t.children[0] = t.children)), F) {
            var r = "function" == typeof this ? this : this.constructor,
              o = this.propTypes || r.propTypes,
              i = this.displayName || r.name;
            o && E.a.checkPropTypes(o, t, "prop", i)
          }
        }
      }

      function w(t) {
        $ = this
      }

      function S() {
        $ === this && ($ = null)
      }

      function O(t, e, n) {
        C.a.call(this, t, e), this.state = this.getInitialState ? this.getInitialState() : {}, this.refs = {}, this._refProxies = {}, n !== L && x.call(this, t, e)
      }

      function P(t, e) {
        O.call(this, t, e)
      }
      n.d(e, "version", function() {
        return N
      }), n.d(e, "DOM", function() {
        return B
      }), n.d(e, "Children", function() {
        return q
      }), n.d(e, "render", function() {
        return o
      }), n.d(e, "createClass", function() {
        return m
      }), n.d(e, "createFactory", function() {
        return s
      }), n.d(e, "createElement", function() {
        return l
      }), n.d(e, "cloneElement", function() {
        return p
      }), n.d(e, "isValidElement", function() {
        return h
      }), n.d(e, "findDOMNode", function() {
        return v
      }), n.d(e, "unmountComponentAtNode", function() {
        return a
      }), n.d(e, "Component", function() {
        return O
      }), n.d(e, "PureComponent", function() {
        return P
      }), n.d(e, "unstable_renderSubtreeIntoContainer", function() {
        return i
      }), n.d(e, "__spread", function() {
        return d
      });
      var j = n(9),
        E = n.n(j),
        C = n(35);
      n.d(e, "PropTypes", function() {
        return E.a
      });
      var N = "15.1.0",
        T = "a abbr address area article aside audio b base bdi bdo big blockquote body br button canvas caption cite code col colgroup data datalist dd del details dfn dialog div dl dt em embed fieldset figcaption figure footer form h1 h2 h3 h4 h5 h6 head header hgroup hr html i iframe img input ins kbd keygen label legend li link main map mark menu menuitem meta meter nav noscript object ol optgroup option output p param picture pre progress q rp rt ruby s samp script section select small source span strong style sub summary sup table tbody td textarea tfoot th thead time title tr track u ul var video wbr circle clipPath defs ellipse g image line linearGradient mask path pattern polygon polyline radialGradient rect stop svg text tspan".split(" "),
        A = "undefined" != typeof Symbol && Symbol.for && Symbol.for("react.element") || 60103,
        R = "undefined" != typeof Symbol ? Symbol.for("__preactCompatWrapper") : "__preactCompatWrapper",
        I = {
          constructor: 1,
          render: 1,
          shouldComponentUpdate: 1,
          componentWillReceiveProps: 1,
          componentWillUpdate: 1,
          componentDidUpdate: 1,
          componentWillMount: 1,
          componentDidMount: 1,
          componentWillUnmount: 1,
          componentDidUnmount: 1
        },
        D = /^(?:accent|alignment|arabic|baseline|cap|clip|color|fill|flood|font|glyph|horiz|marker|overline|paint|stop|strikethrough|stroke|text|underline|unicode|units|v|vector|vert|word|writing|x)[A-Z]/,
        L = {},
        F = void 0 === t || !t.env || !1,
        M = Object(C.c)("a", null).constructor;
      M.prototype.$$typeof = A, M.prototype.preactCompatUpgraded = !1, M.prototype.preactCompatNormalized = !1, Object.defineProperty(M.prototype, "type", {
        get: function() {
          return this.nodeName
        },
        set: function(t) {
          this.nodeName = t
        },
        configurable: !0
      }), Object.defineProperty(M.prototype, "props", {
        get: function() {
          return this.attributes
        },
        set: function(t) {
          this.attributes = t
        },
        configurable: !0
      });
      var U = C.d.event;
      C.d.event = function(t) {
        return U && (t = U(t)), t.persist = Object, t.nativeEvent = t, t
      };
      var V = C.d.vnode;
      C.d.vnode = function(t) {
        if (!t.preactCompatUpgraded) {
          t.preactCompatUpgraded = !0;
          var e = t.nodeName,
            n = t.attributes = d({}, t.attributes);
          "function" == typeof e ? (!0 === e[R] || e.prototype && "isReactComponent" in e.prototype) && (t.children && "" === String(t.children) && (t.children = void 0), t.children && (n.children = t.children), t.preactCompatNormalized || f(t), function(t) {
            var e = t.nodeName,
              n = t.attributes;
            t.attributes = {}, e.defaultProps && d(t.attributes, e.defaultProps), n && d(t.attributes, n)
          }(t)) : (t.children && "" === String(t.children) && (t.children = void 0), t.children && (n.children = t.children), n.defaultValue && (n.value || 0 === n.value || (n.value = n.defaultValue), delete n.defaultValue), function(t, e) {
            var n, r, o;
            if (e) {
              for (o in e)
                if (n = D.test(o)) break;
              if (n) {
                r = t.attributes = {};
                for (o in e) e.hasOwnProperty(o) && (r[D.test(o) ? o.replace(/([A-Z0-9])/, "-$1").toLowerCase() : o] = e[o])
              }
            }
          }(t, n))
        }
        V && V(t)
      };
      var z = function() {};
      z.prototype.getChildContext = function() {
        return this.props.context
      }, z.prototype.render = function(t) {
        return t.children[0]
      };
      for (var $, W = [], q = {
          map: function(t, e, n) {
            return null == t ? null : (t = q.toArray(t), n && n !== t && (e = e.bind(n)), t.map(e))
          },
          forEach: function(t, e, n) {
            if (null == t) return null;
            t = q.toArray(t), n && n !== t && (e = e.bind(n)), t.forEach(e)
          },
          count: function(t) {
            return t && t.length || 0
          },
          only: function(t) {
            if (1 !== (t = q.toArray(t)).length) throw new Error("Children.only() expects only one child.");
            return t[0]
          },
          toArray: function(t) {
            return null == t ? [] : W.concat(t)
          }
        }, B = {}, G = T.length; G--;) B[T[G]] = s(T[G]);
      var H = {
        configurable: !0,
        get: function() {
          return this.class
        },
        set: function(t) {
          this.class = t
        }
      };
      d(O.prototype = new C.a, {
        constructor: O,
        isReactComponent: {},
        replaceState: function(t, e) {
          this.setState(t, e);
          for (var n in this.state) n in t || delete this.state[n]
        },
        getDOMNode: function() {
          return this.base
        },
        isMounted: function() {
          return !!this.base
        }
      }), g.prototype = O.prototype, (P.prototype = new g).isPureReactComponent = !0, P.prototype.shouldComponentUpdate = function(t, e) {
        return y(this.props, t) || y(this.state, e)
      };
      var Q = {
        version: N,
        DOM: B,
        PropTypes: E.a,
        Children: q,
        render: o,
        createClass: m,
        createFactory: s,
        createElement: l,
        cloneElement: p,
        isValidElement: h,
        findDOMNode: v,
        unmountComponentAtNode: a,
        Component: O,
        PureComponent: P,
        unstable_renderSubtreeIntoContainer: i,
        __spread: d
      };
      e.default = Q
    }.call(e, n(30))
}, function(t, e, n) {
  t.exports = n(31)()
}, function(t, e, n) {
  "use strict";
  var r = n(44)();
  t.exports = function(t) {
    return t !== r && null !== t
  }
}, function(t, e, n) {
  var r = n(1)(n(0), "Map");
  t.exports = r
}, function(t, e, n) {
  var r = n(0).Symbol;
  t.exports = r
}, function(t, e) {
  var n = Array.isArray;
  t.exports = n
}, function(t, e, n) {
  "use strict";

  function r(t) {
    a = new i.default(t)
  }

  function o() {
    return a || r({
      "": {}
    }), a
  }
  Object.defineProperty(e, "__esModule", {
    value: !0
  }), e.sprintf = void 0, e.setLocaleData = r, e.getI18n = o, e.__ = function(t) {
    return o().gettext(t)
  }, e._x = function(t, e) {
    return o().pgettext(e, t)
  }, e._n = function(t, e, n) {
    return o().ngettext(t, e, n)
  }, e._nx = function(t, e, n, r) {
    return o().npgettext(r, t, e, n)
  };
  var i = function(t) {
      return t && t.__esModule ? t : {
        default: t
      }
    }(n(28)),
    a = void 0;
  e.sprintf = i.default.sprintf
}, function(t, e, n) {
  "use strict";
  Object.defineProperty(e, "__esModule", {
    value: !0
  }), e.equalRanges = function(t, e) {
    return t.startContainer === e.startContainer && t.startOffset === e.startOffset && t.endContainer === e.endContainer && t.endOffset === e.endOffset
  }, e.uniqueId = function() {
    return ++r
  }, e.scrollElementIntoViewIfNeeded = function(t) {
    var e = t.getBoundingClientRect();
    return !(e.top >= 0 && e.left >= 0 && e.bottom <= document.documentElement.clientHeight && e.right <= document.documentElement.clientWidth || (t.scrollIntoView({
      behavior: "smooth",
      block: e.top < 0 ? "start" : "end"
    }), 0))
  };
  var r = 0
}, function(t, e) {
  t.exports = function(t, e) {
    return t === e || t != t && e != e
  }
}, function(t, e, n) {
  var r = n(4),
    o = n(19),
    i = "[object AsyncFunction]",
    a = "[object Function]",
    s = "[object GeneratorFunction]",
    u = "[object Proxy]";
  t.exports = function(t) {
    if (!o(t)) return !1;
    var e = r(t);
    return e == a || e == s || e == i || e == u
  }
}, function(t, e, n) {
  (function(e) {
    var n = "object" == typeof e && e && e.Object === Object && e;
    t.exports = n
  }).call(e, n(71))
}, function(t, e) {
  t.exports = function(t) {
    var e = typeof t;
    return null != t && ("object" == e || "function" == e)
  }
}, function(t, e) {
  var n = Function.prototype.toString;
  t.exports = function(t) {
    if (null != t) {
      try {
        return n.call(t)
      } catch (t) {}
      try {
        return t + ""
      } catch (t) {}
    }
    return ""
  }
}, function(t, e, n) {
  function r(t) {
    var e = -1,
      n = null == t ? 0 : t.length;
    for (this.clear(); ++e < n;) {
      var r = t[e];
      this.set(r[0], r[1])
    }
  }
  var o = n(77),
    i = n(84),
    a = n(86),
    s = n(87),
    u = n(88);
  r.prototype.clear = o, r.prototype.delete = i, r.prototype.get = a, r.prototype.has = s, r.prototype.set = u, t.exports = r
}, function(t, e, n) {
  var r = n(89),
    o = n(92),
    i = n(93),
    a = 1,
    s = 2;
  t.exports = function(t, e, n, u, c, l) {
    var f = n & a,
      p = t.length,
      h = e.length;
    if (p != h && !(f && h > p)) return !1;
    var d = l.get(t);
    if (d && l.get(e)) return d == e;
    var y = -1,
      v = !0,
      g = n & s ? new r : void 0;
    for (l.set(t, e), l.set(e, t); ++y < p;) {
      var m = t[y],
        b = e[y];
      if (u) var _ = f ? u(b, m, y, e, t, l) : u(m, b, y, t, e, l);
      if (void 0 !== _) {
        if (_) continue;
        v = !1;
        break
      }
      if (g) {
        if (!o(e, function(t, e) {
            if (!i(g, e) && (m === t || c(m, t, n, u, l))) return g.push(e)
          })) {
          v = !1;
          break
        }
      } else if (m !== b && !c(m, b, n, u, l)) {
        v = !1;
        break
      }
    }
    return l.delete(t), l.delete(e), v
  }
}, function(t, e, n) {
  (function(t) {
    var r = n(0),
      o = n(110),
      i = "object" == typeof e && e && !e.nodeType && e,
      a = i && "object" == typeof t && t && !t.nodeType && t,
      s = a && a.exports === i ? r.Buffer : void 0,
      u = (s ? s.isBuffer : void 0) || o;
    t.exports = u
  }).call(e, n(24)(t))
}, function(t, e) {
  t.exports = function(t) {
    return t.webpackPolyfill || (t.deprecate = function() {}, t.paths = [], t.children || (t.children = []), Object.defineProperty(t, "loaded", {
      enumerable: !0,
      get: function() {
        return t.l
      }
    }), Object.defineProperty(t, "id", {
      enumerable: !0,
      get: function() {
        return t.i
      }
    }), t.webpackPolyfill = 1), t
  }
}, function(t, e, n) {
  var r = n(112),
    o = n(113),
    i = n(114),
    a = i && i.isTypedArray,
    s = a ? o(a) : r;
  t.exports = s
}, function(t, e) {
  var n = 9007199254740991;
  t.exports = function(t) {
    return "number" == typeof t && t > -1 && t % 1 == 0 && t <= n
  }
}, function(t, e, n) {
  "use strict";

  function r(t) {
    if (Array.isArray(t)) {
      for (var e = 0, n = Array(t.length); e < t.length; e++) n[e] = t[e];
      return n
    }
    return Array.from(t)
  }

  function o(t, e) {
    return t.matches(e) ? [t] : [].concat(r(t.querySelectorAll(e)))
  }

  function i(t) {
    var e = t.element,
      n = t.contentSelector,
      r = t.chunkifyOptions,
      i = t.useDashicons,
      s = o(e, n),
      u = function(t) {
        if (f.has(t)) return "continue";
        var e = new c.default({
          rootElement: t,
          chunkifyOptions: r,
          useDashicons: i,
          utteranceOptions: a()
        });
        f.set(t, e), e.on("change:playing", function(t) {
          if (t) {
            var n = !0,
              r = !1,
              o = void 0;
            try {
              for (var i, a = f.values()[Symbol.iterator](); !(n = (i = a.next()).done); n = !0) {
                var s = i.value;
                s !== e && s.stop()
              }
            } catch (t) {
              r = !0, o = t
            } finally {
              try {
                !n && a.return && a.return()
              } finally {
                if (r) throw o
              }
            }
          }
        }), e.on("sharedStateChange", function(t) {
          localStorage.setItem(l, JSON.stringify(t));
          var n = !0,
            r = !1,
            o = void 0;
          try {
            for (var i, a = f.values()[Symbol.iterator](); !(n = (i = a.next()).done); n = !0) {
              var s = i.value;
              s !== e && s.setState(t)
            }
          } catch (t) {
            r = !0, o = t
          } finally {
            try {
              !n && a.return && a.return()
            } finally {
              if (r) throw o
            }
          }
        }), e.initialize()
      },
      p = !0,
      h = !1,
      d = void 0;
    try {
      for (var y, v = s[Symbol.iterator](); !(p = (y = v.next()).done); p = !0) u(y.value)
    } catch (t) {
      h = !0, d = t
    } finally {
      try {
        !p && v.return && v.return()
      } finally {
        if (h) throw d
      }
    }
  }

  function a() {
    var t = Object.assign({}, p, h);
    if (!localStorage.getItem(l)) return t;
    try {
      var e = JSON.parse(localStorage.getItem(l)),
        n = !0,
        r = !1,
        o = void 0;
      try {
        for (var i, a = Object.keys(p)[Symbol.iterator](); !(n = (i = a.next()).done); n = !0) {
          var s = i.value;
          void 0 !== e[s] && (t[s] = e[s])
        }
      } catch (t) {
        r = !0, o = t
      } finally {
        try {
          !n && a.return && a.return()
        } finally {
          if (r) throw o
        }
      }
    } catch (t) {
      localStorage.removeItem(l)
    }
    return t
  }

  function s(t) {
    var e = o(t.element, t.contentSelector),
      n = !0,
      r = !1,
      i = void 0;
    try {
      for (var a, s = e[Symbol.iterator](); !(n = (a = s.next()).done); n = !0) {
        var u = a.value,
          c = f.get(u);
        c && (c.destroy(), f.delete(u))
      }
    } catch (t) {
      r = !0, i = t
    } finally {
      try {
        !n && s.return && s.return()
      } finally {
        if (r) throw i
      }
    }
  }
  Object.defineProperty(e, "__esModule", {
    value: !0
  }), e.setLocaleData = void 0;
  var u = n(14);
  Object.defineProperty(e, "setLocaleData", {
    enumerable: !0,
    get: function() {
      return u.setLocaleData
    }
  }), e.getInstances = function() {
    return f.values()
  }, e.initialize = function() {
    var t = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : {},
      e = t.rootElement,
      n = t.contentSelector,
      o = void 0 === n ? d : n,
      a = t.useDashicons,
      u = t.chunkifyOptions,
      c = t.defaultUtteranceOptions,
      l = void 0 === c ? p : c,
      f = t.hasSystemSupport,
      v = void 0 === f ? y : f;
    return h = l, new Promise(function(t, n) {
      if ("undefined" != typeof speechSynthesis && "undefined" != typeof SpeechSynthesisUtterance)
        if (v()) {
          var c = new MutationObserver(function(t) {
              var e = !0,
                n = !1,
                c = void 0;
              try {
                for (var l, f = t[Symbol.iterator](); !(e = (l = f.next()).done); e = !0) {
                  var p = l.value,
                    h = !0,
                    d = !1,
                    y = void 0;
                  try {
                    for (var v, g = [].concat(r(p.addedNodes)).filter(function(t) {
                        return t.nodeType === Node.ELEMENT_NODE
                      })[Symbol.iterator](); !(h = (v = g.next()).done); h = !0) i({
                      element: v.value,
                      contentSelector: o,
                      useDashicons: a,
                      chunkifyOptions: u
                    })
                  } catch (t) {
                    d = !0, y = t
                  } finally {
                    try {
                      !h && g.return && g.return()
                    } finally {
                      if (d) throw y
                    }
                  }
                  var m = !0,
                    b = !1,
                    _ = void 0;
                  try {
                    for (var x, k = [].concat(r(p.removedNodes)).filter(function(t) {
                        return t.nodeType === Node.ELEMENT_NODE
                      })[Symbol.iterator](); !(m = (x = k.next()).done); m = !0) s({
                      element: x.value,
                      contentSelector: o
                    })
                  } catch (t) {
                    b = !0, _ = t
                  } finally {
                    try {
                      !m && k.return && k.return()
                    } finally {
                      if (b) throw _
                    }
                  }
                }
              } catch (t) {
                n = !0, c = t
              } finally {
                try {
                  !e && f.return && f.return()
                } finally {
                  if (n) throw c
                }
              }
            }),
            l = function() {
              var n = e || document.body;
              window.addEventListener("unload", function() {
                speechSynthesis.cancel()
              }), i({
                element: n,
                contentSelector: o,
                chunkifyOptions: u,
                useDashicons: a
              }), c.observe(n, {
                childList: !0,
                subtree: !0
              }), t()
            };
          "complete" === document.readyState ? l() : document.addEventListener("DOMContentLoaded", l)
        } else n("system_not_supported");
      else n("speech_synthesis_not_supported")
    })
  };
  var c = function(t) {
      return t && t.__esModule ? t : {
        default: t
      }
    }(n(29)),
    l = "spokenWordState",
    f = new Map,
    p = {
      pitch: 1,
      rate: 1,
      languageVoices: {}
    },
    h = {},
    d = '.hentry .entry-content, .h-entry .e-content, [itemprop="articleBody"]';
  window.addEventListener("storage", function(t) {
    if (l === t.key && t.storageArea === localStorage) {
      var e = !0,
        n = !1,
        r = void 0;
      try {
        for (var o, i = f.values()[Symbol.iterator](); !(e = (o = i.next()).done); e = !0) {
          o.value.setState(a())
        }
      } catch (t) {
        n = !0, r = t
      } finally {
        try {
          !e && i.return && i.return()
        } finally {
          if (n) throw r
        }
      }
    }
  });
  var y = function() {
    return !/\b(Android|iPhone|iPad|iPod)\b/i.test(navigator.userAgent)
  }
}, function(t, e, n) {
  ! function(n, r) {
    function o(t) {
      return h.PF.compile(t || "nplurals=2; plural=(n != 1);")
    }

    function i(t, e) {
      this._key = t, this._i18n = e
    }
    var a = Array.prototype,
      s = Object.prototype,
      u = a.slice,
      c = s.hasOwnProperty,
      l = a.forEach,
      f = {},
      p = {
        forEach: function(t, e, n) {
          var r, o, i;
          if (null !== t)
            if (l && t.forEach === l) t.forEach(e, n);
            else if (t.length === +t.length) {
            for (r = 0, o = t.length; r < o; r++)
              if (r in t && e.call(n, t[r], r, t) === f) return
          } else
            for (i in t)
              if (c.call(t, i) && e.call(n, t[i], i, t) === f) return
        },
        extend: function(t) {
          return this.forEach(u.call(arguments, 1), function(e) {
            for (var n in e) t[n] = e[n]
          }), t
        }
      },
      h = function(t) {
        if (this.defaults = {
            locale_data: {
              messages: {
                "": {
                  domain: "messages",
                  lang: "en",
                  plural_forms: "nplurals=2; plural=(n != 1);"
                }
              }
            },
            domain: "messages",
            debug: !1
          }, this.options = p.extend({}, this.defaults, t), this.textdomain(this.options.domain), t.domain && !this.options.locale_data[this.options.domain]) throw new Error("Text domain set to non-existent domain: `" + t.domain + "`")
      };
    h.context_delimiter = String.fromCharCode(4), p.extend(i.prototype, {
      onDomain: function(t) {
        return this._domain = t, this
      },
      withContext: function(t) {
        return this._context = t, this
      },
      ifPlural: function(t, e) {
        return this._val = t, this._pkey = e, this
      },
      fetch: function(t) {
        return "[object Array]" != {}.toString.call(t) && (t = [].slice.call(arguments, 0)), (t && t.length ? h.sprintf : function(t) {
          return t
        })(this._i18n.dcnpgettext(this._domain, this._context, this._key, this._pkey, this._val), t)
      }
    }), p.extend(h.prototype, {
      translate: function(t) {
        return new i(t, this)
      },
      textdomain: function(t) {
        if (!t) return this._textdomain;
        this._textdomain = t
      },
      gettext: function(t) {
        return this.dcnpgettext.call(this, r, r, t)
      },
      dgettext: function(t, e) {
        return this.dcnpgettext.call(this, t, r, e)
      },
      dcgettext: function(t, e) {
        return this.dcnpgettext.call(this, t, r, e)
      },
      ngettext: function(t, e, n) {
        return this.dcnpgettext.call(this, r, r, t, e, n)
      },
      dngettext: function(t, e, n, o) {
        return this.dcnpgettext.call(this, t, r, e, n, o)
      },
      dcngettext: function(t, e, n, o) {
        return this.dcnpgettext.call(this, t, r, e, n, o)
      },
      pgettext: function(t, e) {
        return this.dcnpgettext.call(this, r, t, e)
      },
      dpgettext: function(t, e, n) {
        return this.dcnpgettext.call(this, t, e, n)
      },
      dcpgettext: function(t, e, n) {
        return this.dcnpgettext.call(this, t, e, n)
      },
      npgettext: function(t, e, n, o) {
        return this.dcnpgettext.call(this, r, t, e, n, o)
      },
      dnpgettext: function(t, e, n, r, o) {
        return this.dcnpgettext.call(this, t, e, n, r, o)
      },
      dcnpgettext: function(t, e, n, r, i) {
        r = r || n, t = t || this._textdomain;
        var a;
        if (!this.options) return (a = new h).dcnpgettext.call(a, void 0, void 0, n, r, i);
        if (!this.options.locale_data) throw new Error("No locale data provided.");
        if (!this.options.locale_data[t]) throw new Error("Domain `" + t + "` was not found.");
        if (!this.options.locale_data[t][""]) throw new Error("No locale meta information provided.");
        if (!n) throw new Error("No translation key found.");
        var s, u, c, l = e ? e + h.context_delimiter + n : n,
          f = this.options.locale_data,
          p = f[t],
          d = (f.messages || this.defaults.locale_data.messages)[""],
          y = p[""].plural_forms || p[""]["Plural-Forms"] || p[""]["plural-forms"] || d.plural_forms || d["Plural-Forms"] || d["plural-forms"];
        if (void 0 === i) c = 0;
        else {
          if ("number" != typeof i && (i = parseInt(i, 10), isNaN(i))) throw new Error("The number that was passed in is not a number.");
          c = o(y)(i)
        }
        if (!p) throw new Error("No domain named `" + t + "` could be found.");
        return !(s = p[l]) || c > s.length ? (this.options.missing_key_callback && this.options.missing_key_callback(l, t), u = [n, r], !0 === this.options.debug && console.log(u[o(y)(i)]), u[o()(i)]) : (u = s[c]) || (u = [n, r])[o()(i)]
      }
    });
    var d = function() {
      function t(t) {
        return Object.prototype.toString.call(t).slice(8, -1).toLowerCase()
      }

      function e(t, e) {
        for (var n = []; e > 0; n[--e] = t);
        return n.join("")
      }
      var n = function() {
        return n.cache.hasOwnProperty(arguments[0]) || (n.cache[arguments[0]] = n.parse(arguments[0])), n.format.call(null, n.cache[arguments[0]], arguments)
      };
      return n.format = function(n, r) {
        var o, i, a, s, u, c, l, f = 1,
          p = n.length,
          h = "",
          y = [];
        for (i = 0; i < p; i++)
          if ("string" === (h = t(n[i]))) y.push(n[i]);
          else if ("array" === h) {
          if ((s = n[i])[2])
            for (o = r[f], a = 0; a < s[2].length; a++) {
              if (!o.hasOwnProperty(s[2][a])) throw d('[sprintf] property "%s" does not exist', s[2][a]);
              o = o[s[2][a]]
            } else o = s[1] ? r[s[1]] : r[f++];
          if (/[^s]/.test(s[8]) && "number" != t(o)) throw d("[sprintf] expecting number but found %s", t(o));
          switch (void 0 !== o && null !== o || (o = ""), s[8]) {
            case "b":
              o = o.toString(2);
              break;
            case "c":
              o = String.fromCharCode(o);
              break;
            case "d":
              o = parseInt(o, 10);
              break;
            case "e":
              o = s[7] ? o.toExponential(s[7]) : o.toExponential();
              break;
            case "f":
              o = s[7] ? parseFloat(o).toFixed(s[7]) : parseFloat(o);
              break;
            case "o":
              o = o.toString(8);
              break;
            case "s":
              o = (o = String(o)) && s[7] ? o.substring(0, s[7]) : o;
              break;
            case "u":
              o = Math.abs(o);
              break;
            case "x":
              o = o.toString(16);
              break;
            case "X":
              o = o.toString(16).toUpperCase()
          }
          o = /[def]/.test(s[8]) && s[3] && o >= 0 ? "+" + o : o, c = s[4] ? "0" == s[4] ? "0" : s[4].charAt(1) : " ", l = s[6] - String(o).length, u = s[6] ? e(c, l) : "", y.push(s[5] ? o + u : u + o)
        }
        return y.join("")
      }, n.cache = {}, n.parse = function(t) {
        for (var e = t, n = [], r = [], o = 0; e;) {
          if (null !== (n = /^[^\x25]+/.exec(e))) r.push(n[0]);
          else if (null !== (n = /^\x25{2}/.exec(e))) r.push("%");
          else {
            if (null === (n = /^\x25(?:([1-9]\d*)\$|\(([^\)]+)\))?(\+)?(0|'[^$])?(-)?(\d+)?(?:\.(\d+))?([b-fosuxX])/.exec(e))) throw "[sprintf] huh?";
            if (n[2]) {
              o |= 1;
              var i = [],
                a = n[2],
                s = [];
              if (null === (s = /^([a-z_][a-z_\d]*)/i.exec(a))) throw "[sprintf] huh?";
              for (i.push(s[1]);
                "" !== (a = a.substring(s[0].length));)
                if (null !== (s = /^\.([a-z_][a-z_\d]*)/i.exec(a))) i.push(s[1]);
                else {
                  if (null === (s = /^\[(\d+)\]/.exec(a))) throw "[sprintf] huh?";
                  i.push(s[1])
                } n[2] = i
            } else o |= 2;
            if (3 === o) throw "[sprintf] mixing positional and named placeholders is not (yet) supported";
            r.push(n)
          }
          e = e.substring(n[0].length)
        }
        return r
      }, n
    }();
    h.parse_plural = function(t, e) {
      return t = t.replace(/n/g, e), h.parse_expression(t)
    }, h.sprintf = function(t, e) {
      return "[object Array]" == {}.toString.call(e) ? function(t, e) {
        return e.unshift(t), d.apply(null, e)
      }(t, [].slice.call(e)) : d.apply(this, [].slice.call(arguments))
    }, h.prototype.sprintf = function() {
      return h.sprintf.apply(this, arguments)
    }, h.PF = {}, h.PF.parse = function(t) {
      var e = h.PF.extractPluralExpr(t);
      return h.PF.parser.parse.call(h.PF.parser, e)
    }, h.PF.compile = function(t) {
      var e = h.PF.parse(t);
      return function(t) {
        return function(t) {
          return !0 === t ? 1 : t || 0
        }(h.PF.interpreter(e)(t))
      }
    }, h.PF.interpreter = function(t) {
      return function(e) {
        switch (t.type) {
          case "GROUP":
            return h.PF.interpreter(t.expr)(e);
          case "TERNARY":
            return h.PF.interpreter(t.expr)(e) ? h.PF.interpreter(t.truthy)(e) : h.PF.interpreter(t.falsey)(e);
          case "OR":
            return h.PF.interpreter(t.left)(e) || h.PF.interpreter(t.right)(e);
          case "AND":
            return h.PF.interpreter(t.left)(e) && h.PF.interpreter(t.right)(e);
          case "LT":
            return h.PF.interpreter(t.left)(e) < h.PF.interpreter(t.right)(e);
          case "GT":
            return h.PF.interpreter(t.left)(e) > h.PF.interpreter(t.right)(e);
          case "LTE":
            return h.PF.interpreter(t.left)(e) <= h.PF.interpreter(t.right)(e);
          case "GTE":
            return h.PF.interpreter(t.left)(e) >= h.PF.interpreter(t.right)(e);
          case "EQ":
            return h.PF.interpreter(t.left)(e) == h.PF.interpreter(t.right)(e);
          case "NEQ":
            return h.PF.interpreter(t.left)(e) != h.PF.interpreter(t.right)(e);
          case "MOD":
            return h.PF.interpreter(t.left)(e) % h.PF.interpreter(t.right)(e);
          case "VAR":
            return e;
          case "NUM":
            return t.val;
          default:
            throw new Error("Invalid Token found.")
        }
      }
    }, h.PF.extractPluralExpr = function(t) {
      t = t.replace(/^\s\s*/, "").replace(/\s\s*$/, ""), /;\s*$/.test(t) || (t = t.concat(";"));
      var e, n = /nplurals\=(\d+);/,
        r = t.match(n),
        o = {};
      if (!(r.length > 1)) throw new Error("nplurals not found in plural_forms string: " + t);
      if (o.nplurals = r[1], t = t.replace(n, ""), !((e = t.match(/plural\=(.*);/)) && e.length > 1)) throw new Error("`plural` expression not found: " + t);
      return e[1]
    }, h.PF.parser = function() {
      var t = {
          trace: function() {},
          yy: {},
          symbols_: {
            error: 2,
            expressions: 3,
            e: 4,
            EOF: 5,
            "?": 6,
            ":": 7,
            "||": 8,
            "&&": 9,
            "<": 10,
            "<=": 11,
            ">": 12,
            ">=": 13,
            "!=": 14,
            "==": 15,
            "%": 16,
            "(": 17,
            ")": 18,
            n: 19,
            NUMBER: 20,
            $accept: 0,
            $end: 1
          },
          terminals_: {
            2: "error",
            5: "EOF",
            6: "?",
            7: ":",
            8: "||",
            9: "&&",
            10: "<",
            11: "<=",
            12: ">",
            13: ">=",
            14: "!=",
            15: "==",
            16: "%",
            17: "(",
            18: ")",
            19: "n",
            20: "NUMBER"
          },
          productions_: [0, [3, 2],
            [4, 5],
            [4, 3],
            [4, 3],
            [4, 3],
            [4, 3],
            [4, 3],
            [4, 3],
            [4, 3],
            [4, 3],
            [4, 3],
            [4, 3],
            [4, 1],
            [4, 1]
          ],
          performAction: function(t, e, n, r, o, i, a) {
            var s = i.length - 1;
            switch (o) {
              case 1:
                return {
                  type: "GROUP", expr: i[s - 1]
                };
              case 2:
                this.$ = {
                  type: "TERNARY",
                  expr: i[s - 4],
                  truthy: i[s - 2],
                  falsey: i[s]
                };
                break;
              case 3:
                this.$ = {
                  type: "OR",
                  left: i[s - 2],
                  right: i[s]
                };
                break;
              case 4:
                this.$ = {
                  type: "AND",
                  left: i[s - 2],
                  right: i[s]
                };
                break;
              case 5:
                this.$ = {
                  type: "LT",
                  left: i[s - 2],
                  right: i[s]
                };
                break;
              case 6:
                this.$ = {
                  type: "LTE",
                  left: i[s - 2],
                  right: i[s]
                };
                break;
              case 7:
                this.$ = {
                  type: "GT",
                  left: i[s - 2],
                  right: i[s]
                };
                break;
              case 8:
                this.$ = {
                  type: "GTE",
                  left: i[s - 2],
                  right: i[s]
                };
                break;
              case 9:
                this.$ = {
                  type: "NEQ",
                  left: i[s - 2],
                  right: i[s]
                };
                break;
              case 10:
                this.$ = {
                  type: "EQ",
                  left: i[s - 2],
                  right: i[s]
                };
                break;
              case 11:
                this.$ = {
                  type: "MOD",
                  left: i[s - 2],
                  right: i[s]
                };
                break;
              case 12:
                this.$ = {
                  type: "GROUP",
                  expr: i[s - 1]
                };
                break;
              case 13:
                this.$ = {
                  type: "VAR"
                };
                break;
              case 14:
                this.$ = {
                  type: "NUM",
                  val: Number(t)
                }
            }
          },
          table: [{
            3: 1,
            4: 2,
            17: [1, 3],
            19: [1, 4],
            20: [1, 5]
          }, {
            1: [3]
          }, {
            5: [1, 6],
            6: [1, 7],
            8: [1, 8],
            9: [1, 9],
            10: [1, 10],
            11: [1, 11],
            12: [1, 12],
            13: [1, 13],
            14: [1, 14],
            15: [1, 15],
            16: [1, 16]
          }, {
            4: 17,
            17: [1, 3],
            19: [1, 4],
            20: [1, 5]
          }, {
            5: [2, 13],
            6: [2, 13],
            7: [2, 13],
            8: [2, 13],
            9: [2, 13],
            10: [2, 13],
            11: [2, 13],
            12: [2, 13],
            13: [2, 13],
            14: [2, 13],
            15: [2, 13],
            16: [2, 13],
            18: [2, 13]
          }, {
            5: [2, 14],
            6: [2, 14],
            7: [2, 14],
            8: [2, 14],
            9: [2, 14],
            10: [2, 14],
            11: [2, 14],
            12: [2, 14],
            13: [2, 14],
            14: [2, 14],
            15: [2, 14],
            16: [2, 14],
            18: [2, 14]
          }, {
            1: [2, 1]
          }, {
            4: 18,
            17: [1, 3],
            19: [1, 4],
            20: [1, 5]
          }, {
            4: 19,
            17: [1, 3],
            19: [1, 4],
            20: [1, 5]
          }, {
            4: 20,
            17: [1, 3],
            19: [1, 4],
            20: [1, 5]
          }, {
            4: 21,
            17: [1, 3],
            19: [1, 4],
            20: [1, 5]
          }, {
            4: 22,
            17: [1, 3],
            19: [1, 4],
            20: [1, 5]
          }, {
            4: 23,
            17: [1, 3],
            19: [1, 4],
            20: [1, 5]
          }, {
            4: 24,
            17: [1, 3],
            19: [1, 4],
            20: [1, 5]
          }, {
            4: 25,
            17: [1, 3],
            19: [1, 4],
            20: [1, 5]
          }, {
            4: 26,
            17: [1, 3],
            19: [1, 4],
            20: [1, 5]
          }, {
            4: 27,
            17: [1, 3],
            19: [1, 4],
            20: [1, 5]
          }, {
            6: [1, 7],
            8: [1, 8],
            9: [1, 9],
            10: [1, 10],
            11: [1, 11],
            12: [1, 12],
            13: [1, 13],
            14: [1, 14],
            15: [1, 15],
            16: [1, 16],
            18: [1, 28]
          }, {
            6: [1, 7],
            7: [1, 29],
            8: [1, 8],
            9: [1, 9],
            10: [1, 10],
            11: [1, 11],
            12: [1, 12],
            13: [1, 13],
            14: [1, 14],
            15: [1, 15],
            16: [1, 16]
          }, {
            5: [2, 3],
            6: [2, 3],
            7: [2, 3],
            8: [2, 3],
            9: [1, 9],
            10: [1, 10],
            11: [1, 11],
            12: [1, 12],
            13: [1, 13],
            14: [1, 14],
            15: [1, 15],
            16: [1, 16],
            18: [2, 3]
          }, {
            5: [2, 4],
            6: [2, 4],
            7: [2, 4],
            8: [2, 4],
            9: [2, 4],
            10: [1, 10],
            11: [1, 11],
            12: [1, 12],
            13: [1, 13],
            14: [1, 14],
            15: [1, 15],
            16: [1, 16],
            18: [2, 4]
          }, {
            5: [2, 5],
            6: [2, 5],
            7: [2, 5],
            8: [2, 5],
            9: [2, 5],
            10: [2, 5],
            11: [2, 5],
            12: [2, 5],
            13: [2, 5],
            14: [2, 5],
            15: [2, 5],
            16: [1, 16],
            18: [2, 5]
          }, {
            5: [2, 6],
            6: [2, 6],
            7: [2, 6],
            8: [2, 6],
            9: [2, 6],
            10: [2, 6],
            11: [2, 6],
            12: [2, 6],
            13: [2, 6],
            14: [2, 6],
            15: [2, 6],
            16: [1, 16],
            18: [2, 6]
          }, {
            5: [2, 7],
            6: [2, 7],
            7: [2, 7],
            8: [2, 7],
            9: [2, 7],
            10: [2, 7],
            11: [2, 7],
            12: [2, 7],
            13: [2, 7],
            14: [2, 7],
            15: [2, 7],
            16: [1, 16],
            18: [2, 7]
          }, {
            5: [2, 8],
            6: [2, 8],
            7: [2, 8],
            8: [2, 8],
            9: [2, 8],
            10: [2, 8],
            11: [2, 8],
            12: [2, 8],
            13: [2, 8],
            14: [2, 8],
            15: [2, 8],
            16: [1, 16],
            18: [2, 8]
          }, {
            5: [2, 9],
            6: [2, 9],
            7: [2, 9],
            8: [2, 9],
            9: [2, 9],
            10: [2, 9],
            11: [2, 9],
            12: [2, 9],
            13: [2, 9],
            14: [2, 9],
            15: [2, 9],
            16: [1, 16],
            18: [2, 9]
          }, {
            5: [2, 10],
            6: [2, 10],
            7: [2, 10],
            8: [2, 10],
            9: [2, 10],
            10: [2, 10],
            11: [2, 10],
            12: [2, 10],
            13: [2, 10],
            14: [2, 10],
            15: [2, 10],
            16: [1, 16],
            18: [2, 10]
          }, {
            5: [2, 11],
            6: [2, 11],
            7: [2, 11],
            8: [2, 11],
            9: [2, 11],
            10: [2, 11],
            11: [2, 11],
            12: [2, 11],
            13: [2, 11],
            14: [2, 11],
            15: [2, 11],
            16: [2, 11],
            18: [2, 11]
          }, {
            5: [2, 12],
            6: [2, 12],
            7: [2, 12],
            8: [2, 12],
            9: [2, 12],
            10: [2, 12],
            11: [2, 12],
            12: [2, 12],
            13: [2, 12],
            14: [2, 12],
            15: [2, 12],
            16: [2, 12],
            18: [2, 12]
          }, {
            4: 30,
            17: [1, 3],
            19: [1, 4],
            20: [1, 5]
          }, {
            5: [2, 2],
            6: [1, 7],
            7: [2, 2],
            8: [1, 8],
            9: [1, 9],
            10: [1, 10],
            11: [1, 11],
            12: [1, 12],
            13: [1, 13],
            14: [1, 14],
            15: [1, 15],
            16: [1, 16],
            18: [2, 2]
          }],
          defaultActions: {
            6: [2, 1]
          },
          parseError: function(t, e) {
            throw new Error(t)
          },
          parse: function(t) {
            function e(t) {
              o.length = o.length - 2 * t, i.length = i.length - t, a.length = a.length - t
            }

            function n() {
              var t;
              return "number" != typeof(t = r.lexer.lex() || 1) && (t = r.symbols_[t] || t), t
            }
            var r = this,
              o = [0],
              i = [null],
              a = [],
              s = this.table,
              u = "",
              c = 0,
              l = 0,
              f = 0;
            this.lexer.setInput(t), this.lexer.yy = this.yy, this.yy.lexer = this.lexer, void 0 === this.lexer.yylloc && (this.lexer.yylloc = {});
            var p = this.lexer.yylloc;
            a.push(p), "function" == typeof this.yy.parseError && (this.parseError = this.yy.parseError);
            for (var h, d, y, v, g, m, b, _, x, k = {};;) {
              if (y = o[o.length - 1], this.defaultActions[y] ? v = this.defaultActions[y] : (null == h && (h = n()), v = s[y] && s[y][h]), void 0 === v || !v.length || !v[0]) {
                if (!f) {
                  x = [];
                  for (m in s[y]) this.terminals_[m] && m > 2 && x.push("'" + this.terminals_[m] + "'");
                  var w = "";
                  w = this.lexer.showPosition ? "Parse error on line " + (c + 1) + ":\n" + this.lexer.showPosition() + "\nExpecting " + x.join(", ") + ", got '" + this.terminals_[h] + "'" : "Parse error on line " + (c + 1) + ": Unexpected " + (1 == h ? "end of input" : "'" + (this.terminals_[h] || h) + "'"), this.parseError(w, {
                    text: this.lexer.match,
                    token: this.terminals_[h] || h,
                    line: this.lexer.yylineno,
                    loc: p,
                    expected: x
                  })
                }
                if (3 == f) {
                  if (1 == h) throw new Error(w || "Parsing halted.");
                  l = this.lexer.yyleng, u = this.lexer.yytext, c = this.lexer.yylineno, p = this.lexer.yylloc, h = n()
                }
                for (; !(2..toString() in s[y]);) {
                  if (0 == y) throw new Error(w || "Parsing halted.");
                  e(1), y = o[o.length - 1]
                }
                d = h, h = 2, v = s[y = o[o.length - 1]] && s[y][2], f = 3
              }
              if (v[0] instanceof Array && v.length > 1) throw new Error("Parse Error: multiple actions possible at state: " + y + ", token: " + h);
              switch (v[0]) {
                case 1:
                  o.push(h), i.push(this.lexer.yytext), a.push(this.lexer.yylloc), o.push(v[1]), h = null, d ? (h = d, d = null) : (l = this.lexer.yyleng, u = this.lexer.yytext, c = this.lexer.yylineno, p = this.lexer.yylloc, f > 0 && f--);
                  break;
                case 2:
                  if (b = this.productions_[v[1]][1], k.$ = i[i.length - b], k._$ = {
                      first_line: a[a.length - (b || 1)].first_line,
                      last_line: a[a.length - 1].last_line,
                      first_column: a[a.length - (b || 1)].first_column,
                      last_column: a[a.length - 1].last_column
                    }, void 0 !== (g = this.performAction.call(k, u, l, c, this.yy, v[1], i, a))) return g;
                  b && (o = o.slice(0, -1 * b * 2), i = i.slice(0, -1 * b), a = a.slice(0, -1 * b)), o.push(this.productions_[v[1]][0]), i.push(k.$), a.push(k._$), _ = s[o[o.length - 2]][o[o.length - 1]], o.push(_);
                  break;
                case 3:
                  return !0
              }
            }
            return !0
          }
        },
        e = function() {
          var t = {
            EOF: 1,
            parseError: function(t, e) {
              if (!this.yy.parseError) throw new Error(t);
              this.yy.parseError(t, e)
            },
            setInput: function(t) {
              return this._input = t, this._more = this._less = this.done = !1, this.yylineno = this.yyleng = 0, this.yytext = this.matched = this.match = "", this.conditionStack = ["INITIAL"], this.yylloc = {
                first_line: 1,
                first_column: 0,
                last_line: 1,
                last_column: 0
              }, this
            },
            input: function() {
              var t = this._input[0];
              this.yytext += t, this.yyleng++, this.match += t, this.matched += t;
              return t.match(/\n/) && this.yylineno++, this._input = this._input.slice(1), t
            },
            unput: function(t) {
              return this._input = t + this._input, this
            },
            more: function() {
              return this._more = !0, this
            },
            pastInput: function() {
              var t = this.matched.substr(0, this.matched.length - this.match.length);
              return (t.length > 20 ? "..." : "") + t.substr(-20).replace(/\n/g, "")
            },
            upcomingInput: function() {
              var t = this.match;
              return t.length < 20 && (t += this._input.substr(0, 20 - t.length)), (t.substr(0, 20) + (t.length > 20 ? "..." : "")).replace(/\n/g, "")
            },
            showPosition: function() {
              var t = this.pastInput(),
                e = new Array(t.length + 1).join("-");
              return t + this.upcomingInput() + "\n" + e + "^"
            },
            next: function() {
              if (this.done) return this.EOF;
              this._input || (this.done = !0);
              var t, e;
              this._more || (this.yytext = "", this.match = "");
              for (var n = this._currentRules(), r = 0; r < n.length; r++)
                if (t = this._input.match(this.rules[n[r]])) return (e = t[0].match(/\n.*/g)) && (this.yylineno += e.length), this.yylloc = {
                  first_line: this.yylloc.last_line,
                  last_line: this.yylineno + 1,
                  first_column: this.yylloc.last_column,
                  last_column: e ? e[e.length - 1].length - 1 : this.yylloc.last_column + t[0].length
                }, this.yytext += t[0], this.match += t[0], this.matches = t, this.yyleng = this.yytext.length, this._more = !1, this._input = this._input.slice(t[0].length), this.matched += t[0], this.performAction.call(this, this.yy, this, n[r], this.conditionStack[this.conditionStack.length - 1]) || void 0;
              if ("" === this._input) return this.EOF;
              this.parseError("Lexical error on line " + (this.yylineno + 1) + ". Unrecognized text.\n" + this.showPosition(), {
                text: "",
                token: null,
                line: this.yylineno
              })
            },
            lex: function() {
              var t = this.next();
              return void 0 !== t ? t : this.lex()
            },
            begin: function(t) {
              this.conditionStack.push(t)
            },
            popState: function() {
              return this.conditionStack.pop()
            },
            _currentRules: function() {
              return this.conditions[this.conditionStack[this.conditionStack.length - 1]].rules
            },
            topState: function() {
              return this.conditionStack[this.conditionStack.length - 2]
            },
            pushState: function(t) {
              this.begin(t)
            }
          };
          return t.performAction = function(t, e, n, r) {
            switch (n) {
              case 0:
                break;
              case 1:
                return 20;
              case 2:
                return 19;
              case 3:
                return 8;
              case 4:
                return 9;
              case 5:
                return 6;
              case 6:
                return 7;
              case 7:
                return 11;
              case 8:
                return 13;
              case 9:
                return 10;
              case 10:
                return 12;
              case 11:
                return 14;
              case 12:
                return 15;
              case 13:
                return 16;
              case 14:
                return 17;
              case 15:
                return 18;
              case 16:
                return 5;
              case 17:
                return "INVALID"
            }
          }, t.rules = [/^\s+/, /^[0-9]+(\.[0-9]+)?\b/, /^n\b/, /^\|\|/, /^&&/, /^\?/, /^:/, /^<=/, /^>=/, /^</, /^>/, /^!=/, /^==/, /^%/, /^\(/, /^\)/, /^$/, /^./], t.conditions = {
            INITIAL: {
              rules: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17],
              inclusive: !0
            }
          }, t
        }();
      return t.lexer = e, t
    }(), void 0 !== t && t.exports && (e = t.exports = h), e.Jed = h
  }()
}, function(t, e, n) {
  "use strict";

  function r(t) {
    return t && t.__esModule ? t : {
      default: t
    }
  }
  Object.defineProperty(e, "__esModule", {
    value: !0
  });
  var o = function() {
      function t(t, e) {
        for (var n = 0; n < e.length; n++) {
          var r = e[n];
          r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(t, r.key, r)
        }
      }
      return function(e, n, r) {
        return n && t(e.prototype, n), r && t(e, r), e
      }
    }(),
    i = n(8),
    a = r(i),
    s = r(n(36)),
    u = n(52),
    c = r(u),
    l = function(t) {
      if (t && t.__esModule) return t;
      var e = {};
      if (null != t)
        for (var n in t) Object.prototype.hasOwnProperty.call(t, n) && (e[n] = t[n]);
      return e.default = t, e
    }(n(53)),
    f = n(15),
    p = r(n(54)),
    h = r(n(56)),
    d = "h1, h2, h3, h4, h5, h6",
    y = "blockquote, p, dt",
    v = {
      heading: 1e3,
      paragraph: 500
    },
    g = function() {
      function t(e) {
        var n = e.rootElement,
          r = e.useDashicons,
          o = void 0 !== r && r,
          i = e.utteranceOptions,
          a = void 0 === i ? {} : i,
          s = e.chunkifyOptions,
          u = e.pauseDurations,
          c = void 0 === u ? v : u;
        ! function(t, e) {
          if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function")
        }(this, t), this.rootElement = n, this.chunkifyOptions = s, this.pauseDurations = c, this.useDashicons = o, this.controlsElement = null, this.currentUtterance = null;
        for (var l = ["play", "stop", "next", "previous", "updateContainsSelectionState", "handleEscapeKeydown", "renderControls"], f = 0; f < l.length; f++) {
          var p = l[f];
          this[p] = this[p].bind(this)
        }
        this.state = {
          containsSelection: !1,
          settingsShown: !1,
          speakTimeoutId: 0,
          playing: !1,
          chunkIndex: 0,
          chunkRangeOffset: 0,
          languageVoices: {},
          pitch: 1,
          rate: 1
        }, Object.assign(this.state, a)
      }
      return o(t, [{
        key: "initialize",
        value: function() {
          this.chunkify(), this.injectControls(), this.setupStateMachine(), document.addEventListener("selectionchange", this.updateContainsSelectionState), document.addEventListener("keydown", this.handleEscapeKeydown), this.isDialogSupported = "showModal" in document.createElement("dialog") || "undefined" != typeof dialogPolyfill, this.renderControls(), this.on("change", this.renderControls)
        }
      }, {
        key: "setState",
        value: function(t) {
          var e = (arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : {}).suppressEvents,
            n = void 0 !== e && e,
            r = this.state,
            o = Object.assign({}, r, t);
          this.state = o;
          var i = 0,
            a = !0,
            s = !1,
            u = void 0;
          try {
            for (var c, l = Object.keys(t)[Symbol.iterator](); !(a = (c = l.next()).done); a = !0) {
              var f = c.value;
              (0, h.default)(o[f], r[f]) || (n || ("string" == typeof o[f] && this.emit("change:" + f + ":" + o[f], r[f]), this.emit("change:" + f, o[f], r[f])), i += 1)
            }
          } catch (t) {
            s = !0, u = t
          } finally {
            try {
              !a && l.return && l.return()
            } finally {
              if (s) throw u
            }
          }!n && i > 0 && this.emit("change", o, r)
        }
      }, {
        key: "setupStateMachine",
        value: function() {
          var t = this;
          this.on("change:playing", function(e) {
            e ? t.startPlayingCurrentChunkAndQueueNext() : (clearTimeout(t.state.speakTimeoutId), speechSynthesis.cancel())
          }), this.on("change:speakTimeoutId", function(t, e) {
            clearTimeout(e)
          });
          this.on("change:containsSelection", function(e) {
            t.controlsElement.classList.toggle("spoken-word--active", e)
          }), this.on("change:settingsShown", function(e) {
            e && !l.isLoaded() && l.load().then(t.renderControls)
          });
          this.on("change", function(e, n) {
            e.chunkIndex === n.chunkIndex && e.chunkRangeOffset === n.chunkRangeOffset || function() {
              if (t.state.playing) t.startPlayingCurrentChunkAndQueueNext();
              else {
                var e = window.getSelection(),
                  n = document.createRange(),
                  r = t.chunks[t.state.chunkIndex],
                  o = r.nodes[0],
                  i = r.nodes[r.nodes.length - 1];
                e.removeAllRanges(), n.setStart(o, 0), n.setEnd(i, i.length), t.playbackAddedRange = n, e.addRange(n), o.parentElement.scrollIntoView({
                  behavior: "smooth"
                })
              }
            }(), (e.rate !== n.rate || e.pitch !== n.pitch || e.languageVoices !== n.languageVoices && !(0, h.default)(e.languageVoices, n.languageVoices)) && (t.state.playing && (t.voicePropChanged = !0, t.startPlayingCurrentChunkAndQueueNext()), t.emit("sharedStateChange", {
              languageVoices: t.state.languageVoices,
              rate: t.state.rate,
              pitch: t.state.pitch
            }))
          })
        }
      }, {
        key: "chunkify",
        value: function() {
          this.chunks = (0, c.default)(Object.assign({}, this.chunkifyOptions, {
            containerElement: this.rootElement
          }))
        }
      }, {
        key: "injectControls",
        value: function() {
          this.controlsElement = document.createElement("div"), this.controlsElement.classList.add("spoken-word"), this.rootElement.insertBefore(this.controlsElement, this.rootElement.firstChild)
        }
      }, {
        key: "getAvailableVoices",
        value: function() {
          if (this._availableVoices && this._availableVoices.length > 0) return this._availableVoices;
          var t = speechSynthesis.getVoices().filter(function(t) {
            return t.localService
          });
          t.sort(function(t, e) {
            return t.name === e.name ? 0 : t.name < e.name ? -1 : 1
          });
          var e = new Set(t.map(function(t) {
            return t.voiceURI
          }));
          return this._availableVoices = t.filter(function(t) {
            return t.voiceURI.endsWith(".premium") || !e.has(t.voiceURI + ".premium")
          }), t
        }
      }, {
        key: "getLanguageVoices",
        value: function() {
          var t = {},
            e = !0,
            n = !1,
            r = void 0;
          try {
            for (var o, i = this.getAvailableVoices()[Symbol.iterator](); !(e = (o = i.next()).done); e = !0) {
              var a = o.value,
                s = a.lang.replace(/-.*/, "");
              !a.default && s in t || (t[s] = a.voiceURI)
            }
          } catch (t) {
            n = !0, r = t
          } finally {
            try {
              !e && i.return && i.return()
            } finally {
              if (n) throw r
            }
          }
          return Object.assign(t, this.state.languageVoices), t
        }
      }, {
        key: "renderControls",
        value: function() {
          var t = this,
            e = (0, u.getWeightedChunkLanguages)(this.chunks),
            n = Object.keys(e);
          n.sort(function(t, n) {
            return e[n] - e[t]
          });
          var r = Object.assign({}, this.state, {
            play: this.play,
            stop: this.stop,
            next: this.next,
            previous: this.previous,
            useDashicons: this.useDashicons,
            onShowSettings: function() {
              t.setState({
                settingsShown: !0
              })
            },
            onHideSettings: function() {
              t.setState({
                settingsShown: !1
              })
            },
            presentLanguages: n,
            availableVoices: this.getAvailableVoices(),
            languageVoices: this.getLanguageVoices(),
            setProps: function(e) {
              t.setState(e)
            },
            isDialogSupported: this.isDialogSupported
          });
          (0, i.render)(a.default.createElement(p.default, r), this.controlsElement)
        }
      }, {
        key: "getUtteranceOptions",
        value: function(t) {
          var e = {
            pitch: this.state.pitch,
            rate: this.state.rate
          };
          return t.language && (e.voice = this.getVoice(t), e.lang = t.language), e
        }
      }, {
        key: "getVoice",
        value: function(t) {
          var e = this.getLanguageVoices(),
            n = t.language.replace(/-.*/, "").toLowerCase(),
            r = speechSynthesis.getVoices().find(function(t) {
              return t.voiceURI === e[n]
            });
          return r || (r = speechSynthesis.getVoices().find(function(t) {
            return t.lang.startsWith(n)
          })), r
        }
      }, {
        key: "speakChunk",
        value: function() {
          var t = this,
            e = this.state.chunkIndex;
          return new Promise(function(n, r) {
            var o = t.chunks[e];
            if (o) {
              for (var i = window.getSelection(), a = document.createRange(), s = 0, u = [].concat(function(t) {
                  if (Array.isArray(t)) {
                    for (var e = 0, n = Array(t.length); e < t.length; e++) n[e] = t[e];
                    return n
                  }
                  return Array.from(t)
                }(o.nodes)); u[0] && s + u[0].length < t.state.chunkRangeOffset;) s += u.shift().length;
              var c = t.state.chunkRangeOffset - s,
                l = u.shift(),
                p = [l.nodeValue.substr(c)].concat(u.map(function(t) {
                  return t.nodeValue
                })).join("");
              if (p.trim()) {
                t.currentUtterance = new SpeechSynthesisUtterance(p), Object.assign(t.currentUtterance, t.getUtteranceOptions(o)), t.currentUtterance.onpause = function() {
                  return t.setState({
                    playing: !1
                  })
                };
                var h = 0,
                  d = t.state.chunkRangeOffset;
                t.currentUtterance.onboundary = function(e) {
                  if ("word" === e.name) {
                    for (d = s + c + e.charIndex, t.setState({
                        chunkRangeOffset: d
                      }, {
                        suppressEvents: !0
                      }), (0, f.scrollElementIntoViewIfNeeded)(l.parentElement); u.length && e.charIndex + c >= h + l.length;) h += l.length, l = u.shift();
                    if (!t.state.settingsShown) {
                      var n = e.charIndex - h + c,
                        r = e.currentTarget.text.substr(e.charIndex).replace(/\s.+/, "");
                      i.removeAllRanges(), a.setStart(l, n), a.setEnd(l, Math.min(n + r.length, l.length)), t.playbackAddedRange = a, i.addRange(a)
                    }
                  }
                };
                t.currentUtterance.onend = function() {
                  if (t.currentUtterance = null, t.state.settingsShown || i.removeAllRanges(), t.voicePropChanged) return t.voicePropChanged = !1, void r("voice_prop_changed");
                  t.state.chunkIndex === e && d === t.state.chunkRangeOffset ? t.state.playing ? 0 === u.length ? n() : r("playback_interrupted") : r("playback_stopped") : r("chunk_change")
                }, t.currentUtterance.onerror = function() {
                  t.voicePropChanged && (t.voicePropChanged = !1)
                }, speechSynthesis.speak(t.currentUtterance)
              } else n()
            } else r()
          })
        }
      }, {
        key: "getChunkPositionFromRange",
        value: function() {
          var t = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : null;
          if (!t) {
            var e = window.getSelection();
            if (1 !== e.rangeCount) return null;
            t = e.getRangeAt(0)
          }
          try {
            if (t.startContainer.nodeType !== Node.TEXT_NODE) return null
          } catch (t) {
            return null
          }
          for (var n = 0; n < this.chunks.length; n++) {
            var r = 0,
              o = !0,
              i = !1,
              a = void 0;
            try {
              for (var s, u = this.chunks[n].nodes[Symbol.iterator](); !(o = (s = u.next()).done); o = !0) {
                var c = s.value;
                if (t.startContainer === c) return r += t.startOffset, {
                  chunkIndex: n,
                  chunkRangeOffset: r
                };
                r += c.length
              }
            } catch (t) {
              i = !0, a = t
            } finally {
              try {
                !o && u.return && u.return()
              } finally {
                if (i) throw a
              }
            }
          }
          return null
        }
      }, {
        key: "updateContainsSelectionState",
        value: function() {
          var t = window.getSelection();
          if (0 !== t.rangeCount) {
            var e = t.getRangeAt(0),
              n = {
                containsSelection: !1
              };
            try {
              n.containsSelection = this.rootElement.contains(e.startContainer) || this.rootElement.contains(e.endContainer)
            } catch (t) {}
            if (this.state.playing && this.playbackAddedRange && !(0, f.equalRanges)(e, this.playbackAddedRange)) {
              var r = this.getChunkPositionFromRange(e);
              r && Object.assign(n, r)
            }
            this.setState(n)
          }
        }
      }, {
        key: "handleEscapeKeydown",
        value: function(t) {
          this.state.playing && 27 === t.which && !this.state.settingsShown && this.stop()
        }
      }, {
        key: "play",
        value: function() {
          var t = {
              playing: !0
            },
            e = this.getChunkPositionFromRange();
          e ? Object.assign(t, e) : this.state.chunkIndex + 1 === this.chunks.length && (t.chunkIndex = 0, t.chunkRangeOffset = 0), this.setState(t)
        }
      }, {
        key: "getInterChunkPause",
        value: function(t, e) {
          if (t.root !== e.root) {
            if (t.root.matches(d) || e.root.matches(d)) return this.pauseDurations.heading;
            if (t.root.matches(y) || e.root.matches(y)) return this.pauseDurations.paragraph
          }
          return 0
        }
      }, {
        key: "startPlayingCurrentChunkAndQueueNext",
        value: function() {
          var t = this;
          clearTimeout(this.state.speakTimeoutId);
          var e = function(e) {
              e && "playback_interrupted" !== e && "playback_completed" !== e || t.setState({
                playing: !1
              })
            },
            n = new Promise(function(e) {
              if (speechSynthesis.speaking) {
                t.currentUtterance && t.currentUtterance.addEventListener("end", e), setTimeout(e, 100), speechSynthesis.cancel()
              } else e()
            }),
            r = function() {
              if (t.state.chunkIndex + 1 !== t.chunks.length) {
                var n = t.chunks[t.state.chunkIndex],
                  r = t.chunks[t.state.chunkIndex + 1],
                  o = t.getInterChunkPause(n, r),
                  i = t.state.chunkIndex;
                t.setState({
                  speakTimeoutId: setTimeout(function() {
                    t.setState({
                      chunkIndex: i + 1,
                      chunkRangeOffset: 0
                    })
                  }, Math.round(o * (1 / t.state.rate)))
                })
              } else e("playback_completed")
            };
          Promise.all([l.load(), n]).then(function() {
            t.setState({
              speakTimeoutId: setTimeout(function() {
                t.speakChunk().then(r, e)
              })
            })
          }, e)
        }
      }, {
        key: "previous",
        value: function() {
          var t = {
            chunkRangeOffset: 0
          };
          this.state.chunkRangeOffset < 10 && (t.chunkIndex = Math.max(this.state.chunkIndex - 1, 0)), this.setState(t)
        }
      }, {
        key: "next",
        value: function() {
          if (this.state.chunkIndex + 1 !== this.chunks.length) {
            var t = {
              chunkIndex: this.state.chunkIndex + 1,
              chunkRangeOffset: 0
            };
            this.setState(t)
          }
        }
      }, {
        key: "stop",
        value: function() {
          this.setState({
            playing: !1
          })
        }
      }, {
        key: "destroy",
        value: function() {
          this.state.playing && speechSynthesis.cancel(), document.removeEventListener("selectionchange", this.updateContainsSelectionState), document.removeEventListener("keydown", this.handleEscapeKeydown), (0, i.unmountComponentAtNode)(this.controlsElement)
        }
      }]), t
    }();
  e.default = g, (0, s.default)(g.prototype)
}, function(t, e) {
  function n() {
    throw new Error("setTimeout has not been defined")
  }

  function r() {
    throw new Error("clearTimeout has not been defined")
  }

  function o(t) {
    if (c === setTimeout) return setTimeout(t, 0);
    if ((c === n || !c) && setTimeout) return c = setTimeout, setTimeout(t, 0);
    try {
      return c(t, 0)
    } catch (e) {
      try {
        return c.call(null, t, 0)
      } catch (e) {
        return c.call(this, t, 0)
      }
    }
  }

  function i() {
    d && p && (d = !1, p.length ? h = p.concat(h) : y = -1, h.length && a())
  }

  function a() {
    if (!d) {
      var t = o(i);
      d = !0;
      for (var e = h.length; e;) {
        for (p = h, h = []; ++y < e;) p && p[y].run();
        y = -1, e = h.length
      }
      p = null, d = !1,
        function(t) {
          if (l === clearTimeout) return clearTimeout(t);
          if ((l === r || !l) && clearTimeout) return l = clearTimeout, clearTimeout(t);
          try {
            l(t)
          } catch (e) {
            try {
              return l.call(null, t)
            } catch (e) {
              return l.call(this, t)
            }
          }
        }(t)
    }
  }

  function s(t, e) {
    this.fun = t, this.array = e
  }

  function u() {}
  var c, l, f = t.exports = {};
  ! function() {
    try {
      c = "function" == typeof setTimeout ? setTimeout : n
    } catch (t) {
      c = n
    }
    try {
      l = "function" == typeof clearTimeout ? clearTimeout : r
    } catch (t) {
      l = r
    }
  }();
  var p, h = [],
    d = !1,
    y = -1;
  f.nextTick = function(t) {
    var e = new Array(arguments.length - 1);
    if (arguments.length > 1)
      for (var n = 1; n < arguments.length; n++) e[n - 1] = arguments[n];
    h.push(new s(t, e)), 1 !== h.length || d || o(a)
  }, s.prototype.run = function() {
    this.fun.apply(null, this.array)
  }, f.title = "browser", f.browser = !0, f.env = {}, f.argv = [], f.version = "", f.versions = {}, f.on = u, f.addListener = u, f.once = u, f.off = u, f.removeListener = u, f.removeAllListeners = u, f.emit = u, f.prependListener = u, f.prependOnceListener = u, f.listeners = function(t) {
    return []
  }, f.binding = function(t) {
    throw new Error("process.binding is not supported")
  }, f.cwd = function() {
    return "/"
  }, f.chdir = function(t) {
    throw new Error("process.chdir is not supported")
  }, f.umask = function() {
    return 0
  }
}, function(t, e, n) {
  "use strict";
  var r = n(32),
    o = n(33),
    i = n(34);
  t.exports = function() {
    function t(t, e, n, r, a, s) {
      s !== i && o(!1, "Calling PropTypes validators directly is not supported by the `prop-types` package. Use PropTypes.checkPropTypes() to call them. Read more at http://fb.me/use-check-prop-types")
    }

    function e() {
      return t
    }
    t.isRequired = t;
    var n = {
      array: t,
      bool: t,
      func: t,
      number: t,
      object: t,
      string: t,
      symbol: t,
      any: t,
      arrayOf: e,
      element: t,
      instanceOf: e,
      node: t,
      objectOf: e,
      oneOf: e,
      oneOfType: e,
      shape: e,
      exact: e
    };
    return n.checkPropTypes = r, n.PropTypes = n, n
  }
}, function(t, e, n) {
  "use strict";

  function r(t) {
    return function() {
      return t
    }
  }
  var o = function() {};
  o.thatReturns = r, o.thatReturnsFalse = r(!1), o.thatReturnsTrue = r(!0), o.thatReturnsNull = r(null), o.thatReturnsThis = function() {
    return this
  }, o.thatReturnsArgument = function(t) {
    return t
  }, t.exports = o
}, function(t, e, n) {
  "use strict";
  var r = function(t) {};
  t.exports = function(t, e, n, o, i, a, s, u) {
    if (r(e), !t) {
      var c;
      if (void 0 === e) c = new Error("Minified exception occurred; use the non-minified dev environment for the full error message and additional helpful warnings.");
      else {
        var l = [n, o, i, a, s, u],
          f = 0;
        (c = new Error(e.replace(/%s/g, function() {
          return l[f++]
        }))).name = "Invariant Violation"
      }
      throw c.framesToPop = 1, c
    }
  }
}, function(t, e, n) {
  "use strict";
  t.exports = "SECRET_DO_NOT_PASS_THIS_OR_YOU_WILL_BE_FIRED"
}, function(t, e, n) {
  "use strict";

  function r(t, e) {
    var n, r, o, i, a = E;
    for (i = arguments.length; i-- > 2;) j.push(arguments[i]);
    for (e && null != e.children && (j.length || j.push(e.children), delete e.children); j.length;)
      if ((r = j.pop()) && void 0 !== r.pop)
        for (i = r.length; i--;) j.push(r[i]);
      else "boolean" == typeof r && (r = null), (o = "function" != typeof t) && (null == r ? r = "" : "number" == typeof r ? r = String(r) : "string" != typeof r && (o = !1)), o && n ? a[a.length - 1] += r : a === E ? a = [r] : a.push(r), n = o;
    var s = new function() {};
    return s.nodeName = t, s.children = a, s.attributes = null == e ? void 0 : e, s.key = null == e ? void 0 : e.key, void 0 !== P.vnode && P.vnode(s), s
  }

  function o(t, e) {
    for (var n in e) t[n] = e[n];
    return t
  }

  function i(t, e) {
    return r(t.nodeName, o(o({}, t.attributes), e), arguments.length > 2 ? [].slice.call(arguments, 2) : t.children)
  }

  function a(t) {
    !t._dirty && (t._dirty = !0) && 1 == T.push(t) && (P.debounceRendering || C)(s)
  }

  function s() {
    var t, e = T;
    for (T = []; t = e.pop();) t._dirty && k(t)
  }

  function u(t, e, n) {
    return "string" == typeof e || "number" == typeof e ? void 0 !== t.splitText : "string" == typeof e.nodeName ? !t._componentConstructor && c(t, e.nodeName) : n || t._componentConstructor === e.nodeName
  }

  function c(t, e) {
    return t.normalizedNodeName === e || t.nodeName.toLowerCase() === e.toLowerCase()
  }

  function l(t) {
    var e = o({}, t.attributes);
    e.children = t.children;
    var n = t.nodeName.defaultProps;
    if (void 0 !== n)
      for (var r in n) void 0 === e[r] && (e[r] = n[r]);
    return e
  }

  function f(t) {
    var e = t.parentNode;
    e && e.removeChild(t)
  }

  function p(t, e, n, r, o) {
    if ("className" === e && (e = "class"), "key" === e);
    else if ("ref" === e) n && n(null), r && r(t);
    else if ("class" !== e || o)
      if ("style" === e) {
        if (r && "string" != typeof r && "string" != typeof n || (t.style.cssText = r || ""), r && "object" == typeof r) {
          if ("string" != typeof n)
            for (var i in n) i in r || (t.style[i] = "");
          for (var i in r) t.style[i] = "number" == typeof r[i] && !1 === N.test(i) ? r[i] + "px" : r[i]
        }
      } else if ("dangerouslySetInnerHTML" === e) r && (t.innerHTML = r.__html || "");
    else if ("o" == e[0] && "n" == e[1]) {
      var a = e !== (e = e.replace(/Capture$/, ""));
      e = e.toLowerCase().substring(2), r ? n || t.addEventListener(e, h, a) : t.removeEventListener(e, h, a), (t._listeners || (t._listeners = {}))[e] = r
    } else if ("list" !== e && "type" !== e && !o && e in t) ! function(t, e, n) {
      try {
        t[e] = n
      } catch (t) {}
    }(t, e, null == r ? "" : r), null != r && !1 !== r || t.removeAttribute(e);
    else {
      var s = o && e !== (e = e.replace(/^xlink\:?/, ""));
      null == r || !1 === r ? s ? t.removeAttributeNS("http://www.w3.org/1999/xlink", e.toLowerCase()) : t.removeAttribute(e) : "function" != typeof r && (s ? t.setAttributeNS("http://www.w3.org/1999/xlink", e.toLowerCase(), r) : t.setAttribute(e, r))
    } else t.className = r || ""
  }

  function h(t) {
    return this._listeners[t.type](P.event && P.event(t) || t)
  }

  function d() {
    for (var t; t = A.pop();) P.afterMount && P.afterMount(t), t.componentDidMount && t.componentDidMount()
  }

  function y(t, e, n, r, o, i) {
    R++ || (I = null != o && void 0 !== o.ownerSVGElement, D = null != t && !("__preactattr_" in t));
    var a = v(t, e, n, r, i);
    return o && a.parentNode !== o && o.appendChild(a), --R || (D = !1, i || d()), a
  }

  function v(t, e, n, r, o) {
    var i = t,
      a = I;
    if (null != e && "boolean" != typeof e || (e = ""), "string" == typeof e || "number" == typeof e) return t && void 0 !== t.splitText && t.parentNode && (!t._component || o) ? t.nodeValue != e && (t.nodeValue = e) : (i = document.createTextNode(e), t && (t.parentNode && t.parentNode.replaceChild(i, t), g(t, !0))), i.__preactattr_ = !0, i;
    var s = e.nodeName;
    if ("function" == typeof s) return function(t, e, n, r) {
      var o = t && t._component,
        i = o,
        a = t,
        s = o && t._componentConstructor === e.nodeName,
        u = s,
        c = l(e);
      for (; o && !u && (o = o._parentComponent);) u = o.constructor === e.nodeName;
      o && u && (!r || o._component) ? (x(o, c, 3, n, r), t = o.base) : (i && !s && (w(i), t = a = null), o = b(e.nodeName, c, n), t && !o.nextBase && (o.nextBase = t, a = null), x(o, c, 1, n, r), t = o.base, a && t !== a && (a._component = null, g(a, !1)));
      return t
    }(t, e, n, r);
    if (I = "svg" === s || "foreignObject" !== s && I, s = String(s), (!t || !c(t, s)) && (i = function(t, e) {
        var n = e ? document.createElementNS("http://www.w3.org/2000/svg", t) : document.createElement(t);
        return n.normalizedNodeName = t, n
      }(s, I), t)) {
      for (; t.firstChild;) i.appendChild(t.firstChild);
      t.parentNode && t.parentNode.replaceChild(i, t), g(t, !0)
    }
    var h = i.firstChild,
      d = i.__preactattr_,
      y = e.children;
    if (null == d) {
      d = i.__preactattr_ = {};
      for (var m = i.attributes, _ = m.length; _--;) d[m[_].name] = m[_].value
    }
    return !D && y && 1 === y.length && "string" == typeof y[0] && null != h && void 0 !== h.splitText && null == h.nextSibling ? h.nodeValue != y[0] && (h.nodeValue = y[0]) : (y && y.length || null != h) && function(t, e, n, r, o) {
        var i, a, s, c, l, p = t.childNodes,
          h = [],
          d = {},
          y = 0,
          m = 0,
          b = p.length,
          _ = 0,
          x = e ? e.length : 0;
        if (0 !== b)
          for (var k = 0; k < b; k++) {
            var w = p[k],
              S = w.__preactattr_,
              O = x && S ? w._component ? w._component.__key : S.key : null;
            null != O ? (y++, d[O] = w) : (S || (void 0 !== w.splitText ? !o || w.nodeValue.trim() : o)) && (h[_++] = w)
          }
        if (0 !== x)
          for (var k = 0; k < x; k++) {
            c = e[k], l = null;
            var O = c.key;
            if (null != O) y && void 0 !== d[O] && (l = d[O], d[O] = void 0, y--);
            else if (!l && m < _)
              for (i = m; i < _; i++)
                if (void 0 !== h[i] && u(a = h[i], c, o)) {
                  l = a, h[i] = void 0, i === _ - 1 && _--, i === m && m++;
                  break
                } l = v(l, c, n, r), s = p[k], l && l !== t && l !== s && (null == s ? t.appendChild(l) : l === s.nextSibling ? f(s) : t.insertBefore(l, s))
          }
        if (y)
          for (var k in d) void 0 !== d[k] && g(d[k], !1);
        for (; m <= _;) void 0 !== (l = h[_--]) && g(l, !1)
      }(i, y, n, r, D || null != d.dangerouslySetInnerHTML),
      function(t, e, n) {
        var r;
        for (r in n) e && null != e[r] || null == n[r] || p(t, r, n[r], n[r] = void 0, I);
        for (r in e) "children" === r || "innerHTML" === r || r in n && e[r] === ("value" === r || "checked" === r ? t[r] : n[r]) || p(t, r, n[r], n[r] = e[r], I)
      }(i, e.attributes, d), I = a, i
  }

  function g(t, e) {
    var n = t._component;
    n ? w(n) : (null != t.__preactattr_ && t.__preactattr_.ref && t.__preactattr_.ref(null), !1 !== e && null != t.__preactattr_ || f(t), m(t))
  }

  function m(t) {
    for (t = t.lastChild; t;) {
      var e = t.previousSibling;
      g(t, !0), t = e
    }
  }

  function b(t, e, n) {
    var r, o = L[t.name];
    if (t.prototype && t.prototype.render ? (r = new t(e, n), S.call(r, e, n)) : ((r = new S(e, n)).constructor = t, r.render = _), o)
      for (var i = o.length; i--;)
        if (o[i].constructor === t) {
          r.nextBase = o[i].nextBase, o.splice(i, 1);
          break
        } return r
  }

  function _(t, e, n) {
    return this.constructor(t, n)
  }

  function x(t, e, n, r, o) {
    t._disable || (t._disable = !0, (t.__ref = e.ref) && delete e.ref, (t.__key = e.key) && delete e.key, !t.base || o ? t.componentWillMount && t.componentWillMount() : t.componentWillReceiveProps && t.componentWillReceiveProps(e, r), r && r !== t.context && (t.prevContext || (t.prevContext = t.context), t.context = r), t.prevProps || (t.prevProps = t.props), t.props = e, t._disable = !1, 0 !== n && (1 !== n && !1 === P.syncComponentUpdates && t.base ? a(t) : k(t, 1, o)), t.__ref && t.__ref(t))
  }

  function k(t, e, n, r) {
    if (!t._disable) {
      var i, a, s, u = t.props,
        c = t.state,
        f = t.context,
        p = t.prevProps || u,
        h = t.prevState || c,
        v = t.prevContext || f,
        m = t.base,
        _ = t.nextBase,
        S = m || _,
        O = t._component,
        j = !1;
      if (m && (t.props = p, t.state = h, t.context = v, 2 !== e && t.shouldComponentUpdate && !1 === t.shouldComponentUpdate(u, c, f) ? j = !0 : t.componentWillUpdate && t.componentWillUpdate(u, c, f), t.props = u, t.state = c, t.context = f), t.prevProps = t.prevState = t.prevContext = t.nextBase = null, t._dirty = !1, !j) {
        i = t.render(u, c, f), t.getChildContext && (f = o(o({}, f), t.getChildContext()));
        var E, C, N = i && i.nodeName;
        if ("function" == typeof N) {
          var T = l(i);
          (a = O) && a.constructor === N && T.key == a.__key ? x(a, T, 1, f, !1) : (E = a, t._component = a = b(N, T, f), a.nextBase = a.nextBase || _, a._parentComponent = t, x(a, T, 0, f, !1), k(a, 1, n, !0)), C = a.base
        } else s = S, (E = O) && (s = t._component = null), (S || 1 === e) && (s && (s._component = null), C = y(s, i, f, n || !m, S && S.parentNode, !0));
        if (S && C !== S && a !== O) {
          var I = S.parentNode;
          I && C !== I && (I.replaceChild(C, S), E || (S._component = null, g(S, !1)))
        }
        if (E && w(E), t.base = C, C && !r) {
          for (var D = t, L = t; L = L._parentComponent;)(D = L).base = C;
          C._component = D, C._componentConstructor = D.constructor
        }
      }
      if (!m || n ? A.unshift(t) : j || (t.componentDidUpdate && t.componentDidUpdate(p, h, v), P.afterUpdate && P.afterUpdate(t)), null != t._renderCallbacks)
        for (; t._renderCallbacks.length;) t._renderCallbacks.pop().call(t);
      R || r || d()
    }
  }

  function w(t) {
    P.beforeUnmount && P.beforeUnmount(t);
    var e = t.base;
    t._disable = !0, t.componentWillUnmount && t.componentWillUnmount(), t.base = null;
    var n = t._component;
    n ? w(n) : e && (e.__preactattr_ && e.__preactattr_.ref && e.__preactattr_.ref(null), t.nextBase = e, f(e), function(t) {
      var e = t.constructor.name;
      (L[e] || (L[e] = [])).push(t)
    }(t), m(e)), t.__ref && t.__ref(null)
  }

  function S(t, e) {
    this._dirty = !0, this.context = e, this.props = t, this.state = this.state || {}
  }

  function O(t, e, n) {
    return y(n, t, {}, !1, e, !1)
  }
  n.d(e, "c", function() {
    return r
  }), n.d(e, "b", function() {
    return i
  }), n.d(e, "a", function() {
    return S
  }), n.d(e, "e", function() {
    return O
  }), n.d(e, "d", function() {
    return P
  });
  var P = {},
    j = [],
    E = [],
    C = "function" == typeof Promise ? Promise.resolve().then.bind(Promise.resolve()) : setTimeout,
    N = /acit|ex(?:s|g|n|p|$)|rph|ows|mnc|ntw|ine[ch]|zoo|^ord/i,
    T = [],
    A = [],
    R = 0,
    I = !1,
    D = !1,
    L = {};
  o(S.prototype, {
    setState: function(t, e) {
      var n = this.state;
      this.prevState || (this.prevState = o({}, n)), o(n, "function" == typeof t ? t(n, this.props) : t), e && (this._renderCallbacks = this._renderCallbacks || []).push(e), a(this)
    },
    forceUpdate: function(t) {
      t && (this._renderCallbacks = this._renderCallbacks || []).push(t), k(this, 2)
    },
    render: function() {}
  })
}, function(t, e, n) {
  "use strict";
  var r, o, i, a, s, u, c, l = n(37),
    f = n(51),
    p = Function.prototype.apply,
    h = Function.prototype.call,
    d = Object.create,
    y = Object.defineProperty,
    v = Object.defineProperties,
    g = Object.prototype.hasOwnProperty,
    m = {
      configurable: !0,
      enumerable: !1,
      writable: !0
    };
  s = {
    on: r = function(t, e) {
      var n;
      return f(e), g.call(this, "__ee__") ? n = this.__ee__ : (n = m.value = d(null), y(this, "__ee__", m), m.value = null), n[t] ? "object" == typeof n[t] ? n[t].push(e) : n[t] = [n[t], e] : n[t] = e, this
    },
    once: o = function(t, e) {
      var n, o;
      return f(e), o = this, r.call(this, t, n = function() {
        i.call(o, t, n), p.call(e, this, arguments)
      }), n.__eeOnceListener__ = e, this
    },
    off: i = function(t, e) {
      var n, r, o, i;
      if (f(e), !g.call(this, "__ee__")) return this;
      if (!(n = this.__ee__)[t]) return this;
      if ("object" == typeof(r = n[t]))
        for (i = 0; o = r[i]; ++i) o !== e && o.__eeOnceListener__ !== e || (2 === r.length ? n[t] = r[i ? 0 : 1] : r.splice(i, 1));
      else r !== e && r.__eeOnceListener__ !== e || delete n[t];
      return this
    },
    emit: a = function(t) {
      var e, n, r, o, i;
      if (g.call(this, "__ee__") && (o = this.__ee__[t]))
        if ("object" == typeof o) {
          for (n = arguments.length, i = new Array(n - 1), e = 1; e < n; ++e) i[e - 1] = arguments[e];
          for (o = o.slice(), e = 0; r = o[e]; ++e) p.call(r, this, i)
        } else switch (arguments.length) {
          case 1:
            h.call(o, this);
            break;
          case 2:
            h.call(o, this, arguments[1]);
            break;
          case 3:
            h.call(o, this, arguments[1], arguments[2]);
            break;
          default:
            for (n = arguments.length, i = new Array(n - 1), e = 1; e < n; ++e) i[e - 1] = arguments[e];
            p.call(o, this, i)
        }
    }
  }, u = {
    on: l(r),
    once: l(o),
    off: l(i),
    emit: l(a)
  }, c = v({}, u), t.exports = e = function(t) {
    return null == t ? d(c) : v(Object(t), u)
  }, e.methods = s
}, function(t, e, n) {
  "use strict";
  var r = n(38),
    o = n(46),
    i = n(47),
    a = n(48);
  (t.exports = function(t, e) {
    var n, i, s, u, c;
    return arguments.length < 2 || "string" != typeof t ? (u = e, e = t, t = null) : u = arguments[2], null == t ? (n = s = !0, i = !1) : (n = a.call(t, "c"), i = a.call(t, "e"), s = a.call(t, "w")), c = {
      value: e,
      configurable: n,
      enumerable: i,
      writable: s
    }, u ? r(o(u), c) : c
  }).gs = function(t, e, n) {
    var s, u, c, l;
    return "string" != typeof t ? (c = n, n = e, e = t, t = null) : c = arguments[3], null == e ? e = void 0 : i(e) ? null == n ? n = void 0 : i(n) || (c = n, n = void 0) : (c = e, e = n = void 0), null == t ? (s = !0, u = !1) : (s = a.call(t, "c"), u = a.call(t, "e")), l = {
      get: e,
      set: n,
      configurable: s,
      enumerable: u
    }, c ? r(o(c), l) : l
  }
}, function(t, e, n) {
  "use strict";
  t.exports = n(39)() ? Object.assign : n(40)
}, function(t, e, n) {
  "use strict";
  t.exports = function() {
    var t, e = Object.assign;
    return "function" == typeof e && (t = {
      foo: "raz"
    }, e(t, {
      bar: "dwa"
    }, {
      trzy: "trzy"
    }), t.foo + t.bar + t.trzy === "razdwatrzy")
  }
}, function(t, e, n) {
  "use strict";
  var r = n(41),
    o = n(45),
    i = Math.max;
  t.exports = function(t, e) {
    var n, a, s, u = i(arguments.length, 2);
    for (t = Object(o(t)), s = function(r) {
        try {
          t[r] = e[r]
        } catch (t) {
          n || (n = t)
        }
      }, a = 1; a < u; ++a) e = arguments[a], r(e).forEach(s);
    if (void 0 !== n) throw n;
    return t
  }
}, function(t, e, n) {
  "use strict";
  t.exports = n(42)() ? Object.keys : n(43)
}, function(t, e, n) {
  "use strict";
  t.exports = function() {
    try {
      return Object.keys("primitive"), !0
    } catch (t) {
      return !1
    }
  }
}, function(t, e, n) {
  "use strict";
  var r = n(10),
    o = Object.keys;
  t.exports = function(t) {
    return o(r(t) ? Object(t) : t)
  }
}, function(t, e, n) {
  "use strict";
  t.exports = function() {}
}, function(t, e, n) {
  "use strict";
  var r = n(10);
  t.exports = function(t) {
    if (!r(t)) throw new TypeError("Cannot use null or undefined");
    return t
  }
}, function(t, e, n) {
  "use strict";
  var r = n(10),
    o = Array.prototype.forEach,
    i = Object.create;
  t.exports = function(t) {
    var e = i(null);
    return o.call(arguments, function(t) {
      r(t) && function(t, e) {
        var n;
        for (n in t) e[n] = t[n]
      }(Object(t), e)
    }), e
  }
}, function(t, e, n) {
  "use strict";
  t.exports = function(t) {
    return "function" == typeof t
  }
}, function(t, e, n) {
  "use strict";
  t.exports = n(49)() ? String.prototype.contains : n(50)
}, function(t, e, n) {
  "use strict";
  var r = "razdwatrzy";
  t.exports = function() {
    return "function" == typeof r.contains && (!0 === r.contains("dwa") && !1 === r.contains("foo"))
  }
}, function(t, e, n) {
  "use strict";
  var r = String.prototype.indexOf;
  t.exports = function(t) {
    return r.call(this, t, arguments[1]) > -1
  }
}, function(t, e, n) {
  "use strict";
  t.exports = function(t) {
    if ("function" != typeof t) throw new TypeError(t + " is not a function");
    return t
  }
}, function(t, e, n) {
  "use strict";
  Object.defineProperty(e, "__esModule", {
    value: !0
  }), e.getWeightedChunkLanguages = function(t) {
    var e = (arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : {}).excludeCountry,
      n = void 0 === e || e,
      r = {},
      o = !0,
      i = !1,
      a = void 0;
    try {
      for (var s, u = t[Symbol.iterator](); !(o = (s = u.next()).done); o = !0) {
        var c = s.value,
          l = c.language;
        n && (l = l.replace(/-.*/, "")), r[l] || (r[l] = 0), r[l] += c.nodes.reduce(function(t, e) {
          return t + e.length
        }, 0)
      }
    } catch (t) {
      i = !0, a = t
    } finally {
      try {
        !o && u.return && u.return()
      } finally {
        if (i) throw a
      }
    }
    return r
  }, e.default = function(t) {
    var e = t.containerElement,
      n = t.chunkRootIncludeFilter,
      i = void 0 === n ? r : n,
      a = t.chunkLeafExcludeFilter,
      s = i,
      u = void 0 === a ? o : a;
    if ("string" == typeof s) {
      var c = s;
      s = function(t) {
        return t.matches(c)
      }
    }
    if ("string" == typeof u) {
      var l = u;
      u = function(t) {
        return t.matches(l)
      }
    }
    var f = [],
      p = [],
      h = function(t, e) {
        if (/\w/.test(t.nodeValue)) {
          var n = p[p.length - 1];
          if (n) {
            var r = f[f.length - 1];
            r && r.language === e && n === r.root || (r = {
              language: e,
              nodes: [],
              root: n
            }, f.push(r)), r.nodes.push(t)
          }
        }
      };
    return function t(e) {
      var n = function(t) {
          for (var e = t; e && e.nodeType === Node.ELEMENT_NODE;) {
            if (e.lang) return e.lang.toLowerCase();
            e = e.parentNode
          }
          return null
        }(e),
        r = s(e);
      r && p.push(e);
      var o = !0,
        i = !1,
        a = void 0;
      try {
        for (var c, l = e.childNodes[Symbol.iterator](); !(o = (c = l.next()).done); o = !0) {
          var f = c.value;
          switch (f.nodeType) {
            case Node.ELEMENT_NODE:
              u(f) || t(f);
              break;
            case Node.TEXT_NODE:
              0 !== p.length && h(f, n)
          }
        }
      } catch (t) {
        i = !0, a = t
      } finally {
        try {
          !o && l.return && l.return()
        } finally {
          if (i) throw a
        }
      }
      r && p.pop()
    }(e), f
  };
  var r = "h1, h2, h3, h4, h5, h6, p, th, td, caption, li, blockquote, q, dt, dd, figcaption",
    o = "sup, sub"
}, function(t, e, n) {
  "use strict";
  Object.defineProperty(e, "__esModule", {
    value: !0
  }), e.isLoaded = function() {
    return o
  }, e.load = function() {
    return new Promise(function(t, e) {
      if (speechSynthesis.getVoices().length > 0) return o = !0, void t();
      r.push({
        resolve: t,
        reject: e
      })
    })
  };
  var r = [],
    o = !1,
    i = speechSynthesis.onvoiceschanged;
  speechSynthesis.onvoiceschanged = function(t) {
    i && i.call(this, t);
    var e = speechSynthesis.getVoices(),
      n = !0,
      a = !1,
      s = void 0;
    try {
      for (var u, c = r[Symbol.iterator](); !(n = (u = c.next()).done); n = !0) {
        var l = u.value,
          f = l.resolve,
          p = l.reject;
        e.length > 0 ? (o = !0, f()) : p()
      }
    } catch (t) {
      a = !0, s = t
    } finally {
      try {
        !n && c.return && c.return()
      } finally {
        if (a) throw s
      }
    }
  }
}, function(t, e, n) {
  "use strict";

  function r(t) {
    return t && t.__esModule ? t : {
      default: t
    }
  }

  function o(t, e, n) {
    return e in t ? Object.defineProperty(t, e, {
      value: n,
      enumerable: !0,
      configurable: !0,
      writable: !0
    }) : t[e] = n, t
  }
  Object.defineProperty(e, "__esModule", {
    value: !0
  });
  var i = function() {
      function t(t, e) {
        for (var n = 0; n < e.length; n++) {
          var r = e[n];
          r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(t, r.key, r)
        }
      }
      return function(e, n, r) {
        return n && t(e.prototype, n), r && t(e, r), e
      }
    }(),
    a = n(14),
    s = n(8),
    u = r(s),
    c = r(n(9)),
    l = r(n(55)),
    f = n(15),
    p = function(t) {
      function e() {
        return function(t, e) {
            if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function")
          }(this, e),
          function(t, e) {
            if (!t) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
            return !e || "object" != typeof e && "function" != typeof e ? t : e
          }(this, (e.__proto__ || Object.getPrototypeOf(e)).apply(this, arguments))
      }
      return function(t, e) {
        if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function, not " + typeof e);
        t.prototype = Object.create(e && e.prototype, {
          constructor: {
            value: t,
            enumerable: !1,
            writable: !0,
            configurable: !0
          }
        }), e && (Object.setPrototypeOf ? Object.setPrototypeOf(t, e) : t.__proto__ = e)
      }(e, s.Component), i(e, [{
        key: "componentWillMount",
        value: function() {
          this.idPrefix = "input" + (0, f.uniqueId)() + "-"
        }
      }, {
        key: "componentDidMount",
        value: function() {
          var t = this;
          this.updateDialogState(), this.dialog.showModal || "undefined" == typeof dialogPolyfill || dialogPolyfill.registerDialog(this.dialog), this.dialog.addEventListener("cancel", function(e) {
            e.preventDefault(), t.props.onHideSettings()
          })
        }
      }, {
        key: "componentDidUpdate",
        value: function() {
          this.updateDialogState()
        }
      }, {
        key: "updateDialogState",
        value: function() {
          !this.props.settingsShown && this.dialog.open ? (this.dialog.close(), this.previousActiveElement && !this.props.playing && this.previousActiveElement.focus()) : this.props.settingsShown && !this.dialog.open && (this.previousActiveElement = document.activeElement, this.dialog.showModal())
        }
      }, {
        key: "renderLanguageVoiceSelects",
        value: function() {
          var t = this;
          if (0 === this.props.availableVoices.length || 0 === this.props.presentLanguages.length) return null;
          var e = function(e) {
              var n = Object.assign({}, t.props.languageVoices, o({}, e.target.dataset.language, e.target.value));
              t.props.setProps({
                languageVoices: n
              })
            },
            n = [],
            r = function(r) {
              var o = t.props.availableVoices.filter(function(t) {
                return t.lang.startsWith(r)
              });
              if (0 === o.length) return "continue";
              var i = t.idPrefix + "voice-" + r;
              n.push(u.default.createElement("p", {
                key: r
              }, u.default.createElement("label", {
                className: "spoken-word-playback-controls__label",
                htmlFor: i
              }, (0, a.sprintf)((0, a.__)("Stimme (%s):"), r)), " ", u.default.createElement("select", {
                id: i,
                className: "spoken-word-playback-controls__input",
                "data-language": r,
                value: t.props.languageVoices[r],
                onBlur: e,
                onChange: e
              }, o.map(function(t) {
                return u.default.createElement("option", {
                  key: t.voiceURI,
                  value: t.voiceURI
                }, t.lang.includes("-") ? (0, a.sprintf)((0, a.__)("%s (%s)"), t.name, t.lang) : t.name)
              }))))
            },
            i = !0,
            s = !1,
            c = void 0;
          try {
            for (var l, f = this.props.presentLanguages[Symbol.iterator](); !(i = (l = f.next()).done); i = !0) r(l.value)
          } catch (t) {
            s = !0, c = t
          } finally {
            try {
              !i && f.return && f.return()
            } finally {
              if (s) throw c
            }
          }
          return n
        }
      }, {
        key: "renderSettings",
        value: function() {
          var t = this;
          if (!this.props.isDialogSupported) return null;
          var e = function(e) {
            !isNaN(e.target.valueAsNumber) && e.target.validity.valid && t.props.setProps(o({}, e.target.dataset.prop, e.target.valueAsNumber))
          };
          return u.default.createElement("dialog", {
            className: "spoken-word-playback-controls__dialog",
            ref: function(e) {
              t.dialog = e
            }
          }, u.default.createElement("h1", {
            className: "spoken-word-playback-controls__heading"
          }, u.default.createElement("a", {
            href: "https://github.com/westonruter/spoken-word",
            target: "_blank",
            rel: "noopener noreferrer"
          }, (0, a.__)("Spoken Word"))), u.default.createElement("p", null, u.default.createElement("label", {
            className: "spoken-word-playback-controls__label",
            htmlFor: this.idPrefix + "rate"
          }, (0, a.__)("Sprechgeschwindigkeit:")), " ", u.default.createElement("input", {
            id: this.idPrefix + "rate",
            className: "spoken-word-playback-controls__input",
            type: "number",
            "data-prop": "rate",
            value: this.props.rate,
            step: .1,
            min: .1,
            max: 10,
            onChange: e
          })), u.default.createElement("p", null, u.default.createElement("label", {
            className: "spoken-word-playback-controls__label",
            htmlFor: this.idPrefix + "pitch"
          }, (0, a.__)("Stimmhöhe:")), " ", u.default.createElement("input", {
            id: this.idPrefix + "pitch",
            className: "spoken-word-playback-controls__input",
            type: "number",
            "data-prop": "pitch",
            value: this.props.pitch,
            min: 0,
            max: 2,
            step: .1,
            onChange: e
          })), this.renderLanguageVoiceSelects(), u.default.createElement("button", {
            onClick: this.props.onHideSettings
          }, (0, a.__)("OK")))
        }
      }, {
        key: "render",
        value: function() {
          return u.default.createElement("fieldset", {
            className: ["spoken-word-playback-controls"].join(" ")
          }, u.default.createElement("legend", {
            className: "spoken-word-playback-controls__legend"
          }, (0, a.__)("Text to Speech")), u.default.createElement(l.default, {
            useDashicon: this.props.useDashicons,
            dashicon: this.props.playing ? "controls-pause" : "controls-play",
            //emoji: this.props.playing ? "" : "",
            label: this.props.playing ? (0, a.__)("Pause") : (0, a.__)("Play"),
            onClick: this.props.playing ? this.props.stop : this.props.play
          }), u.default.createElement(l.default, {
            useDashicon: this.props.useDashicons,
            dashicon: "controls-back",
            //emoji: "⏪",
            label: (0, a.__)("Previous"),
            onClick: this.props.previous
          }), u.default.createElement(l.default, {
            useDashicon: this.props.useDashicons,
            dashicon: "controls-forward",
            //emoji: "⏩",
            label: (0, a.__)("Forward"),
            onClick: this.props.next
          }), this.props.isDialogSupported ? u.default.createElement(l.default, {
            useDashicon: this.props.useDashicons,
            dashicon: "admin-settings",
            //emoji: "⚙",
            label: (0, a.__)("Settings"),
            onClick: this.props.onShowSettings
          }) : "", this.renderSettings())
        }
      }]), e
    }();
  e.default = p, p.propTypes = {
    playing: c.default.bool.isRequired,
    play: c.default.func.isRequired,
    onShowSettings: c.default.func.isRequired,
    onHideSettings: c.default.func.isRequired,
    stop: c.default.func.isRequired,
    previous: c.default.func.isRequired,
    next: c.default.func.isRequired,
    useDashicons: c.default.bool,
    settingsShown: c.default.bool,
    isDialogSupported: c.default.bool,
    presentLanguages: c.default.array.isRequired,
    availableVoices: c.default.array.isRequired,
    languageVoices: c.default.object.isRequired,
    setProps: c.default.func.isRequired
  }, p.defaultProps = {
    useDashicons: !1
  }
}, function(t, e, n) {
  "use strict";

  function r(t) {
    return t && t.__esModule ? t : {
      default: t
    }
  }
  Object.defineProperty(e, "__esModule", {
    value: !0
  });
  var o = function() {
      function t(t, e) {
        for (var n = 0; n < e.length; n++) {
          var r = e[n];
          r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(t, r.key, r)
        }
      }
      return function(e, n, r) {
        return n && t(e.prototype, n), r && t(e, r), e
      }
    }(),
    i = n(8),
    a = r(i),
    s = r(n(9)),
    u = function(t) {
      function e() {
        return function(t, e) {
            if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function")
          }(this, e),
          function(t, e) {
            if (!t) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
            return !e || "object" != typeof e && "function" != typeof e ? t : e
          }(this, (e.__proto__ || Object.getPrototypeOf(e)).apply(this, arguments))
      }
      return function(t, e) {
        if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function, not " + typeof e);
        t.prototype = Object.create(e && e.prototype, {
          constructor: {
            value: t,
            enumerable: !1,
            writable: !0,
            configurable: !0
          }
        }), e && (Object.setPrototypeOf ? Object.setPrototypeOf(t, e) : t.__proto__ = e)
      }(e, i.Component), o(e, [{
        key: "render",
        value: function() {
          var t = ["spoken-word-playback-controls__button"];
          return this.props.useDashicon ? t.push("spoken-word-playback-controls__button--dashicon") : (t.push("wp-exclude-emoji"), t.push("spoken-word-playback-controls__button--emoji")), a.default.createElement("button", {
            type: "button",
            className: t.join(" "),
            style: {},
            "aria-label": this.props.label,
            onClick: this.props.onClick
          }, this.props.useDashicon ? a.default.createElement("span", {
            className: "dashicons dashicons-" + this.props.dashicon
          }) : this.props.emoji)
        }
      }]), e
    }();
  e.default = u, u.propTypes = {
    label: s.default.string.isRequired,
    emoji: s.default.string.isRequired,
    dashicon: s.default.string.isRequired,
    useDashicon: s.default.bool.isRequired,
    onClick: s.default.func.isRequired
  }
}, function(t, e, n) {
  var r = n(57);
  t.exports = function(t, e) {
    return r(t, e)
  }
}, function(t, e, n) {
  function r(t, e, n, a, s) {
    return t === e || (null == t || null == e || !i(t) && !i(e) ? t != t && e != e : o(t, e, n, a, r, s))
  }
  var o = n(58),
    i = n(7);
  t.exports = r
}, function(t, e, n) {
  var r = n(59),
    o = n(22),
    i = n(94),
    a = n(98),
    s = n(120),
    u = n(13),
    c = n(23),
    l = n(25),
    f = 1,
    p = "[object Arguments]",
    h = "[object Array]",
    d = "[object Object]",
    y = Object.prototype.hasOwnProperty;
  t.exports = function(t, e, n, v, g, m) {
    var b = u(t),
      _ = u(e),
      x = b ? h : s(t),
      k = _ ? h : s(e),
      w = (x = x == p ? d : x) == d,
      S = (k = k == p ? d : k) == d,
      O = x == k;
    if (O && c(t)) {
      if (!c(e)) return !1;
      b = !0, w = !1
    }
    if (O && !w) return m || (m = new r), b || l(t) ? o(t, e, n, v, g, m) : i(t, e, x, n, v, g, m);
    if (!(n & f)) {
      var P = w && y.call(t, "__wrapped__"),
        j = S && y.call(e, "__wrapped__");
      if (P || j) {
        var E = P ? t.value() : t,
          C = j ? e.value() : e;
        return m || (m = new r), g(E, C, n, v, m)
      }
    }
    return !!O && (m || (m = new r), a(t, e, n, v, g, m))
  }
}, function(t, e, n) {
  function r(t) {
    var e = this.__data__ = new o(t);
    this.size = e.size
  }
  var o = n(2),
    i = n(65),
    a = n(66),
    s = n(67),
    u = n(68),
    c = n(69);
  r.prototype.clear = i, r.prototype.delete = a, r.prototype.get = s, r.prototype.has = u, r.prototype.set = c, t.exports = r
}, function(t, e) {
  t.exports = function() {
    this.__data__ = [], this.size = 0
  }
}, function(t, e, n) {
  var r = n(3),
    o = Array.prototype.splice;
  t.exports = function(t) {
    var e = this.__data__,
      n = r(e, t);
    return !(n < 0 || (n == e.length - 1 ? e.pop() : o.call(e, n, 1), --this.size, 0))
  }
}, function(t, e, n) {
  var r = n(3);
  t.exports = function(t) {
    var e = this.__data__,
      n = r(e, t);
    return n < 0 ? void 0 : e[n][1]
  }
}, function(t, e, n) {
  var r = n(3);
  t.exports = function(t) {
    return r(this.__data__, t) > -1
  }
}, function(t, e, n) {
  var r = n(3);
  t.exports = function(t, e) {
    var n = this.__data__,
      o = r(n, t);
    return o < 0 ? (++this.size, n.push([t, e])) : n[o][1] = e, this
  }
}, function(t, e, n) {
  var r = n(2);
  t.exports = function() {
    this.__data__ = new r, this.size = 0
  }
}, function(t, e) {
  t.exports = function(t) {
    var e = this.__data__,
      n = e.delete(t);
    return this.size = e.size, n
  }
}, function(t, e) {
  t.exports = function(t) {
    return this.__data__.get(t)
  }
}, function(t, e) {
  t.exports = function(t) {
    return this.__data__.has(t)
  }
}, function(t, e, n) {
  var r = n(2),
    o = n(11),
    i = n(21),
    a = 200;
  t.exports = function(t, e) {
    var n = this.__data__;
    if (n instanceof r) {
      var s = n.__data__;
      if (!o || s.length < a - 1) return s.push([t, e]), this.size = ++n.size, this;
      n = this.__data__ = new i(s)
    }
    return n.set(t, e), this.size = n.size, this
  }
}, function(t, e, n) {
  var r = n(17),
    o = n(74),
    i = n(19),
    a = n(20),
    s = /^\[object .+?Constructor\]$/,
    u = Function.prototype,
    c = Object.prototype,
    l = u.toString,
    f = c.hasOwnProperty,
    p = RegExp("^" + l.call(f).replace(/[\\^$.*+?()[\]{}|]/g, "\\$&").replace(/hasOwnProperty|(function).*?(?=\\\()| for .+?(?=\\\])/g, "$1.*?") + "$");
  t.exports = function(t) {
    return !(!i(t) || o(t)) && (r(t) ? p : s).test(a(t))
  }
}, function(t, e) {
  var n;
  n = function() {
    return this
  }();
  try {
    n = n || Function("return this")() || (0, eval)("this")
  } catch (t) {
    "object" == typeof window && (n = window)
  }
  t.exports = n
}, function(t, e, n) {
  var r = n(12),
    o = Object.prototype,
    i = o.hasOwnProperty,
    a = o.toString,
    s = r ? r.toStringTag : void 0;
  t.exports = function(t) {
    var e = i.call(t, s),
      n = t[s];
    try {
      t[s] = void 0;
      var r = !0
    } catch (t) {}
    var o = a.call(t);
    return r && (e ? t[s] = n : delete t[s]), o
  }
}, function(t, e) {
  var n = Object.prototype.toString;
  t.exports = function(t) {
    return n.call(t)
  }
}, function(t, e, n) {
  var r = n(75),
    o = function() {
      var t = /[^.]+$/.exec(r && r.keys && r.keys.IE_PROTO || "");
      return t ? "Symbol(src)_1." + t : ""
    }();
  t.exports = function(t) {
    return !!o && o in t
  }
}, function(t, e, n) {
  var r = n(0)["__core-js_shared__"];
  t.exports = r
}, function(t, e) {
  t.exports = function(t, e) {
    return null == t ? void 0 : t[e]
  }
}, function(t, e, n) {
  var r = n(78),
    o = n(2),
    i = n(11);
  t.exports = function() {
    this.size = 0, this.__data__ = {
      hash: new r,
      map: new(i || o),
      string: new r
    }
  }
}, function(t, e, n) {
  function r(t) {
    var e = -1,
      n = null == t ? 0 : t.length;
    for (this.clear(); ++e < n;) {
      var r = t[e];
      this.set(r[0], r[1])
    }
  }
  var o = n(79),
    i = n(80),
    a = n(81),
    s = n(82),
    u = n(83);
  r.prototype.clear = o, r.prototype.delete = i, r.prototype.get = a, r.prototype.has = s, r.prototype.set = u, t.exports = r
}, function(t, e, n) {
  var r = n(5);
  t.exports = function() {
    this.__data__ = r ? r(null) : {}, this.size = 0
  }
}, function(t, e) {
  t.exports = function(t) {
    var e = this.has(t) && delete this.__data__[t];
    return this.size -= e ? 1 : 0, e
  }
}, function(t, e, n) {
  var r = n(5),
    o = "__lodash_hash_undefined__",
    i = Object.prototype.hasOwnProperty;
  t.exports = function(t) {
    var e = this.__data__;
    if (r) {
      var n = e[t];
      return n === o ? void 0 : n
    }
    return i.call(e, t) ? e[t] : void 0
  }
}, function(t, e, n) {
  var r = n(5),
    o = Object.prototype.hasOwnProperty;
  t.exports = function(t) {
    var e = this.__data__;
    return r ? void 0 !== e[t] : o.call(e, t)
  }
}, function(t, e, n) {
  var r = n(5),
    o = "__lodash_hash_undefined__";
  t.exports = function(t, e) {
    var n = this.__data__;
    return this.size += this.has(t) ? 0 : 1, n[t] = r && void 0 === e ? o : e, this
  }
}, function(t, e, n) {
  var r = n(6);
  t.exports = function(t) {
    var e = r(this, t).delete(t);
    return this.size -= e ? 1 : 0, e
  }
}, function(t, e) {
  t.exports = function(t) {
    var e = typeof t;
    return "string" == e || "number" == e || "symbol" == e || "boolean" == e ? "__proto__" !== t : null === t
  }
}, function(t, e, n) {
  var r = n(6);
  t.exports = function(t) {
    return r(this, t).get(t)
  }
}, function(t, e, n) {
  var r = n(6);
  t.exports = function(t) {
    return r(this, t).has(t)
  }
}, function(t, e, n) {
  var r = n(6);
  t.exports = function(t, e) {
    var n = r(this, t),
      o = n.size;
    return n.set(t, e), this.size += n.size == o ? 0 : 1, this
  }
}, function(t, e, n) {
  function r(t) {
    var e = -1,
      n = null == t ? 0 : t.length;
    for (this.__data__ = new o; ++e < n;) this.add(t[e])
  }
  var o = n(21),
    i = n(90),
    a = n(91);
  r.prototype.add = r.prototype.push = i, r.prototype.has = a, t.exports = r
}, function(t, e) {
  var n = "__lodash_hash_undefined__";
  t.exports = function(t) {
    return this.__data__.set(t, n), this
  }
}, function(t, e) {
  t.exports = function(t) {
    return this.__data__.has(t)
  }
}, function(t, e) {
  t.exports = function(t, e) {
    for (var n = -1, r = null == t ? 0 : t.length; ++n < r;)
      if (e(t[n], n, t)) return !0;
    return !1
  }
}, function(t, e) {
  t.exports = function(t, e) {
    return t.has(e)
  }
}, function(t, e, n) {
  var r = n(12),
    o = n(95),
    i = n(16),
    a = n(22),
    s = n(96),
    u = n(97),
    c = 1,
    l = 2,
    f = "[object Boolean]",
    p = "[object Date]",
    h = "[object Error]",
    d = "[object Map]",
    y = "[object Number]",
    v = "[object RegExp]",
    g = "[object Set]",
    m = "[object String]",
    b = "[object Symbol]",
    _ = "[object ArrayBuffer]",
    x = "[object DataView]",
    k = r ? r.prototype : void 0,
    w = k ? k.valueOf : void 0;
  t.exports = function(t, e, n, r, k, S, O) {
    switch (n) {
      case x:
        if (t.byteLength != e.byteLength || t.byteOffset != e.byteOffset) return !1;
        t = t.buffer, e = e.buffer;
      case _:
        return !(t.byteLength != e.byteLength || !S(new o(t), new o(e)));
      case f:
      case p:
      case y:
        return i(+t, +e);
      case h:
        return t.name == e.name && t.message == e.message;
      case v:
      case m:
        return t == e + "";
      case d:
        var P = s;
      case g:
        var j = r & c;
        if (P || (P = u), t.size != e.size && !j) return !1;
        var E = O.get(t);
        if (E) return E == e;
        r |= l, O.set(t, e);
        var C = a(P(t), P(e), r, k, S, O);
        return O.delete(t), C;
      case b:
        if (w) return w.call(t) == w.call(e)
    }
    return !1
  }
}, function(t, e, n) {
  var r = n(0).Uint8Array;
  t.exports = r
}, function(t, e) {
  t.exports = function(t) {
    var e = -1,
      n = Array(t.size);
    return t.forEach(function(t, r) {
      n[++e] = [r, t]
    }), n
  }
}, function(t, e) {
  t.exports = function(t) {
    var e = -1,
      n = Array(t.size);
    return t.forEach(function(t) {
      n[++e] = t
    }), n
  }
}, function(t, e, n) {
  var r = n(99),
    o = 1,
    i = Object.prototype.hasOwnProperty;
  t.exports = function(t, e, n, a, s, u) {
    var c = n & o,
      l = r(t),
      f = l.length;
    if (f != r(e).length && !c) return !1;
    for (var p = f; p--;) {
      var h = l[p];
      if (!(c ? h in e : i.call(e, h))) return !1
    }
    var d = u.get(t);
    if (d && u.get(e)) return d == e;
    var y = !0;
    u.set(t, e), u.set(e, t);
    for (var v = c; ++p < f;) {
      var g = t[h = l[p]],
        m = e[h];
      if (a) var b = c ? a(m, g, h, e, t, u) : a(g, m, h, t, e, u);
      if (!(void 0 === b ? g === m || s(g, m, n, a, u) : b)) {
        y = !1;
        break
      }
      v || (v = "constructor" == h)
    }
    if (y && !v) {
      var _ = t.constructor,
        x = e.constructor;
      _ != x && "constructor" in t && "constructor" in e && !("function" == typeof _ && _ instanceof _ && "function" == typeof x && x instanceof x) && (y = !1)
    }
    return u.delete(t), u.delete(e), y
  }
}, function(t, e, n) {
  var r = n(100),
    o = n(102),
    i = n(105);
  t.exports = function(t) {
    return r(t, i, o)
  }
}, function(t, e, n) {
  var r = n(101),
    o = n(13);
  t.exports = function(t, e, n) {
    var i = e(t);
    return o(t) ? i : r(i, n(t))
  }
}, function(t, e) {
  t.exports = function(t, e) {
    for (var n = -1, r = e.length, o = t.length; ++n < r;) t[o + n] = e[n];
    return t
  }
}, function(t, e, n) {
  var r = n(103),
    o = n(104),
    i = Object.prototype.propertyIsEnumerable,
    a = Object.getOwnPropertySymbols,
    s = a ? function(t) {
      return null == t ? [] : (t = Object(t), r(a(t), function(e) {
        return i.call(t, e)
      }))
    } : o;
  t.exports = s
}, function(t, e) {
  t.exports = function(t, e) {
    for (var n = -1, r = null == t ? 0 : t.length, o = 0, i = []; ++n < r;) {
      var a = t[n];
      e(a, n, t) && (i[o++] = a)
    }
    return i
  }
}, function(t, e) {
  t.exports = function() {
    return []
  }
}, function(t, e, n) {
  var r = n(106),
    o = n(115),
    i = n(119);
  t.exports = function(t) {
    return i(t) ? r(t) : o(t)
  }
}, function(t, e, n) {
  var r = n(107),
    o = n(108),
    i = n(13),
    a = n(23),
    s = n(111),
    u = n(25),
    c = Object.prototype.hasOwnProperty;
  t.exports = function(t, e) {
    var n = i(t),
      l = !n && o(t),
      f = !n && !l && a(t),
      p = !n && !l && !f && u(t),
      h = n || l || f || p,
      d = h ? r(t.length, String) : [],
      y = d.length;
    for (var v in t) !e && !c.call(t, v) || h && ("length" == v || f && ("offset" == v || "parent" == v) || p && ("buffer" == v || "byteLength" == v || "byteOffset" == v) || s(v, y)) || d.push(v);
    return d
  }
}, function(t, e) {
  t.exports = function(t, e) {
    for (var n = -1, r = Array(t); ++n < t;) r[n] = e(n);
    return r
  }
}, function(t, e, n) {
  var r = n(109),
    o = n(7),
    i = Object.prototype,
    a = i.hasOwnProperty,
    s = i.propertyIsEnumerable,
    u = r(function() {
      return arguments
    }()) ? r : function(t) {
      return o(t) && a.call(t, "callee") && !s.call(t, "callee")
    };
  t.exports = u
}, function(t, e, n) {
  var r = n(4),
    o = n(7),
    i = "[object Arguments]";
  t.exports = function(t) {
    return o(t) && r(t) == i
  }
}, function(t, e) {
  t.exports = function() {
    return !1
  }
}, function(t, e) {
  var n = 9007199254740991,
    r = /^(?:0|[1-9]\d*)$/;
  t.exports = function(t, e) {
    return !!(e = null == e ? n : e) && ("number" == typeof t || r.test(t)) && t > -1 && t % 1 == 0 && t < e
  }
}, function(t, e, n) {
  var r = n(4),
    o = n(26),
    i = n(7),
    a = {};
  a["[object Float32Array]"] = a["[object Float64Array]"] = a["[object Int8Array]"] = a["[object Int16Array]"] = a["[object Int32Array]"] = a["[object Uint8Array]"] = a["[object Uint8ClampedArray]"] = a["[object Uint16Array]"] = a["[object Uint32Array]"] = !0, a["[object Arguments]"] = a["[object Array]"] = a["[object ArrayBuffer]"] = a["[object Boolean]"] = a["[object DataView]"] = a["[object Date]"] = a["[object Error]"] = a["[object Function]"] = a["[object Map]"] = a["[object Number]"] = a["[object Object]"] = a["[object RegExp]"] = a["[object Set]"] = a["[object String]"] = a["[object WeakMap]"] = !1, t.exports = function(t) {
    return i(t) && o(t.length) && !!a[r(t)]
  }
}, function(t, e) {
  t.exports = function(t) {
    return function(e) {
      return t(e)
    }
  }
}, function(t, e, n) {
  (function(t) {
    var r = n(18),
      o = "object" == typeof e && e && !e.nodeType && e,
      i = o && "object" == typeof t && t && !t.nodeType && t,
      a = i && i.exports === o && r.process,
      s = function() {
        try {
          return a && a.binding && a.binding("util")
        } catch (t) {}
      }();
    t.exports = s
  }).call(e, n(24)(t))
}, function(t, e, n) {
  var r = n(116),
    o = n(117),
    i = Object.prototype.hasOwnProperty;
  t.exports = function(t) {
    if (!r(t)) return o(t);
    var e = [];
    for (var n in Object(t)) i.call(t, n) && "constructor" != n && e.push(n);
    return e
  }
}, function(t, e) {
  var n = Object.prototype;
  t.exports = function(t) {
    var e = t && t.constructor;
    return t === ("function" == typeof e && e.prototype || n)
  }
}, function(t, e, n) {
  var r = n(118)(Object.keys, Object);
  t.exports = r
}, function(t, e) {
  t.exports = function(t, e) {
    return function(n) {
      return t(e(n))
    }
  }
}, function(t, e, n) {
  var r = n(17),
    o = n(26);
  t.exports = function(t) {
    return null != t && o(t.length) && !r(t)
  }
}, function(t, e, n) {
  var r = n(121),
    o = n(11),
    i = n(122),
    a = n(123),
    s = n(124),
    u = n(4),
    c = n(20),
    l = "[object Promise]",
    f = "[object WeakMap]",
    p = "[object DataView]",
    h = c(r),
    d = c(o),
    y = c(i),
    v = c(a),
    g = c(s),
    m = u;
  (r && m(new r(new ArrayBuffer(1))) != p || o && "[object Map]" != m(new o) || i && m(i.resolve()) != l || a && "[object Set]" != m(new a) || s && m(new s) != f) && (m = function(t) {
    var e = u(t),
      n = "[object Object]" == e ? t.constructor : void 0,
      r = n ? c(n) : "";
    if (r) switch (r) {
      case h:
        return p;
      case d:
        return "[object Map]";
      case y:
        return l;
      case v:
        return "[object Set]";
      case g:
        return f
    }
    return e
  }), t.exports = m
}, function(t, e, n) {
  var r = n(1)(n(0), "DataView");
  t.exports = r
}, function(t, e, n) {
  var r = n(1)(n(0), "Promise");
  t.exports = r
}, function(t, e, n) {
  var r = n(1)(n(0), "Set");
  t.exports = r
}, function(t, e, n) {
  var r = n(1)(n(0), "WeakMap");
  t.exports = r
}]);
//# sourceMappingURL=spoken-word.js.map