export default {
    props: {
        item: Object,
        display: Object,
        indentationLevel: Number,
        classes: {
            type: String,
            default: () => ''
        },
    },
    methods: {
        updateNeoItem: function (fieldUid, typeId, data) {
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
            return 'neo-' + type.type_id;
        }
    },
    template: `
        <div :class="classes + ' line has-sub-fields bg-grey'">
            <field :indentation-level="indentationLevel" :classes="'no-margin'" :item="item" @updateItem="$emit('updateItem', $event)"></field>
            <div class="sub-fields" v-for="type, index in item.types" v-bind:key="index">
                <div :class="'line no-margin no-padding flex indented-' + (indentationLevel + 1)">
                    <div class="block-type-name">
                        <div class="indented"><i>{{ t('Type {type}', {type: type.type.name}) }}</i></div>
                    </div>
                </div>
                <draggable
                    item-key="id"
                    :list="type.fields"
                    :group="sortableGroup(type)"
                    handle=".move"
                    >
                    <template #item="{element}">
                        <component :is="fieldComponent(element.type)" :item="element" :classes="'no-padding'" :indentation-level="indentationLevel + 1" @updateItem="updateNeoItem(element.uid, type.type_id, $event)"/>
                    </template>
                </draggable>
            </div>
        </div>`
};