Thumbs
======

Thumbs plugin for CakePHP 2.x<br>
This plugin allows you to create thumbs from any image on your website, it also creates a cache for each generated thumb. It uses Gd library
or Imagick library (Automatically selected) for thumbs creation. So one of them you will need to have installed.

#Getting started
First fork this plugin into your CakePHP 2.x project, then load this plugin into your bootstrap.php file, you will need to load it it´s router (required).

``` PHP
CakePlugin::load('Thumb',array('routes'=>true));
```

Then create the file thumbs.php in your app/Config directory, for example:

``` PHP
return array(
    'crop'=>array(
        'tiny'=>array(60,60), //Creates a crop thumb 60x60px
        'small'=>array(100,100),
    ),
    'resize'=>array(
        'tiny'=>100, //Creates a resized imagem with 100px of maximum width or 100px of maximum height, defined in url
        'small'=>300,
    ),
    'fill'=>array(
        'sizes'=>array(
            'tiny'=>array(60,60), //Creates a resized image inside a box with 60x60 pixels and defined background by colors below
            'small'=>array(100,100),
            'medium'=>array(200,200),
        ),
        'colors'=>array( //allowed color for fill method
            'red'=>'FF0000', //Red color
            'white'=>'FFFFFF',
            'alpha_red'=>'FF0000/50', //Background will have 50% of alpha, can set to /0 for fully transparent. (PNG only, jpg will have red background without transparency)
        ),
    ),
);
```

These will be the availiable sizes for each thumb method, the methods availiables are crop, resize and fill. Also the availiable colors for fill method.

1. Crop method will genereate a thumb cropping from the center of the image and the result thumbnail will be an image with exactly size you define<br>
URL example: <strong>http://www.mysite.com/thumbs/crop/small/img/logotype.png</strong> generates a 100x100 croped thumb
2. Resize method will resize proportional width and height of the image. The result will be an image wich it´s size will be the maximum defined width or maximum define height of the size, and it´s size will be proportional of the original image.<br>
URL example (width): <strong>http://www.mysite.com/thumbs/resize/small/width/img/logotype.png</strong> creates a resized image with maximum width of 100px<br>
URL example (height): <strong>http://www.mysite.com/thumbs/resize/small/height/img/logotype.png</strong> creates a resized image with maximum height of 100px.
3. Fill method will output a proportional resized image to the original but will fill the rest of the image with a defined collor to create a thumbnail with exactly user defined sizes but with proportional resized imagem surounded by the fill color.<br>
URL example: <strong>http://www.mysite.com/thumbs/fill/small/red/img/logotype.png</strong> will create a resized image inside a red box with 100x100 pixels of size.
 
#how to use?
Now you´ve defined the allowed sizes you can generate your thumbs by using URL´s
For example if you have the image at <strong>webroot/img/someimage.jpg</strong>, you can create a crop by using the url <strong>http://www.mysite.com/thumbs/generate/crop/tiny/img/someimage.jpg</strong>. This will generate an image with tiny size (in this case 60x60) cropped from original.

To understand the URL:

1. http://www.mysite.com/thumbs/generate => The url for thumbs creation
2. /crop => The thumb creation method (can also be resize or fill)
3. /tiny => The thumb size (defined on /app/Config/thumbs.php)
4. /img/someimage.jpg => the location of the image from webroot´s directory

For fill method the url should be: <strong>http://www.mysite.com/thumbs/generate/fill/fillcolor/size/path/to/image.png</strong>

1. http://www.mysite.com/thumbs/generate => URL for thumbs creation
2. /fill => Fill creation method
3. /fillcolor => The color (defined on /app/Config/thumbs.php)
4. /size => The size of the image (defined on /app/Config/thumbs.php)
5. /path/to/image.png => The location of the image from webroot´s directory

If a cache does not exists yet, it will be created and saved on tmp directory for latter use, so if you try to generate the same thumb latter, the cache will be used for a better performance.

#Helper
This plugin has a helper to create those url´s, to use the Helper you should call in your controller, or directly on your AppController:

``` PHP
class AppController extends Controller{

    public $helpers = array('Thumbs.Thumbs');

}
```

In the view:

``` PHP
echo $this->Thumbs->get('img/one.jpg')->crop('tiny'); //Crop using tiny size
echo $this->Thumbs->get('img/one.jpg')->crop('small'); //Crop using small size
echo $this->Thumbs->get('img/one.jpg')->resize('small','width'); //resizes using small size using max width for the size
echo $this->Thumbs->get('img/one.jpg')->resize('small','height'); //resizes using small size using max height for the size
echo $this->Thumbs->get('img/one.jpg')->fill('small','red'); //Creates the resized imagem using small size and red background
echo $this->Thumbs->get('img/one.jpg')->fill('small','alpha_red'); //Creates the resized imagem using small size and blue background with 30% alpha
```

#Cache
The cache files will be created insite tmp directory (/app/tmp/thumbs/...), but if you want to create the cache on webroot´s directory you just
need to add Configure::write('Thumbs.cache','webroot'); on your bootstrap.php, then the images will be generated at
/app/webroot/thumbs/...

``` php
//bootstrap.php
Configure::write('Thumbs.cache','webroot');
```

When cache is sent to webroot, the url will access thumb image directly, so image will not be outputed by PHP.<br>
The good: The generated thumb will not be processed by PHP, so will have faster request time.<br>
The bad: If the original image is modified, you will need to exclude the thumb manually then cakephp can process the image again and
recreate the new thumb.
