import './main.scss';
import Matrix from './components/Matrix.js';
import Table from './components/Table.js';
import SuperTable from './components/SuperTable.js';
import { merge } from 'lodash';

window.CraftThemes.fieldComponents['matrix'] = {
    component : Matrix,
    clone: function (field, app) {
        let newField = merge({}, field);
        for (let i in field.types) {
            for (let j in field.types[i].fields) {
                newField.types[i].fields[j] = app.config.globalProperties.cloneField(field.types[i].fields[j]);
            }
        }
        return newField;
    }
}

window.CraftThemes.fieldComponents['table'] = {
    component: Table,
    clone: function (field, app) {
        let newField = merge({}, field);
        for (let i in field.fields) {
            newFields.fields[i] = app.config.globalProperties.cloneField(field.fields[i]);
        }
        return newField;
    }
}

window.CraftThemes.fieldComponents['super-table'] = {
    component : SuperTable,
    clone: function (field, app) {
        let newField = merge({}, field);
        for (let i in field.types) {
            for (let j in field.types[i].fields) {
                newField.types[i].fields[j] = app.config.globalProperties.cloneField(field.types[i].fields[j]);
            }
        }
        return newField;
    }
}