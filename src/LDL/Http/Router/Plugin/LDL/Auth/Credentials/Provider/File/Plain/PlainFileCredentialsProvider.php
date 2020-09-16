<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Credentials\Provider\File\Plain;

use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Provider\File\AbstractCredentialsFileProvider;

class PlainFileCredentialsProvider extends AbstractCredentialsFileProvider
{

    private $separator = ':';

    public function __construct(string $file, string $separator=null)
    {
        parent::__construct($file);
        $this->separator = $separator ?? $this->separator;
    }

    public function fetch(...$args) : ?array
    {
        [$username, $password] = $args;

        $fp = fopen($this->getFile()->getRealPath(), 'rb');

        while($line = fgets($fp))
        {
            $needle  = strrpos($line, $this->separator);

            /** Bad line */
            if(false === $needle){
                continue;
            }

            $user = substr($line, 0, $needle);
            $pass = substr($line, $needle+1);

            if($username === $user && $pass === $password){
                return ['user' => $user];
            }
        }

        fclose($fp);

        return null;
    }
}