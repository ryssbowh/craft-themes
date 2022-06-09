import { v4 as uuidv4 } from 'uuid';
import { merge } from 'lodash';

const Translate = {
  install(app) {
    app.config.globalProperties.t = (str, params, category = 'themes') => {
      return window.Craft.t(category, str, params);
    }
  },
};

const HandleError = {
  install(app) {
    app.config.globalProperties.handleError = (err) => {
      let message = err;
      if (err.response) {
        if (err.response.data.message ?? null) {
          message = err.response.data.message;
        } else if (err.response.data.error ?? null) {
          message = err.response.data.error;
        }
      }
      Craft.cp.displayError(message);
    }
  }
};

const Clone = {
  install(app) {
    app.config.globalProperties.cloneDisplay = (display) => {
      let newDisplay = merge({}, display);
      let newItem;
      if (display.type == 'group') {
          newItem = app.config.globalProperties.cloneGroup(display.item);
      } else {
          newItem = app.config.globalProperties.cloneField(display.item);
      }
      newDisplay.item = newItem;
      newDisplay.id = null;
      newDisplay.uid = uuidv4();
      return newDisplay;
    };
    app.config.globalProperties.cloneField = (field) => {
      let newField;
      if (typeof window.CraftThemes.fieldComponents[field.type] != 'undefined') {
        newField = window.CraftThemes.fieldComponents[field.type].clone(field, app);
      } else {
        newField = merge({}, field);
      }
      newField.id = null;
      newField.uid = uuidv4();
      return newField;
    };
    app.config.globalProperties.cloneGroup = (group) => {
      let newGroup = merge({}, group);
      let displays = [];
      for (let i in group.displays) {
          displays.push(app.config.globalProperties.cloneDisplay(group.displays[i]));
      }
      newGroup.displays = displays;
      newGroup.id = null;
      newGroup.uid = uuidv4();
      return newGroup;
    }
  },
};

const FieldComponent = {
  install(app) {
    app.config.globalProperties.fieldComponent = (type) => {
      if (typeof window.CraftThemes.fieldComponents[type] != 'undefined') {
        return 'field-' + type;
      }
      return 'field';
    }
  },
};

const HandleGenerator = Craft.HandleGenerator.extend({
  callback: null,

  updateTarget: function() {
    if (!this.$target.is(':visible')) {
        return;
    }

    var sourceVal = this.$source.val();

    if (typeof sourceVal === 'undefined') {
        // The source input may not exist anymore
        return;
    }

    var targetVal = this.generateTargetValue(sourceVal);

    this.$target.val(targetVal);
    this.$target.trigger('change');
    this.$target.trigger('input');

    // If the target already has focus, select its whole value to mimic
    // the behavior if the value had already been generated and they just tabbed in
    if (this.$target.is(':focus')) {
        Craft.selectFullValue(this.$target);
    }

    if (this.callback) {
        this.callback(targetVal);
    }
  },
});

const SelectInput = Craft.BaseElementSelectInput.extend({

  theme: null,
  actionUrl: null,
  createElementCallback: null,
  initialIds: [],
  errors: {},

  init(args) {
    this.base(args);
    this.$elementsContainer.html('');
    if (this.initialIds.length) {
      Craft.postActionRequest(this.actionUrl, {theme: this.theme, id: this.initialIds}, (response) => {
        let elements = [];
        for (let data of response) {
          elements.push(this.createElementCallback(data));
        }
        this.selectElements2(elements);
        this.updateErrors();
      });
    }
  },

  setSettings: function() {
    this.base.apply(this, arguments);
    this.theme = arguments[0].theme;
    this.actionUrl = arguments[0].actionUrl;
    this.createElementCallback = arguments[0].createElementCallback;
    this.initialIds = arguments[0].initialIds;
    this.errors = arguments[0].errors;
  },

  createNewElement: function (elementInfo) {
    let element = this.base(elementInfo);
    let $row = $('<div class="row" style="margin-bottom:5px;display:flex;justify-content:space-between;align-items:center">');
    $row.append(element);
    if (!elementInfo.viewModes) {
      this.fetchViewModes(elementInfo.id).done((response) => {
        this.appendViewModes($row, response[0].viewModes, elementInfo.viewMode ?? response[0].viewModes[0].uid);
        this.trigger('viewModesChanged');
      })
    } else {
      this.appendViewModes($row, elementInfo.viewModes, elementInfo.viewMode);
    }
    return $row;
  },

  updateErrors: function () {
    this.$container.find('.element-error').remove();
    for (let id in this.errors) {
      let $elem = this.$elements.find('.element[data-id=' + id + ']').parent();
      let $error = $('<div class="error element-error">' + this.errors[id] + '</div>');
      $elem.find('.select-wrapper').append($error);
    }
  },

  /**
   * Same method but without the animation
   */
  selectElements2: function(elements) {
    let ids = [];
    for (let i = 0; i < elements.length; i++) {
      let elementInfo = elements[i];
      if (ids.includes(elementInfo.id)) {
        continue;
      }
      ids.push(elementInfo.id);
      let $element = this.createNewElement(elementInfo);

      this.appendElement($element);
      this.addElements($element);

      // Override the element reference with the new one
      elementInfo.$element = $element;
    }

    this.onSelectElements(elements);
  },

  fetchViewModes: function (id) {
    let data = {
      theme: this.theme,
      id: id
    };
    return Craft.postActionRequest(this.actionUrl, data);
  },

  appendViewModes: function ($row, viewModes, selectedViewMode) {
    let select = $('<div class="select-wrapper"><div class="select"><select><option value="">' + Craft.t('themes', 'Select a view mode') + '</select></select></div></div>');
    viewModes.forEach((viewMode) => {
      select.find('select').append('<option value="' + viewMode.uid + '"'+(viewMode.uid == selectedViewMode ? ' selected' : '')+'>' + viewMode.name + '</options>');
    });
    select.find('select').on('change', () => {
      this.trigger('viewModesChanged');
    });
    $row.append(select);
  },

  getSelectedElementIds: function() {
    var ids = [];
    for (var i = 0; i < this.$elements.length; i++) {
      ids.push(this.$elements.eq(i).find('.element').data('id'));
    }
    return ids;
  },

  getSelectedElementData: function () {
    let data = [];
    for (var i = 0; i < this.$elements.length; i++) {
      let elem = this.$elements.eq(i);
      data.push({
          id: elem.find('.element').data('id'),
          viewMode: elem.find('select').val()
      });
    }
    return data;
  },

  removeElements: function($elements) {
    if (this.settings.selectable) {
      this.elementSelect.removeItems($elements);
    }

    if (this.modal) {
      var ids = [];

      for (var i = 0; i < $elements.length; i++) {
        var id = $elements.find('.element').eq(i).data('id');

        if (id) {
            ids.push(id);
        }
      }

      if (ids.length) {
        this.modal.elementIndex.enableElementsById(ids);
      }
    }

    // Disable the hidden input in case the form is submitted before this element gets removed from the DOM
    $elements.children('input').prop('disabled', true);

    this.$elements = this.$elements.not($elements);
    this.updateAddElementsBtn();

    this.onRemoveElements();
  },

  removeElement: function($element) {
    this.removeElements($element.parent());
    this.animateElementAway($element.parent(), () => {
      $element.parent().remove();
    });
  },

  initElementSort: function() {
    //Ignoring the select as handle when sorting elements :
    this.base();
    this.elementSort.settings.ignoreHandleSelector = '.delete, .select';
    this.elementSort.settings.onSortChange = () => {
      this.trigger('orderChanged');
    };
  }
});

export { Translate, HandleError, Clone, FieldComponent, HandleGenerator, SelectInput };