<?php

namespace Geriano\Session\Handlers;

use Geriano\Encryption\Encryptor;
use SessionHandlerInterface;

abstract class BaseHandler implements SessionHandlerInterface
{
  /**
   * @var \Geriano\Encryption\Encryptor
   */
  protected Encryptor $encryptor;

  /**
   * @param string $key
   */
  public function __construct(string $key)
  {
    $this->encryptor = new Encryptor(
      strlen($key) >= 32 ? $key : md5($key), 'AES-256-CBC'
    );
  }

  /**
   * Encrypt data
   * 
   * @param string $data 
   * @return string
   */
  protected function encrypt(string $data) : string 
  {
    if($data)
      return $this->encryptor->encrypt($data);

    return $data;
  }

  /**
   * Decrypt data
   * 
   * @param string $data 
   * @return string
   */
  protected function decrypt(string $data) : string 
  {
    if($data)
      return $this->encryptor->decrypt($data);

    return $data;
  }
}