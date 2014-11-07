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

    const DATETIME = 'Y-m-d H:s:i';
    const DATE = 'Y-m-d';
    const EUR_DATETIME = 'd.m.Y H:s:i';
    const EUR_DATE = 'd.m.Y';

    static function create($date = null, $format = self::DATETIME) {
        $className = __CLASS__;
        return new $className($date, $format);
    }

    function __construct($date, $format) {
        mb_internal_encoding('UTF-8');
        if (is_string($date)) {
            $this->_dateTime = DateTime::createFromFormat($format, $date);
        }
        else {
            $this->_dateTime = new DateTime;
        }
        return;
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

    public function month($plural = false) {
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

    public function today($year = true) {
        $today = new DateTime;
        $today->setTime(0, 0, 0);
        $date = clone $this->_dateTime;
        $date->setTime(0, 0, 0);
        if ($today->diff($date)->format('%a') === '0') {
            $result = $this->todayStr;
        }
        elseif ($today->diff($date)->format('%R%a') === '+1') {
            $result = $this->tomorrowStr;
        }
        elseif ($today->diff($date)->format('%R%a') === '-1') {
            $result = $this->yesterdayStr;
        }
        else {
            $result = $this->longDate($year);
        }
        return $result;
    }

    /*
     * Число, месяц, опц. год, опц. время
     */

    public function longDate($year = false, $time = false) {
        $result = '';
        $result .= $this->day();
        $result .= ' ' . $this->month(true);
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

}
