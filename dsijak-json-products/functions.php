<?php

require_once __DIR__ . '/config.php';

function dsijak_getCategories()
{

	$taxonomy     = 'product_cat';
	$orderby      = 'name';  
	$show_count   = 1;     
	$pad_counts   = 0;     
	$hierarchical = 1;     
	$title        = '';  
	$empty        = 0;

	$args = array(
		 'taxonomy'     => $taxonomy,
		 'orderby'      => $orderby,
		 'show_count'   => $show_count,
		 'pad_counts'   => $pad_counts,
		 'hierarchical' => $hierarchical,
		 'title_li'     => $title,
		 'hide_empty'   => $empty
	);

	$all_categories = get_categories( $args );
   
    $jsonObject = new stdClass;
	$outputArray = [];

	foreach ($all_categories as $cat) 
	{		
		$t = new stdClass;
		$t->id = $cat->term_id;	
		$t->parent = $cat->parent;	
		$t->name = $cat->name;	
		$t->count = $cat->count;
		$t->slug = $cat->slug;	
		$t->description = $cat->description;	
			 
		$thumbnail_id = get_term_meta( $cat->term_id, 'thumbnail_id', true ); 
		$t->thumbnail = parse_url( wp_get_attachment_url( $thumbnail_id ) )['path']; 	

		array_push($outputArray, $t);

	}    

    return $outputArray;	
}

function dsijak_createProductList()
{
	$productList = [];
	
	$args = array(
        'post_type'      => 'product',
        'posts_per_page' => -1,
    );

    $loop = new WP_Query( $args );

    while ( $loop->have_posts() ) : $loop->the_post();
        global $product;
        
		$tempItem = [
			"id" => $product->get_id(),
			"slug" => $product->get_slug(),
			"title" => $product->get_title(),
			"image" => parse_url(get_the_post_thumbnail_url($product->get_id()))['path'],
			"price" => $product->get_price(),
			"categories" => $product->get_category_ids(),			
		];
        
        array_push($productList, $tempItem);

    endwhile;

    return $productList;
	
}	
	

function dsijak_getNumberOfProducts()
{
	$products = new WP_Query( ['post_type' => 'product', 'post_status' => 'publish', 'posts_per_page' => -1] );
	return $products->found_posts;
}


function dsijak_getInfoText()
{	
	$str = "<h2>Dsijak Json Products</h2>";	
	$str .= "<p><b>Creates WooCommerce categories/products list in JSON format.</b><br>";
	$str .= "You can fetch it via route: '/dsijak-json-products.json'. If list is too big, you can async load it. <br>";
	$str .= "This is useful if want you to create external JS menus and sliders.</p>";

	if (dsijak_jsonFileExists())	
	{
		$str .= "<hr>";
		$str .= "<p>JSON file url: <a target='_blank' href='/dsijak-json-products.json'>route</a></p>";
	}
	
	$str .= "<hr>";
	
	return $str;
}

function dsijak_jsonFileExists()
{		
	if (file_exists(DSIJAK_JSON_PRODUCTS_FILE_NAME))
	{
		return true;
	}
	
	return false;
}

function dsijak_createJsonFile()
{		
	$outputJsonFile = [];
	
	$outputJsonFile['numberOfProducts'] = dsijak_getNumberOfProducts();
	$outputJsonFile['categories'] = dsijak_getCategories();
	$outputJsonFile['products'] = dsijak_createProductList();
	
	return file_put_contents(DSIJAK_JSON_PRODUCTS_FILE_NAME, json_encode($outputJsonFile));
	
	
}

function dsijak_deleteJsonFile()
{		
	if (file_exists(DSIJAK_JSON_PRODUCTS_FILE_NAME))
	{
		unlink(DSIJAK_JSON_PRODUCTS_FILE_NAME);
		return true;
	}
	return false;
}
