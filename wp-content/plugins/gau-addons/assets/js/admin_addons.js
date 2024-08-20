/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./node_modules/js-cookie/dist/js.cookie.mjs":
/*!***************************************************!*\
  !*** ./node_modules/js-cookie/dist/js.cookie.mjs ***!
  \***************************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ api; }
/* harmony export */ });
/*! js-cookie v3.0.5 | MIT */
/* eslint-disable no-var */
function assign (target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i];
    for (var key in source) {
      target[key] = source[key];
    }
  }
  return target
}
/* eslint-enable no-var */

/* eslint-disable no-var */
var defaultConverter = {
  read: function (value) {
    if (value[0] === '"') {
      value = value.slice(1, -1);
    }
    return value.replace(/(%[\dA-F]{2})+/gi, decodeURIComponent)
  },
  write: function (value) {
    return encodeURIComponent(value).replace(
      /%(2[346BF]|3[AC-F]|40|5[BDE]|60|7[BCD])/g,
      decodeURIComponent
    )
  }
};
/* eslint-enable no-var */

/* eslint-disable no-var */

function init (converter, defaultAttributes) {
  function set (name, value, attributes) {
    if (typeof document === 'undefined') {
      return
    }

    attributes = assign({}, defaultAttributes, attributes);

    if (typeof attributes.expires === 'number') {
      attributes.expires = new Date(Date.now() + attributes.expires * 864e5);
    }
    if (attributes.expires) {
      attributes.expires = attributes.expires.toUTCString();
    }

    name = encodeURIComponent(name)
      .replace(/%(2[346B]|5E|60|7C)/g, decodeURIComponent)
      .replace(/[()]/g, escape);

    var stringifiedAttributes = '';
    for (var attributeName in attributes) {
      if (!attributes[attributeName]) {
        continue
      }

      stringifiedAttributes += '; ' + attributeName;

      if (attributes[attributeName] === true) {
        continue
      }

      // Considers RFC 6265 section 5.2:
      // ...
      // 3.  If the remaining unparsed-attributes contains a %x3B (";")
      //     character:
      // Consume the characters of the unparsed-attributes up to,
      // not including, the first %x3B (";") character.
      // ...
      stringifiedAttributes += '=' + attributes[attributeName].split(';')[0];
    }

    return (document.cookie =
      name + '=' + converter.write(value, name) + stringifiedAttributes)
  }

  function get (name) {
    if (typeof document === 'undefined' || (arguments.length && !name)) {
      return
    }

    // To prevent the for loop in the first place assign an empty array
    // in case there are no cookies at all.
    var cookies = document.cookie ? document.cookie.split('; ') : [];
    var jar = {};
    for (var i = 0; i < cookies.length; i++) {
      var parts = cookies[i].split('=');
      var value = parts.slice(1).join('=');

      try {
        var found = decodeURIComponent(parts[0]);
        jar[found] = converter.read(value, found);

        if (name === found) {
          break
        }
      } catch (e) {}
    }

    return name ? jar[name] : jar
  }

  return Object.create(
    {
      set,
      get,
      remove: function (name, attributes) {
        set(
          name,
          '',
          assign({}, attributes, {
            expires: -1
          })
        );
      },
      withAttributes: function (attributes) {
        return init(this.converter, assign({}, this.attributes, attributes))
      },
      withConverter: function (converter) {
        return init(assign({}, this.converter, converter), this.attributes)
      }
    },
    {
      attributes: { value: Object.freeze(defaultAttributes) },
      converter: { value: Object.freeze(converter) }
    }
  )
}

var api = init(defaultConverter, { path: '/' });
/* eslint-enable no-var */




/***/ }),

/***/ "./node_modules/nanoid/index.browser.js":
/*!**********************************************!*\
  !*** ./node_modules/nanoid/index.browser.js ***!
  \**********************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   customAlphabet: function() { return /* binding */ customAlphabet; },
/* harmony export */   customRandom: function() { return /* binding */ customRandom; },
/* harmony export */   nanoid: function() { return /* binding */ nanoid; },
/* harmony export */   random: function() { return /* binding */ random; },
/* harmony export */   urlAlphabet: function() { return /* reexport safe */ _url_alphabet_index_js__WEBPACK_IMPORTED_MODULE_0__.urlAlphabet; }
/* harmony export */ });
/* harmony import */ var _url_alphabet_index_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./url-alphabet/index.js */ "./node_modules/nanoid/url-alphabet/index.js");


let random = bytes => crypto.getRandomValues(new Uint8Array(bytes))
let customRandom = (alphabet, defaultSize, getRandom) => {
  let mask = (2 << (Math.log(alphabet.length - 1) / Math.LN2)) - 1
  let step = -~((1.6 * mask * defaultSize) / alphabet.length)
  return (size = defaultSize) => {
    let id = ''
    while (true) {
      let bytes = getRandom(step)
      let j = step
      while (j--) {
        id += alphabet[bytes[j] & mask] || ''
        if (id.length === size) return id
      }
    }
  }
}
let customAlphabet = (alphabet, size = 21) =>
  customRandom(alphabet, size, random)
let nanoid = (size = 21) => {
  let id = ''
  let bytes = crypto.getRandomValues(new Uint8Array(size))
  while (size--) {
    id += _url_alphabet_index_js__WEBPACK_IMPORTED_MODULE_0__.urlAlphabet[bytes[size] & 63]
  }
  return id
}


/***/ }),

/***/ "./node_modules/nanoid/url-alphabet/index.js":
/*!***************************************************!*\
  !*** ./node_modules/nanoid/url-alphabet/index.js ***!
  \***************************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   urlAlphabet: function() { return /* binding */ urlAlphabet; }
/* harmony export */ });
const urlAlphabet =
  'useandom-26T198340PX75pxJACKVERYMINDBUSHWOLF_GQZbfghjklqvwyzrict'


/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/define property getters */
/******/ 	!function() {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = function(exports, definition) {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	!function() {
/******/ 		__webpack_require__.o = function(obj, prop) { return Object.prototype.hasOwnProperty.call(obj, prop); }
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	!function() {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = function(exports) {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	}();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
/*!********************************************************************!*\
  !*** ./wp-content/plugins/gau-addons/resources/js/admin_addons.js ***!
  \********************************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var nanoid__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! nanoid */ "./node_modules/nanoid/index.browser.js");
/* harmony import */ var js_cookie__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! js-cookie */ "./node_modules/js-cookie/dist/js.cookie.mjs");
function _slicedToArray(r, e) { return _arrayWithHoles(r) || _iterableToArrayLimit(r, e) || _unsupportedIterableToArray(r, e) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _iterableToArrayLimit(r, l) { var t = null == r ? null : "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (null != t) { var e, n, i, u, a = [], f = !0, o = !1; try { if (i = (t = t.call(r)).next, 0 === l) { if (Object(t) !== t) return; f = !1; } else for (; !(f = (e = i.call(t)).done) && (a.push(e.value), a.length !== l); f = !0); } catch (r) { o = !0, n = r; } finally { try { if (!f && null != t["return"] && (u = t["return"](), Object(u) !== u)) return; } finally { if (o) throw n; } } return a; } }
function _arrayWithHoles(r) { if (Array.isArray(r)) return r; }
function _toConsumableArray(r) { return _arrayWithoutHoles(r) || _iterableToArray(r) || _unsupportedIterableToArray(r) || _nonIterableSpread(); }
function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _iterableToArray(r) { if ("undefined" != typeof Symbol && null != r[Symbol.iterator] || null != r["@@iterator"]) return Array.from(r); }
function _arrayWithoutHoles(r) { if (Array.isArray(r)) return _arrayLikeToArray(r); }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }


Object.assign(window, {
  Cookies: js_cookie__WEBPACK_IMPORTED_MODULE_0__["default"]
});
jQuery(function ($) {
  /**
   * @param el
   * @return {*|jQuery}
   */
  function rand_element_init(el) {
    var $el = $(el);
    var _rand = (0,nanoid__WEBPACK_IMPORTED_MODULE_1__.nanoid)(9);
    $el.addClass(_rand);
    var _id = $el.attr('id');
    if (!_id) {
      _id = _rand;
      $el.attr('id', _id);
    }
    return _id;
  }
  if (typeof codemirror_settings !== 'undefined') {
    var codemirror_css = _toConsumableArray(document.querySelectorAll('.codemirror_css'));
    _.each(codemirror_css, function (el, index) {
      // Initialize the random element
      rand_element_init(el);

      // Clone the settings if they exist, otherwise create an empty object
      var editorSettings = codemirror_settings.codemirror_css ? _.clone(codemirror_settings.codemirror_css) : {};

      // Extend the settings with additional CodeMirror options
      editorSettings.codemirror = _.extend({}, editorSettings.codemirror, {
        indentUnit: 3,
        tabSize: 3,
        autoRefresh: true
      });

      // Initialize the CodeMirror editor
      wp.codeEditor.initialize(el, editorSettings);
    });
    var codemirror_html = _toConsumableArray(document.querySelectorAll('.codemirror_html'));
    _.each(codemirror_html, function (el, index) {
      // Initialize the random element
      rand_element_init(el);

      // Clone the settings if they exist, otherwise create an empty object
      var editorSettings = codemirror_settings.codemirror_html ? _.clone(codemirror_settings.codemirror_html) : {};

      // Extend the settings with additional CodeMirror options
      editorSettings.codemirror = _.extend({}, editorSettings.codemirror, {
        indentUnit: 3,
        tabSize: 3,
        autoRefresh: true
      });

      // Initialize the CodeMirror editor
      wp.codeEditor.initialize(el, editorSettings);
    });
  }
  $.fn.fadeOutAndRemove = function (speed) {
    return this.fadeOut(speed, function () {
      $(this).remove();
    });
  };
  $.fn.serializeObject = function () {
    var obj = {};
    var array = this.serializeArray();
    $.each(array, function () {
      var name = this.name;
      var value = this.value || '';

      // Check if the name ends with []
      if (name.indexOf('[]') > -1) {
        name = name.replace('[]', '');

        // Ensure the object property is an array
        if (!obj[name]) {
          obj[name] = [];
        }

        // Push the value into the array
        obj[name].push(value);
      } else {
        // Check if the object already has a property with the given name
        if (obj[name] !== undefined) {
          if (!Array.isArray(obj[name])) {
            obj[name] = [obj[name]];
          }
          obj[name].push(value);
        } else {
          obj[name] = value;
        }
      }
    });
    return obj;
  };

  // hide notice
  $(document).on('click', '.notice-dismiss', function (e) {
    var _$$closest;
    (_$$closest = $(this).closest('.notice.is-dismissible')) === null || _$$closest === void 0 || _$$closest.fadeOutAndRemove(400);
  });

  // ajax submit settings
  $(document).on('submit', '#_settings_form', function (e) {
    e.preventDefault();
    var $this = $(this);
    var btn_submit = $this.find('button[name="_submit_settings"]');
    var button_text = btn_submit.html();
    var button_text_loading = '<i class="fa-solid fa-spinner fa-spin-pulse"></i>';
    btn_submit.prop('disabled', true).html(button_text_loading);
    $.ajax({
      type: 'POST',
      url: ajaxurl,
      data: {
        action: 'submit_settings',
        _data: $this.serializeObject(),
        _ajax_nonce: $this.find('input[name="_wpnonce"]').val(),
        _wp_http_referer: $this.find('input[name="_wp_http_referer"]').val()
      }
    }).done(function (data) {
      btn_submit.prop('disabled', false).html(button_text);
      $this.find('#_content').prepend(data);

      // dismissible auto hide
      setTimeout(function () {
        var _$this$find$find;
        (_$this$find$find = $this.find('#_content').find('.dismissible-auto')) === null || _$this$find$find === void 0 || _$this$find$find.fadeOutAndRemove(400);
      }, 4000);
    }).fail(function (jqXHR, textStatus, errorThrown) {
      btn_submit.prop('disabled', false).html(button_text);
      console.log(errorThrown);
    });
  });

  // filter tabs
  var filter_tabs = $('.filter-tabs');
  $.each(filter_tabs, function (i, el) {
    var $el = $(el),
      _id = rand_element_init(el),
      $nav = $el.find('.tabs-nav'),
      $content = $el.find('.tabs-content');
    var _cookie = "cookie_".concat(_id, "_").concat(i);
    var cookieValue = js_cookie__WEBPACK_IMPORTED_MODULE_0__["default"].get(_cookie);
    if (!cookieValue) {
      cookieValue = $nav.find('a:first').attr('href');
      js_cookie__WEBPACK_IMPORTED_MODULE_0__["default"].set(_cookie, cookieValue, {
        expires: 7,
        path: ''
      });
    }
    $nav.find('a.current').removeClass('current');
    $nav.find("a[href=\"".concat(cookieValue, "\"]")).addClass('current');
    $nav.find('a').on('click', function (e) {
      var _$content$find$remove, _$$addClass;
      e.preventDefault();
      var $this = $(this);
      var hash = $this.attr('href');
      js_cookie__WEBPACK_IMPORTED_MODULE_0__["default"].set(_cookie, hash, {
        expires: 7,
        path: ''
      });
      $nav.find('a.current').removeClass('current');
      (_$content$find$remove = $content.find('.tabs-panel:visible').removeClass('show')) === null || _$content$find$remove === void 0 || _$content$find$remove.hide();
      (_$$addClass = $($this.attr('href')).addClass('show')) === null || _$$addClass === void 0 || _$$addClass.show();
      $this.addClass('current');
    }).filter('.current').trigger('click');
  });

  // select2 multiple
  var select2_multiple = $('.select2-multiple');
  $.each(select2_multiple, function (i, el) {
    $(el).select2({
      multiple: true,
      allowClear: true,
      width: 'resolve',
      dropdownAutoWidth: true,
      placeholder: $(el).attr('placeholder')
    });
  });

  // select2 tags
  var select2_tags = $('.select2-tags');
  $.each(select2_tags, function (i, el) {
    $(el).select2({
      multiple: true,
      tags: true,
      allowClear: true,
      width: 'resolve',
      dropdownAutoWidth: true,
      placeholder: $(el).attr('placeholder')
    });
  });

  // select2 IPs
  var select2_ips = $('.select2-ips');
  $.each(select2_ips, function (i, el) {
    $(el).select2({
      multiple: true,
      tags: true,
      allowClear: true,
      width: 'resolve',
      dropdownAutoWidth: true,
      placeholder: $(el).attr('placeholder'),
      createTag: function createTag(params) {
        var term = $.trim(params.term);

        // Validate the term as an IP address or range
        if (isValidIPRange(term)) {
          return {
            id: term,
            text: term
          };
        } else {
          return null;
        }
      }
    });
  });

  // select2 emails
  var select2_emails = $('.select2-emails');
  $.each(select2_emails, function (i, el) {
    $(el).select2({
      multiple: true,
      tags: true,
      allowClear: true,
      width: 'resolve',
      dropdownAutoWidth: true,
      placeholder: $(el).attr('placeholder'),
      createTag: function createTag(params) {
        var term = $.trim(params.term);
        if (isValidEmail(term)) {
          return {
            id: term,
            text: term
          };
        } else {
          return null;
        }
      }
    });
  });
});

/**
 * Validate email address
 *
 * @param {string} email
 * @returns {boolean}
 */
function isValidEmail(email) {
  var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailPattern.test(email);
}

/**
 * validate IP range (IPv4)
 *
 * @param range
 * @returns {boolean}
 */
function isValidIPRange(range) {
  var ipPattern = /^(25[0-5]|2[0-4]\d|1\d{2}|\d{1,2})\.(25[0-5]|2[0-4]\d|1\d{2}|\d{1,2})\.(25[0-5]|2[0-4]\d|1\d{2}|\d{1,2})\.(25[0-5]|2[0-4]\d|1\d{2}|\d{1,2})$/;
  var rangePattern = /^(25[0-5]|2[0-4]\d|1\d{2}|\d{1,2})\.(25[0-5]|2[0-4]\d|1\d{2}|\d{1,2})\.(25[0-5]|2[0-4]\d|1\d{2}|\d{1,2})\.(25[0-5]|2[0-4]\d|1\d{2}|\d{1,2})-(\d|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5])$/;
  var cidrPattern = /^(25[0-5]|2[0-4]\d|1\d{2}|\d{1,2})\.(25[0-5]|2[0-4]\d|1\d{2}|\d{1,2})\.(25[0-5]|2[0-4]\d|1\d{2}|\d{1,2})\.(25[0-5]|2[0-4]\d|1\d{2}|\d{1,2})\/(\d|[1-2]\d|3[0-2])$/;
  if (ipPattern.test(range)) {
    return true;
  }
  if (rangePattern.test(range)) {
    var _range$split = range.split('-'),
      _range$split2 = _slicedToArray(_range$split, 2),
      startIP = _range$split2[0],
      endRange = _range$split2[1];
    var endIP = startIP.split('.').slice(0, 3).join('.') + '.' + endRange;
    return compareIPs(startIP, endIP) < 0;
  }
  return cidrPattern.test(range);
}

/**
 * compare two IP addresses
 *
 * @param ip1
 * @param ip2
 * @returns {number}
 */
function compareIPs(ip1, ip2) {
  var ip1Parts = ip1.split('.').map(Number);
  var ip2Parts = ip2.split('.').map(Number);
  for (var i = 0; i < 4; i++) {
    if (ip1Parts[i] < ip2Parts[i]) return -1;
    if (ip1Parts[i] > ip2Parts[i]) return 1;
  }
  return 0;
}
/******/ })()
;
//# sourceMappingURL=admin_addons.js.map