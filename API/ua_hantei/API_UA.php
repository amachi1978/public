<?php
// docomo/au/softbank
define('CARRIER_DOCOMO', 1);
define('CARRIER_EZWEB', 2);
define('CARRIER_SOFTBANK', 3);
// PC
define('CARRIER_PC', 4);
// iPhone/Android
define('CARRIER_IPHONE', 5);
define('CARRIER_ANDROID', 6);

/**
 * キャリア判別関数
 *
 * @return void
 */
function GET_CARRIER()
{
    global $_CARRIER;
    if ($_CARRIER != null) {
        return $_CARRIER;
    }

    $uaList = array(
        // **********************
        // * docomo/au/softbank *
        // **********************
        array(
            'regexp'  => '!^DoCoMo!',
            'carrier' => CARRIER_DOCOMO,
        ),
        array(
            'regexp'  => '!^KDDI-!',
            'carrier' => CARRIER_EZWEB,
        ),
        array(
            'regexp'  => '!^UP\.Browser!',
            'carrier' => CARRIER_EZWEB,
        ),
        array(
            'regexp'  => '!^SoftBank!',
            'carrier' => CARRIER_SOFTBANK,
        ),
        array(
            'regexp'  => '!^Vodafone!',
            'carrier' => CARRIER_SOFTBANK,
        ),
        array(
            'regexp'  => '!^J-PHONE!',
            'carrier' => CARRIER_SOFTBANK,
        ),
        array(
            'regexp'  => '!^MOT-!',
            'carrier' => CARRIER_SOFTBANK,
        ),
        array(
            'regexp'  => '!^Semulator!',
            'carrier' => CARRIER_SOFTBANK,
        ),
        array(
            'regexp'  => '!^Vemulator!',
            'carrier' => CARRIER_SOFTBANK,
        ),
        array(
            'regexp'  => '!^J-EMULATOR!',
            'carrier' => CARRIER_SOFTBANK,
        ),
        array(
            'regexp'  => '!^MOTEMULATOR!',
            'carrier' => CARRIER_SOFTBANK,
        ),
        // ******************
        // * iPhone/Android *
        // ******************
        array(
            'regexp'  => '/iPhone/i',
            'carrier' => CARRIER_IPHONE,
        ),
        array(
            'regexp'  => '/iPod/i',
            'carrier' => CARRIER_IPHONE,
        ),
        array(
            'regexp'  => '/Android/i',
            'carrier' => CARRIER_ANDROID,
        ),
        array(
            'regexp'  => '/dream/i',
            'carrier' => CARRIER_ANDROID,
        ),
        array(
            'regexp'  => '/CUPCAKE/i',
            'carrier' => CARRIER_OLD_ANDROID,
        ),
        array(
            'regexp'  => '/blackberry/i',
            'carrier' => CARRIER_ANDROID,
        ),
        array(
            'regexp'  => '/webOS/i',
            'carrier' => CARRIER_PALM,
        ),
        array(
            'regexp'  => '/incognito/i',
            'carrier' => CARRIER_ANDROID,
        ),
        array(
            'regexp'  => '/webmate/i',
            'carrier' => CARRIER_ANDROID,
        )
    );

    $ua = $_SERVER['HTTP_USER_AGENT'];
    foreach ($uaList as $item) {
        if (preg_match($item['regexp'], $ua)) {
            $_CARRIER = $item['carrier'];
            break;
        }
    }

    if ($_CARRIER == null) {
        $_CARRIER = CARRIER_PC;
    }

    return $_CARRIER;
}
?>
