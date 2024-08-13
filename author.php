<?php get_header(); ?>

<?php
if (have_posts()) :
    while (have_posts()) : the_post();

        $author_name = get_field('author_name');
        $author_description = get_field('author_description');
        $author_location = get_field('author_location');
        $author_image = get_field('author_image');
        ?>
        
        <div class="author-details">
            <h1><?php echo esc_html($author_name); ?></h1>
            <p><?php echo esc_html($author_description); ?></p>
            <p><?php echo esc_html($author_location); ?></p>
            <?php if ($author_image) : ?>
                <img src="<?php echo esc_url($author_image['url']); ?>" alt="<?php echo esc_attr($author_image['alt']); ?>">
            <?php endif; ?>
        </div>

    <?php endwhile;
endif;
?>

<?php get_footer();?>
