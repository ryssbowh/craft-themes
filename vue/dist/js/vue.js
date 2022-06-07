/*
 * ATTENTION: The "eval" devtool has been used (maybe by default in mode: "development").
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
/******/ (function() { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./vue/src/Helpers.js":
/*!****************************!*\
  !*** ./vue/src/Helpers.js ***!
  \****************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   \"Clone\": function() { return /* binding */ Clone; },\n/* harmony export */   \"FieldComponent\": function() { return /* binding */ FieldComponent; },\n/* harmony export */   \"HandleError\": function() { return /* binding */ HandleError; },\n/* harmony export */   \"HandleGenerator\": function() { return /* binding */ HandleGenerator; },\n/* harmony export */   \"SelectInput\": function() { return /* binding */ SelectInput; },\n/* harmony export */   \"Translate\": function() { return /* binding */ Translate; }\n/* harmony export */ });\n/* harmony import */ var uuid__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! uuid */ \"./node_modules/uuid/dist/esm-browser/index.js\");\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! lodash */ \"./node_modules/lodash/lodash.js\");\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_0__);\n\n\nconst Translate = {\n  install(app) {\n    app.config.globalProperties.t = (str, params, category = 'themes') => {\n      return window.Craft.t(category, str, params);\n    };\n  }\n\n};\nconst HandleError = {\n  install(app) {\n    app.config.globalProperties.handleError = err => {\n      let message = err;\n\n      if (err.response) {\n        if (err.response.data.message ?? null) {\n          message = err.response.data.message;\n        } else if (err.response.data.error ?? null) {\n          message = err.response.data.error;\n        }\n      }\n\n      Craft.cp.displayError(message);\n    };\n  }\n\n};\nconst Clone = {\n  install(app) {\n    app.config.globalProperties.cloneDisplay = display => {\n      let newDisplay = (0,lodash__WEBPACK_IMPORTED_MODULE_0__.merge)({}, display);\n      let newItem;\n\n      if (display.type == 'group') {\n        newItem = app.config.globalProperties.cloneGroup(display.item);\n      } else {\n        newItem = app.config.globalProperties.cloneField(display.item);\n      }\n\n      newDisplay.item = newItem;\n      newDisplay.id = null;\n      newDisplay.uid = (0,uuid__WEBPACK_IMPORTED_MODULE_1__.v4)();\n      return newDisplay;\n    };\n\n    app.config.globalProperties.cloneField = field => {\n      let newField;\n\n      if (typeof window.CraftThemes.fieldComponents[field.type] != 'undefined') {\n        newField = window.CraftThemes.fieldComponents[field.type].clone(field, app);\n      } else {\n        newField = (0,lodash__WEBPACK_IMPORTED_MODULE_0__.merge)({}, field);\n      }\n\n      newField.id = null;\n      newField.uid = (0,uuid__WEBPACK_IMPORTED_MODULE_1__.v4)();\n      return newField;\n    };\n\n    app.config.globalProperties.cloneGroup = group => {\n      let newGroup = (0,lodash__WEBPACK_IMPORTED_MODULE_0__.merge)({}, group);\n      let displays = [];\n\n      for (let i in group.displays) {\n        displays.push(app.config.globalProperties.cloneDisplay(group.displays[i]));\n      }\n\n      newGroup.displays = displays;\n      newGroup.id = null;\n      newGroup.uid = (0,uuid__WEBPACK_IMPORTED_MODULE_1__.v4)();\n      return newGroup;\n    };\n  }\n\n};\nconst FieldComponent = {\n  install(app) {\n    app.config.globalProperties.fieldComponent = type => {\n      if (typeof window.CraftThemes.fieldComponents[type] != 'undefined') {\n        return 'field-' + type;\n      }\n\n      return 'field';\n    };\n  }\n\n};\nconst HandleGenerator = Craft.HandleGenerator.extend({\n  callback: null,\n  updateTarget: function () {\n    if (!this.$target.is(':visible')) {\n      return;\n    }\n\n    var sourceVal = this.$source.val();\n\n    if (typeof sourceVal === 'undefined') {\n      // The source input may not exist anymore\n      return;\n    }\n\n    var targetVal = this.generateTargetValue(sourceVal);\n    this.$target.val(targetVal);\n    this.$target.trigger('change');\n    this.$target.trigger('input'); // If the target already has focus, select its whole value to mimic\n    // the behavior if the value had already been generated and they just tabbed in\n\n    if (this.$target.is(':focus')) {\n      Craft.selectFullValue(this.$target);\n    }\n\n    if (this.callback) {\n      this.callback(targetVal);\n    }\n  }\n});\nconst SelectInput = Craft.BaseElementSelectInput.extend({\n  theme: null,\n  actionUrl: null,\n  createElementCallback: null,\n  initialIds: [],\n  errors: {},\n\n  init(args) {\n    this.base(args);\n\n    if (this.initialIds.length) {\n      Craft.postActionRequest(this.actionUrl, {\n        theme: this.theme,\n        id: this.initialIds\n      }, response => {\n        let elements = [];\n\n        for (let data of response) {\n          elements.push(this.createElementCallback(data));\n        }\n\n        this.selectElements2(elements);\n        this.updateErrors();\n      });\n    }\n  },\n\n  setSettings: function () {\n    this.base.apply(this, arguments);\n    this.theme = arguments[0].theme;\n    this.actionUrl = arguments[0].actionUrl;\n    this.createElementCallback = arguments[0].createElementCallback;\n    this.initialIds = arguments[0].initialIds;\n    this.errors = arguments[0].errors;\n  },\n  createNewElement: function (elementInfo) {\n    let element = this.base(elementInfo);\n    let $row = $('<div class=\"row\" style=\"margin-bottom:5px;display:flex;justify-content:space-between;align-items:center\">');\n    $row.append(element);\n\n    if (!elementInfo.viewModes) {\n      this.fetchViewModes(elementInfo.id).done(response => {\n        this.appendViewModes($row, response[0].viewModes, elementInfo.viewMode ?? response[0].viewModes[0].uid);\n        this.trigger('viewModesChanged');\n      });\n    } else {\n      this.appendViewModes($row, elementInfo.viewModes, elementInfo.viewMode);\n    }\n\n    return $row;\n  },\n  updateErrors: function () {\n    this.$container.find('.element-error').remove();\n\n    for (let id in this.errors) {\n      let $elem = this.$elements.find('.element[data-id=' + id + ']').parent();\n      let $error = $('<div class=\"error element-error\">' + this.errors[id] + '</div>');\n      $elem.find('.select-wrapper').append($error);\n    }\n  },\n\n  /**\n   * Same method but without the animation\n   */\n  selectElements2: function (elements) {\n    for (let i = 0; i < elements.length; i++) {\n      let elementInfo = elements[i],\n          $element = this.createNewElement(elementInfo);\n      this.appendElement($element);\n      this.addElements($element); // Override the element reference with the new one\n\n      elementInfo.$element = $element;\n    }\n\n    this.onSelectElements(elements);\n  },\n  fetchViewModes: function (id) {\n    let data = {\n      theme: this.theme,\n      id: id\n    };\n    return Craft.postActionRequest(this.actionUrl, data);\n  },\n  appendViewModes: function ($row, viewModes, selectedViewMode) {\n    let select = $('<div class=\"select-wrapper\"><div class=\"select\"><select><option value=\"\">' + Craft.t('themes', 'Select a view mode') + '</select></select></div></div>');\n    viewModes.forEach(viewMode => {\n      select.find('select').append('<option value=\"' + viewMode.uid + '\"' + (viewMode.uid == selectedViewMode ? ' selected' : '') + '>' + viewMode.name + '</options>');\n    });\n    select.find('select').on('change', () => {\n      this.trigger('viewModesChanged');\n    });\n    $row.append(select);\n  },\n  getSelectedElementIds: function () {\n    var ids = [];\n\n    for (var i = 0; i < this.$elements.length; i++) {\n      ids.push(this.$elements.eq(i).find('.element').data('id'));\n    }\n\n    return ids;\n  },\n  getSelectedElementData: function () {\n    let data = [];\n\n    for (var i = 0; i < this.$elements.length; i++) {\n      let elem = this.$elements.eq(i);\n      data.push({\n        id: elem.find('.element').data('id'),\n        viewMode: elem.find('select').val()\n      });\n    }\n\n    return data;\n  },\n  removeElements: function ($elements) {\n    if (this.settings.selectable) {\n      this.elementSelect.removeItems($elements);\n    }\n\n    if (this.modal) {\n      var ids = [];\n\n      for (var i = 0; i < $elements.length; i++) {\n        var id = $elements.find('.element').eq(i).data('id');\n\n        if (id) {\n          ids.push(id);\n        }\n      }\n\n      if (ids.length) {\n        this.modal.elementIndex.enableElementsById(ids);\n      }\n    } // Disable the hidden input in case the form is submitted before this element gets removed from the DOM\n\n\n    $elements.children('input').prop('disabled', true);\n    this.$elements = this.$elements.not($elements);\n    this.updateAddElementsBtn();\n    this.onRemoveElements();\n  },\n  removeElement: function ($element) {\n    this.removeElements($element.parent());\n    this.animateElementAway($element.parent(), () => {\n      $element.parent().remove();\n    });\n  },\n  initElementSort: function () {\n    //Ignoring the select as handle when sorting elements :\n    this.base();\n    this.elementSort.settings.ignoreHandleSelector = '.delete, .select';\n\n    this.elementSort.settings.onSortChange = () => {\n      this.trigger('orderChanged');\n    };\n  }\n});\n\n\n//# sourceURL=webpack:///./vue/src/Helpers.js?");

/***/ }),

/***/ "./vue/src/forms/Checkboxes.js":
/*!*************************************!*\
  !*** ./vue/src/forms/Checkboxes.js ***!
  \*************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _Field__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Field */ \"./vue/src/forms/Field.js\");\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  computed: {\n    inputClass() {\n      return 'input ' + Craft.orientation;\n    }\n\n  },\n  props: {\n    value: Array,\n    definition: Object,\n    errors: Array,\n    name: String\n  },\n  data: function () {\n    return {\n      id: null\n    };\n  },\n\n  created() {\n    this.id = Math.floor(Math.random() * 1000000);\n  },\n\n  mounted() {\n    $(this.$el).find('[type=checkbox]').on('change', () => {\n      let val = [];\n      $.each($(this.$el).find('[type=checkbox]'), function (i, elem) {\n        if ($(elem).is(':checked')) {\n          val.push($(elem).val());\n        }\n      });\n      this.$emit('change', val);\n    });\n  },\n\n  components: {\n    'form-field': _Field__WEBPACK_IMPORTED_MODULE_0__[\"default\"]\n  },\n  emits: ['change'],\n  template: `\n        <form-field :errors=\"errors\" :definition=\"definition\" :name=\"name\">\n            <template v-slot:main>\n                <div :class=\"inputClass\">\n                    <fieldset class=\"checkbox-group\">\n                        <div v-for=\"label, cvalue in definition.options\" v-bind:key=\"cvalue\">\n                            <input type=\"checkbox\" :checked=\"value.includes(cvalue)\" class=\"checkbox\" :value=\"cvalue\" :id=\"id + '-' + cvalue\" :disabled=\"definition.disabled\">\n                            <label :for=\"id + '-' + cvalue\">\n                                {{ label }}\n                            </label>\n                        </div>\n                    </fieldset>\n                </div>\n            </template>\n        </form-field>`\n});\n\n//# sourceURL=webpack:///./vue/src/forms/Checkboxes.js?");

/***/ }),

/***/ "./vue/src/forms/Color.js":
/*!********************************!*\
  !*** ./vue/src/forms/Color.js ***!
  \********************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _Field__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Field */ \"./vue/src/forms/Field.js\");\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  props: {\n    value: String,\n    definition: Object,\n    errors: Array,\n    name: String\n  },\n\n  mounted() {\n    this.$nextTick(() => {\n      new Craft.ColorInput($(this.$el).find('.color-container'));\n      $(this.$el).find('input.color-preview-input').on('change', () => {\n        this.$emit('change', $(this.$el).find('input.color-preview-input').val());\n      });\n    });\n  },\n\n  components: {\n    'form-field': _Field__WEBPACK_IMPORTED_MODULE_0__[\"default\"]\n  },\n  emits: ['change'],\n  template: `\n        <form-field :errors=\"errors\" :definition=\"definition\" :name=\"name\">\n            <template v-slot:main>\n                <div class=\"flex color-container\">\n                    <div class=\"color static\">\n                        <div class=\"color-preview\" :style=\"value ? 'background-color:' + value : ''\"></div>\n                    </div>\n                    <input class=\"color-input text\" type=\"text\" size=\"10\" :value=\"value\">\n                </div>\n            </template>\n        </form-field>`\n});\n\n//# sourceURL=webpack:///./vue/src/forms/Color.js?");

/***/ }),

/***/ "./vue/src/forms/Date.js":
/*!*******************************!*\
  !*** ./vue/src/forms/Date.js ***!
  \*******************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _Field__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Field */ \"./vue/src/forms/Field.js\");\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  computed: {\n    inputClass() {\n      return 'input ' + Craft.orientation;\n    }\n\n  },\n  props: {\n    value: String,\n    definition: Object,\n    errors: Array,\n    name: String\n  },\n  components: {\n    'form-field': _Field__WEBPACK_IMPORTED_MODULE_0__[\"default\"]\n  },\n\n  mounted() {\n    this.$nextTick(() => {\n      $(this.$el).find('input.text').datepicker(Craft.datepickerOptions);\n      $(this.$el).find('input.text').on('change', () => {\n        this.$emit('change', $(this.$el).find('input.text').val());\n      });\n    });\n  },\n\n  emits: ['change'],\n  template: `\n        <form-field :errors=\"errors\" :definition=\"definition\" :name=\"name\">\n            <template v-slot:main>\n                <div :class=\"inputClass\">\n                    <div class=\"datewrapper\">\n                        <input type=\"text\" class=\"text\" :value=\"value\" size=\"10\" autocomplete=\"off\" placeholder=\" \">\n                        <div data-icon=\"date\"></div>\n                    </div>\n                </div>\n            </template>\n        </form-field>`\n});\n\n//# sourceURL=webpack:///./vue/src/forms/Date.js?");

/***/ }),

/***/ "./vue/src/forms/DateTime.js":
/*!***********************************!*\
  !*** ./vue/src/forms/DateTime.js ***!
  \***********************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _Field__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Field */ \"./vue/src/forms/Field.js\");\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  computed: {\n    inputClass() {\n      return 'input ' + Craft.orientation;\n    }\n\n  },\n  props: {\n    value: String,\n    definition: Object,\n    errors: Array,\n    name: String\n  },\n  components: {\n    'form-field': _Field__WEBPACK_IMPORTED_MODULE_0__[\"default\"]\n  },\n\n  mounted() {\n    this.$nextTick(() => {\n      $(this.$el).find('input.date').datepicker(Craft.datepickerOptions);\n      $(this.$el).find('input.date').on('change', () => {\n        this.updateValue();\n      });\n      let options = {\n        minTime: this.definition.minTime ?? null,\n        maxTime: this.definition.maxTime ?? null,\n        disableTimeRanges: this.definition.disableTimeRanges ?? null,\n        step: this.definition.minuteIncrement ?? 5,\n        forceRoundTime: this.definition.forceRoundTime ?? false\n      };\n      options = { ...options,\n        ...Craft.timepickerOptions\n      };\n      let input = $(this.$el).find('input.time');\n      input.timepicker(options);\n      input.on('changeTime', () => {\n        this.updateValue();\n      });\n    });\n  },\n\n  methods: {\n    updateValue() {\n      let val = $(this.$el).find('input.date').val() + ' ' + $(this.$el).find('input.time').val();\n      this.$emit('change', val);\n    }\n\n  },\n  emits: ['change'],\n  template: `\n        <form-field :errors=\"errors\" :definition=\"definition\" :name=\"name\">\n            <template v-slot:main>\n                <div :class=\"inputClass\">\n                    <div class=\"datetimewrapper\">\n                        <div class=\"datewrapper\">\n                            <input type=\"text\" class=\"text date\" :value=\"value ? value.split(' ')[0] ?? '' : ''\" size=\"10\" autocomplete=\"off\" placeholder=\" \">\n                            <div data-icon=\"date\"></div>\n                        </div>\n                        <div class=\"timewrapper\">\n                            <input type=\"text\" class=\"text time\" :value=\"value ? value.split(' ')[1] ?? '' : ''\" size=\"10\" autocomplete=\"off\" placeholder=\" \">\n                            <div data-icon=\"time\"></div>\n                        </div>\n                    </div>\n                </div>\n            </template>\n        </form-field>`\n});\n\n//# sourceURL=webpack:///./vue/src/forms/DateTime.js?");

/***/ }),

/***/ "./vue/src/forms/Elements.js":
/*!***********************************!*\
  !*** ./vue/src/forms/Elements.js ***!
  \***********************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _Field__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Field */ \"./vue/src/forms/Field.js\");\n/* harmony import */ var vuex__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! vuex */ \"./node_modules/vuex/dist/vuex.esm-bundler.js\");\n/* harmony import */ var _Helpers_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../Helpers.js */ \"./vue/src/Helpers.js\");\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  data: function () {\n    return {\n      realValue: {}\n    };\n  },\n  computed: {\n    inputClass: function () {\n      return 'input ' + Craft.orientation;\n    },\n    mainErrors: function () {\n      let main = [];\n\n      for (let i in this.errors) {\n        if (typeof this.errors[i] == 'string') {\n          main.push(this.errors[i]);\n        }\n      }\n\n      return main;\n    },\n    options: function () {\n      switch (this.definition.elementType) {\n        case 'assets':\n          return {\n            elementType: 'craft\\\\elements\\\\Asset',\n            id: 'field-assets',\n            class: 'elementselect',\n            ajaxUrl: 'assets-data',\n            elementClass: 'element small hasthumb'\n          };\n\n        case 'users':\n          return {\n            elementType: 'craft\\\\elements\\\\User',\n            id: 'field-users',\n            class: 'elementselect',\n            ajaxUrl: 'users-data',\n            elementClass: 'element small hasstatus hasthumb'\n          };\n\n        case 'categories':\n          return {\n            elementType: 'craft\\\\elements\\\\Category',\n            id: 'field-categories',\n            class: 'categoriesfield',\n            ajaxUrl: 'categories-data',\n            elementClass: 'element small hasstatus'\n          };\n\n        case 'entries':\n          return {\n            elementType: 'craft\\\\elements\\\\Entry',\n            id: 'field-entries',\n            class: 'elementselect',\n            ajaxUrl: 'entries-data',\n            elementClass: 'element small hasstatus'\n          };\n\n        default:\n          return {};\n      }\n    },\n    ...(0,vuex__WEBPACK_IMPORTED_MODULE_2__.mapState)(['theme'])\n  },\n  props: {\n    value: Object,\n    definition: Object,\n    errors: Array,\n    name: String\n  },\n\n  created() {\n    this.realValue = this.value;\n\n    if (this.realValue === null) {\n      this.realValue = [];\n    }\n  },\n\n  mounted() {\n    this.createSelector();\n  },\n\n  methods: {\n    createSelector: function () {\n      this.selector = new _Helpers_js__WEBPACK_IMPORTED_MODULE_1__.SelectInput({\n        actionUrl: 'themes/cp-ajax/' + this.options.ajaxUrl,\n        id: 'field-' + this.name + '-elements',\n        elementType: this.options.elementType,\n        name: 'field-' + this.name,\n        sources: '*',\n        viewMode: 'small',\n        branchLimit: 1,\n        theme: this.theme,\n        selectable: 0,\n        createElementCallback: this.createElement,\n        errors: this.getElementsErrors(),\n        initialIds: Object.keys(this.realValue).map(i => {\n          return this.realValue[i].id;\n        })\n      });\n      this.selector.on('viewModesChanged', () => {\n        this.realValue = this.selector.getSelectedElementData();\n      });\n      this.selector.on('removeElements', () => {\n        this.realValue = this.selector.getSelectedElementData();\n      });\n      this.selector.on('orderChanged', () => {\n        this.realValue = this.selector.getSelectedElementData();\n      });\n    },\n    createElement: function (element) {\n      let inner;\n\n      switch (this.definition.elementType) {\n        case 'assets':\n          inner = `<div class=\"elementthumb\">\n                            <img sizes=\"34px\" srcset=\"` + element.srcset + `\" alt=\"\">\n                        </div>\n                        <div class=\"label\">\n                            <span class=\"title\">` + element.title + `</span>\n                        </div>`;\n          break;\n\n        case 'users':\n          inner = `<span class=\"status ` + element.status + `\"></span>\n                        <div class=\"elementthumb rounded\">\n                            <img sizes=\"34px\" srcset=\"` + element.srcset + `\" alt=\"\">\n                        </div>\n                        <div class=\"label\">\n                            <span class=\"title\">` + element.name + `</span>\n                        </div>`;\n          break;\n\n        case 'categories':\n          inner = `<span class=\"status ` + element.status + `\"></span>\n                        <div class=\"label\">\n                            <span class=\"title\">` + element.title + `</span>\n                        </div>`;\n          break;\n\n        case 'entries':\n          inner = `<span class=\"status ` + element.status + `\"></span>\n                        <div class=\"label\">\n                            <span class=\"title\">` + element.title + `</span>\n                        </div>`;\n          break;\n      }\n\n      return {\n        $element: $(`\n                <div class=\"` + this.options.elementClass + `\"\n                    data-type=\"` + this.options.elementType + `\"\n                    data-id=\"` + element.id + `\"\n                    data-label=\"` + element.title + `\"\n                    title=\"` + element.title + `\"\n                >` + inner + `\n                </div>`),\n        id: element.id,\n        viewModes: element.viewModes,\n        viewMode: this.realValue.filter(e => e.id == element.id)[0].viewMode ?? null\n      };\n    },\n    getElementsErrors: function () {\n      let errors = {};\n\n      for (let i in this.errors) {\n        if (typeof this.errors[i] == 'string') {\n          continue;\n        }\n\n        let keys = Object.keys(this.errors[i]);\n        errors[keys[0]] = this.errors[i][keys[0]];\n      }\n\n      return errors;\n    }\n  },\n  watch: {\n    realValue: {\n      handler: function () {\n        this.$emit('change', this.realValue);\n      },\n      deep: true\n    },\n    errors: {\n      handler: function () {\n        if (this.selector) {\n          this.selector.errors = this.getElementsErrors();\n          this.selector.updateErrors();\n        }\n      },\n      deep: true\n    }\n  },\n  components: {\n    'form-field': _Field__WEBPACK_IMPORTED_MODULE_0__[\"default\"]\n  },\n  emits: ['change'],\n  template: `\n        <form-field :errors=\"errors\" :definition=\"definition\" :name=\"name\" :classes=\"'select-elements'\">\n            <template v-slot:heading>\n                <div class=\"heading\" v-if=\"definition.label\">\n                    <label :class=\"{required: definition.required ?? false}\">{{ definition.label }}</label>\n                    <label :class=\"{required: definition.required ?? false}\">{{ t('View mode') }}</label>\n                </div>\n            </template>\n            <template v-slot:main>\n                <div :class=\"inputClass\">\n                    <div :id=\"'field-' + name + '-elements'\" :class=\"options.class\">\n                        <div class=\"elements\">\n                        </div>\n                        <div class=\"flex\">\n                            <button type=\"button\" class=\"btn add icon dashed\">{{ definition.addElementLabel }}</button>\n                        </div>\n                    </div>\n                </div>\n            </template>\n            <template v-slot:errors>\n                <ul class=\"errors\" v-if=\"mainErrors\">\n                    <li class=\"error\" v-for=\"error, index in mainErrors\" v-bind:key=\"index\">\n                        {{ error }}\n                    </li>\n                </ul>\n            </template>\n        </form-field>`\n});\n\n//# sourceURL=webpack:///./vue/src/forms/Elements.js?");

/***/ }),

/***/ "./vue/src/forms/FetchViewMode.js":
/*!****************************************!*\
  !*** ./vue/src/forms/FetchViewMode.js ***!
  \****************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _Field__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Field */ \"./vue/src/forms/Field.js\");\n/* harmony import */ var vuex__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! vuex */ \"./node_modules/vuex/dist/vuex.esm-bundler.js\");\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  data: function () {\n    return {\n      realValue: {},\n      viewModes: {},\n      element: false\n    };\n  },\n  computed: {\n    inputClass: function () {\n      return 'input ' + Craft.orientation;\n    },\n    ...(0,vuex__WEBPACK_IMPORTED_MODULE_1__.mapState)(['theme'])\n  },\n  props: {\n    value: String,\n    definition: Object,\n    errors: Array,\n    name: String\n  },\n\n  created() {\n    if (this.definition.element ?? null) {\n      this.element = this.definition.element;\n    }\n\n    this.realValue = this.value;\n  },\n\n  mounted() {\n    if ((this.definition.element ?? null) && this.definition.element.startsWith('from:')) {\n      let elems = this.definition.element.split(':');\n      let $elem = $(elems[1]).find(elems[2]);\n      this.element = $elem.val();\n      $elem.change(() => {\n        this.element = $elem.val();\n        this.fetchViewModes();\n      });\n    }\n\n    this.fetchViewModes();\n  },\n\n  methods: {\n    fetchViewModes() {\n      let url = 'themes/ajax/view-modes/' + this.theme + '/' + this.definition.layoutType;\n\n      if (this.element) {\n        url += '/' + this.element;\n      }\n\n      axios.post(Craft.getCpUrl(url)).then(response => {\n        this.viewModes = response.data.viewModes;\n      }).catch(err => {\n        this.handleError(err);\n      });\n    }\n\n  },\n  watch: {\n    realValue: {\n      handler: function () {\n        this.$emit('change', this.realValue);\n      },\n      deep: true\n    }\n  },\n  components: {\n    'form-field': _Field__WEBPACK_IMPORTED_MODULE_0__[\"default\"]\n  },\n  emits: ['change'],\n  template: `\n        <form-field :errors=\"errors\" :definition=\"definition\" :name=\"name\">\n            <template v-slot:main>\n                <div :class=\"inputClass\">                    \n                    <div class=\"select\">\n                        <select v-model=\"realValue\">\n                            <option v-for=\"viewMode in viewModes\" :value=\"viewMode.uid\" v-bind:key=\"viewMode.uid\">{{ viewMode.name }}</option>\n                        </select>\n                    </div>\n                </div>\n            </template>\n        </form-field>`\n});\n\n//# sourceURL=webpack:///./vue/src/forms/FetchViewMode.js?");

/***/ }),

/***/ "./vue/src/forms/Field.js":
/*!********************************!*\
  !*** ./vue/src/forms/Field.js ***!
  \********************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  props: {\n    definition: Object,\n    errors: Array,\n    name: String,\n    classes: String\n  },\n\n  mounted() {\n    this.$nextTick(() => {\n      Craft.initUiElements(this.$el);\n    });\n  },\n\n  template: `\n        <div :class=\"'field ' + classes\" :id=\"'field-' + name\">\n            <slot name=\"heading\">\n                <div class=\"heading\" v-if=\"definition.label\">\n                    <label :class=\"{required: definition.required ?? false}\">{{ definition.label }}</label>\n                </div>\n            </slot>\n            <slot name=\"instructions\">\n                <div class=\"instructions\" v-if=\"definition.instructions\" v-html=\"definition.instructions\">\n                </div>\n            </slot>\n            <slot name=\"main\">\n            </slot>\n            <slot name=\"tip\">\n                <p v-if=\"definition.tip\" class=\"notice with-icon\" v-html=\"definition.tip\">\n                </p>\n            </slot>\n            <slot name=\"warning\">\n                <p v-if=\"definition.warning\" class=\"warning with-icon\" v-html=\"definition.warning\">\n                </p>\n            </slot>\n            <slot name=\"errors\">\n                <ul class=\"errors\" v-if=\"errors\">\n                    <li class=\"error\" v-for=\"error, index in errors\" v-bind:key=\"index\">\n                        {{ error }}\n                    </li>\n                </ul>\n            </slot>\n        </div>`\n});\n\n//# sourceURL=webpack:///./vue/src/forms/Field.js?");

/***/ }),

/***/ "./vue/src/forms/FileDisplayers.js":
/*!*****************************************!*\
  !*** ./vue/src/forms/FileDisplayers.js ***!
  \*****************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _Field__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Field */ \"./vue/src/forms/Field.js\");\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  computed: {\n    inputClass() {\n      return 'input ' + Craft.orientation;\n    }\n\n  },\n  data: function () {\n    return {\n      realValue: {},\n      currentKind: null\n    };\n  },\n  props: {\n    value: Object,\n    definition: Object,\n    errors: Array,\n    name: String\n  },\n\n  created() {\n    let defaultDisplayer;\n\n    for (let kind in this.definition.mapping) {\n      defaultDisplayer = this.definition.mapping[kind].displayers[0];\n\n      if (this.value[kind] ?? null) {\n        this.realValue[kind] = this.value[kind];\n      } else {\n        this.realValue[kind] = {};\n      }\n\n      if (!this.realValue[kind].options) {\n        this.realValue[kind].options = defaultDisplayer.options.defaultValues;\n      }\n\n      if (!this.realValue[kind].displayer) {\n        this.realValue[kind].displayer = defaultDisplayer.handle;\n      }\n    }\n\n    this.currentKind = Object.keys(this.definition.mapping)[0] ?? null;\n  },\n\n  watch: {\n    realValue: {\n      handler: function () {\n        this.$emit('change', this.realValue);\n      },\n      deep: true\n    }\n  },\n  methods: {\n    formFieldComponent(field) {\n      return 'formfield-' + field;\n    },\n\n    getErrors: function (kind) {\n      for (let i in this.errors) {\n        let keys = Object.keys(this.errors[i]);\n\n        if ((keys[0] ?? null) == kind) {\n          return this.errors[i][kind];\n        }\n      }\n\n      return {};\n    },\n    hasErrors: function (kind) {\n      return Object.keys(this.getErrors(kind)).length != 0;\n    },\n    getDisplayer: function (kind) {\n      if (!this.definition.mapping[kind]) {\n        return null;\n      }\n\n      for (let i in this.definition.mapping[kind].displayers) {\n        let displayer = this.definition.mapping[kind].displayers[i];\n\n        if (this.realValue[kind].displayer == displayer.handle) {\n          return displayer;\n        }\n      }\n\n      return null;\n    },\n    getDisplayerName: function (kind) {\n      let displayer = this.getDisplayer(kind);\n      return displayer ? displayer.name : '';\n    },\n    updateDisplayer: function (kind, displayer) {\n      this.realValue[kind] = {\n        displayer: displayer,\n        options: this.getDisplayer(kind).options.defaultValues\n      };\n    },\n    updateOption: function (kind, name, value) {\n      this.realValue[kind].options[name] = value;\n    }\n  },\n  components: {\n    'form-field': _Field__WEBPACK_IMPORTED_MODULE_0__[\"default\"]\n  },\n  emits: ['change'],\n  template: `\n        <form-field :errors=\"errors\" :definition=\"definition\" :name=\"name\">\n            <template #main>\n                <div class=\"displayers-sidebar\">\n                    <div class=\"heading\">\n                        <h5>{{ t('File Kinds') }}</h5>\n                    </div>\n                    <a :class=\"{'kind-item': true, sel: currentKind == handle}\" v-for=\"elem, handle in definition.mapping\" v-bind:key=\"handle\" @click.prevent=\"currentKind = handle\">\n                        <div class=\"name\">\n                            <h4>{{ elem.label }} <span class=\"error\" data-icon=\"alert\" aria-label=\"Error\" v-if=\"hasErrors(handle)\"></span></h4>\n                            <div class=\"smalltext light code\" v-if=\"realValue[handle].displayer ?? null\">\n                                {{ getDisplayerName(handle) }}\n                            </div>\n                        </div>\n                    </a>\n                </div>\n                <div class=\"displayers-settings\">\n                    <div class=\"settings-container\">\n                        <div v-for=\"elem, handle in definition.mapping\" v-bind:key=\"handle\">\n                            <div class=\"displayer-settings\" v-show=\"currentKind == handle\">\n                                <div class=\"field\">\n                                    <div class=\"heading\">\n                                        <label class=\"required\">{{ t('Displayer') }}</label>\n                                    </div>\n                                    <div :class=\"inputClass\">\n                                        <div class=\"select\">\n                                            <select v-model=\"realValue[handle].displayer\" @change=\"updateDisplayer(handle, $event.target.value)\">\n                                                <option v-for=\"displayer, key in elem.displayers\" :value=\"displayer.handle\" v-bind:key=\"key\">{{ displayer.name }}</option>\n                                            </select>\n                                        </div>\n                                    </div>\n                                </div>\n                                <component v-for=\"definition, name in getDisplayer(handle).options.definitions\" :name=\"name\" :is=\"formFieldComponent(definition.field)\" :definition=\"definition\" :value=\"realValue[handle].options[name] ?? null\" :errors=\"getErrors(handle)[name] ?? []\" @change=\"updateOption(handle, name, $event)\" :key=\"name\"></component>\n                            </div>\n                        </div>\n                    </div>\n                </div>\n            </template>\n            <template #errors>\n                <span></span>\n            </template>\n            <template #heading>\n                <span></span>\n            </template>\n            <template #instructions>\n                <span></span>\n            </template>\n            <template #warning>\n                <span></span>\n            </template>\n            <template #tip>\n                <span></span>\n            </template>\n        </form-field>`\n});\n\n//# sourceURL=webpack:///./vue/src/forms/FileDisplayers.js?");

/***/ }),

/***/ "./vue/src/forms/Lightswitch.js":
/*!**************************************!*\
  !*** ./vue/src/forms/Lightswitch.js ***!
  \**************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _Field__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Field */ \"./vue/src/forms/Field.js\");\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  computed: {\n    inputClass() {\n      return 'input ' + Craft.orientation;\n    }\n\n  },\n  props: {\n    value: Boolean,\n    definition: Object,\n    errors: Array,\n    name: String\n  },\n\n  mounted() {\n    this.$nextTick(() => {\n      $(this.$el).find('.lightswitch').on('change', e => {\n        this.$emit('change', $(e.target).hasClass('on'));\n      });\n    });\n  },\n\n  components: {\n    'form-field': _Field__WEBPACK_IMPORTED_MODULE_0__[\"default\"]\n  },\n  emits: ['change'],\n  template: `\n        <form-field :errors=\"errors\" :definition=\"definition\" :name=\"name\">\n            <template v-slot:main>\n                <div :class=\"inputClass\">\n                    <div class=\"lightswitch-outer-container\" v-if=\"definition.onLabel\">\n                        <div class=\"lightswitch-inner-container\">\n                            <span data-toggle=\"off\" aria-hidden=\"true\" v-if=\"definition.offLabel\">{{ definition.offLabel }}</span>\n                            <button type=\"button\" :class=\"{lightswitch: true, on: value}\">\n                                <div class=\"lightswitch-container\">\n                                    <div class=\"handle\"></div>\n                                </div>\n                                <input type=\"hidden\" :value=\"value ? 1 : ''\">\n                            </button>\n                            <span data-toggle=\"off\" aria-hidden=\"true\" v-if=\"definition.onLabel\">{{ definition.onLabel }}</span>\n                        </div>\n                    </div>\n                    <button v-if=\"!definition.onLabel\" type=\"button\" :class=\"{lightswitch: true, on: value}\">\n                        <div class=\"lightswitch-container\">\n                            <div class=\"handle\"></div>\n                        </div>\n                        <input type=\"hidden\" :value=\"value ? 1 : ''\">\n                    </button>\n                </div>\n            </template>\n        </form-field>`\n});\n\n//# sourceURL=webpack:///./vue/src/forms/Lightswitch.js?");

/***/ }),

/***/ "./vue/src/forms/MultiSelect.js":
/*!**************************************!*\
  !*** ./vue/src/forms/MultiSelect.js ***!
  \**************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _Field__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Field */ \"./vue/src/forms/Field.js\");\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  computed: {\n    inputClass() {\n      return 'input ' + Craft.orientation;\n    }\n\n  },\n  data: function () {\n    return {\n      realValue: {}\n    };\n  },\n\n  created() {\n    this.realValue = this.value;\n  },\n\n  props: {\n    value: String,\n    definition: Object,\n    errors: Array,\n    name: String\n  },\n  watch: {\n    realValue: function () {\n      this.$emit('change', this.realValue);\n    }\n  },\n  components: {\n    'form-field': _Field__WEBPACK_IMPORTED_MODULE_0__[\"default\"]\n  },\n  emits: ['change'],\n  template: `\n        <form-field :errors=\"errors\" :definition=\"definition\" :name=\"name\">\n            <template v-slot:main>\n                <div :class=\"inputClass\">\n                    <div class=\"multiselect\">\n                        <select v-model=\"realValue\" :disabled=\"definition.disabled\" :autofocus=\"definition.autofocus ?? false\" multiple>\n                            <option v-for=\"label, value2 in definition.options ?? {}\" :value=\"value2\" v-bind:key=\"value2\">{{ label }}</option>\n                        </select>\n                    </div>\n                </div>\n            </template>\n        </form-field>`\n});\n\n//# sourceURL=webpack:///./vue/src/forms/MultiSelect.js?");

/***/ }),

/***/ "./vue/src/forms/Radio.js":
/*!********************************!*\
  !*** ./vue/src/forms/Radio.js ***!
  \********************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _Field__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Field */ \"./vue/src/forms/Field.js\");\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  computed: {\n    inputClass() {\n      return 'input ' + Craft.orientation;\n    }\n\n  },\n  props: {\n    value: String,\n    definition: Object,\n    errors: Array,\n    name: String\n  },\n  watch: {\n    value: function () {\n      this.$emit('change', this.value);\n    }\n  },\n  data: function () {\n    return {\n      id: null\n    };\n  },\n\n  created() {\n    this.id = Math.floor(Math.random() * 1000000);\n  },\n\n  mounted() {\n    let _this = this;\n\n    $(this.$el).find('[type=radio]').on('change', function () {\n      _this.$emit('change', $(this).val());\n    });\n  },\n\n  components: {\n    'form-field': _Field__WEBPACK_IMPORTED_MODULE_0__[\"default\"]\n  },\n  emits: ['change'],\n  template: `\n        <form-field :errors=\"errors\" :definition=\"definition\" :name=\"name\">\n            <template v-slot:main>\n                <div :class=\"inputClass\">\n                    <fieldset class=\"radio-group\">\n                        <div v-for=\"rvalue, label in definition.options\" v-bind:key=\"rvalue\">\n                            <label>\n                                <input type=\"radio\" :selected=\"rvalue == value\" :value=\"rvalue\" :disabled=\"definition.disabled\" :name=\"name\">\n                                {{ label }}\n                            </label>\n                        </div>\n                    </fieldset>\n                </div>\n            </template>\n        </form-field>`\n});\n\n//# sourceURL=webpack:///./vue/src/forms/Radio.js?");

/***/ }),

/***/ "./vue/src/forms/Select.js":
/*!*********************************!*\
  !*** ./vue/src/forms/Select.js ***!
  \*********************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _Field__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Field */ \"./vue/src/forms/Field.js\");\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  computed: {\n    inputClass() {\n      return 'input ' + Craft.orientation;\n    }\n\n  },\n  data: function () {\n    return {\n      realValue: {}\n    };\n  },\n\n  created() {\n    this.realValue = this.value;\n  },\n\n  props: {\n    value: String,\n    definition: Object,\n    errors: Array,\n    name: String\n  },\n  watch: {\n    realValue: function () {\n      this.$emit('change', this.realValue);\n    }\n  },\n  components: {\n    'form-field': _Field__WEBPACK_IMPORTED_MODULE_0__[\"default\"]\n  },\n  emits: ['change'],\n  template: `\n        <form-field :errors=\"errors\" :definition=\"definition\" :name=\"name\">\n            <template v-slot:main>\n                <div :class=\"inputClass\">\n                    <div class=\"select\">\n                        <select v-model=\"realValue\" :disabled=\"definition.disabled\" :autofocus=\"definition.autofocus ?? false\">\n                            <option v-for=\"label, value2 in definition.options ?? {}\" :value=\"value2\" v-bind:key=\"value2\">{{ label }}</option>\n                        </select>\n                    </div>\n                </div>\n            </template>\n        </form-field>`\n});\n\n//# sourceURL=webpack:///./vue/src/forms/Select.js?");

/***/ }),

/***/ "./vue/src/forms/Text.js":
/*!*******************************!*\
  !*** ./vue/src/forms/Text.js ***!
  \*******************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _Field__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Field */ \"./vue/src/forms/Field.js\");\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  computed: {\n    inputClass() {\n      return 'input ' + Craft.orientation;\n    }\n\n  },\n  data: function () {\n    return {\n      realValue: {}\n    };\n  },\n  props: {\n    value: [Number, String],\n    definition: Object,\n    errors: Array,\n    name: String\n  },\n\n  created() {\n    this.realValue = this.value;\n  },\n\n  watch: {\n    realValue: function () {\n      this.$emit('change', this.realValue);\n    }\n  },\n  components: {\n    'form-field': _Field__WEBPACK_IMPORTED_MODULE_0__[\"default\"]\n  },\n  emits: ['change'],\n  template: `\n        <form-field :errors=\"errors\" :definition=\"definition\" :name=\"name\">\n            <template v-slot:main>\n                <div :class=\"inputClass\">\n                    <input :class=\"{text: true, fullwidth: !definition.size}\" :type=\"definition.type ?? 'text'\" v-model=\"realValue\" :maxlength=\"definition.maxlength\" :autofocus=\"definition.autofocus ?? false\" :disabled=\"definition.disabled\" :readonly=\"definition.readonly ?? false\" :placeholder=\"definition.placeholder\" :step=\"definition.step\" :min=\"definition.min\" :max=\"definition.max\" :size=\"definition.size\">\n                </div>\n            </template>\n        </form-field>`\n});\n\n//# sourceURL=webpack:///./vue/src/forms/Text.js?");

/***/ }),

/***/ "./vue/src/forms/Textarea.js":
/*!***********************************!*\
  !*** ./vue/src/forms/Textarea.js ***!
  \***********************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _Field__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Field */ \"./vue/src/forms/Field.js\");\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  computed: {\n    inputClass() {\n      return 'input ' + Craft.orientation;\n    }\n\n  },\n  data: function () {\n    return {\n      realValue: {}\n    };\n  },\n  props: {\n    value: String,\n    definition: Object,\n    errors: Array,\n    name: String\n  },\n\n  created() {\n    this.realValue = this.value;\n  },\n\n  watch: {\n    realValue: function () {\n      this.$emit('change', this.realValue);\n    }\n  },\n  components: {\n    'form-field': _Field__WEBPACK_IMPORTED_MODULE_0__[\"default\"]\n  },\n  emits: ['change'],\n  template: `\n        <form-field :errors=\"errors\" :definition=\"definition\" :name=\"name\">\n            <template v-slot:main>\n                <div :class=\"inputClass\">\n                    <textarea :class=\"{text: true, fullwidth: !definition.cols}\" :type=\"definition.type ?? 'text'\" v-model=\"realValue\" :maxlength=\"definition.maxlength\" :autofocus=\"definition.autofocus ?? false\" :disabled=\"definition.disabled\" :readonly=\"definition.readonly ?? false\" :placeholder=\"definition.placeholder\" :cols=\"definition.cols ?? 50\" :rows=\"definition.rows ?? 2\"></textarea>\n                </div>\n            </template>\n        </form-field>`\n});\n\n//# sourceURL=webpack:///./vue/src/forms/Textarea.js?");

/***/ }),

/***/ "./vue/src/forms/Time.js":
/*!*******************************!*\
  !*** ./vue/src/forms/Time.js ***!
  \*******************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _Field__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Field */ \"./vue/src/forms/Field.js\");\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  computed: {\n    inputClass() {\n      return 'input ' + Craft.orientation;\n    }\n\n  },\n  data: function () {\n    return {\n      realValue: {}\n    };\n  },\n  props: {\n    value: String,\n    definition: Object,\n    errors: Array,\n    name: String\n  },\n  components: {\n    'form-field': _Field__WEBPACK_IMPORTED_MODULE_0__[\"default\"]\n  },\n\n  mounted() {\n    this.$nextTick(() => {\n      let options = {\n        minTime: this.definition.minTime ?? null,\n        maxTime: this.definition.maxTime ?? null,\n        disableTimeRanges: this.definition.disableTimeRanges ?? null,\n        step: this.definition.minuteIncrement ?? 5,\n        forceRoundTime: this.definition.forceRoundTime ?? false\n      };\n      options = { ...options,\n        ...Craft.timepickerOptions\n      };\n      let input = $(this.$el).find('input.text');\n      input.timepicker(options);\n      input.on('changeTime', () => {\n        this.$emit('change', input.val());\n      });\n    });\n  },\n\n  emits: ['change'],\n  template: `\n        <form-field :errors=\"errors\" :definition=\"definition\" :name=\"name\">\n            <template v-slot:main>\n                <div :class=\"inputClass\">\n                    <div class=\"timewrapper\">\n                        <input type=\"text\" class=\"text\" :value=\"value\" size=\"10\" autocomplete=\"off\" placeholder=\" \">\n                        <div data-icon=\"time\"></div>\n                    </div>\n                </div>\n            </template>\n        </form-field>`\n});\n\n//# sourceURL=webpack:///./vue/src/forms/Time.js?");

/***/ }),

/***/ "./vue/src/vue.js":
/*!************************!*\
  !*** ./vue/src/vue.js ***!
  \************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _forms_Lightswitch_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./forms/Lightswitch.js */ \"./vue/src/forms/Lightswitch.js\");\n/* harmony import */ var _forms_Select_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./forms/Select.js */ \"./vue/src/forms/Select.js\");\n/* harmony import */ var _forms_Text_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./forms/Text.js */ \"./vue/src/forms/Text.js\");\n/* harmony import */ var _forms_Date_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./forms/Date.js */ \"./vue/src/forms/Date.js\");\n/* harmony import */ var _forms_Time_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./forms/Time.js */ \"./vue/src/forms/Time.js\");\n/* harmony import */ var _forms_Color_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./forms/Color.js */ \"./vue/src/forms/Color.js\");\n/* harmony import */ var _forms_DateTime_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./forms/DateTime.js */ \"./vue/src/forms/DateTime.js\");\n/* harmony import */ var _forms_Textarea_js__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./forms/Textarea.js */ \"./vue/src/forms/Textarea.js\");\n/* harmony import */ var _forms_MultiSelect_js__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./forms/MultiSelect.js */ \"./vue/src/forms/MultiSelect.js\");\n/* harmony import */ var _forms_Checkboxes_js__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./forms/Checkboxes.js */ \"./vue/src/forms/Checkboxes.js\");\n/* harmony import */ var _forms_Radio_js__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./forms/Radio.js */ \"./vue/src/forms/Radio.js\");\n/* harmony import */ var _forms_FileDisplayers_js__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./forms/FileDisplayers.js */ \"./vue/src/forms/FileDisplayers.js\");\n/* harmony import */ var _forms_FetchViewMode_js__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./forms/FetchViewMode.js */ \"./vue/src/forms/FetchViewMode.js\");\n/* harmony import */ var _forms_Elements_js__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./forms/Elements.js */ \"./vue/src/forms/Elements.js\");\n/* harmony import */ var _forms_forms_scss__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ./forms/forms.scss */ \"./vue/src/forms/forms.scss\");\n/* harmony import */ var _forms_forms_scss__WEBPACK_IMPORTED_MODULE_14___default = /*#__PURE__*/__webpack_require__.n(_forms_forms_scss__WEBPACK_IMPORTED_MODULE_14__);\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\nwindow.CraftThemes = {\n  formFieldComponents: {\n    lightswitch: _forms_Lightswitch_js__WEBPACK_IMPORTED_MODULE_0__[\"default\"],\n    select: _forms_Select_js__WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n    text: _forms_Text_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"],\n    date: _forms_Date_js__WEBPACK_IMPORTED_MODULE_3__[\"default\"],\n    time: _forms_Time_js__WEBPACK_IMPORTED_MODULE_4__[\"default\"],\n    color: _forms_Color_js__WEBPACK_IMPORTED_MODULE_5__[\"default\"],\n    datetime: _forms_DateTime_js__WEBPACK_IMPORTED_MODULE_6__[\"default\"],\n    textarea: _forms_Textarea_js__WEBPACK_IMPORTED_MODULE_7__[\"default\"],\n    multiselect: _forms_MultiSelect_js__WEBPACK_IMPORTED_MODULE_8__[\"default\"],\n    checkboxes: _forms_Checkboxes_js__WEBPACK_IMPORTED_MODULE_9__[\"default\"],\n    radio: _forms_Radio_js__WEBPACK_IMPORTED_MODULE_10__[\"default\"],\n    filedisplayers: _forms_FileDisplayers_js__WEBPACK_IMPORTED_MODULE_11__[\"default\"],\n    fetchviewmode: _forms_FetchViewMode_js__WEBPACK_IMPORTED_MODULE_12__[\"default\"],\n    elements: _forms_Elements_js__WEBPACK_IMPORTED_MODULE_13__[\"default\"]\n  },\n  fieldComponents: {}\n};\n\n//# sourceURL=webpack:///./vue/src/vue.js?");

/***/ }),

/***/ "./node_modules/css-loader/dist/cjs.js??clonedRuleSet-24.use[1]!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-24.use[2]!./node_modules/sass-loader/dist/cjs.js??clonedRuleSet-24.use[3]!./vue/src/forms/forms.scss":
/*!************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader/dist/cjs.js??clonedRuleSet-24.use[1]!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-24.use[2]!./node_modules/sass-loader/dist/cjs.js??clonedRuleSet-24.use[3]!./vue/src/forms/forms.scss ***!
  \************************************************************************************************************************************************************************************************************************************/
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_css_loader_dist_runtime_noSourceMaps_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../../node_modules/css-loader/dist/runtime/noSourceMaps.js */ \"./node_modules/css-loader/dist/runtime/noSourceMaps.js\");\n/* harmony import */ var _node_modules_css_loader_dist_runtime_noSourceMaps_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_css_loader_dist_runtime_noSourceMaps_js__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../../node_modules/css-loader/dist/runtime/api.js */ \"./node_modules/css-loader/dist/runtime/api.js\");\n/* harmony import */ var _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_1__);\n// Imports\n\n\nvar ___CSS_LOADER_EXPORT___ = _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_1___default()((_node_modules_css_loader_dist_runtime_noSourceMaps_js__WEBPACK_IMPORTED_MODULE_0___default()));\n// Module\n___CSS_LOADER_EXPORT___.push([module.id, \".themes-modal-options {\\n  padding-bottom: 62px;\\n  min-width: 300px !important;\\n  min-height: 300px !important;\\n  height: 60vh !important;\\n  width: 30% !important;\\n}\\n.themes-modal-options .body {\\n  height: calc(100% - 65px);\\n  overflow-y: auto;\\n}\\n.themes-modal-options .field.select-elements .heading {\\n  display: flex;\\n  justify-content: space-between;\\n}\\n.themes-modal-options.displayer-asset-renderfile, .themes-modal-options.displayer-file-file {\\n  width: 50% !important;\\n  height: 80vh !important;\\n}\\n\\n#field-displayers {\\n  position: relative;\\n  height: calc(100% - 2px);\\n  border-radius: 3px;\\n  border: 1px solid rgba(96, 125, 159, 0.25);\\n  background-clip: padding-box;\\n  overflow: hidden;\\n}\\n#field-displayers:after {\\n  display: block;\\n  position: absolute;\\n  z-index: 1;\\n  top: 0;\\n  left: 0;\\n  width: 100%;\\n  height: 100%;\\n  visibility: visible;\\n  content: \\\"\\\";\\n  font-size: 0;\\n  border-radius: 3px;\\n  box-shadow: inset 0 1px 3px -1px #acbed2;\\n  -webkit-user-select: none;\\n     -moz-user-select: none;\\n      -ms-user-select: none;\\n          user-select: none;\\n  pointer-events: none;\\n}\\n\\n.displayers-settings {\\n  height: 100%;\\n  min-width: 300px;\\n  overflow-y: auto;\\n  padding-left: 200px;\\n  background: #fff;\\n  box-shadow: 0 0 0 1px rgba(31, 41, 51, 0.1), 0 2px 5px -2px rgba(31, 41, 51, 0.2);\\n}\\n.displayers-settings .settings-container {\\n  padding: 15px;\\n}\\n\\n.displayers-sidebar {\\n  position: absolute;\\n  background-color: #f3f7fc;\\n  left: 0;\\n  width: 205px;\\n  height: 100%;\\n  overflow-y: auto;\\n}\\n.displayers-sidebar .heading {\\n  padding: 7px 14px 6px;\\n  border-bottom: 1px solid rgba(51, 64, 77, 0.1);\\n  background-color: #f3f7fc;\\n  background-image: linear-gradient(rgba(51, 64, 77, 0), rgba(51, 64, 77, 0.05));\\n}\\n.displayers-sidebar .kind-item {\\n  display: flex;\\n  justify-content: space-between;\\n  padding: 8px 14px;\\n  border-bottom: solid #cdd8e4;\\n  border-width: 1px 0;\\n  background-color: #e4edf6;\\n}\\n.displayers-sidebar .kind-item:hover {\\n  text-decoration: none;\\n}\\n.displayers-sidebar .kind-item.sel {\\n  background-color: #cdd8e4;\\n}\\n.displayers-sidebar .kind-item:last-child {\\n  border-bottom: none;\\n}\\n.displayers-sidebar h4 {\\n  margin-bottom: 5px;\\n}\", \"\"]);\n// Exports\n/* harmony default export */ __webpack_exports__[\"default\"] = (___CSS_LOADER_EXPORT___);\n\n\n//# sourceURL=webpack:///./vue/src/forms/forms.scss?./node_modules/css-loader/dist/cjs.js??clonedRuleSet-24.use%5B1%5D!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-24.use%5B2%5D!./node_modules/sass-loader/dist/cjs.js??clonedRuleSet-24.use%5B3%5D");

/***/ }),

/***/ "./vue/src/forms/forms.scss":
/*!**********************************!*\
  !*** ./vue/src/forms/forms.scss ***!
  \**********************************/
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

eval("// style-loader: Adds some css to the DOM by adding a <style> tag\n\n// load the styles\nvar content = __webpack_require__(/*! !!../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-24.use[1]!../../../node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-24.use[2]!../../../node_modules/sass-loader/dist/cjs.js??clonedRuleSet-24.use[3]!./forms.scss */ \"./node_modules/css-loader/dist/cjs.js??clonedRuleSet-24.use[1]!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-24.use[2]!./node_modules/sass-loader/dist/cjs.js??clonedRuleSet-24.use[3]!./vue/src/forms/forms.scss\");\nif(content.__esModule) content = content.default;\nif(typeof content === 'string') content = [[module.id, content, '']];\nif(content.locals) module.exports = content.locals;\n// add the styles to the DOM\nvar add = (__webpack_require__(/*! !../../../node_modules/vue-style-loader/lib/addStylesClient.js */ \"./node_modules/vue-style-loader/lib/addStylesClient.js\")[\"default\"])\nvar update = add(\"e8a71bdc\", content, false, {\"sourceMap\":false,\"shadowMode\":false});\n// Hot Module Replacement\nif(false) {}\n\n//# sourceURL=webpack:///./vue/src/forms/forms.scss?");

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
/******/ 			id: moduleId,
/******/ 			loaded: false,
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Flag the module as loaded
/******/ 		module.loaded = true;
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = __webpack_modules__;
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/chunk loaded */
/******/ 	!function() {
/******/ 		var deferred = [];
/******/ 		__webpack_require__.O = function(result, chunkIds, fn, priority) {
/******/ 			if(chunkIds) {
/******/ 				priority = priority || 0;
/******/ 				for(var i = deferred.length; i > 0 && deferred[i - 1][2] > priority; i--) deferred[i] = deferred[i - 1];
/******/ 				deferred[i] = [chunkIds, fn, priority];
/******/ 				return;
/******/ 			}
/******/ 			var notFulfilled = Infinity;
/******/ 			for (var i = 0; i < deferred.length; i++) {
/******/ 				var chunkIds = deferred[i][0];
/******/ 				var fn = deferred[i][1];
/******/ 				var priority = deferred[i][2];
/******/ 				var fulfilled = true;
/******/ 				for (var j = 0; j < chunkIds.length; j++) {
/******/ 					if ((priority & 1 === 0 || notFulfilled >= priority) && Object.keys(__webpack_require__.O).every(function(key) { return __webpack_require__.O[key](chunkIds[j]); })) {
/******/ 						chunkIds.splice(j--, 1);
/******/ 					} else {
/******/ 						fulfilled = false;
/******/ 						if(priority < notFulfilled) notFulfilled = priority;
/******/ 					}
/******/ 				}
/******/ 				if(fulfilled) {
/******/ 					deferred.splice(i--, 1)
/******/ 					var r = fn();
/******/ 					if (r !== undefined) result = r;
/******/ 				}
/******/ 			}
/******/ 			return result;
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	!function() {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = function(module) {
/******/ 			var getter = module && module.__esModule ?
/******/ 				function() { return module['default']; } :
/******/ 				function() { return module; };
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	}();
/******/ 	
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
/******/ 	/* webpack/runtime/global */
/******/ 	!function() {
/******/ 		__webpack_require__.g = (function() {
/******/ 			if (typeof globalThis === 'object') return globalThis;
/******/ 			try {
/******/ 				return this || new Function('return this')();
/******/ 			} catch (e) {
/******/ 				if (typeof window === 'object') return window;
/******/ 			}
/******/ 		})();
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
/******/ 	/* webpack/runtime/node module decorator */
/******/ 	!function() {
/******/ 		__webpack_require__.nmd = function(module) {
/******/ 			module.paths = [];
/******/ 			if (!module.children) module.children = [];
/******/ 			return module;
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/jsonp chunk loading */
/******/ 	!function() {
/******/ 		// no baseURI
/******/ 		
/******/ 		// object to store loaded and loading chunks
/******/ 		// undefined = chunk not loaded, null = chunk preloaded/prefetched
/******/ 		// [resolve, reject, Promise] = chunk loading, 0 = chunk loaded
/******/ 		var installedChunks = {
/******/ 			"vue": 0
/******/ 		};
/******/ 		
/******/ 		// no chunk on demand loading
/******/ 		
/******/ 		// no prefetching
/******/ 		
/******/ 		// no preloaded
/******/ 		
/******/ 		// no HMR
/******/ 		
/******/ 		// no HMR manifest
/******/ 		
/******/ 		__webpack_require__.O.j = function(chunkId) { return installedChunks[chunkId] === 0; };
/******/ 		
/******/ 		// install a JSONP callback for chunk loading
/******/ 		var webpackJsonpCallback = function(parentChunkLoadingFunction, data) {
/******/ 			var chunkIds = data[0];
/******/ 			var moreModules = data[1];
/******/ 			var runtime = data[2];
/******/ 			// add "moreModules" to the modules object,
/******/ 			// then flag all "chunkIds" as loaded and fire callback
/******/ 			var moduleId, chunkId, i = 0;
/******/ 			if(chunkIds.some(function(id) { return installedChunks[id] !== 0; })) {
/******/ 				for(moduleId in moreModules) {
/******/ 					if(__webpack_require__.o(moreModules, moduleId)) {
/******/ 						__webpack_require__.m[moduleId] = moreModules[moduleId];
/******/ 					}
/******/ 				}
/******/ 				if(runtime) var result = runtime(__webpack_require__);
/******/ 			}
/******/ 			if(parentChunkLoadingFunction) parentChunkLoadingFunction(data);
/******/ 			for(;i < chunkIds.length; i++) {
/******/ 				chunkId = chunkIds[i];
/******/ 				if(__webpack_require__.o(installedChunks, chunkId) && installedChunks[chunkId]) {
/******/ 					installedChunks[chunkId][0]();
/******/ 				}
/******/ 				installedChunks[chunkId] = 0;
/******/ 			}
/******/ 			return __webpack_require__.O(result);
/******/ 		}
/******/ 		
/******/ 		var chunkLoadingGlobal = self["webpackChunk"] = self["webpackChunk"] || [];
/******/ 		chunkLoadingGlobal.forEach(webpackJsonpCallback.bind(null, 0));
/******/ 		chunkLoadingGlobal.push = webpackJsonpCallback.bind(null, chunkLoadingGlobal.push.bind(chunkLoadingGlobal));
/******/ 	}();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module depends on other loaded chunks and execution need to be delayed
/******/ 	var __webpack_exports__ = __webpack_require__.O(undefined, ["chunk-vendors"], function() { return __webpack_require__("./vue/src/vue.js"); })
/******/ 	__webpack_exports__ = __webpack_require__.O(__webpack_exports__);
/******/ 	
/******/ })()
;