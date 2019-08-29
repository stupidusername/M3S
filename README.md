# M3S

This web app uses the [Yii2 framework](https://www.yiiframework.com/doc/guide/2.0/en).

Refer to the [changelog](CHANGELOG.md) to see the release notes.

## Requirements

* PHP >= 7.3.0 (Tested on 7.3.4).
* RDBMS compatible with Yii2 (Tested on MySQL 8.0.15).
* Composer.


## Installation

* Create a DB for your app.
* Clone/Copy the app code and move it to its directory.
* Create configuration files `local.php` and `config/local.php` (examples are provided).
* Install the composer packages required by the app:
    ```
    $ composer install
    ```
* Apply DB migrations:
    ```
    $ ./yii migrate
    ```
* Install and configure a web server like NGINX or Apache.
  See [Yii2 guide > configuring web serer](https://www.yiiframework.com/doc/guide/2.0/en/start-installation#configuring-web-servers).


## Music and TV channels

Music and TV channels can be loaded into the server by adding them to the web shared folder of the app.
Songs and channels must be placed in a file tree that follows this structure:

```
| - web/
  | - channels/
  | | - channel_category_1/
  | | | - 1 - channel_1.png
  | | | - ...
  | | - channel_category_2/
  | | - ...
  | | - channel_category_n/
  | - music/
    | - music_category_1/
    | | - song.mp3
    | | - ...
    | - music_category_2/
    | - ...
    | - music_category_n/
```

Channel and music category titles are defined by the name of their folders.
Channel files must be named like this `<number> - <title>.<extension>`.
For example "1 - Channel 1.png". No logo image will be provided if the file extension is "txt".

**Supported file extensions:**

* For channel: txt, jpeg, jpg, png
* For music: audio file extensions supported by [getID3](http://getid3.sourceforge.net/).

**Uploading the files**

You can choose any method to upload the files to the server.
The only requirement is that the `channel/` and `music/` folders and their contents can be read by the web app.
A valid option to load the files is using an FTP server such as [vsftpd](https://security.appspot.com/vsftpd.html).


## API endpoints

* `/api/get-update`

    Get the last update of the client app for Android devices.

    * Response:

        ```
        {
            "id": integer,
            "version": string,
            "filename": string,
            "force_update": [integer|null],
            "apkUrl": string,
            "md5": string
        }
        ```

        If there are no loaded updates, the server will return an empty response.

* `/api/get-last-app-update`

    Same as `/api/get-update` but for Linux client devices.

* `/api/get-audio-message?key=<key>&room=<room>`

    Get the information from on audio message.

    * Params:

        * `key`: Integer. Message key identifier.
        * `room`: String. Optional. The room name. Used by particular messages.

    * Response:

        ```
        {
            "id": integer,
            "key": integer,
            "name": null|string,
            "name_spanish": null|string,
            "filename": string,
            "kind": integer|null,
            "audio_output": integer|null,
            "delay": integer|null,
            "manual": integer|null,
            "room": null|string,
            "audioMessageUrl": null|string
        }
        ```

        In the case that the audio message is not found in the app DB, the server will return an empty response.
        The `audioMessageUrl` field will be `null` if the audio message files does not exists on the server.

* `/api/get-radios`

    Get a list of the music radios.

    * Response:

        ```
        [
            {
                "id": integer,
                "title": string
            }
        ]
        ```

* `/api/get-radio-songs?id=<id>`

    Get a list of the song from one radio.

    * Params:

        * `id`: Integer. Radio ID.

    * Response:

        ```
        [
            {
                "id": integer,
                "radio_id": integer,
                "filename": string,
                "title": null|string,
                "album": null|string,
                "author": null|string,
                "songUrl": string,
                "albumart_filename": null|string,
                "albumartUrl": null|string
            }
        ]
        ```

* `/api/get-channel-categories`

    Get a list of the TV channel categories.

    * Response:

        ```
        [
            {
                "id": integer,
                "title": string
            }
        ]
        ```

* `/api/get-channels?categoryId=<categoryId>`

    Get a list of the TV channels from one category.

    * Params:

        * `id`: Integer. Category ID.

    * Response:

        ```
        [
            {
                "id": integer,
                "channel_category_id": integer,
                "number": integer,
                "title": string,
                "logo_filename": null|string,
                "logoUrl": null|string
            }
        ]
        ```

* `/api/get-bar-groups`

    Get a list of the bar article categories.

    * Response:

        ```
        [
            {
                "id": integer,
                "key": integer,
                "id_number": integer|null,
                "name": string
            }
        ]
        ```

* `/api/get-bar-articles?id=<id>`

    Get a list of the bar articles from one category.

    * Params:

        * `id`: Integer. Category ID.

    * Response:

        ```
        [
            {
                "id": integer,
                "bar_group_id": integer,
                "key": integer,
                "id_number": integer|null,
                "name": string,
                "description": null|string,
                "picture_filename": null|string,
                "price": null|string,
                "pictureUrl": null|string,
            }
        ]
        ```

* `/api/get-service-tariffs`

    Get a list of the service tariffs.

    * Response:

        ```
        [
            {
                "id": integer,
                "key": integer|null,
                "price_shift": string|null,
                "price_overnight": string|null,
                "show_price_overnight": integer|null,
                "room_category_name": string|null,
                "room_category_name_short": string|null,
                "turn_duration": string|null,
                "overnight_start": string|null,
                "overnight_finish": string|null,
                "long_turn_start": string|null,
                "long_turn_finish": string|null,
                "show_overnight_start_finish": integer|null,
                "show_long_turn_start_finish": integer|null
            }
        ]
        ```

* `/images/services/hotel.jpg`

    An image that can be displayed along with the list of the services.
    If the image is not found, the server will return a 404 status code.


## API endpoints for the SGH server

The following endpoints can only be used by the SGH server.

* `/admin/update-audio-messages`

    Update the audio messages.

* `/admin/update-bar-articles`

    Update the bar articles.

* `/admin/update-service-tariffs`

    Update the service tariffs.

This web app uses the address of the client (that makes requests to these endpoints) as the SGH address.

If an error occurs during these requests, the server will return a 500 status code.
In this scenario the response will have the following format:

```
{
    'errors': [string]
}
```
