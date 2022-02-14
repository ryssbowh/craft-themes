/** global: Craft */
/**
 * Handle Generator
 */
let HandleGenerator = Craft.HandleGenerator.extend(
    {
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

export default HandleGenerator;