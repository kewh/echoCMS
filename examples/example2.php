<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="echoCMS example">
    <title>echoCMS example 2</title>
    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css' integrity='sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u' crossorigin='anonymous'>
</head>
<body>
    <a href="https://github.com/kewh/echoCMS/"><img style="position: absolute; top: 0; right: 0; border: 0;" src="https://camo.githubusercontent.com/652c5b9acfaddf3a9c326fa6bde407b87f7be0f4/68747470733a2f2f73332e616d617a6f6e6177732e636f6d2f6769746875622f726962626f6e732f666f726b6d655f72696768745f6f72616e67655f6666373630302e706e67" alt="Fork me on GitHub" data-canonical-src="https://s3.amazonaws.com/github/ribbons/forkme_right_orange_ff7600.png"></a>
    <?php require '../cms/model/get.php';?>
    <?php $header = $get->item("header");?>
    <header class="jumbotron">
        <div class="container">
            <header class="col-xs-12">
                <h2><?php echo $header['heading'];?> <img class="img-responsive pull-right" src="images/echocmsLogoMd.png" alt="echocms logo"></h2>
                <?php echo $header['text'];?>
            </header>
        </div>
    </header>
    <div class="container">
        <div class="row">
            <header class='col-md-3'>
                <?php $aside = $get->item("example2", "aside");?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3><?php echo $aside['heading'];?></h3>
                    </div>
                    <div class="panel-body">
                        <?php echo $aside['text'];?>
                    </div>
                </div>
            </header>

            <?php
            if (empty($_GET))
            { ?>
                <h2>no item requested</h2>
                <p>This page will contain an item if accessed with parameters from a link, as in Example 1.</p>
            <?php
            }
            else {
                $article = $get->item($_GET);
            ?>
            <article class="col-md-9">
                <p class="">
                    <mark><em><small>image 0 panorama: </small></em></mark>
                    <img class="img-responsive img-rounded col-xs-12"
                         src="<?php echo $article['images']['0']['panorama']['1x'];?>"
                         srcset="<?php echo $article['images']['0']['panorama']['srcset-w'];?>"
                         alt="<?php echo $article['images']['0']['alt'];?>">
                </p>
                <div class='row'>
                <div class='col-xs-1'>
                    &nbsp;<br><mark><em><small>heading: </small></em></mark>
                </div>
                <div class='col-xs-10'>
                    <h2 class='text-center'><?php echo $article['heading'];?></h2>
                    &nbsp;<br>
                </div>
                </div>

                <p>
                    <mark><em><small>caption: </small></em></mark>
                    <?php echo $article['caption'];?>
                </p>

                <p>
                    <mark><em><small>date: </small></em></mark>
                    <?php echo $article['date_display'];?>
                </p>
                <p class=''>
                    <mark><em><small>image 1 thumbnail: </small></em></mark>
                    <img style='width:10%;' src="<?php echo $article['images']['1']['thumbnail'];?>" alt="<?php echo $article['images']['1']['alt'];?>">
                </p>
                <p>
                    <mark><em><small>text: </small></em></mark><?php echo $article['text'];?>
                </p>

                <p><mark><em><small>download: </small></em></mark>
                    <a href="<?php echo $article["download_src"]; ?>"><?php echo $article["download_name"]; ?></a>
                </p>

                <p>
                <mark><em><small>tags: &nbsp;</small></em></mark>
                <?php foreach ($article['tags'] as $tag) { ?>
                    <a class='btn btn-default btn-xs active' href="example3.php?tag=<?php echo $tag; ?>"><?php echo $tag; ?></a>
                <?php } ?>
                </p>

                <p>
                    <mark><em><small>topic: </small></em></mark>
                    <?php echo $article['topic'];?>
                </p>

                <p>
                    <mark><em><small>subtopic: </small></em></mark>
                    <?php echo $article['subtopic'];?>
                </p>

                <div class='col-xs-12'>
                    <hr>
                    <nav class="col-xs-8 col-xs-offset-2">
                        <a class="btn btn-default <?php if (!$article['prev']) echo 'disabled'; else echo'active'; ?>" href="example2.php<?php echo $article['prev'];?>" role="button">&laquo; previous</a>
                        <a class="btn btn-default pull-right <?php if (!$article['next']) echo 'disabled'; else echo 'active' ?>" href="example2.php<?php echo $article['next'];?>" role="button">&nbsp; next &raquo;&nbsp;</a>
                    </nav>
                    <br>
                </div>
            </article>
            <div class="col-xs-12">
                <hr>
                <?php $i=0;
                      foreach ($article['images'] as $image) { ?>
                <div class='row'>
            <div class="col-xs-12">
                    <mark><em><small><?php echo 'image '.$i .' alt: '?> </small></em></mark>
                    <?php echo $image['alt'];?><br>
                    <img style='width:36.7%;  padding: 10px' class='pull-left' src="<?php echo $image['panorama']['1x'];?>" alt="<?php echo $image['alt'];?>">
                    <img style='width:20.4%;  padding: 10px' class="pull-left" src="<?php echo $image['square']['1x'];?>" alt="<?php echo $image['alt'];?>">
                    <img style='width:18.36%; padding: 10px' class="pull-left" src="<?php echo $image['portrait']['1x'];?>" alt="<?php echo $image['alt'];?>">
                    <img style='width:24.48%; padding: 10px' class="pull-left" src="<?php echo $image['landscape']['1x'];?>" alt="<?php echo $image['alt'];?>">
                </div>
            </div>
                <?php $i++;} ?>

</div>
            <?php } ?>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js' integrity='sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa' crossorigin='anonymous'></script>
</body>
</html>
