if (typeof Craft.Themes === typeof undefined) {
    Craft.Themes = {};
}

Craft.Themes.Blocks = Garnish.Base.extend({ 
	$blocks: null,
	$targetRegions: null,
	$regions: null,
	$saveBtn: null,
	isSaving: false,

	init: function () {
		this.$blocks = $('#theme-blocks .region-blocks .block, #theme-blocks .theme-sidebar .block');
		this.$targetRegions = $('#theme-blocks .region-blocks');
		this.$regions = $('.theme-regions .region');
		this.$saveBtn = $('#toolbar .submit');
		let _this = this;
		$.each(this.$targetRegions.find('.block'), function (block) {
			_this._initEvents($(block));
		});
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
        $('#theme-blocks .theme-sidebar h3').click(function(){
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

	_save: function (a) {
		let _this = this;
		this.isSaving = true;
		this.$saveBtn.attr('disabled', true).addClass('loading');
		let data = {regions: {}};
		$.each(this.$regions, function (index, region) {
			let regionName = $(region).data('handle');
			let blocks = [];
			$.each($(region).find('.block'), function (index, block) {
				blocks.push({
					blockHandle: $(block).data('handle'),
					id: $(block).data('id'),
					region: regionName,
					blockProvider: $(block).data('provider'),
					active: $(block).find('.lightswitch').hasClass('on'),
					order: index
				});
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
				$region.find('.block:nth-child('+(key + 1)+')').data('id', block.id);
			});
		});
	},

	_initEvents ($block) {
		let _this = this;
		$block.find('.delete').click(function () {
			_this.blockDragDrop.removeItems($block);
			$block.remove();
		});
		Craft.initUiElements($block);
		$block.find('.lightswitch').change(function () {
			$block.toggleClass('disabled');
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
				this._initEvents($draggee);
			}
            this.blockDragDrop.$activeDropTarget.removeClass(this.blockDragDrop.settings.activeDropTargetClass);
            this.blockDragDrop.$activeDropTarget.append($draggee);
            this.blockDragDrop.fadeOutHelpers();
            $draggee.css('visibility', 'visible');
        } else {
        	this.blockDragDrop.returnHelpersToDraggees();
        }
	},
});

$(function(){
	new Craft.Themes.Blocks();
});