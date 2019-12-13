<?php
return [
    'adminEmail' => 'admin@example.com',

    'apiUrl' => 'api接口',
    'FileInput' => [
        'uploadUrl' => '/upload/index',
        'uploadExtraData' => [
            'uploadType' => 'image',
        ],
        'deleteUrl' => '/upload/delete',
        'deleteExtraData' => [
        ],
        'allowedFileTypes' => [
            'image'
        ],
        'allowedFileExtensions' => [
            'png', 'jpg', 'gif'     
        ], 
        'showBrowse' => false,
        'browseOnZoneClick' => true,
        'showPreview' => true,
        'initialPreviewAsData' => true,
        'dropZoneEnabled' => true,
        'showCaption' => false,
        'autoReplace' => true,
        'showUpload' => false,
        'showRemove' => false,     
    ],
];
