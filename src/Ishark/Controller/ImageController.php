<?php

namespace Ishark\Controller;

use Ishark\Services\ContentTypeService;
use Ishark\Services\ConvertService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ImageController extends BaseController
{


    /**
     * @param Request $request
     * @param $image
     * @return Response
     * @throws \Exception
     */
    public function imageAction(Request $request, $image)
    {
        // remove ext
        $info = pathinfo($image);
        if ($info['extension'] == null) {
            throw new \Exception('File not found!', 404);
        }
        $md5 = ConvertService::unpackmd5($info['filename']);
        $path = $this->getApp()->getRootPath() . '/images/' . substr($md5, 0, 2) . '/' . $md5 . '.' . $info['extension'];

        if (!file_exists($path)) {
            throw new \Exception('File not found!', 404);
        }

        return $this->responseImage($path);
    }

    public function imageThumbAction(Request $request, $image)
    {
        $info = pathinfo($image);
        $extension = $info['extension'];
        $filename = $info['filename'];
        if (!$extension || !$filename) {
            throw new \Exception('File not found!', 404);
        }
        $md5 = ConvertService::unpackmd5(substr($filename, 0, -6));
        $path = $this->getApp()->getRootPath() . '/images/' . substr($md5, 0, 2) . '/' . $md5 . '.' . $info['extension'];
        if (!file_exists($path)) {
            throw new \Exception('File not found!', 404);
        }

        $thumbPath = $this->getApp()->getRootPath() . '/images-thumb/' . substr($md5, 0, 2) . '/' . $md5 . '.jpg';

        if (!file_exists($thumbPath)) {
            if (!is_dir($this->getApp()->getRootPath() . '/images-thumb/' . substr($md5, 0, 2))) {
                mkdir($this->getApp()->getRootPath() . '/images-thumb/' . substr($md5, 0, 2));
            }

            $img = imagecreatefromstring(file_get_contents($path));
            $img = $this->thumbnail_box($img, 100, 100);
            imagejpeg($img, $thumbPath, 80);
            imagedestroy($img);
        }

        return $this->responseImage($thumbPath);
    }

    /**
     * @param $path
     * @return Response
     */
    private function responseImage($path)
    {

        $info = pathinfo($path);

        $mtime = @filemtime($path);
        $gmt_mtime = gmdate('D, d M Y H:i:s', $mtime) . ' GMT';
        $etag = sprintf('%08x-%08x', crc32($path), $mtime);

        header('ETag: "' . $etag . '"');
        header('Last-Modified: ' . $gmt_mtime);
        header('Cache-Control: private');

        if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && !empty($_SERVER['HTTP_IF_NONE_MATCH'])) {
            $tmp = explode(';', $_SERVER['HTTP_IF_NONE_MATCH']); // IE fix!
            if (!empty($tmp[0]) && strtotime($tmp[0]) == strtotime($gmt_mtime)) {
                header('HTTP/1.1 304 Not Modified');
                exit;
            }
        }

        if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
            if (str_replace(array('\"', '"'), '', $_SERVER['HTTP_IF_NONE_MATCH']) == $etag) {
                header('HTTP/1.1 304 Not Modified');
                exit;
            }
        }
        return Response::create(file_get_contents($path), 200, array('Content-Type' => ContentTypeService::toType($info['extension'])));
    }

    /**
     * @param $img
     * @param $box_w
     * @param $box_h
     * @return null|resource
     * @link http://stackoverflow.com/questions/747101/resize-crop-pad-a-picture-to-a-fixed-size/747277#747277
     */
    private function  thumbnail_box($img, $box_w, $box_h)
    {
        //create the image, of the required size
        $new = imagecreatetruecolor($box_w, $box_h);
        if ($new === false) {
            //creation failed -- probably not enough memory
            return null;
        }


        //Fill the image with a light grey color
        //(this will be visible in the padding around the image,
        //if the aspect ratios of the image and the thumbnail do not match)
        //Replace this with any color you want, or comment it out for black.
        //I used grey for testing =)
        $fill = imagecolorallocate($new, 200, 200, 205);
        imagefill($new, 0, 0, $fill);

        //compute resize ratio
        $hratio = $box_h / imagesy($img);
        $wratio = $box_w / imagesx($img);
        $ratio = min($hratio, $wratio);

        //if the source is smaller than the thumbnail size,
        //don't resize -- add a margin instead
        //(that is, dont magnify images)
        if ($ratio > 1.0)
            $ratio = 1.0;

        //compute sizes
        $sy = floor(imagesy($img) * $ratio);
        $sx = floor(imagesx($img) * $ratio);

        //compute margins
        //Using these margins centers the image in the thumbnail.
        //If you always want the image to the top left,
        //set both of these to 0
        $m_y = floor(($box_h - $sy) / 2);
        $m_x = floor(($box_w - $sx) / 2);

        //Copy the image data, and resample
        //
        //If you want a fast and ugly thumbnail,
        //replace imagecopyresampled with imagecopyresized
        if (!imagecopyresampled($new, $img,
            $m_x, $m_y, //dest x, y (margins)
            0, 0, //src x, y (0,0 means top left)
            $sx, $sy, //dest w, h (resample to this size (computed above)
            imagesx($img), imagesy($img)) //src w, h (the full size of the original)
        ) {
            //copy failed
            imagedestroy($new);
            return null;
        }
        //copy successful
        return $new;
    }
} 