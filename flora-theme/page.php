<?php
/**
 * Default Page Template
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="single-post-wrapper">
        <div class="container">
            <?php flora_render_breadcrumbs(); ?>

            <article id="page-<?php the_ID(); ?>" <?php post_class('single-post-container'); ?>>
                <header class="single-post-header">
                    <h1 class="single-post-title"><?php the_title(); ?></h1>
                </header>

                <div class="single-content entry-content">
                    <?php
                    while (have_posts()) : the_post();
                        the_content();
                    endwhile;
                    ?>
                </div>
            </article>
        </div>
    </div>
</main>

<?php
get_footer();
