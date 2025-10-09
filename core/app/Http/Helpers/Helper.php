<?php

use App\BasicExtra;
use App\Page;
use App\PaymentGateway;

if (! function_exists('setEnvironmentValue')) {
    function setEnvironmentValue(array $values)
    {

        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);

        if (count($values) > 0) {
            foreach ($values as $envKey => $envValue) {

                $str .= "\n"; // In case the searched variable is in the last line without \n
                $keyPosition = strpos($str, "{$envKey}=");
                $endOfLinePosition = strpos($str, "\n", $keyPosition);
                $oldLine = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);

                // If key does not exist, add it
                if (!$keyPosition || !$endOfLinePosition || !$oldLine) {
                    $str .= "{$envKey}={$envValue}\n";
                } else {
                    $str = str_replace($oldLine, "{$envKey}={$envValue}", $str);
                }
            }
        }

        $str = substr($str, 0, -1);
        if (!file_put_contents($envFile, $str)) return false;
        return true;
    }
}


if (! function_exists('convertUtf8')) {
    function convertUtf8($value)
    {
        return mb_detect_encoding($value, mb_detect_order(), true) === 'UTF-8' ? $value : mb_convert_encoding($value, 'UTF-8');
    }
}


if (! function_exists('make_slug')) {
    function make_slug($string)
    {
        $slug = preg_replace('/\s+/u', '-', trim($string));
        $slug = str_replace("/", "", $slug);
        $slug = str_replace("?", "", $slug);
        return $slug;
    }
}


if (! function_exists('make_input_name')) {
    function make_input_name($string)
    {
        return preg_replace('/\s+/u', '_', trim($string));
    }
}


if (! function_exists('serviceCategory')) {
    function serviceCategory()
    {
        $hbex = BasicExtra::first();
        if ($hbex->service_category == 1) {
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('slug_create')) {
    function slug_create($val)
    {
        $slug = preg_replace('/\s+/u', '-', trim($val));
        $slug = str_replace("/", "", $slug);
        $slug = str_replace("?", "", $slug);
        return $slug;
    }
}


if (!function_exists('getHref')) {
    function getHref($link)
    {
        $href = "#";

        if ($link["type"] == 'home') {
            $href = route('front.index');
        } else if ($link["type"] == 'services' || $link["type"] == 'services-megamenu') {
            $href = route('front.services');
        } else if ($link["type"] == 'packages') {
            $href = route('front.packages');
        } else if ($link["type"] == 'portfolios' || $link["type"] == 'portfolios-megamenu') {
            $href = route('front.portfolios');
        } else if ($link["type"] == 'team') {
            $href = route('front.team');
        } else if ($link["type"] == 'career') {
            $href = route('front.career');
        } else if ($link["type"] == 'courses' || $link["type"] == 'courses-megamenu') {
            $href = route('courses');
        } else if ($link["type"] == 'events' || $link["type"] == 'events-megamenu') {
            $href = route('front.events');
        } else if ($link["type"] == 'causes' || $link["type"] == 'causes-megamenu') {
            $href = route('front.causes');
        } else if ($link["type"] == 'knowledgebase') {
            $href = route('front.knowledgebase');
        } else if ($link["type"] == 'calendar') {
            $href = route('front.calendar');
        } else if ($link["type"] == 'gallery') {
            $href = route('front.gallery');
        } else if ($link["type"] == 'faq') {
            $href = route('front.faq');
        } else if ($link["type"] == 'products' || $link["type"] == 'products-megamenu') {
            $href = route('front.product');
        } else if ($link["type"] == 'cart') {
            $href = route('front.cart');
        } else if ($link["type"] == 'checkout') {
            $href = route('front.checkout');
        } else if ($link["type"] == 'blogs' || $link["type"] == 'blogs-megamenu') {
            $href = route('front.blogs');
        } else if ($link["type"] == 'rss') {
            $href = route('front.rss');
        } else if ($link["type"] == 'feedback') {
            $href = route('feedback');
        } else if ($link["type"] == 'contact') {
            $href = route('front.contact');
        } else if ($link["type"] == 'custom') {
            if (empty($link["href"])) {
                $href = "#";
            } else {
                $href = $link["href"];
            }
        } else {
            $pageid = (int)$link["type"];
            $page = Page::find($pageid);
            if (!empty($page)) {
                $href = route('front.dynamicPage', [$page->slug]);
            } else {
                $href = '#';
            }
        }

        return $href;
    }
}



if (!function_exists('create_menu')) {
    function create_menu($arr)
    {
        echo '<ul style="z-index: 0;">';
        foreach ($arr["children"] as $el) {

            // determine if the class is 'submenus' or not
            $class = null;
            if (array_key_exists("children", $el)) {
                $class = 'class="submenus"';
            }


            // determine the href
            $href = getHref($el);


            echo '<li ' . $class . '>';
            echo '<a  href="' . $href . '" target="' . $el["target"] . '">' . $el["text"] . '</a>';
            if (array_key_exists("children", $el)) {
                create_menu($el);
            }
            echo '</li>';
        }
        echo '</ul>';
    }
}



if (!function_exists('hex2rgb')) {
    function hex2rgb($colour)
    {
        if ($colour[0] == '#') {
            $colour = substr($colour, 1);
        }
        if (strlen($colour) == 6) {
            list($r, $g, $b) = array($colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5]);
        } elseif (strlen($colour) == 3) {
            list($r, $g, $b) = array($colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2]);
        } else {
            return false;
        }
        $r = hexdec($r);
        $g = hexdec($g);
        $b = hexdec($b);
        return array('red' => $r, 'green' => $g, 'blue' => $b);
    }
}


if (!function_exists('onlyDigitalItemsInCart')) {
    function onlyDigitalItemsInCart()
    {
        $cart = session()->get('cart');

        if (!empty($cart)) {
            foreach ($cart as $key => $cartItem) {
                if (array_key_exists('type', $cartItem) && $cartItem['type'] != 'digital') {
                    return false;
                }
            }
        }

        return true;
    }
}


if (!function_exists('containsDigitalItemsInCart')) {
    function containsDigitalItemsInCart()
    {
        $cart = session()->get('cart');

        if (!empty($cart)) {
            foreach ($cart as $key => $cartItem) {
                if (array_key_exists('type', $cartItem) && $cartItem['type'] == 'digital') {
                    return true;
                }
            }
        }

        return false;
    }
}


if (!function_exists('onlyDigitalItems')) {
    function onlyDigitalItems($order)
    {
        $oitems = $order->orderitems;

        foreach ($oitems as $key => $oitem) {
            if ($oitem->product->type != 'digital') {
                return false;
            }
        }

        return true;
    }
}


if (!function_exists('containsDigitalItem')) {
    function containsDigitalItem($order)
    {
        $oitems = $order->orderitems;

        foreach ($oitems as $key => $oitem) {
            if ($oitem->product->type == 'digital') {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('cartLength')) {
    function cartLength()
    {
        $length = 0;
        if (session()->has('cart') && !empty(session()->get('cart'))) {
            $cart = session()->get('cart');
            foreach ($cart as $key => $cartItem) {
                $length += (float)$cartItem['qty'];
            }
        }

        return round($length, 2);
    }
}

if (!function_exists('cartTotal')) {
    function cartTotal()
    {
        $total = 0;
        if (session()->has('cart') && !empty(session()->get('cart'))) {
            $cart = session()->get('cart');
            foreach ($cart as $key => $cartItem) {
                $total += (float)$cartItem['price'] * (float)$cartItem['qty'];
            }
        }

        return round($total, 2);
    }
}

if (!function_exists('cartSubTotal')) {
    function cartSubTotal()
    {
        $coupon = session()->has('coupon') && !empty(session()->get('coupon')) ? session()->get('coupon') : 0;
        $cartTotal = cartTotal();
        $subTotal = $cartTotal - $coupon;

        return round($subTotal, 2);
    }
}


if (!function_exists('tax')) {
    function tax()
    {
        $bex = BasicExtra::first();
        $tax = $bex->tax;

        if (session()->has('cart') && !empty(session()->get('cart'))) {
            $tax = (cartSubTotal() * $tax) / 100;
        }

        return round($tax, 2);
    }
}

if (!function_exists('coupon')) {
    function coupon()
    {
        return session()->has('coupon') && !empty(session()->get('coupon')) ? round(session()->get('coupon'), 2) : 0.00;
    }
}

if (!function_exists('paytabInfo')) {
    function paytabInfo()
    {
        // Could please connect me with a support.who can tell me about live api and test api's Payment url ? Now, I am using this https://secure-global.paytabs.com/payment/request url for testing puporse. Is it work for my live api ???
        // paytabs informations
        $paytabs = PaymentGateway::where('keyword', 'paytabs')->first();
        $paytabsInfo = json_decode($paytabs->information, true);
        if ($paytabsInfo['country'] == 'global') {
            // $url = 'https://secure-global.paytabs.com/payment/request';
            $currency = 'USD';
        } elseif ($paytabsInfo['country'] == 'sa') {
            // $url = 'https://secure.paytabs.sa/payment/request';
            $currency = 'SAR';
        } elseif ($paytabsInfo['country'] == 'uae') {
            // $url = 'https://secure.paytabs.com/payment/request';
            $currency = 'AED';
        } elseif ($paytabsInfo['country'] == 'egypt') {
            // $url = 'https://secure-egypt.paytabs.com/payment/request';
            $currency = 'EGP';
        } elseif ($paytabsInfo['country'] == 'oman') {
            // $url = 'https://secure-oman.paytabs.com/payment/request';
            $currency = 'OMR';
        } elseif ($paytabsInfo['country'] == 'jordan') {
            // $url = 'https://secure-jordan.paytabs.com/payment/request';
            $currency = 'JOD';
        } elseif ($paytabsInfo['country'] == 'iraq') {
            // $url = 'https://secure-iraq.paytabs.com/payment/request';
            $currency = 'IQD';
        } else {
            // $url = 'https://secure-global.paytabs.com/payment/request';
            $currency = 'USD';
        }
        return [
            'server_key' => $paytabsInfo['server_key'],
            'profile_id' => $paytabsInfo['profile_id'],
            'url'        => $paytabsInfo['api_endpoint'],
            'currency'   => $currency,
        ];
    }
}
