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
/******/ 		"blockOptions": 0
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
/******/ 	deferredModules.push(["./vue/src/blockOptions/main.js","chunk-vendors"]);
/******/ 	// run deferred modules when ready
/******/ 	return checkDeferredModules();
/******/ })
/************************************************************************/
/******/ ({

/***/ "./vue/src/blockOptions/main.js":
/*!**************************************!*\
  !*** ./vue/src/blockOptions/main.js ***!
  \**************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _home_bobo_Web_puzzlers_sites_craft35_plugins_craft_themes_node_modules_babel_runtime_helpers_esm_objectSpread2__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./node_modules/@babel/runtime/helpers/esm/objectSpread2 */ \"./node_modules/@babel/runtime/helpers/esm/objectSpread2.js\");\n/* harmony import */ var _home_bobo_Web_puzzlers_sites_craft35_plugins_craft_themes_node_modules_core_js_modules_es_array_iterator_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./node_modules/core-js/modules/es.array.iterator.js */ \"./node_modules/core-js/modules/es.array.iterator.js\");\n/* harmony import */ var _home_bobo_Web_puzzlers_sites_craft35_plugins_craft_themes_node_modules_core_js_modules_es_array_iterator_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_home_bobo_Web_puzzlers_sites_craft35_plugins_craft_themes_node_modules_core_js_modules_es_array_iterator_js__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var _home_bobo_Web_puzzlers_sites_craft35_plugins_craft_themes_node_modules_core_js_modules_es_promise_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./node_modules/core-js/modules/es.promise.js */ \"./node_modules/core-js/modules/es.promise.js\");\n/* harmony import */ var _home_bobo_Web_puzzlers_sites_craft35_plugins_craft_themes_node_modules_core_js_modules_es_promise_js__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_home_bobo_Web_puzzlers_sites_craft35_plugins_craft_themes_node_modules_core_js_modules_es_promise_js__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var _home_bobo_Web_puzzlers_sites_craft35_plugins_craft_themes_node_modules_core_js_modules_es_object_assign_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./node_modules/core-js/modules/es.object.assign.js */ \"./node_modules/core-js/modules/es.object.assign.js\");\n/* harmony import */ var _home_bobo_Web_puzzlers_sites_craft35_plugins_craft_themes_node_modules_core_js_modules_es_object_assign_js__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_home_bobo_Web_puzzlers_sites_craft35_plugins_craft_themes_node_modules_core_js_modules_es_object_assign_js__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var _home_bobo_Web_puzzlers_sites_craft35_plugins_craft_themes_node_modules_core_js_modules_es_promise_finally_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./node_modules/core-js/modules/es.promise.finally.js */ \"./node_modules/core-js/modules/es.promise.finally.js\");\n/* harmony import */ var _home_bobo_Web_puzzlers_sites_craft35_plugins_craft_themes_node_modules_core_js_modules_es_promise_finally_js__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_home_bobo_Web_puzzlers_sites_craft35_plugins_craft_themes_node_modules_core_js_modules_es_promise_finally_js__WEBPACK_IMPORTED_MODULE_4__);\n/* harmony import */ var core_js_modules_es_array_find_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! core-js/modules/es.array.find.js */ \"./node_modules/core-js/modules/es.array.find.js\");\n/* harmony import */ var core_js_modules_es_array_find_js__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_find_js__WEBPACK_IMPORTED_MODULE_5__);\n/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! core-js/modules/es.object.to-string.js */ \"./node_modules/core-js/modules/es.object.to-string.js\");\n/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_6__);\n/* harmony import */ var core_js_modules_web_dom_collections_iterator_js__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! core-js/modules/web.dom-collections.iterator.js */ \"./node_modules/core-js/modules/web.dom-collections.iterator.js\");\n/* harmony import */ var core_js_modules_web_dom_collections_iterator_js__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_web_dom_collections_iterator_js__WEBPACK_IMPORTED_MODULE_7__);\n/* harmony import */ var vuex__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! vuex */ \"./node_modules/vuex/dist/vuex.esm-browser.js\");\n\n\n\n\n\n\n\n\n\ndocument.addEventListener(\"register-block-option-components\", function (e) {\n  e.detail['system-template'] = {\n    props: {\n      block: Object\n    },\n    methods: {\n      errors: function errors(field) {\n        var _this$block$errors$op;\n\n        if ((_this$block$errors$op = !this.block.errors.options) !== null && _this$block$errors$op !== void 0 ? _this$block$errors$op : null) {\n          return [];\n        }\n\n        for (var i in this.block.errors.options) {\n          var _this$block$errors$op2;\n\n          if ((_this$block$errors$op2 = this.block.errors.options[i][field]) !== null && _this$block$errors$op2 !== void 0 ? _this$block$errors$op2 : null) {\n            return this.block.errors.options[i][field];\n          }\n        }\n\n        return [];\n      }\n    },\n    template: \"\\n        <div class=\\\"field\\\">\\n            <div class=\\\"heading\\\">\\n                <label class=\\\"required\\\">{{ t('Template Path') }}</label>\\n            </div>\\n            <div class=\\\"input ltr\\\">\\n                <input type=\\\"text\\\" class=\\\"text fullwidth\\\" :value=\\\"block.options.template\\\" @input=\\\"$emit('updateOptions', {template: $event.target.value})\\\">\\n            </div>\\n            <ul class=\\\"errors\\\" v-if=\\\"errors('template')\\\">\\n                <li v-for=\\\"error in errors('template')\\\">{{ error }}</li>\\n            </ul>\\n        </div>\"\n  };\n  e.detail['system-twig'] = {\n    props: {\n      block: Object\n    },\n    template: \"\\n        <div class=\\\"field\\\">\\n            <div class=\\\"heading\\\">\\n                <label>{{ t('Twig Code') }}</label>\\n            </div>\\n            <div class=\\\"input ltr\\\">\\n                <textarea class=\\\"text fullwidth\\\" rows=\\\"10\\\" :value=\\\"block.options.twig\\\" @input=\\\"$emit('updateOptions', {twig: $event.target.value})\\\">\\n                </textarea>\\n            </div>\\n        </div>\"\n  };\n  e.detail['forms-login'] = {\n    props: {\n      block: Object\n    },\n    mounted: function mounted() {\n      var _this = this;\n\n      this.$nextTick(function () {\n        Craft.initUiElements(_this.$el);\n        $(_this.$el).find('.lightswitch').on('change', function (e) {\n          var options = {\n            onlyIfNotAuthenticated: $(e.target).hasClass('on')\n          };\n\n          _this.$emit('updateOptions', options);\n        });\n      });\n    },\n    template: \"\\n        <div class=\\\"field\\\">\\n            <div class=\\\"heading\\\">\\n                <label>{{ t('Show only if the user is not authenticated') }}</label>\\n            </div>\\n            <div class=\\\"input ltr\\\">                    \\n                <button type=\\\"button\\\" :class=\\\"{lightswitch: true, on: block.options.onlyIfNotAuthenticated}\\\">\\n                    <div class=\\\"lightswitch-container\\\">\\n                        <div class=\\\"handle\\\"></div>\\n                    </div>\\n                    <input type=\\\"hidden\\\" name=\\\"onlyIfNotAuthenticated\\\" :value=\\\"block.options.onlyIfNotAuthenticated ? 1 : ''\\\">\\n                </button>\\n            </div>\\n        </div>\"\n  };\n  e.detail['forms-register'] = Object(_home_bobo_Web_puzzlers_sites_craft35_plugins_craft_themes_node_modules_babel_runtime_helpers_esm_objectSpread2__WEBPACK_IMPORTED_MODULE_0__[\"default\"])({}, e.detail['forms-login']);\n  e.detail['system-entry'] = {\n    computed: Object(_home_bobo_Web_puzzlers_sites_craft35_plugins_craft_themes_node_modules_babel_runtime_helpers_esm_objectSpread2__WEBPACK_IMPORTED_MODULE_0__[\"default\"])({}, Object(vuex__WEBPACK_IMPORTED_MODULE_8__[\"mapState\"])(['theme'])),\n    props: {\n      block: Object\n    },\n    data: function data() {\n      return {\n        entries: [],\n        viewModes: []\n      };\n    },\n    watch: {\n      'block.options.type': {\n        handler: function handler() {\n          this.$emit('updateOptions', {\n            entry: '',\n            viewMode: ''\n          });\n          this.fetchEntries();\n          this.fetchViewModes();\n        }\n      }\n    },\n    created: function created() {\n      if (this.block.options.type) {\n        this.fetchEntries();\n        this.fetchViewModes();\n      }\n    },\n    methods: {\n      errors: function errors(field) {\n        var _this$block$errors$op3;\n\n        if ((_this$block$errors$op3 = !this.block.errors.options) !== null && _this$block$errors$op3 !== void 0 ? _this$block$errors$op3 : null) {\n          return [];\n        }\n\n        for (var i in this.block.errors.options) {\n          var _this$block$errors$op4;\n\n          if ((_this$block$errors$op4 = this.block.errors.options[i][field]) !== null && _this$block$errors$op4 !== void 0 ? _this$block$errors$op4 : null) {\n            return this.block.errors.options[i][field];\n          }\n        }\n\n        return [];\n      },\n      fetchEntries: function fetchEntries() {\n        var _this2 = this;\n\n        axios.post(Craft.getCpUrl('themes/ajax/entries/' + this.block.options.type)).then(function (response) {\n          _this2.entries = response.data.entries;\n        }).catch(function (err) {\n          _this2.handleError(err);\n        });\n      },\n      fetchViewModes: function fetchViewModes() {\n        var _this3 = this;\n\n        axios.post(Craft.getCpUrl('themes/ajax/view-modes/' + this.theme + '/entry/' + this.block.options.type)).then(function (response) {\n          _this3.viewModes = response.data.viewModes;\n        }).catch(function (err) {\n          _this3.handleError(err);\n        });\n      }\n    },\n    template: \"\\n        <div class=\\\"field\\\">\\n            <div class=\\\"heading\\\">\\n                <label>{{ t('Entry Type', {}, 'app') }}</label>\\n            </div>\\n            <div class=\\\"input ltr\\\">\\n                <div class=\\\"select\\\">\\n                    <select @input=\\\"$emit('updateOptions', {type: $event.target.value})\\\" :value=\\\"block.options.type\\\">\\n                        <option v-for=\\\"type in block.entryTypes\\\" :value=\\\"type.uid\\\">{{ type.name }}</option>\\n                    </select>\\n                </div>\\n            </div>\\n            <ul class=\\\"errors\\\" v-if=\\\"errors('type')\\\">\\n                <li v-for=\\\"error in errors('type')\\\">{{ error }}</li>\\n            </ul>\\n        </div>\\n        <div class=\\\"field\\\">\\n            <div class=\\\"heading\\\">\\n                <label>{{ t('Entry', {}, 'app') }}</label>\\n            </div>\\n            <div class=\\\"input ltr\\\">\\n                <div class=\\\"select\\\">\\n                    <select @input=\\\"$emit('updateOptions', {entry: $event.target.value})\\\" :value=\\\"block.options.entry\\\">\\n                        <option v-for=\\\"entry in entries\\\" :value=\\\"entry.uid\\\">{{ entry.title }}</option>\\n                    </select>\\n                </div>\\n            </div>\\n            <ul class=\\\"errors\\\" v-if=\\\"errors('entry')\\\">\\n                <li v-for=\\\"error in errors('entry')\\\">{{ error }}</li>\\n            </ul>\\n        </div>\\n        <div class=\\\"field\\\">\\n            <div class=\\\"heading\\\">\\n                <label>{{ t('View mode') }}</label>\\n            </div>\\n            <div class=\\\"input ltr\\\">\\n                <div class=\\\"select\\\">\\n                    <select @input=\\\"$emit('updateOptions', {viewMode: $event.target.value})\\\" :value=\\\"block.options.viewMode\\\">\\n                        <option v-for=\\\"viewMode in viewModes\\\" :value=\\\"viewMode.uid\\\">{{ viewMode.name }}</option>\\n                    </select>\\n                </div>\\n            </div>\\n            <ul class=\\\"errors\\\" v-if=\\\"errors('viewMode')\\\">\\n                <li v-for=\\\"error in errors('viewMode')\\\">{{ error }}</li>\\n            </ul>\\n        </div>\",\n    emits: ['updateOptions']\n  };\n  e.detail['system-category'] = {\n    computed: Object(_home_bobo_Web_puzzlers_sites_craft35_plugins_craft_themes_node_modules_babel_runtime_helpers_esm_objectSpread2__WEBPACK_IMPORTED_MODULE_0__[\"default\"])({}, Object(vuex__WEBPACK_IMPORTED_MODULE_8__[\"mapState\"])(['theme'])),\n    props: {\n      block: Object\n    },\n    data: function data() {\n      return {\n        categories: [],\n        viewModes: []\n      };\n    },\n    watch: {\n      'block.options.group': {\n        handler: function handler() {\n          this.$emit('updateOptions', {\n            category: '',\n            viewMode: ''\n          });\n          this.fetchCategories();\n          this.fetchViewModes();\n        }\n      }\n    },\n    created: function created() {\n      if (this.block.options.group) {\n        this.fetchCategories();\n        this.fetchViewModes();\n      }\n    },\n    methods: {\n      errors: function errors(field) {\n        var _this$block$errors$op5;\n\n        if ((_this$block$errors$op5 = !this.block.errors.options) !== null && _this$block$errors$op5 !== void 0 ? _this$block$errors$op5 : null) {\n          return [];\n        }\n\n        for (var i in this.block.errors.options) {\n          var _this$block$errors$op6;\n\n          if ((_this$block$errors$op6 = this.block.errors.options[i][field]) !== null && _this$block$errors$op6 !== void 0 ? _this$block$errors$op6 : null) {\n            return this.block.errors.options[i][field];\n          }\n        }\n\n        return [];\n      },\n      fetchCategories: function fetchCategories() {\n        var _this4 = this;\n\n        axios.post(Craft.getCpUrl('themes/ajax/categories/' + this.block.options.group)).then(function (response) {\n          _this4.categories = response.data.categories;\n        }).catch(function (err) {\n          _this4.handleError(err);\n        });\n      },\n      fetchViewModes: function fetchViewModes() {\n        var _this5 = this;\n\n        axios.post(Craft.getCpUrl('themes/ajax/view-modes/' + this.theme + '/category/' + this.block.options.group)).then(function (response) {\n          _this5.viewModes = response.data.viewModes;\n        }).catch(function (err) {\n          _this5.handleError(err);\n        });\n      }\n    },\n    template: \"\\n        <div class=\\\"field\\\">\\n            <div class=\\\"heading\\\">\\n                <label>{{ t('Group') }}</label>\\n            </div>\\n            <div class=\\\"input ltr\\\">\\n                <div class=\\\"select\\\">\\n                    <select @input=\\\"$emit('updateOptions', {group: $event.target.value})\\\" :value=\\\"block.options.group\\\">\\n                        <option v-for=\\\"group in block.groups\\\" :value=\\\"group.uid\\\">{{ group.name }}</option>\\n                    </select>\\n                </div>\\n            </div>\\n            <ul class=\\\"errors\\\" v-if=\\\"errors('group')\\\">\\n                <li v-for=\\\"error in errors('group')\\\">{{ error }}</li>\\n            </ul>\\n        </div>\\n        <div class=\\\"field\\\">\\n            <div class=\\\"heading\\\">\\n                <label>{{ t('Category', {}, 'app') }}</label>\\n            </div>\\n            <div class=\\\"input ltr\\\">\\n                <div class=\\\"select\\\">\\n                    <select @input=\\\"$emit('updateOptions', {category: $event.target.value})\\\" :value=\\\"block.options.category\\\">\\n                        <option v-for=\\\"category in categories\\\" :value=\\\"category.uid\\\">{{ category.title }}</option>\\n                    </select>\\n                </div>\\n            </div>\\n            <ul class=\\\"errors\\\" v-if=\\\"errors('category')\\\">\\n                <li v-for=\\\"error in errors('category')\\\">{{ error }}</li>\\n            </ul>\\n        </div>\\n        <div class=\\\"field\\\">\\n            <div class=\\\"heading\\\">\\n                <label>{{ t('View mode') }}</label>\\n            </div>\\n            <div class=\\\"input ltr\\\">\\n                <div class=\\\"select\\\">\\n                    <select @input=\\\"$emit('updateOptions', {viewMode: $event.target.value})\\\" :value=\\\"block.options.viewMode\\\">\\n                        <option v-for=\\\"viewMode in viewModes\\\" :value=\\\"viewMode.uid\\\">{{ viewMode.name }}</option>\\n                    </select>\\n                </div>\\n            </div>\\n            <ul class=\\\"errors\\\" v-if=\\\"errors('viewMode')\\\">\\n                <li v-for=\\\"error in errors('viewMode')\\\">{{ error }}</li>\\n            </ul>\\n        </div>\",\n    emits: ['updateOptions']\n  };\n  e.detail['system-user'] = {\n    computed: Object(_home_bobo_Web_puzzlers_sites_craft35_plugins_craft_themes_node_modules_babel_runtime_helpers_esm_objectSpread2__WEBPACK_IMPORTED_MODULE_0__[\"default\"])({}, Object(vuex__WEBPACK_IMPORTED_MODULE_8__[\"mapState\"])(['theme'])),\n    props: {\n      block: Object\n    },\n    data: function data() {\n      return {\n        users: [],\n        viewModes: []\n      };\n    },\n    created: function created() {\n      this.fetchUsers();\n      this.fetchViewModes();\n    },\n    methods: {\n      errors: function errors(field) {\n        var _this$block$errors$op7;\n\n        if ((_this$block$errors$op7 = !this.block.errors.options) !== null && _this$block$errors$op7 !== void 0 ? _this$block$errors$op7 : null) {\n          return [];\n        }\n\n        for (var i in this.block.errors.options) {\n          var _this$block$errors$op8;\n\n          if ((_this$block$errors$op8 = this.block.errors.options[i][field]) !== null && _this$block$errors$op8 !== void 0 ? _this$block$errors$op8 : null) {\n            return this.block.errors.options[i][field];\n          }\n        }\n\n        return [];\n      },\n      fetchUsers: function fetchUsers() {\n        var _this6 = this;\n\n        axios.post(Craft.getCpUrl('themes/ajax/users')).then(function (response) {\n          _this6.users = response.data.users;\n        }).catch(function (err) {\n          _this6.handleError(err);\n        });\n      },\n      fetchViewModes: function fetchViewModes() {\n        var _this7 = this;\n\n        axios.post(Craft.getCpUrl('themes/ajax/view-modes/' + this.theme + '/user')).then(function (response) {\n          _this7.viewModes = response.data.viewModes;\n        }).catch(function (err) {\n          _this7.handleError(err);\n        });\n      }\n    },\n    template: \"\\n        <div class=\\\"field\\\">\\n            <div class=\\\"heading\\\">\\n                <label>{{ t('User', {}, 'app') }}</label>\\n            </div>\\n            <div class=\\\"input ltr\\\">\\n                <div class=\\\"select\\\">\\n                    <select @input=\\\"$emit('updateOptions', {user: $event.target.value})\\\" :value=\\\"block.options.user\\\">\\n                        <option v-for=\\\"user in users\\\" :value=\\\"user.uid\\\">{{ user.title }}</option>\\n                    </select>\\n                </div>\\n            </div>\\n            <ul class=\\\"errors\\\" v-if=\\\"errors('user')\\\">\\n                <li v-for=\\\"error in errors('user')\\\">{{ error }}</li>\\n            </ul>\\n        </div>\\n        <div class=\\\"field\\\">\\n            <div class=\\\"heading\\\">\\n                <label>{{ t('View mode') }}</label>\\n            </div>\\n            <div class=\\\"input ltr\\\">\\n                <div class=\\\"select\\\">\\n                    <select @input=\\\"$emit('updateOptions', {viewMode: $event.target.value})\\\" :value=\\\"block.options.viewMode\\\">\\n                        <option v-for=\\\"viewMode in viewModes\\\" :value=\\\"viewMode.uid\\\">{{ viewMode.name }}</option>\\n                    </select>\\n                </div>\\n            </div>\\n            <ul class=\\\"errors\\\" v-if=\\\"errors('viewMode')\\\">\\n                <li v-for=\\\"error in errors('viewMode')\\\">{{ error }}</li>\\n            </ul>\\n        </div>\",\n    emits: ['updateOptions']\n  };\n  e.detail['system-current-user'] = {\n    computed: Object(_home_bobo_Web_puzzlers_sites_craft35_plugins_craft_themes_node_modules_babel_runtime_helpers_esm_objectSpread2__WEBPACK_IMPORTED_MODULE_0__[\"default\"])({}, Object(vuex__WEBPACK_IMPORTED_MODULE_8__[\"mapState\"])(['theme'])),\n    props: {\n      block: Object\n    },\n    data: function data() {\n      return {\n        viewModes: []\n      };\n    },\n    created: function created() {\n      this.fetchViewModes();\n    },\n    methods: {\n      errors: function errors(field) {\n        var _this$block$errors$op9;\n\n        if ((_this$block$errors$op9 = !this.block.errors.options) !== null && _this$block$errors$op9 !== void 0 ? _this$block$errors$op9 : null) {\n          return [];\n        }\n\n        for (var i in this.block.errors.options) {\n          var _this$block$errors$op10;\n\n          if ((_this$block$errors$op10 = this.block.errors.options[i][field]) !== null && _this$block$errors$op10 !== void 0 ? _this$block$errors$op10 : null) {\n            return this.block.errors.options[i][field];\n          }\n        }\n\n        return [];\n      },\n      fetchViewModes: function fetchViewModes() {\n        var _this8 = this;\n\n        axios.post(Craft.getCpUrl('themes/ajax/view-modes/' + this.theme + '/user')).then(function (response) {\n          _this8.viewModes = response.data.viewModes;\n        }).catch(function (err) {\n          _this8.handleError(err);\n        });\n      }\n    },\n    template: \"\\n        <div class=\\\"field\\\">\\n            <div class=\\\"heading\\\">\\n                <label>{{ t('View mode') }}</label>\\n            </div>\\n            <div class=\\\"input ltr\\\">\\n                <div class=\\\"select\\\">\\n                    <select @input=\\\"$emit('updateOptions', {viewMode: $event.target.value})\\\" :value=\\\"block.options.viewMode\\\">\\n                        <option v-for=\\\"viewMode in viewModes\\\" :value=\\\"viewMode.uid\\\">{{ viewMode.name }}</option>\\n                    </select>\\n                </div>\\n            </div>\\n            <ul class=\\\"errors\\\" v-if=\\\"errors('viewMode')\\\">\\n                <li v-for=\\\"error in errors('viewMode')\\\">{{ error }}</li>\\n            </ul>\\n        </div>\",\n    emits: ['updateOptions']\n  };\n  e.detail['system-global'] = {\n    computed: Object(_home_bobo_Web_puzzlers_sites_craft35_plugins_craft_themes_node_modules_babel_runtime_helpers_esm_objectSpread2__WEBPACK_IMPORTED_MODULE_0__[\"default\"])({}, Object(vuex__WEBPACK_IMPORTED_MODULE_8__[\"mapState\"])(['theme'])),\n    props: {\n      block: Object\n    },\n    data: function data() {\n      return {\n        viewModes: []\n      };\n    },\n    watch: {\n      'block.options.set': {\n        handler: function handler() {\n          this.fetchViewModes();\n        }\n      }\n    },\n    created: function created() {\n      if (this.block.options.set) {\n        this.fetchViewModes();\n      }\n    },\n    methods: {\n      errors: function errors(field) {\n        var _this$block$errors$op11;\n\n        if ((_this$block$errors$op11 = !this.block.errors.options) !== null && _this$block$errors$op11 !== void 0 ? _this$block$errors$op11 : null) {\n          return [];\n        }\n\n        for (var i in this.block.errors.options) {\n          var _this$block$errors$op12;\n\n          if ((_this$block$errors$op12 = this.block.errors.options[i][field]) !== null && _this$block$errors$op12 !== void 0 ? _this$block$errors$op12 : null) {\n            return this.block.errors.options[i][field];\n          }\n        }\n\n        return [];\n      },\n      fetchViewModes: function fetchViewModes() {\n        var _this9 = this;\n\n        axios.post(Craft.getCpUrl('themes/ajax/view-modes/' + this.theme + '/global/' + this.block.options.set)).then(function (response) {\n          _this9.viewModes = response.data.viewModes;\n        }).catch(function (err) {\n          _this9.handleError(err);\n        });\n      }\n    },\n    template: \"\\n        <div class=\\\"field\\\">\\n            <div class=\\\"heading\\\">\\n                <label>{{ t('Global Set', {}, 'app') }}</label>\\n            </div>\\n            <div class=\\\"input ltr\\\">\\n                <div class=\\\"select\\\">\\n                    <select @input=\\\"$emit('updateOptions', {set: $event.target.value})\\\" :value=\\\"block.options.set\\\">\\n                        <option v-for=\\\"set in block.sets\\\" :value=\\\"set.uid\\\">{{ set.name }}</option>\\n                    </select>\\n                </div>\\n            </div>\\n            <ul class=\\\"errors\\\" v-if=\\\"errors('set')\\\">\\n                <li v-for=\\\"error in errors('set')\\\">{{ error }}</li>\\n            </ul>\\n        </div>\\n        <div class=\\\"field\\\">\\n            <div class=\\\"heading\\\">\\n                <label>{{ t('View mode') }}</label>\\n            </div>\\n            <div class=\\\"input ltr\\\">\\n                <div class=\\\"select\\\">\\n                    <select @input=\\\"$emit('updateOptions', {viewMode: $event.target.value})\\\" :value=\\\"block.options.viewMode\\\">\\n                        <option v-for=\\\"viewMode in viewModes\\\" :value=\\\"viewMode.uid\\\">{{ viewMode.name }}</option>\\n                    </select>\\n                </div>\\n            </div>\\n            <ul class=\\\"errors\\\" v-if=\\\"errors('viewMode')\\\">\\n                <li v-for=\\\"error in errors('viewMode')\\\">{{ error }}</li>\\n            </ul>\\n        </div>\",\n    emits: ['updateOptions']\n  };\n});\n\n//# sourceURL=webpack:///./vue/src/blockOptions/main.js?");

/***/ })

/******/ });