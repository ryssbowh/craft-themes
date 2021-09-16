<template>
    <component :is="type" :item="display.item" :display="display" @updateItem="updateItem"/>
</template>

<script>
import { mapMutations, mapState, mapActions } from 'vuex';

export default {
    computed: {
        type: function () {
            if (this.display.type == 'group') {
                return 'group';
            }
            if (this.fieldComponents().includes(this.display.item.type)) {
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
            this.updateDisplay({uid: this.display.uid, data:{item: data}});
        },
        ...mapMutations(['updateDisplay', 'removeDisplay']),
        ...mapActions([])
    },
    emits: []
};
</script>