<?php

namespace Chipsmaster\StrucD;


/**
 * Class for handling structured data.
 * Holds configuration options
 */
class StrucDHandler
{
    static protected $defaultInstance;

    /**
     * Returns common handler with default options
     *
     * @return StrucDHandler
     */
    static public function getDefault()
    {
        if (!self::$defaultInstance) {
            self::$defaultInstance = new self();
        }
        return self::$defaultInstance;
    }



    protected $options;
    protected $context;


    public function __construct($options = null)
    {
        $this->options = $options;
    }

    /**
     * Merges $input into $output and returns output. Behaviour depends on the object's options.
     * Accepts only arrays or null for output and input (scalar empty values normalized to null).
     *
     * @param array|null $output
     * @param array|null $input
     * @return array|null
     */
    public function merge($output = null, $input = null)
    {
        if (is_scalar($output) && !$output) {
            $output = null;
        }
        if (is_scalar($input) && !$input) {
            $input = null;
        }
        if ((!is_null($output) && !is_array($output))
            || (!is_null($input) && !is_array($input))
        ) {
            throw new \InvalidArgumentException("Accepts only arrays or null");
        }
        if (!is_array($output)) {
            return $input;
        }
        if (!is_array($input)) {
            return $output;
        }
        return $this->mergeInternal($output, $input);
    }

    protected function mergeInternal(array $output, array $input)
    {
        // TODO: strategy depends object's options
        if ($this->isArrayList($input) || $this->isArrayList($output)) {
            return $input;
        }
        foreach ($input as $k => $v) {
            if (is_scalar($v) || is_null($v)) {
                $output[$k] = $v;
            } elseif (is_array($v)) {
                $ov = array_key_exists($k, $output) ? $output[$k] : [];
                if (!is_array($ov)) {
                    if (is_object($ov)) {
                        throw new \InvalidArgumentException("Objects not supported");
                    }
                    $ov = [];
                }
                $output[$k] = $this->mergeInternal($ov, $v);
            } else {
                // Objects and resources
                throw new \InvalidArgumentException("Objects not supported");
            }
        }
        return $output;
    }

    protected function isArrayList(array $array)
    {
        if ($array) {
            // TODO: various detection strategies using object's options
            foreach (array_keys($array) as $key) {
                if (!is_int($key) || $key < 0) {
                    return false;
                }
            }
            return true;
        }
        return null;
    }

    public function remove($data, $path)
    {
        $splittedPath = $this->splitPath($path);
        return $this->removeInternal($data, $splittedPath);
    }

    protected function removeInternal($node, array $pathParts)
    {
        if (is_array($node)) {
            $pathPart = array_shift($pathParts);
            $newNode = array();
            foreach ($node as $k => $v) {
                if ($k === $pathPart) {
                    if ($pathParts) {
                        $newNode[$k] = $this->removeInternal($node[$k], $pathParts);
                    }
                } else {
                    $newNode[$k] = $v;
                }
            }
            return $newNode;
        } elseif (is_object($node)) {
            throw new \InvalidArgumentException("Objects not supported");
        }
        return $node;
    }


    public function get($data, $path, $default = null)
    {
        $splittedPath = $this->splitPath($path);
        $this->context = (object)array(
            'defaultResult' => $default,
        );
        return $this->browse($data, $splittedPath);
    }

    protected function browse($node, array $pathParts)
    {
        if (is_array($node)) {
            $pathPart = array_shift($pathParts);
            if (array_key_exists($pathPart, $node)) {
                if ($pathParts) {
                    return $this->browse($node[$pathPart], $pathParts);
                }
                return $node[$pathPart];
            }
        }
        return $this->context->defaultResult;
    }

    public function splitPath($path)
    {
        // TODO: enhance
        $path = trim($path);
        if ($path === '') {
            return array();
        }
        return explode('/', $path);
    }
}