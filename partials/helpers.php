<?php

if (!function_exists('asset_url')) {
    function asset_url($file)
    {
        $r2Base = getenv('R2_ASSET_URL');

        $file = ltrim($file, '/');
        $file = implode(
            '/',
            array_map('rawurlencode', explode('/', $file))
        );

        if (!empty($r2Base)) {
            return rtrim($r2Base, '/') . '/assets/' . $file;
        }

        return '/assets/' . $file;
    }
}
