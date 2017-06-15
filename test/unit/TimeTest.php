<?php

use Rapture\Helper\Time;

class TimeTest extends \PHPUnit_Framework_TestCase
{
    # TIMEZONE

    public function testTimezone()
    {
        $defaultTz = date_default_timezone_get();

        $tz = Time::safeTimezone(null);
        $this->assertEquals($defaultTz, $tz->getName());

        $tz = Time::safeTimezone('Europe/Bucharest');
        $this->assertEquals('Europe/Bucharest', $tz->getName());

        $tz = Time::safeTimezone('Bucharest');
        $this->assertEquals('Europe/Bucharest', $tz->getName());

        $tz = Rapture\Helper\Time::safeTimezone(320);
        $this->assertEquals('Europe/Bucharest', $tz->getName());

        $tz = Rapture\Helper\Time::safeTimezone(new \DateTimeZone('Europe/Bucharest'));
        $this->assertEquals('Europe/Bucharest', $tz->getName());
    }

    public function testTzSwitch()
    {
        $t = Time::go('2017-01-01 02:00:00', 'Bucharest');

        $this->assertEquals('2017-01-01 00:00:00', $t->goTimezone(Time::UTC)->toDateTime());
        $this->assertEquals('2017-01-01 02:00:00', $t->utc($t)->toDateTime());
    }

    # INIT

    public function testGo()
    {
        $t = Time::go(946684800, 'UTC');
        $this->assertEquals('2000-01-01 00:00:00', $t->format('Y-m-d H:i:s'));

        $t = Time::go(new \DateTime('2000-01-01'));
        $this->assertEquals('2000-01-01', $t->format('Y-m-d'));

        $t = Time::go('2000-01-01');
        $this->assertEquals('2000-01-01', $t->format('Y-m-d'));

        $t = Time::go([2000, 1, 1, 12]);
        $this->assertEquals('2000-01-01 12:00:00', $t->format('Y-m-d H:i:s'));
    }

    # GETTERS

    public function testGetters()
    {
        $t = Time::go('2001-02-03 04:05:06');
        $this->assertEquals(2001, $t->getYear());
        $this->assertEquals(2, $t->getMonth());
        $this->assertEquals(3, $t->getDay());
        $this->assertEquals(4, $t->getHour());
        $this->assertEquals(5, $t->getMinute());
        $this->assertEquals(6, $t->getSecond());
    }

    public function testGetters2()
    {
        $t = Time::go('2017-01-02 00:00:00');
        $this->assertEquals(Time::MONDAY, $t->getDayOfWeek());
        $this->assertEquals(1, $t->getDayOfYear());
        $this->assertEquals(1, $t->getWeekOfYear());
        $this->assertEquals(31, $t->getDaysInMonth());
        $this->assertEquals(1, $t->getQuarter());
    }

    # CHECKS

    public function testChecks()
    {
        $t = Time::go('2017-01-02'); // Monday

        $this->assertTrue($t->isMonday());
        $this->assertTrue($t->goNext('P1D')->isTuesday());
        $this->assertTrue($t->goNext('P2D')->isWednesday());
        $this->assertTrue($t->goNext('P3D')->isThursday());
        $this->assertTrue($t->goNext('P4D')->isFriday());
        $this->assertTrue($t->goNext('P5D')->isSaturday());
        $this->assertTrue($t->goNext('P6D')->isSunday());

        $this->assertTrue($t->isWeekday());
        $this->assertTrue($t->goNext('P5D')->isWeekend());
    }

    public function testCustomChecks()
    {
        $t = Time::now();
        $this->assertTrue($t->isToday());
        $this->assertTrue($t->goYesterday()->isYesterday());
        $this->assertTrue($t->goTomorrow()->isTomorrow());
        $this->assertTrue($t->goBack('P7D')->isLastWeek());
        $this->assertTrue($t->goNext('P7D')->isNextWeek());
        $this->assertTrue($t->goBack('P1M')->isLastMonth());
        $this->assertTrue($t->goNext('P1M')->isNextMonth());
        $this->assertTrue($t->goBack('P1Y')->isLastYear());
        $this->assertTrue($t->goNext('P1Y')->isNextYear());

        // extra
        $this->assertTrue($t->goNext('P1D')->isFuture());
        $this->assertTrue($t->goBack('P1D')->isPast());
        $this->assertTrue(Time::now()->goNext(2)->isPresent(3));
        $this->assertTrue($t->isCurrentWeek());
        $this->assertTrue($t->isCurrentMonth());
        $this->assertTrue($t->isCurrentYear());
    }

    # MODIFIERS

    public function testModifiers()
    {
        $t1 = Time::go('2001-01-01 01:01:01');
        $t2 = $t1->year(2002)->month(2)->day(2)->hour(2)->minute(2)->second(2);
        $this->assertEquals('2001-01-01 01:01:01', $t1->format('Y-m-d H:i:s'));
        $this->assertEquals('2002-02-02 02:02:02', $t2->format('Y-m-d H:i:s'));
    }

    public function testGoModifiers()
    {
        $t = Time::go('2017-01-02 01:02:03'); // Monday

        $this->assertEquals('01:02:23', $t->goNext(20)->toTime());
        $this->assertEquals('2017-01-03 13:02:03', $t->goNext('1 day + 12 hours')->toDateTime());
        $this->assertEquals('2018-02-03 02:03:04', $t->goNext('P1Y1M1DT1H1M1S')->toDateTime());

        $this->assertEquals('01:01:01', $t->goBack(62)->toTime());
        $this->assertEquals('2017-01-01 01:02:03', $t->goBack('1 day')->toDateTime());
        $this->assertEquals('2015-12-01 00:01:02', $t->goBack('P1Y1M1DT1H1M1S')->toDateTime());
    }

    public function testGoToModifiers()
    {
        $t = Time::go('2017-01-02'); // Monday

        // days
        $this->assertEquals('2017-01-09', $t->goToNext(Time::MONDAY)->toDate());
        $this->assertEquals('2017-01-01', $t->goToLast(Time::SUNDAY)->toDate());

        // weeks
        $this->assertEquals('2017-01-02', $t->goToStartOf(Time::WEEK)->toDate());
        $this->assertEquals('2017-01-08', $t->goToEndOf(Time::WEEK)->toDate());

        // month
        $this->assertEquals('2017-01-01', $t->goToStartOf(Time::MONTH)->toDate());
        $this->assertEquals('2017-01-31', $t->goToEndOf(Time::MONTH)->toDate());
    }

    public function testGoToStartEndModifiers()
    {
        $t = Time::go('2017-03-03 03:03:03');

        $this->assertEquals('2017-01-01 00:00:00', $t->goToStartOf(Time::YEAR)->toDateTime());
        $this->assertEquals('2017-03-01 00:00:00', $t->goToStartOf(Time::MONTH)->toDateTime());
        $this->assertEquals('2017-02-27 00:00:00', $t->goToStartOf(Time::WEEK)->toDateTime());
        $this->assertEquals('2017-03-03 00:00:00', $t->goToStartOf(Time::DAY)->toDateTime());
        $this->assertEquals('2017-03-03 03:00:00', $t->goToStartOf(Time::HOUR)->toDateTime());
        $this->assertEquals('2017-03-03 03:03:00', $t->goToStartOf(Time::MINUTE)->toDateTime());

        $this->assertEquals('2017-12-31 23:59:59', $t->goToEndOf(Time::YEAR)->toDateTime());
        $this->assertEquals('2017-03-31 23:59:59', $t->goToEndOf(Time::MONTH)->toDateTime());
        $this->assertEquals('2017-03-05 23:59:59', $t->goToEndOf(Time::WEEK)->toDateTime());
        $this->assertEquals('2017-03-03 23:59:59', $t->goToEndOf(Time::DAY)->toDateTime());
        $this->assertEquals('2017-03-03 03:59:59', $t->goToEndOf(Time::HOUR)->toDateTime());
        $this->assertEquals('2017-03-03 03:03:59', $t->goToEndOf(Time::MINUTE)->toDateTime());
    }

    # HELPERS

    public function testListDays()
    {
        $this->assertEquals([
            Time::MONDAY    =>  'Monday',
            Time::TUESDAY   =>  'Tuesday',
            Time::WEDNESDAY =>  'Wednesday',
            Time::THURSDAY  =>  'Thursday',
            Time::FRIDAY    =>  'Friday',
            Time::SATURDAY  =>  'Saturday',
            Time::SUNDAY    =>  'Sunday',
        ], Time::days());
    }

    public function testListMonths()
    {
        $this->assertEquals([
            Time::JANUARY   => 'January',
            Time::FEBRUARY  => 'February',
            Time::MARCH     => 'March',
            Time::APRIL     => 'April',
            Time::MAY       => 'May',
            Time::JUNE      => 'June',
            Time::JULY      => 'July',
            Time::AUGUST    => 'August',
            Time::SEPTEMBER => 'September',
            Time::OCTOBER   => 'October',
            Time::NOVEMBER  => 'November',
            Time::DECEMBER  => 'December',
         ], Time::months());
    }

    public function testMinMax()
    {
        $this->assertEquals(
            '2000-01-01',
            Time::min(['2010-01-01', new \DateTime('2000-01-01'), Time::go('2013-01-01')])->toDate()
        );

        $this->assertEquals(
            '2013-01-01',
            Time::max(['2010-01-01', new \DateTime('2000-01-01'), Time::go('2013-01-01')])->toDate()
        );
    }

    # DURATION

    public function testDuration()
    {
        $this->assertEquals(24 * 3600, Time::go('2010-01-01')->getUnitDuration('2010-01-02'));
    }

    public function testHuman()
    {
        $this->assertEquals(
            'one year one month and 2 days after',
            Time::go('2017-07-01')->getHumanDuration('2018-08-02')
        );

        $this->assertEquals(
            '5 months 4 weeks and 2 days before',
            Time::go('2017-07-01')->getHumanDuration('2017-01-01')
        );

        $now = Time::go();

        $this->assertEquals(
            'one year 2 months 2 days and one hour from now',
            $now->getHumanDuration($now->goNext('P1Y2M2DT1H'))
        );

        $this->assertEquals(
            '2 months 2 weeks 6 days one hour and 10 minutes ago',
            $now->getHumanDuration($now->goBack('P2M20DT1H10M'))
        );

        $this->assertEquals(
            'more than 1 day ago',
            $now->getHumanDuration($now->goBack('P2D'), null, 24 * 3600, '1 day')
        );

        $this->assertEquals(
            'more than 1 day from now',
            $now->getHumanDuration($now->goNext('P2D'), null, 24 * 3600, '1 day')
        );

        $this->assertEquals(
            'one year and one month after',
            Time::go('2017-07-01')->getHumanDuration('2018-08-02', null, null, null, 2)
        );
    }

    public function testHumanRo()
    {
        $en = Time::getLocale();
        Time::setLocale([
            Time::YEAR  =>  ['0 ani',   'un an', '%d ani'],
            Time::MONTH =>  ['0 luni',  'o lună','%d luni'],
            Time::WEEK  =>  ['0 săptămâni', 'o săptămână', '%d săptămâni'],
            Time::DAY   =>  ['0 zile',  'o zi',  '%d zile'],
            Time::HOUR  =>  ['0 ore',   'o oră', '%d ore'],
            Time::MINUTE=>  ['0 minute','un minut', '%d minute'],
            Time::SECOND=>  ['0 secunde','o secundă', '%d secunde'],
            'separator' =>  ' ',
            '&'         =>  'și',
            'ago'       =>  'acum %s',
            'from_now'  =>  '%s de acum',
            'after'     =>  'după %s',
            'before'    =>  '%s înainte',
            'more'      =>  'mai mult de %s'
        ]);

        $now = Time::go();

        $this->assertEquals(
            'un an 2 luni 2 zile și o oră de acum',
            $now->getHumanDuration($now->goNext('P1Y2M2DT1H'))
        );

        $this->assertEquals(
            'acum 2 luni 2 săptămâni 6 zile o oră și 10 minute',
            $now->getHumanDuration($now->goBack('P2M20DT1H10M'))
        );

        $this->assertEquals(
            'mai mult de acum o zi',
            $now->getHumanDuration($now->goBack('P2D'), null, 24 * 3600, 'o zi')
        );

        $this->assertEquals(
            'mai mult de o zi de acum',
            $now->getHumanDuration($now->goNext('P2D'), null, 24 * 3600, 'o zi')
        );

        Time::setLocale($en);
    }

    # LIMIT



    # EXCEPTIONS

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage  Invalid timezone: Unknown/Unknown
     */
    public function testTimezoneException()
    {
        Time::safeTimezone('Unknown/Unknown');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGoException()
    {
        Time::go(12.1);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGoToStartException()
    {
        Time::go()->goToStartOf('X');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGoToEndException()
    {
        Time::go()->goToEndOf('X');
    }
}
