<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="echoCMS example">
    <title>echoCMS example 3</title>
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
                <?php $aside = $get->item("example3", "aside");?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3><?php echo $aside['heading'];?></h3>
                    </div>
                    <div class="panel-body">
                        <?php echo $aside['text'];?>
                    </div>
                </div>
            </header>

            <article class="col-md-9">
                <?php
                $tag = (empty($_GET['tag'])) ? '' : $_GET['tag'];
                $items = $get->itemsForTag($tag);
                if (empty($items))
                { ?>
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <h2>no tag supplied</h2>
                        This page will contain items if it is accessed from one of the tag links e.g. on Example 2.
                    </div>
                </div>
                <?php
                } else { ?>

                <div class="col-sm-10 col-sm-offset-1 ">
                    <h2>Items tagged with: <?php echo $tag;?></h2>
                    <?php foreach ($items as $item) { ?>
                    <div class="media">
                        <div class="media-left">
                            <a href="example2.php<?php echo $item["this"]; ?>">
                                <img class="media-object" style="width: 150px; height: 150px;" src="<?php echo $item['images'][0]['square']['1x'];?>" alt="">
                            </a>
                        </div>
                        <div class="media-body">
                            <h4 class="media-heading"><?php echo $item["heading"];?></h4>
                            <p><i><?php echo $item["date_display"];?></i></p>
                            <?php echo $item["caption"];?><br>&nbsp;

                            <p>
                            <?php   if (!empty($item['tags'])) echo "<bold><small>TAGS: &nbsp;</small></bold>";
                                    foreach ($item['tags'] as $tag) {
                            ?>

                                <a class='btn btn-default active btn-xs' href="example3.php?tag=<?php echo $tag; ?>"><?php echo $tag; ?></a>
                            <?php } ?>
                            </p>
                        </div>
                    </div>
                    <?php } ?>

                </div>
    <?php } ?>

            </article>
            <br>&nbsp;
        </div>
    </div>
