<?php

/**
 * Plugin Name: Related Posts TailwindCSS
 * Plugin URI: https://github.com/susantokun/wp-plugin-related-posts-tailwindcss.git
 * Description: Related Posts Custom with Tailwind CSS.
 * Version: 2.0
 * Author: Susantokun
 * Author URI: https://www.susantokun.com/
 */

class SRelatedPostsTailwindCSS extends WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            's_related_posts_tailwindcss',
            __('Related Posts TailwindCSS', 'susantokun'),
            ['description' => __('Your site&#8217;s most related posts.', 'susantokun'), ]
        );
    }

    public function widget($args, $instance)
    {
        $number = (!empty($instance['number'])) ? absint($instance['number']) : 5;
        if (!$number) {
            $number = 5;
        }
        $show_date      = isset($instance['show_date']) ? $instance['show_date'] : false;
        $urutanKategori = (!empty($instance['urutanKategori'])) ? absint($instance['urutanKategori']) : 0;
        $category       = get_the_category();
        if (empty($category[$urutanKategori])) {
            return;
        }
        $first_category = $category[$urutanKategori];
        $title          = $first_category->cat_name;

        $r = new WP_Query(
            apply_filters(
                'widget_posts_args',
                [
                    'posts_per_page'      => $number,
                    'category__in'        => [$first_category->term_id],
                    'post__not_in'        => [get_the_ID()],
                    'order'               => 'ASC',
                    'no_found_rows'       => true,
                    'post_status'         => 'publish',
                    'ignore_sticky_posts' => true,
                ],
                $instance
            )
        );
        if (!$r->have_posts()) {
            return;
        }
        echo $args['before_widget'];
        if (!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        // output
        echo '<ul>';
        foreach ($r->posts as $related_post) {
            $post_title = get_the_title($related_post->ID);
            $title      = (!empty($post_title)) ? $post_title : __('(no title)', 'susantokun');
            if ($show_date) : ?>
                <li class="with-date">
                    <a href="<?php the_permalink($related_post->ID); ?>"><?php echo $title; ?></a>
                    <div class="widget-date">
                        <span class="mr-1 text-2xs icon-calendar"></span>
                        <span class="text-xs"><?php echo get_the_date('j F Y', $related_post->ID); ?></span>
                        <span class="ml-2 mr-1 text-xs icon-eye"></span>
                        <span class="text-xs"><?php echo susantokun_get_post_views($related_post->ID); ?></span>
                    </div>
                </li>
            <?php else : ?>
                <li>
                    <a href="<?php the_permalink($related_post->ID); ?>"><?php echo $title; ?></a>
                </li>
        <?php endif;
        }
        echo '</ul>';
        echo $args['after_widget'];
    }

    public function form($instance)
    {
        $number         = isset($instance['number']) ? absint($instance['number']) : 5;
        $show_date      = isset($instance['show_date']) ? (bool) $instance['show_date'] : false;
        $urutanKategori = isset($instance['urutanKategori']) ? absint($instance['urutanKategori']) : 0; ?>
        <p>
            <label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of posts to show:'); ?></label>
            <input class="tiny-text" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" /></p>
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_date); ?> id="<?php echo $this->get_field_id('show_date'); ?>" name="<?php echo $this->get_field_name('show_date'); ?>" />
            <label for="<?php echo $this->get_field_id('show_date'); ?>"><?php _e('Display post date?'); ?></label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('urutanKategori'); ?>"><?php _e('Number of category to show:', 'susantokun'); ?></label>
            <input class="tiny-text" id="<?php echo $this->get_field_id('urutanKategori'); ?>" name="<?php echo $this->get_field_name('urutanKategori'); ?>" type="number" step="1" min="0" value="<?php echo $urutanKategori; ?>" size="3" />
        </p>
        <?php
    }

    public function update($new_instance, $old_instance)
    {
        $instance                   = $old_instance;
        $instance['number']         = (int) $new_instance['number'];
        $instance['show_date']      = isset($new_instance['show_date']) ? (bool) $new_instance['show_date'] : false;
        $instance['urutanKategori'] = (int) $new_instance['urutanKategori'];
        return $instance;
    }
}

function register_SRelatedPostsTailwindCSS()
{
    register_widget('SRelatedPostsTailwindCSS');
}
add_action('widgets_init', 'register_SRelatedPostsTailwindCSS');
