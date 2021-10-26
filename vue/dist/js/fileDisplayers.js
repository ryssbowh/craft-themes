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
/******/ 		"fileDisplayers": 0
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
/******/ 	deferredModules.push(["./vue/src/fileDisplayers/main.js","chunk-vendors"]);
/******/ 	// run deferred modules when ready
/******/ 	return checkDeferredModules();
/******/ })
/************************************************************************/
/******/ ({

/***/ "./vue/src/fileDisplayers/main.js":
/*!****************************************!*\
  !*** ./vue/src/fileDisplayers/main.js ***!
  \****************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("__webpack_require__(/*! ./node_modules/core-js/modules/es.array.iterator.js */ \"./node_modules/core-js/modules/es.array.iterator.js\");\n\n__webpack_require__(/*! ./node_modules/core-js/modules/es.promise.js */ \"./node_modules/core-js/modules/es.promise.js\");\n\n__webpack_require__(/*! ./node_modules/core-js/modules/es.object.assign.js */ \"./node_modules/core-js/modules/es.object.assign.js\");\n\n__webpack_require__(/*! ./node_modules/core-js/modules/es.promise.finally.js */ \"./node_modules/core-js/modules/es.promise.finally.js\");\n\n__webpack_require__(/*! core-js/modules/es.string.link.js */ \"./node_modules/core-js/modules/es.string.link.js\");\n\ndocument.addEventListener(\"register-file-displayers-components\", function (e) {\n  e.detail['raw'] = {\n    props: {\n      displayer: Object,\n      kind: String,\n      errors: Object\n    },\n    template: \"\\n        <div class=\\\"warning with-icon\\\">\\n            {{ t(\\\"This could be used to run potentially dangerous code on your site, do you trust the data you're going to display ?\\\") }}\\n        </div>\\n        \"\n  };\n  e.detail['iframe'] = {\n    props: {\n      displayer: Object,\n      kind: String,\n      errors: Object\n    },\n    emits: ['updateOptions'],\n    template: \"\\n        <div>\\n            <div class=\\\"field\\\">\\n                <div class=\\\"heading\\\">\\n                    <label>{{ t('Width') }}</label>\\n                </div>\\n                <div class=\\\"input ltr\\\">\\n                    <input type=\\\"text\\\" class=\\\"fullwidth text\\\" :value=\\\"displayer.options.width\\\" @input=\\\"$emit('updateOptions', {width: $event.target.value})\\\">\\n                </div>\\n                <ul class=\\\"errors\\\" v-if=\\\"errors.width\\\">\\n                    <li v-for=\\\"error in errors.width\\\">{{ error }}</li>\\n                </ul>\\n            </div>\\n            <div class=\\\"field\\\">\\n                <div class=\\\"heading\\\">\\n                    <label>{{ t('Height') }}</label>\\n                </div>\\n                <div class=\\\"input ltr\\\">\\n                    <input type=\\\"text\\\" class=\\\"fullwidth text\\\" :value=\\\"displayer.options.height\\\" @input=\\\"$emit('updateOptions', {height: $event.target.value})\\\">\\n                </div>\\n                <ul class=\\\"errors\\\" v-if=\\\"errors.height\\\">\\n                    <li v-for=\\\"error in errors.height\\\">{{ error }}</li>\\n                </ul>\\n            </div>\\n        </div>\\n        \"\n  };\n  e.detail['html_video'] = {\n    props: {\n      displayer: Object,\n      kind: String,\n      errors: Object\n    },\n    emits: ['updateOptions'],\n    template: \"\\n        <div>\\n            <div class=\\\"field\\\">\\n                <div class=\\\"heading\\\">\\n                    <label>{{ t('Width') }}</label>\\n                </div>\\n                <div class=\\\"input ltr\\\">\\n                    <input type=\\\"text\\\" class=\\\"fullwidth text\\\" :value=\\\"displayer.options.width\\\" @input=\\\"$emit('updateOptions', {width: $event.target.value})\\\">\\n                </div>\\n                <ul class=\\\"errors\\\" v-if=\\\"errors.width\\\">\\n                    <li v-for=\\\"error in errors.width\\\">{{ error }}</li>\\n                </ul>\\n            </div>\\n            <div class=\\\"field\\\">\\n                <div class=\\\"heading\\\">\\n                    <label>{{ t('Height') }}</label>\\n                </div>\\n                <div class=\\\"input ltr\\\">\\n                    <input type=\\\"text\\\" class=\\\"fullwidth text\\\" :value=\\\"displayer.options.height\\\" @input=\\\"$emit('updateOptions', {height: $event.target.value})\\\">\\n                </div>\\n                <ul class=\\\"errors\\\" v-if=\\\"errors.height\\\">\\n                    <li v-for=\\\"error in errors.height\\\">{{ error }}</li>\\n                </ul>\\n            </div>\\n            <div class=\\\"field\\\">\\n                <div class=\\\"heading\\\">\\n                    <label>{{ t('Show controls') }}</label>\\n                </div>\\n                <lightswitch :on=\\\"displayer.options.controls\\\" @change=\\\"$emit('updateOptions', {controls: $event})\\\">\\n                </lightswitch>\\n            </div>\\n            <div class=\\\"field\\\">\\n                <div class=\\\"heading\\\">\\n                    <label>{{ t('Muted') }}</label>\\n                </div>\\n                <lightswitch :on=\\\"displayer.options.muted\\\" @change=\\\"$emit('updateOptions', {muted: $event})\\\">\\n                </lightswitch>\\n            </div>\\n            <div class=\\\"field\\\">\\n                <div class=\\\"heading\\\">\\n                    <label>{{ t('Autoplay') }}</label>\\n                </div>\\n                <lightswitch :on=\\\"displayer.options.autoplay\\\" @change=\\\"$emit('updateOptions', {autoplay: $event})\\\">\\n                </lightswitch>\\n            </div>\\n        </div>\\n        \"\n  };\n  e.detail['html_audio'] = {\n    props: {\n      displayer: Object,\n      kind: String,\n      errors: Object\n    },\n    emits: ['updateOptions'],\n    template: \"\\n        <div>\\n            <div class=\\\"field\\\">\\n                <div class=\\\"heading\\\">\\n                    <label>{{ t('Show controls') }}</label>\\n                </div>\\n                <lightswitch :on=\\\"displayer.options.controls\\\" @change=\\\"$emit('updateOptions', {controls: $event})\\\">\\n                </lightswitch>\\n            </div>\\n            <div class=\\\"field\\\">\\n                <div class=\\\"heading\\\">\\n                    <label>{{ t('Muted') }}</label>\\n                </div>\\n                <lightswitch :on=\\\"displayer.options.muted\\\" @change=\\\"$emit('updateOptions', {muted: $event})\\\">\\n                </lightswitch>\\n            </div>\\n            <div class=\\\"field\\\">\\n                <div class=\\\"heading\\\">\\n                    <label>{{ t('Autoplay') }}</label>\\n                </div>\\n                <lightswitch :on=\\\"displayer.options.autoplay\\\" @change=\\\"$emit('updateOptions', {autoplay: $event})\\\">\\n                </lightswitch>\\n            </div>\\n        </div>\\n        \"\n  };\n  e.detail['image_transform'] = {\n    props: {\n      displayer: Object,\n      kind: String,\n      errors: Object\n    },\n    emits: ['updateOptions'],\n    template: \"\\n        <div>\\n            <div class=\\\"field\\\">\\n                <div class=\\\"heading\\\">\\n                    <label class=\\\"required\\\">{{ t('Transform') }}</label>\\n                </div>\\n                <div class=\\\"input ltr\\\">                    \\n                    <div class=\\\"select\\\">\\n                        <select v-model=\\\"displayer.options.transform\\\" @input=\\\"$emit('updateOptions', {transform: $event.target.value})\\\">\\n                            <option :value=\\\"handle\\\" v-for=\\\"name, handle in displayer.imageTransforms\\\">{{ name }}</option>\\n                            <option value=\\\"_custom\\\">{{ t('Custom') }}</option>\\n                        </select>\\n                    </div>\\n                </div>\\n                <ul class=\\\"errors\\\" v-if=\\\"errors.transform\\\">\\n                    <li v-for=\\\"error in errors.transform\\\">{{ error }}</li>\\n                </ul>\\n            </div>\\n            <div class=\\\"field\\\" v-if=\\\"displayer.options.transform == '_custom'\\\">\\n                <div class=\\\"heading\\\">\\n                    <label class=\\\"required\\\">{{ t('Custom') }}</label>\\n                </div>\\n                <div class=\\\"instructions\\\">{{ t('Enter a json list of options to transform the image, example: { \\\"width\\\": 300, \\\"height\\\": 300 }') }}</div>\\n                <div class=\\\"input ltr\\\">\\n                    <input type=\\\"text\\\" class=\\\"fullwidth text\\\" :value=\\\"displayer.options.custom\\\" @input=\\\"$emit('updateOptions', {custom: $event.target.value})\\\">\\n                </div>\\n                <ul class=\\\"errors\\\" v-if=\\\"errors.custom\\\">\\n                    <li v-for=\\\"error in errors.custom\\\">{{ error }}</li>\\n                </ul>\\n            </div>\\n            <div class=\\\"field\\\" v-if=\\\"displayer.options.transform == '_custom'\\\">\\n                <div class=\\\"heading\\\">\\n                    <label>{{ t('Sizes') }}</label>\\n                </div>\\n                <div class=\\\"instructions\\\">{{ t('Enter a json list of options to generate different sizes (srcset), example: [\\\"1.5x\\\", \\\"2x\\\", \\\"3x\\\"]') }}</div>\\n                <div class=\\\"input ltr\\\">\\n                    <input type=\\\"text\\\" class=\\\"fullwidth text\\\" :value=\\\"displayer.options.sizes\\\" @input=\\\"$emit('updateOptions', {sizes: $event.target.value})\\\">\\n                </div>\\n                <ul class=\\\"errors\\\" v-if=\\\"errors.sizes\\\">\\n                    <li v-for=\\\"error in errors.sizes\\\">{{ error }}</li>\\n                </ul>\\n            </div>\\n        </div>\\n        \"\n  };\n  e.detail['link'] = {\n    props: {\n      displayer: Object,\n      kind: String,\n      errors: Object\n    },\n    emits: ['updateOptions'],\n    template: \"\\n        <div>\\n            <div class=\\\"field\\\">\\n                <div class=\\\"heading\\\">\\n                    <label class=\\\"required\\\">{{ t('Label', {}, 'app') }}</label>\\n                </div>\\n                <div class=\\\"input ltr\\\">                    \\n                    <div class=\\\"select\\\">\\n                        <select v-model=\\\"displayer.options.label\\\" @input=\\\"$emit('updateOptions', {label: $event.target.value})\\\">\\n                            <option value=\\\"title\\\">{{ t('Asset title') }}</option>\\n                            <option value=\\\"filename\\\">{{ t('File name') }}</option>\\n                            <option value=\\\"custom\\\">{{ t('Custom') }}</option>\\n                        </select>\\n                    </div>\\n                </div>\\n                <ul class=\\\"errors\\\" v-if=\\\"errors.label\\\">\\n                    <li v-for=\\\"error in errors.label\\\">{{ error }}</li>\\n                </ul>\\n            </div>\\n            <div class=\\\"field\\\" v-if=\\\"displayer.options.label == 'custom'\\\">\\n                <div class=\\\"heading\\\">\\n                    <label class=\\\"required\\\">{{ t('Custom') }}</label>\\n                </div>\\n                <div class=\\\"input ltr\\\">\\n                    <input type=\\\"text\\\" class=\\\"fullwidth text\\\" :value=\\\"displayer.options.custom\\\" @input=\\\"$emit('updateOptions', {custom: $event.target.value})\\\">\\n                </div>\\n                <ul class=\\\"errors\\\" v-if=\\\"errors.custom\\\">\\n                    <li v-for=\\\"error in errors.custom\\\">{{ error }}</li>\\n                </ul>\\n            </div>\\n            <div class=\\\"field\\\">\\n                <div class=\\\"heading\\\">\\n                    <label>{{ t('Open in new tab') }}</label>\\n                </div>\\n                <lightswitch :on=\\\"displayer.options.newTab\\\" @change=\\\"$emit('updateOptions', {newTab: $event})\\\">\\n                </lightswitch>\\n            </div>\\n            <div class=\\\"field\\\">\\n                <div class=\\\"heading\\\">\\n                    <label>{{ t('Download link') }}</label>\\n                </div>\\n                <lightswitch :on=\\\"displayer.options.download\\\" @change=\\\"$emit('updateOptions', {download: $event})\\\">\\n                </lightswitch>\\n            </div>\\n        </div>\"\n  };\n});\n\n//# sourceURL=webpack:///./vue/src/fileDisplayers/main.js?");

/***/ })

/******/ });