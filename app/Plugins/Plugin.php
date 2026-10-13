<?php

namespace App\Plugins;

interface Plugin
{
    public function id(): string;

    public function name(): string;

    public function version(): string;

    public function description(): string;

    public function icon(): string;

    public function accent(): string;

    public function url(): string;
}
