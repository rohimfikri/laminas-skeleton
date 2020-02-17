<?php
namespace Core\SaveHandler\Session;

use Laminas\Db\TableGateway\TableGateway;
use Laminas\Session\SaveHandler\SaveHandlerInterface;

/*
CREATE TABLE `_session` (
    `id` varchar(32),
    `name` varchar(32),
    `modified` int,
    `lifetime` int,
    `data` text,
    `uag` varchar(500),
    `ip` varchar(20),
    `uid` varchar(50),
     PRIMARY KEY (`id`, `name`),
     CONSTRAINT UNIQUE session_uq1 (`id`, `name`,`ip`, `uag`,`uid`),
     INDEX session_ip_uag_uid(`ip`,`uag`,`uid`),
     INDEX session_ip_uag(`ip`,`uag`),
     INDEX session_ip_uid(`ip`,`uid`),
     INDEX session_uag_uid(`uag`,`uid`),
     INDEX session_uid(`uid`),
     INDEX session_ip(`ip`),
     INDEX session_uag(`uag`)
);
*/

/**
 * Main Save Handler session save handler
 */
class MainSaveHandler implements SaveHandlerInterface
{
    /**
     * Session Save Path
     *
     * @var string
     */
    protected $sessionSavePath;

    /**
     * Session Name
     *
     * @var string
     */
    protected $sessionName;

    /**
     * Lifetime
     * @var int
     */
    protected $lifetime;

    /**
     * Laminas Db Table Gateway
     * @var TableGateway
     */
    protected $tableGateway;
    protected $container;

    /**
     * DbTableGateway Options
     * @var MainSaveHandlerOptions
     */
    protected $options;

    /**
     * Constructor
     *
     * @param TableGateway $tableGateway
     * @param MainSaveHandlerOptions $options
     */
    public function __construct($container,TableGateway $tableGateway, MainSaveHandlerOptions $options)
    {
        $this->tableGateway = $tableGateway;
        $this->container      = $container;
        $this->options      = $options;
    }

    /**
     * Open Session
     *
     * @param  string $savePath
     * @param  string $name
     * @return bool
     */
    public function open($savePath, $name)
    {
        $this->sessionSavePath = $savePath;
        $this->sessionName     = $name;
        $this->lifetime        = ini_get('session.gc_maxlifetime');

        return true;
    }

    /**
     * Close session
     *
     * @return bool
     */
    public function close()
    {
        return true;
    }

    /**
     * Read session data
     *
     * @param string $id
     * @param bool $destroyExpired Optional; true by default
     * @return string
     */
    public function read($id, $destroyExpired = true)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $uag = $_SERVER['HTTP_USER_AGENT'];
        $row = $this->tableGateway->select([
            $this->options->getIdColumn()   => $id,
            $this->options->getNameColumn() => $this->sessionName,
            $this->options->getUagColumn()   => $uag,
            $this->options->getIPColumn() => $ip,
        ])->current();

        if ($row) {
            if ($row->{$this->options->getModifiedColumn()} +
                $row->{$this->options->getLifetimeColumn()} > time()) {
                return (string) $row->{$this->options->getDataColumn()};
            }
            if ($destroyExpired) {
                $this->destroy($id);
            }
        }
        return '';
    }

    /**
     * Write session data
     *
     * @param string $id
     * @param string $data
     * @return bool
     */
    public function write($id, $data)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $uag = $_SERVER['HTTP_USER_AGENT'];
        $init_container = $this->container->get("container_init");
        $uid = $init_container['uid'];
        // !d($init_container);die();
        $data = [
            $this->options->getModifiedColumn() => time(),
            $this->options->getDataColumn()     => (string) $data,
            $this->options->getUIDColumn() => $uid,
        ];

        $rows = $this->tableGateway->select([
            $this->options->getIdColumn()   => $id,
            $this->options->getNameColumn() => $this->sessionName,
            $this->options->getUagColumn()   => $uag,
            $this->options->getIPColumn() => $ip,
        ])->current();

        // !d($rows,$this->options);
        if ($rows) {
            $ret = $this->tableGateway->update($data, [
                $this->options->getIdColumn()   => $id,
                $this->options->getNameColumn() => $this->sessionName,
                $this->options->getUagColumn()   => $uag,
                $this->options->getIPColumn() => $ip,
            ]);
            // !d($ret);
            return (bool) $ret;
        }
        $data[$this->options->getLifetimeColumn()] = $this->lifetime;
        $data[$this->options->getIdColumn()]       = $id;
        $data[$this->options->getNameColumn()]     = $this->sessionName;
        $data[$this->options->getUagColumn()]       = $uag;
        $data[$this->options->getIPColumn()]     = $ip;
        $data[$this->options->getUIDColumn()]     = $uid;

        return (bool) $this->tableGateway->insert($data);
    }

    /**
     * Destroy session
     *
     * @param  string $id
     * @return bool
     */
    public function destroy($id)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $uag = $_SERVER['HTTP_USER_AGENT'];
        if (! (bool) $this->read($id, false)) {
            return true;
        }

        return (bool) $this->tableGateway->delete([
            $this->options->getIdColumn()   => $id,
            $this->options->getNameColumn() => $this->sessionName,
            $this->options->getUagColumn()   => $uag,
            $this->options->getIPColumn() => $ip,
        ]);
    }

    /**
     * Garbage Collection
     *
     * @param int $maxlifetime
     * @return true
     */
    public function gc($maxlifetime)
    {
        $platform = $this->tableGateway->getAdapter()->getPlatform();
        return (bool) $this->tableGateway->delete(
            sprintf(
                '%s < %d',
                $platform->quoteIdentifier($this->options->getModifiedColumn()),
                (time() - $this->lifetime)
            )
        );
    }
}
