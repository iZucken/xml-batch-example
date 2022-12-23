<?php

namespace App\Features\ImportLogs\DTO;

class ImportStats
{
    public int $discovered = 0;
    public int $created = 0;
    public int $updated = 0;
    public int $malformed = 0;

    public static function fromJson(string $json): ImportStats
    {
        $instance = new self;
        foreach (json_decode($json, true) as $stat => $value) {
            if (property_exists($instance, $stat)) {
                $instance->{$stat} = $value;
            }
        }
        return $instance;
    }

    public function add(ImportStats $stats): ImportStats
    {
        foreach ($stats as $stat => $value) {
            $this->{$stat} += $value;
        }
        return $this;
    }
}
