<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Credentials\Provider\File\Plain;

use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Provider\Exception\DuplicateUsernameException;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Provider\File\AbstractCredentialsFileProvider;

class PlainFileCredentialsProvider extends AbstractCredentialsFileProvider
{
    /**
     * @var PlainFileCredentialsProviderOptionsInterface
     */
    private $options;

    public function __construct(
        string $file,
        PlainFileCredentialsProviderOptionsInterface $options = null
    )
    {
        parent::__construct($file);
        $this->options = $options ?? new PlainFileCredentialsProviderOptions();
    }

    public function fetch(...$args) : ?array
    {
        [$username] = $args;

        $fp = fopen($this->getFile()->getRealPath(), 'rb');

        while($line = fgets($fp))
        {
            $needle  = strrpos($line, $this->options->getSeparator());

            /** Bad line */
            if(false === $needle){
                continue;
            }

            $user = substr($line, 0, $needle);
            $pass = substr($line, $needle+1);

            if($username === $user){
                return [
                    'user' => $user,
                    'password' => $pass
                ];
            }
        }

        fclose($fp);

        return null;
    }

    public function create(string $username, string $password, ...$args) : bool
    {
        $return = true;

        if($this->getUser($username)){
            throw new DuplicateUsernameException("$username already exists");
        }

        $fp = fopen($this->getFile()->getRealPath(), 'rb');

        $value = sprintf(
            '%s%s%s',
            $username,
            $this->options->getSeparator(),
            $password
        );

        if(false === fwrite($fp,$value)){
            $return = false;
        }

        fclose($fp);

        return $return;
    }

    public function validate(...$args) : ?array
    {
        [$username, $password] = $args;

        $user = $this->fetch($username);

        if($user && $this->options->getCipherProvider()->compare($password, $user['password'])){
            return $user;
        }

        return null;
    }
}