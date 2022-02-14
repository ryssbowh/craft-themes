if (Craft.Themes == null) {
    Craft.Themes = {};
}
/** global: Craft */
Craft.Themes.RulesTable = Craft.EditableTable.extend({
    init: function(id, baseName, columns, settings) {
        let _this = this;
        settings.onAddRow = function (tr) {
            _this.onAddRow(tr);
        }
        Craft.EditableTable.prototype.init.call(this, id, baseName, columns, settings);
        this.checkAllRows();
    },

    checkAllRows: function() {
        let _this = this;
        $.each(this.$tbody.find('tr'), function() {
            _this.onAddRow($(this));
        });
    },

    disableRow: function (tr, enabled) {
        if (enabled) {
            tr.removeClass('disabled')
        } else {
            tr.addClass('disabled');
        }
    },

    onAddRow: function (tr) {
        let enabled = tr.find('td.enabled .lightswitch');
        this.disableRow(tr, enabled.hasClass('on'));
        let _this = this;
        enabled.change(function () {
            _this.disableRow(tr, enabled.hasClass('on'));
        });
    }
});

function toggleSelectThemeField(show, selectClass) {
    if (show) {
        $(selectClass).slideDown();
    } else {
        $(selectClass).slideUp(); 
    }
}

$(function() { 
    $('.console-lightswitch').on('change', function(){
        toggleSelectThemeField($('.console-lightswitch').hasClass('on'), '.console-theme');
    });
    $('.cp-lightswitch').on('change', function(){
        toggleSelectThemeField($('.cp-lightswitch').hasClass('on'), '.cp-theme');
    });
});