<?php

/**
 * OptionPage
 */

namespace Moerr;

use KToolbox\FilterInput;

/**
 * Class OptionPage
 *
 * @package Monitoring
 */
class OptionPage {

	/**
	 * Protected vars
	 *
	 * @var string
	 * @var PluginOptions
	 * @var Proxy
	 */
	protected
		$title,
		$options;

	/**
	 * OptionPage constructor.
	 *
	 * @param string $title
	 * @param string $options
	 */
	public function __construct( string $title, \stdClass $options ) {
		$this->title   = $title;
		$this->options = $options;
	}

	/**
	 * Magic getter
	 *
	 * @param $key
	 *
	 * @return mixed
	 *
	 * @codeCoverageIgnore
	 */
	public function __get( $key ) {
		return $this->$key ?? null;
	}

	/**
	 * Factory
	 *
	 * @param $options
	 *
	 * @return OptionPage
	 */
	public static function init( $options ) {
		$title = _x( 'Monitoring Error', 'monitoringError', 'dkwp' );

		$obj        = new self( $title, $options );
		$capability = 'manage_options';

		add_options_page( $obj->title, $obj->title, $capability, __CLASS__, [ $obj, 'render' ] );
		add_action( 'admin_init', [ $obj, 'register' ] );

		return $obj;
	}

	/**
	 * Renders callback
	 *
	 * @codeCoverageIgnore
	 */
	public function render() {
		$this->handle_submissions();
		$options = get_option( 'monitoring_options' );

		echo '<div class="wrap">', "\n", '<div class="icon32" id="icon-options-general"><br/>', "</div>\n";
		printf( "<h2>%s</h2>\n", $this->title );
		echo ' <h2 class="help-button-inline">Set configuration for log file</h2>';
		echo '<form action="options.php" method="post">', "\n";

		echo '<fieldset class="group"><table class="form-table">', PHP_EOL;

		printf( '<tr><th>%s</th></tr>',
			_x( 'Path file log', 'Options Page', 'dkwp' )
		);

		echo PHP_EOL;

		printf(
			'<td><input type="text"  name="%1$s[url_logs]" id="%1$s_%2$d" value="%3$s" ></td>',
			'monitoring_options',
			'url_logs',
			$options['url_logs']
		);

		settings_fields( $this->options->name );
		do_settings_sections( __CLASS__ );

		echo '</table></fieldset>', PHP_EOL;
		submit_button();

		echo "</form>\n";

		echo ' <h2 class="help-button-inline">List of errors</h2>';

		$file   = file( $options['url_logs'], 1 );
		$errors = [];
		$n      = 0;
		$w      = 0;
		$f      = 0;
		$p      = 0;
		foreach ( $file as $row ) {
			if ( strpos( $row, 'PHP Notice:' ) !== false ) {
				if ( ! in_array( $row, $errors ) ) {
					$row      = str_replace( "PHP Notice:", "<b style='color: #e8cf00'>PHP Notice:</b>", $row );
					$errors[] = $row;
					$n ++;
				}
			} elseif ( strpos( $row, 'PHP Warning:' ) !== false ) {
				if ( ! in_array( $row, $errors ) ) {
					$row      = str_replace( "PHP Warning:", "<b style='color: #e88f00'>PHP Warning:</b>", $row );
					$errors[] = $row;
					$w ++;
				}
			} elseif ( strpos( $row, 'Fatal error:' ) !== false ) {
				if ( ! in_array( $row, $errors ) ) {
					$row      = str_replace( "Fatal error:", "<b style='color:#f11b1b'>Fatal error:</b>", $row );
					$errors[] = $row;
					$f ++;
				}
			} elseif ( strpos( $row, 'Parse error:' ) !== false ) {
				if ( ! in_array( $row, $errors ) ) {
					$row      = str_replace( "Parse error:", "<b style='color:#f11b1b'>Parse error:</b>", $row );
					$errors[] = $row;
					$p ++;
				}
			}
		}

		printf( '<b >Count Notice: </b><span style="color: #e8cf00"><b>%s</b></span><br>', $n );
		printf( '<b >Count Warning: </b><span style="color: #e88f00"><b>%s</b></span><br>', $w );
		printf( '<b >Count Parse error: </b><span style="color: #f11b1b"><b>%s</b></span><br><br>', $p );
		printf( '<b >Count Fatal error: </b><span style="color: #f11b1b"><b>%s</b></span><br><br>', $f );

		foreach ( $errors as $error ) {
			echo $error . '<br>';
		}
	}

	/**
	 * Registers callback
	 *
	 * @codeCoverageIgnore
	 */
	public function register() {
		$key = $this->options->name;

		register_setting( $key, $key );

		/**
		 * Lets you add your own settings section
		 *
		 * @param string $page
		 *
		 * @since 1.3.4
		 *
		 */
		do_action( 'dkwp/options_page_add_settings', __CLASS__ );
	}

	/**
	 * Handle the right submission
	 *
	 * @throws \Exception
	 */
	public function handle_submissions() {
		$post = new FilterInput( INPUT_POST, 'monitoring_options' );
		if ( $post->has_var() ) {
			$models = $_POST['monitoring_options'];
			$this->handle();
		}
	}

	/**
	 * Handle Submission
	 *
	 * @return void
	 *
	 * @throws \Exception
	 */
	public function handle() {
		echo '<div class="notice notice-success is-dismissible"><p>Operation done.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Nascondi questa notifica.</span></button></div>';
	}
}
