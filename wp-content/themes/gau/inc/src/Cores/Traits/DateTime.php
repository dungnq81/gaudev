<?php

namespace Cores\Traits;

use DateTimeZone;

\defined( 'ABSPATH' ) || die;

trait DateTime {

	// -------------------------------------------------------------

	/**
	 * @param $post
	 * @param $from
	 * @param $to
	 *
	 * @return mixed|void
	 */
	public static function humanizeTime( $post = null, $from = null, $to = null ) {
		$_ago = __( 'ago', TEXT_DOMAIN );

		if ( empty( $to ) ) {
			$to = current_time( 'U' );
		}
		if ( empty( $from ) ) {
			$from = get_the_time( 'U', $post );
		}

		$diff = (int) abs( $to - $from );

		$since = human_time_diff( $from, $to );
		$since .= ' ' . $_ago;

		return apply_filters( 'humanize_time', $since, $diff, $from, $to );
	}

	// --------------------------------------------------

	/**
	 * @param string $date_time_1
	 * @param string $date_time_2
	 *
	 * @return string
	 * @throws \Exception
	 */
	public static function isoDuration( string $date_time_1, string $date_time_2 ): string {

		$_date_time_1 = new \DateTime( $date_time_1 );
		$_date_time_2 = new \DateTime( $date_time_2 );

		$interval = $_date_time_1->diff( $_date_time_2 );

		$isoDuration = 'P';
		$isoDuration .= ( $interval->y > 0 ) ? $interval->y . 'Y' : '';
		$isoDuration .= ( $interval->m > 0 ) ? $interval->m . 'M' : '';
		$isoDuration .= ( $interval->d > 0 ) ? $interval->d . 'D' : '';
		$isoDuration .= 'T';
		$isoDuration .= ( $interval->h > 0 ) ? $interval->h . 'H' : '';
		$isoDuration .= ( $interval->i > 0 ) ? $interval->i . 'M' : '';
		$isoDuration .= ( $interval->s > 0 ) ? $interval->s . 'S' : '';

		return $isoDuration;
	}

	// -------------------------------------------------------------

	/**
	 *  Given a date in the timezone of the site, returns that date in UTC.
	 *
	 *  Return format can be overridden using the $format parameter.
	 *
	 * @param $date_string
	 * @param string $format timestamp, U, DateTimeInterface::ATOM, 'Y-m-d H:i:s', 'Y-m-d\TH:i', v.v...
	 *
	 * @return false|int|string
	 */
	public static function convertToUTC( $date_string, string $format = 'Y-m-d H:i:s' ): false|int|string {
		if ( self::isInteger( $date_string ) ) {
			$date_string = "@" . $date_string;
		}

		$datetime = date_create( $date_string, wp_timezone() );

		if ( false === $datetime ) {
			return false;
		}

		// Returns a sum of timestamp with timezone offset.
		if ( 'timestamp' === $format || 'U' === $format ) {
			return $datetime->getTimestamp();
		}

		if ( 'mysql' === $format ) {
			$format = 'Y-m-d H:i:s';
		}

		return $datetime->setTimezone( new DateTimeZone( 'UTC' ) )->format( $format );
	}

	// -------------------------------------------------------------

	/**
	 *  Given a date in UTC or GMT timezone, returns that date in the timezone of the site.
	 *
	 *  Default return format of 'Y-m-d H:i:s' can be overridden using the `$format` parameter.
	 *
	 * @param $date_string
	 * @param string $format
	 *
	 * @return false|int|string
	 */
	public static function convertFromUTC( $date_string, string $format = 'Y-m-d H:i:s' ): false|int|string {
		if ( self::isInteger( $date_string ) ) {
			$date_string = "@" . $date_string;
		}

		$datetime = date_create( $date_string, new DateTimeZone( 'UTC' ) );

		if ( false === $datetime ) {
			return false;
		}

		// set to wp_timezone
		$datetime->setTimezone( wp_timezone() );

		// Returns a sum of timestamp with timezone offset.
		if ( 'timestamp' === $format || 'U' === $format ) {
			return $datetime->getTimestamp() + $datetime->getOffset();
		}

		if ( 'mysql' === $format ) {
			$format = 'Y-m-d H:i:s';
		}

		return $datetime->format( $format );
	}

	// -------------------------------------------------------------

	/**
	 * @param $date_string
	 * @param string $format timestamp, U, 'Y-m-d H:i:s', 'Y-m-d\TH:i', DateTimeInterface::ATOM, v.v...
	 *
	 * @return false|int|string
	 */
	public static function convertDatetimeFormat( $date_string, string $format = 'Y-m-d H:i:s' ): false|int|string {

		if ( self::isInteger( $date_string ) ) {
			$date_string = "@" . $date_string;
		}

		$datetime = date_create( $date_string, wp_timezone() );

		if ( false === $datetime ) {
			return false;
		}

		// Returns a sum of timestamp with timezone offset.
		if ( 'timestamp' === $format || 'U' === $format ) {
			return $datetime->getTimestamp() + $datetime->getOffset();
		}

		if ( 'mysql' === $format ) {
			$format = 'Y-m-d H:i:s';
		}

		return $datetime->format( $format );
	}

	// -------------------------------------------------------------

	/**
	 * @param string $date_string
	 *
	 * @return array|int[]
	 * @throws \Exception
	 */
	public static function timeDifference( string $date_string ): array {
		$targetTime = \DateTime::createFromFormat( 'Y-m-d\TH:i:s', $date_string, wp_timezone() );

		if ( $targetTime === false ) {
			return [
				'days'    => '00',
				'hours'   => '00',
				'minutes' => '00',
				'seconds' => '00',
			];
		}

		$currentTime = new \DateTime( 'now', wp_timezone() );
		$interval    = $currentTime->diff( $targetTime );

		if ( $targetTime < $currentTime ) {
			$interval = new \DateInterval( 'P0D' );
		}

		return [
			'days'    => $interval->format( '%a' ) > 10 ? $interval->format( '%a' ) : '0' . $interval->format( '%a' ),
			'hours'   => $interval->format( '%h' ) > 10 ? $interval->format( '%h' ) : '0' . $interval->format( '%h' ),
			'minutes' => $interval->format( '%i' ) > 10 ? $interval->format( '%i' ) : '0' . $interval->format( '%i' ),
			'seconds' => $interval->format( '%s' ) > 10 ? $interval->format( '%s' ) : '0' . $interval->format( '%s' ),
		];
	}

	// --------------------------------------------------
}
