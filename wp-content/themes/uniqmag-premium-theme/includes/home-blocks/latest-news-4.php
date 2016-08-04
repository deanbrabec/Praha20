<?php 
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    $DF_builder = new different_themes_home_builder; 
    //get block data
    $data = $DF_builder->get_data(); 
    //set query
    $my_query = $data[0]; 
    //extract array data
    extract($data[1]); 
    $counter = 1;

?>

<div class="cs-row">
    <?php if($title) { ?>
        <div class="cs-col cs-col-12-of-12">
            <!-- Post block title -->
            <div class="cs-post-block-title" style="border-left-color: #<?php echo esc_attr($color);?>;">
                <h4>
                    <?php if($link) { ?>
                        <a href="<?php echo esc_url($link);?>">
                    <?php } ?>
                        <?php echo esc_html($title);?>
                    <?php if($link) { ?>
                        </a>
                    <?php } ?>
                </h4>
                <?php if($subtitle) { ?>
                    <p><?php echo esc_html($subtitle);?></p>
                <?php } ?>
            </div>
        </div>
    <?php } ?>


    <div class="cs-col cs-col-12-of-12">
        <!-- Block layout 4 -->
        <div class="cs-post-block-layout-4">
            <?php if ($my_query->have_posts()) : while ($my_query->have_posts()) : $my_query->the_post(); ?>
                <?php
                    $image = uniqmag_different_themes_get_post_thumb($my_query->post->ID,0,0);
                    //categories
                    $categories = get_the_category($my_query->post->ID);
                    $catCount = count($categories);
                    //select a random category id
                    $id = rand(0,$catCount-1);

                    //get post ratings information
                    $avarage_rating = Different_Themes()->ratings->avarage_rating( get_the_ID() );

                ?>
                    <!-- Post item -->
                    <div class="cs-post-item">
                        <?php if( $df_post->is_image(get_the_ID()) == true ) { ?>
                            <div class="cs-post-thumb">
                                <?php 
                                    if( $df_post->compare( get_the_ID(), 'post_icons' ) == "1" && $df_post->get_icon(get_post_format()) ) { 
                                ?>
                                    <div class="cs-post-format-icon">
                                        <?php echo uniqmag_different_themes_html_output($df_post->get_icon(get_post_format()));?>
                                    </div>
                                <?php } ?>
                                <?php get_template_part(UNIQMAG_DIFFERENT_THEME_LOOP."image"); ?>
                            </div>
                        <?php } ?>
                        <div class="cs-post-inner">
                            <?php if( $df_post->compare( get_the_ID(), 'post_category' ) == "1" && $categories ) { ?>
                                <div class="cs-post-category-solid cs-clearfix">
                                    <?php 
                                        foreach($categories as $cat) {
                                            $category_color = $df_post->get_color($cat->term_id,"category", false);
                                    ?>
                                        <a href="<?php echo esc_url(get_category_link($cat->term_id));?>" style="background-color:<?php echo esc_attr($category_color);?>">
                                            <?php echo esc_html(get_cat_name($cat->term_id));?>
                                        </a>
                                    <?php } ?>
                                </div>
                            <?php } ?>

                            <h3>
                                <a href="<?php the_permalink();?>">
                                    <?php the_title();?>
                                </a>
                            </h3>
                            <div class="cs-post-meta cs-clearfix">
                                <?php 
                                    if( $df_post->compare( get_the_ID(), 'post_author' ) == "1" ) { 
                                ?>
                                    <span class="cs-post-meta-author">
                                        <?php echo the_author_posts_link(); ?>
                                    </span>
                                <?php
                                    } 
                                ?>
                                <?php if( $df_post->compare( get_the_ID(), 'post_date' ) == "1" ) { ?>
                                    <span class="cs-post-meta-date">
                                        <?php the_time(get_option('date_format'));?>
                                    </span>
                                <?php } ?>
                                <?php if( $avarage_rating ) { ?>
                                    <span class="cs-post-meta-rating" title="<?php printf ( esc_attr__('Rated %1$s out of %2$s','uniqmag'), floatval($avarage_rating[1]), intval($df_ratings::$max_val));?>">
                                        <span style="width: <?php echo floatval($avarage_rating[0]);?>%"><?php printf ( esc_html__('Rated %1$s out of %2$s','uniqmag'), floatval($avarage_rating[1]), intval($df_ratings::$max_val));?></span>
                                    </span>
                                <?php } ?>
                            </div>

                            <p class="cs-post-excerpt">
                                <?php echo esc_html($df_post->get_the_excerpt(20)); ?>
                            </p>

                            <a class="cs-post-read-more" href="<?php the_permalink();?>">
                                <?php esc_html_e("Read more",'uniqmag');?> <i class="fa fa-angle-double-right"></i>
                            </a>
                        </div>
                    </div>
            <?php endwhile; endif; ?>
        </div>
    </div>
</div>
