<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\App\View\Deployment\Version\Storage;

/**
 * Persistence of deployment version of static files in a local file
 */
class File implements \Magento\Framework\App\View\Deployment\Version\StorageInterface
{
    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    private $directory;

    /**
     * @var string
     */
    private $fileName;

    /**
     * @param \Magento\Framework\Filesystem $filesystem
     * @param string $directoryCode
     * @param string $fileName
     */
    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        $directoryCode,
        $fileName
    ) {
        $this->directory = $filesystem->getDirectoryWrite($directoryCode);
        $this->fileName = $fileName;
    }

    /**
     * {@inheritdoc}
     */
    public function load()
    {
        try {
            return $this->directory->readFile($this->fileName);
        } catch (\Magento\Framework\Exception\FileSystemException $e) {
            throw new \UnexpectedValueException(
                'Unable to retrieve deployment version of static files from the file system.',
                0,
                $e
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function save($data)
    {
        $this->directory->writeFile($this->fileName, $data, 'w');
    }
}
