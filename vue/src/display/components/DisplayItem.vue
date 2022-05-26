<template>
    <component :is="type" :display="display" :item="display.item" :indentation-level="indentationLevel" @updateItem="updateItem({displayUid: display.uid, data: $event})"/>
</template>

<script>
import { mapMutations } from 'vuex';

export default {
    computed: {
        type: function () {
            if (this.display.type == 'group') {
                return 'group';
            }
            return this.fieldComponent(this.display.item.type);
        }
    },
    props: {
        display: Object,
        indentationLevel: Number
    },
    methods: {
        ...mapMutations(['updateItem'])
    },
    emits: []
};
</script>