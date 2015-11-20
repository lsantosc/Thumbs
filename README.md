Thumbs plugin for CakePHP 2.x
======

This plugin allows you to create thumbs from any public image on your website.

#Getting started
1. First fork or clone this plugin into your CakePHP 2.x project;
2. Load this plugin into your bootstrap.php file with plugin´s router.

``` PHP
CakePlugin::load('Thumb',array('routes'=>true));
```

Then create the file thumbs.php in your Config directory (app/Config/thumbs.php), if you dont create, you can use predefined one from plugin´s directory.<br>
Example of thumbs.php configuration file:

``` PHP
return array(
    'crop'=>array( //Avaliable sizes for CROP method
        'tiny'=>array(60,60), //Creates a crop thumb 60x60px
        'small'=>array(100,100), //Creates a crop thumb 100x100px
    ),
    'resize'=>array( //Avaliable sizes for RESIZE method
        'tiny'=>100, //Creates a resized image with 100px of maximum width or 100px of maximum height, defined in url
        'small'=>300,
    ),
    'rotate'=>array( //Avaliable degrees for ROTATE method
        '90' => 90  //Rotates the image in 90 degrees
        '180' => 180
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

#How to generate thumbnails?
You can generate thumbnails using the url, there are three methods avaliable for thumbs generation as can viewed on the configuration
file abobe (crop, resize and fill).

Just use the url to create the thumbs, for example, if you have an image publicly accessible by the url
<strong>http://example.com/img/myimage.jpg</strong> and you want to generate a cropped thumb with 100px of width and 100px of height, you can
generate the thumbnail by accessing <strong>http://example.com/thumbs/crop/small/img/myimage.jpg</strong>, and your crop will be generated.

<strong>To understand the URL:</strong><br>

http://example.com/thumbs -> URL for thumb creation<br>

/crop -> Method for crop generation, uses the 'crop' directive on configuration file<br>
/small -> The size, the plugin will get width and height from configuration file, the first value of the array is the width, and
the second value is the height.<br>

/img/myimage.jpg -> The location of image

<strong>Crop URL</strong> http://mysite.com/thumbs/crop/<strong>&lt;size&gt;</strong>/img/myimage.jpg<br>
<strong>Resize URL</strong> http://mysite.com/thumbs/resize/<strong>&lt;size&gt;</strong>/<strong>&lt;width or height&gt;</strong>/img/myimage.jpg<br>
<strong>Fill URL</strong> http://mysite.com/thumbs/fill/<strong>&lt;size&gt;</strong>/<strong>&lt;color&gt;</strong>/img/myimage.jpg<br>
<strong>Rotate URL</strong> http://mysite.com/rotate/<strong>&lt;degrees&gt;</strong>/img/myimage.jpg<br>

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
echo $this->Thumbs->get('img/one.jpg')->fill('small','red'); //Creates the resized image using small size and red background
echo $this->Thumbs->get('img/one.jpg')->fill('small','alpha_red'); //Creates the resized image using small size and blue background with 30% alpha
echo $this->Thumbs->get('img/one.jpg')->rotate('90'); //Rotate the image in 90 degrees
```

#Cache
When in production mode (debug = 0) this plugin generates cached images on webroot folder for faster request time, so you will create the thumbnail
only once time, if you modify the original image latter, you will need to remove cached thumbs for this image manually.

<strong>If you need to exclude the cache from an image</strong> you can use the Thumbs vendor do to it, use the example:
``` php
App::import('Vendor','Thumbs.Thumbs');
Thumbs::clear('img/myimage.jpg');
```
It´s very usefull when you change the original image and needs to recreate all thumbnails again, or when you delete a post and delete a
image related to the post, so thumbnails for that image is not needed anymore and becomes junk, so this command will clear then.