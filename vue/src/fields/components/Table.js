export default {
    props: {
        item: Object,
        display: Object,
        indentationLevel: Number
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
        <field :item="item" :indentation-level="indentationLevel" @updateItem="$emit('updateItem', $event)"></field>
        <div class="sub-fields">
            <component v-for="element, key in item.fields" :is="fieldComponent(element.type)" :item="element" :indentation-level="indentationLevel + 1" @updateItem="updateTableField(key, $event)"/>
        </div>
    </div>`
};