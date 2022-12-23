<?php

namespace App\Features\ImportLogs\DTO;

class ImportStats
{
    public int $discovered = 0;
    public int $created = 0;
    public int $updated = 0;
    public int $malformed = 0;

    public static function fromArray(array $array): ImportStats
    {
        $instance = new self;
        foreach ($array as $key => $value) {
            if (property_exists($instance, $key)) {
                $instance->{$key} = $value;
            }
        }
        return $instance;
    }

    public static function fromJson(string $json): ImportStats
    {
        return self::fromArray(json_decode($json, true));
    }

    public function add(ImportStats $stats): ImportStats
    {
        foreach ($stats as $stat => $value) {
            $this->{$stat} += $value;
        }
        return $this;
    }
}
