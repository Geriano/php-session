<?php

namespace Geriano\Session\Handlers;

use Exception;

class FileHandler extends BaseHandler
{
  /**
   * @var string
   */
  private string $path;

  /**
   * @param string $path
   * @param string $id
   * @return bool
   */
  public function open(string $path, string $id)
  {
    $this->path = $this->path ?? rtrim($path, '/') . '/';

    if(! is_dir($path)) {
      if(! mkdir($this->path, 0777, true)) 
        throw new Exception('Can\'t create directory ' . $path);
    }

    touch($this->path . md5($id));

    return true;
  }

  /**
   * @return bool
   */
  public function close()
  {
    return true;
  }

  /**
   * @param string $id
   * @param string $data
   * @return bool
   */
  public function write(string $id, string $data)
  {
    $file = $this->path . md5($id);
    $data = $this->encrypt($data);

    return file_put_contents($file, $data) === false ? false: true;
  }

  /**
   * @param string $id
   * @return string
   */
  public function read(string $id)
  {
    $file = $this->path . md5($id);

    return $this->decrypt((string) @file_get_contents($file));
  }

  /**
   * @param string $id
   * @return bool
   */
  public function destroy(string $id)
  {
    $file = $this->path . md5($id);

    if(file_exists($file)) {
      unlink($file);
    }

    return true;
  }

  /**
   * @param int $lifetime
   * @return bool
   */
  public function gc(int $lifetime)
  {
    foreach (glob("$this->path*") as $file) {
      if (filemtime($file) + $lifetime < time() && file_exists($file)) {
        unlink($file);
      }
    }

    return true;
  }
}