<?php

/*
 *	Copyright 2015 RhubarbPHP
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

namespace Rhubarb\Crown\Modelling;

use JsonSerializable;
use Rhubarb\Crown\Events\EventEmitter;

/**
 * A starting point for modelling objects, not necessarily just those connected to databases.
 *
 * This class provides an implementation of magical setters and getters providing access to a
 * dictionary of underlying model data
 */
class ModelState implements \ArrayAccess, JsonSerializable
{
    use EventEmitter {
        raiseEvent as traitRaiseEvent;
    }

    /**
     * The dictionary of current model data.
     *
     * @var array
     */
    protected $modelData = [];

    /**
     * The dictionary of model data from the last change snapshot
     * @var array
     */
    protected $changeSnapshotData = [];

    /**
     * A collection of property changed handlers.
     *
     * @var array
     */
    private $propertyChangedCallbacks = [];

    public function __construct()
    {
        $this->attachPropertyChangedNotificationHandlers();
    }

    /**
     * Override this to attach internal property change notification handlers.
     */
    protected function attachPropertyChangedNotificationHandlers()
    {

    }

    public final function addPropertyChangedNotificationHandler($propertyNames, $callback)
    {
        if (!is_array($propertyNames)) {
            $propertyNames = [$propertyNames];
        }

        foreach ($propertyNames as $propertyName) {
            $this->propertyChangedCallbacks[$propertyName][] = $callback;
        }
    }

    /**
     * Set's the model property by name and value.
     *
     * If the model data has changed then HasChanged() will return true. Note that the comparison is not strict
     * so changing a property from "123" to 123 will not cause the model to appear as changed.
     *
     * @see HasChanged()
     * @param $propertyName
     * @param $value
     */
    public function __set($propertyName, $value)
    {
        if (method_exists($this, "set" . $propertyName)) {
            call_user_func([$this, "set" . $propertyName], $value);
            return;
        }

        $this->setModelValue($propertyName, $value);
    }

    /**
     * Sets a models property value while also raising property changed notifications if appropriate.
     *
     * This should be used from setters instead of changing $this->modelData directly.
     *
     * @param $propertyName
     * @param $value
     */
    protected final function setModelValue($propertyName, $value)
    {
        try {
            $oldValue = $this->$propertyName;
        } catch (\Exception $ex) {
            // Catch any exceptions thrown when trying to retrieve the old value for the sake
            // of comparison to trigger the property changed handlers.
            $oldValue = null;
        }

        $this->modelData[$propertyName] = $value;

        if ($value instanceof ModelState) {
            $this->attachChangeListenerToModelProperty($propertyName, $value);
        }

        if ($oldValue != $value) {
            $this->raisePropertyChangedCallbacks($propertyName, $value, $oldValue);

            $this->traitRaiseEvent("AfterChange", $this);
        }
    }

    protected function raisePropertyChangedCallbacks($propertyName, $newValue, $oldValue)
    {
        if (isset($this->propertyChangedCallbacks[$propertyName])) {
            foreach ($this->propertyChangedCallbacks[$propertyName] as $callBack) {
                $callBack($newValue, $propertyName, $oldValue);
            }
        }
    }

    /**
     * Get's the model property by name.
     *
     * @param $propertyName
     * @return mixed
     */
    public function __get($propertyName)
    {
        if (method_exists($this, "get" . $propertyName)) {
            return call_user_func([$this, "get" . $propertyName]);
        }

        if (isset($this->modelData[$propertyName])) {
            return $this->modelData[$propertyName];
        }

        return null;
    }

    public function __unset($name)
    {
        $this->offsetUnset($name);
    }

    public function __isset($name)
    {
        $isset = isset($this->modelData[$name]);

        if ($isset) {
            return true;
        } else {
            if (method_exists($this, "get" . $name)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns an array of properties that are public and can be serialised.
     *
     * This protects the unwary developer from exposing internal secrets by using models that be serialised or
     * published. It is a chore to populate but it is better to be safe than sorry!
     *
     * @return Array
     */
    protected function getPublicPropertyList()
    {
        // Nothing to be public by default.
        return [];
    }

    /**
     * Exports an array of model values that have been marked safe for public consumption.
     *
     * @return Array
     */
    public final function exportPublicData()
    {
        $publicProperties = $this->getPublicPropertyList();

        $data = [];

        foreach ($publicProperties as $property) {
            if (isset($this[$property])) {
                $data[$property] = $this[$property];
            }
        }

        return $data;
    }

    /**
     * Returns the list of properties to export when calling ExportData()
     *
     * By default all properties stored in modelData are returned.
     *
     * @return array
     */
    protected function getExportedPropertyList()
    {
        return array_keys($this->modelData);
    }

    public function exportData()
    {
        $data = [];

        $keys = $this->getExportedPropertyList();

        foreach ($keys as $property) {
            if (isset($this[$property])) {
                $data[$property] = $this[$property];
            }
        }

        return $data;
    }

    /**
     * Imports an array of model values that have been marked safe for public consumption.
     *
     * @param Array $data
     */
    public final function importData($data)
    {
        foreach ($data as $property => $value) {
            $this[$property] = $value;
        }
    }

    /**
     * Returns true if the model has changed
     *
     * @return bool
     */
    public function hasChanged()
    {
        foreach ($this->modelData as $key => $value) {
            if (!isset($this->changeSnapshotData[$key]) || $this->changeSnapshotData[$key] != $value) {
                // Something was added or changed.
                return true;
            }
        }

        foreach ($this->changeSnapshotData as $key => $value) {
            if (!isset($this->modelData[$key])) {
                // Something was removed.
                return true;
            }
        }

        return false;
    }

    /**
     * Takes a snapshot of the model data into change state data.
     *
     * Essentially this resets the change status on the model object. HasChanged() should return
     * false after a call to this.
     *
     * @return array
     */
    public function takeChangeSnapshot()
    {
        $this->changeSnapshotData = $this->modelData;

        foreach ($this->changeSnapshotData as $key => $value) {
            if (is_object($value)) {
                $this->changeSnapshotData[$key] = clone $value;
            }
        }

        return $this->changeSnapshotData;
    }

    public function getModelChanges()
    {
        $differences = [];
        /**
         * array_diff_assoc couldn't tell that two RhubarbDateTimes were different
         * so we're not using that any more.
         * */
        foreach ($this->modelData as $key => $modelDataValue) {
            if (
                (!isset($this->changeSnapshotData[$key]) && $modelDataValue) ||
                (isset($this->changeSnapshotData[$key]) && $this->changeSnapshotData[$key] != $modelDataValue)
            ) {
                $differences[$key] = $modelDataValue;
            }
        }
        return $differences;
    }

    /**
     * Returns true if the specified property has changed since the last snapshot
     */
    public function hasPropertyChanged($propertyName)
    {
        if (!isset($this->modelData[$propertyName])) {
            return false;
        }

        if (!isset($this->changeSnapshotData[$propertyName])) {
            return true;
        }

        if ($this->modelData[$propertyName] != $this->changeSnapshotData[$propertyName]) {
            return true;
        }

        return false;
    }

    /**
     * Exports the raw underlying model data.
     *
     * This should not be used unless you fully understand the difference between this method
     * and ExportData()
     *
     * @see Rhubarb\Crown\Data\Repositories\Repository::StoreObjectData()
     */
    public function exportRawData()
    {
        return $this->modelData;
    }

    /**
     * @param $data
     */
    public function mergeRawData($data)
    {
        if (is_array($data)) {
            $this->modelData = array_merge($this->modelData, $data);
        }

        foreach ($data as $propertyName => $item) {
            if ($item instanceof ModelState) {
                $this->attachChangeListenerToModelProperty($propertyName, $item);
            }
        }

        $this->onDataImported();
    }

    /**
     * Imports raw model data into the model.
     *
     * The data does not pass through any applicable Set methods or data transforms. If required to do so
     * call ImportData() instead, but understand the performance penalty of doing so.
     *
     * @param Array $data
     */
    public function importRawData($data)
    {
        $this->modelData = $data;

        foreach ($data as $propertyName => $item) {
            if ($item instanceof ModelState) {
                $this->attachChangeListenerToModelProperty($propertyName, $item);
            }
        }

        // Make sure we can track changes in existing models.
        $this->takeChangeSnapshot();

        $this->onDataImported();
    }

    /**
     * Attaches a change listener to the model state item and raises a property changed notification when that happens.
     *
     * @param $propertyName
     * @param ModelState $item
     */
    private function attachChangeListenerToModelProperty($propertyName, ModelState $item)
    {
        $item->clearEventHandlers();
        $item->attachEventHandler("AfterChange", function () use ($propertyName, $item) {
            $this->raisePropertyChangedCallbacks($propertyName, $item, null);
        });
    }

    /**
     * Called when data is imported into the model.
     */
    protected function onDataImported()
    {
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return isset($this->$offset);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return $this->__get($offset);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->__set($offset, $value);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->modelData[$offset]);
    }

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    public function jsonSerialize()
    {
        return $this->getSerializableForm();
    }

    public function getSerializableForm($columns = [])
    {
        $data = $this->exportPublicData();

        if (count($columns) > 0) {
            $data = array_intersect_key($data, array_flip($columns));
        }

        return $data;
    }
}
