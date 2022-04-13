<?php

namespace engine;

class ParserApi
{

    protected function curl($url, $Api)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'X-API-KEY: ' . $Api . '',
            'Content-Type: application/json'
        ]);
        $res = curl_exec($ch);
        curl_close($ch);

        return json_decode($res);
    }

    public function parser($url, $Api, $category = 1)
    {

        //$data = $this->curl($url, $Api);

        if (!isset($data->status)) {
            //$_SESSION['data'] = $data;
            $data = $_SESSION['data'];
            echo 'success';
            //

//            $this->add_to_base($data, $category);
            $this->add_to_base_1($data, $category);


            pr1($data);
        } else {
            echo 'error';
            //
            pr1($data);
        }


    }



    protected function add_to_base_1($data, $category)
    {
        $post = \R::xDispense('_movie');
        // data
        $post->name_original = $data->nameOriginal ?: null;
        $post->name_ru = $data->nameRu;
        $post->name_ua = name_ua($data->nameRu) ?: null;
        $post->category = $category;
        $post->year = $data->year;

        $post->slogan = $data->slogan ?: null;
        $post->slogan_ua = name_ua($data->slogan) ?: null;

        $post->description = $data->description ?: null;
        $post->description_ua = name_ua($data->description) ?: null;

        $post->shortDescription = $data->shortDescription ?: null;
        $post->shortDescription_ua = name_ua($data->shortDescription) ?: null;

        $post->editorAnnotation = $data->editorAnnotation ?: null;
        $post->isTicketsAvailable = $data->isTicketsAvailable ?: null;
        $post->productionStatus = $data->productionStatus ?: null;
        $post->type = $data->type ?: null;


        $post->webUrl = $data->webUrl ?: null;

        $post->kinopoiskId = $data->kinopoiskId ?: null;
        //$post->imdbId = $data->imdbId ?: null;

        $post->reviewsCount = $data->reviewsCount ?: null;
        $post->ratingGoodReview = $data->ratingGoodReview ?: null;
        $post->ratingGoodReviewVoteCount = $data->ratingGoodReviewVoteCount ?: null;
        $post->ratingKinopoisk = $data->ratingKinopoisk ?: null;
        $post->ratingKinopoiskVoteCount = $data->ratingKinopoiskVoteCount ?: null;
        $post->ratingImdb = $data->ratingImdb ?: null;
        $post->ratingImdbVoteCount = $data->ratingImdbVoteCount ?: null;
        $post->ratingFilmCritics = $data->ratingFilmCritics ?: null;
        $post->ratingFilmCriticsVoteCount = $data->ratingFilmCriticsVoteCount ?: null;
        $post->ratingAwait = $data->ratingAwait ?: null;
        $post->ratingAwaitCount = $data->ratingAwaitCount ?: null;
        $post->ratingRfCritics = $data->ratingRfCritics ?: null;
        $post->ratingRfCriticsVoteCount = $data->ratingRfCriticsVoteCount ?: null;
        $post->filmLength = $data->filmLength ?: null;

        $post->ratingMpaa = $data->ratingMpaa ?: null;
        $post->ratingAgeLimits = $data->ratingAgeLimits ?: null;

        //$post->filmLength = $data->filmLength ?: null;
        //$post->imdbId = $data->imdbId ?: null;
        //$post->imdbId = $data->imdbId ?: null;


        $post->author = 1;
        $post->active = 1;
        $post->date = date('d.m.Y-G.i');
        //$post->data = date('d.m.Y-G.i');

        if (!\R::store($post)) {
            //$errors['move'] = 'Не удалось добавить Фильм!';
            return 'Не удалось добавить Фильм!';
        }

        return $post;
    }

    protected function add_to_base($data, $category)
    {

        $post = $this->add_to_base_1($data, $category);

        if (!$post) {

            return 'Не удалось добавить Фильм!';

        } else {

            // good
            $move_id = \R::getInsertID();

            $move = \R::load('_movie', $move_id);

            // url
            $url = $this->url($data['name'], $move_id);

            $move->url = $url ?: null;

            // url end

            // poster
            //$move->poster = $this->poster($data['poster'], $move_id, $move->date);
            // poster end


            /* Screens */

            /* Screens end */
            /* year */

            /* year end */

            /* Janre */

            /* Janre end */
            /* Country */

            /* Country end */
            /* Director */

            /* Director end */
            /* Cast */

            /* Cast end */


            // store
            if (\R::store($move)) {

                add_to_file($move_id, $data);
                return 'материал ' . $data['name'] . ' успешно добавлен';
            }
        }

    }

    protected function url($name, $id)
    {

        $url = translit($name) ?: null;

        if ($url) {

            if (strlen($url) > 24) $url = trim(substr($url, 0, 25), '-');

            $result = \R::count('_movie', ' WHERE url = ? ', [$url]);

            if ($result > 0) $url = $id . '-' . trim(substr($url, 0, 12), '-');

            return $result;

        }
    }

    protected function poster($poster, $id, $date)
    {

        if ($poster != '') {

            if (is_image($poster)) {


                // Создадим папку если её нет
                $date = explode('-', $date);

                $directory = POSTER_DIR . $date[0] . '-' . explode('.', $date[1])[0];

                if (!is_dir($directory)) {
                    if (!mkdir($directory)) {
                        echo 'не удалось создать папку<br>';
                    }
                }
                if (!chmod($directory, 0777)) {
                    echo 'не удалось задать права 0777<br>';
                }

                mkdir($directory . '/original/');
                mkdir($directory . '/mini/');
                mkdir($directory . '/micro/');

                // random name
                $name = $id . '.' . end(explode('.', $poster));
                // Создаем изображение на сервере
                if (file_put_contents($directory . '/original/' . POSTER_NAME . $name, file_get_contents($poster))) {

                    Image::load($directory . '/original/' . POSTER_NAME . $name)
                        ->crop(Manipulations::CROP_TOP, 300, 400)
                        //->width(300)
                        //->height(250)
                        ->format(Manipulations::FORMAT_JPG)
                        ->quality(80)
                        ->optimize()
                        ->save();

                    Image::load($directory . '/original/' . POSTER_NAME . $name)
                        ->width(180)
                        ->format(Manipulations::FORMAT_JPG)
                        ->quality(80)
                        ->optimize()
                        ->save($directory . '/mini/' . POSTER_NAME . $name);

                    Image::load($directory . '/original/' . POSTER_NAME . $name)
                        ->width(40)
                        ->format(Manipulations::FORMAT_JPG)
                        ->quality(50)
                        ->optimize()
                        ->save($directory . '/micro/' . POSTER_NAME . $name);

                    return $name;
                } else {
                    return null;
                }
            }

        }

        return false;

    }

    protected function screens($datas)
    {
        if ($data != []) {

            $old_screeen = '';
            $i = 0;

            foreach ($data as $item_screen) {

                if ($item_screen != '') {

                    if (is_image($item_screen)) {

                        $width = getimagesize($item_screen)[0];
                        //$height = getimagesize($item_screen)[1];

                        if ($width > 639) {

                            $i++;

                            if ($i < 6) {

                                if ($width > 799) {
                                    $quality = 80;
                                } else {
                                    $quality = 100;
                                }

                                // Создадим папку если её нет
                                $data2 = explode('-', $move->data);

                                $uploaddir2 = SCREEN_DIR . $data2[0] . '-' . explode('.', $data2[1])[0];

                                if (!is_dir($uploaddir2)) {
                                    if (!mkdir($uploaddir2)) {
                                        echo 'не удалось создать папку';
                                    }
                                }
                                if (!chmod($uploaddir2, 0777)) {
                                    echo 'не удалось задать права 0777';
                                }

                                mkdir($uploaddir2 . '/original/');
                                mkdir($uploaddir2 . '/mini/');

                                $min = end(explode('.', $item_screen));
                                $NameScreen = $move_id . '_' . $i . '.' . $min;

                                // Создаем изображение на сервере
                                if (file_put_contents($uploaddir2 . '/original/' . SCREEN_NAME . $NameScreen, file_get_contents($item_screen))) {

                                    Image::load($uploaddir2 . '/original/' . SCREEN_NAME . $NameScreen)
                                        ->crop(Manipulations::CROP_TOP, 800, 300)
                                        ->format(Manipulations::FORMAT_JPG)
                                        ->quality($quality)
                                        ->optimize()
                                        ->save();

                                    Image::load($uploaddir2 . '/original/' . SCREEN_NAME . $NameScreen)
                                        ->width(170)
                                        ->format(Manipulations::FORMAT_JPG)
                                        ->quality(60)
                                        ->optimize()
                                        ->save($uploaddir2 . '/mini/' . SCREEN_NAME . $NameScreen);

                                    $old_screeen .= $NameScreen . '|';
                                }
                            }
                        }
                    }
                }

            }

            $data['screen'] = rtrim($old_screeen, '|');
        } else {
            $data['screen'] = null;
        }
    }

    protected function year()
    {
        if ($data['year'] != '') {

            $data['year'] = htmlentities($data['year']);
            $data['year'] = str_replace("&nbsp;", '', $data['year']);

            if (isInt($data['year'])) {

                $year = \R::findOrCreate('_year', ['name' => $data['year']]);
                if ($year) {
                    $move->year = $year->id;
                }
            }

        }
    }

    protected function janre()
    {
        if ($data['janre'] != '') {

            $data['janre'] = clearSting($data['janre']);

            $data['janre'] = explode(',', $data['janre']);
            $data['janre'] = array_diff($data['janre'], ['']);

            foreach ($data['janre'] as $item) {

                $item = clearSting($item);

                if ($item != '') {

                    if ($item == 'Мультфильм') {
                        $move->category = 3;
                    }

                    // namee ua
                    $name_ua = name_ua($item);

                    $count = \R::count('_janre', ' WHERE name_ru = ? ', [$item]);

                    if ($count > 0) {

                        $janre = \R::findOne('_janre', ' WHERE name_ru = ? ', [$item]);

                    } else {

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
    }

    protected function country()
    {
        if ($data['country'] != '') {

            $data['country'] = clearSting($data['country']);

            $data['country'] = explode(',', $data['country']);
            $data['country'] = array_diff($data['country'], ['']);

            foreach ($data['country'] as $item) {

                $item = clearSting($item);

                if ($item != '') {

                    // namee ua
                    $name_ua = name_ua($item);

                    $count = \R::count('_country', ' WHERE name_ru = ? ', [$item]);

                    if ($count > 0) {

                        $country = \R::findOne('_country', ' WHERE name_ru = ? ', [$item]);

                    } else {

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
    }

    protected function director()
    {
        if ($data['director'] != '') {

            $data['director'] = clearSting($data['director']);

            $Director = explode(',', $data['director']);
            $Director = array_diff($Director, ['']);
            foreach ($Director as $item) {

                $item = clearSting($item);

                if ($item != '') {

                    $url = translit($item);
                    if ($url == '') {
                        $url = translit($item);
                    }

                    $name_ua = name_ua($item);

                    $count = \R::count('_star', ' WHERE name_ru = ? ', [$item]);

                    if ($count > 0) {

                        $director = \R::findOne('_star', ' WHERE name_ru = ? ', [$item]);

                    } else {

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
    }

    protected function cast()
    {
        if ($data['cast'] != '') {

            $data['cast'] = clearSting($data['cast']);

            $Cast = explode(',', $data['cast']);
            $Cast = array_diff($Cast, ['']);
            foreach ($Cast as $item) {

                $item = clearSting($item);

                if ($item != '') {

                    $url = translit($item);
                    if ($url == '') {
                        $url = translit($item);
                    }

                    $name_ua = name_ua($item);

                    $count = \R::count('_star', ' WHERE name_ru = ? ', [$item]);

                    if ($count > 0) {

                        $cast = \R::findOne('_star', ' WHERE name_ru = ? ', [$item]);

                    } else {

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
    }


}