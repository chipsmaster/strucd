<?php


namespace Chipsmaster\StrucD;


/**
 * Holds structured data.
 * Operations based on a StrucDHandler
 */
class StrucDWrapper
{
    protected $data;
    protected $handler;


    public function __construct($data = null)
    {
        if (!$data) {
            $data = array();
        }
        if (!is_array($data)) {
            throw new \InvalidArgumentException("Accepts only arrays");
        }
        $this->data = $data;
        $this->handler = StrucDHandler::getDefault();
    }

    /**
     * @return StrucDHandler
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @param StrucDHandler $handler
     */
    public function setHandler(StrucDHandler $handler)
    {
        $this->handler = $handler;
    }

    public function addData($data)
    {
        $this->data = $this->getHandler()->merge($this->data, $data);
        return $this;
    }

    public function removeData($path)
    {
        $this->data = $this->getHandler()->remove($this->data, $path);
        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    public function get($path, $default = null)
    {
        return $this->getHandler()->get($this->data, $path, $default);
    }
}