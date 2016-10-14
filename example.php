<?php
include 'bc.php';
use n1ghteyes\bootstrapCarousel as bc;

$banners = new bc(TRUE);
$banners
  ->DefaultCarousel
  ->addSlide([
    '/enom/bootstrap-carousel-php/images/113937.jpg' => ['title' => 'This is a default Carousel, with a Title'],
    '/enom/bootstrap-carousel-php/images/Memoria_Tent_unbranded.jpg' => ['title' => 'This Slide has a Title', 'caption' => 'It also has a caption']
  ]);
$banners
  ->NoNavCarousel
  ->configCarousel(['nav' => FALSE, 'slideDots' => FALSE])
  ->addSlide([
      '/enom/bootstrap-carousel-php/images/113937.jpg' => ['title' => 'This Carousel has no Nav'],
      '/enom/bootstrap-carousel-php/images/Memoria_Tent_unbranded.jpg' => ['title' => 'This Carousel has no Nav'],
  ]);
$banners
  ->DifferentNavCarousel
  ->configCarousel(['leftIcon' => 'fa fa-angle-double-left', 'rightIcon' => 'fa fa-angle-double-right'])
  ->addSlide([
    '/enom/bootstrap-carousel-php/images/113937.jpg' => ['title' => 'This Carousel has different Nav icons'],
    '/enom/bootstrap-carousel-php/images/Memoria_Tent_unbranded.jpg' => ['title' => 'This Carousel has different Nav icons'],
  ]);
$banners
  ->SlowCarousel
  ->configCarousel(['interval' => 5000])
  ->addSlide([
    '/enom/bootstrap-carousel-php/images/113937.jpg' => ['title' => 'This Carousel Scrolls slower (every 5 seconds)'],
    '/enom/bootstrap-carousel-php/images/Memoria_Tent_unbranded.jpg' => ['title' => 'This Carousel Scrolls slowly (every 9 seconds)'],
  ]);
?>
<!DOCTYPE html>
<html>
<head>
  <title>Hello</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
        integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css"
        integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
          integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
          crossorigin="anonymous"></script>
</head>
<body>
<div class="container">
  <?php print $banners->build('DefaultCarousel'); ?>
  <br /><br />
  <?php print $banners->build('NoNavCarousel'); ?>
  <br /><br />
  <?php print $banners->build('DifferentNavCarousel'); ?>
  <br /><br />
  <?php print $banners->build('SlowCarousel'); ?>
  <br /><br />
</div>
</body>
</html>
