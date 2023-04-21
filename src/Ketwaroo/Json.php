<?php


namespace Ketwaroo;


class Json {
    
    public static function loadJsonFile(string $filename) {
        return is_file($filename) ? static::decode(FileSystem::readFileExclusive($filename)) : [];
    }

    public static function writeJsonFile(string $filename, array $data = []) {
        return FileSystem::writeFileExclusive($filename, static::jsonEnc($data));
    }
    
    public static function encode(mixed $data):string {
        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    public static function decode(string $jsonString):mixed {
        return json_decode($jsonString, true);
    }
}
