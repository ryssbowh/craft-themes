<template>
    <component
        :is="type"
        :display="display"
        :item="display.item"
        :identation-level="identationLevel"
        @updateItem="updateItem({displayUid: display.uid, data: $event})"
    />
</template>

<script>
import { mapMutations } from 'vuex';

export default {
    props: {
        display: {
            type: Object,
            default: null
        },
        identationLevel: {
            type: Number,
            default: null
        }
    },
    computed: {
        type: function () {
            if (this.display.type == 'group') {
                return 'group';
            }
            return this.fieldComponent(this.display.item.type);
        }
    },
    methods: {
        ...mapMutations(['updateItem'])
    }
};
</script>