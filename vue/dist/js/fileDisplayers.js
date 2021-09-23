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

eval("__webpack_require__(/*! ./node_modules/core-js/modules/es.array.iterator.js */ \"./node_modules/core-js/modules/es.array.iterator.js\");\n\n__webpack_require__(/*! ./node_modules/core-js/modules/es.promise.js */ \"./node_modules/core-js/modules/es.promise.js\");\n\n__webpack_require__(/*! ./node_modules/core-js/modules/es.object.assign.js */ \"./node_modules/core-js/modules/es.object.assign.js\");\n\n__webpack_require__(/*! ./node_modules/core-js/modules/es.promise.finally.js */ \"./node_modules/core-js/modules/es.promise.finally.js\");\n\n__webpack_require__(/*! core-js/modules/es.string.link.js */ \"./node_modules/core-js/modules/es.string.link.js\");\n\ndocument.addEventListener(\"register-file-displayers-components\", function (e) {\n  e.detail['iframe'] = {\n    props: {\n      displayer: Object,\n      kind: String,\n      errors: Object\n    },\n    data: function data() {\n      return {\n        width: 500,\n        height: 500\n      };\n    },\n    created: function created() {\n      this.width = this.displayer.options.width;\n      this.height = this.displayer.options.height;\n    },\n    methods: {\n      errorList: function errorList(field) {\n        var _this$errors$field;\n\n        return (_this$errors$field = this.errors[field]) !== null && _this$errors$field !== void 0 ? _this$errors$field : [];\n      }\n    },\n    template: \"\\n        <div>\\n            <div class=\\\"field\\\">\\n                <div class=\\\"heading\\\">\\n                    <label>{{ t('Width') }}</label>\\n                </div>\\n                <div class=\\\"input ltr\\\">\\n                    <input type=\\\"text\\\" class=\\\"fullwidth text\\\" :name=\\\"'displayers['+kind+'][options][width]'\\\" v-model=\\\"width\\\">\\n                </div>\\n                <ul class=\\\"errors\\\" v-if=\\\"errorList('width')\\\">\\n                    <li v-for=\\\"error in errorList('width')\\\">{{ error }}</li>\\n                </ul>\\n            </div>\\n            <div class=\\\"field\\\">\\n                <div class=\\\"heading\\\">\\n                    <label>{{ t('Height') }}</label>\\n                </div>\\n                <div class=\\\"input ltr\\\">\\n                    <input type=\\\"text\\\" class=\\\"fullwidth text\\\" :name=\\\"'displayers['+kind+'][options][height]'\\\" v-model=\\\"height\\\">\\n                </div>\\n                <ul class=\\\"errors\\\" v-if=\\\"errorList('height')\\\">\\n                    <li v-for=\\\"error in errorList('height')\\\">{{ error }}</li>\\n                </ul>\\n            </div>\\n        </div>\\n        \"\n  };\n  e.detail['html_video'] = {\n    props: {\n      displayer: Object,\n      kind: String,\n      errors: Object\n    },\n    data: function data() {\n      return {\n        controls: false,\n        muted: false,\n        autoplay: false,\n        width: 500,\n        height: 500\n      };\n    },\n    created: function created() {\n      this.controls = this.displayer.options.controls;\n      this.muted = this.displayer.options.muted;\n      this.autoplay = this.displayer.options.autoplay;\n      this.width = this.displayer.options.width;\n      this.height = this.displayer.options.height;\n    },\n    mounted: function mounted() {\n      var _this = this;\n\n      this.$nextTick(function () {\n        Craft.initUiElements(_this.$el);\n      });\n    },\n    methods: {\n      errorList: function errorList(field) {\n        var _this$errors$field2;\n\n        return (_this$errors$field2 = this.errors[field]) !== null && _this$errors$field2 !== void 0 ? _this$errors$field2 : [];\n      }\n    },\n    template: \"\\n        <div>\\n            <div class=\\\"field\\\">\\n                <div class=\\\"heading\\\">\\n                    <label>{{ t('Width') }}</label>\\n                </div>\\n                <div class=\\\"input ltr\\\">\\n                    <input type=\\\"text\\\" class=\\\"fullwidth text\\\" :name=\\\"'displayers['+kind+'][options][width]'\\\" v-model=\\\"width\\\">\\n                </div>\\n                <ul class=\\\"errors\\\" v-if=\\\"errorList('width')\\\">\\n                    <li v-for=\\\"error in errorList('width')\\\">{{ error }}</li>\\n                </ul>\\n            </div>\\n            <div class=\\\"field\\\">\\n                <div class=\\\"heading\\\">\\n                    <label>{{ t('Height') }}</label>\\n                </div>\\n                <div class=\\\"input ltr\\\">\\n                    <input type=\\\"text\\\" class=\\\"fullwidth text\\\" :name=\\\"'displayers['+kind+'][options][height]'\\\" v-model=\\\"height\\\">\\n                </div>\\n                <ul class=\\\"errors\\\" v-if=\\\"errorList('height')\\\">\\n                    <li v-for=\\\"error in errorList('height')\\\">{{ error }}</li>\\n                </ul>\\n            </div>\\n            <div class=\\\"field\\\">\\n                <div class=\\\"heading\\\">\\n                    <label>{{ t('Show controls') }}</label>\\n                </div>\\n                <div class=\\\"input ltr\\\">                    \\n                    <button type=\\\"button\\\" :class=\\\"{lightswitch: true, on: controls}\\\">\\n                        <div class=\\\"lightswitch-container\\\">\\n                            <div class=\\\"handle\\\"></div>\\n                        </div>\\n                        <input type=\\\"hidden\\\" :name=\\\"'displayers['+kind+'][options][controls]'\\\">\\n                    </button>\\n                </div>\\n            </div>\\n            <div class=\\\"field\\\">\\n                <div class=\\\"heading\\\">\\n                    <label>{{ t('Muted') }}</label>\\n                </div>\\n                <div class=\\\"input ltr\\\">                    \\n                    <button type=\\\"button\\\" :class=\\\"{lightswitch: true, on: muted}\\\">\\n                        <div class=\\\"lightswitch-container\\\">\\n                            <div class=\\\"handle\\\"></div>\\n                        </div>\\n                        <input type=\\\"hidden\\\" :name=\\\"'displayers['+kind+'][options][muted]'\\\">\\n                    </button>\\n                </div>\\n            </div>\\n            <div class=\\\"field\\\">\\n                <div class=\\\"heading\\\">\\n                    <label>{{ t('Autoplay') }}</label>\\n                </div>\\n                <div class=\\\"input ltr\\\">                    \\n                    <button type=\\\"button\\\" :class=\\\"{lightswitch: true, on: autoplay}\\\">\\n                        <div class=\\\"lightswitch-container\\\">\\n                            <div class=\\\"handle\\\"></div>\\n                        </div>\\n                        <input type=\\\"hidden\\\" :name=\\\"'displayers['+kind+'][options][autoplay]'\\\">\\n                    </button>\\n                </div>\\n            </div>\\n        </div>\\n        \"\n  };\n  e.detail['html_audio'] = {\n    props: {\n      displayer: Object,\n      kind: String,\n      errors: Object\n    },\n    data: function data() {\n      return {\n        controls: false,\n        muted: false,\n        autoplay: false\n      };\n    },\n    created: function created() {\n      this.controls = this.displayer.options.controls;\n      this.muted = this.displayer.options.muted;\n      this.autoplay = this.displayer.options.autoplay;\n    },\n    mounted: function mounted() {\n      var _this2 = this;\n\n      this.$nextTick(function () {\n        Craft.initUiElements(_this2.$el);\n      });\n    },\n    template: \"\\n        <div>\\n            <div class=\\\"field\\\">\\n                <div class=\\\"heading\\\">\\n                    <label>{{ t('Show controls') }}</label>\\n                </div>\\n                <div class=\\\"input ltr\\\">                    \\n                    <button type=\\\"button\\\" :class=\\\"{lightswitch: true, on: controls}\\\">\\n                        <div class=\\\"lightswitch-container\\\">\\n                            <div class=\\\"handle\\\"></div>\\n                        </div>\\n                        <input type=\\\"hidden\\\" :name=\\\"'displayers['+kind+'][options][controls]'\\\">\\n                    </button>\\n                </div>\\n            </div>\\n            <div class=\\\"field\\\">\\n                <div class=\\\"heading\\\">\\n                    <label>{{ t('Muted') }}</label>\\n                </div>\\n                <div class=\\\"input ltr\\\">                    \\n                    <button type=\\\"button\\\" :class=\\\"{lightswitch: true, on: muted}\\\">\\n                        <div class=\\\"lightswitch-container\\\">\\n                            <div class=\\\"handle\\\"></div>\\n                        </div>\\n                        <input type=\\\"hidden\\\" :name=\\\"'displayers['+kind+'][options][muted]'\\\">\\n                    </button>\\n                </div>\\n            </div>\\n            <div class=\\\"field\\\">\\n                <div class=\\\"heading\\\">\\n                    <label>{{ t('Autoplay') }}</label>\\n                </div>\\n                <div class=\\\"input ltr\\\">                    \\n                    <button type=\\\"button\\\" :class=\\\"{lightswitch: true, on: autoplay}\\\">\\n                        <div class=\\\"lightswitch-container\\\">\\n                            <div class=\\\"handle\\\"></div>\\n                        </div>\\n                        <input type=\\\"hidden\\\" :name=\\\"'displayers['+kind+'][options][autoplay]'\\\">\\n                    </button>\\n                </div>\\n            </div>\\n        </div>\\n        \"\n  };\n  e.detail['image_transform'] = {\n    props: {\n      displayer: Object,\n      kind: String,\n      errors: Object\n    },\n    data: function data() {\n      return {\n        transform: '',\n        custom: ''\n      };\n    },\n    created: function created() {\n      this.transform = this.displayer.options.transform;\n      this.custom = this.displayer.options.custom;\n\n      if (!this.transform) {// this.transform = Object.keys(this.displayer.imageTransforms)[0];\n      }\n    },\n    methods: {\n      errorList: function errorList(field) {\n        var _this$errors$field3;\n\n        return (_this$errors$field3 = this.errors[field]) !== null && _this$errors$field3 !== void 0 ? _this$errors$field3 : [];\n      }\n    },\n    template: \"\\n        <div>\\n            <div class=\\\"field\\\">\\n                <div class=\\\"heading\\\">\\n                    <label class=\\\"required\\\">{{ t('Transform') }}</label>\\n                </div>\\n                <div class=\\\"input ltr\\\">                    \\n                    <div class=\\\"select\\\">\\n                        <select :name=\\\"'displayers['+kind+'][options][transform]'\\\" v-model=\\\"transform\\\">\\n                            <option :value=\\\"handle\\\" v-for=\\\"name, handle in displayer.imageTransforms\\\">{{ name }}</option>\\n                            <option value=\\\"_custom\\\">{{ t('Custom') }}</option>\\n                        </select>\\n                    </div>\\n                </div>\\n                <ul class=\\\"errors\\\" v-if=\\\"errorList('transform')\\\">\\n                    <li v-for=\\\"error in errorList('transform')\\\">{{ error }}</li>\\n                </ul>\\n            </div>\\n            <div class=\\\"field\\\" v-if=\\\"transform == '_custom'\\\">\\n                <div class=\\\"heading\\\">\\n                    <label class=\\\"required\\\">{{ t('Custom') }}</label>\\n                </div>\\n                <div class=\\\"input ltr\\\">\\n                    <input type=\\\"text\\\" class=\\\"fullwidth text\\\" :name=\\\"'displayers['+kind+'][options][custom]'\\\" v-model=\\\"custom\\\">\\n                </div>\\n                <div class=\\\"instructions\\\">{{ t('Enter a json list of options to transform the image, example: { \\\"width\\\": 300, \\\"height\\\": 300 }') }}</div>\\n                <ul class=\\\"errors\\\" v-if=\\\"errorList('custom')\\\">\\n                    <li v-for=\\\"error in errorList('custom')\\\">{{ error }}</li>\\n                </ul>\\n            </div>\\n        </div>\\n        \"\n  };\n  e.detail['link'] = {\n    props: {\n      displayer: Object,\n      kind: String,\n      errors: Object\n    },\n    data: function data() {\n      return {\n        label: 'title',\n        custom: '',\n        newTab: false,\n        download: false\n      };\n    },\n    created: function created() {\n      this.label = this.displayer.options.label;\n      this.custom = this.displayer.options.custom;\n      this.newTab = this.displayer.options.newTab;\n      this.download = this.displayer.options.download;\n    },\n    methods: {\n      errorList: function errorList(field) {\n        var _this$errors$field4;\n\n        return (_this$errors$field4 = this.errors[field]) !== null && _this$errors$field4 !== void 0 ? _this$errors$field4 : [];\n      }\n    },\n    mounted: function mounted() {\n      var _this3 = this;\n\n      this.$nextTick(function () {\n        Craft.initUiElements(_this3.$el);\n      });\n    },\n    template: \"\\n        <div>\\n            <div class=\\\"field\\\">\\n                <div class=\\\"heading\\\">\\n                    <label class=\\\"required\\\">{{ t('Label') }}</label>\\n                </div>\\n                <div class=\\\"input ltr\\\">                    \\n                    <div class=\\\"select\\\">\\n                        <select :name=\\\"'displayers['+kind+'][options][label]'\\\" v-model=\\\"label\\\">\\n                            <option value=\\\"title\\\">{{ t('Asset title') }}</option>\\n                            <option value=\\\"filename\\\">{{ t('File name') }}</option>\\n                            <option value=\\\"custom\\\">{{ t('Custom') }}</option>\\n                        </select>\\n                    </div>\\n                </div>\\n                <ul class=\\\"errors\\\" v-if=\\\"errorList('label')\\\">\\n                    <li v-for=\\\"error in errorList('label')\\\">{{ error }}</li>\\n                </ul>\\n            </div>\\n            <div class=\\\"field\\\" v-if=\\\"label == 'custom'\\\">\\n                <div class=\\\"heading\\\">\\n                    <label class=\\\"required\\\">{{ t('Custom') }}</label>\\n                </div>\\n                <div class=\\\"input ltr\\\">\\n                    <input type=\\\"text\\\" class=\\\"fullwidth text\\\" :name=\\\"'displayers['+kind+'][options][custom]'\\\" v-model=\\\"custom\\\">\\n                </div>\\n                <ul class=\\\"errors\\\" v-if=\\\"errorList('custom')\\\">\\n                    <li v-for=\\\"error in errorList('custom')\\\">{{ error }}</li>\\n                </ul>\\n            </div>\\n            <div class=\\\"field\\\">\\n                <div class=\\\"heading\\\">\\n                    <label>{{ t('Open in new tab') }}</label>\\n                </div>\\n                <div class=\\\"input ltr\\\">                    \\n                    <button type=\\\"button\\\" :class=\\\"{lightswitch: true, on: newTab}\\\">\\n                        <div class=\\\"lightswitch-container\\\">\\n                            <div class=\\\"handle\\\"></div>\\n                        </div>\\n                        <input type=\\\"hidden\\\" :name=\\\"'displayers['+kind+'][options][newTab]'\\\" :value=\\\"newTab ? 1 : ''\\\">\\n                    </button>\\n                </div>\\n            </div>\\n            <div class=\\\"field\\\">\\n                <div class=\\\"heading\\\">\\n                    <label>{{ t('Download link') }}</label>\\n                </div>\\n                <div class=\\\"input ltr\\\">                    \\n                    <button type=\\\"button\\\" :class=\\\"{lightswitch: true, on: download}\\\">\\n                        <div class=\\\"lightswitch-container\\\">\\n                            <div class=\\\"handle\\\"></div>\\n                        </div>\\n                        <input type=\\\"hidden\\\" :name=\\\"'displayers['+kind+'][options][download]'\\\" :value=\\\"download ? 1 : ''\\\">\\n                    </button>\\n                </div>\\n            </div>\\n        </div>\"\n  };\n});\n\n//# sourceURL=webpack:///./vue/src/fileDisplayers/main.js?");

/***/ })

/******/ });