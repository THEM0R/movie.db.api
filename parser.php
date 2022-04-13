<?php

require_once 'vendor/autoload.php';

use Spatie\Image\Image;
use Spatie\Image\Manipulations;


function parser($url, $start, $end, $category){



    $dir = scandir('movie_txt/');

    foreach ($dir as $item){

        if($item != '.' && $item != '..'){

            $filename = 'movie_txt/'.$item.'/movie.txt';

            $dir2 = 'movie_db/'.$item.'/';

            mkdir($dir2);

            $filename2 = 'movie_db/'.$item.'/base64.txt';

            if( is_file($filename) ){

                $data = unserialize( file_get_contents($filename) );
                file_put_contents($filename2, base64_encode( serialize($data) ) );

            }

        }

    }

    exit;


    foreach ($data as $item){


        file_put_contents($uploaddir_txt.$filename_txt, serialize($ars_txt) );
        file_put_contents($uploaddir_txt.$filename_txt, serialize($ars_txt) );

        pr1($item);


    }

    exit;

    // code
    if($start < $end){
        $file = get_content_curl($url);
        $doc = phpQuery::newDocument($file);

        foreach($doc->find('#data-list .row') as $art){

            if(translate('Привет') == false) exit( 'Перекладач не працює !!!' );

            $art    = pq($art);
            $link   = 'https://my-hit.org' . $art->find('.text-center a')->attr('href');
            // code
            $move   = parsData($link);

            if($move == false){

                echo "<br>";
                echo "<h2>такой материал уже есть</h2>";
                echo "<br>";

            }else{

                //$poster = parsImages($link,'poster');
                $screen = parsImages($link,'frame');
                $move   = array_merge($move,$screen);
                // add to db
                $add    = add_to_base($move, $category);

                echo "<br>";
                echo "<h2>".$add."</h2>";
                echo "<br>";
                echo 'Время выполнения скрипта: '.round(microtime(true) - $start, 4).' сек.';
                echo '<hr>';
            }

        }

        $pages = 'https://my-hit.org' . $doc->find('.pagination .active')->next()->children()->attr('href');

        echo '<hr>';
        echo "<h2>".pr($pages)."</h2>";
        echo '<hr>';


        $next = 'https://my-hit.org' . $doc->find('.pagination .active')->next()->children()->attr('href');
        if( !empty($next) ){
        $start++;
        parser($next, $start, $end, $category);
        }
    }

}


function add_to_base($data, $category){

        $move                   = \R::xDispense('_movie');
        // data
        $move->name_original    = $data['alt_name']         ?: null;
        $move->name_ru          = $data['name'];
        $move->name_ua          = name_ua($data['name'])    ?: null;
        $move->author           = 1;
        $move->category         = $category;
        $move->active           = 1;
        $move->data             = date('d.m.Y-G.i');

        if( !\R::store( $move ) ) {
            //$errors['move'] = 'Не удалось добавить Фильм!';
            return 'Не удалось добавить Фильм!';
        }
        else{

            // good
            $move_id = \R::getInsertID();

            $move = \R::load('_movie',$move_id);

            // url
            $url = translit($data['name']) ?: null;

            if($url) {

                if (strlen($url) > 24) $url = trim(substr($url, 0, 25), '-');

                $url_2 = \R::count('_movie', ' WHERE url = ? ', [$url]);

                if ($url_2 > 0) $url = $move_id . '-' . trim(substr($url, 0, 12), '-');

                $move->url = $url ?: null;
            }

            // poster
            if($data['poster'] != '') {

                if( is_image($data['poster']) ) {


                    // Создадим папку если её нет
                    $data1 = explode('-',$move->data);

                    $uploaddir = POSTER_DIR.$data1[0].'-'.explode('.',$data1[1])[0];

                    if (!is_dir($uploaddir)) {
                        if (!mkdir($uploaddir)) {
                            echo 'не удалось создать папку<br>';
                        }
                    }
                    if (!chmod($uploaddir, 0777)) {
                        echo 'не удалось задать права 0777<br>';
                    }

                    mkdir($uploaddir.'/original/');
                    mkdir($uploaddir.'/mini/');
                    mkdir($uploaddir.'/micro/');

                    $min = end(explode('.', $data['poster']));
                    // random name
                    $posterName = $move_id . '.' . $min;
                    // Создаем изображение на сервере
                    if (file_put_contents($uploaddir .'/original/'. POSTER_NAME.$posterName, file_get_contents($data['poster']))) {

                        Image::load( $uploaddir .'/original/'. POSTER_NAME.$posterName )
                            ->crop(Manipulations::CROP_TOP, 300, 400)
                            //->width(300)
                            //->height(250)
                            ->format(Manipulations::FORMAT_JPG)
                            ->quality(80)
                            ->optimize()
                            ->save();

                        Image::load( $uploaddir .'/original/'. POSTER_NAME.$posterName )
                            ->width(180)
                            ->format(Manipulations::FORMAT_JPG)
                            ->quality(80)
                            ->optimize()
                            ->save( $uploaddir .'/mini/'. POSTER_NAME.$posterName );

                        Image::load( $uploaddir .'/original/'. POSTER_NAME.$posterName )
                            ->width(40)
                            ->format(Manipulations::FORMAT_JPG)
                            ->quality(50)
                            ->optimize()
                            ->save( $uploaddir .'/micro/'. POSTER_NAME.$posterName );

                        $move->poster = $posterName;
                    } else {
                        $move->poster = null;
                    }
                }

            }
            /* Screens */
            if ($data['frame'] != []) {

                $old_screeen = '';
                $i = 0;

                foreach ($data['frame'] as $item_screen) {

                    if($item_screen != '') {

                        if( is_image($item_screen) ) {

                            $width = getimagesize($item_screen)[0];
                            //$height = getimagesize($item_screen)[1];

                            if ($width > 639) {

                                $i++;

                                if($i < 6) {

                                    if($width > 799) {
                                        $quality = 80;
                                    }
                                    else {
                                        $quality = 100;
                                    }

                                    // Создадим папку если её нет
                                    $data2 = explode('-',$move->data);

                                    $uploaddir2 = SCREEN_DIR.$data2[0].'-'.explode('.',$data2[1])[0];

                                    if (!is_dir($uploaddir2)) {
                                        if (!mkdir($uploaddir2)) {
                                            echo 'не удалось создать папку';
                                        }
                                    }
                                    if (!chmod($uploaddir2, 0777)) {
                                        echo 'не удалось задать права 0777';
                                    }

                                    mkdir($uploaddir2.'/original/');
                                    mkdir($uploaddir2.'/mini/');

                                    $min = end(explode('.', $item_screen));
                                    $NameScreen = $move_id . '_' . $i . '.' . $min;

                                    // Создаем изображение на сервере
                                    if (file_put_contents($uploaddir2 .'/original/'.SCREEN_NAME. $NameScreen, file_get_contents($item_screen))) {

                                        Image::load( $uploaddir2 .'/original/'.SCREEN_NAME. $NameScreen )
                                            ->crop(Manipulations::CROP_TOP, 800, 300)
                                            ->format(Manipulations::FORMAT_JPG)
                                            ->quality($quality)
                                            ->optimize()
                                            ->save();

                                        Image::load( $uploaddir2 .'/original/'.SCREEN_NAME. $NameScreen )
                                            ->width(170)
                                            ->format(Manipulations::FORMAT_JPG)
                                            ->quality(60)
                                            ->optimize()
                                            ->save( $uploaddir2 .'/mini/'.SCREEN_NAME. $NameScreen );

                                        $old_screeen .= $NameScreen . '|';
                                    }
                                }
                            }
                        }
                    }

                }

                $data['screen'] = rtrim($old_screeen,'|');
            }
            else{
                $data['screen'] = null;
            }
            /* Screens end */
            /* year */
            if ($data['year'] != '') {

                    $data['year'] = htmlentities($data['year']);
                    $data['year'] = str_replace("&nbsp;", '', $data['year']);

                if ( isInt($data['year']) ) {

                    $year = \R::findOrCreate('_year', ['name' => $data['year']]);
                    if ($year) {
                        $move->year = $year->id;
                    }
                }

            }
            /* year end */

            /* Janre */
            if ($data['janre'] != '') {

                $data['janre'] = clearSting($data['janre']);

                $data['janre'] = explode(',', $data['janre']);
                $data['janre'] = array_diff($data['janre'], ['']);

                foreach ($data['janre'] as $item) {

                    $item = clearSting($item);

                    if($item != ''){

                        if($item == 'Мультфильм'){
                            $move->category = 3;
                        }

                        // namee ua
                        $name_ua = name_ua($item);

                        $count = \R::count('_janre',' WHERE name_ru = ? ',[ $item ]);

                        if($count > 0){

                            $janre = \R::findOne('_janre',' WHERE name_ru = ? ',[ $item ]);

                        }else{

                            $janre = \R::findOrCreate('_janre', [
                                    'name_ru' => $item,
                                    'name_ua' => $name_ua
                                ]
                            );
                        }

                        if ($janre) {

                            $janre_connect = \R::xDispense('_movie_janre');
                            $janre_connect->movie_id = $move_id;
                            $janre_connect->janre_id = $janre->id;

                            \R::store($janre_connect);
                        }
                    }
                }
            }
            /* Janre end */
            /* Country */
            if ($data['country'] != '') {

                $data['country'] = clearSting($data['country']);

                $data['country'] = explode(',', $data['country']);
                $data['country'] = array_diff($data['country'], ['']);

                foreach ($data['country'] as $item) {

                    $item = clearSting($item);

                    if($item != '') {

                        // namee ua
                        $name_ua = name_ua($item);

                        $count = \R::count('_country',' WHERE name_ru = ? ',[ $item ]);

                        if($count > 0){

                            $country = \R::findOne('_country',' WHERE name_ru = ? ',[ $item ]);

                        }else{

                            $country = \R::findOrCreate('_country', [
                                'name_ru' => $item,
                                'name_ua' => $name_ua
                            ]);
                        }

                        if ($country) {

                            $country_connect = \R::xDispense('_movie_country');
                            $country_connect->movie_id = $move_id;
                            $country_connect->country_id = $country->id;

                            \R::store($country_connect);
                        }
                    }
                }
            }
            /* Country end */
            /* Director */
            if ($data['director'] != ''){

                $data['director'] = clearSting($data['director']);

                $Director = explode(',', $data['director']);
                $Director = array_diff($Director, ['']);
                foreach ($Director as $item) {

                    $item = clearSting($item);

                    if($item != '') {

                        $url = translit($item);
                        if ($url == '') {
                            $url = translit($item);
                        }

                        $name_ua = name_ua($item);

                        $count = \R::count('_star',' WHERE name_ru = ? ',[ $item ]);

                        if($count > 0){

                            $director = \R::findOne('_star',' WHERE name_ru = ? ',[ $item ]);

                        }else{

                            $director = \R::findOrCreate('_star', [
                                    'name_ru' => $item ?: null,
                                    'name_ua' => $name_ua ?: null,
                                    'url' => $url ?: null
                                ]
                            );

                        }

                        if ($director) {

                            $director_connect = \R::xDispense('_movie_director');
                            $director_connect->movie_id = $move_id;
                            $director_connect->star_id = $director->id;

                            \R::store($director_connect);
                        }
                    }
                }
            }
            /* Director end */
            /* Cast */
            if ($data['cast'] != '') {

                $data['cast'] = clearSting($data['cast']);

                $Cast = explode(',', $data['cast']);
                $Cast = array_diff($Cast, ['']);
                foreach ($Cast as $item) {

                    $item = clearSting($item);

                    if($item != '') {

                        $url = translit($item);
                        if ($url == '') {
                            $url = translit($item);
                        }

                        $name_ua = name_ua($item);

                        $count = \R::count('_star',' WHERE name_ru = ? ',[ $item ]);

                        if($count > 0){

                            $cast = \R::findOne('_star',' WHERE name_ru = ? ',[ $item ]);

                        }else {

                            $cast = \R::findOrCreate('_star', [
                                    'name_ru' => trim($item) ?: null,
                                    'name_ua' => trim($name_ua) ?: null,
                                    'url' => $url ?: null
                                ]
                            );
                        }

                        if ($cast) {

                            $cast_connect = \R::xDispense('_movie_cast');
                            $cast_connect->movie_id = $move_id;
                            $cast_connect->star_id = $cast->id;

                            \R::store($cast_connect);
                        }
                    }
                }
            }
            /* Cast end */


            // store
            if( \R::store( $move ) ){

                add_to_file($move_id,$data);
                return 'материал '.$data['name'].' успешно добавлен';
            }
        }

    }



function parsData($url){

    // code
    //$url = 'https://my-hit.org/'.trim($href,'/').'/';
    $file = get_content_curl($url);
    $doc = phpQuery::newDocumentHTML($file, 'utf-8');
    // names
    $names = $doc->find('.fullstory > h1')->text();

    if(strpos( $names, '(') === false )
    {
        $name = trim($names);

    }else{
        $name = trim( explode('(',$names)[0] );
        $year = rtrim( explode('(',$names)[1],')' );
    }

    // data
    $count = \R::count('_movie',' name_ru = ? ',[$name]);

    if ($count > 0){
        return false;
        //$ss = 'такой материал уже есть';
    }

    $alt_name = trim($doc->find('.fullstory > h4')->text());
    // jenre
    $doc->find(".fullstory > .list-unstyled > li > b:contains(Жанр:)")->parent('li')->addClass('jenre');
    $doc->find('.fullstory > .list-unstyled > li.jenre')->children('b')->remove();
    // country
    $doc->find(".fullstory > .list-unstyled > li > b:contains(Страна:)")->parent('li')->addClass('country');
    $doc->find('.fullstory > .list-unstyled > li.country')->children('b')->remove();
    // director
    $doc->find(".fullstory > .list-unstyled > li > b:contains(Режиссер:)")->parent('li')->addClass('director');
    $doc->find('.fullstory > .list-unstyled > li.director')->children('b')->remove();
    // cast
    $doc->find(".fullstory > .list-unstyled > li > b:contains(В ролях:)")->parent('li')->addClass('cast');
    $doc->find('.fullstory > .list-unstyled > li.cast')->children('b')->remove();

    $poster = 'https://my-hit.org'.$doc->find('.fullstory > .col-xs-6 > .div-data-poster > img')->attr('src');

    $post = $doc->find('.fullstory')->html();


    // jenre
    $jenre = rtrim(trim($doc->find('.fullstory > .list-unstyled > li.jenre')->text()),'.');
    // country
    $country = rtrim(trim($doc->find('.fullstory > .list-unstyled > li.country')->text()),'.');
    // director
    $director = trim($doc->find('.fullstory > .list-unstyled > li.director')->text());
    $director = clearSting($director);
    // cast
    $cast = trim($doc->find('.fullstory > .list-unstyled > li.cast')->text());
    $cast = clearSting($cast);
    // description
    $description = trim($doc->find('div[itemprop=description]')->text());
    // array
    $move = [
        'name'      => $name,
        'alt_name'  => $alt_name,
        'url'       => translit($name),
        'year'      => $year,
        'janre'     => $jenre,
        'country'   => $country,
        'director'  => $director,
        'cast'      => $cast,
        'description' => $description,
        'poster'    => $poster
    ];

    return $move;

}

function parsImages($href, $page){

    $href = trim($href,'/').'/picture/'.$page.'/';
    $file = file_get_contents($href);
    $doc = phpQuery::newDocumentHTML($file, 'utf-8');
    //
    $doc->find('#picture-list .row .col-xs-3 a img')->addClass('poster');

    $images = $doc->find('#picture-list')->children('.row')->html();

    $images = pq($images);

    $arr = [];
    foreach ($images as $item){
        $item = pq($item);

        $src = $item->find('img')->attr('src');

        if(!empty($src)){

            $src = str_replace('/storage','https://my-hit.org/storage',$src);
            $src = str_replace('220x220x50x1.jpg','1920x1080x500.jpg',$src);

            $arr[$page][] = $src;
        }

    }

    return $arr;

}

function add_to_file($move_id, $data){

    // descriptions
    $description_ru = $data['description'];
    $description_ua = translate($data['description']);

    $array_is_bd = \R::getAll(' SELECT * FROM _movie WHERE id = ? LIMIT 1 ',[$move_id])[0];

    $array_to_file = [];

    if($array_is_bd != []){

        $array_to_file[$array_is_bd['id']]['id']                  = $move_id;
        $array_to_file[$array_is_bd['id']]['description_ru']      = $description_ru ?: null;
        $array_to_file[$array_is_bd['id']]['description_ua']      = $description_ua ?: null;
        $array_to_file[$array_is_bd['id']]['screen']              = $data['screen'] ?: null;
    }
    else{
        return 'Не удалось достать Фильм из бд!';
    }

    // $array_to_file

    // data
    $data5 = explode('-',$array_is_bd['data']);

    // UPLOAD DIR
    $uploaddir_txt = DB_DIR.$data5[0].'-'.explode('.',$data5[1])[0];

    // file name
    $filename_txt = '/movie.txt';


    if (!is_dir($uploaddir_txt)) {

        if (!mkdir($uploaddir_txt)) return 'Не удалось создать папку' . $uploaddir_txt;
        if (!chmod($uploaddir_txt, 0777)) return 'Не удалось дать права папке'.$uploaddir_txt;
    }


    if( !is_file($uploaddir_txt.$filename_txt) ){
        file_put_contents($uploaddir_txt.$filename_txt, serialize([]) );
    }

    $arr_txt = unserialize( file_get_contents( $uploaddir_txt.$filename_txt ) );

    $ars_txt = [];

    if($arr_txt != []){

        $movie_array = array_merge($arr_txt,$array_to_file);

        foreach( $movie_array as $k => $v ){

            $ars_txt[ $v['id'] ]['id']                  = $v['id'];
            $ars_txt[ $v['id'] ]['description_ru']      = $v['description_ru']  ?: null;
            $ars_txt[ $v['id'] ]['description_ua']      = $v['description_ua']  ?: null;
            $ars_txt[ $v['id'] ]['screen']              = $v['screen']          ?: null;
        }

        //return $ars;

        if(file_put_contents($uploaddir_txt.$filename_txt, serialize($ars_txt) ) ) {
            return $ars_txt;
        }else{
            return 'Не удалось создать файл';
        }


    }
    else
        {

        foreach( $array_to_file as $k => $v ){

            $ars_txt[ $v['id'] ]['id']                  = $v['id'];
            $ars_txt[ $v['id'] ]['description_ru']      = $v['description_ru']  ?: null;
            $ars_txt[ $v['id'] ]['description_ua']      = $v['description_ua']  ?: null;
            $ars_txt[ $v['id'] ]['screen']              = $v['screen']          ?: null;
        }

        //return $ars;
        if(file_put_contents($uploaddir_txt.$filename_txt, serialize($ars_txt) ) ) {
            return $ars_txt;
        }else{
            return 'Не удалось создать файл';
        }

    }


}
