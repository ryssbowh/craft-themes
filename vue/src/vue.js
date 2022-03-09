import Lightswitch from './forms/Lightswitch.js';
import Select from './forms/Select.js';
import Text from './forms/Text.js';
import DateField from './forms/Date.js';
import Time from './forms/Time.js';
import Color from './forms/Color.js';
import DateTime from './forms/DateTime.js';
import Textarea from './forms/Textarea.js';
import MultiSelect from './forms/MultiSelect.js';
import Checkboxes from './forms/Checkboxes.js';
import Radio from './forms/Radio.js';
import FileDisplayers from './forms/FileDisplayers.js';
import FetchViewMode from './forms/FetchViewMode.js';
import Elements from './forms/Elements.js';
import './forms/forms.scss';

window.CraftThemes = {
    formFieldComponents: {
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
    },
    fieldComponents: {}
};