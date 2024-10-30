<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
if ( ! function_exists( 'callcontact_menu' ) ) {
	function callcontact_menu() {
		add_menu_page( 'Ustawienia', 'Callcontact', 'manage_options', 'callcontact', 'callcontact', plugin_dir_url( __FILE__ ) . '../assets/img/logocc.png' );
		add_submenu_page( 'callcontact', 'Callcontact - Ustawienia', 'Ustawienia', 'manage_options', 'callcontact_options', 'callcontact_options');
		add_options_page( 'Callcontact - Ustawienia', 'Ustawienia', 'manage_options', 'callcontact_options2',  'callcontact_options');
		add_submenu_page( 'callcontact', 'Callcontact - Pomoc', 'Pomoc', 'manage_options', 'callcontact_help_submenu', 'callcontact_help_submenu');
		remove_submenu_page( 'callcontact', 'callcontact' );
	}
}

add_action('admin_menu', 'callcontact_menu');

function callcontact_options() {

	if (! current_user_can('manage_options') ) {
		wp_die( esc_html__('You do not have sufficient permission to access this page.', 'callcontact') );
	}

	if (isset($_REQUEST['action']) && 'save' === $_REQUEST['action']) {
		if (!isset($_REQUEST['save_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['save_nonce'])), 'save_action')) {
			wp_die('Nonce verification failed.');
		}

		

		if (isset($_REQUEST['callweb-widget-key'])) { 
			$widget_key = sanitize_text_field(wp_unslash($_REQUEST['callweb-widget-key']));
	
			if (strlen(trim($widget_key)) === 32) { 
					update_option('callweb-widget-key', $widget_key); 
			}
			?>
				<div class="notice updated">
					<p>Klucz widgetu dodany poprawnie</p>
				</div>
			<?php
		} else {
			?>
				<div class="notice error">
					<p>Podano nieprawidłowy klucz widgetu. Zmiany nie zostały zapisane</p>
				</div>
			<?php
		}

		if (strlen(get_option('callweb-widget-key', '')) === 32 && isset($_REQUEST['callweb-widget-is-active'])) {
			$widget_is_active = sanitize_text_field(wp_unslash($_REQUEST['callweb-widget-is-active'])); 
	
			update_option('callweb-widget-is-active', $widget_is_active === 'on' ? '1' : '0');
	
			if ($widget_is_active === 'on') {
				?>
					<div class="notice updated">
						<p>Widget został dodany do strony</p>
					</div>
				<?php
			} else {
				?>
					<div class="notice updated">
						<p>Widget został usunięty ze strony</p>
					</div>
				<?php
			}
		} else {
			update_option('callweb-widget-is-active', '0');
		}
	}

	?>

	<div class="callweb-options-wrapper">
		<form method="POST" class="wrap callweb-options__form" action="">
			<h2>Ustawienia wtyczki</h2>
			<div class="callweb--options__header postbox">
				<p>
					Tutaj możesz zmienić ustawienia wtyczki. Podaj klucz widgetu, który chcesz umieścić na stronie.<br>
					Konfiguracji widgetu możesz dokonać na stronie <a href="https://user.callcontact.eu" target="_blank">user.callcontact.eu</a> - to właśnie tu znajdują się ustawienia wyglądu, dostępności i wszelkich innych informacji.
				</p>

				<label class="callweb-label" for="callweb-widget-key">Uniwersalny klucz widgetu</label><br>
				<input type="text" name="callweb-widget-key" class="callweb-key-label" value="<?php echo esc_attr(get_option('callweb-widget-key', '')) ?>">
				<p>Klucz widgetu możesz sprawdzić w aplikacji w zakładce Widget.</p>

				<label class="callweb-label" for="callweb-widget-is-active">Czy dodać kod widgetu na stronę?</label>
				<input type="checkbox" name="callweb-widget-is-active" <?php echo esc_attr(get_option('callweb-widget-is-active', '0')) === '1' ? 'checked' : null ?> >
				<p>Odznaczenie wartości oznacza usunięcie kodu widgetu ze strony co jest równoznaczne z brakiem możliwości jego wyświetlania. Aktywność widgetu można dostosować w panelu administracyjnym w zakładce Widget. Tam można ustalić w jakich dniach i godzinach widget będzie wyświetlał się na stronie</p>

			</div>
			<?php wp_nonce_field('save_action', 'save_nonce'); ?>
			<input type="hidden" name="action" value="save">
			<input type="submit" class="button button-primary" value="Zapisz zmiany">
		</form>
	</div>

	<?php

}

function callcontact_help_submenu() {

	if (! current_user_can('manage_options') ) {
		wp_die( esc_html__('You do not have sufficient permission to access this page.', 'callcontact') );
	}

	?>
		<div class="callweb-options">
			<div class="callweb-options-wrapper wrap">
				<h2>Pomoc</h2>
				<div class="postbox">
					<p>Tutaj znajdziesz instrukcję do wtyczki Callweb umożliwiającej w łatyw sposób umieszczenia kodu widgetu callweb na swoją stronę internetową w Wordpress</p>

					<h3>Jak umieścić widget na stronie?</h3>
					<p>Wystarczy w ustawieniach wtyczki podać poprawny klucz naszego widgetu i zaznaczyć opcję umieszczenia go na stronie. <br>Należy pamiętać, że cała konfiguracja widgetu znajduje się w panelu administracyjnym <a href="user.callcontact.eu" target="_blank">Callcontact</a>.
					
					<h3>Skąd wziąć klucz widgetu?</h3>
					<p>W panelu administracyjnym <a href="user.callcontact.eu" target="_blank">callcontact</a> należy przejść do zakładki widgety. Tam będzie znajdować się lista stworzonych przez nas widgetów. W informacjach o widgecie na liście znajduje się klucz, który należy skopiować i wkleić w ustawieniach wtyczki.
					</p>
				</div>
			</div>
		</div>
	<?php
}

function callcontact_contact_submenu() {
	if (! current_user_can('manage_options') ) {
		wp_die( esc_html__('You do not have sufficient permission to access this page.', 'callcontact') );
	}

	if (isset($_REQUEST['action']) && 'send' === $_REQUEST['action']) {
		if (!isset($_REQUEST['send_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['send_nonce'])), 'send_action')) {
			wp_die('Nonce verification failed.');
		}
		$to = 'infolinie@telestrada.pl';
		if (isset($_POST['subject'])) {
			$subject = sanitize_text_field(wp_unslash($_POST['subject']));
	}
	
	if (isset($_POST['message'])) { 
			$message = sanitize_textarea_field(wp_unslash($_POST['message'])); 
	}
	
		$isMessageDelivered = wp_mail($to, $subject, $message);

		if ($isMessageDelivered == 1) {
			?>
				<div class="notice updated">
					<p>Twoja wiadomość została wysłana</p>
				</div>
			<?php
		} else {
			?>
				<div class="notice error">
					<p>Twoja wiadomość nie została wysłana. Może być to spowodowane nieprawidłowo skonfigurowanym SMTP. Spróbuj ponownie później lub skontaktuj się telefonicznie poprzez widget na stronie <a href="https://user.callcontact.eu" target="_blank">user.callcontact.eu</a></p>
				</div>
			<?php
		}
	}

	?>
		<div class="callweb-options callweb-contact">
			<div class="callweb-options-wrapper wrap">
				<h2>Kontakt</h2>
				<form method="POST">
					<div class="postbox">
						<p>Wypełnij formularz. Postaramy się odpowiedzieć jak najszybciej</p>
						<table class="callweb-contact-table">
							<tbody>
								<tr>
									<th scope="row">
										<label for="subject">Temat</label>
									</th>
									<td>
										<input type="text" name="subject" value="">
									</td>
								</tr>
								<tr>
									<th scope="row">
										<label for="message">Treść zapytania</label>
									</th>
									<td>
										<textarea name="message" value="" cols="36" rows="7"></textarea>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<?php wp_nonce_field('send_action', 'send_nonce'); ?>
					<input type="hidden" name="action" value="send">
					<input type="submit" class="button button-primary" value="Wyślij email">
				</form>
			</div>
		</div>
	<?php
}