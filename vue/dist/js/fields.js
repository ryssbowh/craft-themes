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
/******/ 		"fields": 0
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
/******/ 	deferredModules.push(["./vue/src/fields/main.js","chunk-vendors"]);
/******/ 	// run deferred modules when ready
/******/ 	return checkDeferredModules();
/******/ })
/************************************************************************/
/******/ ({

/***/ "./node_modules/css-loader/dist/cjs.js?!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./vue/src/fields/main.scss":
/*!*******************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader/dist/cjs.js??ref--8-oneOf-3-1!./node_modules/postcss-loader/src??ref--8-oneOf-3-2!./node_modules/sass-loader/dist/cjs.js??ref--8-oneOf-3-3!./vue/src/fields/main.scss ***!
  \*******************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("// Imports\nvar ___CSS_LOADER_API_IMPORT___ = __webpack_require__(/*! ../../../node_modules/css-loader/dist/runtime/api.js */ \"./node_modules/css-loader/dist/runtime/api.js\");\nexports = ___CSS_LOADER_API_IMPORT___(false);\n// Module\nexports.push([module.i, \".matrix-type {\\n  position: relative;\\n}\\n.matrix-type:after {\\n  content: \\\"\\\";\\n  width: 1px;\\n  height: calc(100% - 34px);\\n  background: rgba(96, 125, 159, 0.25);\\n  display: inline-block;\\n  left: 15px;\\n  top: 27px;\\n  position: absolute;\\n}\\n.matrix-type .matrix-type-name {\\n  padding-left: 15px;\\n}\\n.matrix-type .col.move {\\n  padding-left: 25px;\\n}\\n\\n.themes-displays .line, .themes-displays .matrix-type, .themes-displays .table-type {\\n  transition: opacity 0.3s;\\n}\\n.themes-displays .opaque {\\n  opacity: 0.5;\\n}\\n.themes-displays .opaque.matrix ~ .matrix-type, .themes-displays .opaque.table ~ .table-type {\\n  opacity: 0.5;\\n}\", \"\"]);\n// Exports\nmodule.exports = exports;\n\n\n//# sourceURL=webpack:///./vue/src/fields/main.scss?./node_modules/css-loader/dist/cjs.js??ref--8-oneOf-3-1!./node_modules/postcss-loader/src??ref--8-oneOf-3-2!./node_modules/sass-loader/dist/cjs.js??ref--8-oneOf-3-3");

/***/ }),

/***/ "./vue/src/fields/main.js":
/*!********************************!*\
  !*** ./vue/src/fields/main.js ***!
  \********************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _home_bobo_Web_puzzlers_sites_craft35_plugins_craft_themes_node_modules_core_js_modules_es_array_iterator_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./node_modules/core-js/modules/es.array.iterator.js */ \"./node_modules/core-js/modules/es.array.iterator.js\");\n/* harmony import */ var _home_bobo_Web_puzzlers_sites_craft35_plugins_craft_themes_node_modules_core_js_modules_es_array_iterator_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_home_bobo_Web_puzzlers_sites_craft35_plugins_craft_themes_node_modules_core_js_modules_es_array_iterator_js__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var _home_bobo_Web_puzzlers_sites_craft35_plugins_craft_themes_node_modules_core_js_modules_es_promise_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./node_modules/core-js/modules/es.promise.js */ \"./node_modules/core-js/modules/es.promise.js\");\n/* harmony import */ var _home_bobo_Web_puzzlers_sites_craft35_plugins_craft_themes_node_modules_core_js_modules_es_promise_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_home_bobo_Web_puzzlers_sites_craft35_plugins_craft_themes_node_modules_core_js_modules_es_promise_js__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var _home_bobo_Web_puzzlers_sites_craft35_plugins_craft_themes_node_modules_core_js_modules_es_object_assign_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./node_modules/core-js/modules/es.object.assign.js */ \"./node_modules/core-js/modules/es.object.assign.js\");\n/* harmony import */ var _home_bobo_Web_puzzlers_sites_craft35_plugins_craft_themes_node_modules_core_js_modules_es_object_assign_js__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_home_bobo_Web_puzzlers_sites_craft35_plugins_craft_themes_node_modules_core_js_modules_es_object_assign_js__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var _home_bobo_Web_puzzlers_sites_craft35_plugins_craft_themes_node_modules_core_js_modules_es_promise_finally_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./node_modules/core-js/modules/es.promise.finally.js */ \"./node_modules/core-js/modules/es.promise.finally.js\");\n/* harmony import */ var _home_bobo_Web_puzzlers_sites_craft35_plugins_craft_themes_node_modules_core_js_modules_es_promise_finally_js__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_home_bobo_Web_puzzlers_sites_craft35_plugins_craft_themes_node_modules_core_js_modules_es_promise_finally_js__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var _main_scss__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./main.scss */ \"./vue/src/fields/main.scss\");\n/* harmony import */ var _main_scss__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_main_scss__WEBPACK_IMPORTED_MODULE_4__);\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! lodash */ \"./node_modules/lodash/lodash.js\");\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_5__);\n\n\n\n\n\n\ndocument.addEventListener(\"register-fields-components\", function (e) {\n  e.detail['matrix'] = {\n    component: {\n      props: {\n        item: Object\n      },\n      methods: {\n        updateMatrixItem: function updateMatrixItem(fieldId, typeId, data) {\n          for (var i in this.item.types) {\n            var type = this.item.types[i];\n\n            if (type.type.id != typeId) {\n              continue;\n            }\n\n            for (i in type.fields) {\n              var field = type.fields[i];\n\n              if (field.id != fieldId) {\n                continue;\n              }\n\n              field = Object(lodash__WEBPACK_IMPORTED_MODULE_5__[\"merge\"])(field, data);\n            }\n          }\n        }\n      },\n      emits: ['updateItem'],\n      template: \"\\n            <div>\\n                <field :item=\\\"item\\\" @updateItem=\\\"$emit('updateItem', $event)\\\"></field>\\n                <div class=\\\"matrix-type\\\" v-for=\\\"type, index in item.types\\\" v-bind:key=\\\"index\\\">\\n                    <div class=\\\"matrix-type-name\\\">{{ t('Type {type}', {type: type.type.name}) }}</div>\\n                    <draggable\\n                        item-key=\\\"id\\\"\\n                        :list=\\\"type.fields\\\"\\n                        group=\\\"'matrix-'+type.type_id\\\"\\n                        handle=\\\".move\\\"\\n                        >\\n                        <template #item=\\\"{element}\\\">\\n                            <field :item=\\\"element\\\" @updateItem=\\\"updateMatrixItem(element.id, type.type_id, $event)\\\"></field>\\n                        </template>\\n                    </draggable>\\n                </div>\\n            </div>\"\n    },\n    clone: function clone(oldDisplay, display) {\n      for (var i in display.item.types) {\n        for (var j in display.item.types[i].fields) {\n          display.item.types[i].fields[j].id = null;\n          display.item.types[i].fields[j].uid = null;\n        }\n      }\n    }\n  };\n  e.detail['table'] = {\n    component: {\n      props: {\n        item: Object\n      },\n      methods: {\n        updateTableItem: function updateTableItem(key, data) {\n          var item = this.item.fields[key];\n          item = Object(lodash__WEBPACK_IMPORTED_MODULE_5__[\"merge\"])(item, data);\n        }\n      },\n      emits: ['updateItem'],\n      template: \"\\n            <div>\\n                <field :item=\\\"item\\\" @updateItem=\\\"$emit('updateItem', $event)\\\"></field>\\n                <div class=\\\"table-type\\\">\\n                    <field :item=\\\"element\\\" v-for=\\\"element, key in item.fields\\\" :moveable=\\\"false\\\" v-bind:key=\\\"key\\\" @updateItem=\\\"updateTableItem(key, $event)\\\"></field>\\n                </div>\\n            </div>\"\n    },\n    clone: function clone(oldDisplay, display) {}\n  };\n});\n\n//# sourceURL=webpack:///./vue/src/fields/main.js?");

/***/ }),

/***/ "./vue/src/fields/main.scss":
/*!**********************************!*\
  !*** ./vue/src/fields/main.scss ***!
  \**********************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("// style-loader: Adds some css to the DOM by adding a <style> tag\n\n// load the styles\nvar content = __webpack_require__(/*! !../../../node_modules/css-loader/dist/cjs.js??ref--8-oneOf-3-1!../../../node_modules/postcss-loader/src??ref--8-oneOf-3-2!../../../node_modules/sass-loader/dist/cjs.js??ref--8-oneOf-3-3!./main.scss */ \"./node_modules/css-loader/dist/cjs.js?!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./vue/src/fields/main.scss\");\nif(content.__esModule) content = content.default;\nif(typeof content === 'string') content = [[module.i, content, '']];\nif(content.locals) module.exports = content.locals;\n// add the styles to the DOM\nvar add = __webpack_require__(/*! ../../../node_modules/vue-style-loader/lib/addStylesClient.js */ \"./node_modules/vue-style-loader/lib/addStylesClient.js\").default\nvar update = add(\"4431fdda\", content, false, {\"sourceMap\":false,\"shadowMode\":false});\n// Hot Module Replacement\nif(false) {}\n\n//# sourceURL=webpack:///./vue/src/fields/main.scss?");

/***/ })

/******/ });