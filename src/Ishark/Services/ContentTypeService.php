<?php

namespace Ishark\Services;


class ContentTypeService
{
    /**
     * @param string $contentType
     *
     * @throws \Exception
     */
    public static function check($contentType)
    {
        if (!in_array($contentType, array('image/png', 'image/gif', 'image/jpg', 'image/jpeg'))) {
            throw new \Exception('Wrong Content-Type', 400);
        }
    }

    public static function toExt($contenType)
    {
        switch ($contenType) {
            case 'image/png':
                return 'png';
            case 'image/gif':
                return 'gif';
            case 'image/jpg':
            case 'image/jpeg':
                return 'jpg';
        }
        throw new \Exception('Ext not exist for :' . $contenType, 400);
    }

    public static function toType($ext)
    {
        switch ($ext) {
            case 'png':
                return 'image/png';
            case 'gif':
                return 'image/gif';
            case 'jpg':
            case 'jpeg':
                return 'image/jpg';
        }
        throw new \Exception('Content-Type not exist for :' . $ext, 400);
    }


} 