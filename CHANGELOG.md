# Changelog


## 2.0.3 - 2019-08-29

### Fixed
- Fix bug where songs are not listed if its metadata can't be read.


## 2.0.2 - 2019-08-29

### Added
- Web server configuration instructions in `README.md`.

### Fixed
- Typo in `config/local-example.php`.


## 2.0.1 - 2019-05-29

### Fixed
- Convert invalid UTF-8 characters to \0xfffd (Unicode Character 'REPLACEMENT CHARACTER') in API JSON responses to avoid exceptions due to malformed UTF-8 characters.


## 2.0.0 - 2019-04-19

### Fixed
- URLs that are contained within JSON response objects are now encoded in compliance with [RFC 3986](https://www.ietf.org/rfc/rfc3986.txt).


## 1.0.0 - 2019-04-16
