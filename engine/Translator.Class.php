<?php

namespace engine;

require_once 'libs/GoogleTranslate.class.php';
require_once 'libs/TranslateYandex.class.php';

use Statickidz\GoogleTranslate;


class translator
{

    /**
     * translator constructor.
     * @param $text
     * @param $source_language
     * @param $target_language
     */
    public function __construct($text, $source_language, $target_language)
    {

    }


    function rus2translit($string)
    {
        $converter = [
            'а' => 'a', 'б' => 'b', 'в' => 'v',
            'г' => 'g', 'д' => 'd', 'е' => 'e',
            'ё' => 'e', 'ж' => 'zh', 'з' => 'z',
            'и' => 'i', 'й' => 'y', 'к' => 'k',
            'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r',
            'с' => 's', 'т' => 't', 'у' => 'u',
            'ф' => 'f', 'х' => 'h', 'ц' => 'c',
            'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch',
            'ь' => '', 'ы' => 'y', 'ъ' => '',
            'э' => 'e', 'ю' => 'yu', 'я' => 'ya',

            'А' => 'A', 'Б' => 'B', 'В' => 'V',
            'Г' => 'G', 'Д' => 'D', 'Е' => 'E',
            'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z',
            'И' => 'I', 'Й' => 'Y', 'К' => 'K',
            'Л' => 'L', 'М' => 'M', 'Н' => 'N',
            'О' => 'O', 'П' => 'P', 'Р' => 'R',
            'С' => 'S', 'Т' => 'T', 'У' => 'U',
            'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
            'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sch',
            'Ь' => '', 'Ы' => 'Y', 'Ъ' => '',
            'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
        ];
        return strtr($string, $converter);
    }


    function translit($str)
    {
        // переводим в транслит
        $str = rus2translit($str);

        // в нижний регистр
        $str = strtolower($str);

        // заменям все ненужное нам на "-"
        $str = preg_replace('/[^-a-z0-9_]+/', '-', $str);

        // удаляем начальные и конечные '-'
        $str = trim($str, "-");

        $str = trim($str);

        return $str;
    }

    function translate_ua($name)
    {

        $result = translate($name);

        if ($result == false) {

            return false;

        } else {

            return Ua($result);

        }

    }


    function Ua($name)
    {

        $first = mb_substr($name, 0, 1, "UTF-8");
        $next = mb_substr($name, 1, 500, "UTF-8");


        $array = ['#', '"', '«', '('];

        if (in_array($first, $array)) {

            return $name;
        }

        if (is_numeric($first)) {

            return $name;
        }

        $arr = [
            'а' => 'А', 'б' => 'Б',
            'в' => 'В', 'г' => 'Г',
            'ґ' => 'Ґ', 'д' => 'Д',
            'е' => 'Е', 'є' => 'Є',
            'ж' => 'Ж', 'з' => 'З',
            'и' => 'И', 'і' => 'І',
            'ї' => 'Ї', 'й' => 'Й',
            'к' => 'К', 'л' => 'Л',
            'м' => 'М', 'н' => 'Н',
            'о' => 'О', 'п' => 'П',
            'р' => 'Р', 'с' => 'С',
            'т' => 'Т', 'у' => 'У',
            'ф' => 'Ф', 'х' => 'Х',
            'ц' => 'Ц', 'ч' => 'Ч',
            'ш' => 'Ш', 'щ' => 'Щ',
            'ь' => 'Ь', 'ю' => 'Ю',
            'я' => 'Я',
        ];

        $string = '';

        //Узнаю какой регистр

        if (mb_strtolower($first, 'utf-8') != $first) {

            return $name;

        } else {

            foreach ($arr as $k => $v) {

                if ($k == $first) {

                    $string .= $v . $next;

                }
            }

            return $string;
        }


    }

    function Ru($name)
    {

        $first = mb_substr($name, 0, 1, "UTF-8");
        $next = mb_substr($name, 1, 500, "UTF-8");


        $array = ['#', '"', '«', '('];

        if (in_array($first, $array)) {

            return $name;
        }

        if (is_numeric($first)) {

            return $name;
        }

        $arr = [
            'а' => 'А', 'б' => 'Б',
            'в' => 'В', 'г' => 'Г',
            'д' => 'Д', 'е' => 'Е',
            'ё' => 'Ё', 'ж' => 'Ж',
            'з' => 'З', 'и' => 'И',
            'й' => 'Й', 'к' => 'К',
            'л' => 'Л', 'м' => 'М',
            'н' => 'Н', 'о' => 'О',
            'п' => 'П', 'р' => 'Р',
            'с' => 'С', 'т' => 'Т',
            'у' => 'У', 'ф' => 'Ф',
            'х' => 'Х', 'ц' => 'Ц',
            'ч' => 'Ч', 'ш' => 'Ш',
            'щ' => 'Щ', 'ь' => 'Ь',
            'ы' => 'Ы', 'ъ' => 'Ъ',
            'э' => 'Э', 'ю' => 'Ю',
            'я' => 'Я',
        ];

        $string = '';

        //Узнаю какой регистр

        if (mb_strtolower($first, 'utf-8') != $first) {

            return $name;

        } else {

            foreach ($arr as $k => $v) {

                if ($k == $first) {

                    $string .= $v . $next;

                }
            }

            return $string;
        }


    }


    function translate($text)
    {

        $google = @google_translate($text);

        pr3($google);

        if ($google == false) {

            $yandex = @yandex_translate($text);

            if ($yandex == false) {

                return false;

            } else {

                return $yandex;

            }

        } else {

            return $google;

        }
    }

    function yandex_translate($text)
    {

        try {

            $key = 'trnsl.1.1.20180912T200326Z.57cfd7762d891bd8.a10936a4ff9bb24f6e73587c7f74fbe3e6f30920';

            $translator = new Translator($key);

            $translation = $translator->translate($text, 'en-uk');

            $result = $translation->getResult()[0];
            if ($result == '') {
                return false;
            } else {
                return $result;
            }
        } catch (Exception $e) {

            return false;
        }


    }

    function google_translate($text)
    {

        //require 'google.translate.class.php';

        $source = 'ru';
        $target = 'uk';

        $trans = new GoogleTranslate();

        $result = $trans->translate($source, $target, $text);

        if ($result == '') {
            return false;
        } else {
            return $result;
        }

    }

}