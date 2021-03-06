export default Garnish.Modal.extend({
    updateSizeAndPosition: function() {
        if (!this.$container) {
            return;
        }

        this.$container.css({
            'min-width': '200px',
            'min-height': '200px',
            'transform': 'translate(-50%, -50%)',
            'width': 'auto',
            'max-width': Garnish.$win.width() - this.settings.minGutter * 2,
            'left': '50%',
            'height': 'auto',
            'max-height': Garnish.$win.height() - this.settings.minGutter * 2,
            'top': '50%'
        });

        this.trigger('updateSizeAndPosition');
    },
});