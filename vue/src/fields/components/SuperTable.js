export default {
    props: {
        item: Object,
        display: Object,
        identationLevel: Number
    },
    computed: {
        fields: function () {
            let keys = Object.keys(this.item.types);
            return this.item.types[keys[0]].fields ?? [];
        }
    },
    methods: {
        updateItem: function (fieldUid, data) {
            let keys = Object.keys(this.item.types);
            let type = this.item.types[keys[0]];
            let field;
            for (let i in type.fields) {
                field = type.fields[i];
                if (field.uid != fieldUid) {
                    continue;
                }
                for (let index in data) {
                    this.item.types[keys[0]].fields[i][index] = data[index];
                }
                break;
            }
        },
        sortableGroup: function () {
            return 'super-table-' + this.item.id;
        }
    },
    template: `
    <div class="line has-sub-fields bg-grey">
        <field :item="item" :identation-level="identationLevel" @updateItem="$emit('updateItem', $event)"></field>
        <draggable
            item-key="id"
            :list="fields"
            :group="sortableGroup()"
            handle=".move"
            class="sub-fields"
            >
            <template #item="{element}">
                <component :is="fieldComponent(element.type)" :item="element" :identation-level="identationLevel + 1" @updateItem="updateItem(element.uid, $event)"/>
            </template>
        </draggable>
    </div>`
};