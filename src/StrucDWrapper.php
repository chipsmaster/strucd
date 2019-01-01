<?php


namespace Chipsmaster\StrucD;


/**
 * Holds structured data.
 * Operations based on a StrucDHandler
 */
class StrucDWrapper
{
    protected $handler;
    protected $data;
    protected $json;
    protected $jsonReady;
    protected $dataToUnserialize;


    public function __construct($data = null, $handler = null)
    {
        $this->setHandler($handler ? $handler : StrucDHandler::getDefault());
        $this->setData($data);
    }

    /**
     * @param StrucDHandler $handler
     */
    public function setHandler(StrucDHandler $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @return StrucDHandler
     */
    public function getHandler()
    {
        return $this->handler;
    }


    public function setData($data = null)
    {
        $this->data = $data;
        $this->normalizeData();
        $this->dataToUnserialize = false;
        $this->jsonReady = false;
    }

    protected function normalizeData()
    {
        if ($this->data !== null && !is_array($this->data)) {
            $this->data = null;
        }
    }

    /**
     * Returns original wrapped data or new data after StrucD operations
     *
     * @return mixed|array|null
     */
    public function getData()
    {
        if ($this->dataToUnserialize) {
            $this->unserializeFromJson();
            $this->dataToUnserialize = false;
        }
        return $this->data;
    }

    protected function unserializeFromJson()
    {
        // TODO : check strucd handler to choose preferred output format
        $this->data = $this->json ? json_decode($this->json, true) : null;
        $this->normalizeData();
    }


    public function setJson($json)
    {
        if (!$json || $json[0] !== '{') {
            $json = null;
            $this->data = null;
            $this->dataToUnserialize = false;
        } else {
            $this->dataToUnserialize = true;
        }
        $this->json = $json;
        $this->jsonReady = true;
    }

    /**
     * Gets json representation of normalized struc
     *
     * @return string|null
     */
    public function getJson()
    {
        if (!$this->jsonReady) {
            $this->serializeJson();
            $this->jsonReady = true;
        }
        return $this->json;
    }

    protected function serializeJson()
    {
        $this->json = is_array($this->data) ? json_encode($this->data) : null;
    }

    /**
     * In case data holds objects (directly or not), call this if changes occur on these objects outside of this wrapper
     * to clear serialized versions of the data
     *
     * @return StrucDWrapper
     */
    public function notifyDataChanged()
    {
        $this->jsonReady = false;
        $this->dataToUnserialize = false;
        return $this;
    }


    public function addData($data)
    {
        $this->setData($this->getHandler()->merge($this->getData(), $data));
        return $this;
    }

    public function removeData($path)
    {
        $this->setData($this->getHandler()->remove($this->getData(), $path));
        return $this;
    }

    public function get($path, $default = null)
    {
        return $this->getHandler()->get($this->getData(), $path, $default);
    }
}