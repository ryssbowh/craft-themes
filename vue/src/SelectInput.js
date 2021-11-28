/** global: Craft */

let SelectInput = Craft.BaseElementSelectInput.extend({

    theme: null,
    actionUrl: null,
    createElementCallback: null,
    initialIds: [],

    init(args) {
        this.base(args);
        if (this.initialIds.length) {
            Craft.postActionRequest(this.actionUrl, {theme: this.theme, id: this.initialIds}, (response) => {
                let elements = [];
                for (let data of response) {
                    elements.push(this.createElementCallback(data));
                }
                this.selectElements2(elements);
            });
        }
    },

    setSettings: function() {
        this.base.apply(this, arguments);
        this.theme = arguments[0].theme;
        this.actionUrl = arguments[0].actionUrl;
        this.createElementCallback = arguments[0].createElementCallback;
        this.initialIds = arguments[0].initialIds;
    },

    createNewElement: function (elementInfo) {
        let element = this.base(elementInfo);
        let $row = $('<div class="row" style="margin-bottom:5px;display:flex;justify-content:space-between;align-items:center">');
        $row.append(element);
        if (!elementInfo.viewModes) {
            this.fetchViewModes(elementInfo.id).done((response) => {
                this.appendViewModes($row, response[0].viewModes, elementInfo.viewMode);
            })
        } else {
            this.appendViewModes($row, elementInfo.viewModes, elementInfo.viewMode);
        }
        return $row;
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
        let select = $('<div class="select"><select></select></div>');
        viewModes.forEach((viewMode) => {
            select.find('select').append('<option value="' + viewMode.uid + '"'+(viewMode.uid == selectedViewMode ? ' selected' : '')+'>' + viewMode.name + '</options>');
        });
        select.find('select').on('change', () => {
            this.trigger('selectElements');
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
            this.resetElements();
        };
    },
});

export default SelectInput;