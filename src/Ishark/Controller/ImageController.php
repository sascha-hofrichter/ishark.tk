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

            $imageService = $this->getApp()->getImageService();
            $img = imagecreatefromstring(file_get_contents($path));
            $img = $imageService->thumbnail_box($img, 100, 100);
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


} 