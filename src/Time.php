<?php

namespace Rapture\Helper;

/**
 * Class Time
 *
 * Goal of this class is to merge most of the implementations from:
 * - https://github.com/briannesbitt/Carbon
 * - https://github.com/cakephp/chronos
 * - https://github.com/moment/moment
 *
 * Main features:
 * - extend \DateTime
 * - immutable modifiers
 * - extra helper functions
 * - avoid magic getters/setters
 * - simple and short naming
 * - no dependencies
 *
 * @package Rapture\Helper
 * @author  Iulian N. <rapture@iuliann.ro>
 * @license LICENSE MIT
 */
class Time extends \DateTime
{
    const YEAR   = 'y';
    const MONTH  = 'm';
    const WEEK   = 'w';
    const DAY    = 'd';
    const HOUR   = 'h';
    const MINUTE = 'i';
    const SECOND = 's';
    const QUARTER= 'q';

    const MONDAY    = 1;
    const TUESDAY   = 2;
    const WEDNESDAY = 3;
    const THURSDAY  = 4;
    const FRIDAY    = 5;
    const SATURDAY  = 6;
    const SUNDAY    = 7;

    const JANUARY   = 1;
    const FEBRUARY  = 2;
    const MARCH     = 3;
    const APRIL     = 4;
    const MAY       = 5;
    const JUNE      = 6;
    const JULY      = 7;
    const AUGUST    = 8;
    const SEPTEMBER = 9;
    const OCTOBER   = 10;
    const NOVEMBER  = 11;
    const DECEMBER  = 12;

    const UTC = 'UTC';

    protected $days = [
        self::MONDAY    =>  'Monday',
        self::TUESDAY   =>  'Tuesday',
        self::WEDNESDAY =>  'Wednesday',
        self::THURSDAY  =>  'Thursday',
        self::FRIDAY    =>  'Friday',
        self::SATURDAY  =>  'Saturday',
        self::SUNDAY    =>  'Sunday',
    ];

    /*
     * Locale
     */

    protected static $locale = [
        self::YEAR  =>  ['0 years', 'one year', '%d years'],
        self::MONTH =>  ['0 months','one month','%d months'],
        self::WEEK  =>  ['0 weeks', 'one week', '%d weeks'],
        self::DAY   =>  ['0 days',  'one day',  '%d days'],
        self::HOUR  =>  ['0 hours', 'one hour', '%d hours'],
        self::MINUTE=>  ['0 minutes', 'one minute', '%d minutes'],
        self::SECOND=>  ['0 seconds', 'one second', '%d seconds'],
        'separator' =>  ' ',
        '&'         =>  'and',
        'ago'       =>  '%s ago',
        'from_now'  =>  '%s from now',
        'after'     =>  '%s after',
        'before'    =>  '%s before',
        'more'      =>  'more than %s'
    ];

    /**
     * @param array $locale Locale name
     *
     * @return void
     */
    public static function setLocale(array $locale)
    {
        self::$locale = $locale + self::$locale;
    }

    /**
     * @return array
     */
    public static function getLocale():array
    {
        return self::$locale;
    }

    /**
     * Example:
     * - safeTimezone(null)
     * - safeTimezone(320)
     * - safeTimezone(new DateTimeZone)
     * - safeTimezone('Europe/Bucharest')
     * - safeTimezone('Bucharest')
     *
     * @param mixed $tz Timezone
     *
     * @return \DateTimeZone
     */
    public static function safeTimezone($tz)
    {
        if ($tz === null) {
            return new \DateTimeZone(date_default_timezone_get());
        }

        if ($tz instanceof \DateTimeZone) {
            return $tz;
        }

        if (is_scalar($tz)) {
            $list = \DateTimeZone::listIdentifiers();

            if (isset($list[$tz])) {
                return new \DateTimeZone($list[$tz]);
            }

            if (in_array($tz, $list)) {
                return new \DateTimeZone($tz);
            }

            foreach ($list as $idx => $tzName) {
                if (strpos($tzName, "/{$tz}")) {
                    return new \DateTimeZone($tzName);
                }
            }
        }

        throw new \InvalidArgumentException("Invalid timezone: {$tz}");
    }

    /**
     * Examples:
     * - go(123128302192, 'Berlin')
     * - go('2012-01-01', 'Bucharest')
     * - go('2012-01-01 12:00:00', 'Paris')
     * - go(new \DateTime)
     * - go([2000, 01, 01, 12, 0, 0])
     *
     * @param mixed $time Time value
     * @param mixed $tz   Timezone value
     *
     * @return self
     */
    public static function go($time = null, $tz = null)
    {
        if ($time === null) {
            return self::now($tz);
        }

        if (is_int($time)) {
            return (new self(null, self::safeTimezone($tz)))->setTimestamp($time);
        }

        if (is_string($time)) {
            return new self($time, self::safeTimezone($tz));
        }

        if (is_array($time)) {
            $time += [2000, 1, 1, 0, 0, 0];
            return new self("{$time[0]}-{$time[1]}-{$time[2]} {$time[3]}:{$time[4]}:{$time[5]}", self::safeTimezone($tz));
        }

        if ($time instanceof \DateTime || $time instanceof \DateTimeImmutable) {
            return new self($time->format('Y-m-d H:i:s'), self::safeTimezone($tz));
        }

        throw new \InvalidArgumentException("Invalid value for time: {$time}");
    }

    /**
     * @param mixed $tz Timezone
     *
     * @return Time
     */
    public static function now($tz = null)
    {
        return new self('now', self::safeTimezone($tz));
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toDateTime();
    }

    /*
     * Getters
     */

    /**
     * Get YEAR
     *
     * @return int
     */
    public function getYear():int
    {
        return (int)$this->format('Y');
    }

    /**
     * Get MONTH
     *
     * @return int
     */
    public function getMonth():int
    {
        return (int)$this->format('m');
    }

    /**
     * Get DAY
     *
     * @return int
     */
    public function getDay():int
    {
        return (int)$this->format('d');
    }

    /**
     * Get HOUR
     *
     * @return int
     */
    public function getHour():int
    {
        return (int)$this->format('H');
    }

    /**
     * Get MINUTE
     *
     * @return int
     */
    public function getMinute():int
    {
        return (int)$this->format('i');
    }

    /**
     * Get SECOND
     *
     * @return int
     */
    public function getSecond():int
    {
        return (int)$this->format('s');
    }

    /**
     * ISO-8601 numeric representation of the day of the week
     * 1=Monday...7=Sunday
     *
     * @return int
     */
    public function getDayOfWeek():int
    {
        return (int)$this->format('N');
    }

    /**
     * The day of the year (starting from 0)
     * 0 through 365
     *
     * @return int
     */
    public function getDayOfYear():int
    {
        return (int)$this->format('z');
    }

    /**
     * ISO-8601 week number of year, weeks starting on Monday
     *
     * @return int
     */
    public function getWeekOfYear():int
    {
        return (int)$this->format('W');
    }

    /**
     * Days in month
     *
     * @return int
     */
    public function getDaysInMonth():int
    {
        return (int)$this->format('t');
    }

    /**
     * Get QUARTER
     *
     * @return int
     */
    public function getQuarter():int
    {
        return ($this->getMonth() - 1 / 3) + 1;
    }

    /*
     * Checks
     */

    /**
     * Check if WEEKEND
     *
     * @return bool
     */
    public function isWeekend():bool
    {
        return $this->getDayOfWeek() > self::FRIDAY;
    }

    /**
     * Check if WEEKDAY
     *
     * @return bool
     */
    public function isWeekday():bool
    {
        return $this->getDayOfWeek() < self::SATURDAY;
    }

    /**
     * Check if TODAY
     *
     * @return bool
     */
    public function isToday():bool
    {
        return $this->toDate() == self::now($this->getTimezone())->toDate();
    }

    /**
     * Check for YESTERDAY
     *
     * @return bool
     */
    public function isYesterday():bool
    {
        return $this->toDate() == self::now()->goBack('P1D')->toDate();
    }

    /**
     * Check for TOMORROW
     *
     * @return bool
     */
    public function isTomorrow():bool
    {
        return $this->toDate() == self::now()->goNext('P1D')->toDate();
    }

    /**
     * Check for next WEEK
     *
     * @return bool
     */
    public function isNextWeek():bool
    {
        return $this->toDate() >= self::now()->goNext('P7D')->goToStartOf(self::WEEK)->toDate()
            && $this->toDate() <= self::now()->goNext('P7D')->goToEndOf(self::WEEK)->toDate();
    }

    /**
     * Check for last WEEK
     *
     * @return bool
     */
    public function isLastWeek():bool
    {
        return $this->toDate() >= self::now()->goBack('P7D')->goToStartOf(self::WEEK)->toDate()
            && $this->toDate() <= self::now()->goBack('P7D')->goToEndOf(self::WEEK)->toDate();
    }

    /**
     * Check for next MONTH
     *
     * @return bool
     */
    public function isNextMonth():bool
    {
        return $this->toDate() >= self::now()->goNext('P1M')->goToStartOf(self::MONTH)->toDate()
            && $this->toDate() <= self::now()->goNext('P1M')->goToEndOf(self::MONTH)->toDate();
    }

    /**
     * Check for last MONTH
     *
     * @return bool
     */
    public function isLastMonth():bool
    {
        return $this->toDate() >= self::now()->goBack('P1M')->goToStartOf(self::MONTH)->toDate()
            && $this->toDate() <= self::now()->goBack('P1M')->goToEndOf(self::MONTH)->toDate();
    }

    /**
     * Check for next YEAR
     *
     * @return bool
     */
    public function isNextYear():bool
    {
        return $this->getYear() == self::now()->getYear() + 1;
    }

    /**
     * Check for last YEAR
     *
     * @return bool
     */
    public function isLastYear():bool
    {
        return $this->getYear() == self::now()->getYear() - 1;
    }

    /**
     * Check if future
     *
     * @return bool
     */
    public function isFuture()
    {
        return $this->toDateTime() > self::now($this->getTimezone())->toDateTime();
    }

    /**
     * Check if present
     *
     * @param int $seconds Limit in seconds
     *
     * @return bool
     */
    public function isPresent($seconds = 2):bool
    {
        return abs($this->getTimestamp() - self::now($this->getTimezone())->getTimestamp()) <= $seconds;
    }

    /**
     * Check if past
     *
     * @return bool
     */
    public function isPast()
    {
        return $this->toDateTime() < self::now($this->getTimezone())->toDateTime();
    }

    /**
     * Check if is current MONTH
     *
     * @return bool
     */
    public function isCurrentMonth():bool
    {
        return (int)$this->getMonth() === (int)self::now($this->getTimezone())->getMonth();
    }

    /**
     * Check if is current YEAR
     *
     * @return bool
     */
    public function isCurrentYear():bool
    {
        return (int)$this->getYear() === (int)self::now($this->getTimezone())->getYear();
    }

    /**
     * Check if is current WEEK
     *
     * @return bool
     */
    public function isCurrentWeek():bool
    {
        return $this->isCurrentYear() && (int)$this->getWeekOfYear() === (int)self::now($this->getTimezone())->getWeekOfYear();
    }

    /**
     * Check if MONDAY
     *
     * @return bool
     */
    public function isMonday():bool
    {
        return $this->getDayOfWeek() == self::MONDAY;
    }

    /**
     * Check if TUESDAY
     *
     * @return bool
     */
    public function isTuesday():bool
    {
        return $this->getDayOfWeek() == self::TUESDAY;
    }

    /**
     * Check if WEDNESDAY
     *
     * @return bool
     */
    public function isWednesday():bool
    {
        return $this->getDayOfWeek() == self::WEDNESDAY;
    }

    /**
     * Check if THURSDAY
     *
     * @return bool
     */
    public function isThursday():bool
    {
        return $this->getDayOfWeek() == self::THURSDAY;
    }

    /**
     * Check if FRIDAY
     *
     * @return bool
     */
    public function isFriday():bool
    {
        return $this->getDayOfWeek() == self::FRIDAY;
    }

    /**
     * Check if SATURDAY
     *
     * @return bool
     */
    public function isSaturday():bool
    {
        return $this->getDayOfWeek() == self::SATURDAY;
    }

    /**
     * Check if SUNDAY
     *
     * @return bool
     */
    public function isSunday():bool
    {
        return $this->getDayOfWeek() == self::SUNDAY;
    }

    /*
     * Modifiers
     */

    /**
     * Examples:
     * Time::goNext(34) // seconds
     * Time::goNext('P1Y2M3DT4H5M6S') = 1 year, 2 months, 3 days, 4 hours, 5 minutes, 6 seconds
     * Time::goNext('2 days')
     *
     * @param mixed $time Time value
     *
     * @return Time
     */
    public function goNext($time):Time
    {
        if (is_numeric($time)) {
            $interval = new \DateInterval("PT{$time}S");
        }
        elseif (strpos($time, ' ')) {
            $interval = \DateInterval::createFromDateString($time);
        }
        else {
            $interval = new \DateInterval($time);
        }

        return (clone $this)->add($interval);
    }

    /**
     * Examples:
     * Time::goBack(34) // seconds
     * Time::goBack('P1Y2M3DT4H5M6S') = 1 year, 2 months, 3 days, 4 hours, 5 minutes, 6 seconds
     * Time::goBack('2 days')
     *
     * @param mixed $time Time value
     *
     * @return Time
     */
    public function goBack($time):Time
    {
        if (is_numeric($time)) {
            $interval = new \DateInterval("PT{$time}S");
        }
        elseif (strpos($time, ' ')) {
            $interval = \DateInterval::createFromDateString($time);
        }
        else {
            $interval = new \DateInterval($time);
        }

        return (clone $this)->sub($interval);
    }

    /**
     * @return Time
     */
    public function goTomorrow()
    {
        return $this->goNext('P1D');
    }

    /**
     * @return Time
     */
    public function goYesterday()
    {
        return $this->goBack('P1D');
    }

    /**
     * @see Time::go()
     *
     * @param mixed $time Time value
     *
     * @return $this
     */
    public function utc($time)
    {
        return self::go($time, self::UTC);
    }

    /**
     * @param string $tz Timezone value
     *
     * @return $this
     */
    public function goTimezone($tz = self::UTC)
    {
        return (clone $this)->setTimezone(Time::safeTimezone($tz));
    }

    /**
     * @param string $time Time value
     *
     * @return Time
     */
    public function goToNext($time)
    {
        return self::go(strtotime('next ' . $this->days[$time], $this->getTimestamp()));
    }

    /**
     * @param string $time Time value
     *
     * @return Time
     */
    public function goToLast($time)
    {
        return self::go(strtotime('last ' . $this->days[$time], $this->getTimestamp()));
    }

    /**
     * Return start of (immutable)
     *
     * @param string $time Time value
     *
     * @return Time
     */
    public function goToStartOf(string $time):Time
    {
        if ($time == self::WEEK) {
            $days = $this->getDayOfWeek() - 1;
            return $this->goBack("P{$days}D")->goToStartOf(self::DAY);
        }

        $formats = [
            self::YEAR  =>  'Y-01-01 00:00:00',
            self::MONTH =>  'Y-m-01 00:00:00',
            self::DAY   =>  'Y-m-d 00:00:00',
            self::HOUR  =>  'Y-m-d H:00:00',
            self::MINUTE=>  'Y-m-d H:i:00',
        ];

        if (isset($formats[$time])) {
            return new self($this->format($formats[$time]), $this->getTimezone());
        }

        throw new \InvalidArgumentException("Invalid value for time: {$time}");
    }

    /**
     * @param string $time Time value
     *
     * @return Time
     */
    public function goToEndOf(string $time):Time
    {
        if ($time == self::WEEK) {
            $days = (7 - $this->getDayOfWeek());
            return $this->goNext("P{$days}D")->goToEndOf(self::DAY);
        }

        $formats = [
            self::YEAR  =>  'Y-12-31 23:59:59',
            self::MONTH =>  'Y-m-t 23:59:59',
            self::DAY   =>  'Y-m-d 23:59:59',
            self::HOUR  =>  'Y-m-d H:59:59',
            self::MINUTE=>  'Y-m-d H:i:59',
        ];

        if (isset($formats[$time])) {
            return new self($this->format($formats[$time]), $this->getTimezone());
        }

        throw new \InvalidArgumentException("Invalid value for time: {$time}");
    }

    /**
     * Set YEAR (immutable)
     *
     * @param int $year Year value
     *
     * @return Time
     */
    public function year(int $year)
    {
        return new self($this->format("{$year}-m-d H:i:s"), $this->getTimezone());
    }

    /**
     * Set MONTH (immutable)
     *
     * @param int $month 1=January
     *
     * @return Time
     */
    public function month(int $month)
    {
        return new self($this->format("Y-{$month}-d H:i:s"), $this->getTimezone());
    }

    /**
     * Set DAY (immutable)
     *
     * @param int $day Day value
     *
     * @return Time
     */
    public function day(int $day)
    {
        return new self($this->format("Y-m-{$day} H:i:s"), $this->getTimezone());
    }

    /**
     * Set HOUR (immutable)
     *
     * @param int $hour Hour value
     *
     * @return Time
     */
    public function hour(int $hour)
    {
        return new self($this->format("Y-m-d {$hour}:i:s"), $this->getTimezone());
    }

    /**
     * Set MINUTE (immutable)
     *
     * @param int $minute Minute value
     *
     * @return Time
     */
    public function minute(int $minute)
    {
        return new self($this->format("Y-m-d H:{$minute}:s"), $this->getTimezone());
    }

    /**
     * Set SECOND (immutable)
     *
     * @param int $second Second value
     *
     * @return Time
     */
    public function second(int $second)
    {
        return new self($this->format("Y-m-d H:i:{$second}"), $this->getTimezone());
    }

    /*
     * Format
     */

    /**
     * @return string
     */
    public function toDate():string
    {
        return $this->format('Y-m-d');
    }

    /**
     * @return string
     */
    public function toTime():string
    {
        return $this->format('H:i:s');
    }

    /**
     * @return string
     */
    public function toDateTime():string
    {
        return $this->format('Y-m-d H:i:s');
    }

    /*
     * Comparisons
     */

    /**
     * @param \DateTime $dt Time object
     *
     * @return bool
     */
    public function isEqualTo(\DateTime $dt):bool
    {
        return $this->toDateTime() === $dt->format('Y-m-d H:i:s');
    }

    /**
     * @param \DateTime $dt Time object
     *
     * @return bool
     */
    public function isBefore(\DateTime $dt):bool
    {
        return $this->toDateTime() < $dt->format('Y-m-d H:i:s');
    }

    /**
     * @param \DateTime $dt Time object
     *
     * @return bool
     */
    public function isAfter(\DateTime $dt):bool
    {
        return $this->toDateTime() > $dt->format('Y-m-d H:i:s');
    }

    /**
     * @param mixed $d1 Time value
     * @param mixed $d2 Time value
     *
     * @return bool
     */
    public function isBetween($d1, $d2):bool
    {
        $d1 = new self($d1, $this->getTimezone());
        $d2 = new self($d2, $this->getTimezone());
        return $this->toDateTime() >= $d1->toDateTime() && $this->toDateTime() <= $d2->toDateTime();
    }

    /*
     * Duration
     */

    /**
     * @param mixed  $time Time value
     * @param string $unit Unit
     * @param null   $tz   Timezone value
     *
     * @return int|string
     */
    public function getUnitDuration($time, string $unit = self::SECOND, $tz = null)
    {
        $interval = $this->diff(self::go($time, $tz));

        switch ($unit) {
            case self::SECOND:
                return $interval->format('%a') * 24 * 3600 + $interval->h * 3600 + $interval->i * 60 + $interval->s;
            case self::MINUTE:
                return $interval->format('%a') * 24 * 60 + $interval->h * 60 + $interval->i;
            case self::HOUR:
                return $interval->format('%a') * 24 + $interval->h;
            case self::DAY:
                return $interval->format('%a');
            case self::WEEK:
                return (int)($interval->format('%a') / 7);
            case self::MONTH:
                return $interval->y * 12 + $interval->m;
            case self::YEAR:
                return $interval->y;
        }

        throw new \InvalidArgumentException("Invalid unit: {$unit}");
    }

    /**
     * @param mixed  $time         Time value
     * @param mixed  $tz           Timezone value
     * @param int    $secondsLimit Calculate only before this limit
     * @param string $limitText    Text to show if limit is exceeded
     *
     * @return string
     */
    public function getHumanDuration($time, $tz = null, $secondsLimit = 0, $limitText = '')
    {
        $locale   = self::getLocale();
        $interval = $this->diff(self::go($time, self::safeTimezone($tz)));
        $isNow    = $this->isPresent(1);

        $units = array_filter(
            [
                self::YEAR  =>  $interval->y,
                self::MONTH =>  $interval->m,
                self::WEEK  =>  (int)($interval->d / 7),
                self::DAY   =>  $interval->d % 7,
                self::HOUR  =>  $interval->h,
                self::MINUTE=>  $interval->i,
                self::SECOND=>  $interval->s,
            ]
        );

        if ($secondsLimit) {
            $durationInSeconds = $this->getUnitDuration($time, self::SECOND, $tz);
            if ($durationInSeconds > $secondsLimit) {
                return sprintf($locale['more'], sprintf($interval->invert ? $locale['ago'] : $locale['from_now'], $limitText));
            }
        }

        foreach ($units as $unit => $count) {
            $units[$unit] = isset($locale[$unit][$count]) && $count < 2
                ? $locale[$unit][$count]
                : sprintf($locale[$unit][2], $count);
        }

        $last = array_pop($units);
        $text = count($units)
            ? implode($locale['separator'], $units) . "{$locale['separator']}{$locale['&']}{$locale['separator']}{$last}"
            : $last;

        return $interval->invert
            ? sprintf($locale[$isNow ? 'ago' : 'before'], $text)
            : sprintf($locale[$isNow ? 'from_now' : 'after'], $text);
    }

    /*
     * Helpers
     */

    /**
     * @param array $values Time values
     *
     * @return Time
     */
    public static function min(array $values):Time
    {
        foreach ($values as $i => $value) {
            $values[$i] = self::go($value)->toDateTime();
        }

        sort($values);

        return new self($values[0]);
    }

    /**
     * @param array $values Time values
     *
     * @return Time
     */
    public static function max(array $values):Time
    {
        $i = 0;
        foreach ($values as $i => $value) {
            $values[$i] = self::go($value)->toDateTime();
        }

        sort($values);

        return new self($values[$i]);
    }

    /**
     * List DAYS
     *
     * @return array
     */
    public static function days():array
    {
        return [
            self::MONDAY    =>  strftime('%A', (new \DateTime('2000-01-03'))->getTimestamp()),
            self::TUESDAY   =>  strftime('%A', (new \DateTime('2000-01-04'))->getTimestamp()),
            self::WEDNESDAY =>  strftime('%A', (new \DateTime('2000-01-05'))->getTimestamp()),
            self::THURSDAY  =>  strftime('%A', (new \DateTime('2000-01-06'))->getTimestamp()),
            self::FRIDAY    =>  strftime('%A', (new \DateTime('2000-01-07'))->getTimestamp()),
            self::SATURDAY  =>  strftime('%A', (new \DateTime('2000-01-08'))->getTimestamp()),
            self::SUNDAY    =>  strftime('%A', (new \DateTime('2000-01-09'))->getTimestamp()),
        ];
    }

    /**
     * List MONTHS
     *
     * @return array
     */
    public static function months():array
    {
        return [
            self::JANUARY   =>  strftime('%B', (new \DateTime('2000-01-01'))->getTimestamp()),
            self::FEBRUARY  =>  strftime('%B', (new \DateTime('2000-02-01'))->getTimestamp()),
            self::MARCH     =>  strftime('%B', (new \DateTime('2000-03-01'))->getTimestamp()),
            self::APRIL     =>  strftime('%B', (new \DateTime('2000-04-01'))->getTimestamp()),
            self::MAY       =>  strftime('%B', (new \DateTime('2000-05-01'))->getTimestamp()),
            self::JUNE      =>  strftime('%B', (new \DateTime('2000-06-01'))->getTimestamp()),
            self::JULY      =>  strftime('%B', (new \DateTime('2000-07-01'))->getTimestamp()),
            self::AUGUST    =>  strftime('%B', (new \DateTime('2000-08-01'))->getTimestamp()),
            self::SEPTEMBER =>  strftime('%B', (new \DateTime('2000-09-01'))->getTimestamp()),
            self::OCTOBER   =>  strftime('%B', (new \DateTime('2000-10-01'))->getTimestamp()),
            self::NOVEMBER  =>  strftime('%B', (new \DateTime('2000-11-01'))->getTimestamp()),
            self::DECEMBER  =>  strftime('%B', (new \DateTime('2000-12-01'))->getTimestamp()),
        ];
    }
}
