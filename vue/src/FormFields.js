import Lightswitch from './forms/Lightswitch.vue';
import Select from './forms/Select.vue';
import Text from './forms/Text.vue';
import DateField from './forms/Date.vue';
import Time from './forms/Time.vue';
import Color from './forms/Color.vue';
import DateTime from './forms/DateTime.vue';
import Textarea from './forms/Textarea.vue';
import MultiSelect from './forms/MultiSelect.vue';
import Checkboxes from './forms/Checkboxes.vue';
import Radio from './forms/Radio.vue';
import FileDisplayers from './forms/FileDisplayers.vue';
import FetchViewMode from './forms/FetchViewMode.vue';
import Elements from './forms/Elements.vue';

let fields = {
    lightswitch: Lightswitch,
    select: Select,
    text: Text,
    date: DateField,
    time: Time,
    color: Color,
    datetime: DateTime,
    textarea: Textarea,
    multiselect: MultiSelect,
    checkboxes: Checkboxes,
    radio: Radio,
    filedisplayers: FileDisplayers,
    fetchviewmode: FetchViewMode,
    elements: Elements
};

let event = new CustomEvent("register-form-fields-components", {detail: {}});
document.dispatchEvent(event);

for (let name in event.detail) {
    fields[name] = event.detail[name];
}

export default fields;