# PHP Annotator for image annotation
![PHP Annotator](https://raw.githubusercontent.com/klaxxon/phpannotator/master/screenshot2.png)

Web/PHP based image annotator that creates Darknet/YOLO txt files for machine learning systems.

I was looking for a very simple, web based way to take a collection of images and produce annotations from shared contributors over the web.  This is the very beginning so beware of missing features, bugs and security issues.

Creates YOLO/Darknet style .txt files:<br/>
<pre>
0 0.025 0.90416666666667 0.10625 0.1
0 0.73125 0.86458333333333 0.0171875 0.03125
3 0.353125 0.925 0.10625 0.20625
</pre>
  
<h2>Features</h2>
Images are shuffled before serving to the webpage.<br/>
Scroll wheel zooming is supported.<br/>


<h2>TODO</h2>
Add security
Review current annotations


<h2>Install</h2>

All you need is PHP with gdimage.

There are only two files that need web access: index.php and process.php.
Within the web directory containing the two files, create a "data" directory.  This is the operational directory for a training set.  This way, it can simply be linked or copied out.  In the future, you will be able to select a training set to annotate.

Create a classes.txt file in your data directory containing a class name per line, no special characters since this name will be used as IDs in the javascript.<B><i>All annotations are indexes to this file.  Only add new classes to end of file, otherwise you will need to redo all annotations for the new class order.</i></b>

Put all of your images in a data/images directory.

The webserver needs write access to this directory.

<h2>Do Over</h2>

To clear out any annotations associated with an image, simply delete the associated .txt file.
