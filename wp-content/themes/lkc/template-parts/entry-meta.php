<footer class="entry-meta">
	<?php
	if(is_single()){
		if(of_get_option('kcsite_show_categories_in_single_new')){
			$categories_list = get_the_category_list( __( ', ', 'kcsite' ) );
			if ($categories_list){ ?>
				<div class="cat-links">
					<?php printf( __( '<span class="%1$s">Categories:</span> %2$s', 'kcsite' ), 'entry-utility-prep entry-utility-prep-cat-links', $categories_list );
					$show_sep = true; ?>
				</div>
			<?php } 
		}

		if(of_get_option('kcsite_show_tags_in_single_new')){
			$tags_list = get_the_tag_list( '', __( ', ', 'kcsite' ) );
			if ( $tags_list ) { ?>
				<div class="tag-links">
					<?php printf( __( '<span class="%1$s">Tagged:</span> %2$s', 'kcsite' ), 'entry-utility-prep entry-utility-prep-tag-links', $tags_list );?>
				</div>
			<?php }
		}
	} ?>

	<?php if(of_get_option('kcsite_show_admin_links')){ ?>
	<div class="admin-links">
		<?php edit_post_link( __('Edit', 'kcsite'), '<span class="edit-link">', '</span>' ); ?>
	</div>
	<?php } ?>
</footer>