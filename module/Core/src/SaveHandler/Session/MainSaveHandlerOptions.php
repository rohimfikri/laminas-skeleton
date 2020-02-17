<?php
namespace Core\SaveHandler\Session;

use Laminas\Session\Exception;
use Laminas\Stdlib\AbstractOptions;

class MainSaveHandlerOptions extends AbstractOptions
{
    /**
     * ID Column
     * @var string
     */
    protected $idColumn = 'id';

    /**
     * Name Column
     * @var string
     */
    protected $nameColumn = 'name';

    /**
     * Data Column
     * @var string
     */
    protected $dataColumn = 'data';

    /**
     * Lifetime Column
     * @var string
     */
    protected $lifetimeColumn = 'lifetime';

    /**
     * Modified Column
     * @var string
     */
    protected $modifiedColumn = 'modified';

    /**
     * UserAgent Column
     * @var string
     */
    protected $uagColumn = 'uag';

    /**
     * UserID Column
     * @var string
     */
    protected $uidColumn = 'uid';

    /**
     * ipaddress Column
     * @var string
     */
    protected $ipColumn = 'ip';

    /**
     * Set Id Column
     *
     * @param string $idColumn
     * @return MainSaveHandlerOptions
     * @throws Exception\InvalidArgumentException
     */
    public function setIdColumn($idColumn)
    {
        $idColumn = (string) $idColumn;
        if (strlen($idColumn) === 0) {
            throw new Exception\InvalidArgumentException('$idColumn must be a non-empty string');
        }
        $this->idColumn = $idColumn;
        return $this;
    }

    /**
     * Get Id Column
     *
     * @return string
     */
    public function getIdColumn()
    {
        return $this->idColumn;
    }

    /**
     * Set Name Column
     *
     * @param string $nameColumn
     * @return MainSaveHandlerOptions
     * @throws Exception\InvalidArgumentException
     */
    public function setNameColumn($nameColumn)
    {
        $nameColumn = (string) $nameColumn;
        if (strlen($nameColumn) === 0) {
            throw new Exception\InvalidArgumentException('$nameColumn must be a non-empty string');
        }
        $this->nameColumn = $nameColumn;
        return $this;
    }

    /**
     * Get Name Column
     *
     * @return string
     */
    public function getNameColumn()
    {
        return $this->nameColumn;
    }

    /**
     * Set Data Column
     *
     * @param string $dataColumn
     * @return MainSaveHandlerOptions
     * @throws Exception\InvalidArgumentException
     */
    public function setDataColumn($dataColumn)
    {
        $dataColumn = (string) $dataColumn;
        if (strlen($dataColumn) === 0) {
            throw new Exception\InvalidArgumentException('$dataColumn must be a non-empty string');
        }
        $this->dataColumn = $dataColumn;
        return $this;
    }

    /**
     * Get Data Column
     *
     * @return string
     */
    public function getDataColumn()
    {
        return $this->dataColumn;
    }

    /**
     * Set Lifetime Column
     *
     * @param string $lifetimeColumn
     * @return MainSaveHandlerOptions
     * @throws Exception\InvalidArgumentException
     */
    public function setLifetimeColumn($lifetimeColumn)
    {
        $lifetimeColumn = (string) $lifetimeColumn;
        if (strlen($lifetimeColumn) === 0) {
            throw new Exception\InvalidArgumentException('$lifetimeColumn must be a non-empty string');
        }
        $this->lifetimeColumn = $lifetimeColumn;
        return $this;
    }

    /**
     * Get Lifetime Column
     *
     * @return string
     */
    public function getLifetimeColumn()
    {
        return $this->lifetimeColumn;
    }

    /**
     * Set Modified Column
     *
     * @param string $modifiedColumn
     * @return MainSaveHandlerOptions
     * @throws Exception\InvalidArgumentException
     */
    public function setModifiedColumn($modifiedColumn)
    {
        $modifiedColumn = (string) $modifiedColumn;
        if (strlen($modifiedColumn) === 0) {
            throw new Exception\InvalidArgumentException('$modifiedColumn must be a non-empty string');
        }
        $this->modifiedColumn = $modifiedColumn;
        return $this;
    }

    /**
     * Get Modified Column
     *
     * @return string
     */
    public function getModifiedColumn()
    {
        return $this->modifiedColumn;
    }

    /**
     * Set User ID Column
     *
     * @param string $uidColumn
     * @return MainSaveHandlerOptions
     * @throws Exception\InvalidArgumentException
     */
    public function setUIDColumn($uidColumn)
    {
        $uidColumn = (string) $uidColumn;
        if (strlen($uidColumn) === 0) {
            throw new Exception\InvalidArgumentException('$uidColumn must be a non-empty string');
        }
        $this->uidColumn = $uidColumn;
        return $this;
    }

    /**
     * Get User ID Column
     *
     * @return string
     */
    public function getUIDColumn()
    {
        return $this->uidColumn;
    }

    /**
     * Set User Agent Column
     *
     * @param string $uagColumn
     * @return MainSaveHandlerOptions
     * @throws Exception\InvalidArgumentException
     */
    public function setUagColumn($uagColumn)
    {
        $uagColumn = (string) $uagColumn;
        if (strlen($uagColumn) === 0) {
            throw new Exception\InvalidArgumentException('$uagColumn must be a non-empty string');
        }
        $this->uagColumn = $uagColumn;
        return $this;
    }

    /**
     * Get User Agent Column
     *
     * @return string
     */
    public function getUagColumn()
    {
        return $this->uagColumn;
    }

    /**
     * Set IP Address Column
     *
     * @param string $modifiedColumn
     * @return MainSaveHandlerOptions
     * @throws Exception\InvalidArgumentException
     */
    public function setIPColumn($ipColumn)
    {
        $ipColumn = (string) $ipColumn;
        if (strlen($ipColumn) === 0) {
            throw new Exception\InvalidArgumentException('$ipColumn must be a non-empty string');
        }
        $this->ipColumn = $ipColumn;
        return $this;
    }

    /**
     * Get IP Address Column
     *
     * @return string
     */
    public function getIPColumn()
    {
        return $this->ipColumn;
    }
}
