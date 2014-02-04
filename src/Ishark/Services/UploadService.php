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
     * @param string $content
     * @param string $contentType
     *
     * @return string
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
                ContentTypeService::check($imageInfo['mime']);
            } catch (\Exception $e) {
                unlink($tmpPath);
                throw $e;
            }
        }

        $md5Hash = md5_file($tmpPath);
        $ext = ContentTypeService::toExt($contentType);
        $filePath = $this->app->getWebPath() . '/images/' . $md5Hash . '.' . $ext;
        rename($tmpPath, $filePath);
        return ConvertService::packmd5($md5Hash) . '.' . $ext;
    }


}