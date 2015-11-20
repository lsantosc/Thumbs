<?PHP
return array(
    'crop'=>array(
        'tiny'=>array(60,60),
        'small'=>array(100,100),
        'medium'=>array(200,200),
        'large'=>array(1000,1000),
    ),
    'resize'=>array(
        'tiny'=>100,
        'small'=>300,
        'medium'=>600,
        'large'=>1000,
    ),
    'rotate'=>array(
        '90' => 90,
        '180' => 180,
        '270' => 270
    ),
    'fill'=>array(
        'sizes'=>array(
            'tiny'=>array(60,60),
            'small'=>array(100,100),
            'medium'=>array(200,200),
            'large'=>array(1000,1000),
        ),
        'colors'=>array(
            'transparent'=>'FFFFFF/0', //Only for PNG (JPG will have white background)
            'black'=>'000000',
            'white'=>'FFFFFF',
            'red'=>'FF0000',
            'green'=>'00FF00',
            'blue'=>'0000FF',
        ),
    ),
);