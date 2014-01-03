<?php
namespace General;

class TimeMeasurementEntry {

    /**
     * @var float
     */
    private $start = null;

    /**
     * @var float
     */
    private $stop = null;

    /**
     * @param float $start
     * @param float $stop
     */
    public function __construct($start = null, $stop = null)
    {
        $this->start = $start;
        $this->stop = $stop;
    }

    /**
     * @return float|null
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @param float $start
     */
    public function setStart($start)
    {
        $this->start = $start;
    }

    /**
     * @param float $stop
     */
    public function setStop($stop)
    {
        $this->stop = $stop;
    }

    /**
     * @return float|null
     */
    public function getStop()
    {
        return $this->stop;
    }

}

class TimeMeasurement {

    /**
     * @var array
     */
    static private $measurements;

    private function __construct() {

    }

    /**
     * @param string $sName
     */
    static public function start($sName) {
        self::$measurements[$sName] = new TimeMeasurementEntry(microtime(true));
    }

    /**
     * @param string $sName
     * @throws \Exception
     */
    static public function stop($sName) {

        if (empty(self::$measurements[$sName])) {
            throw new \Exception('Time measurement not initiated');
        }

        /** @noinspection PhpUndefinedMethodInspection */
        self::$measurements[$sName]->setStop(microtime(true));
    }

    /**
     * @param string $sName
     * @return float
     */
    static public function get($sName) {
        /** @noinspection PhpUndefinedMethodInspection */
        return self::$measurements[$sName]->getStop() - self::$measurements[$sName]->getStart();
    }

} 