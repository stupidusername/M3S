<?php

namespace app\models;

use getID3;
use getid3_lib;
use Mimey\MimeTypes;
use yii;
use yii\caching\FileDependency;
use yii\helpers\Url;

/**
 * Music radios and their songs.
 */
abstract class Music {

    // Music radios folder.
    const FOLDER = 'music';

    // Data cache duration in seconds (3600 seconds = 1 hour).
    const CACHE_DURATION = 3600;

    /**
     * Get a list of the radios.
     * @return array
     */
    public static function getRadios() {
        // Get the radio list from the cache or set its value.
        return Yii::$app->cache->getOrSet(
            'radios',
            function () {
                // Get a list from the subfolders of the radios folder.
                $path = Yii::getAlias('@webroot/' . self::FOLDER);
                $radios = glob($path . '/*' , GLOB_ONLYDIR);
                // Build the radio list.
                array_walk($radios, function (&$radio, $key) {
                    $radio = [
                        'id' => $key,
                        'title' => basename($radio),
                    ];
                });
                return $radios;
            },
            self::CACHE_DURATION,
            new FileDependency(['fileName' => '@webroot/' . self::FOLDER])
        );
    }

    /**
     * Get a list of the songs from one radio.
     * @param integer $radioId The radio ID.
     * @return array
     */
    public static function getSongs($radioId) {
        $songs = [];
        $radio = null;
        $radios = self::getRadios();

        // Find the specified radio.
        foreach ($radios as $r) {
            if ($r['id'] == $radioId) {
                $radio = $r;
                break;
            }
        }

        if ($radio) {
            $radioPath = Yii::getAlias(
                '@webroot/' . self::FOLDER . '/' . $radio['title']
            );

            // Get the song list from the cache or set its value.
            $songs = Yii::$app->cache->getOrSet(
                'songs.' . $radioId,
                function () use ($radio, $radioPath) {
                    $songs = [];
                    // Get a list of the files from the radio folder.
                    $files = scandir($radioPath);
                    // Build the song list.
                    foreach ($files as $k => $file) {
                        $path = $radioPath . DIRECTORY_SEPARATOR . $file;
                        if (is_file($path)) {
                            $info = self::getSongInformation($path);
                            if ($info) {
                                // Add the song to the list.
                                $songs[] = [
                                    'id' => $k,
                                    'radio_id' => $radio['id']
                                ] + $info;
                            }
                        }
                    }
                    return $songs;
                },
                self::CACHE_DURATION,
                new FileDependency(['fileName' => $radioPath])
            );
        }

        return $songs;
    }

    /**
     * Get metadata information from one song.
     * @param string $path Path to the song file.
     * @return array|null Returns null on error.
     */
    private static function getSongInformation($path) {
        // Get the song information using getID3.
        $getID3 = new getID3();
        $info = $getID3->analyze($path);
        // Merge all available tags (for example, ID3v2 + ID3v1) into one
        // array. All the tags are now in ['comments'].
        getid3_lib::CopyTagsToComments($info);

        // Return null on error.
        if (isset($info['error'])) {
            return null;
        } else {
            // Default values.
            $filename = basename($path);
            $dir = basename(dirname($path));
            $title = null;
            $album = null;
            $author = null;
            $albumartFilename = null;
            $songUrl = Url::to(
                '@web/' . self::FOLDER . '/' . $dir . '/' . $filename,
                true
            );
            $albumartUrl = null;

            // Get metadata.
            if (isset($info['comments']['title'])) {
                $title = implode(' - ', $info['comments']['title']);
            }
            if (isset($info['comments']['album'])) {
                $album = implode(' - ', $info['comments']['album']);
            }
            if (isset($info['comments']['artist'])) {
                $author = implode(' - ', $info['comments']['artist']);
            }
            if (isset($info['comments']['picture'][0])) {
                $picture = $info['comments']['picture'][0];
                if (isset($picture['data'], $picture['image_mime'])) {
                    $mimes = new MimeTypes;
                    $ext = $mimes->getExtension($picture['image_mime']);
                    $songPath = $dir . '/' . $filename;
                    $albumartFilename = $filename . '.' . $ext;
                    $albumartUrl = Url::to(
                        [
                            'api/get-song-albumart',
                            'songPath' => $songPath,
                            'albumartFilename' => $albumartFilename,
                        ],
                        true
                    );
                }
            }

            // Return information.
            return [
                'filename' => $filename,
                'title' => $title,
                'album' => $album,
                'author' => $author,
                'songUrl' => $songUrl,
                'albumart_filename' => $albumartFilename,
                'albumartUrl' => $albumartUrl,
            ];
        }
    }

    /**
     * Get the albumart data from one song.
     * @param string $path Path to the song file.
     * @return array|null.
     */
    public static function getSongAlbumartData($path) {
        // Get the song information using getID3.
        $getID3 = new getID3();
        $info = $getID3->analyze($path);
        // Merge all available tags (for example, ID3v2 + ID3v1) into one
        // array. All the tags are now in ['comments'].
        getid3_lib::CopyTagsToComments($info);

        // Get the albumart information.
        $data = null;
        if (isset($info['comments']['picture'][0])) {
            $picture = $info['comments']['picture'][0];
            if (isset($picture['data'], $picture['image_mime'])) {
                $data = [
                    'data' => $picture['data'],
                    'mimeType' => $picture['image_mime'],
                ];
            }
        }

        return $data;
    }
}
