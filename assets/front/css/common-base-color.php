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
ul.breadcumb li a:hover {
    color: <?php echo $color; ?>;
}
input[type="submit"], button[type="submit"] {
    background-color: <?php echo $color; ?>;
    border: 1px solid <?php echo $color; ?>;
}
input[type="submit"], button[type="submit"] {
    border: 1px solid <?php echo $color; ?>;
}
input[type="submit"]:hover, button[type="submit"]:hover {
    color: <?php echo $color; ?>;
}

.loader-inner {
    background-color: <?php echo $color; ?>;
}
.single-service::before {
    border-right: 2px solid <?php echo $color; ?>;
    border-bottom: 2px solid <?php echo $color; ?>;
}
.single-service::after {
    border-top: 2px solid <?php echo $color; ?>;
    border-left: 2px solid <?php echo $color; ?>;
}
.pagination-nav li.page-item.active a {
    background-color: <?php echo $color; ?>;
    border: 2px solid <?php echo $color; ?>;
}
.category-lists ul li a::after {
    color: <?php echo $color; ?>;
}
.category-lists ul li a:hover {
    color: <?php echo $color; ?>;
}
.subscribe-section span {
    color: <?php echo $color; ?>;
}
.subscribe-section h3::after {
    background-color: <?php echo $color; ?>;
}
.subscribe-form input[type="submit"], .subscribe-form button[type="submit"] {
    background-color: <?php echo $color; ?>;
    border: 1px solid <?php echo $color; ?>;
}
.subscribe-form input[type="submit"]:hover, .subscribe-form button[type="submit"]:hover {
    border: 1px solid <?php echo $color; ?>;
    color: <?php echo $color; ?>;
}
.project-ss-carousel .owl-next {
    background-color: <?php echo $color; ?>;
    border: 1px solid <?php echo $color; ?>;
}
.project-ss-carousel .owl-next:hover {
    color: <?php echo $color; ?>;
}
.project-ss-carousel .owl-prev {
    background-color: <?php echo $color; ?>;
    border: 1px solid <?php echo $color; ?>;
}
.project-ss-carousel .owl-prev:hover {
    color: <?php echo $color; ?>;
}
.popular-post-txt h5 a:hover {
    color: <?php echo $color; ?>;
}
.single-contact-info i {
    color: <?php echo $color; ?>;
}
.single-blog::before {
    border-right: 2px solid <?php echo $color; ?>;
    border-bottom: 2px solid <?php echo $color; ?>;
}
.single-blog::after {
    border-top: 2px solid <?php echo $color; ?>;
    border-left: 2px solid <?php echo $color; ?>;
}
.blog-txt .date span {
    color: <?php echo $color; ?>;
}
.blog-txt .blog-title a:hover {
    color: <?php echo $color; ?>;
}
a.readmore-btn {
    background-color: <?php echo $color; ?>;
}
ul.breadcumb li a:hover {
    color: <?php echo $color; ?>;
}
.pagination-nav li.page-item.active a, .pagination-nav li.page-item.active span {
    background-color: <?php echo $color; ?>;
    border: 2px solid <?php echo $color; ?>;
}
.fc-unthemed td.fc-today {
    background: <?php echo $color; ?> !important;
    color: #fff;
}
.single-job a.title {
    color: <?php echo $color; ?>;
}

.single-job strong i {
    color: <?php echo $color; ?>;
}
.job-details h3 {
    color: <?php echo $color; ?>;
}
.service-txt .service-title a:hover {
    color: <?php echo $color; ?>;
}
.fc-button-primary {
    background-color: <?php echo $color; ?> !important;
    border-color: <?php echo $color; ?> !important;
}
.fc-button-primary:hover {
    background-color: <?php echo $color; ?> !important;
    border-color: <?php echo $color; ?> !important;
}
.fc-button-primary:not(:disabled).fc-button-active, .fc-button-primary:not(:disabled):active {
    background-color: <?php echo $color; ?> !important;
    border-color: <?php echo $color; ?> !important;
}
.product-area .shop-search i {
    color: <?php echo $color; ?>;
}
.product-area .shop-sidebar .shop-box .sidebar-title .title::before {
    background: <?php echo $color; ?>;
}
.product-area .shop-sidebar .shop-box .sidebar-title .title::after {
    background: <?php echo $color; ?>;
}
.product-area .shop-tag .tag-item ul li a:hover {
    background: <?php echo $color; ?>;
}
.product-area .shop-tag .tag-item ul li.active-search a {
    background: <?php echo $color; ?>;
}
.ui-slider-horizontal .ui-slider-range {
    background: <?php echo $color; ?>;
}
.ui-state-default, .ui-widget-content .ui-state-default, .ui-widget-header .ui-state-default {
    border: 1px solid <?php echo $color; ?>;
    background: <?php echo $color; ?>;
}
button.filter-button {
    background-color: <?php echo $color; ?>;
}
.product-area .shop-item .shop-thumb::before {
    background-color: <?php echo $color; ?>8a;
}
li.active-search a {
    color: <?php echo $color; ?> !important;
}
.product-area .shop-item .shop-thumb ul li a {
    color: <?php echo $color; ?>;
}
.product-area .shop-item .shop-content span {
    color: <?php echo $color; ?>;
}
.product-area .shop-item .shop-content a:hover {
    color: <?php echo $color; ?>;
}
.product-details-area .product-item-slide .slick-arrow {
    background: <?php echo $color; ?>;
}
.actions .main-btn {
    background: <?php echo $color; ?>;
}
.product-details-area .product-details-content .product-details-tags ul li {
    color: <?php echo $color; ?>;
}
.shop-tab-area .nav .nav-item .nav-link.active {
    color: <?php echo $color; ?>;
}
.shop-review-area .shop-review-form .input-box ul li a {
    color: <?php echo $color; ?>;
}
.shop-review-area .shop-review-form .input-btn button {
    background: <?php echo $color; ?>;
    border-color: <?php echo $color; ?>;
}
.shop-review-area .shop-review-form .input-btn button:hover {
    color: <?php echo $color; ?>;
}
.product-items .shop-item .shop-thumb::before {
    background-color: <?php echo $color; ?>8a;
}
.product-items .shop-item .shop-thumb ul li a {
    color: <?php echo $color; ?>;
}
.product-items .shop-item .shop-content span {
    color: <?php echo $color; ?>;
}
.shop-tab-area .nav .nav-item .nav-link::before {
    background: <?php echo $color; ?>;
}
.product-details-area .product-details-slide-item .slick-arrow {
    background: <?php echo $color; ?>;
}
.cart-area .cart-table tbody .available-info .icon {
    background: <?php echo $color; ?>;
}
.cart-middle .update-cart button {
    border: 1px solid <?php echo $color; ?>;
    background: <?php echo $color; ?>;
}
.cart-middle .update-cart button:hover {
    color: <?php echo $color; ?>;
}
a.proceed-checkout-btn {
    border: 1px solid <?php echo $color; ?>;
    color: <?php echo $color; ?>;
}
a.proceed-checkout-btn:hover {
    background-color: <?php echo $color; ?>;
}
.cart-area .cart-table tbody tr td .remove span:hover {
    color: <?php echo $color; ?>;
}
.login-area .login-content .input-btn button {
    background: <?php echo $color; ?>;
    border-color: <?php echo $color; ?>;
}
.login-area .login-content .input-btn button:hover {
    color: <?php echo $color; ?>;
}
.login-area .login-content .input-btn a {
    color: <?php echo $color; ?>;
}
.user-sidebar .links li a.active {
    color: <?php echo $color; ?>;
}
.user-sidebar .links li:hover>a {
    color: <?php echo $color; ?>;
}
.main-table .dataTables_wrapper td a.btn {
    border: 1px solid <?php echo $color; ?>;
}
.main-table .dataTables_wrapper td a.btn:hover {
    background: <?php echo $color; ?>;
}
.paginate_button.active .page-link {
    background-color: <?php echo $color; ?> !important;
}
.progress-steps li.active .icon {
    background: <?php echo $color; ?>;
}
.order-info-area .prinit .btn {
    background: <?php echo $color; ?>;
}
.file-upload-area .upload-file span {
    background: <?php echo $color; ?>;
}
.actions .checkout-btn {
    border: 1px solid <?php echo $color; ?>;
}

.actions .checkout-btn:hover {
    background: <?php echo $color; ?>;
}
.product-items .shop-item .shop-content a:hover {
    color: <?php echo $color; ?>;
}
.product-details-area .product-details-content .product-social-icon ul li a:hover {
    color: <?php echo $color; ?>;
}

.faq-section .accordion .card .card-header .btn:hover {
    background-color: <?php echo $color; ?>;
}
.faq-section .accordion .card .card-header .btn[aria-expanded="true"] {
    background-color: <?php echo $color; ?>;
}
.blog-details-quote {
    border-left: 3px solid <?php echo $color; ?>;
}
.comment-lists h3::after {
    background-color: <?php echo $color; ?>;
}
.reply-form-section h3::after {
    background-color: <?php echo $color; ?>;
}
.error-txt a {
    background-color: <?php echo $color; ?>;
    border: 1px solid <?php echo $color; ?>;
}

.error-txt a:hover {
    color: <?php echo $color; ?>;
}

.mega-dropdown .dropbtn::before {
    background-color: <?php echo $color; ?>;
}
.course-item .course-content a:hover, .course-item .course-content a:focus {
    color: <?php echo $color; ?>;
}
.course-item .course-content .course-admin-price .price span {
    background: <?php echo $color; ?>;
    color: #fff;
}
.course-item .course-content .course-admin-price .price span:hover {
    background: <?php echo $color; ?>;
}
.course-item .course-content .course-admin-price .price span.pre-price:hover {
    background: #f1f1f1;
}
.course-item .course-content .course-admin-price .admin span a:hover {
    color: <?php echo $color; ?>;
}
.categorie-box .main-btn {
    color: <?php echo $color; ?>;
}
.courses-sidebar .widget-box h4 {
    border-bottom: 1px solid <?php echo $color; ?>;
}
.single_checkbox input:checked+label .box:before, .single_radio input:checked+label .circle:before {
    background: <?php echo $color; ?>;
}
.single_checkbox input:checked+label, .single_radio input:checked+label {
    color: <?php echo $color; ?>;
}
.course-details-section .courses-img-box .video-box .video-popup {
    background: <?php echo $color; ?>;
}
.course-details-section .courses-img-box .video-box .video-popup::before {
    background: <?php echo $color; ?>;
}
.course-details-section .course-content .button .main-btn {
    background: <?php echo $color; ?>;
}
.discription-area .instructor-wrap .content .social-link li a {
    background: <?php echo $color; ?>;
}
.discription-area .instructor-wrap .content .social-link li a:hover {
    background: <?php echo $color; ?>;
}
.course-videos-section .video_list .content-box .card .card-header {
    background: <?php echo $color; ?>;
}
.course-videos-section .video_list .content-box .card .card-body ul li a span.duration {
    background: <?php echo $color; ?>;
}
.course-details-section .discription-area .discription-tabs .nav-tabs .nav-link.active {
    background: <?php echo $color; ?>;
}
.discription-area .content-box .card .card-header {
    background: <?php echo $color; ?>;
}
.discription-area .content-box .card .card-body ul li a span.duration {
    background: <?php echo $color; ?>;
}
.knowledge-box .btn_link {
background: <?php echo $color ?>;
}

.knowledge-box:hover .btn_link {
border-color: <?php echo $color ?>;
color: <?php echo $color ?>;
}

.requirements-nav .card .card-header {
background: <?php echo $color ?>;
}

.post-share-date .post-date, .post-share-date .share-list li span, .post-share-date .share-list li a:hover, .post-share-date .share-list li:focus {
color: <?php echo $color ?>;
}

.knowledge-box ul li a:hover:hover {
  color: <?php echo $color ?>;
}

.requirements-nav .card .card-body .list li a.active {
  color: <?php echo $color ?>;
}
.single-causes-section .causes-single-wrapper .causes-content .single-progress-bar, .charity-causes .causes-box .causes-content .single-progress-bar {
    border: 1px solid <?php echo $color ?>;
}
.single-causes-section .causes-single-wrapper .causes-content .progress-bar-inner, .charity-causes .causes-box .causes-content .progress-bar-inner {
    background: <?php echo $color ?>;
    border: 1px solid <?php echo $color ?>;
}
.single-causes-section .causes-single-wrapper .causes-content .progress-bar-style, .charity-causes .causes-box .causes-content .progress-bar-style {
    background: <?php echo $color ?>;
}
.charity-causes .causes-box .causes-img .causes-overlay .goal {
    border: 3px solid <?php echo $color ?>;
}
.single-causes-section .causes-single-wrapper .progress-bar-style:after, .charity-causes .causes-box .causes-content .progress-bar-style:after {
    background: <?php echo $color ?>;
}
.causes-box .causes-content .content-info .btn_link:hover, .charity-causes .causes-box .causes-content .content-info h3 a:hover {
    color: <?php echo $color ?>;
}
.donation-form .form_group span {
    background: <?php echo $color ?>;
}
.charity-sidebar .widget-box .widget-title:after {
    background: <?php echo $color ?>;
}
.donation-form ul li a:hover {
    border-color: <?php echo $color ?>;
}
.event-area-section .event-item .event-img .event-overlay .main-btn:hover {
    background: <?php echo $color ?>;
}
.event-area-section .event-item .event-content h3 a:hover {
    color: <?php echo $color ?>;
}
.event-details-section .syotimer-cell__value {
    color: <?php echo $color ?>;
}
.main-btn {
    background: <?php echo $color ?>;
}
.event-details-section .discription-area .discription-tabs .nav-tabs .nav-link:hover, .event-details-section .discription-area .discription-tabs .nav-tabs .nav-link:focus, .event-details-section .discription-area .discription-tabs .nav-tabs .nav-link.active {
    background-color: <?php echo $color ?>;
}
.event-details-section .discription-area .discription-tabs .nav-tabs .nav-link:hover, .event-details-section .discription-area .discription-tabs .nav-tabs .nav-link:focus, .event-details-section .discription-area .discription-tabs .nav-tabs .nav-link.active {
    background-color: <?php echo $color ?>;
}
.event-details-section .event-thumb-slide .arrow {
    background: <?php echo $color ?>;
}
.event-details-section .event-details-wrapper .event-content ul li a {
    background: <?php echo $color ?>;
}
.contact-form-section .single-info .icon-wrapper {
    border: 1px solid <?php echo $color ?>;
}
.contact-form-section .single-info .icon-wrapper i {
    color: <?php echo $color ?>;
}
.contact-form-section .single-info:hover .icon-wrapper {
    background-color: <?php echo $color ?>;
}
.feedback-area-v1 .feedback-form .rating-box .feedback-rating:hover li i {
    color: <?php echo $color ?>;
}
.feedback-area-v1 .feedback-form .rating-box .feedback-rating:after {
    color: <?php echo $color ?>;
}
.faq-area-v1 .sidebar-widget-area .categories-widget .nav-tabs .nav-link:hover, .faq-area-v1 .sidebar-widget-area .categories-widget .nav-tabs .nav-link:focus, .faq-area-v1 .sidebar-widget-area .categories-widget .nav-tabs .nav-link.active {
    color: <?php echo $color ?>;
}
.faq-area-v1 .faq-details-wrapper .card .card-header[aria-expanded=true] {
    background-color: <?php echo $color ?>;
}
.gallery-area-v1 .filter-nav .filter-btn li:hover, .gallery-area-v1 .filter-nav .filter-btn li:focus, .gallery-area-v1 .filter-nav .filter-btn li.active {
    color: <?php echo $color ?>;
}
.pricing-tables .filter-nav .filter-btn li:hover, .pricing-tables .filter-nav .filter-btn li:focus, .pricing-tables .filter-nav .filter-btn li.active {
    color: <?php echo $color ?>;
}
.case-lists .filter-nav .filter-btn li:hover, .case-lists .filter-nav .filter-btn li:focus, .case-lists .filter-nav .filter-btn li.active {
    color: <?php echo $color ?>;
}
.main-menu ul li .mega-menu .sidebar-menu .widget-categories ul.widget-link li a:hover, .main-menu ul li .mega-menu .sidebar-menu .widget-categories ul.widget-link li.active a {
    color: <?php echo $color; ?>;
}
.sidebar-main-wrapper .box-item .box-info h4 a:hover {
    color: <?php echo $color; ?> !important;
}


@media only screen and (max-width : 991px) {
    li.submenus ul {
        background: transparent;
    }
    .mean-container a.meanmenu-reveal {
        color: <?php echo $color; ?>;
    }
}
@media only screen and (max-width: 575px) {
  .case-types ul li a {
      background-color: #fff;
  }
}