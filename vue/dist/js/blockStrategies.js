/******/ (function(modules) { // webpackBootstrap
/******/ 	// install a JSONP callback for chunk loading
/******/ 	function webpackJsonpCallback(data) {
/******/ 		var chunkIds = data[0];
/******/ 		var moreModules = data[1];
/******/ 		var executeModules = data[2];
/******/
/******/ 		// add "moreModules" to the modules object,
/******/ 		// then flag all "chunkIds" as loaded and fire callback
/******/ 		var moduleId, chunkId, i = 0, resolves = [];
/******/ 		for(;i < chunkIds.length; i++) {
/******/ 			chunkId = chunkIds[i];
/******/ 			if(Object.prototype.hasOwnProperty.call(installedChunks, chunkId) && installedChunks[chunkId]) {
/******/ 				resolves.push(installedChunks[chunkId][0]);
/******/ 			}
/******/ 			installedChunks[chunkId] = 0;
/******/ 		}
/******/ 		for(moduleId in moreModules) {
/******/ 			if(Object.prototype.hasOwnProperty.call(moreModules, moduleId)) {
/******/ 				modules[moduleId] = moreModules[moduleId];
/******/ 			}
/******/ 		}
/******/ 		if(parentJsonpFunction) parentJsonpFunction(data);
/******/
/******/ 		while(resolves.length) {
/******/ 			resolves.shift()();
/******/ 		}
/******/
/******/ 		// add entry modules from loaded chunk to deferred list
/******/ 		deferredModules.push.apply(deferredModules, executeModules || []);
/******/
/******/ 		// run deferred modules when all chunks ready
/******/ 		return checkDeferredModules();
/******/ 	};
/******/ 	function checkDeferredModules() {
/******/ 		var result;
/******/ 		for(var i = 0; i < deferredModules.length; i++) {
/******/ 			var deferredModule = deferredModules[i];
/******/ 			var fulfilled = true;
/******/ 			for(var j = 1; j < deferredModule.length; j++) {
/******/ 				var depId = deferredModule[j];
/******/ 				if(installedChunks[depId] !== 0) fulfilled = false;
/******/ 			}
/******/ 			if(fulfilled) {
/******/ 				deferredModules.splice(i--, 1);
/******/ 				result = __webpack_require__(__webpack_require__.s = deferredModule[0]);
/******/ 			}
/******/ 		}
/******/
/******/ 		return result;
/******/ 	}
/******/
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// object to store loaded and loading chunks
/******/ 	// undefined = chunk not loaded, null = chunk preloaded/prefetched
/******/ 	// Promise = chunk loading, 0 = chunk loaded
/******/ 	var installedChunks = {
/******/ 		"blockStrategies": 0
/******/ 	};
/******/
/******/ 	var deferredModules = [];
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/ 	var jsonpArray = window["webpackJsonp"] = window["webpackJsonp"] || [];
/******/ 	var oldJsonpFunction = jsonpArray.push.bind(jsonpArray);
/******/ 	jsonpArray.push = webpackJsonpCallback;
/******/ 	jsonpArray = jsonpArray.slice();
/******/ 	for(var i = 0; i < jsonpArray.length; i++) webpackJsonpCallback(jsonpArray[i]);
/******/ 	var parentJsonpFunction = oldJsonpFunction;
/******/
/******/
/******/ 	// add entry module to deferred list
/******/ 	deferredModules.push(["./vue/src/blockStrategies/main.js","chunk-vendors"]);
/******/ 	// run deferred modules when ready
/******/ 	return checkDeferredModules();
/******/ })
/************************************************************************/
/******/ ({

/***/ "./vue/src/blockStrategies/main.js":
/*!*****************************************!*\
  !*** ./vue/src/blockStrategies/main.js ***!
  \*****************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("var _objectSpread = __webpack_require__(/*! ./node_modules/@babel/runtime/helpers/objectSpread2 */ \"./node_modules/@babel/runtime/helpers/objectSpread2.js\").default;\n\n__webpack_require__(/*! ./node_modules/core-js/modules/es.array.iterator.js */ \"./node_modules/core-js/modules/es.array.iterator.js\");\n\n__webpack_require__(/*! ./node_modules/core-js/modules/es.promise.js */ \"./node_modules/core-js/modules/es.promise.js\");\n\n__webpack_require__(/*! ./node_modules/core-js/modules/es.object.assign.js */ \"./node_modules/core-js/modules/es.object.assign.js\");\n\n__webpack_require__(/*! ./node_modules/core-js/modules/es.promise.finally.js */ \"./node_modules/core-js/modules/es.promise.finally.js\");\n\n__webpack_require__(/*! core-js/modules/es.array.find.js */ \"./node_modules/core-js/modules/es.array.find.js\");\n\ndocument.addEventListener(\"register-block-strategy-components\", function (e) {\n  e.detail['global'] = {\n    props: {\n      block: Object,\n      options: Object\n    },\n    methods: {\n      errors: function errors(field) {\n        var _this$block$errors$op;\n\n        if ((_this$block$errors$op = !this.block.errors.options) !== null && _this$block$errors$op !== void 0 ? _this$block$errors$op : null) {\n          return [];\n        }\n\n        for (var i in this.block.errors.options) {\n          var _this$block$errors$op2;\n\n          if ((_this$block$errors$op2 = this.block.errors.options[i][field]) !== null && _this$block$errors$op2 !== void 0 ? _this$block$errors$op2 : null) {\n            return this.block.errors.options[i][field];\n          }\n        }\n\n        return [];\n      }\n    },\n    mounted: function mounted() {\n      var _this = this;\n\n      this.$nextTick(function () {\n        Craft.initUiElements(_this.$el);\n        $.each($(_this.$el).find('.lightswitch'), function (i, lightswitch) {\n          $(lightswitch).on('change', function (e) {\n            var options = {};\n            options[$(e.target).data('field')] = $(e.target).hasClass('on');\n\n            _this.$emit('updateOptions', options);\n          });\n        });\n      });\n    },\n    emits: ['updateOptions'],\n    template: \"\\n        <div>\\n            <span></span>\\n            <div class=\\\"field\\\">\\n                <div class=\\\"heading\\\">\\n                    <label>{{ t('Cache depends on user authentication') }}</label>\\n                </div>\\n                <div class=\\\"input ltr\\\">                 \\n                    <button type=\\\"button\\\" :class=\\\"{lightswitch: true, on: block.options.cachePerAuthenticated}\\\" data-field=\\\"cachePerAuthenticated\\\">\\n                        <div class=\\\"lightswitch-container\\\">\\n                            <div class=\\\"handle\\\"></div>\\n                        </div>\\n                        <input type=\\\"hidden\\\">\\n                    </button>\\n                </div>\\n                <ul class=\\\"errors\\\" v-if=\\\"errors('cachePerAuthenticated')\\\">\\n                    <li v-for=\\\"error in errors('cachePerAuthenticated')\\\">{{ error }}</li>\\n                </ul>\\n            </div>\\n            <div class=\\\"field\\\">\\n                <div class=\\\"heading\\\">\\n                    <label>{{ t('Cache depends on user') }}</label>\\n                </div>\\n                <div class=\\\"input ltr\\\">                 \\n                    <button type=\\\"button\\\" :class=\\\"{lightswitch: true, on: block.options.cachePerUser}\\\" data-field=\\\"cachePerUser\\\">\\n                        <div class=\\\"lightswitch-container\\\">\\n                            <div class=\\\"handle\\\"></div>\\n                        </div>\\n                        <input type=\\\"hidden\\\">\\n                    </button>\\n                </div>\\n                <ul class=\\\"errors\\\" v-if=\\\"errors('cachePerUser')\\\">\\n                    <li v-for=\\\"error in errors('cachePerUser')\\\">{{ error }}</li>\\n                </ul>\\n            </div>\\n            <div class=\\\"field\\\">\\n                <div class=\\\"heading\\\">\\n                    <label>{{ t('Cache depends on view port (mobile, tablet or desktop)') }}</label>\\n                </div>\\n                <div class=\\\"input ltr\\\">                 \\n                    <button type=\\\"button\\\" :class=\\\"{lightswitch: true, on: block.options.cachePerViewport}\\\" data-field=\\\"cachePerViewport\\\">\\n                        <div class=\\\"lightswitch-container\\\">\\n                            <div class=\\\"handle\\\"></div>\\n                        </div>\\n                        <input type=\\\"hidden\\\">\\n                    </button>\\n                </div>\\n                <ul class=\\\"errors\\\" v-if=\\\"errors('cachePerViewport')\\\">\\n                    <li v-for=\\\"error in errors('cachePerViewport')\\\">{{ error }}</li>\\n                </ul>\\n            </div>\\n        </div>\"\n  };\n  e.detail['path'] = _objectSpread({}, e.detail['global']);\n  e.detail['query'] = _objectSpread({}, e.detail['global']);\n});\n\n//# sourceURL=webpack:///./vue/src/blockStrategies/main.js?");

/***/ })

/******/ });