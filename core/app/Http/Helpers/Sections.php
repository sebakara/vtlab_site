<?php

use App\BasicExtended;
use App\BasicExtra;
use App\BasicSetting;
use App\Language;
use App\Pcategory;
use App\Product;

if (!function_exists('convertHtml') ) {
    function convertHtml($content) {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }
        $be = BasicExtended::firstOrFail();
        $version = $be->theme_version;

        $content = str_replace("{base_url}", url('/'), $content);

        // service category
        $content = str_replace("[pagebuilder-service-category][/pagebuilder-service-category]", serviceCategorySection($currentLang, $version), $content);

        // services
        $content = str_replace("[pagebuilder-services][/pagebuilder-services]", servicesSection($currentLang, $version), $content);

        // portfolios
        $content = str_replace("[pagebuilder-portfolios][/pagebuilder-portfolios]", portfoliosSection($currentLang, $version), $content);

        // team
        $content = str_replace("[pagebuilder-team][/pagebuilder-team]", teamSection($currentLang, $version), $content);

        // statistics
        $content = str_replace("[pagebuilder-statistics][/pagebuilder-statistics]", statisticsSection($currentLang, $version), $content);

        // testimonial
        $content = str_replace("[pagebuilder-testimonial][/pagebuilder-testimonial]", testimonialSection($currentLang, $version), $content);

        // packages
        $content = str_replace("[pagebuilder-packages][/pagebuilder-packages]", packagesSection($currentLang, $version), $content);

        // blogs
        $content = str_replace("[pagebuilder-blogs][/pagebuilder-blogs]", blogsSection($currentLang, $version), $content);

        // partner
        $content = str_replace("[pagebuilder-partner][/pagebuilder-partner]", partnerSection($currentLang, $version), $content);

        // approach
        $content = str_replace("[pagebuilder-approach][/pagebuilder-approach]", approachSection($currentLang, $version), $content);

        // faq
        $content = str_replace("[pagebuilder-faq][/pagebuilder-faq]", faqSection($currentLang), $content);

        // Featured Products
        $content = str_replace("[pagebuilder-featured-product][/pagebuilder-featured-product]", fprodSection($currentLang, $version), $content);

        // Newsletter
        $content = str_replace("[pagebuilder-newsletter-section][/pagebuilder-newsletter-section]", newsletterSection($currentLang, $version), $content);

        // Featured Product Category
        $content = str_replace("[pagebuilder-fproduct-category-section][/pagebuilder-fproduct-category-section]", fProdCatSection($currentLang, $version), $content);

        // Home Product Category
        $content = str_replace("[pagebuilder-hproduct-category-section][/pagebuilder-hproduct-category-section]", hProdCatSection($currentLang, $version), $content);

        return $content;
    }
}

if (!function_exists('replaceBaseUrl') ) {
    function replaceBaseUrl($content) {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }
        $content = str_replace("{base_url}", url('/'), $content);
        return $content;
    }
}

if (!function_exists('faqSection')) {
    function faqSection($currentLang)
    {

        if (!empty($currentLang->faqs)) {
            $faqs = $currentLang->faqs()->orderBy('serial_number', 'ASC')->get();
        } else {
            $faqs = [];
        }

        $faqSec = "<div class='row' data-gjs-draggable='false' data-gjs-editable='false' data-gjs-removable='false' data-gjs-propagate=" . '["removable","editable","draggable"]' . ">
        <div class='col-lg-6'>
        <div class='accordion' id='accordionExample1'>";

        for ($i = 0; $i < ceil(count($faqs) / 2); $i++) {
            $faqSec .= "<div class='card'>
            <div class='card-header' id='heading" . $faqs[$i]->id . "'>
            <h2 class='mb-0'>
            <button class='btn btn-link collapsed btn-block text-left' type='button' data-toggle='collapse' data-target='#collapse" . $faqs[$i]->id . "' aria-expanded='false' aria-controls='collapse" . $faqs[$i]->id . "'>" .
            convertUtf8($faqs[$i]->question)
            . "</button>
            </h2>
            </div>
            <div id='collapse" . $faqs[$i]->id . "' class='collapse' aria-labelledby='heading" . $faqs[$i]->id . "' data-parent='#accordionExample1'>
            <div class='card-body'>" .
            convertUtf8($faqs[$i]->answer) .
            "</div>
            </div>
            </div>";
        }

        $faqSec .= "</div>
        </div>
        <div class='col-lg-6'>
        <div class='accordion' id='accordionExample2'>";
        for ($i = ceil(count($faqs) / 2); $i < count($faqs); $i++) {
            $faqSec .= "<div class='card'>
            <div class='card-header' id='heading" . $faqs[$i]->id . "'>
            <h2 class='mb-0'>
            <button class='btn btn-link collapsed btn-block text-left' type='button' data-toggle='collapse' data-target='#collapse" . $faqs[$i]->id . "' aria-expanded='false' aria-controls='collapse" . $faqs[$i]->id . "'>" . convertUtf8($faqs[$i]->question) .
            "</button>
            </h2>
            </div>
            <div id='collapse" . $faqs[$i]->id . "' class='collapse' aria-labelledby='heading" . $faqs[$i]->id . "' data-parent='#accordionExample2'>
            <div class='card-body'>" .
            convertUtf8($faqs[$i]->answer) .
            "</div>
            </div>
            </div>";
        }
        $faqSec .= "</div>
        </div>
        </div>";

        return $faqSec;
    }
}


if (!function_exists('serviceCategorySection')) {

    function serviceCategorySection($currentLang, $version) {

        if (!empty($currentLang->scategories)) {
            $scats = $currentLang->scategories()->where('status', 1)->where('feature', 1)->orderBy('serial_number', 'ASC')->get();
        } else {
            $scats = [];
        }

        $scatsec = "";
        if ($version == 'lawyer') {
            $scatsec .= "
            <div class='service_slide service-slick'>";
            foreach ($scats as $key => $scat) {
                $scatsec .= "<div class='grid_item'>
                <div class='grid_inner_item'>
                <div class='lawyer_img'>
                <img data-src='" . url('assets/front/img/service_category_icons/' . $scat->image) . "' class='img-fluid lazy' alt=''>
                </div>
                <div class='lawyer_content'>
                <h4>" . convertUtf8($scat->name) . "</h4>
                <p>";
                if (strlen(convertUtf8($scat->short_text)) > 100) {
                    $scatsec .= mb_substr($scat->short_text,0,100,'utf-8') . "<span style='display: none;'>" . mb_substr($scat->short_text,100, null,'utf-8') . "</span>
                    <a href='#' class='see-more'>" . __('see more') . "...</a>";
                }
                else {
                    $scatsec .= $scat->short_text;
                }
                $scatsec .= "</p>
                <a href='" . route('front.services', ['category' => $scat->id]) . "' class='lawyer_btn'>" . __('View Services') . "</a>
                </div>
                </div>
                </div>";
            }
            $scatsec .= "</div>";
        } elseif ($version == 'default' || $version == 'dark') {
            $scatsec = "<div class='row'>";
            foreach ($scats as $key => $scategory) {
                $scatsec .= "<div class='col-xl-3 col-lg-4 col-sm-6'>
                <div class='single-category'>";
                if (!empty($scategory->image)) {
                    $scatsec .= "<div class='img-wrapper'>
                    <img class='lazy' data-src='" . url("assets/front/img/service_category_icons/$scategory->image") . "' alt=''>
                    </div>";
                }
                $scatsec .= "<div class='text'>
                <h4>" . convertUtf8($scategory->name) . "</h4>
                <p>";
                if (strlen($scategory->short_text) > 112) {
                    $scatsec .= mb_substr($scategory->short_text,0,112,'utf-8') . "<span style='display: none;'>" . mb_substr($scategory->short_text,112,null,'utf-8') . "</span>
                    <a href='#' class='see-more'>" . __('see more') . "...</a>";
                } else {
                    $scatsec .= $scategory->short_text;
                }
                $scatsec .= "</p>
                <a href='" . route('front.services', ['category' => $scategory->id]) . "' class='readmore'>" . __('View Services') . "</a>
                </div>
                </div>
                </div>";
            }
            $scatsec .= "</div>";
        } elseif ($version == 'gym') {
            $scatsec .= "<div class='service_slide service-slick'>";
            foreach ($scats as $key => $scat) {
                $scatsec .= "<div class='grid_item'>
                <div class='grid_inner_item'>";
                if (!empty($scat->image)) {
                    $scatsec .= "<div class='finlance_img'>
                    <img data-src='" . url('assets/front/img/service_category_icons/' . $scat->image) . "' class='img-fluid lazy' alt=''>
                    <div class='service_overlay'>
                    <div class='button_box'>
                    <a href='" . route('front.services', ['category' => $scat->id]) . "' class='more_icon'><i class='fas fa-angle-double-right'></i></a>
                    </div>
                    </div>
                    </div>";
                }
                $scatsec .= "<div class='finlance_content'>
                <h3><a href='" . route('front.services', ['category' => $scat->id]) . "'>" . convertUtf8($scat->name) . "</a></h3>
                </div>
                <div class='summary text-center mt-2'>";
                if (strlen(convertUtf8($scat->short_text)) > 112) {
                    $scatsec .= mb_substr($scat->short_text,0,112,'utf-8') . "<span style='display: none;'>" . mb_substr($scat->short_text,112, null,'utf-8') . "</span>
                    <a href='#' class='see-more'>" . __('see more') . "...</a>";
                }
                else {
                    $scatsec .= $scat->short_text;
                }
                $scatsec .= "</div>
                </div>
                </div>";
            }
            $scatsec .= "</div>";
        } elseif ($version == 'car') {
            $scatsec .= "<div class='row'>";
            foreach ($scats as $key => $scat) {
                $scatsec .= "<div class='col-lg-4 col-md-6 col-sm-12 mb-5'>
                <div class='grid_item text-center'>
                <div class='grid_inner_item'>";
                if (!empty($scat->image)) {
                    $scatsec .= "<div class='finlance_icon'>
                    <img data-src='" . url('assets/front/img/service_category_icons/' . $scat->image) . "' class='img-fluid lazy' alt=''>
                    </div>";
                }
                $scatsec .= "<div class='finlance_content'>
                <h4>" . convertUtf8($scat->name) . "</h4>
                <p>";
                    if (strlen(convertUtf8($scat->short_text)) > 112) {
                        $scatsec .= mb_substr($scat->short_text,0,112,'utf-8') . "<span style='display: none;'>" . mb_substr($scat->short_text,112, null,'utf-8') . "</span>
                        <a href='#' class='see-more'>" . __('see more') . "...</a>";
                    }
                    else {
                        $scatsec .= $scat->short_text;
                    }
                $scatsec .= "</p>
                <a href='" . route('front.services', ['category' => $scat->id]) . "' class='btn_link'>" . __('View Services') . "</a>
                </div>
                </div>
                </div>
                </div>";
            }
            $scatsec .= "</div>";
        } elseif ($version == 'construction') {
            $scatsec .= "<div class='service_slide service-slick'>";
            foreach ($scats as $key => $scat) {
                $scatsec .= "<div class='grid_item'>
                    <div class='grid_inner_item'>
                        <div class='finlance_icon'>
                            <img data-src='" . url('assets/front/img/service_category_icons/' . $scat->image) . "' class='img-fluid lazy' alt=''>
                        </div>
                        <div class='finlance_content'>
                            <h4>" . convertUtf8($scat->name) . "</h4>
                            <p class='mb-0'>";
                            if (strlen(convertUtf8($scat->short_text)) > 112) {
                                $scatsec .= mb_substr($scat->short_text,0,112,'utf-8') . "<span style='display: none;'>" . mb_substr($scat->short_text,112, null,'utf-8') . "</span>
                                <a href='#' class='see-more'>" . __('see more') . "...</a>";
                            }
                            else {
                                $scatsec .= $scat->short_text;
                            }
                            $scatsec .= "</p>
                            <a href='" . route('front.services', ['category' => $scat->id]) . "' class='btn_link d-inline-block mt-35'>" . __('View Services') . "</a>
                        </div>
                    </div>
                </div>";
            }
            $scatsec .= "</div>";
        } elseif ($version == 'logistic') {
            $scatsec .= "<div class='service_slide service-slick'>";
            foreach ($scats as $key => $scat) {
                $scatsec .= "<div class='grid_item'>
                    <div class='grid_inner_item'>
                        <div class='logistics_icon'>
                            <img data-src='" . url('assets/front/img/service_category_icons/' . $scat->image) . "' class='img-fluid lazy' alt=''>
                        </div>
                        <div class='logistics_content'>
                            <h4>" . convertUtf8($scat->name) . "</h4>
                            <p>";
                                if (strlen(convertUtf8($scat->short_text)) > 112) {
                                    $scatsec .= mb_substr($scat->short_text,0,112,'utf-8') . "<span style='display: none;'>" . mb_substr($scat->short_text,112, null,'utf-8') . "</span>
                                    <a href='#' class='see-more'>" . __('see more') . "...</a>";
                                }
                                else {
                                    $scatsec .= $scat->short_text;
                                }
                            $scatsec .= "</p>
                            <a href='" . route('front.services', ['category' => $scat->id]) . "' class='btn_link'>" . __('View Services') . "</a>
                        </div>
                    </div>
                </div>";
            }
            $scatsec .= "</div>";
        } elseif ($version == 'cleaning') {
            $scatsec .= "<div class='service-carousel-active service-slick row'>";

            foreach ($scats as $key => $scat) {
                $scatsec .= "<div class='single-service-item col-4 mx-0'>";
                if (!empty($scat->image)) {
                    $scatsec .= "<div class='single-service-bg'>
                        <img data-src='" . url('assets/front/img/service_category_icons/' . $scat->image) . "' class='img-fluid lazy' alt=''>
                        <span><i class='fas fa-quidditch'></i></span>
                        <div class='single-service-link'>
                            <a href='" . route('front.services', ['category' => $scat->id]) . "' class='main-btn service-btn'>" . __('View Services') . "</a>
                        </div>
                    </div>
                    <div class='single-service-content'>
                        <h4>" . convertUtf8($scat->name) . "</h4>
                        <p>";
                            if (strlen(convertUtf8($scat->short_text)) > 100) {
                                $scatsec .= mb_substr($scat->short_text,0,100,'utf-8') . "<span style='display: none;'>" . mb_substr($scat->short_text,100, null,'utf-8') . "</span>
                                <a href='#' class='see-more'>" . __('see more') . "...</a>";
                            }
                            else {
                                $scatsec .= $scat->short_text;
                            }
                        $scatsec .= "</p>
                    </div>";
                }
                $scatsec .= "</div>";
            }


            $scatsec .= "</div>";
        }

        return $scatsec;
    }
}

if (!function_exists('servicesSection')) {
    function servicesSection($currentLang, $version) {

        if (!empty($currentLang->services)) {
            $services = $currentLang->services()->where('feature', 1)->orderBy('serial_number', 'ASC')->get();
        } else {
            $services = [];
        }

        $servicesSec = "";
        if ($version == 'lawyer') {
            $servicesSec .= "<div class='service_slide service-slick'>";
            foreach ($services as $key => $service) {
                $servicesSec .= "<div class='grid_item col-lg-4 mx-0' data-gjs-draggable='false' data-gjs-editable='false' data-gjs-removable='false' data-gjs-propagate=" . '["removable","editable","draggable"]' . ">
                <div class='grid_inner_item'>
                <div class='lawyer_img'>";
                if (!empty($service->main_image)) {
                    $servicesSec .= "<div class='logistics_icon'>
                    <img data-src='" . url('assets/front/img/services/' . $service->main_image) . "' class='img-fluid lazy' alt=''>
                    </div>";
                }
                $servicesSec .= "</div>
                <div class='lawyer_content'>
                <h4>" . convertUtf8($service->title) . "</h4>
                <p>";
                if (strlen(convertUtf8($service->summary)) > 100) {
                    $servicesSec .= mb_substr($service->summary,0,100,'utf-8') . "<span style='display: none;'>" . mb_substr($service->summary,100, null,'utf-8') . "</span>
                    <a href='#' class='see-more'>" . __('see more') . "...</a>";
                }
                else {
                    $servicesSec .= $service->summary;
                }
                $servicesSec .= "</p>";
                if ($service->details_page_status == 1) {
                    $servicesSec .= "<a href='" . route('front.servicedetails', [$service->slug]) . "' class='lawyer_btn'>" . __('Read More') . "</a>";
                }
                $servicesSec .= "</div>
                </div>
                </div>";
            }

            $servicesSec .= "</div>";
        } elseif ($version == 'default' || $version == 'dark') {
            $servicesSec = "<div class='row'>";
            foreach ($services as $service) {
                $servicesSec .= "<div class='col-lg-4 col-md-6 col-sm-8'>
                <div class='services-item mt-30'>
                <div class='services-thumb'>
                <img class='lazy' data-src='" . url('assets/front/img/services/' . $service->main_image) . "' alt='service' />
                </div>
                <div class='services-content'>
                <a class='title'";
                if ($service->details_page_status == 1) {
                    $servicesSec .= "href='" . route('front.servicedetails', [$service->slug]) . "'";
                }
                $servicesSec .= "><h4>" . convertUtf8($service->title) . "</h4></a>
                <p>";
                if (strlen($service->summary) > 120) {
                    $servicesSec .= mb_substr($service->summary,0,120,'utf-8') . "<span style='display: none;'>" . mb_substr($service->summary,120,null,'utf-8') . "</span>
                    <a href='#' class='see-more'>" . __('see more') . "...</a>";
                } else {
                    $servicesSec .= $service->summary;
                }
                $servicesSec .= "</p>";
                if ($service->details_page_status == 1) {
                    $servicesSec .= "<a href='" . route('front.servicedetails', [$service->slug]) . "'>" . __('Read More') . " <i class='fas fa-plus'></i></a>";
                }
                $servicesSec .= "</div>
                </div>
                </div>";
            }
            $servicesSec .= "</div>";
        } elseif ($version == 'gym') {
            $servicesSec .= "<div class='service_slide service-slick'>";
            foreach ($services as $key => $service) {
                $servicesSec .= "<div class='grid_item'>
                <div class='grid_inner_item'>";
                if (!empty($service->main_image)) {
                    $servicesSec .= "<div class='finlance_img'>
                    <img class='lazy' data-src='" . url('assets/front/img/services/' . $service->main_image) . "' alt='service' />";
                    if ($service->details_page_status == 1) {
                        $servicesSec .= "<div class='service_overlay'>
                        <div class='button_box'>
                        <a href='" . route('front.servicedetails', [$service->slug]) . "' class='more_icon'><i class='fas fa-angle-double-right'></i></a>
                        </div>
                        </div>";
                    }
                    $servicesSec .= "</div>";
                }
                $servicesSec .= "<div class='finlance_content'>
                <h3><a ";
                if ($service->details_page_status == 1) {
                    $servicesSec .= " href='" . route('front.servicedetails', [$service->slug]) . "'";
                }
                $servicesSec .= ">" . convertUtf8($service->title) . "</a></h3>
                </div>
                <div class='summary text-center mt-2'>";
                if (strlen(convertUtf8($service->summary)) > 112) {
                    $servicesSec .= mb_substr($service->summary,0,112,'utf-8') . "<span style='display: none;'>" . mb_substr($service->summary,112, null,'utf-8') . "</span>
                    <a href='#' class='see-more'>" . __('see more') . "...</a>";
                }
                else {
                    $servicesSec .= $service->summary;
                }
                $servicesSec .= "</div>
                </div>
                </div>";
            }
            $servicesSec .= "</div>";
        } elseif ($version == 'car') {
            $servicesSec .= "<div class='row'>";
            foreach ($services as $key => $service) {
                $servicesSec .= "<div class='col-lg-4 col-md-6 col-sm-12 mb-5'>
                <div class='grid_item text-center'>
                <div class='grid_inner_item'>";
                if (!empty($service->main_image)) {
                    $servicesSec .= "<div class='finlance_icon' style='margin-bottom: 20px;'>
                    <img class='lazy' data-src='" . url('assets/front/img/services/' . $service->main_image) . "' alt='service' />
                    </div>";
                }
                $servicesSec .= "<div class='finlance_content'>
                <h4>" . convertUtf8($service->title) . "</h4>
                <p>";
                if (strlen(convertUtf8($service->summary)) > 100) {
                    $servicesSec .= mb_substr($service->summary,0,100,'utf-8') . "<span style='display: none;'>" . mb_substr($service->summary,100, null,'utf-8') . "</span>
                    <a href='#' class='see-more'>" . __('see more') . "...</a>";
                }
                else {
                    $servicesSec .= $service->summary;
                }
                $servicesSec .= "</p>";
                if ($service->details_page_status == 1) {
                    $servicesSec .= "<a href='" . route('front.servicedetails', [$service->slug]) . "' class='btn_link'>" . __('Read More') . "</a>";
                }
                $servicesSec .= "</div>
                </div>
                </div>
                </div>";
            }

            $servicesSec .= "</div>";
        } elseif ($version == 'construction') {
            $servicesSec .= "<div class='service_slide service-slick'>";

            foreach ($services as $key => $service) {
                $servicesSec .= "<div class='grid_item'>
                    <div class='grid_inner_item'>";
                    if (!empty($service->main_image)) {
                            $servicesSec .= "<div class='finlance_icon' style='margin-bottom: 20px;'>
                                <img class='lazy' data-src='" . url('assets/front/img/services/' . $service->main_image) . "' alt='service' />
                            </div>";
                    }
                    $servicesSec .= "<div class='finlance_content'>
                        <h4>" . convertUtf8($service->title) . "</h4>
                        <p class='mb-0'>";
                        if (strlen(convertUtf8($service->summary)) > 120) {
                            $servicesSec .= mb_substr($service->summary,0,120,'utf-8') . "<span style='display: none;'>" . mb_substr($service->summary,120, null,'utf-8') . "</span>
                            <a href='#' class='see-more'>" . __('see more') . "...</a>";
                        }
                        else {
                            $servicesSec .= $service->summary;
                        }
                        $servicesSec .= "</p>";
                    if ($service->details_page_status == 1) {
                        $servicesSec .= "<a href='" . route('front.servicedetails', [$service->slug]) . "' class='btn_link d-inline-block mt-35'>" . __('Read More') . "</a>";
                    }
                    $servicesSec .= "</div>
                    </div>
                </div>";
            }


            $servicesSec .= "</div>";
        } elseif ($version == 'logistic') {
            $servicesSec .= "<div class='service_slide service-slick'>";
            foreach ($services as $key => $service) {
                $servicesSec .= "<div class='grid_item'>
                                <div class='grid_inner_item'>";

                if (!empty($service->main_image)) {
                    $servicesSec .= "<div class='logistics_icon'>
                                            <img data-src='" . url('assets/front/img/services/' . $service->main_image) . "' class='img-fluid lazy' alt=''>
                                        </div>";
                }

                $servicesSec .= "<div class='logistics_content'>
                                        <h4>" . convertUtf8($service->title) . "</h4>
                                        <p>";
                                            if (strlen(convertUtf8($service->summary)) > 120) {
                                                $servicesSec .= mb_substr($service->summary,0,120,'utf-8') . "<span style='display: none;'>" . mb_substr($service->summary,120, null,'utf-8') . "</span>
                                                <a href='#' class='see-more'>" . __('see more') . "...</a>";
                                            }
                                            else {
                                                $servicesSec .= $service->summary;
                                            }
                                        $servicesSec .= "</p>";

                if ($service->details_page_status == 1) {
                    $servicesSec .= "<a href='" . route('front.servicedetails', [$service->slug]) . "' class='btn_link'>" . __('View Services') . "</a>";
                }

                $servicesSec .= "</div>
                                </div>
                            </div>";
            }
            $servicesSec .= "</div>";
        } elseif ($version == 'cleaning') {
            $servicesSec .= "<div class='service-carousel-active service-slick'>";

            foreach ($services as $key => $service) {
                $servicesSec .= "<div class='single-service-item'>";
                if (!empty($service->main_image)) {
                    $servicesSec .= "<div class='single-service-bg'>
                                            <img class='lazy' data-src='" . url('assets/front/img/services/' . $service->main_image) . "' alt=''>
                                            <span><i class='fas fa-quidditch'></i></span>";
                    if ($service->details_page_status == 1) {
                        $servicesSec .= "<div class='single-service-link'>
                                                    <a href='" . route('front.servicedetails', [$service->slug]) . "' class='main-btn service-btn'>" . __('View More') . "</a>
                                                </div>";
                    }
                    $servicesSec .= "</div>
                                        <div class='single-service-content'>
                                            <h4>" . convertUtf8($service->title) . "</h4>
                                            <p>";
                                            if (strlen(convertUtf8($service->summary)) > 100) {
                                                $servicesSec .= mb_substr($service->summary,0,100,'utf-8') . "<span style='display: none;'>" . mb_substr($service->summary,100, null,'utf-8') . "</span>
                                                <a href='#' class='see-more'>" . __('see more') . "...</a>";
                                            }
                                            else {
                                                $servicesSec .= $service->summary;
                                            }
                                            $servicesSec .= "</p>
                                        </div>";
                }
                $servicesSec .= "</div>";
            }

            $servicesSec .= "</div>";
        }

        return $servicesSec;
    }
}

if (!function_exists('portfoliosSection')) {
    function portfoliosSection($currentLang, $version) {
        if (!empty($currentLang->portfolios)) {
            $portfolios = $currentLang->portfolios()->where('feature', 1)->orderBy('serial_number', 'ASC')->get();
        } else {
            $portfolios = [];
        }

        $portfoliosSec = "";
        if ($version == 'lawyer') {
            $portfoliosSec .= "<div class='project_slide project-slick'>";
            foreach ($portfolios as $key => $portfolio) {
                $portfoliosSec .= "<div class='grid_item' data-gjs-draggable='false' data-gjs-editable='false' data-gjs-removable='false' data-gjs-propagate=" . '["removable","editable","draggable"]' . ">
                <div class='grid_inner_item'>
                <div class='lawyer_img'>
                <img data-src='" . url('assets/front/img/portfolios/featured/' . $portfolio->featured_image) . "' class='img-fluid lazy' alt=''>
                <div class='overlay_img'></div>
                <div class='overlay_content'>
                <h3><a href='" . route('front.portfoliodetails', [$portfolio->slug]) . "'>" . (strlen($portfolio->title) > 25 ? mb_substr($portfolio->title, 0, 25, 'utf-8') . '...' : $portfolio->title) . "</a></h3>";
                if (!empty($portfolio->service)) {
                    $portfoliosSec .= "<p>" . convertUtf8($portfolio->service->title) . "</p>";
                }
                $portfoliosSec .= "<a href='" . route('front.portfoliodetails', [$portfolio->slug]) . "' class='lawyer_btn'>" . __('View More') . "</a>
                </div>
                </div>
                </div>
                </div>";
            }
            $portfoliosSec .= "</div>";
        } elseif ($version == 'default' || $version == 'dark') {
            $portfoliosSec .= "<div class='case-carousel owl-carousel owl-theme'>";
            foreach ($portfolios as $key => $portfolio) {
                $portfoliosSec .= "<div class='single-case single-case-bg-1 lazy' data-bg='" . url('assets/front/img/portfolios/featured/' . $portfolio->featured_image) . "'>
                <div class='outer-container'>
                <div class='inner-container'>
                <h4>";
                    $portfoliosSec .= strlen($portfolio->title) > 36 ? mb_substr($portfolio->title, 0, 36, 'utf-8') . '...' : $portfolio->title . "</h4>";
                if (!empty($portfolio->service)) {
                    $portfoliosSec .= "<p>" . convertUtf8($portfolio->service->title) . "</p>";
                }

                $portfoliosSec .= "<a href='" . route('front.portfoliodetails', [$portfolio->slug]) . "' class='readmore-btn'><span>" . __('Read More') . "</span></a>;

                </div>
                </div>
                </div>";
            }
            $portfoliosSec .= "</div>";
        } elseif ($version == 'gym') {
            $portfoliosSec .= "<div class='project_slide project-slick'>";
            foreach ($portfolios as $key => $portfolio) {
                $portfoliosSec .= "<div class='grid_item'>
                <div class='grid_inner_item'>
                <div class='finlance_img'>
                <img data-src='" . url('assets/front/img/portfolios/featured/' . $portfolio->featured_image) . "' class='img-fluid lazy' alt=''>
                <div class='project_overlay'>
                <div class='finlance_content'>
                <a href='" . route('front.portfoliodetails', [$portfolio->slug]) . "' class='more_icon'><i class='fas fa-angle-double-right'></i></a>
                <h3><a href='" . route('front.portfoliodetails', [$portfolio->slug]) . "'>" . (strlen($portfolio->title) > 36 ? mb_substr($portfolio->title, 0, 36, 'utf-8') . '...' : $portfolio->title) . "</a></h3>
                </div>
                </div>
                </div>
                </div>
                </div>";
            }
            $portfoliosSec .= "</div>";
        } elseif ($version == 'car') {
            $portfoliosSec .= "<div class='project_slide project_slick'>";
            foreach ($portfolios as $key => $portfolio) {
                $portfoliosSec .= "<div class='grid_item'>
                <div class='grid_inner_item'>
                <div class='finlance_img'>
                <img data-src='" . url('assets/front/img/portfolios/featured/' . $portfolio->featured_image) . "' class='img-fluid lazy' alt=''>
                <div class='project_overlay'>
                <div class='finlance_content'>
                <a href='" . route('front.portfoliodetails', [$portfolio->slug]) . "' class='more_icon'><i class='fas fa-angle-double-right'></i></a>

                <h3><a href='" . route('front.portfoliodetails', [$portfolio->slug]) . "'>" . (strlen($portfolio->title) > 25 ? mb_substr($portfolio->title, 0, 25, 'utf-8') . '...' : $portfolio->title) . "</a></h3>";


                if (!empty($portfolio->service)) {
                    $portfoliosSec .= "<p>" . convertUtf8($portfolio->service->title) . "</p>";
                }
                $portfoliosSec .= "</div>
                </div>
                </div>
                </div>
                </div>";
            }
            $portfoliosSec .= "</div>";
        } elseif ($version == 'construction') {
            $portfoliosSec .= "<div class='project_slide project-slick row'>";
            foreach ($portfolios as $key => $portfolio) {
                $portfoliosSec .= "<div class='grid_item col-3 mx-0'>
                    <div class='grid_inner_item'>
                        <div class='finlance_img'>
                            <img data-src='" . url('assets/front/img/portfolios/featured/' . $portfolio->featured_image) . "' class='img-fluid lazy' alt=''>
                            <div class='overlay_img'></div>
                            <div class='overlay_content'>
                                <div class='button_box'>
                                    <a href='" . route('front.portfoliodetails', [$portfolio->slug]) . "' class='finlance_btn'>" . __('View More') . "</a>
                                </div>
                            </div>
                        </div>
                        <div class='finlance_content'>
                            <h4>" . (strlen($portfolio->title) > 25 ? mb_substr($portfolio->title, 0, 25, 'utf-8') . '...' : $portfolio->title) . "</h4>";

                        if (!empty($portfolio->service)) {
                            $portfoliosSec .= "<p>" . convertUtf8($portfolio->service->title) . "</p>";
                        }
                        $portfoliosSec .= "</div>
                    </div>
                </div>";
            }
            $portfoliosSec .= "</div>";
        } elseif ($version == 'logistic') {
            $portfoliosSec .= "<div class='project_slide project-slick row'>";
            foreach ($portfolios as $key => $portfolio) {
                $portfoliosSec .= "<div class='grid_item col-3 mx-0'>
                                <div class='grid_inner_item'>
                                    <div class='logistics_img'>
                                        <img data-src='" . url('assets/front/img/portfolios/featured/' . $portfolio->featured_image) . "' class='img-fluid lazy' alt=''>
                                        <div class='overlay_img'></div>
                                        <div class='overlay_content'>
                                            <div class='button_box'>
                                                <a href='" . route('front.portfoliodetails', [$portfolio->slug]) . "' class='logistics_btn'>" . __('View More') . "</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class='logistics_content'>
                                        <h4>" . (strlen($portfolio->title) > 25 ? mb_substr($portfolio->title, 0, 25, 'utf-8') . '...' : $portfolio->title) . "</h4>";

                if (!empty($portfolio->service)) {
                    $portfoliosSec .= "<p>" . convertUtf8($portfolio->service->title) . "</p>";
                }
                $portfoliosSec .= "</div>
                                </div>
                            </div>";
            }
            $portfoliosSec .= "</div>";
        } elseif ($version == 'cleaning') {
            $portfoliosSec .= "<div class='project-slider-active project-slick'>";
            foreach ($portfolios as $key => $portfolio) {
                $portfoliosSec .= "<div class='single-project-item'>
                                <img class='lazy' data-src='" . url('assets/front/img/portfolios/featured/' . $portfolio->featured_image) . "' alt=''>
                                <div class='project-link text-center'>
                                <h4>" . (strlen($portfolio->title) > 36 ? mb_substr($portfolio->title, 0, 36, 'utf-8') . '...' : $portfolio->title) . "</h4>";
                if (!empty($portfolio->service)) {
                    $portfoliosSec .= "<span>" . convertUtf8($portfolio->service->title) . "</span>";
                }
                $portfoliosSec .= "<a href='" . route('front.portfoliodetails', [$portfolio->slug]) . "' class='main-btn project-link-btn'>" . __('View Details') . "</a>
                                </div>
                            </div>";
            }
            $portfoliosSec .= "</div>";
        }

        return $portfoliosSec;
    }
}

if (!function_exists('teamSection')) {
    function teamSection($currentLang, $version) {
        if (!empty($currentLang->members)) {
            $members = $currentLang->members()->where('feature', 1)->get();
        } else {
            $members = [];
        }

        $teamSec = "";
        if ($version == 'lawyer') {
            $teamSec .= "<div class='team_slide team-slick'>";
            foreach ($members as $key => $member) {
                $teamSec .= "<div class='grid_item'>
                <div class='grid_inner_item'>
                <div class='lawyer_img'>
                <img data-src='" . url('assets/front/img/members/' . $member->image) . "' class='img-fluid lazy' alt=''>
                </div>
                <div class='lawyer_content text-center'>
                <h4>" . convertUtf8($member->name) . "</h4>
                <p>" . convertUtf8($member->rank) . "</p>
                <ul class='social'>";
                if (!empty($member->facebook)) {
                    $teamSec .= "<li><a href='" . $member->facebook . "' target='_blank'><i class='fab fa-facebook-f'></i></a></li>";
                }
                if (!empty($member->twitter)) {
                    $teamSec .= "<li><a href='" . $member->twitter . "' target='_blank'><i class='fab fa-twitter'></i></a></li>";
                }
                if (!empty($member->linkedin)) {
                    $teamSec .= "<li><a href='" . $member->linkedin . "' target='_blank'><i class='fab fa-linkedin-in'></i></a></li>";
                }
                if (!empty($member->instagram)) {
                    $teamSec .= "<li><a href='" . $member->instagram . "' target='_blank'><i class='fab fa-instagram'></i></a></li>";
                }
                $teamSec .= "</ul>
                </div>
                </div>
                </div>";
            }
            $teamSec .= "</div>";
        } elseif ($version == 'default' || $version == 'dark') {
            $teamSec .= "<div class='team-carousel common-carousel owl-carousel owl-theme'>";
            foreach ($members as $key => $member) {
                $teamSec .= "<div class='single-team-member'>
                <div class='team-img-wrapper'>
                <img class='lazy' data-src='" . url('assets/front/img/members/' . $member->image) . "' alt=''>
                <div class='social-accounts'>
                <ul class='social-account-lists'>";
                if (!empty($member->facebook)) {
                    $teamSec .= "<li class='single-social-account'><a href='" . $member->facebook . "'><i class='fab fa-facebook-f'></i></a></li>";
                }
                if (!empty($member->twitter)) {
                    $teamSec .= "<li class='single-social-account'><a href='" . $member->twitter . "'><i class='fab fa-twitter'></i></a></li>";
                }
                if (!empty($member->linkedin)) {
                    $teamSec .= "<li class='single-social-account'><a href='" . $member->linkedin . "'><i class='fab fa-linkedin-in'></i></a></li>";
                }
                if (!empty($member->instagram)) {
                    $teamSec .= "<li class='single-social-account'><a href='" . $member->instagram . "'><i class='fab fa-instagram'></i></a></li>";
                }
                $teamSec .= "</ul>
                </div>
                </div>
                <div class='member-info'>
                <h5 class='member-name'>" . convertUtf8($member->name) . "</h5>
                <small>" . convertUtf8($member->rank) . "</small>
                </div>
                </div>";
            }
            $teamSec .= "</div>";
        } elseif ($version == 'gym') {
            $teamSec .= "<div class='team_slide team_slick'>";
            foreach ($members as $key => $member) {
                $teamSec .= "<div class='grid_item'>
                <div class='grid_inner_item'>
                <div class='finlance_img'>
                <img data-src='" . url('assets/front/img/members/' . $member->image) . "' class='img-fluid lazy' alt=''>
                <div class='team_overlay'>
                <div class='finlance_content'>
                <h3>" . convertUtf8($member->name) . "</h3>
                <p>" . convertUtf8($member->rank) . "</p>
                <ul class='social_box'>";
                if (!empty($member->facebook)) {
                    $teamSec .= "<li><a href='" . $member->facebook . "' target='_blank'><i class='fab fa-facebook-f'></i></a></li>";
                }
                if (!empty($member->twitter)) {
                    $teamSec .= "<li><a href='" . $member->twitter . "' target='_blank'><i class='fab fa-twitter'></i></a></li>";
                }
                if (!empty($member->linkedin)) {
                    $teamSec .= "<li><a href='" . $member->linkedin . "' target='_blank'><i class='fab fa-linkedin-in'></i></a></li>";
                }
                if (!empty($member->instagram)) {
                    $teamSec .= "<li><a href='" . $member->instagram . "' target='_blank'><i class='fab fa-instagram'></i></a></li>";
                }
                $teamSec .= "</ul>
                </div>
                </div>
                </div>
                </div>
                </div>";
            }
            $teamSec .= "</div>";
        } elseif ($version == 'car') {
            $teamSec .= "<div class='team_slide team_slick'>";
            foreach ($members as $key => $member) {
                $teamSec .= "<div class='grid_item'>
                <div class='grid_inner_item'>
                <div class='finlance_img'>
                <img data-src='" . url('assets/front/img/members/' . $member->image) . "' class='img-fluid lazy' alt=''>
                <div class='team_overlay'>
                <ul class='social_box'>";
                if (!empty($member->facebook)) {
                    $teamSec .= "<li><a href='" . $member->facebook . "' target='_blank'><i class='fab fa-facebook-f'></i></a></li>";
                }
                if (!empty($member->twitter)) {
                    $teamSec .= "<li><a href='" . $member->twitter . "' target='_blank'><i class='fab fa-twitter'></i></a></li>";
                }
                if (!empty($member->linkedin)) {
                    $teamSec .= "<li><a href='" . $member->linkedin . "' target='_blank'><i class='fab fa-linkedin-in'></i></a></li>";
                }
                if (!empty($member->instagram)) {
                    $teamSec .= "<li><a href='" . $member->instagram . "' target='_blank'><i class='fab fa-instagram'></i></a></li>";
                }
                $teamSec .= "</ul>
                </div>
                </div>
                <div class='finlance_content lazy' data-bg='" . url('assets/front/img/pattern_bg.jpg') . "'>
                <h4>" . convertUtf8($member->name) . "</h4>
                <p>" . convertUtf8($member->rank) . "</p>
                </div>
                </div>
                </div>";
            }
            $teamSec .= "</div>";
        } elseif ($version == 'construction') {
            $teamSec .= "<div class='team_slide team-slick'>";
            foreach ($members as $key => $member) {
                $teamSec .= "<div class='grid_item'>
                    <div class='grid_inner_item'>
                        <div class='finlance_img'>
                            <img data-src='" . url('assets/front/img/members/' . $member->image) . "' class='img-fluid lazy' alt=''>
                            <div class='overlay_content'>
                                <div class='social_box'>
                                    <ul>";
                                    if (!empty($member->facebook)) {
                                        $teamSec .= " <li><a href='" . $member->facebook . "' target='_blank'><i class='fab fa-facebook-f'></i></a></li>";
                                    }
                                    if (!empty($member->twitter)) {
                                        $teamSec .= "  <li><a href='" . $member->twitter . "' target='_blank'><i class='fab fa-twitter'></i></a></li>";
                                    }
                                    if (!empty($member->linkedin)) {
                                        $teamSec .= " <li><a href='" . $member->linkedin . "' target='_blank'><i class='fab fa-linkedin-in'></i></a></li>";
                                    }
                                    if (!empty($member->instagram)) {
                                        $teamSec .= "<li><a href='" . $member->instagram . "' target='_blank'><i class='fab fa-instagram'></i></a></li>";
                                    }
                                    $teamSec .= "</ul>
                                </div>
                            </div>
                        </div>
                        <div class='finlance_content text-center'>
                            <h4>" . convertUtf8($member->name) . "</h4>
                            <p>" . convertUtf8($member->rank) . "</p>
                        </div>
                    </div>
                </div>";
            }
            $teamSec .= "</div>";
        } elseif ($version == 'logistic') {
            $teamSec .= "<div class='team_slide team-slick'>";
            foreach ($members as $key => $member) {
                $teamSec .= "<div class='grid_item'>
                                <div class='grid_inner_item'>
                                    <div class='logistics_img'>
                                        <img data-src='" . url('assets/front/img/members/' . $member->image) . "' class='img-fluid lazy' alt=''>
                                        <div class='overlay_content'>
                                            <div class='social_box'>
                                                <ul>";
                if (!empty($member->facebook)) {
                    $teamSec .= "<li><a href='" . $member->facebook . "' target='_blank'><i class='fab fa-facebook-f'></i></a></li>";
                }
                if (!empty($member->twitter)) {
                    $teamSec .= "<li><a href='" . $member->twitter . "' target='_blank'><i class='fab fa-twitter'></i></a></li>";
                }
                if (!empty($member->linkedin)) {
                    $teamSec .= "<li><a href='" . $member->linkedin . "' target='_blank'><i class='fab fa-linkedin-in'></i></a></li>";
                }
                if (!empty($member->instagram)) {
                    $teamSec .= "<li><a href='" . $member->instagram . "' target='_blank'><i class='fab fa-instagram'></i></a></li>";
                }
                $teamSec .= "</ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class='logistics_content text-center'>
                                        <h4>" . convertUtf8($member->name) . "</h4>
                                        <p>" . convertUtf8($member->rank) . "</p>
                                    </div>
                                </div>
                            </div>";
            }
            $teamSec .= "</div>";
        } elseif ($version == 'cleaning') {
            $teamSec .= "<div class='team-carousel-active team-slick'>";
            foreach ($members as $key => $member) {
                $teamSec .= "<div class='single-team-item'>
                                <img class='lazy' data-src='" . url('assets/front/img/members/' . $member->image) . "' alt=''>
                                <div class='single-team-content'>
                                    <div class='single-team-member-details'>
                                        <h4>" . convertUtf8($member->name) . "</h4>
                                        <p>" . convertUtf8($member->rank) . "</p>
                                    </div>
                                    <ul class='team-social-links'>";
                if (!empty($member->facebook)) {
                    $teamSec .= "<li><a href='" . $member->facebook . "' target='_blank'><i class='fab fa-facebook-f'></i></a></li>";
                }
                if (!empty($member->twitter)) {
                    $teamSec .= "<li><a href='" . $member->twitter . "' target='_blank'><i class='fab fa-twitter'></i></a></li>";
                }
                if (!empty($member->linkedin)) {
                    $teamSec .= "<li><a href='" . $member->linkedin . "' target='_blank'><i class='fab fa-linkedin-in'></i></a></li>";
                }
                if (!empty($member->instagram)) {
                    $teamSec .= "<li><a href='" . $member->instagram . "' target='_blank'><i class='fab fa-instagram'></i></a></li>";
                }
                $teamSec .= "</ul>
                                </div>
                            </div>";
            }
            $teamSec .= "</div>";
        }

        return $teamSec;
    }
}

if (!function_exists('statisticsSection')) {
    function statisticsSection($currentLang, $version) {
        if (!empty($currentLang->statistics)) {
            $statistics = $currentLang->statistics()->orderBy('serial_number', 'ASC')->get();
        } else {
            $statistics = [];
        }

        $statisticSec = "";
        if ($version == 'lawyer') {
            $statisticSec .= "<div class='row'>";

            foreach ($statistics as $key => $statistic) {
                $statisticSec .= "<div class='col-lg-3 col-md-6 col-sm-12' data-gjs-draggable='false' data-gjs-editable='false' data-gjs-removable='false' data-gjs-propagate=" . '["removable","editable","draggable"]' . ">
                <div class='counter_box'>
                <div class='icon'>
                <i class='" . $statistic->icon . "'></i>
                </div>
                <h2><span class='counter'>" . convertUtf8($statistic->quantity) . "</span>+</h2>
                <h4>" . convertUtf8($statistic->title) . "</h4>
                </div>
                </div>";
            }

            $statisticSec .= "</div>";
        } elseif ($version == 'default' || $version == 'dark') {
            $bs = BasicSetting::firstOrFail();

            $statisticSec .= "<div class='row no-gutters'>";
            foreach ($statistics as $key => $statistic) {
                $statisticSec .= "<div class='col-lg-3 col-md-6'>
                <div class='round' data-value='1' data-number='" . convertUtf8($statistic->quantity) . "' data-size='200' data-thickness='6' data-fill='{&quot;color&quot;: &quot;#" . $bs->base_color . "&quot;}'>
                <strong></strong>
                <h5><i class='" . $statistic->icon . "'></i> " . convertUtf8($statistic->title) . "</h5>
                </div>
                </div>";
            }
            $statisticSec .= "</div>";
        } elseif ($version == 'gym') {
            $statisticSec .= "<div class='row'>";
            foreach ($statistics as $key => $statistic) {
                $statisticSec .= "<div class='col-lg-3 col-md-6 col-sm-12'>
                <div class='counter_box'>
                <div class='icon'>
                <i class='" . $statistic->icon . "'></i>
                </div>
                <h2><span class='counter'>" . convertUtf8($statistic->quantity) . "</span>+</h2>
                <h4>" . convertUtf8($statistic->title) . "</h4>
                </div>
                </div>";
            }
            $statisticSec .= "</div>";
        } elseif ($version == 'car') {
            $statisticSec .= "<div class='row'>";
            foreach ($statistics as $key => $statistic) {
                $statisticSec .= "<div class='col-lg-3 col-md-6 col-sm-12'>
                <div class='counter_box'>
                <div class='icon'>
                <i class='" . $statistic->icon . "'></i>
                </div>
                <h2><span class='counter'>" . convertUtf8($statistic->quantity) . "</span>+</h2>
                <h4>" . convertUtf8($statistic->title) . "</h4>
                </div>
                </div>";
            }
            $statisticSec .= "</div>";
        } elseif ($version == 'construction') {
            $statisticSec .= "<div class='row'>";
                foreach ($statistics as $key => $statistic) {
                    $statisticSec .= "<div class='col-lg-3 col-md-6 col-sm-12'>
                        <div class='counter_box'>
                            <div class='icon'>
                                <i class='" . $statistic->icon . "'></i>
                            </div>
                            <h2><span class='counter'>" . convertUtf8($statistic->quantity) . "</span>+</h2>
                            <p>" . convertUtf8($statistic->title) . "</p>
                        </div>
                    </div>";
                }
            $statisticSec .= "</div>";
        } elseif ($version == 'logistic') {
            $statisticSec .= "<div class='row'>";
            foreach ($statistics as $key => $statistic) {
                $statisticSec .= "<div class='col-lg-3 col-md-6 col-sm-12'>
                    <div class='counter_box'>
                        <div class='icon'>
                            <i class='" . $statistic->icon . "'></i>
                        </div>
                        <h2><span class='counter'>" . convertUtf8($statistic->quantity) . "</span>+</h2>
                        <p>" . convertUtf8($statistic->title) . "</p>
                    </div>
                </div>";
            }
            $statisticSec .= "</div>";
        } elseif ($version == 'cleaning') {
            $statisticSec .= "<div class='row'>";
            foreach ($statistics as $key => $statistic) {
                $statisticSec .= "<div class='col-lg-3 col-md-6'>
                    <div class='single-counter-item'>
                        <span><i class='" . $statistic->icon . "'></i></span>
                        <h1><span class='count'>" . convertUtf8($statistic->quantity) . "</span>   +</h1>
                        <p>" . convertUtf8($statistic->title) . "</p>
                    </div>
                </div>";
            }
            $statisticSec .= "</div>";
        }

        return $statisticSec;
    }
}

if (!function_exists('testimonialSection')) {
    function testimonialSection($currentLang, $version) {
        if (!empty($currentLang->testimonials)) {
            $testimonials = $currentLang->testimonials()->orderBy('serial_number', 'ASC')->get();
        } else {
            $testimonials = [];
        }

        $testimonialSec = "";
        if ($version == 'lawyer') {
            $testimonialSec .= "<div class='testimonial_slide'>";
            foreach ($testimonials as $key => $testimonial) {
                $testimonialSec .= "<div class='testimonial_box' data-gjs-draggable='false' data-gjs-editable='false' data-gjs-removable='false' data-gjs-propagate=" . '["removable","editable","draggable"]' . ">
                <div class='lawyer_content_box'>
                <img class='lazy' data-src='" . url('assets/front/img/quote_1.png') . "' alt=''>
                <p>" . convertUtf8($testimonial->comment) . "</p>
                <div class='admin_box d-flex align-items-center'>
                <div class='thumb'>
                <img data-src='" . url('assets/front/img/testimonials/' . $testimonial->image) . "' class='img-fluid lazy' alt=''>
                </div>
                <div class='info'>
                <h4>" . convertUtf8($testimonial->name) . "</h4>
                <p>" . convertUtf8($testimonial->rank) . "</p>
                </div>
                </div>
                </div>
                </div>";
            }
            $testimonialSec .= "</div>";
        } elseif ($version == 'default' || $version == 'dark') {
            $testimonialSec .= "<div class='testimonial-carousel owl-carousel owl-theme'>";
            foreach ($testimonials as $key => $testimonial) {
                $testimonialSec .= "<div class='single-testimonial'>
                <div class='img-wrapper'><img class='lazy' data-src='" . url('assets/front/img/testimonials/' . $testimonial->image) . "' alt=''></div>
                <div class='client-desc'>
                <p class='comment'>" . convertUtf8($testimonial->comment) . "</p>
                <h6 class='name'>" . convertUtf8($testimonial->name) . "</h6>
                <p class='rank'>" . convertUtf8($testimonial->rank) . "</p>
                </div>
                </div>";
            }
            $testimonialSec .= "</div>";
        } elseif ($version == 'gym') {
            $testimonialSec .= "<div class='testimonial_slide'>";
            foreach ($testimonials as $key => $testimonial) {
                $testimonialSec .= "<div class='testimonial_box'>
                <div class='row align-items-center'>
                <div class='col-lg-5 col-md-5'>
                <div class='finlance_img'>
                <img data-src='" . url('assets/front/img/testimonials/' . $testimonial->image) . "' class='img-fluid lazy' alt=''>
                </div>
                </div>
                <div class='col-lg-7 col-md-7'>
                <div class='finlance_content'>
                <img class='lazy' data-src='" . url('assets/front/img/quote.png') . "' alt=''>
                <p>" . convertUtf8($testimonial->comment) . "</p>
                <h3>" . convertUtf8($testimonial->name) . "</h3>
                <h6>" . convertUtf8($testimonial->rank) . "</h6>
                </div>
                </div>
                </div>
                </div>";
            }
            $testimonialSec .= "</div>";
        } elseif ($version == 'car') {
            $testimonialSec .= "<div class='testimonial_slide'>";
            foreach ($testimonials as $key => $testimonial) {
                $testimonialSec .= "<div class='testimonial_box'>
                <div class='quote'>
                <img class='lazy' data-src='" . url('assets/front/img/quote.png') . "' alt=''>
                </div>
                <div class='client_box'>
                <div class='thumb'>
                <img class='lazy' data-src='" . url('assets/front/img/testimonials/' . $testimonial->image) . "' alt=''>
                </div>
                <div class='info'>
                <h3>" . convertUtf8($testimonial->name) . "</h3>
                <h6>" . convertUtf8($testimonial->rank) . "</h6>
                </div>
                </div>
                <div class='finlance_content'>
                <p>" . convertUtf8($testimonial->comment) . "</p>
                </div>
                </div>";
            }
            $testimonialSec .= "</div>";
        } elseif ($version == 'construction') {
            $testimonialSec .= "<div class='testimonial_slide'>";
            foreach ($testimonials as $key => $testimonial) {
                $testimonialSec .= "<div class='testimonial_box d-flex align-items-center'>
                    <div class='finlance_img'>
                        <img data-src='" . url('assets/front/img/testimonials/' . $testimonial->image) . "' class='img-fluid lazy' alt=''>
                    </div>
                    <div class='finlance_content'>
                        <h4>" . convertUtf8($testimonial->name) . "</h4>
                        <h6>" . convertUtf8($testimonial->rank) . "</h6>
                        <p>" . convertUtf8($testimonial->comment) . "</p>
                    </div>
                </div>";
            }
            $testimonialSec .= "</div>";
        } elseif ($version == 'logistic') {
            $testimonialSec .= "<div class='testimonial_slide'>";
            foreach ($testimonials as $key => $testimonial) {
                $testimonialSec .= "<div class='testimonial_box d-lg-flex align-items-lg-center'>
                    <div class='logistics_img'>
                        <img data-src='" . url('assets/front/img/testimonials/' . $testimonial->image) . "' class='img-fluid lazy' alt='' width='100%'>
                    </div>
                    <div class='logistics_content'>
                        <h4>" . convertUtf8($testimonial->name) . "</h4>
                        <h6>" . convertUtf8($testimonial->rank) . "</h6>
                        <p>" . convertUtf8($testimonial->comment) . "</p>
                    </div>
                </div>";
            }
            $testimonialSec .= "</div>";
        } elseif ($version == 'cleaning') {
            $testimonialSec .= "<div class='testimonial-active'>";
            foreach ($testimonials as $key => $testimonial) {
                $testimonialSec .= "<div class='single-testimonial-item'>
                    <div class='testimonial-author-img'>
                        <img data-src='" . url('assets/front/img/testimonials/' . $testimonial->image) . "' class='img-fluid lazy' alt=''>
                    </div>
                    <div class='testimonial-author-details'>
                        <h4>" . convertUtf8($testimonial->name) . " <span>" . convertUtf8($testimonial->rank) . "</span></h4>
                        <p>" . convertUtf8($testimonial->comment) . "</p>
                    </div>
                </div>";
            }
            $testimonialSec .= "</div>";
        }

        return $testimonialSec;
    }
}

if (!function_exists('packagesSection')) {
    function packagesSection($currentLang, $version) {
        if (!empty($currentLang->packages)) {
            $packages = $currentLang->packages()->where('feature', 1)->orderBy('serial_number', 'ASC')->get();
        } else {
            $packages = [];
        }
        $bex = BasicExtra::firstOrFail();

        $packageSec = "";
        if ($version == 'lawyer') {
            $packageSec .= "<div class='pricing_slide pricing-slick'>";
            foreach ($packages as $key => $package) {
                $packageSec .= "<div class='pricing_box text-center' data-gjs-draggable='false' data-gjs-editable='false' data-gjs-removable='false' data-gjs-propagate=" . '["removable","editable","draggable"]' . ">
                <div class='pricing_title'>";
                if (!empty($package->image)) {
                    $packageSec .= "<img class='lazy' data-src='" . url('assets/front/img/packages/' . $package->image) . "' alt=''>";
                }
                $packageSec .= "<h3>" . convertUtf8($package->title) . "</h3>";
                if($bex->recurring_billing == 1) {
                    $packageSec .= "<p>" . ($package->duration == 'monthly' ? __('Monthly') : __('Yearly')) . "</p>";
                }
                $packageSec .= "</div>
                <div class='pricing_price'>
                <h3>" . ($bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : '') . " " . $package->price . " " . ($bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : '') . "</h3>
                </div>
                <div class='pricing_body'>" . replaceBaseUrl($package->description) . "</div>
                <div class='pricing_button'>";
                if ($package->order_status == 1) {
                    $packageSec .= "<a href='" . route('front.packageorder.index', $package->id) . "' class='lawyer_btn'>" . __('Place Order') . "</a>";
                }
                $packageSec .= "</div>
                </div>";
            }
            $packageSec .= "</div>";
        } elseif ($version == 'default' || $version == 'dark') {
            $packageSec .= "<div class='pricing-carousel common-carousel owl-carousel owl-theme'>";
            foreach ($packages as $key => $package) {
                $packageSec .= "<div class='single-pricing-table'>
                <span class='title'>" . convertUtf8($package->title) . "</span>";
                if($bex->recurring_billing == 1) {
                    $packageSec .= "<small>" . ($package->duration == 'monthly' ? __('Monthly') : __('Yearly')) . "</small>";
                }
                $packageSec .= "<div class='price'>
                <h1>" . ($bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : '') . $package->price . ($bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : '') . "</h1>
                </div>
                <div class='features'>" . replaceBaseUrl(convertUtf8($package->description)) . "</div>";

                if ($package->order_status == 1) {
                    $packageSec .= "<a href='" . route('front.packageorder.index', $package->id) . "' class='pricing-btn'>" . __('Place Order') . "</a>";
                }

                $packageSec .= "</div>";
            }
            $packageSec .= "</div>";
        } elseif ($version == 'gym') {
            $packageSec .= "<div class='pricing_slide pricing_slick'>";
            foreach ($packages as $key => $package) {
                $packageSec .= "<div class='pricing_box text-center'>
                <div class='pricing_title'>
                <h3>" . convertUtf8($package->title) . "</h3>";
                if($bex->recurring_billing == 1) {
                    $packageSec .= "<p>" . ($package->duration == 'monthly' ? __('Monthly') : __('Yearly')) . "</p>";
                }
                $packageSec .= "</div>
                <div class='pricing_price'>
                <h3>" . ($bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : '') . $package->price . ($bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : '') . "</h3>
                </div>
                <div class='pricing_body'>" . replaceBaseUrl(convertUtf8($package->description)) . "</div>
                <div class='pricing_button'>";
                if ($package->order_status == 1) {
                    $packageSec .= "<a href='" . route('front.packageorder.index', $package->id) . "' class='finlance_btn'>" . __('Place Order') . "</a>";
                }
                $packageSec .= "</div>
                </div>";
            }
            $packageSec .= "</div>";
        } elseif ($version == 'car') {
            $packageSec .= "<div class='pricing_slide pricing_slick'>";
            foreach ($packages as $key => $package) {
                $packageSec .= "<div class='pricing_box text-center'>
                <div class='pricing_title'>
                <h3>" . convertUtf8($package->title) . "</h3>
                </div>
                <div class='pricing_price'>
                <h2>" . ($bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : '') . $package->price . ($bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : '') . "</h2>";
                if($bex->recurring_billing == 1) {
                    $packageSec .= "<p>" . ($package->duration == 'monthly' ? __('Monthly') : __('Yearly')) . "</p>";
                }
                $packageSec .= "</div>
                <div class='pricing_body'>" . replaceBaseUrl(convertUtf8($package->description)) . "</div>
                <div class='pricing_button'>";
                if ($package->order_status == 1) {
                    $packageSec .= "<a href='" . route('front.packageorder.index', $package->id) . "' class='finlance_btn'>" . __('Place Order') . "</a>";
                }
                $packageSec .= "</div>
                </div>";
            }
            $packageSec .= "</div>";
        } elseif ($version == 'construction') {
            $packageSec .= "<div class='pricing_slide pricing-slick'>";
            foreach ($packages as $key => $package) {
                $packageSec .= "<div class='pricing_box text-center'>
                    <div class='pricing_title'>
                        <h3>" . convertUtf8($package->title) . "</h3>";
                        if($bex->recurring_billing == 1) {
                            $packageSec .= "<p>" . ($package->duration == 'monthly' ? __('Monthly') : __('Yearly')) . "</p>";
                        }
                    $packageSec .= "</div>
                    <div class='pricing_price'>
                        <h3>" . ($bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : '') . " " . $package->price . " " . ($bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : '') . "</h3>
                    </div>
                    <div class='pricing_body'>" . replaceBaseUrl(convertUtf8($package->description)) . "</div>
                    <div class='pricing_button'>";
                    if ($package->order_status == 1) {
                        $packageSec .= "<a href='" . route('front.packageorder.index', $package->id) . "' class='finlance_btn'>" . __('Place Order') . "</a>";
                    }
                    $packageSec .= "</div>
                </div>";
            }
            $packageSec .= "</div>";
        } elseif ($version == 'logistic') {
            $packageSec .= "<div class='pricing_slide pricing-slick'>";
            foreach ($packages as $key => $package) {
                $packageSec .= "<div class='pricing_box text-center'>
                                <div class='pricing_title'>
                                    <h3>" . convertUtf8($package->title) . "</h3>";
                                    if($bex->recurring_billing == 1) {
                                        $packageSec .= "<p>" . ($package->duration == 'monthly' ? __('Monthly') : __('Yearly')) . "</p>";
                                    }
                                $packageSec .= "</div>
                                <div class='pricing_price'>
                                    <h3>" . ($bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : '') . " " . $package->price . " " . ($bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : '') . "</h3>
                                </div>
                                <div class='pricing_body'>" . replaceBaseUrl(convertUtf8($package->description)) . "</div>
                                <div class='pricing_button'>";
                if ($package->order_status == 1) {
                    $packageSec .= "<a href='" . route('front.packageorder.index', $package->id) . "' class='logistics_btn'>" . __('Place Order') . "</a>";
                }
                $packageSec .= "</div>
                            </div>";
            }
            $packageSec .= "</div>";
        } elseif ($version == 'cleaning') {
            $packageSec .= "<div class='price-carousel-active pricing-slick'>";
            foreach ($packages as $key => $package) {
                $packageSec .= "<div class='single-price-item text-center'>
                                <div class='price-heading'>
                                    <h3>" . convertUtf8($package->title) . "</h3>";
                                    if($bex->recurring_billing == 1) {
                                        $packageSec .= "<p>" . ($package->duration == 'monthly' ? __('Monthly') : __('Yearly')) . "</p>";
                                    }
                                $packageSec .= "</div>
                                <h1 class='bg-1' style='background: #" . $package->color . ";'>" . ($bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : '') . $package->price . ($bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : '') . "</h1>
                                <div class='price-cata mb-4'>" . replaceBaseUrl(convertUtf8($package->description)) . "</div>";
                if ($package->order_status == 1) {
                    $packageSec .= "<a href='" . route('front.packageorder.index', $package->id) . "' class='main-btn price-btn'>" . __('Place Order') . "</a>";
                }
                $packageSec .= "</div>";
            }
            $packageSec .= "</div>";
        }

        return $packageSec;
    }
}

if (!function_exists('blogsSection')) {
    function blogsSection($currentLang, $version) {
        if (!empty($currentLang->blogs)) {
            $blogs = $currentLang->blogs()->orderBy('serial_number', 'ASC')->get();
        } else {
            $blogs = [];
        }

        $blogSec = "";
        if ($version == 'lawyer') {
            $blogSec .= "<div class='blog_slide blog-slick'>";
            foreach ($blogs as $key => $blog) {
                $blogSec .= "<div class='grid_item' data-gjs-draggable='false' data-gjs-editable='false' data-gjs-removable='false' data-gjs-propagate=" . '["removable","editable","draggable"]' . ">
                <div class='grid_inner_item'>
                <div class='lawyer_img'>
                <a href='" . route('front.blogdetails', [$blog->slug]) . "'><img data-src='" . url('assets/front/img/blogs/' . $blog->main_image) . "' class='img-fluid lazy' alt=''></a>
                </div>
                <div class='lawyer_content'>
                <div class='post_meta'>";

                $blogDate = \Carbon\Carbon::parse($blog->created_at)->locale("$currentLang->code");
                $blogDate = $blogDate->translatedFormat('d M. Y');

                $blogSec .= "<span><i class='far fa-user'></i><a href='#'>" . __('By') . " " . __('Admin') . "</a></span>
                <span><i class='far fa-calendar-alt'></i><a href='#'>" . $blogDate . "</a></span>
                </div>
                <h3 class='post_title'><a href='" . route('front.blogdetails', [$blog->slug]) . "'>" . (strlen($blog->title) > 40 ? mb_substr($blog->title, 0, 40, 'utf-8') . '...' : $blog->title) . "</a></h3>
                <p>" . (strlen(strip_tags($blog->content)) > 100 ? mb_substr(strip_tags($blog->content), 0, 100, 'utf-8') . '...' : strip_tags($blog->content)) . "</p>
                <a href='" . route('front.blogdetails', [$blog->slug]) . "' class='btn_link'>" . __('Read More') . "</a>
                </div>
                </div>
                </div>";
            }
            $blogSec .= "</div>";
        } elseif ($version == 'default' || $version == 'dark') {
            $blogSec .= "<div class='blog-carousel common-carousel owl-carousel owl-theme'>";
            foreach ($blogs as $key => $blog) {
                $blogSec .= "<div class='single-blog'>
                <div class='blog-img-wrapper'>
                <img src='" . url('assets/front/img/blogs/' . $blog->main_image) . "' alt=''>
                </div>
                <div class='blog-txt'>";

                $blogDate = \Carbon\Carbon::parse($blog->created_at)->locale("$currentLang->code");
                $blogDate = $blogDate->translatedFormat('jS F, Y');


                $blogSec .= "<p class='date'><small>" . __('By') .  " <span class='username'>" . __('Admin') . "</span></small> | <small>" . $blogDate . "</small> </p>

                <h4 class='blog-title'><a href='" . route('front.blogdetails', [$blog->slug]) . "'>" . (strlen($blog->title) > 40 ? mb_substr($blog->title, 0, 40, 'utf-8') . '...' : $blog->title) . "</a></h4>


                <p class='blog-summary'>" . (strlen(strip_tags($blog->content)) > 100 ? mb_substr(strip_tags($blog->content), 0, 100, 'utf-8') . '...' : strip_tags($blog->content)) . "</p>


                <a href='" . route('front.blogdetails', [$blog->slug]) . "' class='readmore-btn'><span>" . __('Read More') . "</span></a>

                </div>
                </div>";
            }
            $blogSec .= "</div>";
        } elseif ($version == 'gym') {
            $blogSec .= "<div class='blog_slide blog_slick'>";
            foreach ($blogs as $key => $blog) {
                $blogSec .= "<div class='grid_item'>
                <div class='grid_inner_item'>
                <div class='finlance_img'>
                <a href='" . route('front.blogdetails', [$blog->slug]) . "'><img data-src='" . url('assets/front/img/blogs/' . $blog->main_image) . "' class='img-fluid lazy' alt=''></a>
                <div class='blog-overlay'>
                <div class='finlance_content'>";

                $blogDate = \Carbon\Carbon::parse($blog->created_at)->locale("$currentLang->code");
                $blogDate = $blogDate->translatedFormat('jS F, Y');

                $blogSec .= "<div class='post_meta'>
                <span><i class='far fa-user'></i><a href='#'>" . __('By') . " " . __('Admin') . "</a></span>
                <span><i class='far fa-calendar-alt'></i><a href='#'>" . $blogDate . "</a></span>
                </div>
                <h3 class='post_title'><a href='" . route('front.blogdetails', [$blog->slug]) . "'>" . (strlen($blog->title) > 40 ? mb_substr($blog->title, 0, 40, 'utf-8') . '...' : $blog->title) . "</a></h3>
                <p>" . (strlen(strip_tags($blog->content)) > 100 ? mb_substr(strip_tags($blog->content), 0, 100, 'utf-8') . '...' : strip_tags($blog->content)) . "</p>
                <a href='" . route('front.blogdetails', [$blog->slug]) . "' class='btn_link'>" . __('Read More') . "</a>
                </div>
                </div>
                </div>
                </div>
                </div>";
            }
            $blogSec .= "</div>";
        } elseif ($version == 'car') {
            $blogSec .= "<div class='blog_slide'>";
            foreach ($blogs as $key => $blog) {
                $blogSec .= "<div class='grid_item'>
                <div class='grid_inner_item'>
                <div class='row align-items-end no-gutters'>
                <div class='col-lg-6'>
                <div class='finlance_content'>";

                $blogDate = \Carbon\Carbon::parse($blog->created_at)->locale("$currentLang->code");
                $blogDate = $blogDate->translatedFormat('jS F, Y');

                $blogSec .= "<div class='post_meta'>
                <span><i class='far fa-user'></i><a href='#'>" . __('By') . " " . __('Admin') . "</a></span>
                <span><i class='far fa-calendar-alt'></i><a href='#'>" . $blogDate . "</a></span>
                </div>
                <h3 class='post_title'><a href='" . route('front.blogdetails', [$blog->slug]) . "'>" . (strlen($blog->title) > 40 ? mb_substr($blog->title, 0, 40, 'utf-8') . '...' : $blog->title) . "</a></h3>
                <p>" . (strlen(strip_tags($blog->content)) > 100 ? mb_substr(strip_tags($blog->content), 0, 100, 'utf-8') . '...' : strip_tags($blog->content)) . "</p>
                <a href='" . route('front.blogdetails', [$blog->slug]) . "' class='finlance_btn'>" . __('Read More') . "</a>
                </div>
                </div>
                <div class='col-lg-6'>
                <div class='finlance_img'>
                <a href='" . route('front.blogdetails', [$blog->slug]) . "'><img data-src='" . url('assets/front/img/blogs/' . $blog->main_image) . "' class='img-fluid lazy' alt=''></a>
                </div>
                </div>
                </div>
                </div>
                </div>";
            }
            $blogSec .= "</div>";
        } elseif ($version == 'construction') {
            $blogSec .= "<div class='blog_slide blog-slick'>";
            foreach ($blogs as $key => $blog) {
                $blogSec .= "<div class='grid_item'>
                    <div class='grid_inner_item'>
                        <div class='finlance_img'>
                            <a href='" . route('front.blogdetails', [$blog->slug]) . "'><img data-src='" . url('assets/front/img/blogs/' . $blog->main_image) . "' class='img-fluid lazy' alt=''></a>
                        </div>
                        <div class='finlance_content'>
                            <div class='post_meta'>";

    $blogDate = \Carbon\Carbon::parse($blog->created_at)->locale("$currentLang->code");
    $blogDate = $blogDate->translatedFormat('d M. Y');

    $blogSec .= "<span><i class='far fa-user'></i><a href='#'>" . __('By') . " " . __('Admin') . "</a></span>
                                <span><i class='far fa-calendar-alt'></i><a href='#'>" . $blogDate . "</a></span>
                            </div>
                            <h3 class='post_title'><a href='" . route('front.blogdetails', [$blog->slug]) . "'>" . (strlen($blog->title) > 40 ? mb_substr($blog->title, 0, 40, 'utf-8') . '...' : $blog->title) . "</a></h3>
                            <a href='" . route('front.blogdetails', [$blog->slug]) . "' class='btn_link'>" . __('Read More') . "</a>
                        </div>
                    </div>
                </div>";
            }
            $blogSec .= "</div>";
        } elseif ($version == 'logistic') {
            $blogSec .= "<div class='blog_slide blog-slick'>";
            foreach ($blogs as $key => $blog) {
                $blogSec .= "<div class='grid_item'>
                                <div class='grid_inner_item'>
                                    <div class='logistics_img'>
                                        <a href='" . route('front.blogdetails', [$blog->slug]) . "'><img data-src='" . url('assets/front/img/blogs/' . $blog->main_image) . "' class='img-fluid lazy' alt=''></a>
                                    </div>
                                    <div class='logistics_content'>
                                        <div class='post_meta'>";

                $blogDate = \Carbon\Carbon::parse($blog->created_at)->locale("$currentLang->code");
                $blogDate = $blogDate->translatedFormat('d M. Y');

                $blogSec .= "<span><i class='far fa-user'></i><a href='#'>" . __('By') . " " . __('Admin') . "</a></span>
                                            <span><i class='far fa-calendar-alt'></i><a href='#'>" . $blogDate . "</a></span>
                                        </div>
                                        <h3 class='post_title'><a href='" . route('front.blogdetails', [$blog->slug]) . "'>" . (strlen($blog->title) > 40 ? substr($blog->title, 0, 40) . '...' : $blog->title) . "</a></h3>
                                        <a href='" . route('front.blogdetails', [$blog->slug]) . "' class='btn_link'>" . __('Read More') . "</a>
                                    </div>
                                </div>
                            </div>";
            }
            $blogSec .= "</div>";
        } elseif ($version == 'cleaning') {
            $blogSec .= "<div class='blog-carousel-active blog-slick'>";
            foreach ($blogs as $key => $blog) {

                $blogSec .= "<div class='single-blog-item'>
                                <div class='single-blog-img'>
                                    <img class='lazy' data-src='" . url('assets/front/img/blogs/' . $blog->main_image) . "' alt=''>
                                </div>
                                <div class='single-blog-details'>";

                $blogDate = \Carbon\Carbon::parse($blog->created_at)->locale("$currentLang->code");
                $blogDate = $blogDate->translatedFormat('d M. Y');

                $blogSec .= "<span><i class='fa fa-arrow-right'></i>" . __('By') . " " . __('Admin') . "</span>
                                    <span><i class='fa fa-arrow-right'></i>" . $blogDate . "</span>
                                    <h4>" . (strlen($blog->title) > 40 ? mb_substr($blog->title, 0, 40, 'utf-8') . '...' : $blog->title) . "</h4>
                                    <p>" . (strlen(strip_tags($blog->content)) > 100 ? mb_substr(strip_tags($blog->content), 0, 100, 'utf-8') . '...' : strip_tags($blog->content)) . "</p>
                                    <a href='" . route('front.blogdetails', [$blog->slug]) . "' class='blog-btn'>" . __('Read More') . " <i class='fa fa-arrow-right'></i></a>
                                </div>
                            </div>";
            }
            $blogSec .= "</div>";
        } elseif ($version == 'ecommerce') {
            $blogSec .= "<div class='blog-slide'>";
                foreach ($blogs as $key => $blog) {
                    $blogSec .= "<div class='blog-item mb-40'>
                        <div class='post-thumb'>
                            <img class='lazy w-100' data-src='" . asset('assets/front/img/blogs/'.$blog->main_image) . "' alt=''>
                            <a href='#' class='cat'>" . (!empty($blog->bcategory) ? $blog->bcategory->name : '') . "</a>
                        </div>
                        <div class='entry-content'>
                            <h3 class='title'><a href='" . route('front.blogdetails', [$blog->slug, $blog->id]) . "'>" . (strlen($blog->title) > 40 ? mb_substr($blog->title, 0, 40, 'utf-8') . '...' : $blog->title) . "</a></h3>
                            <div class='post-meta'>
                                <ul>";
                                    
                                    $blogDate = \Carbon\Carbon::parse($blog->created_at)->locale($currentLang->code);
                                    $blogDate = $blogDate->translatedFormat('jS F, Y');
                                    
                                    $blogSec .= "<li><span><a href='#'>" . $blogDate . "</a></span></li>
                                    <li><span><a href='#'>By " . __('Admin') . "</a></span></li>
                                </ul>
                            </div>
                        </div>
                    </div>";
                }
            $blogSec .= "</div>";
        }

        return $blogSec;
    }
}

if (!function_exists('approachSection')) {
    function approachSection($currentLang, $version) {
        if (!empty($currentLang->points)) {
            $points = $currentLang->points()->orderBy('serial_number', 'ASC')->get();
        } else {
            $points = [];
        }

        $approachsec = "";
        if ($version == 'lawyer') {
            $approachsec .= "<div class='lawyer_icon_box'>";
            foreach ($points as $key => $point) {
                $approachsec .= "<div class='icon_list d-flex' data-gjs-draggable='false' data-gjs-editable='false' data-gjs-removable='false' data-gjs-propagate=" . '["removable","editable","draggable"]' . ">
                <div class='icon'>
                <i class='" . $point->icon . "'></i>
                </div>
                <div class='text'>
                <h4>" . convertUtf8($point->title) . "</h4>
                <p>";
                if (strlen($point->short_text) > 150) {
                    $approachsec .= mb_substr($point->short_text,0,150,'utf-8') . "<span style='display: none;'>" . mb_substr($point->short_text,150, null,'utf-8') . "</span>
                    <a href='#' class='see-more'>" . __('see more') . "...</a>";
                } else {
                    $approachsec .= convertUtf8($point->short_text);
                }
                $approachsec .= "</p>
                </div>
                </div>";
            }
            $approachsec .= "</div>";
        } elseif ($version == 'default' || $version == 'dark') {
            $approachsec .= "<ul class='approach-lists'>";
            foreach ($points as $key => $point) {
                $approachsec .= "<li class='single-approach' data-gjs-draggable='false' data-gjs-editable='false' data-gjs-removable='false' data-gjs-propagate=" . '["removable","editable","draggable"]' . ">
                <div class='approach-icon-wrapper'><i class='" . $point->icon . "'></i></div>
                <div class='approach-text'>
                <h4>" . convertUtf8($point->title) . "</h4>
                <p>";
                if (strlen($point->short_text) > 150) {
                    $approachsec .= mb_substr($point->short_text,0,150,'utf-8') . "<span style='display: none;'>" . mb_substr($point->short_text,150,null,'utf-8') . "</span>
                    <a href='#' class='see-more'>" . __('see more') . "...</a>";
                } else {
                    $approachsec .= $point->short_text;
                }
                $approachsec .= "</p>
                </div>
                </li>";
            }
            $approachsec .= "</ul>";
        } elseif ($version == 'gym') {
            foreach ($points as $key => $point) {
                $approachsec .= "<div class='icon_list d-flex'>
                <div class='icon'>
                <i class='" . $point->icon . "'></i>
                </div>
                <div class='text'>
                <h3>" . convertUtf8($point->title) . "</h3>
                <p>";
                if (strlen($point->short_text) > 150) {
                    $approachsec .= mb_substr($point->short_text,0,150,'utf-8') . "<span style='display: none;'>" . mb_substr($point->short_text,150, null,'utf-8') . "</span>
                    <a href='#' class='see-more'>" . __('see more') . "...</a>";
                } else {
                    $approachsec .= convertUtf8($point->short_text);
                }
                $approachsec .= "</p>
                </div>
                </div>";
            }
        } elseif ($version == 'car') {
            $approachsec .= "<div class='finlance_icon_box'>";
            foreach ($points as $key => $point) {
                $approachsec .= "<div class='icon_list d-flex'>
                    <div class='icon'>
                        <i class='" . $point->icon . "'></i>
                    </div>
                    <div class='text'>
                        <h4>" . convertUtf8($point->title) . "</h4>
                        <p>";
                            if (strlen($point->short_text) > 150) {
                                $approachsec .= mb_substr($point->short_text,0,150,'utf-8') . "<span style='display: none;'>" . mb_substr($point->short_text,150, null,'utf-8') . "</span>
                                <a href='#' class='see-more'>" . __('see more') . "...</a>";
                            } else {
                                $approachsec .= convertUtf8($point->short_text);
                            }
                        $approachsec .= "</p>
                    </div>
                </div>";
            }
            $approachsec .= "</div>";
        } elseif ($version == 'construction') {
            $approachsec .= "<div class='finlance_icon_box'>";
            foreach ($points as $key => $point) {
                $approachsec .= "<div class='icon_list d-flex'>
                    <div class='icon'>
                        <i class='" . $point->icon . "'></i>
                    </div>
                    <div class='text'>
                        <h4>" . convertUtf8($point->title) . "</h4>
                        <p>";
                            if (strlen($point->short_text) > 150) {
                                $approachsec .= mb_substr($point->short_text,0,150,'utf-8') . "<span style='display: none;'>" . mb_substr($point->short_text,150, null,'utf-8') . "</span>
                                <a href='#' class='see-more'>" . __('see more') . "...</a>";
                            } else {
                                $approachsec .= convertUtf8($point->short_text);
                            }
                        $approachsec .= "</p>
                    </div>
                </div>";
            }
            $approachsec .= "</div>";
        } elseif ($version == 'logistic') {
            $approachsec .= "<div class='logistics_icon_box'>";
            foreach ($points as $key => $point) {
                $approachsec .= "<div class='icon_list d-flex'>
                    <div class='icon'>
                        <i class='" . $point->icon . "'></i>
                    </div>
                    <div class='text'>
                        <h4>" . convertUtf8($point->title) . "</h4>
                        <p>";
                            if (strlen($point->short_text) > 150) {
                                $approachsec .= mb_substr($point->short_text,0,150,'utf-8') . "<span style='display: none;'>" . mb_substr($point->short_text,150, null,'utf-8') . "</span>
                                <a href='#' class='see-more'>" . __('see more') . "...</a>";
                            } else {
                                $approachsec .= convertUtf8($point->short_text);
                            }
                        $approachsec .= "</p>
                    </div>
                </div>";
            }
            $approachsec .= "</div>";
        } elseif ($version == 'cleaning') {
            foreach ($points as $key => $point) {
                $approachsec .= "<div class='single-about-item'>
                    <p  class='bg-1' style='color: #" . $point->color . "; background: #" . $point->color . "2a;'><span><i class='" . $point->icon . "'></i></span></p>
                    <h4>" . convertUtf8($point->title) . "
                    <span>";
                        if (strlen($point->short_text) > 150) {
                            $approachsec .= mb_substr($point->short_text,0,150,'utf-8') . "<span style='display: none;'>" . mb_substr($point->short_text,150, null,'utf-8') . "</span>
                            <a href='#' class='see-more'>" . __('see more') . "...</a>";
                        } else {
                            $approachsec .= convertUtf8($point->short_text);
                        }
                    $approachsec .= "</span>
                    </h4>
                </div>";
            }
        }

        return $approachsec;
    }
}

if (!function_exists('partnerSection')) {
    function partnerSection($currentLang, $version) {
        if (!empty($currentLang->partners)) {
            $partners = $currentLang->partners()->orderBy('serial_number', 'ASC')->get();
        } else {
            $partners = [];
        }

        $partnerSec = "";
        if ($version == 'lawyer') {
            $partnerSec .= "<div class='partner_slide'>";
            foreach ($partners as $key => $partner) {
                $partnerSec .= "<div class='single_partner'>
                <a href='" . $partner->url . "'><img data-src='" . url('assets/front/img/partners/' . $partner->image) . "' class='img-fluid lazy' alt=''></a>
                </div>";
            }
            $partnerSec .= "</div>";
        } elseif ($version == 'default' || $version == 'dark') {
            $partnerSec .= "<div class='partner-carousel common-carousel owl-carousel owl-theme'>";
            foreach ($partners as $key => $partner) {
                $partnerSec .= "<a class='single-partner-item d-block' href='" . $partner->url . "' target='_blank'>
                <div class='outer-container'>
                <div class='inner-container'>
                <img class='lazy' data-src='" . url('assets/front/img/partners/' . $partner->image) . "' alt=''>
                </div>
                </div>
                </a>";
            }
            $partnerSec .= "</div>";
        } elseif ($version == 'gym') {
            $partnerSec .= "<div class='partner_slide'>";
            foreach ($partners as $key => $partner) {
                $partnerSec .= "<div class='single_partner'>
                <a href='" . $partner->url . "' target='_blank'><img data-src='" . url('assets/front/img/partners/' . $partner->image) . "' class='img-fluid lazy' alt=''></a>
                </div>";
            }
            $partnerSec .= "</div>";
        } elseif ($version == 'car') {
            $partnerSec .= "<div class='partner_slide'>";
            foreach ($partners as $key => $partner) {
                $partnerSec .= "<div class='single_partner'>
                    <a href='" . $partner->url . "' target='_blank'><img data-src='" . url('assets/front/img/partners/' . $partner->image) . "' class='img-fluid lazy' alt=''></a>
                </div>";
            }
            $partnerSec .= "</div>";
        } elseif ($version == 'construction') {
            $partnerSec .= "<div class='partner_slide'>";
            foreach ($partners as $key => $partner) {
                $partnerSec .= "<div class='single_partner'>
                    <a href='" . $partner->url . "'><img data-src='" . url('assets/front/img/partners/' . $partner->image) . "' class='img-fluid lazy' alt=''></a>
                </div>";
            }
            $partnerSec .= "</div>";
        } elseif ($version == 'logistic') {
            $partnerSec .= "<div class='partner_slide'>";
            foreach ($partners as $key => $partner) {
                $partnerSec .= "<div class='single_partner'>
                    <a href='" . $partner->url . "'><img data-src='" . url('assets/front/img/partners/' . $partner->image) . "' class='img-fluid lazy' alt=''></a>
                </div>";
            }
            $partnerSec .= "</div>";
        } elseif ($version == 'cleaning') {
            $partnerSec .= "<div class='row'>
                <div class='col-lg-12'>
                    <div class='brand-container brand-carousel-active'>";
                    foreach ($partners as $key => $partner) {
                        $partnerSec .= "<div class='single-brand-logo'>
                            <a class='d-block' href='" . $partner->url . "' target='_blank'><img data-src='" . url('assets/front/img/partners/' . $partner->image) . "' class='img-fluid lazy' alt=''></a>
                        </div>";
                    }
                    $partnerSec .= "</div>
                </div>
            </div>";
        } elseif ($version == 'ecommerce') {
            $partnerSec .= "<div class='custom-container'>
                <div class='sponsor-wrapper' style='padding: 60px 0px;'>
                    <div class='row sponsor-slide'>";
                        foreach ($partners as $key => $partner) {
                            $partnerSec .= "<div class='sponsor-item col-3 mx-0'>
                                <a href='" . $partner->url . "' target='_blank'><img data-src='" . asset('assets/front/img/partners/'.$partner->image) . "' class='lazy w-100' alt=''></a>
                            </div>";
                        }
                    $partnerSec .= "</div>
                </div>
            </div>";
        }

        return $partnerSec;
    }
}


if (!function_exists('fprodSection')) {
    function fprodSection($currentLang, $version) {
        $fprodsec = '';
        if (!empty($currentLang->products)) {
            $fproducts = Product::where('status', 1)->where('is_feature',1)->where('language_id',$currentLang->id)->orderBy('id', 'DESC')->get();
            $products = Product::where('status', 1)->where('language_id',$currentLang->id)->orderBy('id', 'DESC')->limit(10)->get();
        } else {
            $fproducts = [];
            $products = [];
        }
        $bex = $currentLang->basic_extra;

        $fprodsec = "<div class='custom-container'>
                        <div class='row'>
                            <div class='col-lg-12'>
                                <div class='featured-tabs'>
                                    <ul class='nav nav-tabs'>
                                        <li class='nav-item'>
                                        <a class='nav-link active' data-toggle='tab' href='#cat1'>" . __('Featured') . "</a>
                                        </li>
                                        <li class='nav-item'>
                                        <a class='nav-link' data-toggle='tab' href='#cat3'>" . __('New Arrivals') . "</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class='featured-arrows'></div>
                        <div class='tab-content'>
                            <div id='cat1' class='tab-pane active'>
                                <div class='featured-slide'>";
                                    
                                    foreach ($fproducts as $product) {
                                        $fprodsec .= "<div class='shop-item'>
                                            <a class='shop-img' href='" . route('front.product.details',$product->slug) . "'>
                                                <img class='lazy w-100' data-src='" . asset('assets/front/img/product/featured/'.$product->feature_image) . "' alt=''>
                                            </a>
                                            <div class='shop-info'>";
                                                if ($bex->product_rating_system == 1 && $bex->catalog_mode == 0) {
                                                    $fprodsec .= "<div class='rate'>
                                                        <div class='rating' style='width:" . $product->rating * 20 . "%'></div>
                                                    </div>";
                                                }
                                                $fprodsec .= "<h3><a href='" . route('front.product.details',$product->slug) . "'>" . (strlen($product->title) > 40 ? mb_substr($product->title,0,40,'utf-8') . '...' : $product->title) . "</a></h3>";

                                                if ($bex->catalog_mode == 0) {
                                                    $fprodsec .= "<div class='shop-price'>
                                                        <p class='price'>";
                                                            if (!empty($product->previous_price)) {
                                                                $fprodsec .= "<span class='main-price'>" . 
                                                                    ($bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : '') . $product->previous_price . ($bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : '')
                                                                . "</span>";
                                                            }
                                                            $fprodsec .= "<span class='off-price'>" . ($bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : '') .  $product->current_price .  ($bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : '') . "</span>
                                                        </p>
                                                    </div>";
                                                }
                                            $fprodsec .= "</div>
                                        </div>";
                                    }
                                $fprodsec .= "</div>
                            </div>
                            <div id='cat3' class='tab-pane fade'>
                                <div class='featured-slide'>";
                                    foreach ($products as $product) {
                                        $fprodsec .= "<div class='shop-item'>
                                            <a class='shop-img' href='" . route('front.product.details',$product->slug) . "'>
                                                <img class='lazy' data-src='" . asset('assets/front/img/product/featured/'.$product->feature_image) . "' alt=''>
                                            </a>
                                            <div class='shop-info'>";
                                                if ($bex->product_rating_system == 1 && $bex->catalog_mode == 0) {
                                                    $fprodsec .= "<div class='rate'>
                                                        <div class='rating' style='width:" . $product->rating * 20 . "%'></div>
                                                    </div>";
                                                }
                                                $fprodsec .= "<h3><a href='" . route('front.product.details',$product->slug) . "'>" . (strlen($product->title) > 40 ? mb_substr($product->title,0,40,'utf-8') . '...' : $product->title) . "</a></h3>";
                                                if ($bex->catalog_mode == 0) {
                                                    $fprodsec .= "<div class='shop-price'>
                                                        <p class='price'>";
                                                            if (!empty($product->previous_price)) {
                                                                $fprodsec .= "<span class='main-price'>" .
                                                                ($bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : '') . $product->previous_price . ($bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : '')
                                                                . "</span>";
                                                            }
                                                            $fprodsec .= "<span class='off-price'>" . ($bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : '') . $product->current_price . ($bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : '') . 
                                                            "</span>
                                                        </p>
                                                    </div>";
                                                }
                                            $fprodsec .= "</div>
                                        </div>";
                                    }
                                $fprodsec .= "</div>
                            </div>
                            
                        </div>
                    </div>";

        return $fprodsec;
    }
}


if (!function_exists('newsletterSection')) {
    function newsletterSection($currentLang, $version) {
        $bs = $currentLang->basic_setting;

        $newsletterSec = '';
        $csrf = csrf_field();
        
        $newsletterSec = "<div class='newsletter-form'>
            <form class='footer-newsletter' id='footerSubscribeForm' action='" . route('front.subscribe') . "' method='post'>"
            . $csrf . 
            "<div class='form_group'>
                    <input type='email' class='form_control' placeholder='" . __('Enter Email Address') . "' name='email' value='' required>
                    <button class='main-btn'>" . __('Subscribe') . "</button>
                </div>
            </form>
        </div>";

        return $newsletterSec;
    }
}


if (!function_exists('fProdCatSection')) {
    function fProdCatSection($currentLang, $version) {
        $pcatsec = '';
        if (!empty($currentLang->pcategories)) {
            $fcategories = Pcategory::where('status', 1)->where('language_id',$currentLang->id)->where('is_feature',1)->get();
        } else {
            $fcategories = [];
        }

        $pcatsec = "<div class='categories-slide'>";
            foreach ($fcategories as $category) {
                $pcatsec .= "<a href='" . route('front.product', ['category_id' => $category->id]) . "' class='categories-item'>
                    <div class='cat-img'>
                        <img src='" . asset('assets/front/img/product/categories/' . $category->image) . "' alt=''>
                    </div>
                    <div class='cat-content'>
                        <h5>" . $category->name . "</h5>
                    </div>
                </a>";
            }
        $pcatsec .= "</div>";

        return $pcatsec;
    }
}


if (!function_exists('hProdCatSection')) {
    function hProdCatSection($currentLang, $version) {
        $bex = BasicExtra::firstOrFail();
        $hcatsec = '';
        if ($currentLang->pcategories()->where('status', 1)->where('language_id',$currentLang->id)->where('products_in_home',1)->count() > 0) {
            $hcategories = Pcategory::where('status', 1)->where('language_id',$currentLang->id)->where('products_in_home',1)->get();
        } else {
            $hcategories = [];
        }


        foreach ($hcategories as $category) {

            $hcatsec .= "<section class='product-categories'>
                <div class='custom-container'>
                    <div class='section-header mb-40'>
                        <div class='row align-items-center'>
                            <div class='col-lg-4 col-md-12 col-sm-12'>
                                <div class='section-title'>
                                    <h2>" . $category->name . "</h2>
                                </div>
                            </div>
                            <div class='col-lg-8'>
                                <div class='section-img float-right'>
                                    <img src='" . asset('assets/front/img/product/categories/' . $category->image) . "' alt=''>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class='shop-categories-slide'>";

                        if ($category->products()->count() > 0) {
                            foreach ($category->products as $product) {
                                $hcatsec .= "<div class='shop-item'>
                                    <a class='shop-img' href='" . route('front.product.details',$product->slug) . "'>
                                        <img class='lazy w-100' data-src='" . asset('assets/front/img/product/featured/'.$product->feature_image) . "' alt=''>
                                    </a>
                                    <div class='shop-info'>";
                                        if ($bex->product_rating_system == 1 && $bex->catalog_mode == 0) {
                                            $hcatsec .= "<div class='rate'>
                                                <div class='rating' style='width:" . $product->rating * 20 . "%'></div>
                                            </div>";
                                        }
                                        $hcatsec .= "<h3><a href='" . route('front.product.details',$product->slug) . "'>" . (strlen($product->title) > 40 ? mb_substr($product->title,0,40,'utf-8') . '...' : $product->title) . "</a></h3>";
                                        if ($bex->catalog_mode == 0) {
                                            $hcatsec .= "<div class='shop-price'>
                                                <p class='price'>";
                                                    if (!empty($product->previous_price)) {
                                                        $hcatsec .= "<span class='main-price'>" .
                                                        ($bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : '' ) . $product->previous_price . ( $bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : '' ) . 
                                                        "</span>";
                                                    }
                                                    $hcatsec .= "<span class='off-price'>" . 
                                                        ($bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : '' ) . $product->current_price . ( $bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : '' ) . 
                                                    "</span>";
                                                $hcatsec .= "</p>
                                            </div>";
                                        }
                                    $hcatsec .= "</div>
                                </div>";
                            }
                        }
                    $hcatsec .= "</div>
                </div>
            </section>";

        }

        return $hcatsec;
    }
}