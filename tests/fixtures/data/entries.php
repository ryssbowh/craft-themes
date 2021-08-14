<?php
return [
    [
        // Standard `craft\elements\Entry` fields.
        'authorId' => 1,
        'sectionId' => $this->sectionIds['myChannel'],
        'typeId' => $this->typeIds['myChannel']['myChannel'],
        'title' => 'My Entry',

        // Set a field layout
        'fieldLayoutType' => 'mylayout'
    ]
];
