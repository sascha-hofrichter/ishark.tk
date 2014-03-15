<?php

namespace Ishark\Services;

use Ishark\Application;

class UploadService
{

    /** @var \Ishark\Application */
    protected $app;

    function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @return string
     */
    private function getTmpPath()
    {
        return tempnam($this->app->getRootPath() . '/tmp', 'up_');
    }

    /**
     * @param string $content
     * @return string
     */
    public function fromRaw($content)
    {
        $tmpPath = $this->getTmpPath();
        file_put_contents($tmpPath, $content);
        return $tmpPath;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @return string
     */
    public function fromFile(\Symfony\Component\HttpFoundation\File\UploadedFile $file)
    {
        $tmpPath = $this->getTmpPath();
        $pathInfo = pathinfo($tmpPath);
        $file->move($pathInfo['dirname'], $pathInfo['filename']);
        return $tmpPath;
    }

    /**
     * @param string $tmpPath
     *
     * @return string
     * @throws \Exception
     */
    public function saveFile($tmpPath)
    {
        $imageInfo = getimagesize($tmpPath);
        if (!$imageInfo) {
            unlink($tmpPath);
            throw new \Exception('No Image uploaded', 400);
        }

        $contentType = $imageInfo['mime'];
        try {
            ContentTypeService::check($contentType);
        } catch (\Exception $e) {
            unlink($tmpPath);
            throw $e;
        }

        if (filesize($tmpPath) > 5242880) {
            unlink($tmpPath);
            throw new \Exception('File to big...', 400);
        }

        $md5Hash = md5_file($tmpPath);
        $ext = ContentTypeService::toExt($contentType);

        $pathDir = $this->app->getRootPath() . '/images/';
        $subDir = substr($md5Hash, 0, 2);

        if (!is_dir($pathDir . $subDir)) {
            mkdir($pathDir . $subDir);
        }

        $filePath = $pathDir . $subDir . '/' . $md5Hash . '.' . $ext;
        rename($tmpPath, $filePath);
        return ConvertService::packmd5($md5Hash) . '.' . $ext;
    }


}