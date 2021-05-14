<template>
    <div>
        <field :item="item" @updateItem="$emit('updateItem', $event)"></field>
        <div class="matrix-type" v-for="type, index in item.types" v-bind:key="index">
            <div class="matrix-type-name">{{ t('Type {type}', {type: type.type.name}) }}</div>
            <draggable
                item-key="id"
                :list="type.fields"
                group="'matrix-'+type.type_id"
                handle=".move"
                >
                <template #item="{element}">
                    <field :item="element" @updateItem="$emit('updateMatrixItem', {fieldId: element.id, typeId: type.type_id, data: $event})"></field>
                </template>
            </draggable>
        </div>
    </div>
</template>

<script>
import { mapMutations, mapState, mapActions } from 'vuex';
import Field from './Field';
import Draggable from 'vuedraggable';

export default {
    computed: {
        ...mapState([])
    },
    props: {
        item: Object
    },
    data() {
        return {};
    },
    methods: {
        ...mapMutations([]),
        ...mapActions([]),
    },
    components: {Field, Draggable},
    emits: ['updateItem'],
};
</script>
<style lang="scss">
.matrix-type {
    position: relative;
    &:after {
        content: '';
        width: 1px;
        height: calc(100% - 34px);
        background: rgba(96, 125, 159, 0.25);
        display: inline-block;
        left: 15px;
        top: 27px;
        position: absolute;
    }
    .matrix-type-name {
        padding-left: 15px;
    }
    .col.move {
        padding-left: 25px;
    }
}
</style>