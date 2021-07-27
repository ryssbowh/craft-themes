<template>
    <component :is="type" :item="display.item" :display="display" @updateItem="updateItem" @delete="deleteDisplay" />
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
            this.updateDisplay({id: this.display.id, data:{item: data}});
        },
        deleteDisplay: function () {
            this.removeDisplay(this.display.id);
        },
        ...mapMutations(['updateDisplay', 'removeDisplay']),
        ...mapActions([])
    },
    emits: []
};
</script>