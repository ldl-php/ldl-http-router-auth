<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Credentials\Provider\File;

use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Provider\CredentialsProviderInterface;

interface AbstractCredentialsFileProviderInterface extends CredentialsProviderInterface
{
    /**
     * Obtains the credentials file
     * @return \SplFileInfo
     */
    public function getFile() : \SplFileInfo;
}