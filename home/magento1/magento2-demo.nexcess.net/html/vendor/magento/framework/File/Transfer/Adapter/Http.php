<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\Framework\File\Transfer\Adapter;

class Http
{
    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\Response
     */
    private $response;

    /**
     * @var \Magento\Framework\File\Mime
     */
    private $mime;

    /**
     * @param \Magento\Framework\App\Response\Http
     * @param \Magento\Framework\File\Mime $mime
     */
    public function __construct(\Magento\Framework\HTTP\PhpEnvironment\Response $response, \Magento\Framework\File\Mime $mime)
    {
        $this->response = $response;
        $this->mime = $mime;
    }

    /**
     * Send the file to the client (Download)
     *
     * @param  string|array $options Options for the file(s) to send
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @return void
     */
    public function send($options = null)
    {
        if (is_string($options)) {
            $filepath = $options;
        } elseif (is_array($options) && isset($options['filepath'])) {
            $filepath = $options['filepath'];
        } else {
            throw new \InvalidArgumentException("Filename is not set.");
        }

        if (!is_file($filepath) || !is_readable($filepath)) {
            throw new \InvalidArgumentException("File '{$filepath}' does not exists.");
        }

        $mimeType = $this->mime->getMimeType($filepath);

        $this->response->setHeader('Content-length', filesize($filepath));
        $this->response->setHeader('Content-Type', $mimeType);

        $this->response->sendHeaders();

        $handle = fopen($filepath, 'r');
        if ($handle) {
            while (($buffer = fgets($handle, 4096)) !== false) {
                echo $buffer;
            }
            if (!feof($handle)) {
                throw new \UnexpectedValueException("Unexpected end of file");
            }
            fclose($handle);
        }
    }
}
