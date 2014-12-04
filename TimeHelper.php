<?php

class TimeHelper {

    private $_dateTime;
    protected $todayStr = 'Сегодня';
    protected $yesterdayStr = 'Вчера';
    protected $tomorrowStr = 'Завтра';
    protected $month = array(
        "Январь",
        "Февраль",
        "Март",
        "Апрель",
        "Май",
        "Июнь",
        "Июль",
        "Август",
        "Сентябрь",
        "Октябрь",
        "Ноябрь",
        "Декабрь"
    );
    protected $monthPlural = array(
        "Января",
        "Февраля",
        "Марта",
        "Апреля",
        "Мая",
        "Июня",
        "Июля",
        "Августа",
        "Сентября",
        "Октября",
        "Ноября",
        "Декабря"
    );
    protected $shortMonth = array(
        "Янв",
        "Фев",
        "Мар",
        "Апр",
        "Май",
        "Июн",
        "Июл",
        "Авг",
        "Сен",
        "Окт",
        "Ноя",
        "Дек"
    );
    protected $day = array(
        'Понедельник',
        'Вторник',
        'Среда',
        'Четверг',
        'Пятница',
        'Суббота',
        'Воскресение',
    );
    protected $shortDay = array(
        'Пн',
        'Вт',
        'Ср',
        'Чт',
        'Пт',
        'Сб',
        'Вс',
    );

    const DATETIME = 'Y-m-d H:i:s';
    const DATE = 'Y-m-d';
    const EUR_DATETIME = 'd.m.Y H:i:s';
    const EUR_DATE = 'd.m.Y';
    const STRDATE = 'd month year time';

    static function create($date = null, $format = self::DATETIME) {
        $className = __CLASS__;
        return new $className($date, $format);
    }

    public function __toString() {
        return $this->datetime();
    }

    function __construct($date = null, $format = self::DATETIME) {
        mb_internal_encoding('UTF-8');
        if ($format === self::STRDATE) {
            $this->_dateTime = $this->parse($date);
        }
        elseif (is_string($date)) {
            $this->_dateTime = DateTime::createFromFormat($format, $date);
        }
        else {
            $this->_dateTime = new DateTime;
        }
        return $this;
    }

    /*
     * Разбор даты из строки вида '2  Мая 2014 в 12:05'
     */

    public function parse($str) {
        $str = mb_strtolower(trim($str));
        preg_match('/^(\d+) +([ъхзщшгнекуцйфывапролджэёюбьтимсчя]+) +(\d{4})[A-zА-я ]+(\d{2}:?\d?\d?)?/', $str, $matches);
        $result['day'] = (isset($matches[1]) && is_numeric($matches[1])) ? str_pad($matches[1], 2, '0', STR_PAD_LEFT) : date('d');
        if (isset($matches[2])) {
            $month = array_map('mb_strtolower', $this->month);
            $monthPlural = array_map('mb_strtolower', $this->monthPlural);
            $shortMonth = array_map('mb_strtolower', $this->shortMonth);
            if (array_keys($month, $matches[2])) {
                $monthNum = array_keys($month, $matches[2]);
            }
            elseif (array_keys($monthPlural, $matches[2])) {
                $monthNum = array_keys($monthPlural, $matches[2]);
            }
            elseif (array_keys($shortMonth, $matches[2])) {
                $monthNum = array_keys($shortMonth, $matches[2]);
            }
        }
        $result['month'] = isset($monthNum[0]) ? str_pad($monthNum[0] * 1 + 1, 2, '0', STR_PAD_LEFT) : date('m');
        $result['year'] = (isset($matches[3]) && strlen($matches[3]) === 4) ? $matches[3] : date('Y');
        $result['time'] = isset($matches[4]) ? str_pad($matches[4], 8, ':00') : "00:00:00";
        $dateStr = $result['year'] . '-' . $result['month'] . '-' . $result['day'] . ' ' . $result['time'];
        return $result ? DateTime::createFromFormat(self::DATETIME, $dateStr) : new DateTime;
    }

    public function plusDay($day = 1) {
        $this->_dateTime->modify("+$day day");
        return $this;
    }

    public function datetime($time = true, $dateFormat = self::DATE) {
        $result = '';
        switch ($dateFormat) {
            case self::DATETIME:
            case self::EUR_DATETIME:
                $time = false;
                break;
            default:
                break;
        }
        $result .= $this->_dateTime->format($dateFormat);
        if ($time) {
            $result .= ' ' . $this->_dateTime->format('H:i:s');
        }
        return $result;
    }

    public function day() {
        $result = '';
        $result .= $this->_dateTime->format('j') * 1;
        return $result;
    }

    public function month($plural = true) {
        $result = '';
        $M = $this->_dateTime->format('n') * 1 - 1;
        if ($plural) {
            $result .= ' ' . mb_convert_case($this->monthPlural[$M], MB_CASE_TITLE);
        }
        else {
            $result .= ' ' . mb_convert_case($this->month[$M], MB_CASE_TITLE);
        }
        return $result;
    }

    public function diff($format = '%i') {
        $now = new DateTime();
        $date = clone $this->_dateTime;
        return $now->diff($date)->format($format);
    }

    public function today($year = true, $time = false) {
        $today = new DateTime;
        $today->setTime(0, 0, 0);
        $date = clone $this->_dateTime;
        $date->setTime(0, 0, 0);
        $result = $this->longDate($year);
        if ($today->diff($date)->format('%a') === '0') {
            $result = $this->todayStr;
        }
        elseif ($today->diff($date)->format('%R%a') === '+1') {
            $result = $this->tomorrowStr;
        }
        elseif ($today->diff($date)->format('%R%a') === '-1') {
            $result = $this->yesterdayStr;
        }
        if ($time) {
            $result .= ' ' . $this->shortTime(false);
        }
        return $result;
    }

    /*
     * Число, месяц, опц. год, опц. время
     */

    public function longDate($year = false, $time = false) {
        $result = '';
        $result .= $this->day();
        $result .= ' ' . $this->month();
        if ($year) {
            $result .= ' ' . $this->_dateTime->format('Y');
        }
        if ($time) {
            $result .= ' ' . $this->shortTime(false);
        }
        return $result;
    }

    /*
     * Опц. день недели, число, 3 буквы месяца
     */

    public function shortDate($day = false) {
        $result = '';
        if ($day) {
            $N = $this->_dateTime->format('N') * 1 - 1;
            if (isset($this->shortDay[$N])) {
                $result .= $this->shortDay[$N] . ', ';
            }
        }
        $result .= $this->_dateTime->format('j') * 1;
        $M = $this->_dateTime->format('n') * 1 - 1;
        $result .= ' ' . mb_strtolower($this->shortMonth[$M]);
        return $result;
    }

    /*
     * Опц. день недели, время
     */

    public function shortTime($day = true) {
        $result = '';
        if ($day) {
            $N = $this->_dateTime->format('N') * 1 - 1;
            if (isset($this->shortDay[$N])) {
                $result .= mb_convert_case($this->shortDay[$N], MB_CASE_TITLE) . ' ';
            }
        }
        $result .= $this->_dateTime->format('H:i');
        return $result;
    }

    /*
     * Выводит год
     * @param $start – год начала интервала, если нужен интервал вида "2014 – 2015"
     */

    public function year($start = false) {
        $result = '';
        if ($start && is_numeric($start) && $start * 1 !== $this->_dateTime->format('Y') * 1) {
            $result = $start . ' – ';
        }
        $result .= $this->_dateTime->format('Y');
        return $result;
    }

}
