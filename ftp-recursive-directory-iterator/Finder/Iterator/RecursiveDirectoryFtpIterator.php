<?php

namespace Finder\Iterator;

use Finder\FtpSplFileInfo;

class RecursiveDirectoryFtpIterator extends FtpSplFileInfo implements \RecursiveIterator
{

    private static $connections = array();

    private $connectionId = false;

    private $contents;

    /**
     * destructor should close ftp connection
     */


    /**
     *
     * Examples:
     *     $i = new RecursiveDirectoryFtpIterator('ftp://example.com/dir');
     *     $i = new RecursiveDirectoryFtpIterator('/pub/dir', self::TYPE_DIRECTORY);
     *     $i = new RecursiveDirectoryFtpIterator('/pub/dir/info.txt', self::TYPE_FILE);
     *
     *
     * @param type $item
     * @param type $type
     * @throws \InvalidArgumentException
     */
    public function __construct($item, $type = self::TYPE_DIRECTORY)
    {
        if (0 === strpos($item, 'ftp://')) {
            $parsedUrl = parse_url($item);
            if ($parsedUrl['scheme'] != 'ftp') {
                throw new \InvalidArgumentException(sprintf('Ftp url expected. Incorrect value: "%s"', $item));
            }
            $defaults = array(
              'user' => 'anonymous',
              'pass' => '',
              'path' => '/'
            );
            $defaults = array_merge($defaults, $parsedUrl);
            $this->setFtpParameters(array(
                'host' => $defaults['host'],
                'user' => $defaults['user'],
                'pass' => $defaults['pass'],
            ));
            $this->connect();
            parent::__construct($defaults['path']);
        } else {
            parent::__construct($item, $type);
        }
    }

    /**
     *
     * @return Boolean
     */
    public function isConnected()
    {
        return is_resource($this->getFtpResource());
    }

    public function getConnectionId()
    {
        return $this->connectionId;
    }

    public function setConnectionId($connectionId)
    {
        $this->connectionId = $connectionId;
    }

    public function setFtpParameters(array $ftpParameters)
    {
        if (empty(self::$connections)) {
            self::$connections[1] = $ftpParameters;
            $this->connectionId = 1;
        } else {
            $prev = self::$connections;
            self::$connections[] = $ftpParameters;
            $tmp = array_keys(array_diff_key(self::$connections, $prev));
            $this->connectionId = $tmp[0];
        }
    }

    public function getFtpParameters()
    {
        return self::$connections[$this->connectionId];
    }

    public function getFtpHost()
    {
        if (isset(self::$connections[$this->connectionId]['host'])) {
            return self::$connections[$this->connectionId]['host'];
        } else {
            return '';
        }
    }

    public function setFtpHost($host)
    {
        self::$connections[$this->connectionId]['host'] = $host;
    }

    public function getFtpUser()
    {
        if (isset(self::$connections[$this->connectionId]['user'])) {
            return self::$connections[$this->connectionId]['user'];
        } else {
            return '';
        }
    }

    public function setFtpUser($user)
    {
        self::$connections[$this->connectionId]['user'] = $user;
    }

    public function getFtpPass()
    {
        if (isset(self::$connections[$this->connectionId]['pass'])) {
            return self::$connections[$this->connectionId]['pass'];
        } else {
            return '';
        }
    }

    public function setFtpPass($pass)
    {
        self::$connections[$this->connectionId]['pass'] = $pass;
    }

    public function getFtpResource()
    {
        if (isset(self::$connections[$this->connectionId]['resource'])) {
            return self::$connections[$this->connectionId]['resource'];
        } else {
            return false;
        }
    }

    public function setFtpResource($resource)
    {
        self::$connections[$this->connectionId]['resource'] = $resource;
    }

    public function connect()
    {
        if (!$this->isConnected()) {
            $ftp_conn_id  = ftp_connect($this->getFtpHost());
            $login_result = ftp_login($ftp_conn_id, $this->getFtpUser(), $this->getFtpPass());

            if ((!$ftp_conn_id) || (!$login_result)) {
                throw new \RuntimeException('Cannnot ftp_connect() or ftp_login()');
            } else {
                $this->setFtpResource($ftp_conn_id);
                ftp_pasv($ftp_conn_id, true);
            }
        }
    }

    /**
     *
     * @return \Symfony\Component\Finder\Iterator\RecursiveDirectoryFtpIterator
     */
    public function getChildren()
    {
        $iterator = new RecursiveDirectoryFtpIterator($this->current()->getPath());
        $iterator->setConnectionId($this->getConnectionId());
        return $iterator;
    }

    /**
     *
     *
     * @return Boolean
     *
     */
    public function hasChildren()
    {
        return $this->current()->isDir();
    }

    /**
     *
     * @return mixed
     *
     */
    public function current()
    {
        return current($this->contents);
    }

    /**
     *
     * @return scalar
     *
     */
    public function key()
    {
        return $this->current()->getFullfilename();
    }

    public function next()
    {
        next($this->contents);
    }

    public function rewind()
    {
        ftp_chdir($this->getFtpResource(), $this->getPath());

        $names = ftp_nlist($this->getFtpResource(),   $this->getFilename());
        $types = ftp_rawlist($this->getFtpResource(), $this->getFilename());

        $this->contents = array();
        foreach ($names as $k => $name) {
            $parsedType = self::parseRawListItem($types[$k]);
            $iterator = new RecursiveDirectoryFtpIterator($this->getItemname($name), $parsedType);
            $iterator->setConnectionId($this->getConnectionId());
            $this->contents[] = $iterator;
        }
    }

    /**
     *
     *
     * @return Boolean
     *
     */
    public function valid()
    {
        return $this->isConnected() && (current($this->contents) instanceof FtpSplFileInfo);
    }

    /**
     *
     *
     *
     * @return Boolean
     *
     */
    public static function isValidFtpUrl($url)
    {
        if (0 !== strpos($url, 'ftp://')) {
            return false;
        }
        $parsedUrl = parse_url($url);
        if ($parsedUrl['scheme'] === 'ftp') {
            return true;
        }
        return false;
    }


    /**
     *
     * Current dir: /my/data/
     *
     * (a) /my/data/lorem/ipsum/one/two/three/
     * (b) /my/data/lorem/ipsum/one/two/three/file.txt
     *
     * ->in('lorem/ipsum')
     *
     *  getSubPath()      for (a) returns one/two
     *  getSubPathname()  for (a) returns one/two/three
     *
     *
     *  getSubPath()      for (b) returns one/two/three
     *  getSubPathname()  for (b) returns one/two/three/file.txt
     *
     *
     * @return Boolean
     *
     */
    public function getSubPath()
    {
        return $this->getPath();
    }

    public function getSubPathname()
    {
        return $this->getFullFilename();
    }

}
