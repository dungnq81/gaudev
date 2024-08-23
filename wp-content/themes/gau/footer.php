<?php
/**
 * The template for displaying the footer.
 * Contains the body & html closing tags.
 *
 * @package HD
 */

\defined( 'ABSPATH' ) || die;

/**
 * After Site Content
 */
do_action( 'gau_after_site_content' );

?>
    </div><!-- #site-content -->
    <?php

    /**
     * Before Footer
     */
    do_action( 'gau_before_footer' );

    ?>
    <footer id="site-footer" class="site-footer" <?php echo \Cores\Helper::microdata( 'footer' ); ?>>
        <?php

        /**
         * Footer
         *
         * @see __construct_footer_widgets - 5
         * @see __construct_footer_credit - 10
         */
        do_action( 'gau_footer' );

        ?>
    </footer><!-- .site-footer -->
    <?php

    /**
     * After Footer
     */
    do_action( 'gau_after_footer' );

    /**
     * Footer
     *
     * @see __wp_footer - 98
     */
    wp_footer();

    ?>
</body>
</html>
