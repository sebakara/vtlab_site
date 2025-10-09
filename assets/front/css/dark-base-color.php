<?php
header("Content-Type:text/css");
$color = $_GET['color']; // Change your Color Here


function checkhexcolor($color)
{
  return preg_match('/^#[a-f0-9]{6}$/i', $color);
}

if (isset($_GET['color']) and $_GET['color'] != '') {
  $color = "#" . $_GET['color'];
}

if (!$color or !checkhexcolor($color)) {
  $color = "#25D06F";
}

?>

.single-pricing-table:hover {
border: 1px solid <?php echo $color ?>;
}
.page-link:hover {
background-color: <?php echo $color ?>;
}
input[type="submit"], button[type="submit"] {
background-color: <?php echo $color ?>;
}
input[type="submit"]:hover, button[type="submit"]:hover {
color: <?php echo $color ?>;
}
.fc-unthemed td.fc-today {
background: <?php echo $color ?>;
}
.fc-view-container tr.fc-list-item:hover td {
background: <?php echo $color ?>;
}
.paginate_button .page-link {
color: #73818c;
}
div.dataTables_wrapper div.dataTables_info {
color: #73818c;
}
.table thead th {
color: #fff;
}
.view-order-page .order-info h3 {
color: #fff;
}
.order-info strong {
color: #fff;
}
.reply-form label {
color: #fff;
}
.message-section>h5 {
color: #fff;
}
.reply-section>h5 {
color: #fff;
}
.single-message {
background: #0a0d14;
}
.user-infos h6.name {
color: #fff;
}
.single-message .user-infos span.type {
color: #73818c;
}
.message p {
color: #73818c;
}
.description p {
color: #73818c;
}
.form_control {
border: 1px solid #ffffff2a;
}

.single_checkbox input:checked+label, .single_radio input:checked+label {
color: <?php echo $color; ?>;
}

.single_checkbox input:checked+label .box:before,
.single_radio input:checked+label .circle:before {
background: <?php echo $color; ?>;
}

.categories-widget ul li:hover {
color: <?php echo $color; ?>;
}

.price-range-widget .ui-widget .ui-slider-handle {
background: <?php echo $color; ?>;
}

.price-range-widget .ui-slider .ui-slider-range {
background: <?php echo $color; ?>;
}

.course-details-section .discription-area .discription-tabs .nav-tabs .nav-link.active {
background: <?php echo $color; ?>;
}

.course-details-section .discription-area .discription-tabs .nav-tabs .nav-link {
color: <?php echo $color; ?>;
}

.discription-area .content-box .card .card-header {
background: <?php echo $color; ?>;
}

.discription-area .content-box .card .card-body ul li a span.duration {
background: <?php echo $color; ?>;
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
