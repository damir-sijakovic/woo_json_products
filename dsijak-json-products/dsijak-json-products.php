<?php
/**
 * Plugin Name: dsijak-json-products
 * Plugin URI: https://damir.ioox.studio
 * Description: Creates JSON list of products and categories.
 * Version: 0.6
 * Text Domain: dsijak-json-products
 * Author: Damir Šijaković
 * Author URI: https:damir.//ioox.studio
 */
 
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';


add_action('admin_menu', 'dsijak_jsonProductsMenu');
function dsijak_jsonProductsMenu()
{
  add_menu_page('Dsijak Json Products', 'Dsijak Json Products', 'manage_options', 'dsijak-json-products-slug', 'dsijak_mainAdminPage');
}

function dsijak_mainAdminPage() 
{	
			
	if (!function_exists('wc_get_products')) 
	{
		echo '<div id="message" class="error fade"><p>'
		.'You have no WooCommerece plugin installed!' . '</p></div>';
		die();
	} 


  if (!current_user_can('manage_options'))  
  {
    wp_die( __('You do not have sufficient pilchards to access this page.')    );
  }

  echo '<div class="wrap">';
  echo dsijak_getInfoText();  
  
  if (!dsijak_jsonFileExists())
  {
	  echo '<p>Create json file:</p>';
	  
	  // Check whether the button has been pressed AND also check the nonce
	  if (isset($_POST['post_createListButton']) && check_admin_referer('post_createListButtonToken')) 
	  {
		dsijak_jsonProductsCreateButtonAction();
	  }
	  echo '<form action="options-general.php?page=dsijak-json-products-slug" method="post">';
	  wp_nonce_field('post_createListButtonToken');
	  echo '<input type="hidden" value="true" name="post_createListButton" />';
	  submit_button('Create JSON file');
	  echo '</form>';  
  }
  else
  {  
	  echo '<p>Delete json file:</p>';

	  if (isset($_POST['post_deleteListButton']) && check_admin_referer('post_deleteListButtonToken')) 
	  {
		dsijak_jsonProductsDeleteButtonAction();
	  }
	  echo '<form action="options-general.php?page=dsijak-json-products-slug" method="post">';
	  wp_nonce_field('post_deleteListButtonToken');
	  echo '<input type="hidden" value="true" name="post_deleteListButton" />';
	  submit_button('Delete JSON file');
	  echo '</form>';   
  }
  
  echo '</div>';
}

function dsijak_jsonProductsCreateButtonAction()
{	
	if (dsijak_createJsonFile())
	{
		echo '<div id="message" class="updated fade"><p>'
		.'List was created successfully.' . '</p>
		<script type="text/javascript">  window.location=document.location.href; </script>
		</div>';
	}
	else
	{
		echo '<div id="message" class="error fade"><p>'
		.'Can\'t create JSON list!' . '</p></div>';
	}

}  

function dsijak_jsonProductsDeleteButtonAction()
{	
	if(dsijak_deleteJsonFile())
	{
		echo '<div id="message" class="updated fade"><p>'
		.'List was deleted successfully.' . '</p>
		<script type="text/javascript">  window.location=document.location.href; </script>
		</div>';
	}
	else
	{
		echo '<div id="message" class="error fade"><p>'
		.'No JSON list found!' . '</p></div>';
	}
}  



