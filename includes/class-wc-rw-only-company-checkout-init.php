<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Main class for initializing the plugin's functionality.
 */
class  Wc_Rw_Only_Company_Checkout_Init
{
    private static $instance;

    /**
     * Constructor. Registers all the necessary actions and filters.
     */
    public function __construct()
    {
        // Add custom product field  "Only for companies"
        add_action( 'woocommerce_product_options_general_product_data', array($this, 'add_custom_product_field' ));

        // Save custom product field "Only for companies"
        add_action( 'woocommerce_process_product_meta', array($this, 'save_custom_product_field') );

        // Display warning message on the single product page if applicable
        add_action('woocommerce_single_product_summary', [$this, 'add_single_product_warning'], 10);

        // Display warning message on the checkout page if applicable
        add_action('woocommerce_checkout_before_customer_details', [$this, 'display_checkout_warning'], 6);

        // Modify checkout fields if applicable
        add_filter('woocommerce_checkout_fields', [$this, 'change_checkout_fields']);


    }

    /**
     * Returns the singleton instance of the class.
     *
     * @return Wc_Rw_Only_Company_Checkout_Init
     */
    public static function get_instance() {

        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * Adds a custom checkbox field to WooCommerce products.
     */
    public function add_custom_product_field() {

        echo '<div class="options_group">';

        woocommerce_wp_checkbox(
            array(
                'id'          => '_only_company',
                'label'       => 'Only for companies',
                'description' => 'Отметьте, если это товар может быть продан только фирмам.'
            )
        );

        echo '</div>';
    }


    /**
     * Saves the custom checkbox field value when the product is saved.
     *
     * @param int $post_id The ID of the product being saved.
     */
    public function save_custom_product_field($post_id) {

        $only_company = $_POST['_only_company'];
        update_post_meta( $post_id, '_only_company', $only_company );

    }


    /**
     * Displays a warning message on the single product page if the product is available only for companies.
     */
    public function add_single_product_warning()
    {
       global $post;

       $only_company = get_post_meta($post->ID, '_only_company', true);

       if ($only_company === 'yes') {
           echo '<div class="wc-rw-only-company-checkout-notice wc-rw-only-company-checkout-notice-warning wc-rw-only-company-checkout-is-dismissible">';

           echo __(
               'This product is available only to business entities and individual entrepreneurs.',
               'wc-rw-only-company-checkout'
           );

           echo '</div>';
       }

    }

    /**
     * Displays a warning message on the checkout page if the cart contains products available only for companies.
     */
    public function display_checkout_warning(){

        $check_cart = $this->check_cart_for_only_company_products();

        if($check_cart['result']){
            echo '<div class="wc-rw-only-company-checkout-notice wc-rw-only-company-checkout-notice-warning wc-rw-only-company-checkout-is-dismissible">';
            echo __(
                'The next products in you order are available only for business and individual entrepreneurs:',
                'wc-rw-only-company-checkout'
            );
            echo '<ul>';
            foreach ($check_cart['products'] as $product){

                echo '<li>' . $product . '</li>';
            }
            echo '</ul>';
            echo '<div style="margin-bottom: 5px;"><a class="button button-primary" href="/cart">' . __('Back to cart', 'wc-rw-only-company-checkout') . '</a></div>';
            echo __(
                'For placing an order, please fill in the Company or Entrepreneur name and tax number for invoicing. If you would like to send goods to a different address, please fill in the shipping data as well.',
                'wc-rw-only-company-checkout'
            );
            echo '</div>';
        }


    }

    /**
     * Modifies checkout fields if the cart contains products available only for companies.
     *
     * @param array $fields The checkout fields.
     * @return array Modified checkout fields.
     */
    public function change_checkout_fields($fields){
        $check_cart = $this->check_cart_for_only_company_products();

        if($check_cart['result']){


                // Change billing first name to Company/Entrepreneur name
                $fields['billing']['billing_first_name']['label'] = __('Company/Entrepreneur name', 'wc-rw-only-company-checkout');
                $fields['billing']['billing_first_name']['class'][] = 'wc-rw-only-company-checkout-company-field';

                // Change billing last name to Tax number
                $fields['billing']['billing_last_name']['label'] = __('Tax number', 'wc-rw-only-company-checkout');
                $fields['billing']['billing_last_name']['class'][] = 'wc-rw-only-company-checkout-company-field';

                // Add a placeholder for clarity
                $fields['billing']['billing_first_name']['placeholder'] = __('Enter your company or entrepreneur name', 'wc-rw-only-company-checkout');
                $fields['billing']['billing_last_name']['placeholder'] = __('Enter your tax number', 'wc-rw-only-company-checkout');

        }

        return $fields;
    }

    /**
     * Checks if the cart contains products available only for companies.
     *
     * @return array Result and product names if applicable.
     */
    private function check_cart_for_only_company_products(){

        global $woocommerce;

        $only_company_product_in_cart = [
            'result' => false,
            'products' => []
        ];

        foreach ($woocommerce->cart->get_cart() as $cart_item_key => $cart_item){

            $product_id = $cart_item['product_id'];
            $product = wc_get_product($product_id);

            if(get_post_meta($product_id, '_only_company', true) === 'yes'){
                $only_company_product_in_cart['products'][] = $product ->get_name();
            }


        }

        if(!empty($only_company_product_in_cart['products'])){
            $only_company_product_in_cart['result'] = true;

        }

        return $only_company_product_in_cart;

    }

}





