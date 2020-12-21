<?php

namespace Geriano\Session\Handlers;

class NullHandler extends BaseHandler
{
  /**
   * @return bool
   */
  public function open(string $path, string $id)
  {
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
   * @return bool
   */
  public function read(string $id)
  {
    return true;
  }

  /**
   * @param string $id
   * @param string $data 
   * @return bool
   */
  public function write($id, $data)
  {
    return true;
  }

  /**
   * @param string $id
   * @return bool
   */
  public function destroy($id)
  {
    return true;
  }

  /**
   * @param int $lifetime
   * @return bool
   */
  public function gc(int $lifetime)
  {
    return true;
  }
}