<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Credentials\Provider\File;

abstract class AbstractCredentialsFileProvider implements AbstractCredentialsFileProviderInterface
{
    /**
     * @var \SplFileInfo
     */
    private $file;

    public function __construct(string $file)
    {
        $file = new \SplFileInfo($file);

        if(false === $file->getRealPath()){
            $msg = "Credentials File: \"$file\" could not be found!";
            throw new Exception\FileAccessException($msg);
        }

        if(false === $file->isReadable()){
            $msg = "File: \"$file\" is not readable by you! Check file permissions.";
            throw new Exception\FileAccessException($msg);
        }

        if($file->isDir()){
            $msg = "File: \"$file\" is a directory";
            throw new Exception\FileAccessException($msg);
        }

        $this->file = $file;
    }

    public function getFile() : \SplFileInfo
    {
        return $this->file;
    }
}