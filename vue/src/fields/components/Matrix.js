export default {
    props: {
        item: Object,
        display: Object,
        identationLevel: Number
    },
    methods: {
        updateMatrixItem: function (fieldUid, typeId, data) {
            outerLoop:
            for (let i in this.item.types) {
                let type = this.item.types[i];
                if (type.type.id != typeId) {
                    continue;
                }
                for (let j in type.fields) {
                    let field = type.fields[j];
                    if (field.uid != fieldUid) {
                        continue;
                    }
                    for (let index in data) {
                        this.item.types[i].fields[j][index] = data[index];
                    }
                    break outerLoop;
                }
            }
        },
        sortableGroup: function (type) {
            return 'matrix-' + type.type_id;
        }
    },
    template: `
        <div class="line has-sub-fields bg-grey">
            <field :identation-level="identationLevel" :item="item" @updateItem="$emit('updateItem', $event)"></field>
            <div class="matrix-type sub-fields" v-for="type, index in item.types" v-bind:key="index">
                <div class="matrix-type-name"><i>{{ t('Type {type}', {type: type.type.name}) }}</i></div>
                <draggable
                    item-key="id"
                    :list="type.fields"
                    :group="sortableGroup(type)"
                    handle=".move"
                    >
                    <template #item="{element}">
                        <component :is="fieldComponent(element.type)" :item="element" :identation-level="identationLevel + 1" @updateItem="updateMatrixItem(element.uid, type.type_id, $event)"/>
                    </template>
                </draggable>
            </div>
        </div>`
};