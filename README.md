![EchoCMS](https://raw.githubusercontent.com/kewh/echoCMS/master/cms/assets/images/echocmsLogoMd.png)

>Simple and elegant CMS for developers who craft their own Front End code. Easy to use input of text, creates responsive-friendly images in multiple sizes and aspect ratios. No templates, minimum impact on front end design, content simply inserted with PHP echo statements.

## Features
* provides flexible structuring of content to match your website pages.
* content is simply inserted into your HTML with PHP echo statements,
* Neat and simple user input pages with full featured text editing and image cropping,
* multiple configurable aspect ratios for each image, each individually cropped,
* configurable image sizes to support Responsive Images, with `srcset` statements generated automatically,
* secure user authentication, using powerful password hashing and attack blocking,
* built with PHP, MySQL, HTML5, Bootstrap, jQuery, TinyMCE, Jcrop.

## Content structure

The basic building block for the content structure is an **item**. Content for each **item** is added and updated using a single input form for text and images, and one further page for cropping images (accessed from the menu links 'create item' and 'edit item'). Note that, in order to keep things flexible, no content is mandatory. Leave fields blank if you do not need them for display or sequencing.

Each **item** can be assigned to a **page** and/or an **element**, and can have multiple **tags**, as described below. These are used to determine the items to be retrieved. A single item can be used on multiple pages, for example the content of a header section. Multiple items can belong to the same page and element, for example when there are multiple sections or articles. And different data from an item can appear on more than one page, for example a page listing blog entry titles and a page with all the data for a blog entry.

* `page` is the name of a page of your website and can be picked from a dropdown list, which can be configured by Admin or, if configured to updatable, added here on the user input page. See the Configuration section below for further details.

* `element` is a part of a website page for which you want to have unique content. Elements can be picked from a dropdown list which can be configured by Admin or, if configured to updatable, added here on the user input page. These could be set to HTML5 Semantic Elements (e.g. header, section, article, etc) or to elements of your choice  to align them to business entities (e.g. people, project, product, etc).

* `tags` multiple tags can be entered for each item. They can be used to retrieve groups of associated items. Tag values are not set by the configuration but are entered here and, once entered, will be available in a drop down list for all other items.

* `date` defaults to the current date but can be updated. It is made available for display in several formats but also determines the sequencing of items, even if not displayed.

* `heading` and `caption` are plain text and can be formatted and used in your code as required.

* `download` is a single PDF file selected for each item. Once selected, the file name, minus its file extension, appears as a draft download name but can be updated as required.

* `text` is the main text edited into html format, using the outstandingly good TinyMCE editing plugin. The facilities are relatively intuitive; particularly useful are the **_lorem ipsum_** feature which can quickly generate test data, and the **_full screen_** feature, which is useful for items with a lot of text to edit.

* `images` multiple images can be uploaded for each item. Their sequence can be changed by drag and dropping them into the required sequence. Clicking on the **crop** button for an image will bring up a new page where the cropping for each aspect ratio can be defined. The text for the image's `alt` tag can also be entered on this page. The **confirm crop** button must be clicked to record the crop and return to the main data entry page.

* `status` of the item is shown at the top left of the input page. The status can be updated using the **save draft**, **publish** and **take offline** buttons.  The **publish**  process is where the images are created for the website and may take some time depending on the number of images the item has and also on the number and sizes of images defined in the config settings.

## Getting content into your code

#### To start...
Install echoCMS on your server (see installation element below). Use the .php suffix for all your HTML pages. Then in each of your pages:
````php
     <?php require 'cms/model/get.php'; ?>
````
#### To get single items
The first call to the **`item`** function will get the most recent item for the specified page and/or element. Arguments of null or 'all' will look within all pages and/or elements. The data for the single item is then available to echo into your HTML, for example:
````php
<?php
     $yourItem = $get->item('yourPage', 'yourElement');
     echo $item['heading'];
     echo $item['text'];
     // see below for list of all data entities available for each item
?>
````
After the first call to the **`item`** function, subsequent calls can get the next and previous items within the specified page/element, by using the `next` and `prev` items, for example:
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

#### To get multiple items
Use the **`items`** function (note the plural) to get an array of all items for a specified page and/or element (or use null or 'all' to get items within all pages/elements.). Then do something like the following to loop around the array and echo the data:

````php
    <?php $yourItems = $get->items('yourPage','yourElement');?>

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
#### To add images
To use images in your HTML do something like the following (see also the element below for details of the image types available for each item):
````php
    <?php $yourItems = $get->items('yourPage','yourElement');?>

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
Or if you want a single image for a specific item, do something like the following; this example gets the first image in panorama format in the x2 size for the header of the an index page:
````php
    <?php $header = $get->item("index", "header"); ?>
    ....
    <img src="<?php echo $heading['images']['0']['panorama']['2x'];?>"
         alt="<?php echo $heading['images']['0']['alt'];?>">
````
#### To get items by `tag`
Each item can contain multiple `tags` in the `item` array. A tag can be used by passing it to another page in a URL string to retrieve items with the same tag value for example,  like this:
````php
    <?php foreach ($yourItem['tags'] as $tag) { ?>
        <a href="another.php?tag=<?php echo $tag;?>"><?php echo $tag; ?></a>
    <?php } ?>

    <!-- and in the linked page -->
    <?php $yourItems = $get->items($_GET);?>
````
## Data available for each item

|data|notes|
|------|------|
|page|text for page name, as used to retrieve data
|element|text for element name, as used to retrieve data
|heading|free format plain text heading
|caption|free format plain text caption
|text|main item HTML formatted text|
|tags|tags in an indexed array|
|download_src|absolute url of download file|
|download_name|text to display for download link|
|date_display|display format (as per config. Default: 13 Jan 2016)|
|date_tw|twitter-style format (e.g 2 days ago, 13 Jan 2016)|
|date|date in MySQL datetime format (e.g. 2016-01-13 21:27:02)|
|prev|link to previous item, in URL query string format|
|next|link to next item, in URL query string format|
|this|link to this item, in URL query string format|
|images|see following element for details|


## Image data available for each item
The images array can contain multiple images for each item, each with the following:

| format|size|notes|
|------|------|------|
|panorama||array containing absolute URLs for following format/sizes:|
||x1|base size image, as per config setting
||x2|x2 base size|
||x3|x3 base size
||srcset-w|`srcset` text containing 3 image sizes with width descriptors|
||srcset-d|`srcset` text containing 3 image sizes with density descriptors|
|portrait|as panorama above||
|landscape|as above||
|square|as above||
|thumbnail||absolute URL of 200x200px square crop|
|alt||text for image alt tag|

## User authentication
#### Authentication features
* Uses PHP's implementation of the bcrypt algoithm to hash passwords, see [wikipedia.org/wiki/Bcrypt](http://en.wikipedia.org/wiki/Bcrypt).
* Uses PHP's PDO database interface, see [php.net/manual](http://php.net/manual/en/book.pdo.php), and uses prepared statements to provide resilience against SQL injection attacks.
* Requires strong passwords by using Dropbox's zxcvbn password strength estimator.
* Blocks attackers by IP address after a configurable number of failed access attempts.
* Enables sending of notification emails via SMTP [dependent on configuration].

#### User authentication model:
The authentication model is designed for a single Admin managing multiple Users who are allowed access to all functions of the CMS system.

* Unrestricted access
 * Login page
 * User registration (email sent to Admin for activation)
 * “Forgotten Password” request password reset (sent by email)
 * Reset password using reset key
* Admin
 * Update system configuration
 * Activate user (usually following email notification to Admin of User registration)
 * Deactivate user
 * List user logins and banned IPs
 * Recreate images (usually after an image configuration setting has been updated)
 * Backup images
* Logged-in User
 * Access to CMS functions
 * Change password
 * Change email address
 * Logout

## Configuration

#### CMS Configuration

* `title` : website title used for the header of the CMS pages and notification emails.

* `change logo` : upload a logo for the CMS pages.

* `date format` : format used to display all dates using options DjSF mMn Yy of the PHP format (see [php.net/manual/en/function.date.php](http://php.net/manual/en/function.date.php)

* `timezone` : determines the basis for the time and dates used by the system. See [php.net/manual/en/timezones.php](http://php.net/manual/en/timezones.php) for a list of valid timezones

#### Content structure settings

* `pages` :  the default values to be selected in the dropdown list on the user content input page. See Content Structure section above.

* `updatable` : setting associated to `pages' can be set to allow additional values to be added by the user content input page.

* `elements` : the default values to be selected in the dropdown list on the user content input page. See Content Structure section above. By default, these are set to the HTML5 Semantic Elements (e.g. header, section, etc) but they can be configured to elements of your choice, for example to align them to business descriptions of your content items (e.g. people, projects, etc).

* `updatable` setting associated to `elements` can be set to allow additional values to be added by the user content input page.

#### Authentication settings

* `remember me days` : the number of days the cookie is left active to enable users to be kept logged in between sessions.

* `IP Ban Minutes` : the time in minutes after which banned IPs are able to attempt to log in again, and also the time within which the number of “IP Ban Attempts” are made before the IP is banned, default is 30.

* `IP Ban Attempts` : the number of failed attempts made within the “IP Ban Minutes” before the IP address is banned.

* `bcrypt cost` : is the algorithmic cost of the bcrypt password hashing function, in the range 0 to 24. Recommended setting is 12.

* `password reset minutes` : is the number of minutes before the password reset key becomes inoperative. The password reset key is sent by email in response to a “forgotten password” request.

#### Image related settings

* `cropping bg` : defines the default background colour for image cropping. This can be overridden by the user on the cropping page to suit the predominate colour of each image.

* `quality` : image quality in the range 0 to 100 determines the quality of the JPGs created for the website. All images are created as JPGs irrespective of their original format (JPG, PNG, GIF). The quality parameter ranges from 0 (lower quality, smaller file, faster page loading) to 100 (best quality, biggest file). The default setting is 80.

* `image formats` : there are 4 image aspect ratio formats – landscape, portrait, panorama or square - each of which can be de-selected if not required so that server disk space is reduced and processing time for image generation is reduced (images are created when “go live” button is pressed on the edit item page).

* `aspect ratio` : for each image format the aspect ratio can be defined in the format “width:height” where width and height are in the range 1-99. (Note that aspect ratios are flexible - and not even confined to the names given to them...)

* `base width` : is a value in pixels used to define the width of each image of type 1x, and multiplied by 2 or 3 for images of types 2x and 3x.

* `2 & 3x` : checkbox turns on the generation of additional 2x and 3x sized images to be used as Responsive Images. Also created are `srcset` values for both Display Density and Width Descriptor formats. See https://cloudfour.com/thinks/responsive-images-101-definitions/ for information on using Responsive Images.

**NOTE:** changing the image settings does not update images already created. Use the **recreate images** menu item to do this. Recreating images takes some time depending on the size and number of images. The messages on screen show progress and, on completion, the name of a backup file containing the old images.

#### email settings

* `send user emails` : to send emails for notification of authentication activities.

* `site email` : is the email address from which authentication related emails are sent.

#### SMTP server settings

* `smtp` : select to send emails by smtp, otherwise emails will be sent by mail and following settings in this section will be ignored.

* `authentication` : select if authentication is required.

* `encryption` : select "TLS", "SSL" or "none".

* `port` : the port for the SMTP server.

* `hostname`, `username` and `password` : for the SMTP server.

**NOTE:** emails are sent using PHPMailer. Please refer to [github.com/PHPMailer/PHPMailer](https://github.com/PHPMailer/PHPMailer/wiki/) for information and support.

## Installation
#### Server requirements
Requires Apache web server with mod_rewrite and PHP version 5.5 or above with GD extension. Database support is for MySQL plus potentially others if PDO drivers are available. To check, see step 2 below.

#### Installation steps

1. Download echoCMS from [github.com/kewh/echoCMS](https://github.com/kewh/echoCMS), and ftp the cms folder to your website root folder.
1. Navigate to cms/setup/check.php in your browser to check your server configuration is OK.
1. Setup a database, with a name of your choice, to hold the echoCMS tables. Import the tables from cms/setup/setupDatabase.sql
1. Edit cms/config/db.php with a text editor and fill in your database connection details and edit cms/config/url.php to fill in your site URL.
1. Navigate to yourwebsite/cms in your browser and you should be looking at the login page. Login with user: _admin@change.this_ and password: _changethis_ and then select the menu items to change your password and email.
1. Select the menu item for **configure** and set up your configuration parameters (see the Configuration section above.)

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
