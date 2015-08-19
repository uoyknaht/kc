<?php

/*

hardcoded:

- all static (semi hardcoded) vars are defined in functions.php kcsite_theme_setup() function $staticVars array


- wp-polls plugin wp-polls file around 1128 line search fro string 'wp-polls-paging'
  needed to remove some unnecesarry elments - comemnted them. Also added paging wrap div and hr

  wp-polls.php around 145 line added if() to hide poll if no polls are active

- options framework plugin. Replaced hardcoded text 'Use this image' with 'Naudoti šį paveikslėlį' in of-medialibrary-uploader.js

- smart archives plugin. Month heading structure is hardcoded in plugin. So changed it in 2 places in generator.php around 132 line

- interesting facts post type articles are not pages, so "Interesting facts menu item in left-col sub menu does not get current class"
	had to use javascript to achive that in col-left.php template part. This is not implemented for english language for now.
- the same with breadcrumbs viewing single interesting facts.  See simple_breadcrumb function

- events calendar:
	widget-list.class.php 74 line "all events button"
    amdin-view/venue-meta-box.php countries list


- what to do when moving to different domain
	-
	-
	- wp-polls in admin interface, templates, change "Apklausu archyvas" link. Search for string "wp-poll-btn-go-to-archive"


*/
?>