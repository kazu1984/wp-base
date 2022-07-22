<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="format-detection" content="telephone=no">
  <meta name=“robots” content=“noindex”>


  <title><?php bloginfo('name'); ?></title>
  <meta name="description" content="<?php bloginfo('description') ?>">
  <link rel="shortcut icon" href="<?php echo esc_url(get_template_directory_uri()) . '/assets/images/common/icon.png' ?>">
</head>


<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
  <div class="l-container">
    <header class="l-header p-header">
      
    </header>
    <main>