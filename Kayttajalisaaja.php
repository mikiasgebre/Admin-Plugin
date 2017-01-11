<?php
 /**
 * Plugin Name: Käyttäjälisääjä
 * Description: Keino lisätä käyttäjätietoja tietokantaan
 * Version: Valmisversio
 * Author: Tatu Oksala & Mikias Berkhanu
 */
function Mikias_create_menu() {

	//create new top-level menu
	add_menu_page('Mikias Plugin Settings', 'Käyttäjät', 'administrator', __FILE__, 'Mikias_settings_page',plugins_url('/images/icon.png', __FILE__));
	//crate submenus
	add_submenu_page( __FILE__, 'Settings page title', 'Lisää käyttäjä', 'manage_options', 'Add_user', 'addfunction');
	add_submenu_page( null, 'Settings page title', 'Muokkaa käyttäjää', 'manage_options', 'Modify_user', 'editfunction');
	//call register settings function
	add_action( 'admin_init', 'register_mysettings' );
	
}

//file location and submit button implementation
 include_once($_SERVER['DOCUMENT_ROOT'].'/wordpress/wp-config.php' );
 if(isset($_POST['submit'])){
	 
	if($_POST['Funktio'] == 'Lisaa')

	 {
wp_redirect( home_url().'/wp-admin/admin.php?page=Kayttajalisaaja%2FKayttajalisaaja.php' );
uusi_funktio();
 } elseif($_POST['submit'] == 'delete'){

 $wpdb->delete( 'wp_testsiteturunsoitannollinenkerho', 
array( 'ID' => $_POST['ID'] ), null );

echo "Poistettu osoite: ".$_POST['ID'];
 } 
 
 }
 if($_POST['submit'] == 'Muokkaa'){
	 global $wpdb;
	
	$userInfo = $wpdb->get_row("UPDATE * FROM wp_testsiteturunsoitannollinenkerho WHERE ID = '".intval($_GET['ID'])."'");
		}

  if($_POST['submit'] == 'Luo uusi käyttäjä'){
	 wp_redirect( home_url().'/wp-admin/admin.php?page=Add_user' ); exit;
  }

 //Admin menus function
add_action('admin_menu', 'Mikias_create_menu');
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

//Input boxes validation 
function uusi_funktio() {
 global $wpdb;
 
 if (
 (strlen ($_POST['etunimi']) <1) ||
 (strlen ($_POST['sukunimi']) <1) ||
 (strlen ($_POST['syntymavuosi']) <1) ||
 (strlen ($_POST['puhelinnumero']) <1) ||
 (strlen ($_POST['sahkopostiosoite']) <1) ||
 (strlen ($_POST['kotiosoite']) <1)
 ){
	 echo "Pakollinen kenttä tyhjä!";
	 
 }
 else { 
 $wpdb->insert($wpdb->prefix.'TurunSoitannollinenKerho', array('Etunimi' => $_POST['etunimi'] ,
                             'Sukunimi' => $_POST['sukunimi'] , 
							  'Syntymavuosi' => $_POST['syntymavuosi'],
							   'Puhelinnumero' => $_POST['puhelinnumero'],
							   'Sahkopostiosoite' => $_POST['sahkopostiosoite'],
							    'Kotiosoite' => $_POST['kotiosoite'],));
 }
}

// Adding the database
register_activation_hook( __FILE__, 'Tietokannanlisays');
global $Tietokantaversio;
$Tietokantaversio='1.0';
function Tietokannanlisays () {
	global $wpdb;
	global $Tietokantaversio;

	
	//Database creation automatically if it doesn't already exist
	$TurunSoitannollinenKerho=$wpdb->prefix .'TurunSoitannollinenKerho';

	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE IF NOT EXISTS $TurunSoitannollinenKerho (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`Etunimi` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
	`Sukunimi` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
	`Syntymavuosi` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
	`Sahkopostiosoite` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
	`Kotiosoite` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
	`Puhelinnumero` varchar(50) COLLATE utf8_unicode_ci NOT NULL, PRIMARY KEY ID (ID)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
		add_option( 'Tietokantaversio', $Tietokantaversio);
  
}


//add submenus function part
function addfunction()
{
	//Retrieves the absolute URL to the plugin directory
	$plugins_url = plugins_url('Kayttajalisaaja/Kayttajalisaaja.php' , dirname(__FILE__) ); ?>
	
<!-- including css. file -->
<link rel='stylesheet' href='../wp-content/plugins/Kayttajalisaaja/Kayttajat.css'>
<div class="wrap">


<!-- User adder -->
<h2>Käyttäjälisääjä</h2>

<form action="<?php echo $plugins_url ?>" method="POST">
    <?php settings_fields( 'Mikias-settings-group' ); ?>
    <?php do_settings_sections( 'Mikias-settings-group' ); ?>
	
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Etunimi </th>
        <td><input type="text" name="etunimi" required placeholder= "Kirjoita tähän"value="<?php echo esc_attr( get_option('Etunimi') ); ?>" /></td>
		<input type="hidden" name="Funktio" value="Lisaa">
        </tr>
         
        <tr valign="top">
        <th scope="row">Sukunimi</th>
        <td><input type="text" name="sukunimi" required placeholder= "Kirjoita tähän"value="<?php echo esc_attr( get_option('Sukunimi') ); ?>" /></td>
        </tr>
        
        <tr valign="top">
        <th scope="row">Syntymävuosi</th>
        <td><input type="date" name="syntymavuosi" required pattern="(\/?\d[- .]*){6,13}"required placeholder= "esim. 01.01.2015"value="<?php echo esc_attr( get_option('Syntymävuosi') ); ?>" /></td>
        </tr>
		
		<tr valign="top">
        <th scope="row">Kotiosoite.</th>
        <td><input type="text" name="kotiosoite" required placeholder= "Kirjoita tähän"value="<?php echo esc_attr( get_option('Kotiosoite') ); ?>" /></td>
        </tr>
		
		<tr valign="top">
        <th scope="row">Sähköpostiosoite.</th>
        <td><input type="email" name="sahkopostiosoite" required placeholder= "esim. Matti.92@hotmail.fi"value="<?php echo esc_attr( get_option('Sähköpostiosoite') ); ?>" /></td>
        </tr>
		
		<tr valign="top">
        <th scope="row">Puhelinnumero.</th>
        <td><input type="tel" name="puhelinnumero" required pattern="(\+?\d[- .]*){7,13}" required placeholder= "esim. +358 40 1234567"value="<?php echo esc_attr( get_option('Puhelinnumero') ); ?>" /></td>
        </tr>

    </table>
    
<?php submit_button('Lisää');?>
</form>
</div>
</div>
<?php
}

//Function that updates the userinformation to the database
function update($ID)
{
	global $wpdb;
	$käyttäjätiedot = array( 
		'Etunimi' => $_POST['etunimi'],
		'Sukunimi' => $_POST['sukunimi'],
		'Syntymavuosi' => $_POST['syntymavuosi'],
		'Puhelinnumero' => $_POST['puhelinnumero'],
		'Sahkopostiosoite' => $_POST['sahkopostiosoite'],
		'Kotiosoite' => $_POST['kotiosoite'],
	);				
	
	$tietokanta = array( 'ID' => $ID );
	
	
	$wpdb->update( 
	'wp_testsiteturunsoitannollinenkerho',           					//    <--- Database name inserted inside the quotes
	 $käyttäjätiedot,
	$tietokanta
);
}

//Uses update function and redirects 
function editfunction()
{
	global $wpdb;
	
	if(isset($_POST['ID'])) {
		
		update($_POST['ID']);
		wp_redirect( home_url().'/wp-admin/admin.php?page=Kayttajalisaaja%2FKayttajalisaaja.php' ); 
		exit; 
	}
	
//Fetch one line from DB containing user information
$userInfo = $wpdb->get_row("SELECT * FROM wp_testsiteturunsoitannollinenkerho WHERE ID = '".intval($_GET['ID'])."'");	
$plugins_url = plugins_url('Kayttajalisaaja/Kayttajalisaaja.php' , dirname(__FILE__) );
?>
<link rel='stylesheet' href='../wp-content/plugins/Kayttajalisaaja/Kayttajat.css'>
<div class="wrap">


<!-- User modifier -->
<h2>Käyttäjämuokkaaja</h2>

<form action="#" method="POST">
    <?php settings_fields( 'Mikias-settings-group' ); ?>
    <?php do_settings_sections( 'Mikias-settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Etunimi </th>
        <td><input type="text" name="etunimi" required placeholder="Kirjoita tähän" value="<?php echo $userInfo->Etunimi; ?>" /></td>
		<input type="hidden" name="Funktio" value="Lisaa">
        </tr>
         
        <tr valign="top">
        <th scope="row">Sukunimi</th>
        <td><input type="text" name="sukunimi" required placeholder="Kirjoita tähän" value="<?php echo $userInfo->Sukunimi; ?>" /></td>
        </tr>
        
        <tr valign="top">
        <th scope="row">Syntymävuosi</th>
        <td><input type="date" name="syntymavuosi" required pattern="(\/?\d[- .]*){6,13}" required placeholder= "esim. 01.01.2015"value="<?php echo $userInfo->Syntymavuosi; ?>" /></td>
        </tr>
		
		<tr valign="top">
        <th scope="row">Kotiosoite.</th>
        <td><input type="text" name="kotiosoite" required placeholder="Kirjoita tähän" value="<?php echo $userInfo->Kotiosoite; ?>" /></td>
        </tr>
		
		<tr valign="top">
        <th scope="row">Sähköpostiosoite.</th>
        <td><input type="email" name="sahkopostiosoite" required placeholder="esim. Matti.92@hotmail.fi" value="<?php echo $userInfo->Sahkopostiosoite; ?>" /></td>
        </tr>
		
		<tr valign="top">
        <th scope="row">Puhelinnumero.</th>
        <td><input type="tel" name="puhelinnumero" required pattern="(\+?\d[- .]*){7,13}" required placeholder= "esim. +358 40 1234567"value="<?php echo $userInfo->Puhelinnumero; ?>" /></td>
        </tr>
		
		<tr valign="top">
        <th scope="row"></th>
        <td><input type="hidden" name="ID" value="<?php echo $userInfo->ID; ?>" /></td>
        </tr>
		
		<th class="tg-031e"> <input id="editUser" name="Tallenna" type="submit" value="Tallenna" ;></th>
		
    </table>
    
</form>
</div>
</div>
<?php
}
	  ?>
<?php 

//register our settings
function register_mysettings() {
	register_setting( 'Mikias-settings-group', 'Etunimi' );
	register_setting( 'Mikias-settings-group', 'Sukunimi' );
	register_setting( 'Mikias-settings-group', 'Syntymävuosi' );
	register_setting( 'Mikias-settings-group', 'Kotiosoite' );
	register_setting( 'Mikias-settings-group', 'Sähköpostiosoite' );
	register_setting( 'Mikias-settings-group', 'Puhelinnumero' );
}

//Admin menus function
function Mikias_settings_page() {
global $wpdb;
?>
<?php

//Delete buttons function
if(isset($_GET['delete_user'])) {
	
	   $wpdb->delete( 'wp_testsiteturunsoitannollinenkerho', array( 'ID' => $_GET['delete_user'] ), null );

	   echo "Poistettu osoite: ".$_GET['delete_user']; 
	   wp_redirect( home_url().'/wp-admin/admin.php?page=Kayttajalisaaja%2FKayttajalisaaja.php' ); exit; 
}

$plugins_url = plugins_url('Kayttajalisaaja/Kayttajalisaaja.php' , dirname(__FILE__) ); ?>

<!-- Confirmation box for the delete function -->
<script>
	function confirmDelete(ID) {
		if(confirm('Oletko varma, että haluat poistaa tämän tiedot?')) {
			window.location.replace('admin.php?page=Kayttajalisaaja%2FKayttajalisaaja.php&delete_user=' + ID);
		}
	}
</script>

<!-- Include css. file -->
<link rel='stylesheet' href='../wp-content/plugins/Kayttajalisaaja/Kayttajat.css'>
<div class="wrap">
<form action = "<?php echo $plugins_url; ?>" method="POST"><?php echo submit_button ('Luo uusi käyttäjä', Luo); ?></form>
<?php 

//fetch information from the database
$jasenet = $wpdb->get_results( 
	"
	SELECT * 
	FROM `wp_testsiteturunsoitannollinenkerho`
	"
);
?>

<!-- Information added to the admin menu -->

<form action="<?php echo $plugins_url ?>" method="POST">
<table class="tg">
<tr>
	<th id="otsikko">Etunimi</th>
    <th id="otsikko">Sukunimi</th>
    <th id="otsikko">Syntymävuosi</th>
    <th id="otsikko">Kotiosoite</th>
    <th id="otsikko">Sähkopostiosoite</th>
    <th id="otsikko">Puhelinnumero</th>
	<th id="otsikko">Poista</th>
	<th id="otsikko">Muokkaa</th>
</tr>

		<?php
		foreach ( $jasenet as $jasen ) 
		{
			//Gets the properties of the given object
			$jasen = get_object_vars($jasen);
			?>
			<input type="hidden" name="<?php $jasen['ID'] ?>" value="<?php echo $jasen['ID']; ?>" />

			<tr>
				<td class="tg-031e"><?php echo $jasen['Etunimi']; ?></td>
				<td class="tg-031e"><?php echo $jasen['Sukunimi']; ?></td>
				<td class="tg-031e"><?php echo $jasen['Syntymavuosi']; ?></td>
				<td class="tg-031e"><?php echo $jasen['Kotiosoite']; ?></td>
				<td class="tg-031e"><?php echo $jasen['Sahkopostiosoite']; ?></td>
				<td class="tg-031e"><?php echo $jasen['Puhelinnumero']; ?></td>
				<td class="tg-031e"><input type="button" value="Poista käyttäjä" onclick="confirmDelete(<?php echo $jasen['ID'] ?>)"/></td>
				<td class="tg-031e"><input type="button" value="Muokkaa tietoja" onclick="window.location.replace('admin.php?page=Modify_user&ID=<?php echo $jasen['ID']; ?>')"/></td>
			</tr>
			<?php
		}
		?>
	</td>
</table>
</form>
</div>
</div>
<?php }?>