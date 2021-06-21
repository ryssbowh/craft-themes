<template>
    <component :is="type" :item="display.item" @updateItem="updateItem" />
</template>

<script>
import { mapMutations, mapState, mapActions } from 'vuex';

export default {
    computed: {
        type: function () {
            if (this.display.type == 'group') {
                return 'group';
            }
            if (Object.keys(window.themesFields).includes(this.display.item.type)) {
                return 'field-' + this.display.item.type;
            }
            return 'field';
        },
        ...mapState([])
    },
    props: {
        display: Object
    },
    data() {
        return {
        }
    },
    methods: {
        updateItem: function (data) {
            this.updateDisplay({id: this.display.id, data:{item: data}});
        },
        ...mapMutations(['updateDisplay']),
        ...mapActions([])
    },
    emits: []
};
</script>