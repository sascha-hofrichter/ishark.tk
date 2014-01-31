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
     * @param string $contentType
     *
     * @throws \Exception
     */
    public function checkContentType($contentType)
    {
        if (!in_array($contentType, array('image/png', 'image/gif', 'image/jpg', 'image/jpeg'))) {
            throw new \Exception('Wrong Content-Type', 500);
        }
    }

    /**
     * @param string $content
     * @param string $contentType
     *
     * @throws \Exception
     */
    public function saveFile($content, $contentType)
    {
        $tmpPath = tempnam($this->app->getRootPath() . '/tmp', 'up_');
        file_put_contents($tmpPath, $content);

        $imageInfo = getimagesize($tmpPath);
        if (!$imageInfo) {
            unlink($tmpPath);
            throw new \Exception('No Image uploaded', 500);
        }

        if (array_key_exists('mime', $imageInfo)) {
            $contentType = $imageInfo['mime'];
            try {
                $this->checkContentType($imageInfo['mime']);
            } catch (\Exception $e) {
                unlink($tmpPath);
                throw $e;
            }
        }
        $newFileName = $this->getFileName($tmpPath, $contentType);
        rename($tmpPath, $this->app->getWebPath() . '/images/' . $newFileName);
    }


    private function getFileName($file, $contentType)
    {
        $hash = md5_file($file);
        $ext = $this->getExtByContentType($contentType);
        return $hash . '.' . $ext;
    }

    public function getExtByContentType($contenType)
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
        throw new \Exception('Ext not exist for :' . $contenType);
    }

}