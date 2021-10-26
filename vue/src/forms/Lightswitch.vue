<template>
    <div class="input ltr">
        <button type="button" :class="{lightswitch: true, on: on}">
            <div class="lightswitch-container">
                <div class="handle"></div>
            </div>
            <input type="hidden" :value="on ? 1 : ''">
        </button>
    </div>
</template>

<script>
export default {
    props: {
        on: Boolean
    },
    mounted () {
        this.$nextTick(() => {
            Craft.initUiElements(this.$el);
            $(this.$el).find('.lightswitch').on('change', (e) => {
                this.$emit('change', $(e.target).hasClass('on'));
            });
        });
    },
    emits: ['change']
};
</script>
