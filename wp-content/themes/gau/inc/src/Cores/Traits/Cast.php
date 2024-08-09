<?php

namespace Cores\Traits;

\defined( 'ABSPATH' ) || die;

trait Cast {

	use Base;
	use Arr;

	// --------------------------------------------------

	/**
	 * @param $delimiters
	 * @param $string
	 * @param bool $remove_empty
	 *
	 * @return mixed
	 */
	public static function explodeMulti( $delimiters, $string, bool $remove_empty = true ): mixed {
		if ( is_string( $delimiters ) ) {
			return explode( $delimiters, $string );
		}

		if ( is_array( $delimiters ) ) {
			$ready  = str_replace( $delimiters, $delimiters[0], $string );
			$launch = explode( $delimiters[0], $ready );
			if ( $remove_empty ) {
				$launch = array_filter( $launch );
			}

			return $launch;
		}

		return $string;
	}

	// --------------------------------------------------

	/**
	 * @param mixed $value
	 *
	 * @return int
	 */
	public static function toInt( mixed $value ): int {
		return (int) round( self::toFloat( $value ) );
	}

	// --------------------------------------------------

	/**
	 * @param mixed $value
	 *
	 * @return float
	 */
	public static function toFloat( mixed $value ): float {
		return (float) filter_var( $value, FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND );
	}

	// --------------------------------------------------

	/**
	 * @param mixed $value
	 * @param bool $explode
	 *
	 * @return array
	 * @throws \JsonException
	 */
	public static function toArray( mixed $value, bool $explode = true ): array {
		if ( is_object( $value ) ) {
			$reflection = new \ReflectionObject( $value );
			$properties = $reflection->hasMethod( 'toArray' )
				? $value->toArray()
				: get_object_vars( $value );

			return json_decode( json_encode( $properties, JSON_THROW_ON_ERROR ), true, 512, JSON_THROW_ON_ERROR );
		}

		if ( is_scalar( $value ) && $explode ) {
			return self::convertFromString( $value );
		}

		return (array) $value;
	}

	// --------------------------------------------------

	/**
	 * @param mixed $value
	 * @param bool $strict
	 *
	 * @return string
	 */
	public static function toString( mixed $value, bool $strict = true ): string {
		if ( is_object( $value ) && in_array( '__toString', get_class_methods( $value ) ) ) {
			return (string) $value->__toString();
		}
		if ( self::isEmpty( $value ) ) {
			return '';
		}
		if ( self::isIndexedAndFlat( $value ) ) {
			return implode( ', ', $value );
		}
		if ( ! is_scalar( $value ) ) {
			return $strict ? '' : serialize( $value );
		}

		return (string) $value;
	}

	// --------------------------------------------------

	/**
	 * @param mixed $value
	 *
	 * @return bool
	 */
	public static function toBool( mixed $value ): bool {
		return filter_var( $value, FILTER_VALIDATE_BOOLEAN );
	}

	// --------------------------------------------------

	/**
	 * @param mixed $value
	 *
	 * @return object
	 * @throws \JsonException
	 */
	public static function toObject( mixed $value ): object {
		if ( ! is_object( $value ) ) {
			return (object) self::toArray( $value );
		}

		return $value;
	}
}
