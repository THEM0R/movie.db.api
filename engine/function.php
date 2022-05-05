<?php



use Spatie\Image\Image;
use Spatie\Image\Manipulations;


function get_content_curl($url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $res = curl_exec($ch);
    curl_close($ch);
    return $res;
}


function screen_rsize($img_name){

    Image::load(__DIR__ . '/img/1.jpg')
        ->crop(Manipulations::CROP_CENTER, 300, 400)
        ->format(Manipulations::FORMAT_JPG)
        ->quality(50)
        ->optimize()
        //->width(250)
        //->height(250)
        ->save(__DIR__.'/img/'.$img_name);
}


function isInt($var){
    if (preg_match('/^\+?\d+$/', $var)) {
        return true;
    }else{
        return false;
    }
}

function is_image($img){

    if( getimagesize($img) ){
        return true;
    }else{
        return false;
    }
}

function pr($arr)
{
    echo '<pre>' . print_r($arr, true) . '</pre>';
}

function pr1($arr)
{
    echo '<pre>' . print_r($arr, true) . '</pre>';
    exit;
}

function pr2($arr)
{
    echo '<pre>' , var_dump($arr) , '</pre>';
}

function pr3($arr)
{
    echo '<pre>' , var_dump($arr) , '</pre>';
    exit;
}




function clearSting($string)
{

    $clear = trim($string);
    $clear = htmlentities($clear);

    $clear = rtrim($clear,'.');

    if( strpos($clear, '...') !== false ){
        $clear = str_replace('...','', $clear);
    }

    $clear = str_replace("&nbsp;",'',$clear);

    $clear = trim($clear,' ');
    $clear = str_replace('-','',$clear);
    $clear = str_replace('-','',$clear);

    $clear = preg_replace("/&#?[a-z0-9]+;/i","",$clear);

    //$clear = htmlspecialchars($clear);

    return $clear;
}