Using an SVG Icon for a WordPress Custom Post Type
==================================================

When creating a Custom Post Type in WordPress there is a convenient way to select a custom icon when [registering the post type](https://developer.wordpress.org/plugins/post-types/registering-custom-post-types/). WordPress comes with a set of built-in icons called [Dashicons](https://developer.wordpress.org/resource/dashicons/) that have easy names that can be used for your `menu_icon` property:

    register_post_type( 'acme_product',
        array(
            'menu_icon'   => 'dashicons-products',
        )
    );

Having these icons at hand is great. Unfortunately, I often have trouble finding an icon that “fits” my custom post type well. As a comparison Dashicons includes 303 icons while popular font icon library, [FontAwesome](https://fontawesome.com/), has over 7,000. I am going to walk you through my process of using an SVG from FontAwesome as a Custom Post Type icon.

The first step, as you’ve no doubt guessed, is to find the SVG icon that you would like to use for your Custom Post Type. In my example, I will use an icon from FontAwesome but there are many other sources for SVGs including [Google’s Material Icons](https://fonts.google.com/icons) which are free and Open Source.

FontAwesome allows you to download an icon as an SVG file:

[![](https://second-cup-of-coffee.com/wp-content/uploads/2021/05/download-fontawesome-svg.png)

![](https://second-cup-of-coffee.com/wp-content/uploads/2021/05/download-fontawesome-svg.png)

](https://second-cup-of-coffee.com/wp-content/uploads/2021/05/download-fontawesome-svg.png)

FontAwesome’s option to download an icon as a SVG.

With my new SVG icon in hand, I can add it to, for example, my plugin containing the code registering my Custom Post Type. I placed my [honey badger icon](https://fontawesome.com/icons/badger-honey?style=duotone) SVG file in the `assets` folder of my plugin.

[![](https://second-cup-of-coffee.com/wp-content/uploads/2021/05/plugin-file-structure.png)

![](https://second-cup-of-coffee.com/wp-content/uploads/2021/05/plugin-file-structure.png)

](https://second-cup-of-coffee.com/wp-content/uploads/2021/05/plugin-file-structure.png)

The SVG file in my plugin’s assets folder.

In the function where I am registering my Custom Post Type I read in the contents of that SVG image with PHP’s `[file_get_contents](https://www.php.net/manual/en/function.file-get-contents.php)` function. Getting the plugin’s file path can be done through WordPress’s [plugin\_dir\_path](https://developer.wordpress.org/reference/functions/plugin_dir_path/) function and the \_\_FILE\_\_ constant. `plugin_dir_path( __FILE__ )` is a way to get the directory of the current file. In this case, it is my plugin file where I am registering the `honey_badgers` Custom Post Type. I know that from this file that my SVG graphic is in the `assets` folder. Therefore, I can read the file in using the following line:

    $menu_icon = file_get_contents( plugin_dir_path( __FILE__ ) . 'assets/badger-honey-duotone.svg' );

The `$menu_icon` variable now contains the content of the SVG file. I will need to run that content through PHP’s `[base64_encode](https://www.php.net/manual/en/function.base64-encode.php)` function to make it a string usable for my `menu_icon` property. Instead of using the identifier for a Dashicon, I will tell `menu_icon` to use the SVG data with the following line:

    "menu_icon" => 'data:image/svg+xml;base64,' . base64_encode( $menu_icon ),

My SVG icon will now be used in the WordPress Dashboard.

[![](https://second-cup-of-coffee.com/wp-content/uploads/2021/05/cpt-svg-icon.png)

![](https://second-cup-of-coffee.com/wp-content/uploads/2021/05/cpt-svg-icon.png)

](https://second-cup-of-coffee.com/wp-content/uploads/2021/05/cpt-svg-icon.png)

An SVG icon used for a Custom Post Type.

Published May 18, 2021By [Scott Tirrell](https://second-cup-of-coffee.com/author/stirrell/)

Categorized as [Web Development](https://second-cup-of-coffee.com/category/web-development/), [WordPress](https://second-cup-of-coffee.com/category/wordpress/)
