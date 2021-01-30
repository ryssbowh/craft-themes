if (typeof Craft.Themes === typeof undefined) {
    Craft.Themes = {};
}
/** global: Craft */
Craft.Themes.SettingsTable = Craft.EditableTable.extend({
	init: function(id, baseName, columns, settings) {
		let _this = this;
		settings.onAddRow = function (tr) {
			_this.onAddRow(tr);
		}
        Craft.EditableTable.prototype.init.call(this, id, baseName, columns, settings);
        this.checkAllRows();
        this.displayErrors();
    },

    displayErrors: function () {
    	let _this = this;
    	this.settings.errors.forEach(function (value, index) {
    		let row, column;
    		[row, column] = value.split(':').map(str => parseInt(str));
    		_this.$tbody.find('tr:nth-child('+(row+1)+') td:nth-child('+(column+1)+')').addClass('error');
    	});

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

    checkRow: function (tr) {
    	let type = tr.find('td.type select').val();
    	let url = tr.find('td.url textarea');
    	let site = tr.find('td.site select');
    	let language = tr.find('td.language select');
    	url.closest('td').removeClass('error');
    	if (type == 'site') {
    		site.attr('disabled', false).removeClass('disabled');
    		[language, url].map(item => item.attr('disabled', 'disabled').addClass('disabled'));
    	} else if (type == 'url') {
    		
    		url.attr('disabled', false).removeClass('disabled');
    		[language, site].map(item => item.attr('disabled', 'disabled').addClass('disabled'));
    	} else {
    		language.attr('disabled', false).removeClass('disabled');
    		[site, url].map(item => item.attr('disabled', 'disabled').addClass('disabled'));
    	}
    },

    onAddRow: function (tr) {
    	let enabled = tr.find('td.enabled .lightswitch');
    	let url = tr.find('td.url textarea');
    	this.checkRow(tr);
    	this.disableRow(tr, enabled.hasClass('on'));
    	let _this = this;
    	tr.find('td.type select').change(function () {
    		_this.checkRow($(this).closest('tr'));
    	});
    	enabled.change(function () {
    		_this.disableRow(tr, enabled.hasClass('on'));
    	});
    	url.keyup(function () {
    		if ($(this).val().trim() == '') {
    			$(this).closest('td').addClass('error');
    		} else {
    			$(this).closest('td').removeClass('error');
    		}
    	});
    }
});