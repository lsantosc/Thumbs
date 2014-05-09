<?PHP
App::import('Lib','Thumbs.ThumbsRoute');

$crop = array('plugin'=>'thumbs','controller'=>'thumbs','action'=>'crop');
$resize = array('plugin'=>'thumbs','controller'=>'thumbs','action'=>'resize');
$fill = array('plugin'=>'thumbs','controller'=>'thumbs','action'=>'fill');
$params = array('routeClass'=>'ThumbsRoute','size'=>'[a-zA-Z9-9-_]+','side'=>'(width|height)','color'=>'[a-zA-Z0-9-_]+');

Router::connect('/thumbs/crop/:size/*',$crop,$params);
Router::connect('/thumbs/resize/:size/:side/*',$resize,$params);
Router::connect('/thumbs/fill/:size/:color/*',$fill,$params);
