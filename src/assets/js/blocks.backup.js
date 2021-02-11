if (typeof Craft.Themes === typeof undefined) {
    Craft.Themes = {};
}

Craft.Themes.Blocks = Garnish.Base.extend({ 
	$blocks: null,
	$targetRegions: null,
	$regions: null,
	$saveBtn: null,
	$settings: null,
	$blockForms: null,
	isSaving: false,
	activeBlock: null,
	blockCount: 0,

	init: function () {
		this.$blocks = $('#theme-blocks .region-blocks .block, #theme-blocks .theme-sidebar .block');
		this.$targetRegions = $('#theme-blocks .region-blocks');
		this.$regions = $('.theme-regions .region');
		this.$saveBtn = $('#toolbar .submit');
		this.$settings = $('#theme-blocks .theme-settings .settings');
		this.$blockForms = $('#theme-blocks .block-forms');
		this._createBlocks();
		let _this = this;
		this.blockDragDrop = new Garnish.DragDrop({
            dropTargets: this.$targetRegions,
            activeDropTargetClass: 'drop-active',
            helperOpacity: 0.75,
            helperBaseZindex: 800,
            onDragStop: $.proxy(this, '_onDragStop'),
            helper: $.proxy(function($file) {
				return this._getFileDragHelper($file);
			}, this),
        });
        this.blockDragDrop.addItems(this.$blocks);
        $('#theme-blocks h5.slide').click(function(){
        	$(this).next().slideToggle('fast');
        	$(this).toggleClass('closed');
        });
        this.$saveBtn.click(function(e){
        	e.preventDefault();
        	if (!_this.isSaving) {
        		_this._save($(this));
        	}
        })
	},

	_createBlocks: function () {
		let _this = this;
		$.each(this.$targetRegions, function (i, region) {
			$.each($(region).find('.block'), function (index, block) {
				_this._createBlock($(block), $(region));
			});
		});
	},

	_createForms: function () {
		let _this = this;
		let forms = $('<div class="settings-forms hidden">');
		let ignore = this.$blockForms.find('.ignore-form').clone().data('id', this.blockCount);
		$.each(ignore.find('input'), function (i, input) {
			let id = 'checkbox-' + _this.blockCount + '-' + i;
			$(input).attr('id', id);
			$(input).next().attr('for', id);
		});
		forms.append(ignore);
		forms.appendTo(this.$settings);
		return forms;
	},

	_createBlock: function ($block, $region) {
		let _this = this;
		let $forms = _this._createForms();
		$block.click(function () {
			if (_this.activeBlock) {
				_this.activeBlock.setUnactive();
			}
			_this.activeBlock = $block.data('block');
			_this.activeBlock.setActive();
		});
		new Craft.Themes.Block(this.blockCount, $block, $region, $forms, {
			beforeRemove: function (block) {
				_this.blockDragDrop.removeItems(block.$element);
				_this.blockCount--;
			}
		});
		this.blockCount++;
	},

	_save: function (a) {
		let _this = this;
		this.isSaving = true;
		this.$saveBtn.attr('disabled', true).addClass('loading');
		let data = {regions: {}};
		$.each(this.$regions, function (index, region) {
			let regionName = $(region).data('handle');
			let blocks = [];
			$.each($(region).find('.block'), function (index, block) {
				let data = $(block).data('block').getData();
				data.order = index;
				blocks.push(data);
			});
			data.regions[regionName] = blocks;
		});
		Craft.sendActionRequest('POST', a.attr('href'), {data: data})
			.then(function (response) {
				Craft.cp.displayNotice(response.data.message);
				_this._populateBlockIds(response.data.layout);
			}).catch(function (error) {
				Craft.cp.displayError(error);
			}).finally(function(data){
				_this.isSaving = false;
				_this.$saveBtn.attr('disabled', false).removeClass('loading');
			});
	},

	_populateBlockIds: function (layout) {
		let _this = this;
		Object.keys(layout.regions).forEach(function (region) {
			let $region = _this.$regions.filter('[data-handle='+region+']');
			layout.regions[region].forEach(function (block, key) {
				$region.find('.block:nth-child('+(key + 1)+')').data('block').setId(block.id);
			});
		});
	},

	_getFileDragHelper: function($element) {
        let $outerContainer = $('<div class="theme-block-drap-helper"/>').appendTo(Garnish.$bod);

        $element.clone().appendTo($outerContainer);

        return $outerContainer;
	},

	_onDragStop: function($element) {
		if (this.blockDragDrop.$dropTargets && this.blockDragDrop.$activeDropTarget) {
			let $draggee = this.blockDragDrop.$draggee;
			if ($draggee.hasClass('original')) {
				$draggee = $draggee.clone().removeClass('original');
				this.blockDragDrop.addItems($draggee);
				this._createBlock($draggee, this.blockDragDrop.$activeDropTarget.closest('.region'));
			}
            this.blockDragDrop.$activeDropTarget.removeClass(this.blockDragDrop.settings.activeDropTargetClass);
            this.blockDragDrop.$activeDropTarget.append($draggee);
            this.blockDragDrop.fadeOutHelpers();
            $draggee.click();
            $draggee.css('visibility', 'visible');
        } else {
        	this.blockDragDrop.returnHelpersToDraggees();
        }
	}
});

Craft.Themes.Block = Garnish.Base.extend({
	$element: null,
	$region: null,
	$forms: null,
	index: null,
	settings: null,
	id: null,

	init: function (index, $element, $region, $forms, settings) {
		this.$element = $element;
		this.$region = $region;
		this.$forms = $forms;
		this.index = index;
		this.settings = settings;
		this.id = $element.data('id');
		this.$element.data('block', this);
		this._initEvents();
	},

	_initEvents: function () {
		let _this = this;
		this.$element.find('.delete').click(function () {
			_this.beforeRemove();
			_this.$element.remove();
		});
		Craft.initUiElements(this.$element);
		this.$element.find('.lightswitch').change(function () {
			_this.$element.toggleClass('disabled');
		});
	},

	setActive: function () {
		this.$element.addClass('active');
		this.$forms.removeClass('hidden');
	},

	setUnactive: function () {
		this.$element.removeClass('active');
		this.$forms.addClass('hidden');
	},

	beforeRemove: function () {
		if (this.settings.beforeRemove) {
			this.settings.beforeRemove(this);
		}
	},

	setId: function (id) {
		this.id = id;
	},

	getData: function () {
		return {
			blockHandle: this.$element.data('handle'),
			id: this.id,
			blockProvider: this.$element.data('provider'),
			active: this.$element.find('.lightswitch').hasClass('on'),
			region: this.$region.data('handle'),
			ignore: this.$forms.find('.ignore-form').serializeJSON()
		}
	}
});

$(function(){
	new Craft.Themes.Blocks();
});