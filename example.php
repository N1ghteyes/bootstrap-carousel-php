<?php
include 'bc.php';
use n1ghteyes\bootstrapCarousel;

error_reporting(E_ALL);
$banners = new bootstrapCarousel(TRUE);
//$myCarousel = $banners->my_carousel->addSlide(['images/113937.jpg', 'images/Memoria_Tent_unbranded.jpg']);
print "<pre>";
print_r($banners);
print "</pre>";
?>
<!DOCTYPE html>
<html>
<head>
  <title>Hello</title>
</head>
<body>
<?php print $myCarousel; ?>
</body>
</html>
