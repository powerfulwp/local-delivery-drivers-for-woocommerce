<?php

/**
 * Website store class
 *
 * @link  http://www.powerfulwp.com
 * @since 1.0.0
 *
 * @package    LDDFW
 * @subpackage LDDFW/includes
 */
/**
 * Website store class.
 *
 * All store functions.
 *
 * @link  http://www.powerfulwp.com
 * @since      1.0.0
 * @package    LDDFW
 * @subpackage LDDFW/includes
 * @author     powerfulwp <cs@powerfulwp.com>
 */
class LDDFW_Store
{
    /**
     * Store address.
     *
     * @since 1.0.0
     * @param string $format address format.
     * @return string
     */
    public function lddfw_store_address( $format )
    {
        // store address.
        $store_address = get_option( 'woocommerce_store_address', '' );
        $store_address_2 = get_option( 'woocommerce_store_address_2', '' );
        $store_city = get_option( 'woocommerce_store_city', '' );
        $store_postcode = get_option( 'woocommerce_store_postcode', '' );
        $store_raw_country = get_option( 'woocommerce_default_country', '' );
        $split_country = explode( ':', $store_raw_country );
        
        if ( false === strpos( $store_raw_country, ':' ) ) {
            $store_country = $split_country[0];
            $store_state = '';
        } else {
            $store_country = $split_country[0];
            $store_state = $split_country[1];
        }
        
        if ( '' !== $store_country ) {
            $store_country = WC()->countries->countries[$store_country];
        }
        
        if ( 'map_address' === $format ) {
            $store_address = $store_address . ' ' . $store_address_2 . ' ' . $store_city . ' ' . $store_state . ' ' . $store_postcode . ' ' . $store_country;
            $store_address = str_replace( '  ', ' ', $store_address );
            $store_address = str_replace( ' ', '+', $store_address );
            return $store_address;
        }
        
        
        if ( 'address' === $format ) {
            // Format address.
            if ( '' !== $store_address_2 ) {
                $store_address .= ',' . $store_address_2 . ' ';
            }
            $store_address .= '<br>' . $store_city . ' ';
            if ( '' !== $store_state ) {
                $store_address .= $store_state . ' ';
            }
            if ( '' !== $store_postcode ) {
                $store_address .= $store_postcode . ' ';
            }
            if ( '' !== $store_country ) {
                $store_address .= '<br>' . $store_country;
            }
            return $store_address;
        }
    
    }

}