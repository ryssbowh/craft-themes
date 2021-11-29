/** global: Craft */

let SelectInput = Craft.BaseElementSelectInput.extend({

    theme: null,
    actionUrl: null,
    createElementCallback: null,
    initialIds: [],
    errors: {},

    init(args) {
        this.base(args);
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
        for (let i = 0; i < elements.length; i++) {
            let elementInfo = elements[i],
                $element = this.createNewElement(elementInfo);

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
    },
});

export default SelectInput;