export default {
    props: {
        item: Object,
        display: Object,
        identationLevel: Number
    },
    methods: {
        updateTableField: function (key, data) {
            for (let index in data) {
                this.item.fields[key][index] = data[index];
            }
        }
    },
    template: `
    <div class="line has-sub-fields bg-grey">
        <field :item="item" :identation-level="identationLevel" @updateItem="$emit('updateItem', $event)"></field>
        <div class="sub-fields">
            <component v-for="element, key in item.fields" :is="fieldComponent(element.type)" :item="element" :identation-level="identationLevel + 1" @updateItem="updateTableField(key, $event)"/>
        </div>
    </div>`
};