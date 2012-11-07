<?php

namespace Cron;

use DateInterval;
use DateTime;
use RuntimeException;
use InvalidArgumentException;

/**
 * CRON expression parser that can determine whether or not a CRON expression is
 * due to run, the next run date and previous run date of a CRON expression.
 * The determinations made by this class are accurate if checked run once per minute.
 *
 * Schedule parts must map to:
 * minute [0-59], hour [0-23], day of month, month [1-12|JAN-DEC], day of week
 * [1-7|MON-SUN], and an optional year.
 *
 * @author Michael Dowling <mtdowling@gmail.com>
 * @link http://en.wikipedia.org/wiki/Cron
 */
class CronExpression
{
    const MINUTE = 0;
    const HOUR = 1;
    const DAY = 2;
    const MONTH = 3;
    const WEEKDAY = 4;
    const YEAR = 5;

    /**
     * @var array CRON expression parts
     */
    private $cronParts;

    /**
     * @var FieldFactory CRON field factory
     */
    private $fieldFactory;

    /**
     * @var array Order in which to test of cron parts
     */
    private static $order = array(self::YEAR, self::MONTH, self::DAY, self::WEEKDAY, self::HOUR, self::MINUTE);

    /**
     * Factory method to create a new CronExpression.
     *
     * @param string $expression The CRON expression to create.  There are
     *      several special predefined values which can be used to substitute the
     *      CRON expression:
     *
     *      @yearly, @annually) - Run once a year, midnight, Jan. 1 - 0 0 1 1 *
     *      @monthly - Run once a month, midnight, first of month - 0 0 1 * *
     *      @weekly - Run once a week, midnight on Sun - 0 0 * * 0
     *      @daily - Run once a day, midnight - 0 0 * * *
     *      @hourly - Run once an hour, first minute - 0 * * * *
     * @param FieldFactory $fieldFactory (optional) Field factory to use with
     *      the expression.  Leave NULL to use the default factory.
     *
     * @return CronExpression
     */
    public static function factory($expression, FieldFactory $fieldFactory = null)
    {
        $mappings = array(
            '@yearly' => '0 0 1 1 *',
            '@annually' => '0 0 1 1 *',
            '@monthly' => '0 0 1 * *',
            '@weekly' => '0 0 * * 0',
            '@daily' => '0 0 * * *',
            '@hourly' => '0 * * * *'
        );

        if (isset($mappings[$expression])) {
            $expression = $mappings[$expression];
        }

        return new self($expression, $fieldFactory ?: new FieldFactory());
    }

    /**
     * Parse a CRON expression
     *
     * @param string $expression CRON expression (e.g. '8 * * * *')
     * @param FieldFactory $fieldFactory Factory to create cron fields
     */
    public function __construct($expression, FieldFactory $fieldFactory)
    {
        $this->fieldFactory = $fieldFactory;
        $this->setExpression($expression);
    }

    /**
     * Set or change the CRON expression
     *
     * @param string $schedule CRON expression (e.g. 8 * * * *)
     *
     * @return CronExpression
     * @throws InvalidArgumentException if not a valid CRON expression
     */
    public function setExpression($value)
    {
        $this->cronParts = explode(' ', $value);
        if (count($this->cronParts) < 5) {
            throw new InvalidArgumentException(
                $value . ' is not a valid CRON expression'
            );
        }

        foreach ($this->cronParts as $position => $part) {
            $this->setPart($position, $part);
        }

        return $this;
    }

    /**
     * Set part of the CRON expression
     *
     * @param int $position The position of the CRON expression to set
     * @param string $value The value to set
     *
     * @return CronExpression
     * @throws InvalidArgumentException if the value is not valid for the part
     */
    public function setPart($position, $value)
    {
        if (!$this->fieldFactory->getField($position)->validate($value)) {
            throw new InvalidArgumentException(
                'Invalid CRON field value ' . $value . ' as position ' . $position
            );
        }

        $this->cronParts[$position] = $value;

        return $this;
    }

    /**
     * Get the date in which the CRON expression will run next
     *
     * @param string $currentTime (optional) The current date time for testing
     *     purposes or disregarding the current second
     * @param int $nth (optional) The number of matches to skip before returning
     *     a matching next run date.  0, the default, will return the current
     *     date and time if the next run date falls on the current date and
     *     time.  Setting this value to 1 will skip the first match and go to
     *     the second match.  Setting this value to 2 will skip the first 2
     *     matches and so on.
     *
     * @return DateTime
     * @throws RuntimeExpression on too many iterations
     */
    public function getNextRunDate($currentTime = 'now', $nth = 0)
    {
        return $this->getRunDate(false, $currentTime, $nth);
    }

    /**
     * Get a previous run date
     *
     * @param string $currentTime (optional) The current date time for testing
     *      purposes or disregarding the current second
     * @param int $nth (optional) The number of matches to skip before returning
     *     a matching next run date.
     *
     * @return DateTime
     * @throws RuntimeExpression on too many iterations
     */
    public function getPreviousRunDate($currentTime = 'now', $nth = 0)
    {
        return $this->getRunDate(true, $currentTime, $nth);
    }

    /**
     * Get all or part of the CRON expression
     *
     * @param string $part (optional) Specify the part to retrieve or NULL to
     *      get the full cron schedule string.
     *
     * @return string|null Returns the CRON expression, a part of the
     *      CRON expression, or NULL if the part was specified but not found
     */
    public function getExpression($part = null)
    {
        if (null === $part) {
            return implode(' ', $this->cronParts);
        } else if (array_key_exists($part, $this->cronParts)) {
            return $this->cronParts[$part];
        }

        return null;
    }

    /**
     * Deterime if the cron is due to run based on the current time.  Unless
     * a string is passed, this method assumes that the current number of
     * seconds are irrelevant, and that this method will be called once per
     * minute.
     *
     * @param string|DateTime $currentTime (optional) Set the current time
     *      If left NULL, the current time is used, less seconds
     *      If a DateTime object is passed, the DateTime is used less seconds
     *      If a string is used, the exact strotime of the string is used
     *
     * @return bool Returns TRUE if the cron is due to run or FALSE if not
     */
    public function isDue($currentTime = null)
    {
        if (null === $currentTime || 'now' === $currentTime) {
            $currentDate = date('Y-m-d H:i');
            $currentTime = strtotime($currentDate);
        } else if ($currentTime instanceof DateTime) {
            $currentDate = $currentTime->format('Y-m-d H:i');
            $currentTime = strtotime($currentDate);
        } else {
            $currentTime = new DateTime($currentTime);
            $currentTime->setTime($currentTime->format('H'), $currentTime->format('i'), 0);
            $currentDate = $currentTime->format('Y-m-d H:i');
            $currentTime = $currentTime->getTimeStamp();
        }

        return $this->getNextRunDate($currentDate)->getTimestamp() == $currentTime;
    }

    /**
     * Get the next or previous run date of the expression relative to a date
     *
     * @param bool $invert Set to TRUE to go backwards in time
     * @param string $currentTime (optional) Relative time used for the checks
     * @param int $nth (optional) The number of matches to skip before returning
     *     a matching next run date.
     *
     * @return DateTime
     * @throws RuntimeExpression on too many iterations
     */
    protected function getRunDate($invert = false, $currentTime = null, $nth = 0)
    {
        $currentDate = $currentTime instanceof DateTime
            ? $currentTime
            : new DateTime($currentTime ?: 'now');

        $nextRun = clone $currentDate;
        $nextRun->setTime($nextRun->format('H'), $nextRun->format('i'), 0);
        $nth = (int) $nth;

        // Set a hard limit to bail on an impossible date
        for ($i = 0; $i < 1000; $i++) {

            foreach (self::$order as $position) {
                $part = $this->getExpression($position);
                if (null === $part) {
                    continue;
                }

                $satisfied = false;
                // Get the field object used to validate this part
                $field = $this->fieldFactory->getField($position);
                // Check if this is singular or a list
                if (strpos($part, ',') === false) {
                    $satisfied = $field->isSatisfiedBy($nextRun, $part);
                } else {
                    foreach (array_map('trim', explode(',', $part)) as $listPart) {
                        if ($field->isSatisfiedBy($nextRun, $listPart)) {
                            $satisfied = true;
                            break;
                        }
                    }
                }

                // If the field is not satisfied, then start over
                if (!$satisfied) {
                    $field->increment($nextRun, $invert);
                    continue 2;
                }
            }

            // Skip this match if needed
            if (--$nth > -1) {
                $this->fieldFactory->getField(0)->increment($nextRun, $invert);
                continue;
            }

            return $nextRun;
        }

        // @codeCoverageIgnoreStart
        throw new RuntimeException('Impossible CRON expression');
        // @codeCoverageIgnoreEnd
    }
}
