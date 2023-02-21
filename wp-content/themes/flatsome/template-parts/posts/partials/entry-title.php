<h6 class="entry-category is-xsmall">
	<?php echo get_the_category_list( __( ', ', 'flatsome' ) ) ?>
</h6>

<?php
if ( is_single() ) {
	echo '<h1 class="entry-title">' . get_the_title() . '</h1>';
} else {
	echo '<h2 class="entry-title"><a href="' . get_the_permalink() . '" rel="bookmark" class="plain">' . get_the_title() . '</a></h2>';
}
?>
<div class="entry-divider is-divider small"></div>
<div class="large-12 col-thongtin">
	<div class="nguoidang">
	<?php the_author(); ?>
	</div>
	<div class="ngaydang">
	<?php echo get_the_time('d/m/y', get_the_ID()); ?>
	</div>
</div>
<?php
$single_post = is_singular( 'post' );
if ( $single_post && get_theme_mod( 'blog_single_header_meta', 1 ) ) : ?>
	<div class="entry-meta uppercase is-xsmall">
		<?php flatsome_posted_on(); ?>
	</div>
<?php elseif ( ! $single_post && 'post' == get_post_type() ) : ?>
	<div class="entry-meta uppercase is-xsmall">
		<?php flatsome_posted_on(); ?>
	</div>
<?php endif; ?>