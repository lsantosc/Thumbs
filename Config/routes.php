<?PHP
App::import('Lib','Thumbs.CropRoute');
App::import('Lib','Thumbs.ResizeRoute');
App::import('Lib','Thumbs.FillRoute');

//CROP ROUTING
Router::connect('/thumbs/crop/:size/*',array(
    'plugin'=>'thumbs',
    'controller'=>'thumbs',
    'action'=>'crop'
),array(
    'routeClass'=>'CropRoute',
    'size'=>'[a-zA-Z0-9-_]+',
));

//RESIZE ROUTING
Router::connect('/thumbs/resize/:size/:side/*',array(
    'plugin'=>'thumbs',
    'controller'=>'thumbs',
    'action'=>'resize',
),array(
    'routeClass'=>'ResizeRoute',
    'side'=>'(width|height)',
    'size'=>'[a-zA-Z0-9-_]+',
));

//FILL ROUTING
Router::connect('/thumbs/fill/:size/:fill/*',array(
    'plugin'=>'thumbs',
    'controller'=>'thumbs',
    'action'=>'fill',
),array(
    'routeClass'=>'FillRoute',
    'fill'=>'[a-zA-Z0-9-_]+',
    'size'=>'[a-zA-Z0-9]+',
));