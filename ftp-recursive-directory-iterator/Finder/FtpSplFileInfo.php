<?php

namespace Finder;

class FtpSplFileInfo extends \SplFileInfo
{
    const TYPE_UNKNOWN   = 1;
    const TYPE_DIRECTORY = 2;
    const TYPE_FILE      = 3;
    const TYPE_LINK      = 4;

    private $type     = self::TYPE_DIRECTORY;
    private $path     = '/';
    private $filename = '.';

    public function __construct($item, $type = self::TYPE_DIRECTORY)
    {
        $this->setType($type);

        if ($type === self::TYPE_DIRECTORY) {
            $this->filename = '.';
            $this->path     = $item;
        } else if ($type === self::TYPE_FILE) {
            $tmp = self::parseFile($item);
            $this->filename = $tmp['filename'];
            $this->path     = $tmp['path'];
        }
        parent::__construct($this->getFullFilename());
    }

    /**
     *
     * @return Boolean
     *
     */
    public function isDir()
    {
      return $this->type === self::TYPE_DIRECTORY;
    }

    /**
     *
     * @return Boolean
     *
     */
    public function isFile()
    {
      return $this->type === self::TYPE_FILE;
    }

    /**
     *
     * @return string
     *
     */
    public function getFullFilename()
    {
        if ($this->isDir()) {
            return $this->getPath();
        } else {
            if ($this->path === '/' && $this->filename === '.') {
                return '/';
            }
            if ($this->path === '/') {
                return '/' . $this->filename;
            } else {
                return $this->path . '/' . $this->filename;
            }
        }

        throw new \RuntimeException(sprintf('Object is not a dir nor file! Type: "%i"', $this->getType()));
    }

    /**
     *
     * @return string
     *
     */
    public function getItemname($item)
    {
        if ($this->path === '/') {
            return '/' . $item;
        } else {
            return $this->path . '/' . $item;
        }
    }

    /**
     *
     * @return string
     *
     */
    public function getFilename()
    {
        return $this->filename;
    }

    public function setType($type)
    {
      $this->type = $type;
    }

    public function getType()
    {
      return $this->type;
    }

    public function getPath()
    {
      return $this->path;
    }

    public static function parseRawListItem($item)
    {
        if ($item === '') {
            return self::TYPE_UNKNOWN;
            //throw new \InvalidArgumentException('$item is null!');
        }
        switch ($item[0]) {

            case 'd':
                return self::TYPE_DIRECTORY;

            case '-':
                return self::TYPE_FILE;

            case 'l':
                return self::TYPE_LINK;

            default:
                return self::TYPE_UNKNOWN;
                //throw new \InvalidArgumentException(sprintf('Unknown argument "%s"', $item));

        }
    }

    public static function parseFile($file = '/')
    {
        if (0 !== strpos($file, '/')) {
            throw new \InvalidArgumentException(sprintf('File must start with /. It doesnt: "%s"', $file));
        }

        if (strlen($file) < 2) {
            throw new \InvalidArgumentException(sprintf('The name must contain at least two characters. It doesnt: "%s"', $file));
        }

        $len = strlen($file);
        $found = strrpos($file, '/');
        $tmpPath = substr($file, 0, $found);
        $tmpName = substr($file, $found + 1);
        if ($tmpPath === '') {
            $tmpPath = '/';
        }
        return array(
            'filename'  => $tmpName,
            'path'      => $tmpPath
        );

        //throw new \InvalidArgumentException(sprintf('Unknown argument "%s"', $directory));
    }

    public function __toString()
    {
        return $this->getFullFilename();
    }

}
