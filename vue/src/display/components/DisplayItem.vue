<template>
    <component :is="type" :display="display" :item="display.item" @updateItem="updateItem({displayUid: display.uid, data: $event})"/>
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
        ...mapMutations(['updateItem', 'removeDisplay']),
        ...mapActions([])
    },
    emits: []
};
</script>