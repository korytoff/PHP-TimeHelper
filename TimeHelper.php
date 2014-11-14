<?php

class TimeHelper {

    private $_dateTime;
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

    static function create($date = null, $format = self::DATETIME) {
        $className = __CLASS__;
        return new $className($date, $format);
    }

    public function __toString() {
        return $this->datetime();
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

    public function datetime($time = true) {
        $result = '';
        $result .= $this->_dateTime->format('Y-m-d');
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
            $result = 'Сегодня';
        }
        elseif ($today->diff($date)->format('%R%a') === '+1') {
            $result = 'Завтра';
        }
        elseif ($today->diff($date)->format('%R%a') === '-1') {
            $result = 'Вчера';
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

}
