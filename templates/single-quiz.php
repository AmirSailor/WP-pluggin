<?php
/**
 * The template for displaying a single quiz.
 *
 * This template is loaded by the 'aigq_load_quiz_template' function in 'public/quiz-page.php'.
 */

get_header();

while (have_posts()) :
    the_post();

    $lecture_id = get_post_meta(get_the_ID(), '_quiz_lecture_id', true);
    $transcript = get_post_meta($lecture_id, '_lecture_transcript', true);
    $quiz_data_json = get_post_meta($lecture_id, '_lecture_quiz_data', true);
    $quiz_data = json_decode($quiz_data_json, true);
    $summary = $quiz_data['summary'];
    $quiz = $quiz_data['quiz'];

    ?>
    <div id="primary" class="content-area">
        <main id="main" class="site-main">
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="entry-header">
                    <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
                </header>

                <div class="entry-content">
                    <a href="<?php echo add_query_arg('generate_pdf', 'true'); ?>" class="button"><?php _e('Download PDF', 'aigq'); ?></a>

                    <h2><?php _e('Summary', 'aigq'); ?></h2>
                    <p><?php echo esc_html($summary); ?></p>

                    <h2><?php _e('Transcript', 'aigq'); ?></h2>
                    <p><?php echo esc_html($transcript); ?></p>

                    <h2><?php _e('Quiz', 'aigq'); ?></h2>
                    <form id="aigq-quiz-form" data-quiz-id="<?php the_ID(); ?>">
                        <?php foreach ($quiz as $index => $question) : ?>
                            <div class="aigq-question">
                                <h4><?php printf(__('Question %d: %s', 'aigq'), $index + 1, esc_html($question['question'])); ?></h4>
                                <ul>
                                    <?php foreach ($question['options'] as $option) : ?>
                                        <li>
                                            <label>
                                                <input type="radio" name="aigq_answers[<?php echo $index; ?>]" value="<?php echo esc_attr($option); ?>" />
                                                <?php echo esc_html($option); ?>
                                            </label>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endforeach; ?>
                        <?php submit_button(__('Submit Quiz', 'aigq')); ?>
                    </form>
                    <div id="aigq-quiz-results" style="display:none;"></div>
                </div>
            </article>
        </main>
    </div>
    <?php

endwhile;

get_footer();
