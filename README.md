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
        'tiny'=>array('width'=>60,'height'=>60),
        'small'=>array('width'=>150,'height'=>150),
    ),
    'resize'=>array(
        'tiny'=>array('width'=>100,'height'=>300),
        'small'=>array('width'=>200,'height'=>300),
    )
    'fill'=>array(
        'tiny'=>array('width'=>100,'height'=>100),
        'medium'=>array('width'=>300,'height'=>300),
        'mycustomsize'=>array('width'=>300,'height'=>150),
    ),
);
```

These will be the availiable sizes for each thumb method, the methods availiables are crop, resize and fill.

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
4. /img/someimage.jpg => the location of the image from webroot directory

If a cache does not exists yet, it will be created and saved on tmp directory for latter use, so if you try to generate the same thumb latter, the cache will be used for a better performance.





