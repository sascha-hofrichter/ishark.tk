<?php

namespace Ishark\Services;

use Ishark\Application;

class UploadService
{
    public static $maxFileSize = 5242880;

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
     * @param $url
     * @return string
     * @throws \Exception
     */
    public function fromUrl($url)
    {
        $filesize = $this->curl_get_file_size($url);

        if ($filesize == -1 || $filesize > self::$maxFileSize) {
            throw new \Exception('File to big...', 400);
        }

        return $this->fromRaw(file_get_contents($url));
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

        if (filesize($tmpPath) > self::$maxFileSize) {
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

    /**
     * Returns the size of a file without downloading it, or -1 if the file
     * size could not be determined.
     *
     * @param $url - The location of the remote file to download. Cannot
     * be null or empty.
     *
     * @return The size of the file referenced by $url, or -1 if the size
     * could not be determined.
     * @link http://stackoverflow.com/questions/2602612/php-remote-file-size-without-downloading-file
     */
    private function curl_get_file_size($url)
    {
        // Assume failure.
        $result = -1;

        $curl = curl_init($url);

        // Issue a HEAD request and follow any redirects.
        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        #curl_setopt($curl, CURLOPT_USERAGENT, get_user_agent_string());

        $data = curl_exec($curl);
        curl_close($curl);

        if ($data) {
            $content_length = "unknown";
            $status = "unknown";

            if (preg_match("/^HTTP\/1\.[01] (\d\d\d)/", $data, $matches)) {
                $status = (int)$matches[1];
            }

            if (preg_match("/Content-Length: (\d+)/", $data, $matches)) {
                $content_length = (int)$matches[1];
            }

            // http://en.wikipedia.org/wiki/List_of_HTTP_status_codes
            if ($status == 200 || ($status > 300 && $status <= 308)) {
                $result = $content_length;
            }
        }

        return $result;
    }
}