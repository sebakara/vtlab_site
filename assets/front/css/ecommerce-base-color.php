<?php
header ("Content-Type:text/css");
$color = $_GET['color']; // Change your Color Here


function checkhexcolor($color) {
    return preg_match('/^#[a-f0-9]{6}$/i', $color);
}

if( isset( $_GET[ 'color' ] ) AND $_GET[ 'color' ] != '' ) {
    $color = "#".$_GET[ 'color' ];
}

if( !$color OR !checkhexcolor( $color ) ) {
    $color = "#25D06F";
}

?>


.base-bg {
    background-color: <?php echo $color; ?> !important;
}
.base-color {
    color: <?php echo $color; ?> !important;
}
.header-area .header-top {
    background: <?php echo $color; ?>;
}
.header-navigation .nav-container .main-menu ul li > a.quote-btn {
    background: <?php echo $color; ?>;
}
.featured-tabs .nav-tabs .nav-link.active {
    color: <?php echo $color; ?>;
}
.featured-tabs .nav-tabs .nav-link.active:before {
    background: <?php echo $color; ?>;
}
.featured-tabs .nav-tabs .nav-link.active:after {
    background: <?php echo $color; ?>;
}
.shop-item .shop-info .shop-price p.price span.off-price {
    color: <?php echo $color; ?>;
}
.shop-item .shop-info .shop-price p.price:before {
    background: <?php echo $color; ?>;
}
.blog-grid-section .blog-item .entry-content .post-meta ul li span:hover,
.blog-grid-section .blog-item .entry-content .post-meta ul li span:focus,
.blog-grid-section .blog-item .entry-content h3.title:hover,
.blog-grid-section .blog-item .entry-content h3.title:focus{
	color: <?php echo $color; ?>;
}
.back-to-top {
	background: <?php echo $color; ?>;
}
.header-navigation .nav-container .main-menu ul li:hover > a {
    color: <?php echo $color; ?>;
}
.header-navigation .nav-container .main-menu ul li .sub-menu li a:hover {
    background-color: <?php echo $color; ?>;
}
.banner-area .banner-wrapper .banner-left .top-tools ul.social-link li a:hover, .banner-area .banner-wrapper .banner-left .top-tools ul.social-link li a:focus {
    background: <?php echo $color; ?>;
}
.main-btn:hover,
.main-btn:focus {
	color: <?php echo $color; ?>;
}
.banner-area .banner-wrapper .banner-left .hero-arrows .slick-arrow:hover,
.banner-area .banner-wrapper .banner-left .hero-arrows .slick-arrow:focus{
	border-color: <?php echo $color; ?>;
	color: <?php echo $color; ?>;;
}
.plus-categories .categories-slide .categories-item:hover {
	border-color: <?php echo $color; ?>;
}
.header-navigation .nav-container .main-menu ul li .mega-menu .sidebar-menu .widget-categories ul.widget-link li a:hover,
.header-navigation .nav-container .main-menu ul li .mega-menu .sidebar-menu .widget-categories ul.widget-link li.active a{
	color: <?php echo $color; ?>;
}