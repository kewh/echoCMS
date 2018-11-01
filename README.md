![EchoCMS](https://raw.githubusercontent.com/kewh/echoCMS/master/cms/assets/images/echocmsLogoMd.png)

>EchoCMS ia a Contents Management System intended for developers who create their own Front End code and need a simple, elegant, PHP/SQL-based CMS for content input. It does not use templates and is designed to impinge as little as possible on Front End structure and design.

## Features
* provides flexible structuring of content to match your website pages,
* introduces no new syntax, requires only simple standard PHP to add content to HTML,
* full featured text editing,
* multiple configurable aspect ratios for each image, each individually cropped,
* configurable image sizes to support Responsive Images, with `srcset` statements generated automatically,
* secure user authentication, using powerful password hashing and attack blocking.

## Content structure

The basic building block for the content structure is an **item**. Content for each **item** is added and updated using a single input form for text and images, and one further page for cropping images (accessed from the menu links 'create item' and 'edit item').

Each **item** can be assigned to a `topic` and/or a `subtopic`, and can have multiple `tags`. These can then be used to retrieve individual items and/or groups of associated items.

Topics and subtopics can be used as you wish - for example topics could be aligned to your page names, subtopics could be HTML5 Semantic Elements or aligned to business entities (e.g. people, project, product, etc)...

## Images
Multiple images can be uploaded for each item. They are generated as web opitimised images in multiple aspect ratios each of which can be individually cropped. The configuration will determine which aspect ratios are generated, plus their dimensions and size.

The main aspect ratios are `landscape`, `portrait`, `panorama`, `square` and fluid.

For each image one of the main aspect ratios can be selected as the `prime aspect ratio` for use, for example in slideshows, to display the most appropriate format for each image.
The `collage` image, if configured, is generated from the first 1 to 4 images of an item.

## Content input
From the main menu, new items can be created and existing items edited using the same format input page.

* `status` of the item is shown at the top left of the input page. The status can be updated using the **save draft**, **publish** and **take offline** buttons.  The **publish**  process is where the images are created for the website and may take some time depending on the number of images the item has and also on the number and the configured aspect ratios and sizes.

* `date` defaults to the current date but can be updated. It is made available for display in several formats but also determines the sequencing of items, even if not displayed.

* `topics` and `subtopics` can be picked from dropdown lists, which can be defined in the configuration or, if configured to be updatable, added on the user input page.

* `tags` multiple tags can be entered on the user input page. Tag values are not set by the configuration but are entered on the user input page and, once entered, are available for subsequent items in a dropdown list.

* `heading` and `caption` are plain text and can be formatted and used in your code as required.

* `download` is a single PDF file selected for each item. Once selected, the file name, minus its file extension, appears as a draft download name but can be updated as required.

* `text` is the main text edited into html format, using the outstandingly good TinyMCE editing plugin. The facilities are relatively intuitive; particularly useful are the **_lorem ipsum_** feature which can quickly generate test data, and the **_full screen_** feature, which is useful for items with a lot of text to edit.

* `images` multiple images are uploaded individually. The sequence of the images can be changed by drag and drop into the required sequence. The **edit** button for an image will bring up a new page where the cropping for each aspect ratio is defined. One of the aspect ratios can be selected as the **prime aspect ratio**. The text for the image's `alt` tag is entered on this page. The **confirm image settings** button must be clicked to record the crop and return to the main data entry page.

>Note also that, in order to keep things flexible, no content is mandatory. Leave fields blank if you do not need them for selection, display or sequencing.

## Getting content into your code

#### To start...
Install echoCMS on your server (see installation section below). Then in each of your pages require **get.php** from the location you installed the cms directory, for example:
````php
     <?php require 'cms/model/get.php'; ?>
````
#### To get single items
The first call to the **item** function will get the most recent item for the specified topic and/or subtopic. Arguments of null or 'all' will look within all topics and/or subtopics. The data for the single item is then available to echo into your HTML, for example:
````php
<?php
     $yourItem = $get->item('yourTopic', 'yourSubtopic');
     echo $yourItem['heading'];
     echo $yourItem['text'];
     // see below for list of all data entities available for each item
?>
````
#### To get multiple items
Use the **items** function (note the plural) to get an array of all items for a specified topic and/or subtopic (or use null or 'all' to get items for all topics or subtopics). Then do something like the following to loop around the array and echo the data:
````php
    <?php $yourItems = $get->items('yourTopic','yourSubtopic');?>

    <section>
        <?php foreach ($yourItems as $yourItem) { ?>
            <item>
                <header>
                    <h2><?php echo $yourItem['heading'];?></h2>
                </header>
                <section>
                    <?php echo $yourItem['text'];?>
                </section>
            </item>
        <?php } ?>
    </section>
````
#### To get items by tag
Each item can contain multiple `tags` in an array. A tag can be used by passing it in a URL string to another page which uses the **itemsByTag** function to retrieve all items with the same tag value, like this:
````php
    <?php foreach ($yourItem['tags'] as $tag) { ?>
        <a href="another.php?tag=<?php echo $tag;?>"><?php echo $tag; ?></a>
    <?php } ?>

    <!-- and in the linked page -->
    <?php $yourItems = $get->itemsByTag($_GET); ?>
````
#### Next and Previous items
After items have been obtained using one of the above functions, using an items `next` and `prev` data entities as parameters to the **item** function, will get the next and previous items with the topic/subtopic or tag specified in the previous call, for example:
````php
<?php
     $yourItem = $get->item($yourItem['next']);
     ....
     $yourItem = $get->item($yourItem['prev']);
?>
````
Or if you want to use the next or previous item on another page, use the `next` or `prev` item in the URL query string of a link to that page, for example:

````php
    <a href='another.php<?php echo $yourItem['next'];?>'>go to next</a>

    <!-- and in the next page -->
    <?php $yourItem = $get->item($_GET);?>
````


#### To add images
To use images in your HTML do something like the following (see also the section below for details of the image types available for each item):
````php
    <?php $yourItems = $get->items('yourTopic','yourSubtopic');?>

    <section>
       <?php foreach ($yourItems as $yourItem) { ?>
          <article>
              .....
              <?php foreach ($yourItem['images'] as $image) { ?>
                  ....
                  <img src="<?php echo $image['landscape']['1x'];?>"
                       alt="<?php echo $image['alt'];?>">
                  ....
                  <!-- or for Responsive Images use srcset -->
                  <img src="<?php echo $image['landscape']['1x'];?>"
                       srcset="<?php echo $image['landscape']['srcset-w'];?>"
                       alt="<?php echo $image['alt'];?>">
                  ....
              <?php } ?>

         </article>
        <?php } ?>
    </section>
````

Or if you want a single image for a specific item, do something like the following; this example gets an image for the header of  an index page in panorama format using the srcset, which will contain all 3 image sizes with width descriptors, and defining the x2 size as the fallback:
````php
    <?php $yourHeader = $get->item("index", "header"); ?>
    ....
    <img src="<?php echo    $yourHeader['images']['0']['panorama']['2x'];?>"
         srcset="<?php echo $yourHeader['images']['0']['panorama']['srcset-w'];?>"
         alt="<?php echo    $yourHeader['images']['0']['alt'];?>">
````

To use the collage image - (note that only one size is available and the size parameter is not used).
````php
    <img src="<?php echo $yourHeader['image']['collage'];?>">
````  

> Note that [github.com/kewh/echoCMS-examples](https://github.com/kewh/echoCMS-examples) has examples of how to use echoCMS.

## Data available for each item

|data|notes|
|------|------|
|topic|text for topic, as used to retrieve data
|subtopic|text for subtopic, as used to retrieve data
|tags|tags in an indexed array|
|heading|free format plain text heading
|caption|free format plain text caption
|text|main item HTML formatted text|
|download_src|absolute url of download file|
|download_name|text to display for download link|
|date_display|display format (as per config. Default: 13 Jan 2016)|
|date_tw|twitter-style format (e.g 2 days ago, 13 Jan 2016)|
|date|date in MySQL datetime format (e.g. 2016-01-13 21:27:02)|
|prev|link to previous item, in URL query string format|
|next|link to next item, in URL query string format|
|this|link to this item, in URL query string format|
|collage|src of collage image (note: there is 1 per item)|
|images|see following for details|


## Data available for each Image
The images array for an item can contain multiple images. SRC fields are in absolute URL format, dimension fields are integers in pixels. Each image has the following information:

|data|notes|
|------|------|
|thumbnail|src of 200x200px square crop|
|alt|text for image alt tag|

For each image aspect ratios (panorama, portrait, landscape, square, fluid and prime_aspect_ratio), the following information is provided:

|data|notes|
|------|------|
|x1|src for base size image, as per config setting
|x1-height| height for x1 image
|x1-width| width for x1 image
|x2|src for x2 base size|
|x2-height| height for x2 image
|x2-width| width for x2 image
|x3|src for x3 base size
|x3-height| height for x3 image
|x3-width| width for x3 image
|srcset-w|`srcset` text containing 3 image sizes with width descriptors|
|srcset-d|`srcset` text containing 3 image sizes with density descriptors|


## User authentication
#### Authentication features
* Uses PHP's implementation of the bcrypt algoithm to hash passwords, see [wikipedia.org/wiki/Bcrypt](http://en.wikipedia.org/wiki/Bcrypt).
* Uses PHP's PDO database interface, see [php.net/manual](http://php.net/manual/en/book.pdo.php), and uses prepared statements to provide resilience against SQL injection attacks.
* Requires strong passwords by using Dropbox's zxcvbn password strength estimator.
* Blocks attackers by IP address after a configurable number of failed access attempts.
* Enables sending of notification emails via SMTP [dependent on configuration].

#### User authentication model:
The authentication model is designed for a single Admin managing multiple Users who are allowed access to all functions of the CMS system.

###### Unrestricted access
* Login page
* User registration (email sent to Admin for activation)
* “Forgotten Password” request password reset (sent by email)
* Reset password using reset key

###### Admin
* Update system configuration
* Activate user (usually following email notification to Admin of User registration)
* Deactivate user
* List user logins and banned IPs
* List access log
* Recreate images (usually after an image configuration setting has been updated)
* Backup images

###### Logged-in User
* Access to CMS functions
* Change password
* Change email address
* Logout

## Configuration

##### CMS Configuration

* `title` : website title used for the header of the CMS pages and notification emails.

* `change logo` : upload a logo for the CMS pages.

* `date format` : format used to display all dates using options DjSF mMn Yy of the PHP format (see [php.net/manual/en/function.date.php](http://php.net/manual/en/function.date.php)

* `timezone` : determines the basis for the time and dates used by the system. See [php.net/manual/en/timezones.php](http://php.net/manual/en/timezones.php) for a list of valid timezones

##### Content structure settings

* `topics` :  the default values to be selected in the dropdown list on the user content input page. See Content Structure section above.

* `updatable` : setting associated to `topics' can be set to allow additional values to be added by the user content input page.

* `subtopics` : the default values to be selected in the dropdown list on the user content input page. See Content Structure section above.

* `updatable` setting associated to `subtopics` can be set to allow additional values to be added by the user content input page.

##### Authentication settings

* `remember me days` : the number of days the cookie is left active to enable users to be kept logged in between sessions.

* `IP Ban Minutes` : the time in minutes after which banned IPs are able to attempt to log in again, and also the time within which the number of “IP Ban Attempts” are made before the IP is banned, default is 30.

* `IP Ban Attempts` : the number of failed attempts made within the “IP Ban Minutes” before the IP address is banned.

* `bcrypt cost` : is the algorithmic cost of the bcrypt password hashing function, in the range 0 to 24. Recommended setting is 12.

* `password reset minutes` : is the number of minutes before the password reset key becomes inoperative. The password reset key is sent by email in response to a “forgotten password” request.

##### Image related settings

* `cropping bg` : defines the default background colour for image cropping. This can be overridden by the user on the cropping page to suit the predominate colour of each image.

* `quality` : image quality in the range 0 to 100 determines the quality of the JPGs created for the website. All images are created as JPGs irrespective of their original format (JPG, PNG, GIF). The quality parameter ranges from 0 (lower quality, smaller file, faster page loading) to 100 (best quality, biggest file). The default setting is 80.

* `image formats` : Each of the image aspect ratio formats – landscape, portrait, panorama, square and fluid - can be de-selected if not required so that server disk space is reduced and processing time for image generation is reduced (images are created when “go live” button is pressed on the edit item page).

* `aspect ratio` : for each image format (except for fluid) the aspect ratio is defined in the format “width:height” where width and height are in the range 1-99.
>Note that aspect ratios are flexible - and not even confined to the names given to them.

* `base width` : is a value in pixels used to define the width of each image of type 1x, and multiplied by 2 or 3 for images of types 2x and 3x.

* `2 & 3x` : checkbox turns on the generation of additional 2x and 3x sized images to be used as Responsive Images. Also created are `srcset` values for both Display Density and Width Descriptor formats. See https://cloudfour.com/thinks/responsive-images-101-definitions/ for information on using Responsive Images.

>Note that changing the image settings does not update images already created. Use the **recreate images** menu item to do this. Recreating images takes some time depending on the size and number of images. The messages on screen show progress and, on completion, the name of a backup file containing the old images.

##### email settings

* `send user emails` : to send emails for notification of authentication activities.

* `site email` : is the email address from which authentication related emails are sent.

##### SMTP server settings

* `smtp` : select to send emails by smtp, otherwise emails will be sent by mail and following settings in this section will be ignored.

* `authentication` : select if authentication is required.

* `encryption` : select "TLS", "SSL" or "none".

* `port` : the port for the SMTP server.

* `hostname`, `username` and `password` : for the SMTP server.

>Note that emails are sent using PHPMailer. Please refer to [github.com/PHPMailer/PHPMailer](https://github.com/PHPMailer/PHPMailer/wiki/) for information and support.

## Installation
#### Server requirements
Requires Apache web server with mod_rewrite and PHP version 5.5 or above with GD extension. Database support is for MySQL plus potentially others if PDO drivers are available. To check, see step 2 below.

#### Installation steps

1. Download echoCMS from [github.com/kewh/echoCMS](https://github.com/kewh/echoCMS), and ftp the cms directory to your website.
1. Navigate to cms/setup/check.php in your browser to check your server configuration is OK.
1. Setup a database, with a name of your choice, to hold the echoCMS tables. Import the tables from cms/setup/setupDatabase.sql
1. Edit cms/config/db.php with a text editor and fill in your database connection details
1. Edit cms/config/url.php to fill in your site URL details and the sub-directory name, if you have installed the cms in a sub-directory.
1. Navigate to the cms directory in your browser and you should be looking at the login page. Login with user: _admin@change.this_ and password: _echoCMS99_ and then select the menu items to change your password and email.
1. Select the menu item for **configure** and set up your configuration parameters (see the Configuration section above.)

> Note that by default the cms directory is expected to be at the top level of the root directory but it may be placed in a sub-directory. To do this:
> * insert the sub-directory name in cms/config/url.php as per step 5.
> * make the **require get.php** statements in your website pages point to the actual location of cms (see the **To Start** section above).

## Dependencies

Dependencies|loaded from CDN
-|-
jQuery|https://jquery.com
Bootstrap|http://getbootstrap.com
zxcvbn  | https://github.com/dropbox/zxcvbn
Jcrop   | https://github.com/tapmodo/Jcrop
jquery-ui  |  https://jqueryui.com
tinymce	  |   https://www.tinymce.com
| **bundled with EchoCMS**
validator	| https://github.com/1000hz/bootstrap-validator
pick-a-color | https://github.com/lauren/pick-a-color
selectize	| https://github.com/selectize/selectize.js
simplyCountable| https://github.com/aaronrussell/jquery-simply-countable/
PHPMailer	| https://github.com/PHPMailer/PHPMailer
