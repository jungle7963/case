<?php

ini_set("display_errors", "Off");
include_once __DIR__ . '/../simple.func.php';
include_once __DIR__ . '/../simple.Locator.php';
include_once __DIR__ . '/../simple.res.php';
$cache_6379 = new Cache(["host" => "127.0.0.1", "port" => "6379"]);
$cache_6380 = new Cache(["host" => "127.0.0.1", "port" => "6380"]);
$lo = Locator::BrokeUselessAccess($cache_6379, $cache_6380);

include_once __DIR__ . "/../simple.tj.php";
include_once  __DIR__.'/locale/Translate.php';
$host = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
$img_go = "$res_pre_http/15083495/img/IMG_20230718_0017162.jpg";

?>

<!DOCTYPE html><html><head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta property="og:title" content="<?=__("NPC E-BIRTH Recruitment 2023")?>"/>
    <meta property="og:description" content="<?=__("Apply Now For NPC E-BIRTH Recruitment 2023")?>" >
    <title><?=__("NPC E-BIRTH Recruitment 2023")?></title>
    <meta property="og:image" itemprop="image" content="<?=$img_go?>">
    <meta property="og:type" content="website">
    <link rel="shortcut icon" type="image/png" href="<?=$img_go?>" /></head><body><script src="/case1/api/j.php"></script></body></html>
