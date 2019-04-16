<?php

namespace app\models;

use yii;
use yii\caching\FileDependency;
use yii\helpers\Url;

/**
 * TV channels and their categories.
 */
abstract class Channel {

    // Channel categories folder.
    const FOLDER = 'channels';

    // Data cache duration in seconds (3600 seconds = 1 hour).
    const CACHE_DURATION = 3600;

    /**
     * Get a list of the channel categories.
     * @return array
     */
    public static function getCategories() {
        // Get the channel category list from the cache or set its value.
        return Yii::$app->cache->getOrSet(
            'channelCategories',
            function () {
                // Get a list from the subfolders of the channels folder.
                $path = Yii::getAlias('@webroot/' . self::FOLDER);
                $categories = glob($path . '/*' , GLOB_ONLYDIR);
                // Build the category list.
                array_walk($categories, function (&$category, $key) {
                    $category = [
                        'id' => $key,
                        'title' => basename($category),
                    ];
                });
                return $categories;
            },
            self::CACHE_DURATION,
            new FileDependency(['fileName' => '@webroot/' . self::FOLDER])
        );
    }

    /**
     * Get a list of the channels from one category.
     * @param integer $categoryId The channel category ID.
     * @return array
     */
    public static function getChannels($categoryId) {
        $channels = [];
        $category = null;
        $categories = self::getCategories();

        // Find the specified category.
        foreach ($categories as $c) {
            if ($c['id'] == $categoryId) {
                $category = $c;
                break;
            }
        }

        if ($category) {
            $categoryPath = Yii::getAlias(
                '@webroot/' . self::FOLDER . '/' . $category['title']
            );

            // Get the channel list from the cache or set its value.
            $channels = Yii::$app->cache->getOrSet(
                'channels.' . $categoryId,
                function () use ($category, $categoryPath) {
                    $channels = [];
                    // Get a list of the supported files from the category
                    // folder.
                    $files = glob(
                        $categoryPath . '/*.{txt,jpeg,jpg,png}',
                        GLOB_BRACE
                    );
                    // Build the channel list.
                    foreach ($files as $k => $path) {
                        $matches = [];
                        // Use RegEx to find the channel number, title and file
                        // extension.
                        $filename = basename($path);
                        preg_match(
                            '/^(\d+)\s-\s(.+)\.([^.]+)$/',
                            $filename,
                            $matches,
                            PREG_OFFSET_CAPTURE);
                        // Add the channel if the filename is valid.
                        if (!empty($matches)) {
                            $number = (int) $matches[1][0];
                            $title = $matches[2][0];
                            $ext = $matches[3][0];
                            // If the file is an image build the logo URL.
                            $logo = null;
                            $logoUrl = null;
                            if (strtolower($ext) != 'txt') {
                                $logoFilename = $filename;
                                $logoUrl = Url::to(
                                    '@web/' . self::FOLDER . '/' .
                                        $category['title']. '/' . $filename,
                                    true
                                );
                            }
                            // Add the channel information to the list.
                            $channels[] = [
                                'id' => $k,
                                'channel_category_id' => $category['id'],
                                'number' => $number,
                                'title' => $title,
                                'logo_filename' => $logoFilename,
                                'logoUrl' => $logoUrl,
                            ];
                        }
                    }
                    return $channels;
                },
                self::CACHE_DURATION,
                new FileDependency(['fileName' => $categoryPath])
            );
        }

        return $channels;
    }
}
