<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AWSM_Job_Openings_UI_Builder {
	private static $instance = null;

	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public static function generate_css( $styles, $wrap_tag = true ) {
		$css = '';
		if ( ! empty( $styles ) ) {
			foreach ( $styles as $attrs ) {
				if ( ! isset( $attrs['selector'] ) && ! isset( $attrs['declaration'] ) && ! isset( $attrs['media_query'] ) ) {
					continue;
				}

				if ( isset( $attrs['selector'] ) && ! empty( $attrs['selector'] ) && isset( $attrs['declaration'] ) && is_array( $attrs['declaration'] ) ) {
					$selector        = $attrs['selector'];
					$declaration_arr = $attrs['declaration'];

					$declaration = '';
					foreach ( $declaration_arr as $property => $value ) {
						$declaration .= sprintf( '%1$s: %2$s;', $property, $value );
					}
					$css .= wp_strip_all_tags( sprintf( '%1$s { %2$s } ', $selector, $declaration ) );
				} else {
					$media_query = isset( $attrs['media_query'] ) ? $attrs['media_query'] : '';
					if ( ! empty( $media_query ) && isset( $attrs['css'] ) && is_array( $attrs['css'] ) ) {
						$css .= sprintf( '%1$s { %2$s } ', wp_strip_all_tags( $media_query ), self::generate_css( $attrs['css'], false ) );
					}
				}
			}

			$css = trim( $css );
			if ( ! empty( $css ) && $wrap_tag ) {
				$css = sprintf( '<style>%s</style>', $css );
			}
		}
		return $css;
	}
}
