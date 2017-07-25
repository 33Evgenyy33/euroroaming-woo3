<?php
/**
 * functionality of the plugin.
 *
 * @link       @TODO
 * @since      1.0
 *
 * @package    @TODO
 * @subpackage @TODO
 * @author     Varun Sridharan <varunsridharan23@gmail.com>
 */
if ( ! defined( 'WPINC' ) ) { die; }

class WooCommerce_Quick_Buy_FrontEnd {
	static public $settings;
    /**
     * Class Constructor
     */
    public function __construct() {
        add_action( 'wp_enqueue_scripts', array($this,'enqueue_style_script') );
        add_action('woocommerce_before_add_to_cart_button',array($this,'add_wc_quick_buy_chain'));
        add_filter( 'woocommerce_add_to_cart_redirect',array($this,'quick_buy_redirect'),99);
		add_action( 'wp_ajax_wc_quick_buy_style', array($this,'render_quick_buy_style') );
		add_action( 'wp_ajax_nopriv_wc_quick_buy_style', array($this,'render_quick_buy_style' ));		
    }
	
	public function render_quick_buy_style(){
		$style = wc_qb_option('btn_css');
		$style = str_replace('<style>','',$style);
		$style = str_replace('</style>','',$style);
		header( 'Content-Type: text/css' );
		echo $style; 
		wp_die();
	}
	
    
    public function add_wc_quick_buy_chain(){
        global $product;
        if($product != null){
            echo '<input type="hidden" id="wc_quick_buy_hook_'.$product->id.'" value="'.$product->id.'"  />';
        }
    }
    
    /**
     * Adds Plugins Script To Site Front End
     */
    public function enqueue_style_script(){
		$style_url = admin_url('admin-ajax.php');
		$style_url = add_query_arg('action','wc_quick_buy_style',$style_url);
		wp_register_style(WCQB_DB.'_plugin_style',$style_url,'',WCQB_V);
		
        wp_enqueue_script(WCQB_DB.'_frontend', WCQB_JS.'frontend.js', array( 'jquery'),WCQB_V );
		wp_enqueue_style(WCQB_DB.'_plugin_style') ;
    }

    
    public function get_from_db(){
		self::$settings = array();
        self::$settings['redirect'] = get_option(WCQB_DB.'redirect');
        self::$settings['custom_redirect'] = get_option(WCQB_DB.'custom_redirect');
        self::$settings['product_types'] = get_option(WCQB_DB.'product_types');
        self::$settings['auto'] = get_option(WCQB_DB.'auto');
        self::$settings['position'] = get_option(WCQB_DB.'position');
        self::$settings['label'] = get_option(WCQB_DB.'label');
        self::$settings['class'] = get_option(WCQB_DB.'class');
        self::$settings['btn_css'] = get_option(WCQB_DB.'btn_css');
    }
    
	public function get_option($key = ''){
		if(isset(self::$settings[$key])){
			return self::$settings[$key];
		}
		return false;
	}
	
    public function add_quick_buy_button(){ 
        global $product;
        $args = array( 'product' => $product );
        echo $this->generate_button($args);
    }
    
    
    
    public function generate_button($args){
        $default_args = array(
            'product' => null,
            'label' => wc_qb_option('label'),
            'class' => wc_qb_option('class'),
			'tag' => 'button',
        );
        
        $args = wp_parse_args( $args, $default_args );
        extract($args);
        $return = '';
        
		if($product == null){return;}
        $type = $product->product_type;
		if(wc_qb_option('product_types') == null){return;}
        
		if(!in_array('all',wc_qb_option('product_types')) && !in_array($type,wc_qb_option('product_types'))){return;}
		$pid = $product->id;
		
		$defined_class = 'wc_quick_buy_button quick_buy_button quick_buy_'.$type.' quick_buy_'.$pid.'_button quick_buy_'.$pid.''.$class;
        $defined_id = 'quick_buy_'.$pid.'_button';
		$defined_attrs = 'name=""  data-product-type="'.$type.'" data-product-id="'.$pid.'"';
		
        
        $return .= '<div class="quick_buy_container quick_buy_'.$pid.'_container" id="quick_buy_'.$pid.'_container" >';
		
		if($tag == 'button'){
			$return .= '<input value="'.$label.'" type="button" id="'.$defined_id.'" '.$defined_attrs.'  class="wcqb_button '.$defined_class.'">';
		} 
		
		if($tag == 'link'){
			$qty = wc_qb_option('product_qty');
			$link = $this->get_product_addtocartLink($product,$qty);
			$return .= '<a href="'.$link.'" id="'.$defined_id.'" '.$defined_attrs.'  class="wcqb_button '.$defined_class.'">';
			$return .= $label;
			$return .= '</a>';
		}
        
        
        $return .= '</div>'; 
        return $return;
    }
	
	public function get_product_addtocartLink($product,$qty = 1){
		if($product->product_type == 'simple'){
			$link = $product->add_to_cart_url();
			$link = add_query_arg('quantity',$qty,$link);
			$link = add_query_arg('quick_buy',true,$link);
			return $link;
		}
	}
    
	/**
	 * Function to redirect user after qucik buy button is submitted
	 * @since 0.1
	 * @updated 0.2
	 * @return string [[Description]]
	 */
	public function quick_buy_redirect($url){
		if(isset($_REQUEST['quick_buy']) && $_REQUEST['quick_buy'] == true){
			$redirect = wc_qb_option('redirect');
            if($redirect == 'cart'){
                return WC()->cart->get_cart_url();
            } else if($redirect == 'checkout'){
                return WC()->cart->get_checkout_url();
            } else if($redirect == 'custom' && wc_notice_count( 'error' ) === 0){
            	$cr = wc_qb_option('custom_redirect');
                if(!empty($cr)){
                    wp_safe_redirect($cr);
                    exit;
                }
            }
		}
		return $url;
	}	

}