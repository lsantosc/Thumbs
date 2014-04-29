Thumbs
======

Thumbs plugin for CakePHP 2.x<br>
This plugin allows you to create thumbs from any image on your website, it also creates a cache for each generated thumb.

#Getting started
First fork this plugin into your CakePHP 2.x project, then load this plugin into your bootstrap.php file

Then create the file thumbs.php in your app/Config directory, for example:

``` PHP
return array(
    'crop'=>array(
        'tiny'=>array(60,60), //Creates a crop thumb 60x60px
        'small'=>array(100,100),
    ),
    'resize'=>array(
        'tiny'=>array(100,100), //Creates a resized imagem with 100px of maximum width or 100px of maximum height
        'small'=>array(300,300),
    )
    'fill'=>array(
        'tiny'=>array(60,60), //Creates a resized image inside a box with 60x60 pixels and defined background by colors below
        'small'=>array(100,100),
        'medium'=>array(200,200),
    ),
    'color'=>array(
        'red'=>'FF0000', //Red color for fill method
        'green'=>'00FF00',
        'alpha_blue'=>'0000FF/30', //Blue color with alpha = 30% for fill method
    ),
);
```

These will be the availiable sizes for each thumb method, the methods availiables are crop, resize and fill. Also the availiable colors for fill method.

1. Crop method will genereate a thumb cropping from the center of the image and the result thumbnail will be an image with exactly size you define
2. Resize method will resize proportional width and height of the image. The result will be an image wich it´s size will be the maximum defined width or maximum define height of the size, and it´s size will be proportional of the original image.
3. Fill method will output a proportional resized image to the original but will fill the rest of the image with a defined collor to create a thumbnail with exactly user defined sizes but with proportional resized imagem surounded by the fill color
 
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
echo $this->Thumbs->get('img/one.jpg')->resize('small'); //resizes using small size
echo $this->Thumbs->get('img/one.jpg')->fill('small',red); //Creates the resized imagem using small size and red background
echo $this->Thumbs->get('img/one.jpg')->fill('small',alpha_blue); //Creates the resized imagem using small size and blue background with 30% alpha
```




