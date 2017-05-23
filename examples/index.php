<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="echoCMS example">
    <title>echoCMS example 1</title>
    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css' integrity='sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u' crossorigin='anonymous'>
</head>
<body>
    <a href="https://github.com/kewh/echoCMS/"><img style="position: absolute; top: 0; right: 0; border: 0;" src="https://camo.githubusercontent.com/652c5b9acfaddf3a9c326fa6bde407b87f7be0f4/68747470733a2f2f73332e616d617a6f6e6177732e636f6d2f6769746875622f726962626f6e732f666f726b6d655f72696768745f6f72616e67655f6666373630302e706e67" alt="Fork me on GitHub" data-canonical-src="https://s3.amazonaws.com/github/ribbons/forkme_right_orange_ff7600.png"></a>
    <?php require '../cms/model/get.php';?>
    <?php $header = $get->item("header");?>
    <header class="jumbotron">
        <div class="container">
            <header class="col-xs-12">
                <h2><?php echo $header['heading'];?> <img class="img-responsive pull-right" src="../cms/assets/images/echocmsLogoMd.png" alt="echocms logo"></h2>
                <?php echo $header['text'];?>
            </header>
        </div>
    </header>
    <div class="container">
        <div class="row">
            <header class='col-xs-6 col-xs-offset-3'>
                <?php $index = $get->item("index");?>
                <div class="jumbotron">
                     <?php echo $index['text'];?>
                     <ul class="nav nav-pills nav-justified btn-lg">
                         <li><a href="example1.php">Example 1</a></li>
                         <li><a href="example2.php">Example 2</a></li>
                         <li><a href="example3.php">Example 3</a></li>
                     </ul>
                </div>
            </header>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js' integrity='sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa' crossorigin='anonymous'></script>
</body>
</html>
