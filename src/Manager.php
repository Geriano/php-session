<?php

namespace Geriano\Session;

use ArrayIterator;
use Exception;
use IteratorAggregate;
use SessionHandlerInterface;

class Manager implements IteratorAggregate
{
  /**
   * 
   */
  public function __construct(
    protected SessionHandlerInterface $handler,
    protected array $config = [],
  )
  {
    $this->flash = [];

    if(session_status() === PHP_SESSION_ACTIVE)
      throw new Exception('You don\'t need to manually use session_start()');

    if(! $handler instanceof NullHandler) {
      $path     = $config['path'] ?? ini_get('session.save_path');
      $lifetime = $config['lifetime'] ?? ini_get('session.gc_maxlifetime');
      $domain   = $config['domain'] ?? null;
      $secure   = $config['secure'] ?? false;
      $http     = $config['http_only'] ?? true;

      session_set_save_handler($handler, true);
      session_save_path($path);
      // session_set_cookie_params($lifetime, $path, $domain, $secure, $http);

      ini_set('session.gc_maxlifetime', $lifetime);
    }
    
    session_name('GCode_Session');

    ini_set('session.use_cookies', 1);
    ini_set('session.use_trans_sid', 0);
    ini_set('session.use_strict_mode', 1);
    ini_set('session.use_only_cookies', 1);

    session_start();
  }

  /**
   * @return string
   */
  public function __toString()
  {
    return json_encode($this->all(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
  }

  /**
   * Get all registered session
   * 
   * @return array
   */
  public function all()
  {
    return $_SESSION;
  }

  /**
   * @return \ArrayIterator
   */
  public function getIterator()
  {
    return new ArrayIterator($this->all());
  }

  /**
   * Get session from key
   * 
   * @param string $key
   * @param mixed $default
   * @return mixed
   */
  public function get(string $key, $default = null)
  {
    
    if($this->has($key)) {
      $result = $_SESSION[$key];

      if(in_array($key, $this->flash))
        $this->remove($key);

      return $result;
    }

    return $default;
  }

  /**
   * Set value to session
   * 
   * @param string|array $key
   * @param mixed $value
   * @param bool $replace
   * @return mixed
   */
  public function set(string|array $key, $value = null, bool $replace = true)
  {
    if(is_array($key)) {
      foreach($key as $k => $v) {
        $this->set($k, $v, $replace);
      }

      return $this;
    }

    if($this->has($key)) {
      if($replace) {
        return $this->remove($key)->set($key, $value, $replace);
      }
    } else {
      $_SESSION[$key] = $value;
    }
    

    return $this;
  }

  /**
   * Check if key in session
   * 
   * @param string $key
   * @return bool
   */
  public function has(string $key)
  {
    return array_key_exists($key, $_SESSION);
  }

  /**
   * Remove value from session
   * 
   * @param mixed $key
   * @return mixed
   */
  public function remove(...$key)
  {
    if(is_array($key[0])) {
      return $this->remove(...$key[0]);
    }

    foreach($key as $k) {
      if($this->has($k))
        unset($_SESSION[$k]);
    }

    return $this;
  }

  /**
   * Set flash session
   * 
   * @param string $key
   * @param mixed $value
   * @return self
   */
  public function flash(string $key, $value)
  {
    $this->flash[] = $key;
    $this->set($key, $value);

    return $this;
  }
}