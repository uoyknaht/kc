				<?php if(!is_front_page()){ ?>			
							</div><!-- .contentbar -->
							<?php get_template_part('template-parts/sidebar-right');?>

						</div><!-- .col-right -->
					</div><!-- .col-wrap -->

				</div><!-- .wrap -->
				<?php } ?>

				<?php get_template_part('template-parts/sidebar-before-footer');?>
			</div><!-- .content-wrap -->

			<div id="footer" class="clearfix">
				<div class="wrap">
					<div class="footer-wrap-inner">
						<?php echo of_get_option_by_lang('kcsite_footer_contacts_text'); ?>
					</div>
				</div>
			</div>
			<div class="side-shadow side-shadow-l"></div>
			<div class="side-shadow side-shadow-r"></div>			
	
	<?php wp_footer(); ?>
	</body>
</html>